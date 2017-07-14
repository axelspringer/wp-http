<?php

namespace Asse\Plugin\Http;

use \Asse\Settings\Section;
use \Asse\Settings\Field;
use \Asse\Settings\Config;

final class Settings extends Config  {

  public function register() {
    // Basic Settings
		$args = array(
			'id'			    => 'asse_http_basic',
			'title'			  => 'Grundeinstellungen',
			'page'			  => $this->page,
			'description'	=> '',
		);
		$asse_http_basic = new Section( $args );

    $args = array(
			'id'				    => 'asse_http_send_cache_control_header',
			'title'				  => 'Cache-Control Headers',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page
		);
		$asse_http_send_cache_control_header = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_etag',
			'title'				  => 'ETag',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_add_etag = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_generate_weak_etag',
			'title'				  => 'Weak ETag',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_generate_weak_etag = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_last_modified',
			'title'				  => 'Modified Header',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_add_last_modified = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_expires',
			'title'				  => 'Expries Header',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_add_expires = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_add_backwards_cache_control',
			'title'				  => 'Legacy Cache Control',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_add_backwards_cache_control = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_mobile_detect',
			'title'				  => 'Mobile Geräteerkennung',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_mobile_detect = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_expires_max_age',
			'title'				  => 'Expires Max-Age',
			'page'				  => $this->page,
			'section'			  => 'asse_http_basic',
			'description'   => 'Sekunden',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_expires_max_age = new Field( $args );

    // Compression
		$args = array(
			'id'			    => 'asse_http_compression',
			'title'			  => 'Kompression',
			'page'			  => $this->page,
			'description'	=> '',
		);
		$asse_http_compression = new Section( $args );

    $args = array(
			'id'				    => 'asse_http_gzip',
			'title'				  => 'GZip',
			'page'				  => $this->page,
			'section'			  => 'asse_http_compression',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_gzip = new Field( $args );

	  $args = array(
			'id'				    => 'asse_http_br',
			'title'				  => 'Brotli',
			'page'				  => $this->page,
			'section'			  => 'asse_http_compression',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_br = new Field( $args );

	  $args = array(
			'id'				    => 'asse_http_deflate',
			'title'				  => 'Deflate (Zlib)',
			'page'				  => $this->page,
			'section'			  => 'asse_http_compression',
			'description'   => '',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_deflate = new Field( $args );

    // CDN
		$args = array(
			'id'			    => 'asse_http_cdn',
			'title'			  => 'CDN',
			'page'			  => $this->page,
			'description'	=> '',
		);
		$asse_http_cdn = new Section( $args );

    $args = array(
			'id'				    => 'asse_http_cdn',
			'title'				  => 'CDN',
			'page'				  => $this->page,
			'section'			  => 'asse_http_cdn',
			'description'   => '',
			'type'				  => 'dropdown', // text, textarea, password, checkbox, dropbox
			'multi'				  => false,
			'option_group'	=> $this->page,
      'options'       => array( CDN::None => 'Keins', CDN::Akamai => 'Akamai', CDN::Cloudfront => 'Cloudfront' )
		);
		$asse_http_cdn = new Field( $args );

    // Advanced
		$args = array(
			'id'			    => 'asse_http_experimental',
			'title'			  => 'Experimental',
			'page'			  => $this->page,
			'description'	=> '',
		);
		$asse_http_experimental = new Section( $args );

    $args = array(
			'id'				    => 'asse_http_origin',
			'title'				  => 'Origin',
			'page'				  => $this->page,
			'section'			  => 'asse_http_experimental',
			'description'   => 'Vorsicht! Kann nicht gesetzt werden, wenn HTTP_ORIGIN gesetzt ist',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
      'disabled'      => defined( 'HTTP_ORIGIN' ),
      'disabled_default' => $this->options['origin'],
			'option_group'	=> $this->page,
		);
		$asse_http_origin = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_replace_urls',
			'title'				  => 'Urls ersetzen',
			'page'				  => $this->page,
			'section'			  => 'asse_http_experimental',
			'description'   => 'Vorsicht!',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => true,
			'option_group'	=> $this->page,
		);
		$asse_http_etag_salt = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_etag_salt',
			'title'				  => 'ETag Salt',
			'page'				  => $this->page,
			'section'			  => 'asse_http_experimental',
			'description'   => '',
			'type'				  => 'text', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_etag_salt = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_try_rewrite_categories',
			'title'				  => 'Rewrite Kategorien',
			'page'				  => $this->page,
			'section'			  => 'asse_http_experimental',
			'description'   => 'Vorsicht!',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_try_rewrite_categories = new Field( $args );

    $args = array(
			'id'				    => 'asse_http_try_catch_404',
			'title'				  => 'Try Catch 404',
			'page'				  => $this->page,
			'section'			  => 'asse_http_experimental',
			'description'   => 'Vorsicht!',
			'type'				  => 'checkbox', // text, textarea, password, checkbox
			'multi'				  => false,
			'option_group'	=> $this->page,
		);
		$asse_http_expires_max_age = new Field( $args );
  }
}
