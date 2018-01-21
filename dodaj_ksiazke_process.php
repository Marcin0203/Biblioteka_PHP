<?php
$_SESSION['baza'] = "";
require_once 'baza.php';

if(!(isset($_SESSION['dodaj_ksiazke_process']))){
    header('Location: index.php');
    exit();
}
else{
    unset($_SESSION['dodaj_ksiazke_process']);
}

//Obsługa przycisku dodaj autora
if (isset($_REQUEST['dodaj_autora'])){
    $_SESSION['dodaj_autora'] = "";
    header('Location: dodaj_ksiazke_page.php');  
}

//Obsługa przycisku dodaj autora
if (isset($_REQUEST['powrot'])){
    unset($_SESSION['dodaj_autora']);
    header('Location: dodaj_ksiazke_page.php');  
}

//Obsługa przycisku dodaj książkę
if (isset($_REQUEST['dodaj_ksiazke'])){
    if(addBook()){
        $_SESSION['dodano_ksiazke'] = "<span class='good'></br>Udało się dodać nową książkę!</span>";
        header('Location: dodaj_ksiazke_page.php');  
    }
}

//Obsługa przycisku dodaj autora. Który dodaje autora do bazy
if (isset($_REQUEST['dodaj_autora_baza'])){
    if(addAutor()){
        if(isset($_SESSION['istnieje_autor'])){
            unset($_SESSION['istnieje_autor']);
        }
        unset($_SESSION['dodaj_autora']);
        $_SESSION['dodano_autora'] = "";
        header('Location: dodaj_ksiazke_page.php');  
    }
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
        $_SESSION['istnieje_autor'] = "<span class = 'error'>Istnieje w bazie autor o podanym imieniu i nazwisku! Wybierz z listy poniżej autora.</span>";
        header('Location: dodaj_ksiazke_page.php');
    }
    $baza->disconnectDatabase($connect);
    return $status;
}

//Funkcja wypisująca treść strony gdy dodajemy książkę
function printAutor(){
    $tresc = "<td class='formularz_td_text'>Wybierz autora: </td>";
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
    echo $tresc;
    $baza->disconnectDatabase($connect);
}

//Funckja wypisująca treść strony gdy dodajemy autora
function printAddAutor(){
    $tresc = "<td class='formularz_td_text'>Imię: </td>";
    $tresc .= "<td class='formularz_td_input'>";
    $tresc .= "<input class='formularz_inputText' type='text' name='imie' placeholder='Wpisz imię...'></td></tr>";
    $tresc .= "<tr><td class='formularz_td_text'>Nazwisko: </td>";
    $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nazwisko' placeholder='Wpisz nazwisko...'></td></tr>";
    $tresc .= "<tr><td></td><td class='formularz_td_button'><button class='formularz_button_zarejestruj' name='dodaj_autora_baza'>Dodaj</button>";
    $tresc .= "<button class='formularz_button_back' name='powrot'>Powrót</button></td>";
    echo $tresc;
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

