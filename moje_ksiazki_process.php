<?php

if(!(isset($_SESSION['moje_ksiazki_process']))){    //Sprawdzenie czy NIE istnieje zmienna sesyjna moje_ksiazki_process
    header('Location: index.php');                  //Jeśli nie istnieje przenieś na index.php
    exit();                                         //exit
}
else{
    unset($_SESSION['moje_ksiazki_process']);       //Jeśli istnieje usuń zmienną sesyjną
}

$_SESSION['baza'] = "";                             //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku baza.php
require_once 'baza.php';                            //Dołączenie klasy obsługującej połączenie z bazą

if (isset($_REQUEST['anuluj'])){                                            //Obsługa przycisku anuluj rezerwację
    if (anuluj_rezerwacje(htmlspecialchars(trim($_REQUEST['anuluj'])))){    //Wywołanie funkcji anuluj rezerwację z parametrem ID rezerwacji
        $_SESSION['anulowano'] = "";                                        //Jeśli funkcja zwróciła TRUE utworz zmienna sesyjna informujaca ze udalo sie anulowac
        header('Location: moje_ksiazki.php');                               //Powrót na stronę moje_ksiazki.php
        exit();                                                             //exit
    }
    else{
        header('Location: moje_ksiazki.php');                               //Jeśli funkcja zwróciła False powrót na stronę moje_ksiazki.php
        exit();                                                             //exit
    }
}

//Funkcja obsługujaca przysick anuluj rezerwację przez osobę która złożyła rezerwację
function anuluj_rezerwacje($id){
    $status = FALSE;
    $baza = new baza();    
    $connect = $baza->connectDatabase();
    if($rezultat = $connect->query(sprintf("SELECT rezerwacje.ID_ksiazki FROM rezerwacje WHERE ID='".$id."'"))){
        while ($row = $rezultat->fetch_object()) {
            $id_ksiazki = $row->ID_ksiazki;
        }
        if($connect->query(sprintf("UPDATE rezerwacje SET rezerwacje.data_zakonczenia='".date("Y-m-d H:i:s")."', rezerwacje.uwagi='Czytelnik anulował' WHERE ID='%s'",mysqli_real_escape_string($connect,$id)))){
            $query = "UPDATE ksiazki SET ksiazki.wypozyczona='0' WHERE ksiazki.ID='".$id_ksiazki."'";
            if($connect->query($query)){
                $status = TRUE;
                $baza->disconnectDatabase($connect);
                return $status;
            }
        }  
    }
    $baza->disconnectDatabase($connect);
    return $status;
}


//Funkcja wypisująca tabelę z książkami wypożyczynymi

function printWypozyczone(){
    $tresc = "";   
    $baza = new baza();
    $id = $_SESSION['id_user'];    
    $connect = $baza->connectDatabase();
        
    $query = "SELECT autorzy.imie, autorzy.nazwisko, tab1.tytul, tab1.ID, tab1.data_wypozyczenia "
            ."FROM (SELECT ksiazki.ID_autorzy, ksiazki.tytul, wypozyczenia.ID, wypozyczenia.data_wypozyczenia "
            ."FROM ksiazki INNER JOIN wypozyczenia ON ksiazki.ID=wypozyczenia.ID_ksiazki "
            ."WHERE wypozyczenia.ID_users=".$id." AND wypozyczenia.data_oddania IS NULL)tab1 LEFT JOIN autorzy ON tab1.ID_autorzy=autorzy.ID";
    
    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        while ($row = $rezultat->fetch_object()) {
            $tresc.="<tr>";
            for ($i =0; $i < 4; $i++) {
                switch ($i){
                    case 0:
                        $tresc .= "<td class='ksiazki_td'>".$row->imie." ".$row->nazwisko."</td>";
                        break;
                    case 1:
                        $tresc .= "<td class='ksiazki_td'>".$row->tytul."</td>";
                        break;
                    case 2:
                        $tresc .= "<td class='ksiazki_td'>".$row->data_wypozyczenia."</td>";
                        break;
                }   
            }
            $tresc .= "</tr>";
        }
        $rezultat->close();
    }
    else {
        //Błąd w zapytaniu do bazy
        $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
        $baza->disconnectDatabase($connect);
    }
     $baza->disconnectDatabase($connect);
     return $tresc;
}

