<?php
    if(!isset($_SESSION)){
        session_start();
    }
    $_SESSION['userLogin'] = "";
    require_once 'userLogin.php';
    $user = new userLogin();
    if(!($user->getLoggedInUser(session_id()) == 1)){
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
        <title>Biblioteka - Dodaj Książkę</title>
    </head>
    <body>
        <div id="naglowek_div">
            <h1>
                <span id="naglowek_tresc">Dodaj książkę!</span>
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
                    if(isset($_SESSION['istnieje_autor'])){
                        echo $_SESSION['istnieje_autor'];
                        unset($_SESSION['istnieje_autor']);
                    }
                    if(isset($_SESSION['istnieje_ksiazka'])){
                        echo $_SESSION['istnieje_ksiazka'];
                        unset($_SESSION['istnieje_ksiazka']);
                    }
                    else{
                        if(isset($_SESSION['dodaj_autora'])){
                            echo 'Możesz tutaj dodać nowego autora do biblioteki.';
                        }
                        else{
                            echo 'Możesz tutaj dodać nową książkę do biblioteki.';
                        }
                    }
                ?>
            </span>
            <?php
                if(isset($_SESSION['dodano_autora'])){
                    echo '<br><span class="good">Dodano nowego autora!</span>';
                    unset($_SESSION['dodano_autora']);
                }
                if(isset($_SESSION['dodano_ksiazke'])){
                    echo $_SESSION['dodano_ksiazke'];
                    unset($_SESSION['dodano_ksiazke']);
                }
            ?>
            <div id="formularz_div">
                <form method="post">
                    <table class="formularz_table">
                        <tr>
                            <?php
                                $_SESSION['dodaj_ksiazke_process'] = "";
                                require 'dodaj_ksiazke_process.php';
                                if(isset($_SESSION['dodaj_autora'])){
                                    printAddAutor();
                                }
                                else{
                                    printAutor();
                                }  
                            ?>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <div id="stopka"> &copy; 2017 Marcin Małocha</div>
    </body>
</html>