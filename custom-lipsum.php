<?php

if ( ! class_exists( 'CustomLipsum' ) ) {
	class CustomLipsum {
		var $_source_file = null;
		
		var $_strings = array(
			'paragraphs_desc'    => 'Paragraphs:',
			'type_desc'          => 'Type:',
			'start_with_desc'    => 'Start with &#8216;%s&#8230;&#8217;',
			'start_with'         => 'REPLACE ME',
			'all_option'         => 'REPLACE ME',
			'filler_option'      => 'REPLACE ME',
			'submit_button_desc' => 'REPLACE ME',
		);
		
		
		function CustomLipsum( $source_file, $strings = array() ) {
			if ( empty( $source_file ) )
				die( 'A source file containing comma-separated lists of words/phrases must be supplied.' );
			
			if ( file_exists( $source_file ) )
				$this->_source_file = $source_file;
			else if ( file_exists( dirname( __FILE__ ) . '/' . $source_file ) )
				$this->_source_file = dirname( __FILE__ ) . '/' . $source_file;
			else
				die( 'Unable to find the supplied source file: ' . $source_file );
			
			$this->_source_file = $source_file;
			
			$this->_strings = array_merge( $this->_strings, $strings );
		}
		
		function generate_ipsum( $values ) {
			require_once( dirname( __FILE__ ) . '/class-lorem-ipsum-generator.php' );
			
			$lorem = new LoremIpsumGenerator( $this->_source_file );
			
			$args = array(
				'output'    => 'html',
				'limit'     => $values['paragraphs'],
				'beginning' => ( ! empty( $values['start-with-lorem'] ) ) ? $this->_strings['start_with'] : '',
			);
			
			echo $lorem->get_content( $args );
		}
		
		function get_content( $atts, $content = null, $code = '' ) {
			$default_values = array(
				'paragraphs'       => 5,
				'add-filler'       => '',
				'start-with-lorem' => '',
			);
			
			$values = array_merge( $default_values, $_GET );
			
			if ( ! is_numeric( $values['paragraphs'] ) || ( $values['paragraphs'] < 1 ) )
				$values['paragraphs'] = 1;
			else if ( $values['paragraphs'] > 100 )
				$values['paragraphs'] = 100;
			else
				$values['paragraphs'] = intval( $values['paragraphs'] );
			
			if ( ! in_array( $values['type'], array( 'all', 'add-filler' ) ) )
				$values['type'] = 'all';
			
			
			foreach ( array_keys( $values ) as $key ) {
				if ( isset( $_REQUEST[$key] ) ) {
					$this->generate_ipsum( $values );
					echo "<br />\n";
					
					break;
				}
			}
			
			
			if ( ! empty( $content ) )
				echo "<p>$content</p>\n";
			
?>
	<form id="custom-lipsum" action="/" method="get">
		<table>
			<tbody>
				<tr>
					<td><?php echo $this->_strings['paragraphs_desc']; ?></td>
					<td><input style="width: 40px;" maxlength="2" name="paragraphs" type="text" value="<?php echo $values['paragraphs']; ?>" /></td>
				</tr>
<?php
/*
				<tr>
					<td>Type:</td>
					<td>
						<label><input name="add-filler" type="radio" value="no" <?php if ( 'no' == $values['add-filler'] ) echo 'checked="checked"'; ?>/> <?php echo $this->_strings['all_option']; ?></label><br />
						<label><input name="add-filler" type="radio" value="yes" <?php if ( 'yes' == $values['add-filler'] ) echo 'checked="checked"'; ?>/> <?php echo $this->_strings['filler_option']; ?></label>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<label><input name="start-with-lorem" type="checkbox" value="1" /> <?php printf( $this->_strings['start_with_desc'], $this->_strings['start_with'] ); ?></label>
					</td>
				</tr>
*/
?>
				<tr>
					<td></td>
					<td>
						<input type="submit" value="<?php echo $this->_strings['submit_button_desc']; ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
<?php
			
		}
	}
}
