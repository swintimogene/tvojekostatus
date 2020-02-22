<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Instagram
 * @subpackage Woo_Instagram/admin
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Instagram_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->load_dependencies();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-instagram-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-instagram-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function load_dependencies() {
		/**
		 * The class responsible for defining function for display Html element
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/partials/class-woo-instagram-html-output.php';

		/**
		 * The class is responsible for display admin settings
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/partials/woo-instagram-admin-display.php';
	}

	/**
	 * Add a new tab to the product data meta box. Render HTML markup
	 */
	public function render_product_data_tab_markup() {
		echo '<li class="instagram_options instagram_data wc-2-0-x"><a href="#instagram_data">' . __( 'Woo Instagram', 'woo-instagram' ) . '</a></li>';
	}

	/**
	 * Render fields for our newly added tab.
	 */
	public function product_data_tab_markup() {
		?>
		<div id="instagram_data" class="panel woocommerce_options_panel">
			<?php
			// Instagram hashtag.
			woocommerce_wp_text_input( array(
					'id'          => '_woo_instagram_hashtag',
					'class'       => 'short',
					'label'       => __( 'Hash Tag', 'woocommerce-instagram' ),
					'description' => __( 'This is the hashtag for which images will be displayed. If no hashtag is entered, no images will display.', 'woo-instagram' ),
					'desc_tip'    => true,
					'type'        => 'text',
				)
			);
			?>
		</div>
		<br />
		<?php
	}

	/**
	 * Save the hastag fields.
	 */
	public function save_product_data_tab_fields( $post_id ) {
		if ( isset( $_POST['_woo_instagram_hashtag'] ) && '' !== $_POST['_woo_instagram_hashtag'] ) {
			$value = stripslashes( wc_clean( $_POST['_woo_instagram_hashtag'] ) );
			// Strip out spaces.
			// Strip out the #, if it's at the front.
			$value = str_replace( array( ' ', '#' ), array( '', '' ), $value );
			update_post_meta( $post_id, '_woo_instagram_hashtag', $value );
		} else {
			delete_post_meta( $post_id, '_woo_instagram_hashtag' );
		}
	}

	/**
	 * Function For welcome screen
	 *
	 *
	 */
	public function welcome_woocommerce_instagram_product_photos_screen_do_activation_redirect() {

		if ( ! get_transient( '_woocommerce_instagram_product_photos_welcome_screen' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_woocommerce_instagram_product_photos_welcome_screen' );

		// if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}
		// Redirect to extra cost welcome  page
		wp_safe_redirect( add_query_arg( array( 'page' => 'woocommerce-instagram-product-photos&tab=about' ), admin_url( 'index.php' ) ) );
	}

	public function welcome_pages_screen_woocommerce_instagram_product_photos() {
		add_dashboard_page(
			'woocommerce-instagram-product-photos Dashboard', 'Woocommerce Instagram Product Photos Dashboard', 'read', 'woocommerce-instagram-product-photos', array(
				&$this,
				'welcome_screen_content_woocommerce_instagram_product_photos',
			)
		);
	}

	public function welcome_screen_woocommerce_instagram_product_photos_remove_menus() {
		remove_submenu_page( 'index.php', 'woocommerce-instagram-product-photos' );
	}

	public function welcome_screen_content_woocommerce_instagram_product_photos() {
		?>
		<div class="wrap about-wrap">
			<h1 style="font-size: 2.1em;"><?php printf( __( 'Welcome to Woocommerce Instagram Product Photos', 'woo-instagram' ) ); ?></h1>

			<div class="about-text woocommerce-about-text">
				<?php
				$message = '';
				printf( __( '%s Woocommerce Instagram Product Photos displays Instagram photographs of your products, based on a hashtag.', 'woo-instagram' ), $message, $this->version );
				?>
				<img class="version_logo_img" src="<?php echo plugin_dir_url( __FILE__ ) . 'images/woo-instagram.png'; ?>">
			</div>

			<?php
			$setting_tabs_wc = apply_filters( 'woocommerce_save_for_later_setting_tab', array( "about" => "Overview", "other_plugins" => "Checkout our other plugins" ) );
			$current_tab_wc  = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';
			?>
			<h2 id="woo-extra-cost-tab-wrapper" class="nav-tab-wrapper">
				<?php
				foreach ( $setting_tabs_wc as $name => $label ) {
					echo '<a  href="' . home_url( 'wp-admin/index.php?page=woocommerce-instagram-product-photos&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab_wc == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
				}
				?>
			</h2>
			<?php
			foreach ( $setting_tabs_wc as $setting_tabkey_wc => $setting_tabvalue ) {
				switch ( $setting_tabkey_wc ) {
					case $current_tab_wc:
						do_action( 'woocommerce_instagram_product_photos_' . $current_tab_wc );
						break;
				}
			}
			?>
			<hr />
			<div class="return-to-dashboard">
				<a href="<?php echo home_url( '/wp-admin/admin.php?page=instagrampage' ); ?>"><?php _e( 'Go to Woocommerce Instagram Product Photos Settings', 'woo-instagram' ); ?></a>
			</div>
		</div>
		<?php
	}

	public function woocommerce_instagram_product_photos_about() {
		?>
		<div class="changelog">
			</br>
			<style type="text/css">
				p.woocommerce_intragram_overview {
					max-width: 100% !important;
					margin-left: auto;
					margin-right: auto;
					font-size: 15px;
					line-height: 1.5;
				}

				.woocommerce_intragram_content_ul ul li {
					margin-left: 3%;
					list-style: initial;
					line-height: 23px;
				}
			</style>
			<div class="changelog about-integrations">
				<div class="wc-feature feature-section col three-col">
					<div>
						<p class="woocommerce_intragram_overview"><?php _e( 'Bring these images on your website, by integrating Instagram photographs, tagged with a specific hashtag, directly on to product details page.', 'woo-instagram' ); ?></p>

						<p class="woocommerce_intragram_overview"><strong>How to setup:</strong></p>
						<div class="woocommerce_intragram_content_ul">
							<ul>
								<li>Add Client Id, Client Secret and Redirect URI to WooCommerce Instagram Settings Page in your admin panel.</li>
								<li>Add a hashtag to each product you'd like to display Instagram images on.</li>
								<li>You're done!</li>
								<li>Flat Rate Shipping Method For Specific Product SKU.</li>
							</ul>
						</div>

					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function woocommerce_instagram_product_photos_pointers_footer() {

		$admin_pointers = woocommerce_instagram_product_photos_admin_pointers();
		?>
		<script type="text/javascript">
					/* <![CDATA[ */
					(function( $ ) {
			  <?php
			  foreach ($admin_pointers as $pointer => $array) {
			  if ($array['active']) {
			  ?>
						$( '<?php echo esc_attr( $array['anchor_id'] ); ?>' ).pointer( {
							content: '<?php echo esc_attr( $array['content'] ); ?>',
							position: {
								edge: '<?php echo esc_attr( $array['edge'] ); ?>',
								align: '<?php echo esc_attr( $array['align'] ); ?>'
							},
							close: function() {
								$.post( ajaxurl, {
									pointer: '<?php echo esc_attr( $pointer ); ?>',
									action: 'dismiss-wp-pointer'
								} );
							}
						} ).pointer( 'open' );
			  <?php
			  }
			  }
			  ?>
					})( jQuery );
					/* ]]> */
		</script>
		<?php
	}

	public function woocommerce_instagram_template_redirect() {
		if ( isset( $_GET['access_token'] ) ) {
			wp_redirect( home_url( 'wp-admin/admin.php?page=instagrampage' ) );
			exit();
		}
	}
	
	function wi_plugin_row_meta( $links, $file ) {

		if ( strpos( $file, 'woo-instagram.php' ) !== false ) {
			$new_links = array(
				'support' => '<a href="https://www.thedotstore.com/support/" target="_blank">Support</a>',
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}

}

function woocommerce_instagram_product_photos_admin_pointers() {
	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
	$version   = '1_0'; // replace all periods in 1.0 with an underscore
	$prefix    = 'woocommerce_instagram_product_photos_admin_pointers' . $version . '_';

	$new_pointer_content = '<h3>' . __( 'Welcome to Woocommerce Instagram Product Photos' ) . '</h3>';
	$new_pointer_content .= '<p>' . __( 'Woocommerce Instagram Product Photos displays Instagram photographs of your products, based on a hashtag.' ) . '</p>';

	return array(
		$prefix . 'woocommerce_instagram_product_photos_admin_pointers' => array(
			'content'   => $new_pointer_content,
			'anchor_id' => '#toplevel_page_instagrampage',
			'edge'      => 'left',
			'align'     => 'left',
			'active'    => ( ! in_array( $prefix . 'woocommerce_instagram_product_photos_admin_pointers', $dismissed ) ),
		),
	);
}
