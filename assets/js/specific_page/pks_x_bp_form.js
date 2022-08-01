"use strict";

$(document).ready(function()
{
    $('#divBtnAction').hide()

    const inputIdBP = $('#inputIdBP').val() ? $('#inputIdBP').val() : ''
    const inputTipeBP = $('#inputTipeBP').val() ? $('#inputTipeBP').val() : ''
    const inputCabang = $('#inputCabang').val() ? $('#inputCabang').val() : ''
    const cabang = inputCabang ? inputCabang.split(',') : ''	
    const flag = $('#flag').val()

    let urlBP = ''
    let urlCabang = ''

    if (flag == 1)
    {
        urlBP = '../../../business_partner/get'
        urlCabang = '../../../kantor_cabang/get'
    }
    
    if (flag == 2)
    {
        urlBP = '../../../../business_partner/get'
        urlCabang = '../../../../kantor_cabang/get'
    }
    
    $.post(urlBP,(data) =>
    {
        const option = JSON.parse(data)
        if (option)
        {
            option.data.forEach((val) => {						
                $("<option />", {value:val.id, text:val.nama, selected: val.id == inputIdBP ? true : false, type:val.jenis}).appendTo($('#idbusiness'))
            })
        }
    });
    
    $.post(urlCabang,(data) =>
    {
        const option = JSON.parse(data)

        if (option)
        {
            option.data.forEach((val) =>
            {
                $("<option />", {value:val.kode_cabang, text:val.nama_cabang, selected: inputCabang.includes(val.kode_cabang) ? true : false}).appendTo($('#idcabangask'))
            })
            
            if (cabang.length == option.length)
            {
                $('#idcabangask').multiselect({
                    includeSelectAllOption: true
                })
            } 

            $('#idcabangask').multiselect().multiselect('rebuild') 
            $('#divBtnAction').show()
        }
    });
    
    if (inputIdBP && (inputTipeBP == 2 || inputTipeBP == 3))
    {
        $('#divInputBrokerAgent').css('visibility', 'visible')
    }
    else
    {
        $('#divInputBrokerAgent').css('visibility', 'hidden')
    }

    $('#idbusiness').change(function()
    {
        if ($('#idbusiness option:selected').attr('type') === 'AGENT' || $('#idbusiness option:selected').attr('type') === 'BROKER')
        {
            $('#divInputBrokerAgent').css('visibility', 'visible')
        }
        else
        {
            $('#divInputBrokerAgent').css('visibility', 'hidden')
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
                $.post('../../../../pks_business_partner/soft_delete', {
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
                            location.replace('../../pksxbp')
                        }
                    })
                })

			return
            }

            swal('Data Aman')
        })
    })

    $('#pksBpForm').submit(function(e)
    {
        e.preventDefault()

        const id = $('#id').val() ? $('#id').val() : ''
        const idbusiness = $('#idbusiness option:selected').val()
        const askrindo = $('#askrindo').val()
        const eksternal = $('#eksternal').val()
        const produk = $('#produk').val()
        const bank = $('#bank').val()
        const cabangbank = $('#cabangbank').val()
        const idcabangask = $('#idcabangask').val()
        const brokeragent = $('#brokeragent').val()

        let url = ''
        let reload = ''

        if (idbusiness == '')
        {
            $('#idbusiness').addClass('is-invalid')   
        }
        
        if (askrindo.trim() == '')
        {
            $('#askrindo').addClass('is-invalid')   
        }
        
        if (eksternal.trim() == '')
        {
            $('#eksternal').addClass('is-invalid')   
        }
        
        if (produk.trim() == '')
        {
            $('#produk').addClass('is-invalid')   
        }
        
        if (bank.trim() == '')
        {
            $('#bank').addClass('is-invalid')   
        }
        
        if (cabangbank.trim() == '')
        {
            $('#cabangbank').addClass('is-invalid')   
        }
        
        if (idcabangask.length === 0)
        {
            $('.text-danger').show()
        }

        if (($('#idbusiness option:selected').attr('type') === 'AGENT' || $('#idbusiness option:selected').attr('type') === 'BROKER') && brokeragent.trim() == '')
        {
            $('#brokeragent').addClass('is-invalid')
        }

        if (idbusiness && askrindo.trim() && eksternal.trim() && produk.trim() && bank.trim() && bank.trim() && idcabangask)
        {
            $('#idbusiness').addClass('is-valid')
            $('#idbusiness').removeClass('is-invalid')

            $('#askrindo').addClass('is-valid')
            $('#askrindo').removeClass('is-invalid')

            $('#eksternal').addClass('is-valid')
            $('#eksternal').removeClass('is-invalid')

            $('#produk').addClass('is-valid')
            $('#produk').removeClass('is-invalid')

            $('#bank').addClass('is-valid')
            $('#bank').removeClass('is-invalid')

            $('#cabangbank').addClass('is-valid')
            $('#cabangbank').removeClass('is-invalid')
            
            $('.text-danger').hide()

            if (($('#idbusiness option:selected').attr('type') === 'AGENT' || $('#idbusiness option:selected').attr('type') === 'BROKER') && brokeragent.trim() == '')
            {
                $('#brokeragent').addClass('is-invalid')
                return
            }
            
            $('#brokeragent').addClass('is-valid')
            $('#brokeragent').removeClass('is-invalid')

            $('#divAlert').show()
            $('#divAlert').removeClass('alert-danger')
            $('#divAlert').addClass('alert-light')
            $('#divAlert').html('Proses Pengecekan').css('color', 'black')
            $('#submit').hide()

            if (flag == 1)
            {
                url = '../../../pks_business_partner/submit'
                reload = '../pksxbp'
            } 
            
            if (flag == 2)
            {
                url = '../../../../pks_business_partner/update'
                reload = '../../pksxbp'
            }

            $.post(url, {
                id:id,
                idbusiness:idbusiness,
                askrindo:askrindo,
                eksternal:eksternal,
                produk:produk,
                bank:bank,
                cabangbank:cabangbank,
                idcabangask:idcabangask,
                brokeragent:brokeragent
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