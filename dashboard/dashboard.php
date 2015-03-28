<?php
add_action('wp_dashboard_setup', 'add_dashboard_widgets' ); // Hint: For Multisite Network Admin Dashboard use wp_network_dashboard_setup instead of wp_dashboard_setup.
add_action('wp_network_dashboard_setup', 'add_dashboard_widgets' ); // Hint: For Multisite Network Admin Dashboard use wp_network_dashboard_setup instead of wp_dashboard_setup.

function add_dashboard_widgets() {
    if ( current_user_can('administrator') ) {
        wp_add_dashboard_widget('logged_in_users', 'Online Users', 'logged_in_users_function');
    }
}

function logged_in_users_function(){
    echo '<div class="online_container">';
    echo '<p>View the activities of users who were logged in the last 30 minutes.</p>';

        // Display users active over the last 30 minutes
        $minutes_30 = strtotime(current_time('mysql', 1))-1800;
        $user_query = new WP_User_Query( array( 'meta_key' => 'last_active', 'meta_value' => $minutes_30, 'meta_compare' => '>' , 'orderby' => 'meta_value', 'order' => 'DESC' ) );
        $users = $user_query->get_results();
        $users_online = array();
        $users_offline = array();

        foreach($users as $user){
            $logged_in = get_user_meta( $user->ID, 'logged_in', true );

            if($logged_in && $user->roles[0] == 'banned'){
                array_push($users_offline, $user);
            }
            elseif($logged_in){
                array_push($users_online, $user);
            }
            else{
                array_push($users_offline, $user);
            }
        }

        echo '<div class="listContainer">';

            echo'<h4>Online</h4>';
            echo '<table class="onlineList">';

            foreach ($users_online as $key => $user_online) {

                $last_active_timestamp = get_user_meta( $user_online->ID, 'last_active', true );
                $last_visit_page = get_user_meta( $user_online->ID, 'last_visit_page', true );
                $you = wp_get_current_user();
                $your_id = $you->ID;
                $is_you = $user_online->ID == $your_id ? true : false;
                $is_you_phrase = $is_you ? '( YOU )' : '';

                $spy = esc_attr(get_the_author_meta('spy', $user_online->ID));
                $spy  = $spy == 1 ? 'checked' : '';

//                $params = parse_url($last_visit_page);
//                parse_str($params['query'], $query);

                $class = $last_active_timestamp <= strtotime("-6 minutes") ? 'inactive_6' : '';
                $class = $last_active_timestamp <= strtotime("-12 minutes") ? 'inactive_12' : $class;
                $class = $last_active_timestamp <= strtotime("-24 minutes") ? 'inactive_24' : $class;
                $userProfileLink = 'user-edit.php?user_id='.$user_online->ID;

                $role = $user_online->roles[0];

                $list = '<tr class="userOnline '.$class.'" data-rank="'.$key.'" data-userID="'.$user_online->ID.'">';

                ob_start();
                wp_nonce_field('update-user_' . $user_online->ID,'nonce_'.$user_online->ID, false); //get the nonce of this user
                $nonce = ob_get_clean();

                $nonce = $role != 'administrator' ? $nonce : '';

                $list .= '<td class="avatar_cell">'.get_avatar($user_online->ID, 32).$nonce.'</td>';
                $list .= '<td class="name"><a href="'.$userProfileLink.'#userHistory">'.$user_online->user_login.'</a> '.$is_you_phrase;

                if($is_you != true) {
                    $list .= make_phrase($last_visit_page, true);
                }

                $list .= '</td>';

                $current_stamp = strtotime(current_time('mysql', 1));
                $seconds = $current_stamp - $last_active_timestamp;
                $hours = floor($seconds / 3600);
                $mins = floor(($seconds - ($hours*3600)) / 60);
                $mins = sprintf('%02d', $mins);
                $secs = floor($seconds % 60);
                $secs = sprintf('%02d', $secs);
                $timer = '['.$mins.':'.$secs.']';
                $list .= '<td class="role">'.$role.'</td>';
                $list .= '<td class="time_spending" data-seconds="'.$seconds.'">'.$timer.'</td>';

                if($is_you != true && $role != 'administrator'){
                    $list .= '<td class="spy">';
                    $list .= '<a href="#"  class="spy_user '.$spy.'" title="Spy User"></a>';
                    $list .= '</td>';
                    $list .= '<td class="ban">';
                    $list .= '<a href="#" class="ban_user" title="Log Out And Ban User"></a>';
                    $list .= '</td>';
                    $list .= '<td class="destroy">';
                    $list .= '<a href="#"  class="destroy_user" title="Log Out User"></a>';
                    $list .= '</td>';
                }
                $list .= '</tr>';
                echo $list;
            }
            echo '</table>';

            if($users_offline){
                echo'<h4>Recently Offline</h4>';
                echo '<table class="offlineList">';

                foreach ($users_offline as $user_offline) {
                    $last_active_timestamp = get_user_meta( $user_offline->ID, 'last_active', true );
                    $userProfileLink = 'user-edit.php?user_id='.$user_offline->ID;
                    $banned_phrase = $user_offline->roles[0] == 'banned' ? '( BANNED )' :'';

                    $list = '<tr class="userOffline">';
                    $list .= '<td class="avatar_cell">'.get_avatar($user_offline->ID, 32).'</td>';
                    $list .= '<td class="name"><a href="'.$userProfileLink.'">'.$user_offline->user_login.'</a> '.$banned_phrase.'</td>';
                    $list .= '</tr>';
                    echo $list;
                }
                echo'</table>';
            };
        echo '</div>';
    echo '<button class="button button-primary refresh_online">refresh</button>';

    echo '</div>';
}