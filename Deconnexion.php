<?php
    require 'Config.php';
    $_SESSION = array();
    session_destroy(); 
    header('Location: homepage.php');
?>