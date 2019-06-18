<?php
/**
@Description : Ce fichier PHP permet d'extraire les informations Teltonika d'un fichier logs.txt, de les traiter et les stocker dans un fichier logs_copie.txt.
Ensuite, ces informations seront envoyées vers le serveur Traccar. 
/!\ Le fichier logs.txt doit exister et contenir les logs de Traccar à envoyer à l'appareil. 
Les logs Traccar sont disponibles à l'addresse %DIR%/Traccar/config/logs.txt

/!\ Ne pas copier tous les logs Traccar dans le fichier logs.txt cela serait trop long.
/!\ Le fichier logs_copie.txt doit exister et être vide

/!\ Changez l'adresse du serveur Traccar si nécessaire (ligne 206)

Le programme met plusieurs secondes à se lancer.

 
 */

 
   
$fichier_a_traiter = fopen("logs.txt", "r") or exit("Unable to open fichier_a_traiter!"); //il faut que ce fichier existe. Il doit contenir les logs Traccar.
//Output a line of the fichier_a_traiter until the end is reached

$fichier_traite = fopen("logs_copie.txt", "r") or exit("Unable to open file!"); //il faut que ce fichier existe (le laisser vide)


/**
Fonction qui va renvoyer un booléen si un String contient le mot du paramètre "needle" au début du string "haystack"
 */

function startsWith($haystack, $needle) ///fonction qui va renvoyer un booléen si un String contient le mot du paramètre "needle" au début du string "haystack"
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}


/**
Fonction qui va renvoyer un booléen si un String contient le mot du paramètre "needle" à la fin du string "haystack"
 */

function endsWith($haystack, $needle) ///fonction qui va renvoyer un booléen si un String contient le mot du paramètre "needle" à la fin du string "haystack"
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}


/**
Fonction simulant la connexion d'un appareil Teltonika au serveur Traccar
 */
function connexionTCP($trame1,$trame2,$server_address,$server_port) //Trame 1 :Message qu'envoie normalement un appareil Teltonika essayant de se connecter. Trame2 :correspond à la trame de données contenant toutes les informations envoyées par le tracker GPS
{
//$address="127.0.0.1"; //Adresse du serveur Traccar. Changez cette adresse si nécessaire
//$port="5027"; //port utilisé par le protocole Teltonika


$sock=socket_create(AF_INET,SOCK_STREAM,0) or die("Cannot create a socket"); //Création de socket
socket_connect($sock,$server_address,$server_port) or die("Could not connect to the socket"); 
socket_write($sock,$trame1); //Écriture de la première trame de l'appareil Teltonika vers le serveur

$lecture=socket_read($sock,1); //Lecture d'un seul byte.

if($lecture=="\x01")
{
	echo "La connexion a ete acceptee par le serveur. \n<br /><br />";
	
	
}
else echo "Erreur, connexion refusee par le serveur. Consultez les logs Traccar";




socket_write($sock,$trame2); //Écriture de la deuxième trame


$lecture2=socket_read($sock,4); //Lecture des quatre bytes confirmant la réception



if($lecture2=="\x00\x00\x00\x01")//Quand la trame de données est envoyée, un message de 4 bytes est renvoyée pour confirmer l'établissement de la connexion
{
	
	
	echo "\r\nLa trame de donnees a ete envoyee et a bien ete recue par le serveur.";
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	echo "<br/>";
	
}

else echo "Erreur, vérifiez que vous avez ajouté le bon identifiant Teltonika au serveur Traccar et que vous avez l'autorisation d'ouvrir des sockets. Consultez les logs Traccar";

usleep(250000); //temps d'attente de 0.25 seconde. Sans temps d'attente, le serveur n'arrive pas à traiter l'information.
socket_close($sock);//fermeture du socket

}



/**
Fonction traitant les informations du fichier logs.txt et les envoie vers le fichier logs_copie.txt. 
Le fichier logs_copie.txt contient seulement les trames que le tracker GPS envoie au serveur.
 */
while(!feof($fichier_a_traiter)) //tant que l'on atteint pas la fin du fichier logs.txt
{
    $result=fgets($fichier_a_traiter); //on lit la première ligne du fichier
	$result_substring=substr($result,65); //on enlève les informations qui ne nous intéressent pas
 	$myFile2 = "logs_copie.txt"; //on indique le nom du fichier où l'on va stocker les informations traitées
	
	
	if (startsWith($result_substring,"000f")) //on vérifie si la trame commence par "000f"
	{ 
	$myFileLink2 = fopen($myFile2, 'a') or die("Can't open file.");
	fwrite($myFileLink2, $result_substring); //écriture de la ligne d'initialisation dans le fichier logs_copie
	fclose($myFileLink2);
	
	
	}
	
	elseif(startsWith($result_substring,"00000000")) //on vérifie si la trame commence par "00000000". Cette trame est notre trame de données.

	{
		
	$myFileLink2 = fopen($myFile2, 'a') or die("Can't open file.");
	fwrite($myFileLink2, $result_substring);  //écriture de la ligne de données dans le fichier logs_copie
	fclose($myFileLink2);
	
			
	}
	
	
}

/**
Fonction lisant les informations du fichier logs_copie.txt et les envoie vers le serveur Traccar. 
Cette fonction permet de distinguer les trames d'initialisation et les trames d'informations
Cette fonction permet également de convertir les string contenus dans le fichier texte en hexadécimal.

 */
while(!feof($fichier_traite)) //tant que l'on atteint pas la fin du fichier logs_copie.txt
{	
	$result_fichier_traite=fgets($fichier_traite);
	
	if(substr($result_fichier_traite,0,4)=="000f") //on vérifie si le début de la trame d'initialisation est "00000000"
	{
		
		$trame1=substr($result_fichier_traite,0,1000); //on récupère la trame complète
		
		
			
	}
	
	if(substr($result_fichier_traite,0,8)=="00000000") //on vérifie si le début de la trame de données est "00000000"
	{
		
		$trame2=substr($result_fichier_traite,0,10000);//on récupère la trame complète
		
			
	}
	
	
	
	if(isset($trame1)&&isset($trame2))//on vérifie la présence des deux trames 
	{
		
		$trame1_substring=substr($trame1,0,strlen($trame1)-2); //on enlève deux octets à la fin de la trame car PHP a rajouté deux caractères non imprimables durant l'écriture du nouveau fichier
		$trame1_hex=hex2bin($trame1_substring); //conversion de la trame string en hex
		
		echo"Trame 1 hexadecimal et string: \n<br />";
		echo "<br/>";
		echo($trame1_hex);
		echo "<br/>";
		echo($trame1_substring);
		echo "<br/>";

		echo "<br/>";
		
		
		
		$trame2_substring=substr($trame2,0,strlen($trame2)-2); //on enlève deux octets à la fin de la trame car PHP a rajouté deux caractères non imprimables durant l'écriture du nouveau fichier
		$trame2_hex=hex2bin($trame2_substring); //conversion de la trame string en hex
		echo "<br/>";

		echo"Trame 2 hexadecimal et string : \n <br />";
		echo "<br/>";

		echo($trame2_hex);
		echo "<br/>";
		echo($trame2_substring);
		echo "<br/>";

		echo "<br/>";
		
		
		
		for($j=0;$j<1;$j++)
		{
		connexionTCP($trame1_hex,$trame2_hex,"127.0.0.1","5027");
		}
	}
	
	
}
			
?>
