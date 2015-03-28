<?php

function get_all_default_capabilities(){

    $read = $levels = $pages = $comments = $posts = $media = $users = $themes = $plugins = $tools = $settings = $others = array();
    $default_caps = get_default_caps();

    foreach( $default_caps as $capability ) {

        if ($capability == 'read' || strpos($capability,'edit_dashboard') !== false ||  strpos($capability,'Show_Admin_Bar_in_Front') !== false ) {
            $read[] = $capability;
        }
        elseif (strpos($capability,'level') !== false) {
            $levels[] = $capability;
        }
        elseif (strpos($capability,'pages') !== false) {
            $pages[] = $capability;
        }
        elseif (strpos($capability,'comments') !== false) {
            $comments[] = $capability;
        }
        elseif (strpos($capability,'posts') !== false || strpos($capability,'manage_categories') !== false ) {
            $posts[] = $capability;
        }
        elseif (strpos($capability,'users') !== false) {
            $users[] = $capability;
        }
        elseif (strpos($capability,'theme') !== false) {
            $themes[] = $capability;
        }
        elseif (strpos($capability,'plugins') !== false) {
            $plugins[] = $capability;
        }
        elseif (strpos($capability,'port') !== false) {
            $tools[] = $capability;
        }
        elseif (strpos($capability,'files') !== false) {
            $media[] = $capability;
        }
        elseif (strpos($capability,'manage_options') !== false) {
            $settings[] = $capability;
        }
        elseif (strpos($capability,'unfiltered') !== false || strpos($capability,'links') !== false || strpos($capability,'core') !== false) {
            $others[] = $capability;
        }
	    else{
		    $others[] = $capability; //just in case...
	    }
    }

    $media = array_reverse($media);
    $levels = array_reverse($levels);


    $read2 = '{
        "title": "Dashboard & Profile",
        "desc": "Allows access to Administration Panel options \"Dashboard\" and \"Profile\" Allows access to the configuration parameters of widgets which included to the dashboard.",
        "capabilities":'.json_encode($read).'
    }';

    $posts2 = '{
        "title": "Posts",
        "desc": "Allows access to Administration Panel options \"Posts\".",
        "capabilities":'.json_encode($posts).'
    }';

    $media2 = '{
        "title": "Media",
        "desc": "Allows access to Administration Panel options \"Media\".",
        "capabilities":'.json_encode($media).'
    }';
    $pages2 = '{
        "title": "Pages",
        "desc": "Allows access to Administration Panel options \"Pages\".",
        "capabilities":'.json_encode($pages).'
    }';
    $comments2 = '{
        "title": "Comments",
        "desc": "Allows access to Administration Panel options \"Comments\" (\"Edit posts\" needs to be checked in Posts). To edit comments \"Edit others posts\" and \"Edit published posts\" need to be checked in Posts.",
        "capabilities":'.json_encode($comments).'
    }';
    $themes2 = '{
        "title": "Appearance",
        "desc": "Allows access to Administration Panel options \"Appearance\".",
        "capabilities":'.json_encode($themes).'
    }';
    $plugins2 = '{
        "title": "Plugins",
        "desc": "Allows access to Administration Panel options \"Plugins\".",
        "capabilities":'.json_encode($plugins).'
    }';
    $users2 = '{
        "title": "Users",
        "desc": "Allows access to Administration Panel options \"Users\".",
        "capabilities":'.json_encode($users).'
    }';
    $tools2 = '{
        "title": "Tools",
        "desc": "Allows access to Administration Panel options \"Tools\".",
        "capabilities":'.json_encode($tools).'
    }';
    $settings2 = '{
        "title": "Settings",
        "desc": "Allows access to Administration Panel options \"Settings\".",
        "capabilities":'.json_encode($settings).'
    }';
    $others2 = '{
        "title": "Others",
        "desc": "Allows access to Administration Panel options \"Links\", allows user to post HTML markup or even JavaScript code in pages, posts, comments and widgets and allow to update the core.",
        "capabilities":'.json_encode($others).'
    }';
    $levels2 = '{
        "title": "Levels",
        "desc": "To maintain backwards compatibility with plugins that still use the user levels system (although this is very much discouraged), the default Roles in WordPress also include Capabilities that correspond to these levels. User Levels were finally deprecated in version 3.0.",
        "capabilities":'.json_encode($levels).'
    }';

    //Set order
    return array($read2,$posts2,$media2,$pages2,$comments2,$themes2,$plugins2,$users2,$tools2,$settings2,$others2,$levels2);

}


