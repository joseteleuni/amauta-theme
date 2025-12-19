// vite.config.js
import { defineConfig } from 'vite';
import liveReload from 'vite-plugin-live-reload';
import path from 'path';

export default defineConfig({
  plugins: [
    // Recarga el navegador si tocas PHP o Twig
    liveReload([__dirname + '/**/*.php', __dirname + '/**/*.twig']),
  ],
  root: '',
  base: process.env.NODE_ENV === 'development' ? '/' : '/dist/',
  
  build: {
    // Output folder para producción
    outDir: 'dist',
    emptyOutDir: true,
    manifest: true, // Importante para que PHP sepa qué archivo cargar
    rollupOptions: {
      input: {
        // Puntos de entrada de tus assets
        main: path.resolve(__dirname, 'src/scripts/main.js'),
        style: path.resolve(__dirname, 'src/styles/main.scss'), 
      },
    },
  },
  
  server: {
    // Configuración para entorno local
    cors: true,
    strictPort: true,
    port: 3000,
    hmr: {
      host: 'localhost',
    },
  },
});