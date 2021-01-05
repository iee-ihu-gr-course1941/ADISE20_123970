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
});

function login(data) {
    console.log('logging user in');

    hideLoginError();

    $.ajax({
        url: 'api/login.php',
        method: 'POST',
        dataType: 'json',
        contentType: 'application/json', 
        data: JSON.stringify(data),
        success: function() {
            location.reload();
        },
        error: function(xhr) {
            response = xhr.responseJSON;

            console.log('login error');
            console.log(response);

            showLoginError(response.status, response.message);
        }

    });
}

function logout() {
    console.log('logging user out');

    $.ajax({
        url: 'api/logout.php',
        method: 'DELETE',
        contentType: 'application/json', 
        success: function() {
            location.reload();
        },
        error: function(xhr) {
            response = xhr.responseJSON;

            console.log('logout error');
            console.log(response);
        }

    });
}

function hideLoginError() {
    console.log('hiding login error');

    $('#loginError').text('');
    $('#loginError').addClass('d-none');
}

function showLoginError(status, text) {
    console.log('showing login error');

    $('#loginError').text(text);
    $('#loginError').removeClass('d-none');
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

            updateGameStatus();
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

function updateGameStatus() {
    console.log('showing game stats');

    $('#gameStats').removeClass('d-none');
    $('#gameId').text(game.game_id);
    $('#player1').text(game.player_1);
    $('#player2').text(game.player_2);
    $('#status').text(game.status);
}

function setCookie(name, value, days) {
    var d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    
    for (var i = 0; i <ca.length; i++) {
        var c = ca[i];
        
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }

    return "";
}
