<?php 

if(!isset($_SESSION)){                                  //Sprawdzenie czy sesja nie istnieje
    session_start();                                    //Jeśli nie istenieje Start sesji
}
    
    
    $_SESSION['userLogin'] = "";                        //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
    require_once 'userLogin.php';                       //Dołączenie pliku userLogin.php
    
    
    $_SESSION['login_process'] = "";                    //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku login_process.php
    require_once 'login_process.php';                   //Dołączenie pliku login_process.php
    
    $user = new userLogin();                            //Utworzenie obiektu klasy userLogin.php
    if($user->getLoggedInUser(session_id()) == 1){      //Sprawdzenie czy nie istnieje w bazie zalogowany uzytkownik z aktualnym ID sesji
        header('Location: user_page.php');              //Jeśli istnieje przenieś na user_page.php
    }
    else{
        printTresc();                                   //Jeśli nie istnieje drukuj treść strony
    }
