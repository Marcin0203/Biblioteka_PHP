<?php

if(!isset($_SESSION)){                  //Sprawdzenie czy sesja nie istnieje
    session_start();                    //Jeśli nie istenieje Start sesji
}

$_SESSION['katalog_process'] = "";      //Utworzenie zmiennej sesyjnej dajacej dostep do pliku katalog_process.php
require_once 'katalog_process.php';     //Dołączenie pliku katalog_process.php
printTresc();                           //Drukuj tresc strony katalog_ksiazek
    
