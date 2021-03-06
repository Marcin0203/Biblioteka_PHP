<?php
    if(!isset($_SESSION)){                          //Sprawdzenie czy sesja nie istnieje
        session_start();                            //Jeśli nie istenieje Start sesji
    }
    
    $_SESSION['userLogin'] = "";                    //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
    require_once 'userLogin.php';                   //Dołączenie klasy reprezentującej użytkownika podczas logowania
    
    $_SESSION['moje_konto_process'] = "";           //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku moje_konto_process.php
    require_once 'moje_konto_process.php';          //Dołączenie pliku moje_konto_process.php
    
    $user = new userLogin();                        //Utworzenie nowego obiektu klasy userLogin.php
    if($user->getLoggedInUser(session_id()) == -1){ //Sprawdzenie czy ID sesji znajduje sie w tabeli logged_users
        header('Location: index.php');              //Jeśli nie przejdź na index.php
        exit();                                     //exit
    }
    else{
        printTresc();                               //Jeśli jest drukuj treść strony
    }

