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

    // roll start
    $('#rollStart').on('click', function() {
        if (me.token && (game.status == 'game created' || game.status == 'game started')) {
            hideRollDice();
            showDiceLoading();
            rollStart(game.game_id, me.token);
        } else {
            // TODO: make pretty, e.g. modal
            alert('If you\'re seeing this, something went really wrong... LoL');
        }
    });

    // roll turn
    $('#rollTurn').on('click', function() {
        if (me.token && (game.status == 'player 1 turn' || game.status == 'player 2 turn')) {
            hideRollDice();
            showDiceLoading();
            rollTurn(game.game_id, me.token);
        } else {
            // TODO: make pretty, e.g. modal
            alert('If you\'re seeing this, something went really wrong... LoL');
        }
    });

    // select piece
    $('.thesi').on('click', '.pouli', function() {
        selectPiece(this);
    });
});


//
// UI
//

function play() {
    if (window.me.username != window.game.player_1 && window.me.username != window.game.player_2) {
        return;
    }

    var am_player = (window.me.username == window.game.player_1) ? 1 : 2;

    refreshGameStatus();
    refreshBoard();

    switch (window.game.status) {
        case 'game created':
        case 'game started':
            // both players must do initial roll
            if ($.isEmptyObject(window.board) || window.board['die_' + am_player] == 0) {
                showRollDice('start');
            } else {
                loadGame(window.game.game_id, window.me.token);
            }
            break;
        case 'player 1 turn':
            if (am_player == 1) {
                stopGameTick();
                showRollDice('turn');
                hideDiceLoading();
            } else {
                startGameTick(play);
                hideRollDice();
                showDiceLoading();
            }
            break;
        case 'player 2 turn':
            if (am_player == 2) {
                stopGameTick();
                showRollDice('turn');
                hideDiceLoading();
            } else {
                startGameTick(play);
                hideRollDice();
                showDiceLoading();
            }
            break;
        case 'game ended':
        case 'game aborted':
            hideRollDice();
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

    if ($.isEmptyObject(window.board.board_data)) {
        return;
    }

    var data = window.board.board_data;

    // loop positions
    for (var pos in data) {
        if (data[pos].length == 0) {
            continue;
        }

        // loop pieces in position
        var pieces = data[pos];
        for (var key in pieces) {
            var piece = pieces[key];
            
            var el = $('#' + piece).detach();
            $('.thesi.thesi' + pos).append(el);
        }
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
            cache      : false,
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
            cache      : false,
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
        cache      : false,
        success    : function(response) {
            window.game = response.data;
            setCookie('game_id', window.game.game_id, 1);

            if (!$.isEmptyObject(window.board) || window.game.status != 'game created') {
                syncBoard(window.game.game_id, window.me.token);
            } else {
                play();
            }
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

function syncBoard(game_id, token) {
    console.log('syncing board');

    $.ajax({
        url        : 'api/doors.php/board/game/' + game_id,
        headers    : { 'X-Token': token },
        method     : 'GET',
        contentType: 'application/json',
        cache      : false,
        success    : function(response) {
            window.board            = response.data;
            window.board.board_data = JSON.parse(response.data.board_data);

            refreshBoard();
            play();
        },
        error      : function(xhr) {
            response = xhr.responseJSON;

            console.log('sync board error');
            console.log(response);

            // TODO: make pretty, e.g. modal
            alert('Error syncing board, status: ' + response.status + ', message: ' + response.message);
        }
    });
}

function rollStart(game_id, token) {
    console.log('rolling dice (start)');

    $.ajax({
        url        : 'api/doors.php/board/dice/',
        headers    : { 'X-Token': token },
        method     : 'POST',
        data       : JSON.stringify({ game_id: game_id }),
        dataType   : 'json',
        contentType: 'application/json',
        cache      : false,
        success    : function(response) {
            console.log('rolled dice (start)');
            console.log(response);

            deleteRollStart();
            syncBoard(window.game.game_id, window.me.token);
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

function rollTurn(game_id, token) {
    console.log('rolling dice (turn)');

    $.ajax({
        url        : 'api/doors.php/board/dice/',
        headers    : { 'X-Token': token },
        method     : 'PUT',
        data       : JSON.stringify({ game_id: game_id }),
        dataType   : 'json',
        contentType: 'application/json',
        cache      : false,
        success    : function(response) {
            console.log('rolled dice (turn)');
            console.log(response);

            window.board.die_1 = response.data.die_1;
            window.board.die_2 = response.data.die_2;
            updateDice(response.data.die_1, response.data.die_2);
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
