<?php
/**
 * Plugin Name: FastDev
 * Plugin URI:  http://zerowp.com/fastdev
 * Description: Provides helpful information and functions for WordPress developers to make the development even faster.
 * Author:      ZeroWP Team
 * Author URI:  http://zerowp.com/
 * Version:     1.3.1
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: fastdev
 * Domain Path: /languages
 */

// Do not allow direct access to this file.
if( ! function_exists('add_action') )
	die();

function fastdev_version(){

	return '1.3.1';
	
}

// Constants
if( ! defined('FASTDEV_PATH') )
	define( 'FASTDEV_PATH', plugin_dir_path(__FILE__) );
if( ! defined('FASTDEV_URI') )
	define( 'FASTDEV_URI', plugin_dir_url(__FILE__) );


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
	$serialized = maybe_serialize( $code );
	$size = strlen( $serialized );

	$class = ( 50000 > $size ) ? ' class="language-'. $language .'"' : ' class="disable-highlight"';

	echo '<pre'. $class .' data-size="'. esc_attr( $size ) .'"><code'. $class .'>';
	if( is_array($code) && $escape ){
		print_r( esc_html( var_export( $code, true ) ) );
	}
	elseif( is_string($code) && $escape ){
		print_r( esc_html( $code ) );
	}
	else{
		print_r( $code );
	}
	echo '</code></pre>';
}


include fastdev_path() . 'autoloader.php';

$fastdev_page = new Fastdev\MainPage('fd-main');
$fastdev_page->init();


$wpo = new Fastdev\Options( 'options', 'fd-main' );
$wpo->registerAjax();


new Fastdev\Hooks( 'hooks', 'fd-main' );


new Fastdev\Classes( 'classes', 'fd-main' );


new Fastdev\Functions( 'functions', 'fd-main' );


new Fastdev\UserMeta( 'fd-user-meta', 'fd-main' );


new Fastdev\PhpInfo( 'fd-phpinfo', 'fd-main' );


new Fastdev\Constants( 'fd-wp-constants', 'fd-main' );


new Fastdev\MySQLInfo( 'fd-mysqlinfo', 'fd-main' );


new Fastdev\RegisteredWidgetsList( 'fd-wpregisteredwidgetslist', 'fd-main' );


new Fastdev\Mimes( 'fd-wp-mimes', 'fd-main' );

new Fastdev\AdminBarInfo;

/*
-------------------------------------------------------------------------------
Benchmark
-------------------------------------------------------------------------------
*/
$fd_bench_start = microtime(true);
add_filter( 'fastdev_admin_bar_top_menu_title', function( $title, $id ){
	global $fd_bench_start;

	return $title .' ('. number_format( microtime(true) - $fd_bench_start, 2 ) .'s)';
}, 10, 2 );