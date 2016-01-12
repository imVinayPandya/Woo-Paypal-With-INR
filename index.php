<?php
/*
Plugin Name: Woocommerce inr for paypal
Plugin URI: https://www.panduboys.com
Description: This plugin enable inr for paypal gateway. its automatically convert inr to usd when customer try to check out with paypal.
Author: Vinay Pandya
Version: 1.0
Author URI: http://vinaypandya.com
*/

// add indian currency to woocommerce store
add_filter( 'woocommerce_currencies', 'inr_currency' );

function inr_currency( $currencies ) {
    $currencies['INR'] = __( 'Indian Currency (INR)', 'woocommerce' );
    return $currencies;
}


// add inr symbol
add_filter('woocommerce_currency_symbol', 'inr_currency_symbol', 10, 2);

function inr_currency_symbol( $currency_symbol, $currency ) {
    switch( $currency ) {
    case 'INR': 
		$currency_symbol = '₹'; 
		break;
	}
	return $currency_symbol;
}


//enable particular currency for paypal
add_filter( 'woocommerce_paypal_supported_currencies', 'add_paypal_valid_currency' ); 
 
function add_paypal_valid_currency( $currencies ) { 
     array_push ( $currencies , 'INR' ); /* YOUR CURRENCY */
     return $currencies; 
}


//convert your currency to USD
add_filter('woocommerce_paypal_args', 'woocommerce_paypal_args_for_inr');

function woocommerce_paypal_args_for_inr($paypal_args){
    if ( $paypal_args['currency_code'] == 'INR'){
 
        $convert_rate = getFromYahoo(); 
 
        $count = 1;
        while( isset($paypal_args['amount_' . $count]) ){
            $paypal_args['amount_' . $count] = round( $paypal_args['amount_' . $count] / $convert_rate, 2);
            $count++;
        }
 $paypal_args['tax_cart'] = round( $paypal_args['tax_cart'] / $convert_rate, 2);
    }
    return $paypal_args;
}

// get your currency rate from yahoo 
function getFromYahoo()
{
 $from   = 'USD'; /*change it to your required currencies */
 $to     = 'INR';
 $url = 'http://finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from . $to .'=X';
 $handle = @fopen($url, 'r');
 
 if ($handle) {
 $result = fgets($handle, 4096);
 fclose($handle);
 }
 $allData = explode(',',$result); /* Get all the contents to an array */
 return $allData[1];
}