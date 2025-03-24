<?php 
// Subscription plans 
// Minimum amount is $0.50 US 
// Interval day, week, month or year 
$GLOBALS['stripe_currency'] = "USD";  
 $GLOBALS['PAYPAL_TESTMODE'] =1;
/* Stripe API configuration 
 * Remember to switch to your live publishable and secret key in production! 
 * See your keys here: https://dashboard.stripe.com/account/apikeys 
 */ 
 // TEST INFO ======================================================================
 if($GLOBALS['PAYPAL_TESTMODE'] == 1){ 
	 $GLOBALS['stripe_plans'] = array( 
		'1' => array( 
			'name' => 'Gold Membership', 
			'price' => 12, 
			'interval' => 'monthly',
			'id' => 'price_1JysTLGE4ZvK3hRimNzCgmyF',
			'member_type' => 0
		), 
		'2' => array( 
			'name' => 'Gold Membership', 
			'price' => 124, 
			'interval' => 'yearly',
			'id' => 'price_1JysTLGE4ZvK3hRiL9rj4PNc',
			'member_type' => 0 
		)
	); 
	define('STRIPE_API_KEY', 'sk_test_YT6JeUNHEDko6ikCc37bqFkQ00WK3L3Tqm'); 
	define('STRIPE_PUBLISHABLE_KEY', 'pk_test_AtjBsBp9MAXfBkTptAVgLv5K00RNxcBwCu'); 
	define('STRIPE_WEBHOOK_SECRET', 'whsec_5ZiLNamtDmsBRqKjDA8GpXWX3bORMAUd'); 
 }else{


// LIVE INFO ============================================================================
	 $GLOBALS['stripe_plans'] = array( 
		'1' => array( 
			'name' => 'Gold Membership', 
			'price' => 12, 
			'interval' => 'monthly',
			'id' => 'price_1JzafRGE4ZvK3hRi4bMR95Zc',
			'member_type' => 0
		), 
		'2' => array( 
			'name' => 'Gold Membership', 
			'price' => 124, 
			'interval' => 'yearly',
			'id' => 'price_1JzafRGE4ZvK3hRiPrJyKO78',
			'member_type' => 0 
		)
	); 
	
	define('STRIPE_API_KEY', 'sk_live_M9Pr1jZGscFMByy8qvgbPvMH00VNon3YB4'); 
	define('STRIPE_PUBLISHABLE_KEY', 'pk_live_Jow0ljmNgFVApWOKHPLLWeCf004V7wUzkL');
	define('STRIPE_WEBHOOK_SECRET', 'whsec_mX4s5hcaJOCY4pdWJL8Ie2uZZgWDEqrU');
 }
?>