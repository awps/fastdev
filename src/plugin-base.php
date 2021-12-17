<?php
/**
 * Plugin Name: FastDev
 * Plugin URI:  https://zerowp.com/fastdev
 * Description: Helpful information and tools for WordPress developers.
 * Author:      Andrei Surdu
 * Author URI:  http://zerowp.com/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: fastdev
 * Domain Path: /languages
 * Requires PHP: 5.3
 * Requires at least: 4.4
 * Version: 1.7.2
 */

// Do not allow direct access to this file.
if ( ! function_exists( 'add_action' ) ) {
	die();
}

define( 'FASTDEV_VERSION', '1.7.2' );

function fastdev_version() {
	return FASTDEV_VERSION;
}

// Constants
if ( ! defined( 'FASTDEV_FILE' ) ) {
	define( 'FASTDEV_FILE', __FILE__ );
}
if ( ! defined( 'FASTDEV_PATH' ) ) {
	define( 'FASTDEV_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'FASTDEV_URI' ) ) {
	define( 'FASTDEV_URI', plugin_dir_url( __FILE__ ) );
}

/**
 * Load translations
 *
 * Load plugin translations.
 *
 * @return void
 */
add_action( 'init', 'fastdev_load_textdomain' );
function fastdev_load_textdomain() {
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
if ( ! function_exists( 'fastdev_path' ) ) {
	function fastdev_path( $name = false, $return = true, $return_uri = false ) {
		//Return the path or uri.
		$dir = ( $return_uri ) ? FASTDEV_URI : FASTDEV_PATH;

		//If the folder name is set
		$path = ( $name && ! empty( $name ) ) ? $dir . $name . '/' : $dir;

		//Return or echo the result
		if ( $return ) {
			return $path;
		}

		echo $path;
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
if ( ! function_exists( 'fastdev_uri' ) ) {
	function fastdev_uri( $name = false, $return = true ) {
		return fastdev_path( $name, $return, true );
	}
}

function fd_search() {
	echo '<input type="text" class="fd-filter-field" placeholder="' . __( 'Search...', 'fastdev' ) . '" autofocus />';
}

function fd_nonce_action( $id ) {
	return 'fastdev' . PHP_VERSION . home_url() . $id;
}

function fd_get_temp_url( $id ) {
	$key = wp_create_nonce( fd_nonce_action( $id ) );

	return trailingslashit( home_url() ) . 'fastdev/' . $key;
}

function fd_temp_url_is_valid( $id ) {
	$point = get_query_var( 'fastdev', null );

	if ( empty( $point ) ) {
		return false;
	}

	$nonce = wp_verify_nonce( $point, fd_nonce_action( $id ) );

	return $nonce;
}

function fd_create_temp_link( $id ) {
	echo '<div class="fd-access-url">';
	$link = fd_get_temp_url( $id );
	echo __( 'Temporary public link to this table', 'fastdev' ) . ': <a href="' . $link . '" target="_blank">' . $link . '</a>';
	echo '<em>' . __( 'This link is available for approx. 12 hours', 'fastdev' ) . '</em>';
	echo '</div>';
}

/**
 * Code debug
 *
 * @param        $code
 * @param bool $escape
 * @param string $language
 */
function fd_code( $code, $escape = false, $language = 'php' ) {
	$serialized = maybe_serialize( $code );
	$size       = strlen( $serialized );

	$class = ( 50000 > $size ) ? ' class="language-' . $language . '"' : ' class="disable-highlight"';

	echo '<pre' . $class . ' data-size="' . esc_attr( $size ) . '""><code' . $class . '>';
	if ( is_array( $code ) && $escape ) {
		print_r( esc_html( var_export( $code, true ) ) );
	} elseif ( is_string( $code ) && $escape ) {
		print_r( esc_html( $code ) );
	} else {
		print_r( $code );
	}
	echo '</code></pre>';
}

//------------------------------------//--------------------------------------//

/**
 * Get all classes from a project.
 *
 * Return an array containing all classes defined in a project.
 *
 * @param string $project_path
 *
 * @return array
 */
function smk_get_classes_from_project( $project_path ) {
	// Placeholder for my classes
	$classes = array();

	// Get all classes
	$dc = get_declared_classes();

	// Loop
	foreach ( $dc as $class ) {
		$reflect = new \ReflectionClass( $class );

		// Get the path to the file where is defined this class.
		$filename = $reflect->getFileName();

		// Only user defined classes, exclude internal or classes added by PHP extensions.
		if ( ! $reflect->isInternal() ) {
			// Replace backslash with forward slash.
			$filename     = str_replace( array( '\\' ), array( '/' ), $filename );
			$project_path = str_replace( array( '\\' ), array( '/' ), $project_path );

			// Remove the last slash.
			// If last slash is present, some classes from root will not be included.
			// Probably there's an explication for this. I don't know...
			$project_path = rtrim( $project_path, '/' );

			// Add the class only if it is defined in "my-project-name" dir.
			if ( stripos( $filename, $project_path ) !== false ) {
				$classes[] = $class;
			}
		}
	}

	return $classes;
}

include fastdev_path() . 'autoloader.php';

$fastdev_page = new Fastdev\MainPage( 'fd-main' );
$fastdev_page->init();

$wpo = new Fastdev\Options( 'options', 'fd-main' );
$wpo->registerAjax();

(new Fastdev\Hooks( 'hooks', 'fd-main' ));
(new Fastdev\Classes( 'classes', 'fd-main' ));
(new Fastdev\Functions( 'functions', 'fd-main' ));
(new Fastdev\UserMeta( 'fd-user-meta', 'fd-main' ));
(new Fastdev\PostMeta( 'fd-post-meta', 'fd-main' ));
(new Fastdev\PhpInfo( 'fd-phpinfo', 'fd-main' ));
(new Fastdev\Constants( 'fd-wp-constants', 'fd-main' ));
(new Fastdev\MySQLInfo( 'fd-mysqlinfo', 'fd-main' ));
(new Fastdev\RegisteredWidgetsList( 'fd-wpregisteredwidgetslist', 'fd-main' ));
(new Fastdev\Sidebars( 'fd-sidebars', 'fd-main' ));
(new Fastdev\Mimes( 'fd-wp-mimes', 'fd-main' ));

$testing = new Fastdev\Testing( 'fd-testing', 'fd-main' );
$testing->registerAjaxHook();

$json_parser = new Fastdev\JsonParse( 'fd-json-parser', 'fd-main' );
$json_parser->registerAjaxHook();

new Fastdev\AdminBarInfo();

new Fastdev\Endpoint();

/*
-------------------------------------------------------------------------------
Benchmark
-------------------------------------------------------------------------------
*/
$fd_bench_start = microtime( true );
add_filter( 'fastdev_admin_bar_top_menu_title', function ( $title, $id ) {
	global $fd_bench_start;

	return $title . ' (' . number_format( microtime( true ) - $fd_bench_start, 2 ) . 's)';
}, 10, 2 );
