<?php

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