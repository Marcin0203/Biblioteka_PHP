<?php
//Plik obsługujący wylogowanie
if(!isset($_SESSION)){                          //Sprawdzenie czy sesja nie istnieje
    session_start();                            //Jeśli nie istenieje Start sesji
}

$_SESSION['userLogin'] = "";                    //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
require_once 'userLogin.php';                   //Dołączenie klasy reprezentującej użytkownika podczas logowania

    $user = new userLogin();                    //Utworzenie nowego obiektu klasy userLogin.php
    if($user->logout()){                        //Wywolanie funkcji logout
        header('Location: index.php');          //Jeśli funkcja zwróci TRUE przejdz do index.php
        session_unset();                        //Zakończ sesje
    }
    else{
         header('Location: user_page.php');     //Jeśli funkcja zwroci FALSE przejdz na  user_page.php   
    }


