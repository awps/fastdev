<?php
namespace Fastdev;

class AdminBarInfo{
	
	public function __construct(){
		add_action( 'admin_bar_menu', array( $this, 'conditionalTags' ), 199 );
		add_action( 'admin_bar_menu', array( $this, 'currentObjectId' ), 249 );
		add_action( 'admin_bar_menu', array( $this, 'currentSiteId' ), 299 );
		add_action( 'admin_bar_menu', array( $this, 'quickMenus' ), 999 );
	}

	public function conditionalTags(){
		if ( ! current_user_can('manage_options') )
			return;

		global $wp_admin_bar;

		$cond = $this->conditionals();

		if( !empty($cond) ){

			$cond = array_map( function($v){
				return '<em style="display: block; color: #90cdf7;">'. $v .'</em>';
			}, $cond );

			foreach ($cond as $c => $t) {
				$wp_admin_bar->add_node(array(
					'id' => 'fastdev-conditionals-'. $c,
					'parent' => 'fd-main',
					'title' => $t,
				));
			}
		}
	}

	public function currentObjectId(){
		if ( ! current_user_can('manage_options') )
			return;
		
		global $wp_admin_bar;

		$id = get_queried_object_id();
		$title = __( 'Current ID:', 'fastdev' );

		if( is_page() ){
			$title = __( 'Page ID:', 'fastdev' );
		}
		elseif( is_single() ){
			$title = __( 'Post ID:', 'fastdev' );
		}
		elseif( is_author() ){
			$title = __( 'User ID:', 'fastdev' );
		}

		if( !empty( $id ) ){
			$wp_admin_bar->add_node(array(
				'id' => 'fastdev-ab-current-id',
				'parent' => 'fd-main',
				'title' =>'<em style="display: block; color: #90cdf7;">'. $title . ' ' . esc_attr( $id ) .'</em>',
			));
		}
	}

	public function currentSiteId(){
		if ( ! current_user_can('manage_options') )
			return;
		
		global $wp_admin_bar;

		if( is_multisite() ){
			$title = __( 'Current Site ID:', 'fastdev' );
			$wp_admin_bar->add_node(array(
				'id' => 'fastdev-ab-current-id',
				'parent' => 'fd-main',
				'title' =>'<em style="display: block; color: #90cdf7;">'. $title . ' ' . esc_attr( get_current_blog_id() ) .'</em>',
			));
		}
	}

	public function conditionals(){

		$conds = array(
			'is_404',
			'is_admin',
			'is_archive',
			'is_attachment',
			'is_author',
			'is_blog_admin',
			'is_category',
			'is_comment_feed',
			'is_customize_preview',
			'is_date',
			'is_day',
			'is_embed',
			'is_feed',
			'is_front_page',
			'is_home',
			'is_main_network',
			'is_main_site',
			'is_month',
			'is_network_admin',
			'is_page',
			'is_page_template',
			'is_paged',
			'is_post_type_archive',
			'is_preview',
			'is_robots',
			'is_rtl',
			'is_search',
			'is_single',
			'is_singular',
			'is_ssl',
			'is_tag',
			'is_tax',
			'is_time',
			'is_trackback',
			'is_user_admin',
			'is_year',
		);

		$math = array();

		foreach ( $conds as $cond ) {
			if ( function_exists( $cond ) ) {

				if ( call_user_func( $cond ) ) {
					if( ! is_multisite() && in_array( $cond, array( 'is_main_network', 'is_main_site' ) ) )
						continue;
					
					$math[$cond] = $cond;
				}

			}
		}

		return $math;
	}

	public function quickMenus(){
		if ( ! current_user_can('manage_options') )
			return;
		
		global $wp_admin_bar;

		$menus = array();

		$post_types = get_post_types( array(
			'public' => true
		), 'objects' );

		if( !empty($post_types) ){
			foreach ($post_types as $key => $value) {
				$menus[] =array(
					'title' =>  $value->label,
					'href' => 'edit.php?post_type='. $key
				);
			}
		}

		$menus[] = array(
			'title' => __( 'Plugins', 'fastdev' ),
			'href' => 'plugins.php',
		);

		$menus[] = array(
			'title' => __( 'Users', 'fastdev' ),
			'href' => 'users.php',
		);

		$menus[] = array(
			'title' => __( 'Settings', 'fastdev' ),
			'href' => 'options-general.php',
		);

		// Create menus
		if( is_array( $menus ) ){
			foreach ($menus as $key => $m) {
				if( empty($m['title']) )
					continue; // This menu item does not have a title


				$wp_admin_bar->add_node(array(
					'id' => 'fd-quick-menu-' . sanitize_title( $m['title'] ),
					'parent' => 'site-name',
					'title' => $m['title'],
					'href' => admin_url( $m['href'] )
				));
			}	
		}
		
	}

}