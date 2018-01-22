<?php
//Plik obsługujący stronę statystyki_page

if(!(isset($_SESSION['statystyki_process']))){                                  //Sprawdzenie czy NIE istnieje zmienna sesyjna statystyki_process
    header('Location: index.php');                                              //Jeśli nie istnieje przenieś na index.php
    exit();                                                                     //exit
}
else{
    unset($_SESSION['statystyki_process']);                                     //Jeśli istnieje usuń zmienną sesyjną
}

$_SESSION['baza'] = "";                                                         //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku baza.php
require_once 'baza.php';                                                        //Dołączenie klasy obsługującej połączenie z bazą
 
if (isset($_REQUEST['menu_button'])){                                           //Obsługa pierwszego przycisku wybierz w formularzy z trzema radio buttonami
    checkRadio();                                                               //Wywolanie funkcji checkRadio
}

if (isset($_REQUEST['pokaz_czytelnik'])){                                       //Obsługa przycisku pokaż statystyki dla czytelnika
    $_SESSION['statystyki_czytelnik'] = getLogin($_REQUEST['select_czytelnik']);//Utworzenie zmiennej sesyjnej ktorej przypisujemy wynik dzialania funkcji getLogin. 
                                                                                //Zmienna ta pozwala wydrukowac tresc strony ze statystykami wybranego czytelnika
}

if (isset($_REQUEST['pokaz_pracownik'])){                                       //Obsługa przycisku pokaż statystyki dla pracownika
    $_SESSION['statystyki_pracownik'] = getLogin($_REQUEST['select_pracownik']);//Utworzenie zmiennej sesyjnej ktorej przypisujemy wynik dzialania funkcji getLogin. 
                                                                                //Zmienna ta pozwala wydrukowac tresc strony ze statystykami wybranego pracownika
}

if (isset($_REQUEST['back'])){                                                  //Obsługa przycisku powrot
    if(isset($_SESSION['biblioteka'])){                                         //Sprawdzenie czy istnieje zmienna sesyjna biblioteka
        unset($_SESSION['biblioteka']);                                         //Jeśli istnieje zwolnij ją
    }
    if(isset($_SESSION['czytelnik'])){                                          //Sprawdzenie czy istnieje zmienna sesyjna czytelnik
        unset($_SESSION['czytelnik']);                                          //Jeśli istnieje zwolnij ją
    }
    if(isset($_SESSION['pracownik'])){                                          //Sprawdzenie czy istnieje zmienna sesyjna pracownik
        unset($_SESSION['pracownik']);                                          //Jeśli istnieje zwolnij ją
    }
    if(isset($_SESSION['statystyki_czytelnik'])){                               //Sprawdzenie czy istnieje zmienna sesyjna statystyki_czytelnik
        unset($_SESSION['statystyki_czytelnik']);                               //Jeśli istnieje zwolnij ją
    }
    if(isset($_SESSION['statystyki_pracownik'])){                               //Sprawdzenie czy istnieje zmienna sesyjna statystyki_pracownik
        unset($_SESSION['statystyki_pracownik']);                               //Jeśli istnieje zwolnij ją
    }
}

//Funkcja uzyskująca login wybranego uzytkowanika
function getLogin($id){
    $baza = new baza();
        
    $connect = $baza->connectDatabase();
    $query = "SELECT login FROM users_login WHERE ID ='".$id."'";
    if ($rezultat = $connect->query($query)){
        while ($row = $rezultat->fetch_object()) {
            $login = $row->login;
            $baza->disconnectDatabase($connect);
            return $login;
         }
     }
}

