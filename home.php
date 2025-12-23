<?php

use Timber\Timber;

$context = Timber::context();

// --- ESTA ES LA LÍNEA QUE TE FALTA ---
// Timber::get_posts() recupera los posts del loop actual de WordPress
$context['posts'] = Timber::get_posts(); 
// -------------------------------------

Timber::render( 'views/home.twig', $context );