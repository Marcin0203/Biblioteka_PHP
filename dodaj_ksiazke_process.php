<?php
//Plik obsługujący stronę dodaj_ksiazke_page

if(!(isset($_SESSION['dodaj_ksiazke_process']))){                       //Sprawdzenie czy NIE istnieje zmienna sesyjna wypozycz_process
    header('Location: index.php');                                      //Jeśli nie istnieje przenieś na index.php
    exit();                                                             //exit
}
else{
    unset($_SESSION['dodaj_ksiazke_process']);                          //Jeśli istnieje usuń zmienną sesyjną
}

$_SESSION['baza'] = "";                                                 //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku baza.php
require_once 'baza.php';                                                //Dołączenie klasy obsługującej połączenie z bazą

if (isset($_REQUEST['dodaj_autora'])){                                  //Obsługa przycisku dodaj autora
    $_SESSION['dodaj_autora'] = "";                                     //Utworz zmienna sesyjna powodujaca wydrukowanie tresci strony z formularzem do dodania autora
    header('Location: dodaj_ksiazke_page.php');                         //Powrot na strone dodaj_ksiazke_page.php
    exit();                                                             //exit
}

if (isset($_REQUEST['powrot'])){                                        //Obsługa przycisku powrot
    unset($_SESSION['dodaj_autora']);                                   //Zwolnij zmienna sesyjna powodujaca wydrukowanie tresci strony z formularzem do dodania autora
    header('Location: dodaj_ksiazke_page.php');                         //Powrot na strone dodaj_ksiazke_page.php
    exit();                                                             //exit
}

if (isset($_REQUEST['dodaj_ksiazke'])){                                 //Obsługa przycisku dodaj książkę
    if(addBook()){                                                      //Wywolanie funkcji addBook
        $_SESSION['dodano_ksiazke'] = "<span class='good'></br>Udało się dodać nową książkę!</span>";   //Jeśli funkcja addBook zroci TRUE utworz zmienna sesyjna informujaca o dodaniu ksiazki
        header('Location: dodaj_ksiazke_page.php');                     //Powrot na strone dodaj_ksiazke_page.php
        exit();                                                         //exit
    }
    else{
        header('Location: dodaj_ksiazke_page.php');                     //Jeśli funkcja addBook zwroci FALSE, powrot na strone dodaj_ksiazke_page.php
        exit();                                                         //exit
    }
}

if (isset($_REQUEST['dodaj_autora_baza'])){                             //Obsługa przycisku dodaj autora, który dodaje autora do bazy
    if(addAutor()){                                                     //Wywolanie funkcji addAutor
        if(isset($_SESSION['istnieje_autor'])){                         //Jesli funkcja addAutor zwroci TRUE sprawdz czy istnieje zmienna sesyjna istnieje_autor
            unset($_SESSION['istnieje_autor']);                         //Jeśli istnieje zmienna sesyjna istnieje_autor zwolnij ją
        }
        unset($_SESSION['dodaj_autora']);                               //Zwolnij zmienna sesyjna dodaj_autora
        $_SESSION['dodano_autora'] = "";                                //Utworz zmienna sesyjna informujaca o dodaniu autora
        header('Location: dodaj_ksiazke_page.php');                     //Powrot na strone dodaj_ksiazke_page.php
        exit();                                                         //exit 
    }
    else{
        header('Location: dodaj_ksiazke_page.php');                     //Jeśli funkcja addAutor zwroci FALSE, powrot na strone dodaj_ksiazke_page.php
        exit();                                                         //exit
    }
}

