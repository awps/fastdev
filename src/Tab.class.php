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

	public function __construct($tab_id, $parent_page_id = false){
		if( isset($parent_page_id) ){
			$this->parent_page = new Page( $parent_page_id );
			$this->parent_page->add_tabs( array( $this, 'tab' ) ); 
		}
		$this->tab_id = $tab_id;
	}

	public function settings(){
		return array();
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

	public function tab($tabs){
		$tabs[] = $this->getSettings();
		return $tabs;
	}

}