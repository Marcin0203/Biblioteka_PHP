<?php

if(!(isset($_SESSION['wypozycz_process']))){
    header('Location: index.php');
    exit();
}
else{
    unset($_SESSION['wypozycz_process']);
}

//Obsługa przycisku wypożycz
if (isset($_REQUEST['wypozycz'])){
    if (wypozycz(htmlspecialchars(trim($_REQUEST['wypozycz'])))){
        $_SESSION['pracownik_wypozyczono'] = "";
        header('Location: wypozycz_page.php');
    }  
}

//Obsługa przycisku anuluj rezerwację gdy rezerwacja minęła
if (isset($_REQUEST['anuluj_minelo'])){
    if (anuluj(htmlspecialchars(trim($_REQUEST['anuluj_minelo'])),"Rezerwacja minęła")){
        $_SESSION['pracownik_anulowano'] = "";
        header('Location: wypozycz_page.php');
    }  
}

//Obsługa przycisku anuluj rezerwację
if (isset($_REQUEST['anuluj'])){
    if (anuluj(htmlspecialchars(trim($_REQUEST['anuluj'])),"Pracownik anulował")){
        $_SESSION['pracownik_anulowano'] = "";
        header('Location: wypozycz_page.php');
    }  
}

//Obsługa przycisku oddaj
if (isset($_REQUEST['oddaj'])){
    if (oddaj(htmlspecialchars(trim($_REQUEST['oddaj'])))){
        $_SESSION['pracownik_oddano'] = "";
        header('Location: wypozycz_page.php');
    }  
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
     echo $tresc;
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
     echo $tresc;
}