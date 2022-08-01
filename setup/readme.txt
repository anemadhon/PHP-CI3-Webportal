php.ini config for file upload
-max_execution_time=3600 (nilai lama: 30)
-max_input_time=7200 (nilai lama: 60)
-log_errors_max_len=1024 (tidak berubah)
-post_max_size=400M (nilai lama; 8M)
-upload_max_filesize=540M (nilai lama; 128M)
-max_input_vars = 5000 (nilai lama; 1000)
-max_file_uploads=20 (nilai lama; 20)

client_buffer_max_kb_size = '50240'
sqlsrv.ClientBufferMaxKBSize = 50240


CREATE TABLE t_management_role (
	id_role tinyint IDENTITY(1,1) PRIMARY KEY,
	nama_role varchar(20) not null,
	description varchar(100) not null,
	created_by varchar(20) not null,
	created_date datetime2(0) not null,
	modified_date datetime2(0) null,
	modified_by varchar(20) null,
	list_id_menu_accessbility varchar(50) not null
);

CREATE TABLE t_management_user (
	id_user int IDENTITY(1,1) PRIMARY KEY,
	nama_user varchar(100) not null,
	username varchar(20) not null unique,
	password varchar(60) not null,
	linkage_kode_cabang varchar(2) null,
	linkage_id_role tinyint not null,
	created_by varchar(20) not null,
	created_date datetime2(0) not null,
	created_from varchar(30) not null,
	modified_date datetime2(0) null,
	modified_by varchar(20) null,
	is_login tinyint not null,
	is_active tinyint not null,
	counter_wrong_pwd tinyint not null,
	counter_login int not null,
	from_ip_address varchar(30) null,
	last_login_date datetime2(0) null,
	flag_delete tinyint not null default 0,
	deleted_by varchar(20) null,
	deleted_ate datetime2(0) null,
	CONSTRAINT fk_role FOREIGN KEY (linkage_id_role)
	REFERENCES t_management_role (id_role)
);

CREATE TABLE t_prm_kantor_cbg (
	id_cabang tinyint IDENTITY(1,1) PRIMARY KEY,
	kode_cabang varchar(2) not null unique,
	nama_cabang varchar(100) not null,
	linkage_kanwil int not null,
	created_by varchar(20) not null,
	created_date datetime2(0) null,
	modified_date datetime2(0) null,
	modified_by varchar(20) not null
);

CREATE TABLE t_prm_bsns_ptnr (
	id_business_partner int IDENTITY(1,1) PRIMARY KEY,	
	nama_business_partner varchar(100) not null,
	jenis_business_partner tinyint not null,
	flag_delete tinyint not null default 0,
	created_by varchar(20) not null,
	created_date datetime2(0) not null,
	created_from varchar(30) not null,
	modified_date datetime2(0) null,
	modified_by varchar(20) null,
	deleted_by varchar(20) null,
	deleted_ate datetime2(0) null,
);

CREATE TABLE t_prm_pks_bsns_ptnr (
	id_pks_business_partner int IDENTITY(1,1) PRIMARY KEY,
	linkage_id_business_partner int not null,	
	no_pks_askrindo varchar(50) not null,
	no_pks_eksternal varchar(50) not null,
	kode_bank varchar(50) not null,
	kode_cabang_bank varchar(255) not null,
	kode_produk_eksternal varchar(400) not null,
	list_cabang varchar(100) not null,
	flag_delete tinyint not null default 0,
	created_by varchar(20) not null,
	created_date datetime2(0) not null,
	created_from varchar(30) not null,
	modified_date datetime2(0) null,
	modified_by varchar(20) null,
	deleted_by varchar(20) null,
	deleted_ate datetime2(0) null,
	kode_broker_agent varchar(50) null,
	CONSTRAINT fk_bsns_ptnr FOREIGN KEY (linkage_id_business_partner)
	REFERENCES t_prm_bsns_ptnr (id_business_partner)
);


CREATE TABLE t_file_upload (
	id_file int IDENTITY(1,1) PRIMARY KEY,	
	no_batch varchar(30) not null,
	nama_file varchar(50) not null,
	status tinyint not null,
	queue tinyint,
	upload_by varchar(20) not null,
	upload_date datetime2(0) default getdate() not null,
	upload_from varchar(30) not null
);

