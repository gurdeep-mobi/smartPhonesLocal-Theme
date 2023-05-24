<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php esc_html_e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php esc_html_e( 'Billing details', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $checkout->get_checkout_fields( 'billing' );

		foreach ( $fields as $key => $field ) {
			if($field['name']!='billing_payment_options' 
	        && $field['name']!='billing_paypal_email' 
	        && $field['name']!='billing_paypal_email_confirm'
	        && $field['name']!='billing_venmo_no'
	        && $field['name']!='billing_venmo_no_confirm')
			{
				woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
			}			
		}
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>

<style>
 	#billing_payment_options_Check,#billing_payment_options_PayPal,#billing_payment_options_Venmo {
  		position: absolute;
  		left: -9999px;
	}

	.check_paypal_venmo_class label {
  		display: inline-block;
  		cursor: pointer;  
	}
 </style>

 <script>
 	// jQuery('label[for="billing_payment_options_Check"]').html('image check');
 	// jQuery('label[for="billing_payment_options_PayPal"]').html('image paypal');
 	// jQuery('label[for="billing_payment_options_Venmo"]').html('image venmo');

 	// Iterate over each label element
	jQuery('label').each(function() {
	  // Check the class and "for" attribute values
	  if (jQuery(this).hasClass('radio') && jQuery(this).attr('for') === 'billing_payment_options_Check') {
	    // Change the innerHTML of the label
	    jQuery(this).html('image check'); // Replace 'New Text' with the desired content
	  }

	  if (jQuery(this).hasClass('radio') && jQuery(this).attr('for') === 'billing_payment_options_PayPal') {
	    // Change the innerHTML of the label
	    jQuery(this).html('image paypal'); // Replace 'New Text' with the desired content
	  }

	  if (jQuery(this).hasClass('radio') && jQuery(this).attr('for') === 'billing_payment_options_Venmo') {
	    // Change the innerHTML of the label
	    jQuery(this).html('image venmo'); // Replace 'New Text' with the desired content
	  }

	});

 </script>
