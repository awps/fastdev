<?php

namespace Fastdev;

class MySQLInfo extends Tab {

	public function settings() {
		return array(
			'label' => __( 'MySQL', 'fastdev' ),
		);
	}

	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			ksort( $options );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head">';
			$output .= '<div><div class="fd-kv-code">' . __( 'Variable', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . __( 'Value', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $options as $key => $value ) {
				$output .= '<div class="fd-kv-row">';
				$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg( 'fd-get-option', $key ) . '">' . esc_html($key) . '</a></div></div>';
				$output .= '<div class="filter-this"><div class="fd-kv-code">' . esc_html( $value ) . '</div></div>';
				$output .= '</div>';
			}
			$output .= '</div>';
			echo wp_kses_post($output);
		} else {
			fd_code( $options );
		}
	}

	public function page() {
        if ( ! wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')) {
            return;
        }

        global $wpdb;
		$mysql   = $wpdb->get_results( "SHOW VARIABLES" );
		$options = array();
		if ( $mysql ) {
			foreach ( $mysql as $sql ) {
				$options[ $sql->Variable_name ] = $sql->Value;
			}

			if ( ! empty( $options ) ) :
				if ( ! empty( $_GET['fd-get-option'] ) ) {
					$option = sanitize_title( wp_unslash($_GET['fd-get-option']) );

					echo '<h3>' . esc_html($option) . '</h3>';
					fd_code( $options[ $option ] );
				} else {
					fd_search();
					$this->makeTable( $options );
				}
			endif;
		}
	}

}
