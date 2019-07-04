<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
require_once("DBconnection.php");

$format = "";
$key = "";
$parameters = "";
$pre = "WHERE ";
$rows = "";


//Controlleer als er GET variable beschikbaar zijn
if (sizeof($_GET) > 0) {
	
	foreach ($_GET as $index => $data) {

		// Sla het opgegen formaat op
		if ($index == "format") {
			$format = $data;
		}

		// Sla de API key op
		else if ($index == "key") {
			$key = $data;
		}

		//Maak van verder opgegven GET variabelen een suffix voor in een database query
		else {
			$parameters .= $pre.$index." = '".$data."' ";
			$pre = "AND ";
		}
	}
}

//Controleer als opgegeven API key voor komt in de database, zo ja ga verder
$authentication = mysqli_query($DBconnection,"SELECT * FROM PHP32_users WHERE apikey = '".$key."' ");
if(mysqli_num_rows($authentication) > 0) {

	//Maak nu een query met daar achter onze suffix
	$result = mysqli_query($DBconnection,"SELECT * FROM PHP32_csv $parameters");


	//Als het opgegen formaat XML is, ga verder
	if ($format == "xml") {

		//Maak niet XML object aan
		$xml = new SimpleXMLElement('<root/>');

		//Loop nu door de gemaakte query heen
		while($row = mysqli_fetch_array($result)) {


			//Plaats nu elementen in de gemaakt XML object
			$object = "Object_".$row["column1"];

			$xml->addChild($object  );
			$xml->$object->addChild("column1", $row["column1"]);
			$xml->$object->addChild("column2", $row["column2"]);
			$xml->$object->addChild("column3", $row["column3"]);
			$xml->$object->addChild("column4", $row["column4"]);
		}

		//Bepaal contenttype
		header('Content-Type: application/xml; charset=utf-8');

		//Weergef de gemaakte XML structuur
		echo $xml->asXML();
	}

	//Als het opgegeven formaat JSON is, ga verder
	else if ($format == "json") {

		//Loop nu door de gemaakte query heen
		while($row = mysqli_fetch_array($result)) {

			//Maak plaats alle waardes in één array
			$rows[$row["column1"]] = array(
				'column1' => $row["column1"],
				'column2' => $row["column2"],
				'column3' => $row["column3"],
				'column4' => $row["column4"]
				);
		}

		//Bepaal contenttype
		header('Content-type:application/json');

		//Maak van de array een JSON structuur
		echo json_encode($rows);
	}

	//print($parameters);
}