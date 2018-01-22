<?php
//Plik obsługujący stronę wypozycz_page

if(!(isset($_SESSION['wypozycz_process']))){                            //Sprawdzenie czy NIE istnieje zmienna sesyjna wypozycz_process
    header('Location: index.php');                                      //Jeśli nie istnieje przenieś na index.php
    exit();                                                             //exit
}
else{
    unset($_SESSION['wypozycz_process']);                               //Jeśli istnieje usuń zmienną sesyjną
}

$_SESSION['baza'] = "";                                                 //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku baza.php
require_once 'baza.php';                                                //Dołączenie klasy obsługującej połączenie z bazą

if (isset($_REQUEST['wypozycz'])){                                      //Obsługa przycisku wypożycz
    if (wypozycz(htmlspecialchars(trim($_REQUEST['wypozycz'])))){       //Wywolanie funkcji wypozycz z paremetrem ID_rezerwacji
        $_SESSION['pracownik_wypozyczono'] = "";                        //Jeśli funkcja wypozycz zwroci TRUE utworz zmienna sesyjna informujaca o wypozyczeniu ksiazki
        header('Location: wypozycz_page.php');                          //Powrot na strone wypozycz_page.php
        exit();                                                         //exit
    }
    else{
        header('Location: wypozycz_page.php');                          //Jeśli funkcja wypozycz zwroci FALSE, powrot na strone wypozycz_page.php
        exit();                                                         //exit
    }
}

if (isset($_REQUEST['anuluj_minelo'])){                                 //Obsługa przycisku anuluj rezerwację, gdy rezerwacja minęła
    if (anuluj(htmlspecialchars(trim($_REQUEST['anuluj_minelo'])),"Rezerwacja minęła")){    //Wywolanie funkcji anuluj z parametrem ID_rezerwacji oraz z trescia uwag
        $_SESSION['pracownik_anulowano'] = "";                          //Jeśli funkcja anuluj zwroci TRUE utworz zmienna sesyjna informujaca o anulowaniu rezerwacji
        header('Location: wypozycz_page.php');                          //Powrot na strone wypozycz_page.php
        exit();                                                         //exit
    }  
    else{
        header('Location: wypozycz_page.php');                          //Jeśli funkcja anuluj zwroci FALSE, powrot na strone wypozycz_page.php
        exit();                                                         //exit
    }
}

if (isset($_REQUEST['anuluj'])){                                        //Obsługa przycisku anuluj rezerwację, gdy rezerwacja NIE minęła
    if (anuluj(htmlspecialchars(trim($_REQUEST['anuluj'])),"Pracownik anulował")){  //Wywolanie funkcji anuluj z parametrem ID_rezerwacji oraz z trescia uwag
        $_SESSION['pracownik_anulowano'] = "";                          //Jeśli funkcja anuluj zwroci TRUE utworz zmienna sesyjna informujaca o anulowaniu rezerwacji
        header('Location: wypozycz_page.php');                          //Powrot na strone wypozycz_page.php
        exit();                                                         //exit
    } 
    else{
        header('Location: wypozycz_page.php');                          //Jeśli funkcja anuluj zwroci FALSE, powrot na strone wypozycz_page.php
        exit();                                                         //exit
    }
}

if (isset($_REQUEST['oddaj'])){                                         //Obsługa przycisku oddaj
    if (oddaj(htmlspecialchars(trim($_REQUEST['oddaj'])))){             //Wywolanie funkcji oddaj z parametrem ID_wypozyczenia
        $_SESSION['pracownik_oddano'] = "";                             //Jeśli funkcja oddaj zwroci TRUE utworz zmienna sesyjna informujaca o oddaniu ksiazki
        header('Location: wypozycz_page.php');                          //Powrot na strone wypozycz_page.php
        exit();                                                         //exit
    } 
    else{
        header('Location: wypozycz_page.php');                          //Jeśli funkcja oddaj zwroci FALSE, powrot na strone wypozycz_page.php
        exit();                                                         //exit
    }
}

