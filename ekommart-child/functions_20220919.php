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
  if ( is_page( array( 'iphones','sell-iphones', 'samsung','other-smartphones','macbook','smartwatch','tablet','mac','microsoftsurface','other-items','sell-apple-watch','sell-apple-watch-se','sell-my-apple-watch-2april','sell-apple-watch-series-6','sell-apple-watch-series-5','sell-apple-watch-series-4','sell-apple-watch-series-3','sell-apple-watch-series-2','sell-apple-watch-series-1','sell-apple-watch-series-1st-gen','google','motorola','lg','sell-oneplus','htc','sony','essential','caterpillar','blackberry','samsungwatch','appletablet','samsungtablet','ipad','ipadair','ipadmini','ipadpro','macbooksub','macbookair','sell-macbookpro','imac','imacpro','macmini','macpro-2','surface','surfacebook','surfacego','surfacelaptop') ) ) {
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
  
jQuery('#billing_paypal_email_field').append('<span style="color: #ff0000">Quickest Payment Option - Please note that there is a $1.30 + 2.9% fee to receive funds using PayPal. The fee will be deducted from the payout amount.</span>')
  
jQuery('#billing_venmo_no_field').append('<span style="color: #ff0000">Quickest Payment Option - Please note that there is a $1.00 fee to receive funds using Venmo. The fee will be deducted from the payout amount.</span>')  
  
jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field,#billing_venmo_no_field,#billing_venmo_no_confirm_field').find('span.optional').remove();   

/*show paypal required fields */
if(jQuery('body').hasClass('woocommerce-checkout')) {
if(jQuery('select[name="billing_payment_options"] option:selected').val() == 'PayPal') {     
  jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').show();         
} else {
  jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').hide();
}
if(jQuery('select[name="billing_payment_options"] option:selected').val() == 'Venmo') {
  jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').show();       
} else {
  jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').hide();
}
jQuery('select[name="billing_payment_options"]').on('change', function() {
  var selectVal = jQuery(this).find('option:selected').val();
  if(selectVal == 'PayPal') {
    jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').show();           
  } else {
    jQuery('#billing_paypal_email_field,#billing_paypal_email_confirm_field').hide();
  }
  if(selectVal == 'Venmo') {
    jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').show();           
  } else {
    jQuery('#billing_venmo_no_field,#billing_venmo_no_confirm_field').hide();
  }    
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
    
</script>
    <?php
    }
add_action('wp_footer', 'footer_js');


function noindex_for_payouts()
{
    if ( is_singular( 'payouts' ) ) {
        echo '<meta name="robots" content="noindex, follow">';
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