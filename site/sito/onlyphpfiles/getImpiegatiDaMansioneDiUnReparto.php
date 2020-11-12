<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$reparto = $_GET['reparto'];
$mansione = $_GET['mansione'];
if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT nome, cognome, \"dataAssunzione\" 
                FROM impiegati JOIN persone p ON impiegati.persona = p.\"codF\"
                WHERE impiegati.supermercato='".$supermercato."' AND \"nomeReparto\"='".$reparto."' AND mansione='".$mansione."'";
    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);
    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {
        echo "<table class='striped highlight'>
        <thead>
          <tr>
              <th>Nome</th>
              <th>Cognome</th>
              <th>Data Assunzione</th>
          </tr>
        </thead>

        <tbody>";
        while ($row = pg_fetch_array($result)) {
            echo "<tr>
                    <td>".$row['nome']."</td>
                    <td>".$row['cognome']."</td>
                    <td>".$row['dataAssunzione']."</td>";
        };
        echo "</tbody>
             </table>";
    };
};

?>