<?php
ini_set('display_errors',1);  error_reporting(E_ALL);


//Hieronder word content opgebouwd die voor de gebruiker zichtbaar is

echo '
<!Doctype HTML>
<html>
<head>

<title>Website</title>

<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
	
<link rel="stylesheet" type="text/css" href="css/style.css">

</head>
<body>
<div id="bgContainer"><img id="bg" src="img/bg.jpg"></div>
';

//Als gebruiker niet ingelogd is, toon login/registreer pagina
if (!isset($_SESSION["loggedinAs"])) {

	if (isset($_GET["register"])) {
		echo '	
		<div id="loginBox">
			<h1>Registreren</h1>';

		echo messages();

		echo '
			<form id="loginForm" method="POST" action="controller.php">
				<input placeholder="Gebruikersnaam" name="givenUsername" type="text">
				<input placeholder="Wachtwoord" name="givenPassword" type="password">
				<input name="subscribeRequest" type="hidden" value="true"/>
				<input value="Verzenden" type="submit"/>
			</form>
			<a class="button" href="index.php">Terug</a>
		</div>
		';
	}
	else {
		echo '	
		<div id="loginBox">
			<h1>Inloggen</h1>';

		echo messages();

		echo '
			<form id="loginForm" method="POST" action="controller.php">
				<input placeholder="Gebruikersnaam" name="givenUsername" type="text">
				<input placeholder="Wachtwoord" name="givenPassword" type="password">
				<input name="logingRequest" type="hidden" value="true"/>
				<input value="Inloggen" type="submit"/>
			</form>
			<a class="button" href="index.php?register=true">Registreren</a>
		</div>
		';
	}
}

//Als gebruiker wel ingelogd is, toon dan homepage
else {

	//Als gebruiker niet meer ingelogd is, verwijs hem dan terug naar de inlogpagina
	loggedinControl(true,"redirect");

	echo "
	<header>
		<div id='headerTitle'>Homepage</div>
		<div id='headerUsername'>Ingelogd als ".$_SESSION["loggedinAs"]."</div>
		<nav>	
			<ul id='menu'>
				<li><a href='index.php'>Home</a></li>
				<li><a href='controller.php?logoutRequest=true'>Uitloggen</a></li>
			</ul>
		</nav>
	</header>
	<section class='content'>
	";

	//Als gebruiker admin is, toon dan admintools
	if (isAdmin()) {
		echo "
		<article>
			<h2>Users</h2>
		";

		getUserTools();

		echo "
		</article>
		";
	}

	echo "
		<article>
			<h2>CSV bestand uploaden en downloaden</h2>
			<form id='uploadForm' method='POST' action='controller.php' enctype='multipart/form-data'>
				<input placeholder='Bestandsnaam'  type='file' name='file'>
				<input name='uploadRequest' type='hidden' value='true'/>
				<input value='Uploaden' type='submit'>
				<a class='button' href='controller.php?clearCSV=true'>leeg maken</a>
			</form>

	";

	getCSVtable();

	echo "
		<a class='button' href='controller.php?viewCSV=true'>Download data als CSV bestand</a>
		</article>
		<article>
			<h2>API Guide</h2>
			<p>
			Met behulp van de API is het mogelijk informatie uit de database te halen en deze om te zetten naar JSON of XML formaat.
			De informatie die beschikbaar is in de database kun je zelf met een CSV bestand uploaden met behulp van de CSV uploader.
			Zodra je infomatie hebt geupload is deze beschikbaar via een speciaal opgebouwde link waarin minimaal 2 dingen in voor komen,
			namelijk je API key en het formaat waarin de informatie in getoont moet worden. De beschikbare formaten zijn JSON en XML.
			Hieronder zie je twee voorbeeld links om alle informatie in de database te kunnen weergeven.
			</p>

			<p>API key: ".apiKey("this user")."</p>

			<p>
			API url JSON format: <a href='api.php?format=json&key=".apiKey("this user")."'>http://i304260.iris.fhict.nl/php32/eindopdracht/api.php<span class='highlight'>?format=json&key=".apiKey("this user")."</span></a>
			<br>
			API url XML format: <a href='api.php?format=xml&key=".apiKey("this user")."'>http://i304260.iris.fhict.nl/php32/eindopdracht/api.php<span class='highlight'>?format=xml&key=".apiKey("this user")."</span></a>
			</p>

			<p>
			Daarnaast is het nog mogelijk de data te filteren met behulp van de kolomnamen ('column1', 'column2', 'column3' en 'column4').
			Dit kan gedaan worden door bijvoorbeeld achter in de url '&column1=Waarde' te typen waarbij 'column1' de kolomnaam is en 'Waarde' het getal of woord is waarop jij wilt filteren. 
			Je krijgt dan alleen resultaten te zien waarbij de waarde in 'column1' gelijk is aan de opgegeve waarde.
			</p>

			<p>Voorbeeld filteren: http://i304260.iris.fhict.nl/php32/eindopdracht/api.php?format=json&key=".apiKey("this user")."<span class='highlight'>&column1=12</span></p>
		</article>
		<article>
			<h2>RSS feed</h2>
	";

	getRSSfeed();

	echo "
		</article>
		<article>
			<h2>API feed</h2>
	";

	getFlickrImages();

	echo "
		</article>
	</section>
	";
}

echo "
</body>
</html>
";