jQuery(function($) {

    var update_times = 0;

    $('.dd-new-role').keydown(function(e){
        if(e.keyCode == 13) {
            e.preventDefault();
            $('.js-newRole').trigger('click');
            return false;
        }
    });

    $('.dd-new-role').focus(function(){
        $('.settings-error').fadeOut('fast', function(){
            $(this).addClass('hidden').attr('style', '');
        });
    });

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
                    // console.log (unique);
                }
                else {
                    showError();
                }
            });
        }
    });

    $(document).on('click','.refresh_online', function(){
        update_times = 0;
        update_user(); //update user first. in the callback is the refresh();
    });

    function refresh_online(){

        var data = {
            action: 'update_online'
        };

        jQuery.post(ajaxurl, data, function(online) {

            $('.online_container').html(online);
            timer();
            refresh();
            $('.spy_user').removeClass('spinner');
        });

    }
    function update_user(){

        var data = {
            action: 'update_user'
        };

        jQuery.post(ajaxurl, data, function(online) {
            // console.log('user updated');

            refresh_online();
            $('.userOnline ').removeClass('outdated');
        });

    }

    function showError(){
        $('.duplicated').removeClass('hidden');
//        // console.log('bestaat al of is leeg stop error');
//        show error messages empty or duplicated
    }

    $('.js-migrate').on('click', function(){
       var fromRole =  $('.fromRole').val();
       var toRole =  $('.toRole').val();


        // console.log(fromRole);
        // console.log(toRole);

        var data = {
            action: 'migrateUsers',
            fromRole: fromRole,
            toRole: toRole
        };
        jQuery.post(ajaxurl, data, function(migrated) {

            // console.log(migrated);
            location.reload();
        });
    });

    $('.capLabel.active').change(function() {

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
            percent = Math.round(percent);
            percent = percent > 100 ? '100%' : percent+'%'
            thisCapLabel.parents('.roleRow').find('.progress-bar').css("width", percent);
            thisCapLabel.parents('.roleRow').find('.progressCount').val(count);
            thisCapLabel.parents('.roleRow').find('.sr-only').text(percent+' Capabilities');
        });
    });

    $('.openInfo').on('click', function(e){

        e.preventDefault();
        var currentName = $(this).text();
        var newName = $(this).data("othertext");

        $(this).toggleClass('open').text(newName).data('othertext',currentName).parents('.roleRow').find('.capabilitiesBlock').toggleClass('hidden');
    });

    $('.deleteRole').on('click', function(e){
        e.preventDefault();
        var role_id = $(this).parents('.roleRow').find('.role_id').val();

        var data = {
            action: 'deleteRole',
            role_id: role_id
        };
        jQuery.post(ajaxurl, data, function(roleDeleted) {
            // console.log(roleDeleted);
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
            // console.log(capDeleted);
            setInterval(location.reload(),5000);
        });
    })

    $('.dd-sidebar').on('click','.handlediv', function(){
        $(this).parent().toggleClass('closed');
    });


    timer();

    function timer(){
        $('.time_spending').each(function() {
            var timer = $(this).data("timer");
            var seconds = $(this).data("seconds");

            var thistimer = $(this);

            // set interval
            var tid = setInterval(every_second, 1000);
            function every_second() {

                seconds++
                thistimer.html('['+secondsTimeSpanToHMS(seconds)+']');
            }
            function abortTimer() { // to be called when you want to stop the timer
                clearInterval(tid);
            }
        });
    }

    function secondsTimeSpanToHMS(s) {
        var h = Math.floor(s/3600); //Get whole hours
        s -= h*3600;
        var m = Math.floor(s/60); //Get remaining minutes
        s -= m*60;
        return (m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); //zero padding on minutes and seconds
    }




    if($('#logged_in_users').length > 0 ){
        refresh();
    }




    function refresh(){
        var ajaxCall_interval = 30; // update every .... seconds
        var maximum_updates = 20;  // do something after max updates (maybe inactive account)
        var seconds = 0;
        var tid = setInterval(every_second, 1000);

        function every_second() {

            if(seconds >= ajaxCall_interval && update_times < maximum_updates){
                refresh_online();
                update_times++;
                abortTimer();

                // console.log(ajaxCall_interval+' sec passed');
                //console.log('Update '+update_times+' out of '+maximum_updates);
            }
            else if(update_times >= maximum_updates){
                $('.refresh_online').fadeIn();
                $('.userOnline ').addClass('outdated');
                // console.log(maximum_updates+'x geupdate');
                abortTimer();
            }
            else{
                seconds++;
                // console.log(seconds);
            }
        }
        function abortTimer() { // to be called when you want to stop the timer
            clearInterval(tid);
        }
    }



    $( document).on( 'click','.destroy_user', function( e ) {
        var $this = $(this),
            user_id = $this.parents('.userOnline ').data('userid'),
            nonce = $this.parents('.userOnline ').find('#nonce_'+user_id).val();

        var data = {
            action: 'logout_user',
            user_id: user_id
        };
        $this.addClass('spinner');
        jQuery.post(ajaxurl, data, function(callback) {

            destroy_user_session(user_id,nonce);

        });
        e.preventDefault();
    });
    $( document).on( 'click','.ban_user', function( e ) {
        var $this = $(this),
            user_id = $this.parents('.userOnline ').data('userid'),
            nonce = $this.parents('.userOnline ').find('#nonce_'+user_id).val();

        var data = {
            action: 'ban_user',
            user_id: user_id
        };
        $this.addClass('spinner');
        jQuery.post(ajaxurl, data, function(ban_user) {
            destroy_user_session(user_id,nonce);

            console.log('bann');
        });
        e.preventDefault();

    });
    $( document).on( 'click','.spy_user', function( e ) {
        var $this = $(this),
            user_id = $this.parents('.userOnline ').data('userid'),
            value = $this.hasClass('checked') ? 0 : 1; //turn off when is checked

        var data = {
            action: 'spy_user',
            user_id: user_id
        };

        $this.addClass('spinner').removeClass('checked');

        jQuery.post(ajaxurl, data, function(spy_user) {
            //console.log(spy_user);
            update_user(); //update user first. in the callback is the refresh();
        });
        e.preventDefault();

    });
    $( '.userHistory').on( 'click','.collapse', function( e ) {

        var $this = $(this);
        var day = $this.parents('.Days_last_action').data('day');

        $('.'+day+'').nextAll('tr.'+day+'').toggleClass('hidden');

        $this.toggleClass('open');
        e.preventDefault();

    });

    $('.userHistory .collapse').each(function(){
        var $this = $(this),
            parentRow = $this.parents('tr'),
            day = parentRow.data('day');

        if($(".userHistory tr."+day).length == 1){
            $this.addClass('hidden');
        }
    });


    $(document).on( 'click','.clear_user_history', function( e ) {

        var user_id = $(this).data('userid');
        var data = {
            action: 'dd_history_clear_user',
            user_id: user_id
        };
        jQuery.post(ajaxurl, data, function(callback) {
            console.log(callback);

            location.reload();
        });
        e.preventDefault();

    });

    function destroy_user_session(user_id, nonce){
        // console.log('dit is user:'+user_id);
        var data = {
            action: 'destroy-sessions',
            nonce: nonce,
            user_id: user_id
        };
        jQuery.post(ajaxurl, data, function(callback) {

            // // console.log(callback);
            refresh_online();

        });
    }
});



