<?php

if(!isset($_SESSION)){                          //Sprawdzenie czy sesja nie istnieje
    session_start();                            //Jeśli nie istenieje Start sesji
}

if(!(isset($_SESSION['user_process']))){        //Sprawdzenie czy NIE istnieje zmienna sesyjna user_process
    header('Location: index.php');              //Jeśli nie istnieje przenieś na index.php
    exit();                                     //exit
}
else{                                           
    unset($_SESSION['user_process']);        //Jeśli istnieje usuń zmienną sesyjną
}

//Funkcja wypisująca treść strony user_page.php
function printTresc(){
    $tresc = "";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' />"
            . "<script src='js/whcookies.js'></script><title>Biblioteka - Zalogowano</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Witaj na swoim koncie!</span></h1></div>";
    switch ($_SESSION['typ_konta']){
        case 1:
            $tresc .= "<div id='menu1'>";
            $tresc .= "<a class='button_menu1' href='user_page.php'>Strona Główna</a>";
            $tresc .= "<a class='button_menu1' href='katalog_ksiazek.php'>Katalog książek</a>";
            $tresc .= "<a class='button_menu1' href='moje_ksiazki.php'>Moje książki</a>";
            $tresc .= "<a class='button_menu1' href='moje_konto_page.php'>Moje konto</a>";
            $tresc .= "<a class='button_menu1' href='wyloguj.php'>Wyloguj</a>";
            break;
        case 2:
            $tresc .= "<div id='menu2'><ol>";
            $tresc .= "<li><a class='button_menu2' href='user_page.php'>Strona Główna</a></li>";
            $tresc .= "<li><a class='button_menu2' href='katalog_ksiazek.php'>Katalog książek</a></li>";
            $tresc .= "<li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a><ul><li>";
            $tresc .= "<a class='button_menu2' href='moje_ksiazki.php'>Moje książki</a></li>";
            $tresc .= "<li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a></li></ul></li>";
            $tresc .= "<li><a class='button_menu2' href='wypozycz_page.php'>Opcje pracownika</a><ul><li><a class='button_menu2' href='wypozycz_page.php'>Wypożycz/Oddaj</a></li>";
            $tresc .= "<li><a class='button_menu2' href='dodaj_ksiazke_page.php'>Dodaj książkę</a></li>";
            $tresc .= "<li><a class='button_menu2' href='rejestracja.php'>Dodaj użytkownika</a></li></ul></li>";
            $tresc .= "<li><a class='button_menu2' href='wyloguj.php'>Wyloguj</a></li></ol>";
            break;
        case 3:
            $tresc .= "<div id='menu3'><ol>";
            $tresc .= "<li><a class='button_menu2' href='user_page.php'>Strona Główna</a></li>";
            $tresc .= "<li><a class='button_menu2' href='katalog_ksiazek.php'>Katalog książek</a></li>";
            $tresc .= "<li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a><ul><li>";
            $tresc .= "<a class='button_menu2' href='moje_ksiazki.php'>Moje książki</a></li>";
            $tresc .= "<li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a></li></ul></li>";
            $tresc .= "<li><a class='button_menu2' href='wypozycz_page.php'>Opcje pracownika</a><ul><li><a class='button_menu2' href='wypozycz_page.php'>Wypożycz/Oddaj</a></li>";
            $tresc .= "<li><a class='button_menu2' href='dodaj_ksiazke_page.php'>Dodaj książkę</a></li></ul></li>";
            $tresc .= "<li><a class='button_menu2' href='statystyki_page.php'>Opcje Właściciela</a><ul><li><a class='button_menu2' href='rejestracja.php'>Dodaj użytkownika</a></li>";
            $tresc .= "<li><a class='button_menu2' href='statystyki_page.php'>Statystyki</a></li></ul></li>";
            $tresc .= "<li><a class='button_menu2' href='wyloguj.php'>Wyloguj</a></li></ol>";
            break; 
    }
    $tresc .= "</div><div id='tresc_div'><span class='tresc_span'>";
    if (isset($_SESSION['zalogowano']) || isset($_SESSION['zarejestrowano'])) {
        if (isset($_SESSION['zalogowano'])){
            $tresc .= "Poprawnie zalogowano! Masz teraz dostęp do innych opcji.";
            unset ($_SESSION['zalogowano']);
        }
        if (isset($_SESSION['zarejestrowano'])){
            $tresc .= ($_SESSION['zarejestrowano']);
            unset ($_SESSION['zarejestrowano']);
        }
    }
    else{
        if($_SESSION['typ_konta'] == 1){
            $tresc .= 'Witaj czytelniku.';
        }
        if($_SESSION['typ_konta'] == 2){
            $tresc .= 'Witaj pracowniku.';
        }
        if($_SESSION['typ_konta'] == 3){
            $tresc .= 'Witaj właścicielu biblioteki.';
        }
    }
    if (isset($_SESSION['wypozyczono'])) {
        $tresc .= "Poprawnie wypożyczono książkę!";
        unset ($_SESSION['wypozyczono']);
    }
    $tresc .= "</span>";
     if(isset($_SESSION['blad_select'])) {
        $tresc .= $_SESSION['blad_select'];
        unset($_SESSION['blad_select']);
    }
    if (isset($_SESSION['blad_polaczenia'])) {
        $tresc .= $_SESSION['blad_polaczenia'];
        unset ($_SESSION['blad_polaczenia']);
    }
    $tresc .= "</div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

