jQuery(document).ready(function($) {
    
    /**
     * GESTION DE LA CONNEXION (Point 1 du sujet)
     */
    $('#headless-login-form').on('submit', function(e) {
        e.preventDefault();
        
        // Préparation des données à envoyer
        const data = {
            action: 'headless_login_action',
            security: decode_headless_obj.nonce,
            login: $('#headless_login').val(),
            password: $('#headless_password').val(),
            secret: $('#headless_secret').val()
        };

        // Feedback visuel immédiat
        $('#login-response-message')
            .text('Tentative de connexion en cours...')
            .css('color', '#666');

        // Envoi de la requête POST asynchrone
        $.post(decode_headless_obj.ajax_url, data, function(response) {
            if (response.success) {
                // Succès : affichage du message et mise à jour du champ Token
                $('#login-response-message')
                    .css('color', 'green')
                    .text(response.data.message);
                
                $('#headless_token').val(response.data.token);
            } else {
                // Erreur : affichage du message d'erreur
                $('#login-response-message')
                    .css('color', 'red')
                    .text(response.data.message);
            }
        });
    });

    /**
     * GESTION DE LA DÉCONNEXION (Point 1 du sujet)
     */
    $('#headless-logout').on('click', function(e) {
        e.preventDefault();

        const data = {
            action: 'headless_logout_action',
            security: decode_headless_obj.nonce
        };

        $.post(decode_headless_obj.ajax_url, data, function(response) {
            if (response.success) {
                // Réinitialisation de l'interface
                $('#headless_token').val('');
                $('#headless_login, #headless_password, #headless_secret').val('');
                
                $('#login-response-message')
                    .css('color', '#2271b1')
                    .text(response.data.message);
            }
        });
    });

});