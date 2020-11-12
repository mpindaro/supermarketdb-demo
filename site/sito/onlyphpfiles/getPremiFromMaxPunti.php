<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
$punti = $_GET['punti'];

if (!is_numeric($punti)){
    echo "Devi inserire un numero";
    exit();
}


if (!$conn) {
    echo 'Connessione al database fallita.';

} else {

    //Senza pagare
    $query = "SELECT nome, categoria, \"puntiNecessari\" AS punti, \"dataFineValidita\" as datascadenza
                FROM premi JOIN \"premiSpeciali\" pS on premi.codice = pS.premio
                WHERE \"dataFineValidita\">current_date AND \"puntiNecessari\"<=" . $punti . "
                ORDER BY categoria";
    $result = pg_query($conn, $query);
    $status = pg_result_status($result, PGSQL_STATUS_STRING);
    if (!$result) {
        echo "Si è verificato un errore.<br/>";
        echo pg_last_error($conn);
    } else {

        echo "<h5> Usando solo punti: </h5> ";
        echo "<table class='striped highlight'>
        <thead>
          <tr>
              <th>Nome</th>
              <th>Categoria</th>
              <th>Punti Necessari</th>
              <th>Disponibile fino al</th>
          </tr>
        </thead>

        <tbody>";
        while ($row = pg_fetch_array($result)) {
            echo "<tr>
                    <td>" . $row['nome'] . "</td>
                    <td>" . $row['categoria'] . "</td>
                    <td>" . $row['punti'] . "</td>
                   <td>" . $row['datascadenza'] . "</td> ";
        };
        echo "</tbody>
             </table>";

        //Pagando
        $queryPagando = "SELECT nome, categoria, \"partePunti\"  AS punti, \"dataFineValidita\" as datascadenza, \"parteDenaro\" as costo
                        FROM premi JOIN \"premiSpeciali\" pS on premi.codice = pS.premio
                        WHERE \"dataFineValidita\">current_date AND \"partePunti\"<=" . $punti . "
                        ORDER BY categoria
                        ";
        $result = pg_query($conn, $queryPagando);
        $status = pg_result_status($result, PGSQL_STATUS_STRING);
        if (!$result) {
            echo "Si è verificato un errore.<br/>";
            echo pg_last_error($conn);
        } else {
            echo "<h5> Oppure, pagando una somma in denaro: </h5> ";
            echo "<table class='striped highlight'>
        <thead>
          <tr>
              <th>Nome</th>
              <th>Categoria</th>
              <th>Punti Necessari</th>
              <th>Disponibile fino al</th>
              <th>Costo</th>
          </tr>
        </thead>

        <tbody>";
            while ($row = pg_fetch_array($result)) {
                echo "<tr>
                    <td>" . $row['nome'] . "</td>
                    <td>" . $row['categoria'] . "</td>
                    <td>" . $row['punti'] . "</td>
                   <td>" . $row['datascadenza'] . "</td> 
                <td>" . $row['costo'] . " euro </td> ";
            };
            echo "</tbody>
             </table>";

        }

    };
};

?>