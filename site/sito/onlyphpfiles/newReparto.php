<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$nomeReparto = $_GET['nomeReparto'];

if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT nome
    FROM reparti WHERE nome='".$nomeReparto."' AND supermercato='".$supermercato."'";

    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);

    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {

        if (pg_num_rows($result) != 0) {
            // Problem here!
            echo "Esiste già un reparto in quel supermercato con quel nome";
            exit();
        }else {

            $queryInsert="INSERT INTO reparti (supermercato, nome) 
                            VALUES ('".$supermercato."','".$nomeReparto."');";


            $result=pg_query($conn, $queryInsert);
            $status = pg_result_status($result, PGSQL_STATUS_STRING);

            if (!$result) {
                echo "Si è verificato un errore.<br/>";
                echo pg_last_error($conn);
            } else {


                echo "Inserimento avvenuto con successo";

            }
        }

    }
}

?>