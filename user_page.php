<?php
    session_start();
    
    $_SESSION['user_process'] = "";                 //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku user_process.php
    require_once 'user_process.php';                //Dołączenie pliku user_process.php
    
    $_SESSION['userLogin'] = "";                    //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
    require 'userLogin.php';                        //Dołączenie klasy reprezentującej użytkownika podczas logowania

    $user = new userLogin();                        //Utworzenie nowego obiektu klasy userLogin.php
    if($user->getLoggedInUser(session_id()) == -1){ //Sprawdzenie czy ID sesji znajduje sie w tabeli logged_users
        header('Location: index.php');              //Jeśli nie przejdź na index.php
        exit();                                     //exit
    }
    else{
        printTresc();                               //Jeśli jest drukuj treść strony
    }
