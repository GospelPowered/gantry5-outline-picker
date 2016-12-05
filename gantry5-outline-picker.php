<?php
/**
 * Plugin Name: Gantry 5 Outline Picker
 * Plugin URI: http://gospelpowered.com/
 * Description: Outline picker for WordPres pages on Gantry 5 based themes.
 * Version: 1.0.1
 * Author: Gospel Powered
 * Author URI: http://gospelpowered.com/
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gantry5-outline-picker
 */

/*
 * Load class if it doesn't exist
 */
spl_autoload_register( function ( $class ) {
	$prefix   = 'GOP';
	$base_dir = __DIR__;
	$len      = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}
	$relative_class = substr( $class, $len );
	$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
	if ( file_exists( $file ) ) {
		/** @noinspection PhpIncludeInspection */
		require $file;
	}
} );

/**
 * Create meta box
 *
 * @since 1.0.0
 */
function gop_register_meta_box() {
	add_meta_box( 'gop-picker', __( 'Outline Assignment', 'gantry5-outline-picker' ), 'gop_init', 'page', 'side', 'default' );
}

add_action( 'add_meta_boxes', 'gop_register_meta_box' );

/**
 * Update assignments on page save
 *
 * @since 1.0.0
 *
 * @param int $post_id Page ID
 */
function gop_save_post_init( $post_id ) {
	if ( isset( $_POST['gop_outline'] ) ) {
		$gop_save = new GOP\gop\gop_save;
		$gop_save->save_assignments( $post_id, sanitize_text_field( $_POST['gop_outline'] ) );
	}
}

add_action( 'save_post', 'gop_save_post_init' );

/**
 * Create new instance of main class
 *
 * @since 1.0.0
 * @return void
 */
function gop_init() {
	new GOP\gop\gop_read;
}

/**
 * Add our custom CSS
 *
 * @since 1.0.0
 * @return void
 */
function gop_enqueue_scripts() {
	if ( ( ( $GLOBALS['pagenow'] === 'post.php' && $_GET['action'] === 'edit' ) || $GLOBALS['pagenow'] === 'post-new.php' ) && get_post_type() === 'page' ) {
		/** @noinspection PhpIncludeInspection */
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugin_path = is_plugin_active( substr( __FILE__, strrpos( __FILE__, '/', - 28 ) + 1 ) ) ? plugin_dir_url( __FILE__ ) : get_template_directory_uri() . '/includes/vendor/gantry5-outline-picker/';

		wp_enqueue_style( 'gop-main', $plugin_path . 'assets/gop.css' );
	}
}

add_action( 'admin_enqueue_scripts', 'gop_enqueue_scripts' );