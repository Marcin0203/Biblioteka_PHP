<?php
//Plik obsługujący proces rejestracji

if(!isset($_SESSION)){                          //Sprawdzenie czy sesja nie istnieje
    session_start();                            //Jeśli nie istenieje Start sesji
}

if(!(isset($_SESSION['rejestracja_process']))){     //Sprawdzenie czy NIE istnieje zmienna sesyjna rejestracja_process
    header('Location: index.php');                  //Jeśli nie istnieje przenieś na index.php
    exit();                                         //exit
}
else{                                           
    unset($_SESSION['rejestracja_process']);        //Jeśli istnieje usuń zmienną sesyjną
}

$_SESSION['userRejestracja'] = "";                  //utworzenie zmiennej sesyjne dajacej dostep do dołączenia klasy userRejestracja.php
require_once 'userRejestracja.php';                 //Dołączenie klasy obsługującej użytkowników rejestracji

if (isset($_REQUEST['zarejestruj'])){               //Obsluga klikniecia przycisku Zarejestruj
    
    $user = new userRejestracja();                  //Tworzenie nowego obiektu dla danych użytkownika które wprowdził w formularzu
    
    //Pobranie danych wprowadzonych do formularza i zapisanie do obiektu
    $user->setLogin($_POST['login_rej']);
    $user->setEmail($_POST['email_rej']);
    $user->setPassword($_POST['password_rej']);
    $user->setRepeatPassword($_POST['repeatPassword_rej']);
    $user->setImie($_POST['imie']);
    $user->setDrugieImie($_POST['drugie_imie']);
    $user->setNazwisko($_POST['nazwisko']);
    $user->setMiasto($_POST['miasto']);
    $user->setPoczta($_POST['poczta']);
    $user->setUlica($_POST['ulica']);
    $user->setNrDomu($_POST['nr_domu']);
    $user->setNrMieszkania($_POST['nr_mieszkania']);
    $user->setTelefon($_POST['telefon']);
    if($_SESSION['typ_konta'] == 3){                                        //Sprawdzenie czy formularz został wyświetlony z poziomu właściciela
        $user->setTypKonta(htmlspecialchars(trim($_REQUEST['typ_konta']))); //Jeśli tak to pobierz z radio buttona typ konta
    }
    else{
        $user->setTypKonta("1");                                            //Jeśli nie utworz konto czytelnika
    }
    
    
    if($user->walidacja()){                                                 //Wywołanie funkcji walidującej formularz
        
        //Sprawdzenie czy na wpisane dane nie istnieje juz uzytkownik w bazie. Sprawdzane są dane kontaktowe, login oraz email
        if(!$user->checkUserInDatabase("contact") && !$user->checkUserInDatabase("login") && !$user->checkUserInDatabase("email")){
            
            if($user->addUser("contact") && $user->addUser("login")){       //Wywołanie funkcji ktora dodaje nowego uzytkownika w tabeli users_login oraz users_contact
               
                $user->unlockFormSession();                                 //Jeśli dodano poprawnie do bazy usuń wszystkie zmienne sesyjne związane z procesem rejestracji
                if(isset($_SESSION['typ_konta'])){
                    if($_SESSION['typ_konta'] == 2 || $_SESSION['typ_konta'] == 3){ //Sprawdzenie czy formularz zostal wywolany przez pracownika lub wlasciciela
                        $_SESSION['zarejestrowano'] .= '<tr><td></td><td class="formularz_td_error"><span class="good">Utworzono konto użytkownika!</span></td></tr>';
                        header('Location: user_page.php');
                    }
                }
                else{
                    $_SESSION['zarejestrowano'] .= '<tr><td></td><td class="formularz_td_error"><span class="good">Zarejestrowano poprawnie! Możesz teraz zalogować się na swoje konto.</span></td></tr>';
                    header('Location: index.php');
                }
            }
            else{
                $_SESSION['error_addUser'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd przy dodawaniu nowego użytkownika!</span></td></tr>';
                header('Location: rejestracja.php');
            } 
        }
        else{
            header('Location: rejestracja.php');
        }     
    }
    else{
        header('Location: rejestracja.php');
    }
    
}

//Obsługa przycisku Powrót
if(isset($_REQUEST['back'])){
    
    //Utworzenie nowego obiektu aby wywołać funkcję usuwającą wszystkie zmienne sesyjne utworzone podczas procesu rejestracji
    $user = new userRejestracja();
    $user->unlockFormSession();
    header('Location: index.php');
}

//Funkcja wypisująca treść strony rejestracja.php
function printTresc(){
    $tresc = "";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' />"
            . "<script src='js/whcookies.js'></script><title>Biblioteka - Rejestracja</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Formularz rejestracyjny</span></h1></div>";
    $user = new userLogin();
    if($user->getLoggedInUser(session_id()) == 1){
                        
        switch ($_SESSION['typ_konta']){
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
    }
    else{
        $tresc .= '<div id="menu_index">';
        $tresc .= '<a class="button_menu_index" href="index.php">Strona Główna</a>';
        $tresc .= '<a class="button_menu_index" href="katalog_ksiazek.php">Katalog książek</a>';
        $tresc .= '<a class="button_menu_index" href="rejestracja.php">Rejestracja</a>';
    }
    $tresc .= "</div><div id='tresc_div'><span class='tresc_span'>Wpisz wszystkie dane, aby założyć nowe konto:</span>"
            . "<div id='formularz_div'><form method='post'><table class='formularz_table'><tr><td class='formularz_td_text'>E-mail:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='email' name='email_rej' placeholder='E-mail...' ";
    if (isset($_SESSION['email'])) {
        $tresc .= 'value="'.$_SESSION['email'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_email'])) {
        $tresc .= $_SESSION['error_email'];
        unset ($_SESSION['error_email']);
    }
    if (isset($_SESSION['error_istniejeEmail'])) {
        $tresc .= $_SESSION['error_istniejeEmail'];
        unset ($_SESSION['error_istniejeEmail']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Login:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='login_rej' placeholder='Login...' ";
    if (isset($_SESSION['login'])) {
        $tresc .= 'value="'.$_SESSION['login'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_login'])) {
        $tresc .= $_SESSION['error_login'];
        unset ($_SESSION['error_login']);
    }
    if (isset($_SESSION['error_istniejeLogin'])) {
        $tresc .= $_SESSION['error_istniejeLogin'];
        unset ($_SESSION['error_istniejeLogin']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Hasło:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='password' name='password_rej' placeholder='Hasło...'></td>";
    if (isset($_SESSION['error_password'])) {
        $tresc .= $_SESSION['error_password'];
        unset ($_SESSION['error_password']);
    }
    else{
        $tresc .= "<tr><td></td><td class='formularz_td_error'><span class='good'>Wymagania hasła:<br> "
                . "Długość: od 6 do 30 znaków <br>Min. jedna mała, duża litera oraz cyfra</span></td>";
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Powtórz hasło:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='password' name='repeatPassword_rej' placeholder='Powtórz hasło...'></td>";
    if (isset($_SESSION['error_repeatpassword'])) {
        $tresc .= $_SESSION['error_repeatpassword'];
        unset ($_SESSION['error_repeatpassword']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Imię:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='imie' placeholder='Twoje imię... ' ";
    if (isset($_SESSION['imie'])) {
        $tresc .= 'value="'.$_SESSION['imie'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_imie'])) {
        $tresc .= $_SESSION['error_imie'];
        unset ($_SESSION['error_imie']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Drugie imię:</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='drugie_imie' placeholder='Jeśli posiadasz drugie imię... ' ";
    if (isset($_SESSION['drugieImie'])) {
        $tresc .= 'value="'.$_SESSION['drugieImie'].'"';
    }
    $tresc .= "></td></tr><tr><td class='formularz_td_text'>Nazwisko:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nazwisko' placeholder='Twoje nazwisko... ' ";
    if (isset($_SESSION['nazwisko'])) {
        $tresc .= 'value="'.$_SESSION['nazwisko'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_nazwisko'])) {
        $tresc .= $_SESSION['error_nazwisko'];
        unset ($_SESSION['error_nazwisko']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Miasto:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='miasto' placeholder='Miasto gdzie jest poczta... ' ";
    if (isset($_SESSION['miasto'])) {
        $tresc .= 'value="'.$_SESSION['miasto'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_miasto'])) {
        $tresc .= $_SESSION['error_miasto'];
        unset ($_SESSION['error_miasto']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Poczta:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='poczta' placeholder='Np. 22-335... ' ";
    if (isset($_SESSION['poczta'])) {
        $tresc .= 'value="'.$_SESSION['poczta'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_poczta'])) {
        $tresc .= $_SESSION['error_poczta'];
        unset ($_SESSION['error_poczta']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Ulica:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='ulica' placeholder='Nazwa ulicy (Ewentualnie nazwa miejscowości)... ' ";
    if (isset($_SESSION['ulica'])) {
        $tresc .= 'value="'.$_SESSION['ulica'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_ulica'])) {
        $tresc .= $_SESSION['error_ulica'];
        unset ($_SESSION['error_ulica']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Nr domu:*</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nr_domu' placeholder='Twój nr domu/bloku... ' ";
    if (isset($_SESSION['nrDomu'])) {
        $tresc .= 'value="'.$_SESSION['nrDomu'].'"';
    }
    $tresc .= "></td>";
    if (isset($_SESSION['error_nrDomu'])) {
        $tresc .= $_SESSION['error_nrDomu'];
        unset ($_SESSION['error_nrDomu']);
    }
    $tresc .= "</tr><tr><td class='formularz_td_text'>Nr mieszkania:</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nr_mieszkania' placeholder='Twój nr mieszkania... ' ";
    if (isset($_SESSION['nrMieszkania'])) {
        $tresc .= 'value="'.$_SESSION['nrMieszkania'].'"';
    }
    $tresc .= "></td></tr><tr><td class='formularz_td_text'>Telefon kontaktowy:</td>"
            . "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='telefon' placeholder='Twój nr telefonu... ' ";
    if (isset($_SESSION['telefon'])) {
        $tresc .= 'value="'.$_SESSION['telefon'].'"';
    }
    $tresc .= "></td></tr>";
    if (isset($_SESSION['blad_select'])) {
        $tresc .= $_SESSION['blad_select'];
        unset ($_SESSION['blad_select']);
    }
    if (isset($_SESSION['blad_polaczenia'])) {
        $tresc .= $_SESSION['blad_polaczenia'];
        unset ($_SESSION['blad_polaczenia']);
    }
    if (isset($_SESSION['error_addUser'])) {
        $tresc .= $_SESSION['error_addUser'];
        unset ($_SESSION['error_addUser']);
    }
    if (isset($_SESSION['error_istniejeContact'])) {
        $tresc .= $_SESSION['error_istniejeContact'];
        unset ($_SESSION['error_istniejeContact']);
    }
    if(isset($_SESSION['typ_konta']) && $_SESSION['typ_konta'] == 3){
        $tresc .= "<tr><td></td><td class='formularz_td_input'>Typ konta*:</td></tr><tr>";
        $tresc .= "<td></td><td class='formularz_td_input'><input type='radio' name='typ_konta' value='1' ";
        if(isset($_SESSION['typ']) && $_SESSION['typ'] == 1){
            $tresc .= "checked='checked'";
            unset($_SESSION['typ']);
        }
        $tresc .= "> Zwykły (Czytelnik)<br>";
        $tresc .= "<input type='radio' name='typ_konta' value='2' ";
        if(isset($_SESSION['typ']) && $_SESSION['typ'] == 2){
            $tresc .= "checked='checked'";
            unset($_SESSION['typ']);
        }
        $tresc .= "> Pracownik<br>";
        $tresc .= "<input type='radio' name='typ_konta' value='3' ";
        if(isset($_SESSION['typ']) && $_SESSION['typ'] == 3){
            $tresc .= "checked='checked'";
            unset($_SESSION['typ']);
        }
        $tresc .= "> Właściciel<br></td></tr>";
        if(isset($_SESSION['error_typ'])){
            $tresc .= $_SESSION['error_typ'];
            unset($_SESSION['error_typ']);
        }
    }
    $tresc .= "<tr><td></td><td class='formularz_td_text' id='formularz_dane_wymagane'>*Dane wymagane</td>"
            . "</tr><tr><td></td><td class='formularz_td_button'>";
    if(isset($_SESSION['typ_konta'])){
        if($_SESSION['typ_konta'] == 3 || $_SESSION['typ_konta'] == 2){
            $tresc .= '<button class="formularz_button_zarejestruj" name="zarejestruj">Dodaj</button>';
        }
    }
    else{
        $tresc .= '<button class="formularz_button_zarejestruj" name="zarejestruj">Zarejestruj</button>';
        $tresc .= '<button class="formularz_button_back" name="back">Powrót</button>';
    }
    $tresc .= "</td></tr></table></form></div></div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

