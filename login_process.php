<?php
//Plik obsługujący proces logowania

if(!isset($_SESSION)){                       //Sprawdzenie czy sesja nie istnieje
    session_start();                         //Jeśli nie istenieje Start sesji
}

$_SESSION['userLogin'] = "";                //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
require_once 'userLogin.php';               //Dołączenie klasy obsługującej użytkowników logowania

if(!(isset($_SESSION['login_process']))){   //Sprawdzenie czy NIE istnieje zmienna sesyjna login_process
    header('Location: index.php');          //Jeśli nie istnieje przenieś na index.php         
    exit();                                 //exit
}
else{
    unset($_SESSION['login_process']);      //Jeśli istnieje usuń zmienną sesyjną
}

if (isset($_REQUEST['zaloguj'])){           //Obsluga klikniecia przycisku Zaloguj
     
    $user = new userLogin();                //Tworzenie nowego obiektu dla danych użytkownika które wprowdził w formularzu
    
    $user->setLogin($_POST['Login']);       //Pobranie danych wprowadzonych do formularza i zapisanie do obiektu
    $user->setPassword($_POST['Password']);
    
    if($user->walidacja()){                 //Sprawdzenie walidacji
        $_SESSION['zalogowano']="";         //Jeśli TRUE ustaw zmienna sesyjna i przejdz do user_page.php
        header('Location: user_page.php');
        exit();                             //exit
    }
    else{
        header('Location: index.php');      //Jeśli nie powrót do index.php
        exit();                             //exit
    }
    
}

function printTresc(){                      //Funckja wypisująca treść strony index
    $tresc = "";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' />"
            . "<script src='js/whcookies.js'></script><title>Biblioteka - Strona Główna</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Witaj na stronie biblioteki!</span></h1></div>"
            . "<div id='menu_index'><a class='button_menu_index' href='index.php'>Strona Główna</a>"
            . "<a class='button_menu_index' href='katalog_ksiazek.php'>Katalog książek</a>"
            . "<a class='button_menu_index' href='rejestracja.php'>Rejestracja</a></div>"
            . "<div id='tresc_div'><span class='tresc_span'>Zaloguj się lub przejdź do rejestracji.</span>"
            . "<div id='formularz_div'><form method='post'><table class='formularz_table'>"
            . "<tr><td class='formularz_td_text'>Login:</td><td class='formularz_td_input'>"
            . "<input class='formularz_inputText' type='text' name='Login' placeholder='Login...' ></td>";
    if (isset($_SESSION['error_login'])) {
        $tresc .= $_SESSION['error_login'];
        unset ($_SESSION['error_login']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Hasło:</td><td class='formularz_td_input'>"
            . "<input class='formularz_inputText' type='password' name='Password' placeholder='Hasło...'></td>";
    if (isset($_SESSION['error_password'])) {
        $tresc .= $_SESSION['error_password'];
        unset ($_SESSION['error_password']);
    }
    $tresc .= "</tr>";
    if (isset($_SESSION['blad_select'])) {
        $tresc .= $_SESSION['blad_select'];
        unset ($_SESSION['blad_select']);
    }
    if (isset($_SESSION['blad_polaczenia'])) {
        $tresc .= $_SESSION['blad_polaczenia'];
        unset ($_SESSION['blad_polaczenia']);
    }
    if (isset($_SESSION['zarejestrowano'])) {
        $tresc .= $_SESSION['zarejestrowano'];
        unset ($_SESSION['zarejestrowano']);
    }
    if (isset($_SESSION['error_brak_konta'])) {
        $tresc .= $_SESSION['error_brak_konta'];
        unset ($_SESSION['error_brak_konta']);
    }
    $tresc .= "<tr><td class='formularz_td_button'></td><td class='formularz_td_button'>"
            . "<button class='formularz_button_zarejestruj' name='zaloguj'>Zaloguj</button></td></tr>"
            . "</table></form></div></div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

