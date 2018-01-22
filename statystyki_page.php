<?php
     if(!isset($_SESSION)){                          //Sprawdzenie czy sesja nie istnieje
        session_start();                            //Jeśli nie istenieje Start sesji
    }
    
    $_SESSION['userLogin'] = "";                    //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
    require_once 'userLogin.php';                   //Dołączenie klasy reprezentującej użytkownika podczas logowania
    
    $_SESSION['statystyki_process'] = "";           //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku statystyki_process.php
    require_once 'statystyki_process.php';          //Dołączenie pliku statystyki_process.php
    
     $user = new userLogin();                        //Utworzenie nowego obiektu klasy userLogin.php
    if($user->getLoggedInUser(session_id()) == -1){ //Sprawdzenie czy ID sesji znajduje sie w tabeli logged_users
        header('Location: index.php');              //Jeśli nie przejdź na index.php
        exit();                                     //exit
    }
    else{
        if($_SESSION['typ_konta'] == 1 || $_SESSION['typ_konta'] == 2){//Jeśli id sesji znajduje się w tabeli logged_users sprawdzenie czy typ konta = czytelnik Lub pracownik
            header('Location: index.php');          //Jeśli tak przejdź na index.php
            exit();                                 //exit
        }
        else{
            printTresc();                          //Jeśli nie drukuj treść strony
        }
    }
               