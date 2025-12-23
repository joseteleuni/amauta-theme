<?php
namespace App\Core;

/**
 * Class Vite
 * Se encarga AUTOMÁTICAMENTE de encolar los scripts y estilos
 * detectando si estás en Desarrollo (Local) o Producción.
 */
class Vite {

    /**
     * El Constructor: Se ejecuta solo al hacer 'new Vite()'
     */
    public function __construct() {
        // AQUÍ ESTÁ LA MAGIA:
        // Nos "enganchamos" a WordPress automáticamente.
        // No necesitas llamar a enqueue_assets() manualmente fuera de aquí.
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * La lógica real que WordPress ejecutará cuando llegue el momento.
     */
    public function enqueue_assets() {
        // 1. Detectamos el entorno
        if ($this->is_dev()) {
            // --- MODO DESARROLLO (Hot Reload) ---
            // Cargamos el cliente de Vite para que refresque el navegador solo
            wp_enqueue_script('vite-client', 'http://localhost:3000/@vite/client', [], null, true);
            
            // Cargamos tu JS principal (que inyectará el CSS automáticamente en dev)
            wp_enqueue_script('main-js', 'http://localhost:3000/src/scripts/main.js', [], null, true);
            
        } else {
            // --- MODO PRODUCCIÓN (Archivos compilados) ---
            $manifest = $this->get_manifest();
            
            if (!$manifest) return;

            // Cargar JS minificado
            if (isset($manifest['src/scripts/main.js']['file'])) {
                $js_file = $manifest['src/scripts/main.js']['file'];
                wp_enqueue_script('main-js', get_theme_file_uri('dist/' . $js_file), [], null, true);
            }

            // Cargar CSS minificado
            // (Vite a veces agrupa el CSS bajo la entrada del JS)
            $css_file = null;
            if (isset($manifest['src/styles/main.scss']['file'])) {
                $css_file = $manifest['src/styles/main.scss']['file'];
            } elseif (isset($manifest['src/scripts/main.js']['css'][0])) {
                $css_file = $manifest['src/scripts/main.js']['css'][0];
            }

            if ($css_file) {
                wp_enqueue_style('main-css', get_theme_file_uri('dist/' . $css_file), [], null);
            }
        }
    }

    /**
     * Helper para saber si estamos en local
     */
    private function is_dev() {
        // Si estamos en localhost y el puerto 3000 responde (simplificado)
        return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
    }

    /**
     * Helper para leer el archivo manifest.json
     */
    private function get_manifest() {
        // Vite 5 suele guardar el manifest en dist/.vite/manifest.json
        $path = get_theme_file_path('dist/.vite/manifest.json');
        
        if (!file_exists($path)) {
            // Fallback para versiones anteriores
            $path = get_theme_file_path('dist/manifest.json');
        }

        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return null;
    }
}