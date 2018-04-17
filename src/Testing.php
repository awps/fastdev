<?php

namespace Fastdev;

class Testing extends Tab {

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

		$result = $function_name();

		// The function returned a value?
		if ( isset( $result ) ) {
			fd_code( $result );
		} else {
			self::stopAjax( "Nothing to show..." ); // Clear the previous result.
		}

		die();
	}

	protected static function stopAjax( $message ) {
		echo '<div class="notice inline notice-error notice-alt"><p>' . $message . '</p></div>';
		die();
	}

	public function settings() {
		return [
			'label' => __( 'Testing', 'fastdev' ),
		];
	}

	public function tip() {
		$content = '<h4>' . __( 'Run testing functions.', 'fastdev' ) . '</h4>';

		$content .= sprintf(
			__( 'The function name must start with the prefix "%s" and must not have any required parameters.',
				'fastdev' ),
			'<code>test</code>'
		);

		return $content;
	}

	public function page() {
		$form = '<form method="post" class="fd-form js-fastdev-testing-form">';

		$form .= '<div class="field">
				<input type="text" 
						value="" 
						name="function_name" 
						class="regular-text" 
						placeholder="' . __( 'function name', 'fastdev' ) . '">
				
				<input type="hidden" value="' . wp_create_nonce( 'fastdev_testing' ) . '" name="nonce">
				' . get_submit_button( __( 'Execute', 'fastdev' ), 'primary', 'submit', false ) . '
				&nbsp;&nbsp;&nbsp;<label class="inline-label">
				    <input type="checkbox" value="1" name="autorefresh" id="testing-autorefresh">
				    Auto refresh
				</label>
			</div>';

		$form .= '</form>';

		echo $form;

		echo '<h3>Result:</h3>';
		echo '<div id="js-fastdev-testing-result" class="fastdev-testing-result"></div>';
	}

}