//Funkcja drukujaca tresc strony wypozycz_page
function printTresc(){
    $tresc ="";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' />"
            . "<script src='js/whcookies.js'></script><title>Biblioteka - Wypożycz</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Wypożycz książki!</span></h1></div>";
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
    $tresc .= "</div><div id='tresc_div'><span class='tresc_span' id='tresc_katalog'>";
    if (isset($_SESSION['pracownik_wypozyczono'])){
        $tresc .= 'Udało się wypożyczyć książkę!<br>';
        unset($_SESSION['pracownik_wypozyczono']);
    }
                    
     if (isset($_SESSION['pracownik_oddano'])){
        $tresc .= 'Udało się oddać książkę!<br>';
        unset($_SESSION['pracownik_oddano']);
    }
                    
    if (isset($_SESSION['pracownik_anulowano'])){
        $tresc .= 'Udało się anulować rezerwację książki!<br>';
        unset($_SESSION['pracownik_anulowano']);
    }
    $tresc .= "Możesz tutaj przeglądać książki które czytelnicy zarezerwowali i wypożyczyli:"
            . "</span><div id='formularz_div'><form><table class='do_wypozyczenia_table'><tr>"
            . "<td colspan='5' class='ksiazki_td_naglowek'>Zarezerwowane książki:</td></tr><tr>"
            . "<td class='ksiazki_td_naglowek'>Login</td><td class='ksiazki_td_naglowek'>Autor</td>"
            . "<td class='ksiazki_td_naglowek'>Tytuł</td><td class='ksiazki_td_naglowek'>Data rezerwacji</td>"
            . "<td class='ksiazki_td_naglowek'>Akcja</td></tr>".printZarezerwowane("TRUE")."</table><br><table class='do_wypozyczenia_table'>"
            . "<tr><td colspan='5' class='ksiazki_td_naglowek'>Zarezerwowane książki których rezerwacja upłynęła:</td>"
            . "</tr><tr><td class='ksiazki_td_naglowek'>Login</td><td class='ksiazki_td_naglowek'>Autor</td>"
            . "<td class='ksiazki_td_naglowek'>Tytuł</td><td class='ksiazki_td_naglowek'>Data rezerwacji</td>"
            . "<td class='ksiazki_td_naglowek'>Akcja</td></tr>".printZarezerwowane("FALSE")."</table><br><table class='wypozyczone_table_pracownik'>"
            . "<tr><td colspan='5' class='ksiazki_td_naglowek'>Wypożyczone książki:</td></tr><tr><td class='ksiazki_td_naglowek'>Login</td>"
            . "<td class='ksiazki_td_naglowek'>Autor</td><td class='ksiazki_td_naglowek'>Tytuł</td>"
            . "<td class='ksiazki_td_naglowek'>Data rezerwacji</td><td class='ksiazki_td_naglowek'>Akcja</td></tr>".printWypozyczone();
    if (isset($_SESSION['blad_select'])){
        $tresc .= $_SESSION['blad_select'];
        unset($_SESSION['blad_select']);
    }
    $tresc .= "</table></form></div></div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

//Funkcja obslugujaca oddanie ksiazki
function oddaj($id_wypozyczenia){
    $status = FALSE;
    $baza = new baza();    
    $connect = $baza->connectDatabase();
    $id_pracownika = $_SESSION['id_user'];
    if($rezultat = $connect->query(sprintf("SELECT wypozyczenia.ID_ksiazki FROM wypozyczenia WHERE ID='".$id_wypozyczenia."'"))){
        while ($row = $rezultat->fetch_object()) {
            $id_ksiazki = $row->ID_ksiazki;
        }
        if($connect->query(sprintf("UPDATE wypozyczenia SET wypozyczenia.data_oddania='".date("Y-m-d H:i:s")."', wypozyczenia.ID_pracownika_oddanie='"
                .$id_pracownika."' WHERE ID='%s'",mysqli_real_escape_string($connect,$id_wypozyczenia)))){
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

//Funkcja obslugujaca anulownie rezerwacji, która się skończyła
function anuluj($id_rezerwacji, $tresc){
    $status = FALSE;
    $baza = new baza();    
    $connect = $baza->connectDatabase();
    $id_pracownika = $_SESSION['id_user'];
    if($rezultat = $connect->query(sprintf("SELECT rezerwacje.ID_ksiazki FROM rezerwacje WHERE ID='".$id_rezerwacji."'"))){
        while ($row = $rezultat->fetch_object()) {
            $id_ksiazki = $row->ID_ksiazki;
        }
        if($connect->query(sprintf("UPDATE rezerwacje SET rezerwacje.data_zakonczenia='".date("Y-m-d H:i:s")."', rezerwacje.uwagi='".$tresc."', "
                . " rezerwacje.ID_pracownika='".$id_pracownika."' WHERE rezerwacje.ID = '".$id_rezerwacji."'"))){
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

//Funkcja obslugujaca wypozyczenie ksiazki
function wypozycz($id_rezerwacji){
    $status = FALSE;
    $baza = new baza();    
    $connect = $baza->connectDatabase();
    $query = "SELECT * FROM rezerwacje WHERE rezerwacje.ID=".$id_rezerwacji;
    
    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        while ($row = $rezultat->fetch_object()) {
            $id_ksiazki = $row->ID_ksiazki;
            $id_user = $row->ID_users;
        }
        $id_pracownika = $_SESSION['id_user'];
        $query = sprintf("INSERT INTO wypozyczenia (ID_ksiazki, ID_users, ID_pracownika_wypozyczenie, data_wypozyczenia) VALUES ('%s', '%s', '%s', '%s');",
                    mysqli_real_escape_string($connect,$id_ksiazki),
                    mysqli_real_escape_string($connect,$id_user),
                    mysqli_real_escape_string($connect,$id_pracownika),
                    mysqli_real_escape_string($connect,date("Y-m-d H:i:s")));
         if ($rezultat = $connect->query($query)){
            //Poprawne zapytanie do bazyWHERE ID='%s'",mysqli_real_escape_string($connect,$id)"DELETE FROM rezerwacje 
            $query = "UPDATE rezerwacje SET rezerwacje.data_zakonczenia='".date("Y-m-d H:i:s")."', rezerwacje.uwagi='Wypożyczono',"
                    . "rezerwacje.ID_pracownika='".$id_pracownika."' WHERE rezerwacje.ID = '".$id_rezerwacji."'";
            if ($rezultat = $connect->query($query)){
                //Poprawne zapytanie do bazy
                    $baza->disconnectDatabase($connect);
                    $status = TRUE;
                    return $status;
            }
        }
        
    }
    $baza->disconnectDatabase($connect);
    return $status;
}

//Funkcja wypisująca tabele z zarezerwowanymi książkami możliwymi do wypożyczenia
function printZarezerwowane($option){
    $tresc = "";   
    $baza = new baza();    
    $connect = $baza->connectDatabase();
    if($option == "TRUE"){
        $query = "SELECT tab2.ID, users_login.login, tab2.imie, tab2.nazwisko, tab2.tytul, tab2.data_rezerwacji FROM("
            . "SELECT tab1.ID, tab1.ID_users, autorzy.imie, autorzy.nazwisko, tab1.tytul, tab1.data_rezerwacji FROM ("
            . "SELECT rezerwacje.ID, rezerwacje.ID_users, ksiazki.ID_autorzy, ksiazki.tytul, rezerwacje.data_rezerwacji FROM "
            . "ksiazki INNER JOIN rezerwacje ON ksiazki.ID=rezerwacje.ID_ksiazki WHERE "
            . "ADDDATE(rezerwacje.data_rezerwacji, INTERVAL 3 DAY) >= now() AND rezerwacje.data_zakonczenia IS NULL)tab1 "
            . "LEFT JOIN autorzy ON tab1.ID_autorzy=autorzy.ID)tab2 INNER JOIN users_login ON tab2.ID_users=users_login.ID";
    }
    else{
        $query = "SELECT tab2.ID, users_login.login, tab2.imie, tab2.nazwisko, tab2.tytul, tab2.data_rezerwacji FROM("
            . "SELECT tab1.ID, tab1.ID_users, autorzy.imie, autorzy.nazwisko, tab1.tytul, tab1.data_rezerwacji FROM ("
            . "SELECT rezerwacje.ID, rezerwacje.ID_users, ksiazki.ID_autorzy, ksiazki.tytul, rezerwacje.data_rezerwacji FROM "
            . "ksiazki INNER JOIN rezerwacje ON ksiazki.ID=rezerwacje.ID_ksiazki WHERE "
            . "ADDDATE(rezerwacje.data_rezerwacji, INTERVAL 3 DAY) < now() AND rezerwacje.data_zakonczenia IS NULL)tab1 "
            . "LEFT JOIN autorzy ON tab1.ID_autorzy=autorzy.ID)tab2 INNER JOIN users_login ON tab2.ID_users=users_login.ID";
    }

    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        while ($row = $rezultat->fetch_object()) {
            $tresc.="<tr>";
            for ($i =0; $i < 5; $i++) {
                switch ($i){
                    case 0:
                        $tresc .= "<td class='ksiazki_td'>".$row->login."</td>";
                        break;
                    case 1:
                        $tresc .= "<td class='ksiazki_td'>".$row->imie." ".$row->nazwisko."</td>";
                        break;
                    case 2:
                        $tresc .= "<td class='ksiazki_td'>".$row->tytul."</td>";
                        break;
                    case 3:
                        $tresc .= "<td class='ksiazki_td'>".$row->data_rezerwacji."</td>";
                        break;
                    case 4:
                        if($option == "TRUE"){
                            $tresc .= "<td class='ksiazki_td'> <button name='wypozycz' type='submit' value='".$row->ID."'>Wypożycz książkę</button>";
                            $tresc .= "<button name='anuluj' type='submit' value='".$row->ID."'>Anuluj rezerwację</button></td>";
                        }
                        else{
                            $tresc .= "<td class='ksiazki_td'> <button name='anuluj_minelo' type='submit' value='".$row->ID."'>Anuluj rezerwację</button></td>";
                        }
                        
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



//Funkcja wypisująca tabele z wypożyczynymi książkami
function printWypozyczone(){
    $tresc = "";   
    $baza = new baza();    
    $connect = $baza->connectDatabase();
        $query = "SELECT tab2.ID, users_login.login, tab2.imie, tab2.nazwisko, tab2.tytul, tab2.data_wypozyczenia FROM("
            . "SELECT tab1.ID, tab1.ID_users, autorzy.imie, autorzy.nazwisko, tab1.tytul, tab1.data_wypozyczenia FROM ("
            . "SELECT wypozyczenia.ID, wypozyczenia.ID_users, ksiazki.ID_autorzy, ksiazki.tytul, wypozyczenia.data_wypozyczenia FROM "
            . "ksiazki INNER JOIN wypozyczenia ON ksiazki.ID=wypozyczenia.ID_ksiazki WHERE "
            . "wypozyczenia.data_oddania IS NULL)tab1 LEFT JOIN autorzy ON tab1.ID_autorzy=autorzy.ID)tab2 "
            . "INNER JOIN users_login ON tab2.ID_users=users_login.ID";

    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        while ($row = $rezultat->fetch_object()) {
            $tresc.="<tr>";
            for ($i =0; $i < 5; $i++) {
                switch ($i){
                    case 0:
                        $tresc .= "<td class='ksiazki_td'>".$row->login."</td>";
                        break;
                    case 1:
                        $tresc .= "<td class='ksiazki_td'>".$row->imie." ".$row->nazwisko."</td>";
                        break;
                    case 2:
                        $tresc .= "<td class='ksiazki_td'>".$row->tytul."</td>";
                        break;
                    case 3:
                        $tresc .= "<td class='ksiazki_td'>".$row->data_wypozyczenia."</td>";
                        break;
                    case 4:
                            $tresc .= "<td class='ksiazki_td'> <button name='oddaj' type='submit' value='".$row->ID."'>Oddaj książkę</button></td>";       
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