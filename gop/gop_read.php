<?php

namespace GOP\gop;

use GOP\vendor\Spyc\Spyc;

class gop_read {

	/**
	 * @var string Outlines location, usually at theme/custom/config/
	 * @since 1.0.0
	 */

	public $outlines_dir;

	/**
	 * @var string Error message
	 * @since 1.0.0
	 */

	public $error_message = 'An error has occured.';

	/**
	 * @var array Array of outlines
	 * @since 1.0.0
	 */

	public $outlines = array(
		'default' => 'Base Outline',
	);

	function __construct() {
		$this->outlines_dir = get_template_directory() . '/custom/config';

		if ( ! $this->pre_init_tests() || ! $this->get_outlines() ) {
			echo $this->spawn_error( $this->error_message );

			return;
		}

		if ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' ) {
			echo $this->show_outlines();
		}
	}

	/**
	 * Check if everything is good and proceed
	 *
	 * @since 1.0.0
	 * @return bool Good or not
	 */
	function pre_init_tests() {
		if ( ! is_dir( $this->outlines_dir ) ) {
			$this->error_message = 'Outlines do not exist!';

			return false;
		}

		return true;
	}

	/**
	 * Get a list of outlines
	 *
	 * @since 1.0.0
	 * @return bool true on success, false otherwise
	 */
	function get_outlines() {
		$outlines_list = array_slice( scandir( $this->outlines_dir ), 2 );
		$outlines      = array();

		if ( empty( $outlines_list ) ) {
			$this->error_message = 'No outlines found.';

			return false;
		}

		// Remove system and default outlines
		foreach ( $outlines_list as $key => $outline ) {
			if ( substr( $outline, 0, 1 ) !== '_' && $outline !== 'default' ) {
				$outlines[ $outline ] = ucwords( str_replace( '_', ' ', $outline ) );
			}
		}

		$this->outlines += $outlines;

		return true;
	}

	/**
	 * Show a formatted error message
	 *
	 * @since 1.0.0
	 *
	 * @param string $message The message to format
	 *
	 * @return string
	 */
	function spawn_error( $message ) {
		return $message;
	}

	/**
	 * Show a list of outlines
	 *
	 * @since 1.0.0
	 * @return string HTML select box with outlines
	 */
	function show_outlines() {
		$assigned_outline = $this->get_assigned_outline();

		$return = '<select name="gop_outline">';

		foreach ( $this->outlines as $slug => $outline ) {
			$return .= '<option value="' . $slug . '" ' . ( $assigned_outline === $slug ? 'selected' : ( $slug === 'default' ? 'selected' : '' ) ) . '>' . $outline . '</option>';
		}

		$return .= '</select>';

		return $return;
	}

	/**
	 * Return assigned outline if there is one
	 *
	 * @since 1.0.0
	 * @return bool|string false if there isn't one, slug if there is
	 */
	function get_assigned_outline() {
		$current_post = $_GET['post'];
		settype( $current_post, 'int' );

		foreach ( $this->outlines as $slug => $outline ) {
			$assignments_file = $this->outlines_dir . '/' . $slug . '/assignments.yaml';

			if ( file_exists( $assignments_file ) ) {
				$assignments = Spyc::YAMLLoad( $assignments_file );

				if ( isset( $assignments['post']['page'][ $current_post ] ) && $assignments['post']['page'][ $current_post ] === true ) {
					return $slug;
				}
			}
		}

		return false;
	}
}