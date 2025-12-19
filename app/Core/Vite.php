<?php
namespace App\Core;

class Vite {

    /**
     * Entry point principal de tus scripts/estilos en Vite
     */
    private $entry_script = 'src/scripts/main.js';
    private $entry_style  = 'src/styles/main.scss';

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets() {
        if ($this->is_dev()) {
            // --- MODO DESARROLLO ---
            // Cargamos el cliente de Vite para el Hot Reload
            wp_enqueue_script('vite-client', 'http://localhost:3000/@vite/client', [], null, true);
            
            // Cargamos nuestro script principal desde el servidor de desarrollo
            wp_enqueue_script('main-js', 'http://localhost:3000/' . $this->entry_script, [], null, true);
            
            // En dev, el CSS se inyecta via JS, pero podemos cargar el SCSS si queremos
            wp_enqueue_style('main-css', 'http://localhost:3000/' . $this->entry_style, [], null);
            
        } else {
            // --- MODO PRODUCCIÓN ---
            // Leemos el manifest para buscar los nombres reales de los archivos
            $manifest = $this->get_manifest();
            
            if (!$manifest) return;

            // En el manifest, las llaves son las rutas relativas originales
            if (isset($manifest[$this->entry_script])) {
                $js_file = $manifest[$this->entry_script]['file'];
                wp_enqueue_script('main-js', get_theme_file_uri('dist/' . $js_file), [], null, true);
            }

            if (isset($manifest[$this->entry_style])) {
                // A veces Vite empaqueta el CSS dentro del JS entry, otras veces separado.
                // Vite 5 suele generar css bajo la llave del entry js o css
                // Esta lógica busca el archivo CSS generado
                $css_file = $manifest[$this->entry_style]['file'] ?? null;
                
                // Fallback: Si importaste el CSS dentro del JS, búscalo en la propiedad 'css' del JS
                if (!$css_file && isset($manifest[$this->entry_script]['css'])) {
                    $css_file = $manifest[$this->entry_script]['css'][0];
                }

                if ($css_file) {
                    wp_enqueue_style('main-css', get_theme_file_uri('dist/' . $css_file), [], null);
                }
            }
        }
    }

    /**
     * Detecta si estamos corriendo Vite en local
     * (Verifica si el puerto 3000 responde o si definimos una constante)
     */
    private function is_dev() {
        // La forma más simple: definir define('IS_VITE_DEV', true); en wp-config.php local
        // O verificar si existe el archivo "hot" que algunos plugins crean.
        // Por ahora, asumiremos que si NO existe dist/manifest.json, estamos en dev, 
        // o si definimos la constante WP_ENVIRONMENT_TYPE = 'local'
        
        return wp_get_environment_type() === 'local';
    }

    private function get_manifest() {
        // Ruta al manifest.json (Vite 5 lo pone en .vite/manifest.json dentro de dist)
        $path = get_theme_file_path('dist/.vite/manifest.json');
        
        if (!file_exists($path)) {
            // Fallback para versiones anteriores de Vite
            $path = get_theme_file_path('dist/manifest.json');
        }

        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        return null;
    }
}