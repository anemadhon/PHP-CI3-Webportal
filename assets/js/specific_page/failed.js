"use strict";

$(document).ready(function()
{
    $("#reportFailedTable").dataTable({
        "columnDefs": [
            {"sortable": false, "targets": ['no-sort']}
        ],
        "order": [],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "../../../report_failed_upload/get",
            "type": "POST"
        },
        "columns": [
            {"data": "no", "className": "text-center"},
            {"data": "file"},
            {"data": "batch"},
            {"data": "uploaded_time"},
            {"data": "uploaded_by"},
            {"data": "rekening"},
            {"data": "perjanjian_kredit"},
            {"data": "no_polis"},
            {"data": "terbit"},
            {"data": "terbit_inquiry"},
            {"data": "status"},
            {"data": "tgl_awal_pk"},
            {"data": "tgl_akhir_pk"},
            {"data": "jk_waktu_kredit"},
            {"data": "id_valuta"},
            {"data": "kurs_valuta"},
            {"data": "plafond_kredit"},
            {"data": "suku_bunga_kredit"},
            {"data": "jenis_kredit"},
            {"data": "sub_jenis_kredit"},
            {"data": "type_tujuan_kredit"},
            {"data": "kolektibilitas_kredit"},
            {"data": "sektor_ekonomi"},
            {"data": "sumber_pelunasan_kredit"},
            {"data": "sumber_dana_kredit"},
            {"data": "mekanisme_penyaluran"},
            {"data": "cif_customer"},
            {"data": "nama_debitur"},
            {"data": "no_ktp_debitur"},
            {"data": "ttl"},
            {"data": "jk"},
            {"data": "alamat"},
            {"data": "jenis_pekerjaan"},
            {"data": "status_pegawai"},
            {"data": "tlp"},
            {"data": "hp"},
            {"data": "npwp"},
            {"data": "jenis_agunan"},
            {"data": "jenis_pengikatan"},
            {"data": "nilai_agunan"},
            {"data": "tgl_kirim"},
            {"data": "lain"},
            {"data": "broker_agent"},
            {"data": "kode_broker_agent"},
            {"data": "nama_broker_agent"},
            {"data": "nilai_tanggungan"}, 
            {"data": "rate_premi"},
            {"data": "nilai_premi"},
            {"data": "tgl_awal_tanggungan"},
            {"data": "tgl_akhir_tanggungan"},
            {"data": "jk_waktu_tanggungan"}
        ]
    })
})
