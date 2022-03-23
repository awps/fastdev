<?php

namespace Fastdev;

class Sidebars extends Tab {

	public function settings() {
		return array(
			'label' => esc_html__( 'Sidebars', 'fastdev' ),
		);
	}

	public function makeTable( $sidebars ) {
		if ( is_array( $sidebars ) ) {
			ksort( $sidebars );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head cols-30x40x30">';
			$output .= '<div><div class="fd-kv-code">' . esc_html__( 'Widget', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . esc_html__( 'Sidebar', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $sidebars as $sidebar_id => $widgets ) {
				if ( empty( $widgets ) ) {
					break;
				}

				foreach ( $widgets as $widget ) {
					$output .= '<div class="fd-kv-row cols-30x40x30">';
					$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg( 'fd-get-option', sanitize_key( $widget ) ) . '">' . $widget . '</a></div></div>';
					$output .= '<div class="filter-this"><div class="fd-kv-code">' . esc_html( $sidebar_id ) . '</div></div>';
					$output .= '</div>';
				}
			}
			$output .= '</div>';
			echo $output;  // phpcs:ignore  -- The table, inner columns are already escaped
		} else {
			fd_code( $sidebars );
		}
	}

	public function page() {
        if (!wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')){
            return;
        }

        if ( empty ( $GLOBALS['wp_widget_factory'] ) ) {
			return;
		}

		$widgets = $GLOBALS['wp_widget_factory']->widgets;

		if ( ! empty( $_GET['fd-get-option'] ) ) {
			$option = sanitize_key( $_GET['fd-get-option'] );

			echo '<h3>' . esc_html($option) . '</h3>';

			preg_match( '/-([0-9]*)$/', $option, $matches );
			$widget_number = ! empty( $matches[1] ) ? $matches[1] : false;

			$widgets_family = get_option( 'widget_' . preg_replace( '/-[0-9]*$/', '', $option ) );

			$widget_options = ! empty( $widgets_family[ $widget_number ] ) ? $widgets_family[ $widget_number ] : false;

			fd_code( $widget_options );
		} else {
			echo '<h3>' . esc_html__( 'A list of all widgets and their sidebars', 'fastdev' ) . '</h3>';
			fd_search();

			$sidebars = wp_get_sidebars_widgets();

			if ( empty( $sidebars ) ) {
				echo '<div class="notice inline notice-warning notice-alt"><p>
					' . esc_html__( 'No sidebars.', 'fastdev' ) . '
				</p></div>';
			}

			$this->makeTable( $sidebars );
		}
	}

}
