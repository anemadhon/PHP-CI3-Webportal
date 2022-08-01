"use strict";

$(document).ready(function()
{
    $('#divBtnAction').hide()

    const inputKodeCabang = $('#inputKodeCabang').val() ? $('#inputKodeCabang').val() : ''
    
    if (inputKodeCabang === '')
    {
        $('#divSelectCabang').css('visibility', 'hidden')
    }
    else 
    {
        $('#divSelectCabang').css('visibility', 'visible')
    }

    const flag = $('#flag').val()
    let url = ''
    
    if (flag == 1)
    {
        url = '../../../kantor_cabang/get'
    }

    if (flag == 2)
    {
        url = '../../../../kantor_cabang/get'
    }

    $.post(url,(data) =>
    {
        const option = JSON.parse(data)
        if (option)
        {
            option.data.forEach((val) =>
            {						
                $("<option />", {value:val.kode_cabang, text:val.nama_cabang, selected: val.kode_cabang == inputKodeCabang ? true : false}).appendTo($('#cabang'))
            })

            $('#divBtnAction').show()
        }
    })

    $('#role').change(function()
    {
        if ($('#role option:selected').text() === 'Kantor Cabang')
        {
            $('#divSelectCabang').css('visibility', 'visible')
        }
        else
        {
            $('#divSelectCabang').css('visibility', 'hidden')
        }
    })

    $('#hapus').click(function(e)
    {
        e.preventDefault()

        const id = $('#id').val() ? $('#id').val() : ''

        swal({
            title: 'Anda Yakin ?',
            text: 'Ketika anda Hapus, Data akan hilang',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) =>
          {
            if (willDelete)
            {
                $.post('../../../../user/soft_delete', {
                    id:id
                })
                .done(function(res)
                {
                    if (res)
                    {
                        const response = JSON.parse(res)
    
                        if (response.success === false)
                        {
                            swal(response.message, {
                                icon: 'error',
                            })

                            return
                        }
                    } 

                    swal('Data Berhasil Di hapus', {
                        icon: 'success',
                    })
                    .then((feedback) =>
                    {
                        if (feedback)
                        {
                            location.replace('../../user')
                        }
                    })
                })

			return
            }

            swal('Data Aman')
        })
    })

    $('#userForm').submit(function(e)
    {
        e.preventDefault()

        const id = $('#id').val() ? $('#id').val() : ''
        const name = $('#name').val()
        const username = $('#username').val()
        const password = $('#password').val()
        const role = $('#role option:selected').val()
        const roleText = $('#role option:selected').text()
        const kantorCabang = $('#cabang option:selected').val()
        
        let reload = ''

        if (name.trim() == '')
        {
            $('#name').addClass('is-invalid')   
        }
        
        if (username.trim() == '')
        {
            $('#username').addClass('is-invalid')    
        }
        
        if (password == '')
        {
            $('#password').addClass('is-invalid')
        }
        
        if (role == '')
        {
            $('#role').addClass('is-invalid')
        }

        if (roleText === 'Kantor Cabang' && kantorCabang == '')
        {
            $('#cabang').addClass('is-invalid')
        }

        if (name.trim() && username.trim() && role && (password || (flag == 2 || flag == 1)))
        {
            $('#name').addClass('is-valid')
            $('#name').removeClass('is-invalid')

            $('#username').addClass('is-valid')
            $('#username').removeClass('is-invalid')

            $('#password').addClass('is-valid')
            $('#password').removeClass('is-invalid')

            $('#role').addClass('is-valid')
            $('#role').removeClass('is-invalid')

            if (roleText === 'Kantor Cabang' && kantorCabang == '')
            {
                $('#cabang').addClass('is-invalid')
                return
            }

            $('#cabang').addClass('is-valid')
            $('#cabang').removeClass('is-invalid')

            $('#divAlert').show()
            $('#divAlert').removeClass('alert-danger')
            $('#divAlert').addClass('alert-light')
            $('#divAlert').html('Proses Pengecekan').css('color', 'black')
            $('#submit').hide()

            if (flag == 1)
            {
                url = '../../../user/submit'
                reload = '../user'
            } 
            
            if (flag == 2)
            {
                url = '../../../../user/update'
                reload = '../../user'
            }

            $.post(url, {
                id:id,
                name:name,
                username:username,
                password:password,
                role:role,
                cabang:kantorCabang
            })
            .done(function(res)
            {
                if (res)
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
                }
                
                $('#divAlert').hide()

                location.replace(reload)
            })
            .fail(function(xhr)
            {
                $('#submit').show()

                swal({
                    title: `Eror ${xhr.status}`,
                    text: xhr.statusText,
                    icon: 'error',
                    dangerMode: true,
                })
                .then((res) =>
                {
                    if (res) {
                        location.reload(true)
                    }
                })
            })
        }
    })
})