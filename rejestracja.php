<?php

    if(!isset($_SESSION)){                                  //Sprawdzenie czy sesja nie istnieje
        session_start();                                    //Jeśli nie istenieje Start sesji
    }
    
    $_SESSION['userLogin'] = "";                            //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
    require_once 'userLogin.php';                           //Dołączenie pliku userLogin.php

    $_SESSION['rejestracja_process'] = "";                  //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku rejestracja_process.php
    require_once 'rejestracja_process.php';                 //Dołączenie pliku rejestracja_process.php
    
    printTresc();                                           //Drukuj treść strony