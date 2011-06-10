<?php

if ( ! class_exists( 'LoremIpsumGenerator' ) ) {
	class LoremIpsumGenerator {
		/**
		*	Copyright (c) 2009, Mathew Tinsley (tinsley@tinsology.net)
		*	All rights reserved.
		*
		*	Redistribution and use in source and binary forms, with or without
		*	modification, are permitted provided that the following conditions are met:
		*		* Redistributions of source code must retain the above copyright
		*		  notice, this list of conditions and the following disclaimer.
		*		* Redistributions in binary form must reproduce the above copyright
		*		  notice, this list of conditions and the following disclaimer in the
		*		  documentation and/or other materials provided with the distribution.
		*		* Neither the name of the organization nor the
		*		  names of its contributors may be used to endorse or promote products
		*		  derived from this software without specific prior written permission.
		*
		*	THIS SOFTWARE IS PROVIDED BY MATHEW TINSLEY ''AS IS'' AND ANY
		*	EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
		*	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
		*	DISCLAIMED. IN NO EVENT SHALL <copyright holder> BE LIABLE FOR ANY
		*	DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
		*	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
		*	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
		*	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
		*	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
		*	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
		*/
		
		/* Improve these to have individual thresholds to make calculating
		 * when to use each simpler.
		 */
		var $_stats = array(
			'sentences_per_paragraph' => array(
				'average' => 5,
				'stddev'  => 3,
			),
			'words_per_sentence'      => array(
				'average' => 13,
				'stddev'  => 5,
			),
			'comma_frequency'         => array(
				'average' => .35,
				'stddev'  => .5,
			),
			'semicolon_frequency'     => array(
				'average' => .1,
				'stddev'  => .5,
			),
			'colon_frequency'         => array(
				'average' => .25,
				'stddev'  => .33,
			),
			'em_dash_frequency'       => array(
				'average' => .10,
				'stddev'  => .35,
			),
			'open_quote_frequency'    => array(
				'average' => .4,
				'stddev'  => .3,
			),
			'close_quote_frequency'   => array(
				'average' => .6,
				'stddev'  => .3,
			),
			'exclamation_frequency'   => array(
				'average' => .15,
				'stddev'  => .5,
			),
			'question_frequency'      => array(
				'average' => .3,
				'stddev'  => .5,
			),
		);
		
		var $_args = array(
			'limit_type' => 'paragraphs',
			'limit'      => 5,
			'beginning'  => '',
			'output'     => 'text',
		);
		
		var $_type_limits = array(
			'paragraphs' => array( 1, 100 ),
			'sentences'  => array( 1, 1200 ),
			'words'      => array( 1, 30000 ),
		);
		
		var $_words = array();
		
		
		function LoremIpsumGenerator( $source_file ) {
			if ( ! file_exists( $source_file ) ) {
				if ( ! file_exists( dirname( __FILE__ ) . '/' . $source_file ) )
					die( "Unable to locate requested word source file: $source_file" );
				
				$source_file = dirname( __FILE__ ) . '/' . $source_file;
			}
			
			$this->_words = explode( ',', file_get_contents( $source_file ) );
		}
		
		function get_content( $args = array() ) {
			if ( is_numeric( $args ) )
				$args = array( 'limit' => $args );
			else if ( is_string( $args ) )
				$args = array( 'output' => 'html' );
			else if ( ! is_array( $args ) )
				$args = array();
			
			$args = array_merge( $this->_args, $args );
			
			if ( ! isset( $this->_type_limits[$args['limit_type']] ) )
				$args['limit_type'] = $this->_args['limit_type'];
			
			if ( ! is_numeric( $args['limit'] ) || ( $args['limit'] < $this->_type_limits[$args['limit_type']][0] ) )
				$args['limit'] = $this->_type_limits[$args['limit_type']][0];
			else if ( $args['limit'] > $this->_type_limits[$args['limit_type']][1] )
				$args['limit'] = $this->_type_limits[$args['limit_type']][1];
			else
				$args['limit'] = intval( $args['limit'] );
			
			if ( ! is_string( $args['beginning'] ) )
				$args['beginning'] = $this->_args['beginning'];
			
			$this->_args = $args;
			
			
			$paragraphs = array();
			$paragraphs[] = $this->get_paragraph( true );
			
			for ( $i = 1; $i < $this->_args['limit']; $i++ )
				$paragraphs[] = $this->get_paragraph();
			
			
			if ( 'array' == $this->_args['output'] )
				return $paragraphs;
			else if ( 'html' == $this->_args['output'] )
				return '<p>' . implode( "</p>\n<p>", $paragraphs ) . '</p>';
			else
				return implode( "\n\n", $paragraphs );
		}
		
		function get_paragraph( $add_beginning_text = false ) {
			$sentence_count = $this->get_random( 'sentences_per_paragraph' );
			
			if ( $sentence_count < 1 )
				$sentence_count = 1;
			
			
			$paragraph = $this->get_sentence( $add_beginning_text );
			
			for ( $i = 1; $i <= $sentence_count; $i++ )
				$paragraph .= ' ' . $this->get_sentence();
			
			
			return $paragraph;
		}
		
		function get_sentence( $add_beginning_text = false ) {
			$word_count = $this->get_random( 'words_per_sentence' );
			
			if ( $word_count < 1 )
				$word_count = 1;
			
			
			if ( true === $add_beginning_text )
				$sentence = $this->_args['beginning'];
			else
				$sentence = '';
			
			$in_quote = false;
			$just_closed_quote = false;
			$no_space = false;
			
			while ( empty( $sentence ) || ( count( explode( ' ', $sentence ) ) < $word_count ) ) {
				if ( ! empty( $sentence ) && ! $no_space )
					$sentence .= ' ';
				
				$just_closed_quote = false;
				$no_space = false;
				
				$sentence .= $this->_words[rand( 0, count( $this->_words ) - 1 )];
				
				
				$comma = $this->get_random( 'comma_frequency' );
				
				if ( $comma >= 1 ) {
					$sentence .= ',';
					continue;
				}
				
				
				$semicolon = $this->get_random( 'semicolon_frequency' );
				
				if ( $semicolon >= 1 ) {
					$sentence .= ';';
					continue;
				}
				
				
				$colon = $this->get_random( 'colon_frequency' );
				
				if ( $colon >= 1 ) {
					$sentence .= ':';
					continue;
				}
				
				
				if ( ! $in_quote ) {
					$em_dash = $this->get_random( 'em_dash_frequency' );
					
					if ( $em_dash >= 1 ) {
						$sentence .= '--';
						$no_space = true;
						continue;
					}
				}
				
				
				if ( ! $in_quote )
					$quote = $this->get_random( 'open_quote_frequency' );
				else
					$quote = $this->get_random( 'close_quote_frequency' );
				
				if ( $quote > 1 ) {
					if ( $in_quote ) {
						$just_closed_quote = true;
						$sentence .= ',"';
						$in_quote = false;
					}
					else {
						$no_space = true;
						$sentence .= ', "';
						$in_quote = true;
					}
				}
			}
			
			
			$stop = '.';
			
			if ( $this->get_random( 'exclamation_frequency' ) > 1 )
				$stop = '!';
			else if ( $this->get_random( 'question_frequency' ) > 1 )
				$stop = '?';
			
			
			if ( $just_closed_quote )
				$sentence = preg_replace( '/,"$/', "$stop\"", $sentence, -1, $count );
			else {
				$sentence = preg_replace( '/[^a-z0-9]+$/i', '', $sentence );
				
				if ( true === $in_quote )
					$sentence .= "$stop\"";
				else
					$sentence .= $stop;
			}
			
			
			$sentence = ucfirst( $sentence );
			
			return $sentence;
		}
		
		function get_random( $m = 0.0, $s = 1.0 ) {
			if ( is_string( $m ) )
				list( $m, $s ) = array( $this->_stats[$m]['average'], $this->_stats[$m]['stddev'] );
			
			return $this->get_gaussian_random() * $s + $m;
		}
		
		function get_gaussian_random() {
			$x1 = $x2 = $w = 0;
			
			do {
				$x1 = 2.0 * $this->get_uniform_number() - 1.0;
				$x2 = 2.0 * $this->get_uniform_number() - 1.0;
				
				$w = ( $x1 * $x1 ) + ( $x2 * $x2 );
			} while ( $w >= 1.0 );
			
			$w = sqrt( ( -2.0 * log( $w ) ) / $w );
			
			return $x1 * $w;
		}
		
		function get_uniform_number() {
			return (float) mt_rand() / (float) mt_getrandmax();
		}
	}
}
