<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT nome FROM reparti WHERE supermercato='".$supermercato."' ORDER BY reparti";
    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);
    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {
        while ($row = pg_fetch_array($result)) {
            echo ' <option value="'.$row['nome'].'"> '. $row['nome'].' </option>';
        };
    };
};
?>