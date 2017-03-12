<?php

namespace Fastdev;

class PhpInfoPage extends Tab{

	public function settings(){
		return array(
			'label' => __('PHP', 'fastdev')
		);
	}

	public function page(){
		fd_search();
		
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();
		 
		$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms','$1',$pinfo);
		
		echo '<div id="phpinfo">'. $pinfo .'</div>';
		
	}

}