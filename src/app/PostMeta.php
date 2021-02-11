<?php

namespace Fastdev;

class PostMeta extends Tab
{
    public function settings()
    {
        $currentId = '';

        if (is_single() || is_page()) {
            $currentId = ' ('. get_queried_object_id() .')';
        }

        return [
            'label' => __('Post Meta'. $currentId, 'fastdev'),
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
        if (is_array($options) && !$single_meta) {
            ksort($options);
            $output = '<div class="fd-key-val-table">';
            foreach ($options as $key => $value) {
                $output .= '<div class="fd-kv-row cols-30x40x30">';
                $output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg([
                        'fd-get-post-meta' => $key,
                    ]) . '">' . $key . '</a></div></div>';
                $output .= '<div><div class="fd-kv-code">' . esc_html($value[0]) . '</div></div>';
                $output .= '</div>';
            }
            $output .= '</div>';
            echo $output;
        }
        else {
            fd_code($options);
        }
    }

    public function page()
    {
        $field_value = !empty($_GET['fd-post-id']) ? $_GET['fd-post-id'] : '';
        $btn_label = __('Get post meta', 'fastdev');

        $postId = !empty($_GET['fd-post-id']) && (int)$_GET['fd-post-id'] > 0 ? (int)$_GET['fd-post-id'] : null;

        if ($postId !== null) {
            if (!empty($_GET['fd-get-post-meta'])) {
                echo '<h3>' . sprintf(
                        __('Single meta key %s for: %s', 'fastdev'),
                        '<code>' . sanitize_key($_GET['fd-get-post-meta']) . '</code>',
                        '<code>' . $postId . '</code>'
                    ) . '</h3>';

                $this->makeTable(get_post_meta($postId, sanitize_key($_GET['fd-get-post-meta'])), true);
            }

            echo '<h3>' . sprintf(
                    __('Meta data for: %s', 'fastdev'),
                    '<code>' . $postId . '</code>'
                ) . '</h3>';

            $this->makeTable(get_post_meta($postId));
        }

        echo '<form method="get" class="fd-form">
                <div class="field">
                <label>' . __('New Post Meta Query', 'fastdev') . '</label> 
				<input type="number" value="' . $field_value . '" name="fd-post-id" class="regular-text">
				' . get_submit_button($btn_label, 'primary large', false, false) . '
				<input type="hidden" value="fd-main" name="page">
				<input type="hidden" value="' . $this->tab_id . '" name="tab">
			</div>
			</form>';
    }

}
