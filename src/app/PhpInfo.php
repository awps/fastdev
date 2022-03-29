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

		$this->pageContent();
	}

	public function pageContent() {
		ob_start();
		call_user_func('phpinfo'); // phpcs: WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_phpinfo -- This is part of the tool. Displayed internally in WP admin.
		$pinfo = ob_get_contents();
		ob_end_clean();

		$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo );

		echo '<div id="phpinfo">' . wp_kses_post($pinfo) . '</div>';
	}

}
