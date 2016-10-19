<?php
function connect_db($db_host, $db_username, $db_password, $db_name){
	// Create connection
	$conn = new mysqli($db_host, $db_username, $db_password, $db_name);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
}

define( "SLACK_TOKEN", getenv["SLACK_TOKEN"] );

if($_POST["token"] != SLACK_TOKEN) {
	$result_description = "Error: Invalid token";
} else {
	$conn = connect_db(getenv('OPENSHIFT_MYSQL_DB_HOST') . ':' . getenv('OPENSHIFT_MYSQL_DB_PORT'), getenv('OPENSHIFT_MYSQL_DB_USERNAME'), getenv('OPENSHIFT_MYSQL_DB_PASSWORD'), getenv('OPENSHIFT_APP_NAME'));
	$keyword = rtrim(ltrim(str_replace($_POST["trigger_word"], "", $_POST["text"])));
	$sql = "SELECT description from articles where keyword = '".$keyword."'";
	$result = $conn->query($sql);
	$result_description = "Keyword doesn't match";
	if ($result->num_rows > 0) {
	    $row = $result->fetch_assoc();
	    $result_description = $row["description"];
	}
	mysqli_close($conn);
}
$room = $_POST["channel_name"]; 
$data = json_encode(array(        
		"username"		=>	"Team Bot Tom", 
        "channel"       =>  "#{$room}",
        "text"          =>  $result_description
    ));

echo $data;
?>
