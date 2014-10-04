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