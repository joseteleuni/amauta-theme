<?php
namespace App\Core;

use Timber\Timber;

class Setup {
    public function __construct() {
        add_action('after_setup_theme', [$this, 'theme_supports']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_filter('timber/context', [$this, 'add_to_context']);
    }

    public function theme_supports() {
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_theme_support('title-tag');
    }

    public function add_to_context($context) {
        $context['menu'] = Timber::get_menu();
        $context['site'] = $this; // Datos del sitio
        return $context;
    }
    
    public function enqueue_assets() {
        // Aquí cargaremos el CSS/JS compilado por Vite
        // Usaremos un helper para leer el manifest.json de Vite
    }
}