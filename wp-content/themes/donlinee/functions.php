<?php
/**
 * Theme functions and definitions
 *
 * @package Donlinee
 * @author webdot
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Theme setup
 */
function donlinee_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'donlinee' ),
    ) );
}
add_action( 'after_setup_theme', 'donlinee_setup' );

/**
 * Enqueue styles
 */
function donlinee_enqueue_styles() {
    wp_enqueue_style( 'donlinee-style', get_stylesheet_uri(), array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'donlinee_enqueue_styles' );
