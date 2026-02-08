<?php
/**
 * Plugin Name: Decode Headless Plugin
 * Description: Plugin de communication avec le CMS Headless - Projet Semestriel (POO, AJAX, API, Cache).
 * Version: 1.1
 * Author: Ton Nom
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DecodeHeadless {

    public function __construct() {
        // Hooks d'administration
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // Hooks AJAX
        add_action('wp_ajax_headless_login_action', [$this, 'handle_login']);
        add_action('wp_ajax_headless_logout_action', [$this, 'handle_logout']);
        add_action('wp_ajax_headless_clear_cache', [$this, 'handle_clear_cache']);

        // Shortcodes (Point 2)
        add_shortcode('headless_post', [$this, 'render_post_shortcode']);
        add_shortcode('headless_list', [$this, 'render_list_shortcode']);
    }

    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_headless-settings' !== $hook) return;
        wp_enqueue_script('headless-ajax-js', plugin_dir_url(__FILE__) . 'assets/admin-scripts.js', ['jquery'], '1.1', true);
        wp_localize_script('headless-ajax-js', 'decode_headless_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('headless_ajax_nonce')
        ]);
    }

    public function add_admin_menu() {
        add_menu_page('Headless CMS', 'Headless Admin', 'manage_options', 'headless-settings', [$this, 'render_admin_page'], 'dashicons-cloud', 110);
    }

    /**
     * Point 3 & 4 : Récupération API avec Système de Cache (Transients)
     */
    private function get_api_content() {
        $cache_key = 'headless_api_content_cache';
        $cached_data = get_transient($cache_key);

        if (false !== $cached_data) {
            return $cached_data; // Retourne les données du cache
        }

        // Si pas de cache, on requête l'API
        $response = wp_remote_get('https://jsonplaceholder.typicode.com/posts?_limit=5');
        
        if (is_wp_error($response)) return [];

        $data = json_decode(wp_remote_retrieve_body($response), true);

        // On stocke en cache pour 1 heure (3600 secondes)
        set_transient($cache_key, $data, HOUR_IN_SECONDS);

        return $data;
    }

    public function render_admin_page() {
        $token = get_option('headless_security_token', '');
        $api_data = $this->get_api_content();
        ?>
        <div class="wrap">
            <h1>Configuration Headless CMS</h1>
            <hr>
            
            <div style="display: flex; gap: 20px;">
                <div style="flex: 1; background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                    <h3>Connexion API</h3>
                    <form id="headless-login-form">
                        <p><label>Identifiant</label><br><input type="text" id="headless_login" class="regular-text" required></p>
                        <p><label>Mot de passe</label><br><input type="password" id="headless_password" class="regular-text" required></p>
                        <button type="submit" class="button button-primary">Se connecter</button>
                        <button type="button" id="headless-logout" class="button">Déconnexion</button>
                    </form>
                    <div id="login-response-message" style="margin-top: 15px;"></div>
                    <hr>
                    <label>Token de sécurité (Lecture seule) :</label>
                    <input type="text" id="headless_token" value="<?php echo esc_attr($token); ?>" readonly style="width: 100%; background: #eee; margin-top:5px;">
                </div>

                <div style="flex: 2; background: #fff; padding: 20px; border: 1px solid #ccd0d4;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3>Contenu reçu (API)</h3>
                        <button id="clear-cache-btn" class="button button-secondary">Vider le cache</button>
                    </div>
                    
                    <table class="wp-list-table widefat fixed striped" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Titre de l'article</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($api_data)) : foreach ($api_data as $post) : ?>
                                <tr>
                                    <td><?php echo esc_html($post['id']); ?></td>
                                    <td><?php echo esc_html($post['title']); ?></td>
                                </tr>
                            <?php endforeach; else : ?>
                                <tr><td colspan="2">Aucune donnée trouvée.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    // Handlers AJAX
    public function handle_login() {
        check_ajax_referer('headless_ajax_nonce', 'security');
        $login = sanitize_text_field($_POST['login']);
        if ($login === 'admin') {
            $token = bin2hex(random_bytes(16));
            update_option('headless_security_token', $token);
            wp_send_json_success(['message' => 'Connexion réussie !', 'token' => $token]);
        }
        wp_send_json_error(['message' => 'Identifiants incorrects.']);
    }

    public function handle_logout() {
        check_ajax_referer('headless_ajax_nonce', 'security');
        delete_option('headless_security_token');
        wp_send_json_success(['message' => 'Session fermée.']);
    }

    public function handle_clear_cache() {
        check_ajax_referer('headless_ajax_nonce', 'security');
        delete_transient('headless_api_content_cache');
        wp_send_json_success(['message' => 'Cache vidé avec succès !']);
    }

    // Shortcodes (Point 2)
    public function render_post_shortcode($atts) {
        $a = shortcode_atts(['id' => 1], $atts);
        return "<div class='headless-card'>Affichage de l'article API n°" . esc_html($a['id']) . "</div>";
    }

    public function render_list_shortcode($atts) {
        return "<ul class='headless-list'><li>Item API A</li><li>Item API B</li></ul>";
    }
}

new DecodeHeadless();