<?php
$role = $this->session->userdata('role');
?>
<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data Pengeluaran</h4>

    <div class="row">
        <div class="col-12">

            <?php if ($role == 'admin') { ?>
                <button class="btn btn-sm btn-primary" onclick="add_data()"><i class="fa fa-plus"></i> Tambah Pengeluaran</button>
            <?php } ?>


            <div class="card mt-3">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm w-100" id="main-table">
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
                            <?php $i = 1;
                            foreach ($data as $d) { ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= cek_tgl($d->tanggal) ?></td>
                                    <td><?= $d->biaya ?></td>
                                    <td>Rp. <?= number_format($d->nominal) ?></td>
                                    <td><?= $d->ket ?></td>
                                    <td>
                                        <?php if ($role == 'admin') { ?>
                                            <button class="btn btn-sm btn-success" onclick="edit_data('<?= $d->id ?>', '<?= $d->tanggal ?>', '<?= $d->biaya ?>', '<?= $d->nominal ?>', '<?= $d->ket ?>')"><i class="fa fa-edit"></i></button>
                                            <button class="btn btn-sm btn-danger" onclick="delete_data('<?= $d->id ?>')"><i class="fa fa-trash"></i></button>
                                        <?php } ?>
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
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-white" aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= form_open('validation_pengeluaran', 'id="form-pengeluaran"') ?>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="act" id="act">
            <div class="modal-body">
                <div class="form-group">
                    <label><b>Tanggal</b></label>
                    <input type="date" name="date" id="date" required class="form-control">
                </div>

                <div class="form-group">
                    <label><b>Biaya</b></label>
                    <input type="text" name="biaya" id="biaya" required class="form-control">
                    <small class="text-danger" id="err_biaya"></small>
                </div>

                <div class="form-group">
                    <label><b>Nominal</b></label>
                    <input type="text" name="nominal" id="nominal" required class="form-control">
                    <small class="text-danger" id="err_nominal"></small>
                </div>

                <div class="form-group">
                    <label><b>Ket</b></label>
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
    $(document).ready(() => {
        $('#main-table').dataTable({
            ordering: false
        })
    })

    $('#nominal').on('keyup mouseup', () => {
        $('#nominal').mask("#.##0", {
            reverse: true
        });
    })

    $('#form-pengeluaran').submit(function(e) {
        e.preventDefault()
        loading()
        $('#nominal').unmask()

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
                                $('#staticBackdrop').modal('hide')
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


    function add_data() {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Tambah Pengeluaran')

        $('#id').val('')
        $('#act').val('add')
        $('#date').val('')
        $('#biaya').val('')
        $('#nominal').val('')
        $('#ket').val('')

        $('#err_biaya').html('')
        $('#err_nominal').html('')
    }

    function edit_data(id, tgl, biaya, nominal, ket) {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Edit Pengeluaran')

        $('#id').val(id)
        $('#act').val('edit')
        $('#date').val(tgl)
        $('#biaya').val(biaya)
        $('#nominal').val(nominal)
        $('#ket').val(ket)

        $('#err_biaya').html('')
        $('#err_nominal').html('')
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