<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<?php /* translators: %s: Order number */ ?>
<p><?php printf( esc_html__( 'Just to let you know &mdash; we\'ve received your order #%s, and it is now being processed:', 'woocommerce' ), esc_html( $order->get_order_number() ) ); ?></p>

<p>Please print off the prepaid shipping label using the button below or via the attachment to this email to send in your device(s).</p>

<p>Please bring your packaged and ready-to-ship item to your nearest Post office and show the Label Broker Code below to the USPS Retal Associate at the Retail Counter to print the label. </p>

<p style="text-align: center;"><a href="<?php echo home_url(); ?>/wp-content/uploads/shipping_labels/<?php echo $order->get_order_number(); ?>_return_label_raw.pdf" target="_blank" style="background-color:#e06901!important; border-color:#e06901!important; color:#fff; text-decoration: none; border: 2px solid;border-radius: 3px; background: 0 0; cursor: pointer; padding: 0.6180469716em 1.41575em;font-weight: 700;text-shadow: none;display: inline-block;-webkit-appearance: none;">Print Shipping Label</a></p>

<p style="text-align:center;"><img src="<?php echo home_url(); ?>/wp-content/uploads/shipping_labels/<?php echo $order->get_order_number(); ?>_qrcode.png" /></p>

<?php
$label_id = get_post_meta( $order->get_id(), 'usps_qr_label_id', true );
$billing_zipcode = $order->get_billing_postcode();
?>

<p>You can also access your label <a href="https://tools.usps.com/label-broker.htm">online.</a></p>


<p>If you requested packaging material expect to receive them within 3-6 business days. The same shipping label attached will be included in the package for you to use.</p>

<p>Payment is sent once the shipment is received in our facility and inspected.</p>

<p>
    <b>Please follow these instructions before shipping:</b>
</p>
<p>
    <b>1. Remove your iCloud or Google/Samsung account and all passwords. View our guide to removing accounts <a href="<?php echo home_url(); ?>/guide-to-removing-icloud-google-and-samsung-accounts/">here</a>.</b>
    Please note: We do not accept devices that are locked or reported lost or stolen.<br />
    <b>2. Deactivate carrier service & pay off any remaining balance on your account.</b>
    Please contact your carrier to pay any balance due on your device or to terminate service. We may send you a revised offer or have to return your device if this has not been completed.<br />
    <b>3. Backup files if necessary then factory reset your device(s).</b>
    We recommend that you backup any files or photos that you would like saved and then factory restore the device.<br />
    <b>4. Remember to remove your SD and/or SIM card from your device.</b><br />
    <b>5. Before shipping we recommend that you insure your shipment against loss, theft, and damage.</b>
    To add insurance the USPS will need to create a new label for you. We will include a $5.00 shipping credit to your order if the label we provided is not used.<br />
</p>
<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
