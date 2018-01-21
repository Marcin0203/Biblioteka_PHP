<?php

if(!isset($_SESSION)){                  //Sprawdzenie czy sesja nie istnieje
    session_start();                    //Jeśli nie istenieje, Start sesji
}

$_SESSION['baza'] = "";                 //utworzenie zmiennej sesyjne dajacej dostep do dołączenia pliku baza.php
require_once 'baza.php';                //Dołączenie klasy baza.php

if(!(isset($_SESSION['userLogin']))){   //Sprawdzenie czy NIE istnieje zmienna sesyjna userLogin
    header('Location: index.php');      //Jeśli nie istnieje przenieś na index.php
    exit();                             //exit
}
else{
    unset($_SESSION['userLogin']);      //Jeśli istnieje usuń zmienną sesyjną
}

class userLogin {                       //Klasa reprezentująca użytkowników podczas logowania
    
    private $login;
    private $password;
    
    
    //Fukncja haszujaca haslo
    function has($password) {
        return hash("sha256", $password);
    }
    
    //Funkcja walidująca formularz logowania zwraca TRUE lub FALSE
    function walidacja(){
        $status = TRUE;
        
        if($this->checkNotNullInput()){
            if($this->login()){
                return $status;
            }
            else{
                $status = FALSE;
                return $status;
            }
        }
        else{
            $status = FALSE;
            return $status;
        }
    }
    
