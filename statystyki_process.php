<?php
$_SESSION['baza'] = "";
require_once 'baza.php';

if(!(isset($_SESSION['statystyki_process']))){
    header('Location: index.php');
    exit();
}
else{
    unset($_SESSION['statystyki_process']);
}

//Obsługa pierwszego przycisku wybierz w formularzy z trzema radio buttonami 
if (isset($_REQUEST['menu_button'])){
    checkRadio();
}

//Obsługa przycisku pokaż statystyki dla czytelnika
if (isset($_REQUEST['pokaz_czytelnik'])){
    $_SESSION['statystyki_czytelnik'] = getLogin($_REQUEST['select_czytelnik']);
}

//Obsługa przycisku pokaż statystyki dla pracownika
if (isset($_REQUEST['pokaz_pracownik'])){
    $_SESSION['statystyki_pracownik'] = getLogin($_REQUEST['select_pracownik']);
}

//Obsługa przycisku powrot
if (isset($_REQUEST['back'])){
    if(isset($_SESSION['biblioteka'])){
        unset($_SESSION['biblioteka']);
    }
    if(isset($_SESSION['czytelnik'])){
        unset($_SESSION['czytelnik']);
    }
    if(isset($_SESSION['pracownik'])){
        unset($_SESSION['pracownik']);
    }
    if(isset($_SESSION['statystyki_czytelnik'])){
        unset($_SESSION['statystyki_czytelnik']);
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
    $tresc .= "<div id='statystyki_div'>";
    $tresc .= "<form method='post'>";
    $tresc .= "<table class='statystyki_table'><tr><td>Wybierz jakie statystyki chcesz wyświetlić:</td>";
    $tresc .= "<tr><td> <input type='radio' name='menu' value='biblioteka'>Biblioteki</td></tr>";
    $tresc .= "<tr><td> <input type='radio' name='menu' value='czytelnik'>Czytelników</td></tr>";
    $tresc .= "<tr><td> <input type='radio' name='menu' value='pracownik'>Pracowników</td></tr>";
    $tresc .= "<tr><td > <button class='button_wybierz' name='menu_button'>Wybierz</button></td></tr>";
    $tresc .= "</table></form></div>";
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

//Funkcja wypisująca tresc strony ze statystykami biblioteki
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
            echo $tresc;
            break;
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
            echo $tresc;
            break;
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
            echo $tresc;
            break;
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
    echo $tresc;
}

