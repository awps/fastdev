<?php
/**
 * Plugin Name: FastDev
 * Plugin URI:  http://zerowp.com/fastdev
 * Description: Provides helpful information and functions for WordPress developers to make the development even faster.
 * Author:      ZeroWP Team
 * Author URI:  http://zerowp.com/
 * Version:     1.2
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: fastdev
 * Domain Path: /languages
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') )
	die();

// Constants
if( ! defined('FASTDEV_PATH') )
	define( 'FASTDEV_PATH', plugin_dir_path(__FILE__) );
if( ! defined('FASTDEV_URI') )
	define( 'FASTDEV_URI', plugin_dir_url(__FILE__) );

/**
 * Plugin version
 *
 * Get the current plugin version.
 *
 * @return string
 */
function fastdev_version(){
	if( is_admin() ){
		$data = get_file_data( __FILE__, array( 'Version' ) );
		return empty( $data ) ? '' : $data[0];
	}
	else{
		return false;
	}
}

/**
 * Load translations
 *
 * Load plugin translations.
 *
 * @return void
 */
add_action( 'init', 'fastdev_load_translations' );
function fastdev_load_translations() {
	load_plugin_textdomain(
		'fastdev',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages/'
	);
}

/**
 * FastDev path
 *
 * Main function to get the path to a directory from plugin root
 *
 * @param string $name Directory name ex: 'mod'
 * @param bool $return Return or echo(boolean). Default return.
 * @param bool $return_uri Return the uri(not path) if is `true`.
 *
 * @return string The path or URI
 */
if( ! function_exists('fastdev_path') ){
	function fastdev_path($name = false, $return = true, $return_uri = false){
		//Return the path or uri.
		$dir = ( $return_uri ) ? FASTDEV_URI : FASTDEV_PATH;
		//If the folder name is set
		$path = ( $name && !empty($name) ) ? $dir . $name . "/" : $dir;
		//Return or echo the result
		if( $return ) {
			return $path;
		}
		else{
			echo $path;
		}
	}
}

//------------------------------------//--------------------------------------//

/**
 * FastDev uri
 *
 * Main function to get the uri to a directory from plugin root
 *
 * @param string $name Directory name ex: 'mod'
 * @param bool $return Return or echo(boolean). Default return.
 *
 * @return string The URI
 */
if( ! function_exists('fastdev_uri') ){
	function fastdev_uri($name = false, $return = true){
		return fastdev_path($name, $return, true);
	}
}

function fd_search(){
	echo '<input type="text" class="fd-filter-field" placeholder="'. __('Search...', 'fastdev') .'" autofocus />';
}

function fd_code( $code, $escape = false, $language = 'php' ){
	echo '<pre class="language-'. $language .'"><code class="language-'. $language .'">';
	if( is_array($code) && $escape ){
		print_r( array_map( '_fd_code_filter_callback', $code ) );
	}
	elseif( is_string($code) && $escape ){
		print_r( htmlspecialchars( $code ) );
	}
	else{
		print_r( $code );
	}
	echo '</code></pre>';
}

function _fd_code_filter_callback( $code_to_escape ){
	if( !empty($code_to_escape) && ! is_array($code_to_escape) ){
		return htmlspecialchars( $code_to_escape );
	}
	else{
		return $code_to_escape;
	}
}

if ( is_admin() ) {
	include fastdev_path() . 'autoloader.php';

	$fastdev_page = new Fastdev\MainPage('fd-main');
	$fastdev_page->init();


	$wpo = new Fastdev\Options( 'fd-wpoptions', 'fd-main' );
	$wpo->registerAjax();


	new Fastdev\WpHooksPage( 'fd-wp-hooks', 'fd-main' );


	new Fastdev\WpClassesPage( 'fd-wp-classes', 'fd-main' );


	new Fastdev\WpFunctionsPage( 'fd-wp-functions', 'fd-main' );


	new Fastdev\WpUserMetaPage( 'fd-user-meta', 'fd-main' );


	new Fastdev\PhpInfoPage( 'fd-phpinfo', 'fd-main' );


	new Fastdev\WpConstantsPage( 'fd-wp-constants', 'fd-main' );


	new Fastdev\MySQLInfoPage( 'fd-mysqlinfo', 'fd-main' );


	new Fastdev\WPRegisteredWidgetsList( 'fd-wpregisteredwidgetslist', 'fd-main' );


	new Fastdev\WpMimesPage( 'fd-wp-mimes', 'fd-main' );

}