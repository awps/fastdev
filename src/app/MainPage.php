<?php

namespace Fastdev;

class MainPage extends Page
{

    public function settings()
    {
        return [
            'menu_type'         => 'menu',
            'menu_title'        => esc_html__('Fastdev', 'fastdev'),
            'default_tab_label' => esc_html__('Site info', 'fastdev'),
        ];
    }

    public function enqueue()
    {
        // Register styles
        wp_register_style('fastdev_prism', fastdev_uri('assets') . 'prism.css');
        wp_register_style('fastdev_json_tree', fastdev_uri('assets') . 'jsonTree.css');
        wp_register_style('fastdev_ztip', fastdev_uri('assets/ztip') . 'ztip.css');
        wp_register_style('fastdev', fastdev_uri('assets') . 'style.css');

        // Register scripts
        wp_register_script('fastdev_prism', fastdev_uri('assets') . 'prism.js',
            [],
            false,
            true
        );
        wp_register_script('fastdev_json_tree', fastdev_uri('assets') . 'jsonTree.js',
            [],
            false,
            true
        );
        wp_register_script('fastdev_ztip', fastdev_uri('assets/ztip') . 'ztip.min.js',
            [],
            false,
            true
        );
        wp_register_script('fastdev', fastdev_uri('assets') . 'scripts.js',
            [],
            false,
            true
        );

        // Enqueue
        wp_enqueue_style('fastdev_prism');
        wp_enqueue_style('fastdev_json_tree');
        wp_enqueue_style('fastdev_ztip');
        wp_enqueue_style('fastdev');

        wp_enqueue_script('fastdev_prism');
        wp_enqueue_script('fastdev_json_tree');
        wp_enqueue_script('fastdev_ztip');
        wp_enqueue_script('fastdev');
    }

