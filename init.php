<?php

/*
Plugin Name: A Fishier Lorem Ipsum Generator
Plugin URI: http://tunaipsum.com/
Description: This is not just a plugin, it symbolizes the hunger for fish held by an entire generation summed up in two words said most famously by nobody: Tuna Ipsum. When activated you will experience enlightenment. Also, you need to use the shortcode [get-fishier].
Author: Chris Jean
Version: 1.0.0
Author URI: http://chrisjean.com/
*/

/* TODO
 *
 * Finish implementing matching feature set to baconipsum.com
 * Improve statistical model of sentence generation
 * Add control over sentence length and other attributes
 * Implement a widget that can be used
 * Add more fish
 */

require_once( dirname( __FILE__ ) . '/class-custom-lipsum.php' );

$strings = array(
	'start_with'         => 'Tuna ipsum dolor sit amet',
	'all_option'         => 'All Fish',
	'filler_option'      => 'Fish and Filler',
	'submit_button_desc' => 'Give me Tuna Ipsum',
);

$tuna_ipsum_obj =& new CustomLipsum( 'fish.txt', $strings );
add_shortcode( 'get-fishier', array( &$tuna_ipsum_obj, 'get_content' ) );
