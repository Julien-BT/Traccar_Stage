<! --
* Auteurs : BAUDOT Julien - BOUZID Houdheyfa

* Ecole : Polytech Nancy
* Société Transalliance Dudelange
* 
* Version : 1.1
* Date de création : 13/06/2019
* Date de dernière modification : 19/06/2019
* @Description : Ce fichier PHP permet d'extraire les informations Teltonika d'un fichier logs, de les traiter et les stocker dans un fichier logs_copie.txt.
* Ensuite, ces informations seront envoyées vers le serveur Traccar.

--> 

<!--                          Formulaire HTML d'upload                      -->

<html><body>

        <h1 style = " color:#DE8D41;font-family: Gill Sans, sans-serif;border: 2px solid black; outline: #20458F solid 10px; margin: auto; padding: 20px; text-align: center;">Programme de simulation de logs Teltonika</h1>

        <br>
        <br>

    <center>

        <form method="POST"
              action="LogSimulationTeltonika.php" 
              enctype="multipart/form-data">


            <input style="-moz-box-shadow: 0px 1px 0px 0px #1c1b18;
                   -webkit-box-shadow: 0px 1px 0px 0px #1c1b18;
                   box-shadow: 0px 1px 0px 0px #1c1b18;
                   background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #eae0c2), color-stop(1, #ccc2a6));
                   background:-moz-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:-webkit-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:-o-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:-ms-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:linear-gradient(to bottom, #eae0c2 5%, #ccc2a6 100%);
                   filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#eae0c2', endColorstr='#ccc2a6',GradientType=0);
                   background-color:#eae0c2;
                   -moz-border-radius:15px;
                   -webkit-border-radius:15px;
                   border-radius:15px;
                   border:2px solid #333029;
                   display:inline-block;
                   cursor:pointer;
                   color:#505739;
                   font-family:Arial;
                   font-size:14px;
                   font-weight:bold;
                   padding:12px 16px;
                   text-decoration:none;
                   text-shadow:0px 1px 0px #ffffff;" type="file" name="monfichier"> 
            <br><br><br>

            <input style="-moz-box-shadow: 0px 1px 0px 0px #1c1b18;
                   -webkit-box-shadow: 0px 1px 0px 0px #1c1b18;
                   box-shadow: 0px 1px 0px 0px #1c1b18;
                   background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #eae0c2), color-stop(1, #ccc2a6));
                   background:-moz-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:-webkit-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:-o-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:-ms-linear-gradient(top, #eae0c2 5%, #ccc2a6 100%);
                   background:linear-gradient(to bottom, #eae0c2 5%, #ccc2a6 100%);
                   filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#eae0c2', endColorstr='#ccc2a6',GradientType=0);
                   background-color:#eae0c2;
                   -moz-border-radius:15px;
                   -webkit-border-radius:15px;
                   border-radius:15px;
                   border:2px solid #333029;
                   display:inline-block;
                   cursor:pointer;
                   color:#505739;
                   font-family:Arial;
                   font-size:14px;
                   font-weight:bold;
                   padding:12px 16px;
                   text-decoration:none;
                   text-shadow:0px 1px 0px #ffffff;" type="submit" name="envoyer" value="Rejouer le log">
        </form></center>

    <br>
    <br>
</body></html>




<!-- 
    ------------------------------------------------             PARTIE PHP        ------------------------------------------------------------------------
 -->	


<?php
// Si j'ai appuyé sur le bouton après l'upload de mon fichier, exécuter le programme pour ce fichier.
if (is_uploaded_file($_FILES["monfichier"]["tmp_name"])) {

    $fichier_a_traiter = fopen($_FILES["monfichier"]["tmp_name"], "r") or exit("Unable to open fichier_a_traiter!");
    $fichier_cree = fopen("logs_copie.txt", "w") or exit("Unable to open file!");
    fclose($fichier_cree);
    $fichier_traite = fopen("logs_copie.txt", "r") or exit("Unable to open file!");


    /* ------------------------------------------------------------------------------------------------------------------------ */

    /**
      Fonction traitant les informations du fichier logs.txt et les envoie vers le fichier logs_copie.txt.
      Le fichier logs_copie.txt contient seulement les trames que le tracker GPS envoie au serveur.
     */
    //tant que l'on atteint pas la fin du fichier logs.txt
    while (!feof($fichier_a_traiter)) {

        //Lecture première ligne du fichier, Retrait informations inutiles, création du fichier de destination
        $result = fgets($fichier_a_traiter);
        $result_substring = substr($result, 65);
        $myFile2 = "logs_copie.txt";


        //on vérifie si la trame commence par "000f", si oui, écriture de la 1ère ligne
        if (startsWith($result_substring, "000f")) {
            $myFileLink2 = fopen($myFile2, 'a') or die("Can't open file.");
            fwrite($myFileLink2, $result_substring);
            fclose($myFileLink2);

            //on vérifie si la trame commence par "00000000". Cette trame est notre trame de données.    
        } elseif (startsWith($result_substring, "00000000")) {
            $myFileLink2 = fopen($myFile2, 'a') or die("Can't open file.");
            fwrite($myFileLink2, $result_substring);
            fclose($myFileLink2);
        }
    }

    /**
      Fonction lisant les informations du fichier logs_copie.txt et les envoie vers le serveur Traccar.
      Cette fonction permet de distinguer les trames d'initialisation et les trames d'informations
      Cette fonction permet également de convertir les string contenus dans le fichier texte en hexadécimal.

     */
    //tant que l'on atteint pas la fin du fichier logs_copie.txt
    while (!feof($fichier_traite)) {
        $result_fichier_traite = fgets($fichier_traite);

        //on vérifie si le début de la trame d'initialisation est "000f"
        //Et on récupère la trame complète
        if (substr($result_fichier_traite, 0, 4) == "000f") {
            $trame1 = substr($result_fichier_traite, 0, 1000);
        }

        //on vérifie si le début de la trame d'initialisation est "00000000"
        //Et on récupère la trame complète
        if (substr($result_fichier_traite, 0, 8) == "00000000") {
            $trame2 = substr($result_fichier_traite, 0, 10000);
        }

        //on vérifie la présence des deux trames 
        if (isset($trame1) && isset($trame2)) {

            //pour éviter que l'ancienne trame2 ne se rejoue durant le parcours de la 3e ligne, on ajoute cette condition
            if (substr($result_fichier_traite, 0, 4) == "000f") {
                $trame2 = 0;
            }


            //on exécute l'action si la trame2 n'est pas égale à 0 pour éviter les erreurs
            //on enlève deux octets à la fin de la trame car PHP a rajouté deux caractères inutiles
            //puis conversion de la trame string en hex

            if ($trame2 != 0) {
                $trame1_substring = substr($trame1, 0, strlen($trame1) - 2);
                $trame1_hex = hex2bin($trame1_substring);

                echo"Trame 1 : \n<br />";
                echo "<br/>";
                echo($trame1_hex);
                echo "<br/>";

                //IDEM que précédémment

                $trame2_substring = substr($trame2, 0, strlen($trame2) - 2);
                $trame2_hex = hex2bin($trame2_substring);
                echo "<br/>";

                echo"Trame 2 : \n <br />";
                echo "<br/>";

                echo($trame2_hex);
                echo "<br/>";

                echo "<br/>";


                connexionTCP($trame1_hex, $trame2_hex, "127.0.0.1", "5027");
            }
        }
    }
}





/* -------------------------------------------------                    FONCTIONS                  ----------------------------------------------------------------- */

/**
  Fonction qui va renvoyer un booléen si un String contient le mot du paramètre "needle" au début du string "haystack" (startsWith)
  ou qui le contient à la fin du string "haystack" (endsWith)
 */
function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

/* ------------------------------------------------------------------------------------------------------------------------ */

/**
  Fonction simulant la connexion d'un appareil Teltonika au serveur Traccar
 */
// Trame 1 : Message qu'envoie normalement un appareil Teltonika essayant de se connecter. (= IMEI de l'appareil) 
// Trame2 :correspond à la trame de données contenant toutes les informations envoyées par le tracker GPS

function connexionTCP($trame1, $trame2, $address_server, $port) {

    //Création de socket
    $sock = socket_create(AF_INET, SOCK_STREAM, 0) or die("Cannot create a socket");
    socket_connect($sock, $address_server, $port) or die("Could not connect to the socket");

    //Écriture de la première trame de l'appareil Teltonika vers le serveur
    socket_write($sock, $trame1);

    //Lecture d'un seul byte.
    $lecture = socket_read($sock, 1);

    if ($lecture == "\x01") {
        echo "La connexion a ete acceptee par le serveur. \n<br /><br />";
    } else {
        echo "Erreur, connexion refusee par le serveur. Consultez les logs Traccar";
    }

    //Tempo pour le bon déroulement des trames
    usleep(250000);

    //Ecriture de la seconde trame + lecture des 4 bytes confirmant réception
    socket_write($sock, $trame2);
    $lecture2 = socket_read($sock, 4);

    //Quand la trame de données est envoyée, un message de 4 bytes est renvoyée pour confirmer l'établissement de la connexion
    if ($lecture2 == "\x00\x00\x00\x01") {
        echo "\r\nLa trame de donnees a ete envoyee et bien recue par le serveur.";
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";
        echo "<br/>";
    } else {
        echo "Erreur, vérifiez que vous avez ajouté le bon identifiant Teltonika au serveur Traccar et que vous avez l'autorisation d'ouvrir des sockets. Consultez les logs Traccar";
    }

    usleep(250000);
    socket_close($sock);
}
