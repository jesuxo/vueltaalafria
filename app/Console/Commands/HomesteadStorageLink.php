<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class HomesteadStorageLink extends Command
{
    protected $signature = 'homestead:storage-link';
    protected $description = 'Create storage link for Homestead environment';

    public function handle()
    {
        $this->info('Creating storage link for Homestead...');

        $source = storage_path('app/public');
        $link = public_path('storage');

        // Verificar si estamos en Homestead
        if (!file_exists('/home/vagrant')) {
            $this->error('This command should only run in Homestead');
            return 1;
        }

        // Crear directorios si no existen
        if (!File::exists($source)) {
            File::makeDirectory($source, 0755, true);
            $this->info("Created source: {$source}");
        }

        // Eliminar si existe
        if (File::exists($link)) {
            if (is_link($link) || File::isDirectory($link)) {
                File::deleteDirectory($link);
                $this->info("Removed existing: {$link}");
            }
        }

        // Intentar crear enlace
        try {
            // Ejecutar comando ln directamente
            $command = sprintf('ln -s %s %s', $source, $link);
            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                $this->info('Storage link created successfully!');
                return 0;
            } else {
                // Fallback: copiar archivos
                $this->warn('Symlink failed. Falling back to copy...');
                File::copyDirectory($source, $link);
                $this->info('Files copied successfully!');
                return 0;
            }
        } catch (\Exception $e) {
            $this->error('Failed: ' . $e->getMessage());
            return 1;
        }
    }
}
