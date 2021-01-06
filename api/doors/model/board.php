<?php

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
