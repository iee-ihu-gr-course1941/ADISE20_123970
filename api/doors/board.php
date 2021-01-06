<?php

/**
 * The Board Web Service
 * 
 * Endpoints:
 *  - [GET]  /board/game/{id} Get the board data of the game id {id}
 *  - [POST] /board/dice/     Initial dice roll (game started)
 *  - [PUT]  /board/dice/     Roll dice (player turn)
 *  - [PUT]  /board/piece/{p} Move piece {p} at location (defined in data)
 */
function board_routes($user, $endpoint, $method, $parameters, $data)
{    
    // [GET] /board/game/{id}
    if ($endpoint == 'game' && $method == 'GET' && !empty($parameters)) {
        get_board($user, $parameters[0]);
    }
    
    // [POST] /board/dice/
    else if ($endpoint == 'dice' && $method == 'POST') {
        roll_start($user, $data);
    }

    // [PUT] /board/dice/
    else if ($endpoint == 'dice' && $method == 'PUT') {
        roll_turn($user, $data);
    }

    // [PUT] /board/piece/{p}
    else if ($endpoint == 'piece' && $method == 'PUT' && !empty($parameters)) {
        // TODO
        //move_piece();
    }
    
    // 405 method not allowed
    else {
        echo json_response(405, 'Method Not Allowed');
        exit();
    }
}

function get_board($user, $game_id)
{
    // validate payload
    if (empty($game_id)) {
        echo json_response(400, 'Bad Request');
        exit();
    }

    // get game data
    $game = db_get_game($game_id);

    if ($game === false) {
        echo json_response(500, 'Error fetching game data');
        exit();
    }

    if (empty($game)) {
        echo json_response(404, 'Game not found');
        exit();
    }
    
    if ($user['user_id'] != $game['player_1_id'] && $user['user_id'] != $game['player_2_id']) {
        echo json_response(403, 'User is not allowed in this game');
        exit();
    }

    // get board data
    $board = db_get_board($game['game_id']);

    if ($board === false || empty($board)) {
        echo json_response(500, 'Error fetching board data');
        exit();
    }

    echo json_response(200, 'OK', $board);
    exit();
}

function roll_start($user, $data)
{
    // validate payload
    if (empty($data) || !isset($data['game_id'])) {
        echo json_response(400, 'Bad Request');
        exit();
    }

    // get game data
    $game = db_get_game($data['game_id']);

    if ($game === false) {
        echo json_response(500, 'Error fetching game data');
        exit();
    }

    if (empty($game)) {
        echo json_response(404, 'Game not found');
        exit();
    }
    
    if ($user['user_id'] != $game['player_1_id'] && $user['user_id'] != $game['player_2_id']) {
        echo json_response(403, 'User is not allowed in this game');
        exit();
    }

    $am_owner = ($user['user_id'] == $game['player_1_id']);

    // get board data
    $board = db_get_board($game['game_id']);

    // initialize board if necessary
    if (empty($board)) {
        $board_data = json_encode(get_new_board());
        $board_id   = db_create_board($game['game_id'], $board_data);

        if ($board_id === false) {
            echo json_response(500, 'Error initializing board');
            exit();
        }

        $board = db_get_board($game['game_id']);
        
        if ($board === false) {
            echo json_response(500, 'Error fetching board data');
            exit();
        }

        if (empty($board)) {
            echo json_response(500, 'Board could not be initialized');
            exit();
        }
    }

    // roll and update board
    $roll     = get_initial_roll($am_owner, $board['die_1'], $board['die_2']);
    $board_id = db_update_initial_roll($am_owner, $roll, $board['board_id']);

    if ($board_id == false || $board_id == 0) {
        echo json_response(500, 'Error updating initial roll');
        exit();
    }

    if ($am_owner) {
        $board['die_1'] = $roll;
    } else {
        $board['die_2'] = $roll;
    }

    // check if both players have rolled and update game status
    if ($board['die_1'] != 0 && $board['die_2'] != 0) {
        $status  = ($board['die_1'] > $board['die_2']) ? 'player 1 turn' : 'player 2 turn';
        $game_id = db_update_game_status($game['game_id'], $status);

        if ($game_id == false || $game_id == 0) {
            echo json_response(500, 'Error updating game status');
            exit();
        }

        $game['status'] = $status;

        echo json_response(201, 'Both players rolled, ' . $status);
        exit();
    }

    echo json_response(201, 'Waiting for other player to roll');
    exit();
}

function roll_turn($user, $data)
{
    // validate payload
    if (empty($data) || !isset($data['game_id'])) {
        echo json_response(400, 'Bad Request');
        exit();
    }

    // get game data
    $game = db_get_game($data['game_id']);

    if ($game === false) {
        echo json_response(500, 'Error fetching game data');
        exit();
    }

    if (empty($game)) {
        echo json_response(404, 'Game not found');
        exit();
    }
    
    if ($user['user_id'] != $game['player_1_id'] && $user['user_id'] != $game['player_2_id']) {
        echo json_response(403, 'User is not allowed in this game');
        exit();
    }

    // get board data
    $board = db_get_board($game['game_id']);

    if ($board === false || empty($board)) {
        echo json_response(500, 'Error fetching board data');
        exit();
    }

    // roll and update board
    $roll     = get_roll();
    $board_id = db_update_roll($board['board_id'], $roll['die_1'], $roll['die_2']);

    if ($board_id == false || $board_id == 0) {
        echo json_response(500, 'Error updating roll');
        exit();
    }

    echo json_response(200, 'OK', $roll);
    exit();
}

?>
