<?php

namespace Fastdev;

class MainPage extends Page{
	public function enqueue(){
		wp_register_style( 'fastdev_css', fastdev_uri('assets') . 'style.css' );
		wp_register_script( 'fastdev_js', fastdev_uri('assets') . 'scripts.js' );
		wp_register_style( 'fastdev_prism_css', fastdev_uri('assets') . 'prism.css' );
		wp_register_script( 'fastdev_prism_js', fastdev_uri('assets') . 'prism.js' );
		wp_enqueue_style( 'fastdev_prism_css' );
		wp_enqueue_style( 'fastdev_css' );

		wp_enqueue_script( 'fastdev_prism_js' );
		wp_enqueue_script( 'fastdev_js' );
	}
	public function settings(){
		return array(
			'menu_type'     => 'menu',
			'menu_title'    => __('Fastdev', 'fastdev'),
		);
	}

	public function makeTable( $options ){
		if( is_array($options) ){
			ksort($options);
			$output = '<div class="fd-key-val-table">';
				foreach ($options as $key => $value) {

					//Value
					$val = isset( $value[1] ) ? $value[1] : '';

					//Title
					$title = $value[0];
					if('{section_title}' === $val){
						$title = '<h3>'. $title .'</h3>';
						$val = '';
					}

					//Status notice
					$good_or_bad = isset($value[2]) ? $value[2] : 'neutral';
					$notice = isset($value[3]) ? $value[3] : '';
					if( $good_or_bad == 'good' ){
						$notice = '';
					}

					//Markup
					$output .= '<div class="fd-kv-row">';
						$output .= '<div class="filter-this"><div class="fd-kv-code">'. $title .'</div></div>';
						$output .= '<div><div class="fd-kv-code"><span class="'. $good_or_bad .'">'. $val .'</span> '. $notice .'</div></div>';
					$output .= '</div>';

				}
			$output .= '</div>';
			echo $output;
		}
		else{
			fd_code( $options );
		}

	}

	public function activeTheme(){
		if( get_bloginfo( 'version' ) < '3.4' ) {
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$theme      = $theme_data['Name'] . ' ' . $theme_data['Version'];
		} else {
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name . ' ' . $theme_data->Version;
		}
		return $theme;
	}

	public function sysArray(){
		$sys = array();

		/* WordPress Installation
		------------------------------*/
		$sys[] = array( __('Active theme', 'fastdev'), $this->activeTheme() );
		$sys[] = array( __('Site URL', 'fastdev'), site_url() );
		$sys[] = array( __('Home URL', 'fastdev'), home_url() );
		$sys[] = array( __('Multisite', 'fastdev'), ( is_multisite() ? 'Yes' : 'No' ) );

		$cur = get_preferred_from_update_core();
		$wp_version_gb = ( version_compare($cur->current, get_bloginfo( 'version' )) > 0 ) ? 'bad' : 'good';
		$sys[] = array( __('WordPress Version', 'fastdev'), get_bloginfo( 'version' ), $wp_version_gb, 'The latest WP version is ' . $cur->current );

		$sys[] = array( __('WordPress Language', 'fastdev'), ( defined( 'WPLANG' ) && WPLANG ? WPLANG : 'en_US' ) );
		$sys[] = array( __('Permalink Structure', 'fastdev'), ( get_option( 'permalink_structure' ) ? '<code>'. get_option( 'permalink_structure' ) .'</code>' : 'Default' ) );
		$sys[] = array( __('Show On Front', 'fastdev'), get_option( 'show_on_front' ) );

		// Plugins info
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_option('active_plugins');
		if( !empty($all_plugins) ){
			$active_plugins = '';
			foreach ($all_plugins as $plugin_key => $plugin_path) {
				$active_plugins .= $plugin_path . '<br>';
			}

			$sys[] = array( __('Active plugins', 'fastdev'), $active_plugins );
		}

		/* System Info
		------------------*/
		$sys[] = array( __('System:', 'fastdev'), '{section_title}' );
		$sys[] = array( __('Operating system', 'fastdev'), PHP_OS );
		$sys[] = array( __('Server Software', 'fastdev'), $_SERVER["SERVER_SOFTWARE"] );
		$sys[] = array( __('PHP Version', 'fastdev'), PHP_VERSION, ( version_compare(PHP_VERSION, '5.4.0') >= 0 ) ? 'good' : 'notsogood', __('PHP 5.4+ is recommended.', 'fastdev') );
		$sys[] = array( __('MySQL Version', 'fastdev'), $this->mySQLVersion() );
		$sys[] = array( __('GD Version', 'fastdev'), $this->gdVersion() );
		$sys[] = array( __('Hostname', 'fastdev'), $_SERVER['SERVER_NAME'] );
		$sys[] = array( __('Server IP', 'fastdev'), $_SERVER['SERVER_ADDR']);
		$sys[] = array( __('Server Port', 'fastdev'), $_SERVER['SERVER_PORT'] );
		$sys[] = array( __('Server Document Root', 'fastdev'), $_SERVER['DOCUMENT_ROOT'] );
		$sys[] = array( __('Server Admin', 'fastdev'), (isset($_SERVER['SERVER_ADMIN']) ? $_SERVER['SERVER_ADMIN']: '') );
		$sys[] = array( __('Server Time', 'fastdev'), mysql2date(sprintf(__('%s - %s', 'fastdev'), get_option('date_format'), get_option('time_format')), current_time('mysql')) );

		return $sys;
	}


	public function page(){
		$this->makeTable( $this->sysArray() );
	}

	public function mySQLVersion() {
		global $wpdb;
		return $wpdb->get_var("SELECT VERSION() AS version");
	}

	public function gdVersion(){
		if( function_exists('gd_info') ){
			$gd = gd_info();
			return $gd["GD Version"];
		}
		else{
			return __('Unknown', 'fastdev');
		}
	}
}