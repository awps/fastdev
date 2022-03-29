<?php

namespace Fastdev;

class Constants extends Tab {

	public function settings() {
		return array(
			'label' => __( 'Constants', 'fastdev' ),
		);
	}

	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			ksort( $options );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head">';
			$output .= '<div><div class="fd-kv-code">' . __( 'Constant name', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . __( 'Value', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $options as $key => $value ) {
				$output .= '<div class="fd-kv-row">';
				$output .= '<div class="filter-this"><div class="fd-kv-code">' . esc_html($key) . '</div></div>';
				$output .= '<div><div class="fd-kv-code">' . esc_html( $value ) . '</div></div>';
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
		$all_constants = get_defined_constants( true );

		if ( ! empty( $all_constants['user'] ) ) {
			fd_search();
			$this->makeTable( $all_constants['user'] );
		}
	}

}