//Funkcja wypisująca treść strony statystyki
function printTresc(){
    $tresc = "";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' /><script src='js/whcookies.js'></script><title>Biblioteka - Statystyki</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Statystyki!</span></h1></div><div id='menu3'><ol>"
            . "<li><a class='button_menu2' href='user_page.php'>Strona Główna</a></li><li><a class='button_menu2' href='katalog_ksiazek.php'>Katalog książek</a></li>"
            . "<li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a><ul><li><a class='button_menu2' href='moje_ksiazki.php'>Moje książki</a></li>"
            . "<li><a class='button_menu2' href='moje_konto_page.php'>Moje konto</a></li></ul></li><li><a class='button_menu2' href='wypozycz_page.php'>Opcje pracownika</a>"
            . "<ul><li><a class='button_menu2' href='wypozycz_page.php'>Wypożycz/Oddaj</a></li><li><a class='button_menu2' href='dodaj_ksiazke_page.php'>Dodaj książkę</a></li>"
            . "</ul></li><li><a class='button_menu2' href='statystyki_page.php'>Opcje Właściciela</a><ul><li><a class='button_menu2' href='rejestracja.php'>Dodaj użytkownika</a></li>"
            . "<li><a class='button_menu2' href='statystyki_page.php'>Statystyki</a></li></ul></li><li><a class='button_menu2' href='wyloguj.php'>Wyloguj</a></li>"
            . "</ol></div><div id='tresc_div'><span class='tresc_span' id='tresc_katalog'>Tutaj możesz przeglądać statystyki Twojej biblioteki!</span>";
    if(!isset($_SESSION['biblioteka']) && !isset($_SESSION['czytelnik']) && !isset($_SESSION['pracownik']) && 
                   !isset($_SESSION['statystyki_czytelnik']) && !isset($_SESSION['statystyki_pracownik'])){
        $tresc .= "<div id='statystyki_div'><form method='post'><table class='statystyki_table'><tr><td>Wybierz jakie statystyki chcesz wyświetlić:</td>"
                . "<tr><td> <input type='radio' name='menu' value='biblioteka'>Biblioteki</td></tr>"
                . "<tr><td> <input type='radio' name='menu' value='czytelnik'>Czytelnika</td></tr>"
                . "<tr><td> <input type='radio' name='menu' value='pracownik'>Pracownika</td></tr>"
                . "<tr><td > <button class='button_wybierz' name='menu_button'>Wybierz</button></td></tr></table></form></div>";
    }
    if(isset($_SESSION['biblioteka'])){
        $tresc .= printStatystyki("biblioteka");
        unset($_SESSION['biblioteka']);
    }
    if(isset($_SESSION['czytelnik'])){
        $tresc .= printOptions("czytelnik");
        unset($_SESSION['czytelnik']);
    }
    if(isset($_SESSION['pracownik'])){
        $tresc .= printOptions("pracownik");
        unset($_SESSION['pracownik']);
    }
    if(isset($_SESSION['statystyki_czytelnik'])){
        $tresc .= printStatystyki("czytelnik");
        unset($_SESSION['statystyki_czytelnik']);
    }
    if(isset($_SESSION['statystyki_pracownik'])){
        $tresc .= printStatystyki("pracownik");
        unset($_SESSION['statystyki_pracownik']);
    }
    $tresc .= "</div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

//Funkcja obsługująca formularz z trzema radio buttonami
function checkRadio(){
    $radio_button = htmlspecialchars(trim($_REQUEST['menu']));
    
    switch ($radio_button){
        case "biblioteka":
            $_SESSION['biblioteka'] = "";
            break;
        case "czytelnik":
            $_SESSION['czytelnik'] = "";
            break;
        case "pracownik":
            $_SESSION['pracownik'] = "";
            break;
    }
}

//Funkcja wypisująca statystyki
function printStatystyki($opcja){
    switch ($opcja){
        case "biblioteka":
            $tresc = "<div id='formularz_div'><form method='post'><table class='statystyki_biblioteka_table'>";
            $tresc .= "<tr><td colspan='2' class='statystyki_td_naglowek'>Statystyki biblioteki:</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość zarejestrowanych czytelników:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("zarejestrowaniCzytelnicy"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość zarejestrowanych pracowników:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("zarejestrowaniPracownicy"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość zarejestrowanych właścicieli:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("zarejestrowaniWlasciciele"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość wszystkich książek w bibliotece:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("iloscKsiazek"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość zarezerwowanych książek w bibliotece:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("iloscZarezerwowanych"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość wypożyczonych książek w bibliotece:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("iloscWypozyczonych"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość wszystkich rezerwacji książek w bibliotece:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("iloscRezerwacji"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość wszystkich wypożyczeń książek w bibliotece:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("iloscWypozyczen"))."</td></tr>";
            $tresc .= "<tr><td></td><td class='formularz_td_button'><button class='formularz_button_back' name='back'>Powrót</button></td></tr>";
            $tresc .= "</table></form></div>";    
            return $tresc;
        case "czytelnik":
            $tresc = "<div id='formularz_div'><form method='post'><table class='statystyki_biblioteka_table'>";
            $tresc .= "<tr><td colspan='2' class='statystyki_td_naglowek'>Statystyki czytelnika ".$_SESSION['statystyki_czytelnik'].":</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość wypożyczeń:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("czytelnikIloscWypozyczen"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość rezerwacji:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("czytelnikIloscRezerwacji"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość anulowanych rezerwacji przez czytelnika:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("czytelnikIloscAnulowanychRezerwacji"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość anulowanych rezerwacji przez pracownika:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("czytelnikIloscAnulowanychRezerwacjiPracownik"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość rezerwacji które upłyneły:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("czytelnikIloscUplynietychRezerwacji"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość książek aktualnie wypożyczonych:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("czytelnikWypozyczone"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość książek aktualnie zarezerwowanych:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("czytelnikZarezerwowane"))."</td></tr>";
            $tresc .= "<tr><td></td><td class='formularz_td_button'><button class='formularz_button_back' name='back'>Powrót</button></td></tr>";
            $tresc .= "</table></form></div>";
            return $tresc;
        case "pracownik":
            $tresc = "<div id='formularz_div'><form method='post'><table class='statystyki_biblioteka_table'>";
            $tresc .= "<tr><td colspan='2' class='statystyki_td_naglowek'>Statystyki pracownika ".$_SESSION['statystyki_pracownik'].":</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość książek wypożyczonych:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("pracownikWypozyczone"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość książek oddanych:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("pracownikOddane"))."</td></tr>";
            $tresc .= "<tr><td class='statystyki_td'>Ilość anulowanych rezerwacji:</td>";
            $tresc .= "<td class='statystyki_td'>". select(query("pracownikAnulowane"))."</td></tr>";
            $tresc .= "<tr><td></td><td class='formularz_td_button'><button class='formularz_button_back' name='back'>Powrót</button></td></tr>";
            $tresc .= "</table></form></div>";
            return $tresc;
    }
    
}

//Funckja wysylajaca zapytania do bazy danych. Funkcja zwraca wartosc w zaleznosci od zapytania
function select($query){
    $baza = new baza();
        
    $connect = $baza->connectDatabase();
    if ($rezultat = $connect->query($query)){
        while ($row = $rezultat->fetch_object()) {
            $ile = $row->ilosc;
            $baza->disconnectDatabase($connect);
            return $ile;
         }
     }
}

//Funkcja zawieracja skladnie zapytan bo bazy danych
function query($pole){
    switch ($pole){
        case "zarejestrowaniCzytelnicy":
            $query="SELECT COUNT(users_login.ID) AS ilosc FROM users_login WHERE users_login.typ_konta=1 ORDER BY users_login.ID";
            return $query;
        case "zarejestrowaniPracownicy":
            $query="SELECT COUNT(users_login.ID) AS ilosc FROM users_login WHERE users_login.typ_konta=2 ORDER BY users_login.ID";
            return $query;
        case "zarejestrowaniWlasciciele":
            $query="SELECT COUNT(users_login.ID) AS ilosc FROM users_login WHERE users_login.typ_konta=3 ORDER BY users_login.ID";
            return $query;
        case "iloscKsiazek":
            $query="SELECT COUNT(ksiazki.ID) AS ilosc FROM ksiazki ORDER BY ksiazki.ID";
            return $query;
        case "iloscZarezerwowanych":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje WHERE rezerwacje.data_zakonczenia IS NULL ORDER BY rezerwacje.ID";
            return $query;
        case "iloscWypozyczonych":
            $query="SELECT COUNT(wypozyczenia.ID) AS ilosc FROM wypozyczenia WHERE wypozyczenia.data_oddania IS NULL ORDER BY wypozyczenia.ID";
            return $query;
        case "iloscRezerwacji":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje ORDER BY rezerwacje.ID";
            return $query;
        case "iloscWypozyczen":
            $query="SELECT COUNT(wypozyczenia.ID) AS ilosc FROM wypozyczenia ORDER BY wypozyczenia.ID";
            return $query;
        case "czytelnikIloscWypozyczen":
            $query="SELECT COUNT(wypozyczenia.ID) AS ilosc FROM wypozyczenia WHERE ID_users='".$_REQUEST['select_czytelnik']."' ORDER BY wypozyczenia.ID";
            return $query;
        case "czytelnikIloscRezerwacji":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje WHERE ID_users='".$_REQUEST['select_czytelnik']."' ORDER BY rezerwacje.ID";
            return $query;
        case "czytelnikIloscAnulowanychRezerwacji":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje WHERE ID_users='".$_REQUEST['select_czytelnik']."' "
                . "AND uwagi='Czytelnik anulował' ORDER BY rezerwacje.ID";
            return $query;
        case "czytelnikIloscAnulowanychRezerwacjiPracownik":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje WHERE ID_users='".$_REQUEST['select_czytelnik']."' "
                . "AND uwagi='Pracownik anulował' ORDER BY rezerwacje.ID";
            return $query;
        case "czytelnikIloscUplynietychRezerwacji":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje WHERE ID_users='".$_REQUEST['select_czytelnik']."' "
                . "AND uwagi='Rezerwacja minęła' ORDER BY rezerwacje.ID";
            return $query;
        case "czytelnikWypozyczone":
            $query="SELECT COUNT(wypozyczenia.ID) AS ilosc FROM wypozyczenia WHERE ID_users='".$_REQUEST['select_czytelnik']."' AND "
                . "data_oddania IS NULL ORDER BY wypozyczenia.ID";
            return $query;
        case "czytelnikZarezerwowane":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje WHERE ID_users='".$_REQUEST['select_czytelnik']."' AND "
                . "data_zakonczenia IS NULL ORDER BY rezerwacje.ID";
            return $query;
        case "pracownikWypozyczone":
            $query="SELECT COUNT(wypozyczenia.ID) AS ilosc FROM wypozyczenia WHERE wypozyczenia.ID_pracownika_wypozyczenie='".$_REQUEST['select_pracownik']."'";
            return $query;
        case "pracownikOddane":
            $query="SELECT COUNT(wypozyczenia.ID) AS ilosc FROM wypozyczenia WHERE wypozyczenia.ID_pracownika_oddanie='".$_REQUEST['select_pracownik']."'";
            return $query;
        case "pracownikAnulowane":
            $query="SELECT COUNT(rezerwacje.ID) AS ilosc FROM rezerwacje WHERE rezerwacje.ID_pracownika='".$_REQUEST['select_pracownik']."' AND "
                . "rezerwacje.uwagi='Pracownik anulował'";
            return $query;
            
    }
}

//Funkcja wypisujaca tresc strony do wyboru czytelnika z bazy
function printOptions($opcja){
    $tresc = "";
    $tresc .= "<div id='statystyki_div'>";
    $tresc .= "<form method='post'>";
    $tresc .= "<table class='statystyki_table'><tr><td colspan='3'>Wybierz ";
    if($opcja == "czytelnik"){
        $tresc .= "czytelnika ";
    }
    else{
        $tresc .= "pracownika ";
    }
    $tresc .= "którego dane chcesz wyświetlić</td>";
    $tresc .= "<tr><td class='formularz_td_text'>Login:</td><td class='formularz_td_input'>";
    if($opcja == "czytelnik"){
        $tresc .= "<select name='select_czytelnik'>";
    }
    else{
        $tresc .= "<select name='select_pracownik'>";
    }

    $baza = new baza();
        
    $connect = $baza->connectDatabase();
    if($opcja == "czytelnik"){
        $query = "SELECT ID,login FROM users_login";
    }
    else{
        $query = "SELECT ID,login FROM users_login WHERE typ_konta = '2' OR typ_konta= '3'";
    }
    
    if ($rezultat = $connect->query($query)){
                //Poprawne zapytanie do bazy
            
        while ($row = $rezultat->fetch_object()) {
            $tresc.="<option value='".$row->ID."'";
                $tresc.=">".$row->login."</option>";
            }
        $rezultat->close();
    }
    else {
        //Błąd w zapytaniu do bazy
        $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
        $baza->disconnectDatabase($connect);
    }
    $tresc .= "</select></td>";
    $tresc .= "<td class='formularz_td_input'><button class='button_pokaz_statystyki' ";
    if($opcja == "czytelnik"){
        $tresc .= "name='pokaz_czytelnik'";
    }
    else{
        $tresc .= "name='pokaz_pracownik'";
    }
    $tresc .= ">Pokaż statystyki</button></td></tr>";
    $tresc .= "<tr><td></td><td class='formularz_td_button'><button class='formularz_button_back' name='back'>Powrót</button></td></tr>";
    $baza->disconnectDatabase($connect);
    $tresc .= "</table></form></div>";
    return $tresc;
}

