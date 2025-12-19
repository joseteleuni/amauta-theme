<?php
namespace App\Core;

use Timber\Timber;

/**
 * Class Setup
 * Se encarga de la configuración inicial del tema (Soporte, Menús, Contexto Global).
 */
class Setup {

    public function __construct() {
        // 1. Hook para configuraciones que deben correr al iniciar el tema
        add_action('after_setup_theme', [$this, 'theme_supports']);
        
        // 2. Hook para registrar menús (opcional, si no lo haces en theme_supports)
        add_action('init', [$this, 'register_menus']);

        // 3. Hook para pasar datos globales a todos los archivos Twig
        add_filter('timber/context', [$this, 'add_to_context']);
    }

    /**
     * Define qué características de WordPress soporta este tema.
     */
    public function theme_supports() {
        // Permite que WP gestione la etiqueta <title> automáticamente
        add_theme_support('title-tag');

        // Habilita imágenes destacadas en posts y páginas
        add_theme_support('post-thumbnails');

        // Soporte para menús
        add_theme_support('menus');

        // Soporte para HTML5 en formularios y galerías
        add_theme_support('html5', [
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script'
        ]);

        // Soporte para logo personalizado
        add_theme_support('custom-logo', [
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ]);
    }

    /**
     * Registra las ubicaciones de los menús.
     */
    public function register_menus() {
        register_nav_menus([
            'primary_menu' => __('Menú Principal', 'mi-tema'),
            'footer_menu'  => __('Menú Footer', 'mi-tema'),
        ]);
    }

    /**
     * Inyecta datos globales a Twig.
     * Todo lo que definas aquí estará disponible en CUALQUIER archivo .twig
     * sin necesidad de pasarlo desde el controlador.
     * * @param array $context El contexto original de Timber.
     * @return array El contexto modificado.
     */
    public function add_to_context($context) {
        // Añadimos el menú principal al contexto global
        $context['menu'] = Timber::get_menu('primary_menu');
        
        // Información general del sitio
        $context['site'] = $this; // 'site' ya viene por defecto, pero a veces es útil personalizarlo
        
        // Ejemplo: Variable global para el año actual (útil para el footer)
        $context['now'] = date('Y');

        // Ejemplo: Logotipo del sitio
        $custom_logo_id = get_theme_mod('custom_logo');
        $context['logo'] = $custom_logo_id ? wp_get_attachment_image_url($custom_logo_id, 'full') : false;

        return $context;
    }
}