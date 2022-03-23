<?php

namespace Fastdev;

class Options extends Tab
{

    public function settings()
    {
        return [
            'label' => __('WP Options', 'fastdev'),
        ];
    }

    public function makeTable($options)
    {
        if (is_array($options)) {
            ksort($options);
            $output = '<div class="fd-key-val-table">';

            $output .= '<div class="fd-kv-row fd-kv-head">';
            $output .= '<div><div class="fd-kv-code">' . __('Option ID', 'fastdev') . '</div></div>';
            $output .= '<div><div class="fd-kv-code">' . __('Value', 'fastdev') . '</div></div>';
            $output .= '</div>';

            foreach ($options as $key => $value) {
                $data_attr = $this->getDataAtr($key);

                $val_display = $value;
                $val_size    = strlen($value);

                if ($val_size > 1500) {
                    $val_display = '<div class="fastdev-trimmed-string">';
                    $val_display .= '<span class="original-string" style="display: none;">' . esc_html($value) . '</span>';
                    $val_display .= '<span class="trimmed-string">' . esc_html(substr($value, 0, 1000)) . '</span>';
                    $val_display .= '<div class="toggle-string">
								<span data-expand="' . __('Expand full source code',
                            'fastdev') . '" data-collapse="' . __('Collapse full source code',
                            'fastdev') . '">' . __('Expand full source code', 'fastdev') . '</span>
							</div>';
                    $val_display .= '</div>';
                }

                $output .= '<div class="fd-kv-row fd-kv-row--deletable">';
                $output .= '<div class="filter-this"><div class="fd-kv-code">
                    <a href="' . esc_url(add_query_arg('fd-get-option', $key)) . '">' . esc_html($key) . '</a>
                    <span id="fd-delete-option" class="fd-button-delete fd-button-delete--inline"' . $data_attr . '>' . __('Delete option',
                        'fastdev') . '</span>
                </div></div>';
                $output .= '<div class="filter-this"><div class="fd-kv-code">' . $val_display . '</div></div>';
                $output .= '</div>';
            }
            $output .= '</div>';
            echo $output; // phpcs:ignore -- Table HTML. Columns are already escaped
        } else {
            fd_code($options);
        }
    }

    public function page()
    {
        if ( ! empty($this->get('fd-get-option'))) {
            $option    = sanitize_title($this->get('fd-get-option'));
            $data_attr = $this->getDataAtr($option);

            $action_buttons = '<span id="fd-refresh-option" class="fd-button-refresh"' . $data_attr . '>' . __('Refresh',
                    'fastdev') . '</span>';
            $action_buttons .= '<label class="fd-auto-refresh"><input type="checkbox" id="fd-auto-refresh"> ' . __('Auto refresh',
                    'fastdev') . '</label>';
            $action_buttons .= '<span id="fd-delete-option" class="fd-button-delete"' . $data_attr . '>' . __('Delete option',
                    'fastdev') . '</span>';

            echo '<h3><strong>' . esc_html($option) .'</strong>'. $action_buttons. '</h3>'; // phpcs:ignore -- Already done above
            echo '<div id="fd-wpo-code-block">';
            fd_code(get_option($option), true);
            echo '</div>';
        } else {
            fd_search();
            $all_wpo = wp_load_alloptions();
            $this->makeTable($all_wpo);
        }
    }

    public function registerAjax()
    {
        add_action('wp_ajax_fastdev_delete_option', [$this, 'deleteOption']);
        add_action('wp_ajax_fastdev_refresh_option', [$this, 'refreshOption']);
    }

    public function canProcess()
    {
        if (empty($_POST['option_id']) || empty($_POST['nonce'])) {
            return false;
        }

        $nonce  = wp_kses_data(wp_unslash($_POST['nonce']));
        $option = sanitize_title(wp_unslash($_POST['option_id']));

        if ( ! wp_verify_nonce($nonce, $option)) {
            return false;
        }

        return $option;
    }

    public function refreshOption()
    {
        if (($option = $this->canProcess()) === false) {
            $this->ajaxMessage(__('Error. Please refresh the page.', 'fastdev'));
        }

        if (($opt = get_option($option)) !== false) {
            fd_code($opt, true);
        } else {
            $this->ajaxMessage(__('Option does not exists.', 'fastdev'));
        }

        die();
    }

    public function deleteOption()
    {
        if (($option = $this->canProcess()) === false) {
            $this->ajaxMessage(__('Error. Please refresh the page.', 'fastdev'));
        }

        if ($opt = delete_option($option)) {
            $this->ajaxMessage(__('Option deleted.', 'fastdev'));
        } else {
            $this->ajaxMessage(__('Can\'t delete this option.', 'fastdev'));
        }

        die();
    }

    public function ajaxMessage($msg, $type = 'error')
    {
        die('<div class="fastdev-ajax-message is-' . sanitize_html_class($type) . '">' . esc_html($msg) . '</div>');
    }

    public function getDataAtr($option)
    {
        $data_attr = ' data-option="' . esc_attr($option) . '"';
        $data_attr .= ' data-nonce="' . esc_attr(wp_create_nonce($option)) . '"';

        return $data_attr;
    }
}
