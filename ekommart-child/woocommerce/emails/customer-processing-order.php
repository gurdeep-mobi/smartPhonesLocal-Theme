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
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates/Emails
 * @version 3.7.0
 */

/**
 * NOTES ABOUT TEMPLATE EDIT FOR KADENCE WOOMAIL DESIGNER, 
 * 1. add hook 'kadence_woomail_designer_email_details' to pull in main text
 * 2. Remove static main text area.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 
?>
<?php
$order_id = $order->get_id();
$label_id = get_post_meta( $order_id, 'usps_qr_label_id', true );
$billing_zipcode = $order->get_billing_postcode();

$shipping_track_number = '';
$tracking_items = get_post_meta( $order_id, '_wc_shipment_tracking_items', true );
if(isset($tracking_items[0]['tracking_number'])){
        $shipping_track_number = $tracking_items[0]['tracking_number'];
}
?>
<p>
    <span>No printer for the shipping label? No problem!</span>
    <ul>
        <li>Bring your packaged device(s) to the USPS location. To find your most convienient location use the <a href="https://tools.usps.com/find-location.htm?locationType=po&serviceType=lbroretail&address=<?php echo $billing_zipcode; ?>">USPS store locator.</a></li>
        <li>Show the QR code below to the USPS associate or use the self service kiosk. They will print your shipping label and ship your device(s) for free.</li>
        <li>We'll update you when your package has been received and inspected.</li>
    </ul>
</p>

<p style="text-align: center;"></p>

<p style="text-align:center;">
    <img src="<?php echo home_url(); ?>/wp-content/uploads/shipping_labels/<?php echo $order->get_order_number(); ?>_qrcode.png" />
</p>
<p style="text-align:center;">
    <span style="padding: 0.6180469716em 1.41575em;">
        <a href="https://tools.usps.com/find-location.htm?locationType=po&serviceType=lbroretail&address=<?php echo $billing_zipcode; ?>" target="_blank">
          <img src="https://smartphonesstg.wpengine.com/wp-content/uploads/2020/02/Find-post-office.png" />
        </a>
    </span>
    <span style="padding: 0.6180469716em 1.41575em;">
        <a href="<?php echo home_url(); ?>/wp-content/uploads/shipping_labels/<?php echo $order->get_order_number(); ?>_return_label_raw.pdf" target="_blank" >
            <img src="https://smartphonesstg.wpengine.com/wp-content/uploads/2020/02/Return-Label.png" />
        </a>
    </span>
</p>
<?php if(!empty($shipping_track_number)){ ?>
<p style="text-align:center;">
    <b>USPS Tracking number <u style="color:darkblue;"><?php echo $shipping_track_number; ?></u> 
    </b>
</p>
<?php } ?>
<p>If packaging material was requested, expect delivery within 3-6 business days. The shipping label will be included.</p>
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
/**
 * @hooked Kadence_Woomail_Designer::email_main_text_area
 */
do_action( 'kadence_woomail_designer_email_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additonal content - this is set in each email's settings.
 */
$additional_enable = Kadence_Woomail_Customizer::opt( 'additional_content_enable' );
if ( isset( $additional_content ) && ! empty( $additional_content ) && apply_filters( 'kadence_email_customizer_additional_enable', $additional_enable, $email ) ) {
    echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
