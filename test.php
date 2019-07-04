<?php

ini_set('display_errors',1);  error_reporting(E_ALL);
session_start();

require_once("DBconnection.php");


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


?>

<html>
	<head>
	</head>
	<body>
		<table>
			<tr>
				<th>Invoer</th>
				<th>Resultaat</th>
			</tr>
			<tr>
				<td>\</td>
				<td><?php echo $_GET["1"]?></td>
			</tr>
			<tr>
				<td>/</td>
				<td><?php echo $_GET["2"]?></td>
			</tr>
			<tr>
				<td>'</td>
				<td><?php echo $_GET["3"]?></td>
			</tr>
			<tr>
				<td>"</td>
				<td><?php echo $_GET["4"]?></td>
			</tr>
			<tr>
				<td>&#60;!-- --&#62;</td> 
				<td><?php echo $_GET["5"]?></td>
			</tr>
			<tr>
				<td>&lt;h1&gt;&lt;h2&gt;&lt;h3&gt;</td>
				<td><?php echo $_GET["6"]?></td>
			</tr>
			<tr>
				<td>#</td> 
				<td><?php echo $_GET["7"]?></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
			</tr>	
		</table>
	</body>
</html>