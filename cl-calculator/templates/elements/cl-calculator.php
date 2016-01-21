<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single calculator element.
 *
 * @var $basic_rate
 * @var $required_skills array
 * @var $required_skills ['title']
 * @var $required_skills ['help']
 * @var $skills array
 * @var $skills ['title']
 * @var $skills ['rate']
 * @var $skills ['help']
 * @var $el_class string Extra class name
 */

foreach ( array( 'required_skills', 'skills' ) as $key ) {
	if ( empty( $$key ) ) {
		$$key = array();
	} else {
		$$key = json_decode( urldecode( $$key ), TRUE );
		if ( ! is_array( $$key ) ) {
			$$key = array();
		}
	}
}


if ( ! empty( $el_class ) ) {
	$el_class = ' ' . $el_class;
}
$output = '<div class="cl-calculator' . $el_class . '">';

// Required skills
$output .= '<div class="cl-calculator-list type_required">';
$output .= '<div class="cl-calculator-list-header"><h3>Минимальные требования</h3>';
$output .= '<span class="cl-calculator-list-header-rate" data-rate="' . esc_attr( $basic_rate ) . '">' . $basic_rate . ' руб. / час</span>';
$output .= '</div>';
$output .= '<div class="cl-calculator-list-items">';
foreach ( $required_skills as $item ) {
	$output .= '<div class="cl-calculator-list-item">';
	$output .= '<span class="cl-calculator-list-item-title">' . $item['title'] . '</span>';
	if ( isset( $item['help'] ) AND ! empty( $item['help'] ) ) {
		$output .= '<span class="cl-calculator-list-item-help" title="' . esc_attr( $item['help'] ) . '"></span>';
	}
	$output .= '</div>';
}
$output .= '</div>';
$output .= '</div>';

// Additional skills
$output .= '<div class="cl-calculator-list type_advanced">';
$output .= '<div class="cl-calculator-list-header"><h3>Будет плюсом</h3></div>';
$output .= '<div class="cl-calculator-list-items">';
foreach ( $skills as $index => $item ) {
	$output .= '<label class="cl-calculator-list-item">';
	$output .= '<input type="hidden" name="advanced_skill_' . $index . '" value="0">';
	$output .= '<input type="checkbox" name="advanced_skill_' . $index . '" value="1">';
	$output .= '<span class="cl-calculator-list-item-title">' . $item['title'] . '</span>';
	if ( isset( $item['help'] ) AND ! empty( $item['help'] ) ) {
		$output .= '<span class="cl-calculator-list-item-help" title="' . esc_attr( $item['help'] ) . '"></span>';
	}
	$output .= '<span class="cl-calculator-list-item-rate" data-rate="' . esc_attr( $item['rate'] ) . '">+ ' . $item['rate'] . ' руб. / час</span>';
	$output .= '</label>';
}
$output .= '</div>';
$output .= '</div>';

// Result
$output .= '<div class="cl-calculator-result">';
$output .= '<div class="cl-calculator-result-title"><h3>Итого</h3></div>';
$output .= '<div class="cl-calculator-result-hourly"><span>450</span> руб. / час</div>';
$output .= '<div class="cl-calculator-result-monthly"><span>87 500</span> руб. в месяц</div>';
$output .= '</div>';

$output .= '</div>';

echo $output;
