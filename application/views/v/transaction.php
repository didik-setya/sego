<?php
date_default_timezone_set('Asia/Jakarta');
$show_date = date('Y-m');
$get_periode = $this->input->get('periode');
$role = $this->session->userdata('role');

if ($get_periode) {
    $periode = $get_periode;
} else {
    $periode = $show_date;
}
$create_date = date_create($periode);
$month_periode = date_format($create_date, 'm');
$year_periode = date_format($create_date, 'Y');

$kost_id = $this->session->userdata('kost_id');
$data_pengeluaran = $this->db->order_by('tanggal', 'DESC')->get_where('pengeluaran', [
    'id_kost' => $kost_id,
    'month(tanggal)' => $month_periode,
    'year(tanggal)' => $year_periode
])->result();

$data_setoran = $this->db->order_by('tanggal', 'DESC')->get_where('setoran', [
    'id_kost' => $kost_id,
    'month(tanggal)' => $month_periode,
    'year(tanggal)' => $year_periode
])->result()

?>

<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data Transaksi</h4>


    <div class="card mb-3">
        <div class="card-body row">
            <div class="col-md-3">
                <input type="month" name="periode" id="periode_filter" class="form-control" value="<?= $periode ?>">
                <small>Periode</small>
            </div>

            <div class="col-md-7"></div>
            <div class="col-md-2 text-center">
                <button class="btn btn-sm btn-dark" onclick="filter_data()"><i class="fa fa-filter"></i> Filter</button>
            </div>
        </div>
    </div>



    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-toggle="tab" data-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Pemasukan</button>
            <button class="nav-link" id="nav-profile-tab" data-toggle="tab" data-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Pengeluaran</button>
            <button class="nav-link" id="nav-contact-tab" data-toggle="tab" data-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Setoran</button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

            <!-- Pemasukan -->

            <div class="row">
                <div class="col-12 table-responsive">
                    <?php if ($role == 'admin') { ?>
                        <button class="btn btn-sm btn-primary my-3" onclick="add_pembayaran()"><i class="fa fa-plus"></i> Tambah Pembayaran</button>
                    <?php } ?>

                    <table class="table table-bordered table-sm w-100" id="tbl_pemasukan">
                        <thead>
                            <tr class="bg-dark text-light">
                                <th>#</th>
                                <th>Nama</th>
                                <th>Kamar</th>
                                <th>Tgl Seharusnya Bayar</th>
                                <th>Tgl Bayar</th>
                                <th>Harga Kamar</th>
                                <th>Bayar</th>
                                <th>Via</th>
                                <th>Ket</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $data_pay = $this->app->get_data_pemasukan($periode)->result();
                            $i = 1;
                            $id_pay = [];
                            $t_est_pay = 0;
                            $t_real_pay = 0;

                            foreach ($data_pay as $d) {
                                $row = $d->id_penghuni;
                                $id_pay[] = $row;
                                if ($d->tgl_penempatan == null || $d->tgl_penempatan == '' || $d->tgl_penempatan == '0000-00-00') {
                                    $tgl_pembayaran = '-';
                                } else {
                                    $tgl = date_create($d->tgl_penempatan);
                                    $tgl_pembayaran = date_format($tgl, 'd');
                                }

                                $tgl_bayar = cek_tgl($d->tgl_bayar);

                                $t_est_pay += $d->price;
                                $t_real_pay += $d->jml_bayar;
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $d->nama_penghuni ?></td>
                                    <td><?= $d->no_kamar ?> ( km <?= $d->km ?>)</td>
                                    <td><?= $tgl_pembayaran ?></td>
                                    <td><?= $tgl_bayar ?></td>
                                    <td><?= number_format($d->price); ?></td>
                                    <td><?= number_format($d->jml_bayar) ?></td>
                                    <td><?= $d->via_pembayaran ?></td>
                                    <td><?= $d->ket ?></td>
                                    <td>
                                        <?php if ($role == 'admin') { ?>
                                            <button class="btn btn-sm btn-success" onclick="edit_pembayaran('<?= $d->id_pembayaran ?>')">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <button class="btn btn-sm btn-danger" onclick="delete_pembayaran('<?= $d->id_pembayaran ?>')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }
                            $data_not_pay = $this->app->get_penghuni_not_pay($id_pay);

                            foreach ($data_not_pay as $d) {
                                if ($d->tgl_penempatan == null || $d->tgl_penempatan == '' || $d->tgl_penempatan == '0000-00-00') {
                                    $tgl_pembayaran = '-';
                                } else {
                                    $tgl = date_create($d->tgl_penempatan);
                                    $tgl_pembayaran = date_format($tgl, 'd');
                                }
                                $t_est_pay += $d->price;
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $d->nama_penghuni ?></td>
                                    <td><?= $d->no_kamar ?> ( km <?= $d->km ?>)</td>
                                    <td><?= $tgl_pembayaran ?></td>
                                    <td>-</td>
                                    <td><?= number_format($d->price); ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            <?php }
                            $selisih_pay = $t_est_pay - $t_real_pay;
                            ?>
                        </tbody>
                        <tbody>
                            <tr class="bg-success text-white">
                                <th colspan="5">Total</th>
                                <th><?= number_format($t_est_pay) ?></th>
                                <th><?= number_format($t_real_pay) ?></th>
                                <th colspan="3"></th>
                            </tr>
                            <tr class="bg-success text-white">
                                <th colspan="5">Selisih</th>
                                <th colspan="2"><?= number_format($selisih_pay) ?></th>
                                <th colspan="3"></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- end pemasukan -->

        </div>
        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="row">
                <div class="col-12">
                    <?php if ($role == 'admin') { ?>
                        <button class="btn btn-sm btn-primary my-3" onclick="add_pengeluaran()"><i class="fa fa-plus"></i> Tambah Pengeluaran</button>
                    <?php } ?>

                    <table class="table table-bordered table-sm" id="table_pengeluaran">
                        <thead>
                            <tr class="bg-dark text-light">
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Biaya</th>
                                <th>Nominal</th>
                                <th>Ket</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $total_pengeluaran = 0;
                            foreach ($data_pengeluaran as $dp) {
                                $total_pengeluaran += $dp->nominal;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= cek_tgl($dp->tanggal) ?></td>
                                    <td><?= $dp->biaya ?></td>
                                    <td><?= number_format($dp->nominal) ?></td>
                                    <td><?= $dp->ket ?></td>
                                    <td>
                                        <?php if ($role == 'admin') { ?>
                                            <button class="btn btn-sm btn-success" onclick="edit_pengeluaran('<?= $dp->id ?>')">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <button class="btn btn-sm btn-danger" onclick="delete_pengeluaran('<?= $dp->id ?>')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-success text-white">
                                <th colspan="3">Total</th>
                                <th colspan="3"><?= number_format($total_pengeluaran) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">

            <?php if ($role == 'admin') { ?>
                <button class="btn btn-sm btn-primary my-3" onclick="add_setoran()"><i class="fa fa-plus"></i> Tambah Setoran</button>
            <?php } ?>

            <table class="table table-bordered table-sm" id="table_setoran">
                <thead>
                    <tr class="bg-dark text-light">
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nominal</th>
                        <th>Ket</th>
                        <th><i class="fa fa-cogs"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $n = 1;
                    $total_setoran = 0;
                    foreach ($data_setoran as $ds) {
                        $total_setoran += $ds->nominal;
                    ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= cek_tgl($ds->tanggal) ?></td>
                            <td><?= number_format($ds->nominal) ?></td>
                            <td><?= $ds->ket ?></td>
                            <td>
                                <?php if ($role == 'admin') { ?>
                                    <button class="btn btn-sm btn-success" onclick="edit_setoran('<?= $ds->id ?>')">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <button class="btn btn-sm btn-danger" onclick="delete_setoran('<?= $ds->id ?>')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr class="bg-success text-white">
                        <th colspan="2">Total</th>
                        <th colspan="3"><?= number_format($total_setoran) ?></th>
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>

</div>



<!-- Modal pembayaran -->
<div class="modal" id="modalPembayaran" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= form_open('validation_payment', 'id="frm_pembayaran"') ?>
            <input type="hidden" name="id" id="id_pembayaran">
            <input type="hidden" name="act" id="act_pembayaran">
            <div class="modal-body row">

                <div class="form-group col-12">
                    <label><b>Penghuni</b></label>
                    <br>
                    <select name="penghuni_kost" id="penghuni_kost" style="width: 100%;" required>
                        <option value="">--pilih--</option>
                        <?php foreach ($penghuni as $p) { ?>
                            <option value="<?= $p->id ?>" data-kost="<?= $p->no_kamar ?>" data-price="<?= $p->price ?>"><?= $p->nama_penghuni ?></option>
                        <?php } ?>
                    </select>

                    <input type="text" name="penghuni_name" id="penghuni_name" disabled class="form-control">
                    <input type="hidden" name="id_penghuni" id="id_penghuni">
                </div>

                <div class="form-group col-md-6">
                    <label><b>Kamar</b></label>
                    <input type="text" name="km" id="km_penghuni" class="form-control" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label><b>Harga Kamar</b></label>
                    <input type="text" name="price" id="price_penghuni" class="form-control" readonly>
                </div>

                <div class="form-group col-md-6">
                    <label><b>Periode</b></label>
                    <input type="month" name="periode" id="periode_pembayaran" class="form-control" required>
                </div>

                <div class="form-group col-md-6">
                    <label><b>Tanggal Pembayaran</b></label>
                    <input type="date" name="tgl" id="tgl_pembayaran" class="form-control" required>
                </div>

                <div class="form-group col-md-6">
                    <label><b>Jumlah Pembayaran</b></label>
                    <input type="text" name="jumlah" id="jumlah_pembayaran" class="form-control" required>
                    <small class="text-danger" id="err_jumlah"></small>
                </div>

                <div class="form-group col-md-6">
                    <label><b>Via Pembayaran</b></label>
                    <input type="text" name="via" id="via_pembayaran" class="form-control" required>
                    <small class="text-danger" id="err_via"></small>
                </div>

                <div class="form-group col-md-12">
                    <label><b>Keterangan</b></label>
                    <textarea name="ket" id="ket_pembayaran" class="form-control"></textarea>
                </div>



            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>

        </div>
    </div>
</div>
<!-- end modal pembayaran -->

<!-- modal pengeluaran -->
<div class="modal" id="modalPengeluaran" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-white" aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= form_open('validation_pengeluaran', 'id="form-pengeluaran"') ?>
            <input type="hidden" name="id" id="id_pengeluaran">
            <input type="hidden" name="act" id="act_pengeluaran">
            <div class="modal-body">
                <div class="form-group">
                    <label><b>Tanggal</b></label>
                    <input type="date" name="date" id="date_pengeluaran" required class="form-control">
                </div>

                <div class="form-group">
                    <label><b>Biaya</b></label>
                    <input type="text" name="biaya" id="biaya_pengeluaran" required class="form-control">
                    <small class="text-danger" id="err_biaya"></small>
                </div>

                <div class="form-group">
                    <label><b>Nominal</b></label>
                    <input type="text" name="nominal" id="nominal_pengeluaran" required class="form-control">
                    <small class="text-danger" id="err_nominal"></small>
                </div>

                <div class="form-group">
                    <label><b>Ket</b></label>
                    <textarea name="ket" id="ket_pengeluaran" class="form-control"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- end modal pengeluaran -->

<!-- modal setor -->
<div class="modal" id="modalSetor" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>


            <?= form_open('validation_setoran', 'id="form_setor"') ?>
            <input type="hidden" name="id" id="id_setor">
            <input type="hidden" name="act" id="act_setor">
            <div class="modal-body">


                <div class="form-group">
                    <label><b>Tanggal</b></label>
                    <input type="date" name="date" id="date_setor" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><b>Nominal</b></label>
                    <input type="text" name="nominal" id="nominal_setor" required class="form-control">
                    <small class="text-danger" id="err_nominal"></small>
                </div>

                <div class="form-group">
                    <label><b>Ket</b></label>
                    <textarea name="ket" id="ket_setor" class="form-control"></textarea>
                    <small class="text-danger" id="err_ket"></small>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- end modal setor -->


<script>
    $(document).ready(function() {

        $('#penghuni_kost').select2({
            dropdownParent: $('#modalPembayaran')
        })

        $('#jumlah_pembayaran, #nominal_pengeluaran, #nominal_setor').on('keyup mouseup', () => {
            $('#jumlah_pembayaran, #nominal_pengeluaran, #nominal_setor').mask("#.##0", {
                reverse: true
            })
        })

        $('#tbl_pemasukan, #table_pengeluaran, #table_setoran').DataTable()
    })

    function filter_data() {
        let date = $('#periode_filter').val()
        if (!date) {
            error_alert('Harap pilih periode')
        } else {
            loading()
            let url = '<?= base_url('transaction') ?>?periode=' +
                date;
            window.location.href = url;
        }
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



    //pembayaran

    $('#penghuni_kost').change(function() {
        var kost = $(this).find('option:selected').attr('data-kost');
        var harga = $(this).find('option:selected').attr('data-price');

        $('#km_penghuni').val(kost);
        $('#price_penghuni').val(harga)

        $('#price_penghuni').mask("#.##0", {
            reverse: true
        })
    })

    $('#frm_pembayaran').submit(function(e) {
        e.preventDefault();
        $('#jumlah_pembayaran').unmask();
        loading()


        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token);

                setTimeout(() => {
                    Swal.close();

                    if (d.type == 'validation') {

                        if (d.err_jumlah == '') {
                            $('#err_jumlah').html('')
                        } else {
                            $('#err_jumlah').html(d.err_jumlah)
                        }
                        if (d.err_via == '') {
                            $('#err_via').html('')
                        } else {
                            $('#err_via').html(d.err_via)
                        }

                    } else if (d.type == 'result') {
                        $('#err_jumlah').html('')
                        $('#err_via').html('')
                        if (d.status == false) {
                            error_alert(d.msg)
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: d.msg
                            }).then((res) => {
                                $('#staticBackdrop').modal('hide')
                                window.location.reload();
                            })
                        }
                    }

                }, 200);

            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close();
                    error_alert(error, xhr);
                }, 200);
            }
        })


    })

    function add_pembayaran() {
        $('#modalPembayaran').modal('show')
        $('#modalPembayaran').find('.modal-title').html('Tambah Pembayaran')

        $('#id_pembayaran').val('')
        $('#act_pembayaran').val('add')

        $('#penghuni_kost').val('')
        $('#km_penghuni').val('')
        $('#price_penghuni').val('')

        $('#periode_pembayaran').val('')
        $('#tgl_pembayaran').val('')
        $('#jumlah_pembayaran').val('')
        $('#via_pembayaran').val('')
        $('#ket_pembayaran').val('')

        $('#err_jumlah').html('')
        $('#err_via').html('')

        $('.select2-container').css('display', 'block')
        $('#penghuni_name').addClass('d-none')
        $('#penghuni_name').val('')
        $('#id_penghuni').val('')
    }

    function edit_pembayaran(id) {
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()

        $('#modalPembayaran').find('.modal-title').html('Edit Pembayaran')

        $('#id_pembayaran').val(id)
        $('#act_pembayaran').val('edit')

        $('.select2-container').css('display', 'none')
        $('#penghuni_name').removeClass('d-none')


        $('#err_jumlah').html('')
        $('#err_via').html('')

        loading()
        $.ajax({
            url: '<?= base_url('load_data_transaksi') ?>',
            data: {
                "<?= $this->security->get_csrf_token_name() ?>": token,
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                let data = d.data;
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close()

                    $('#modalPembayaran').modal('show')

                    $('#penghuni_name').val(data.nama_penghuni)
                    $('#id_penghuni').val(data.id_penghuni)
                    $('#km_penghuni').val(data.no_kamar)
                    $('#price_penghuni').val(data.price)

                    $('#periode_pembayaran').val(data.periode)
                    $('#tgl_pembayaran').val(data.tgl_bayar)
                    $('#jumlah_pembayaran').val(data.jml_bayar)
                    $('#via_pembayaran').val(data.via_pembayaran)
                    $('#ket_pembayaran').val(data.ket)

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

    function delete_pembayaran(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Untuk menghapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                process_delete_pembayaran(id)
            }
        });
    }

    function process_delete_pembayaran(id) {
        loading()
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()
        $.ajax({
            url: '<?= base_url('delete_payment') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close()
                    if (d.status == false) {
                        error_alert(d.msg)
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: d.msg
                        }).then((res) => {
                            window.location.reload();
                        })
                    }
                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close();
                    error_alert(error, xhr)
                }, 200);
            }
        })
    }
    //end pembayaran


    //pengeluaran
    $('#form-pengeluaran').submit(function(e) {
        e.preventDefault()
        loading()
        $('#nominal_pengeluaran').unmask()

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token)
                setTimeout(() => {
                    Swal.close()

                    if (d.type == 'validation') {
                        if (d.err_biaya == '') {
                            $('#err_biaya').html('');
                        } else {
                            $('#err_biaya').html(d.err_biaya);
                        }

                        if (d.err_nominal == '') {
                            $('#err_nominal').html('');
                        } else {
                            $('#err_nominal').html(d.err_nominal);
                        }
                    } else if (d.type == 'result') {
                        $('#err_nominal').html('');
                        $('#err_biaya').html('');

                        if (d.status == false) {
                            error_alert(d.msg)
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: d.msg
                            }).then((res) => {
                                $('#modalPengeluaran').modal('hide')
                                window.location.reload()
                            })
                        }

                    }

                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close()
                    error_alert(error, xhr)
                }, 200);
            }
        })
    })

    function add_pengeluaran() {
        $('#modalPengeluaran').modal('show')
        $('#modalPengeluaran').find('.modal-title').html('Tambah Pengeluaran')

        $('#id_pengeluaran').val('')
        $('#act_pengeluaran').val('add')
        $('#date_pengeluaran').val('')
        $('#biaya_pengeluaran').val('')
        $('#nominal_pengeluaran').val('')
        $('#ket_pengeluaran').val('')

        $('#err_biaya').html('')
        $('#err_nominal').html('')
    }

    function edit_pengeluaran(id) {
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()
        loading()
        $.ajax({
            url: '<?= base_url('get_data_pengeluaran') ?>',
            data: {
                id: id,
                '<?= $this->security->get_csrf_token_name() ?>': token
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                let data = d.data;
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close()

                    $('#modalPengeluaran').modal('show')
                    $('#modalPengeluaran').find('.modal-title').html('Edit Pengeluaran')

                    $('#id_pengeluaran').val(id)
                    $('#act_pengeluaran').val('edit')
                    $('#date_pengeluaran').val(data.tanggal)
                    $('#biaya_pengeluaran').val(data.biaya)
                    $('#nominal_pengeluaran').val(data.nominal)
                    $('#ket_pengeluaran').val(data.ket)

                    $('#err_biaya').html('')
                    $('#err_nominal').html('')

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

    function delete_pengeluaran(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Untuk menghapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                process_delete_pengeluaran(id)
            }
        });
    }

    function process_delete_pengeluaran(id) {
        loading()
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()

        $.ajax({
            url: '<?= base_url('delete_pengeluaran') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close()
                    if (d.status == false) {
                        error_alert(d.msg)
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: d.msg
                        }).then((res) => {
                            window.location.reload();
                        })
                    }
                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close();
                    error_alert(error, xhr)
                }, 200);
            }
        })
    }
    //end pengeluaran



    //setoran
    $('#form_setor').submit(function(e) {
        e.preventDefault()
        $('#nominal_setor').unmask()
        loading()

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close();

                    if (d.type == 'validation') {
                        if (d.err_nominal == '') {
                            $('#err_nominal').html('')
                        } else {
                            $('#err_nominal').html(d.err_nominal)
                        }

                        if (d.err_ket == '') {
                            $('#err_ket').html('')
                        } else {
                            $('#err_ket').html(d.err_ket)
                        }
                    } else if (d.type == 'result') {
                        $('#err_ket').html('')
                        $('#err_nominal').html('')

                        if (d.status == false) {
                            error_alert(d.msg)
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: d.msg
                            }).then((res) => {
                                $('#modalSetor').modal('hide');
                                window.location.reload()
                            })
                        }

                    }

                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close()
                    error_alert(error, xhr)
                }, 200);
            }
        })
    })

    function add_setoran() {
        $('#modalSetor').modal('show');
        $('#modalSetor').find('.modal-title').html('Tambah Setoran')

        $('#id_setor').val('')
        $('#act_setor').val('add')
        $('#date_setor').val('')
        $('#nominal_setor').val('')
        $('#ket_setor').val('')

        $('#err_nominal').html('')
        $('#err_ket').html('')
    }

    function edit_setoran(id) {
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()
        loading();
        $.ajax({
            url: '<?= base_url('get_data_setoran') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                let data = d.data;
                regenerate_token(d.token);

                setTimeout(() => {
                    Swal.close()
                    $('#modalSetor').modal('show');
                    $('#modalSetor').find('.modal-title').html('Edit Setoran')

                    $('#id_setor').val(id)
                    $('#act_setor').val('edit')
                    $('#date_setor').val(data.tanggal)
                    $('#nominal_setor').val(data.nominal)
                    $('#ket_setor').val(data.ket)

                    $('#err_nominal').html('')
                    $('#err_ket').html('')
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

    function delete_setoran(id) {
        Swal.fire({
            title: "Apakah anda yakin?",
            text: "Untuk menghapus data ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                process_delete_setoran(id)
            }
        });
    }

    function process_delete_setoran(id) {
        loading()
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()
        $.ajax({
            url: '<?= base_url('delete_setor') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close()
                    if (d.status == false) {
                        error_alert(d.msg)
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: d.msg
                        }).then((res) => {
                            window.location.reload();
                        })
                    }
                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close();
                    error_alert(error, xhr)
                }, 200);
            }
        })
    }
    //end setoran
</script>