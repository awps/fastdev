<?php

namespace Fastdev;

class Hooks extends Tab{

	public function settings(){
		return array(
			'label' => __('Hooks', 'fastdev')
		);
	}

	public function makeTable( $options ){
		if( is_array($options) ){
			ksort($options);
			$output = '<div class="fd-key-val-table">';
				foreach ($options as $key => $value) {
					$output .= '<div class="fd-kv-row cols-100">';
						$output .= '<div class="filter-this"><div class="fd-kv-code"><a href="'. add_query_arg( 'fd-get-hook', $key ) .'">'. $key .'</a></div></div>';
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
		global $wp_filter;

		if( !empty($_GET['fd-get-hook']) ){
			$hook = sanitize_title( $_GET['fd-get-hook'] );

			if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
				return;

			echo '<h3>'. $hook .'</h3>';
			fd_code( $wp_filter[$hook] );
		}
		else{
			fd_search();
			$this->makeTable($wp_filter);
		}
		
	}

}