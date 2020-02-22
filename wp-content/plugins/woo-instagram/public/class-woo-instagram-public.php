<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Instagram
 * @subpackage Woo_Instagram/public
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Instagram_Public {

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
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-instagram-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-instagram-public.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Displays images of instagram based on hashtag.
	 */
	public function woo_instagram_display_img() {
		global $post;

		$id                         = $post->ID;
		$woo_instagram_access_token = get_option( 'woo_instagram_access_token', true );
		// $woo_instagram_redirect_uri = get_option('woo_instagram_redirect_uri', true);
		$get_hashtag = get_post_meta( $id, '_woo_instagram_hashtag', true );
		if ( isset( $woo_instagram_access_token, $get_hashtag ) && ! empty( $woo_instagram_access_token ) && ! empty( $get_hashtag ) ) {
			$tag                = esc_attr( $get_hashtag );
			$client_acess_token = $woo_instagram_access_token;
			$url                = 'https://api.instagram.com/v1/tags/' . $tag . '/media/recent?access_token=' . $client_acess_token;
			$inst_stream        = $this->callInstagram( $url );
			$results            = json_decode( $inst_stream, true );

			$limit_images = get_option( 'woo_instagram_limit_images', true );
			$limit_images_count = (int)$limit_images;

//			echo "limit images".$limit_images;
			//Now parse through the $results array to display your results...
			$html = '';
			$html .= '<h1 class="woo_insta_lable">Instagram Media</h1>';
			$html .= '<ul class="products">' . "\n";

			// Loop through the images.
			$count = 0;
			if ( isset( $results['data'] ) && ! empty( $results['data'] ) && is_array( $results['data'] ) ) {

				foreach ( $results['data'] as $item ) {
					$image_link = $item['images']['thumbnail']['url'];
					$class      = 'product instagram';
					$image_url  = $item['link'];

					if ( isset( $limit_images ) && $limit_images > 0 ) {
						if ( $count === $limit_images_count ) {
							break;
						}
					}
					$html .= '<li class="' . esc_attr( $class ) . '">' . '<a href="' . esc_url( $image_url ) . '" target="_blank"><img src="' . esc_url( $image_link ) . '" /></a>' . '</li>' . "\n";
					$count ++;
				}
			} else {
				$html .= '<li> No Result found. </li>' . "\n";
			}

			if ( $count == 0 ) {
				$html .= '<li> You set 0 limit. </li>';
			}

			$html .= '</ul>' . "\n";
			echo $html;
		}
	}

	public function access_token_redirect_script() {
		//$insta_woo_page = site_url('/?woo_insta_uri');
		if ( isset( $_GET['woo_insta_uri'] ) && ! empty( $_GET['woo_insta_uri'] ) ) {
			?>
			<script type="text/javascript">
							function getParameterByName( name ) {
								name = name.replace( /[\[]/, '\\[' ).replace( /[\]]/, '\\]' );
								var regex = new RegExp( '[\\?&]' + name + '=([^&#]*)' ),
									results = regex.exec( location.search );
								return results == null ? '' : decodeURIComponent( results[ 1 ].replace( /\+/g, ' ' ) );
							}

							var code = getParameterByName( 'code' );

							if ( code !== '' ) {

								window.location.replace( '<?= site_url( "/wp-admin/admin.php?page=instagrampage" ); ?>?code=' + code );
							} else {

								var access_token = window.location.hash;
								var woo_insta_uri = getParameterByName( 'woo_insta_uri' );

								var templateUrl = '<?= site_url( "/wp-admin/admin.php?page=instagrampage" ); ?>';

								if ( woo_insta_uri == templateUrl ) {

									window.location.replace( woo_insta_uri + access_token );
								}
							}
			</script>

			<?php
		}
	}

	/**
	 * Curl instagram url.
	 */
	public function callInstagram( $url ) {
		$ch = curl_init();
		curl_setopt_array( $ch, array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2,
		) );
		$result = curl_exec( $ch );
		curl_close( $ch );

		return $result;
	}

	/**
	 * BN code added
	 */
	function paypal_bn_code_filter_woocomerce_intragram( $paypal_args ) {
		$paypal_args['bn'] = 'Multidots_SP';

		return $paypal_args;
	}

}
