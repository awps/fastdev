<?php

namespace Fastdev;

class Functions extends Tab {

	public function settings() {
		return array(
			'label' => __( 'Functions', 'fastdev' ),
		);
	}

	/**
	 * @param $options
	 */
	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			sort( $options );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head">';
			$output .= '<div style="width: 30%;"><div class="fd-kv-code">' . __( 'Function', 'fastdev' ) . '</div></div>';
			$output .= '<div style="width: 30%;"><div class="fd-kv-code">' . __( 'Defined in:', 'fastdev' ) . '</div></div>';
			$output .= '<div style="width: 20%;"><div class="fd-kv-code">' . __( 'Codex', 'fastdev' ) . '</div></div>';
			$output .= '<div style="width: 20%;"><div class="fd-kv-code">' . __( 'Developer', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $options as $key => $value ) {
				$cl       = new \ReflectionFunction( $value );
				$filename = $cl->getFileName();
				$filename = str_replace( array( '\\', get_home_path() ), array( '/', '' ), $filename );

				$codex_url_val = trim( $value );

				$output .= '<div class="fd-kv-row">';
				$output .= '<div class="filter-this" style="width: 30%;"><div class="fd-kv-code"><a href="' . add_query_arg( 'fd-get-function', $value ) . '">' . $value . '</a></div></div>';
				$output .= '<div style="width: 30%;"><div class="fd-kv-code">' . $filename . '</div></div>';
				$output .= '<div style="width: 20%;"><div class="fd-kv-code">
							<a href="https://codex.wordpress.org/Function_Reference/' . ( ( '__' == $codex_url_val ) ? '_2' : $codex_url_val ) . '" target="_blank">' . __( 'View &rarr;', 'fastdev' ) . '</a>
							</div></div>';

				$output .= '<div style="width: 20%;"><div class="fd-kv-code">
							<a href="https://developer.wordpress.org/reference/functions/' . $codex_url_val . '/" target="_blank">' . __( 'View &rarr;', 'fastdev' ) . '</a>
							</div></div>';
				$output .= '</div>';
			}
			$output .= '</div>';
			echo $output;
		}
		else {
			fd_code( $options );
		}
	}

	public function page() {
		if ( ! empty( $_GET['fd-get-function'] ) ) {
			$function = $_GET['fd-get-function'];

			$func = new \ReflectionFunction( $function );

			echo '<h3>' . $function . '</h3>';

			fd_code( $func->getDocComment() );

			$filename   = $func->getFileName();
			$start_line = $func->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
			$end_line   = $func->getEndLine();
			$length     = $end_line - $start_line;

			$source = file( $filename );
			$body   = implode( "", array_slice( $source, $start_line, $length ) );

			fd_code( $func->getParameters() );
			fd_code( htmlspecialchars( $body ) );
		}
		else {
			fd_search();
			$all_functions = get_defined_functions();
			$this->makeTable( $all_functions['user'] );
		}
	}

}
