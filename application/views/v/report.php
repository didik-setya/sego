<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Laporan</h4>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <input type="month" name="periode" id="periode" class="form-control">
                <small>Pilih Periode</small>
            </div>
        </div>
        <div class="col-md-5"></div>
        <div class="col-md-4 text-center">
            <button class="btn btn-sm btn-primary" id="filter_data"><i class="fas fa-filter"></i> Filter</button>
            <button class="btn btn-sm btn-success" id="export_data"><i class="far fa-file-excel"></i> Export</button>
        </div>


        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive" id="show_report">

                    <div class="w-100 bg-warning text-white text-center py-1">
                        <h4>Harap pilih periode</h4>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
<script>
    $('#filter_data').click(() => {
        let periode = $('#periode').val()

        if (periode == '') {
            error_alert('Harap pilih periode')
        } else {
            let token_name = '<?= $this->security->get_csrf_token_name() ?>'
            let token = $('input[name=' + token_name + ']').val()

            loading()

            $.ajax({
                url: '<?= base_url('get_rekap_report') ?>',
                data: {
                    periode: periode,
                    '<?= $this->security->get_csrf_token_name() ?>': token,
                },
                type: 'POST',
                dataType: 'JSON',
                success: function(d) {
                    regenerate_token(d.token);
                    setTimeout(() => {
                        Swal.close();
                        let pembayaran = d.pembayaran;
                        let pengeluaran = d.pengeluaran;
                        let setoran = d.setoran;

                        let body_pembayaran = '';
                        let body_pengeluaran = '';
                        let body_setoran = '';

                        let i;
                        let a;

                        let no = 1;
                        for (i = 0; i < pembayaran.length; i++) {
                            body_pembayaran += '<tr ' + pembayaran[i].class + '> <td>' + no++ + '</td> <td>' + pembayaran[i].tgl_seharusnya_bayar + '</td> <td>' + pembayaran[i].tgl_bayar + '</td> <td>' + pembayaran[i].no_kamar + '</td> <td>' + pembayaran[i].nama + '</td> <td>' + pembayaran[i].harga_kamar + '</td> <td>' + pembayaran[i].bayar + '</td> <td>' + pembayaran[i].via + '</td> <td>' + pembayaran[i].ket + '</td> </tr>'
                        }

                        let n = 1;
                        for (a = 0; a < pengeluaran.length; a++) {
                            body_pengeluaran += '<tr> <td>' + n++ + '</td> <td>' + pengeluaran[a].tanggal + '</td> <td>' + pengeluaran[a].biaya + '</td> <td>' + pengeluaran[a].nominal + '</td> <td>' + pengeluaran[a].ket + '</td> </tr>';
                        }

                        let q = 1;
                        for (i = 0; i < setoran.length; i++) {
                            body_setoran += '<tr> <td>' + q++ + '</td> <td>' + setoran[i].tanggal + '</td> <td>' + setoran[i].ket + '</td> <td>' + setoran[i].nominal + '</td> </tr>'
                        }

                        let table_pembayaran = '<table class="table table-bordered table-sm my-2"><thead><tr class="bg-primary text-light"><th colspan="9">Pendapatan</th></tr><tr class="bg-dark text-light"><th>#</th><th>Tgl Seharusnya Bayar</th><th>Tgl Bayar</th><th>No. Kamar</th><th>Nama</th><th>Harga Kamar</th><th>Bayar</th><th>Via</th><th>Ket</th></tr></thead><tbody>' + body_pembayaran + '</tbody></table>';

                        let table_pengeluaran = '<table class="table table-bordered table-sm my-2"><thead><tr class="bg-primary text-light"><th colspan="9">Pengeluaran</th></tr><tr class="bg-dark text-light"><th>#</th><th>Tanggal</th><th>Biaya</th><th>Nominal</th><th>Ket</th></tr></thead><tbody>' + body_pengeluaran + '</tbody></table>';

                        let table_setoran = ' <table class="table table-bordered table-sm my-2"><thead><tr class="bg-primary text-light"><th colspan="9">Setoran</th></tr><tr class="bg-dark text-light"><th>#</th><th>Tanggal</th><th>Ket</th><th>Nominal</th></tr></thead><tbody>' + body_setoran + '</tbody></table>';


                        let main_html = table_pembayaran + table_pengeluaran + table_setoran;
                        $('#show_report').html(main_html)


                    }, 200);
                },
                error: function(xhr, status, error) {
                    setTimeout(() => {
                        Swal.close()
                        error_alert(error, xhr)
                    }, 200);
                }
            })

        }


    })










    function regenerate_token(token) {
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        $('input[name=' + token_name + ']').val(token)
    }

    function error_alert(msg, xhr) {

        if (xhr == null) {
            Swal.fire({
                title: "Error",
                text: msg,
                icon: "error"
            });
        } else {
            Swal.fire({
                title: "Error",
                text: msg,
                icon: "error"
            }).then((res) => {
                window.location.reload();
            });
        }

    }

    function loading() {
        Swal.fire({
            title: "Loading",
            html: "Please wait...",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
    }
</script>