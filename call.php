<?php
function connect_db(){
	$conn = new mysqli(
			getenv('MYSQL_DB_HOST') . ':' . getenv('MYSQL_DB_PORT'), 
			getenv('MYSQL_DB_USERNAME'), 
			getenv('MYSQL_DB_PASSWORD'), 
			getenv('MYSQL_DB_NAME'));
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	return $conn;
}
function fetch_content($trigger_word){
	$conn = connect_db();
	$sql = "SELECT description FROM articles WHERE keyword = '".$trigger_word."'";
	$result_arr = $conn->query($sql);
	mysqli_close($conn);
	return build_content($result_arr);
}
function build_content($result_arr){
	if ($result_arr->num_rows > 0) {
	    $row = $result_arr->fetch_assoc();
	    $result_content = $row["description"];
	} else {
		$result_content = "Keyword doesn't match";
	}
	return $result_content;
}
function post_content($username, $channel_name, $result_content){
	$data = json_encode(array(        
			"username"		=>	$username, 
	        "channel"       =>  "#{$channel_name}",
	        "text"          =>  $result_content
	    ));
	return $data;
}
if($_POST["token"] != getenv("SLACK_TOKEN")) {
	$response = "Error: Invalid token";
} else {
	$trigger_word = rtrim(ltrim(str_replace($_POST["trigger_word"], "", $_POST["text"])));
	$response = fetch_content($trigger_word);
}
echo post_content("Team Bot Tom", $_POST["channel_name"], $response);
?>
