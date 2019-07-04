<?php
define("DBhost", "localhost");
define("DBusername", "i304260_admin");
define("DBpassword", "St00mB00t");
define("DBname", "i304260_DB");


// Create connection
$DBconnection = mysqli_connect(DBhost, DBusername, DBpassword, DBname);


// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}