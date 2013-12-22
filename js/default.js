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
        if(newRoleName == ''){
            showError();
            return;
        }
        else{
            //Do AJAX VERIFICATION AND ADD
            var data = {
                action: 'verify_and_add',
                new_role_display_name: newRoleName
            };
            jQuery.post(ajaxurl, data, function(unique) {

                if (unique) {

                    location.reload();
                    console.log (unique);
                }
                else {
                    showError();
                }
            });
        }
    })

    function showError(){

        $('.duplicated').removeClass('hidden');
//        console.log('bestaat al of is leeg stop error');
        //show error messages empty or duplicated
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

            thisCapLabel.removeClass('loading');
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
        var thisEdit = $(this).parents('.roleRow').find('.edit').children('.viewRole');

        thisEdit.text( (thisEdit.text() == 'View' ? 'Close' : 'View') );
    })


    $('.deleteRole').on('click', function(e){
        e.preventDefault();
        var role_id = $(this).next('.role_id').val();
        var data = {
            action: 'deleteRole',
            role_id: role_id
        };
        jQuery.post(ajaxurl, data, function(roleDeleted) {

            console.log(roleDeleted);
            location.reload();
        });

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



