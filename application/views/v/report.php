<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Laporan</h4>
    <div class="row">
        <div class="col-6 col-md-3">
            <div class="form-group">
                <input type="date" name="periode_a" id="periode_a" class="form-control">
                <small>Dari Tanggal</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="form-group">
                <input type="date" name="periode_b" id="periode_b" class="form-control">
                <small>Sampai Tanggal</small>
            </div>
        </div>
        <div class="col-md-2"></div>

        <div class="col-md-4 text-center my-2">
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
        // let periode = $('#periode').val()
        let periode_a = $('#periode_a').val()
        let periode_b = $('#periode_b').val()

        if (periode_a && periode_b) {

            let token_name = '<?= $this->security->get_csrf_token_name() ?>'
            let token = $('input[name=' + token_name + ']').val()

            loading()

            $.ajax({
                url: '<?= base_url('get_rekap_report') ?>',
                data: {
                    periode_a: periode_a,
                    periode_b: periode_b,
                    '<?= $this->security->get_csrf_token_name() ?>': token,
                },
                type: 'POST',
                dataType: 'JSON',
                success: function(d) {
                    regenerate_token(d.token);
                    setTimeout(() => {
                        Swal.close();
                        $('#show_report').html(d.html)
                    }, 200);
                },
                error: function(xhr, status, error) {
                    setTimeout(() => {
                        Swal.close()
                        error_alert(error, xhr)
                    }, 200);
                }
            })
        } else {
            error_alert('Harap pilih periode')
        }


    })

    $('#export_data').click(() => {
        let periode_a = $('#periode_a').val()
        let periode_b = $('#periode_b').val()


        if (periode_a && periode_b) {
            window.open('<?= base_url('excel/index?date_a=') ?>' + periode_a + '&date_b=' + periode_b);
        } else {
            error_alert('Harap pilih periode')
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