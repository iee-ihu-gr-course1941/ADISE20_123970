$(function() {
    $('#loginForm').on('submit', function(event) {
        event.preventDefault();
    
        var data = { 
            username: $('#username').val(),
            password: $('#password').val()
        };

        login(data);
    });
});


function login(data) {
    hideLoginError();

    $.ajax({
        url: "api/login.php",
        method: "POST",
        dataType: "json",
        contentType: 'application/json', 
        data: JSON.stringify(data),
        success: function() {
            location.reload();
        },
        error: function(xhr) {
            response = xhr.responseJSON;

            console.log(response);

            showLoginError(response.status, response.message);
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
