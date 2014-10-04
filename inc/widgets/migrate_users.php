<?php
$onlyAdminRoles = onlyAdminRoles();
if (!$onlyAdminRoles){
    ?>
    <div id="migratediv" class="postbox ">
        <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Migrate Users</span></h3>
        <div class="inside">
            <div class="submitmigrate" id="submitmigrate">
                <form method="post" action="options.php">
                    <div id="minor-publishing">
                        <div class="misc-pub-section">
                            <select name="fromRole" id="role" class="widefat fromRole">
                                <?php
                                global $wp_roles;
                                $roles = $wp_roles->get_names();

                                foreach ($roles as $role_id => $role) {

                                    if($role_id != 'administrator'){

                                        $totalUsers = get_totalUsers($role_id);
                                        // echo $totalUsers;
                                        if($totalUsers){
                                            $totalUsers = '('.ngettext($totalUsers.' '.__('User'), $totalUsers.' '.__('Users'), $totalUsers).')'; // prints "4 cats"
                                            echo '<option value="'.$role_id.'">'.translate_user_role( $role ).' '.$totalUsers.'</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="misc-pub-section migrate_to">
                            to
                        </div>
                        <div class="misc-pub-section">
                            <select name="toRole" id="role" class="widefat toRole">
                                <?php
                                global $wp_roles;
                                $roles = $wp_roles->get_names();
                                foreach ($roles as $role_id => $role) {
                                    if($role_id != 'administrator'){
                                        $totalUsers = get_totalUsers($role_id);
                                        $totalUsers = $totalUsers? '('.ngettext($totalUsers.' '.__('User'), $totalUsers.' '.__('Users'), $totalUsers).')' : ''; // prints "4 cats"
                                        echo '<option value="'.$role_id.'">'.translate_user_role( $role ).' '.$totalUsers.'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div id="major-publishing-actions">
                        <div id="publishing-action">
                            <span class="spinner"></span>
                            <button type="submit" class="button button-primary hidden js-migrate-submit">Migrate</button>
                            <button type="button" class="button button-primary js-migrate">Migrate</button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}