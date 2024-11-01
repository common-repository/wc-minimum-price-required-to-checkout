<?php
/**
 * Minimum Price Required to Checkout for WooCommerce
 *
 * Plugin Name: Minimum Price Required to Checkout for WooCommerce
 * Plugin URI: 	https://wpacho.com/
 * Description: Control minimum amount required before checkout.
 * Version: 	1.0
 * Author: 		WPacho
 * Author URI: 	https://wpacho.com/
 * Text Domain: wmpfc
 * Domain Path: /languages/
 * License: 	GPLv2 or later
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

/**
 * Final Class - wmpfc.
 *
 * @class wmpfc
 * @since 1.0
 */
final class wmpfc {

	/**
	 * wmpfc version.
	 *
	 * @var string
	 * @since 1.0
	 */
	public $version = '1.0';

	/**
	 * wmpfc full plugin name.
	 *
	 * @var string
	 * @since 1.0
	 */
	public $name = 'Woo Minimum Price to Checkout';

	/**
	 * wmpfc slug.
	 *
	 * @var string
	 * @since 1.0
	 */
	public $slug = 'wmpfc';

	/**
	 * Author URL
	 *
	 * @var string
	 * @since 1.0
	 */
	public $url = 'https://wpacho.com/';

	/**
	 * wmpfc Constructor.
	 * @since 1.0
	 */
	public function __construct() {
		$this->wmpfc_start();
	}

