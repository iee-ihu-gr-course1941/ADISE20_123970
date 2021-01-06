// global variables (window)
var game  = {};
var board = {};

$(function() {
    // load game if already joined
    var game_id = getCookie('game_id');
    if (game_id && me.token) {
        loadGame(game_id, me.token);
    }

    // login
    $('#loginForm').on('submit', function(event) {
        event.preventDefault();
    
        var data = { 
            username: $('#username').val(),
            password: $('#password').val()
        };

        login(data);
    });

    // logout
    $('#logout').on('click', function() {
        logout();
    });

    // create game
    $('#createGame').on('click', function(event) {
        event.preventDefault();

        if (me.token) {
            createGame(me.token);
        } else {
            // TODO: make pretty, e.g. modal
            alert('Please login to create or join a game');
        }
    });

    // join game
    $('#joinGame').on('click', function(event) {
        event.preventDefault();

        if (me.token) {
            joinGame(me.token);
        } else {
            // TODO: make pretty, e.g. modal
            alert('Please login to create or join a game');
        }
    });

    // roll dice
    $('#rollDice').on('click', function() {
        rollDice(me.token);
    });
});

function loadGame(game_id, token) {
    console.log('loading game');

    $.ajax({
        url        : 'api/doors.php/game/' + game_id,
        headers    : { 'X-Token': token },
        method     : 'GET',
        contentType: 'application/json',
        success    : function(response) {
            window.game = response.data;
            refreshUI();
        },
        error      : function(xhr) {
            response = xhr.responseJSON;

            console.log('load game error');
            console.log(response);

            // TODO: make pretty, e.g. modal
            alert('Error loading game, status: ' + response.status + ', message: ' + response.message);
        }
    });
}

function createGame(token) {
    console.log('creating game');

    var game_id = getCookie('game_id');

    // game already exists
    if (game_id != "") {
        // TODO: make pretty, e.g. modal
        alert("Game with id " + game_id + " already exists, cannot create another one");
    } else {
        $.ajax({
            url        : 'api/doors.php/game/',
            headers    : { 'X-Token': token },
            method     : 'POST',
            contentType: 'application/json',
            success    : function(response) {
                var game_id = response.data.game_id;
                setCookie('game_id', game_id, 1);
                loadGame(game_id, window.me.token);
            },
            error      : function(xhr) {
                response = xhr.responseJSON;
    
                console.log('create game error');
                console.log(response);
    
                // TODO: make pretty, e.g. modal
                alert('Error creating game, status: ' + response.status + ', message: ' + response.message);
            }
        });
    }
}

function joinGame(token) {
    console.log('joining game');
    
    var game_id = getCookie('game_id');

    // game already exists
    if (game_id != "") {
        // TODO: make pretty, e.g. modal
        alert("Game with id " + game_id + " already exists, cannot create another one.");
    } else {
        // TODO: make pretty, e.g. modal
        game_id = prompt("Please enter the id of the game you wish to join.");

        if (parseInt(Number(game_id)) != game_id || isNaN(parseInt(game_id, 10))) {
            alert("Please enter a valid game id.");
            return;
        }

        $.ajax({
            url        : 'api/doors.php/game/',
            headers    : { 'X-Token': token },
            method     : 'PUT',
            contentType: 'application/json',
            data       : JSON.stringify({ game_id: game_id }),
            success    : function(response) {
                var game_id = response.data.game_id;
                setCookie('game_id', game_id, 1);
                loadGame(game_id, window.me.token);
            },
            error      : function(xhr) {
                response = xhr.responseJSON;
    
                console.log('join game error');
                console.log(response);
    
                // TODO: make pretty, e.g. modal
                alert('Error joining game, status: ' + response.status + ', message: ' + response.message);
            }
        });
    }
}

function refreshUI() {
    console.log('refreshing UI');

    $('#gameStats').removeClass('d-none');
    $('#gameId').text(game.game_id);
    $('#player1').text(game.player_1);
    $('#player2').text(game.player_2);
    $('#status').text(game.status);
}
