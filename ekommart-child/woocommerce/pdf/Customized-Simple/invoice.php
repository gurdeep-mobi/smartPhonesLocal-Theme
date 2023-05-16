<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); ?>
<style type="text/css">
    .woocommerce-Price-currencySymbol{
        display: none;
    }
</style>
<table class="head container">
    <tr>
        <td class="header" colspan="2">
            <?php
			if( $this->has_header_logo() ) {
				$this->header_logo();
			} else {
				echo $this->get_title();
			}
		?>
        </td>

    </tr>
    <tr id="below-header">
        <td>
            <div class="shop-info">
                <div class="shop-address" style="line-height: 22px;"><?php $this->shop_address(); ?></div>
            </div>
        </td>
        <td class="order-data">
            <table>
                <?php do_action( 'wpo_wcpdf_before_order_data', $this->type, $this->order ); ?>
                <tr class="order-date">
                    <th style="line-height: 22px;"><b><?php _e( 'DATE:', 'woocommerce-pdf-invoices-packing-slips' ); ?></b></th>
                    <td style="line-height: 22px;"><?php  
                        if(empty($this->order->get_date_completed())){
                            echo $this->order->get_date_created()->format ('m-d-Y'); 
                        }else{
                            echo $this->order->get_date_completed()->format ('m-d-Y');  
                        }

                     ?></td>
                </tr>
                <tr class="order-number">
                    <th style="line-height: 22px;"><b><?php _e( 'ORDER#', 'woocommerce-pdf-invoices-packing-slips' ); ?><b></th>
                    <td style="line-height: 22px;"><?php $this->order_number(); ?></td>
                </tr>

                <?php do_action( 'wpo_wcpdf_after_order_data', $this->type, $this->order ); ?>
            </table>
        </td>
    </tr>
</table>

<?php do_action( 'wpo_wcpdf_after_document_label', $this->type, $this->order ); ?>
<h2 class="document-type-label">TO</h2><br><br>
<table class="order-data-addresses">
    <tr>
        <td class="address billing-address" style="line-height: 22px;">
            <!-- <h3><?php _e( 'Billing Address:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3> -->
            <?php do_action( 'wpo_wcpdf_before_billing_address', $this->type, $this->order ); ?>
            <?php $this->billing_address(); ?>
            <?php do_action( 'wpo_wcpdf_after_billing_address', $this->type, $this->order ); ?>
            <?php if ( isset($this->settings['display_email']) ) { ?>
            <div class="billing-email"><?php $this->billing_email(); ?></div>
            <?php } ?>
            <?php if ( isset($this->settings['display_phone']) ) { ?>
            <div class="billing-phone"><?php $this->billing_phone(); ?></div>
            <?php } ?>
        </td>
        <td class="address shipping-address" style="line-height: 22px;">
            <?php if ( !empty($this->settings['display_shipping_address']) && ( $this->ships_to_different_address() || $this->settings['display_shipping_address'] == 'always' ) ) { ?>
            <h3><?php _e( 'Ship To:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
            <?php do_action( 'wpo_wcpdf_before_shipping_address', $this->type, $this->order ); ?>
            <?php $this->shipping_address(); ?>
            <?php do_action( 'wpo_wcpdf_after_shipping_address', $this->type, $this->order ); ?>
            <?php } ?>
        </td>

    </tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->type, $this->order ); ?>

<table class="order-details">
    <thead>
        <tr>
            <th class="product" style="width: 200px;"><?php _e('Description', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
            <th class="quantity"><?php _e('Quantity', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
            <th class="price"><?php _e('Price', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
            <th class="sub_total"><?php _e('Total', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $items = $this->get_order_items(); if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : //print_r($item); die;?>
        <tr
            class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
            <td class="product" style="width: 200px;">
                <?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
                <span class="item-name"><?php echo wordwrap($item['name'],20,"<br>\n"); ?></span><br>
                <?php do_action( 'wpo_wcpdf_before_item_meta', $this->type, $item, $this->order  ); ?>
                <?php  $device_condition = wc_get_order_item_meta( $item_id, 'pa_device-condition', true ); ?>
                <?php if($device_condition){ ?>
                <span class="item-meta"><?php echo 'Condition: '.ucfirst($device_condition); ?></span>
                <?php } ?>
                <?php do_action( 'wpo_wcpdf_after_item_meta', $this->type, $item, $this->order  ); ?>
            </td>
            <td class="quantity"><?php echo $item['quantity']; ?></td>
            <td class="price"><span style="margin-right: 10px;">$</span><?php echo $item['order_price']; ?></td>
            <td class="sub_total"><span style="margin-right: 10px;">$</span><?php echo $item['line_subtotal']; ?><?php /*
									$sum_total = ($item['quantity'] * $item['product']->price);
									$formatedNumber = number_format($sum_total, 2, '.', '');
									echo '$'.$formatedNumber; */
									 ?>
            </td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody>
    <tfoot>
        <tr class="no-borders">
            <td class="no-borders">
                <div class="document-notes">
                    <?php do_action( 'wpo_wcpdf_before_document_notes', $this->type, $this->order ); ?>
                    <?php if ( $this->get_document_notes() ) : ?>
                    <h3><?php _e( 'Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
                    <?php $this->document_notes(); ?>
                    <?php endif; ?>
                    <?php do_action( 'wpo_wcpdf_after_document_notes', $this->type, $this->order ); ?>
                </div>
                <div class="customer-notes">
                    <?php do_action( 'wpo_wcpdf_before_customer_notes', $this->type, $this->order ); ?>
                    <?php if ( $this->get_shipping_notes() ) : ?>
                    <h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
                    <?php $this->shipping_notes(); ?>
                    <?php endif; ?>
                    <?php do_action( 'wpo_wcpdf_after_customer_notes', $this->type, $this->order ); ?>
                </div>
            </td>
            <td class="no-borders"></td>
            <td class="no-borders" colspan="2">
                <table class="totals">
                    <tfoot>
                        <?php foreach( $this->get_woocommerce_totals() as $key => $total ) : ?>
                        <tr class="<?php echo $key; ?>">
                            <td class="no-borders"></td>
                            <?php if($total['label'] == 'Total') { ?>
                            <th class="description" style="width: 50%;"><?php echo $total['label']; ?></th>
                            <td class="price" style="width: 50%;"><span class="totals-price"><span style="margin-right: 10px;">$</span><?php echo $total['value']; ?></span></td>
                            <?php } ?>
                        </tr>
                        <?php endforeach; ?>
                    </tfoot>
                </table>
            </td>
        </tr>
    </tfoot>
</table>
<?php //die; ?>

<div class="bottom-spacer"></div>
<?php do_action( 'wpo_wcpdf_after_order_details', $this->type, $this->order ); ?>
<div id="footer">
    <p>Please visit us at www.SmartphonesPLUS.com to sell your devices in the future!</p><br>
    <p>THANK YOU FOR YOUR BUSINESS!</p>
</div>
<?php do_action( 'wpo_wcpdf_after_document', $this->type, $this->order ); ?>