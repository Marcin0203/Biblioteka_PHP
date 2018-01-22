<?php

$_SESSION['baza'] = "";                         //Utworzenie zmiennej sesyjnej dającej dostep do klasy baza.php 
require_once 'baza.php';                        //Dołączenie klasy baza.php

if(!(isset($_SESSION['userRejestracja']))){     //Sprawdzenie czy NIE istnieje zmienna sesyjna userRejestracja
    header('Location: index.php');              //Jeśli nie istnieje przenieś na index.php
    exit();                                     //exit
}
else{
    unset($_SESSION['userRejestracja']);        //Jeśli istnieje usuń zmienną sesyjną
}

//Klasa reprezentujaca użytkowników podczas rejestracji
class userRejestracja {

    private $login;
    private $email;
    private $password;
    private $repeatPassword;
    private $imie;
    private $drugieImie;
    private $nazwisko;
    private $miasto;
    private $poczta;
    private $ulica;
    private $nrDomu;
    private $nrMieszkania;
    private $telefon;
    private $idContact;
    private $typKonta;


    //Funkcja walidacyjna formularza rejestracji. Jeśli wszystko jest dobrze zwaraca TRUE w innym przypadku zwraca FALSE
    function walidacja() {

        $statusWalidacja = TRUE;
        if ($this->checkNotNullInput(TRUE)) {
            if (!$this->checkPassword()) {

                $statusWalidacja = FALSE;

            }
            if(!$this->checkPoczta()){
                
                $statusWalidacja = FALSE;
                
            }
        } else {
            $statusWalidacja = FALSE;
        }

        return $statusWalidacja;
    }

