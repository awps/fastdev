<?php

namespace Fastdev;

class JsonParse extends Tab {

	public function registerAjaxHook() {
		add_action( 'wp_ajax_fastdev_json_post_meta', [ $this, 'postMetaJson' ] );
	}

	public function postMetaJson() {
//		header( 'Content-Type: application/json' );

		if ( ! empty( $_GET['post_meta'] ) ) {
			$meta        = explode( ':', $_GET['post_meta'] );
			$post_id     = absint( $meta[0] );
			$meta_key    = trim( $meta[1] );
			$metadata    = get_post_meta( $post_id, $meta_key, true );
			$post_status = get_post_status( $post_id );

			if ( empty( $post_status ) ) {
				echo json_encode( [ 'error' => "The post #{$post_id} does not exists." ] );
			}

			if ( ! empty( $metadata ) ) {
				echo $metadata;
				die();
			} else {
				echo json_encode( [ 'error' => 'Empty meta value or invalid meta value.' ] );
				die();
			}
		}

		echo json_encode( [ 'error' => 'Invalid post ID or meta key' ] );
		die();
	}

	public function settings() {
		return array(
			'label' => __( 'JSON Parser', 'fastdev' ),
		);
	}

	/**
	 * Define the tooltip for this tab.
	 *
	 * @return string
	 */
	public function tip() {
		return __( 'Parse plain JSON in an human readable tree.', 'fastdev' );
	}

	public function page() {
		$form = '<form method="post" class="fd-form js-fastdev-json-parser-form fastdev-json-parser-form">';

		$form .= '<div class="field">
				<div id="js-json-parser-tabs" class="json-parser-tabs">
					<a href="#" class="js-jp-url active">' . __( 'URL', 'fastdev' ) . '</a>
					<a href="#" class="js-jp-string">' . __( 'String', 'fastdev' ) . '</a>
					<a href="#" class="js-jp-post-meta">' . __( 'Post meta', 'fastdev' ) . '</a>
				</div>
				
				<div class="js-jp-tab-url">
					<input type="url"  
					id="js-json-url" 
					class="js-json-url full-input" 
					placeholder="' . esc_html__( 'Enter the JSON URL here and press "Parse"', 'fastdev' ) . '"/>
				</div>
				
				<div class="js-jp-tab-string" style="display:none;">
					<textarea id="js-json-string" 
					class="js-json-string full-textarea" 
					placeholder="' . esc_html__( 'Enter the JSON string here and press "Parse"', 'fastdev' ) . '"
					data-gramm_editor="false"></textarea>
				</div>
				
				<div class="js-jp-tab-post-meta" style="display:none;">
					<input type="text"  
					id="js-json-post-meta" 
					class="js-json-post-meta full-input" 
					placeholder="' . esc_html__( 'Enter the post ID and meta key here and press "Parse"', 'fastdev' ) . '"/>
					<p class="description">' . sprintf( esc_html__(
				'Enter the post ID and meta key separated by a colon. Example: %s',
				'fastdev' ), '<code>187:my_meta_key</code>' ) . '</p>
				</div>
				
				<div class="cursor-position-reveal">
					' . __( 'Cursor position', 'fastdev' ) . ': <span id="js-cursor-position-reveal">-</span>
				</div>
				' . get_submit_button( __( 'Parse', 'fastdev' ), 'primary', 'submit', false ) . '
			</div>';

		$form .= '</form>';

		echo $form;

		echo '<h3>Result:</h3>';
		echo '<div class="fastdev-json-parser-expanders">';
		echo '<a href="#" class="js-fastdev-json-parser-collapse collapse">
			' . esc_html__( 'Collapse all', 'fastdev' ) . '
			</a>';
		echo '<a href="#" class="js-fastdev-json-parser-expand">
			' . esc_html__( 'Expand all', 'fastdev' ) . '
			</a>';
		echo '</div>';

		echo '<div id="js-fastdev-json-parser-result" class="fastdev-json-parser-result"></div>';
	}

}
