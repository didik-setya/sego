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
    <div class="modal-dialog modal-lg">
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

                <div class="row justify-content-center">
                    <div class="form-group col-md-6" id="form-kost">
                        <label><b>Nama Kost</b></label>
                        <input type="text" name="kost" id="kost" class="form-control">
                        <small class="text-danger" id="err_kost"></small>
                    </div>

                    <div class="form-group col-md-6" id="form-foto">
                        <label><b>Foto</b></label>
                        <input type="file" name="foto_kost" id="foto_kost" class="form-control">
                    </div>

                    <div class="form-group col-12">
                        <label><b>Alamat</b></label>
                        <textarea name="alamat" id="alamat" class="form-control"></textarea>
                    </div>
                </div>

                <hr>
                <h5><b>Kontak</b></h5>
                <hr>
                <div id="contac_area">
                    <div class="row">
                        <div class="form-group col-md-5">
                            <input type="text" name="nama_kontak[]" id="nama_kontak[]" class="form-control" placeholder="Nama Kontak" required>
                        </div>
                        <div class="form-group col-md-5">
                            <input type="number" name="no_kontak[]" id="no_kontak[]" class="form-control" placeholder="No. Kontak" required>
                        </div>
                        <div class="form-group col-md-2">
                            <button style="height: 100%" onclick="add_contact()" class="btn btn-sm btn-success w-100" type="button"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
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

<div id="contact_form_hidden" class="d-none">
    <div class="row">
        <div class="form-group col-md-5">
            <input type="text" name="nama_kontak[]" id="nama_kontak[]" class="form-control" placeholder="Nama Kontak" required>
        </div>
        <div class="form-group col-md-5">
            <input type="number" name="no_kontak[]" id="no_kontak[]" class="form-control" placeholder="No. Kontak" required>
        </div>
        <div class="form-group col-md-2">
            <button style="height: 100%" class="btn btn-sm btn-danger w-100 remove-contact" type="button"><i class="fa fa-trash"></i></button>
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

        $('#foto_kost').attr('required', true)
        $('#foto_kost').val('')
        $('#alamat').val('')
    }

    function edit_data(id, kost) {
        $('#staticBackdrop').modal('show')
        $('#staticBackdrop').find('.modal-title').html('Edit Data')

        $('#id').val(id)
        $('#act').val('edit')
        $('#kost').val(kost)
        $('#err_kost').html('')

        $('#foto_kost').removeAttr('required')
        $('#foto_kost').val('')
        $('#alamat').val('')
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
            contentType: false,
            processData: false,
            url: $(this).attr('action'),
            data: new FormData(this),
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
                        $('#err_kost').html('')
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

    function add_contact() {
        let html = $('#contact_form_hidden').html()
        $('#contac_area').append(html)
    }

    $(document).on('click', '.remove-contact', function() {
        $(this).parent('div').parent('div').remove()
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