function get_caps_names($role_id){

    $role_id = get_role($role_id);
    $rol_capabilities = array_keys( $role_id->capabilities );
    $caps = array();
    foreach( $rol_capabilities as $capability ) {
        $caps[] = $capability;
    }
    return $caps;
}


function register_dd_roles_settings() {
    register_setting( 'dd_roles_settings', 'dd_roles' );
}

function get_totalUsers($role_id){
    $result = count_users();
    $totalUsers = '';

    foreach ($result['avail_roles'] as $role => $count) {

        if ($role == $role_id) {
            $totalUsers = $count;
            break;
        }
    }
    return $totalUsers;
}

function get_cap_count($role){
    $role = get_role($role);
    $rol_capabilities = array_keys( $role->capabilities );

    $countCap = count($rol_capabilities);
    return $countCap;
}



function get_default_caps(){

	$role =  get_role( 'administrator' );
	$defaultCaps = array_keys($role->capabilities);
    return $defaultCaps;
}

function customCapBoolean(){
    $default_caps = get_default_caps();
    $all_caps =  $rol_capabilities = get_caps_names('administrator');

    foreach( $all_caps as $capability ) {
        if(!in_array($capability ,$default_caps)){
            return true;
        }
    }
}

function onlyAdminRoles(){

    global $wp_roles;
    $roles = $wp_roles->get_names();
    $onlyAdmin = true;

    foreach ($roles as $role_id => $role) {
        if($role_id != 'administrator'){
            $totalUsers = get_totalUsers($role_id);
            if($totalUsers){
                $onlyAdmin = false;
            }
        }
    }
    return $onlyAdmin;
}

function make_groups(){
    $capsGroups = array();
    $all_caps  = get_caps_names('administrator');

    foreach( $all_caps as $capability ) {
        if(!in_array($capability ,$default_caps)){

            $groupName = $capability;


            print_r($groupName);
            echo '<br>';

            $prefixes = array("others_", "edit_", "manage_","private_", "read_", "delete_", "published_", "publish_", "view_", "export_");
            $groupName = str_replace($prefixes, '', $groupName);

            print_r($groupName);
            echo '<br>';
            echo '<br>';

            if(!in_array($groupName ,$capsGroups) ){

                //Add the new one to the array
                $capsGroups[] = $groupName;

                foreach( $capsGroups as $key => $capsGroup ) {

                    if(strpos($capsGroup,$groupName) !== false && strpos($groupName,$capsGroup) !== false){

                        //Do nothing, those are the same';
                    }

                    elseif(strpos($capsGroup,$groupName) !== false && strpos($groupName,$capsGroup) == ''){

                        //The new one fits in the one already existing in the array.
                        //Remove the one in the array.
                        $capsGroups = array_diff($capsGroups, array($capsGroup));

                    }
                    elseif(strpos($groupName,$capsGroup) !== false){

                        //The one in the array fits in the new one.
                        //So the new one (just added) needs to be removed!
                        $capsGroups = array_diff($capsGroups, array($groupName));

                    }
                }
            }
        }
    }
}

function add_new_capabilities($addcaps){

    //array for feature: to clean up multi-select. For now 1 by 1
    $add_caps = array($addcaps);
    global $wp_roles;
    foreach ($add_caps as $cap) {
        foreach (array_keys($wp_roles->roles) as $role) {
            $wp_roles->add_cap($role, $cap);
        }
    }
}

function delete_capabilities($delcaps){

    //array for feature: to clean up multi-select. For now 1 by 1
    $delete_caps = array($delcaps);
    global $wp_roles;
    foreach ($delete_caps as $cap) {
        foreach (array_keys($wp_roles->roles) as $role) {
            $wp_roles->remove_cap($role, $cap);
        }
    }
}