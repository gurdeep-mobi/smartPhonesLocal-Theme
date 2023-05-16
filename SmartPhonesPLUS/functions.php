<?php


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'font-awesome','rgs' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_ext1', 'https://fonts.googleapis.com/css?family=Montserrat' );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 150 );

// END ENQUEUE PARENT ACTION
// Remove read more button
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );


// change proceed to checkout button text
function woocommerce_button_proceed_to_checkout() {
 $checkout_url = WC()->cart->get_checkout_url(); ?>
 <a href="<?php echo esc_url( wc_get_checkout_url() );?>" class="checkout-button button alt wc-forward">
 <?php esc_html_e( 'Continue', 'woocommerce' ); ?>
 </a>
 <?php
}
// restrict search output based on category
function SearchFilter($query) {
  if ($query->is_search) {
    $query->set('sell','94');
  }
  return $query;
}
if(!is_admin())
  add_filter('pre_get_posts','SearchFilter');


// Tooltips 


add_filter( 'woocommerce_attribute_label', 'custom_attribute_label', 10, 3 );
function custom_attribute_label( $label, $name, $product ) {
    $taxonomy = 'pa_'.$name;

    if( $taxonomy = array('pa_carrier', 'pa_storage-capacity'))
        $label .= '<div class="custom-label">' . __('<a target="_blank" href="https://imgdemo.wpengine.com/smartphonesplus/need-help/"><i class="fas fa-info-circle"></i></a> ', 'woocommerce') . '</div>';

    return $label;
}

// conditionally hide packaging field based on price - Not working right

function test_filter_billing_fields( $fields ){
    $cart_total = WC()->cart->total; 
    if( $cart_total < 10 ) {
        unset( $fields["billing_need_packaging_material_field"] );
    }
	return $fields;
}
	
add_filter( 'woocommerce_billing_fields', 'test_filter_billing_fields' );




//* TN - Remove Query String from Static Resources
function remove_css_js_ver( $src ) {
if( strpos( $src, '?ver=' ) )
$src = remove_query_arg( 'ver', $src );
return $src;
}
add_filter( 'style_loader_src', 'remove_css_js_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_css_js_ver', 10, 2 ); 

/* Modify the Ajax Product Variation Threshold - Can increase based on total variation # as needed */
function iconic_wc_ajax_variation_threshold( $qty, $product ) {
    return 30;
}

add_filter( 'woocommerce_ajax_variation_threshold', 'iconic_wc_ajax_variation_threshold', 10, 2 );

/**
 * Pre-populate Woocommerce shipping checkout fields
 */

add_filter('woocommerce_checkout_get_value', function($input, $key ) {
	global $current_user;
	switch ($key) :
		
		case 'shipping_first_name':
		return 'SmartphonesPLUS';
		break;
		case 'shipping_last_name':
		return 'LLC';
		break;
		case 'shipping_company':
		return 'SmartphonesPLUS';
		break;
		case 'shipping_address_1':
		return '3315 Williams Blvd. SW Suite 2-180';
		break;
		case 'shipping_city':
		return 'Cedar Rapids';
		break;
		case 'shipping_state':
		return 'IA';
		break;
		case 'shipping_postcode':
		return '52404';
		break;
	
	
		break;
	endswitch;
}, 10, 2);






/**
 * Create a coupon programatically
 
$coupon_code = 'UNIQUECODE'; // Code
$amount = '10'; // Amount
$discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product
					
$coupon = array(
	'post_title' => $coupon_code,
	'post_content' => '',
	'post_status' => 'publish',
	'post_author' => 1,
	'post_type'		=> 'shop_coupon'
);
					
$new_coupon_id = wp_insert_post( $coupon );
					
// Add meta
update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
update_post_meta( $new_coupon_id, 'individual_use', 'no' );
update_post_meta( $new_coupon_id, 'product_ids', '' );
update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
update_post_meta( $new_coupon_id, 'usage_limit', '' );
update_post_meta( $new_coupon_id, 'expiry_date', '' );
update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

*/

function coupon_add_cart_fee( $bookable_total = 0 ) {
    $has_coupon = false;

    // Set here your specials coupons slugs (one by line - last one have no coma)
    $coupon_codes = array (
        '5MORE',
        'TADO' 
    );

    // Set here your fee amount or make fees calculation (see the links in reference)
    $fee = 5;

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    // checking if that "special" coupon is applied to customer cart
    foreach ($coupon_codes as $coupon_code) {
        if ( WC()->cart->has_discount( $coupon_code ) ) {

            // If yes apply the fee to the cart
            if ( !$has_coupon ) {
                $has_coupon = true;
                break;
            }
        }
    }

    if ( $has_coupon ) {
        WC()->cart->add_fee( 'Coupon ', $fee, false );
    }
}
add_action( 'woocommerce_cart_calculate_fees','coupon_add_cart_fee' );




/* Paypal Email Address */
/*
add_action('woocommerce_before_order_notes', 'custom_checkout_field');

function custom_checkout_field($checkout)

{

woocommerce_form_field('custom_field_name', array(

'type' => 'text',

'class' => array(

'my-field-class form-row-wide'

) ,

'label' => __('PayPal Email Address?') ,

'placeholder' => __('Enter Email Here') ,

) ,

$checkout->get_value('custom_field_name'));


}
*/

/* Need Packaging Material */
/*
add_action('woocommerce_before_order_notes', 'wps_add_select_checkout_field');
function wps_add_select_checkout_field( $checkout ) {
	woocommerce_form_field( 'daypart', array(
	    'type'          => 'select',
	    'class'         => array( 'wps-drop' ),
	    'label'         => __( 'Do you need a box and packaging material?' ),
	    'options'       => array(
	    	'blank'		=> __( 'Select one', 'wps' ),
	        'morning'	=> __( 'Yes', 'wps' ),
	        'afternoon'	=> __( 'No', 'wps' )
	    )
 ),
	$checkout->get_value( 'daypart' ));
}
*/

/* Hide Shipment Tracking ID */



// Order SKU Array Function 
function thankyou_custom_items_data( $order_id ) {
    // Get an instance of the the WC_Order Object
    $order = wc_get_order( $order_id );

    $items_data = array();

    foreach ($order->get_items() as $item_id => $item ) {

        // Get an instance of corresponding the WC_Product object
        $product = $item->get_product();

        $items_data[$item_id] = array(
            'sku'        => $product->get_sku()
        );
    }
    
}