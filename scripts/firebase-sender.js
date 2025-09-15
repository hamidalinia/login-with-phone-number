jQuery(document).ready(function ($) {
var fb_lwp_nonce=idehweb_lwp.nonce,fb_lwp_phone_number='',fb_lwp_email='';

    if (firebase)
        if (!firebase.apps.length) {
            try {
                firebase.initializeApp(firebaseConfig);

            } catch (e) {
                console.error('firebaseConfig is not defined!')

            }

        } else {
            firebase.app(); // if already initialized, use that one
        }

    $('body').on('submit', 'form.ajax-auth.lwp-login-form-i.firebase', function (e) {

        // $('form.ajax-auth.lwp-login-form-i.firebase').on('submit', function (e) {
        console.log('click on submit firebase...');
        let method = $('input[name="otp-method"]:checked').val();
        if (!method) {
            method = $('#lwp_login').attr('data-method');
        }
        console.log('method', method)

        if (method !== 'firebase') {
            console.log('method is not firebase, return false')
            return false;
        }
        // if (!$(this).valid()) return false;
        if (!$(this).valid()) {
            var reason = '';
            if ($('[name="lwp_accept_terms"]').length && !$('[name="lwp_accept_terms"]').valid()) {
                reason = 'You must agree to the terms of service!'
            } else {
                reason = 'Phone number is required';
            }
            $('p.status', this).show().text(reason);
            return false;
        }
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_login';
        // var username = $('.lwp_username').val();
        // username = username.replace(/^0+/, '');

        var username = $('[name="lwp_username"]').val();
        username = username.replace(/^[0\+]+/, '');

        var lwp_country_codes = $('#lwp_country_codes').val();
        // console.log('lwp_country_codes', lwp_country_codes);

        // if (!lwp_country_codes) {
        //     lwp_country_codes = $('.iti__country-list li:first-child').attr('data-dial-code');
        // }
        // console.log('lwp_country_codes', lwp_country_codes);
        // return
        username = lwp_country_codes + username;
        fb_lwp_phone_number=username;

        console.log('security', fb_lwp_nonce)
        var ctrl = $(this);
        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'nonce': fb_lwp_nonce,
                'username': username,
                'method': method,
                // 'password': password,
                // 'email': email,
                // 'security': security
            },
            success: function (data) {
                if (data && data.ID)
                    idehweb_lwp.UserId = data.ID;

                // $('p.status', ctrl).text(data.message);
                $('.lwp_bottom_activation').css('display', 'block');
                $('.lwp_bottom_activation .lwp_change_pn').css('display', 'block');
                $('.lwp_bottom_activation .lwp_change_el').css('display', 'none');
                if (data.success == true) {
                    if (data.authWithPass && data.showPass) {
                        $('#lwp_login_email').fadeOut(10);
                        $('#lwp_login').fadeOut(10);
                        $('#lwp_enter_password').fadeIn(500);

                    } else {
                        $('p.status').html(idehweb_lwp?.message_running_recaptcha);

                        runFBase("+" + username);


                    }
                    //     document.location.href = idehweb_lwp.redirecturl;
                } else {
                    $('p.status').html(data.message);
                }
            }
        });
        e.preventDefault();


        // firebaseAuth.confirm( otpForm.getOtpValue() ).then(function (result) {
        //
        //     firebase.auth().currentUser.getIdToken( false ).then(function(idToken) {
        //         otpForm.verifyOTP( { firebase_idToken: idToken } );
        //     })
        //
        // }).catch(function (error) {
        //     // User couldn't sign in (bad verification code?)
        //     otpForm.verifyOTP( { firebase_error: JSON.stringify( error ) } );
        // });
    });
    $('body').on('submit', 'form.ajax-auth.lwp-register-form-i.firebase', function (e) {
        e.preventDefault();

        // $('form#lwp_activate').on('submit', function (e) {
        if (!$(this).valid()) return false;

        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_ajax_register';
        var security = $('.lwp_scode').val();

        var obj = {
            'action': action,
            'secod': security,
            'method': 'firebase',
            'nonce': fb_lwp_nonce
        };
        $('#lwp_login').fadeOut(10);
        $('#lwp_login_email').fadeOut(10);
        // $('#lwp_activate').fadeOut(500);
        var phone_number = $('[name="lwp_username"]').val();
        if (phone_number) {
            var lwp_country_codes = $('#lwp_country_codes').val();
            phone_number = phone_number.replace(/^[0\+]+/, '');
            phone_number = lwp_country_codes + phone_number;
            obj['phone_number'] = phone_number;
            fb_lwp_phone_number=phone_number;

        }
        var email = $('.lwp_email').val();
        if (email) {
            obj['email'] = email;
            fb_lwp_email=email;

        }
        if (window.confirmationResult && window.confirmationResult.verificationId) {
            obj['verificationId'] = window.confirmationResult.verificationId;
        }
        var ctrl = $(this);
        console.log('code entered:', obj['secod']);
        // return confirmationResult.confirm(obj['secod']).then((result) => {
        //     console.log('result',result);
        // User signed in successfully.
        // const user = result.user;
        $.ajax({
            // type: 'GET',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: obj,
            success: function (data) {
                if(data.nonce){
                    fb_lwp_nonce=data.nonce;
                }
                if (!data?.authWithPass) {

                    if (data?.lwp_update_extra_fields && data?.userRegisteredNow) {
                        console.log("data.lwp_update_extra_fields && data.userRegisteredNow")

                        $('#lwp_activate').fadeOut(500);
                        $('#lwp_update_extra_fields').fadeIn(500);
                        return;
                    }
                }

                if (data?.authWithPass) {

                    if (data.updatedPass && data.lwp_update_extra_fields) {
                        console.log("data.updatedPass && data.lwp_update_extra_fields")
                        $('#lwp_activate').fadeOut(500);
                        $('#lwp_update_extra_fields').fadeIn(500);

                    }else if (!data.updatedPass && data.lwp_update_extra_fields) {
                        console.log("!data.updatedPass && data.lwp_update_extra_fields")

                        $('#lwp_activate').fadeOut(500);
                        $('#lwp_update_password').fadeIn(500);
                        $('#lwp_update_password').addClass(data?.userRegisteredNow ? "" : "lwp-hide-extra");

                    }else if (data.updatedPass && !data.lwp_update_extra_fields) {
                        console.log("data.updatedPass && !data.lwp_update_extra_fields")

                        $('#lwp_activate').fadeOut(500);
                        $('#lwp_update_password').fadeIn(500);

                    }else if (!data.updatedPass && !data.lwp_update_extra_fields) {
                        console.log("!data.updatedPass && !data.lwp_update_extra_fields")

                        $('#lwp_activate').fadeOut(500);
                        $('#lwp_update_password').fadeIn(500);

                    } else {
                        console.log("else of data.updatedPass && data.lwp_update_extra_fields")

                        $('p.status', ctrl).text(data.message);
                        if (data.success)
                            document.location.href = idehweb_lwp.redirecturl;

                    }
                } else {
                    $('p.status', ctrl).text(data.message);
                    if (data.success)
                        document.location.href = idehweb_lwp.redirecturl;
                }
            }
        });
        // }).catch((error) => {
        //     console.log('error',error);
        //     // User couldn't sign in (bad verification code?)
        //     // ...
        // });

    });

    $('body').on('submit', 'form#lwp_update_password.firebase', function (e) {
        e.preventDefault();

        if (!$(this).valid()) return false;
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_update_password_action';
        var lwp_up_password = $('.lwp_up_password').val();
        var update_object={};
        $("#lwp_update_password :input").each(function(){
            var input = $(this);
            let name=input.attr('name'),
                type=input.attr('type');

            if(name=="security" || name=="_wp_http_referer" || name=="lwp_up_password")
                return

            if(type=="text")
                update_object[name]=input.val()

            if(type=="radio")
                update_object[name]=$("input[name='"+name+"']:checked").val();



        });
        // console.log("update_object:",update_object)
        var obj = {
            'action': action,
            'password': lwp_up_password,
            'nonce':fb_lwp_nonce,
            ...update_object
        };
        if(fb_lwp_phone_number){
            obj['phone_number']=fb_lwp_phone_number;
        }
        if(fb_lwp_email){
            obj['email']=fb_lwp_email;
        }
        var ctrl = $(this);

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: obj,
            xhrFields: {
                withCredentials: true
            },
            success: function (data) {
                $('p.status', ctrl).text(data.message);
                if (data.success)
                    document.location.href = idehweb_lwp.redirecturl;

            }
        });
    });

    $('body').on('click', '.forgot_password.firebase , .lwp_didnt_r_c.firebase', function (e) {
        console.log('forgot_password with method firebase');

        if (!$(this).valid()) return false;
        let method = $('input[name="otp-method"]:checked').val();
        if (!method) {
            method = $('#lwp_login').attr('data-method');
        }
        if (method !== 'firebase') {
            console.log('method is not firebase')

            return false;
        }
        $('p.status', this).show().text(idehweb_lwp.loadingmessage);
        var action = 'lwp_forgot_password';
        // var username = $('.lwp_username').val();
        // username = username.replace(/^0+/, '');
        var username = $('[name="lwp_username"]').val();
        username = username.replace(/^[0\+]+/, '');
        var lwp_country_codes = $('#lwp_country_codes').val();
        username = lwp_country_codes + username;
        var email = $('.lwp_email').val();
        // $('#lwp_login').fadeOut(10);
        $('#lwp_enter_password').fadeOut(10);
        $('#lwp_login').fadeIn(500);
if(username){
    fb_lwp_phone_number=username;
}
if(email){
    fb_lwp_email=email;
}
        $.ajax({
            dataType: 'json',
            url: idehweb_lwp.ajaxurl,
            data: {
                'action': action,
                'phone_number': username,
                'email': email,
                'nonce': fb_lwp_nonce,
                'ID': idehweb_lwp.UserId,
                'method': method
            },
            success: function (data) {

                runFBase("+" + username);
            }
        });
        e.preventDefault();
    });


    function runFBase(phoneNumber) {
        // Ensure reCAPTCHA container exists
        if (!$('#lwp-firebase-recaptcha').length) {
            $('<div id="lwp-firebase-recaptcha"></div>').insertBefore('.submit_button.auth_phoneNumber');
        }

        // Initialize recaptchaVerifier if it doesn't exist
        if (!window.recaptchaVerifier) {
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('lwp-firebase-recaptcha', {
                'size': 'invisible'
            });
        }

        // Use a timeout to ensure reCAPTCHA is ready
        setTimeout(function() {
            firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier)
                .then(function(confirmationResult) {
                    window.confirmationResult = confirmationResult;
                    $('#lwp_login_email').fadeOut(10);
                    $('#lwp_login').fadeOut(10);
                    $('#lwp_activate').fadeIn(500);
                    window.lwp_runTimer();
                }).catch(function(error) {
                console.error('Error:', error);
                if (error && error.message && error.code) {
                    $('p.status').html("<strong>"+error.code+"</strong><div>"+error.message+"</div>");
                }

                // Reset and recreate recaptchaVerifier on error
                if (window.recaptchaVerifier) {
                    window.recaptchaVerifier.clear();
                    window.recaptchaVerifier = null;
                }
            });
        }, 1000); // 1 second delay to ensure everything is loaded
    }

    function runFBaseOld(phoneNumber) {

        if (!window.recaptchaVerifier) {
            $('<div id="lwp-firebase-recaptcha"></div>').insertBefore('.submit_button.auth_phoneNumber');
            //Firebase
            window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('lwp-firebase-recaptcha', {
                'size': 'invisible',
                'callback': function (response) {
                    console.log('recaptchaVerifier response');
                }
            });
        }
        const appVerifier = window.recaptchaVerifier;
        // console.log('phoneNumber', phoneNumber);
        firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
            .then(function (confirmationResult) {
                // console.log('confirmationResult', confirmationResult);
                // console.log('verificationId', confirmationResult.verificationId);
                window.confirmationResult = confirmationResult;
                $('#lwp_login_email').fadeOut(10);
                $('#lwp_login').fadeOut(10);
                $('#lwp_activate').fadeIn(500);
                window.lwp_runTimer();
                // SMS sent. Prompt user to type the code from the message, then sign the
                // user in with confirmationResult.confirm(code).
                // console.log('confirmationResult', confirmationResult);
                // return confirmationResult.confirm(testVerificationCode)
                // phoneForm.otpFormHandler.firebaseAuth = confirmationResult;
                // phoneForm.onRequestOTP(response);
            }).catch(function (error) {

            // Error; SMS not sent
            // response.otp_sent 	= 0;
            // response.notice 	= error.message ? parse_notice( error.message ) : xoo_ml_phone_localize.notices.try_later;
            // phoneForm.onRequestOTP(response);

            window.recaptchaVerifier.render().then(function (widgetId) {
                grecaptcha.reset(widgetId);
            });
            console.log(error);
            if (error && error.message && error.code)
                $('p.status').html("<strong>"+error.code+"</strong><div>"+error.message+"</div>");

        });
    }
})