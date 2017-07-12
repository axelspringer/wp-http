<?php

namespace Asse\Plugin\Http;

class Settings {

  protected $plugin_title;
  protected $plugin_menu_title;
  protected $plugin_permission;
  protected $plugin_slug;
  protected $hook_suffix;

  public function __construct( $slug ){

    $this->plugin_slug = $slug;

    $this->init();

	  add_action(	'admin_menu',			  array( &$this, 'add_admin_menu' ) );
	  add_action( 'admin_init',			  array( &$this, 'register_settings' ) );
	  add_action( 'admin_notices', 		  array( &$this, 'theme_settings_admin_notices' ) );
    add_action( 'admin_enqueue_scripts',  array( &$this, 'enqueue_admin_scripts' ) );
  }

  private function init() {
    $this->plugin_title       = __( 'ASSE HTTP', 'asse-http' );
	  $this->plugin_menu_title  = __( 'HTTP', 'asse-http' );
    $this->plugin_slug        = $this->plugin_slug . '_settings_page';

    $this->plugin_permission  = 'manage_options';
  }

	public function register_settings() {

    // Basic Settings
		$args = array(
			'id'			    => 'asse_http_basic',
			'title'			  => 'Grundeinstellungen',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_http_basic = new \Asse\Settings\Section( $args );

    $args = array(
			'id'				    => 'asse_http_send_cache_control_header',
			'title'				  => 'Cache-Control Headers',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_send_cache_control_header = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_etag',
			'title'				  => 'ETag',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_add_etag = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_generate_weak_etag',
			'title'				  => 'Weak ETag',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_generate_weak_etag = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_last_modified',
			'title'				  => 'Modified Header',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_add_last_modified = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_expires',
			'title'				  => 'Expries Header',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_add_expires = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_backwards_cache_control',
			'title'				  => 'Legacy Cache Control',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_add_backwards_cache_control = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_mobile_detect',
			'title'				  => 'Mobile Geräteerkennung',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_mobile_detect = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_expires_max_age',
			'title'				  => 'Expires Max-Age',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_basic',
			'description'   => 'Sekunden',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_expires_max_age = new \Asse\Settings\Field( $args );

    // Compression
		$args = array(
			'id'			    => 'asse_http_compression',
			'title'			  => 'Kompression',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_http_compression = new \Asse\Settings\Section( $args );

    $args = array(
			'id'				    => 'asse_http_gzip',
			'title'				  => 'GZip',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_compression',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_gzip = new \Asse\Settings\Field( $args );

	  $args = array(
			'id'				    => 'asse_http_br',
			'title'				  => 'Brotli',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_compression',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_br = new \Asse\Settings\Field( $args );

	  $args = array(
			'id'				    => 'asse_http_deflate',
			'title'				  => 'Deflate (Zlib)',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_compression',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_deflate = new \Asse\Settings\Field( $args );

    // CDN
		$args = array(
			'id'			    => 'asse_http_cdn',
			'title'			  => 'CDN',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_http_cdn = new \Asse\Settings\Section( $args );

    $args = array(
			'id'				    => 'asse_http_cdn',
			'title'				  => 'CDN',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_cdn',
			'description'   => '',
			'type'				  => 'dropdown', // text, textarea, password, checkbox, dropbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
      'options'       => array( \Asse\Plugin\Http\CDN::None => 'Keins', \Asse\Plugin\Http\CDN::Akamai => 'Akamai', \Asse\Plugin\Http\CDN::Cloudfront => 'Cloudfront' )
		);
		$asse_http_cdn = new \Asse\Settings\Field( $args );

    // Advanced
		$args = array(
			'id'			    => 'asse_http_experimental',
			'title'			  => 'Experimental',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_http_experimental = new \Asse\Settings\Section( $args );

    $args = array(
			'id'				    => 'asse_http_etag_salt',
			'title'				  => 'ETag Salt',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_experimental',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_etag_salt = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_try_rewrite_categories',
			'title'				  => 'Rewrite Kategorien',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_experimental',
			'description'   => 'Vorsicht!',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_try_rewrite_categories = new \Asse\Settings\Field( $args );

    $args = array(
			'id'				    => 'asse_http_try_catch_404',
			'title'				  => 'Try Catch 404',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_experimental',
			'description'   => 'Vorsicht!',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
		);
		$asse_http_expires_max_age = new \Asse\Settings\Field( $args );
	}

	public function add_admin_menu() {
	  add_options_page( $this->plugin_title, $this->plugin_menu_title, $this->plugin_permission, $this->plugin_slug, array( $this, 'settings_page' ) );
	}

  public function settings_page() {
    \Asse\Settings\Page( $this->slug );
  }

	public function theme_settings_admin_notices() {
    \Asse\Settings\Notice( $this->slug );
  }

}
