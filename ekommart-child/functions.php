<?php
/**
 * Theme functions and definitions.
 */
		 
//to show Download button on success page to print the invoice in pdf
add_filter('woocommerce_thankyou_order_received_text', 'wpo_wcpdf_thank_you_link', 10, 2);
function wpo_wcpdf_thank_you_link( $text, $order ) {
    if ( is_user_logged_in() ) {
        $pdf_url = wp_nonce_url( admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids=' . $order->get_id() . '&my-account'), 'generate_wpo_wcpdf' );
        $text .= '<p><a href="'.esc_attr($pdf_url).'">Download a Printable Invoice</a></p>';
    }else{
        $pdf_url = admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids=' . $order->get_id() . '&order_key=' . $order->get_order_key() );
        $text .= '<p><a href="'.esc_attr($pdf_url).'">Download a Printable Invoice</a></p>';
    }
    return $text;
}

// new section added for pdp 

/* We have move this section towards right side
add_action( 'woocommerce_after_add_to_cart_form', 'add_section_after_add_to_cart_button' );


function add_section_after_add_to_cart_button() {
		echo '<div class="pdp-text-block">
			<div>
				<img src="/wp-content/uploads/2020/02/infoicon.png" class="pdpinfo"/>
			</div>
			<div>
				<p>We do NOT accept items that have been reported lost or stolen.<br>
				Any item that still has an iCloud, Google, Samsung account active 
				will NOT be accepted either. View our <a target="_blank" href="https://www.smartphonesplus.com/guide-to-removing-icloud-google-and-samsung-accounts/"> guide to removing accounts</a>.</p>
				<p>The prices shown represent devices that have been fully paid off 
				and are no longer on an account. Devices with a balance due or active 
				account will have a reduced value. Devices that have mobile device management 
				(MDM) active will also have a reduced value.</p>
			</div>
		</div>
		
		';
	}
  */

//  products price range as 'upto'  added

function upto_format_price_range( $price, $from, $to ) {
    return sprintf( '%s %s', __( '<span> Up to </span>', 'iconic' ), wc_price( $to ) );
} 
add_filter( 'woocommerce_format_price_range', 'upto_format_price_range', 10, 3 );

// change link in cart page

add_filter( 'woocommerce_continue_shopping_redirect', 'changed_woocommerce_continue_selling_redirect', 10, 1 );
function changed_woocommerce_continue_selling_redirect( $return_to ){
    $return_to = "/sell";
	
    return $return_to;
}

// change name in cartpage

add_filter( 'wc_add_to_cart_message_html', 'my_changed_wc_add_to_cart_message_html', 10, 2 );
function my_changed_wc_add_to_cart_message_html($message, $products){
    if (strpos($message, 'Continue shopping') !== false) {
        $message = str_replace("Continue shopping", "Continue Selling", $message);
    }
    return $message;
}

// step 1 add a location rule type
add_filter('acf/location/rule_types', 'acf_wc_product_type_rule_type');
function acf_wc_product_type_rule_type($choices)
{
  // first add the "Product" Category if it does not exist
  // this will be a place to put all custom rules assocaited with woocommerce
  // the reason for checking to see if it exists or not first
  // is just in case another custom rule is added
  if (!isset($choices['Product'])) {
    $choices['Product'] = array();
  }
  // now add the 'Category' rule to it
  if (!isset($choices['Product']['product_cat'])) {
    // product_cat is the taxonomy name for woocommerce products
    $choices['Product']['product_cat_term'] = 'Product Category Term';
  }
  return $choices;
}

// step 2 skip custom rule operators, not needed


// step 3 add custom rule values
add_filter('acf/location/rule_values/product_cat_term', 'acf_wc_product_type_rule_values');
function acf_wc_product_type_rule_values($choices)
{
  // basically we need to get an list of all product categories
  // and put the into an array for choices
  $args = array(
    'taxonomy' => 'product_cat',
    'hide_empty' => false
  );
  $terms = get_terms($args);
  foreach ($terms as $term) {
    $choices[$term->term_id] = $term->name;
  }
  return $choices;
}

// step 4, rule match
add_filter('acf/location/rule_match/product_cat_term', 'acf_wc_product_type_rule_match', 10, 3);
function acf_wc_product_type_rule_match($match, $rule, $options)
{
  if (!isset($_GET['tag_ID'])) {
    // tag id is not set
    return $match;
  }
  if ($rule['operator'] == '==') {
    $match = ($rule['value'] == $_GET['tag_ID']);
  } else {
    $match = !($rule['value'] == $_GET['tag_ID']);
  }
  return $match;
}

// add tooltip icon in pdp page

add_filter( 'woocommerce_attribute_label', 'custom_attribute_label', 10, 3 );
  function custom_attribute_label( $label, $name, $product ) {
    if ( is_product() ){
      global $post;
      $args = array( 'taxonomy' => 'product_cat',);
      $terms = wp_get_post_terms($post->ID,'product_cat', $args);
      $count = count($terms); 
      
      if ($count > 0) {
        $count_fields = 0;
        foreach ($terms as $term) {
          $fields = get_field_objects($term);
          if( $fields  && $count_fields == 0 ): ?>
            <?php foreach( $fields as $field ): ?>
      
      <?php
      if($label==$field['label']){
      $label .= '<span class="pdp-info">!</span><p class="sp-label">'.$field['value'].'</p>';
      }
       endforeach; 
       $count_fields++;
       ?>
       <?php endif;
    }
  }
}
    return $label;
}


add_action( 'woocommerce_check_cart_items', 'remove_additonal_box_field' );
function remove_additonal_box_field() {
  $categories   = array('iMac','Homepods','Mac Pro','Macbook','MacBook Air','MacBook Pro','macbook sub','Apple TV','SurfaceBook','Surface Laptop');  
  $has_category = false;
// Loop through cart items
  foreach ( WC()->cart->get_cart() as $cart_item ) {
    if ( has_term( $categories, 'product_cat', $cart_item['product_id'])|| $cart_item['data']->get_title()=='Samsung Galaxy View2') {
      $has_category = true;
        break;
    }
  }
  if ( $has_category && is_checkout() ) { 
    echo '<span class="remove-fields" data-category-checkout="true" ></span>';
  }
}

/* Begin Custom Text below price on shop page */
add_action( 'woocommerce_after_shop_loop_item', 'start_selling', 5 );
function start_selling() {
global $product;
    echo '<a rel="nofollow" href="'.$product->get_permalink().'" data-quantity="1" data-product_id="'.$product->get_id().'" data-product_sku="" class="button _start_selling">Start Selling</a>';
}

// Redirect Registration Page
function my_registration_page_redirect()
{
  global $pagenow;

  if ( ( strtolower($pagenow) == 'wp-login.php') && ( strtolower( $_GET['action']) == 'register' ) ) {
    wp_redirect( home_url('/my-account'));
  }
}

add_filter( 'init', 'my_registration_page_redirect' );

add_filter('woocommerce_get_breadcrumb', 'custom_breadcrumb', 20, 2);

function custom_breadcrumb($crumbs, $breadcrumb)
{
  if ( is_page( array( 'iphones','sell-iphones','sell-samsung','samsung','sell-android','sell-macbook','sell-smartwatch','sell-ipad-tablet','sell-mac-desktop','sell-microsoft-surface','sell-other-items','other-smartphones','macbook','smartwatch','tablet','mac','microsoftsurface','other-items','sell-apple-watch','sell-apple-watch-se','sell-my-apple-watch-2april','sell-apple-watch-series-6','sell-apple-watch-series-5','sell-apple-watch-series-4','sell-apple-watch-series-3','sell-apple-watch-series-2','sell-apple-watch-series-1','sell-apple-watch-series-1st-gen','google','motorola','lg','sell-oneplus','htc','sony','essential','caterpillar','blackberry','samsungwatch','appletablet','samsungtablet','ipad','ipadair','ipadmini','ipadpro','macbooksub','macbookair','sell-macbookpro','imac','imacpro','macmini','macpro-2','surface','surfacebook','surfacego','surfacelaptop') ) ) {
    $url = site_url();
         if (!empty($crumbs)) {
            array_splice($crumbs, 1, 0, array(array(
                'Start Selling',
                $url.'/sell/'
            )));
        }
        return $crumbs;
    } else if(is_product_category()) {
        $url = site_url();
          if (!empty($crumbs)) {
              array_splice($crumbs, 1, 0, array(array(
                  'Start Selling',
                  $url.'/sell/'
              )));
          }
        return $crumbs;
    }else{
      return $crumbs;
    }
}

//Change the Billing Address checkout label
function wc_billing_field_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Billing details' :
            $translated_text = __( 'Mailing details', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'wc_billing_field_strings', 20, 3 );

add_action( 'woocommerce_after_cart_totals', 'tl_continue_shopping_button' );
function tl_continue_shopping_button() {
 $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
 
 echo '<div class="wc-proceed-to-continue">';
 echo ' <a href="'.$shop_page_url.'" class="checkout-button button">Continue Selling </a>';
 echo '</div>';
}

 /**
  * Edit my account menu order
  */

 function my_account_menu_order() {
  $menuOrder = array(
    'dashboard'          => __( 'Dashboard', 'woocommerce' ),
    'orders'             => __( 'Orders', 'woocommerce' ),
    'edit-address'       => __( 'Address', 'woocommerce' ),
    'edit-account'      => __( 'Account Details', 'woocommerce' ),
    'customer-logout'    => __( 'Logout', 'woocommerce' ),
  );
  return $menuOrder;
 }
 add_filter ( 'woocommerce_account_menu_items', 'my_account_menu_order' );

 add_action( 'woocommerce_checkout_process', 'bbloomer_checkout_fields_custom_validation' );
    function bbloomer_checkout_fields_custom_validation() { 
       if ( isset( $_POST['billing_address_1'] ) && ! empty( $_POST['billing_address_1'] ) ) {
          if ( strlen( $_POST['billing_address_1'] ) > 32 ) {
             wc_add_notice( 'Address one requires max 32 characters', 'error' );
          }
       } 
       if ( isset( $_POST['billing_address_2'] ) && ! empty( $_POST['billing_address_2'] ) ) {
          if ( strlen( $_POST['billing_address_2'] ) > 32 ) {
             wc_add_notice( 'Address two requires max 32 characters', 'error' );
          }
       }   
    }

add_action( 'woocommerce_after_checkout_validation', 'misha_validate_checkout', 10, 2);
 
function misha_validate_checkout( $fields, $errors ){

    $error_status = false;
    $discount_applicable = false;

    if (empty($fields['billing_payment_options'])) {
        // If empty, set an error status and display the error at the top
        $error_message =  __('<strong>Billing / Mailing Detail </strong> is a required field.', 'your-text-domain');
        wc_add_notice($error_message, 'error');
    }

    if($fields['billing_payment_options'] == 'PayPal' ){
        $discount_applicable = true;
        if ( empty($fields[ 'billing_paypal_email' ] ) ) {
            $error_status = true;
            $errors->add( 'validation', 'Please provide your PayPal email address' );
        }elseif($fields[ 'billing_paypal_email' ]!= $fields['billing_paypal_email_confirm']){            
            $error_status = true;
            $errors->add( 'validation', 'PayPal email address should match confirm paypal email address.' );        
        }
    }elseif($fields['billing_payment_options'] == 'Venmo'){
        $discount_applicable = true;
        if ( empty($fields[ 'billing_venmo_no' ] ) ) {
            $error_status = true;
            $errors->add( 'validation', 'Please provide your Venmo account phone number' );
        }elseif($fields[ 'billing_venmo_no' ]!= $fields['billing_venmo_no_confirm']){            
            $error_status = true;
            $errors->add( 'validation', 'Venmo account phone number should match confirm venmo account phone number.' );        
        }
    }
    elseif($fields['billing_payment_options'] == 'GiftCard'){
        $discount_applicable = true;
        if ( empty($fields[ 'billing_gift_card_email' ] ) ) {
            $error_status = true;
            $errors->add( 'validation', 'Please provide your gift card email address' );
        }elseif($fields[ 'billing_gift_card_email' ]!= $fields['billing_gift_card_email_confirm']){            
            $error_status = true;
            $errors->add( 'validation', 'GiftCard email address should match confirm GiftCard email address.' );        
        }
    }
    elseif($fields['billing_payment_options'] == 'Check'){
        $discount_applicable = true;
        if ( empty($fields[ 'billing_check_name' ] ) ) {
            $error_status = true;
            $errors->add( 'validation', 'Please enter your correct name on check.' );   
        }
    }
    else{

    }
 

    /** removed this discount code on checkout page
    if($error_status == false && $discount_applicable == true){
        $product_id = '132841';
        $product_cart_id = WC()->cart->generate_cart_id( $product_id );
        $in_cart = WC()->cart->find_product_in_cart( $product_cart_id );
        
        if ( $in_cart ) {
        
            // avoid adding
        
        }else{
            WC()->cart->add_to_cart( $product_id );
        }        
    }
    **/
    
}    
    
    function footer_js() { ?>        
<script>

//  remove extra selling button on category page
jQuery('._start_selling').siblings('.button').hide();
  
setTimeout(function() {
jQuery('.elementor-element-c15ca39 #elementor-tab-title-2021').removeClass('elementor-active').next('#elementor-tab-content-2021').css('display', 'none');

},100);



jQuery('ul[data-attribute_name="attribute_pa_device-condition"] li, ul[data-attribute_name="attribute_device-condition"] li').click(function() {
  jQuery('.woo-variation-items-wrapper').find('.append-condition').remove();
  var conditiontext = jQuery(this).attr('data-value').toLowerCase().replace(' ', '-');
  switch (conditiontext) {
    case 'flawless':
      var $text = '<ul class="dev-condition"><li>Device looks brand new.</li><li>Has absolutely no scratches, scuffs, or marks.</li><li>Battery health must be above 85% (86% or higher).</li><li>Absolutely no cracks or chips on the device.</li><li>Absolutely no screen burn, white dots, or other pixel damage.</li><li>Software is not modified or rooted.</li><li>The device is fully functional.</li></ul>';
      break;
    case 'good':
      var $text = '<ul class="dev-condition"><li>Device shows light signs of wear.</li><li>Contains few light scratches or marks.</li><li>Absolutely no cracks or chips on the device.</li><li>Absolutely no screen burn, white dots, or other pixel damage.</li><li>Software is not modified or rooted.</li><li>The device is fully functional.</li></ul>';
      break;
    case 'fair':
      var $text = '<ul class="dev-condition"><li>Device shows moderate to heavy signs of wear.</li><li>Contains a moderate to excessive amount of scratches, marks, or scuffs.</li><li>Contains dents or deep scratches.</li><li>Absolutely no cracks or chips on the device.</li><li>Absolutely no screen burn, white dots, or other pixel damage.</li><li>Software is not modified or rooted.</li><li>The device is fully functional.</li></ul>';
      break;
    case 'broken':
      var $text = '<ul class="dev-condition"><li>Cracked, chipped, or other damage to the screen, back glass, camera lens, frame or anywhere else on the device.</li><li>Screen burn, ghost image, white dots, or other pixel damage.</li><li>Bad port(s), bad battery, water damage, or any other hardware or software issues.</li><li>Broken Face/Touch ID or Biometrics.</li><li>Modified software or rooted.</li><li>All parts of the device must be included.</li></ul>';
      break;
    case 'no-power':
      var $text = '<ul class="dev-condition"><li>The device does not fully power on, charge, or show any signs of life.</li><li>All parts of the device must be included.</li></ul>';
      break;
  }
    jQuery(this).closest('.woo-variation-items-wrapper').append('<div class="append-condition" id="'+conditiontext+'">'+$text+'</div>');
});
jQuery('.reset_variations').click(function() {
     jQuery('.woo-variation-items-wrapper').find('.append-condition').remove();
});
  
// append account link on mobile
jQuery(document).find('#masthead .header-cart-mobile').prepend('<a class="header-cart-mobile-account" href="/my-account/"><i class="ekommart-icon-user"></i></a>');


if( window.location.pathname == "/")
{
  jQuery('.site-header-account').css('visibility','hidden');
  jQuery('.site-header-wishlist').css('visibility','hidden');
  jQuery('.site-header-cart').css('visibility','hidden');
}

jQuery('.header-container .aws-search-btn .aws-search-btn_icon').text('Search');
  
jQuery('#billing_paypal_email_field').append('<span style="color: #ff0000">Quickest Payment Option - Please note that there is a $0.30 + 2.9% fee to receive funds using PayPal. The fee will be deducted from the payout amount.</span>');
  
jQuery('#billing_venmo_no_field').append('<span style="color: #ff0000">Quickest Payment Option - No Fee!</span>');
  
//jQuery('#billing_check_name_field').append('<span style="color: #ff0000">Name on Check</span>'); 
  
jQuery('#billing_gift_card_email_field').append('<span style="color: #ff0000">Get up to a 10% bonus in additional funds. Redeem your funds to retailers such as Amazon, Target, Walmart, Starbucks, Nike, and over 300 leading digital gift card brands. After mailing in your device and order is completed, a link to our gift card portal will be emailed to you.</span>');  
  
jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field,#billing_venmo_no_field,#billing_venmo_no_confirm_field,#billing_check_name_field,#billing_gift_card_email_field,#billing_gift_card_email_confirm_field').find('span.optional').remove();   

/*show paypal required fields */
if(jQuery('body').hasClass('woocommerce-checkout')) {

  var totalAmountCheck = parseInt(jQuery('.order-total').find('.woocommerce-Price-amount.amount').text().replace('$', '').replace(',', ''));
  
  if(totalAmountCheck==750 || totalAmountCheck<750){
    //do nothing
  }
  else{
    jQuery("#billing_payment_options option[value='Venmo']").remove();
  }

jQuery('#billing_payment_options_PayPal').next('.radio').css('background-color', ''); 
jQuery('#billing_payment_options_Venmo').next('.radio').css('background-color', '');
jQuery('#billing_payment_options_GiftCard').next('.radio').css('background-color', ''); 
jQuery('#billing_payment_options_Check').next('.radio').css('background-color', ''); 

if(jQuery('input[name="billing_payment_options"]:checked').val() == 'PayPal') {    
    jQuery('#billing_payment_options_PayPal').next('.radio').css('background-color', 'lightgray'); 
    jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').show();   
    billingInputVlidationPaypal();      
} 
else {
  jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').hide();
  jQuery('#billing_paypal_email,#billing_paypal_email_confirm').val('');  
  billingInputVlidationPaypal();   
}

if(jQuery('input[name="billing_payment_options"]:checked').val() == 'Venmo') {
    jQuery('#billing_payment_options_Venmo').next('.radio').css('background-color', 'lightgray'); 
    jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').show();    
    billingInputVlidationVenmo();     
} 
else {
    jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').hide();
    jQuery('#billing_venmo_no,#billing_venmo_no_confirm').val('');   
    billingInputVlidationVenmo();  
}

if(jQuery('input[name="billing_payment_options"]:checked').val() == 'Gift Card') {
    jQuery('#billing_payment_options_GiftCard').next('.radio').css('background-color', 'lightgray'); 
    jQuery('#billing_gift_card_email_field,#billing_gift_card_email_confirm_field').show();  
    billingInputVlidationGiftcard();
} 
else {
  jQuery('#billing_gift_card_email_field,#billing_gift_card_email_confirm_field').hide();
  jQuery('#billing_gift_card_email,#billing_gift_card_email_confirm').val('');   
  billingInputVlidationGiftcard();
}

if(jQuery('input[name="billing_payment_options"]:checked').val() == 'Check') {
    jQuery('#billing_payment_options_Check').next('.radio').css('background-color', 'lightgray'); 
    jQuery('#billing_check_name_field').show();
    billingInputVlidationCheck();      
} 
else {
    jQuery('#billing_check_name_field').hide();
    jQuery('#billing_check_name').val('');  
    billingInputVlidationCheck(); 
}

var checkImage = 'https://smartphonesstg.wpengine.com/wp-content/uploads/2020/02/bank.png';
var paypalImage = 'https://smartphonesstg.wpengine.com/wp-content/uploads/2020/02/paypal.png';
var venmoImage = 'https://smartphonesstg.wpengine.com/wp-content/uploads/2020/02/finance.png';
var giftCardImage = 'https://smartphonesstg.wpengine.com/wp-content/uploads/2020/02/giftbox.png';

// Set images dynamically
jQuery('#billing_payment_options_Check').next('.radio').prepend('<img src="' + checkImage + '" alt="Check" class="radio-image check-radio-image" style="width:30px">');
jQuery('#billing_payment_options_PayPal').next('.radio').prepend('<img src="' + paypalImage + '" alt="PayPal" class="radio-image paypal-radio-image" style="width:30px">');
jQuery('#billing_payment_options_Venmo').next('.radio').prepend('<img src="' + venmoImage + '" alt="Venmo" class="radio-image venmo-radio-image" style="width:30px">');
jQuery('#billing_payment_options_GiftCard').next('.radio').prepend('<img src="' + giftCardImage + '" alt="Gift Card" class="radio-image giftcard-radio-image" style="width:30px">');


//jQuery('select[name="billing_payment_options"]').on('change', function() {
  //var selectVal = jQuery(this).find('option:selected').val();

jQuery('input[name="billing_payment_options"]').on('change', function() {

    var selectVal = jQuery(this).val();

    jQuery('#billing_payment_options_PayPal').next('.radio').css('background-color', ''); 
    jQuery('#billing_payment_options_Venmo').next('.radio').css('background-color', '');
    jQuery('#billing_payment_options_GiftCard').next('.radio').css('background-color', ''); 
    jQuery('#billing_payment_options_Check').next('.radio').css('background-color', ''); 
  
    if(selectVal == 'PayPal') {
        jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').show();  
        jQuery('#billing_payment_options_PayPal').next('.radio').css('background-color', 'lightgray');
        billingInputVlidationPaypal();            
    } 
    else {
        jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').hide();
        jQuery('#billing_paypal_email,#billing_paypal_email_confirm').val('');   
        billingInputVlidationPaypal();     
    }

    if(selectVal == 'Venmo') {
        jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').show(); 
        jQuery('#billing_payment_options_Venmo').next('.radio').css('background-color', 'lightgray');    
        billingInputVlidationVenmo();      
    } 
    else {
        jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').hide();
        jQuery('#billing_venmo_no,#billing_venmo_no_confirm').val('');  
        billingInputVlidationVenmo();  
    }  

    if(selectVal == 'GiftCard') {
        jQuery('#billing_gift_card_email_field,#billing_gift_card_email_confirm_field').show();   
        jQuery('#billing_payment_options_GiftCard').next('.radio').css('background-color', 'lightgray');  
        billingInputVlidationGiftcard();      
    } 
    else {
        jQuery('#billing_gift_card_email_field,#billing_gift_card_email_confirm_field').hide();
        jQuery('#billing_gift_card_email,#billing_gift_card_email_confirm').val('');  
        billingInputVlidationGiftcard(); 
    }  

    if(selectVal == 'Check') {
        jQuery('#billing_check_name_field').show();      
        jQuery('#billing_payment_options_Check').next('.radio').css('background-color', 'lightgray');  
        billingInputVlidationCheck();  
    } 
    else {
        jQuery('#billing_check_name_field').hide();
        jQuery('#billing_check_name').val('');  
        billingInputVlidationCheck();
    }   

});

jQuery("#billing_check_name_field").on("input", function() {
    billingInputVlidationCheck();
});
jQuery("#billing_venmo_no_field").on("input", function() {
    billingInputVlidationVenmo();
});
jQuery("#billing_venmo_no_confirm_field").on("input", function() {
    billingInputVlidationVenmo();
});
jQuery("#billing_gift_card_email_field").on("input", function() {
    billingInputVlidationGiftcard();
});
jQuery("#billing_gift_card_email_confirm_field").on("input", function() {
    billingInputVlidationGiftcard();
});
jQuery("#billing_paypal_email_field").on("input", function() {
    billingInputVlidationPaypal();
});
jQuery("#billing_paypal_email_confirm_field").on("input", function() {
    billingInputVlidationPaypal();
});

var totalAmuont = parseInt(jQuery('.order-total').find('.woocommerce-Price-amount.amount').text().replace('$', ''));
if(totalAmuont < 15 || jQuery(document).find('span[data-category-checkout="true"]').hasClass('remove-fields')) {
  jQuery('#additional_box_field').hide();
} else {
  jQuery('#additional_box_field').show();
}

}
/*show paypal required fields end */
  
jQuery(window).scroll(function(){
  var sticky = jQuery('.header-sticky'),
      scroll = jQuery(window).scrollTop();

  if (scroll >= 102) sticky.addClass('active');
  else sticky.removeClass('active');
}); 
  
jQuery('#mobileSearchIcon').on('click', function(){
  jQuery(".header-container .header-bottom").slideToggle();
})  


function billingInputVlidationPaypal() {

    if (jQuery('#billing_paypal_email').val() === '') {
        jQuery('#billing_paypal_email').css('box-shadow', 'inset 2px 0 0 #e2401c');
    } else {
        jQuery('#billing_paypal_email').css('box-shadow', '');
    }

    if (jQuery('#billing_paypal_email_confirm').val() === '') {
        jQuery('#billing_paypal_email_confirm').css('box-shadow', 'inset 2px 0 0 #e2401c');
    } else {
        jQuery('#billing_paypal_email_confirm').css('box-shadow', '');
    }  
}

function billingInputVlidationVenmo() {

    if (jQuery('#billing_venmo_no').val() === '') {
        jQuery('#billing_venmo_no').css('box-shadow', 'inset 2px 0 0 #e2401c');
    } else {
        jQuery('#billing_venmo_no').css('box-shadow', '');
    }

    if (jQuery('#billing_venmo_no_confirm').val() === '') {
        jQuery('#billing_venmo_no_confirm').css('box-shadow', 'inset 2px 0 0 #e2401c');
    } else {
        jQuery('#billing_venmo_no_confirm').css('box-shadow', '');
    }
}

function billingInputVlidationGiftcard() {

    if (jQuery('#billing_gift_card_email').val() === '') {
        jQuery('#billing_gift_card_email').css('box-shadow', 'inset 2px 0 0 #e2401c');
    } else {
        jQuery('#billing_gift_card_email').css('box-shadow', '');
    }

    if (jQuery('#billing_gift_card_email_confirm').val() === '') {
        jQuery('#billing_gift_card_email_confirm').css('box-shadow', 'inset 2px 0 0 #e2401c');
    } else {
        jQuery('#billing_gift_card_email_confirm').css('box-shadow', '');
    }

}

function billingInputVlidationCheck() {

    if (jQuery('#billing_check_name').val() === '') {
        jQuery('#billing_check_name').css('box-shadow', 'inset 2px 0 0 #e2401c');
    } else {
        jQuery('#billing_check_name').css('box-shadow', '');
    }
}
    
</script>
    <?php
    }
add_action('wp_footer', 'footer_js');


function noindex_for_payouts()
{
    if ( is_singular( 'payouts' ) ) {
        echo '<meta name="robots" content="noindex, follow">';
    }
    if ( is_singular('product') ) { ?>
        <script>!function(e,t,s,a,p,n){e.swp||((a=e.swp=function(){a.process?a.process.apply(a,arguments):a.queue.push(arguments)}).queue=[],a.t=+new Date,(p=t.createElement(s)).async=1,p.src="https://static.swappa.com/static/tr/swappapixel.min.js?t="+864e5*Math.ceil(new Date/864e5),(n=t.getElementsByTagName(s)[0]).parentNode.insertBefore(p,n))}(window,document,"script"),swp("event","page_load");</script>
        <?php 
    }
}

add_action('wp_head', 'noindex_for_payouts');

add_action( 'template_redirect', 'wpse_128636_redirect_post' );

function wpse_128636_redirect_post() {
  if ( is_singular( 'payouts' ) ) {
    wp_redirect( home_url(), 301 );
    exit;
  }
}
//used for order status code

//add_action( 'woocommerce_admin_order_data_after_order_details', 'action_woocommerce_order_note' );
//add_action( 'init', 'action_woocommerce_order_note' );
   if( !function_exists('action_woocommerce_order_note')) { 
        function action_woocommerce_order_note()
        { 
            
            
           // echo $current_date_time = current_datetime()->format('Y-m-d H:i:s');
           
           
             global $woocommerce, $post,$pagenow,$wpdb;
             
             $result_hour_date = $wpdb->get_results("SELECT NOW() as 'currentTime',DATE_SUB(NOW(), INTERVAL 6 HOUR) as 'date_after_6'");
             $new_time = date("Y-m-d H:i:s", strtotime('-6 hours'));
           
             $final_date = date('Y-m-d', strtotime($initial_date. ' - 7 days'));
             
//             $ordersArray = wc_get_orders(array(
//            'limit'=>50,
//            'type'=> 'shop_order',
//            'date_created'=> $initial_date .'<='. $final_date ,
//            'date_created'=> $new_time .'<'. date("Y-m-d H:i:s")
//            )
//            );(TIMESTAMPDIFF(HOUR,'".$result_hour_date[0]->date_after_6."','".$result_hour_date[0]->currentTime."')) >= 6
             $query = "SELECT ID "
                     . " FROM `wp_posts` WHERE "
                     . "`post_type` = 'shop_order' AND `post_status` NOT IN ('trash') and "
                     . "(`post_date` >= '".$final_date."' and `post_date` <= '".date('Y-m-d', strtotime($initial_date. ' + 1 days'))."')"
                     . " order by ID DESC";
             $ordersArray = $wpdb->get_results($query);
           //  echo $query."...hi.test..".$initial_date."...".$final_date."..".$new_time."..".date("Y-m-d H:i:s")."..<pre>";print_r($ordersArray);echo "</pre>"; die();
             foreach($ordersArray as $orderRow){
             
        //     $arrayStatusConsider = ["pending","no_box_required","processing","box_required","not_received","on-hold"];
       
             $order_info = new WC_Order($orderRow->ID);
             
             //$order_info->save();
             $noteInfo = wc_get_order_notes(['order_id' => $orderRow->ID],true);
            if(count($noteInfo)>0){
                foreach($noteInfo as $rowContent){
                    $noteContentArray[] = trim($rowContent->content);
                }
            }
            
             $tracking_items = get_post_meta( $orderRow->ID, '_wc_shipment_tracking_items', true );
          //   if(in_array($order_info->status, $arrayStatusConsider)){
                if(isset($tracking_items[0]['tracking_number'])){
                $shippingInfo = getShippingApiNote($tracking_items[0]['tracking_number']);
                
//                echo "<pre>";
//                print_r($shippingInfo['order_status']);
//                echo "</pre>";die();
                // = new WC_Order("126355"); 
                if(isset($shippingInfo['shipping_info']['TrackInfo']['TrackSummary'])){
                    $trackSummary = $shippingInfo['shipping_info']['TrackInfo']['TrackSummary'];
                    $noteCustom = "USPS</br>".$tracking_items[0]['tracking_number']."</br><b>".$shippingInfo['order_status']."</b></br>".$trackSummary;
                    if(!in_array($noteCustom, $noteContentArray)){
                        $order_info->add_order_note($noteCustom);
                    }
                }
            }
         //    }
           $shipping_status = isset($shippingInfo['order_status']) ? $shippingInfo['order_status']:'Undelivered';
           $order_info->update_meta_data("api_shipping_status",$shipping_status);
           $order_info->save();
             }
 

        }
    }
    
if( !function_exists('getShippingApiNote')) {
function getShippingApiNote($tracking_id){
//$tracking_id = '9201990247032300179608'; //success 
//$tracking_id = '9201990247032300172401'; //success
//$tracking_id = '9202090247032300193587'; //pre shipment
$orderStatuses = '[{"status":"Delivered","keyword":"Your item was delivered at"},{"status":"Delivered","keyword":"Your item was delivered in"},{"status":"Delivered","keyword":"Your item was delivered at"},{"status":"Delivered","keyword":"Your item was delivered to"},{"status":"Delivered","keyword":"Your item was delivered to"},{"status":"Delivered","keyword":"Your item was delivered to"},{"status":"Delivered","keyword":"Your item was delivered to"},{"status":"Delivered","keyword":"Your item was delivered to"},{"status":"Delivered","keyword":"Your item has been delivered and is available at "},{"status":"Delivered","keyword":"Your item was picked up at a postal facility"},{"status":"Delivered","keyword":"Your item was picked up at the post office"},{"status":"Delivered","keyword":"Your item was delivered to"},{"status":"Delivered","keyword":"Your item has been delivered to an agent"},{"status":"Delivered","keyword":"Your item has been delivered to the original"},{"status":"Delivered","keyword":"Your item was delivered at"},{"status":"Delivery Attempt: Action Needed","keyword":"We attempted to deliver your item at"},{"status":"Delivery Attempt: Action Needed or Delivery Attempt","keyword":"We attempted to deliver your item at"},{"status":"Delivery Attempt: Action Needed or Delivery Attempt","keyword":"We attempted to deliver your item at"},{"status":"Delivery Attempt: Action Needed or Delivery Attempt","keyword":"We attempted to deliver your item at"},{"status":"Delivery Attempt: Action Needed or Delivery Attempt","keyword":"returned to sender"},{"status":"Accepted","keyword":"USPS has received electronic notification"},{"status":"Alert","keyword":"Your item was refused"},{"status":"Alert","keyword":"Your item was forwarded to a different address"},{"status":"Alert","keyword":"The forward on your item"},{"status":"In Transit","keyword":"Your item arrived at the Post Office"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item could not be delivered"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"The return on your item was processed"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"Alert","keyword":"Please contact the US"},{"status":"Alert","keyword":"Please contact the US"},{"status":"n\/a","keyword":"n\/a"},{"status":"Available for Pickup","keyword":"ready for pickup."},{"status":"Available for Pickup","keyword":"You can inquire about the status"},{"status":"Available for Agent Pickup","keyword":"and is ready for pickup by the agent"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"Available for Agent Pickup","keyword":"Your item is available for pickup"},{"status":"Delivered to Agent","keyword":"Your item was picked up by the shipping agent"},{"status":"Alert","keyword":"We attempted to deliver your package at"},{"status":"Alert","keyword":"Your item was processed through"},{"status":"Alert","keyword":"Your item could not be delivered"},{"status":"Alert","keyword":"oversize or overweight"},{"status":"Alert","keyword":"Your item could not be delivered"},{"status":"Alert","keyword":"Your item has been received by"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"Delivered","keyword":"Your shipment was received at"},{"status":"Delivered to Agent \/ In Transit","keyword":"Your item has been tendered to the returns agent"},{"status":"Delivered","keyword":"Your item was delivered at"},{"status":"Alert","keyword":"This item has been intercepted"},{"status":"Alert","keyword":"Your item is not mailable"},{"status":"Alert","keyword":"Your item is not mailable"},{"status":"In Transit","keyword":"Your item has been tendered to a military agent"},{"status":"n\/a","keyword":"n\/a"},{"status":"Delivery Attempt","keyword":"We attempted to deliver your package at"},{"status":"Delivery Attempt","keyword":"This is at the request of the customer"},{"status":"Alert","keyword":"We were unable to attempt delivery"},{"status":"Available for Pickup","keyword":"This is at the request of the customer"},{"status":"Available for Pickup","keyword":"The item was removed from a full parcel locker.  "},{"status":"Out for Delivery","keyword":"Your item is out for delivery"},{"status":"Delivered to Agent","keyword":"Your item has been delivered to a agent for final delivery agent"},{"status":"Delivered to Agent","keyword":"Your item has been delivered to the college"},{"status":"Alert","keyword":"Your item was returned"},{"status":"Alert","keyword":"We apologize we are unable"},{"status":"Alert","keyword":"We were unable to attempt delivery"},{"status":"Delivery Attempt: Action Needed or Delivery Attempt","keyword":" no shelf barcode"},{"status":"Alert","keyword":"NA"},{"status":"Available for Pickup","keyword":"extraordinary circumstances"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"Accepted","keyword":"Your item has been accepted"},{"status":"In Transit","keyword":"facility on"},{"status":"n\/a","keyword":"n\/a"},{"status":"In Transit","keyword":"processed through"},{"status":"In Transit","keyword":"Your item was received by the U.S."},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"Alert","keyword":"We apologize we are unable"},{"status":"In Transit","keyword":"The item is currently in transit to the destination "},{"status":"In Transit","keyword":"The item is currently in transit to the destination"},{"status":"Pre-Shipment","keyword":" associated a return receipt to your item"},{"status":"In Transit","keyword":"Your item departed our USPS facility in ZIP Code"},{"status":"Delivered","keyword":"Postal Service anticipates"},{"status":"Alert","keyword":"Postal Service has identified"},{"status":"Pre-Shipment","keyword":"electronically notified by the shippe"},{"status":"Delivered to Agent","keyword":"processed by the shipping agent"},{"status":"n\/a","keyword":"n\/a"},{"status":"n\/a","keyword":"n\/a"},{"status":"In Transit","keyword":" transit to the next facility"},{"status":"In Transit","keyword":" transit to the next facility"},{"status":"In Transit","keyword":" transit to the next facility"},{"status":"Accepted","keyword":"Your item has been accepted"},{"status":"In Transit","keyword":"facility on"},{"status":"Out for Delivery","keyword":"Your item is out for delivery"},{"status":"Alert","keyword":"emergency or other conditions"},{"status":"In Transit","keyword":"N\/A"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"In Transit","keyword":"facility on"},{"status":"In Transit","keyword":"The item is currently in transit to the destination"},{"status":"N\/A","keyword":"N\/A"},{"status":"Accepted","keyword":"The acceptance"},{"status":"Alert","keyword":"delayed due to transportation problems"},{"status":"Accepted","keyword":"Your item has been accepted"},{"status":"In Transit","keyword":"Your item arrived at our USPS facility"},{"status":"n\/a","keyword":"n\/a"},{"status":"Delivery Attempt or Delivery Attempt: Action Needed","keyword":"arrange for redelivery"},{"status":"n\/a","keyword":"n\/a"},{"status":"n\/a","keyword":"n\/a"},{"status":"Alert","keyword":"Missing Mail Search Request"},{"status":"Alert","keyword":"Missing Mail Search Request"},{"status":"Alert","keyword":"Missing Mail Search Request"},{"status":"Alert","keyword":"Missing Mail Search Request"},{"status":"Pre-Shipment","keyword":"n\/a"},{"status":"In Transit","keyword":" Postal Service redeliver"},{"status":"n\/a","keyword":"n\/a"},{"status":"n\/a","keyword":"n\/a"},{"status":"n\/a","keyword":"n\/a"},{"status":"Alert","keyword":"delayed due to weather conditions"},{"status":"Delivered to Agent","keyword":"received by the agent"},{"status":"Delivered to Agent","keyword":"received by the agent"},{"status":"Delivered","keyword":"Your item was delivered to"},{"status":"Alert","keyword":"undeliverable to recipient"},{"status":"n\/a","keyword":"n\/a"},{"status":"On its Way to USPS","keyword":"shipping label has been prepared "},{"status":"On its Way to USPS","keyword":"arrived at a shipping partner facility"},{"status":"On its Way to USPS","keyword":" departed a shipping partner facility"},{"status":"Accepted","keyword":"Postal Service by a shipping partner"},{"status":"Pre-Shipment","keyword":"received by the merchant"},{"status":"Delivered to Agent","keyword":"arrived at an agent facility"},{"status":"Delivered to Agent","keyword":"departed an agent facility "},{"status":"Delivered to Agent","keyword":"delivered by an agent"},{"status":"Delivered to Agent","keyword":" received its final disposition"}]';
$statusArr = json_decode($orderStatuses, true);

$path = "https://secure.shippingapis.com/ShippingAPI.dll?API=TrackV2&XML=%3CTrackRequest%20USERID=%22935SMART5341%22%3E%3CTrackID%20ID=%22".$tracking_id."%22%3E%3C/TrackID%3E%3C/TrackRequest%3E";

$result = file_get_contents($path);
if (!empty($result)) {
	$xmlResponse = simplexml_load_string($result);
	if (is_object($xmlResponse)){
		$jsonResponse = json_encode($xmlResponse);
		$response = json_decode($jsonResponse,true);
		$orderStatus = '';
		if (is_array($response) && isset($response['TrackInfo']) && isset($response['TrackInfo']['TrackSummary'])) {
			foreach ($statusArr as $status) {
				if (strpos($response['TrackInfo']['TrackSummary'], $status['keyword']) !== false) {
					$orderStatus = $status['status'];
					break;
				}
			}
		}
                $resultArray = array("order_status"=>$orderStatus,"shipping_info"=>$response);
		//echo "order_status-->".$orderStatus;
		//echo '<pre>'; print_r($response);
                return $resultArray;
	}
}
}
}
// update shipping status
if( !function_exists('add_order_new_column_api_shipping_status')) {
function add_order_new_column_api_shipping_status( $columns ) {

    $new_columns = array();

    foreach ( $columns as $column_name => $column_info ) {

        $new_columns[ $column_name ] = $column_info;

        if ( 'order_total' === $column_name ) {
            $new_columns['api_shipping_status'] = __( 'Shipping Status', 'api_shipping_status' );
        }
    }

    return $new_columns;
}
}

add_filter( 'manage_edit-shop_order_columns', 'add_order_new_column_api_shipping_status',6);

add_action( 'manage_shop_order_posts_custom_column', 'add_wc_order_admin_list_column_content' );
if( !function_exists('add_wc_order_admin_list_column_content')) {
function add_wc_order_admin_list_column_content( $column ) {
  
    global $post;

    if ( 'api_shipping_status' == $column ) {
 
       $orderInfo = new WC_Order($post->ID);
       
       $tracking_items = $orderInfo->get_meta('api_shipping_status');
echo !empty($tracking_items)? "<b>".$tracking_items."</b>":'<b>Undelivered</b>';

// Loop through order line items

       // echo $post->ID;
      
    }
}
}
if(!function_exists('custom_order_filters'))
{
    function custom_order_filters( $post_type ) {


// Check if filter has been applied already so we can adjust the input element accordingly
    $statusArray = ["Delivered","Delivery Attempt: Action Needed","Delivery Attempt: Action Needed or Delivery Attempt","Accepted","Alert","In Transit","n/a","Available for Pickup","Available for Agent Pickup","Delivered to Agent","Delivered to Agent / In Transit","Delivery Attempt","Out for Delivery","Pre-Shipment","N/A","Delivery Attempt or Delivery Attempt: Action Needed","On its Way to USPS"];


// Check this is the products screen
if( $post_type == 'shop_order' ) {

  // Add your filter input here. Make sure the input name matches the $_GET value you are checking above.
 // echo '<input type="text" id="api_shipping_status" name="api_shipping_status" value="'.@$_GET['api_shipping_status'].'" placeholder = "Filter Shipping Status" >';
   if('Undelivered' == @$_GET['api_shipping_status']){
      $selectStr = "selected";    
      }else {
          $selectStr = "";
      }
  echo '<select name = "api_shipping_status" class="">';
  echo '<option value="">Filter Shipping Status</option>';
  //Undelivered
   echo '<option value="Undelivered" '.$selectStr.'>Undelivered</option>';
  foreach($statusArray as $rowStatus){
      if($rowStatus == @$_GET['api_shipping_status']){
      $selectStr = "selected";    
      }else {
          $selectStr = "";
      }
      
      echo '<option value="'.$rowStatus.'" '.$selectStr.'>'.$rowStatus.'</option>';
  }
  echo "</select>";

}

}


}
add_action( 'restrict_manage_posts', 'custom_order_filters','shop_order' );
if( !function_exists('apply_custom_order_filters')) {
function apply_custom_order_filters( $query ) {
global $pagenow;

// Ensure it is an edit.php admin page, the filter exists and has a value, and that it's the products page
if ( $query->is_admin && $pagenow == 'edit.php' && isset( $_GET['api_shipping_status'] ) && $_GET['api_shipping_status'] != '' && $_GET['post_type'] == 'shop_order' ) {

  // Create meta query array and add to WP_Query
  $meta_key_query = array(
    array(
      'key'     => 'api_shipping_status',
      'value'   => esc_attr( $_GET['api_shipping_status'] ),
    )
  );
  $query->set( 'meta_query', $meta_key_query );

}

}
}

add_action( 'pre_get_posts', 'apply_custom_order_filters' );

/*Cron code to run on every five minutes */
add_filter( 'cron_schedules', 'isa_add_every_five_minutes' );
function isa_add_every_five_minutes( $schedules ) {
    $schedules['every_five_minutes'] = array(
            'interval'  => 3600,
            'display'   => __( 'Update Every 60 Minutes', 'textdomain' )
    );
    return $schedules;
}

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'isa_add_every_five_minutes' ) ) {
    wp_schedule_event( time(), 'every_five_minutes', 'isa_add_every_five_minutes' );
}

