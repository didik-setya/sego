<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data Kost</h4>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-sm btn-dark" onclick="add_data()"><i class="fa fa-plus"></i> Tambah Data</button>


            <div class="card mt-3">
                <div class="card-body table-responsive">
                    <table class="table table-sm table-bordered" id="main_table">
                        <thead>
                            <tr class="bg-dark text-light">
                                <th>#</th>
                                <th>Nama Kost</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($data as $d) { ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $d->kost_name ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="delete_data('<?= $d->id ?>')"><i class="fa fa-trash"></i></button>

                                        <button class="btn btn-sm btn-primary" onclick="edit_data('<?= $d->id ?>', '<?= $d->kost_name ?>')"><i class="fa fa-edit"></i></button>
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
            <div class="modal-header bg-dark text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('action_kost', 'id="form-kost"') ?>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="act" id="act">
            <div class="modal-body">
                <div class="form-group">
                    <label><b>Nama Kost</b></label>
                    <input type="text" name="kost" id="kost" class="form-control">
                    <small class="text-danger" id="err_kost"></small>
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
        $('#main_table').dataTable()
    })

    function add_data() {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Tambah Data')

        $('#id').val('')
        $('#act').val('add')
        $('#kost').val('')
        $('#err_kost').html('')
    }

    function edit_data(id, kost) {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Edit Data')

        $('#id').val(id)
        $('#act').val('edit')
        $('#kost').val(kost)
        $('#err_kost').html('')
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
            url: '<?= base_url('delete_kost') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token)
                if (d.status == false) {
                    error_alert(d.msg)
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: d.msg
                    }).then((res) => {
                        window.location.reload()
                    })
                }
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close();
                    error_alert(error, xhr)
                }, 200);
            }
        })

    }

    $('#form-kost').submit(function(e) {
        e.preventDefault()
        loading()

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
                        if (d.err_kost == '') {
                            $('#err_kost').html('')
                        } else {
                            $('#err_kost').html(d.err_kost)
                        }
                    } else if (d.type == 'result') {
                        if (d.status == false) {
                            error_alert(d.msg)
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
                    Swal.close()
                    error_alert(error, xhr)
                }, 200);
            }
        })
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