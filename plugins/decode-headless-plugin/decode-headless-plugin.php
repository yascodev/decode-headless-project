<?php
/**
 * Plugin Name: Decode Headless Plugin
 * Description: Plugin de communication avec le CMS Headless (Projet Semestriel)
 * Version: 1.0
 * Author: Ton Nom
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DecodeHeadless {

    public function __construct() {
        // On enregistre le menu dans l'administration
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
    }

    public function add_admin_menu() {
        add_menu_page(
            'Headless CMS',           // Titre de la page
            'Headless Admin',         // Nom dans le menu
            'manage_options',         // Droit requis
            'decode-headless-admin',  // Identifiant (slug)
            [ $this, 'render_admin_page' ], // Fonction d'affichage
            'dashicons-cloud'         // Icône cloud
        );
    }

public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Connexion au CMS Headless</h1>
            <hr>
            
            <form id="headless-login-form" method="post" style="max-width: 400px; background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                <p>
                    <label for="headless_login">Identifiant API</label><br>
                    <input type="text" id="headless_login" name="headless_login" class="regular-text" required>
                </p>
                <p>
                    <label for="headless_password">Mot de passe</label><br>
                    <input type="password" id="headless_password" name="headless_password" class="regular-text" required>
                </p>
                <p>
                    <label for="headless_secret">Secret Key (Optionnel)</label><br>
                    <input type="text" id="headless_secret" name="headless_secret" class="regular-text">
                </p>
                
                <p>
                    <button type="submit" class="button button-primary">Se connecter</button>
                    <button type="button" id="headless-logout" class="button">Déconnexion</button>
                </p>
                
                <div id="login-response-message" style="margin-top: 15px; font-weight: bold;"></div>
            </form>

            <h2 style="margin-top: 30px;">Token de sécurité</h2>
            <input type="text" id="headless_token" value="<?php echo get_option('headless_security_token', 'Aucun token généré'); ?>" readonly style="width: 100%; background: #f0f0f1; cursor: not-allowed;">
            <p class="description">Ce token est généré automatiquement après une connexion réussie.</p>
        </div>
        <?php
    }
}

// Lancement automatique
new DecodeHeadless();