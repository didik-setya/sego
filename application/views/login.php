<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/') ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/') ?>css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/') ?>vendor/sweetalert/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-dark">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-md-6">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Login Page</h1>
                                    </div>

                                    <?= form_open('actionlogin', 'class="user" id="loginuser"') ?>
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Enter username..." name="username">
                                        <small class="text-danger" id="err_username"></small>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" name="password">
                                        <small class="text-danger" id="err_password"></small>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('assets/') ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('assets/') ?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/') ?>js/sb-admin-2.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor/sweetalert/dist/sweetalert2.all.min.js"></script>

    <script>
        $('#loginuser').submit(function(e) {
            e.preventDefault();
            loading()

            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: 'POST',
                dataType: 'JSON',
                success: function(d) {

                    regenerate_token(d.token)
                    setTimeout(() => {

                        Swal.close();
                        if (d.type == 'validation') {

                            if (d.err_username == '') {
                                $('#err_username').html('')
                            } else {
                                $('#err_username').html(d.err_username)
                            }

                            if (d.err_password == '') {
                                $('#err_password').html('')
                            } else {
                                $('#err_password').html(d.err_password)
                            }

                        } else if (d.type == 'result') {
                            $('#err_username').html('')
                            $('#err_password').html('')

                            if (d.status == false) {
                                error_alert(d.msg)
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: d.msg
                                }).then((res) => {
                                    window.location.href = d.redirect
                                })
                            }

                        }

                    }, 200);

                },
                error: function(xhr, status, error) {
                    setTimeout(() => {
                        Swal.close()
                        error_alert(error);
                    }, 200);
                }
            })

        })

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

</body>

</html>