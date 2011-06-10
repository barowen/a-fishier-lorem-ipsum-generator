<?php
/*
Plugin Name: A Fishier Lorem Ipsum Generator
Plugin URI: http://wordpress.org/extend/plugins/hello-dolly/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Matt Mullenweg
Version: 1.6
Author URI: http://ma.tt/
*/

require_once( dirname( __FILE__ ) . '/lib/classes/load.php' );

function tuna_ipsum_init() {
	require_once( dirname( __FILE__ ) . '/custom-lipsum.php' );
	
	$strings = array(
		'start_with'         => 'Tuna ipsum dolor sit amet',
		'all_option'         => 'All Fish',
		'filler_option'      => 'Fish and Filler',
		'submit_button_desc' => 'Give me Tuna Ipsum',
	);
	
	$tuna_ipsum_obj =& new CustomLipsum( 'fish.txt', $strings );
	add_shortcode( 'get-fishier', array( &$tuna_ipsum_obj, 'get_content' ) );
}
add_action( 'it_libraries_loaded', 'tuna_ipsum_init' );
