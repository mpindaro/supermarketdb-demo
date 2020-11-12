<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title>Gestione reparti</title>

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
            <h4>Modifica e inserimento di un reparto</h4>

            <div class="switch col s12 center">
                <label>
                    Inserimento
                    <input id="op" type="checkbox">
                    <span class="lever"></span>
                    Modifica
                </label>
            </div>


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
            <div class="input-field col s6" id="divReparto" style="display: none">
                <select id="reparto">
                    <option value="" disabled selected>Reparto</option>

                </select>
                <label>Reparto</label>
            </div>

            <div class="input-field col s6">
                <input placeholder="Nome del nuovo reparto" id="nomeReparto" type="text" class="validate">
            </div>

            <div class="input-field col s6">
                <button class="btn waves-effect waves-light" onclick="inviaDati()" id="invio"> Inserisci
                    <i class="material-icons right">send</i>
                </button>
            </div>

            <div class="input-field col s6" id="risposta">

            </div>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>


<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="js/materialize.js"></script>
<script src="js/init.js"></script>
<script>
    var operazione = "inserimento"

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

    $(document).ready(function () {
        $("#op").change(function () {
            if ($(this).is(":checked")) {
                operazione = "modifica"
                document.getElementById("invio").innerHTML = "Modifica <i class=\"material-icons right\">send</i>";
                document.getElementById("divReparto").style.display = 'block';
                document.getElementById("nomeReparto").placeholder="Nuovo nome";
            } else {
                operazione = "inserimento"
                document.getElementById("invio").innerHTML = "Inserisci <i class=\"material-icons right\">send</i>";
                document.getElementById("divReparto").style.display = 'none';
                document.getElementById("nomeReparto").placeholder="Nome del nuovo reparto";
            }
        })
    });

    function inviaDati() {
        console.log("clicked")
        if (operazione == "modifica") {

            supermercato = document.getElementById('supermercato').value
            reparto = document.getElementById('reparto').value
            nuovoNome = document.getElementById('nomeReparto').value

            if (nuovoNome==""){
                document.getElementById("risposta").innerHTML="Compila tutti i campi"
                return;
            }


            if (supermercato=="" || reparto==""){
                document.getElementById("risposta").innerHTML="Devi selezionare un reparto e un supermercato"
                return;
            }

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("risposta").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "onlyphpfiles/setReparto.php" + "?supermercato=" + supermercato + "&reparto=" + reparto+ "&nuovoNome=" + nuovoNome, true);
            xhttp.send();
        } else {
            supermercato = document.getElementById('supermercato').value
            nomeReparto = document.getElementById('nomeReparto').value

            if (nomeReparto==""){
                document.getElementById("risposta").innerHTML="Compila tutti i campi"
                return;
            }


            if (supermercato=="" || reparto==""){
                document.getElementById("risposta").innerHTML="Devi selezionare un reparto e un supermercato"
                return;
            }


            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("risposta").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "onlyphpfiles/newReparto.php?"+"supermercato=" + supermercato + "&nomeReparto=" + nomeReparto , true);
            xhttp.send();
        }
        return false;
    }
</script>
</body>
</html>
