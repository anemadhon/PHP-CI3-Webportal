"use strict";

$(document).ready(function()
{
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
                $.post('../../../../business_partner/soft_delete', {
                    id:id
                })
                .done(function(res)
                {
                    if (res)
                    {
                        const response = JSON.parse(res)
    
                        if (response.success === false) {
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
                            location.replace('../../bp')
                        }
                    })
                })
			
			return
            }

            swal('Data Aman')
        })
    })

    $('#bpForm').submit(function(e)
    {
        e.preventDefault()

        const id = $('#id').val() ? $('#id').val() : ''
        const nama = $('#nama').val()
        const jenis = $('#jenis option:selected').val()
        const flag = $('#flag').val()

        let url = ''
        let reload = ''

        if (nama.trim() == '')
        {
            $('#nama').addClass('is-invalid')   
        }
        
        if (jenis == '')
        {
            $('#jenis').addClass('is-invalid')   
        }

        if (nama.trim() && jenis)
        {
            $('#nama').addClass('is-valid')
            $('#nama').removeClass('is-invalid')

            $('#jenis').addClass('is-valid')
            $('#jenis').removeClass('is-invalid')

            $('#divAlert').show()
            $('#divAlert').removeClass('alert-danger')
            $('#divAlert').addClass('alert-light')
            $('#divAlert').html('Proses Pengecekan').css('color', 'black')
            $('#submit').hide()

            if (flag == 1)
            {
                url = '../../../business_partner/submit'
                reload = '../bp'
            } 
            
            if (flag == 2)
            {
                url = '../../../../business_partner/update'
                reload = '../../bp'
            }

            $.post(url, {
                id:id,
                nama:nama,
                jenis:jenis
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
                    if (res)
                    {
                        location.reload(true)
                    }
                })
            })
        }
    })
})