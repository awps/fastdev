<?php

namespace Fastdev;

class JsonParse extends Tab {

	public function registerAjaxHook() {
		add_action( 'wp_ajax_fastdev_testing', [ $this, 'ajax' ] );
	}

	public function ajax() {
		$result = [];

		if ( empty( $_POST['nonce'] ) ) {
			self::stopAjax( 'Invalid nonce.' );
		}

		if ( empty( $_POST['function_name'] ) ) {
			self::stopAjax( 'Invalid function name.' );
		}

		$function_name = preg_replace( '/[^a-zA-Z0-9_]/', '', $_POST['function_name'] );

		if ( substr( $function_name, 0, 4 ) !== "test" ) {
			$function_name = 'test' . $function_name;
		}

		if ( ! function_exists( $function_name ) ) {
			self::stopAjax( "Function '{$function_name}' does not exists." );
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], 'fastdev_testing' ) ) {
			self::stopAjax( "Cheating?" );
		}

		// Only admins allowed to run functions
		if ( ! current_user_can( 'activate_plugins' ) ) {
			self::stopAjax( 'You do not have enough permissions.' );
		}
		$start  = microtime( true );
		$result = $function_name();

		// The function returned a value?
		if ( isset( $result ) ) {
			fd_code( $result );
			$end = microtime( true );
			echo '<p><strong>Completed in: </strong>' .
			     number_format( $end - $start, 8 ) .
			     ' seconds</p>';
		} else {
			self::stopAjax( "Nothing to show..." ); // Clear the previous result.
		}

		die();
	}

	protected static function stopAjax( $message = false ) {
		if ( $message ) {
			echo '<div class="notice inline notice-error notice-alt"><p>' . $message . '</p></div>';
		}
		die();
	}

	public function settings() {
		return [
			'label' => __( 'JSON Parser', 'fastdev' ),
		];
	}

	public function tip() {
		return __( 'Parse plain JSON in an human readable tree.', 'fastdev' );
	}

	public function page() {
		$form = '<form method="post" class="fd-form js-fastdev-json-parser-form">';

		$form .= '<div class="field">
				<textarea id="js-json-string" 
				class="js-json-string full-textarea" 
				placeholder="' . esc_html__( 'Enter the JSON string here and press "Parse"', 'fastdev' ) . '"
				data-gramm_editor="false"></textarea>
				
				<div class="cursor-position-reveal">
					' . __( 'Cursor postion', 'fastdev' ) . ': <span id="js-cursor-position-reveal">-</span>
				</div>
				' . get_submit_button( __( 'Parse', 'fastdev' ), 'primary', 'submit', false ) . '
			</div>';

		$form .= '</form>';

		echo $form;

		echo '<h3>Result:</h3>';
		echo '<div id="js-fastdev-json-parser-result" class="fastdev-json-parser-result"></div>';
	}

}
