"use strict";

$(document).ready(function()
{
    $('#userTable').dataTable({
        "columnDefs": [
            {"sortable": false, "targets": [0, 6]}
        ],
        "ajax": {
            "url": "../../user/get",
            "type": "POST"
        },
        "columns": [
            {"data": "no", "className": "text-center"},
            {"data": "name"},
            {"data": "username"},
            {"data": "role"},
            {"data": "cabang"},
            {"data": "status"},
            {"data": "id", "className": "text-center", render: function(data)
            {
                let rr = `<a href="user/edit/${data}" class="btn btn-icon btn-success"><i class="fas fa-edit" title="Ubah Pengguna"></i></a>
                    <button class="btn btn-icon btn-warning unlock-user" id="unlock-user-${data}"><i class="fas fa-unlock" title="Buka Pengguna"></i></button>
                    <button class="btn btn-icon btn-danger disable-user" id="disable-user-${data}"><i class="fas fa-user-slash" title="Tidak Aktif Pengguna"></i></button>
                    <button class="btn btn-icon btn-info activate-user" id="activate-user-${data}"><i class="fas fa-user" title="Aktif Pengguna"></i></button>
                    <button class="btn btn-icon btn-secondary reset-pwd" id="reset-pwd-${data}"><i class="fas fa-cog" title="Reset Kata sandi"></i></button>`;
                
                return rr
            }}
        ]
    })

    const userTable = $('#userTable tbody')
    userTable.on('click','.unlock-user', function()
    {
        const tr = $(this).closest('tr')
        const idx = tr[0].rowIndex

        setUnlockUser(idx)
    })
  
    userTable.on('click','.disable-user', function()
    {
        const tr = $(this).closest('tr')
        const idx = tr[0].rowIndex

        setDisableUser(idx)
    })
    
    userTable.on('click','.activate-user', function()
    {
        const tr = $(this).closest('tr')
        const idx = tr[0].rowIndex

        setActivateUser(idx)
    })
    
    userTable.on('click','.reset-pwd', function()
    {
        const tr = $(this).closest('tr')
        const idx = tr[0].rowIndex

        setResetPwd(idx)
    })

    function setUnlockUser(idx)
    {
        const rowUserTable = document.getElementById('userTable').rows[idx].cells
        const prm = rowUserTable[6].children[1].id
        const id = prm.split('-')

        $.post('../../user/unlock', {id:id[2]}, function(res)
        {
            const response = JSON.parse(res)
            swal(response.title, response.message, response.success)
            .then((data) =>
            {
                if (data)
                {
                    $("#userTable").DataTable().ajax.reload()
                }
            })
        })
    }
    
    function setDisableUser(idx)
    {
        const rowUserTable = document.getElementById('userTable').rows[idx].cells
        const prm = rowUserTable[6].children[1].id
        const id = prm.split('-')

        $.post('../../user/disable', {id:id[2]}, function(res)
        {
            const response = JSON.parse(res)
            swal(response.title, response.message, response.success)
            .then((data) =>
            {
                if (data)
                {
                    $("#userTable").DataTable().ajax.reload()
                }
            })
        })
    }
    
    function setActivateUser(idx)
    {
        const rowUserTable = document.getElementById('userTable').rows[idx].cells
        const prm = rowUserTable[6].children[1].id
        const id = prm.split('-')

        $.post('../../user/activate', {id:id[2]}, function(res)
        {
            const response = JSON.parse(res)
            swal(response.title, response.message, response.success)
            .then((data) =>
            {
                if (data)
                {
                    $("#userTable").DataTable().ajax.reload()
                }
            })
        })
    }

    function setResetPwd(idx)
    {
        const rowUserTable = document.getElementById('userTable').rows[idx].cells
        const prm = rowUserTable[6].children[1].id
        const id = prm.split('-')

        swal({
            title: 'Silahkan Isi Kata Sandi Baru',
            content: {
                element: 'input',
                attributes: {
                placeholder: 'Kata Sandi baru',
                type: 'password',
                id: 'password'
                },
            },
        }).then((data) =>
        {
            if (data !== null)
            {
                $.post('../../password/reset', {
                    id:id[2],
                    password:data
                }, function(res)
                {
                    const response = JSON.parse(res)
                    swal(response.title, response.message, response.success)
                })
            }
        })
    }
})