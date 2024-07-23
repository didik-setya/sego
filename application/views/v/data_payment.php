<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data Pembayaran <?= $penghuni->nama_penghuni ?></h4>
    <div class="row">
        <div class="col-12">

            <?php if ($penghuni->status == 1 || $penghuni->status == 2) { ?>
                <button class="btn btn-sm btn-danger" onclick="add_payment()"><i class="fa fa-plus"></i> Tambah Pembayaran</button>
            <?php } ?>

            <div class="card mt-3">
                <div class="card-body">

                    <table class="table table-sm table-bordered w-100" id="main_table">
                        <thead>
                            <tr class="bg-dark text-light">
                                <th>#</th>
                                <th>Periode</th>
                                <th>Tanggal bayar</th>
                                <th>Jumlah</th>
                                <th>Via Pembayaran</th>
                                <th>Ket</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($data as $d) { ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?php $periode = date_create($d->periode);
                                        echo date_format($periode, 'F Y') ?></td>
                                    <td><?= cek_tgl($d->tgl_bayar) ?></td>
                                    <td>Rp. <?= number_format($d->jml_bayar) ?></td>
                                    <td><?= $d->via_pembayaran ?></td>
                                    <td><?= $d->ket ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="delete_data('<?= $d->id ?>')">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                        <button class="btn btn-sm btn-primary" onclick="edit_data('<?= $d->id ?>', '<?= $d->periode ?>', '<?= $d->tgl_bayar ?>', '<?= $d->jml_bayar ?>', '<?= $d->via_pembayaran ?>', '<?= $d->ket ?>')">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= form_open('validation_payment', 'id="form_pembayaran"') ?>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="act" id="act">
            <input type="hidden" name="penghuni" id="penghuni" value="<?= $penghuni->id ?>">
            <div class="modal-body">

                <div class="form-group">
                    <label><b>Periode</b></label>
                    <input type="month" name="periode" id="periode" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><b>Tanggal Pembayaran</b></label>
                    <input type="date" name="tgl" id="tgl" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><b>Jumlah Pembayaran</b></label>
                    <input type="text" name="jumlah" id="jumlah" class="form-control" required>
                    <small class="text-danger" id="err_jumlah"></small>
                </div>

                <div class="form-group">
                    <label><b>Via Pembayaran</b></label>
                    <input type="text" name="via" id="via" class="form-control" required>
                    <small class="text-danger" id="err_via"></small>
                </div>

                <div class="form-group">
                    <label><b>Keterangan</b></label>
                    <textarea name="ket" id="ket" class="form-control"></textarea>
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

<script>
    $(document).ready(function() {
        $('#main_table').dataTable({
            ordering: false
        })
    })

    function add_payment() {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Tambah Pembayaran')

        $('#id').val('')
        $('#act').val('add')
        $('#periode').val('')
        $('#tgl').val('')
        $('#jumlah').val('')
        $('#via').val('')
        $('#ket').val('')

        $('#err_jumlah').html('')
        $('#err_via').html('')
    }

    function edit_data(id, periode, tgl, jml, via, ket) {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Edit Pembayaran')

        $('#id').val(id)
        $('#act').val('edit')
        $('#periode').val(periode)
        $('#tgl').val(tgl)
        $('#jumlah').val(jml)
        $('#via').val(via)
        $('#ket').val(ket)

        $('#err_jumlah').html('')
        $('#err_via').html('')
    }

    function delete_data(id) {
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
                process_delete(id)
            }
        });
    }

    function process_delete(id) {
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

    $('form').submit(function(e) {
        e.preventDefault();
        $('#jumlah').unmask();
        loading();

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

    $('#jumlah').on('keyup mouseup', () => {
        $('#jumlah').mask("#.##0", {
            reverse: true
        });
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