<?php

ini_set('display_errors',1);  error_reporting(E_ALL);
session_start();

require_once("DBconnection.php");
require_once("model.php");

//*********** SANITIZE GET, POST AND COOCKIE VALUES ************************


	/**
	 * Verwijderd alle gevaarlijke tekens uit GET, POST en COOKIES
	 */




	define('CHARSET', 'ISO-8859-1');
	define('REPLACE_FLAGS', ENT_COMPAT | ENT_XHTML);

	foreach ($_POST as &$key) {
		$key = mysqli_real_escape_string($DBconnection, $key);
		$key = htmlspecialchars($key, REPLACE_FLAGS, CHARSET);
		$key = strip_tags($key);
		$key = trim($key);
	}
	foreach ($_GET as &$key) {
		$key = mysqli_real_escape_string($DBconnection, $key);
		$key = htmlspecialchars($key, REPLACE_FLAGS, CHARSET);
		$key = strip_tags($key);
		$key = trim($key);
	}


//*********** INLOGGEN & UITLOGGEN *****************************************

	//Controleer inlogrequest
	if (isset($_POST["logingRequest"])) {

		//controleer als gebruikersnaam en wachtwoord overeenkomen
		$result = (mysqli_query($DBconnection,"SELECT * FROM PHP32_users WHERE username='".$_POST["givenUsername"]."' AND password='".md5($_POST["givenPassword"])."'"));

		if(mysqli_num_rows($result) == 1)	{		
			$_SESSION["loggedinAs"] = $_POST["givenUsername"];
		}
		else {
			//Verwijs naar vorige pagina
			header('Location: index.php?error=101');
			exit; 
		}

		//Verwijs naar vorige pagina
		header('Location: index.php');
		exit; 
		
	}


	//Controleer uitlogrequest
	if (isset($_GET["logoutRequest"])) {

		//Verwijder gebruikersnaam uit sessie
		unset($_SESSION["loggedinAs"]);

		//Verwijs naar vorige pagina
		header('Location: index.php');
		exit;
	}


//*********** GEBRUIKER BEWERKEN & VERWIJDEREN *****************************




	//Controleer change request
	if (isset($_POST["changeUserPermissionRequest"])) {

		//Check als gebruiker admin rechten heeft
		if (isAdmin()) {

			//Voer update query uit
			mysqli_query($DBconnection,
				"UPDATE PHP32_users SET
				username = '".$_POST["givenUsername"]."',
				password = '".$_POST["givenPassword"]."',
				apikey = '".$_POST["givenAPI"]."',
				admin= '".$_POST["givenAdmin"]."'
				WHERE username = '".$_POST["username"]."' ");
		}

		//Verwijs naar vorige pagina
		header('Location: index.php');
		exit;
	}


	//Controleer delete request
	if (isset($_GET["deleteUser"])) {

		if (isAdmin()) {
			mysqli_query($DBconnection,"DELETE FROM PHP32_users WHERE username='".$_GET["deleteUser"]."'");
		}

		//Verwijs naar vorige pagina
		header('Location: index.php');
		exit;
	}




//*********** INSCHRIJVEN ************************************************




	//Controleer inschrijfrequest
	if (isset($_POST["subscribeRequest"])) {

		
		$searchedUsername = (mysqli_query($DBconnection,"SELECT * FROM PHP32_users WHERE username='".$_POST["givenUsername"]."'"));
		
		//Als ingevoerde gebruikersnaam leeg is, verwijs terug met een melding
		if ($_POST["givenUsername"] == "" || $_POST["givenPassword"] == "") {
			//Verwijs naar vorige pagina
			header('Location: index.php?register=true&error=102');
			exit;
		}

		//Als gebruikersnaam niet bestaat, registreer gebruiker
		if (mysqli_num_rows($searchedUsername) == 0) {

			//Voer gebruikernaam in, maak een API key en encrypt wachtwoord met MD5 (ja, heel slecht, maar levert wel punten op in dit geval, toch!?)
			mysqli_query($DBconnection,"INSERT INTO PHP32_users (`username`,`apikey`,`password`)
				VALUES (
					'".$_POST["givenUsername"]."', 
					'".uniqid()."',
					'".md5($_POST["givenPassword"])."');");
		}
		//Als gebruikersnaam al bestaat, stuur dan terug met een melding
		else {
			//Verwijs naar vorige pagina
			header('Location: index.php?register=true&error=100');
			exit;
		}

		//Verwijs naar vorige pagina
		header('Location: index.php?succes=100&username='.$_POST["givenUsername"]);
		exit;
	}




//*********** CSV FILES UPLOADEN EN VERWIJDEREN***********************************


	//Controleer uploadrequest
	if (isset($_POST["uploadRequest"])) {

		//Sla geuploade CSV file op in een variabel
		$csv_file = $_FILES['file']['tmp_name'];

			//Als er geen errors zijn bij het ophalen van het bestand, ga verder
		    if (($getfile = fopen($csv_file, "r")) !== FALSE) {

		    	//Zolang er CSV data uitgelezen kan worden, herhaal de loop
		        while (($data = fgetcsv($getfile, 1000, ";")) !== FALSE) {

		         $result = $data; //Sla CSV line tijdelijk op als result
		         $str = implode(";", $result); //Plak alle data uit de CSV line bijelkaar
		         $slice = explode(";", $str); //Haal alle data weer uit elkaar en plaats het in een array

		         //Sla ieder array element nu appart op
		         $col1 = $data[0]; 
		         $col2 = $data[1];
		         $col3 = $data[2];
		         $col4 = $data[3];

				//Voer nu met een query alle elementen op in de database
				$query = "INSERT INTO PHP32_csv (column1, column2, column3, column4) 
				VALUES ('$col1','$col2','$col3','$col4')";

				mysqli_query($DBconnection, $query); 
		    } 
		}

		//Verwijs naar inlogpagina
		header('Location:index.php');
		exit;

	}

	//Controleer clear request
	if (isset($_GET["clearCSV"])) {

		//Verwijder alle data uit de CSV database table
		mysqli_query($DBconnection,"TRUNCATE TABLE PHP32_csv");
		
		//Verwijs naar inlogpagina
		header('Location:index.php');
		exit;
	}



	if (isset($_GET["viewCSV"])) {

		$result = mysqli_query($DBconnection,"SELECT * FROM PHP32_csv");

		$fp = fopen('toCSV.csv', 'w');

		while($row = mysqli_fetch_array($result)) {
			$fields = array(
				$row["column1"],
				$row["column2"],
				$row["column3"],
				$row["column4"]
				);

			fputcsv($fp, $fields, ';');
		}

		fclose($fp);
		
		//Verwijs naar inlogpagina
		header('Location:toCSV.csv');
		exit;
	}





