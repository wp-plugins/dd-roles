<?php


add_action('wp_login', 'update_user', 10, 2);
add_action('wp_logout', 'dd_user_logoff', 10, 2);

add_action( 'admin_menu', 'update_user'); //on every load in the admin

add_action('user_new_form', 'dd_user_history');
add_action( 'show_user_profile', 'dd_user_history' );
add_action( 'edit_user_profile', 'dd_user_history' );

add_action('user_register', 'save_dd_user_history');
add_action( 'personal_options_update', 'save_dd_user_history' );
add_action( 'edit_user_profile_update', 'save_dd_user_history' );


function dd_user_history( $user )
{

    if (! current_user_can('administrator') ) {
        return;
    }

    $last_login = esc_attr(get_the_author_meta('last_login', $user->ID));
    $logged_in = esc_attr(get_the_author_meta('logged_in', $user->ID));
    $spy = esc_attr(get_the_author_meta('spy', $user->ID));
    $spy  = $spy == 1 ? 'checked' : '';
    $last_visit_page = esc_attr(get_the_author_meta('last_visit_page', $user->ID));
    $login_checked = $logged_in == 1 ? 'checked' : '';
    $last_active = esc_attr(get_the_author_meta('last_active', $user->ID));

    global $wpdb;
    $table_name = "dd_history";
    $meta_key1 = 'time';
    $retrieve_data = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id='$user->ID' ORDER BY $meta_key1" );
    $retrieve_data = array_reverse($retrieve_data, true); //newest first

    echo '<i id="userHistory"></i>';
    echo '<h3>User\'s History</h3>';
    echo '<table class="form-table">';
//    echo '<tr>';
//    echo '<th scope="row"><label for="last_logged_in">Last Logged In</label></th>';
//    echo '<td>';
////    echo 'Logged in: <input type="checkbox" disabled name="logged_in" value="1" '.$login_checked.'>';
//    echo '<input type="text" name="last_logged_in" disabled id="last_logged_in" value="' . $last_login . '" class="regular-text" placeholder="last login time" />';
//    echo '</td>';
//    echo '</tr>';
//
//    echo '<tr>';
//    echo '<th scope="row"><label for="last_active">Last Active</label></th>';
//    echo '<td>';
//    echo '<input type="text" name="last_active" disabled id="last_active" value="' . $last_active . '" class="regular-text" placeholder="last activity time" />';
//    echo '</td>';
//    echo '</tr>';
//
//    echo '<tr>';
//    echo '<th scope="row"><label for="last_active">Last Visite Page</label></th>';
//    echo '<td>';
//    echo '<input type="text" name="last_visit_page" disabled id="last_visit_page" value="' . $last_visit_page . '" class="regular-text" placeholder="last visit page" />';
//    echo '</td>';
//    echo '</tr>';

    echo '<tr>';
    echo '<th scope="row"><label for="spy_user_setting" class="spy_user_setting">Spy</label></th>';
    echo '<td>';
    echo '<input id="spy_user_setting" type="checkbox" name="spy" value="1" '.$spy.'>';
    echo '<strong>Track User.</strong>';
    if($retrieve_data){
        echo '<p>The User History will be cleared by pressing the clear button or All Users Histories by deactivate the plugin.</p><br>';
        echo '<span class="clear_user_history button-secondary" data-userid="'.$user->ID.'">Clear User History</span>';
    }
    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<th scope="row"><label for="last_active">History</label></th>';

        echo '<td>';

        $prev_date = '';
        $dayCount = 0;
        if($retrieve_data) {

            echo '<table class="userHistory">';
            foreach ($retrieve_data as $key => $retrieved_data) {
                $timestamp = $retrieved_data->time;
                $date = date('d-m-Y', $timestamp);
                $time = date('H:i', $timestamp);
                $url = $retrieved_data->url;
                $role = $retrieved_data->role_id;

                if ($prev_date != $date) {
                    $dayCount++;
                    $dayClass = 'day_' . $dayCount;
                }

                $rowClass = $prev_date != $date ? 'Days_last_action' : 'hidden'; //else hidden ???


                echo '<tr class="' . $rowClass . ' ' . $dayClass . '" data-day="' . $dayClass . '">';
                $class = $prev_date != $date ? 'show' : '';
                echo '<td class="date ' . $class . '">';
                if ($prev_date != $date) {
                    echo $date;

                }
                echo '</td>';
                echo '<td class="time">';
                echo $time;
                echo '</td>';
                echo '<td class="url">';

                echo make_phrase($url, false);
                echo '<br>';
                echo '<p class="meta_url">' . $url . '</p>';
                echo '</td>';

                $collapseClass = $prev_date != $date ? 'collapse' : '';
                echo '<td class="' . $collapseClass . '">';
                echo '</td>';
                echo '</tr>';

                $prev_date = $date;

            }
            echo '</table>';

        }
        else{
            echo '<strong>No track data available!</strong>';
        }
        echo '</td>';

    echo '</tr>';


    echo '</table>';

}

