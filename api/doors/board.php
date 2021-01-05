<?php

/**
 * The Board Web Service
 * 
 * Endpoints:
 * TODO
 *  - [GET] /board/          Get the board
 *  - [GET] /board/piece/{x} Get the piece(s) at location x
 *  - [PUT] /board/piece/{x} Move a piece to location x
 */

function board_routes($user, $endpoint, $method, $parameters, $data)
{    
    // [GET] /board/
    if (empty($endpoint) && $method == 'GET') {
        // TODO
        //get_board();
    }
    
    // [POST] /board/
    else if (empty($endpoint) && $method == 'POST') {
        create_game($user);
    }
    
    // [GET] /board/piece/{x}/{y}
    else if ($endpoint == 'piece' && $method == 'GET') {
        // TODO
        //get_piece();
    }
    
    // [PUT] /board/piece/{x}/{y}
    else if ($endpoint == 'piece' && $method == 'PUT') {
        // TODO
        //move_piece();
    }
    
    // 405 method not allowed
    else {
        echo json_response(405, 'Method Not Allowed');
        exit();
    }
}

function create_game($user)
{
    // create new game
    $game_id = db_create_game($user['user_id']);

    if ($game_id === false) {
        echo json_response(500, 'Internal Server Error');
        exit();
    }

    // create new board
    $board_data = get_new_board();
    $board_id   = db_create_board($game_id, json_encode($board_data));

    if ($board_id === false) {
        echo json_response(500, 'Internal Server Error');
        exit();
    }

    $response_data = array(
        'game_id'    => $game_id,
        'board_data' => $board_data
    );

    echo json_response(201, 'Game created', $response_data);
    exit();
}

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

function db_create_board($game_id, $board_data)
{
    $conn = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql  = "INSERT INTO board (game_id, board_data) VALUES ('$game_id', '$board_data')";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    if (!mysqli_query($conn, $sql)) {
        mysqli_close($conn);
        return false;
    }

    $board_id = mysqli_insert_id($conn);

    mysqli_close($conn);
    
    return $board_id;
}

function get_new_board()
{
    return array(
        1  => ['m1', 'm2'],
        2  => [],
        3  => [],
        4  => [],
        5  => [],
        6  => ['a11', 'a12', 'a13', 'a14', 'a15'],
        7  => [],
        8  => ['a8', 'a9', 'a10'],
        9  => [],
        10 => [],
        11 => [],
        12 => ['m3', 'm4', 'm5', 'm6', 'm7'],
        13 => ['a3', 'a4', 'a5', 'a6', 'a7'],
        14 => [],
        15 => [],
        16 => [],
        17 => ['m8', 'm9', 'm10'],
        18 => [],
        19 => ['m11', 'm12', 'm13', 'm14', 'm15'],
        20 => [],
        21 => [],
        22 => [],
        23 => [],
        24 => ['a1', 'a2'],
    );
}

?>
