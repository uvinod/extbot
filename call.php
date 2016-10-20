<?php
function connect_db(){
	$conn = new mysqli(getenv('MYSQL_DB_HOST') . ':' . getenv('MYSQL_DB_PORT'), getenv('MYSQL_DB_USERNAME'), getenv('MYSQL_DB_PASSWORD'), getenv('MYSQL_DB_NAME'));
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
}
function build_result($result_arr){
	if ($result_arr->num_rows > 0) {
	    $row = $result_arr->fetch_assoc();
	    $result_content = $row["description"];
	} else {
		$result_content = "Keyword doesn't match";
	}
	return $result_content;
}
function fetch_content($trigger_word, $text){
	$conn = connect_db();
	$keyword = rtrim(ltrim(str_replace($_POST["trigger_word"], "", $_POST["text"])));
	$sql = "SELECT description from articles where keyword = '".$keyword."'";
	$result_arr = $conn->query($sql);
	mysqli_close($conn);
	return build_result($result_arr);
}
function post_content($username, $channel_name, $result_content){
	$data = json_encode(array(        
			"username"		=>	$username, 
	        "channel"       =>  "#{$channel_name}",
	        "text"          =>  $result_content
	    ));
	return $data;
}

define( "SLACK_TOKEN", getenv("SLACK_TOKEN") );

if($_POST["token"] != SLACK_TOKEN) {
	$result_description = "Error: Invalid token";
} else {
	$result_content = fetch_content($_POST["trigger_word"], $_POST["text"]);
	$response = post_content("Team Bot Tom", $_POST["channel_name"], $result_content);
	echo $response;
}
?>
