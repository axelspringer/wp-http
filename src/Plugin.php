<?php

namespace Asse\Plugin;

use \Asse\Settings\Page;
use \Asse\Settings\Notice;
use \Asse\Plugin\Http\MobileDetectUA;
use \Asse\Plugin\Http\MobileDetectCloudfront;
use \Asse\Plugin\Http\MobileDetectAkamai;
use \Asse\Plugin\Http\CDN;
use \Asse\Plugin\Http\Legacy;
use \Asse\Plugin\Http\Code;
use \Asse\Plugin\Http\Header;
use \Asse\Plugin\Http\Encoding;
use \Asse\Plugin\Http\Defaults;
use \Asse\Plugin\AbstractPlugin;
use \Asse\Plugin\Http\Settings;

class Http extends AbstractPlugin {

  protected $headers;
  protected $encodings;
  protected $settings;

  private $query_var;
  private $rewrite_rule;

  public function init() {
    // include for plugin detection
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    // if plugin not active, return
		if ( ! is_plugin_active( $this->config->basename ) ) {
			return false;
		}

    class_exists( '\Asse\Plugin\WPHelper' ) || exit;

    $this->headers        = array();
    $this->encodings      = $this->get_encodings();

    $this->settings = new Settings(
      __( 'ASSE HTTP', 'asse-http' ),
      __( 'HTTP', 'asse-http' ),
      $this->config->name . '_setting_page',
      'manage_options',
      $this->config->version,
      $this->options
    );

    $this->mobile_detect();
  }

  /**
   * Register Hooks
   *
   * @return void
   */
	public function register_hooks() {
	  add_action( 'admin_init',			  array( &$this, 'register_settings' ) );

    // header
		add_action( 'wp', array( &$this, 'send_cache_control_header' ) );
    add_action( 'template_redirect', array( &$this, 'try_rewrite_categories' ) );
    add_action( 'template_redirect', array( &$this, 'try_catch_404' ) );
    add_action( 'template_redirect', array( &$this, 'send_extra_headers' ) );
    add_action( 'template_redirect', array( &$this, 'send_http_304' ) );

    // replace urls
    if ( $this->options['replace_urls']
      && count( $this->options['replace_urls'] ) > 0 ) {
      add_action( 'template_redirect', array( &$this, 'start_ob_replace_urls' ), 98 );
      add_action( 'shutdown', array( &$this, 'end_ob_flush' ), 98 );
    }

    // compression
    while( list( , $encoding ) = each( $this->encodings ) ) {
      if ( $this->options[$encoding] ) {
        add_action( 'template_redirect', array( &$this, 'start_ob_' . $encoding ), 97 );
        add_action( 'shutdown', array( &$this, 'end_ob_flush' ), 97 );
        break; // have found encoding
      }
    }

    // healthz check
    if ( $this->options['health'] ) {
      $can_url            = ltrim( $this->options['health_url'], '/' );
      $this->query_var    = $can_url;
      $this->rewrite_rule = $can_url . '/?$';

      add_filter( 'query_vars', array( &$this, 'add_query_vars' ) );
      add_filter( 'redirect_canonical', array( &$this, 'prevent_redirect_canonical' ) );

      add_action( 'init', array( &$this, 'rewrites_init' ) );
      add_action( 'template_redirect', array( &$this, 'send_health' ), 0 );
    }
	}

  /**
   * Undocumented function
   *
   * @param [type] $redirect_url
   * @return void
   */
  public function prevent_redirect_canonical( $redirect_url ) {
    if ( strpos( $redirect_url, $this->url ) ) {
      return false;
    }
    return $redirect_url;
  }

