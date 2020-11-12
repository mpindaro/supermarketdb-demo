<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title>Turni in un Reparto</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>

<?php include 'navbar.php'; ?>

<?php
$conn = pg_connect("host=localhost port=5432 dbname=CatenaDiSupermercati user=postgres password=postgres");
if (!$conn){
    echo 'Connessione al database fallita.';

} else { ?>

<div class="container">
    <div class="section flex">
        <div class="row">
            <h4>Turni settimanali in un reparto</h4>
            <div class="input-field col s6">
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
            <div class="input-field col s6">
                <select id="reparto" onchange="getTurni()">
                    <option value="" disabled selected>Reparto</option>

                </select>
                <label>Reparto</label>
            </div>
            <div class="col s12" id="turni">

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

    function getTurni() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var risposta = JSON.parse(this.responseText);
                toTabella(risposta);
                
            }
        };
        xhttp.open("GET", "onlyphpfiles/getTurniDaReparto.php?supermercato=" + document.getElementById("supermercato").value+ "&reparto=" + document.getElementById("reparto").value, true);
        xhttp.send();
    }
    
    function toTabella(infoJson) {

        var dictionary = {'Lunedi': [],'Martedi': [],'Mercoledi': [],'Giovedi': [],'Venerdi': [],'Sabato': [],'Domenica': []};

        for (var i = 0; i < infoJson.length; i++){
            var obj = infoJson[i];
            dictionary[obj['giorno']].push(obj['impiegato']+" "+obj['inizioOra']+"-"+obj['fineOra']);
        }
        console.log(dictionary);


        document.getElementById("turni").innerHTML="<h5> Lunedì </h5> <br> "

        for (let i = 0; i < dictionary['Lunedi'].length ; i++) {
            document.getElementById("turni").innerHTML+= dictionary["Lunedi"][i] + "<br>";
        }

        document.getElementById("turni").innerHTML+="<h5>Martedì<h5> "
        for (let i = 0; i < dictionary['Martedi'].length ; i++) {
            document.getElementById("turni").innerHTML+=dictionary["Martedi"][i] +"</br>";
        }

        document.getElementById("turni").innerHTML+="<h5>Mercoledì</h5> "
        for (let i = 0; i < dictionary['Mercoledi'].length ; i++) {
            document.getElementById("turni").innerHTML+=dictionary["Mercoledi"][i] +"</br>";
        }

        document.getElementById("turni").innerHTML+="<h5>Giovedì</h5> "
        for (let i = 0; i < dictionary['Giovedi'].length ; i++) {
            document.getElementById("turni").innerHTML+=dictionary["Giovedi"][i] +"</br>";
        }

        document.getElementById("turni").innerHTML+="<h5>Venerdì</h5>"
        for (let i = 0; i < dictionary['Venerdi'].length ; i++) {
            document.getElementById("turni").innerHTML+=dictionary["Venerdi"][i] +"</br>";
        }

        document.getElementById("turni").innerHTML+="<h5>Sabato</h5> "
        for (let i = 0; i < dictionary['Sabato'].length ; i++) {
            document.getElementById("turni").innerHTML+=dictionary["Sabato"][i] +"</br>";
        }

        document.getElementById("turni").innerHTML+="<h5>Domenica</h5> "
        for (let i = 0; i < dictionary['Domenica'].length ; i++) {
            document.getElementById("turni").innerHTML+=dictionary["Domenica"][i] +"</br>";
        }

    }
</script>
</body>
</html>
