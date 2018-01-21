<?php

if(!$_SESSION){
    session_start();
}

if(!(isset($_SESSION['moje_konto_process']))){
    header('Location: index.php');
    exit();
}
else{
    unset($_SESSION['moje_konto_process']);
}

$_SESSION['baza'] = "";
require_once 'baza.php';

$_SESSION['userLogin'] = "";
require_once 'userLogin.php';

$_SESSION['userRejestracja'] = "";
require_once 'userRejestracja.php';

//Obsługa przycisku zmiana hasła
if (isset($_REQUEST['zmiana'])){
    $_SESSION['zmiana'] = "";
    header('Location: moje_konto_page.php');  
}

//Obsługa przycisku zmień w edycji dancyh
if (isset($_REQUEST['edytuj_zapisz'])){
    if(editDane()){
        $_SESSION['zaktualizowano'] = "<span class='good'>Udało się zaktualizować dane osobowe!</span><br>";
        unset($_SESSION['edycja']);
    }
}

//Obsługa przycisku zmiana hasła
if (isset($_REQUEST['edytuj'])){
    $_SESSION['edycja'] = "";
    header('Location: moje_konto_page.php');  
}

//Obsługa przycisku back
if (isset($_REQUEST['back'])){
    if(isset($_SESSION['zmiana'])){
        unset($_SESSION['zmiana']);
    }
    if(isset($_SESSION['edycja'])){
        unset($_SESSION['edycja']);
    }
    $user = new userRejestracja();
    $user->unlockFormSession();
    header('Location: moje_konto_page.php');  
}


//Obsługa przycisku zmień hasło
if (isset($_REQUEST['zmien'])){
    if(checkPassword()){
        if(changePassword()){
            $_SESSION['zmieniono'] = "<span class='good'>Udało się zmienić hasło!</span><br>";
            unset($_SESSION['zmiana']);
        }    
    }
}

//Funkcja wypisująca przyciski Edytuj dane oraz zmiana hasła
function printTresc(){
    $tresc = "Możesz tutaj zarządzać swoim kontem.<br></span>";
    $tresc .= "<div id='moje_konto_div'>";
    if(isset($_SESSION['zmieniono'])){
        $tresc .= $_SESSION['zmieniono'];
        unset($_SESSION['zmieniono']);
    }
    if(isset($_SESSION['zaktualizowano'])){
        $tresc .= $_SESSION['zaktualizowano'];
        unset($_SESSION['zaktualizowano']);
    }
    $tresc .= "<form method='post'>";
    $tresc .= "<button name='edytuj' type='submit'>Edytuj dane</button>   ";
    $tresc .= "<button name='zmiana' type='submit'>Zmiana hasła</button>";
    $tresc .= "</form></div>";
    echo $tresc;
}


//Funkcja wypisująca treść strony gdy zmieniamy hasło
function printZmiana(){
    $tresc = "Możesz tutaj zmienić swoje hasło.<br></span>";
    $tresc .= "<div id='formularz_div'>";
    $tresc .= "<form method='post'>";
    $tresc .= "<table class='formularz_table'>";
    $tresc .= "<tr><td class='formularz_td_text'>Aktualne hasło:<br><br><br></td>";
    $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='password' name='password_aktualne' placeholder='Aktualne hasło...'><br><br><br></td></tr>";
    $tresc .= "<tr><td class='formularz_td_text'>Nowe hasło:</td>";
    $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='password' name='password_nowe' placeholder='Nowe hasło...'></td></tr>";
    $tresc .= "<tr><td class='formularz_td_text'>Powtórz nowe hasło:</td>";
    $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='password' name='password_repeat' placeholder='Powtórz nowe hasło...'></td></tr>";
    if (isset($_SESSION['error_password'])) {
        $tresc .= $_SESSION['error_password'];
        unset ($_SESSION['error_password']);
    }
    else{
        $tresc .= '<tr><td></td><td class="formularz_td_error"><span class="good">Wymagania hasła:<br> '
                . 'Długość: od 6 do 30 znaków <br>Min. jedna mała, duża litera oraz cyfra</span></td></tr>';
    }
    $tresc .= "<tr><td><br><br><br></td><td class='formularz_td_button'>";
    $tresc .= '<button class="formularz_button_zarejestruj" name="zmien">Zmień</button>';
    $tresc .= '<button class="formularz_button_back" name="back">Powrót</button></td></tr>';
    $tresc .= "</table></form></div>";
    if(isset($_SESSION['repeat_password'])){
        $tresc .= $_SESSION['repeat_password'];
        unset ($_SESSION['repeat_password']);
    }
    if(isset($_SESSION['blad_password'])){
        $tresc .= $_SESSION['blad_password'];
        unset ($_SESSION['blad_password']);
    }
    
    echo $tresc;
}

