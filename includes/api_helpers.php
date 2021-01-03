<?php

function authenticate($token)
{
    if (!isset($token)) {
        echo json_response(400, 'Token not sent in the request headers');
        exit();
    }

    $user = get_user_by_token($token);
    
    if ($user === false) {
        echo json_response(500, 'Internal Server Error');
        exit();
    }
    
    if (empty($user)) {
        echo json_response(403, 'User not logged in');
        exit();
    }

    return $user;
}

function json_response($code = 200, $message = null, $data = null)
{
    // clear the old headers
    header_remove();
    // set the actual code
    http_response_code($code);
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    // treat this as json
    header('Content-Type: application/json');

    $status = array(
        200 => '200 OK',
        201 => '201 Created',
        204 => '204 No Content',
        400 => '400 Bad Request',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        500 => '500 Internal Server Error'
    );
    
    // ok, validation error, or failure
    header('Status: '.$status[$code]);

    // return the encoded json
    return json_encode(array(
        'status'  => $code,
        'message' => $message,
        'data'    => $data
    ));
}

function generate_token($characters)
{
    // https://stackoverflow.com/questions/18910814/best-practice-to-generate-random-token-for-forgot-password/18910943
    
    $length = $characters / 2;
    $token  = bin2hex(random_bytes($length));

    return $token;
}

function get_user_by_token($token)
{
    $conn = new mysqli(HOST,USERNAME,DB_PWD,DATABASE);
    $sql  = "SELECT * FROM user WHERE token='$token'";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    $result = mysqli_query($conn, $sql);
    $user   = mysqli_fetch_assoc($result);

    mysqli_close($conn);

    return $user;
}

?>
