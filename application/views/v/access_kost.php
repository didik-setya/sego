<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Data User & Access Kost</h4>


    <div class="row">
        <div class="col-12">
            <button class="btn btn-sm btn-outline-dark" onclick="add_user()"><i class="fa fa-plus"></i> Tambah User</button>

            <div class="card mt-3">
                <div class="card-body">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="bg-dark text-light">
                                <th>#</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th><i class="fa fa-cogs"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($data as $d) { ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $d->name ?></td>
                                    <td><?= $d->username ?></td>
                                    <td><?= $d->role ?></td>
                                    <td>
                                        <?php if ($d->status == 1) {
                                            echo 'Aktif';
                                        } else {
                                            echo 'nonaktif';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if ($d->role != 'admin') { ?>
                                            <div class="btn-group dropleft">
                                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-cogs"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" onclick="edit_data('<?= $d->id ?>', '<?= $d->name ?>', '<?= $d->username ?>', '<?= $d->role ?>')"><i class="fa fa-edit"></i> Edit</a>
                                                    <a class="dropdown-item" href="#" onclick="delete_data('<?= $d->id ?>')"><i class="fa fa-trash"></i> Delete</a>
                                                    <a class="dropdown-item" href="#" onclick="access_kost('<?= $d->id ?>')"><i class="fas fa-key"></i> Access Kost</a>

                                                    <?php if ($d->status == 1) { ?>
                                                        <a class="dropdown-item" href="#" onclick="change_status('<?= $d->id ?>', 'nonaktif')"><i class="fa fa-power-off"></i> Nonaktifkan</a>
                                                    <?php } else { ?>
                                                        <a class="dropdown-item" href="#" onclick="change_status('<?= $d->id ?>', 'aktif')"><i class="fa fa-power-off"></i> Aktifkan</a>
                                                    <?php } ?>
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
            <div class="modal-header bg-dark text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= form_open('action_user', 'id="form_user"') ?>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="act" id="act">
            <div class="modal-body">
                <div class="form-group">
                    <label><b>Nama User</b></label>
                    <input type="text" name="name" id="name" class="form-control">
                    <small class="text-danger" id="err_name"></small>
                </div>

                <div class="form-group">
                    <label><b>Username</b></label>
                    <input type="text" name="username" id="username" class="form-control">
                    <small class="text-danger" id="err_username"></small>
                </div>

                <div class="form-group" id="form_newpass">
                    <label><b>Password Baru</b></label>
                    <input type="password" name="newpass" id="newpass" class="form-control">
                    <small class="text-danger" id="err_newpass"></small>
                </div>

                <div class="form-group" id="form_repass">
                    <label><b>Ulangi Password </b></label>
                    <input type="password" name="repass" id="repass" class="form-control">
                    <small class="text-danger" id="err_repass"></small>
                </div>


                <div class="form-group">
                    <label><b>Role User</b></label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">--pilih--</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
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


<!-- Modal -->
<div class="modal" id="modalAccess" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-light" aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('access_kost', 'id="form_access"') ?>
            <input type="hidden" name="id" id="id_access">
            <input type="hidden" name="act" id="act_access" value="change">
            <div class="modal-body">
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
    function add_user() {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop .modal-title').html('Tambah User Baru')

        $('#id').val('')
        $('#act').val('add')
        $('#name').val('')
        $('#username').val('')
        $('#newpass').val('')
        $('#repass').val('')
        $('#role').val('')

        $('#err_name').html('')
        $('#err_username').html('')
        $('#err_newpass').html('')
        $('#err_repass').html('')

        $('#form_newpass').removeClass('d-none')
        $('#form_repass').removeClass('d-none')
    }

    function edit_data(id, name, username, role) {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop .modal-title').html('Edit User')

        $('#id').val(id)
        $('#act').val('edit')
        $('#name').val(name)
        $('#username').val(username)
        $('#newpass').val('')
        $('#repass').val('')
        $('#role').val(role)

        $('#err_name').html('')
        $('#err_username').html('')
        $('#err_newpass').html('')
        $('#err_repass').html('')

        $('#form_newpass').addClass('d-none')
        $('#form_repass').addClass('d-none')
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
            url: '<?= base_url('action_user') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id,
                act: 'delete'
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                processing_result(d)
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close()
                    error_alert(error, xhr)
                }, 200);
            }
        })
    }

    function processing_result(d) {
        regenerate_token(d.token)
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
                    window.location.reload()
                })
            }
        }, 200);
    }

    function change_status(id, status) {
        loading()
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()


        $.ajax({
            url: '<?= base_url('action_user') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                id: id,
                act: 'status',
                status: status
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                processing_result(d)
            },
            error: function(xhr, status, error) {
                setTimeout(() => {
                    Swal.close()
                    error_alert(error, xhr)
                }, 200);
            }
        })
    }


    $('#form_user').submit(function(e) {
        e.preventDefault()
        loading();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token);
                setTimeout(() => {
                    Swal.close()

                    if (d.type == 'validation') {
                        if (d.err_name == '') {
                            $('#err_name').html('')
                        } else {
                            $('#err_name').html(d.err_name)
                        }

                        if (d.err_username == '') {
                            $('#err_username').html('')
                        } else {
                            $('#err_username').html(d.err_username)
                        }

                        if (d.err_newpass == '') {
                            $('#err_newpass').html('')
                        } else {
                            $('#err_newpass').html(d.err_newpass)
                        }

                        if (d.err_repass == '') {
                            $('#err_repass').html('')
                        } else {
                            $('#err_repass').html(d.err_repass)
                        }
                    } else if (d.type == 'result') {
                        $('#err_name').html('')
                        $('#err_username').html('')
                        $('#err_newpass').html('')
                        $('#err_repass').html('')

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

    $('#form_access').submit(function(e) {
        e.preventDefault()
        loading();

        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token)
                setTimeout(() => {
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


    function access_kost(id) {
        $('#modalAccess .modal-title').html('Access Kost')
        $('#modalAccess .modal-body').html('')
        $('#id_access').val(id)
        let token_name = '<?= $this->security->get_csrf_token_name() ?>'
        let token = $('input[name=' + token_name + ']').val()

        loading()
        $.ajax({
            url: '<?= base_url('access_kost') ?>',
            data: {
                '<?= $this->security->get_csrf_token_name() ?>': token,
                act: 'get_data',
                id: id
            },
            type: 'POST',
            dataType: 'JSON',
            success: function(d) {
                regenerate_token(d.token)
                setTimeout(() => {
                    Swal.close()
                    $('#modalAccess').modal('show');

                    const access = d.access;
                    let html = '';
                    let i;
                    let a = 1;
                    let b = 1;

                    for (i = 0; i < access.length; i++) {
                        if (access[i].access == 1) {
                            html += '<div class="form-check form-check-inline"><input checked class="form-check-input" type="checkbox" id="inlineCheckbox' + a++ + '" value="' + access[i].id_kost + '" name="kost[]"><label class="form-check-label" for="inlineCheckbox' + b++ + '">' + access[i].kost_name + '</label></div>';
                        } else {
                            html += '<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" id="inlineCheckbox' + a++ + '" value="' + access[i].id_kost + '" name="kost[]"><label class="form-check-label" for="inlineCheckbox' + b++ + '">' + access[i].kost_name + '</label></div>';
                        }
                    }
                    $('#modalAccess .modal-body').html(html)

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