<?php
session_start();

//Bikin Koneksi
$c = mysqli_connect('localhost', 'root', '', 'aplikasi_kasir_2024');

//Login

if (isset($_POST['login'])){
    //inisiate variable
    $username = $_POST['username'];
    $password = $_POST['password'];
  
    $check = mysqli_query($c, "SELECT * FROM user WHERE username='$username' and password='$password' ");
    $hitung = mysqli_num_rows($check);
  
    if ($hitung > 0){
        //Jika datanya ditemukan
        //berhasil login
        $_SESSION['login'] = 'True';
        header('location:index.php');
    } else {
        //data tidak ditemukan
        //gagal login
        echo '
        <script>alert("Username atau Password Salah");
        window.location.href="login.php"
        </script>
        ';
    }
}


//=================stock.php===============================//


//Function menambah barang
if(isset($_POST['tambah_barang'])){
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stock = $_POST['stock'];

    $insert = mysqli_query($c, "insert into produk (nama_produk, deskripsi, harga, stock) values ('$nama_produk', 
    '$deskripsi', '$harga', '$stock')");

    if($insert){
        header('location:stock.php');
    } else {
        echo ' 
        <script>alert("Gagal menambah barang baru");
        window.location.href="stock.php"
        </script>
        ';
    }
}

//edit barang
if(isset($_POST['edit_barang'])){
    $np = $_POST['nama_produk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $id_p = $_POST['id_p']; //id produk

    $query = mysqli_query($c, "update produk set nama_produk='$np', deskripsi='$desc', harga='$harga' where id_produk='$id_p'");

    if($query){
        header('location:stock.php'); //jika berhasil redirect ke stock.php
    } else {
        echo ' 
        <script>alert("Gagal");
        window.location.href="stock.php"
        </script>
        ';
    }
}

//hapus barang
if(isset($_POST['hapus_barang'])){
    $id_p = $_POST['id_p'];

    $query = mysqli_query($c, "delete from produk where id_produk='$id_p'");

    if($query){
        header('location:stock.php'); //jika berhasil redirect ke stock.php
    } else {
        echo ' 
        <script>alert("Gagal");
        window.location.href="stock.php"
        </script>
        ';
    }
}


//====================pelanggan.php============================//


//Menambah pelanggan
if(isset($_POST['tambah_pelanggan'])){
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($c, "insert into pelanggan (nama_pelanggan, no_telepon, alamat) values ('$nama_pelanggan', 
    '$no_telepon', '$alamat')");

    if($insert){
        header('location:pelanggan.php');
    } else {
        echo ' 
        <script>alert("Gagal menambah pelanggan baru");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}


//edit pelanggan
if(isset($_POST['edit_pelanggan'])){
    $npel = $_POST['nama_pelanggan'];
    $nt = $_POST['no_telepon'];
    $a = $_POST['alamat'];
    $id = $_POST['id_pl'];

    $query = mysqli_query($c, "update pelanggan set nama_pelanggan='$npel', no_telepon='$nt', alamat='$a' where id_pelanggan='$id'");

    if($query){
        header('location:pelanggan.php'); //jika berhasil redirect ke pelanggan.php
    } else {
        echo ' 
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';
    }

}

//hapus pelanggan
if(isset($_POST['hapus_pelanggan'])){
    $id_pl= $_POST['id_pl'];

    $query = mysqli_query($c, "delete from pelanggan where id_pelanggan='$id_pl'");

    if($query){
        header('location:pelanggan.php'); //jika berhasil redirect ke pelanggan.php
    } else {
        echo ' 
        <script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>
        ';
    }
}





//===================index.php =============================//

//menambah pesanan/order pada index.php
if(isset($_POST['tambah_pesanan'])){
    $id_pelanggan = $_POST['id_pelanggan'];

    $insert = mysqli_query($c, "insert into pesanan (id_pelanggan) values ('$id_pelanggan')");

    if($insert){
        header('location:index.php');
    } else {
        echo ' 
        <script>alert("Gagal menambah pesanan baru");
        window.location.href="index.php"
        </script>
        ';
    }
}

//hapus pesanan
if(isset($_POST['hapus_pesanan'])){
    $id_pes= $_POST['id_pes']; //id pesanan

    $cekdata = mysqli_query($c, "select * from detail_pesanan dp where id_pesanan='$id_pes'");

    while($cd=mysqli_fetch_array($cekdata)){
        //balikin stock
        $qty = $cd['qty'];
        $id_produk = $cd['id_produk'];
        $id_dp = $cd['id_detail_pesanan'];

        //cari tahu stock yang ada sekarang
        $caristock = mysqli_query($c, "select * from produk where id_produk='$id_produk'");
        $caristock2 = mysqli_fetch_array($caristock);
        $stock_sekarang = $caristock2['stock'];

        $newstock = $stock_sekarang+$qty;

        $queryupdate = mysqli_query($c, "update produk set stock ='$newstock' where id_produk='$id_produk'");

        //hapus data
        $querydelete = mysqli_query($c, "delete from detail_pesanan where id_detail_pesanan='$id_dp'");
    }

    $query = mysqli_query($c, "delete from pesanan where id_pesanan='$id_pes'");

    if($queryupdate && $querydelete && $query){
        header('location:index.php'); //jika berhasil redirect ke index.php
    } else {
        echo ' 
        <script>alert("Gagal");
        window.location.href="index.php"
        </script>
        ';
    }
}


//===================view.php =============================//

//menambah produk di data pesanan
if(isset($_POST['add_produk'])){
    $id_produk = $_POST['id_produk'];
    $id_p = $_POST['id_p'];
    $qty = $_POST['qty'];

    //hitung stock sekarang ada berapa
    $hitung1 = mysqli_query($c, "select * from produk where id_produk='$id_produk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stock_sekarang = $hitung2['stock']; //stock barang saat ini

    if($stock_sekarang>=$qty){

        //Kurangi stocknya dengan jumlah yg akan dikurangkan
        $selisih = $stock_sekarang-$qty;

        //stocknya cukup
        $insert = mysqli_query($c, "insert into detail_pesanan (id_pesanan, id_produk, qty) values ('$id_p', '$id_produk', '$qty')");
        $update = mysqli_query($c, "update produk set stock='$selisih' where id_produk='$id_produk'");

    if($insert&&$update){
        header('location:view.php?id_p='.$id_p);
    } else {
        echo ' 
        <script>alert("Gagal menambah pesanan baru");
        window.location.href="view.php?id_p='.$id_p.'"
        </script>
        ';
    }
    } else {
        //stock tidak cukup
        echo ' 
        <script>alert("Stock barang tidak cukup");
        window.location.href="view.php?id_p='.$id_p.'"
        </script>
        ';
    }
}

//hapus produk pesanan
if(isset($_POST['hapus_produk_pesanan'])){
    $id_p = $_POST['id_p']; //id_detail_pesanan
    $id_pr = $_POST['id_pr'];
    $id_pesanan = $_POST['id_pesanan'];


    //cek qty sekarang
    $cek1 = mysqli_query($c, "select * from detail_pesanan where id_detail_pesanan='$id_p'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['qty'];

    //cek stock sekarang
    $cek3 = mysqli_query($c, "select * from produk where id_produk='$id_pr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stock_sekarang = $cek4['stock'];

    $hitung = $stock_sekarang+$qtysekarang;

    $update = mysqli_query($c, "update produk set stock='$hitung' where id_produk='$id_pr'"); //update stock
    $hapus = mysqli_query($c, "delete from detail_pesanan where id_produk='$id_pr' and id_detail_pesanan='$id_p'");

    if($update&&$hapus){
        header('location:view.php?id_p='.$id_pesanan);
    } else {
        echo ' 
        <script>alert("Gagal menghapus barang");
        window.location.href="view.php?id_p='.$id_pesanan.'"
        </script>
        ';
    }

}

//Mengubah Data Detail Pesanan
if(isset($_POST['edit_detail_pesanan'])){
    $qty = $_POST['qty'];
    $id_dp = $_POST['id_dp']; //id detail pesanan
    $id_pr = $_POST['id_pr']; //id produk
    $id_p = $_POST['id_p']; //id pesanan

    //cari tau qty sekarang berapa
    $caritahu = mysqli_query($c, "select * from detail_pesanan where id_detail_pesanan='$id_dp'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    //cari tahu stock yang ada sekarang
    $caristock = mysqli_query($c, "select * from produk where id_produk='$id_pr'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stock_sekarang = $caristock2['stock'];
    
    if($qty >= $qtysekarang){
        //kalau inputan user lebih besar daripada qty yang tercatat sekarang
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stock_sekarang-$selisih;

        $query1 = mysqli_query($c, "update detail_pesanan set qty ='$qty' where id_detail_pesanan='$id_dp'");
        $query2 = mysqli_query($c, "update produk set stock ='$newstock' where id_produk='$id_pr'");

        if($query1&&$query2){
            header('location:view.php?id_p='.$id_p); //jika berhasil redirect ke view php sesuai id pelanggannya
        } else {
            echo ' 
            <script>alert("Gagal");
            window.location.href="view.php?id_p='.$id_p.'"
            </script>
            ';
        }

    } else {
        //jika lebih kecil
        //hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stock_sekarang+$selisih;

        $query1 = mysqli_query($c, "update detail_pesanan set qty ='$qty' where id_detail_pesanan='$id_dp'");
        $query2 = mysqli_query($c, "update produk set stock ='$newstock' where id_produk='$id_pr'");

        if($query1&&$query2){
            header('location:view.php?id_p='.$id_p); //jika berhasil redirect ke view php sesuai id pelanggannya
        } else {
            echo ' 
            <script>alert("Gagal");
            window.location.href="view.php?id_p='.$id_p.'"
            </script>
            ';
        }
    }

}



//=======================barangmasuk.php=========================//



//Menambah barang masuk
if(isset($_POST['barang_masuk'])){
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];

    //cari tahu stock yang ada sekarang berapa
    $caristock = mysqli_query($c, "select * from produk where id_produk='$id_produk'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stock_sekarang = $caristock2['stock'];

    //hitung
    $newstock = $stock_sekarang+$qty;

    //insert barang masuk
    $insertb = mysqli_query($c, "insert into masuk (id_produk,qty) values ('$id_produk', '$qty')");
    $updatetb = mysqli_query($c, "update produk set stock='$newstock' where id_produk='$id_produk'");

    if($insertb&&$updatetb){
        //jika berhasil redirect ke barangmasuk.php
        header('location:barangmasuk.php');
    } else {
        echo ' 
        <script>alert("Gagal");
        window.location.href="barangmasuk.php"
        </script>
        ';
    }
}


//Mengubah Data Barang Masuk
if(isset($_POST['edit_data_barang_masuk'])){
    $qty = $_POST['qty'];
    $id_m = $_POST['id_m']; //id masuk
    $id_p = $_POST['id_p']; //id produk

    //cari tau qty sekarang berapa
    $caritahu = mysqli_query($c, "select * from masuk where id_masuk='$id_m'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    //cari tahu stock yang ada sekarang
    $caristock = mysqli_query($c, "select * from produk where id_produk='$id_p'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stock_sekarang = $caristock2['stock'];
    
    if($qty >= $qtysekarang){
        //kalau inputan user lebih besar daripada qty yang tercatat sekarang
        //hitung selisih
        $selisih = $qty-$qtysekarang;
        $newstock = $stock_sekarang+$selisih;

        $query1 = mysqli_query($c, "update masuk set qty ='$qty' where id_masuk='$id_m'");
        $query2 = mysqli_query($c, "update produk set stock ='$newstock' where id_produk='$id_p'");

        if($query1&&$query2){
            header('location:barangmasuk.php'); //jika berhasil redirect ke baragnmasuk.php
        } else {
            echo ' 
            <script>alert("Gagal");
            window.location.href="barangmasuk.php"
            </script>
            ';
        }

    } else {
        //jika lebih kecil
        //hitung selisih
        $selisih = $qtysekarang-$qty;
        $newstock = $stock_sekarang-$selisih;

        $query1 = mysqli_query($c, "update masuk set qty ='$qty' where id_masuk='$id_m'");
        $query2 = mysqli_query($c, "update produk set stock ='$newstock' where id_produk='$id_p'");

        if($query1&&$query2){
            header('location:barangmasuk.php'); //jika berhasil redirect ke baragnmasuk.php
        } else {
            echo ' 
            <script>alert("Gagal");
            window.location.href="barangmasuk.php"
            </script>
            ';
        }
    }

}


//hapus data barang masuk
if(isset($_POST['hapus_data_barang_masuk'])){
    $id_m= $_POST['id_m']; //id_masuk
    $id_p= $_POST['id_p']; //id_produk

    //cari tau qty sekarang berapa
    $caritahu = mysqli_query($c, "select * from masuk where id_masuk='$id_m'");
    $caritahu2 = mysqli_fetch_array($caritahu);
    $qtysekarang = $caritahu2['qty'];

    //cari tahu stock yang ada sekarang
    $caristock = mysqli_query($c, "select * from produk where id_produk='$id_p'");
    $caristock2 = mysqli_fetch_array($caristock);
    $stock_sekarang = $caristock2['stock'];


    //hitung selisih
    $newstock = $stock_sekarang-$qtysekarang;

    $query1 = mysqli_query($c, "delete from masuk where id_masuk='$id_m'");
    $query2 = mysqli_query($c, "update produk set stock ='$newstock' where id_produk='$id_p'");

    if($query1&&$query2){
        header('location:barangmasuk.php'); //jika berhasil redirect ke baragnmasuk.php
    } else {
        echo ' 
        <script>alert("Gagal");
        window.location.href="barangmasuk.php"
        </script>
        ';
    }
}


?>
