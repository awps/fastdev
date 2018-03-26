<?php

namespace Fastdev;

class Testing extends Tab {

	public function registerAjaxHook() {
		add_action( 'wp_ajax_fastdev_testing', array( $this, 'ajax' ) );
	}

	public function ajax() {
		$result = array();

		if ( empty( $_POST['nonce'] ) ) {
			self::stopAjax( 'Invalid nonce.' );
		}

		if ( empty( $_POST['function_name'] ) ) {
			self::stopAjax( 'Invalid function name.' );
		}

		$function_name = 'test_fd_' . preg_replace( '/[^a-zA-Z0-9_]/', '', $_POST['function_name'] );
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
		}

		die();
	}

	protected static function stopAjax( $message ) {
		echo $message;
		die();
	}

	public function settings() {
		return array(
			'label' => __( 'Testing', 'fastdev' ),
		);
	}

	public function page() {
		?>
        <div class="notice inline notice-warning notice-alt">
            <p>
				<?php _e( 'This page allows to run testing functions.', 'fastdev' ); ?>
				<?php printf(
					__( 'The function must have the %s prefix and must not have required parameters.', 'fastdev' ),
					'<code>test_fd_</code>' ); ?>
				<?php _e( 'The function may return or print(echo) the data.', 'fastdev' ); ?>
				<?php _e( 'It\'s usefull to debug(dump) some data without reloading the page.', 'fastdev' ); ?>
            </p>
        </div>
		<?php

		$form = '<form method="post" class="fd-form js-fastdev-testing-form">';

		$form .= '<div class="field"><label>' . __( 'Function name(without prefix)', 'fastdev' ) . '
				</label> 
				<input type="text" value="" name="function_name" class="regular-text">
				<input type="hidden" value="' . wp_create_nonce( 'fastdev_testing' ) . '" name="nonce">
				' . get_submit_button( 'Test', 'primary', 'submit', false ) . '
				&nbsp;&nbsp;&nbsp;<label>
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
