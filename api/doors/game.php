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

?>
