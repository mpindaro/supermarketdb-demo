<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$reparto = $_GET['reparto'];
$matricola = $_GET['matricola'];
$mansione = $_GET['mansione'];
$livello = $_GET['livello'];
if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT matricola
    FROM impiegati
    WHERE matricola=".$matricola;


    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);

    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {

        if (pg_num_rows($result) == 0) {
            // Problem here!
            echo "Non esiste nessun dipendente con questa matricola. Forse volevi fare un inserimento?";
            exit();
        }else {

            $queryUpdate="UPDATE impiegati SET mansione = '".$mansione."', livello=".$livello.", supermercato='".$supermercato."', \"nomeReparto\"='".$reparto."' 
                            WHERE matricola = ".$matricola;


            $result=pg_query($conn, $queryUpdate);
            $status = pg_result_status($result, PGSQL_STATUS_STRING);

            if (!$result) {
                echo "Si è verificato un errore.<br/>";
                echo pg_last_error($conn);
            } else {
                echo "Modifica avvenuta con successo";
            }
        }

    };
};

?>