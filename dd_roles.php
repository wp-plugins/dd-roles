<?php
/*
Plugin Name: DD_Roles
Version: 1.2
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

require_once('inc/setting_page.php');
include_once('functions/ajax_callbacks.php');





register_activation_hook( __FILE__, array('dd_roles', 'activate_plugin') );
register_deactivation_hook( __FILE__, array('dd_roles', 'deactivate_plugin') );



class dd_roles {

    static function activate_plugin()
    {
        //require_once('functions/add_role.php');
    }

    static function deactivate_plugin()
    {
        //TODO reset roles before deactivate

    }
}

function dd_Roles_addToAdminHead()
{
    $plugin_url_path = WP_PLUGIN_URL;
    echo '<link rel="stylesheet" href="'.$plugin_url_path.'/dd-roles/css/progressBar.css">';
    echo '<link rel="stylesheet" href="'.$plugin_url_path.'/dd-roles/css/style.css">';
}
add_action( 'admin_head', 'dd_Roles_addToAdminHead' );

function dd_Roles_addToAdminFooter()
{
    $plugin_url_path = WP_PLUGIN_URL;
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('dd-roles', $plugin_url_path . '/dd-roles/js/default.js', array(), 'jquery');
}
add_action( 'admin_footer', 'dd_Roles_addToAdminFooter' );
