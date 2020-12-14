<?php

require("../includes/db_conf.php");
require("../includes/api_helpers.php");

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    echo json_response(405, 'Method Not Allowed');
    exit();
}

$form_data = json_decode(file_get_contents("php://input"), true) ? : [];
$username  = $form_data['username'];
$password  = $form_data['password'];
$conn      = new mysqli(HOST,USERNAME,DB_PWD,DATABASE);
$sql       = "SELECT * FROM user WHERE username='$username'";   

mysqli_set_charset($conn,"utf8");

if (mysqli_connect_errno()) {
    echo json_response(405, 'Method Not Allowed');
    exit();
}	    

$result = mysqli_query($conn, $sql);
$user   = mysqli_fetch_assoc($result);

mysqli_close($conn);

if (empty($user)) {
    echo json_response(404, 'User Not Found');
    exit();
} 

if ($password != $user['password']) {
    echo json_response(400, 'Password is incorrect');
    exit();
}

session_start();

$_SESSION['user_id']  = $user['user_id'];
$_SESSION['username'] = $username;
$_SESSION['avatar']   = $user['avatar'];

echo json_response(200, 'Success');
exit();

?>
