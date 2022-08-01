"use strict";

$(document).ready(function()
{
    $('#loginForm').submit(function(e)
    {
        e.preventDefault()
        
        const username = $('#username').val()
        const password = $('#password').val()
        const remember = $('#rememberMe').is(':checked') ? 1 : 0
        const recaptcha = $('#g-recaptcha-response').val()

        if (username.trim() == '')
        {
            $('#username').addClass('is-invalid')  
        }
        
        if (password.trim() == '')
        {
            $('#password').addClass('is-invalid')
        }
        
        if (username.trim() && password.trim())
        {
            $('#username').addClass('is-valid')
            $('#username').removeClass('is-invalid')

            $('#password').addClass('is-valid')
            $('#password').removeClass('is-invalid')

            $('#divAlert').show()
            $('#divAlert').removeClass('alert-danger')
            $('#divAlert').addClass('alert-light')
            $('#divAlert').html('Proses Pengecekan').css('color', 'black')
            $('#submit').hide()

            $.post('auth/login', {
                username:username,
                password:password,
                remember:remember,
                recaptcha:recaptcha
            })
            .done(function(res)
            {
                const response = JSON.parse(res)

                if (response.response && response.response['error-codes'][0] == 'timeout-or-duplicate')
                {
                    $('.link').show()
                    $('#link').click(function(e)
                    {
                        e.preventDefault()
                        let src = $('iframe[title="reCAPTCHA"]')[0].attributes[1].value
                        $('iframe[title="reCAPTCHA"]')[0].attributes[1].value = src
                        
                        setTimeout(() =>
                        {
                            $('.link').hide()
                        }, 600);
                    })
                }  

                $('#divAlert').removeClass('alert-light')

                if (response.success === false)
                {
                    $('#submit').show()

                    $('#divAlert').removeClass('alert-success')
                    $('#divAlert').addClass('alert-danger')
                    $('#divAlert').html(response.message).css('color', 'black')

                    return
                }

                $('#divAlert').removeClass('alert-danger')
                $('#divAlert').addClass('alert-success')
                $('#divAlert').html(response.message).css('color', 'black')

                if (response.login === 0)
                {
                    setTimeout(() =>
                    { 
                        window.location.href = 'auth/password/change'
                    }, 800)
                } 
                
                if (response.login === 1)
                {
                    setTimeout(() =>
                    { 
                        window.location.href = 'dashboard'
                    }, 800)
                }
            })
        }
    })
})