//Funkcja wypisujaca tresc zakladki edycja danych
function printEdycja(){
    $tresc = "Możesz tutaj zmienić swoje dane kontaktowe:<br></span>";
    $id = $_SESSION['id_user'];
    $tresc .= "<div id='formularz_div'>";
    $tresc .= "<form method='post'>";
    $tresc .= "<table class='formularz_table'>";
    
    $baza = new baza();
    $connect = $baza->connectDatabase();
    $query = "SELECT users_login.ID_users_contact FROM users_login WHERE users_login.ID='".$id."'";
    
    if ($rezultat = $connect->query($query)){
        while ($row = $rezultat->fetch_object()) {
            $id_contact = $row->ID_users_contact;
        }
    }
    $query = "SELECT * FROM users_contact WHERE users_contact.ID='".$id_contact."'";
    
    if ($rezultat = $connect->query($query)){
        while ($row = $rezultat->fetch_object()) {
            $tresc .= "<tr><td class='formularz_td_text'>Imię:*</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='imie' value='";
            if(isset($_SESSION['imie'])){
                $tresc .= $_SESSION['imie'];
            }
            else{
                $tresc .= $row->imie;
            }
            $tresc .= "'></td></tr>";
            if (isset($_SESSION['error_imie'])) {
                $tresc .= $_SESSION['error_imie'];
                unset ($_SESSION['error_imie']);
            }
            $tresc .= "<tr><td class='formularz_td_text'>Drugie imię:</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='drugie_imie' value='";
            if(isset($_SESSION['drugieImie'])){
                $tresc .= $_SESSION['drugieImie'];
            }
            else{
                $tresc .= $row->drugie_imie;
            }
            $tresc .= "'></td></tr>";
            $tresc .= "<tr><td class='formularz_td_text'>Nazwisko:*</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nazwisko' value='";
            if(isset($_SESSION['nazwisko'])){
                $tresc .= $_SESSION['nazwisko'];
            }
            else{
                $tresc .= $row->nazwisko;
            }
            $tresc .= "'></td></tr>";
            if (isset($_SESSION['error_nazwisko'])) {
                $tresc .= $_SESSION['error_nazwisko'];
                unset ($_SESSION['error_nazwisko']);
            }
            $tresc .= "<tr><td class='formularz_td_text'>Miasto:*</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='miasto' value='";
            if(isset($_SESSION['miasto'])){
                $tresc .= $_SESSION['miasto'];
            }
            else{
                $tresc .= $row->miasto;
            }
            $tresc .= "'></td></tr>";
            if (isset($_SESSION['error_miasto'])) {
                $tresc .= $_SESSION['error_miasto'];
                unset ($_SESSION['error_miasto']);
            }
            $tresc .= "<tr><td class='formularz_td_text'>Poczta:*</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='poczta' value='";
            if(isset($_SESSION['poczta'])){
                $tresc .= $_SESSION['poczta'];
            }
            else{
                $tresc .= $row->poczta;
            }
            $tresc .= "'></td></tr>";
            if (isset($_SESSION['error_poczta'])) {
                $tresc .= $_SESSION['error_poczta'];
                unset ($_SESSION['error_poczta']);
            }
            $tresc .= "<tr><td class='formularz_td_text'>Ulica:*</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='ulica' value='";
            if(isset($_SESSION['ulica'])){
                $tresc .= $_SESSION['ulica'];
            }
            else{
                $tresc .= $row->ulica;
            }
            $tresc .= "'></td></tr>";
            if (isset($_SESSION['error_ulica'])) {
                $tresc .= $_SESSION['error_ulica'];
                unset ($_SESSION['error_ulica']);
            }
            $tresc .= "<tr><td class='formularz_td_text'>Nr domu:*</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nr_domu' value='";
            if(isset($_SESSION['nrDomu'])){
                $tresc .= $_SESSION['nrDomu'];
            }
            else{
                $tresc .= $row->nr_domu;
            }
            $tresc .= "'></td></tr>";
            if (isset($_SESSION['error_nrDomu'])) {
                $tresc .= $_SESSION['error_nrDomu'];
                unset ($_SESSION['error_nrDomu']);
            }
            $tresc .= "<tr><td class='formularz_td_text'>Nr mieszkania:</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='nr_mieszkania' value='";
            if(isset($_SESSION['nrMieszkania'])){
                $tresc .= $_SESSION['nrMieszkania'];
            }
            else{
                $tresc .= $row->nr_mieszkania;
            }
            $tresc .= "'></td></tr>";
            $tresc .= "<tr><td class='formularz_td_text'>Telefon:</td>";
            $tresc .= "<td class='formularz_td_input'><input class='formularz_inputText' type='text' name='telefon' value='";
            if(isset($_SESSION['telefon'])){
                $tresc .= $_SESSION['telefon'];
            }
            else{
                $tresc .= $row->telefon;
            }
            $tresc .= "'></td></tr>";
        }
    }
    $tresc .= "<tr><td></td><td class='formularz_td_text' id='formularz_dane_wymagane'>*Dane wymagane</td></tr>";
    if(isset($_SESSION['error_istniejeContact'])){
        $tresc .= $_SESSION['error_istniejeContact'];
        unset($_SESSION['error_istniejeContact']);
    }
    $baza->disconnectDatabase($connect);
    $tresc .= "<tr><td><br><br><br></td><td class='formularz_td_button'>";
    $tresc .= '<button class="formularz_button_zarejestruj" name="edytuj_zapisz">Zmień</button>';
    $tresc .= '<button class="formularz_button_back" name="back">Powrót</button></td></tr>';
    $tresc .= "</table></form></div>";
    echo $tresc;
}

