<?php

class DM_wcd {

	protected $loader;

	protected $plugin_name;

	protected $version;

	public function __construct() {

		$this->plugin_name = 'dm-wcd';
		$this->version = '0.1';

		$this->load_dependencies();
		//$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
    
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dm-wcd-loader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dm-wcd-admin.php';

		$this->loader = new DM_WCD_Loader();

	}
    
	private function define_admin_hooks() {

		$plugin_admin = new DM_wcd_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'init', $plugin_admin, 'init_sessions', 1);
        //$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_dm_wcd_menu');
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/dm-wcd-admin.php';        
	}        

	private function define_public_hooks() {

        $discount=new DM_WCD_Discount(false);
        $this->loader->add_filter( 'woocommerce_cart_item_price', $discount, 'get_cart_item_html', 99, 3 );
        $this->loader->add_filter( 'woocommerce_get_sale_price', $discount, 'get_sale_price', 99, 2 );                
        $this->loader->add_filter( 'woocommerce_product_get_price', $discount, 'get_sale_price', 99, 2 );        
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
