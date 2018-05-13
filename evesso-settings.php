<?php
/*
 * Plugin Name: Eve SSO Settings
 * Plugin URI: https://github.com/fuzzysteve/WordpressSocialLogin-EveSSO
 * Description: A plugin to let you change approved character/alliances/etc
 * Version: 1.0
 * Author: Steve Ronuken
 * Author URI: https://github.com/fuzzysteve/
 * License: MIT
 * */

class Evesso_Settings_Plugin {

    public function __construct() {
        // Hook into the admin menu
        add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
    }

    public function create_plugin_settings_page() {
        // Add the menu item and page
        $page_title = 'EveSSO Settings Page';
        $menu_title = 'EveSSO Settings';
        $capability = 'manage_options';
        $slug = 'Evesso_fields';
        $callback = array( $this, 'plugin_settings_page_content' );
        $icon = 'dashicons-admin-plugins';
        $position = 100;

        add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }

    public function plugin_settings_page_content() {
        if( $_POST['updated'] === 'true' ){
            $this->handle_form();
        } ?>
        <div class="wrap">
            <h2>EveSSO Settings</h2>
            <form method="POST">
                <input type="hidden" name="updated" value="true" />
                <?php wp_nonce_field( 'awesome_update', 'awesome_form' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th><label for="corporations">Approved Corporations</label></th>
                            <td><input name="corporations" id="corporations" type="text" value="<?php echo get_option('allowed_corporations'); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="alliances">Approved Alliances</label></th>
                            <td><input name="alliances" id="alliances" type="text" value="<?php echo get_option('allowed_alliances'); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="characters">Approved Characters</label></th>
                            <td><input name="characters" id="characters" type="text" value="<?php echo get_option('allowed_characters'); ?>" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label for="allapproved">All Approved</label></th>
                            <td><input name="allapproved" id="allapproved" type="checkbox" <? if (get_option('allowed_all') == true) { print "checked"; } ?> /></td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Set">
                </p>
            </form>
        </div> <?php
    }

    public function handle_form() {
        if( ! isset( $_POST['awesome_form'] ) || ! wp_verify_nonce( $_POST['awesome_form'], 'awesome_update' ) ){ ?>
           <div class="error">
               <p>Sorry, your nonce was not correct. Please try again.</p>
           </div> <?php
           exit;
        } else {
            update_option( 'allowed_corporations', $_POST["corporations"] );
            update_option( 'allowed_alliances', $_POST["alliances"] );
            update_option( 'allowed_characters', $_POST["characters"] );
            if (isset($_POST['allapproved'])) {
                update_option( 'allowed_all', true );
            } else {
                update_option( 'allowed_all', false );
            }
            ?>
                <div class="updated">
                    <p>Your fields were saved!</p>
                </div> <?php
        }
    }
}
new EveSSO_Settings_Plugin();
