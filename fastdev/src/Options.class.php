<?php

namespace Fastdev;

class Options extends Tab{

	public function settings(){
		return array(
			'label' => __('WP Options', 'fastdev')
		);
	}

	public function makeTable( $options ){
		if( is_array($options) ){
			ksort($options);
			$output = '<div class="fd-key-val-table">';

				$output .= '<div class="fd-kv-row fd-kv-head">';
					$output .= '<div><div class="fd-kv-code">'. __('Option ID', 'fastdev') .'</div></div>';
					$output .= '<div><div class="fd-kv-code">'. __('Value', 'fastdev') .'</div></div>';
				$output .= '</div>';

				foreach ($options as $key => $value) {
					$output .= '<div class="fd-kv-row">';
						$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="'. add_query_arg( 'fd-get-option', $key ) .'">'. $key .'</a></div></div>';
						$output .= '<div class="filter-this"><div class="fd-kv-code">'. esc_html( $value ) .'</div></div>';
					$output .= '</div>';
				}
			$output .= '</div>';
			echo $output;
		}
		else{
			fd_code( $options );
		}

	}

	public function page(){
		if( !empty($_GET['fd-get-option']) ){
			$option = sanitize_title( $_GET['fd-get-option'] );
			$data_attr = ' data-option="'. $option .'"';
			$data_attr .= ' data-nonce="'. wp_create_nonce( $option ) .'"';

			$action_buttons = '<span id="fd-refresh-option" class="fd-button-refresh"'. $data_attr .'>'. __('Refresh', 'fastdev') .'</span>';
			$action_buttons .= '<label class="fd-auto-refresh"><input type="checkbox" id="fd-auto-refresh"> '. __('Auto refresh', 'fastdev') .'</label>';
			$action_buttons .= '<span id="fd-delete-option" class="fd-button-delete"'. $data_attr .'>'. __('Delete option', 'fastdev') .'</span>';

			echo '<h3>'. $option . $action_buttons .'</h3>';
			echo '<div id="fd-wpo-code-block">';
				fd_code( get_option( $option ), true );
			echo '</div>';
		}
		else{
			fd_search();
			$all_wpo = wp_load_alloptions();
			$this->makeTable($all_wpo);
		}

	}

	public function registerAjax(){
		add_action( 'wp_ajax_fastdev_delete_option', array( $this, 'deleteOption' ) );
		add_action( 'wp_ajax_fastdev_refresh_option', array( $this, 'refreshOption' ) );
	}

	public function canProcess(){
		if( empty($_POST['option_id']) )
			return false;

		$option = sanitize_title( $_POST['option_id'] );

		if( ! wp_verify_nonce( $_POST['nonce'], $option ) )
			return false;

		return $option;
	}

	public function refreshOption(){
		if( ( $option = $this->canProcess() ) === false )
			$this->ajaxMessage( __('Error. Please refresh the page.', 'fastdev') );

		if( ($opt = get_option( $option )) !== false ){
			fd_code( $opt, true );
		}
		else{
			$this->ajaxMessage( __('Option does not exists.', 'fastdev') );
		}

		die();
	}

	public function deleteOption(){
		if( ( $option = $this->canProcess() ) === false )
			$this->ajaxMessage( __('Error. Please refresh the page.', 'fastdev') );

		if( $opt = delete_option( $option ) ){
			$this->ajaxMessage( __('Option deleted.', 'fastdev') );
		}
		else{
			$this->ajaxMessage( __('Can\'t delete this option.', 'fastdev') );
		}

		die();
	}

	public function ajaxMessage( $msg, $type = 'error' ){
		die( '<div class="fastdev-ajax-message is-'. $type .'">'. $msg .'</div>' );
	}

}