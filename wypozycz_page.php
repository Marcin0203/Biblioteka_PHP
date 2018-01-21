<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['userLogin'] = "";
    
    require_once 'userLogin.php';
    
    $user = new userLogin();
    if($user->getLoggedInUser(session_id()) == -1){
        header('Location: index.php');
    }
    else{
        if($_SESSION['typ_konta'] == 1){
            header('Location: index.php');
        }
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
        <title>Biblioteka - Wypożycz</title>
    </head>
    <body>
        <div id="naglowek_div">
            <h1>
                <span id="naglowek_tresc">Wypożycz książki!</span>
            </h1>     
        </div>
            <?php
            if($_SESSION['typ_konta'] == 2){
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
            }
            else{
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
            }    
            ?>
        </div>
        <div id="tresc_div">
            <span class="tresc_span" id="tresc_katalog">
                <?php
                    if (isset($_SESSION['pracownik_wypozyczono'])){
                        echo 'Udało się wypożyczyć książkę!<br>';
                        unset($_SESSION['pracownik_wypozyczono']);
                    }
                    
                     if (isset($_SESSION['pracownik_oddano'])){
                        echo 'Udało się oddać książkę!<br>';
                        unset($_SESSION['pracownik_oddano']);
                    }
                    
                    if (isset($_SESSION['pracownik_anulowano'])){
                        echo 'Udało się anulować rezerwację książki!<br>';
                        unset($_SESSION['pracownik_anulowano']);
                    }
                ?>
                Możesz tutaj przeglądać książki które czytelnicy zarezerwowali i wypożyczyli:
            </span>
            <div id='formularz_div'>
                <form>
                    <table class='do_wypozyczenia_table'>
                        <tr><td colspan="5" class='ksiazki_td_naglowek'>Zarezerwowane książki:</td>
                        </tr>
                        <tr>
                            <td class='ksiazki_td_naglowek'>Login</td>
                            <td class='ksiazki_td_naglowek'>Autor</td>
                            <td class='ksiazki_td_naglowek'>Tytuł</td>
                            <td class='ksiazki_td_naglowek'>Data rezerwacji</td>
                            <td class='ksiazki_td_naglowek'>Akcja</td>
                        </tr>
                        <?php
                            $_SESSION['wypozycz_process'] = "";
                            require 'wypozycz_process.php';
                            printZarezerwowane("TRUE");  
                        ?>  
                    </table>
                    <br>
                    <table class='do_wypozyczenia_table'>
                        <tr><td colspan="5" class='ksiazki_td_naglowek'>Zarezerwowane książki których rezerwacja upłynęła:</td>
                        </tr>
                        <tr>
                            <td class='ksiazki_td_naglowek'>Login</td>
                            <td class='ksiazki_td_naglowek'>Autor</td>
                            <td class='ksiazki_td_naglowek'>Tytuł</td>
                            <td class='ksiazki_td_naglowek'>Data rezerwacji</td>
                            <td class='ksiazki_td_naglowek'>Akcja</td>
                        </tr>
                        <?php
                            printZarezerwowane("FALSE"); 
                        ?>  
                    </table>
                    <br>
                    <table class='wypozyczone_table_pracownik'>
                        <tr><td colspan="5" class='ksiazki_td_naglowek'>Wypożyczone książki:</td>
                        </tr>
                        <tr>
                            <td class='ksiazki_td_naglowek'>Login</td>
                            <td class='ksiazki_td_naglowek'>Autor</td>
                            <td class='ksiazki_td_naglowek'>Tytuł</td>
                            <td class='ksiazki_td_naglowek'>Data rezerwacji</td>
                            <td class='ksiazki_td_naglowek'>Akcja</td>
                        </tr>
                        <?php
                            printWypozyczone();
                            if (isset($_SESSION['blad_select'])){
                                echo $_SESSION['blad_select'];
                                unset($_SESSION['blad_select']);
                            }
                        ?>  
                    </table>
                </form>
            </div>
        </div>
        <div id="stopka"> &copy; 2017 Marcin Małocha</div>
    </body>
</html>

