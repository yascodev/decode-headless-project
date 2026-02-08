jQuery(document).ready(function($) {
    
    /**
     * 1. GESTION DE LA CONNEXION (Point 1 du sujet)
     * Envoi asynchrone des identifiants vers WordPress via AJAX
     */
    $('#headless-login-form').on('submit', function(e) {
        e.preventDefault();
        
        const data = {
            action: 'headless_login_action',
            security: decode_headless_obj.nonce,
            login: $('#headless_login').val(),
            password: $('#headless_password').val(),
            secret: $('#headless_secret').val()
        };

        $('#login-response-message')
            .text('Tentative de connexion...')
            .css('color', '#666');

        $.post(decode_headless_obj.ajax_url, data, function(response) {
            if (response.success) {
                $('#login-response-message')
                    .css('color', 'green')
                    .text(response.data.message);
                
                // Mise à jour du champ token en lecture seule
                $('#headless_token').val(response.data.token);
            } else {
                $('#login-response-message')
                    .css('color', 'red')
                    .text(response.data.message);
            }
        });
    });

    /**
     * 2. GESTION DE LA DÉCONNEXION (Point 1 du sujet)
     * Supprime le token côté serveur et vide l'interface
     */
    $('#headless-logout').on('click', function(e) {
        e.preventDefault();

        const data = {
            action: 'headless_logout_action',
            security: decode_headless_obj.nonce
        };

        $.post(decode_headless_obj.ajax_url, data, function(response) {
            if (response.success) {
                // Réinitialisation des champs
                $('#headless_token').val('');
                $('#headless_login, #headless_password, #headless_secret').val('');
                
                $('#login-response-message')
                    .css('color', '#2271b1')
                    .text(response.data.message);
            }
        });
    });

    /**
     * 3. VIDER LE CACHE (Point 4 Bonus)
     * Supprime le Transient WordPress pour forcer une nouvelle requête API
     */
    $('#clear-cache-btn').on('click', function(e) {
        e.preventDefault();
        const $btn = $(this);
        
        $btn.text('Nettoyage...').prop('disabled', true);

        const data = {
            action: 'headless_clear_cache',
            security: decode_headless_obj.nonce
        };

        $.post(decode_headless_obj.ajax_url, data, function(response) {
            if (response.success) {
                alert(response.data.message);
                // Rechargement de la page pour rafraîchir le tableau des données
                location.reload();
            }
        });
    });

});