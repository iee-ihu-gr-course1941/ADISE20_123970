$(function() {
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
});

function login(data) {
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
    console.log('log user out');

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
    $('#loginError').text('');
    $('#loginError').addClass('d-none');
}

function showLoginError(status, text) {
    $('#loginError').text(text);
    $('#loginError').removeClass('d-none');
}

function createGame(token) {
    var game_id = getCookie('game_id');

    // game already exists
    if (game_id != "") {
        // TODO: make pretty, e.g. modal
        alert("Game with id " + game_id + " already exists, cannot create another one");
    } else {
        $.ajax({
            url: 'api/doors.php/board/',
            headers: { 'X-Token': token },
            method: 'POST',
            contentType: 'application/json',
            success: function(response) {
                var game_id = response.data.game_id;
                
                setCookie('game_id', game_id, 1);
                showGameStats(game_id);
                refreshBoard(response.data.board_data);
            },
            error: function(xhr) {
                response = xhr.responseJSON;
    
                console.log('create game error');
                console.log(response);
    
                // TODO: make pretty, e.g. modal
                alert('Error creating game, status: ' + response.status + ', message: ' + response.message);
            }
        });
    }
}

function showGameStats(game_id) {
    $('#gameStats').removeClass('d-none');
    $('#gameId').text(game_id);
}

function refreshBoard(data) {
    // TODO

    console.log('refreshing board');
    console.log(data);
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
