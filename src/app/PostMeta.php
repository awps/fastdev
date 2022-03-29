<?php

namespace Fastdev;

class PostMeta extends Tab
{
    public function settings()
    {
        $currentId = '';

        if (is_single() || is_page()) {
            $currentId = ' (' . get_queried_object_id() . ')';
        }

        return [
            'label' => sprintf(esc_html__('Post Meta %s', 'fastdev'), $currentId),
        ];
    }

    public function adminBarQueryArgs()
    {
        $currentId = get_queried_object_id();

        if (is_singular() || is_page()) {
            return ['fd-post-id' => $currentId];
        }

        return [];
    }

    public function makeTable($options, $single_meta = false)
    {
        if (is_array($options) && ! $single_meta) {
            ksort($options);
            $output = '<div class="fd-key-val-table">';
            foreach ($options as $key => $value) {
                $output .= '<div class="fd-kv-row cols-30x40x30">';
                $output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . fdGlobalNonceUrl(add_query_arg([
                        'fd-get-post-meta' => $key,
                    ])) . '">' . $key . '</a></div></div>';
                $output .= '<div><div class="fd-kv-code">' . esc_html($value[0]) . '</div></div>';
                $output .= '</div>';
            }
            $output .= '</div>';
            echo wp_kses_post($output);
        } else {
            fd_code($options);
        }
    }

    public function page()
    {
        if ( ! wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')) {
            return;
        }

        $field_value = (int) $this->get('fd-post-id');
        $btn_label   = esc_html__('Get post meta', 'fastdev');

        $postId = ! empty($this->get('fd-post-id')) && (int)$this->get('fd-post-id') > 0 ? (int)$this->get('fd-post-id') : null;

        if ($postId !== null) {
            if ( ! empty($this->get('fd-get-post-meta'))) {
                echo '<h3>' . sprintf(
                        esc_html__('Single meta key %s for: %s', 'fastdev'),
                        '<code>' . sanitize_key($this->get('fd-get-post-meta')) . '</code>',
                        '<code>' . absint($postId) . '</code>'
                    ) . '</h3>';

                $this->makeTable(get_post_meta(absint($postId), sanitize_key($this->get('fd-get-post-meta'))), true);
            }

            echo '<h3>' . sprintf(
                    esc_html__('Meta data for: %s', 'fastdev'),
                    '<code>' . absint($postId) . '</code>'
                ) . '</h3>';

            $this->makeTable(get_post_meta(absint($postId)));
        }

        echo '<form method="get" class="fd-form">
                <div class="field">
                <label>' . esc_html__('Post ID', 'fastdev') . '</label> 
				<input type="number" value="' . esc_attr($field_value) . '" name="fd-post-id">
				' .
             wp_kses_post(get_submit_button(esc_html($btn_label), 'primary large', false, false))
             . '
				<input type="hidden" value="fd-main" name="page">
				<input type="hidden" value="' . esc_attr($this->tab_id) . '" name="tab">
				<input type="hidden" value="' . esc_attr(fdGetGlobalNonce()) . '" name="gnonce">
			</div>
			</form>';
    }

}
