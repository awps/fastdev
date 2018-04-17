<?php

namespace Fastdev;

class Endpoint {

	public $endpoint = 'fastdev';

	public function __construct() {
		add_action( 'init', array( $this, 'add' ) );
		add_action( 'template_redirect', array( $this, 'redirect' ) );
		register_activation_hook( FASTDEV_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( FASTDEV_FILE, array( $this, 'deactivate' ) );
	}

	function add() {
		add_rewrite_endpoint( $this->endpoint, EP_ALL );
	}

	function redirect() {
		$point = get_query_var( $this->endpoint, null );

		if ( empty( $point ) ) {
			return;
		}

		// Show the content here
		$this->showContent();
		exit;
	}

	function showContent() {
		?>

        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <title>Fastdev</title>

            <meta name="viewport" content="width=device-width,initial-scale=1.0">

            <link rel='stylesheet' href='<?php echo fastdev_uri( 'assets' ) . 'style.css'; ?>'/>

        </head>
        <body class="fd-frontend">
        <div class="fd-front-inner">

			<?php

			if ( fd_temp_url_is_valid( 'fd-main' ) ) {
				$main_info = new MainPage();
				$main_info->makeTable( $main_info->sysArray() );
			} elseif ( fd_temp_url_is_valid( 'fd-phpinfo' ) ) {
				$main_info = new PhpInfo();
				$main_info->pageContent();
			} else {
				?>
                <div class="fd-fail">
                    <h3>The info is not available</h3>
                    <p>You may see this because the link expired or is not valid.</p>
                </div>
				<?php

			}
			?>

            <div class="fd-copy">Powered by <a href="http://zerowp.com/fastdev">FastDev</a></div>

        </div>
        </body>
        </html>


		<?php

	}

	function activate() {
		// ensure our endpoint is added before flushing rewrite rules
		$this->add();

		// flush rewrite rules - only do this on activation as anything more frequent is bad!
		flush_rewrite_rules();
	}

	function deactivate() {
		// flush rules on deactivate as well so they're not left hanging around uselessly
		flush_rewrite_rules();
	}

}
