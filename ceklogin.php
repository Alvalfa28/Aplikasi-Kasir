<?php

require 'function.php';

if(isset($_SESSION['login'])){
    
} else {
    //belum login
    header('location:login.php');
}