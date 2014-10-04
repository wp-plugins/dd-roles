<?php
/**
 * Created by PhpStorm.
 * User: dijkstradesign
 * Date: 04-12-13
 * Time: 20:28
 */

add_action('admin_menu', 'DD_rolesSettings');

function DD_rolesSettings() {

    add_users_page('DD Roles', 'DD Roles', 'manage_options', 'dd-roles.php', 'rolesSettingsPage');
    add_action( 'admin_init', 'register_dd_roles_settings' );
}

function rolesSettingsPage(){
    ?>
<div class="wrap">
    <div id="icon-edit" class="icon32 icon32-posts-ws_drinks"><br></div><h2>DD Roles</h2>
    <div id="lost-connection-notice" class="error hidden below-h2">
        <p><span class="spinner"></span> <strong>Connection lost.</strong> Saving has been disabled until you’re reconnected.	<span class="hide-if-no-sessionstorage">We’re backing up this post in your browser, just in case.</span>
        </p>
    </div>
    <div id="setting-error" class="error duplicated settings-error hidden">
        <p><strong>Duplicated, Empty or too Fancy!</strong> The role name is already excisting, empty or contains strange characters.</p>
    </div>
    <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
    <!-- /post-body-content -->

    <div id="postbox-container-1" class="postbox-container dd-sidebar">
        <div id="side-sortables" class="meta-box-sortables ui-sortable">

            <?php

            include_once('widgets/new_role.php');
            include_once('widgets/migrate_users.php');
            include_once('widgets/clean_up.php');
            include_once('widgets/donate.php');

            ?>
        </div>
    </div>
    <div id="postbox-container-2" class="postbox-container">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
            <table class="wp-list-table widefat fixed posts" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" id="title" class="manage-column column-title" style="">
                                <span><?php _e('Role'); ?></span>
                        </th>
                        <th scope="col" id="author" class="manage-column column-author" style=""><?php _e('Users'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date sortable asc" style=""><?php _e('Capabilities'); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" class="manage-column column-title" style="">
                                <span><?php _e('Role'); ?></span>
                        </th>
                        <th scope="col" class="manage-column column-author" style=""><?php _e('Users'); ?></th>
                        <th scope="col" id="date" class="manage-column column-date sortable asc" style=""><?php _e('Capabilities'); ?></th>
                    </tr>
                </tfoot>
                <tbody id="the-list">

                <?php

                global $wp_roles;
                $roles = $wp_roles->get_names();
                $rolcount = 0;
                foreach ($roles as $role_id => $role) {

                    $rowZebra = (($rolcount++)%2) ? 'even' : 'odd alternate';
                    $displayName = translate_user_role( $role );
                    $wp_default_roles = array('administrator','editor','author','contributor','subscriber');
                    $is_default = in_array($role_id, $wp_default_roles) ? true : false;

                    ?>

                    <tr id="role-<?php echo $role_id ?>" class="role-<?php echo $role_id.' '.$rowZebra;  if($is_default){echo ' defaultRole';}?> roleRow" valign="top">


                        <td class="post-title role-title column-title">
                            <strong><a class="row-title editRole" href="#" title="<?php echo $displayName;?>"><?php _e($displayName);?></a></strong>
                            <input type="hidden" class="role_id" value="<?php echo $role_id ?>">
                            <?php

                            $viewName = __('View');
                            $editName = __('Edit');
                            $closeName = __('Close');
                            $trashName = __('Delete');

                            $CustomCapTrue = customCapBoolean();

                            $className = !$is_default | $CustomCapTrue ? 'editRole' : 'viewRole';
                            $titleName = $role_id != 'administrator' && !$is_default | $CustomCapTrue ? 'Edit this item' : 'View this item';
                            $linkText = $role_id == 'administrator' | !$CustomCapTrue && $is_default ? $viewName : $editName;
                            $secondItem = !$is_default ? '<span class="trash"><a class="deleteRole" title="Delete this role: Users will be migrate to Subscribers" href="#">'.$trashName.'</a></span>' : '<span>'.__("Default").' Wordpress '.__("Role").'</span>';

                            ?>
                            <div><span class="edit"><a class="<?php echo $className ?> openInfo" href="#" data-OtherText="<?php echo $closeName ?>" title="<?php echo $titleName ?>"><?php echo $linkText ?></a> | </span><?php echo $secondItem ?></div>
                            <div class="capabilitiesBlock hidden">
                                <?php

                                $result = get_all_default_capabilities();

                                $rol_capabilities = get_caps_names($role_id);

                                foreach( $result as $defaultCap ) {

                                    $defaultCap = json_decode($defaultCap);
                                    $title = $defaultCap->title;
                                    $desc = $defaultCap->desc;
                                    $capabilities = $defaultCap->capabilities;

                                    ?>

                                    <div class="defaultCaps">
                                        <h4><?php echo $title; ?></h4>
                                        <p class="howto"><?php echo $desc; ?></p>

                                        <ul class="read_cap clearfix">
                                            <?php
                                            foreach( $capabilities as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                if( $capability == 'Show_Admin_Bar_in_Front'){
                                                    $disabled = 'active';
                                                }

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';

                                            }

                                            ?>
                                        </ul>
                                    </div>
                                    <?php
                                }

                                $CustomCapTrue = customCapBoolean();
                                if($CustomCapTrue){

                                ?>
                                    <div class="otherCaps">
                                        <?php

                                        print_r(get_post_type_capabilities($args));

                                        // $capsGroups = make_groups();
                                        echo '<h4>Other WP capabilities</h4>';
                                        echo '<p class="howto">This is a bunch of capabilities set by third-part plugins or themes.</p>';
                                        // foreach( $capsGroups as $capsGroup ) {
                                            echo '<ul class="customCap clearfix">';

                                            $all_caps =  $rol_capabilities = get_caps_names('administrator');
                                            $rol_capabilities = get_caps_names($role_id);
                                            $default_caps = get_default_caps();

                                            foreach( $all_caps as $capability ) {
                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $role_id === "administrator" ? 'disabled' : 'active';


                                                // if(!in_array($capability ,$default_caps) && strpos($capability,$capsGroup) !== false){
                                                if(!in_array($capability ,$default_caps)){
                                                    echo '<li><label class="capLabel '.$disabled.'"><input '.$disabled.' type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                                }
                                            }
                                            echo '</ul>';
                                        // }
                                        ?>
                                    </div>
                                 <?php
                                 }
                                 ?>
                            </div>
                        </td>
                        <td class="author column-user">
                            <?php
                                $totalUsers = get_totalUsers($role_id);

                                if(!$totalUsers){
                                    $totalUsers = '0 '.__('Users');
                                    echo '<p>'.$totalUsers.'</p>';
                                }
                                else{
                                    $totalUsers = $totalUsers == 1 ? $totalUsers.' '.__('User') : $totalUsers.' '.__('Users');
                                    echo '<a href="users.php?role='.$role.'">'.$totalUsers.'</a>';
                                }
                            ?>
                        </td>
                        <td class="date column-capabilities">
                            <?php
                                $count =  get_cap_count($role_id);
                                $countTotal =  get_cap_count('administrator');
                                $count = ($count) ? $count : 0;
                                $percent = $count*(100/$countTotal);
                                $percent = floor($percent);
                            ?>
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" data-role="progressbar" data-aria-valuenow="<?php echo $percent;?>" data-aria-valuemin="0" data-aria-valuemax="100" style="width: <?php echo $percent;?>%">
                                    <span class="sr-only"><?php echo $percent;?>% Capabilities</span>
                                </div>
                                <input type="hidden" class="progressCount" value="<?php echo $count; ?>">
                                <input type="hidden" class="progressCountTotal" value="<?php echo $countTotal ?>">
                            </div>
                        </td>
                    </tr>

                <?php }//end foreach ?>
                </tbody>
            </table>
        </div>
    </div><!-- /post-body -->
    <br class="clear">
    </div>

<?php
}

