<?php
/**
 * functions.php
 * Entry point for the theme logic.
 */

// 1. Cargar el Autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// 2. Inicializar Timber
Timber\Timber::init();

// 3. Configurar directorios de vistas
Timber::$dirname = ['views'];

// 4. Cargar nuestra clase de configuración (Bootstrapping)
// Asumiendo que crearemos esta clase para organizar los hooks
new App\Core\Setup();
new App\Core\Vite();