  /**
   * Undocumented function
   *
   * @param [type] $vars
   * @return void
   */
  public function add_query_vars( $vars ) {
    $vars[] = $this->query_var;
    return $vars;
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function rewrites_init() {
    add_rewrite_rule(
      $this->rewrite_rule,
      'index.php?' . $this->query_var . '=true',
      'top'
    );

    $rules  = get_option( 'rewrite_rules' );
    if ( ! isset( $rules[$this->rewrite_rule] ) ) {
      global $wp_rewrite;
      $wp_rewrite->flush_rules();
    }
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function send_health() {

    $is_health = get_query_var( $this->query_var, false );
    if ( true != $is_health ) {
      return;
    }

    header_remove();
    ob_start();

    load_template( get_template_directory() . '/single.php' );
    $buffer = ob_get_contents();
    ob_end_clean();

    if ( count( wp_load_alloptions() ) === 0 ||
      empty( $buffer ) ) {
      $this->send_http_header( Legacy::HTTP503, null, true, Code::HTTP503 );
      exit;
    }

    exit;
  }

  /**
   * Try to rewrite categories
   *
   * This functions tries to rewrite articles to its default category.
   *
   * @return void
   */
  public function try_rewrite_categories() {
    global $wp_query;
    global $wp;

    $wp_queried_object = get_queried_object();

    if ( ! $this->options['try_rewrite_categories']
      || ! ( isset ( $wp->query_vars['category_name'] )
      && isset( $wp->query_vars['name'] ) ) ) {
      return;
    }

    if ( ! $wp_query->is_single() ) {
      return;
    }

    $permalink      = get_permalink( $wp_queried_object->ID );
    $wp_url         = wp_parse_url( $permalink );
    $wp_url_pattern = '/^\/' . preg_quote( $wp->query_vars['category_name'], '/' ) . '/';

    if ( ! preg_match( $wp_url_pattern, $wp_url['path'] ) ) {
      $this->send_http_header( Header::Location, $permalink, true, 301 );

      exit();
    }
  }

  /**
   * Try to catch 404 of not found singles
   *
   * @return void
   */
  public function try_catch_404() {
    global $wp_query;
    global $wpdb;
    global $wp;

    if ( ! $this->options['try_catch_404'] ) {
      return;
    }

    if ( $wp_query->is_404()
      && isset( $wp->query_vars['name'] ) ) { // detect queries
      $results = $wpdb->get_results( $wpdb->prepare(
        "
        SELECT ID
        FROM $wpdb->posts
        WHERE post_type IN ( 'page', 'post', 'attachment' )
        AND post_name = %s
        ", $wpdb->esc_like( $wp->query_vars['name'] )
      ), OBJECT );

      // exit if not unique
      if ( count( $results ) !== 1 ) {
        return;
      }

      $post = current( $results );
      $this->send_http_header( Header::Location, get_permalink( $post->ID ), true, 301 );

      exit();
    }
  }

  /**
   * Send Cache-Control Header
   *
   * @return void
   */
  public function send_cache_control_header() {

    if ( headers_sent() ) {
      return;
    }

    if ( ! isset( $this->options['send_cache_control_header'] )
      || ! $this->options['send_cache_control_header'] ) {
      return;
    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
      return;
    } elseif( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
      return;
    } elseif( defined('REST_REQUEST') && REST_REQUEST ) {
      return;
    } elseif ( is_admin() ) {
      return;
    }

    $directives = $this->get_cache_control_directives();

    if ( ! empty( $directives ) ) {
      $this->send_http_header( Header::CacheControl, $directives, true );
    }
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function get_cache_control_directives() {
    global $wp_query;

    $directives   = null;

    if ( ! ( is_preview()
      || is_user_logged_in()
      || is_trackback()
      || is_admin() ) ) {
      $directives = Http::get_cache_control_directive( null );
    }

    if ( $wp_query->is_front_page() && ! is_paged() ) {
        $directives = Http::get_cache_control_directive( 'front_page' );
    } elseif ( $wp_query->is_single() ) {
        $directives = Http::get_cache_control_directive( 'single' );
    } elseif ( $wp_query->is_page() ) {
        $directives = Http::get_cache_control_directive( 'page' );
    } elseif ( $wp_query->is_home() ) {
        $directives = Http::get_cache_control_directive( 'home' );
    } elseif ( $wp_query->is_category() ) {
        $directives = Http::get_cache_control_directive( 'category' );
    } elseif ( $wp_query->is_tag() ) {
        $directives = Http::get_cache_control_directive( 'tag' );
    } elseif ( $wp_query->is_author() ) {
        $directives = Http::get_cache_control_directive( 'author' );
    } elseif ( $wp_query->is_attachment() ) {
        $directives = Http::get_cache_control_directive( 'attachement' );
    } elseif ( $wp_query->is_search() ) {
        $directives = Http::get_cache_control_directive( 'search' );
    } elseif ( $wp_query->is_404() ) {
        $directives = Http::get_cache_control_directive( '404' );
    } elseif ( $wp_query->is_date() ) {
      if ( ( is_year() && strcmp(get_the_time('Y'), date('Y')) < 0 ) ||
        ( is_month() && strcmp(get_the_time('Y-m'), date('Y-m')) < 0 ) ||
        ( ( is_day() || is_time() ) && strcmp(get_the_time('Y-m-d'), date('Y-m-d')) < 0 ) ) {
          $directives = Http::get_cache_control_directive( 'date' );
      } else {
          $directives = Http::get_cache_control_directive( 'home' );
      }
    }

    return apply_filters( 'asse_http_get_cache_control_directives', $directives);
  }

  /**
   * Cache-Control directives
   *
   * @param [type] $cache_default
   * @return void
   */
  public static function get_cache_control_directive( $cache_default ) {
    if ( empty( $cache_default ) || ! @array_key_exists( $cache_default, Defaults::CacheControl ) ) {
      return 'no-cache, no-store, must-revalidate';
    }

    $cache_default = array_intersect_key( Defaults::CacheControl[ $cache_default ], array_flip( Defaults::AllowedCacheControllHeaders ) );
    $directives = [];

    foreach( $cache_default as $key => $value ) {
        $directives[] = is_bool( $value ) ? $key : $key . '=' . $value;
    }

    return implode( ', ', $directives );
  }


  /**
   * Send extra headers
   *
   * @return void
   */
  public function send_extra_headers() {
    global $wp_query;

    if ( headers_sent() ) {
      return;
    }

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
      return;
    } elseif( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
      return;
    } elseif( defined('REST_REQUEST') && REST_REQUEST ) {
      return;
    } elseif ( is_admin() ) {
      return;
    }

    if ( $wp_query->is_singular() ) {
       $this->send_extra_headers_for_object();
    }

    return;
  }

  /**
   * Send extra HTTP Headers
   *
   * @param [type] $post
   * @param [type] $mtime
   * @return void
   */
  public function send_extra_http_headers( $post, $mtime ) {
    global $wp_query;

    if ( ! $wp_query->is_singular() ) {
      return;
    }

    if ( $this->options['add_etag'] ) {
      $this->headers[Header::ETag]  = Http::etag( $post, $mtime, $this->options['generate_weak_etag'], $this->options['etag_salt'] );
    }

    if ( $this->options['add_last_modified'] ) {
      $this->headers[Header::LastModified] = Http::last_modified( $mtime );
    }

    if ( $this->options['add_expires'] ) {
      $this->headers[Header::Expires] = Http::expires( $this->options['expires_max_age'] );
    }

    if ( $this->options['add_backwards_cache_control'] ) {
      $this->headers[Header::Pragma] = Http::pragma( $this->options['expires_max_age'] );
    }

    $this->headers = apply_filters( 'asse_http_send_extra_headers', $this->headers );

    foreach( $this->headers as $directive => $value ) {
      $this->send_http_header( $directive, $value, true );
    }
  }

  /**
   * Send extra HTTP Headers for object
   *
   * @return void
   */
  public function send_extra_headers_for_object() {
    $post = get_queried_object();

    if ( ! is_object( $post) || ! isset( $post->post_type ) ) {
      return;
    }

    // should check for post types
    if ( post_password_required() ) {
      return;
    }

    $post_mtime = $post->post_modified_gmt;
    $post_mtime_unix = strtotime( $post_mtime );

    $mtime = $post_mtime_unix;

    $this->send_extra_http_headers( $post, $mtime );
  }

  /**
   * Get HTTP Last-Modified
   *
   * @param [type] $mtime
   * @return void
   */
  public static function last_modified( $mtime ) {
    return str_replace( '+0000', 'GMT', gmdate('r', $mtime) );
  }

  /**
   * Get HTTP Expires
   *
   * @param [type] $max_age
   * @return void
   */
  public static function expires( $max_age ) {
    return str_replace( '+0000', 'GMT', gmdate('r', time() + $max_age ) );
  }

  /**
   * Get HTTP Pragma
   *
   * @param [type] $max_age
   * @return void
   */
  public static function pragma( $max_age ) {
    if ( intval( $max_age ) > 0 ) {
      return 'public';
    };
    return 'no-cache';
  }

  /**
   * Get HTTP ETag
   *
   * @param [type] $post
   * @param [type] $mtime
   * @param [type] $weak_etag
   * @return void
   */
  public static function etag( $post, $mtime, $weak_etag, $salt = '' ) {
    global $wp;

    $to_hash    = array( $mtime, $post->post_date_gmt, $post->guid, $post->ID, serialize( $wp->query_vars ), $salt );
    $hash       = hash( 'crc32b', serialize( $to_hash ) );

    if ( (bool) $weak_etag ) {
      return sprintf( 'W/"%s-%s"', $hash, $post->guid );
    }

    return sprintf( '"%s-%s"', $hash, $post->guid );
  }

  /**
   * Send HTTP 403
   *
   * @return void
   */
  public function send_http_304() {
    if ( $this->options['add_etag'] &&
      isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {

      if ( $this->headers[Header::ETag] !== stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {
        return;
      }

      if ( false === http_response_code() ) {
        http_response_code( Code::HTTP304 );
      } else {
        header( Legacy::HTTP304 );
      }

      exit;
    }

    if ( $this->options['add_last_modified'] &&
      isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) {

      if ( ! ( strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) >= $mtime ) ) {
        return;
      }

      if ( false === http_response_code() ) {
        http_response_code( Code::HTTP304 );
      } else {
        header( Legacy::HTTP304 );
      }

      exit;
    }
  }

  /**
   * Undocumented function
   *
   * @param [type] $header
   * @param [type] $content
   * @param boolean $replace
   * @param [type] $response_code
   * @return void
   */
  public function send_http_header( $header, $content = null, $replace = false, $response_code = null ) {
    if ( ! empty( $content ) ) {
      $header = $header . ': ' . $content;
    }
    $header = apply_filters( 'asse_http_send_http_header', $header );
    header( $header, $replace, $response_code );
  }

  /**
   * Get accepted encoding
   *
   * @return void
   */
  public function get_encodings() {
    if ( ! isset( $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) {
      return array();
    }

    return array_intersect( Defaults::AcceptedEncoding, array_filter( array_map( 'trim' , explode( ',', $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) ) );
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function mobile_detect() {
    // if not mobile detect is enabled
    if ( ! $this->options['mobile_detect'] ) {
      return;
    }

    // make default detection
    $ua = new MobileDetectUA();

    // specific detection for CDN's
    if ( $this->options['cdn'] == CDN::Cloudfront ) {
      $ua = new MobileDetectCloudfront();
    } elseif ( $this->options['cdn'] == CDN::Akamai ) {
      $ua = new MobileDetectAkamai();
    }

    $ua->set_header();
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function start_ob_replace_urls() {
     ob_start( array( &$this, 'ob_replace_urls_handler' ) );
  }

  /**
   * Undocumented function
   *
   * @param [type] $buffer
   * @param [type] $args
   * @return void
   */
  public function ob_replace_urls_handler( $buffer, $args  ) {
    $origin = $this->options['origin'];

    if ( defined( 'HTTP_ORIGIN' )
      && filter_var( HTTP_ORIGIN, FILTER_VALIDATE_URL) !== false ) {
      $origin = HTTP_ORIGIN;
    }

    if ( empty( $origin ) ) {
      return $buffer;
    }

    return str_replace( $this->options['replace_urls'], $origin, $buffer );
  }

  /**
   * Brotli start
   *
   * @return void
   */
  public function start_ob_br() {
    ob_start( array( &$this, 'ob_br_handler' ) );
  }

  /**
   * Brotli handler
   *
   * @return void
   */
  public function ob_br_handler( $buffer, $args ) {
    $this->send_http_header( Header::ContentEncoding, Encoding::Brotli );
    return brotli_compress( $buffer, Defaults::BrotliCompressionLevel );
  }

  /**
   * Gzip start
   *
   * @return void
   */
  public function start_ob_gzip() {
    ob_start( 'ob_gzhandler' );
  }

  /**
   * Deflate
   *
   * @return void
   */
  public function start_ob_deflate() {
    ob_start( array( &$this, 'ob_deflate_handler' ) );
  }

  /**
   * Deflate handler
   *
   * @return void
   */
  public function ob_deflate_handler( $buffer, $args ) {
    $this->send_http_header( Header::ContentEncoding, Encoding::Deflate );
    return gzcompress( $buffer, Defaults::ZLibCompressionLevel );
  }

  /**
   * Gzip end
   *
   * @return void
   */
  public function end_ob_flush() {
    if ( ob_get_level() > 0 ) {
      ob_end_flush();
    }
  }

  /**
   * Get options
   *
   * @return array
   */
  public function set_options() {
    $options = array(
      'add_backwards_cache_control'   => get_option( 'asse_http_add_backwards_cache_control' ),
      'add_etag'                      => get_option( 'asse_http_add_etag' ),
      'add_expires'                   => get_option( 'asse_http_add_expires' ),
      'add_last_modified'             => get_option( 'asse_http_add_last_modified' ),
      'br'                            => get_option( 'asse_http_br' ),
      'cdn'                           => get_option( 'asse_http_cdn' ),
      'deflate'                       => get_option( 'asse_http_deflate' ),
      'etag_salt'                     => get_option( 'asse_http_etag_salt' ),
      'expires_max_age'               => get_option( 'asse_http_expires_max_age' ),
      'generate_weak_etag'            => get_option( 'asse_http_generate_weak_etag' ),
      'gzip'                          => get_option( 'asse_http_gzip' ),
      'mobile_detect'                 => get_option( 'asse_http_mobile_detect' ),
      'send_cache_control_header'     => get_option( 'asse_http_send_cache_control_header' ),
      'try_catch_404'                 => get_option( 'asse_http_try_catch_404' ),
      'try_rewrite_categories'        => get_option( 'asse_http_try_rewrite_categories' ),
      'replace_urls'                  => get_option( 'asse_http_replace_urls' ),
      'origin'                        => get_option( 'asse_http_origin' ),
      'health'                        => get_option( 'asse_http_health' ),
      'health_url'                    => get_option( 'asse_http_health_url' )
    );

    $this->options = $options;
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  protected function version_migrate() {

  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function enqueue_admin_scripts() {

  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function register_settings() {
    $this->settings->register();
  }

  /**
   * Activate plugin
   *
   * @return void
   */
	public static function activate() {
    class_exists( '\Asse\Plugin\WPHelper' ) || die( '\'\Asse\Plugin\WPHelper\' required.' );

    return;
	}

  /**
   * Deactivate plugin
   *
   * @return void
   */
	public static function deactivate() {
    return;
	}

}
