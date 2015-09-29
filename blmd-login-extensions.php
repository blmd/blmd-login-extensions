<?php
/*
Plugin Name: BLMD Login Extensions
Plugin URI: https://github.com/blmd/blmd-login-extensions
Description: Adds another cookie for logged in admin
Author: blmd
Author URI: https://github.com/blmd
Version: 0.2

GitHub Plugin URI: https://github.com/blmd/blmd-login-extensions
*/

!defined( 'ABSPATH' ) && die;
define( 'BLMD_LOGIN_EXTENSIONS_VERSION', '0.2' );
define( 'BLMD_LOGIN_EXTENSIONS_URL', plugin_dir_url( __FILE__ ) );
define( 'BLMD_LOGIN_EXTENSIONS_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLMD_LOGIN_EXTENSIONS_BASENAME', plugin_basename( __FILE__ ) );

define( 'BLMD_LOGIN_EXTENSIONS_COOKIE_NAME', 'wp_admin_logged_in_' );

class BLMD_Login_Extensions {

	protected $expire;
	protected $expiration;

	public static function factory() {
		static $instance = null;
		if ( ! ( $instance instanceof self ) ) {
			$instance = new self;
			$instance->setup_actions();
		}
		return $instance;
	}

	protected function setup_actions() {
		add_action( 'set_logged_in_cookie', array( $this, 'set_logged_in_cookie' ), 10, 5 );
		add_action( 'wp_login', array( $this, 'wp_login' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'wp_logout' ) );
	}

	public function set_logged_in_cookie( $logged_in_cookie, $expire, $expiration, $user_id, $scheme ) {
		$this->expire     = $expire;
		$this->expiration = $expiration;
	}

	public function wp_login( $user_login, $user ) {
		$value     = wp_generate_auth_cookie( $user->ID, $this->expiration, 'logged_in' );
		// $secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		$secure  = apply_filters( 'secure_logged_in_cookie', false, get_current_user_id(), is_ssl() );
		setcookie( BLMD_LOGIN_EXTENSIONS_COOKIE_NAME . COOKIEHASH, $value, $this->expire, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, true );
		setcookie( BLMD_LOGIN_EXTENSIONS_COOKIE_NAME . COOKIEHASH, $value, $this->expire, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN, $secure, true );
		// setcookie(BLMD_LOGIN_EXTENSIONS_COOKIE_NAME . COOKIEHASH, $value, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true);
	}

	public function wp_logout() {
		setcookie( BLMD_LOGIN_EXTENSIONS_COOKIE_NAME . COOKIEHASH, ' ', time() - YEAR_IN_SECONDS, ADMIN_COOKIE_PATH, COOKIE_DOMAIN );
		setcookie( BLMD_LOGIN_EXTENSIONS_COOKIE_NAME . COOKIEHASH, ' ', time() - YEAR_IN_SECONDS, PLUGINS_COOKIE_PATH, COOKIE_DOMAIN );
		// setcookie(BLMD_LOGIN_EXTENSIONS_COOKIE_NAME . COOKIEHASH, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}

	public function __construct() { }

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'core-plugin' ), '0.1' );
	}

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'core-plugin' ), '0.1' );
	}

};

function BLMD_Login_Extensions() {
	return BLMD_Login_Extensions::factory();
}

BLMD_Login_Extensions();
