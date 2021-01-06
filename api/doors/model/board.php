<?php

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

function db_get_board($game_id)
{
    $conn = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql  = "SELECT * FROM board WHERE game_id = '$game_id'";

    mysqli_set_charset($conn, "utf8");

    if (mysqli_connect_errno()) {
        return false;
    }

    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_close($conn);   
        return array();
    }
    
    $board_data = mysqli_fetch_assoc($result);

    mysqli_close($conn);

    return $board_data;
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

function db_update_initial_roll($am_owner, $roll, $board_id)
{
    $die_col = ($am_owner) ? 'die_1' : 'die_2';
    $conn    = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql     = "UPDATE board SET $die_col='$roll' WHERE board_id='$board_id'";

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
    
    return $board_id;
}

function db_update_roll($board_id, $die_1, $die_2)
{
    $conn = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql  = "UPDATE board SET die_1='$die_1', die_2='$die_2' WHERE board_id='$board_id'";

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
    
    return $board_id;
}

function db_update_game_status($game_id, $status)
{
    $conn = new mysqli(HOST, USERNAME, DB_PWD, DATABASE);
    $sql  = "UPDATE game SET status='$status' WHERE game_id='$game_id'";

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

function get_initial_roll($am_owner, $die_1, $die_2)
{
    $roll = mt_rand(1,6);

    if ($die_1 == 0 && $die_2 == 0) {
        return $roll;
    }

    if ($am_owner) {
        while ($die_2 == $roll) {
            $roll = mt_rand(1,6);
        }
    } else {
        while ($die_1 == $roll) {
            $roll = mt_rand(1,6);
        }
    }

    return $roll;
}

function get_roll()
{
    return array(
        'die_1' => mt_rand(1,6),
        'die_2' => mt_rand(1,6)
    );
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
