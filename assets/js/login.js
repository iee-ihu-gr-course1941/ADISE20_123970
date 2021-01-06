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