<?php

namespace Fastdev;

class RegisteredWidgetsList extends Tab {

	public function settings() {
		return array(
			'label' => esc_html__( 'Widgets', 'fastdev' ),
		);
	}

	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			ksort( $options );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head cols-30x40x30">';
			$output .= '<div><div class="fd-kv-code">' . esc_html__( 'Class name', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . esc_html__( 'Name', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . esc_html__( 'ID Base', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $options as $key => $value ) {
				$output .= '<div class="fd-kv-row cols-30x40x30">';
				$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg( 'fd-get-option', $key ) . '">' . esc_html($key) . '</a></div></div>';
				$output .= '<div class="filter-this"><div class="fd-kv-code">' . esc_html( $value['name'] ) . '</div></div>';
				$output .= '<div class="filter-this"><div class="fd-kv-code">' . esc_html( $value['id_base'] ) . '</div></div>';
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

        if ( empty ( $GLOBALS['wp_widget_factory'] ) ) {
			return;
		}

		$widgets = $GLOBALS['wp_widget_factory']->widgets;

		if ( ! empty( $_GET['fd-get-option'] ) ) {
			$option = wp_kses_data(wp_unslash($_GET['fd-get-option']));

			echo '<h3>' . esc_html($option) . '</h3>';
			fd_code( $widgets[ $option ] );
		} else {
			echo '<h3>' . esc_html__( 'A list of all registered widgets', 'fastdev' ) . '</h3>';
			fd_search();

			$options = array();
			foreach ( $widgets as $wk => $widget ) {
				$options[ $wk ] = array(
					'id_base' => esc_html($widget->id_base),
					'name'    => esc_html($widget->name),
				);
			}

			$this->makeTable( $options );
		}
	}

}