    //funkcja sprawdzająca czy wszystkie pola wymagane w formularzu zostały wprowadzone. 
    //Jeśli wszystko wprowadzono poprawnie zwraca TRUE
    //Jeśli któreś z pól wymaganych zostało nie wprowadzone zwraca FALSE
    //Dodatkowo ustawia zmienne sesyjne z opisem które pola są puste
    //Oraz zmienne sesyjne z danymi wprowadzonymi przez użytkownika w formularzu
    //W momencie gdy użytkownik nie wprowadzi minimum jednego pola wymaganego
    //Reszta pól pozostanie usupełniona danymi które wcześniej wprowadził
    function checkNotNullInput($zakladka) {

        $status = TRUE;

        if($zakladka){  //Jeśli jest TRUE tzn że została funkcja wywołana z zakładki rejestracja i sprawdzamy dodatkowe pola
            if ($this->login == "") {

            if (isset($_SESSION['login'])) {
                unset($_SESSION['login']);
            }

            $_SESSION['error_login'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz login!</span></td></tr>';
            $status = FALSE;
            } else {
                $_SESSION['login'] = $this->login;
            }
            
             if ($this->email == "") {

            if (isset($_SESSION['email'])) {
                unset($_SESSION['email']);
            }

            $_SESSION['error_email'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz e-mail!</span></td></tr>';
            $status = FALSE;
            } else {
                $_SESSION['email'] = $this->email;
            }
            
            if ($this->password == "") {
            $_SESSION['error_password'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz hasło!</span></td></tr>';
            $status = FALSE;
            }
            
             if ($this->repeatPassword == "") {
            $_SESSION['error_repeatpassword'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Powtórz hasło!</span></td></tr>';
            $status = FALSE;
            }
        }

        if ($this->imie == "") {

            if (isset($_SESSION['imie'])) {
                unset($_SESSION['imie']);
            }

            $_SESSION['error_imie'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz imię!</span></td></tr>';
            $status = FALSE;
        } else {
            $_SESSION['imie'] = $this->imie;
        }

        if ($this->nazwisko == "") {

            if (isset($_SESSION['nazwisko'])) {
                unset($_SESSION['nazwisko']);
            }

            $_SESSION['error_nazwisko'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz nazwisko!</span></td></tr>';
            $status = FALSE;
        } else {
            $_SESSION['nazwisko'] = $this->nazwisko;
        }

        if ($this->miasto == "") {

            if (isset($_SESSION['miasto'])) {
                unset($_SESSION['miasto']);
            }

            $_SESSION['error_miasto'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz miasto!</span></td></tr>';
            $status = FALSE;
        } else {
            $_SESSION['miasto'] = $this->miasto;
        }

        if ($this->poczta == "") {

            if (isset($_SESSION['poczta'])) {
                unset($_SESSION['poczta']);
            }

            $_SESSION['error_poczta'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz poczte!</span></td></tr>';
            $status = FALSE;
        } else {
            $_SESSION['poczta'] = $this->poczta;
        }

        if ($this->ulica == "") {

            if (isset($_SESSION['ulica'])) {
                unset($_SESSION['ulica']);
            }

            $_SESSION['error_ulica'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz ulice!</span></td></tr>';
            $status = FALSE;
        } else {
            $_SESSION['ulica'] = $this->ulica;
        }

        if ($this->nrDomu == "") {

            if (isset($_SESSION['nrDomu'])) {
                unset($_SESSION['nrDomu']);
            }

            $_SESSION['error_nrDomu'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz nr domu!</span></td></tr>';
            $status = FALSE;
        } else {
            $_SESSION['nrDomu'] = $this->nrDomu;
        }

        if ($this->telefon != "") {
            $_SESSION['telefon'] = $this->telefon;
        }
        
        if ($this->drugieImie != "") {
            $_SESSION['drugieImie'] = $this->drugieImie;
        }

        if ($this->nrMieszkania != "") {
            $_SESSION['nrMieszkania'] = $this->nrMieszkania;
        }
        
        if(isset($_SESSION['typ_konta']) && $_SESSION['typ_konta'] == 3){
            if(($this->typKonta == NULL)){
                if (isset($_SESSION['typ'])) {
                    unset($_SESSION['typ']);
                }
                $_SESSION['error_typ'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wybierz typ konta!</span></td></tr>';
            }
            else{
                $_SESSION['typ'] = $this->typKonta;
            }
        }

        return $status;
    }

    //Funkcja sprawdzająca wprowadzone hasło. Wyrażenie regularne sprawdza długość hasła (od 6 do 30 znaków)
    //I czy hasło zawiera min jedną małą literę min jedną dużą literę oraz min jedną cyfrę
    //Jeśli warunki są spełnionę haszuje hasło oraz powtórzone hasło
    //Jeśli hasła się zgadzają zwraca TRUE jeśli coś się nie zgadza zwraca FALSE
    //oraz zmienna sesyjną z odpowiednim komunikatem błędu
    function checkPassword() {

        $statusPassword = TRUE;

        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?:[a-zA-Z0-9!@#$%^&*()_+|-]{6,30})$/', $this->password)) {

            $this->password = $this->has($this->password);
            $this->repeatPassword = $this->has($this->repeatPassword);

            if ($this->porownajPassword()) {
                return $statusPassword;
            } else {
                $_SESSION['error_repeatpassword'] .= '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisane hasła nie są takie same!</span></td></tr>';
                $statusPassword = FALSE;
            }
        } else {
            $_SESSION['error_password'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wymagania hasła:</br> '
                    . 'Długość: od 6 do 30 znaków </br>Min. jedna mała, duża litera oraz cyfra</span></td></tr>';
            $statusPassword = FALSE;
        }

        return $statusPassword;
    }
    
    function checkPoczta(){
        $status = FALSE;
        
        if(preg_match('/^[0-9]{2}[-][0-9]{3}$/D', $this->poczta)){
           $status = TRUE;
           return $status;
        }
        else{
            $_SESSION['error_poczta'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz w formacie 00-000!</span></td></tr>';
            return $status; 
        }  
    }

    //Funkcja porównująca wpisane hasła w formularzu, jeśli hasło powtórzone zgadza się zwraca TRUE
    //W przeciwnym wypadku zwraca FALSE
    function porownajPassword() {
        $status = TRUE;

        if ($this->password != $this->repeatPassword) {
            $status = FALSE;
            return $status;
        }

        return $status;
    }

    //Fukncja haszujaca haslo
    function has($password) {
        return hash("sha256", $password);
    }
    
    //Funkcja sprawdzająca czy na wpisane dane nie jest już utworzone konto w bazie
    function checkUserInDatabase($table){
        $status = FALSE;
        
        $baza = new baza();
        
        $connect = $baza->connectDatabase();
        
        switch ($table) {
            case "contact":
                $query = sprintf("SELECT * FROM users_contact WHERE imie='%s' AND nazwisko='%s' AND miasto='%s' AND poczta='%s' AND ulica='%s' AND nr_domu='%s' AND nr_mieszkania='%s'",
		mysqli_real_escape_string($connect,$this->imie),
                mysqli_real_escape_string($connect,$this->nazwisko),
                mysqli_real_escape_string($connect,$this->miasto),
                mysqli_real_escape_string($connect,$this->poczta),
                mysqli_real_escape_string($connect,$this->ulica),
                mysqli_real_escape_string($connect,$this->nrDomu),
                mysqli_real_escape_string($connect,$this->nrMieszkania));
                break;
            case "login":
                $query = sprintf("SELECT * FROM users_login WHERE login='%s'",
                    mysqli_real_escape_string($connect,$this->login));
                break;
            case "email":
                $query = sprintf("SELECT * FROM users_login WHERE email='%s'",
                    mysqli_real_escape_string($connect,$this->email));
                break;
        }
        if ($rezultat = $connect->query($query)){
            //Poprawne zapytanie do bazy
            $ilu_userow = $rezultat->num_rows;
                
                if($ilu_userow>0){
                    if($table == "contact"){
                        $_SESSION['error_istniejeContact'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Istnieje konto na podane Imię, nazwisko oraz adres.</span></td></tr>';
                    }
                    if($table == "login"){
                        $_SESSION['error_istniejeLogin'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisany login jest zajęty.</span></td></tr>';
                    }
                    if($table == "email"){
                        $_SESSION['error_istniejeEmail'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Na podany adres e-mail założone jest konto.</span></td></tr>';
                    }
                    $status = TRUE;
                }
             
        } else {
            //Błąd w zapytaniu do bazy
            $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
            $status = FALSE;
            
        }
        $baza->disconnectDatabase($connect);
        return $status;
        
    }

    
    //Funkcja dodająca nowego użytkownika w zależności od parametru $table wykona się inne INSERT INTO
    //Dodatkowo jeśli dodajemy do tabeli Contact zapisuje nam do zmiennej ID zapisanego rekordu
    //W bazie dane które są stringiem zapisuje wszystko z małej litery. Będzie mi to pomagało w porównywaniu
    //np czy dany login juz istnieje w bazie
    
    function addUser($table) {

        $status = TRUE;
        
        $baza = new baza();
        
        $connect = $baza->connectDatabase();

        switch ($table) {
            case "contact":
                $query = sprintf("INSERT INTO users_contact (imie, drugie_imie, nazwisko, miasto, poczta, ulica, nr_domu, nr_mieszkania, telefon) "
                        . "VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
                    mysqli_real_escape_string($connect,$this->imie),
                    mysqli_real_escape_string($connect,$this->drugieImie),
                    mysqli_real_escape_string($connect,$this->nazwisko),
                    mysqli_real_escape_string($connect,$this->miasto),
                    mysqli_real_escape_string($connect,$this->poczta),
                    mysqli_real_escape_string($connect,$this->ulica),
                    mysqli_real_escape_string($connect, $this->nrDomu),
                    mysqli_real_escape_string($connect, $this->nrMieszkania),
                    mysqli_real_escape_string($connect, $this->telefon));
                break;
            case "login":
                $query = sprintf("INSERT INTO users_login (login, email, password, typ_konta, created_at, ID_users_contact) "
                        . "VALUES ('%s', '%s', '%s', '%s', '%s', '%s');",
                    mysqli_real_escape_string($connect,$this->login),
                    mysqli_real_escape_string($connect,$this->email),
                    mysqli_real_escape_string($connect, $this->password),
                    mysqli_real_escape_string($connect, $this->typKonta),
                    mysqli_real_escape_string($connect, date("Y-m-d H:i:s")),
                    mysqli_real_escape_string($connect, $this->getIdContact()));
                break;
        }
        if ($rezultat = $connect->query($query)) {
            //Poprawne dodano do bazy
            if($table == "contact"){
                $this->setIdContact(mysqli_insert_id($connect));     
            }
            $baza->disconnectDatabase($connect);
            return $status;
             
        } else {
            //Błąd w zapytaniu do bazy
            $_SESSION['blad_select'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w zapytaniu do bazy.</span></td></tr>';
            $status = FALSE;
            
        }
        $baza->disconnectDatabase($connect);
        return $status;
    }

    //Funkcja usuwająca wszyskie zmienne sesyjne z formularza rejestracji
    function unlockFormSession() {

        if (isset($_SESSION['login'])) {
            unset($_SESSION['login']);
        }

        if (isset($_SESSION['email'])) {
            unset($_SESSION['email']);
        }

        if (isset($_SESSION['imie'])) {
            unset($_SESSION['imie']);
        }

        if (isset($_SESSION['nazwisko'])) {
            unset($_SESSION['nazwisko']);
        }

        if (isset($_SESSION['miasto'])) {
            unset($_SESSION['miasto']);
        }

        if (isset($_SESSION['poczta'])) {
            unset($_SESSION['poczta']);
        }

        if (isset($_SESSION['ulica'])) {
            unset($_SESSION['ulica']);
        }

        if (isset($_SESSION['nrDomu'])) {
            unset($_SESSION['nrDomu']);
        }

        if (isset($_SESSION['telefon'])) {
            unset($_SESSION['telefon']);
        }

        if (isset($_SESSION['drugieImie'])) {
            unset($_SESSION['drugieImie']);
        }

        if (isset($_SESSION['nrMieszkania'])) {
            unset($_SESSION['nrMieszkania']);
        }
    }

    //Gettery i Settery
    function getLogin() {
        return $this->login;
    }

    function setLogin($login) {
        $this->login = $login;
    }

    function getEmail() {
        return $this->email;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function getPassword() {
        return $this->password;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function getRepeatPassword() {
        return $this->repeatPassword;
    }

    function setRepeatPassword($repeatPassword) {
        $this->repeatPassword = $repeatPassword;
    }

    function getImie() {
        return $this->imie;
    }

    function setImie($imie) {
        $this->imie = $imie;
    }

    function getDrugieImie() {
        return $this->drugieImie;
    }

    function setDrugieImie($drugieImie) {
        $this->drugieImie = $drugieImie;
    }

    function getNazwisko() {
        return $this->nazwisko;
    }

    function setNazwisko($nazwisko) {
        $this->nazwisko = $nazwisko;
    }

    function getMiasto() {
        return $this->miasto;
    }

    function setMiasto($miasto) {
        $this->miasto = $miasto;
    }

    function getPoczta() {
        return $this->poczta;
    }

    function setPoczta($poczta) {
        $this->poczta = $poczta;
    }

    function getUlica() {
        return $this->ulica;
    }

    function setUlica($ulica) {
        $this->ulica = $ulica;
    }

    function getNrDomu() {
        return $this->nrDomu;
    }

    function setNrDomu($nrDomu) {
        $this->nrDomu = $nrDomu;
    }

    function getNrMieszkania() {
        return $this->nrMieszkania;
    }

    function setNrMieszkania($nrMieszkania) {
        $this->nrMieszkania = $nrMieszkania;
    }

    function getTelefon() {
        return $this->telefon;
    }

    function setTelefon($telefon) {
        $this->telefon = $telefon;
    }
    
    function setIdContact($idContact){
        $this->idContact = $idContact;
    }
    
    function  getIdContact(){
        return $this->idContact;
    }
    
    function setTypKonta($typKonta){
        $this->typKonta = $typKonta;
    }
    
    function  getTypKonta(){
        return $this->typKonta;
    }

}
