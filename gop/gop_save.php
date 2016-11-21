<?php

namespace GOP\gop;

use GOP\vendor\Spyc\Spyc;

class gop_save extends gop_read {

	/**
	 * Function used to update outline assignments
	 *
	 * @since 1.0.0
	 *
	 * @param int    $page_id      ID of the page
	 * @param string $outline_slug Name of the outline folder
	 *
	 * @return bool True on success
	 */
	function save_assignments( $page_id, $outline_slug ) {
		$outline_dir      = $this->outlines_dir . '/' . $outline_slug;
		$assignments_file = $outline_dir . '/assignments.yaml';

		if ( ! file_exists( $assignments_file ) ) {
			touch( $assignments_file );
		}

		$this->clear_assignments( $page_id );

		$assignments = Spyc::YAMLLoad( $assignments_file );

		$assignments['post']['page'][ $page_id ] = true;

		file_put_contents( $assignments_file, Spyc::YAMLDump( $assignments ), LOCK_EX );

		return true;
	}

	/**
	 * Function used to remove all assignments for a page
	 *
	 * @since 1.0.0
	 *
	 * @param int $page_id
	 *
	 * @return void
	 */
	function clear_assignments( $page_id ) {
		foreach ( $this->outlines as $slug => $outline ) {
			$assignments_file = $this->outlines_dir . '/' . $slug . '/assignments.yaml';

			if ( file_exists( $assignments_file ) ) {
				$assignments = Spyc::YAMLLoad( $assignments_file );

				if ( isset( $assignments['post']['page'][ $page_id ] ) ) {
					$assignments['post']['page'][ $page_id ] = false;
					file_put_contents( $assignments_file, Spyc::YAMLDump( $assignments ), LOCK_EX );
				}
			}
		}
	}
}