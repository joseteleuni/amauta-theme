<?php
namespace App\Core;

use Timber\Timber;

/**
 * Class Setup
 * Responsabilidad: Configuración del Tema y Contexto Global de Twig.
 * (Ya no carga estilos, de eso se encarga Vite.php)
 */
class Setup {

    public function __construct() {
        // 1. Configuración básica al iniciar el tema
        add_action('after_setup_theme', [$this, 'theme_supports']);
        
        // 2. Registrar menús de navegación
        add_action('init', [$this, 'register_menus']);

        // 3. Pasar datos globales a las vistas .twig
        add_filter('timber/context', [$this, 'add_to_context']);
    }

    /**
     * Define qué características de WordPress soporta este tema.
     */
    public function theme_supports() {
        // SEO: Deja que WP maneje el <title> del sitio
        add_theme_support('title-tag');

        // Imágenes: Habilita thumbnails en posts
        add_theme_support('post-thumbnails');

        // Menús: Habilita el gestor de menús
        add_theme_support('menus');

        // HTML5: Formularios y galerías con marcado moderno
        add_theme_support('html5', [
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script'
        ]);

        // Logo personalizable desde el personalizador
        add_theme_support('custom-logo', [
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ]);
    }

    /**
     * Registra las zonas de menús.
     */
    public function register_menus() {
        register_nav_menus([
            'primary_menu' => __('Menú Principal', 'mi-tema'),
            'footer_menu'  => __('Menú Footer', 'mi-tema'),
        ]);
    }

    /**
     * Variables globales disponibles en TODOS los archivos .twig
     */
    public function add_to_context($context) {
        // Menú principal disponible como {{ menu }}
        $context['menu'] = Timber::get_menu('primary_menu');
        
        // Datos del sitio (nombre, descripción, url) como {{ site }}
        $context['site'] = $this;
        
        // Logo (si existe)
        $custom_logo_id = get_theme_mod('custom_logo');
        $context['logo'] = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : false;

        return $context;
    }
}