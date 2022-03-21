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
        global $wpdb;

        $tables = $wpdb->get_results('SHOW TABLES', ARRAY_N);
        $list   = wp_list_pluck($tables, 0);
        ?>
        <div class="fd-db-table-view">
            <div>
                <?php $this->tables($list); ?>
            </div>
            <div style="width: 100%; overflow-x: auto">
                <?php if ( ! empty($_GET['fd-table']) && in_array($_GET['fd-table'], $list)) : ?>
                    <?php
                    $tableName   = esc_sql($_GET['fd-table']);
                    $columns     = wp_list_pluck($wpdb->get_results("SHOW COLUMNS FROM $tableName", ARRAY_A), 'Field');
                    $firstColumn = $columns[0];

                    $results = $wpdb->get_results("SELECT * FROM $tableName ORDER BY $firstColumn DESC LIMIT 0, 100",
                        ARRAY_A);

                    $tableWidth = count($columns) / 5 * 100;
                    ?>
                    <div class="posts-table fd-db-table" style="width: <?php echo $tableWidth ?>%;">
                        <div class="wp-table">
                            <table class="wp-list-table widefat fixed striped table-view-list"
                                   style="table-layout: auto !important;">
                                <thead>
                                <tr>
                                    <?php
                                    foreach ($columns as $column) {
                                        ?>
                                        <th scope="col" class="manage-column"
                                            style="width: auto; max-width: 800px;"><?php echo $column; ?></th>
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
                                            style="width: auto; max-width: 800px;"><?php echo $column; ?></th>
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
        $output = '<div class="fd-key-val-table">';
        foreach ($list as $value) {
            $output .= '<div class="fd-kv-row col-100">';
            $output .= '<div class="filter-this"><div class="fd-kv-code"><a href="' . add_query_arg([
                    'fd-table' => $value,
                ]) . '" class="'. (! empty($_GET['fd-table']) && $_GET['fd-table'] === $value ? 'selected-table' : '') .'">' . $value . '</a></div></div>';
            $output .= '</div>';
        }
        $output .= '</div>';
        echo $output;
    }

}
