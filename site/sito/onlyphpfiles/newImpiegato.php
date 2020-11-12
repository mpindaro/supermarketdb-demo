<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$reparto = $_GET['reparto'];
$codF = $_GET['cofF'];
$mansione = $_GET['mansione'];
$livello = $_GET['livello'];
$nome = $_GET['nome'];
$cognome = $_GET['cognome'];
$indirizzo = $_GET['indirizzo'];
$telefono = $_GET['telefono'];
$mail = $_GET['mail'];
$dataNascita = $_GET['dataNascita'];
$dataAssunzione = $_GET['dataAssunzione'];


if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT persona
    FROM impiegati WHERE persona='".$codF."'";


    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);

    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {

        if (pg_num_rows($result) != 0) {
            // Problem here!
            echo "Esiste già un dipendente con quel codice fiscale. Forse volevi fare una modifica?";
            exit();
        }else {

            $queryControlloEsistenzaPersona="SELECT \"codF\"
            FROM persone WHERE \"codF\"='".$codF."'";


            $result=pg_query($conn, $queryControlloEsistenzaPersona);
            $status = pg_result_status($result, PGSQL_STATUS_STRING);

            if (!$result) {
                echo "Si è verificato un errore.<br/>";
                echo pg_last_error($conn);
            } else {
                if (pg_num_rows($result) == 0) {
                    $queryInserimentoPersona="INSERT INTO persone (\"codF\", nome, cognome, indirizzo, telefono, \"dataNascita\", mail)
                                                VALUES ('".$codF."', '".$nome."', '".$cognome."', '".$indirizzo."', '".$telefono."', '".$dataNascita."', '".$mail."')";
                    $result=pg_query($conn, $queryInserimentoPersona);
                    $status = pg_result_status($result, PGSQL_STATUS_STRING);

                    if (!$result) {
                        echo "Si è verificato un errore.<br/>";
                        echo pg_last_error($conn);
                        exit();
                    }
                }

                $queryNuovoDipendente="INSERT INTO impiegati (mansione, livello, \"nomeReparto\", supermercato, \"dataAssunzione\",persona)
                    VALUES ('".$mansione."', ".$livello.", '".$reparto."', '".$supermercato."', '".$dataAssunzione."', '".$codF."');";
                $result=pg_query($conn, $queryNuovoDipendente);
                $status = pg_result_status($result, PGSQL_STATUS_STRING);

                if (!$result) {
                    echo "Si è verificato un errore.<br/>";
                    echo pg_last_error($conn);
                    exit();
                }else{
                    echo "Inserimento avvenuto con successo";
                }

            }
        }

    }
}

?>