//Funkcja drukujaca tresc strony dodaj_ksiazke_page
function printTresc(){
    $tresc ="";
    $tresc .= "<!DOCTYPE html><html><head><meta charset='UTF-8'>"
            . "<meta name='viewport' content='width=device-width, initial-scale=1.0'>"
            . "<link rel='stylesheet' href='CSS/style.css' type='text/css' />"
            . "<script src='js/whcookies.js'></script><title>Biblioteka - Dodaj Książkę</title>"
            . "</head><body><div id='naglowek_div'><h1><span id='naglowek_tresc'>Dodaj książkę!</span></h1></div>";
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
    if(isset($_SESSION['istnieje_autor'])){
        $tresc .= $_SESSION['istnieje_autor'];
        unset($_SESSION['istnieje_autor']);
    }
    if(isset($_SESSION['istnieje_ksiazka'])){
        $tresc .= $_SESSION['istnieje_ksiazka'];
        unset($_SESSION['istnieje_ksiazka']);
    }
    else{
        if(isset($_SESSION['dodaj_autora'])){
            $tresc .= 'Możesz tutaj dodać nowego autora do biblioteki.';
        }
        else{
            $tresc .= 'Możesz tutaj dodać nową książkę do biblioteki.';
        }
    }
    $tresc .= "</span>";
    if(isset($_SESSION['pusty_tytul'])){
        $tresc .= $_SESSION['pusty_tytul'];
        unset($_SESSION['pusty_tytul']);
    }
    if(isset($_SESSION['pusty_autor'])){
        $tresc .= $_SESSION['pusty_autor'];
        unset($_SESSION['pusty_autor']);
    }
    if(isset($_SESSION['dodano_autora'])){
        $tresc .= '<br><span class="good">Dodano nowego autora!</span>';
        unset($_SESSION['dodano_autora']);
    }
        if(isset($_SESSION['dodano_ksiazke'])){
            $tresc .= $_SESSION['dodano_ksiazke'];
        unset($_SESSION['dodano_ksiazke']);
    }
    $tresc .= "<div id='formularz_div'><form method='post'><table class='formularz_table'><tr>";
    if(isset($_SESSION['dodaj_autora'])){
        $tresc .= printAddAutor();
    }
        else{
            $tresc .= printAutor();
    }
    $tresc .= "</tr></table></form></div></div><div id='stopka'> &copy; 2017 Marcin Małocha</div></body></html>";
    echo $tresc;
}

//Funkcja sprawdzająca czy książka nie istnieje w bazie biblioteki
function checkBook($connect, $id_autora, $tytul){
    $status = FALSE;
    $query = sprintf("SELECT * FROM ksiazki WHERE ksiazki.ID_autorzy='%s' AND ksiazki.tytul='%s';",
                    mysqli_real_escape_string($connect,$id_autora),
                    mysqli_real_escape_string($connect,$tytul));
    if ($rezultat = $connect->query($query)){
        $ilu_userow = $rezultat->num_rows; 
        if($ilu_userow>0){
            $status = TRUE;
            return $status;
        }
    }
    return $status;
}

//Funckja dodająca nową książkę do bazy biblioteki
function addBook(){
    $status = FALSE;
    $tytul = $_POST['tytuł'];
    $id_autora = htmlspecialchars(trim($_REQUEST['select_autor']));
    $baza = new baza();
    $connect = $baza->connectDatabase();
    if(checkNull("book")){
        if(!checkBook($connect, $id_autora, $tytul)){
            $query = sprintf("INSERT INTO ksiazki (ID_autorzy, tytul, wypozyczona) VALUES ('%s', '%s', '0');",
                    mysqli_real_escape_string($connect,$id_autora),
                    mysqli_real_escape_string($connect,$tytul));
            if ($rezultat = $connect->query($query)){
                $status = TRUE;
                $baza->disconnectDatabase($connect);
                return $status;
            }
        }
        else{
            $_SESSION['istnieje_ksiazka'] = "<span class = 'error'>Istnieje w bazie książka wybranego autora o wpisanym tytule!</span>";
            header('Location: dodaj_ksiazke_page.php');
        }
    }
    else{
        $_SESSION['pusty_tytul']="<br><span class = 'error'>Wpisz tytuł książki!<br></span>";
        header('Location: dodaj_ksiazke_page.php');
    }
    $baza->disconnectDatabase($connect);
    return $status;   
}

