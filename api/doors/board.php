<?php

/**
 * The Board Web Service
 * 
 * Endpoints:
 *  - [GET]  /board/          Get the board data
 *  - [POST] /board/dice/     Initial dice roll (game started)
 *  - [PUT]  /board/dice/     Roll dice (player turn)
 *  - [PUT]  /board/piece/{p} Move piece {p} at location (defined in data)
 */
function board_routes($user, $endpoint, $method, $parameters, $data)
{    
    // [GET] /board/
    if (empty($endpoint) && $method == 'GET') {
        // TODO
        //get_board();
    }
    
    // [POST] /board/dice/
    else if ($endpoint == 'piece' && $method == 'POST') {
        // TODO
        //roll_start();
    }

    // [PUT] /board/dice/
    else if ($endpoint == 'piece' && $method == 'PUT') {
        // TODO
        //roll();
    }

    // [PUT] /board/piece/{p}
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

?>