	/**
	 * wmpfc start if woocommerce plugin is active
	 *
	 * @var string
	 * @since 1.0
	 */
	public function wmpfc_start() {
		register_activation_hook( __FILE__, array( $this, 'wmpfc_ini') );
		if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))) {
			$this->localize_plugin();
			add_action( 'admin_init', array($this, 'wmpfc_admin_ini'));
			add_action( 'admin_menu', array($this, 'wmpfc_menu') );
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'wmpfc_link'));
			add_filter( 'plugin_row_meta', array( $this, 'wmpfc_rhs_link' ), 10, 2 );
			$this->wmpfc_woocommerce();
		}else{
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * wmpfc error notice
	 *
	 * @var string
	 * @since 1.0
	 */
	public function woocommerce_missing_notice(){
		echo '<div class="error"><p><strong>' . esc_html__( 'WooCommerce Minimum Price for Checkout plugin requires active WooCommerce plugin', 'wmpfc' ) . '</strong></p></div>';
	}

	/**
	 * wmpfc Initialize for admin dashboard.
	 *
	 * @since 1.0
	 */
	public function wmpfc_admin_ini() {
		register_setting('wmpfc_fields_manager', 'wmpfc_fields');
	}

	/**
	 * wmpfc Initialize during activation.
	 *
	 * @since 1.0
	 */
	public function wmpfc_ini() {
		update_option('wmpfc_fields', array(
			'activate'  => 1,
			'min_price' => 50,
			'inc_tax'   => 0,
			'cart_page_message' => '<strong>A minimum of $50 is required before checking out (Excluding all taxes).</strong>',
		));
		update_site_option('wmpfc_installed_date', date('Y-m-d H:i:s'));
	}

	/**
	 * wmpfc menu
	 *
	 * @since 1.0
	 */
	public function wmpfc_menu() {
		add_submenu_page(
			'options-general.php',
			__($this->name, 'wmpfc'),
			__($this->name, 'wmpfc'),
			'manage_options',
			$this->slug,
			array( $this, 'wmpfc_page_template' )
		);
	}

	/**
	 * wmpfc submenu page content
	 *
	 * @since 1.0
	 */
	function wmpfc_page_template() { ?>
		<style>
			.settings_page_<?php echo $this->slug; ?> a{
				text-decoration:none;
			}
			.settings_page_<?php echo $this->slug; ?> a:focus,
			.settings_page_<?php echo $this->slug; ?> a:hover{
				box-shadow: none;
				text-decoration:none
			}
			.settings_page_<?php echo $this->slug; ?> a:focus,
			.settings_page_<?php echo $this->slug; ?> a:hover{
				box-shadow: none;
				text-decoration:none
			}
			.settings_page_<?php echo $this->slug; ?> tr.wmpfc-header td {
				font-style: italic;
				padding: 10px 5px;
				margin: 0px;
			}
			.settings_page_<?php echo $this->slug; ?> tr.wmpfc-header h4 {
				font-weight: bold;
				font-size: 15px;
				font-style: normal;
				border-bottom: 1px solid rgb(222, 222, 222);
				margin: 10px 0px 10px;
				padding: 9px 10px 10px;
				background: #e5e5e5;
			}
			.settings_page_<?php echo $this->slug; ?> tr[valign="top"] th {
				padding-left: 15px;
			}
			.settings_page_<?php echo $this->slug; ?> code {
				padding: 5px 10px;
				font-size: 12px;
			}
			.settings_page_<?php echo $this->slug; ?> .space th,
			.settings_page_<?php echo $this->slug; ?> .space td{
				padding: 15px;
			}
			.settings_page_<?php echo $this->slug; ?> .no-space th,
			.settings_page_<?php echo $this->slug; ?> .no-space td{
				padding: 4px;
			}
			.settings_page_<?php echo $this->slug; ?> input[type="text"],
			.settings_page_<?php echo $this->slug; ?> input[type="number"]{
				padding: 15px 10px;
				font-size: 15px;
				line-height: 100%;
				height: 36px;
				width: 55%;
				outline: 0;
				background-color: #fff;
			}
			.settings_page_<?php echo $this->slug; ?> i{
				padding-top: 5px;
				font-size: 18px;
			}
		</style>
		<div class="wrap">
			<h1><?php echo $this->name; ?></h1>
			<?php do_action('wmpfc_errors'); ?>
			<form id="wmpfc-options" method="post" action="options.php">
				<?php settings_fields( 'wmpfc_fields_manager' ); ?>
				<div class="metabox-holder">
					<div style="width: 70%;">
						<div class="postbox">
							<div class="inside">
							<?php $wmpfc = get_option( 'wmpfc_fields' ); ?>
							<table class="form-table">
								<tbody>
									<tr class="wmpfc-header">
										<td colspan="2">
										<h4><?php _e('Settings', 'wmpfc'); ?></h4>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e('Activate', 'wmpfc'); ?></th>
										<td>
											<input type="checkbox" name="wmpfc_fields[activate]" <?php checked( 1, isset($wmpfc['activate']) ); ?>/>
										</td>
									</tr>
									<tr class="wmpfc-header">
										<td colspan="2">
										<h4><?php _e('Price settings', 'wmpfc'); ?></h4>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e('Minimum price', 'wmpfc'); ?></th>
										<td>
											<input type="number" name="wmpfc_fields[min_price]" value="<?php echo (isset($wmpfc['min_price']))? $wmpfc['min_price'] : ''; ?>" style="width: 20%;"/>
											<i><?php echo get_woocommerce_currency_symbol(); ?></i>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e('Include all taxes', 'wmpfc'); ?></th>
										<td>
											<input type="checkbox" name="wmpfc_fields[inc_tax]" <?php checked( 1, isset($wmpfc['inc_tax']) ); ?>/>
										</td>
									</tr>
									<tr class="wmpfc-header">
										<td colspan="2">
										<h4><?php _e('Message display settings', 'wmpfc'); ?></h4>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e('Cart page error message', 'wmpfc'); ?></th>
										<td>
											<textarea rows="8" cols="50" name="wmpfc_fields[cart_page_message]"><?php echo (isset($wmpfc['cart_page_message']))? $wmpfc['cart_page_message'] : ''; ?></textarea>
										</td>
									</tr>
								</tbody>
							</table>
							</div>
						</div>
					</div>
					<p class="submit">
						<input type="submit" class="button-primary" name="Submit" value="Save Changes">
					</p>
				</div>
			</form>
		</div>
	<?php }

	/**
	 * wmpfc important woocommerce actions
	 *
	 * @since 1.0
	 */
	function wmpfc_woocommerce() {
	
		$wmpfc = get_option( 'wmpfc_fields' );
		if(!empty($wmpfc['activate'])){
		
			add_action( 'woocommerce_check_cart_items', array($this, 'wmpfc_set_min_total') );
			add_action( 'woocommerce_proceed_to_checkout', array($this, 'wmpfc_disable_checkout_button'), 1 );
			add_action( 'wp', array($this, 'wmpfc_prevent_checkout_access') );
			
		}
		
	}

	/**
	 * wmpfc setting minimum value
	 *
	 * @since 1.0
	 */
	function wmpfc_set_min_total() {
	
		$wmpfc = get_option( 'wmpfc_fields' );
		
		// Only run in the Cart or Checkout pages
		if( is_cart() || is_checkout() ) {
			global $woocommerce;

			// Set minimum cart total
			$minimum_cart_total = (isset($wmpfc['min_price']))? $wmpfc['min_price'] : '50';

			// A Minimum amount is required before checking out.
			if(empty($wmpfc['inc_tax'])){
				$price = '';
				foreach ( WC()->cart->get_cart() as $cart_item ){
					$price += ($cart_item['data']->get_price()) * ($cart_item['quantity']);
				}
			}else{
				$price = WC()->cart->total;
			}

			if( $price != 0 ){
				$total = number_format( $price, 2 );
			}

			// Compare values and add an error is Cart's total
			if( $price < $minimum_cart_total && $price != 0  ){
				// Display our error message
				wc_add_notice( sprintf(
					$wmpfc['cart_page_message'],
					$minimum_cart_total,
					$total
				),
				'error' );
			}
			
		}
		
	}

	/**
	 * wmpfc disable all checkout buttons
	 *
	 * @since 1.0
	 */
	function wmpfc_disable_checkout_button(){
	
		$wmpfc = get_option( 'wmpfc_fields' );
		
		// Set this variable to specify a minimum order value
		$minimum = (isset($wmpfc['min_price']))? $wmpfc['min_price'] : '50';
		if(empty($wmpfc['inc_tax'])){
			$price = '';
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				$price += ($cart_item['data']->get_price()) * ($cart_item['quantity']);
			}
		}else{
			$price = WC()->cart->total;
		}

		if( $price < $minimum ){
			remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
			echo '<a href="#" class="checkout-button button alt wc-forward">Proceed to checkout</a>';
		}
		
	}

	/**
	 * wmpfc disable direct checkout access
	 *
	 * @since 1.0
	 */
	function wmpfc_prevent_checkout_access() {
	
		$wmpfc = get_option( 'wmpfc_fields' );
		
		// Check that WC is enabled and loaded
		if( function_exists( 'is_checkout' ) && is_checkout() ) {
		
			// Set minimum cart total
			$minimum_cart_total = (isset($wmpfc['min_price']))? $wmpfc['min_price'] : '50';
			if(empty($wmpfc['inc_tax'])){
				$price = '';
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					$price += ($cart_item['data']->get_price()) * ($cart_item['quantity']);
				}
			}else{
				$price = WC()->cart->total;
			}

			// Compare values and add an error is Cart's total
			if( $price < $minimum_cart_total  ) {
				wp_redirect( esc_url( WC()->cart->get_cart_url() ) );
				exit;
			}
			
		}
		
	}

	/**
	 * Add links to plugin after activation
	 *
	 * @since 1.0
	 */
	public function wmpfc_link( $links ) {
		$arr 	    = $links;
		$main_arr   = array();
		$main_arr[] = '<a href="' . admin_url( 'options-general.php?page='.$this->slug .'' ) .'">' . __('Settings', 'wmpfc') . '</a>';
		return array_merge($main_arr,$arr);
	}

	/**
	* Adds a Donate and Rating button
	*
	* @since 1.0
	*/
	public function wmpfc_rhs_link( $links, $file ) {

		$plugin = plugin_basename(__file__);
		if ( $file == $plugin ) {

			return array_merge(
				$links,
				array(
					'<a href="">' . __( 'Rating', 'wmpfc' ) . '</a>',
					'<a href="'. $this->url .'?donate=yes" target="_blank"><b style="font-weight: 700;">' . __( 'Donate', 'wmpfc') . '</b></a>'
				)
			);

		}

		return $links;
	}

	/**
	 * wmpfc Initialize during activation.
	 *
	 * @since 1.0
	 */
	public function localize_plugin() {
		load_plugin_textdomain(
			'wmpfc',
			false,
			plugin_basename( dirname( __FILE__ ) ) . '/language'
		);
	}

	/**
	 * initialize function
	 *
	 * @since 1.0
	 */
	public static function init() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new wmpfc();
		}
		return $instance;
	}

}

/**
 * initialize control function wmpfc()
 *
 * @since 1.0
 */
function wmpfc() {
    return wmpfc::init();
}

/**
 * Start wmpfc()
 *
 * @since 1.0
 */
wmpfc();