<?php

/**
 * The Game Web Service
 * 
 * Endpoints:
 *  - [POST] /game/          Create new game
 *  - [PUT]  /game/          Join an existing game
 *  - [GET]  /game/{game_id} Get game status
 */

function game_routes($user, $endpoint, $method, $parameters, $data)
{
    // [POST] /game/
    if (empty($endpoint) && $method == 'POST') {
        create_game($user);
    }

    // [PUT] /game/
    else if (empty($endpoint) && $method == 'PUT' && !empty($data)) {
        join_game($user, $data);
    }

    // [GET] /game/{game_id}
    else if (isset($endpoint) && $method == 'GET') {
        // $endpoint here contains the {$game_id}
        load_game($user, $endpoint);
    }

    // 405 method not allowed
    else {
        echo json_response(405, 'Method Not Allowed');
        exit();
    }
}

function create_game($user)
{
    $game_id = db_create_game($user['user_id']);

    if ($game_id === false) {
        echo json_response(500, 'Internal Server Error');
        exit();
    }

    $response_data = array(
        'game_id' => $game_id
    );

    echo json_response(201, 'Game created', $response_data);
    exit();
}

function join_game($user, $data)
{
    if (!isset($data['game_id'])) {
        echo json_response(400, 'Bad Request');
        exit();
    }

    $affected_rows = db_join_game($user['user_id'], $data['game_id']);

    if ($affected_rows === false) {
        echo json_response(500, 'Internal Server Error');
        exit();
    }
    
    if ($affected_rows === 0) {
        echo json_response(404, 'Game not found');
        exit();
    }

    $response_data = array(
        'game_id' => $affected_rows
    );

    echo json_response(200, 'Player joined game', $response_data);
    exit();
}

function load_game($user, $game_id)
{
    if (!isset($game_id)) {
        echo json_response(400, 'Bad Request');
        exit();
    }

    $game = db_get_game($game_id);

    if (empty($game)) {
        echo json_response(404, 'Game not found');
        exit();
    }
    
    if ($user['user_id'] != $game['player_1_id'] && $user['user_id'] != $game['player_2_id']) {
        echo json_response(403, 'User is not allowed in this game');
        exit();
    }

    $game['am_owner'] = ($user['user_id'] == $game['player_1_id']);
    unset($game['player_1_id']);
    unset($game['player_2_id']);

    echo json_response(200, 'OK', $game);
    exit();
}


//
// Database functions
//

function db_create_game($user_id)
{
    $conn = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql  = "INSERT INTO game (player_1_id, status, created) VALUES ('$user_id', 'game created', NOW())";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    if (!mysqli_query($conn, $sql)) {
        mysqli_close($conn);
        return false;
    }

    $game_id = mysqli_insert_id($conn);

    mysqli_close($conn);
    
    return $game_id;
}

function db_join_game($user_id, $game_id)
{
    $conn = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql  = "UPDATE game SET player_2_id='$user_id', status='player 2 joined' WHERE game_id='$game_id'";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    if (!mysqli_query($conn, $sql)) {
        mysqli_close($conn);
        return false;
    }

    if (mysqli_affected_rows($conn) != 1) {
        return 0;
    }

    mysqli_close($conn);
    
    return $game_id;
}

function db_get_game($game_id)
{
    $conn = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql  = "
        SELECT
            g.*,
            u1.username as player_1,
            u2.username as player_2
        FROM game as g
        LEFT JOIN user as u1 ON g.player_1_id=u1.user_id
        LEFT JOIN user as u2 ON g.player_2_id=u2.user_id
        WHERE game_id = '$game_id'
    ";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_close($conn);   
        return array();
    }
    
    $game_data = mysqli_fetch_assoc($result);

    mysqli_close($conn);

    return $game_data;
}

?>