//Funkcja dodająca nowego autora
function addAutor(){
    $status = FALSE;
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $baza = new baza();
    $connect = $baza->connectDatabase();
    if(checkNull("autor")){
        if(!checkAutor($connect, $imie, $nazwisko)){
        $query = sprintf("INSERT INTO autorzy (imie, nazwisko) VALUES ('%s', '%s');",
                    mysqli_real_escape_string($connect,$imie),
                    mysqli_real_escape_string($connect,$nazwisko));
        if ($rezultat = $connect->query($query)){
            $status = TRUE;
            $baza->disconnectDatabase($connect);
            return $status;
        }
        }
        else{
            if(isset($_SESSION['dodaj_autora'])){
                unset($_SESSION['dodaj_autora']);
            }
            $_SESSION['istnieje_autor'] = "<span class = 'error'>Istnieje w bazie autor o podanym imieniu i nazwisku! Wybierz z listy poniżej autora.<br></span>";
            header('Location: dodaj_ksiazke_page.php');
        }
    }
    else{
        $_SESSION['pusty_autor']="<br><span class = 'error'>Wypełnij minimun jedno pole!<br></span>";
        header('Location: dodaj_ksiazke_page.php');
    }
    $baza->disconnectDatabase($connect);
    return $status;
}

//Funkcja wypisująca treść strony gdy dodajemy książkę
function printAutor(){
    $tresc = "";
    $tresc .= "<td class='formularz_td_text'>Wybierz autora: </td>";
    $tresc .= "<td class='formularz_td_input'>";
    $tresc .= "<select name='select_autor'>";
    $baza = new baza();
    $connect = $baza->connectDatabase();
    $query = "SELECT * FROM autorzy ORDER BY autorzy.imie ASC, autorzy.nazwisko ASC";
    if ($rezultat = $connect->query($query)){
        //Poprawne zapytanie do bazy
        while ($row = $rezultat->fetch_object()) {
            $tresc.="<option value='".$row->ID."'";
            $tresc.=">".$row->imie." ".$row->nazwisko."</option>";
        }
        $rezultat->close();
    }
    $tresc .= "</select></td>";
    $tresc .= " <td class='formularz_td_text'>Lub <button class='button_pokaz_ksiazki' name='dodaj_autora'>Dodaj autora</button></td>";
    $tresc .= "</tr><tr><td class='formularz_td_text'>";
    $tresc .= "Tytuł:</td><td class='formularz_td_input'><input class='formularz_inputText' type='text' name='tytuł' placeholder='Wpisz tytuł...'></td><td></td>";
    $tresc .= "</tr><tr><td></td><td class='formularz_td_button'>";
    $tresc .= '<button class="formularz_button_zarejestruj" name="dodaj_ksiazke">Dodaj</button></td><td></td>';
    $baza->disconnectDatabase($connect);
    return $tresc;
}

//Funckja wypisująca treść strony gdy dodajemy autora
function printAddAutor(){
    $tresc = "";
    $tresc .= "<td class='formularz_td_text'>Imię: </td>";
    $tresc .= "<td class='formularz_td_input'>";
    $tresc .= "<input class='formularz_inputText' type='text' name='imie' placeholder='Wpisz imię...'></td></tr>";
    $tresc .= "<tr><td class='formularz_td_text'>Nazwisko: </td>";
    $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nazwisko' placeholder='Wpisz nazwisko...'></td></tr>";
    $tresc .= "<tr><td></td><td class='formularz_td_button'><button class='formularz_button_zarejestruj' name='dodaj_autora_baza'>Dodaj</button>";
    $tresc .= "<button class='formularz_button_back' name='powrot'>Powrót</button></td>";
    return $tresc;
}

//Funkcja sprawdzająca czy istnieje dany autor w bazie
function checkAutor($connect, $imie, $nazwisko){
    $status = FALSE;
    $query = sprintf("SELECT * FROM autorzy WHERE autorzy.imie='%s' AND autorzy.nazwisko='%s';",
                    mysqli_real_escape_string($connect,$imie),
                    mysqli_real_escape_string($connect,$nazwisko));
    if ($rezultat = $connect->query($query)){
        $ilu_userow = $rezultat->num_rows;
            
            if($ilu_userow>0){
                $status = TRUE;
                return $status;
            }
    }
    return $status;
}

//funkcja sprawdzajaca czy pola nie są puste
function checkNull($opcja){
    $status = FALSE;
    if($opcja == "autor"){
        if($_POST['imie'] == "" && $_POST['nazwisko'] == ""){
            return $status;
        }
        else{
            $status = TRUE;
            return $status;
        }
    }
    else{
        if($_POST['tytuł'] == ""){
            return $status;
        }
        else{
            $status = TRUE;
            return $status;
        }
    }
}

