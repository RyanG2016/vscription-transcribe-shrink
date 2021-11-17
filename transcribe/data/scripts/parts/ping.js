$(document).ready(function () {

    const expiryCheckURL = '/api/v1/sessions/expiry';
    const extendURL = '/api/v1/sessions/extend';

    // const expiryCheckURL = '/api/v1/sessions/expiry?XDEBUG_SESSION_START=10004';
    // const extendURL = '/api/v1/sessions/extend?XDEBUG_SESSION_START=10004';
    var expire_at;
    var expired = false;
    var time_left;

    var notifyAfter = null;
    var notifyTimeStr = null;
    var notifySet = false;

    var newScheduleStr = null;

    // watchdogs
    var pinging = null;
    var notifyTimeoutObj = null;

    const notifyBefore = 10*60; // 10 minutes (in seconds)

    function checkExpired()
    {

        $.get( expiryCheckURL, function() {
            // alert( "success" );
        })
            .done(function(response) {
                /** Response Example
                * {
                        "session": "qeb785uun1r3bo3objqal8k40i",
                        "revoked": 0,
                        "expired": true,
                        "expire_date": 0,
                        "time_left": 0
                    }
                * */

                expire_at = response.expire_date;
                expired = response.expired;
                time_left = response.time_left;
/*
                console.log(
                    'time now: ' + moment().tz('America/Winnipeg').format('YYYY-MM-DD HH:mm:ss') +
                    '\nexpire at: ' + expire_at +
                    '\nmoment at: ' + moment().tz('America/Winnipeg').add(time_left, 's').format('YYYY-MM-DD HH:mm:ss') +
                    '\nnotify at: ' + moment().tz('America/Winnipeg').add(time_left-notifyBefore, 's').format('YYYY-MM-DD HH:mm:ss') +
                    '\nexpired: ' + expired  +
                    '\ntime left: ' + time_left + ' secs'
                );
*/

                newScheduleStr = moment().tz('America/Winnipeg').add(time_left-notifyBefore, 's').format('YYYY-MM-DD HH:mm:ss');

                if(response.revoked) {
                    // session access has been revoked
                    stopAll();
                    $.confirm({
                        icon: 'fas fa-exclamation-triangle',
                        title: 'Session Revoked',
                        draggable: false,
                        backgroundDismiss: false,
                        // lazyOpen: true,
                        content: 'Your session has been revoked please login again.',
                        buttons: {
                            extend: {
                                text: 'Ok',
                                btnClass: 'btn-blue',
                                action: function(){location.reload();}
                            },
                            close: {
                                text: 'close',
                                isHidden: true
                            }
                        }
                    });
                }

                else if(expired) {
                    // your session has expired
                    stopAll();
                    $.confirm({
                        icon: 'fas fa-exclamation-triangle',
                        title: 'Session Expired',
                        draggable: false,
                        backgroundDismiss: false,
                        // lazyOpen: true,
                        content: 'Your session has expired please login again.',
                        buttons: {
                            extend: {
                                text: 'Ok',
                                btnClass: 'btn-blue',
                                action: function(){location.reload();}
                            },
                            close: {
                                text: 'close',
                                isHidden: true
                            }
                        }
                    });
                }

                else if(!notifySet || notifyTimeStr !== newScheduleStr) {
                    // console.log('===================================');
                    // console.log('notifySet: ' + notifySet);
                    // console.log(time_left + " | " + notifyAfter);
                    // console.log('=============================');
                    //
                    // console.log('notify at: ' + newScheduleStr);
                    // console.log('notify in: ' + ((time_left - notifyBefore)/3600).toFixed(2) + ' hrs');
                    // console.log('-----------------------------------');

                    // re-init user notification time
                    notifyTimeStr = newScheduleStr;
                    notifySet = true;
                    notifyAfter = time_left;

                    if(notifyTimeoutObj) clearTimeout(notifyTimeoutObj);
                    notifyTimeoutObj = setTimeout(function () {
                        $.confirm({
                            icon: 'fas fa-exclamation-triangle',
                            title: 'Session Expiring Soon',
                            type: 'yellow',
                            draggable: false,
                            backgroundDismiss: false,
                            content: `Your session will expire in ${getExpireIn(time_left, notifyBefore, true)} minutes.`,
                            buttons: {
                                extend: {
                                    text: 'Extend',
                                    btnClass: 'btn-blue',
                                    action: function(){
                                        // ajax extend current session


                                        $.get( extendURL, function() {})
                                            .done(function(response) {
                                                // console.log("logged in? -> " + response.logged_in)
                                                if(response.extended)
                                                {
                                                    $.confirm({
                                                        icon: 'fas fa-check',
                                                        title: 'Session Extended',
                                                        type: 'green',
                                                        draggable: false,
                                                        backgroundDismiss: false,
                                                        content: response.msg,
                                                        buttons: {
                                                            extend: {
                                                                text: 'Ok',
                                                                btnClass: 'btn-blue'
                                                            },
                                                            close: {
                                                                text: 'close',
                                                                isHidden: true
                                                            }
                                                        }
                                                    });
                                                }else{
                                                    $.confirm({
                                                        icon: 'fas fa-exclamation-triangle',
                                                        title: 'Failed',
                                                        type: 'red',
                                                        draggable: false,
                                                        backgroundDismiss: false,
                                                        content: response.msg,
                                                        buttons: {
                                                            extend: {
                                                                text: 'Ok',
                                                                btnClass: 'btn-blue'
                                                            },
                                                            close: {
                                                                text: 'close',
                                                                isHidden: true
                                                            }
                                                        }
                                                    });

                                                }
                                            })
                                            .fail(function(error) {
                                                $.confirm({
                                                    icon: 'fas fa-exclamation-triangle',
                                                    title: 'Failed.',
                                                    draggable: false,
                                                    backgroundDismiss: false,
                                                    content: 'Unknown error occurred.',
                                                    buttons: {
                                                        extend: {
                                                            text: 'Ok',
                                                            btnClass: 'btn-blue'
                                                        },
                                                        close: {
                                                            text: 'close',
                                                            isHidden: true
                                                        }
                                                    }
                                                });
                                            });

                                    }
                                },
                                close: {
                                    text: 'close',
                                    isHidden: true
                                }
                            }
                        });
                    }, getExpireIn(time_left, notifyBefore, false));
                }
            });
    }

    function getExpireIn(timeLeft, notifyBefore, returnText = false)
    {
        if((timeLeft-notifyBefore)<0)
        {
            // notify now
            if(returnText)
            {
                return '~' + Math.floor(timeLeft/60); // minutes left
            }else{
                return 1000; // 1 second
            }
        }else{
            if(returnText)
            {
                return (notifyBefore/60); // default delay time in minutes
            }else{
                return ((timeLeft-notifyBefore)*1000); // default delay time
            }
        }
    }

    function stopAll()
    {
        if(pinging) clearTimeout(pinging);
        if(notifyTimeoutObj) clearTimeout(notifyTimeoutObj);
        // setTimeout(function(){
        //     location.reload();
        // }, 5000);
    }

    pinging = setInterval(function () {

        checkExpired();

    }, 30000);

    checkExpired();

});