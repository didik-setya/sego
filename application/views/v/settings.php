<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">Settings</h4>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <?= form_open('validate_settings', 'id="form_settings"') ?>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-8 col-lg-6">
                            <input type="hidden" name="id" id="id" value="<?= $data->id ?>">

                            <div class="form-group">
                                <label><b>Username</b></label>
                                <input type="text" name="username" id="username" class="form-control" value="<?= $data->username ?>">
                                <small class="text-danger" id="err_username"></small>
                            </div>

                            <div class="form-group">
                                <label><b>Nama</b></label>
                                <input type="text" name="name" id="name" class="form-control" value="<?= $data->name ?>">
                                <small class="text-danger" id="err_name"></small>
                            </div>
                        </div>

                        <div class="col-12 text-right">
                            <button class="btn btn-sm btn-dark" onclick="open_modal()" type="button"><i class="fa fa-key"></i> Change Password</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="staticBackdropLabel">Update Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="text-white" aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open('valid_password', 'id="form_pass"') ?>
            <input type="hidden" name="id" id="id" value="<?= $data->id ?>">
            <div class="modal-body">

                <div class="form-group">
                    <label><b>Password Lama</b></label>
                    <input type="password" name="old_pass" id="old_pass" class="form-control">
                    <small class="text-danger" id="err_old_pass"></small>
                </div>

                <div class="form-group">
                    <label><b>Password Baru</b></label>
                    <input type="password" name="new_pass" id="new_pass" class="form-control">
                    <small class="text-danger" id="err_new_pass"></small>
                </div>

                <div class="form-group">
                    <label><b>Ulangi Password Baru</b></label>
                    <input type="password" name="re_newpass" id="re_newpass" class="form-control">
                    <small class="text-danger" id="err_re_newpass"></small>
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
    $('#form_settings').submit(function(e) {
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
                        if (d.err_username == '') {
                            $('#err_username').html('');
                        } else {
                            $('#err_username').html(d.err_username);
                        }

                        if (d.err_name == '') {
                            $('#err_name').html('');
                        } else {
                            $('#err_name').html(d.err_name);
                        }
                    } else if (d.type == 'result') {
                        $('#err_username').html('');
                        $('#err_name').html('');

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

    $('#form_pass').submit(function(e) {
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
                        if (d.err_oldpass == '') {
                            $('#err_old_pass').html('')
                        } else {
                            $('#err_old_pass').html(d.err_oldpass)
                        }


                        if (d.err_newpass == '') {
                            $('#err_new_pass').html('')
                        } else {
                            $('#err_new_pass').html(d.err_newpass)
                        }


                        if (d.err_re_newpass == '') {
                            $('#err_re_newpass').html('')
                        } else {
                            $('#err_re_newpass').html(d.err_re_newpass)
                        }
                    } else if (d.type == 'result') {
                        $('#err_old_pass').html('')
                        $('#err_new_pass').html('')
                        $('#err_re_newpass').html('')

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

    function open_modal() {
        $('#staticBackdrop').modal('show')

        $('#old_pass').val('')
        $('#new_pass').val('')
        $('#re_newpass').val('')

        $('#err_old_pass').html('')
        $('#err_new_pass').html('')
        $('#err_re_newpass').html('')
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