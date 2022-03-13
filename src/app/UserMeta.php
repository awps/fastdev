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
            'label' => __('User Meta' . $currentId, 'fastdev'),
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
        if (is_array($options) && !$single_meta) {
            ksort($options);
            $output = '<div class="fd-key-val-table">';
            foreach ($options as $key => $value) {
                $output .= '<div class="fd-kv-row cols-30x40x30">';
                $output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg([
                        'fd-get-user-meta' => $key,
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
        $field_value = !empty($_POST['fd-get-username']) ? $_POST['fd-get-username'] : '';
        $btn_label = !empty($_POST['fd-get-username']) ? __('Refresh user details', 'fastdev') : __('Get user details', 'fastdev');

        $userId = null;

        if (!empty($_GET['fd-user-id'])) {
            $userId = (int)$_GET['fd-user-id'];
        }
        else if (!empty($_POST['fd-get-username'])) {
            $user = get_user_by('login', $_POST['fd-get-username']);
            $userId = $user instanceof \WP_User ? $user->ID : null;
        }

        if ($userId !== null) {
            $user = get_user_by('id', $userId);

            if ($user instanceof \WP_User === false) {
                _e('User not found!', 'fastdev');

                return;
            }

            $username = $user->user_login;

            if (!empty($_GET['fd-get-user-meta'])) {
                echo '<h3>' . sprintf(
                        __('Single meta key %s for: %s :: %s', 'fastdev'),
                        '<code>' . sanitize_key($_GET['fd-get-user-meta']) . '</code>',
                        '<code>' . $username . '</code>',
                        '<code>' . $userId . '</code>'
                    ) . '</h3>';

                $this->makeTable(get_user_meta($userId, $_GET['fd-get-user-meta']), true);
            }

            echo '<h3>' . sprintf(
                    __('Meta data for: %s :: %s', 'fastdev'),
                    '<code>' . $username . '</code>',
                    '<code>' . $userId . '</code>'
                ) . '</h3>';

            $this->makeTable(get_user_meta($userId));
        }

        echo '<form method="post" class="fd-form">
                <div class="field"><label>' . __('Username', 'fastdev') . '
				</label> <input type="text" value="' . $field_value . '" name="fd-get-username" class="regular-text">
				' . get_submit_button($btn_label, 'primary large', false, false) . '
			</div>
			</form>';
    }

}
