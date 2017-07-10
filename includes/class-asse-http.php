<?php

class AsseHttp {

	protected $settings;
	protected $options;
  protected $headers;
  protected $encodings;

  /**
   * Constructor
   */
	public function __construct() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( ! is_plugin_active( 'asse-http/asse-http.php' ) ) {
			return;
		}

		$this->maybe_update();

		$this->settings       = new AsseHttpSettings( ASSE_HTTP_PLUGIN_NAME );
		$this->options        = $this->get_options();
    $this->headers        = array();
    $this->encodings      = $this->get_encodings();

		$this->register_hooks();
	}

  /**
   * Register Hooks
   *
   * @return void
   */
	public function register_hooks() {
		add_action( 'wp', array( &$this, 'send_cache_control_header' ) );
    add_action( 'template_redirect', array( &$this, 'try_rewrite_categories' ) );
    add_action( 'template_redirect', array( &$this, 'try_catch_404' ) );
    add_action( 'template_redirect', array( &$this, 'send_extra_headers' ) );
    add_action( 'template_redirect', array( &$this, 'send_http_403' ) );

    // iterate
    if ( $this->options['brotli'] && in_array( 'br', $this->encodings ) ) {
      add_action( 'template_redirect', array( &$this, 'start_ob_brotli' ), 100 );
      add_action( 'shutdown', array( &$this, 'end_ob_flush' ), 100 );
    } else if ( $this->options['gzip'] && in_array( 'gzip', $this->encodings ) ) {
      add_action( 'template_redirect', array( &$this, 'start_ob_gzip' ), 100 );
      add_action( 'shutdown', array( &$this, 'end_ob_flush' ), 100 );
    } else if ( $this->options['deflate'] && in_array( 'deflate', $this->encodings ) ) {
      add_action( 'template_redirect', array( &$this, 'start_ob_deflate' ), 100 );
      add_action( 'shutdown', array( &$this, 'end_ob_flush' ), 100 );
    }
	}

  /**
   * Try to rewrite urls
   *
   */
  public function try_rewrite_categories() {
    global $wp_query;
    global $wp;

    $wp_queried_object = get_queried_object();

    if ( ! $this->options['try_rewrite_categories']
      || ! ( isset ( $wp->query_vars['category_name'] ) && isset( $wp->query_vars['name'] ) ) ) {
      return;
    }

    if ( ! $wp_query->is_single() ) {
      return;
    }

    $permalink      = get_permalink( $wp_queried_object->ID );
    $wp_url         = wp_parse_url( $permalink );
    $wp_url_pattern = '/^\/' . preg_quote( $wp->query_vars['category_name'], '/' ) . '/';

    if ( ! preg_match( $wp_url_pattern, $wp_url['path'] ) ) {
      $this->send_http_header( 'Location: ' . $permalink, true, 301 );

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
        FROM wp_posts
        WHERE post_type IN ( 'page', 'post', 'attachment' )
        AND post_name = %s
        ", $wpdb->esc_like( $wp->query_vars['name'] )
      ), OBJECT );

      // exit if not unique
      if ( count( $results ) !== 1 ) {
        return;
      }

      $post = current( $results );
      $this->send_http_header( 'Location: ' . get_permalink( $post->ID ), true, 301 );

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
      $this->send_http_header( 'Cache-Control: ' . $directives, true );
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
      $directives = AsseHttp::get_cache_control_directive( null );
    }

    if ( $wp_query->is_front_page() && ! is_paged() ) {
        $directives = AsseHttp::get_cache_control_directive( 'front_page' );
    } elseif ( $wp_query->is_single() ) {
        $directives = AsseHttp::get_cache_control_directive( 'single' );
    } elseif ( $wp_query->is_page() ) {
        $directives = AsseHttp::get_cache_control_directive( 'page' );
    } elseif ( $wp_query->is_home() ) {
        $directives = AsseHttp::get_cache_control_directive( 'home' );
    } elseif ( $wp_query->is_category() ) {
        $directives = AsseHttp::get_cache_control_directive( 'category' );
    } elseif ( $wp_query->is_tag() ) {
        $directives = AsseHttp::get_cache_control_directive( 'tag' );
    } elseif ( $wp_query->is_author() ) {
        $directives = AsseHttp::get_cache_control_directive( 'author' );
    } elseif ( $wp_query->is_attachment() ) {
        $directives = AsseHttp::get_cache_control_directive( 'attachement' );
    } elseif ( $wp_query->is_search() ) {
        $directives = AsseHttp::get_cache_control_directive( 'search' );
    } elseif ( $wp_query->is_404() ) {
        $directives = AsseHttp::get_cache_control_directive( '404' );
    } elseif ( $wp_query->is_date() ) {
      if ( ( is_year() && strcmp(get_the_time('Y'), date('Y')) < 0 ) ||
        ( is_month() && strcmp(get_the_time('Y-m'), date('Y-m')) < 0 ) ||
        ( ( is_day() || is_time() ) && strcmp(get_the_time('Y-m-d'), date('Y-m-d')) < 0 ) ) {
          $directives = AsseHttp::get_cache_control_directive( 'date' );
      } else {
          $directives = AsseHttp::get_cache_control_directive( 'home' );
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
    if ( empty( $cache_default ) || ! @array_key_exists( $cache_default, ASSE_HTTP_CACHE_CONTROL_DEFAULTS ) ) {
      return 'no-cache, no-store, must-revalidate';
    }

    $cache_default = array_intersect_key( ASSE_HTTP_CACHE_CONTROL_DEFAULTS[ $cache_default ], array_flip( ASSE_HTTP_CACHE_CONTROL_HEADERS ) );
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
      $this->headers['ETag']  = AsseHttp::etag( $post, $mtime, $this->options['generate_weak_etag'], $this->options['etag_salt'] );
    }

    if ( $this->options['add_last_modified'] ) {
      $this->headers['Last-Modified'] = AsseHttp::last_modified( $mtime );
    }

    if ( $this->options['add_expires'] ) {
      $this->headers['Expires'] = AsseHttp::expires( $this->options['expires_max_age'] );
    }

    if ( $this->options['add_backwards_cache_control'] ) {
      $this->headers['Pragma'] = AsseHttp::pragma( $this->options['expires_max_age'] );
    }

    $this->headers = apply_filters( 'asse_http_send_extra_headers', $this->headers );

    foreach( $this->headers as $directive => $value ) {
      $this->send_http_header( $directive . ': ' . $value, true );
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

    $to_hash  = array( $mtime, $post->post_date_gmt, $post->guid, $post->ID, serialize( $wp->query_vars ), $salt );
    $etag     = hash( 'crc32b', serialize( $to_hash ) );

    if ( (bool) $weak_etag ) {
      return sprintf( 'W/"%s"', $etag );
    }

    return sprintf( '"%s"', $etag );
  }

  /**
   * Send HTTP 403
   *
   * @return void
   */
  public function send_http_403() {
    if ( $this->options['add_etag'] &&
      isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {

      if ( $this->headers['ETag'] !== stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {
        return;
      }

      if ( false === http_response_code() ) {
        http_response_code( 304 );
      } else {
        header( 'HTTP/1.1 304 Not Modified' );
      }

      exit;
    }

    if ( $this->options['add_last_modified'] &&
      isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) {

      if ( ! ( strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) >= $mtime ) ) {
        return;
      }

      if ( false === http_response_code() ) {
        http_response_code( 304 );
      } else {
        header( 'HTTP/1.1 304 Not Modified' );
      }

      exit;
    }
  }

  /**
   * Send HTTP Header
   *
   * @param [type] $header
   * @param boolean $replace
   * @return void
   */
  public function send_http_header( $header, $replace = false, $response_code = null ) {
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
    
    return array_intersect( ASSE_HTTP_ACCEPT_ENCODING, array_filter( array_map( 'trim' , explode( ',', $_SERVER['HTTP_ACCEPT_ENCODING'] ) ) ) );
  }

  /**
   * Brotli start
   *
   * @return void
   */
  public function start_ob_brotli() {
    ob_start( array( &$this, 'ob_brotli_handler' ) );
  }

  /**
   * Brotli handler
   *
   * @return void
   */
  public function ob_brotli_handler( $buffer, $args ) {
    $this->send_http_header( 'Content-Encoding: br' );
    return brotli_compress( $buffer, ASSE_HTTP_BROTLI_LEVEL );
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
    $this->send_http_header( 'Content-Encoding: deflate' );
    return gzcompress( $buffer, ASSE_HTTP_ZLIB_LEVEL );
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
   * Maybe do some update things
   *
   * @return void
   */
	public function maybe_update() {
    $option  = ASSE_HTTP_PLUGIN_NAME . '_version';
		$version = get_option( $option );

		if ( false === $version ) {
      // something to update
		}

		update_option( $option, ASSE_HTTP_VERSION );
	}

  /**
   * Get options
   *
   * @return array
   */
  public function get_options() {
    $options = array(
      'add_backwards_cache_control'   => get_option( 'asse_http_add_backwards_cache_control' ),
      'add_etag'                      => get_option( 'asse_http_add_etag' ),
      'add_expires'                   => get_option( 'asse_http_add_expires' ),
      'add_last_modified'             => get_option( 'asse_http_add_last_modified' ),
      'etag_salt'                     => get_option( 'asse_http_etag_salt' ),
      'expires_max_age'               => get_option( 'asse_http_expires_max_age' ),
      'generate_weak_etag'            => get_option( 'asse_http_generate_weak_etag' ),
      'gzip'                          => get_option( 'asse_http_gzip' ),
      'brotli'                        => get_option( 'asse_http_brotli' ),
      'deflate'                       => get_option( 'asse_http_deflate' ),
      'send_cache_control_header'     => get_option( 'asse_http_send_cache_control_header' ),
      'try_catch_404'                 => get_option( 'asse_http_try_catch_404' ),
      'try_rewrite_categories'        => get_option( 'asse_http_try_rewrite_categories' )
    );

    return $options;
  }

  /**
   * Activate plugin
   *
   * @return void
   */
	public static function activate() {
    $option  = ASSE_HTTP_PLUGIN_NAME . '_version';
		add_option( $option, ASSE_HTTP_VERSION );
	}

  /**
   * Deactivate plugin
   *
   * @return void
   */
	public static function deactivate() {
    $option  = ASSE_HTTP_PLUGIN_NAME . '_version';
    remove_option( $option, ASSE_HTTP_VERSION );
	}

}
