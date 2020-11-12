<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title>Mansioni in Uso</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
if (!$conn){
    echo 'Connessione al database fallita.';

} else { ?>
<?php include 'navbar.php'; ?>


<div class="container">
    <div class="section flex">
        <div class="row">
            <h4>Mansioni in un Reparto</h4>
            <div class="input-field col s4">
                <!--Selezione supermercato-->
                <select id='supermercato' onchange="getReparti()">
                    <option value="" disabled selected>Supermercato</option>
                    <?php

                    $query = "SELECT codice FROM supermercati";
                    $result = pg_query($conn, $query);
                    $status = pg_result_status($result, PGSQL_STATUS_STRING);
                    if (!$result) {
                        echo "Si è verificato un errore.<br/>";
                        echo pg_last_error($conn);
                    } else {
                        while ($row = pg_fetch_array($result)) {
                            echo ' <option value= "' . $row['codice'] . '" >' . $row['codice'] . '</option>';
                        };
                    };
                    };
                    ?>
                </select>
                <label>Supermercato</label>
            </div>

            <!--Selezione Reparto-->
            <div class="input-field col s4" onchange="getMansioni()">
                <select id="reparto" >
                    <option value="" disabled selected>Reparto</option>

                </select>
                <label>Reparto</label>
            </div>

            <!--Selezione Mansione-->
            <div class="input-field col s4">
                <select id="mansione" onchange="getImpiegati()">
                    <option value="" disabled selected>Mansione</option>

                </select>
                <label>Mansione</label>
            </div>
            <div class="col s12" id="divDipendenti">

            </div>
        </div>
    </div>

</div>
<?php include 'footer.php'; ?>


<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="js/materialize.js"></script>
<script src="js/init.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('select');
        var instances = M.FormSelect.init(elems);
    });

    function getReparti() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("reparto").innerHTML = " <option value=\"\" disabled selected>Reparto</option>" + this.responseText;
                var elems = document.querySelectorAll('select');
                var instances = M.FormSelect.init(elems);
            }
        };
        xhttp.open("GET", "onlyphpfiles/getRepartiDaSupermercatoPHP.php?supermercato=" + document.getElementById("supermercato").value, true);
        xhttp.send();
    }

    function getMansioni() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("mansione").innerHTML = "<option value=\"\" disabled selected>Mansione</option>" + this.responseText;
                var elems = document.querySelectorAll('select');
                var instances = M.FormSelect.init(elems);
            }
        };
        xhttp.open("GET", "onlyphpfiles/getMansioniDaRepartoPHP.php?supermercato=" + document.getElementById("supermercato").value + "&reparto=" + document.getElementById("reparto").value, true);
        xhttp.send();
    }

    function getImpiegati() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("divDipendenti").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "onlyphpfiles/getImpiegatiDaMansioneDiUnReparto.php?supermercato=" + document.getElementById("supermercato").value + "&reparto=" + document.getElementById("reparto").value + "&mansione=" + document.getElementById("mansione").value , true);
        xhttp.send();
    }

</script>

</body>
</html>
