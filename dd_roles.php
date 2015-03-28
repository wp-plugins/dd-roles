<?php
/*
Plugin Name: DD_Roles
Version: 2.0
Plugin URI: http://dijkstradesign.com
Description: A plug-in to add and edit the roles and capabilities
Author: Wouter Dijkstra
Author URI: http://dijkstradesign.com
*/


/*  Copyright 2013  WOUTER DIJKSTRA  (email : info@dijkstradesign.nl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('inc/functions.php');
require_once('inc/setting_page.php');
include_once('functions/ajax_callbacks.php');
require_once('functions/get_online_users.php');
require_once('functions/dd_history.php');
require_once('dashboard/dashboard.php');

register_activation_hook( __FILE__, array('dd_roles', 'activate_plugin') );
register_deactivation_hook( __FILE__, array('dd_roles', 'deactivate_plugin') );

class dd_roles {

    static function activate_plugin()
    {
        add_new_capabilities('Show_Admin_Bar_in_Front');
        add_dd_role('Banned');
        dd_history_install();
    }

    static function deactivate_plugin()
    {
        delete_capabilities('Show_Admin_Bar_in_Front');
        dd_history_uninstall();
    }
}

function dd_add_style_and_js_dd_roles()
{
    wp_register_style( 'dd_roles_styles', plugins_url('/css/style.css', __FILE__) );
    wp_enqueue_style( 'dd_roles_styles' );

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-sortable');

    wp_enqueue_script( 'dd_js_roles', plugins_url( '/js/default.js', __FILE__ ) , array( 'jquery' ), '' );

}
add_action( 'admin_init', 'dd_add_style_and_js_dd_roles' );


load_default_textdomain();

add_action('wp_head', 'dd_adminbar_front');

function dd_adminbar_front(){
    if ( ! current_user_can( 'Show_Admin_Bar_in_Front' ) ) {
        show_admin_bar( false );
    }
}