    public function sysArray()
    {
        $sys = [];

        // General
        $sys[] = [
            'label' => esc_html__('General', 'fastdev'),
            'value' => '{section_title}',
        ];

        $sys[] = [
            'label' => esc_html__('Site URL', 'fastdev'),
            'value' => site_url(),
        ];

        $sys[] = [
            'label' => esc_html__('Home URL', 'fastdev'),
            'value' => home_url(),
        ];
        $sys[] = [
            'label' => esc_html__('Multisite enabled', 'fastdev'),
            'value' => (is_multisite() ? 'Yes' : 'No'),
        ];

        if ( ! function_exists('get_preferred_from_update_core')) {
            include_once(wp_normalize_path(ABSPATH . 'wp-admin/includes/update.php'));
        }

        $core   = get_preferred_from_update_core();
        $status = (version_compare($core->current, get_bloginfo('version')) > 0) ? 'bad' : 'good';

        $sys[] = [
            'label'  => esc_html__('Installed WordPress version', 'fastdev'),
            'value'  => get_bloginfo('version'),
            'status' => $status,
            // translators: WP version
            'notice' => sprintf(esc_html__('The latest WP version is %1$s', 'fastdev'), $core->current),
        ];

        $lang  = get_option('WPLANG');
        $sys[] = [
            'label'  => esc_html__('Site language', 'fastdev'),
            'value'  => ! empty($lang) ? esc_html($lang) : 'en_US',
            'notice' => esc_html__('The language option is empty and en_US is set as default.', 'fastdev'),
            'tip'    => esc_html__('You can change this from Settings -> General -> Site Language', 'fastdev'),
        ];

        $sys[] = [
            'label' => esc_html__('Permalink structure', 'fastdev'),
            'value' => get_option('permalink_structure') ? '<code>' . get_option('permalink_structure') . '</code>' : esc_html__('Default',
                'fastdev'),
            'tip'   => esc_html__('You can change this from Settings -> Permalinks', 'fastdev'),
        ];

        $sys[] = [
            'label' => esc_html__('What to show on homepage', 'fastdev'),
            'value' => get_option('show_on_front'),
            'tip'   => esc_html__('You can change this from Customize -> Homepage Settings', 'fastdev'),
        ];

        $page_id = absint(get_option('page_on_front'));
        $sys[]   = [
            'label'  => esc_html__('Front page', 'fastdev'),
            'value'  => $page_id > 0 ? get_the_title($page_id) : '',
            'notice' => $page_id > 0 ? ' #' . $page_id : esc_html__('Not set', 'fastdev'),
            'tip'    => esc_html__('You can change this from Customize -> Homepage Settings', 'fastdev'),
        ];

        $page_id = absint(get_option('page_for_posts'));
        $sys[]   = [
            'label'  => esc_html__('Blog page', 'fastdev'),
            'value'  => $page_id > 0 ? get_the_title($page_id) : '',
            'notice' => $page_id > 0 ? ' #' . $page_id : esc_html__('Not set', 'fastdev'),
            'tip'    => esc_html__('You can change this from Customize -> Homepage Settings', 'fastdev'),
        ];

        // System Info
        $sys[] = [
            'label' => esc_html__('System info', 'fastdev'),
            'value' => '{section_title}',
        ];

        $sys[] = [
            'label' => esc_html__('Operating System', 'fastdev'),
            'value' => PHP_OS,
        ];

        $sys[] = [
            'label' => esc_html__('System Software', 'fastdev'),
            'value' => isset($_SERVER['SERVER_SOFTWARE']) ? wp_kses_post(wp_unslash($_SERVER['SERVER_SOFTWARE'])) : '--',
        ];

        $php_version = PHP_VERSION;
        $php_status  = [];

        // 5.3.x
        if ((version_compare($php_version, '5.4') < 0)) {
            $php_status = $this->getPhpStatus('14 August 2014');
        } // 5.4.x
        elseif ((version_compare($php_version, '5.5') < 0)) {
            $php_status = $this->getPhpStatus('3 September 2015');
        } // 5.5.x
        elseif ((version_compare($php_version, '5.6') < 0)) {
            $php_status = $this->getPhpStatus('21 July 2016');
        } // 5.6.x
        elseif ((version_compare($php_version, '7.0') < 0)) {
            $php_status = $this->getPhpStatus('19 January 2017', '31 December 2018');
        } // 7.0.x
        elseif ((version_compare($php_version, '7.1') < 0)) {
            $php_status = $this->getPhpStatus('3 December 2017', '3 December 2018');
        } // 7.1.x
        elseif ((version_compare($php_version, '7.2') < 0)) {
            $php_status = $this->getPhpStatus('1 December 2018', '1 December 2019');
        } // 7.2.x
        elseif ((version_compare($php_version, '7.3') < 0)) {
            $php_status = $this->getPhpStatus('30 November 2019', '30 November 2020');
        }
        elseif ((version_compare($php_version, '7.4') < 0)) {
            $php_status = $this->getPhpStatus('6 Dec 2020', '6 Dec 2021');
        }
        elseif ((version_compare($php_version, '8') < 0)) {
            $php_status = $this->getPhpStatus('28 Nov 2021', '28 Nov 2022');
        }
        elseif ((version_compare($php_version, '8.1') < 0)) {
            $php_status = $this->getPhpStatus('26 Nov 2022', '26 Nov 2023');
        }
        elseif ((version_compare($php_version, '8.2') < 0)) {
            $php_status = $this->getPhpStatus('25 Nov 2023', '25 Nov 2024');
        }
        elseif ((version_compare($php_version, '8.1') >= 0)) {
            $php_status = $this->getPhpStatus();
        }

        $sys[] = wp_parse_args($php_status, [
            'label' => esc_html__('PHP version', 'fastdev'),
            'value' => $php_version,
        ]);

        $sys[] = [
            'label' => esc_html__('MySQL Version', 'fastdev'),
            'value' => $this->mySQLVersion(),
        ];

        $sys[] = [
            'label' => esc_html__('GD Version', 'fastdev'),
            'value' => $this->gdVersion(),
        ];

        $sys[] = [
            'label' => esc_html__('Hostname', 'fastdev'),
            'value' => isset($_SERVER['SERVER_NAME']) ? wp_kses_post(wp_unslash($_SERVER['SERVER_NAME'])) : '--',
        ];

        $sys[] = [
            'label' => esc_html__('Server IP Address', 'fastdev'),
            'value' => isset($_SERVER['SERVER_ADDR']) ? wp_kses_post(wp_unslash($_SERVER['SERVER_ADDR'])) : '--',
        ];

        $sys[] = [
            'label' => esc_html__('Server Port', 'fastdev'),
            'value' => isset($_SERVER['SERVER_PORT']) ? wp_kses_post(wp_unslash($_SERVER['SERVER_PORT'])) : '--',
        ];

        $sys[] = [
            'label' => esc_html__('Server Document Root', 'fastdev'),
            'value' => isset($_SERVER['DOCUMENT_ROOT']) ? wp_kses_post(wp_unslash($_SERVER['DOCUMENT_ROOT'])) : '--',
        ];

        $sys[] = [
            'label' => esc_html__('Server Admin', 'fastdev'),
            'value' => isset($_SERVER['SERVER_ADMIN']) ? wp_kses_post(wp_unslash($_SERVER['SERVER_ADMIN'])) : '',
        ];

        $sys[] = [
            'label' => esc_html__('Server Time', 'fastdev'),
            'value' => mysql2date(
                sprintf(
                    esc_html__('%1$s - %2$s', 'fastdev'),
                    get_option('date_format'), 'H:m:s'
                ),
                current_time('mysql')
            ),
        ];

        $sys[] = [
            'label' => esc_html__('Debug', 'fastdev'),
            'value' => $this->getConstant('WP_DEBUG'),
            // translators: WP debug.
            'tip'   => sprintf(esc_html__('Open %1$s and add this code %2$s. This will enable PHP error printing.', 'fastdev'),
                '<code>wp-config.php</code>', '<code>define( \'WP_DEBUG\', true );</code>'),
        ];

        $sys[] = [
            'label' => esc_html__('Scripts debug', 'fastdev'),
            'value' => $this->getConstant('SCRIPT_DEBUG'),
            // translators: Scripts debug.
            'tip'   => sprintf(esc_html__('Open %1$s and add this code %2$s. This will enable unminified scripts and styles loading.',
                'fastdev'), '<code>wp-config.php</code>', '<code>define( \'SCRIPT_DEBUG\', true );</code>'),
        ];

        $sys[] = [
            'label' => esc_html__('Debug display', 'fastdev'),
            'value' => $this->getConstant('WP_DEBUG_DISPLAY'),
            // translators: WP debug.
            'tip'   => sprintf(esc_html__('Open %1$s and add this code %2$s. This will display the PHP errors.', 'fastdev'),
                '<code>wp-config.php</code>', '<code>define( \'WP_DEBUG_DISPLAY\', true );</code>'),
        ];

        $sys[] = [
            'label' => esc_html__('Debug display', 'fastdev'),
            'value' => $this->getConstant('WP_DEBUG_LOG'),
            // translators: WP debug.
            'tip'   => sprintf(esc_html__('Open %1$s and add this code %2$s. This will enable Debug logging to the /wp-content/debug.log file.', 'fastdev'),
                '<code>wp-config.php</code>', '<code>define( \'WP_DEBUG_LOG\', true );</code>'),
        ];

        global $wpdb;
        $sys[] = [
            'label' => esc_html__('WP Table prefix', 'fastdev'),
            'value' => $wpdb->prefix,
        ];

        $sys[] = [
            'label' => esc_html__('WordPress memory limit', 'fastdev'),
            'value' => $this->getConstant('WP_MEMORY_LIMIT'),
        ];

        $sys[] = [
            'label' => esc_html__('PHP Safe Mode', 'fastdev'),
            'value' => $this->iniGet('safe_mode', 'yn'),
        ];

        $inis = [
            'memory_limit'         => esc_html__('PHP Memory Limit', 'fastdev'),
            'upload_max_filesize'  => esc_html__('PHP Upload Max Size', 'fastdev'),
            'post_max_size'        => esc_html__('PHP Post Max Size', 'fastdev'),
            'max_execution_time'   => esc_html__('PHP Time Limit', 'fastdev'),
            'max_input_vars'       => esc_html__('PHP Max Input Vars', 'fastdev'),
            'arg_separator.output' => esc_html__('PHP Arg Separator', 'fastdev'),
        ];

        foreach ($inis as $ini => $label) {
            $sys[] = [
                'label' => $label,
                'value' => $this->iniGet($ini),
            ];
        }

        $sys[] = [
            'label' => esc_html__('Allow URL File Open', 'fastdev'),
            'value' => $this->iniGet('allow_url_fopen', 'ed'),
        ];

        $sys[] = [
            'label' => esc_html__('fsockopen', 'fastdev'),
            'value' => function_exists('fsockopen') ? esc_html__('Supported', 'fastdev') : esc_html__('Not supported', 'fastdev'),
        ];

        $sys[] = [
            'label' => esc_html__('SOAP Client', 'fastdev'),
            'value' => class_exists('SoapClient') ? esc_html__('Enabled', 'fastdev') : esc_html__('Disabled', 'fastdev'),
        ];

        $sys[] = [
            'label' => esc_html__('Suhosin', 'fastdev'),
            'value' => extension_loaded('suhosin') ? esc_html__('Loaded', 'fastdev') : esc_html__('Not loaded', 'fastdev'),
        ];

        // Themes
        $sys[] = [
            'label' => esc_html__('Themes', 'fastdev'),
            'value' => '{section_title}',
        ];

        $sys[] = [
            'label' => esc_html__('Active theme', 'fastdev'),
            'value' => $this->activeTheme(),
        ];

        // Plugins
        $sys[] = [
            'label' => esc_html__('Plugins', 'fastdev'),
            'value' => '{section_title}',
        ];

        // Plugins info
        if ( ! function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $all_plugins    = get_plugins();
        $active_plugins = get_option('active_plugins');

        if ( ! empty($active_plugins)) {
            $plugins = '';

            foreach ($active_plugins as $plugin_path) {
                $plugin       = $all_plugins[$plugin_path];
                $plugin_label = $plugin['Name'] . ' v' . $plugin['Version'];

                $plugins .= '<div class="fd-single-plugin">';

                if ( ! empty($plugin['PluginURI'])) {
                    $plugin_label = '<a href="' . esc_url_raw($plugin['PluginURI']) . '" target="_blank">' . $plugin_label . '</a>';
                }

                $plugins .= '<div><strong class="fdsp-title">' . $plugin_label . '</strong> <em> ( ' . $plugin_path . ' )</em></div>';

                $author_name = '';

                if ( ! empty($plugin['AuthorName'])) {
                    $author_name = '<strong class="fdsp-title">' . esc_html($plugin['AuthorName']) . '</strong>';
                }

                if ( ! empty($plugin['AuthorURI'])) {
                    $plugins .= '<div>' . $author_name . '<a href="' . esc_url_raw($plugin['AuthorURI']) . '" target="_blank">' . esc_html($plugin['AuthorURI']) . '</a></div>';
                }

                $plugins .= '</div>';
            }

            $sys[] = [
                'label' => esc_html__('Active plugins', 'fastdev'),
                'value' => $plugins,
            ];
        }

        return $sys;
    }

    public function getPhpStatus($active_date_end = false, $security_date_end = false)
    {
        if ( ! $active_date_end) {
            return [
                'status' => 'unknown',
            ];
        }

        if ( ! $security_date_end) {
            $security_date_end = $active_date_end;
        }

        // translators: %1$s - end o life date
        $supported     = esc_html__('Great! You are using a version of PHP that is actively supported until %1$s', 'fastdev');
        $supported_tip = esc_html__('You are using a release that is actively supported. Reported bugs and security issues are fixed and regular point releases are made.',
            'fastdev');

        // translators: %1$s - end o life date
        $not_supported     = esc_html__('This release is no longer supported from %1$s', 'fastdev');
        $not_supported_tip = esc_html__('Using a release that is no longer supported is not recommended and you should upgrade as soon as possible, as your sever may be exposed to unpatched security vulnerabilities.',
            'fastdev');

        // translators: %1$s - end o life date
        $security_supported     = esc_html__('This release will get critical updates only until %1$s', 'fastdev');
        $security_supported_tip = esc_html__('This is a release that is supported for critical security issues only. Releases are only made on an as-needed basis.',
            'fastdev');

        $result = [
            'status' => '',
            'notice' => '',
            'tip'    => $not_supported_tip,
        ];

        $time = current_time('timestamp');

        $human_time     = $time;
        $time_direction = _x('%s remaining', 'Time direction: future', 'fastdev');

        // 31 December 2018 is past
        if (strtotime($active_date_end) > $time) {
            $result['status'] = 'good';
            $result['notice'] = sprintf($supported, $active_date_end);
            $result['tip']    = $supported_tip;
            $human_time       = esc_html(human_time_diff(strtotime($active_date_end), $time));
        } elseif (strtotime($security_date_end) < $time) {
            $result['status'] = 'bad';
            $result['notice'] = sprintf($not_supported, $security_date_end);
            $human_time       = esc_html(human_time_diff(strtotime($security_date_end), $time));
            $time_direction   = _x('%s ago', 'Time direction: past', 'fastdev');
        } elseif (strtotime($active_date_end) < $time) {
            $result['status'] = 'notsogood';
            $result['notice'] = sprintf($security_supported, $security_date_end);
            $result['tip']    = $security_supported_tip;
            $human_time       = esc_html(human_time_diff(strtotime($security_date_end), $time));
        } else {
            $result['status'] = 'unknown';
        }

        if ('unknown' !== $result['status']) {
            $result['notice'] = $result['notice'] . '<span class="fd-humantime time-' . sanitize_html_class($result['status']) . '">' . sprintf($time_direction,
                    $human_time) . '</span>';
        }

        return $result;
    }

    public function makeTable($options)
    {
        if (is_array($options)) {
            ksort($options);
            $output = '<div class="fd-key-val-table">';
            foreach ($options as $key => $value) {
                //Value
                $val = isset($value['value']) ? $value['value'] : '';

                if (is_array($val)) {
                    ob_start();
                    fd_code($val);
                    $val = ob_get_clean();
                }

                //Title
                $label = $value['label'];
                if ('{section_title}' === $val) {
                    $label = '<h3>' . $label . '</h3>';
                    $val   = '';
                }

                //Status notice
                $good_or_bad = isset($value['status']) ? $value['status'] : 'neutral';
                $notice      = isset($value['notice']) ? $value['notice'] : '';

                $tip = isset($value['tip']) ? '<span class="fdtip" title="' . esc_attr($value['tip']) . '">
					<span class="dashicons dashicons-editor-help"></span>
				</span>' : '';

                //Markup
                $output .= '<div class="fd-kv-row">';
                $output .= '<div class="filter-this fd-label-col">
					<div class="fd-kv-code">' . $label . '</div>
					' . $tip . '
				</div>';
                $output .= '<div>
					<div class="fd-kv-code">
						<span class="' . $good_or_bad . '">' . $val . '</span>
						<span class="fd-info-notice">' . $notice . '</span>
					</div>
				</div>';
                $output .= '</div>';
            }
            $output .= '</div>';

            echo wp_kses_post($output);
        } else {
            fd_code($options);
        }
    }

    public function getConstant($const)
    {
        if ( ! defined($const)) {
            // translators: %s - constant name
            return sprintf(esc_html__('%s is not defined', 'fastdev'), '<code>' . $const . '</code>');
        }

        $val = constant($const);

        if (empty($val)) {
            return '<span class="fd-info-notice">' . esc_html__('(empty)', 'fastdev') . '</span>';
        } elseif (true === $val) {
            return esc_html__('TRUE', 'fastdev');
        }

        return $val;
    }

    public function iniGet($ini, $return_mode = false)
    {
        $val = ini_get($ini);

        if ($return_mode) {
            if ('yn' === $return_mode) {
                return ! empty($ini) ? esc_html__('Yes', 'fastdev') : esc_html__('No', 'fastdev');
            }
            if ('ed' === $return_mode) {
                return ! empty($ini) ? esc_html__('Enabled', 'fastdev') : esc_html__('Disabled', 'fastdev');
            }
            if ('bool' === $return_mode) {
                return ! empty($ini) ? esc_html__('TRUE', 'fastdev') : esc_html__('FALSE', 'fastdev');
            }
        }

        if (empty($val)) {
            return '<span class="fd-info-notice">' . esc_html__('(Not set)', 'fastdev') . '</span>';
        } elseif (true === $val) {
            return esc_html__('TRUE', 'fastdev');
        }

        return $val;
    }

    public function activeTheme()
    {
        if (get_bloginfo('version') < '3.4') {
            $theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');  // phpcs: WordPress.WP.DeprecatedFunctions.get_theme_dataFound -- Checking if is old WP install
            $theme      = $theme_data['Name'] . ' v' . $theme_data['Version'];
        } else {
            $theme_data = wp_get_theme();
            $theme      = $theme_data->Name . ' v' . $theme_data->Version;
        }

        return $theme;
    }

    public function page()
    {
        $this->makeTable($this->sysArray());
    }

    public function mySQLVersion()
    {
        global $wpdb;

        return $wpdb->get_var('SELECT VERSION() AS version');
    }

    public function gdVersion()
    {
        if (function_exists('gd_info')) {
            $gdex = gd_info();

            return $gdex['GD Version'];
        }

        return esc_html__('Unknown', 'fastdev');
    }
}