//Funkcja sprawdzająca czy wpisane hasła są takie same
function checkPassword(){
    $status = FALSE;
    $password = $_POST['password_nowe'];
    $repeatPassword = $_POST['password_repeat'];
    if($password == $repeatPassword){
        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?:[a-zA-Z0-9!@#$%^&*()_+|-]{6,30})$/', $password)) {
            $status = TRUE;
            return $status;
        }
        else{
            $_SESSION['error_password'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wymagania hasła:<br> '
                    . 'Długość: od 6 do 30 znaków <br>Min. jedna mała, duża litera oraz cyfra</span></td></tr>';
            return $status;
        }
    }
    else{
        $_SESSION['repeat_password'] = "<span class='error'>Wpisane hasła nie są takie same!</span>";
        return $status;
    }
}

//Funkcja obsługująca zmiane hasła
function changePassword(){
    $status = FALSE;
    $baza = new baza();
    $id = $_SESSION['id_user'];
    $user = new userLogin();
    
    $password = $user->has($_POST['password_aktualne']);
    $passwordNew = $user->has($_POST['password_nowe']);
    
    
    $connect = $baza->connectDatabase();
    
    $query = "SELECT * FROM users_login WHERE users_login.ID='".$id."' AND users_login.password='".$password."';";
    
    if ($rezultat = $connect->query($query)){
        $ilu_userow = $rezultat->num_rows;
        
        if($ilu_userow>0){
            $query = "UPDATE users_login SET users_login.password='".$passwordNew."' WHERE users_login.ID='".$id."'";
            if($connect->query($query)){
                $status = TRUE;
                $baza->disconnectDatabase($connect);
                return $status;
            }
        }
        else{
            $_SESSION['blad_password']= "<span class='error'>Wpisane hasło jest błędne!</span>";
            $baza->disconnectDatabase($connect);
            return $status;
        }
    }
    $baza->disconnectDatabase($connect);
    return $status;
}

function editDane(){
    $status = FALSE;
    $user = new userRejestracja();
    $user->setImie($_POST['imie']);
    $user->setDrugieImie($_POST['drugie_imie']);
    $user->setNazwisko($_POST['nazwisko']);
    $user->setMiasto($_POST['miasto']);
    $user->setPoczta($_POST['poczta']);
    $user->setUlica($_POST['ulica']);
    $user->setNrDomu($_POST['nr_domu']);
    $user->setNrMieszkania($_POST['nr_mieszkania']);
    $user->setTelefon($_POST['telefon']);
    
    if($user->checkNotNullInput(FALSE)){
        if($user->checkPoczta()){
            if(!$user->checkUserInDatabase("contact")){
                if(updateDane($user)){
                    $status = TRUE;
                    return $status;
                }
                else{
                    return $status;
                }
            }
            else{
                return $status;
            }     
        }
        else{
            return $status;
        }
    }
    else{
        return $status;
    }
    
}

//Funkcja aktualizująca dane kontaktowe uzytkownika
function updateDane($user){
    $status = FALSE;
    $baza = new baza();
    $id = $_SESSION['id_user'];
    $connect = $baza->connectDatabase();
    $query = "SELECT  users_login.ID_users_contact FROM users_login WHERE users_login.ID='".$id."'";
    if ($rezultat = $connect->query($query)){
         while ($row = $rezultat->fetch_object()) {
            $id_contact = $row->ID_users_contact;
        }
        
        $query = sprintf("UPDATE users_contact SET users_contact.imie='%s', users_contact.drugie_imie='%s', users_contact.nazwisko='%s', "
            . "users_contact.miasto='%s', users_contact.poczta='%s', users_contact.ulica='%s', users_contact.nr_domu='%s', "
            . "users_contact.nr_mieszkania='%s', users_contact.telefon='%s' WHERE users_contact.ID='%s'",
            mysqli_real_escape_string($connect,$user->getImie()),
            mysqli_real_escape_string($connect,$user->getDrugieImie()),
            mysqli_real_escape_string($connect,$user->getNazwisko()),
            mysqli_real_escape_string($connect,$user->getMiasto()),
            mysqli_real_escape_string($connect,$user->getPoczta()),
            mysqli_real_escape_string($connect,$user->getUlica()),
            mysqli_real_escape_string($connect,$user->getNrDomu()),
            mysqli_real_escape_string($connect,$user->getNrMieszkania()),
            mysqli_real_escape_string($connect,$user->getTelefon()),
            mysqli_real_escape_string($connect,$id_contact));
        
        if ($rezultat = $connect->query($query)){
            $status = TRUE;
            return $status;
        }
        else{
            $baza->disconnectDatabase($connect);
            return $status;
        }
    }
    else{
        $baza->disconnectDatabase($connect);
        return $status;
    } 
}
