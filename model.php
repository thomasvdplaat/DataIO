<?php

ini_set('display_errors',1);  error_reporting(E_ALL);



/**
 * Met deze functie kan gecontroleerd worden als een gebruiker ingelogd is
 * of niet, en wat er gedaan moet worden indien.
 *
 * @param string 	Moet gebruiker ingelogd zijn
 * @param string 	Soort actie dat uitgevoerd moet worden
 * @return boolean  Indien gekoze actie een "interger" is, beantwoord de vraag met Ja/Nee
 */

function loggedinControl($loginStatus, $returnType) {

	global $DBconnection;

	if (isset($_SESSION["loggedinAs"])) {
		$result = mysqli_query($DBconnection,"SELECT * FROM PHP32_users WHERE username = '".$_SESSION["loggedinAs"]."'");

		if(mysqli_num_rows($result) == 0) {
			unset($_SESSION["loggedinAs"]);
		}
	}

	

	if ($loginStatus && !isset($_SESSION["loggedinAs"])) {
		if($returnType == "redirect") {
			header('Location: index.php');
			exit;
		}
		if($returnType == "integer") {
			return false;
		}
	}
	else if (!$loginStatus && isset($_SESSION["loggedinAs"])) {
		if($returnType == "redirect") {
			header('Location: index.php');
			exit;
		}
		if($returnType == "integer") {
			return false;
		}
	}
	else if ($returnType == "integer") {
		return true;
	}
}


/**
 * Met deze functie kan gecontroleerd worden als een gebruiker admin rechten heeft
 *
 * @return boolean	Beantwoord de vraag met Ja/Nee
 */

function isAdmin() {

	global $DBconnection;

	$result = mysqli_query($DBconnection,"SELECT * FROM PHP32_users WHERE username = '".$_SESSION["loggedinAs"]."' AND admin = 'true'");

	if(mysqli_num_rows($result) > 0) {
		return true;
	}
	else {
		return false;
	}

}


/**
 * Deze functie bouwd een tabel op met alle gebruikers die te beheren zijn door admins
 */

function getUserTools() {
	global $DBconnection;

	$result = mysqli_query($DBconnection,"SELECT * FROM PHP32_users");

	echo "
		<table>
			<tr>
				<th>Username</th>
				<th>Password</th>
				<th>API key</th>
				<th>Admin</th>
				<th>Save changes</th>
				<th>Delete user</th>
			</tr>";

	while($row = mysqli_fetch_array($result)) {
		echo "
			<tr>
				<form method='POST' action='controller.php'>
					<td><input name='givenUsername' type='text' value='".$row['username']."'> </td>
					<td><input name='givenPassword' type='text' value='".$row['password']."'> </td>
					<td><input name='givenAPI' type='text' value='".$row['apikey']."'> </td>
					<td><input name='givenAdmin' type='text' value='".$row['admin']."'> </td>
					<td>
						<input name='username' type='hidden' value='".$row['username']."'/>
						<input name='changeUserPermissionRequest' type='hidden' value='true'/>
						<input value='save' type='submit'>
					</td>
					<td><a class='button' href='controller.php?deleteUser=".$row['username']."'>delete</a></td>
				</form>
			</tr>";
	}

	echo "</table>";

}

/**
 * Deze functie controleerd als er een meldingscode in de GET voorkomt, en echo't dan de bijbehorende een melding
 */

function messages() {

	if (isset($_GET["error"])) {

		switch ($_GET["error"]) {
		case '100':
			echo "<p class='errorMessage'>Username bestaat al!</p>";
			break;
		
		case '101':
			echo "<p class='errorMessage'>Verkeerde  gebruikersnaam/wachtwoord!</p>";
			break;
		
		case '102':
			echo "<p class='errorMessage'>Niet alle velden ingevuld!</p>";
			break;
		
		default:
			# code...
			break;
		}
	}

	if (isset($_GET["succes"])) {

		switch ($_GET["succes"]) {
		case '100':
			echo "<p class='succesMessage'>Succesvol geregistreerd als ".$_GET["username"]."</p>";
			break;
		
		default:
			# code...
			break;
		}
	}
}


/**
 * Deze functie haald alle data uit de CSV table uit de database en echo't dan een tabel op basis van deze informatie
 */

function getCSVtable() {

	global $DBconnection;

	$result = mysqli_query($DBconnection,"SELECT * FROM PHP32_csv");

		$table = "<table>";

	while($row = mysqli_fetch_array($result)) {
		$table .= "<tr><td>".$row['column1']."</td><td>".$row['column2']."</td><td>".$row['column3']."</td><td>".$row['column4']."</td></tr>";	
	}

	$table .= "</table>";

	echo $table;

}

/**
 * Deze functie echo't alle artikelen uit een RSS feed
 */

function getRSSfeed() {

    $RSSfeed = new SimpleXmlElement(file_get_contents("http://tweakers.net/feeds/nieuws.xml"));
     
   
    foreach($RSSfeed->channel->item as $entry) {

        echo "
        <a href='".$entry->link."'><h3>".$entry->title."</h3></a>
        <p>".$entry->description."</p>
        ";

    }
}


/**
 * Met deze functie word de API key van een gebruiker opgevraagd
 *
 * @param string 	Gebruikersnaam
 * @return string 	API key
 */

function apiKey($user) {

	global $DBconnection;

	if ($user = "this user") {
		$user = $_SESSION["loggedinAs"];
	}

	$result = mysqli_query($DBconnection,"SELECT * FROM PHP32_users WHERE username = '".$user."'");

	while($row = mysqli_fetch_array($result)) {
		return $row["apikey"];
	}
}




function getFlickrImages() {

	$flickrJsonUrl = file_get_contents('https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=711c5d59121d0645c7bced079df8626e&text=lego&sort=relevance&per_page=20&format=json&nojsoncallback=1');
	$flickrJson = json_decode($flickrJsonUrl, true);

	foreach ($flickrJson['photos']['photo'] as $doc) {

		// Afbeeldinggegevens verzamelen
	    $farm 		= $doc['farm'];
	    $server 	= $doc['server'];
	    $id 		= $doc['id'];
	    $secret 	= $doc['secret'];
	    $title 		= $doc['title'];

		// Afbeelding weergeven
		echo '<img class="picture" src="https://farm' . $farm . '.staticflickr.com/' . $server . '/' . $id . '_' . $secret . '_b.jpg" alt="' . $title . '">';
	}
}