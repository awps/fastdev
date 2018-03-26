<?php

namespace Fastdev;

class Options extends Tab {

	public function settings() {
		return array(
			'label' => __( 'WP Options', 'fastdev' ),
		);
	}

	public function makeTable( $options ) {
		if ( is_array( $options ) ) {
			ksort( $options );
			$output = '<div class="fd-key-val-table">';

			$output .= '<div class="fd-kv-row fd-kv-head">';
			$output .= '<div><div class="fd-kv-code">' . __( 'Option ID', 'fastdev' ) . '</div></div>';
			$output .= '<div><div class="fd-kv-code">' . __( 'Value', 'fastdev' ) . '</div></div>';
			$output .= '</div>';

			foreach ( $options as $key => $value ) {
				$val_display = $value;
				$val_size    = strlen( $value );

				if ( $val_size > 1500 ) {
					$val_display = '<div class="fastdev-trimmed-string">';
					$val_display .= '<span class="original-string" style="display: none;">' . esc_html( $value ) . '</span>';
					$val_display .= '<span class="trimmed-string">' . esc_html( substr( $value, 0, 1000 ) ) . '</span>';
					$val_display .= '<div class="toggle-string">
								<span data-expand="' . __( 'Expand full source code', 'fastdev' ) . '" data-collapse="' . __( 'Collapse full source code', 'fastdev' ) . '">' . __( 'Expand full source code', 'fastdev' ) . '</span>
							</div>';
					$val_display .= '</div>';
				}

				$output .= '<div class="fd-kv-row">';
				$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg( 'fd-get-option', $key ) . '">' . $key . '</a></div></div>';
				$output .= '<div class="filter-this"><div class="fd-kv-code">' . $val_display . '</div></div>';
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
		if ( ! empty( $_GET['fd-get-option'] ) ) {
			$option            = sanitize_title( $_GET['fd-get-option'] );
			$data_attr         = ' data-option="' . $option . '"';
			$data_attr         .= ' data-nonce="' . wp_create_nonce( $option ) . '"';
			$data_original_key = ' data-original-option-key="' . $option . '"';

			$action_buttons = '<span id="fd-refresh-option" class="fd-button-refresh"' . $data_attr . '>' . __( 'Refresh', 'fastdev' ) . '</span>';
			$action_buttons .= '<label class="fd-auto-refresh"><input type="checkbox" id="fd-auto-refresh"> ' . __( 'Auto refresh', 'fastdev' ) . '</label>';
			$action_buttons .= '<span id="fd-delete-option" class="fd-button-delete"' . $data_attr . '>' . __( 'Delete option', 'fastdev' ) . '</span>';

			echo '<h3><input id="wp-option-edit-key" type="text" value="' . esc_attr( $option ) . '" ' . $data_original_key . ' />' . $action_buttons . '</h3>';
			echo '<div id="fd-wpo-code-block">';
			fd_code( get_option( $option ), true );
			echo '</div>';
		}
		else {
			fd_search();
			$all_wpo = wp_load_alloptions();
			$this->makeTable( $all_wpo );
		}

	}

	public function registerAjax() {
		add_action( 'wp_ajax_fastdev_delete_option', array( $this, 'deleteOption' ) );
		add_action( 'wp_ajax_fastdev_refresh_option', array( $this, 'refreshOption' ) );
		add_action( 'wp_ajax_fastdev_edit_option_key', array( $this, 'editOptionKey' ) );
	}

	public function canProcess() {
		if ( empty( $_POST['option_id'] ) ) {
			return false;
		}

		$option = sanitize_title( $_POST['option_id'] );

		if ( ! wp_verify_nonce( $_POST['nonce'], $option ) ) {
			return false;
		}

		return $option;
	}

	public function refreshOption() {
		if ( ( $option = $this->canProcess() ) === false ) {
			$this->ajaxMessage( __( 'Error. Please refresh the page.', 'fastdev' ) );
		}

		if ( ( $opt = get_option( $option ) ) !== false ) {
			fd_code( $opt, true );
		}
		else {
			$this->ajaxMessage( __( 'Option does not exists.', 'fastdev' ) );
		}

		die();
	}

	public function deleteOption() {
		if ( ( $option = $this->canProcess() ) === false ) {
			$this->ajaxMessage( __( 'Error. Please refresh the page.', 'fastdev' ) );
		}

		if ( $opt = delete_option( $option ) ) {
			$this->ajaxMessage( __( 'Option deleted.', 'fastdev' ) );
		}
		else {
			$this->ajaxMessage( __( 'Can\'t delete this option.', 'fastdev' ) );
		}

		die();
	}

	public function editOptionKey() {
		$key_from    = $_POST['option_from'];
		$key_to      = $_POST['option_to'];
		$option_data = get_option( $key_from );

		if ( add_option( $key_to, $option_data ) ) {
			delete_option( $key_from );
			echo 'success';
		}
		else {
			echo 'fail';
		}

		die();
	}

	public function ajaxMessage( $msg, $type = 'error' ) {
		die( '<div class="fastdev-ajax-message is-' . $type . '">' . $msg . '</div>' );
	}

}
