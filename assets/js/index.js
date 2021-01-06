//
// global variables (window)
//

var game  = {};
var board = {};


//
// Page loaded
//

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
        if (me.token) {
            rollDice(me.token);
        } else {
            // TODO: make pretty, e.g. modal
            alert('If you\'re seeing this, something went really wrong... LoL');
        }
    });
});


//
// UI
//

function play() {
    refreshGameStatus();
    refreshBoard();

    switch (window.me.username) {
        // host
        case window.game.player_1:
            var am_player = 1;
            break;
        // rival
        case window.game.player_2:
            var am_player = 2;
            break;
        // guest
        default:
            var am_player = 0;
            break;
    }

    // TODO:
    switch (window.game.status) {
        case 'game created':
        case 'game started':
            showRollDice();
            break;
        case 'player 1 turn':
            (am_player == 1) ? showRollDice() : hideRollDice()
            break;
        case 'player 2 turn':
            (am_player == 2) ? showRollDice() : hideRollDice()
            break;
        case 'game ended':
            stopGameTick();
            break;
        case 'game aborted':
            stopGameTick();
            break;
    }
}

function refreshGameStatus() {
    console.log('refreshing game status bar');

    $('#gameStats').removeClass('d-none');
    $('#gameId').text(window.game.game_id);
    $('#player1').text(window.game.player_1);
    $('#player2').text(window.game.player_2);
    $('#status').text(window.game.status);
}

function refreshBoard() {
    console.log('refreshing board');

    // TODO
}

function rollDice(token) {
    hideRollDice();
    showDiceLoading();

    if (window.game.status == 'game created' || window.game.status == 'game started') {
        rollStart(token);
    }

    if (window.game.status == 'player 1 turn' || window.game.status == 'player 2 turn') {
        rollTurn(token);
    }
}


//
// AJAX
//

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

function loadGame(game_id, token) {
    console.log('loading game');

    $.ajax({
        url        : 'api/doors.php/game/' + game_id,
        headers    : { 'X-Token': token },
        method     : 'GET',
        contentType: 'application/json',
        success    : function(response) {
            window.game = response.data;
            setCookie('game_id', window.game.game_id, 1);
            startGameTick(play);
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

function rollStart(token) {
    console.log('rolling dice (start)');

    $.ajax({
        url        : 'api/doors.php/board/dice/',
        headers    : { 'X-Token': token },
        method     : 'POST',
        contentType: 'application/json',
        success    : function(response) {
            // TODO: update board dice values
        },
        error      : function(xhr) {
            response = xhr.responseJSON;

            console.log('roll dice error (start)');
            console.log(response);

            // TODO: make pretty, e.g. modal
            alert('Error rolling dice (start), status: ' + response.status + ', message: ' + response.message);
        }
    });
}

function rollTurn(token) {
    console.log('rolling dice (turn)');

    $.ajax({
        url        : 'api/doors.php/board/dice/',
        headers    : { 'X-Token': token },
        method     : 'PUT',
        contentType: 'application/json',
        success    : function(response) {
            // TODO: update board dice values
        },
        error      : function(xhr) {
            response = xhr.responseJSON;

            console.log('roll dice error (turn)');
            console.log(response);

            // TODO: make pretty, e.g. modal
            alert('Error rolling dice (turn), status: ' + response.status + ', message: ' + response.message);
        }
    });
}
