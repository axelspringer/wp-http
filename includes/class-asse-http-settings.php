<?php

class AsseHttpSettings {

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
		$asse_http_basic = new AsseHttpSettingsSection( $args );

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
		$asse_http_send_cache_control_header = new AsseHttpSettingsField( $args );

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
		$asse_http_add_etag = new AsseHttpSettingsField( $args );

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
		$asse_http_generate_weak_etag = new AsseHttpSettingsField( $args );

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
		$asse_http_add_last_modified = new AsseHttpSettingsField( $args );

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
		$asse_http_add_expires = new AsseHttpSettingsField( $args );

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
		$asse_http_add_backwards_cache_control = new AsseHttpSettingsField( $args );

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
		$asse_http_mobile_detect = new AsseHttpSettingsField( $args );

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
		$asse_http_expires_max_age = new AsseHttpSettingsField( $args );

    // Compression
		$args = array(
			'id'			    => 'asse_http_compression',
			'title'			  => 'Kompression',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_http_compression = new AsseHttpSettingsSection( $args );

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
		$asse_http_gzip = new AsseHttpSettingsField( $args );

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
		$asse_http_br = new AsseHttpSettingsField( $args );

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
		$asse_http_deflate = new AsseHttpSettingsField( $args );

    // CDN
		$args = array(
			'id'			    => 'asse_http_cdn',
			'title'			  => 'CDN',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_http_cdn = new AsseHttpSettingsSection( $args );

    $args = array(
			'id'				    => 'asse_http_cdn',
			'title'				  => 'CDN',
			'page'				  => $this->plugin_slug,
			'section'			  => 'asse_http_cdn',
			'description'   => '',
			'type'				  => 'dropdown', // text, textarea, password, checkbox, dropbox
			'multi'				  => false,
			'option_group'	=> $this->plugin_slug,
      'options'       => array( AsseHttpCDN::None => 'Keins', AsseHttpCDN::Akamai => 'Akamai', AsseHttpCDN::Cloudfront => 'Cloudfront' )
		);
		$asse_http_cdn = new AsseHttpSettingsField( $args );

    // Advanced
		$args = array(
			'id'			    => 'asse_http_experimental',
			'title'			  => 'Experimental',
			'page'			  => $this->plugin_slug,
			'description'	=> '',
		);
		$asse_http_experimental = new AsseHttpSettingsSection( $args );

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
		$asse_http_etag_salt = new AsseHttpSettingsField( $args );

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
		$asse_http_try_rewrite_categories = new AsseHttpSettingsField( $args );

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
		$asse_http_expires_max_age = new AsseHttpSettingsField( $args );
	}

	public function add_admin_menu() {
		$theme_page = add_options_page( $this->plugin_title, $this->plugin_menu_title, $this->plugin_permission, $this->plugin_slug, array( $this, 'settings_page' ) );
	}

	public function settings_page() {
		?>
		<div class="wrap afbia-settings-page">
			<h2><span class='hidden-xs'><?= esc_html($this->plugin_menu_title) ?></span></h2>
			<form action="options.php" method="post">
			<?php
				global $wp_settings_sections, $wp_settings_fields;
				settings_fields( $this->plugin_slug );
				$page = $this->plugin_slug;
			?>
			<div class="container-fluid settings-container">
				<div class="row container-row">
					<div class="col-xs-12 col-sm-4 col-md-3 navigation-container">
						<ul class="navigation">
						<?php

							if ( isset( $wp_settings_sections[$page] ) ) {
								foreach ( (array) $wp_settings_sections[$page] as $section ) {
									echo '<li class="nav-item">';
										echo '<a href="#'.$section['id'].'">';
											if($section['icon'])
												echo '<i class="fa fa-'.$section['icon'].'"></i> ';

											echo '<span class="hidden-xs">' . $section['title'] . '</span>';

										echo '</a>';
									echo '</li>';
								}
							}

						?>
						</ul>
					</div>
					<div class="col-xs-12 col-sm-8 col-md-9 content-container">
						<?php

							if ( isset( $wp_settings_sections[$page] ) ) {
								foreach ( (array) $wp_settings_sections[$page] as $section ) {
									echo '<div class="section" id="section-'.$section['id'].'">';
									if ( $section['icon'] ) {
										$icon = "<i class='fa fa-{$section['icon']}'></i>";
									} else {
										$icon = null;
									}
									if ( $section['title'] )
										echo "<h2>$icon {$section['title']}</h2>\n";
									if ( $section['callback'] )
										call_user_func( $section['callback'], $section );

									do_action("afb_settings_section_" . $section['id']);

									if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
										echo '</div>';
										continue;
									}
									echo '<table class="form-table">';
										do_settings_fields( $page, $section['id'] );
									echo '</table>';
									echo '
				<p class="submit">
					<input name="Submit" type="submit" class="button-primary" value="'.esc_attr(__('Save Changes')).'" />
				</p>';
									echo '</div>';
								}
							}

						?>
					</div>
				</div>
			</div>
			</form>


			<div class="credits-container">
				<div class="row">
					<div class="col-xs-12">
            Version <?= get_option( 'asse_http_version' ) ?>
					</div>
				</div>
			</div>
		</div><!-- wrap -->
		<?php
	}

  public function enqueue_admin_scripts() {
		wp_register_style( 'asse_http_admin_style', ASSE_HTTP_PLUGIN_URL . 'admin/admin.css', false, get_option('asse_http_version') );
    wp_register_script( 'asse_http_admin_script' , ASSE_HTTP_PLUGIN_URL . 'admin/admin.min.js', array( 'jquery', 'wp-util'), get_option('asse_http_version'), true );

    wp_enqueue_style( 'asse_http_admin_style' );
		wp_enqueue_script( 'asse_http_admin_script' );
	}

	public function theme_settings_admin_notices(){
		if( isset( $_GET['page'] ) && $_GET['page'] !== 'theme_settings' ){
			return;
		}

		if( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] === true){
			add_settings_error( $this->plugin_slug, $this->plugin_slug, 'Erfolgreich aktualisiert.' , 'updated' );
		}

		settings_errors( $this->plugin_slug );
  }

}