// Hook into that action that'll fire every three minutes
add_action( 'isa_add_every_five_minutes', 'every_five_minutes_event_func' );
function every_five_minutes_event_func() {
    // do something
 //   global $wpdb;
 // $wpdb->query("INSERT INTO `wp_check_cronjob` (`val_key`, `creatdate`) VALUES ('test', CURRENT_TIME())");
   action_woocommerce_order_note();
}

add_action( 'pre_get_posts', 'apply_custom_order_filters' );

//bar code generator for woo commerece invoice and label generator.
add_filter('wf_pklist_alter_barcode_data','wf_pklist_alter_barcode_data_fn',10,3);
function wf_pklist_alter_barcode_data_fn($invoice_number, $template_type, $order)
{
	return $order->get_order_number();
}

add_action( 'admin_menu', 'smartphones_add_admin_menu_for_order_export' );
function smartphones_add_admin_menu_for_order_export() {
    add_menu_page( __( 'Products Export', 'smartphones' ), 'Products Export', 'nosuchcapability', 'smartphones-products-export', null, 'dashicons-chart-bar', 6 );
    add_submenu_page( 'smartphones-products-export', 'Products Export', 'Products Export', 'manage_options', 'products-export', 'variants_exports' );
    
}
function variants_exports(){ 
  echo 'https://www.smartphonesplus.com/wp-content/uploads/csv/variantData.csv'; die;
  //Samsung = 642,640,639,641,88,113,111;
  //google = 104,665;
  //ipad = 117,115,116,114
  //ipod = 97,134,135,133
  // die;
  $prod_categories = array(16,131,132,136,110,386,385,384,383,382,381,526,527,645,661,662,663,642,640,639,641,88,113,111,104,665,117,115,116,114,97,134,135,133);
  //$prod_categories = array(97);
  $args = array(
    'post_type' => 'product',
    'numberposts' => -1,
    'tax_query'             => array(
      array(
          'taxonomy'      => 'product_cat',
          'field'         => 'id', //This is optional, as it defaults to 'term_id'
          'terms'         => $prod_categories,
          'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
      )
    )
  );
  $products = get_posts( $args );
  $array_data = $array_data1 = [];
  $all_childs = [];
  $final=[];
  $_product_title = '';
  $i=0;
  
  foreach($products as $product):   //echo '<pre>'; print_r($product);
      $child = new WC_Product_Variable($product->ID);
      $available_variations = $child->get_available_variations(); 
      if(strpos($product->post_name, "iphone") !== false){
        $_product_title = 'apple-'.$product->post_name;
      }elseif (strpos($product->post_name, "ipad") !== false) {
        $_product_title = 'apple-'.$product->post_name;
      }elseif (strpos($product->post_name, "ipod") !== false) {
        $_product_title = 'apple-'.$product->post_name;
      }else{
        $_product_title = $product->post_name;
      }
      foreach ($available_variations as $key => $value): 
        if($value['attributes']['attribute_pa_device-condition']!='no-power'){
           $all_childs[$i]['product_id'] = $product->ID;
           $all_childs[$i]['product_title'] = $_product_title;
           $all_childs[$i]['url'] = get_permalink($product->ID);
           $all_childs[$i]['carrier'] = $value['attributes']['attribute_pa_carrier'];
           $all_childs[$i]['memory'] = (isset($value['attributes']['attribute_pa_ram'])) ? str_replace("-", "", $value['attributes']['attribute_pa_ram']) : '';
           $all_childs[$i]['storage'] = (isset($value['attributes']['attribute_pa_storage-capacity'])) ? str_replace("-", "", $value['attributes']['attribute_pa_storage-capacity']) : '';
           $all_childs[$i]['processor'] = $value['attributes']['attribute_pa_processor'];
           $all_childs[$i]['condition'] = $value['attributes']['attribute_pa_device-condition'];
           $all_childs[$i]['charger-included'] = $value['attributes']['attribute_pa_is-the-charger-included'];
           $all_childs[$i]['price'] = $value['display_price'];
           $i++;
        }
      endforeach;
  endforeach;
  $variants = [];
  $variants1 = [];
  $array_search_check = [];
    //echo '<pre>'; print_r($all_childs); 
    //die('END');
  foreach ($all_childs as $k => $val) : 
        if(!empty($val['carrier']) && !empty($val['storage'])){ 
          if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
            && array_search($val['carrier'], array_column($variants, 'carrier')) !== FALSE 
            && array_search($val['storage'], array_column($variants, 'storage')) !== FALSE):

            $array_search_check = multi_array_search($variants, array('carrier' => $val['carrier'], 'storage' => $val['storage'], 'product_id' => $val['product_id']));

              if(empty($array_search_check)){ 
                $variants[] = array(
                      'product_id'=>$val['product_id'],
                      'product_title'=>$val['product_title'],
                      'carrier'=>$val['carrier'],
                      'memory'=>$val['memory'],
                      'storage'=>$val['storage'],
                      'processor'=>$val['processor'],
                      'price_new'=>'',
                      $val['condition']=> $val['price']
                );
              }else{
                  $getKey = $array_search_check[0];
                  $variants[$getKey][$val["condition"]] = $val['price'];
              }
          else: 
              $variants[] = array(
                  'product_id'=>$val['product_id'],
                  'product_title'=>$val['product_title'],
                  'carrier'=>$val['carrier'],
                  'memory'=>$val['memory'],
                  'storage'=>$val['storage'],
                  'processor'=>$val['processor'],
                  'price_new'=>'',
                  $val['condition']=> $val['price']
                );
             endif;
        }
        if(!empty($val['carrier']) && empty($val['storage'])){ 
          if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
            && array_search($val['carrier'], array_column($variants, 'carrier')) !== FALSE ):
            $array_search_check = multi_array_search($variants, array('carrier' => $val['carrier'], 'product_id' => $val['product_id']));
            
              if(empty($array_search_check)){ 
                $variants[] = array(
                    'product_id'=>$val['product_id'],
                    'product_title'=>$val['product_title'],
                    'carrier'=>$val['carrier'],
                    'memory'=>$val['memory'],
                    'storage'=>$val['storage'],
                    'processor'=>$val['processor'],
                    'price_new'=>'',
                    $val['condition']=> $val['price']
                );
              }else{
                  $getKey = $array_search_check[0];
                  $variants[$getKey][$val["condition"]] = $val['price'];
              }
            else: 
              if(!empty($val['condition'])){
                  $variants[] = array(
                      'product_id'=>$val['product_id'],
                      'product_title'=>$val['product_title'],
                      'carrier'=>$val['carrier'],
                      'memory'=>$val['memory'],
                      'storage'=>$val['storage'],
                      'processor'=>$val['processor'],
                      'price_new'=>'',
                      $val['condition']=> $val['price']
                  );
              }
              
          endif;
        }
        if(empty($val['carrier']) && empty($val['condition']) && !empty($val['storage'])){ 
          if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
            && array_search($val['storage'], array_column($variants, 'storage')) !== FALSE ):
            $array_search_check = multi_array_search($variants, array('storage' => $val['storage'], 'product_id' => $val['product_id'])); 
              if(empty($array_search_check)){ 
                $variants[] = array(
                    'product_id'=>$val['product_id'],
                    'product_title'=>$val['product_title'],
                    'carrier'=>$val['carrier'],
                    'memory'=>$val['memory'],
                    'storage'=>$val['storage'],
                    'processor'=>$val['processor'],
                    'price_new'=>'',
                    $val['condition']=> $val['price']
                );
              }else{ 
                  $getKey = $array_search_check[0];
                  $variants[$getKey][$val["condition"]] = $val['price'];
              }
            else:  
              $variants[] = array(
                  'product_id'=>$val['product_id'],
                  'product_title'=>$val['product_title'],
                  'carrier'=>$val['carrier'],
                  'memory'=>$val['memory'],
                  'storage'=>$val['storage'],
                  'processor'=>$val['processor'],
                  'price_new'=>'',
                  $val['condition']=> $val['price']
              );
          endif;
        }
        if(!empty($val['memory']) && !empty($val['processor']) && !empty($val['charger-included'])){
         
          if(isset($val['charger-included']) && $val['charger-included']=='yes'){
              if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
              && array_search($val['memory'], array_column($variants, 'memory')) !== FALSE 
              && array_search($val['processor'], array_column($variants, 'processor')) !== FALSE):

              $array_search_check = multi_array_search($variants, array('memory' => $val['memory'], 'processor' => $val['processor'], 'product_id' => $val['product_id']));

                if(empty($array_search_check)){ 
                  $variants[] = array(
                      'product_id'=>$val['product_id'],
                      'product_title'=>$val['product_title'],
                      'carrier'=>$val['carrier'],
                      'memory'=>$val['memory'],
                      'storage'=>$val['storage'],
                      'processor'=>$val['processor'],
                      'price_new'=>'',
                      $val['condition']=> $val['price']
                  );
                  
                }else{
                    $getKey = $array_search_check[0];
                    
                    $variants[$getKey][$val["condition"]] = $val['price'];
                }
            else: 
              $variants[] = array(
                  'product_id'=>$val['product_id'],
                  'product_title'=>$val['product_title'],
                  'carrier'=>$val['carrier'],
                  'memory'=>$val['memory'],
                  'storage'=>$val['storage'],
                  'processor'=>$val['processor'],
                  'price_new'=>'',
                  $val['condition']=> $val['price']
                );
                
            endif;
          }
          
        }
        if(!empty($val['memory']) && !empty($val['processor']) && empty($val['charger-included'])){
          if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
          && array_search($val['memory'], array_column($variants, 'memory')) !== FALSE 
          && array_search($val['processor'], array_column($variants, 'processor')) !== FALSE):

          $array_search_check = multi_array_search($variants, array('memory' => $val['memory'], 'processor' => $val['processor'], 'product_id' => $val['product_id']));

            if(empty($array_search_check)){ 
              $variants[] = array(
                  'product_id'=>$val['product_id'],
                  'product_title'=>$val['product_title'],
                  'carrier'=>$val['carrier'],
                  'memory'=>$val['memory'],
                  'storage'=>$val['storage'],
                  'processor'=>$val['processor'],
                  'price_new'=>'',
                  $val['condition']=> $val['price']
              );
              
            }else{
                $getKey = $array_search_check[0];
                
                $variants[$getKey][$val["condition"]] = $val['price'];
            }
          else: 
            $variants[] = array(
                'product_id'=>$val['product_id'],
                'product_title'=>$val['product_title'],
                'carrier'=>$val['carrier'],
                'memory'=>$val['memory'],
                'storage'=>$val['storage'],
                'processor'=>$val['processor'],
                'price_new'=>'',
                $val['condition']=> $val['price']
              );
            endif;
        }
        if(!empty($val['condition']) && !empty($val['charger-included']) && empty($val['carrier']) && empty($val['memory']) && empty($val['storage'])){ 
         
          if(isset($val['charger-included']) && $val['charger-included']=='yes'){
              if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
              && array_search($val['condition'], array_column($variants, 'condition')) !== FALSE ):

              $array_search_check = multi_array_search($variants, array('condition' => $val['condition'], 'product_id' => $val['product_id']));

                if(empty($array_search_check)){ 
                  $variants[] = array(
                      'product_id'=>$val['product_id'],
                      'product_title'=>$val['product_title'],
                      'carrier'=>$val['carrier'],
                      'memory'=>$val['memory'],
                      'storage'=>$val['storage'],
                      'processor'=>$val['processor'],
                      'price_new'=>'',
                      $val['condition']=> $val['price']
                  );
                  
                }else{
                    $getKey = $array_search_check[0];
                    
                    $variants[$getKey][$val["condition"]] = $val['price'];
                }
            else: 
              $variants[] = array(
                  'product_id'=>$val['product_id'],
                  'product_title'=>$val['product_title'],
                  'carrier'=>$val['carrier'],
                  'memory'=>$val['memory'],
                  'storage'=>$val['storage'],
                  'processor'=>$val['processor'],
                  'price_new'=>'',
                  $val['condition']=> $val['price']
                );
                
            endif;
          }
          
        }
        if(!empty($val['condition']) && empty($val['carrier']) && empty($val['memory']) && empty($val['storage']) && empty($val['processor']) && empty($val['charger-included'])){ 
            if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
              && array_search($val['condition'], array_column($variants, 'condition')) !== FALSE ):

              $array_search_check = multi_array_search($variants, array('condition' => $val['condition'], 'product_id' => $val['product_id']));

                if(empty($array_search_check)){ 
                  $variants[] = array(
                      'product_id'=>$val['product_id'],
                      'product_title'=>$val['product_title'],
                      'carrier'=>$val['carrier'],
                      'memory'=>$val['memory'],
                      'storage'=>$val['storage'],
                      'processor'=>$val['processor'],
                      'price_new'=>'',
                      $val['condition']=> $val['price']
                  );
                  
                }else{
                    $getKey = $array_search_check[0];
                    
                    $variants[$getKey][$val["condition"]] = $val['price'];
                }
            else: 
              $variants[] = array(
                  'product_id'=>$val['product_id'],
                  'product_title'=>$val['product_title'],
                  'carrier'=>$val['carrier'],
                  'memory'=>$val['memory'],
                  'storage'=>$val['storage'],
                  'processor'=>$val['processor'],
                  'price_new'=>'',
                  $val['condition']=> $val['price']
                );
                
            endif;
        }
        if(!empty($val['condition']) && !empty($val['storage']) && empty($val['carrier'])){
          if (array_search($val['product_id'], array_column($variants, 'product_id')) !== FALSE 
            && array_search($val['storage'], array_column($variants, 'storage')) !== FALSE):

            $array_search_check = multi_array_search($variants, array('storage' => $val['storage'], 'product_id' => $val['product_id']));

              if(empty($array_search_check)){ 
                $variants[] = array(
                      'product_id'=>$val['product_id'],
                      'product_title'=>$val['product_title'],
                      'carrier'=>$val['carrier'],
                      'memory'=>$val['memory'],
                      'storage'=>$val['storage'],
                      'processor'=>$val['processor'],
                      'price_new'=>'',
                      $val['condition']=> $val['price']
                );
              }else{
                  $getKey = $array_search_check[0];
                  $variants[$getKey][$val["condition"]] = $val['price'];
              }
          else: 
              $variants[] = array(
                  'product_id'=>$val['product_id'],
                  'product_title'=>$val['product_title'],
                  'carrier'=>$val['carrier'],
                  'memory'=>$val['memory'],
                  'storage'=>$val['storage'],
                  'processor'=>$val['processor'],
                  'price_new'=>'',
                  $val['condition']=> $val['price']
                );
             endif;
        }
        //echo '<pre>'; print_r($variants); die('END1');   
  endforeach;

  //echo '<pre>'; print_r($variants); die('END1');

     $file_dir = WP_CONTENT_DIR . '/uploads/csv/';
     $filename = $file_dir.'variantData.csv';
     $fp = fopen($filename,"w");
     fputcsv($fp,array('partner_ref','product','carrier','memory','storage','processor','price_new','price_mint','price_good','price_fair','price_broken','url'));
    foreach ($variants as $list) {
      if(array_key_exists('flawless',$list) == false){
        $list['flawless']='';
      }
      if(array_key_exists('good',$list) == false){
        $list['good']='';
      }
      if(array_key_exists('fair',$list) == false){
        $list['fair']='';
      }
      if(array_key_exists('broken',$list) == false){
        $list['broken']='';
      }
      $list['url']=get_permalink($list['product_id']);
        fputcsv($fp, $list);
    }
    fclose($fp);

}

function multi_array_search($array, $search)
{
    // Create the result array
    $result = array();
    // Iterate over each array element
    foreach ($array as $key => $value)
      {
        // Iterate over each search condition
        foreach ($search as $k => $v)
        {
          // If the array element does not meet the search condition then continue to the next element
          if (!isset($value[$k]) || $value[$k] != $v)
          {
            continue 2;
          }
        }
        // Add the array element's key to the result array
        $result[] = $key;
      }
    // Return the result array
     return $result;
  }

  function update_variations_number(){
    return 150;
  }
  add_filter('woocommerce_admin_meta_boxes_variations_per_page', 'update_variations_number');