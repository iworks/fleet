<?php

function twentytwenty_scripts() {
	wp_enqueue_style( 'parent-theme', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'twentytwenty_scripts' );

