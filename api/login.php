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

$user = get_user($username);

if ($user === false) {
    echo json_response(500, 'Internal Server Error');
    exit();
}
if (empty($user)) {
    echo json_response(404, 'User Not Found');
    exit();
}
if ($password != $user['password']) {
    echo json_response(400, 'Password is incorrect');
    exit();
}

$token  = generate_token(16);
$result = update_user_token($user['user_id'], $token);

if ($result === false) {
    echo json_response(500, 'Internal Server Error');
    exit();
}

session_start();
$_SESSION["username"] = $username;
$_SESSION["token"]    = $token;
$_SESSION["avatar"]   = $user['avatar'];
echo json_response(200, 'Success');
exit();


function get_user($username)
{
    $conn = new mysqli(HOST,USERNAME,DB_PWD,DATABASE);
    $sql  = "SELECT * FROM user WHERE username='$username'";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    $result = mysqli_query($conn, $sql);
    $user   = mysqli_fetch_assoc($result);

    mysqli_close($conn);

    return $user;
}

function update_user_token($user_id, $token)
{
    $conn = new mysqli(HOST,USERNAME,DB_PWD,DATABASE);
    $sql  = "UPDATE user SET token='$token' WHERE user_id=$user_id";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    if (!mysqli_query($conn, $sql)) {
        mysqli_close($conn);
        return false;
    }

    mysqli_close($conn);
    return true;
}


?>
