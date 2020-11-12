<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title>Gestione impiegati</title>

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
            <h4>Modifica e inserimento di un impiegato</h4>

            <div class="switch col s12 center">
                <label>
                    Inserimento
                    <input id="op" type="checkbox">
                    <span class="lever"></span>
                    Modifica
                </label>
            </div>


            <div class="col s4">
                <input placeholder="Codice fiscale" id="codF" type="text" class="validate">
            </div>
            <div class="col s4">
                <input placeholder="Mansione" id="mansione" type="text" class="validate">
            </div>
            <div class="col s4">
                <input placeholder="Livello" id="livello" type="text" class="validate">
            </div>
            <div id="divSoloInserimento">
                <div class="col s4">
                    <input placeholder="Nome" id="nome" type="text" class="validate">
                </div>
                <div class="col s4">
                    <input placeholder="Cognome" id="cognome" type="text" class="validate">
                </div>
                <div class="col s4">
                    <input placeholder="Indirizzo" id="indirizzo" type="text" class="validate">
                </div>
                <div class="col s4">
                    <input placeholder="Telefono" id="telefono" type="text" class="validate">
                </div>
                <div class="col s4">
                    <input placeholder="Data di nascita" id="dataNascita" type="text" class="datepicker">
                </div>
                <div class="col s4">
                    <input placeholder="Mail" id="mail" type="email" class="validate">
                </div>
            </div>

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
            <div class="input-field col s4">
                <select id="reparto">
                    <option value="" disabled selected>Reparto</option>

                </select>
                <label>Reparto</label>
            </div>

            <div class="input-field col s4">
                <button class="btn waves-effect waves-light"onclick="inviaDati()" id="invio"> Inserisci
                    <i class="material-icons right">send</i>
                </button>
            </div>

            <div class="col s12" id="risposta">

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

    $(document).ready(function () {
        $('.datepicker').datepicker({
            defaultDate: new Date(1980, 1, 31),
            format: "yyyy-mm-dd"
        });
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
                document.getElementById("divSoloInserimento").style.display = 'none';
                document.getElementById("codF").id = "matricola";
                document.getElementById("matricola").placeholder = "Matricola";
            } else {
                operazione = "inserimento"
                document.getElementById("invio").innerHTML = "Inserisci <i class=\"material-icons right\">send</i>";
                document.getElementById("divSoloInserimento").style.display = 'block';
                document.getElementById("matricola").id = "codF";
                document.getElementById("codF").placeholder = "Codice Fiscale";
            }
        })
    });

    function inviaDati() {
        console.log("clicked")
        if (operazione == "modifica") {
            matricola = document.getElementById('matricola').value
            mansione = document.getElementById('mansione').value
            livello = document.getElementById('livello').value
            supermercato = document.getElementById('supermercato').value
            reparto = document.getElementById('reparto').value

            if (matricola=="" || mansione==""|| livello==""){
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
                    // M.toast({html: this.responseText, classes: 'rounded'});
                    document.getElementById("risposta").innerHTML=this.responseText;
                }
            };
            xhttp.open("GET", "onlyphpfiles/setImpiegato.php" + "?matricola=" + matricola + "&mansione=" + mansione + "&livello=" + livello + "&supermercato=" + supermercato + "&reparto=" + reparto, true);
            xhttp.send();
        } else {
            codF = document.getElementById('codF').value
            mansione = document.getElementById('mansione').value
            livello = document.getElementById('livello').value
            supermercato = document.getElementById('supermercato').value
            reparto = document.getElementById('reparto').value
            nome = document.getElementById('nome').value
            cognome = document.getElementById('cognome').value
            indirizzo = document.getElementById('indirizzo').value
            telefono = document.getElementById('telefono').value
            mail = document.getElementById('mail').value
            dataNascita = document.getElementById('dataNascita').value
            dataAssunzione = new Date().toISOString().split('T')[0]

            if (codF=="" || mansione==""|| livello==""|| nome==""|| cognome==""|| indirizzo==""|| mail=="" || telefono==""){
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
                    document.getElementById("risposta").innerHTML=this.responseText;
                }
            };
            xhttp.open("GET", "onlyphpfiles/newImpiegato.php?" + "cofF=" + codF + "&mansione=" + mansione + "&livello=" + livello + "&supermercato=" + supermercato + "&reparto=" + reparto + "&nome=" + nome + "&cognome=" + cognome + "&indirizzo=" + indirizzo + "&telefono=" + telefono + "&mail=" + mail + "&dataNascita=" + dataNascita + "&dataAssunzione=" + dataAssunzione.toString(), true);
            xhttp.send();
        }
        return false;
    }
</script>
</body>
</html>
