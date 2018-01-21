<?php
    session_start();
    
    $_SESSION['userLogin'] = "";
    require_once 'userLogin.php';  
    
    $user = new userLogin();
    
    //if($user->getLoggedInUser(session_id()) == -1){
        //header('Location: index.php');
    //}
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="CSS/style.css" type="text/css" />
        <script src="js/whcookies.js"></script>
        <title>Biblioteka - Moje konto</title>
    </head>
    <body>
        <div id="naglowek_div">
            <h1>
                <span id="naglowek_tresc">Moje konto</span>
            </h1>     
        </div>
            <?php
                switch ($_SESSION['typ_konta']){
                    case 1:
                        $tresc = "";
                        $tresc .= "<div id='menu1'>";
                        $tresc .= "<a class='button_menu1' href='user_page.php'>Strona Główna</a>";
                        $tresc .= "<a class='button_menu1' href='katalog_ksiazek.php'>Katalog książek</a>";
                        $tresc .= "<a class='button_menu1' href='moje_ksiazki.php'>Moje książki</a>";
                        $tresc .= "<a class='button_menu1' href='moje_konto_page.php'>Moje konto</a>";
                        $tresc .= "<a class='button_menu1' href='wyloguj.php'>Wyloguj</a>";
                        echo $tresc;
                        break;
                    case 2:
                        $tresc = "";
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
                        echo $tresc;
                        break;
                    case 3:
                        $tresc = "";
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
                        echo $tresc;
                        break;
                }
            ?>
        </div>
        <div id="tresc_div">
            <span class="tresc_span">
                <?php
                    $_SESSION['moje_konto_process'] = "";
                    require_once 'moje_konto_process.php';
                    if(isset($_SESSION['zmiana'])){
                        printZmiana();    
                    }
                    if(isset($_SESSION['edycja'])){
                        printEdycja();
                    }
                    if(!isset($_SESSION['edycja']) && !isset($_SESSION['zmiana'])){
                        printTresc();
                    }
                    
                ?>
        </div>
        <div id="stopka"> &copy; 2017 Marcin Małocha</div>
    </body>
</html>

