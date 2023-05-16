<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>


<iframe src="https://scdcb.com/p.ashx?a=35&e=69&t=<?php echo $order->get_order_number();  ?>&p=<?php echo $order->get_total();  ?>" height="1" width="1" frameborder="0"></iframe>



<div class="woocommerce-order">

	<?php if ( $order ) : ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay', 'woocommerce' ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php _e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>

			<p class="woocommerce-notice woocommerce-notice--success-thankyou"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you for selling with us!', 'woocommerce' ), $order ); ?></p>
			
<hr>
            <div class="thankyou-container">
            	<h2> What's next? </h2>
                <p>Please follow these steps to prepare and ship your order:</p>
                <div class="ty-points">
                	<h2>Device Preparation:</h2>
                    <ul>
                    	<li>Remove your iCloud or Google, and/or Samsung accounts and all passwords. Please note: We do not accept devices that are account locked or reported lost or stolen.</li>
                        <li>Deactivate carrier service & pay off any remaining balance on your account. Payment may be delayed if this has not been completed.						  </li>
                        <li>
                        	Backup files, if necessary, then factory reset your device(s).
                        </li>
                        <li>
                        	Remember to remove your SD and/or SIM card from your device(s).
                        </li>
                    
                    </ul>
                </div>
                
                <div class="ty-points">
                	<h2>Packaging Your Order:</h2>
                    	<p>
                        	It is important that you package your item(s) securely. We recommend that you use a sturdy box or padded envelope. Wrap your items in padding such as bubble wrap. Fill any empty space in your package with extra padding such as crumpled paper, packing peanuts, or air cushions so that your item(s) do not move around during shipping. Seal the package shut with tape. <br><br>
                            If you requested to receive packaging materials from us, you should receive them within a few days.
                        </p>
                </div>
                
                <div class="ty-points">
                	<h2>Shipping Your Order:</h2>
                    <p>
      						Print off your pre-paid shipping label. Please securely adhere the shipping label to your package. Make sure that the barcode is clearly visible, so that it can be scanned. If you requested packaging materials you should receive a shipping label along with your packing materials.
      <br>
      Drop your package off at your local USPS or schedule a pickup.
      <br>
      Although it is not required, we recommend that you insure your shipment against loss, theft, and damage.
      <br>
      <b>Your offer is valid for 30 days</b> from your order date. So, be sure to ship your package as soon as you can. 
      
                        </p>
                </div>
                <hr>
            
            </div>			

			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<li class="woocommerce-order-overview__order order">
					<?php _e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_order_number(); ?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php _e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php _e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php _e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php _e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>

			</ul>

		<?php endif; ?>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>

	<?php endif; ?>

</div>
<?php 
$items = "";
foreach ( $order->get_items() as $item_id => $item ) {
	$itemId = $item->get_variation_id() > 0 ? $item->get_variation_id() : $item->get_product_id();
	if ($itemId > 0) {
		$items .= "&items[]=" . $itemId;
	}
}
?>
<!-- START: BANKMYCELL --> 
<img src="https://www.bankmycell.com/merchant/sale/8lQxw8mrd42vhmxZ/2?email=<?= md5(trim($order->get_billing_email())); ?>&total=<?= $order->get_total(); ?>&id=<?= $order->get_order_number(); ?><?= $items; ?>" />
<!-- END: BANKMYCELL --> 
