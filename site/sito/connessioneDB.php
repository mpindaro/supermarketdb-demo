<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
if (!$conn){
	echo 'Connessione al database fallita.';

} else {
	echo "Connessione riuscita.<br/>";
	$query = "SELECT codice FROM supermercati";
                                $result=pg_query($conn, $query);
                                $status = pg_result_status($result, PGSQL_STATUS_STRING);
                                if (!$result) {
                                    echo "Si è verificato un errore.<br/>";
                                    echo pg_last_error($conn);
                                } else {
                                    while ($row = pg_fetch_array($result)) {
                                        echo '<option value="$row[\'codice\']">'. $row['codice'].'</option>';
                                    };
                                };
                            };
?>