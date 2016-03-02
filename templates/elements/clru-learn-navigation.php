<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a learn navigation shortcode
 *
 * @var $parent_id int
 * @var $user_logged bool
 * @var $total_markers int
 * @var $learn_pages array
 * @var $pages_markers array
 * @var $user_progress_pages array
 *
 */

$output = '<div class="cl-learnnav">' . "\n";
$output .= show_tree( $learn_pages, $parent_id, 1, $user_progress_pages, $user_logged, $pages_markers );
$output .= '</div>' . "\n";
echo $output;

/**
 * Output a navigation tree with progress values
 *
 * @param $learn_pages Array
 * @param $parent_id int
 * @param $level int
 * @param $user_progress_pages Array
 * @param $user_logged bool
 *
 * @return string
 */
function show_tree( $learn_pages, $parent_id, $level, $user_progress_pages, $user_logged, $pages_markers ) {
	$output = '';

	foreach ( $learn_pages as $page_parent_id => $page ) {
		if ( $page_parent_id == $parent_id ) {

			foreach ( $page as $page_id => $page_title ) {
				$total_markers = $pages_markers[ $page_id ]['total_markers'];
				$completed_markers = $pages_markers[ $page_id ]['completed_markers'];

				$real_progress = child_progress( $pages_markers, $page_id );

				$total_markers_out = $total_markers + $real_progress['total_markers'];
				$completed_markers_out = $completed_markers + $real_progress['completed_markers'];
				$current_progress = floor( ( $completed_markers_out / $total_markers_out ) * 10000 ) / 100;
				if ( ! $current_progress ) {
					$current_progress = 0;
				}
				if ( $current_progress == 100 ) {
					$class_completed = 'completed';
				} else {
					$class_completed = '';
				}

				$output .= '<div class="cl-learnnav-item level_' . $level . ' ' . $class_completed . '" data-id="' . $page_id . '"';
				if ( $user_logged ) {
					$output .= 'data-progress="' . $completed_markers . '/' . $total_markers . '"';
				}
				$output .= '>' . "\n";
				$output .= '<a class="cl-learnnav-item-anchor" href="' . get_the_permalink( $page_id ) . '">' . "\n";
				$output .= $page_title . "\n";
				$output .= '<span class="cl-learnnav-item-progress">(' . $current_progress . '%)</span>' . "\n";
				$output .= '</a>' . "\n";
				$output .= show_tree( $learn_pages, $page_id, $level + 1, $user_progress_pages, $user_logged, $pages_markers );
				$output .= '</div>' . "\n";
			}
		}
	}

	return $output;
}
