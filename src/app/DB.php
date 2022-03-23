<?php

namespace Fastdev;

class DB extends Tab
{

    public function settings()
    {
        return [
            'label' => __('DB', 'fastdev'),
        ];
    }

    public function page()
    {
        $this->pageContent();
    }

    public function pageContent()
    {
        if (!wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')){
            return '';
        }

        global $wpdb;

        $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
        $list   = wp_list_pluck($tables, 0);


        ?>
        <div class="fd-db-table-view">
            <div>
                <?php $this->tables($list); ?>
            </div>
            <div style="width: 100%; overflow-x: auto">
                <?php if ( ! empty($this->get('fd-table')) && in_array($this->get('fd-table'), $list)) : ?>
                    <?php
                    $tableName   = esc_sql($this->get('fd-table'));
                    $columns     = wp_list_pluck($wpdb->get_results("SHOW COLUMNS FROM $tableName", ARRAY_A), 'Field');   // phpcs:ignore  -- Already escaped
                    $firstColumn = esc_sql($columns[0]);

                    $results = $wpdb->get_results(
                            "SELECT * FROM $tableName ORDER BY $firstColumn DESC LIMIT 0, 100",   // phpcs:ignore  -- Already escaped
                        ARRAY_A);

                    $tableWidth = count($columns) / 5 * 100;
                    ?>
                    <div class="posts-table fd-db-table" style="width: <?php echo (float)$tableWidth ?>%;">
                        <div class="wp-table">
                            <table class="wp-list-table widefat fixed striped table-view-list"
                                   style="table-layout: auto !important;">
                                <thead>
                                <tr>
                                    <?php
                                    foreach ($columns as $column) {
                                        ?>
                                        <th scope="col" class="manage-column"
                                            style="width: auto; max-width: 800px;"><?php echo esc_html($column); ?></th>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                </thead>
                                <tbody id="the-list">
                                <?php
                                foreach ($results as $result) {
                                    ?>
                                    <tr><?php
                                        foreach ($columns as $column) {
                                            ?>
                                            <td style="width: auto; max-width: 800px;"><?php echo esc_html($result[$column]); ?></td>
                                            <?php
                                        }
                                        ?></tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <?php
                                    foreach ($columns as $column) {
                                        ?>
                                        <th scope="col" class="manage-column"
                                            style="width: auto; max-width: 800px;"><?php echo esc_html($column); ?></th>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public function tables($list)
    {
        if (!wp_verify_nonce(fdGetGlobalNonce(), 'fastdev-admin')){
            return '';
        }

        $output = '<div class="fd-key-val-table">';
        foreach ($list as $value) {
            $output .= '<div class="fd-kv-row col-100">';
            $output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg([
                    'fd-table' => esc_html($value),
                ]) . '" class="'. (! empty($this->get('fd-table')) && $this->get('fd-table') === esc_html($value) ? 'selected-table' : '') .'">' . esc_html($value) . '</a></div></div>';
            $output .= '</div>';
        }
        $output .= '</div>';
        echo $output;    // phpcs:ignore  -- Table HTML; Already escaped the columns inner
    }

}
