"use strict";

$(document).ready(function()
{
    $("#roleTable").dataTable({
        "columnDefs": [
            {"sortable": false, "targets": [0]}
        ],
        "ajax": {
            "url": "../../role/get",
            "type": "POST"
        },
        "columns": [
            {"data": "no", "className": "text-center"},
            {"data": "role"},
            {"data": "description"},
            {"data": "list", render: function(data)
            {
                let rr = `<ul>`
                data.forEach(list =>
                {
                    rr += `<li>${list}</li>`
                });
    
                rr += `</ul>`
    
                return rr
            }}
        ]
    })
    
    $("#kcTable").dataTable({
        "columnDefs": [
            {"sortable": false, "targets": [0]}
        ],
        "ajax": {
            "url": "../../kantor_cabang/get",
            "type": "POST"
        },
        "columns": [
            {"data": "no", "className": "text-center"},
            {"data": "nama_cabang"},
            {"data": "kode_cabang"},
            {"data": "kanwil"}
        ]
    })
})
