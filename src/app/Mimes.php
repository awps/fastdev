<?php

namespace Fastdev;

class Mimes extends Tab {

	public function settings() {
		return array(
			'label' => __( 'Mimes', 'fastdev' ),
		);
	}

	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			$allowed_mimes = get_allowed_mime_types();
			ksort( $options );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head cols-30x50x20">';
			$output .= '<div><div class="fd-kv-code">' . __( 'Extension', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . __( 'Mime type', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . __( 'Allowed', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $options as $key => $value ) {
				$output .= '<div class="fd-kv-row cols-30x50x20">';
				$output .= '<div class="filter-this"><div class="fd-kv-code">' . esc_html($key) . '</div></div>';
				$output .= '<div><div class="fd-kv-code">' . esc_html( $value ) . '</div></div>';
				$output .= '<div><div class="fd-kv-code">' . $this->_mimeIsAllowed( $key, $allowed_mimes ) . '</div></div>';
				$output .= '</div>';
			}
			$output .= '</div>';
			echo wp_kses_post($output);
		} else {
			fd_code( $options );
		}
	}

	public function _mimeIsAllowed( $key, $array ) {
		return ( array_key_exists( $key, $array ) ) ? '<span class="fd-mime-allowed"></span>' . __( 'Yes', 'fastdev' ) : '<span class="fd-mime-not-allowed"></span>';
	}

	public function page() {
        if (!wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')){
            return;
        }

        $all_mimes = wp_get_mime_types();

		if ( ! empty( $all_mimes ) ) {
			fd_search();
			$this->makeTable( $all_mimes );
		}
	}

}
