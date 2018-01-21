<?php
    session_start();
    
    $_SESSION['statystyki_process'] = "";
    require_once 'statystyki_process.php';
    $_SESSION['userLogin'] = "";
    
    require_once 'userLogin.php';
    
    $user = new userLogin();
    if(!($user->getLoggedInUser(session_id()) == 1)){
        header('Location: index.php');
    }
    else{
        if($_SESSION['typ_konta'] == 1 || $_SESSION['typ_konta'] == 2){
            header('Location: index.php');
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="CSS/style.css" type="text/css" />
        <script src="js/whcookies.js"></script>
        <title>Biblioteka - Strona Główna</title>
    </head>
    <body>
        <div id="naglowek_div">
            <h1>
                <span id="naglowek_tresc">Witaj na swoim koncie!</span>
            </h1>     
        </div>
        <div id='menu3'>
            <ol>
                <li><a class='button_menu2' href='user_page.php'>Strona Główna</a></li>
                <li><a class='button_menu2' href='katalog_ksiazek.php'>Katalog książek</a></li>
                <li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a>
                    <ul>
                        <li><a class='button_menu2' href='moje_ksiazki.php'>Moje książki</a></li>
                        <li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a></li>
                    </ul>
                </li>
                <li><a class='button_menu2' href='wypozycz_page.php'>Opcje pracownika</a>
                    <ul>
                        <li><a class='button_menu2' href='wypozycz_page.php'>Wypożycz/Oddaj</a></li>
                        <li><a class='button_menu2' href='dodaj_ksiazke_page.php'>Dodaj książkę</a></li>
                    </ul>
                </li>
                <li><a class='button_menu2' href='statystyki_page.php'>Opcje Właściciela</a>
                    <ul>
                        <li><a class='button_menu2' href='rejestracja.php'>Dodaj użytkownika</a></li>
                        <li><a class='button_menu2' href='statystyki_page.php'>Statystyki</a></li>
                    </ul>
                </li>
                <li><a class='button_menu2' href='wyloguj.php'>Wyloguj</a></li>
            </ol>
        </div>
         <div id="tresc_div">
            <span class="tresc_span" id="tresc_katalog">
                Tutaj możesz przeglądać statystyki Twojej biblioteki!
            </span>
            <?php
                if(!isset($_SESSION['biblioteka']) && !isset($_SESSION['czytelnik']) && !isset($_SESSION['pracownik']) && 
                   !isset($_SESSION['statystyki_czytelnik']) && !isset($_SESSION['statystyki_pracownik'])){
                    printTresc();
                }
                if(isset($_SESSION['biblioteka'])){
                    printStatystyki("biblioteka");
                    unset($_SESSION['biblioteka']);
                }
                if(isset($_SESSION['czytelnik'])){
                    printOptions("czytelnik");
                    unset($_SESSION['czytelnik']);
                }
                if(isset($_SESSION['pracownik'])){
                    printOptions("pracownik");
                    unset($_SESSION['pracownik']);
                }
                if(isset($_SESSION['statystyki_czytelnik'])){
                    printStatystyki("czytelnik");
                    unset($_SESSION['statystyki_czytelnik']);
                }
                if(isset($_SESSION['statystyki_pracownik'])){
                    printStatystyki("pracownik");
                    unset($_SESSION['statystyki_pracownik']);
                }
            ?>
        </div>
        <div id="stopka"> &copy; 2017 Marcin Małocha</div>
    </body>
</html>
                            
                