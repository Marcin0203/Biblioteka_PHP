<?php

if(!isset($_SESSION)){          //Sprawdzenie czy sesja nie istnieje
    session_start();            //Jeśli nie istenieje Start sesji
}

$_SESSION['katalog_process'] = "";
require_once 'katalog_process.php';
printTresc();
    
