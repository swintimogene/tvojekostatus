<?php

function zlavy_nastavenie() {
global $wpdb;

foreach ($_POST as $key => $value) {
    if (strpos($key, "zlava_") !== false) {
        $user_id = substr($key, strlen("zlava_"), 99);
        
        $wpdb->query("delete from " . $wpdb->prefix . "dc_wcd where users_id = " . $user_id);        
        $wpdb->query("insert into " . $wpdb->prefix . "dc_wcd values (''," . $user_id . "," . $value . ")"); 
    }
}

/*
$args = array(
	'blog_id'      => $GLOBALS['blog_id'],
	'role'         => '',
	'role__in'     => array(),
	'role__not_in' => array(),
	'meta_key'     => '',
	'meta_value'   => '',
	'meta_compare' => '',
	'meta_query'   => array(),
	'date_query'   => array(),        
	'include'      => array(),
	'exclude'      => array(),
	'orderby'      => 'login',
	'order'        => 'ASC',
	'offset'       => '',
	'search'       => '',
	'number'       => '',
	'count_total'  => false,
	'fields'       => 'all',
	'who'          => ''
 ); 
get_users( $args );

WP_User Object	(
	[data] => stdClass Object
	(
		[ID] => 3
		[user_login] => dabid
		[user_pass] => $P$BK1deNU7lV/rgwp2oncgESRHRYLedRF.
		[user_nicename] => David
		[user_email] => example@example.com
		[user_url] => 
		[user_registered] => 2017-03-21 14:11:32
		[user_activation_key] => 149492:$P$BV76R0AcpmGMNAStZbdO.uVu7QWF9l1
		[user_status] => 0
		[display_name] => David Example
	)

*/
    echo '<div class="wrap">';

	$blogusers = get_users('orderby=meta_value&meta_key=nickname');
    
    echo '    <table class="wp-list-table widefat fixed striped posts">';
    echo '        <thead>';
    echo '            <tr>';
    echo '              <td>';    
    echo '              Nickname';    
    echo '              </td>';    
    echo '              <td>';
    echo '              Meno';        
    echo '              </td>';
    echo '              <td>';
    echo '              e-mail';        
    echo '              </td>';
    echo '              <td>';
    echo '              Zľava';        
    echo '              </td>';
    echo '              <td>';    
    echo '              </td>';                
    echo '            </tr>';
    echo '        </thead>';
    echo '        <tbody id="the-list">';

    foreach ( $blogusers as $user ) {    
        echo '        <tr>';
        echo '           <form action="" method="post">';
        echo '    	     <td>' . esc_html( get_user_meta ($user->ID, 'nickname', true) ) . '</td>';
        echo '           <td>' . esc_html( get_user_meta ($user->ID, 'first_name', true) ) . ' ' . esc_html( get_user_meta ($user->ID, 'last_name', true) ) . '</td>';
        echo '    	     <td>' . esc_html( $user->user_email ) . '</td>';
        echo '           <td><input name="zlava_' . $user->ID . '" type="text" id="zlava" value="';
        
        $query_discount = "select " . $wpdb->prefix . "dc_wcd.discount from " . $wpdb->prefix . "dc_wcd where users_id = " . $user->ID;
        $result_discount = $wpdb->get_results($query_discount);
        
        $discount = 0;
        
        foreach ($result_discount as $discount_value) {
            $discount = $discount_value->discount;   
        }    
            
        echo $discount;
        
        echo '"> %</td>';
        echo '<td>';
        echo '<p><input type="submit" name="save_discount_' . $user->ID . '" id="submit" class="button button-primary" value="Uložiť zmeny"  /></p>';
        echo '</td>';
        echo '           </form>';            
        echo '        </tr>';
    }

    echo '       </tbody>';
    echo '    </table>';
    
	echo '</div>';    
}