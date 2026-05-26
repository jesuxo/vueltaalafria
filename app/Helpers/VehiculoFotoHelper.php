<?php
// app/Helpers/VehiculoFotoHelper.php
namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class VehiculoFotoHelper
{
    /**
     * Obtener la ruta del directorio del vehículo
     */
    public static function getDirectorio($vehiculoId)
    {
        return "vehiculos/{$vehiculoId}";
    }

    /**
     * Generar nombre de archivo único
     */
    public static function generarNombreArchivo($tipo = 'foto')
    {
        return $tipo . '_' . time() . '.jpg';
    }

    /**
     * Obtener la URL pública de la foto
     */
    public static function getUrl($ruta)
    {
        if (!$ruta) {
            return asset('storage/vehiculos/default/logo-light.png');
        }
        // CORREGIDO: Quitar 'public/' de la ruta
        return asset('storage/public/' . $ruta);
    }

    /**
     * Eliminar foto anterior si existe
     */
    public static function eliminarFoto($ruta)
    {
        if ($ruta && Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
            return true;
        }
        return false;
    }

    /**
     * Listar todas las fotos de un vehículo
     */
    public static function listarFotos($vehiculoId)
    {
        $directorio = self::getDirectorio($vehiculoId);

        if (!Storage::disk('public')->exists($directorio)) {
            return [];
        }

        $archivos = Storage::disk('public')->files($directorio);
        $fotos = [];

        foreach ($archivos as $archivo) {
            $fotos[] = [
                'ruta'   => $archivo,
                // CORREGIDO: Quitar 'public/' de la URL
                'url'    => asset('storage/public/' . $archivo),
                'nombre' => basename($archivo),
                'fecha'  => Storage::disk('public')->lastModified($archivo)
            ];
        }

        // Ordenar por fecha (más reciente primero)
        usort($fotos, function($a, $b) {
            return $b['fecha'] - $a['fecha'];
        });

        return $fotos;
    }
}
