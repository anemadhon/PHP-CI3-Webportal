"use strict";

$(document).ready(function()
{
    $("#bpTable").dataTable({
        "columnDefs": [
            {"sortable": false, "targets": [0, 3]}
        ],
        "ajax": {
            "url": "../../business_partner/get",
            "type": "POST"
        },
        "columns": [
            {"data": "no", "className": "text-center"},
            {"data": "nama"},
            {"data": "jenis"},
            {"data": "id", "className": "text-center", render: function(data)
            {
                let rr = `<a href="bp/edit/${data}" class="btn btn-icon btn-success"><i class="fas fa-edit" title="Ubah Data"></i></a>`;
                
                return rr
            }}
        ]
    })

    $("#pksBpTable").dataTable({
        "columnDefs": [
            {"sortable": false, "targets": [0, 8]}
        ],
        "ajax": {
            "url": "../../pks_business_partner/get",
            "type": "POST"
        },
        "columns": [
            {"data": "no", "className": "text-center"},
            {"data": "nama"},
            {"data": "pks_askrindo"},
            {"data": "pks_eksternal"},
            {"data": "produk_eksternal", render: function(data)
            {
                let rr = `<ul>`
                data.forEach(list =>
                {
                    rr += `<li>${list}</li>`
                });

                rr += `</ul>`

                return rr
            }},
            {"data": "bank"},
            {"data": "bank_cabang", render: function(data)
            {
                let rr = `<ul>`
                data.forEach(list =>
                {
                    rr += `<li>${list}</li>`
                });

                rr += `</ul>`

                return rr
            }},
            {"data": "list", render: function(data)
            {
                let rr = `<ul>`
                data.forEach(list =>
                {
                    rr += `<li>${list}</li>`
                });

                rr += `</ul>`

                return rr
            }},
            {"data": "id", "className": "text-center", render: function(data)
            {
                let rr = `<a href="pksxbp/edit/${data}" class="btn btn-icon btn-success"><i class="fas fa-edit" title="Ubah Data"></i></a>`;
                
                return rr
            }}
        ]
    })
})