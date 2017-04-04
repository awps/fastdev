<?php
namespace Fastdev;

class AdminBarInfo{
	
	public function __construct(){
		add_action( 'admin_bar_menu', array($this, 'adminBar'), 199 );
	}

	public function adminBar(){
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
}