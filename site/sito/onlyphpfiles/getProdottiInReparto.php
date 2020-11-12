<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$supermercato = $_GET['supermercato'];
$reparto = $_GET['reparto'];
if (!$conn){
    echo 'Connessione al database fallita.';

} else {
    $query = "SELECT nome, qta, \"dataScadenza\", prezzo
FROM prodotti JOIN \"contenutoReparto\" ON prodotti.id = \"contenutoReparto\".prodotto
WHERE \"nomeReparto\"='".$reparto."' AND \"supermercatoReparto\"='".$supermercato."'
";
    $result=pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);
    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {


        echo "<table class='striped highlight'>
        <thead>
          <tr>
              <th>Nome Prodotto</th>
              <th>Quantità</th>
              <th>Data Scadenza</th>
              <th>Prezzo</th>
          </tr>
        </thead>

        <tbody>";
        while ($row = pg_fetch_array($result)) {
                echo "<tr>
                    <td>".$row['nome']."</td>
                    <td>".$row['qta']."</td>
                    <td>".$row['dataScadenza']."</td>
                   <td>".$row['prezzo']."</td> ";
        };
        echo "</tbody>
             </table>";
    };
};

?>