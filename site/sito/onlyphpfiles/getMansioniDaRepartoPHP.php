<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$reparto = $_GET['reparto'];
if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT DISTINCT mansione 
                FROM impiegati 
                WHERE supermercato='".$supermercato."' AND \"nomeReparto\"='".$reparto."'";
    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);
    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {
        while ($row = pg_fetch_array($result)) {
            echo ' <option value="'.$row['mansione'].'"> '. $row['mansione'].' </option>';
        };

    };
};




?>
