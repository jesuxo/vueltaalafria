<?php
// app/Http/Controllers/MantenimientoRapidoController.php
namespace App\Http\Controllers;

use App\Helpers\VehiculoFotoHelper;

use App\Models\Cwmantenimientofoto;
use App\Models\Cwmantenimientoproducto;
use App\Models\CwMantenimientoTipo;
use App\Models\CWProductoRecomendado;
use App\Models\Saclie;
use App\Models\CWVehiculo;
use App\Models\CWTipoVehiculo;
use App\Models\CWMantenimiento;
use App\Models\Savend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;

class MantenimientoRapidoController extends Controller
{
    public function index()
    {
        $tipos_vehiculo = CWTipoVehiculo::orderBy('tipo')->get();
        $tipos_mantenimiento = [
            'cambio_aceite'            => 'Cambio de Aceite',
            'cambio_filtro_aceite'     => 'Cambio Filtro de Aceite',
            'cambio_filtro_gasolina'   => 'Cambio Filtro de Gasolina',
            'cambio_filtro_aire'       => 'Cambio Filtro de Aire',
            'mantenimiento_inyectores' => 'Mantenimiento de Inyectores',
            'bateria'                  => 'Batería',
            'otros'                    => 'Otros'
        ];

        // Obtener vendedores activos
        $vendedores = Savend::where('activo', 1)
            ->orderBy('descrip')
            ->get(['codvend', 'descrip']);

        return view('mantenimientorapido', compact('tipos_vehiculo', 'tipos_mantenimiento', 'vendedores'));
    }

