<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax for register
 */
add_action( 'wp_ajax_do_progress', 'clru_do_progress' );
function clru_do_progress() {

	check_ajax_referer( 'clru_do_progress_nonce', '_ajax_nonce' );

	$data = [ ];

	if ( ! $_POST['pageID'] ) {
		wp_send_json_error( array(
			'error' => 'Unknown page.',
		) );
	} else {
		$page_id = $_POST['pageID'];
	}

	if ( ! $_POST['userID'] ) {
		wp_send_json_error( array(
			'error' => 'Unknown user.',
		) );
	} else {
		$user_id = $_POST['userID'];
	}

	if ( ! $_POST['markerID'] ) {
		wp_send_json_error( array(
			'error' => 'Unknown marker.',
		) );
	} else {
		$marker_id = $_POST['markerID'];
	}

	$user_progress = get_user_meta( $user_id, 'clru_progress', TRUE );
	$page_markers = unserialize( get_post_meta( $page_id, 'clru_markers', TRUE ) );
	$total_markers = count( $page_markers );

	if ( ! $user_progress ) {
		if ( $_POST['checked'] == 'true' ) {
			$progress = clru_count_progress( $total_markers, array( $marker_id ) );
			$user_progress = array(
				'pages' => array(
					$page_id => array(
						'markers' => array( $marker_id ),
						'progress' => $progress,
					),
				),
			);
			add_user_meta( $_POST['userID'], 'clru_progress', $user_progress, TRUE );
		}
	} else {
		$markers = $user_progress['pages'][ $page_id ]['markers'];
		if ( $_POST['checked'] == 'true' ) {
			if ( ! in_array( $marker_id, $markers ) ) {
				$markers[] = $marker_id;
				$user_progress['pages'][ $page_id ]['markers'] = $markers;
				$progress = clru_count_progress( $total_markers, $markers );
				$user_progress['pages'][ $page_id ]['progress'] = $progress;
			}
		} else {
			$marker_found = array_search( $marker_id, $markers );
			if ( $marker_found !== FALSE AND count( $markers ) > 1 ) {
				unset( $markers[ $marker_found ] );
				$user_progress['pages'][ $page_id ]['markers'] = $markers;
				$progress = clru_count_progress( $total_markers, $markers );
				$user_progress['pages'][ $page_id ]['progress'] = $progress;
			} else if ( $marker_found !== FALSE AND count( $markers ) == 1 ) {
				unset( $user_progress['pages'][ $page_id ] );
				if ( count( $user_progress['pages'] ) == 0 ) {
					unset( $user_progress['pages'] );
				}
			}
		}
		if ( count( $user_progress['pages'] ) > 0 ) {
			update_user_meta( $user_id, 'clru_progress', $user_progress );
		} else {
			delete_user_meta( $user_id, 'clru_progress' );
		}
	}

	wp_send_json_success();
}

/**
 * Count progress from checked markers and total markers
 *
 * @param $total_markers int
 * @param $markers array
 *
 * @return float
 */
function clru_count_progress( $total_markers, $markers ) {
	return floor( ( count( $markers ) / $total_markers ) * 10000 ) / 100;
}

/**
 * Extract shortcodes 'cl-learnmarker' from post content and save as post meta
 */
add_action( 'save_post', 'clru_progress_markers_to_post_meta' );
function clru_progress_markers_to_post_meta( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( $_POST && $_POST['post_type'] == 'page' ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		$content = stripslashes( $_POST['post_content'] );

		if ( has_shortcode( $content, 'cl-learnmarker' ) ) {
			$shortcodes = clru_get_all_shortcode_attributes( 'cl-learnmarker', $content );
			$page_shortcodes = [ ];
			foreach ( $shortcodes as $shortcode ) {
				$page_shortcodes[] = $shortcode['id'];
			}

			$meta_value = serialize( $page_shortcodes );
			update_post_meta( $post_id, 'clru_markers', $meta_value );
		} else {
			delete_post_meta( $post_id, 'clru_markers' );
		}
	}
}


