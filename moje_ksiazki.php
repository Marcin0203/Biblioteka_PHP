<?php
    session_start();
    $_SESSION['userLogin'] = "";
    require_once 'userLogin.php';   
    $user = new userLogin();
    if($user->getLoggedInUser(session_id()) == -1){
        header('Location: index.php');
    }
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
        <title>Biblioteka - Moje książki</title>
    </head>
    <body>
        <div id="naglowek_div">
            <h1>
                <span id="naglowek_tresc">Moje książki</span>
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
                    if(!isset($_SESSION['anulowano']) && !isset($_SESSION['zarezerwowano'])){
                        echo 'Twoje wypożyczone i zarezerwowane książki.</span>';
                    }
                    if (isset($_SESSION['zarezerwowano'])) {
                        echo "Poprawnie zarezerwowano książkę! Masz teraz 3 dni na odebranie jej w bibliotece.</br></br>Twoje wypożyczone i zarezerwowane książki.</span>";
                        unset ($_SESSION['zarezerwowano']);
                    }
                    if (isset($_SESSION['anulowano'])) {
                        echo "Poprawnie anulowano rezerwację książki.</br></br>Twoje wypożyczone i zarezerwowane książki.</span>";
                        unset ($_SESSION['anulowano']);
                    }
                ?>
                
                <div id='formularz_div'>
                    <form>
                        <table class='zarezerwowane_table'>
                            <tr><td colspan="4" class='ksiazki_td_naglowek'>Zarezerwowane książki:</td>
                            </tr>
                            <tr>
                                <td class='ksiazki_td_naglowek'>Autor</td>
                                <td class='ksiazki_td_naglowek'>Tytuł</td>
                                <td class='ksiazki_td_naglowek'>Pozostały czas rezerwacji</td>
                                <td class='ksiazki_td_naglowek'>Akcja</td>
                            </tr>
                            <?php
                                $_SESSION['moje_ksiazki_process'] = "";
                                require 'moje_ksiazki_process.php';
                                printZarezerwowane();  
                            ?>
                        </table>
                        <table class='wypozyczone_table'>
                            <tr><td colspan="3" class='ksiazki_td_naglowek'>Wypozyczone książki:</td>
                            </tr>
                            <tr>
                                <td class='ksiazki_td_naglowek'>Autor</td>
                                <td class='ksiazki_td_naglowek'>Tytuł</td>
                                <td class='ksiazki_td_naglowek'>Data wypożyczenia</td>
                            </tr>
                            <?php
                                printWypozyczone();  
                            ?>
                        </table>
                    </form>
                    <?php
                        if (isset($_SESSION['blad_select'])) {
                            echo $_SESSION['blad_select'];
                            unset ($_SESSION['blad_select']);
                        }
                    ?>
                </div>
        </div>
        <div id="stopka"> &copy; 2017 Marcin Małocha</div>
    </body>
</html>

