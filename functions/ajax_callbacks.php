<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 06-12-13
 * Time: 21:25
 */

// Same handler function...
add_action( 'wp_ajax_verifyRole', 'verifyRole_callback' );
add_action( 'wp_ajax_makeRole', 'makeRole_callback' );
add_action( 'wp_ajax_deleteRole', 'deleteRole_callback' );
add_action( 'wp_ajax_migrateUsers', 'migrateUsers_callback' );
add_action( 'wp_ajax_cleanUp', 'cleanUp_callback' );
add_action( 'wp_ajax_changeCapState', 'changeCapState_callback' );

function verifyRole_callback() {

    global $wp_roles;
    $unique = true;
    $new_role = $_POST['newRoleID'];

    $roles = isset($wp_roles) ? $wp_roles->get_names() : '';

    foreach ($roles as $role_id => $role_name) {

        if($new_role == $role_name){
            $unique = false;
        }


    }

    $new_role = str_replace(' ', '', $new_role); //replace whitespace

    if (!ctype_alnum($new_role)) {
        //check if NOT alphanumeric
        //So don't pass
        $unique = false;
    }


    echo $unique;
    die();
}

function makeRole_callback() {

    $displayName = $_POST['new_role'];
    $roleName = str_replace(' ', '_', $displayName); //replace whitespace
    $roleName = strtolower($roleName); //make all lowercase like: administrator, visitor_hello which can be cerified with the role's name.

    echo $roleName;
    if ( ! isset( $GLOBALS[ 'wp_roles' ] ) )
        $GLOBALS[ 'wp_roles' ] = new WP_Roles();

// do not generate any output here
    $dish_caps = array(

//        'delete_others_ws_dishes' => false,
//        'delete_private_ws_dishes' => false,
//        'delete_published_ws_dishes' => false
    );
    $GLOBALS[ 'wp_roles' ]->add_role( $roleName, __( $displayName, 'cftp_band' ), $dish_caps );

    die();
}

function deleteRole_callback() {

    $role_id = $_POST['role_id'];

    userMigrate($role_id);

    $wp_roles = new WP_Roles();
    $wp_roles->remove_role($role_id);

    die();
}

function changeCapState_callback() {


    $state = $_POST['state'];

    $state = filter_var($state, FILTER_VALIDATE_BOOLEAN);

    $capname = $_POST['capname'];
    $role_id = $_POST['role_id'];

    $role = get_role( $role_id);

    if($state){
        $role->add_cap( $capname );
        echo 'dit is de state:'.$state;
        echo 'save';
    }
    else{
        $role->remove_cap( $capname );
        echo 'dit is de state:'.$state;
        echo 'delete';
    }



    die();
}

function migrateUsers_callback() {
    $fromRole = $_POST['fromRole'];
    $toRole = $_POST['toRole'];

    userMigrate($fromRole,$toRole);

    die();
}


function userMigrate($roleName,$toRole){

    if(!$toRole){
        $toRole = 'subscriber';
    }

    $usersRole = 'role='.$roleName.'';

    $users = get_users($usersRole);
    foreach ($users as $user) {


        // Remove role
        $user->remove_role( $roleName );

        // Add role
        $user->add_role( $toRole );
    }
}

function cleanUp_callback(){


    $delcaps = $_POST['delcaps'];

    //array for feature: to clean up multi-select. For now 1 by 1
    $delete_caps = array($delcaps);
    global $wp_roles;
    foreach ($delete_caps as $cap) {
        foreach (array_keys($wp_roles->roles) as $role) {
            $wp_roles->remove_cap($role, $cap);
        }
    }
    echo 'caps deleted';

    die();
}

function activate_cap() {
    // gets the author role
    $role = get_role( 'author' );

    // This only works, because it accesses the class instance.
    // would allow the author to edit others' posts for current theme only
    $role->add_cap( 'edit_others_posts' );
}

function deactivate_cap() {

    $role = get_role( 'author' );
    $role->remove_cap( $cap );
}

