$(function() {
    $('#loginForm').on('submit', function(event) {
        event.preventDefault();
    
        var data = { 
            username: $('#username').val(),
            password: $('#password').val()
        };

        login(data);
    });

    $('#logout').on('click', function() {
        logout();
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
