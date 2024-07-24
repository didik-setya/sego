<?php
$role = $this->session->userdata('role');
?>
<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data Setoran</h4>
    <div class="row">
        <div class="col-12">
            <?php if ($role == 'admin') { ?>
                <button class="btn btn-sm btn-danger" onclick="add_data()"><i class="fa fa-plus"></i> Tambah Setoran</button>
            <?php } ?>

            <div class="card mt-3">
                <div class="card-body table-responsive">


                    <table class="table table-bordered table-sm" id="main-table">
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
                            <?php $i = 1;
                            foreach ($data as $d) { ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= cek_tgl($d->tanggal) ?></td>
                                    <td>Rp. <?= number_format($d->nominal) ?></td>
                                    <td><?= $d->ket ?></td>
                                    <td>
                                        <?php if ($role == 'admin') { ?>
                                            <button class="btn btn-sm btn-danger" onclick="delete_data('<?= $d->id ?>')"><i class="fa fa-trash"></i></button>

                                            <button class="btn btn-sm btn-primary" onclick="edit_data('<?= $d->id ?>', '<?= $d->tanggal ?>', '<?= $d->ket ?>', '<?= $d->nominal ?>')"><i class="fa fa-edit"></i></button>
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
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>


            <?= form_open('validation_setoran', 'id="form_setor"') ?>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="act" id="act">
            <div class="modal-body">


                <div class="form-group">
                    <label><b>Tanggal</b></label>
                    <input type="date" name="date" id="date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><b>Nominal</b></label>
                    <input type="text" name="nominal" id="nominal" required class="form-control">
                    <small class="text-danger" id="err_nominal"></small>
                </div>

                <div class="form-group">
                    <label><b>Ket</b></label>
                    <textarea name="ket" id="ket" class="form-control"></textarea>
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

<script>
    $(document).ready(() => {
        $('#main-table').dataTable({
            ordering: false
        })
    })

    function add_data() {
        $('#staticBackdrop').modal('show');
        $('#staticBackdrop').find('.modal-title').html('Tambah Setoran')

        $('#id').val('')
        $('#act').val('add')
        $('#date').val('')
        $('#nominal').val('')
        $('#ket').val('')

        $('#err_nominal').html('')
        $('#err_ket').html('')
    }

    function edit_data(id, tgl, ket, jml) {
        $('#staticBackdrop').modal('show');
        $('#staticBackdrop').find('.modal-title').html('Edit Setoran')

        $('#id').val(id)
        $('#act').val('edit')
        $('#date').val(tgl)
        $('#nominal').val(jml)
        $('#ket').val(ket)

        $('#err_nominal').html('')
        $('#err_ket').html('')
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

    $('#form_setor').submit(function(e) {
        e.preventDefault()
        $('#nominal').unmask()
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
                                $('#staticBackdrop').modal('hide');
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

    $('#nominal').on('keyup mouseup', () => {
        $('#nominal').mask("#.##0", {
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