function save_dd_user_history( $user_id ) {
    if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
    update_user_meta( $user_id, 'history', $_POST['history'] );
    update_user_meta( $user_id, 'spy', $_POST['spy'] );
}


function dd_user_logoff() {
    $user = wp_get_current_user();
    update_user_meta( $user->ID, 'logged_in', 0);
}

function update_user(){
    $user = wp_get_current_user();
    $currentTimestamp = strtotime(current_time('mysql', 1));
    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $spy = esc_attr(get_the_author_meta('spy', $user->ID));
    $spy  = $spy == 1 ? true : false;

    update_user_meta( $user->ID, 'last_login', $currentTimestamp);
    update_user_meta( $user->ID, 'last_active', $currentTimestamp);
    update_user_meta( $user->ID, 'last_visit_page', $actual_link);
    update_user_meta( $user->ID, 'logged_in', 1);

    if($spy){
        $user_id = $user->ID;
        $role_id = $user->roles[0];
        $time = $currentTimestamp;
        $url = $actual_link;
        $user_ip = '123.123.123.123';
        $action = 'read';


        dd_history_install_data($user_id,$role_id,$time,$url,$user_ip,$action);
    }
}

function make_phrase($url, $present){

    $verb = $present ? 'is' : 'was';

//    $present = true; //past present for displaying present on dashboard-widget and past on history


//    $params = parse_url($url);
//    parse_str($params['query'], $query);
//
//    $action = $query['action'];
//    $posttype = $query['post_type'];
//    $page = $query['page'];

    $profile = strpos($url,'profile.php');
    $uploads = strpos($url,'upload.php');

    $add_media = strpos($url,'media-new.php');

    $tools = strpos($url,'tools.php');
    $comments = strpos($url,'edit-comments.php');
    $users = strpos($url,'wp-admin/users.php');
    $add_new_user = strpos($url,'wp-admin/user-new.php');
    $comments_edit = strpos($url,'comment.php');
    $edit = strpos($url,'wp-admin/edit.php');
    $posting_new = strpos($url,'wp-admin/post-new.php');
    $edit_tags = strpos($url,'wp-admin/edit-tags.php');

    $themes = strpos($url,'wp-admin/themes.php');
    $customize = strpos($url,'wp-admin/customize.php');
    $widgets = strpos($url,'wp-admin/widgets.php');
    $nav_menu = strpos($url,'wp-admin/nav-menus.php');

    $theme_editor = strpos($url,'wp-admin/theme-editor.php');

    $plugins = strpos($url,'wp-admin/plugins.php');
    $plugins_install = strpos($url,'wp-admin/plugin-install.php');

    $plugins_editor = strpos($url,'wp-admin/plugin-editor.php');

    $user_edit = strpos($url,'wp-admin/user-edit.php');

    $import = strpos($url,'wp-admin/import.php');
    $export = strpos($url,'wp-admin/export.php');

    $options_general = strpos($url,'wp-admin/options-general.php');
    $options_writing = strpos($url,'wp-admin/options-writing.php');
    $options_reading = strpos($url,'wp-admin/options-reading.php');
    $options_discussion = strpos($url,'wp-admin/options-discussion.php');
    $options_media = strpos($url,'wp-admin/options-media.php');
    $options_permalink = strpos($url,'wp-admin/options-permalink.php');

    $phrase ='';

    if($add_new_user){
        $phrase .= $verb.' adding a <a href="'.$url.'">new user</a>';
    }
    elseif($comments){
        $phrase .= $verb.' on the <a href="'.$url.'">comments overview</a>';
    }
    elseif($comments_edit){
        $phrase .= $verb.' editing a <a href="'.$url.'">comment</a>';
    }
    elseif($add_media){
        $phrase .= $verb.' adding <a href="'.$url.'">new media</a>';
    }
    elseif($users){
//        $page =  $page ? $page : '';
        $phrase .= $verb.' working in <a href="'.$url.'">users</a>';
    }
    elseif($edit_tags){
        $phrase .= $verb.' <a href="'.$url.'">editing tags</a>';
    }
    elseif($options_general){
        $phrase .= $verb.' on the <a href="'.$url.'">General Settings</a> page';
    }
    elseif($options_reading){
        $phrase .= $verb.' on the <a href="'.$url.'">Reading Settings</a> page';
    }
    elseif($options_discussion){
        $phrase .= $verb.' on the <a href="'.$url.'">Discussion Settings</a> page';
    }
    elseif($options_media){
        $phrase .= $verb.' on the <a href="'.$url.'">Media Settings</a> page';
    }
    elseif($options_permalink){
        $phrase .= $verb.' on the <a href="'.$url.'">Permalink Settings</a> page';
    }
    elseif($themes){
        $phrase .= $verb.' on the <a href="'.$url.'">Themes</a> page';
    }
    elseif($customize){
        $phrase .= $verb.' on the <a href="'.$url.'">Customize</a> page';
    }
    elseif($widgets){
        $phrase .= $verb.' on the <a href="'.$url.'">Widgets</a> page';
    }
    elseif($nav_menu){
        $phrase .= $verb.' on the <a href="'.$url.'">Edit Menu</a> page';
    }

    elseif($theme_editor){
        $phrase .= $verb.' on the <a href="'.$url.'">Theme editor</a> page';
    }
    elseif($import){
        $phrase .= $verb.' on the <a href="'.$url.'">Import</a> page';
    }
    elseif($export){
        $phrase .= $verb.' on the <a href="'.$url.'">Export</a> page';
    }
    elseif($user_edit){
        $phrase .= $verb.' editing a <a href="'.$url.'">User</a>';
    }
    elseif($plugins){
        $phrase .= $verb.' on the <a href="'.$url.'">Plugins Overview</a> page';
    }
    elseif($plugins_install){
        $phrase .= $verb.' on the <a href="'.$url.'">Add Plugins</a> page';
    }
    elseif($plugins_editor){
        $phrase .= $verb.' on the <a href="'.$url.'">Edit Plugins</a> page';
    }
    elseif($options_writing){
        $phrase .= $verb.' on the <a href="'.$url.'">Writing Settings</a> page';
    }
//    elseif($posting_new){
//        $posttype =  $posttype ? $posttype : 'post';
//        $phrase .= $verb.' working on a new '.$posttype.'';
//    }
//    elseif($action == 'edit'){
//        $phrase .= $verb.' <a href="'.$url.'">editing</a>';
//    }
//    elseif($edit){
//        $posttype =  $posttype ? $posttype : 'post';
//        $phrase .= $verb.' viewing the <a href="'.$url.'">'.$posttype.' overview</a>';
//    }
    elseif($profile){
        $phrase .= $verb.' working on his/her <a href="'.$url.'">profile</a>';
    }
    elseif($uploads){
        $phrase .= $verb.' working in <a href="'.$url.'">media</a>';
    }
    elseif($tools){
        $phrase .= $verb.' working in <a href="'.$url.'">tools</a>';
    }
    else{
        $phrase .= $verb.' on the <a href="'.$url.'">dashboard</a>';
    }

    return $phrase;
}