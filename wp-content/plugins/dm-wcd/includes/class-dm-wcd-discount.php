<?php

class DM_WCD_Discount {

    public function __construct($discount_id) {

    }
    
    function get_cart_item_html($price_html, $cart_item, $cart_item_key) {
        if(!empty($cart_item))
        {
            $product_id = $cart_item["product_id"];
            
            if ($cart_item["variation_id"]) {
                $product_id = $cart_item["variation_id"];
            }
            
            $product_obj = wc_get_product($product_id);

            //$used_price = $this->get_regular_price($product_obj->price, $product_obj);
            
            $used_price = $product_obj->get_regular_price($product_obj->price, $product_obj);

            if ($used_price != $cart_item['data']->price) {
                //$old_price_html = wc_price($product_obj->price);
                $old_price_html = $product_obj->get_regular_price();
                $price_html = "<span class='wad-discount-price' style='text-decoration: line-through;'>$old_price_html</span>" . " $price_html";
            }
        }
        return $price_html;
    }    
    
    public function get_sale_price($sale_price, $product) {

        global $wpdb;        

        $query_discount = "select " . $wpdb->prefix . "dc_wcd.discount from " . $wpdb->prefix . "dc_wcd where users_id = " . wp_get_current_user()->ID;
        $result_discount = $wpdb->get_results($query_discount);
        
        $discount = 0;
        
        foreach ($result_discount as $discount_value) {
            $discount = $discount_value->discount;   
        }        
        
        if ($discount != 0) {
            return $product->get_regular_price() - ($product->get_regular_price() * ($discount / 100));
        } else {
            return $product->get_regular_price();
        } 
    }
}