CREATE TABLE t_logging (
	id int IDENTITY(1,1) PRIMARY KEY,	
	type varchar(10) not null,
	message varchar(1500) not null,
	created_date datetime2(0) default GETDATE() not null,
	created_from varchar(30)
);

CREATE TABLE t_temp_akseptasi_produksi (
	id int IDENTITY(1,1) PRIMARY KEY,
	id_file int not null,
	kode_bank varchar(50) not null,
	kode_cabang_bank varchar(5) not null,
	kode_cabang_askrindo varchar(5) not null,
	kode_produksi varchar(50) not null,
	no_rek_pinjaman varchar(50) not null,
	no_perjanjian_kredit varchar(50) not null,
	tgl_awal_pk varchar(10) not null,
	tgl_akhir_pk varchar(10) not null,
	jk_waktu_kredit varchar(5) not null,
	id_valuta varchar(2) not null,
	kurs_valuta varchar(5),
	plafond_kredit varchar(20) not null,
	suku_bunga_kredit varchar(5) not null,
	jenis_kredit varchar(50),
	sub_jenis_kredit varchar(50),
	type_tujuan_kredit varchar(2) not null,
	kolektibilitas_kredit varchar(2) not null,
	sektor_ekonomi varchar(50) not null,
	sumber_pelunasan_kredit varchar(20) not null,
	sumber_dana_kredit varchar(2) not null,
	mekanisme_penyaluran varchar(2),
	cif_customer varchar(50),
	nama_debitur varchar(50) not null,
	no_ktp_debitur varchar(30) not null,
	tmpt_lahir varchar(30),
	tgl_lahir varchar(10),
	jenis_kelamin varchar(10),
	alamat_debitur varchar(255) not null,
	kode_pos varchar(10) not null,
	jenis_pekerjaan varchar(5) not null,
	status_pegawai varchar(2) not null,
	no_tlp varchar(30),
	no_hp varchar(30),
	npwp varchar(30) not null,
	jenis_agunan varchar(2) not null,
	jenis_pengikatan varchar(2),
	nilai_agunan varchar(20) not null,
	tgl_kirim varchar(10),
	lain_1 varchar(100),
	lain_2 varchar(100),
	broker_agent varchar(2),
	kode_broker_agent varchar(30),
	nama_broker_agent varchar(100),
	nilai_tanggungan varchar(20),
	rate_premi varchar(15),
	nilai_premi varchar(20),
	tgl_awal_tanggungan varchar(10),
	tgl_akhir_tanggungan varchar(10),
	jk_waktu_tanggungan varchar(5),
	no_surat_pengantar varchar(50),
	tgl_no_surat_pengantar varchar(10),
	flag_status_validasi_web tinyint not null,
	no_polis_acs varchar(600),	
	flag_status_terbit tinyint,
	flag_status_terbit_inquiry tinyint,
	counter_inquiry_polis tinyint,
	keterangan varchar(255),
	upload_from varchar(30) not null,
	upload_date datetime2(0) default getdate() not null,
	max_counter_inquiry tinyint not null default 3
	CONSTRAINT fk_file_upload FOREIGN KEY (id_file)
	REFERENCES t_file_upload (id_file)
);