//Funckcja wypisująca tabelę z książkami zarezerwowanymi
function printZarezerwowane(){
    $tresc = "";   
    $baza = new baza();
    $id = $_SESSION['id_user'];    
    $connect = $baza->connectDatabase();
        
    $query = "SELECT autorzy.imie, autorzy.nazwisko, tab1.tytul, tab1.data_rezerwacji, tab1.ID "
            ."FROM (SELECT ksiazki.ID_autorzy, ksiazki.tytul, rezerwacje.data_rezerwacji, rezerwacje.ID "
            ."FROM ksiazki INNER JOIN rezerwacje ON ksiazki.ID=rezerwacje.ID_ksiazki "
            ."WHERE rezerwacje.ID_users=".$id." AND ADDDATE(rezerwacje.data_rezerwacji, INTERVAL 3 DAY) >= now() AND "
            . "rezerwacje.data_zakonczenia IS NULL)tab1 LEFT JOIN autorzy ON tab1.ID_autorzy=autorzy.ID";
    
    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        while ($row = $rezultat->fetch_object()) {
            $tresc.="<tr>";
            for ($i =0; $i < 4; $i++) {
                switch ($i){
                    case 0:
                        $tresc .= "<td class='ksiazki_td'>".$row->imie." ".$row->nazwisko."</td>";
                        break;
                    case 1:
                        $tresc .= "<td class='ksiazki_td'>".$row->tytul."</td>";
                        break;
                    case 2:
                        $date = date( "Y-m-d H:i:s", strtotime( $row->data_rezerwacji .' +3 day' ));
                        $tresc .= "<td class='ksiazki_td'>";
                        if((strtotime($date)-strtotime(date("Y-m-d H:i:s")))/86400 <= 1 ){
                            $tresc .= "<span class='error'>".$date."</span></td>";
                        }
                        else{
                        $tresc .= $date."</td>";
                        }
                        break;
                    case 3:
                        $tresc .= "<td class='ksiazki_td'> <button name='anuluj' type='submit' value='".$row->ID."'>Anuluj rezerwację</button></td>";
                }   
            }
            $tresc .= "</tr>";
        }
        $rezultat->close();
    }
    else {
        //Błąd w zapytaniu do bazy
        $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
        $baza->disconnectDatabase($connect);
    }
     $baza->disconnectDatabase($connect);
     return $tresc;
}

//Funkcja wypisująca treść strony moje_ksiazki.php
function printTresc(){
    $tresc ="";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' />"
            . "<script src='js/whcookies.js'></script><title>Biblioteka - Moje książki</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Moje książki</span></h1></div>";
    switch ($_SESSION['typ_konta']){
        case 1:
            $tresc .= "<div id='menu1'>";
            $tresc .= "<a class='button_menu1' href='user_page.php'>Strona Główna</a>";
            $tresc .= "<a class='button_menu1' href='katalog_ksiazek.php'>Katalog książek</a>";
            $tresc .= "<a class='button_menu1' href='moje_ksiazki.php'>Moje książki</a>";
            $tresc .= "<a class='button_menu1' href='moje_konto_page.php'>Moje konto</a>";
            $tresc .= "<a class='button_menu1' href='wyloguj.php'>Wyloguj</a>";
            break;
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
    $tresc .= "</div><div id='tresc_div'><span class='tresc_span'>";
    if(!isset($_SESSION['anulowano']) && !isset($_SESSION['zarezerwowano'])){
        $tresc .= 'Twoje wypożyczone i zarezerwowane książki.</span>';
    }
    if (isset($_SESSION['zarezerwowano'])) {
        $tresc .= "Poprawnie zarezerwowano książkę! Masz teraz 3 dni na odebranie jej w bibliotece.<br><br>Twoje wypożyczone i zarezerwowane książki.</span>";
        unset ($_SESSION['zarezerwowano']);
    }
    if (isset($_SESSION['anulowano'])) {
        $tresc .= "Poprawnie anulowano rezerwację książki.<br><br>Twoje wypożyczone i zarezerwowane książki.</span>";
        unset ($_SESSION['anulowano']);
    }
    $tresc .= "<div id='formularz_div'><form><table class='zarezerwowane_table'><tr><td colspan='4' class='ksiazki_td_naglowek'>Zarezerwowane książki:</td>"
            . "</tr><tr><td class='ksiazki_td_naglowek'>Autor</td><td class='ksiazki_td_naglowek'>Tytuł</td>"
            . "<td class='ksiazki_td_naglowek'>Pozostały czas rezerwacji</td><td class='ksiazki_td_naglowek'>Akcja</td></tr>"
            . printZarezerwowane()."</table><table class='wypozyczone_table'><tr><td colspan='3' class='ksiazki_td_naglowek'>Wypozyczone książki:</td>"
            . "</tr><tr><td class='ksiazki_td_naglowek'>Autor</td><td class='ksiazki_td_naglowek'>Tytuł</td><td class='ksiazki_td_naglowek'>Data wypożyczenia</td></tr>"
            . printWypozyczone()."</table></form>";
    if (isset($_SESSION['blad_select'])) {
        $tresc .= $_SESSION['blad_select'];
        unset ($_SESSION['blad_select']);
    }
    $tresc .= "</div></div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

