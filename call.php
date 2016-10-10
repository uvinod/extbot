<?php
$servername = "localhost";
$username = "root";
$password = "1";
$dbname = "extbot";

$keyword = str_replace($_POST["trigger_word"], "", $_POST["text"]);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT description from articles where keyword = '".$keyword."'";
$result = $conn->query($sql);
$result_description = "Keyword doesn't match";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $result_description = $row["description"];
}
echo $result_description;
$room = $_POST["channel_name"]; 

$data = "payload=" . json_encode(array(         
        "channel"       =>  "#{$room}",
        "text"          =>  $result_description
    ));
$url = "https://hooks.slack.com/services/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";
         
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
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