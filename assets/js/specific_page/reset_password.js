"use strict";

$(document).ready(function()
{
    $('#resetPwdForm').submit(function(e)
    {
        e.preventDefault()
        
        const password = $('#password').val()
        const confirm = $('#confirm').val()
        
        if (password.trim() == '')
        {
            $('#password').addClass('is-invalid')
            
        }

        if (confirm.trim() == '')
        {
            $('#confirm').addClass('is-invalid')   
        }

        if (password.trim() !== confirm.trim())
        {
            $('#divAlert').show()
            $('#divAlert').removeClass('alert-light')
            $('#divAlert').removeClass('alert-success')
            $('#divAlert').addClass('alert-danger')
            $('#divAlert').html('Kata Sandi Tidak Sama Dengan Konfirmasi').css('color', 'black')
        }
        
        if (password.trim() && confirm.trim() && (password.trim() === confirm.trim()))
        {
            $('#password').addClass('is-valid')
            $('#password').removeClass('is-invalid')

            $('#confirm').addClass('is-valid')
            $('#confirm').removeClass('is-invalid')

            $('#divAlert').show()
            $('#divAlert').removeClass('alert-danger')
            $('#divAlert').addClass('alert-light')
            $('#divAlert').html('Proses Pengecekan').css('color', 'black')
            $('#submit').hide()

            $.post('../../password/change', {
                password:password,
                confirm:confirm,
            })
            .done(function(res)
            {
                const response = JSON.parse(res)

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

                setTimeout(() =>
                { 
                    window.location.href = '../../dashboard'; 
                }, 800)
            })
        }
    })
})