<?php

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
    $sql  = "UPDATE game SET player_2_id='$user_id', status='game started' WHERE game_id='$game_id'";

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
