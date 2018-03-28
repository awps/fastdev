<?php

namespace Fastdev;

class PhpInfo extends Tab {

	public function settings() {
		return array(
			'label' => __( 'PHP', 'fastdev' ),
		);
	}

	public function page() {
		fd_search();
		fd_create_temp_link( $this->tab_id );

		$this->pageContent();
	}

	public function pageContent() {
		ob_start();
		phpinfo();
		$pinfo = ob_get_contents();
		ob_end_clean();

		$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo );

		echo '<div id="phpinfo">' . $pinfo . '</div>';
	}

}
