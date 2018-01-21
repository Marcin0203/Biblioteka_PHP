<?php
$_SESSION['baza'] = "";
require_once 'baza.php';

if(!(isset($_SESSION['moje_ksiazki_process']))){
    header('Location: index.php');
    exit();
}
else{
    unset($_SESSION['moje_ksiazki_process']);
}

//Obsługa przycisku anuluj rezerwację
if (isset($_REQUEST['anuluj'])){
    if (anuluj_rezerwacje(htmlspecialchars(trim($_REQUEST['anuluj'])))){
        $_SESSION['anulowano'] = "";
        header('Location: moje_ksiazki.php');
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
     echo $tresc;
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
     echo $tresc;
}

