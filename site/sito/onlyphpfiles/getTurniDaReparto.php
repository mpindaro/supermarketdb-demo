<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$reparto = $_GET['reparto'];
if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT giorno,\"inizioOra\",\"fineOra\", nome||' '||cognome as impiegato
FROM turni join impiegati i on turni.impiegato = i.matricola join persone p on i.persona = p.\"codF\"
WHERE impiegato IN (SELECT matricola
                    FROM impiegati
                    WHERE \"nomeReparto\"='".$reparto."' AND supermercato='".$supermercato."'
                    )
ORDER BY
     CASE
          WHEN giorno = 'Lunedì' THEN 1
          WHEN giorno = 'Martedì' THEN 2
          WHEN giorno = 'Mercoledì' THEN 3
          WHEN giorno = 'Giovedì' THEN 4
          WHEN giorno = 'Venerdì' THEN 5
          WHEN giorno = 'Sabato' THEN 6
          WHEN giorno = 'Domenica' THEN 7
     END ASC, \"inizioOra\"";
    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);
    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {


        echo "[";
        $i=0;
        while ($row = pg_fetch_array($result)) {
            echo '{'.'"impiegato":"'.$row['impiegato'].'", "giorno":"'.$row['giorno'].'","inizioOra":"'.$row['inizioOra'].'","fineOra":"'.$row['fineOra'].'"}';
            if ($i<pg_num_rows($result)-1){
                echo ",";
            }
            $i++;
        };
        echo "]";

    };
};

?>