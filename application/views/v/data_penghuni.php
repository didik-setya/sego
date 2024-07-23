<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data Penghuni</h4>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-sm btn-success" onclick="add_data()"><i class="fa fa-plus"></i> Tambah Data</button>

            <div class="card mt-3">
                <div class="card-body">

                    <table class="table table-sm table-bordered w-100" id="main_table">
                        <thead>
                            <tr class="bg-dark text-light">
                                <th>#</th>
                                <th>Nama</th>
                                <th>Kamar</th>
                                <th>Status</th>
                                <th>Last Update</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $i = 1;
                            foreach ($data as $d) {
                                $hash_id = md5(sha1($d->id));
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $d->nama_penghuni ?></td>
                                    <td><?= $d->no_kamar . ' (km ' . $d->km . ')' ?></td>
                                    <td>
                                        <?php
                                        if ($d->status == 0) {
                                            echo '<span class="badge badge-danger">Batal Pesan</span>';
                                        } else if ($d->status == 1) {
                                            $tgl_pesan = cek_tgl($d->tgl_pemesanan);
                                            echo '<span class="badge badge-secondary">Pemesanan (' . $tgl_pesan . ')</span>';
                                        } else if ($d->status == 2) {
                                            $tgl_penempatan = cek_tgl($d->tgl_penempatan);
                                            echo '<span class="badge badge-success">Di tempati (' . $tgl_penempatan . ')</span>';
                                        } else if ($d->status == 3) {
                                            $tgl_keluar = cek_tgl($d->tgl_keluar);
                                            echo '<span class="badge badge-warning">Keluar (' . $tgl_keluar . ')</span>';
                                        } else {
                                            echo '<span class="badge badge-dark">Unknow</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?= cek_tgl($d->last_update) ?></td>
                                    <td>
                                        <div class="btn-group dropleft">
                                            <button type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" onclick="edit_data('<?= $d->id ?>', '<?= $d->id_kamar ?>', '<?= $d->nama_penghuni ?>', '<?= $d->status ?>')"><i class="fa fa-edit"></i> Edit</a>
                                                <a class="dropdown-item" href="#" onclick="delete_penghuni('<?= $d->id ?>')"><i class="fa fa-trash"></i> Hapus</a>
                                                <a class="dropdown-item" href="<?= base_url('payment?id=' . $hash_id) ?>" target="_blank"><i class="fas fa-money-bill-wave"></i> Pembayaran</a>
                                            </div>
                                        </div>
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
            <div class="modal-header bg-success text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-light">&times;</span>
                </button>
            </div>
            <?= form_open('action_data_penghuni', 'id="form_penghuni"') ?>
            <input type="hidden" name="id" id="id_modal">
            <input type="hidden" name="act" id="act_modal">
            <div class="modal-body">
                <div class="form-group">
                    <label><b>Kamar</b></label>
                    <select name="kamar" id="kamar" required class="form-control">
                        <option value="">--pilih--</option>
                        <?php foreach ($kamar as $k) { ?>
                            <option value="<?= $k->id ?>"><?= $k->no_kamar . ' (km ' . $k->km . ')' ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label><b>Nama Penghuni</b></label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><b>Status Penghuni</b></label>
                    <select name="status" id="status" required class="form-control">
                        <option value="">-pilih--</option>
                        <option value="1">Pemesanan</option>
                        <option value="2">Di tempati</option>
                        <option value="3">Keluar</option>
                        <option value="0">Batal Pesan</option>

                    </select>
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
        $('#main_table').dataTable({
            scrollX: false,
            ordering: false
        })
    })

    function add_data() {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Tambah Data Penghuni')
        $('#kamar').attr('required', true);


        $('#id_modal').val('')
        $('#act_modal').val('add')
        $('#kamar').val('')
        $('#name').val('')
        $('#status').val('')
    }

    $('#form_penghuni').submit(function(e) {
        e.preventDefault();
        loading()

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
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
                            $('#staticBackdrop').modal('hide')
                            window.location.reload()
                        })
                    }
                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close()
                    error_alert(error)
                }, 200);
            }
        })
    })

    function edit_data(id, kamar, penghuni, status) {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Edit Data Penghuni')
        $('#kamar').removeAttr('required');

        $('#id_modal').val(id)
        $('#act_modal').val('edit')
        $('#kamar').val('')
        $('#name').val(penghuni)
        $('#status').val(status)
    }


    function delete_penghuni(id) {
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
            url: '<?= base_url('action_data_penghuni') ?>',
            type: 'POST',
            dataType: 'JSON',
            data: {
                act: 'delete',
                id: id,
                '<?= $this->security->get_csrf_token_name() ?>': token
            },
            success: function(d) {
                setTimeout(() => {
                    Swal.close()
                    regenerate_token(d.token);
                    if (d.status == false) {
                        error_alert(d.msg)
                    } else {
                        Swal.fire({
                            title: "Success",
                            text: d.msg,
                            icon: "success",
                        }).then((res) => {
                            window.location.reload()
                        })
                    }

                }, 200);
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close()
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