CREATE TABLE t_request_api_acs (
	id_request int IDENTITY(1,1) PRIMARY KEY,
	id int not null,
	id_file int not null,
	kode_bank varchar(20) not null,
	kode_cabang_bank varchar(10) not null,
	kode_cabang_askrindo varchar(2) not null,
	kode_produksi varchar(50) not null,
	no_rek_pinjaman varchar(50) not null,
	no_perjanjian_kredit varchar(50) not null,
	tgl_awal_pk varchar(10) not null,
	tgl_akhir_pk varchar(10) not null,
	jk_waktu_kredit varchar(5) not null,
	id_valuta varchar(2) not null,
	kurs_valuta varchar(5),
	plafond_kredit varchar(20) not null,
	suku_bunga_kredit varchar(5) not null,
	jenis_kredit varchar(50),
	sub_jenis_kredit varchar(50),
	type_tujuan_kredit varchar(2) not null,
	kolektibilitas_kredit varchar(2) not null,
	sektor_ekonomi varchar(50) not null,
	sumber_pelunasan_kredit varchar(20) not null,
	sumber_dana_kredit varchar(2) not null,
	mekanisme_penyaluran varchar(2),
	cif_customer varchar(50),
	nama_debitur varchar(30) not null,
	no_ktp_debitur varchar(30) not null,
	tmpt_lahir varchar(30),
	tgl_lahir varchar(10),
	jenis_kelamin varchar(10),
	alamat_debitur varchar(255) not null,
	kode_pos varchar(10) not null,
	jenis_pekerjaan varchar(5) not null,
	status_pegawai varchar(2) not null,
	no_tlp varchar(30),
	no_hp varchar(30),
	npwp varchar(30) not null,
	jenis_agunan varchar(2) not null,
	jenis_pengikatan varchar(2),
	nilai_agunan varchar(20) not null,
	tgl_kirim varchar(10),
	lain_1 varchar(100),
	lain_2 varchar(100),
	broker_agent varchar(2),
	kode_broker_agent varchar(30),
	nama_broker_agent varchar(100),
	nilai_tanggungan varchar(20),
	rate_premi varchar(15),
	nilai_premi varchar(20),
	tgl_awal_tanggungan varchar(10),
	tgl_akhir_tanggungan varchar(10),
	jk_waktu_tanggungan varchar(5),
	requested_date datetime2(0) default getdate() not null,
	CONSTRAINT fk_request_acs_1 FOREIGN KEY (id_file)
	REFERENCES t_file_upload (id_file),
	CONSTRAINT fk_request_acs_2 FOREIGN KEY (id)
	REFERENCES t_temp_akseptasi_produksi (id)
);

CREATE TABLE t_request_api_inquiry (
	id_request int IDENTITY(1,1) PRIMARY KEY,
	id int not null,		
	id_file int not null,	
	no_polis_acs varchar(50) not null,
	currency varchar(5) DEFAULT "IDR" not null,
	requested_date datetime2(0) default getdate() not null,
	CONSTRAINT fk_request_inquiry_1 FOREIGN KEY (id_file)
	REFERENCES t_file_upload (id_file),
	CONSTRAINT fk_request_inquiry_2 FOREIGN KEY (id)
	REFERENCES t_temp_akseptasi_produksi (id)
);

CREATE TABLE t_response_api_acs (
	id_request int IDENTITY(1,1) PRIMARY KEY,
	id int not null,	
	id_file int not null,	
	status_code varchar(5),
	created_by varchar(50),
	created_date varchar(50),
	modified_by varchar(50),
	modified_date varchar(50),
	status varchar(50),
	success varchar(50),
	message varchar(255),
	kode_response varchar(5),
	request_date varchar(50),
	username varchar(50),
	password varchar(100),
	request_id varchar(50),
	request_id_original varchar(50),
	flag_rehit varchar(50),
	no_rekening varchar(50),
	no_sertifikat varchar(50),
	tanggal_sertifikat varchar(50),
	no_urut_lampiran varchar(50),
	tanggal_rekam varchar(50),
	keterangan_response varchar(255),
	addtional_data varchar(50),
	timestamp varchar(50),
	error varchar(255),
	path varchar(255),
	responsed_date datetime2(0) default getdate() not null,
	CONSTRAINT fk_response_acs_1 FOREIGN KEY (id_file)
	REFERENCES t_file_upload (id_file),
	CONSTRAINT fk_response_acs_2 FOREIGN KEY (id)
	REFERENCES t_temp_akseptasi_produksi (id)
);

CREATE TABLE t_response_api_inquiry (
	id_request int IDENTITY(1,1) PRIMARY KEY,
	id int not null,
	id_file int not null,	
	status_code varchar(5),
	trx_date_response varchar(20),
	data varchar(2),
	data_jangka_waktu varchar(30),
	data_nama varchar(255),
	data_produk varchar(255),
	data_status_terbit varchar(2),
	data_premiums varchar(2),
	data_premiums_currency varchar(5),
	data_premiums_amount varchar(20),
	data_nomor_polis varchar(50),
	data_status varchar(30),
	message varchar(255),
	transaction_id varchar(50),
	error_number varchar(10),
	status varchar(5),
	responsed_date datetime2(0) default getdate() not null,
	CONSTRAINT fk_response_inquiry_1 FOREIGN KEY (id_file)
	REFERENCES t_file_upload (id_file),
	CONSTRAINT fk_response_inquiry_2 FOREIGN KEY (id)
	REFERENCES t_temp_akseptasi_produksi (id)
);

email untuk recaptcha : 
askrindo.webportal@gmail.com (w3bp0rt@l)
no tlp : madhon mii
tgl lahir : 06-05-2000