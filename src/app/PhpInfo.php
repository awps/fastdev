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
		phpinfo();  // phpcs:ignore -- That's what we are looking for already. The full PHP info.
		$pinfo = ob_get_contents();
		ob_end_clean();

		$pinfo = preg_replace( '%^.*<body>(.*)</body>.*$%ms', '$1', $pinfo );

		echo '<div id="phpinfo">' . $pinfo . '</div>';  // phpcs:ignore -- It's coming from PHP already. No user input
	}

}
