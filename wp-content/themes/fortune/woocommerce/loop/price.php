<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
?>

<?php

if ( $product->has_child() ) {
    $prices = $product->get_variation_prices( true );
    
    $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
    
    if (!empty($tax_rates)) {
        $tax_rate = reset($tax_rates);
    }        

	if ( empty( $prices['price'] ) ) {
		$price = apply_filters( 'woocommerce_variable_empty_price_html', '', $product );
	} else {
		if ( $product->is_taxable() ) {
            $min_price     = current( $prices['price'] ) * (1 + $tax_rate['rate'] / 100);
    		$max_price     = end( $prices['price'] ) * (1 + $tax_rate['rate'] / 100);
    		$min_reg_price = current( $prices['regular_price'] ) * (1 + $tax_rate['rate'] / 100);
    		$max_reg_price = end( $prices['regular_price'] ) * (1 + $tax_rate['rate'] / 100);
        } else {
            $min_price     = current( $prices['price'] );
    		$max_price     = end( $prices['price'] );
    		$min_reg_price = current( $prices['regular_price'] );
    		$max_reg_price = end( $prices['regular_price'] );
        }

		if ( $min_price !== $max_price ) {
			$price = wc_format_price_range( $min_price, $max_price );
		} elseif ( $product->is_on_sale() && $min_reg_price === $max_reg_price ) {
			$price = wc_format_sale_price( wc_price( $max_reg_price ), wc_price( $min_price ) );
		} else {
			$price = wc_price( $min_price );
		}

		$price = apply_filters( 'woocommerce_variable_price_html', $price , $product );
	}

    echo "<span><strong>" . $product->get_price_html() . "</strong></span>";

    if ($product->is_taxable()) {
        echo "<span class=\"price\">" . $price . ' ' . WC()->countries->inc_tax_or_vat() . "</span>";
    }
} else {
    echo "<span><strong>" . $product->get_price_html() . "</strong></span>";
    
    if ($product->is_taxable()) {
    echo "<span class=\"price\">" . woocommerce_price($product->get_price_including_tax()) . ' ' . WC()->countries->inc_tax_or_vat() . "</span>";    
    }
}
?>