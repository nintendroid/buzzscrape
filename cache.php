<?php
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

function bzs_table_name() {
    global $wpdb;
    return $wpdb->prefix . "buzzscrape_channels";
}

function bzs_create_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = bzs_table_name();

    $sql = "CREATE TABLE {$table_name} (
        bid varchar(255) NOT NULL,
        cid varchar(255) NOT NULL,
        cname varchar(255) NOT NULL,
        url varchar(2048) NOT NULL,
        iconurl varchar(2048),
        modified datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) $charset_collate;";

    dbDelta($sql);
}

function bzs_clear_channels($bid) {
    global $wpdb;
    $table_name = bzs_table_name();
    $sql = $wpdb->prepare("DELETE FROM {$table_name} WHERE bid=%s", $bid);
    $wpdb->query($sql);
}

function bzs_delete_table() {
    global $wpdb;
    $table_name = bzs_table_name();
    $sql = "DROP TABLE IF EXISTS {$table_name}";
    $wpdb->query($sql);
    delete_option("bzs_db_version");
}

function bzs_find_channels($bid) {
    global $wpdb;
    $table_name = bzs_table_name();

    $sql = $wpdb->prepare("SELECT * FROM {$table_name} WHERE bid=%s", $bid);
    $result = $wpdb->get_results($sql);

    return (empty($result) ? null : $result);
}

function bzs_get_ids() {
    global $wpdb;
    $table_name = bzs_table_name();

    $sql = $wpdb->prepare("SELECT DISTINCT bid FROM {$table_name}");
    $result = $wpdb->get_results($sql);

    return (empty($result) ? null : $result);
}

function bzs_update_item($id, $name, $description, $url) {
    global $wpdb;
    $table_name = bzs_table_name();
    
    $sql = $wpdb->prepare(
        "INSERT INTO {$table_name} (bid,cid,cname,url) " .
        "VALUES (%s,%s,%s,%s)",
        $id, $name, $description, $url
    );
    $result = $wpdb->query($sql);
}

function bzs_parse($id, $text) {
    $config = include(__DIR__ . '/config.php');
    $pattern = $config['scrape_pattern'];

    // find modal dialog div
    $done = (preg_match('/"listen-modal"/', $text, $matches, PREG_OFFSET_CAPTURE) === false);
    $count = 0;

    while (!$done) {
        // shift past class name
        $offset = $matches[0][1] + strlen($matches[0][0]);

        // find next service
        $matches = array();
        $done = (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset) === false);
        $done = $done || ($matches == null) || (count($matches) == 0);

        if (!$done) {
            $name = $matches[1][0];
            $url = $matches[2][0];
            $description = $matches[3][0];
            bzs_update_item($id, $name, $description, $url);
            $count++;
        }
    }

    return $count;
}

function bzs_refresh($bid) {
    $config = include(__DIR__ . '/config.php');
    $url = $config['content_base_url'] . '/' . $bid;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $count = 0;

    if ($httpStatus == 200) {
        bzs_clear_channels($bid);
        $count = bzs_parse($bid, $response);
    }

    return array('status' => $httpStatus, 'count' => $count);
}

function bzs_ajax_refresh() {
    $valid = current_user_can('editor') || current_user_can('administrator');
    $valid = $valid && isset($_POST['bid']);

    if (!$valid) {
        wp_send_json_error('Access denied.');
        return;
    }

    $result = bzs_refresh($_POST['bid']);

    if (!isset($result) || $result['status'] != 200) {
        wp_send_json_error('Failed to retrieve data.');
        return;
    }

    wp_send_json_success('count: ' . $result['count']);
}

// Register admin action
add_action('wp_ajax_bzs_refresh', 'bzs_ajax_refresh');
