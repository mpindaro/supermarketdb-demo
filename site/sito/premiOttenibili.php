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



<div class="container">
    <div class="section flex">
        <div class="row">
            <h4>Premi ottenibili specificando una soglia di punti </h4>


            <div class="input-field col s6">
                <input placeholder="MassimoPunti" id="punti" type="text" class="validate">
            </div>

            <div class="input-field col s6">
                <button class="btn waves-effect waves-light" onclick="getPremi()" id="invio"> Visualizza
                    <i class="material-icons right">send</i>
                </button>
            </div>

            <div class="input-field col s12" id="risposta">

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

    function getPremi() {
        console.log("clicked")

        punti=document.getElementById("punti").value
        if (punti==""){
            document.getElementById("risposta").innerHTML = "Devi inserire i punti"
            return;
        }else if (punti<0){
            document.getElementById("risposta").innerHTML = "Non possono esserci punti negativi"
            return;
        }

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("risposta").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "onlyphpfiles/getPremiFromMaxPunti.php?"+"punti=" + punti , true);
        xhttp.send();

    }
</script>
</body>
</html>
