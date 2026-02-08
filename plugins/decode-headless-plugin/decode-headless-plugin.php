<?php
/**
 * Plugin Name: Decode Headless Plugin
 * Description: Plugin de communication avec le CMS Headless - Projet Semestriel.
 * Version: 1.0
 * Author: Ton Nom
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DecodeHeadless {

    public function __construct() {
        // Administration
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // AJAX
        add_action('wp_ajax_headless_login_action', [$this, 'handle_login']);
        add_action('wp_ajax_headless_logout_action', [$this, 'handle_logout']);

        // Shortcodes (Point 2 du sujet)
        add_shortcode('headless_post', [$this, 'render_post_shortcode']);
        add_shortcode('headless_list', [$this, 'render_list_shortcode']);
        add_shortcode('headless_info', [$this, 'render_info_shortcode']);
    }

    /**
     * Chargement des scripts et styles pour l'admin
     */
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_headless-settings' !== $hook) return;

        wp_enqueue_script('headless-ajax-js', plugin_dir_url(__FILE__) . 'assets/admin-scripts.js', ['jquery'], '1.0', true);
        
        // Passage de variables PHP à JS
        wp_localize_script('headless-ajax-js', 'decode_headless_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('headless_ajax_nonce')
        ]);
    }

    /**
     * Création du menu d'administration
     */
    public function add_admin_menu() {
        add_menu_page(
            'Headless CMS',
            'Headless Admin',
            'manage_options',
            'headless-settings',
            [$this, 'render_admin_page'],
            'dashicons-cloud',
            110
        );
    }

    /**
     * Rendu HTML de la page d'administration
     */
    public function render_admin_page() {
        $token = get_option('headless_security_token', '');
        ?>
        <div class="wrap">
            <h1>Connexion au CMS Headless</h1>
            <hr>
            
            <div id="headless-admin-container" style="display: flex; gap: 20px;">
                <div style="flex: 1; background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                    <form id="headless-login-form">
                        <p>
                            <label for="headless_login">Identifiant API</label><br>
                            <input type="text" id="headless_login" class="regular-text" required>
                        </p>
                        <p>
                            <label for="headless_password">Mot de passe</label><br>
                            <input type="password" id="headless_password" class="regular-text" required>
                        </p>
                        <p>
                            <label for="headless_secret">Secret Key (optionnel)</label><br>
                            <input type="text" id="headless_secret" class="regular-text">
                        </p>
                        <p>
                            <button type="submit" class="button button-primary">Se connecter</button>
                            <button type="button" id="headless-logout" class="button">Déconnexion</button>
                        </p>
                    </form>
                    <div id="login-response-message" style="margin-top: 15px; font-weight: bold;"></div>
                </div>

                <div style="flex: 1; background: #f9f9f9; padding: 20px; border: 1px solid #ccd0d4;">
                    <h3>Statut de la connexion</h3>
                    <label>Token de sécurité (non éditable) :</label>
                    <input type="text" id="headless_token" value="<?php echo esc_attr($token); ?>" readonly style="width: 100%; background: #eee; margin-top: 5px;">
                    
                    <hr>
                    <h3>Shortcodes disponibles</h3>
                    <ul>
                        <li><code>[headless_post id="1"]</code> : Affiche un article spécifique.</li>
                        <li><code>[headless_list limit="5"]</code> : Liste les derniers articles.</li>
                        <li><code>[headless_info]</code> : Affiche les infos du CMS.</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Traitement de la connexion (AJAX)
     */
    public function handle_login() {
        check_ajax_referer('headless_ajax_nonce', 'security');

        $login = sanitize_text_field($_POST['login']);
        // Ici, on simule une vérification API
        if ($login === 'admin') {
            $token = bin2hex(random_bytes(16)); // Génération d'un token fictif
            update_option('headless_security_token', $token);
            wp_send_json_success(['message' => 'Connexion réussie !', 'token' => $token]);
        } else {
            wp_send_json_error(['message' => 'Identifiants incorrects.']);
        }
    }

    /**
     * Traitement de la déconnexion (AJAX)
     */
    public function handle_logout() {
        check_ajax_referer('headless_ajax_nonce', 'security');
        delete_option('headless_security_token');
        wp_send_json_success(['message' => 'Déconnecté avec succès.']);
    }

    /**
     * Fonctions de rendu des Shortcodes
     */
    public function render_post_shortcode($atts) {
        $a = shortcode_atts(['id' => 0], $atts);
        return "<div class='headless-post'>Contenu de l'article ID : " . esc_html($a['id']) . " (Reçu via API)</div>";
    }

    public function render_list_shortcode($atts) {
        return "<ul class='headless-list'><li>Article exemple 1</li><li>Article exemple 2</li></ul>";
    }

    public function render_info_shortcode() {
        return "<p class='headless-info'>CMS Headless v1.0 connecté.</p>";
    }
}

// Initialisation
new DecodeHeadless();