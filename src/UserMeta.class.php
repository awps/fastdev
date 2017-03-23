<?php

namespace Fastdev;

class UserMeta extends Tab{

	public function settings(){
		return array(
			'label' => __('User Meta', 'fastdev')
		);
	}

	public function makeTable( $options, $user_id ){
		
		if( is_array($options) ){
			ksort($options);
			$output = '<div class="fd-key-val-table">';
				foreach ($options as $key => $value) {
					$output .= '<div class="fd-kv-row cols-30x40x30">';
						$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="'. add_query_arg( array('fd-get-user-meta' => $key, 'fd-get-user-id' => $user_id) ) .'">'. $key .'</a></div></div>';
						$output .= '<div><div class="fd-kv-code">'. esc_html($value[0]) .'</div></div>';
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

		$field_value = !empty($_POST['fd-get-username']) ? $_POST['fd-get-username'] : '';
		$btn_label   = !empty($_POST['fd-get-username']) ? __('Refresh user details', 'fastdev') : __('Get user details', 'fastdev');
		$show_field  = !empty($_POST['fd-get-username']) ? ' style="display: none;"' : '';
		
		$form = '<form method="post" class="fd-form">';
			
			$form .= '<div class="field"'. $show_field .'><label>'. __('Username', 'fastdev') .'
				</label> <input type="text" value="'. $field_value .'" name="fd-get-username" class="regular-text">
			</div>';

			$form .= get_submit_button( $btn_label );

		$form .= '</form>';

		echo $form;

		if( !empty($_POST['fd-get-username']) ){
			$username = $_POST['fd-get-username'];
			$user = get_user_by( 'login', $username );
			if( $user ){
				echo '<h3>'. sprintf( __( 'Meta data for %s(ID: %d)', 'fastdev' ), $username, $user->ID ) .'</h3>';
				$meta = get_user_meta($user->ID);
				$this->makeTable( $meta, $user->ID );
			}
			else{
				_e('User not found!', 'fastdev');
			}
		}
		elseif( !empty($_GET['fd-get-user-meta']) && !empty($_GET['fd-get-user-id']) ){
			$meta = get_user_meta( absint( $_GET['fd-get-user-id'] ), esc_html( $_GET['fd-get-user-meta'] ) );

			echo '<h3>'. $_GET['fd-get-user-meta'] .'</h3>';
			fd_code( $meta );
		}
	}

}