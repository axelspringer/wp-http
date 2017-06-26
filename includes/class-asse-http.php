<?php

class AsseHttp {

	const VERSION                 = ASSE_HTTP_VERSION;
  const PLUGIN_NAME             = ASSE_HTTP_PLUGIN_NAME;

  const CACHE_CONTROL_HEADERS   = [
    'max-age',
    's-maxage',
    'min-fresh',
    'must-revalidate',
    'no-cache',
    'no-store',
    'no-transform',
    'public',
    'private',
    'proxy-revalidate',
    'stale-while-revalidate',
    'stale-if-error'
  ];

  const CACHE_CONTROL_DEFAULTS  = [
    'front_page'   => [
        'max-age'  => 300,           //                5 min
        's-maxage' => 150,            //                2 min 30 sec
        'public'   => true,
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'single'      => [
        'max-age'  => 600,           //               10 min
        's-maxage' => 60,            //                1 min
        'mmulti'   => 1,              // enabled,
        'public'   => true,
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'page'        => [
        'max-age'  => 1200,          //               20 min
        's-maxage' => 300,            //                5 min
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'home'         => [
        'max-age'  => 180,           //                3 min
        's-maxage' => 45,            //                      45 sec
        'paged'    => 5,              //                       5 sec
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'category'   => [
        'max-age'  => 900,           //               15 min
        's-maxage' => 300,           //                5 min
        'paged'    => 8,              //                       8 sec
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'tag'         => [
        'max-age'  => 900,           //               15 min
        's-maxage' => 300,           //                5 min            //                       8 sec
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'author'      => [
        'max-age'  => 1800,          //               30 min
        's-maxage' => 600,           //               10 min
        'paged'    => 10,             //                      10 sec
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'date'        =>  [
        'max-age'  => 10800,         //      3 hours
        's-maxage' => 2700,          //               45 min
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'feed'        => [
        'max-age'  => 5400,          //       1 hours 30 min
        's-maxage' => 600,            //               10 min
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'attachment'   => [
        'max-age'  => 10800,         //       3 hours
        's-maxage' => 2700,          //               45 min
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    'search'       => [
        'max-age'  => 1800,          //               30 min
        's-maxage' => 600,            //               10 min
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ],
    '404'     => [
        'max-age'  => 900,           //               15 min
        's-maxage' => 300,            //                5 min
        'stale-while-revalidate' => 3600 * 24,
        'stale-if-error' => 3600 * 24 * 3
    ]
  ];

	protected $settings;
	protected $options;

	public function __construct() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( ! is_plugin_active( 'asse-http/asse-http.php' ) ) {
			return;
		}

		$this->maybe_update();

		$this->settings       = new AsseHttpSettings( self::PLUGIN_NAME );
		$this->options        = $this->get_options();

		$this->register_hooks();
	}

	public function register_hooks() {
		add_action( 'wp', array( &$this, 'send_cache_control_headers' ), 0 );
    add_action( 'template_redirect', array( &$this, 'add_http_headers' ) );
	}

  public function cache_control_directives() {
    global $wp_query;

    $directives = null;

    if ( ! $this->should_cache() ) {
      $directives = $this->cache_control_directive( null );
    }

    if ( $wp_query->is_front_page() && ! is_paged() ) {
        $directives = $this->cache_control_directive( 'front_page' );
    } elseif ( $wp_query->is_single() ) {
        $directives = $this->cache_control_directive( 'single' );
    } elseif ( $wp_query->is_page() ) {
        $directives = $this->cache_control_directive( 'page' );
    } elseif ( $wp_query->is_home() ) {
        $directives = $this->cache_control_directive( 'home' );
    } elseif ( $wp_query->is_category() ) {
        $directives = $this->cache_control_directive( 'category' );
    } elseif ( $wp_query->is_tag() ) {
        $directives = $this->cache_control_directive( 'tag' );
    } elseif ( $wp_query->is_author() ) {
        $directives = $this->cache_control_directive( 'author' );
    } elseif ( $wp_query->is_attachment() ) {
        $directives = $this->cache_control_directive( 'attachement' );
    } elseif ( $wp_query->is_search() ) {
        $directives = $this->cache_control_directive( 'search' );
    } elseif ( $wp_query->is_404() ) {
        $directives = $this->cache_control_directive( '404' );
    } elseif ( $wp_query->is_date() ) {
        if ( ( is_year() && strcmp(get_the_time('Y'), date('Y')) < 0 ) ||
          ( is_month() && strcmp(get_the_time('Y-m'), date('Y-m')) < 0 ) ||
          ( ( is_day() || is_time() ) && strcmp(get_the_time('Y-m-d'), date('Y-m-d')) < 0 ) ) {
            $directives = $this->cache_control_directive( 'date' );
        } else {
            $directives = $this->cache_control_directive( 'home' );
        }
    }

    return apply_filters( 'asse_http_cache_control_directives', $directives);
  }

  public function cache_control_directive( $default ) {
    if ( empty( $default ) || ! array_key_exists( $default, self::CACHE_CONTROL_DEFAULTS ) ) {
        return 'no-cache, no-store, must-revalidate';
    }

    $default = array_intersect_key( self::CACHE_CONTROL_DEFAULTS[ $default ], array_flip( self::CACHE_CONTROL_HEADERS ) );
    $directives = [];

    foreach( $default as $key => $value ) {
        $directives[] = is_bool( $value ) ? $key : $key . '=' . $value;
    }

    return implode( ', ', $directives );
  }

  public function add_http_headers() {
    global $wp_query;

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    } elseif( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
        return;
    } elseif( defined('REST_REQUEST') && REST_REQUEST ) {
        return;
    } elseif ( is_admin() ) {
        return;
    }

    $this->options = apply_filters( 'asse_http_add_headers', $this->options );

    if ( $wp_query->is_singular() ) {
       $this->send_headers_for_object();
    }

    return;
  }

  public function send_http_headers( $post, $mtime ) {
    global $wp_query;

    if ( ! $wp_query->is_singular() ) {
      return;
    }

    $headers = [];
    $supported_headers = [
      'ETag',
      'Last-Modified',
      'Expires',
      'Cache-Control',
      'Pragma'
    ];

    if ( $this->options['add_etag'] ) {
      $headers['ETag'] = $this->get_etag_header( $post, $mtime );
    }

    if ( $this->options['add_last_modified'] ) {
      $headers['Last-Modified'] = $this->get_last_modified_header( $post, $mtime );
    }

    if ( $this->options['add_expires'] ) {
      $headers['Expires'] = $this->get_expires_header( $post, $mtime );
    }

    if ( $this->options['add_backwards_cache_control'] ) {
      $headers['Pragma'] = $this->get_pragma_header( $post, $mtime );
    }

    $headers = apply_filters( 'asse_http_add_headers_send', $headers );

    if ( headers_sent() ) {
      // should error?!
      return;
    }

    // if ( true === $tho['remove_pre_existing_headers'] ) {
    //     // should do something ;)
    // }

    foreach( $headers as $key => $value ) {
      header( sprintf('%s: %s', $key, $value) );
    }

    if (  )

    if ( $this->options['add_etag'] &&
      isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {
        if ( $headers['ETag'] !== stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) {
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

  public function send_headers_for_object() {
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

    $this->send_http_headers( $post, $mtime );
  }

  public function send_headers_for_archive() {
    global $posts;

    if ( empty($posts) ) {
        return;
    }
    $post = $posts[0];

    if ( ! is_object($post) || ! isset($post->post_type) ) {
        return;
    }

    $post_mtime = $post->post_modified_gmt;
    $mtime = strtotime( $post_mtime );

    $this->send_http_headers( $post, $mtime );
  }

  public function get_last_modified_header( $post, $mtime ) {
    return str_replace( '+0000', 'GMT', gmdate('r', $mtime) );
  }

  public function get_expires_header( $post, $mtime ) {
    return str_replace( '+0000', 'GMT', gmdate('r', time() + $this->options['expires_max_age'] ) );
  }

  public function get_pragma_header( $post, $mtime ) {
    if ( intval($this->options['cache_max_age_seconds']) > 0 ) {
      return 'public';
    };
    return 'no-cache';
  }

  public function get_etag_header( $post, $mtime ) {
    global $wp;

    $to_hash  = array( $mtime, $post->post_date_gmt, $post->guid, $post->ID, serialize( $wp->query_vars ), self::VERSION );
    $etag     = hash( 'crc32b', serialize( $to_hash ) );

    if ( $this->options['generate_weak_etag'] ) {
      return sprintf( 'W/"%s"', $etag );
    }

    return sprintf( '"%s"', $etag );
  }

  public function send_cache_control_headers() {
    if ( is_admin() ) {
      return;
    }

    if ( $wp_env = getenv('WP_LAYER') ) {
      if ( $wp_env !== 'frontend' ) {
        return;
      }
    }

    if ( ! isset( $this->options['send_cache_control_headers'] )
      || ! $this->options['send_cache_control_headers'] ) {
      return;
    }

    $directives = $this->cache_control_directives();

    if ( ! empty( $directives ) ) {
      header ( 'Cache-Control: ' . $directives , true );
    }
  }

  public function should_cache() {
    return ! ( is_preview() || is_user_logged_in() || is_trackback() || is_admin() );
  }

	public function maybe_update() {
    $option  = self::PLUGIN_NAME . '_version';
		$version = get_option( $option );

		if ( false === $version ) {
      // something to update
		}

		update_option( $option, AsseHttp::VERSION );
	}

  public function get_options() {
    $options = array(
      'send_cache_control_headers'    => get_option( 'asse_http_send_cache_control_headers' ),
      'add_etag'                      => get_option( 'asse_http_add_etag' ),
      'generate_weak_etag'            => get_option( 'asse_http_generate_weak_etag' ),
      'add_last_modified'             => get_option( 'asse_http_add_last_modified' ),
      'add_expires'                   => get_option( 'asse_http_add_expires' ),
      'add_backwards_cache_control'   => get_option( 'asse_http_add_backwards_cache_control' ),
      'expires_max_age'               => get_option( 'asse_http_expires_max_age' )
    );

    return $options;
  }

	public static function activate() {
    $option  = self::PLUGIN_NAME . '_version';
		add_option( $option, AsseHttp::VERSION );
	}

	public static function deactivate() {

	}

}
