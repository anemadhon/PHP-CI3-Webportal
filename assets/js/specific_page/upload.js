"use strict";

$(document).ready(function()
{
    FilePond.registerPlugin(FilePondPluginFileValidateType)

    const inputElement = document.querySelector('#file')
    
    FilePond.create(inputElement, {
        allowFileTypeValidation: true,
        acceptedFileTypes: ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
    })
    
    FilePond.setOptions({
        server: '../../../upload_template/to_temporary_storage',
    })

    $('#uploadForm').submit(function(e)
    {
        e.preventDefault()

        const file = $('input:hidden[name=file]').val()
        const fileText = file ? file.split('|') : '00'

        if (!file)
        {
            $('.text-danger').show()
        }

        if (fileText[0] == '00')
        {
            $('.text-danger').show()
            $('.text-danger').text(fileText[1])

            setTimeout(() => {
                $('.text-danger').hide() 
            }, 600);

            return
        }

        if (file && fileText[0] != '00')
        {
            $('.progress').show()
            $('.progress-bar').width('10%')
            $('.progress-bar').text('10%')

            $('#upload').hide()
            $('.text-danger').hide()

            $.post('../../../upload_template/excel_to_db', {
                file:file
            })
            .done(function(res)
            {
                if (res === '0' || res === '00')
                {
                    $('#divAlert').show()
                    $('#divAlert').removeClass('alert-success')
                    $('#divAlert').addClass('alert-danger')
                    $('#idSpan').html('Gagal')

                    return
                }

                let percentage = 0

                const timer = setInterval(() =>
                {
                    percentage = percentage + 20
                    progress_bar(percentage, timer, res)
                }, 1000)
            })
            .fail(function(xhr)
            {
                $('#upload').show()

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

    function progress_bar(percentage, timer, response)
    {
        $('.progress').show()
        $('.progress-bar').width(`${percentage}%`)
        $('.progress-bar').text(`${percentage}%`)
        
        if(percentage > 100)
        {
            clearInterval(timer)

            $('.progress-bar').width('100%')
            $('.progress-bar').text('100%')

            $('#upload').hide()

            const text = `Berhasil Mengunggah file ${response.split('|')[0]} dengan No. Batch ${response.split('|')[1]}`
            const textDanger = `Sedang berlangsung proses upload utk File: ${response.split('|')[0]} dengan No. Batch: ${response.split('|')[1]}. Proses selanjutkan bisa dilakukan setelah proses ini selesai`

            setTimeout(() =>
            {
                $('#divAlert').show()
                $('#idSpan').text(text)
                $('#divAlertDanger').show()
                $('#idSpanDanger').text(textDanger)
                $('#divInfo').show()
            }, 1000)
        }
    }
})
