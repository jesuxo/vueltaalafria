<?php

namespace App\Console\Commands;

use App\Models\Saclie;
use App\Models\CWVehiculo;
use App\Models\CWTipoVehiculo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportarVehiculosDesdeCSV extends Command
{
    protected $signature = 'importar:vehiculos {archivo? : Ruta del archivo CSV a importar}';
    protected $description = 'Importar vehículos desde archivo CSV';

    protected $tiposCache = [];
    protected $stats = [
        'clientes_nuevos' => 0,
        'clientes_existentes' => 0,
        'vehiculos_nuevos' => 0,
        'vehiculos_omitidos' => 0,
        'errores' => 0
    ];

    public function handle()
    {
        $this->info('🚗 INICIO DE IMPORTACIÓN DE VEHÍCULOS');
        $this->newLine();

        // Determinar archivo a importar
        $archivo = $this->argument('archivo');

        if (!$archivo) {
            // Buscar archivos CSV en storage
            $archivos = glob(storage_path('*.{csv,xls,xlsx}'), GLOB_BRACE);

            if (empty($archivos)) {
                $this->error('❌ No se encontraron archivos CSV en ' . storage_path());
                $this->info('Por favor, especifica la ruta del archivo:');
                $this->info('php artisan importar:vehiculos /ruta/completa/del/archivo.csv');
                return 1;
            }

            // Mostrar archivos disponibles
            $this->info('Archivos disponibles:');
            foreach ($archivos as $index => $archivoPath) {
                $this->line('  [' . ($index + 1) . '] ' . basename($archivoPath));
            }

            $opcion = $this->ask('Selecciona el archivo a importar (número)', 1);

            if (is_numeric($opcion) && isset($archivos[$opcion - 1])) {
                $archivo = $archivos[$opcion - 1];
            } else {
                $this->error('❌ Opción inválida');
                return 1;
            }
        }

        if (!file_exists($archivo)) {
            $this->error("❌ El archivo no existe: {$archivo}");
            return 1;
        }

        $this->info("📁 Procesando archivo: " . basename($archivo));

        // Procesar el archivo
        $this->procesarArchivo($archivo);

        // Mostrar resumen
        $this->mostrarResumen();

        return 0;
    }

    protected function procesarArchivo($archivo)
    {
        $handle = fopen($archivo, 'r');
        if (!$handle) {
            $this->error('❌ No se pudo abrir el archivo');
            return;
        }

        // Leer encabezados (primera línea)
        $headers = fgetcsv($handle, 0, ';');

        // Limpiar BOM del primer encabezado si existe
        if (isset($headers[0])) {
            $headers[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]);
        }

        $this->info('📋 Columnas detectadas:');
        foreach ($headers as $i => $header) {
            $this->line("  [{$i}] {$header}");
        }
        $this->newLine();

        // Índices de columnas basados en el CSV
        $idx = [
            'marca' => 0,
            'modelo' => 1,
            'anio' => 2,
            'placa' => 3,
            'color' => 4,
            'tipo_vehiculo' => 5,
            'ultimo_aceite' => 7,
            'combustible' => 11,
            'cedula' => 12,
            'nombre' => 13,
            'apellido' => 14,
            'telefono' => 15,
            'email' => 16
        ];

        DB::beginTransaction();

        try {
            $linea = 1;
            while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
                $linea++;

                // Saltar líneas vacías
                if (empty(array_filter($data))) {
                    continue;
                }

                $this->procesarLinea($data, $idx, $linea);
            }

            DB::commit();
            fclose($handle);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            $this->error('❌ Error durante la importación: ' . $e->getMessage());
            Log::error('Error importando vehículos: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function procesarLinea($data, $idx, $linea)
    {
        // Extraer datos
        $cedula = trim($data[$idx['cedula']] ?? '');
        $nombre = trim($data[$idx['nombre']] ?? '');
        $apellido = trim($data[$idx['apellido']] ?? '');
        $marca = trim($data[$idx['marca']] ?? '');
        $modelo = trim($data[$idx['modelo']] ?? '');
        $placa = trim($data[$idx['placa']] ?? '');
        $anio = trim($data[$idx['anio']] ?? '');
        $telefono = trim($data[$idx['telefono']] ?? '');
        $email = trim($data[$idx['email']] ?? '');
        $tipoTexto = trim($data[$idx['tipo_vehiculo']] ?? '');
        $color = trim($data[$idx['color']] ?? '');
        $ultimoAceite = trim($data[$idx['ultimo_aceite']] ?? '');
        $combustible = trim($data[$idx['combustible']] ?? '');

        // Validar datos mínimos
        if (empty($cedula) || empty($nombre) || empty($marca) || empty($modelo) || empty($placa)) {
            $this->warn("⚠️ Línea {$linea}: Datos incompletos, se omite");
            $this->stats['vehiculos_omitidos']++;
            return;
        }

        // Limpiar cédula (quitar espacios, convertir a mayúsculas)
        $cedula = strtoupper(preg_replace('/\s+/', '', $cedula));

        // Buscar o crear cliente
        $cliente = $this->buscarOCrearCliente($cedula, $nombre, $apellido, $telefono, $email, $linea);

        if (!$cliente) {
            $this->stats['errores']++;
            return;
        }

        // Verificar si el vehículo ya existe por placa
        $vehiculoExistente = CWVehiculo::where('identificacion', $placa)->first();

        if ($vehiculoExistente) {
            $this->warn("⚠️ Línea {$linea}: Vehículo con placa {$placa} ya existe (ID: {$vehiculoExistente->id})");
            $this->stats['vehiculos_omitidos']++;
            return;
        }

        // Determinar tipo de vehículo
        $fkTipo = $this->determinarTipoVehiculo($tipoTexto);

        // Limpiar año
        $anio = preg_replace('/[^0-9]/', '', $anio);
        if (empty($anio) || $anio < 1900 || $anio > 2100) {
            $anio = null;
        }

        // Crear observaciones con datos adicionales
        $observaciones = "Color: {$color} - Último aceite: {$ultimoAceite} - Combustible: {$combustible}";
        if (strlen($observaciones) > 200) {
            $observaciones = substr($observaciones, 0, 197) . '...';
        }

        // Crear vehículo
        try {
            $vehiculo = CWVehiculo::create([
                'codclie' => $cliente->codclie,
                'fk_tipo' => $fkTipo,
                'modelo' => $modelo,
                'marca' => $marca,
                'identificacion' => $placa,
                'year' => $anio,
                'observaciones' => $observaciones,
                'serialchasis' => null,
                'serialmotor' => null,
                'foto_vehiculo' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->info("✅ Línea {$linea}: Vehículo creado - {$marca} {$modelo} ({$placa}) para {$cliente->descrip}");
            $this->stats['vehiculos_nuevos']++;

        } catch (\Exception $e) {
            $this->error("❌ Línea {$linea}: Error al crear vehículo - " . $e->getMessage());
            $this->stats['errores']++;
            Log::error('Error creando vehículo', [
                'linea' => $linea,
                'error' => $e->getMessage(),
                'data' => compact('cedula', 'marca', 'modelo', 'placa')
            ]);
        }
    }

    protected function buscarOCrearCliente($cedula, $nombre, $apellido, $telefono, $email, $linea)
    {
        // Limpiar cédula: eliminar puntos y espacios, pero mantener letras (V, E, J)
        $cedulaLimpia = preg_replace('/[.\s]/', '', $cedula);
        $cedulaLimpia = strtoupper(trim($cedulaLimpia));

        // Si está vacía después de limpiar, error
        if (empty($cedulaLimpia)) {
            $this->error("❌ Línea {$linea}: Cédula vacía después de limpiar");
            return null;
        }

        $this->info("🔍 Línea {$linea}: Buscando cliente con cédula '{$cedulaLimpia}'");

        // ===== PASO 1: GENERAR TODOS LOS POSIBLES FORMATOS DE BÚSQUEDA =====
        $numeros = preg_replace('/[^0-9]/', '', $cedulaLimpia);

        $formatosBusqueda = [
            $cedulaLimpia,                          // Formato original (ej: V17930073)
            $numeros,                                // Solo números (ej: 17930073)
            'V' . $numeros,                          // V + números (ej: V17930073)
            'E' . $numeros,                          // E + números
            'J' . $numeros,                          // J + números
            'V-' . $numeros,                         // V- + números
            'E-' . $numeros,                         // E- + números
            'J-' . $numeros,                         // J- + números
        ];

        // Si ya tiene prefijo, también buscar sin él
        if (preg_match('/^[VEJ]/i', $cedulaLimpia)) {
            $sinPrefijo = preg_replace('/^[VEJ]-?/i', '', $cedulaLimpia);
            $formatosBusqueda[] = $sinPrefijo;
        }

        // Eliminar duplicados
        $formatosBusqueda = array_unique($formatosBusqueda);

        // ===== PASO 2: BUSCAR EN TODOS LOS FORMATOS =====
        $cliente = null;
        $formatoEncontrado = null;

        foreach ($formatosBusqueda as $formato) {
            // Buscar en id3 (campo de cédula)
            $cliente = Saclie::where('id3', $formato)->first();

            if ($cliente) {
                $formatoEncontrado = "id3 = '{$formato}'";
                break;
            }

            // Buscar en codclie (código de cliente)
            $cliente = Saclie::where('codclie', $formato)->first();

            if ($cliente) {
                $formatoEncontrado = "codclie = '{$formato}'";
                break;
            }
        }

        // Si encontramos cliente, lo USAMOS INMEDIATAMENTE (sin importar el formato)
        if ($cliente) {
            $this->info("  ✅ Cliente EXISTENTE encontrado con {$formatoEncontrado}");
            $this->line("    └─ ID: {$cliente->id3}, Código: {$cliente->codclie}, Nombre: {$cliente->descrip}");
            $this->stats['clientes_existentes']++;
            return $cliente; // ← DEVOLVEMOS EL CLIENTE EXISTENTE
        }

        // ===== PASO 3: SOLO SI NO EXISTE, CREAMOS UNO NUEVO =====
        $this->info("  👤 Cliente NO encontrado en ningún formato, creando nuevo...");

        $nombreCompleto = trim($nombre . ' ' . $apellido);
        if (empty($nombreCompleto)) {
            $nombreCompleto = $nombre;
        }
        if (empty($nombreCompleto)) {
            $nombreCompleto = 'CLIENTE SIN NOMBRE';
        }

        // Limpiar teléfono
        $telefono = preg_replace('/[^0-9+]/', '', $telefono);

        try {
            // Usar SOLO números para el codclie
            $codclie = $numeros;

            // Si no tiene números, generar uno
            if (empty($codclie)) {
                $codclie = 'CLI' . time();
            }

            // Verificar si el código ya existe
            $codclieExistente = Saclie::where('codclie', $codclie)->first();

            if ($codclieExistente) {
                // Si existe, generar uno único con sufijo
                $codclie = $codclie . '_' . time();
            }

            // Crear el cliente
            $cliente = Saclie::create([
                'codclie' => $codclie,
                'id3' => $cedulaLimpia, // Guardar el formato original
                'descrip' => $nombreCompleto,
                'DescripExt' => $nombreCompleto,
                'telef' => $telefono,
                'movil' => $telefono,
                'email' => $email,
                'activo' => 1,
                'tipocli' => 1,
                'tipopvp' => 3,
                'escredito' => 0,
                'LimiteCred' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->info("  ✅ Cliente NUEVO creado: {$nombreCompleto}");
            $this->line("    └─ Cédula: {$cedulaLimpia}, Código: {$codclie}");
            $this->stats['clientes_nuevos']++;

            return $cliente;

        } catch (\Exception $e) {
            $this->error("❌ Línea {$linea}: Error al crear cliente - " . $e->getMessage());
            Log::error('Error creando cliente', [
                'linea' => $linea,
                'cedula' => $cedulaLimpia,
                'nombre' => $nombreCompleto,
                'error' => $e->getMessage()
            ]);
            $this->stats['errores']++;
            return null;
        }
    }

    protected function determinarTipoVehiculo($tipoTexto)
    {
        if (isset($this->tiposCache[$tipoTexto])) {
            return $this->tiposCache[$tipoTexto];
        }

        $tipoTexto = strtolower(trim($tipoTexto));

        // Mapeo de tipos
        $tipos = [
            'motocicleta' => 1,
            'moto' => 1,
            'carro o camioneta' => 2,
            'automovil' => 2,
            'carro' => 2,
            'camioneta' => 3,
            'camión' => 4,
            'camion' => 4,
            'autobus' => 6,
            'bus' => 6,
            'otro' => 7,
            'otros' => 7
        ];

        foreach ($tipos as $key => $id) {
            if (strpos($tipoTexto, $key) !== false) {
                $this->tiposCache[$tipoTexto] = $id;
                return $id;
            }
        }

        // Buscar en la base de datos
        $tipo = CWTipoVehiculo::whereRaw('LOWER(tipo) LIKE ?', ['%' . $tipoTexto . '%'])->first();

        if ($tipo) {
            $this->tiposCache[$tipoTexto] = $tipo->id;
            return $tipo->id;
        }

        // Por defecto "Otro"
        $this->tiposCache[$tipoTexto] = 7;
        return 7;
    }

    protected function mostrarResumen()
    {
        $this->newLine();
        $this->info('📊 RESUMEN DE IMPORTACIÓN');
        $this->line(str_repeat('=', 40));

        $this->line("👤 Clientes nuevos:      {$this->stats['clientes_nuevos']}");
        $this->line("👤 Clientes existentes:  {$this->stats['clientes_existentes']}");
        $this->line("🚗 Vehículos nuevos:     {$this->stats['vehiculos_nuevos']}");
        $this->line("⏭️  Vehículos omitidos:   {$this->stats['vehiculos_omitidos']}");
        $this->line("❌ Errores:               {$this->stats['errores']}");

        $this->newLine();

        if ($this->stats['errores'] > 0) {
            $this->warn('⚠️  La importación completó con errores. Revisa el log para más detalles.');
        } else {
            $this->info('✅ Importación completada exitosamente!');
        }
    }
}
