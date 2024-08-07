<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Access Kost</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/') ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/') ?>css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/') ?>vendor/sweetalert/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>

    <?php if ($access) { ?>
        <div class="container my-5">
            <div class="row justify-content-center">

                <div class="col-12">
                    <h5 class="text-center">Silahkan Pilih Kost</h5>
                    <hr>
                </div>

                <?php foreach ($access as $a) { ?>
                    <div class="col-12 col-md-4 col-lg-3 my-2">
                        <div class="card">
                            <div class="card-body bg-primary text-light">
                                <h6 class="text-center my-3">
                                    <b>
                                        <i class="fas fa-home"></i> <?= $a->kost_name ?>
                                    </b>
                                </h6>
                            </div>
                            <?= form_open('to_login', 'class="select_kost"') ?>
                            <div class="card-footer bg-dark text-light text-center">
                                <input type="hidden" name="id" value="<?= $a->id ?>">
                                <button class="btn btn-sm btn-success" type="submit">Go <i class="far fa-arrow-alt-circle-right"></i></button>
                            </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    <?php } else { ?>
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-12 my-5">
                    <h5 class="text-center text-dark">Maaf anda tidak mempunyai akses ke kost manapun. hubungi admin untuk mendapatkan akses</h5>
                    <div class="text-center">
                        <a href="<?= base_url('login/logout') ?>" class="btn btn-sm btn-primary"><i class="far fa-arrow-alt-circle-left"></i> Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>


    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('assets/') ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('assets/') ?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/') ?>js/sb-admin-2.min.js"></script>
    <script src="<?= base_url('assets/') ?>vendor/sweetalert/dist/sweetalert2.all.min.js"></script>
    <script>
        $('.select_kost').submit(function(e) {
            loading()
        })

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