<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$reparto= $_GET['reparto'];
$nuovoNome= $_GET['nuovoNome'];
if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT nome
    FROM reparti WHERE nome='".$reparto."' AND supermercato='".$supermercato."'";


    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);

    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {

        if (pg_num_rows($result) == 0) {
            // Problem here!
            echo "Non esiste quel reparto in quel supermercato. Forse lo volevi inserire?";
            exit();
        }else {

            $queryInsert="UPDATE reparti SET nome = '".$nuovoNome."'
                            WHERE nome = '".$reparto."' AND supermercato='".$supermercato."';";

            $result=pg_query($conn, $queryInsert);
            $status = pg_result_status($result, PGSQL_STATUS_STRING);

            if (!$result) {
                echo "Si è verificato un errore.<br/>";
                echo pg_last_error($conn);
            } else {


                echo "Modifica avvenuta con successo";

            }
        }

    }
}

?>