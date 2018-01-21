<?php
//Plik obsługujący stronę katalog książek

if(!isset($_SESSION)){                          //Sprawdzenie czy sesja nie istnieje
    session_start();                            //Jeśli nie istenieje Start sesji
}

if(!(isset($_SESSION['katalog_process']))){     //Sprawdzenie czy NIE istnieje zmienna sesyjna katalog_process
    header('Location: index.php');              //Jeśli nie istnieje przenieś na index.php
    exit();                                     //exit
}
else{                                           
    unset($_SESSION['katalog_process']);        //Jeśli istnieje usuń zmienną sesyjną
}

$_SESSION['baza'] = "";                         //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku baza.php
require_once 'baza.php';                        //Dołączenie klasy obsługującej połączenie z bazą

$_SESSION['userLogin'] = "";                    //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku userLogin.php
require 'userLogin.php';                        //Dołączenie klasy reprezentującej użytkownika podczas logowania    

if (isset($_REQUEST['pokaz_ksiazki'])){                                 //Obsługa przycisku pokaż książki w katalogu książek
    $_SESSION['select_autor'] = $_REQUEST['select_autor'];              //Zmiennej sesyjnej przypisz wartosc ID wybranego autora
    $_SESSION['table_ksiazki']=printBook($_SESSION['select_autor']);    //Wywołanie funkcji printBook z parametrem ID wybranego autora
    header('Location: katalog_ksiazek.php');                            //Powrót na stornę katalog_ksiazek.php
    exit();                                                             //exit
}

if (isset($_REQUEST['rezerwuj'])){                                      //Obsługa przycisku rezerwuj w katalogu książek dla osób zalogowanych
    if (rezerwuj(htmlspecialchars(trim($_REQUEST['rezerwuj'])))){       //Wywolanie funkcji rezerwuj z parametrem ID ksiazki
        $_SESSION['zarezerwowano'] = "";                                //Utworzenie zmiennej sesyjnej zarezerwowano
        header('Location: moje_ksiazki.php');                           //Przenieś na stornę moje_ksiazki.php
        exit();                                                         //exit
    }
    else{
        header('Location: katalog_ksiazek.php');                        //Jeśli funkcja rezerwuj zwróci FALSE (nie uda się zarezerwować) powrót na stronę katalog_ksiazek.php
        exit();                                                         //exit
    }  
}

if (isset($_REQUEST['wypozycz'])){                                                      //Obsługa przycisku wypożycz w katalogu książek dla pracowników
    $_SESSION['wypozycz_id_ksiazki'] = htmlspecialchars(trim($_REQUEST['wypozycz']));   //Utworzenie zmiennej sesyjnej z ID książki. Jeśli ta zmienna istnienie pojawia się możliwość wyboru czytelnika
    header('Location: katalog_ksiazek.php');                                            //Powrót na stornę katalog_ksiazek.php
    exit();                                                                             //exit
}

if (isset($_REQUEST['wypozycz_user'])){                                 //Obsługa przycisku wypożycz gdy pracownik wybiera użytkownika ktoremu wypozycza ksiazke
    if(wypozycz(htmlspecialchars(trim($_REQUEST['select_user'])))){     //Wywolanie funkcji wypozycz z parametrem ID czytelnika
        unset($_SESSION['wypozycz_id_ksiazki']);                        //Zwolnienie zmiennej sesyjnej z ID ksiazki
        $_SESSION['pracownik_wypozyczono'] = "";                        //Utworzenie zmiennej sesyjne informujacej o pozytywnym wypozyczeniu ksiazki
        header('Location: katalog_ksiazek.php');                        //Powrót na stornę katalog_ksiazek.php
        exit();                                                         //exit
    }
    else{
        header('Location: katalog_ksiazek.php');                        //Jeśli funkcja wypozycz zwroci FALSE powrot na strone katalog_ksiazek.php
        exit();                                                         //exit
    }   
}

function wypozycz($id_user){                                                    //Funkcja obslugujaca wypozyczenie ksiazki
    $status = FALSE;                                                                                                        
    $baza = new baza();    
    $connect = $baza->connectDatabase();
    $id_ksiazki = $_SESSION['wypozycz_id_ksiazki'];
    $id_pracownika = $_SESSION['id_user'];
    $query = sprintf("INSERT INTO wypozyczenia (ID_ksiazki, ID_users, ID_pracownika_wypozyczenie, data_wypozyczenia) VALUES ('%s', '%s', '%s', '%s');",
                    mysqli_real_escape_string($connect,$id_ksiazki),
                    mysqli_real_escape_string($connect,$id_user),
                    mysqli_real_escape_string($connect,$id_pracownika),
                    mysqli_real_escape_string($connect,date("Y-m-d H:i:s")));
    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        $query = "UPDATE ksiazki SET ksiazki.wypozyczona='1' WHERE ksiazki.ID='".$id_ksiazki."'";
        
        if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
            $baza->disconnectDatabase($connect);
            $status = TRUE;
            return $status;
        }
        else {
            //Błąd w zapytaniu do bazy
            $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
        }
    }
    else {
        //Błąd w zapytaniu do bazy
        $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
    }
    $baza->disconnectDatabase($connect);
    return $status;
}

