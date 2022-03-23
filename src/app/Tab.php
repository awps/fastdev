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

class Tab
{

    public $parent_page;
    public $tab_id;

    public function __construct($tab_id = false, $parent_page_id = false)
    {
        $this->parent_page_id = $parent_page_id;
        $this->tab_id         = $tab_id;

        if ( ! empty($parent_page_id)) {
            $this->parent_page = new Page($parent_page_id);
            $this->parent_page->add_tabs([$this, 'tab']);
            add_action('admin_bar_menu', [$this, 'adminBar'], 99);
        }
    }

    public function adminBarQueryArgs()
    {
        return [];
    }

    public function adminBar()
    {
        global $wp_admin_bar;

        $wp_admin_bar->add_node([
            'id'     => $this->tab_id,
            'parent' => $this->parent_page_id,
            'title'  => $this->getSetting('label'),
            'href'   => fdGlobalNonceUrl(admin_url(add_query_arg(
                    wp_parse_args($this->adminBarQueryArgs(), ['tab' => $this->tab_id]),
                    'admin.php?page=' . $this->parent_page_id))
            ),
        ]);
    }

    public function settings()
    {
        return [];
    }

    public function getSetting($key)
    {
        $s = $this->getSettings();

        return isset($s[$key]) ? $s[$key] : null;
    }

    public function getSettings()
    {
        return wp_parse_args(
            [
                'id'       => $this->tab_id,
                'callback' => [$this, 'page'],
                'tip'      => $this->tip(),
            ],
            $this->settings()
        );
    }

    /**
     * Define the tooltip for this tab.
     *
     * @return string
     */
    public function tip()
    {
        return '';
    }

    /**
     * The page content. This is the callback that renders the final HTML.
     */
    public function page()
    {
        esc_html_e('Congrats! you\'ve created a new page tab.', 'fastdev');
    }

    public function pageContent()
    {
    }

    public function tab($tabs)
    {
        $tabs[] = $this->getSettings();

        return $tabs;
    }

    public function get($key)
    {
        return isset($_GET[$key]) ? wp_kses_data(wp_unslash($_GET[$key])) : null;  // phpcs:ignore -- Nonce not required
    }

}
