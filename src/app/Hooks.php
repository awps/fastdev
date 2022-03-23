<?php

namespace Fastdev;

class Hooks extends Tab {

	public function settings() {
		return array(
			'label' => __( 'Hooks', 'fastdev' ),
		);
	}

	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			// ksort($options);
			$output = '<div class="fd-key-val-table">';
			foreach ( $options as $key => $value ) {
				$output .= '<div class="fd-kv-row cols-100">';
				$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg( 'fd-get-hook', $key ) . '">' . esc_html($key) . '</a></div></div>';
				$output .= '</div>';
			}
			$output .= '</div>';
			echo $output; // phpcs:ignore  -- The table, inner columns are already escaped
		} else {
			fd_code( $options );
		}
	}

	public function page() {
        if ( ! wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')) {
            return;
        }

		global $wp_filter;

		if ( ! empty( $_GET['fd-get-hook'] ) ) {
			$hook = esc_html( stripcslashes( urldecode( wp_unslash($_GET['fd-get-hook']) ) ) ); // phpcs:ignore  -- False positive

			if ( empty( $hook ) || ! isset( $wp_filter[ $hook ] ) ) {
				return;
			}

			echo '<h3>' . esc_html($hook) . '</h3>';
			fd_code( $wp_filter[ $hook ] );
		} else {
			fd_search();
			$this->makeTable( $wp_filter );
		}
	}

}
