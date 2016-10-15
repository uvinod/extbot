<?php
define( "SLACK_TOKEN", getenv["SLACK_TOKEN"] );
define( "SLACK_WEBHOOK", getenv["SLACK_HOOK_URL"] );

if($_POST["token"] != SLACK_TOKEN) {
	$result_description = "Error: Invalid token";
} else {
	// Create connection
	$conn = new mysqli(getenv('OPENSHIFT_MYSQL_DB_HOST') . ':' . getenv('OPENSHIFT_MYSQL_DB_PORT'), getenv('OPENSHIFT_MYSQL_DB_USERNAME'), getenv('OPENSHIFT_MYSQL_DB_PASSWORD'), getenv('OPENSHIFT_APP_NAME'));
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	$keyword = rtrim(ltrim(str_replace($_POST["trigger_word"], "", $_POST["text"])));
	$sql = "SELECT description from articles where keyword = '".$keyword."'";
	$result = $conn->query($sql);
	$result_description = "Keyword doesn't match";
	if ($result->num_rows > 0) {
	    $row = $result->fetch_assoc();
	    $result_description = $row["description"];
	}
}
$room = $_POST["channel_name"]; 
$data = "payload=" . json_encode(array(        
		"username"		=>	"Team Bot Tom", 
        "channel"       =>  "#{$room}",
        "text"          =>  $result_description
    ));

 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, SLACK_WEBHOOK);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
echo var_dump($result);
if($result === false)
{
    echo 'Curl error: ' . curl_error($ch);
}
 
curl_close($ch);
?>