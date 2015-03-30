<?php

global $dd_history_db_version;
$dd_history_db_version = '1.0';

function dd_history_install() {
    global $wpdb;
    global $dd_history_db_version;

    $table_name = 'dd_history';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9) NOT NULL,
		role_id tinytext NOT NULL,
		time varchar(12) NOT NULL,
		url varchar(200) DEFAULT '' NOT NULL,
		action tinytext NOT NULL,
		user_ip tinytext NOT NULL,
		UNIQUE KEY id (id)

	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'dd_history_db_version', $dd_history_db_version );
}

function dd_history_install_data($user_id,$role_id,$time,$url,$user_ip,$action) {
    global $wpdb;
    $table_name = 'dd_history';

    $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'time' => $time,
            'url' => $url,
            'action' => $action,
            'user_ip' => $user_ip,
        )
    );
}

function dd_history_uninstall() {

    // Drop the table dd_history

    global $wpdb;
    $table = 'dd_history';

    $wpdb->query("DROP TABLE IF EXISTS $table");
}