//Funckja odpowiadająca za wykonanie rezerwacji książki. Dodaj wpis w tabeli rezerwacje.
//Oraz aktualizuje status książki na wypożyczoną w tabeli książki.
function rezerwuj($id){
    $status = FALSE;
    $baza = new baza();    
    $connect = $baza->connectDatabase();
    $id_user = $_SESSION['id_user'];
    $query = sprintf("INSERT INTO rezerwacje (ID_ksiazki, ID_users, data_rezerwacji, uwagi) VALUES ('%s', '%s', '%s', '%s');",
                    mysqli_real_escape_string($connect,$id),
                    mysqli_real_escape_string($connect,$id_user),
                    mysqli_real_escape_string($connect,date("Y-m-d H:i:s")),
                    mysqli_real_escape_string($connect,"brak"));
    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        
        $query = "UPDATE ksiazki SET ksiazki.wypozyczona='1' WHERE ksiazki.ID='".$id."'";
        
        if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
            $baza->disconnectDatabase($connect);
            $status = TRUE;
            return $status;
        }
        else {
            //Błąd w zapytaniu do bazy
            $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
        }   
    }
    else {
        //Błąd w zapytaniu do bazy
        $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
    }
    $baza->disconnectDatabase($connect);
    return $status;
}

//Funkcja tworząca tabelę z książkami wybranego autora
function printBook($id){
    $tresc = "";
        
    $baza = new baza();
        
    $connect = $baza->connectDatabase();
        
    $query = "SELECT ksiazki.ID, autorzy.imie, autorzy.nazwisko, ksiazki.tytul
                FROM ksiazki INNER JOIN autorzy ON ksiazki.ID_autorzy=autorzy.ID
                WHERE ksiazki.ID_autorzy=".$id;
    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        $tresc .="<div id='formularz_div'><form><table class='";
        if($_SESSION['typ_konta'] == 3){
            $tresc .="do_wypozyczenia_table";
        }
        else{
            $tresc .="ksiazki_table";
        }
        $tresc .= "'>"; 
        $tresc .="<tr><td class='ksiazki_td_naglowek'>Autor</td><td class='ksiazki_td_naglowek'>Tytuł</td>";
        if (isset($_SESSION['typ_konta'])){
            $ile = 3;
            $tresc .="<td class='ksiazki_td_naglowek'>Akcja</td>";
        }
        else{
            $ile = 2;
        }
        $tresc .= "</tr>";
        while ($row = $rezultat->fetch_object()) {
            $tresc.="<tr>";
            for ($i =0; $i < $ile; $i++) {
                if($i==0){
                    $tresc .= "<td class='ksiazki_td'>".$row->imie." ".$row->nazwisko."</td>";
                }
                else{
                    if($i==1){
                    $tresc .= "<td class='ksiazki_td'>".$row->tytul."</td>";
                    }
                    else{
                        $tresc .= "<td class='ksiazki_td'> <button name='rezerwuj' type='submit' value='".$row->ID."'>Rezerwuj</button>";
                        if($_SESSION['typ_konta'] == 2){
                            $tresc .= "<button name='wypozycz' type='submit' value='".$row->ID."'>Wypożycz</button>";
                        }
                        if($_SESSION['typ_konta'] == 3){
                            $tresc .= "<button name='wypozycz' type='submit' value='".$row->ID."'>Wypożycz</button>";
                        }
                        $tresc .= "</td>";
                    }
                }
            }
            $tresc .= "</tr>";
        }
        $tresc .="</table></form></div>";
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

//Funkcja wypisujące opcje do wybrania w liście rozwijanej autorów na stronie Katalog książek.
//Zapytanie do bazy pokazuje tylko autorów którzy mają jakieś książki w bibliotece,
//oraz jeśli książka autora jest wypożyczona to nie pokaże nam tego autora.
function printOptions($option){
    
    switch ($option){
        case "book":
            $tresc = "<td class='formularz_td_text'>Wybierz autora: </td>";
            $tresc .= "<td class='formularz_td_input'>";
            $tresc .= "<select name='select_autor'>";
        
            $baza = new baza();
        
            $connect = $baza->connectDatabase();
        
            $query = "SELECT DISTINCT autorzy.ID, autorzy.imie, autorzy.nazwisko FROM autorzy INNER JOIN ksiazki
                        ON autorzy.ID=ksiazki.ID_autorzy WHERE ksiazki.wypozyczona=0
                        ORDER BY autorzy.imie ASC, autorzy.nazwisko ASC";
            if ($rezultat = $connect->query($query)){
                //Poprawne zapytanie do bazy
            
                while ($row = $rezultat->fetch_object()) {
                    $tresc.="<option value='".$row->ID."'";
                    //Ustawienie wartosci domyslnej dla select
                    if (isset($_SESSION['select_autor']) && $_SESSION['select_autor']==$row->ID) {
                        $tresc .= " selected='selected'";
                        unset ($_SESSION['select_autor']);
                    }
                    $tresc.=">".$row->imie." ".$row->nazwisko."</option>";
                }
                $rezultat->close();
            }
            else {
                //Błąd w zapytaniu do bazy
                $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
                $baza->disconnectDatabase($connect);
            }
            $tresc .= "</select></td>";
            $tresc .= " <td class='formularz_td_input'><button class='button_pokaz_ksiazki' name='pokaz_ksiazki'>Pokaż Książki</button></td>";
            break;
            
        case "user":
            $tresc = "<td class='formularz_td_text'>Wybierz użytkownika: </td>";
            $tresc .= "<td class='formularz_td_input'>";
            $tresc .= "<select name='select_user'>";
        
            $baza = new baza();
        
            $connect = $baza->connectDatabase();
        
            $query = "SELECT users_login.ID, users_login.login FROM users_login";
            if ($rezultat = $connect->query($query)){
                //Poprawne zapytanie do bazy
            
                while ($row = $rezultat->fetch_object()) {
                    $tresc.="<option value='".$row->ID."'";
                    $tresc.=">Login: ".$row->login."</option>";
                }
                $rezultat->close();
            }
            else {
                //Błąd w zapytaniu do bazy
                $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
                $baza->disconnectDatabase($connect);
            }
            $tresc .= "</select></td>";
            $tresc .= " <td class='formularz_td_input'><button class='button_pokaz_ksiazki' name='wypozycz_user'>Wypożycz</button></td>";
            break;
    }
    
    return $tresc;
    $baza->disconnectDatabase($connect);
}

//Funkcja wypisujaca tresc strony katalog_ksiazek
function printTresc(){
    $tresc = "";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' />"
            . "<script src='js/whcookies.js'></script><title>Biblioteka - Katalog Książek</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Katalog książek!</span></h1></div>";
    $user = new userLogin();
    if($user->getLoggedInUser(session_id()) == 1){
                        
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
    }
    else{
        $tresc .= '<div id="menu_index">';
        $tresc .= '<a class="button_menu_index" href="index.php">Strona Główna</a>';
        $tresc .= '<a class="button_menu_index" href="katalog_ksiazek.php">Katalog książek</a>';
        $tresc .= '<a class="button_menu_index" href="rejestracja.php">Rejestracja</a>';
    }
    $tresc .= "</div><div id='tresc_div'><span class='tresc_span' id='tresc_katalog'>";
    if (isset($_SESSION['pracownik_wypozyczono'])){
        $tresc .= 'Udało się wypożyczyć książkę!</br>';
        unset($_SESSION['pracownik_wypozyczono']);
    }
    if (isset($_SESSION['typ_konta'])){
        $tresc .= 'Możesz tutaj rezerwować dostępne do wypożyczenia książki. Po rezerwacji jest 3 dni na odbiór książki'
                . ' w bibliotece. <br>Po tym czasie jeśli nie odbierzesz rezerwacji w bilbiotece osobiście,'
                . ' rezerwacja zostanie anulowana.';
    }
    else{
        $tresc .= 'Możesz tutaj przeglądać dostępne do wypożyczenia książki.';
    }
    $tresc .= "</span><div id='formularz_div'><form method='post'><table class='formularz_table'><tr>";
    
    if (isset($_SESSION['wypozycz_id_ksiazki'])){
        $tresc .= printOptions("user");
    }
    else{
        $tresc .= printOptions("book");
    }
    $tresc .= "</tr></table></form>";
    if (isset($_SESSION['blad_select'])) {
        $tresc .= $_SESSION['blad_select'];
        unset ($_SESSION['blad_select']);
    }
    $tresc .= "</div>";
    if (isset($_SESSION['table_ksiazki'])) {
        $tresc .= $_SESSION['table_ksiazki'];
        unset ($_SESSION['table_ksiazki']);
    }
    $tresc .= "</div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

