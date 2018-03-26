<?php
/** 
* Admin page tab
*
* Create a tab on an existing page created with Fastdev\Page
* 
* @since 1.0
*
*/
namespace Fastdev;

class Tab{
	
	public $parent_page;
	public $tab_id;

	public function __construct($tab_id = false, $parent_page_id = false){
		$this->parent_page_id = $parent_page_id;
		$this->tab_id = $tab_id;

		if( !empty($parent_page_id) ){
			$this->parent_page = new Page( $parent_page_id );
			$this->parent_page->add_tabs( array( $this, 'tab' ) );
			add_action( 'admin_bar_menu', array($this, 'adminBar'), 99 );
		}
	}

	public function adminBar(){
		global $wp_admin_bar;

		$wp_admin_bar->add_node(array(
			'id' => $this->tab_id,
			'parent' => $this->parent_page_id,
			'title' => $this->getSetting( 'label' ),
			'href' => admin_url( add_query_arg( 'tab', $this->tab_id, 'admin.php?page=' . $this->parent_page_id ) )
		));
	}

	public function settings(){
		return array();
	}
	
	public function getSetting( $key ){
		$s = $this->getSettings();
		return isset($s[ $key ]) ? $s[ $key ] : null;
	}

	public function getSettings(){
		return wp_parse_args( 
			array( 
				'id' => $this->tab_id, 
				'callback' => array( $this, 'page' ) 
			), 
			$this->settings()
		);
	}

	public function page(){
		_e('Congrats! you\'ve created a new page tab.', 'fastdev');
	}

	public function pageContent(){}

	public function tab($tabs){
		$tabs[] = $this->getSettings();
		return $tabs;
	}

}
