<?php
/**
 * The Template for displaying all single posts
 */

use Timber\Timber;

$context = Timber::context();

// A diferencia de index.php donde usamos get_posts(), 
// aquí usamos get_post() para obtener EL post actual.
$timber_post = Timber::get_post();
$context['post'] = $timber_post;

// Renderizamos una vista diferente: single.twig
Timber::render( 'views/single.twig', $context );