<?php

require("../includes/api_helpers.php");

if ($_SERVER['REQUEST_METHOD'] != 'DELETE') {
    echo json_response(405, 'Method Not Allowed');
    exit();
}

session_start();
session_unset();

echo json_response(204, 'Deleted');
exit();

?>