"use strict";

loadTotalData()
loadTotalDataOnProgress()
loadTotalDataSuccessed()
loadTotalDataFailed()

$(document).ready(function()
{
    $("#monitorUploadTable").dataTable({
        "columnDefs": [
            {"sortable": false, "targets": [0, 3, 4, 5, 6, 9]}
        ],
        "order": [],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "../../monitoring_upload_template/get",
            "type": "POST"
        },
        "columns": [
            {"data": "no", "className": "text-center"},
            {"data": "batch"},
            {"data": "file"},
            {"data": "rows"},
            {"data": "process"},
            {"data": "successed"},
            {"data": "failed"},
            {"data": "uploader"},
            {"data": "uploaded_time"},
            {"data": "status"}
        ]
    })

    setTimeout(() => {
        $("#monitorUploadModalFailed").dataTable({
            "columnDefs": [
                {"sortable": false, "targets": ['no-sort'], "pagingType": "full"}
            ],
            "ajax": {
                "url": "../../monitoring_upload_template/get_failed_data",
                "type": "POST"
            },
            "columns": [
                {"data": "no", "className": "text-center"},
                {"data": "no_rekening"},
                {"data": "no_perjanian"},
                {"data": "alasan_tolak"},
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
    }, 600);

    $("#modalFailedOnly").hover(function() {
        $(this).css('cursor','pointer');
    })

    $("#modalFailedOnly").fireModal({
        title: 'Data Gagal (ditolak / tidak lolos validasi)',
        body: $("#modal-login-part"),
	   size: 'modal-lg',
    })
        
    setInterval(() =>
    {
        loadTotalData()
        loadTotalDataOnProgress()
        loadTotalDataSuccessed()
        loadTotalDataFailed()
    }, 5000)
})

function loadTotalData()
{
    $.post('../../monitoring_upload_template/get_total_data',{})
    .done(function(total)
    {
		$('#totalDataUploaded').text(total)
    })
}

function loadTotalDataOnProgress()
{
    $.post('../../monitoring_upload_template/get_total_data_on_progress',{})
    .done(function(total)
    {
		$('#totalDataOnProgress').text(total)
    })
}

function loadTotalDataSuccessed()
{
    $.post('../../monitoring_upload_template/get_total_data_successed',{})
    .done(function(total)
    {
		$('#totalDataSuccessed').text(total)
    })
}

function loadTotalDataFailed()
{
    $.post('../../monitoring_upload_template/get_total_data_failed',{})
    .done(function(total)
    {
		$('#totalDataFailed').text(total)
    })
}
