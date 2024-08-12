<?php
$role = $this->session->userdata('role');
?>
<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data Kamar</h4>

    <div class="row">
        <div class="col-12">

            <?php if ($role == 'admin') { ?>
                <button class="btn btn-sm btn-primary" onclick="add_data()"><i class="fa fa-plus"></i> Tambah Data Baru</button>
            <?php } ?>


            <div class="card mt-3">
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm" id="main_table">
                        <thead>
                            <tr class="bg-dark text-light">
                                <th>#</th>
                                <th>Kamar</th>
                                <th>Kamar Mandi</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Gedung</th>
                                <th>Last Update</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($data as $d) {
                            ?>

                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $d->no_kamar ?></td>
                                    <td><?= $d->km ?></td>
                                    <td>Rp. <?= number_format($d->price) ?></td>
                                    <td>
                                        <?php
                                        if ($d->status == 0) {
                                            echo '<span class="badge badge-danger">Nonaktif</span>';
                                        } else if ($d->status == 1) {
                                            echo '<span class="badge badge-success">Tersedia</span>';
                                        } else if ($d->status == 2) {
                                            echo '<span class="badge badge-info">Di Pesan</span>';
                                        } else if ($d->status == 3) {
                                            echo '<span class="badge badge-primary">Di Tempati</span>';
                                        } else if ($d->status == 4) {
                                            echo '<span class="badge badge-warning">Renovasi</span>';
                                        } else {
                                            echo '<span class="badge badge-dark">Unknow</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $d->lokasi_gedung ?></td>
                                    <td><?= cek_tgl($d->last_update) ?></td>
                                    <td>
                                        <?php if ($role == 'admin') { ?>
                                            <div class="btn-group dropleft">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-cogs"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="edit_data('<?= $d->id ?>', '<?= $d->no_kamar ?>', '<?= $d->km ?>', '<?= $d->status ?>', '<?= $d->price ?>', '<?= $d->lokasi_gedung ?>')"><i class="fa fa-edit"></i> Edit</a>
                                                    <a class="dropdown-item" href="#" onclick="delete_data('<?= $d->id ?>')"><i class="fa fa-trash"></i> Hapus</a>
                                                </div>
                                            </div>
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
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-light">&times;</span>
                </button>
            </div>

            <?= form_open('validation_kamar', 'id="form_kamar"') ?>
            <div class="modal-body">
                <input type="hidden" name="act" id="act_kamar">
                <input type="hidden" name="id" id="id_kamar">

                <div class="form-group">
                    <label><b>No Kamar</b></label>
                    <input type="text" name="kamar" id="kamar" class="form-control" required>
                    <small class="text-danger" id="err_kamar"></small>
                </div>

                <div class="form-group">
                    <label><b>Kamar Mandi</b></label>
                    <select name="km" id="km" required class="form-control">
                        <option value="">--pilih--</option>
                        <option value="luar">Luar</option>
                        <option value="dalam">Dalam</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><b>Harga Kamar</b></label>
                    <input type="text" name="price" id="price" class="form-control" required>
                    <small class="text-danger" id="err_price"></small>
                </div>


                <div class="form-group">
                    <label><b>Status Kamar</b></label>
                    <select name="status" id="status" class="form-control">
                        <option value="">--pilih--</option>
                        <option value="1">Tersedia</option>
                        <option value="2">Di Pesan</option>
                        <option value="3">Di Tempati</option>
                        <option value="4">Renovasi</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>


                <div class="form-group">
                    <label><b>Lokasi Gedung</b></label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control">
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
            autoWidth: false,
            scrollX: false,
            ordering: false
        })

        $('#price').on('keyup mouseup', () => {
            $('#price').mask("#.##0", {
                reverse: true
            });
        })
    })

    $('#form_kamar').submit(function(e) {
        e.preventDefault();
        loading()
        $('#price').unmask();
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
                        if (d.err_kamar == '') {
                            $('#err_kamar').html('')
                        } else {
                            $('#err_kamar').html(d.err_kamar)
                        }

                        if (d.err_price == '') {
                            $('#err_price').html('')
                        } else {
                            $('#err_price').html(d.err_price)
                        }
                    } else {
                        $('#err_price').html('')
                        $('#err_kamar').html('')
                        if (d.status == false) {
                            error_alert(d.msg);
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: d.msg
                            }).then((res) => {
                                window.location.reload()
                            })
                        }
                    }

                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close();
                    error_alert(error)
                }, 200);
            }
        })
    })

    function add_data() {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Tambah Data Kamar')
        $('#act_kamar').val('add')

        $('#kamar').val('');
        $('#km').val('');
        $('#status').val('');
        $('#status').attr('disabled', true);
        $('#status').removeAttr('required')
        $('#err_kamar').html('')
        $('#err_price').html('')
        $('#id_kamar').val('')
        $('#price').val('')
        $('#lokasi').val('')
    }

    function edit_data(id, kamar, km, status, price, lokasi) {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Edit Data Kamar')
        $('#act_kamar').val('edit')

        $('#kamar').val(kamar);
        $('#km').val(km);
        $('#price').val(price)
        $('#status').val('');


        // $('#status').removeAttr('disabled')
        if (status == 1 || status == 4 || status == 0) {
            $('#status').attr('required', true);
            $('#status').removeAttr('disabled');
            $('#status').val(status);
        } else {
            $('#status').attr('disabled', true);
            $('#status').removeAttr('required');
            $('#status').val('');
        }

        $('#err_kamar').html('')
        $('#err_price').html('')
        $('#id_kamar').val(id)
        $('#lokasi').val(lokasi)
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
            url: '<?= base_url('delete_kamar') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close();
                    if (d.status == false) {
                        error_alert(d.msg);
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: d.msg
                        }).then((res) => {
                            window.location.reload()
                        })
                    }
                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close();
                    error_alert(error)
                }, 200);
            }
        })
    }


    function regenerate_token(token) {
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        $('input[name=' + token_name + ']').val(token)
    }

    function error_alert(msg) {
        Swal.fire({
            title: "Error",
            text: msg,
            icon: "error"
        });
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