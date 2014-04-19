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
        <div id="submitdiv" class="postbox ">
                <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Add New Role</span></h3>
                <div class="inside">
                    <div class="submitrole" id="submitrole">
                        <form method="post" action="options.php">

                            <?php settings_fields('dd_roles_settings'); ?>
                            <?php do_settings_sections('dd_roles_settings'); ?>

                            <div id="minor-publishing">
                                <div class="misc-pub-section">
                                    <input type="text" class="widefat dd-new-role" name="dd-new-role" placeholder="New role"/>
                                    <input type="hidden" class="dd_roles" name="dd_roles" value="<?php print_r( get_option('dd_roles')); ?>">
                                </div>

                                <div class="clear"></div>
                            </div>

                            <div id="major-publishing-actions">
                                <div id="publishing-action">
                                    <span class="spinner"></span>
                                    <button type="button" class="js-newRole button button-primary">Make Role</button>
                                    <button type="submit" style="display: none" class="js-newRole-submit button button-primary">Make Role</button>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>


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
                                                            $totalUsers = '('.ngettext($totalUsers.' user', $totalUsers.' users', $totalUsers).')'; // prints "4 cats"
                                                            echo '<option value="'.$role_id.'">'.$role.' '.$totalUsers.'</option>';
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
                                                    echo '<option value="'.$role_id.'">'.$role.'</option>';
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

        $CustomCapTrue = customCapBoolean();

        if($CustomCapTrue){
            ?>
            <div id="cleanUpdiv" class="postbox ">
                    <div class="handlediv" title="Click to toggle"><br></div>
                    <h3 class="hndle"><span>Clean Up</span></h3>
                    <div class="inside">
                        <div class="cleanupCap" id="cleanupCap">
                            <form method="post" action="options.php">
                                <div id="minor-publishing">
                                    <div class="misc-pub-section">
                                        <p>
                                            Through DD-roles you maybe notice that a lot of plugins
                                            leave their marks when they are inactive or deleted. By using this function you can cleanup the superfluous capabilities, <br><br>but be sure!
                                        </p>
                                        <select name="deleteCap" id="role" class="widefat deleteCap">
                                            <?php

                                                $default_caps = get_default_caps();
                                                $all_caps =  $rol_capabilities = get_caps_names('administrator');

                                                foreach( $all_caps as $capability ) {

                                                    $capabilityDisplay = str_replace('_',' ',$capability);
                                                    $capabilityDisplay = ucfirst($capabilityDisplay);

                                                    if(!in_array($capability ,$default_caps)){
                                                        echo '<option value="'.$capability.'">'.$capabilityDisplay.'</option>';
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
                                        <button type="button" class="js-cleanUp button button-primary">Delete Cap</button>
                                        <button type="submit" style="display: none" class="js-cleanUp-submit button button-primary">Delete Cap</button>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
    <?php
        }
    ?>

        <div id="donate" class="postbox closed">
            <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Donate to Developer</span></h3>
            <div class="inside">
                <div class="donate" id="submitrole">
                    <div  class="misc-pub-section">
                        <p>
                            This plugin is distributed for free under a GNU General Public License or a GPL compatible license. This software is free as in beer and as in freedom; however, donations help to pay for time I could have spent on billable projects. Donations allow me to spend more time developing these free projects instead of working on billable projects. Help support your favorite plugins by donating to help pay for espresso to keep me awake. (I do most of my open source work while at the end of the day.).
                        </p>
                        <p>
                            Donations allow me to spend more time developing all aspects of this plugin and providing the free support that so many people have enjoyed. (It also keeps me motivated: it is a great feeling for someone to be willing to pay — even a few Euros — for something they can get for free.) So be kind and please consider donating. Any amount is appreciated whether it be € 3.00 (price of a Dutch Beer) or more ;).
                        </p>
                        <h5>
                            Wouter Dijkstra
                        </h5>
                    </div>
                    <div id="major-publishing-actions">
                        <div id="publishing-action">
                            <span class="spinner"></span>
                            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5V2C94HQAN63C&lc=US&item_name=Dijkstra%20Design&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted" target="_blank" class="beer button button-primary" title="Donate the developer">Donate</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    <div id="postbox-container-2" class="postbox-container">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                <table class="wp-list-table widefat fixed posts" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col" id="title" class="manage-column column-title" style="">
                                    <span>Role</span>
                            </th>
                            <th scope="col" id="author" class="manage-column column-author" style="">Users</th>
                            <th scope="col" id="date" class="manage-column column-date sortable asc" style="">Capabilities</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th scope="col" class="manage-column column-title" style="">
                                    <span>Role</span>
                            </th>
                            <th scope="col" class="manage-column column-author" style="">Users</th>
                            <th scope="col" id="date" class="manage-column column-date sortable asc" style="">Capabilities</th>
                        </tr>
                    </tfoot>
                    <tbody id="the-list">
                    <?php

                    global $wp_roles;
                    $roles = $wp_roles->get_names();
                    $rolcount = 0;
                    foreach ($roles as $role_id => $role) {

                        $rowZebra = (($rolcount++)%2) ? 'even' : 'odd alternate';
                        $displayName = $role;
                        $wp_default_roles = array('administrator','editor','author','contributor','subscriber');
                        $is_default = in_array($role_id, $wp_default_roles) ? true : false;

                        ?>

                        <tr id="role-<?php echo $role_id ?>" class="role-<?php echo $role_id.' '.$rowZebra;  if($is_default){echo ' defaultRole';}?> roleRow" valign="top">

                            <td class="post-title role-title column-title">
                                <strong><a class="row-title editRole" href="#" title="<?php echo $displayName;?>"><?php echo $displayName;?></a></strong>
                                <input type="hidden" class="role_id" value="<?php echo $role_id ?>"
                                <?php



                                if (!$is_default) {
                                    echo '<div class="">
                                    <span class="edit"><a class="editRole" href="#" title="Edit this item">Edit</a> | </span><span class="trash"><a class="deleteRole" title="Delete this role: Users will be migrate to Subscribers" href="#">Trash</a> <input type="hidden" class="role_id" value="'.$role_id.'">

                                    </div>';

                                }
                                else{
                                    $CustomCapTrue = customCapBoolean();

                                    if($role_id == 'administrator'){
                                        $text = 'View';
                                        $class = 'viewRole';
                                    }
                                    else{
                                        $text = $CustomCapTrue ? 'Edit' : 'View';
                                        $class = $CustomCapTrue ? 'editRole' : 'viewRole';
                                    }
                                    echo '
                                        <div class="">
                                            <span class="edit"><a class="'.$class.'" href="#" title="Edit this item">'.$text.'</a> | </span><span class="">Default Wordpress Role</span>
                                        </div>';
                                }

                                ?>

                                <div class="capabilitiesBlock hidden">
                                    <?php

                                    $read= array();
                                    $levels= array();
                                    $pages = array();
                                    $comments = array();
                                    $posts = array();
                                    $media = array();
                                    $users = array();
                                    $themes = array();
                                    $plugins = array();
                                    $tools = array();
                                    $settings = array();
                                    $others = array();
                                    $default_caps = get_default_caps();

                                    foreach( $default_caps as $capability ) {

                                        $capabilityDisplay = str_replace('_',' ',$capability);

                                        if ($capability == 'read' || strpos($capability,'edit_dashboard') !== false ) {

                                            $read[] = $capability;
                                        }
                                        if (strpos($capability,'level') !== false) {

                                            $levels[] = $capability;
                                        }
                                        if (strpos($capability,'pages') !== false) {

                                            $pages[] = $capability;
                                        }
                                        if (strpos($capability,'comments') !== false) {

                                            $comments[] = $capability;
                                        }
                                        if (strpos($capability,'posts') !== false || strpos($capability,'manage_categories') !== false ) {

                                            $posts[] = $capability;
                                        }
                                        if (strpos($capability,'users') !== false) {

                                            $users[] = $capability;
                                        }
                                        if (strpos($capability,'theme') !== false) {

                                            $themes[] = $capability;
                                        }
                                        if (strpos($capability,'plugins') !== false) {

                                            $plugins[] = $capability;
                                        }
                                        if (strpos($capability,'port') !== false) {

                                            $tools[] = $capability;
                                        }
                                        if (strpos($capability,'files') !== false) {

                                            $media[] = $capability;
                                        }
                                        if (strpos($capability,'manage_options') !== false) {

                                            $settings[] = $capability;
                                        }
                                        if (strpos($capability,'unfiltered') !== false || strpos($capability,'links') !== false || strpos($capability,'core') !== false) {

                                            $others[] = $capability;
                                        }

                                    }

                                    $media = array_reverse($media);
                                    $levels = array_reverse($levels);
                                    $rol_capabilities = get_caps_names($role_id);

                                    ?>
                                    <div class="defaultCaps">
                                        <h4>Dashboard & Profile</h4>
                                        <p class="howto">Allows access to Administration Panel options "Dashboard" and "Profile"<br>
                                            Allows access to the configuration parameters of widgets which included to the dashboard.</p>

                                        <ul class="read_cap clearfix">
                                            <?php
                                            foreach( $read as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Posts</h4>
                                        <p class="howto">Allows access to Administration Panel options "Posts"</p>

                                        <ul class="posts_cap clearfix">
                                            <?php
                                            foreach( $posts as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Media</h4>
                                        <p class="howto">Allows access to Administration Panel options "Media"</p>

                                        <ul class="posts_cap clearfix">
                                            <?php
                                            foreach( $media as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Pages</h4>
                                        <p class="howto">Allows access to Administration Panel options "Pages"</p>

                                        <ul class="pages_cap clearfix">
                                            <?php
                                            foreach( $pages as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Comments</h4>
                                        <p class="howto">Allows access to Administration Panel options "Comments" ("Edit posts" needs to be checked in Posts). <br>To edit comments "Edit others posts" and "Edit published posts" need to be checked in Posts. </p>

                                        <ul class="pages_cap clearfix">
                                            <?php
                                            foreach( $comments as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Appearance</h4>
                                        <p class="howto">Allows access to Administration Panel options "Appearance"</p>
                                        <ul class="themes_cap clearfix">
                                            <?php
                                            foreach( $themes as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Plugins</h4>
                                        <p class="howto">Allows access to Administration Panel options "Plugins"</p>
                                        <ul class="plugins_cap clearfix">
                                            <?php
                                            foreach( $plugins as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Users</h4>
                                        <p class="howto">Allows access to Administration Panel options "Users"</p>
                                        <ul class="users_cap clearfix">
                                            <?php
                                            foreach( $users as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Tools</h4>
                                        <p class="howto">Allows access to Administration Panel options "Tools"</p>
                                        <ul class="plugins_cap clearfix">
                                            <?php
                                            foreach( $tools as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Settings</h4>
                                        <p class="howto">Allows access to Administration Panel options "Settings"</p>
                                        <ul class="plugins_cap clearfix">
                                            <?php
                                            foreach( $settings as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Others</h4>
                                        <p class="howto">Allows access to Administration Panel options "Links", allows user to post HTML markup or even JavaScript code in pages, posts, comments and widgets and allow to update the core.</p>
                                        <ul class="plugins_cap clearfix">
                                            <?php
                                            foreach( $others as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="defaultCaps">
                                        <h4>Levels</h4>
                                        <p class="howto">To maintain backwards compatibility with plugins that still use the user levels system (although this is very much discouraged), the default Roles in WordPress also include Capabilities that correspond to these levels. User Levels were finally deprecated in version 3.0.</p>
                                        <ul class="levels_cap clearfix">

                                            <?php
                                            foreach( $levels as $capability ) {

                                                $capabilityDisplay = str_replace('_',' ',$capability);
                                                $capabilityDisplay = ucfirst($capabilityDisplay);
                                                $selected = in_array($capability ,$rol_capabilities) ? 'checked' : '';
                                                $disabled = $is_default ? 'disabled' :'active';

                                                echo ' <li><label class="capLabel '.$disabled.'"><input '.$disabled.' class="'.$disabled.'" type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <?php
                                    $CustomCapTrue = customCapBoolean();
                                    if($CustomCapTrue){

                                    ?>
                                        <div class="otherCaps">
                                            <?php

                                            $capsGroups = array();

                                            $default_caps = $default_caps;
                                            $all_caps =  $rol_capabilities = get_caps_names('administrator');
                                            $rol_capabilities = get_caps_names($role_id);

                                            foreach( $all_caps as $capability ) {
                                                if(!in_array($capability ,$default_caps)){

                                                    $groupName = $capability;

                                                    $groupName = str_replace('others_', '', $groupName);
                                                    $groupName = str_replace('edit_', '', $groupName);
                                                    $groupName = str_replace('manage_', '', $groupName);
                                                    $groupName = str_replace('private_', '', $groupName);
                                                    $groupName = str_replace('read_', '', $groupName);
                                                    $groupName = str_replace('delete_', '', $groupName);
                                                    $groupName = str_replace('delete_', '', $groupName);
                                                    $groupName = str_replace('published_', '', $groupName);
                                                    $groupName = str_replace('publish_', '', $groupName);
                                                    $groupName = str_replace('view_', '', $groupName);
                                                    $groupName = str_replace('export_', '', $groupName);

                                                    if(!in_array($groupName ,$capsGroups) ){

                                                        $capsGroupsOld = $capsGroups;
                                                        $capsGroups[] = $groupName;

                                                        foreach( $capsGroups as $key => $capsGroup ) {

                                                            $matchingCharacters = similar_text($capsGroup, $groupName, $matchingPercentage);

                                                            $capsGroupLength = strlen($capsGroup);
                                                            $groupNameLength = strlen($groupName);

                                                            $set_matchingStrength = 51; //51% of the characters names are the same

                                                            if($matchingPercentage > $set_matchingStrength && $capsGroupLength > $groupNameLength){

                                                                unset($capsGroups[$key]);
                                                            }
                                                            if($matchingPercentage > $set_matchingStrength && $capsGroupLength < $groupNameLength){

                                                                $capsGroups = $capsGroupsOld;
                                                            }
                                                        }
                                                    }
                                                }
                                            }

//                                            print_r($capsGroups);

                                            echo '<h4>Other WP capabilities</h4>';
                                            foreach( $capsGroups as $capsGroup ) {
                                                echo '<ul class="customCap clearfix">';

                                                $default_caps = $default_caps;
                                                $all_caps =  $rol_capabilities = get_caps_names('administrator');
                                                $rol_capabilities = get_caps_names($role_id);

                                                foreach( $all_caps as $capability ) {
                                                    $capabilityDisplay = str_replace('_',' ',$capability);
                                                    $capabilityDisplay = ucfirst($capabilityDisplay);
                                                    $selected = in_array($capability,$rol_capabilities) ? 'checked' : '';
                                                    $disabled = $role_id === "administrator" ? 'disabled' : 'active';

                                                    if(!in_array($capability ,$default_caps) && strpos($capability,$capsGroup) !== false){
                                                        echo '<li><label class="capLabel '.$disabled.'"><input '.$disabled.' type="checkbox" '.$selected.' id="'.$capability.'" name="'.$capability.'" value="'.$capability.'">'.$capabilityDisplay.'<span class="spinner"></span></label></li>';
                                                    }
                                                }

                                                echo '</ul>';
                                            }
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
                                        $totalUsers = 'No Users';
                                        echo '<p>'.$totalUsers.'</p>';
                                    }
                                    else{
                                        $totalUsers = $totalUsers == 1 ? $totalUsers.' user' : $totalUsers.' users';
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
                                    $percent = round($percent);
                                ?>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $percent;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percent;?>%">
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

function get_caps_names($role_id){

    $role_id = get_role($role_id);
    $rol_capabilities = array_keys( $role_id->capabilities );
    $caps = array();
    foreach( $rol_capabilities as $capability ) {
        $caps[] = $capability;
    }
    return $caps;
}

function get_default_caps(){
    
    $defaultCaps = array('switch_themes','edit_themes','activate_plugins','edit_plugins','edit_users','edit_files','manage_options','moderate_comments','manage_categories','manage_links','upload_files','import','unfiltered_html','edit_posts','edit_others_posts','edit_published_posts','publish_posts','edit_pages','read','level_10','level_9','level_8','level_7','level_6','level_5','level_4','level_3','level_2','level_1','level_0','edit_others_pages','edit_published_pages','publish_pages','delete_pages','delete_others_pages','delete_published_pages','delete_posts','delete_others_posts','delete_published_posts','delete_private_posts','edit_private_posts','read_private_posts','delete_private_pages','edit_private_pages','read_private_pages','delete_users','create_users','unfiltered_upload','edit_dashboard','update_plugins','delete_plugins','install_plugins','update_themes','install_themes','update_core','list_users','remove_users','add_users','promote_users','edit_theme_options','delete_themes','export');

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