/**
 * Created by dijkstradesign on 06-12-13.
 */
jQuery(function($) {


    $('.dd-new-role').keydown(function(event){
        if(event.keyCode == 13) {
            event.preventDefault();
            $('.js-newRole').trigger('click');
            return false;
        }
    });

    $('.dd-new-role').focus(function(){

        $('.settings-error').fadeOut('slow', function(){
            $(this).addClass('hidden').attr('style', '');
        });

    })



    $('.js-newRole').on('click', function(){

        var newRoleName = $('.dd-new-role').val();

        var newRoleID = newRoleName.split(' ').join('_');
        newRoleID = newRoleID.toLowerCase();

        if(newRoleName == ''){
            showError();
            return;
        }
        else{
            //Do AJAX VERIFICATION
            var data = {
                action: 'verifyRole',
                new_roleName: newRoleName,
                newRoleID: newRoleID
            };
            jQuery.post(ajaxurl, data, function(unique) {

                if (unique) {
                    console.log (unique);
                    add_to_ddRoles(newRoleID); //update the hiddenfield
                    make_new_role(newRoleName); //Make the new role
                    save_ddRoles(); //ddRoles new list
                }
                else {
                    showError();
                }
            });
        }
    })

    function showError(){

        $('.duplicated').removeClass('hidden');
        console.log('bestaat al of is leeg stop error')
        //show error messages empty or duplicated
    }

    function add_to_ddRoles(newRole){

        //Add new_role to the list of dd_roles (js) BUGS
        //needs to trigger savebtn so the role can be add to list and made..

        var dd_roleList = $('.dd_roles').val();
        newRole = newRole.split(' ').join('_');
        newRole = newRole.toLowerCase();

        if (dd_roleList == ''){
            $('.dd_roles').val(newRole);
        }
        else{
            var excistingList = dd_roleList.split(",");

            //Extra: if there are still double:
            newList = $.grep(excistingList, function(value) {
                return newRole != value;
            });

            newList.push(newRole);//now 20 values

            $('.dd_roles').val(newList);
        }





        //do ajax to add_role with values



    }
    function make_new_role(newRole){

        //ajaxcall to make new role;

        var data = {
            action: 'makeRole',
            new_role: newRole
        };
        jQuery.post(ajaxurl, data, function(roleSaved) {

            console.log(roleSaved)
        });


    }
    function save_ddRoles(){
        $('.js-newRole-submit').trigger('click');
    }

    $('.js-migrate').on('click', function(){
       var fromRole =  $('.fromRole').val();
       var toRole =  $('.toRole').val();


        console.log(fromRole);
        console.log(toRole);

        var data = {
            action: 'migrateUsers',
            fromRole: fromRole,
            toRole: toRole
        };
        jQuery.post(ajaxurl, data, function(migrated) {

            console.log(migrated);
            location.reload();
        });






    });
    $('.capLabel.active').change(function() {

//        console.log($(this).children('input').is(':checked'));

        var state = $(this).children('input').is(':checked'); // true/false
        var capname = $(this).children('input').val();
        var role_id = $(this).parents('.roleRow').find('.role_id').val();



        $(this).addClass('loading');


        var thisCapLabel = $(this);

        var count = thisCapLabel.parents('.roleRow').find('.progressCount').val();
        var countTotal = thisCapLabel.parents('.roleRow').find('.progressCountTotal').val();
        var plusMinus = state == true ? +1 : -1;
        count = parseInt(count)+plusMinus;
        var percent = count*(100/countTotal);

        var data = {
            action: 'changeCapState',
            state: state,
            capname: capname,
            role_id : role_id
        };
        jQuery.post(ajaxurl, data, function(capDeleted) {

            console.log(capDeleted)
            thisCapLabel.removeClass('loading');







            console.log('count:'+count+'total'+countTotal+'this is percent: '+percent);

            percent = Math.round(percent)+'%';

            thisCapLabel.parents('.roleRow').find('.progress-bar').css("width", percent);
            thisCapLabel.parents('.roleRow').find('.progressCount').val(count);

            thisCapLabel.parents('.roleRow').find('.sr-only').text(percent+' Capabilities');

        });



    });



    $('.editRole').on('click', function(e){
        e.preventDefault();
        $(this).parents('.roleRow').find('.capabilitiesBlock').toggleClass('hidden');

        var thisEdit = $(this).parents('.roleRow').find('.edit').children('.editRole');

        thisEdit.text( (thisEdit.text() == 'Edit' ? 'Close' : 'Edit') );
    })
    $('.viewRole').on('click', function(e){
        e.preventDefault();
        $(this).parents('.roleRow').find('.capabilitiesBlock').toggleClass('hidden');
        var thisEdit = $(this).parents('.roleRow').find('.edit').children('.editRole');

        thisEdit.text( (thisEdit.text() == 'View' ? 'Close' : 'View') );
    })


    $('.deleteRole').on('click', function(){
        var role_id = $(this).next('.role_id').val();


        var dd_roleList = $('.dd_roles').val();

        if (dd_roleList == ''){
            dd_roleList = [];
        }

        var excistingList = dd_roleList.split(",");

        //Extra: if there are still double:
        newList = $.grep(excistingList, function(value) {
            return role_id != value;
        });


        var data = {
            action: 'deleteRole',
            role_id: role_id
        };
        jQuery.post(ajaxurl, data, function(roleDeleted) {

            console.log(roleDeleted);
            save_ddRoles();
        });

        $('.dd_roles').val(newList);




    });


    $('.js-cleanUp').on('click', function(e){
        e.preventDefault();

        var delcaps = $('.deleteCap').val();

        var data = {
            action: 'cleanUp',
            delcaps: delcaps
        };
        jQuery.post(ajaxurl, data, function(capDeleted) {

            console.log(capDeleted)

            setInterval(location.reload(),5000);
        });

    })


    $('.dd-sidebar').on('click','.handlediv', function(){
        console.log('hallo');

        $(this).parent().toggleClass('closed');
    });

});