    public function buscarVehiculo(Request $request)
    {

        $request->validate([
            'identificacion' => 'required|string|max:50'
        ]);
        $placa    = trim($request->identificacion);
        $placa    = strtoupper($placa);
        $placa    = str_replace(" ",'',$placa);

        // Buscar por coincidencia parcial (LIKE)
        $vehiculos = CWVehiculo::with(['cliente', 'tipo'])
            ->where('identificacion', 'LIKE', '%' . $placa . '%')
            ->orWhere('serialmotor', 'LIKE', '%' . $placa . '%')
            ->orWhere('serialchasis', 'LIKE', '%' . $placa . '%')
            ->limit(10)
            ->get();

        if ($vehiculos->count() == 1) {
            $vehiculo = $vehiculos->first();
            $ultimos_mantenimientos = CWMantenimiento::with(['productos', 'tipos']) // <-- AGREGAMOS TIPOS
            ->where('fk_vehiculo', $vehiculo->id)
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'tipo_resultado' => 'unico',
                'vehiculo' => $this->formatearVehiculo($vehiculo),
                'ultimos_mantenimientos' => $ultimos_mantenimientos
            ]);
        }
        elseif ($vehiculos->count() > 1) {
            // Múltiples vehículos encontrados
            $vehiculos_formateados = [];
            foreach ($vehiculos as $v) {
                $vehiculos_formateados[] = $this->formatearVehiculo($v);
            }

            return response()->json([
                'success' => true,
                'tipo_resultado' => 'multiple',
                'vehiculos' => $vehiculos_formateados
            ]);
        }
        else {
            // No se encontraron vehículos
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron vehículos con ese criterio de búsqueda'
            ]);
        }
    }

    private function formatearVehiculo($vehiculo)
    {
        $fotoUrl = null;
        if ($vehiculo->foto_vehiculo) {
            // Usar asset() para generar URL directa desde public
            $fotoUrl = asset($vehiculo->foto_vehiculo);
        }

        return [
            'id'             => $vehiculo->id,
            'modelo'         => $vehiculo->modelo,
            'marca'          => $vehiculo->marca,
            'year'           => $vehiculo->year,
            'identificacion' => $vehiculo->identificacion,
            'tipo'           => $vehiculo->tipo->tipo ?? 'N/A',
            'fk_tipo'        => $vehiculo->fk_tipo,
            'serialmotor'    => $vehiculo->serialmotor,
            'serialchasis'   => $vehiculo->serialchasis,
            'observaciones'  => $vehiculo->observaciones,
            'foto_vehiculo'  => $vehiculo->foto_vehiculo,
            'foto_url'       => $fotoUrl,
            'cliente' => [
                'codclie'  => $vehiculo->cliente->codclie,
                'nombre'   => $vehiculo->cliente->descrip,
                'cedula'   => $vehiculo->cliente->id3,
                'telefono' => $vehiculo->cliente->telef ?? $vehiculo->cliente->movil,
                'email'    => $vehiculo->cliente->email
            ]
        ];
    }

    public function obtenerDetallesVehiculo(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|integer|exists:cwvehiculo,id'
        ]);

        $vehiculo = CWVehiculo::with(['cliente', 'tipo'])
            ->where('id', $request->vehiculo_id)
            ->first();

        if ($vehiculo) {
            $ultimos_mantenimientos = CWMantenimiento::with(['productos', 'tipos']) // <-- AGREGAMOS TIPOS
            ->where('fk_vehiculo', $vehiculo->id)
                ->orderBy('fecha_mantenimiento', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success'  => true,
                'vehiculo' => $this->formatearVehiculo($vehiculo),
                'ultimos_mantenimientos' => $ultimos_mantenimientos
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vehículo no encontrado'
        ]);
    }

    public function buscarCliente(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|max:20'
        ]);

        $cliente = Saclie::where('id3', $request->cedula)
            ->orWhere('codclie', $request->cedula)
            ->orWhere('id3', 'V'.$request->cedula)
            ->orWhere('id3', 'v'.$request->cedula)
            ->orWhere('id3', 'v-'.$request->cedula)
            ->orWhere('id3', 'V-'.$request->cedula)
            ->first();

        if ($cliente) {
            return response()->json([
                'success' => true,
                'cliente' => [
                    'codclie'  => $cliente->codclie,
                    'nombre'   => $cliente->descrip,
                    'cedula'   => $cliente->id3,
                    'telefono' => $cliente->telef ?? $cliente->movil,
                    'email'    => $cliente->email
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cliente no encontrado'
        ]);
    }

    public function crearCliente(Request $request)
    {
        $request->validate([
            'cedula'   => 'required|string|max:20',
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100'
        ]);

        // Verificar si el cliente ya existe por cédula
        $cliente = Saclie::where('id3', $request->cedula)->first();

        if (!$cliente) {
            $cliente          = new Saclie();
            $cliente->codclie = $request->cedula;
            $cliente->id3     = $request->cedula;
            $cliente->descrip = $request->nombre;
            $cliente->telef   = $request->telefono;
            $cliente->movil   = $request->telefono;
            $cliente->email   = $request->email;
            $cliente->activo  = 1;
            $cliente->tipopvp = 1; // Precio por defecto
            $cliente->save();

            // Crear usuario para el cliente
            $this->crearUsuarioCliente($cliente, $request->email);
        }

        return response()->json([
            'success' => true,
            'cliente' => [
                'codclie'  => $cliente->codclie,
                'nombre'   => $cliente->descrip,
                'cedula'   => $cliente->id3,
                'telefono' => $cliente->telef ?? $cliente->movil,
                'email'    => $cliente->email
            ]
        ]);
    }

    public function buscarProductos(Request $request)
    {
        $request->validate([
            'busqueda' => 'required|string|min:2'
        ]);

        $busqueda = $request->busqueda;
        $busqueda = str_replace("\"", "", $busqueda);
        $busqueda = str_replace("'", "", $busqueda);
        $busqueda = str_replace("*", " ", $busqueda);
        $vector = explode(" ", $busqueda);

        if ($vector) {
            $numerito = 0;
            $cadena   = '';
            foreach ($vector as $value) {
                if ($numerito > 0) {
                    $cadena  .= ' AND ';
                }
                $cadena  .= "(codprod like '%$value%' or descrip like '%$value%' or refere like '%$value%' or marca like '%$value%' or descrip2 like '%$value%')";
                $numerito++;
            }
        }

        $productos = \App\Models\Saprod::where('activo', 1)
            ->whereRaw($cadena)
            ->limit(10)
            ->get(['codprod', 'descrip', 'refere']);

        return response()->json([
            'success' => true,
            'productos' => $productos
        ]);
    }

    public function crearVehiculo(Request $request)
    {
        $request->validate([
            'codclie'        => 'required|string|max:50',
            'fk_tipo'        => 'required|integer',
            'modelo'         => 'required|string|max:50',
            'marca'          => 'required|string|max:50',
            'identificacion' => 'required|string|max:50|unique:cwvehiculo,identificacion',
            'year'           => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'serialmotor'    => 'nullable|string|max:100',
            'serialchasis'   => 'nullable|string|max:100',
            'observaciones'  => 'nullable|string|max:200'
        ]);

        $vehiculo = CWVehiculo::create([
            'codclie'        => $request->codclie,
            'fk_tipo'        => $request->fk_tipo,
            'modelo'         => $request->modelo,
            'marca'          => $request->marca,
            'identificacion' => $request->identificacion,
            'year'           => $request->year,
            'serialmotor'    => $request->serialmotor,
            'serialchasis'   => $request->serialchasis,
            'observaciones'  => $request->observaciones
        ]);

        return response()->json([
            'success'  => true,
            'vehiculo' => [
                'id'             => $vehiculo->id,
                'modelo'         => $vehiculo->modelo,
                'marca'          => $vehiculo->marca,
                'identificacion' => $vehiculo->identificacion
            ]
        ]);
    }

    public function guardar(Request $request)
    {
        // Para manejar FormData con archivos, necesitamos obtener los datos así
        $data = $request->all();

        $validator = Validator::make($data, [
            'fk_vehiculo'                => 'required|integer|exists:cwvehiculo,id',
            'fecha_mantenimiento'        => 'required|date',
            'hora_mantenimiento'         => 'nullable|string',
            'tipos_mantenimiento'        => 'required|array|min:1',
            'tipos_mantenimiento.*'      => 'required|string',
            'kilometraje'                => 'nullable|integer',
            'observaciones'              => 'nullable|string',
            'fotos.*'                    => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'proximo_mantenimiento'      => 'nullable|date',
            'proximo_kilometraje'        => 'nullable|integer',
            'codvend'                    => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $vehiculo = CWVehiculo::findOrFail($data['fk_vehiculo']);

            $mantenimientoData = [
                'codclie'               => $vehiculo->codclie,
                'fk_vehiculo'           => $data['fk_vehiculo'],
                'fecha_mantenimiento'   => $data['fecha_mantenimiento'],
                'hora_mantenimiento'    => $data['hora_mantenimiento'] ?? date('H:i:s'),
                'tipo_mantenimiento'    => 'multiple',
                'kilometraje'           => $data['kilometraje'] ?? null,
                'observaciones'         => $data['observaciones'] ?? null,
                'proximo_mantenimiento' => $data['proximo_mantenimiento'] ?? null,
                'proximo_kilometraje'   => $data['proximo_kilometraje'] ?? null,
                'realizado_por'         => Auth::id(),
                'codvend'               => $data['codvend'] ?? ''
            ];

            $mantenimiento = CWMantenimiento::create($mantenimientoData);

            $token = \Illuminate\Support\Str::random(64);
            $mantenimiento->token_cliente = $token;
            $mantenimiento->save();

            // ============ GUARDAR MÚLTIPLES TIPOS ============
            $tiposMantenimiento = is_array($data['tipos_mantenimiento']) ? $data['tipos_mantenimiento'] : json_decode($data['tipos_mantenimiento'], true);

            foreach ($tiposMantenimiento as $tipo) {
                $descripcion = null;
                if ($tipo === 'otros' and isset($data['otro_tipo_descripcion'])) {
                    $descripcion = $data['otro_tipo_descripcion'];
                }

                CwMantenimientoTipo::create([
                    'mantenimiento_id' => $mantenimiento->id,
                    'tipo' => $tipo,
                    'descripcion' => $descripcion
                ]);
            }

            // Guardar productos
            if (isset($data['productos'])) {
                $prods = json_decode($data['productos']);

                foreach ($prods as $p) {
                    Cwmantenimientoproducto::create([
                        'mantenimiento_id' => $mantenimiento->id,
                        'codprod'     => $p->codprod ?? null,
                        'descripcion' => $p->descripcion,
                        'referencia'  => $p->referencia ?? null,
                        'cantidad'    => $p->cantidad ?? 1,
                        'tipo'        => $p->tipo ?? 'producto'
                    ]);
                }
            }

            // ============ PROCESAR FOTOS DE EVIDENCIA (guardar en public/mantenimientos) ============
            $fotosSubidas = 0;

            if ($request->hasFile('fotos')) {
                $fotos = $request->file('fotos');

                // Crear directorio en public/mantenimientos
                $directorio = public_path('mantenimientos/' . $mantenimiento->id);
                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }

                foreach ($fotos as $index => $foto) {
                    // Validar cada foto individualmente
                    $validator = Validator::make(
                        ['foto' => $foto],
                        ['foto' => 'image|mimes:jpeg,png,jpg|max:5120']
                    );

                    if ($validator->fails()) {
                        continue; // Saltar fotos inválidas
                    }

                    // Obtener tipo y descripción de la foto
                    $tipoFoto = $request->input('tipo_foto_' . $index, 'general');
                    $descripcionFoto = $request->input('descripcion_foto_' . $index, '');

                    // Generar nombre único para el archivo
                    $nombreOriginal = $foto->getClientOriginalName();
                    $nombreArchivo = time() . '_' . $index . '_' . uniqid() . '.jpg';
                    $rutaRelativa = 'mantenimientos/' . $mantenimiento->id . '/' . $nombreArchivo;
                    $rutaCompleta = public_path($rutaRelativa);

                    try {
                        // Crear manager de imágenes
                        $manager = new ImageManager();

                        // Leer la imagen
                        $imagen = $manager->make($foto->getPathname());
                        $imagen = $this->corregirOrientacionImagen1($imagen, $foto->getPathname());

                        // Redimensionar si es muy grande (max 1200px)
                        if ($imagen->width() > 1200) {
                            $imagen->resize(1200, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }

                        // Comprimir y guardar como JPG con calidad 75%
                        $imagen->save($rutaCompleta, 75);

                        // Guardar en la base de datos
                        Cwmantenimientofoto::create([
                            'mantenimiento_id' => $mantenimiento->id,
                            'ruta_foto'        => $rutaRelativa,
                            'nombre_original'  => $nombreOriginal,
                            'tipo_evidencia'   => $tipoFoto,
                            'descripcion'      => $descripcionFoto,
                            'orden'            => $index
                        ]);

                        $fotosSubidas++;
                        Log::info('Foto de evidencia guardada en: ' . $rutaCompleta);

                    } catch (\Exception $e) {
                        Log::error('Error al procesar foto de evidencia: ' . $e->getMessage());

                        // Fallback: guardar la foto original sin comprimir
                        $extension = $foto->getClientOriginalExtension();
                        $nombreArchivoFallback = time() . '_' . $index . '_' . uniqid() . '.' . $extension;
                        $rutaFallback = 'mantenimientos/' . $mantenimiento->id . '/' . $nombreArchivoFallback;
                        $rutaCompletaFallback = public_path($rutaFallback);

                        $foto->move($directorio, $nombreArchivoFallback);

                        Cwmantenimientofoto::create([
                            'mantenimiento_id' => $mantenimiento->id,
                            'ruta_foto'        => $rutaFallback,
                            'nombre_original'  => $nombreOriginal,
                            'tipo_evidencia'   => $tipoFoto,
                            'descripcion'      => $descripcionFoto,
                            'orden'            => $index
                        ]);

                        $fotosSubidas++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success'           => true,
                'message'           => 'Mantenimiento guardado exitosamente',
                'mantenimiento_id'  => $mantenimiento->id,
                'token_cliente'     => $token,
                'url_cliente'       => route('cliente.mantenimiento.ver', $token),
                'fotos_subidas'     => $fotosSubidas,
                'tipos_guardados'   => count($tiposMantenimiento),
                'route'             => route('clientes.vehiculos.mantenimientos.show', [
                        $vehiculo->codclie,
                        $vehiculo->id,
                        $mantenimiento->id
                    ]) . '?fresh=' . time() . '&nocache=' . rand(1000, 9999)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error en guardar mantenimiento: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener marcas para autocompletado predictivo
     */
    public function obtenerMarcas(Request $request)
    {
        $marcas = CWVehiculo::select('marca', DB::raw('COUNT(*) as vehiculos_count'))
            ->whereNotNull('marca')
            ->where('marca', '!=', '')
            ->groupBy('marca')
            ->orderBy('marca')
            ->limit(1000)
            ->get();

        return response()->json([
            'success' => true,
            'marcas' => $marcas
        ]);
    }

    /**
     * Obtener modelos para autocompletado predictivo
     */
    public function obtenerModelos(Request $request)
    {
        $modelos = CWVehiculo::select('modelo', 'marca')
            ->whereNotNull('modelo')
            ->where('modelo', '!=', '')
            ->orderBy('modelo')
            ->groupByRaw('modelo, marca')
            ->get();

        return response()->json([
            'success' => true,
            'modelos' => $modelos
        ]);
    }

    /**
     * NUEVO MÉTODO: Notificar al cliente
     */
    public function notificarCliente(Request $request)
    {
        $request->validate([
            'mantenimientoId' => 'required|integer|exists:cwmantenimientos,id'
        ]);

        $mantenimiento = CWMantenimiento::with(['vehiculo', 'vehiculo.cliente'])
            ->findOrFail($request->mantenimientoId);

        // Generar token si no existe
        if (!$mantenimiento->token_cliente) {
            $mantenimiento->token_cliente = \Illuminate\Support\Str::random(64);
            $mantenimiento->save();
        }

        $url = route('cliente.mantenimiento.ver', $mantenimiento->token_cliente);

        return response()->json([
            'success' => true,
            'message' => 'Enlace generado correctamente',
            'url' => $url,
            'token' => $mantenimiento->token_cliente
        ]);
    }

    private function crearUsuarioCliente($cliente, $email)
    {
        if ($email) {
            $usuario = User::where('email', $email)->first();
            if (!$usuario) {
                User::create([
                    'first_name' => $cliente->descrip,
                    'last_name'  => '',
                    'type'       => 'cliente',
                    'codclie'    => $cliente->codclie,
                    'email'      => $email,
                    'password'   => Hash::make($cliente->codclie) // Contraseña = cédula
                ]);
            }
        }
    }

    public function actualizarCliente(Request $request)
    {
        $request->validate([
            'codclie'  => 'required|string|max:50',
            'cedula'   => 'required|string|max:20',
            'nombre'   => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:100'
        ]);

        $cliente = Saclie::where('codclie', $request->codclie)->first();

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        // Verificar si la cédula ya existe en otro cliente
        $existente = Saclie::where('id3', $request->cedula)
            ->where('codclie', '!=', $request->codclie)
            ->first();

        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'La cédula/RIF ya está registrada en otro cliente'
            ], 422);
        }

        $cliente->id3     = $request->cedula;
        $cliente->descrip = $request->nombre;
        $cliente->telef   = $request->telefono;
        $cliente->movil   = $request->telefono;
        $cliente->email   = $request->email;
        $cliente->save();

        return response()->json([
            'success' => true,
            'cliente' => [
                'codclie'  => $cliente->codclie,
                'nombre'   => $cliente->descrip,
                'cedula'   => $cliente->id3,
                'telefono' => $cliente->telef ?? $cliente->movil,
                'email'    => $cliente->email
            ]
        ]);
    }

    public function actualizarVehiculo(Request $request)
    {
        $request->validate([
            'id'             => 'required|integer|exists:cwvehiculo',
            'fk_tipo'        => 'required|integer|exists:cwtipovehiculo,id',
            'marca'          => 'required|string|max:50',
            'modelo'         => 'required|string|max:50',
            'identificacion' => 'required|string|max:50',
            'year'           => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'serialmotor'    => 'nullable|string|max:100',
            'serialchasis'   => 'nullable|string|max:100',
            'observaciones'  => 'nullable|string|max:200'
        ]);

        $vehiculo = CWVehiculo::findOrFail($request->id);

        // Verificar si la placa ya existe en otro vehículo
        $existente = CWVehiculo::where('identificacion', $request->identificacion)
            ->where('id', '!=', $request->id)
            ->first();

        if ($existente) {
            return response()->json([
                'success' => false,
                'message' => 'La placa/identificación ya está registrada en otro vehículo'
            ], 422);
        }

        $vehiculo->update([
            'fk_tipo'        => $request->fk_tipo,
            'marca'          => $request->marca,
            'modelo'         => $request->modelo,
            'identificacion' => $request->identificacion,
            'year'           => $request->year,
            'serialmotor'    => $request->serialmotor,
            'serialchasis'   => $request->serialchasis,
            'observaciones'  => $request->observaciones
        ]);

        // Cargar relaciones para respuesta
        $vehiculo->load('tipo');

        return response()->json([
            'success' => true,
            'vehiculo' => [
                'id'             => $vehiculo->id,
                'fk_tipo'        => $vehiculo->fk_tipo,
                'tipo'           => $vehiculo->tipo->tipo ?? 'N/A',
                'marca'          => $vehiculo->marca,
                'modelo'         => $vehiculo->modelo,
                'identificacion' => $vehiculo->identificacion,
                'year'           => $vehiculo->year,
                'serialmotor'    => $vehiculo->serialmotor,
                'serialchasis'   => $vehiculo->serialchasis,
                'observaciones'  => $vehiculo->observaciones
            ]
        ]);
    }

    private function corregirOrientacionImagen1($imagen, $rutaOriginal)
    {
        if (!function_exists('exif_read_data')) {
            return $imagen;
        }

        try {
            $exif = @exif_read_data($rutaOriginal);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];

                // Log para debug
                Log::info('Orientación EXIF detectada: ' . $orientation . ' para archivo: ' . basename($rutaOriginal));

                switch ($orientation) {
                    case 3:
                        $imagen->rotate(180);
                        break;
                    case 6:
                        $imagen->rotate(-90);
                        break;
                    case 8:
                        $imagen->rotate(90);
                        break;
                    case 2: // espejo horizontal
                        $imagen->flip('h');
                        break;
                    case 4: // espejo vertical
                        $imagen->flip('v');
                        break;
                    case 5: // espejo horizontal + rotar 90 CCW
                        $imagen->flip('h');
                        $imagen->rotate(90);
                        break;
                    case 7: // espejo horizontal + rotar 90 CW
                        $imagen->flip('h');
                        $imagen->rotate(-90);
                        break;
                }
            } else {
                Log::info('No se encontró orientación EXIF en: ' . basename($rutaOriginal));
            }
        } catch (\Exception $e) {
            Log::warning('Error al leer EXIF: ' . $e->getMessage());
        }

        return $imagen;
    }

    private function corregirOrientacionImagen($imagen, $rutaOriginal)
    {
        if (!function_exists('exif_read_data')) {
            return $imagen;
        }

        try {
            $exif = @exif_read_data($rutaOriginal);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];

                // Log para debug
                Log::info('Orientación EXIF detectada: ' . $orientation . ' para archivo: ' . basename($rutaOriginal));

                switch ($orientation) {
                    case 3:
                        $imagen->rotate(180);
                        break;
                    case 6:
                        $imagen->rotate(0);
                        break;
                    case 8:
                        $imagen->rotate(90);
                        break;
                    case 2: // espejo horizontal
                        $imagen->flip('h');
                        break;
                    case 4: // espejo vertical
                        $imagen->flip('v');
                        break;
                    case 5: // espejo horizontal + rotar 90 CCW
                        $imagen->flip('h');
                        $imagen->rotate(90);
                        break;
                    case 7: // espejo horizontal + rotar 90 CW
                        $imagen->flip('h');
                        $imagen->rotate(-90);
                        break;
                }
            } else {
                Log::info('No se encontró orientación EXIF en: ' . basename($rutaOriginal));
            }
        } catch (\Exception $e) {
            Log::warning('Error al leer EXIF: ' . $e->getMessage());
        }

        return $imagen;
    }


    public function actualizarFotoVehiculo(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|integer|exists:cwvehiculo,id',
            'foto'        => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $vehiculo = CWVehiculo::findOrFail($request->vehiculo_id);
        $tipo = $request->input('tipo', 'foto_adicional');

        // Crear directorio en public/vehiculos
        $directorio = public_path('vehiculos/' . $vehiculo->id);
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Si es foto principal, eliminar la anterior
        if ($tipo == 'foto_principal' && $vehiculo->foto_vehiculo) {
            $rutaAnterior = public_path($vehiculo->foto_vehiculo);
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

        $foto = $request->file('foto');
        if (!$foto) {
            return response()->json([
                'success' => false,
                'message' => 'No se recibió el archivo de foto correctamente'
            ], 400);
        }

        // Generar nombre de archivo - siempre como JPG
        $nombreArchivo = $tipo . '_' . time() . '_' . uniqid() . '.jpg';
        $rutaRelativa = 'vehiculos/' . $vehiculo->id . '/' . $nombreArchivo;
        $rutaCompleta = public_path($rutaRelativa);

        try {
            // Crear manager de imágenes
            $manager = new ImageManager();

            // Leer la imagen y redimensionar
            $imagen = $manager->make($foto->getPathname());
            $imagen = $this->corregirOrientacionImagen1($imagen, $foto->getPathname());

            // Redimensionar si es más grande que 1200px
            if ($imagen->width() > 1200) {
                $imagen->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Comprimir y guardar como JPG con calidad 75%
            $imagen->save($rutaCompleta, 75);

            Log::info('Foto guardada en: ' . $rutaCompleta);

        } catch (\Exception $e) {
            Log::error('Error al procesar imagen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la imagen: ' . $e->getMessage()
            ], 500);
        }

        // Guardar en BD
        if ($tipo == 'foto_principal') {
            $vehiculo->foto_vehiculo = $rutaRelativa;
            $vehiculo->save();
            Log::info('Foto principal actualizada en BD: ' . $rutaRelativa);
        }

        // Construir URL pública
        $urlPublica = asset($rutaRelativa);

        // Listar todas las fotos
        $fotos = $this->listarFotosVehiculo($vehiculo->id);

        return response()->json([
            'success' => true,
            'message' => $tipo == 'foto_principal' ? 'Foto principal actualizada y comprimida' : 'Foto adicional guardada',
            'ruta_foto' => $rutaRelativa,
            'url_foto' => $urlPublica,
            'fotos' => $fotos,
            'es_principal' => ($tipo == 'foto_principal')
        ]);
    }


    public function eliminarFotoVehiculo(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|integer|exists:cwvehiculo,id',
            'ruta_foto' => 'required|string'
        ]);

        $vehiculo = CWVehiculo::findOrFail($request->vehiculo_id);

        // Verificar que la foto pertenece al vehículo
        if (strpos($request->ruta_foto, "vehiculos/{$vehiculo->id}/") !== 0) {
            return response()->json([
                'success' => false,
                'message' => 'La foto no pertenece a este vehículo'
            ], 403);
        }

        // Si es la foto principal, limpiar el campo
        if ($vehiculo->foto_vehiculo == $request->ruta_foto) {
            $vehiculo->foto_vehiculo = null;
            $vehiculo->save();
        }

        // Eliminar el archivo físico
        $rutaCompleta = public_path($request->ruta_foto);
        if (file_exists($rutaCompleta)) {
            unlink($rutaCompleta);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto eliminada exitosamente'
        ]);
    }

    private function listarFotosVehiculo($vehiculoId)
    {
        $directorio = public_path('vehiculos/' . $vehiculoId);
        $fotos = [];

        if (file_exists($directorio)) {
            $archivos = scandir($directorio);
            foreach ($archivos as $archivo) {
                if ($archivo != '.' && $archivo != '..') {
                    $rutaRelativa = 'vehiculos/' . $vehiculoId . '/' . $archivo;
                    $fotos[] = [
                        'ruta' => $rutaRelativa,
                        'url' => asset($rutaRelativa),
                        'nombre' => $archivo,
                        'es_principal' => (strpos($archivo, 'foto_principal') === 0)
                    ];
                }
            }
        }

        return $fotos;
    }
}
