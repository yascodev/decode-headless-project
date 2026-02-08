jQuery(document).ready(function($) {
    $('#headless-login-form').on('submit', function(e) {
        e.preventDefault();
        
        const data = {
            action: 'headless_login_action',
            security: decode_headless_obj.nonce,
            login: $('#headless_login').val(),
            password: $('#headless_password').val(),
            secret: $('#headless_secret').val()
        };

        $('#login-response-message').text('Connexion en cours...');

        $.post(decode_headless_obj.ajax_url, data, function(response) {
            if (response.success) {
                $('#login-response-message').css('color', 'green').text(response.data.message);
                $('#headless_token').val(response.data.token);
            } else {
                $('#login-response-message').css('color', 'red').text(response.data.message);
            }
        });
    });
});