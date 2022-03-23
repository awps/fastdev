<?php

namespace Fastdev;

class UserMeta extends Tab
{

    public function settings()
    {
        $currentId = '';

        if (is_author()) {
            $currentId = ' (' . get_queried_object_id() . ')';
        }

        return [
            'label' => sprintf(
                esc_html__('User Meta %s', 'fastdev'),
                $currentId
            ),
        ];
    }

    public function adminBarQueryArgs()
    {
        $currentId = get_queried_object_id();

        if (is_author()) {
            return ['fd-user-id' => $currentId];
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
                $output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' .
                           fdGlobalNonceUrl(add_query_arg([
                               'fd-get-user-meta' => $key,
                           ]))
                           . '">' . $key . '</a></div></div>';
                $output .= '<div><div class="fd-kv-code">' . esc_html($value[0]) . '</div></div>';
                $output .= '</div>';
            }
            $output .= '</div>';
            echo $output;   // phpcs:ignore  -- The table, inner columns are already escaped
        } else {
            fd_code($options);
        }
    }

    public function page()
    {
        if ( ! wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')) {
            return;
        }

        $field_value = ! empty($_GET['fd-get-username']) ? wp_kses_data(wp_unslash($_GET['fd-get-username'])) : '';
        $btn_label   = ! empty($_GET['fd-get-username']) ? esc_html__('Refresh user details',
            'fastdev') : esc_html__('Get user details', 'fastdev');

        $userId = null;

        if ( ! empty($_GET['fd-user-id'])) {
            $userId = (int)$_GET['fd-user-id'];
        } elseif ( ! empty($field_value)) {
            $user   = get_user_by('login', $field_value);
            $userId = $user instanceof \WP_User ? $user->ID : null;
        }

        if ($userId !== null) {
            $user = get_user_by('id', $userId);

            if ($user instanceof \WP_User === false) {
                esc_html_e('User not found!', 'fastdev');

                return;
            }

            $username = $user->user_login;

            if ( ! empty($_GET['fd-get-user-meta'])) {
                echo '<h3>' . sprintf(
                        esc_html__('Single meta key %s for: %s :: %s', 'fastdev'),
                        '<code>' . sanitize_key($_GET['fd-get-user-meta']) . '</code>',
                        '<code>' . esc_html($username) . '</code>',
                        '<code>' . esc_html($userId) . '</code>'
                    ) . '</h3>';

                $this->makeTable(get_user_meta($userId, wp_kses_data(wp_unslash($_GET['fd-get-user-meta']))), true);
            }

            echo '<h3>' . sprintf(
                    esc_html__('Meta data for: %s :: %s', 'fastdev'),
                    '<code>' . esc_html($username) . '</code>',
                    '<code>' . esc_html($userId) . '</code>'
                ) . '</h3>';

            $this->makeTable(get_user_meta((int)$userId));
        }

        echo '<form method="get" class="fd-form">
                <div class="field"><label>' . esc_html__('Username', 'fastdev') . '
				</label> <input type="text" value="' . esc_attr($field_value) . '" name="fd-get-username" class="regular-text">
				' .
             get_submit_button(esc_html($btn_label), 'primary large', false, false) // phpcs:ignore  -- All good here. Already escaped
             . '
			</div>
				<input type="hidden" value="fd-main" name="page">
				<input type="hidden" value="' . esc_attr($this->tab_id) . '" name="tab">
				<input type="hidden" value="' . esc_attr(fdGetGlobalNonce()) . '" name="gnonce">
			</form>';
    }

}
