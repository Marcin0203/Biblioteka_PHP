<?php

// Klasa obslugująca połączenie z bazą

if(!(isset($_SESSION['baza']))){
    header('Location: index.php');
    exit();
}
else{
    unset($_SESSION['baza']);
}

class baza {
    
    private $host = "localhost";
    private $db_user = "root";
    private $db_password = "";
    private $db_name = "biblioteka";
    
    //Funkcja tworząca połączenie z bazą danych
    function connectDatabase() {

        $connect = mysqli_connect($this->host, $this->db_user, $this->db_password, $this->db_name);

        if ($connect->connect_errno == 0) {
            //połączono prawidłowo z bazą
            mysqli_query($connect, "SET NAMES 'utf8'");         //Ustawienie kodowania na UTF8, żeby w bazie dodawało polskie znaki.
            return $connect;
        } else {
            //Błąd połączenia z bazą
            $_SESSION['blad_polaczenia'] = '<tr><td></td><td class="formularz_td_error"><span class="error">Błąd w połączeniu z bazą.'
                    . 'Nr błędu: ' . $connect->connect_errno . '</span></td></tr>';
        }
    }
    
    //Funkcja kończąca połączenie 
    function disconnectDatabase($connect){
        $connect->close();
    }
    
    //Gettery i Settery
    function getHost(){
        return $this->host;
    }
    
    function setHost($host){
        $this->host = $host;
    }
    
    function getDb_user(){
        return $this->db_user;
    }
    
    function setDb_user($db_user){
        $this->db_user = $db_user;
    }
    
    function setDb_password(){
        return $this->db_password;
    }
    
    function getDb_password($db_password){
        $this->db_password = $db_password;
    }
    
    function setDb_name(){
        return $this->db_name;
    }
    
    function getDb_name($db_name){
        $this->db_name = $db_name;
    }
    
}


