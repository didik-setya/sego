<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $this->session->userdata('kost_name') ?></h1>
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jumlah Kamar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="jml_kamar">$40,000</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-house-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jumlah Penghuni</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="jml_penghuni">$40,000</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendapatan (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="jml_pendapatan">$40,000</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pengeluaran (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="jml_pengeluaran">$40,000</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
<script>
    $(document).ready(function() {

        let loading = '<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>';
        $('#jml_kamar').html(loading)
        $('#jml_penghuni').html(loading)
        $('#jml_pendapatan').html(loading)
        $('#jml_pengeluaran').html(loading)
        setTimeout(() => {
            get_data()
        }, 200);
    })

    function get_data() {
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()

        $.ajax({
            url: '<?= base_url('data_dashboard'); ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token)
                let pemasukan = new Intl.NumberFormat({
                    style: 'currency',
                    currency: 'IDR'
                }).format(d.jml_pemasukan)

                let pengeluaran = new Intl.NumberFormat({
                    style: 'currency',
                    currency: 'IDR'
                }).format(d.jml_pengeluaran)

                $('#jml_kamar').html(d.jml_kost)
                $('#jml_penghuni').html(d.jml_penghuni)
                $('#jml_pendapatan').html(pemasukan)
                $('#jml_pengeluaran').html(pengeluaran)
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    error_alert(error, xhr)
                }, 200);
            }
        })
    }

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