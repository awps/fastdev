<?php

namespace Fastdev;

class Classes extends Tab {

	public function settings() {
		return array(
			'label' => __( 'Classes', 'fastdev' ),
		);
	}

	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			sort( $options );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head">';
			$output .= '<div><div class="fd-kv-code">' . __( 'Class name', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . __( 'Defined in:', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $options as $key => $value ) {
				$cl       = new \ReflectionClass( $value );
				$filename = $cl->getFileName();
				if ( ! empty( $filename ) ) {
					$filename = str_replace( array( '\\', get_home_path() ), array( '/', '' ), $filename );
					$output   .= '<div class="fd-kv-row">';
					$output   .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg( 'fd-get-class', $value ) . '">' . $value . '</a></div></div>';
					$output   .= '<div><div class="fd-kv-code">' . $filename . '</div></div>';
					$output   .= '</div>';
				}
			}
			$output .= '</div>';
			echo $output;
		}
		else {
			fd_code( $options );
		}
	}

	public function page() {
		if ( ! empty( $_GET['fd-get-class'] ) ) {
			$class = str_replace( '\\\\', '\\', $_GET['fd-get-class'] );

			$cl = new \ReflectionClass( $class );

			echo '<h3>' . $class . '</h3>';

			fd_code( $cl->getDocComment() );

			echo '<hr /><h4>' . __( 'Properties', 'fastdev' ) . '</h4>';
			fd_code( get_class_vars( $class ) );

			echo '<hr /><h4>' . __( 'Methods', 'fastdev' ) . '</h4>';
			fd_code( get_class_methods( $class ) );

			$filename   = $cl->getFileName();
			$start_line = $cl->getStartLine() - 1; // it's actually - 1, otherwise you wont get the class() block
			$end_line   = $cl->getEndLine();
			$length     = $end_line - $start_line;

			$source = file( $filename );
			echo '<hr /><h4>' . __( 'Source', 'fastdev' ) . '</h4>';
			$body = implode( "", array_slice( $source, $start_line, $length ) );

			fd_code( htmlspecialchars( $body ) );
		}
		else {
			fd_search();
			$this->makeTable( get_declared_classes() );
		}
	}

}
