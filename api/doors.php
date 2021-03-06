<?php

/*
 * The Doors API Router
 * 
 * All API routes are declared here
 */

require("../includes/db_conf.php");
require("../includes/api_helpers.php");

$user    = authenticate($_SERVER['HTTP_X_TOKEN']);
$method  = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$data    = json_decode(file_get_contents('php://input'), true);

// Web Service Routes
$web_service = array_shift($request);
$endpoint    = array_shift($request);
$parameters  = $request;

switch ($web_service) {
    case 'game':
        require("./doors/game.php");
        require("./doors/model/game.php");
        game_routes($user, $endpoint, $method, $parameters, $data);
        break;
    case 'board':
        require("./doors/board.php");
        require("./doors/model/board.php");
        board_routes($user, $endpoint, $method, $parameters, $data);
        break;
    default:
        echo json_response(404, 'Web Service Not Found');
        exit();
}

?>