    //Funkcja sprawdzająca czy wszystkie pola zostały wpisane zwraca TRUE lub FALSE
    function checkNotNullInput() {

        $status = TRUE;

        if ($this->login == "") {
            $_SESSION['error_login'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz login!</span></td></tr>';
            $status = FALSE;
        }
        
        if ($this->password == "") {
            $_SESSION['error_password'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Wpisz hasło!</span></td></tr>';
            $status = FALSE;
        }
        
        return $status;
     }
    
    //Funkcja logująca. Zwraca TRUE lub FALSE 
    function login(){
        $status = TRUE;
        $this->password = $this->has($this->password);
        
        if ($this->checkInDatabase()){
            if($this->CheckLogged()){
                if($this->addLogged()){
                    return $status;
                }
                else{
                    $status = FALSE;
                    return $status;
                }  
            }
            else{
                $status = FALSE;
                return $status;
            }
            
        }
        else{
            $status = FALSE;
            return $status;
        }
    } 

    //Funckja sprawdzające czy istnieje konto na podane dane. Zwraca TRUE lub FALSE
    function checkInDatabase(){
        
        $status = FALSE;
        
        $baza = new baza();
        
        $connect = $baza->connectDatabase();
        
        $query = sprintf("SELECT * FROM users_login WHERE login='%s' AND password='%s'",
            mysqli_real_escape_string($connect, $this->login),
            mysqli_real_escape_string($connect, $this->password));
        
         if ($rezultat = $connect->query($query)){
            //Poprawne zapytanie do bazy
             
            $ilu_userow = $rezultat->num_rows;
                
            if($ilu_userow>0){
                while ($row = $rezultat->fetch_object()) {
                    $_SESSION['id_sesji'] = session_id();
                    $_SESSION['id_user'] = $row->ID;
                    $_SESSION['typ_konta'] = $row->typ_konta;
                }
                $status = TRUE;
                $rezultat->close();
                $baza->disconnectDatabase($connect);
                return $status;
            }
            else{
                $_SESSION['error_brak_konta'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błędny login lub hasło.</span></td></tr>';
                $rezultat->close();
                $baza->disconnectDatabase($connect);
                return $status;
            }
         }    
    }
    
    //Funkcja sprawdzająca czy dany użytkownik który się zalogował nie ma wpisów w tabeli logged i usunięcie jeśli są takie wpisy
    function CheckLogged (){
        
        $status = FALSE;
        $baza = new baza();
        $connect = $baza->connectDatabase();
        $id = $_SESSION['id_user'];
        
        $query = sprintf("SELECT * FROM logged_users WHERE users_id='%s'",
		mysqli_real_escape_string($connect,$id));
        
        if ($connect->connect_errno == 0){
            //Połączono prawidłowo z bazą
            
            if ($rezultat = $connect->query($query)){
            //Poprawne zapytanie do bazy
                
            $ilu_userow = $rezultat->num_rows;
            if($ilu_userow>0){
                $connect->query(
                    sprintf("DELETE FROM logged_users WHERE users_id='%s'",
                    mysqli_real_escape_string($connect,$id))); 
            }
            $status = TRUE;
            $rezultat->close();
            $baza->disconnectDatabase($connect);
            return $status;
            }
        else{
                //Błąd w zapytaniu do bazy
                $_SESSION['blad_select'] = '<br/><span style="color:red">Błąd w zapytaniu do bazy.</span>';
            }  
  
        }
        else{
            //Błąd połączenia z bazą
            $_SESSION['blad_polaczenia'] = '<br/><span style="color:red">Błąd w połączeniu z bazą. '
                                            . 'Nr błędu: '.$connect->connect_errno.'</span>';
        }
        $baza->disconnectDatabase($connect);
        return $status;
    }
    
    
    //Funkcja dodając wpis w tabeli logged gdy użytkownik pomyślnie się zaloguje.
    function addLogged(){
        $status = FALSE;
        $id_session = $_SESSION['id_sesji'];
        $id_user = $_SESSION['id_user'];

        $baza = new baza();
        $connect = $baza->connectDatabase();
        
        $query = sprintf("INSERT INTO logged_users (session_id, users_id, last_update) VALUES ('%s', '%s', '%s');",
                    mysqli_real_escape_string($connect,$id_session),
                    mysqli_real_escape_string($connect,$id_user),
                    mysqli_real_escape_string($connect,date("Y-m-d H:i:s")));
        
        if ($connect->connect_errno == 0){
            
            //Połączono prawidłowo z bazą
            
            if ($rezultat = $connect->query($query)){  
                //Poprawne dodano do bazy
                $status = TRUE;
                $baza->disconnectDatabase($connect);
                return $status;
            }
            else{
                //Błąd w zapytaniu do bazy
                $_SESSION['blad_select'] = '<br/><span style="color:red">Błąd w zapytaniu do bazy.</span>';
            }    
        }
        else{
            //Błąd połączenia z bazą
            $_SESSION['blad_polaczenia'] = '<br/><span style="color:red">Błąd w połączeniu z bazą. '
                                            . 'Nr błędu: '.$connect->connect_errno.'</span>';
        }
        $baza->disconnectDatabase($connect);
        return $status;
        
    }
    
    //Funkcja sprawdzająca czy dane id sesji znajduję się w tabeli logged users.
    function getLoggedInUser($id){
        
        $baza = new baza();
        $connect = $baza->connectDatabase();
        
        if ($connect->connect_errno == 0){
            //Połączono prawidłowo z bazą
            
            if ($rezultat = $connect->query(
		sprintf("SELECT * FROM logged_users WHERE session_id='%s'",
		mysqli_real_escape_string($connect,$id)))){

            //Poprawne zapytanie do bazy
                
            $ilu_userow = $rezultat->num_rows;
            
            if($ilu_userow>0){
                $baza->disconnectDatabase($connect);
                return 1;           //wynik 1 - znaleziono wpis z id sesji w tabeli logged_users
            }
            else{
                $baza->disconnectDatabase($connect);
                return -1;          //wynik -1 - nie ma wpisu dla tego id w tabeli logged_users
            }
            
            }
        else{
                //Błąd w zapytaniu do bazy
                $_SESSION['blad_select'] = '<br/><span style="color:red">Błąd w zapytaniu do bazy.</span>';
            }  
  
        }
        else{
            //Błąd połączenia z bazą
            $_SESSION['blad_polaczenia'] = '<br/><span style="color:red">Błąd w połączeniu z bazą. '
                                            . 'Nr błędu: '.$connect->connect_errno.'</span>';
        } 
    }
    
    //Funkcja do wylogowywania
    function logout(){ 
        $status = FALSE;
        $baza = new baza();
        $connect = $baza->connectDatabase();
        $id = $_SESSION['id_user'];
        
        if ($connect->connect_errno == 0){

            //Połączono prawidłowo z bazą
            
            if ($rezultat = $connect->query(
		sprintf("SELECT * FROM logged_users WHERE users_id='%s'",
		mysqli_real_escape_string($connect,$id)))){
                //Poprawne zapytanie do bazy
                
                $ilu_userow = $rezultat->num_rows;

                if($ilu_userow>0){
                    $connect->query(
                        sprintf("DELETE FROM logged_users WHERE users_id='%s'",
                        mysqli_real_escape_string($connect,$id)));
                        $baza->disconnectDatabase($connect);
                        $status = TRUE;
                        return $status;
                }
            }
        else{
                //Błąd w zapytaniu do bazy
                $_SESSION['blad_select'] = '<br/><span style="color:red">Błąd w zapytaniu do bazy.</span>';
            }  
        }
        else{
            //Błąd połączenia z bazą
            $_SESSION['blad_polaczenia'] = '<br/><span style="color:red">Błąd w połączeniu z bazą. '
                                            . 'Nr błędu: '.$connect->connect_errno.'</span>';
        }
        $baza->disconnectDatabase($connect);
        return $status;
    }

    //Gettery i settery
    function getLogin(){
        return $this->login;
    }
    
    function setLogin($login){
        $this->login = $login;
    }
    
    function getPassword(){
        return $this->password;
    }
    
    function setPassword($password){
        $this->password = $password;
    }
}
