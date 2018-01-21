<?php
session_start();
$_SESSION['userLogin'] = "";
require_once 'userLogin.php';

    $user = new userLogin();
    if($user->logout()){
        
        header('Location: index.php');
        session_unset();
    
    }
    else{
         header('Location: user_page.php');     
    }


