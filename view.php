<?php

require 'ceklogin.php';
if(isset($_GET['id_p'])){ //id_p = id pelanggan
    $id_p = $_GET['id_p'];

    $ambil_nama_pelanggan = mysqli_query($c, "select * from pesanan p, pelanggan pl where p.id_pelanggan=pl.
    id_pelanggan and p.id_pesanan='$id_p'");
    //nama pelanggan
    $np = mysqli_fetch_array($ambil_nama_pelanggan);
    $nama_pel = $np['nama_pelanggan'];
} else{
    header('location:index.php');
}

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Data Pesanan</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous"/>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">Aplikasi Kasir</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>

        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Menu</div>
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Order
                            </a>
                            <a class="nav-link" href="stock.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Stock Barang
                            </a>
                            <a class="nav-link" href="barangmasuk.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Barang Masuk
                            </a>
                            <a class="nav-link" href="pelanggan.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Kelola Pelanggan
                            </a>
                            <a class="nav-link" href="logout.php">
                                Logout
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Start Bootstrap
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Data Pesanan: <?=$id_p;?></h1>
                        <h4 class="mt-4">Nama Pelanggan: <?=$nama_pel;?></h4>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Selamat Datang</li>
                        </ol>


                         <!-- Button to Open the Modal -->
                         <button type="button" class="btn btn-info mb-4" data-toggle="modal" data-target="#myModal">
                            Tambah Barang
                        </button>

                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Data Pesanan
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Produk</th>
                                            <th>Harga Satuan</th>
                                            <th>Jumlah</th>
                                            <th>Sub-Total</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $get = mysqli_query($c, "select * from detail_pesanan p, produk pr where p.id_produk =
                                    pr.id_produk and id_pesanan='$id_p'");

                                    $i = 1;
                                    
                                    while($p=mysqli_fetch_array($get)){
                                    $id_pr = $p['id_produk']; //id produk new object
                                    $id_dp = $p['id_detail_pesanan'];
                                    $qty = $p['qty'];    
                                    $harga = $p['harga'];    
                                    $nama_produk = $p['nama_produk'];
                                    $desc = $p['deskripsi'];
                                    $sub_total = $qty*$harga;
                                    
                                    ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=$nama_produk;?> (<?=$desc;?>)</td>
                                            <td>Rp<?=number_format($harga);?></td>
                                            <td><?=number_format($qty);?></td>
                                            <td>Rp<?=number_format($sub_total);?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#edit<?=$id_pr;?>">
                                                    Edit
                                                </button>
                                                <button type="button" class="btn btn-danger " data-toggle="modal" data-target="#detele<?=$id_pr;?>">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- The Modal Edit -->
                                        <div class="modal fade" id="edit<?=$id_pr;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                        
                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                <h4 class="modal-title">Ubah Data Detail Pesanan</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                
                                                <form method="post">


                                                <!-- Modal body -->
                                                <div class="modal-body">
                                                    <input type="text" name="nama_produk" class="form-control" placeholder="Nama produk" value="<?=$nama_produk;?> : <?=$desc;?>" disabled>
                                                    <input type="number" name="qty" class="form-control mt-2" placeholder="Harga Produk" value="<?=$qty;?>">
                                                    <input type="hidden" name="id_dp" value="<?=$id_dp;?>"> 
                                                    <input type="hidden" name="id_p" value="<?=$id_p;?>"> 
                                                    <input type="hidden" name="id_pr" value="<?=$id_pr;?>"> 
                                                </div>
                                                
                                                <!-- Modal footer -->
                                                <div class="modal-footer">
                                                <button type="submit" class="btn btn-success" name="edit_detail_pesanan">Submit</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>

                                                </form>
                                                
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Delete-->
                                        <div class="modal fade" id="detele<?=$id_pr;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                        
                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                <h4 class="modal-title"></h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                
                                                <form method="post">


                                                <!-- Modal body -->
                                                <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus barang ini?
                                                    <input type="hidden" name="id_p" value="<?=$id_dp;?>">
                                                    <input type="hidden" name="id_pr" value="<?=$id_pr;?>">
                                                    <input type="hidden" name="id_pesanan" value="<?=$id_p;?>">

                                                </div>
                                                
                                                <!-- Modal footer -->
                                                <div class="modal-footer">
                                                <button type="submit" class="btn btn-success" name="hapus_produk_pesanan">Ya</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>

                                                </form>
                                                
                                                </div>
                                            </div>
                                        </div>

                                    <?php
                                    }; //end of while

                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy;Aplikasi Kasir 2024</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.5.1.slim.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/datatables-demo.js"></script>
        <script src="js/datatables-simple-demo.js"></script>


    <!-- The Modal -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
      
            <!-- Modal Header -->
            <div class="modal-header">
            <h4 class="modal-title">Tambah Barang</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <form method="post">


            <!-- Modal body -->
            <div class="modal-body">
                Pilih Barang
                <select name="id_produk" class="form-control">
                    <?php
                    $getproduk = mysqli_query($c, "select * from produk where id_produk not in (select id_produk from detail_pesanan 
                    where id_pesanan='$id_p')");

                    while($pl=mysqli_fetch_array($getproduk)){
                        $nama_produk = $pl['nama_produk'];
                        $stock = $pl['stock'];
                        $deskripsi = $pl['deskripsi'];
                        $id_produk = $pl['id_produk'];
                    
                    ?>

                    <option value="<?=$id_produk;?>"><?=$nama_produk;?> - <?=$deskripsi;?> (Stock: <?=$stock;?>)</option>

                    <?php
                    }    
                    ?>
                    
                </select>

                <input type="number" name="qty" class="form-control mt-4" placeholder="Jumlah" min="1" required>
                <input type="hidden" name="id_p" value="<?=$id_p;?>">

            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
            <button type="submit" class="btn btn-success" name="add_produk">Submit</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

            </form>
            
            </div>
        </div>
    </div>

</html>
