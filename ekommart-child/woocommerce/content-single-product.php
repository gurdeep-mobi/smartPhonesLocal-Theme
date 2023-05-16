<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css" />

<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
echo get_the_password_form(); // WPCS: XSS ok.
return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

<?php
/**
 * Hook: woocommerce_before_single_product_summary.
 *
 * @hooked woocommerce_show_product_sale_flash - 10
 * @hooked woocommerce_show_product_images - 20
 */
do_action( 'woocommerce_before_single_product_summary' );
?>

<div class="summary entry-summary">
    <?php
    /**
     * Hook: woocommerce_single_product_summary.
     *
     * @hooked woocommerce_template_single_title - 5
     * @hooked woocommerce_template_single_rating - 10
     * @hooked woocommerce_template_single_price - 10
     * @hooked woocommerce_template_single_excerpt - 20
     * @hooked woocommerce_template_single_add_to_cart - 30
     * @hooked woocommerce_template_single_meta - 40
     * @hooked woocommerce_template_single_sharing - 50
     * @hooked WC_Structured_Data::generate_product_data() - 60
     */
    do_action( 'woocommerce_single_product_summary' );
    ?>
</div>
</div>

<?php
/**
 * Hook: woocommerce_after_single_product_summary.
 *
 * @hooked woocommerce_output_product_data_tabs - 10
 * @hooked woocommerce_upsell_display - 15
 * @hooked woocommerce_output_related_products - 20
 */
do_action( 'woocommerce_after_single_product_summary' );
?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>



<!-- How to Sell section -->
<div class="how_to_sell__wrapper">
    <h2>How to Sell Your <?php echo $product->get_name(); ?> <span>Using SmartphonesPLUS</span></h2>
    <div class="sell__box-wrapper">
        <div class="sell__box">
            <img src="<?php echo get_stylesheet_directory_uri();?>/images/doller.png" alt="">
            <p><?php $sale_text_1 = get_field( "sale_text_1" );
                if( $sale_text_1 ) {
                    echo $sale_text_1;
                } else {
                    echo 'Select the details that match your device to get an instant quote';
                }
            ?></p>
        </div>
        <div class="sell__box">
            <img src="<?php echo get_stylesheet_directory_uri();?>/images/freeship.png" alt="">
            <p><?php $sale_text_2 =  get_field( "sale_text_2" );
                if( $sale_text_2 ) {
                    echo $sale_text_2;
                } else {
                    echo 'Select the details that match your device to get an instant quote';
                }
            ?></p>
        </div>
        <div class="sell__box">
            <img src="<?php echo get_stylesheet_directory_uri();?>/images/saving.png" alt="">
            <p><?php $sale_text_3 = get_field( "sale_text_3" );
                if( $sale_text_3 ) {
                    echo $sale_text_3;
                } else {
                    echo 'Select the details that match your device to get an instant quote';
                }
            ?></p>
        </div>
    </div>
    <div class="sell__box-bottomtext">
        <h3>Sell your <?php echo $product->get_name(); ?> with SmartphonesPLUS</h3>
        <h3>Get paid cash for your <?php echo $product->get_name(); ?> trade-in with SmartphonesPLUS</h3>
    </div>
</div>

    <!-- Sell Product slider -->
<div class="sell__product-slider-wrapper">
    <section class="related products__new">
        <h2 class="sell__product-title">Other Electronics <span>Sold Online Recently</span></h2>
        <ul class="sell__product-slider-wrap sellProductSlider">
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(3136)?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/sell-iphone-100x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(3136)?>" class="sell_button" tabindex="-1"><span>Sell iPhone</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item"> 
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(3895)?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/Samsung-Galaxy-S20-2-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(3895)?>" class="sell_button" tabindex="-1"><span>Sell Samsung</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(19887)?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/Sell-Android-Smartphone-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(19887)?>" class="sell_button" tabindex="-1"><span>Sell Smartphone</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(3891)?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/apple-macbook-air-2020-silver-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(3891)?>" class="sell_button" tabindex="-1"><span>Sell MacBook</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(3893)?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/apple-watch-series-6-space-gray-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(3893)?>" class="sell_button" tabindex="-1"><span>Sell Smartwatch</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(3892);?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/Sell-iPad-Tablet-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(3892)?>" class="sell_button" tabindex="-1"><span>Sell iPad / Tablet</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(16675);?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/iMac-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(16675)?>" class="sell_button" tabindex="-1"><span>Sell Mac Desktop</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(16671);?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/sell-microsoft-surface-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(16671)?>" class="sell_button" tabindex="-1"><span>Sell Surface</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                </div>
            </li>
            <li class="sell__product-slider-item">
                <div class="sell__product-slider-inner-item">
                    <div class="product-image-wrap">
                        <a href="<?php echo get_page_link(16673);?>">
                            <img alt="" class="attachment-shop_catalog size-shop_catalog"
                                src="<?php echo get_stylesheet_directory_uri();?>/images/Sell-Other-Items-200x200.jpg" />
                        </a>
                        <div class="wpb_wrapper">
			                <a href="<?php echo get_page_link(16673)?>" class="sell_button" tabindex="-1"><span>Sell Other Items</span> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
		                </div>
                    </div>
                    <!--div class="sell__product-slider-title">
                        <a href="#"> Samsung Galaxy S5 Active </a>
                        <p>From $354.44</p>
                    </div-->
                </div>
            </li>
        </ul>
    </section>
</div>


<!-- faq section -->
<div class="pdp__faq-section">
    <h2 class="accordian_heading">
    Frequently <span class="accordian_span_heading">Asked Questions</span>
    </h2>

<div class="accordian_parent_div">
    <div class="accordian_righr_div">
        <div class="accordian_data">
        <label for="title1" class="pdp__faq-title"
            >Who is SmartphonesPLUS?</label
        >
        <div class="content">
            <p>
            SmartphonesPLUS was started by three tech-minded brothers who
            thought it should be simpler to buy, sell, and repair your
            devices. Our family-owned business values honesty, excellent
            service, and fair pricing. This combination ensures you always
            get the best deal possible for all things mobile.
            SmartphonesPLUS has two retail locations in
            <a href="/cedar-rapids-ia-cell-phone-repair/">
                Cedar Rapids, IA
            </a>
            and
            <a href="/coralville-ia-cell-phone-repair/">Coralville, IA </a>
            and also serves anyone looking to buy, sell, or repair devices
            online.
            </p>
        </div>
        </div>

        <div class="accordian_data">
        <label for="title2" class="pdp__faq-title"
            >How does the selling process work?</label
        >
        <div class="content">
            <p>
            We've made our selling process straightforward and simple: Click
            <a href="/sell/">Start Selling</a> on our homepage or click on
            “Sell” on the top menu from any page then select the device
            you'd like to sell. Once you have found your device you will be
            prompted to answer a few questions and will be given an instant
            quote. Tell us how you want to be paid (by PayPal or check) and
            if you need a box and packaging materials. Click place order and
            you will receive an Order # and confirmation email. Once your
            order is placed you will be sent a shipping label and
            instructions via email explaining how to prepare & ship your
            device. With SmartphonesPLUS shipping is always free! Once we
            receive the device we'll inspect it to be sure it is the correct
            model, condition, etc. that was quoted. If it is not, we will
            send a revised offer which you can accept or decline. If you
            decline a revised offer we ship it back for free! If everything
            checks out, we'll send your cash the same day the order is
            processed! If you are selling your device in-store we will
            inspect your device on the spot and pay out cash or store
            credit.
            </p>
        </div>
        </div>

        <div class="accordian_data">
        <label for="title3" class="pdp__faq-title">
            What condition is my device in?</label
        >
        <div class="content">
            <p>
            Your device can be categorized into one of the following
            conditions: <br />
            Flawless: New cosmetic appearance and functions as if it has not
            yet been used. For example, no cosmetic issues, no scratches and
            or scuffs, no dents, no cracks, no water damage, all buttons and
            ports work, still fully functional, no LCD burn/ghost image, no
            pixel damage and the screen lights up normally. Battery health
            must be above 85% (86% or higher). All parts of the device must
            be included. <br />
            Good: Normal signs of use - may have a couple of light
            scratches. No clear/deep scratches, scuffs, or dents. No cracks,
            no water damage, all buttons/ports work, must be fully
            functional, no LCD burn/ghost image, no pixel damage, and the
            screen must light up normally. All parts of the device must be
            included. <br />
            Fair: Heavy signs of use - deep or multiple scratches and or
            scuffs, dents, no cracks, no water damage, all buttons/ports
            work, still fully functional, no LCD burn/ghost image, no pixel
            damage and the screen must light up normally. All parts of the
            device must be included. <br />
            Broken: Does not function normally - cracked, chipped and or
            other damages to the screen, back glass, frame, camera lens, or
            other parts, screen burn, ghost image, white dots, or other
            pixel damage. Bad port(s), bad battery, water damage, or any
            other hardware or software issues. All parts of the device must
            be included. <br />
            No Power: The device does not power on, turn on, or work. All
            parts of the device must be included. If your phone falls in any
            of the above categories you can sell your iPhone with us.
            </p>
        </div>
        </div>
        <div class="accordian_data">
        <label for="title3" class="pdp__faq-title">
            How do I remove my Apple ID or Google Account?</label
        >
        <div class="content">
            <p>
            Please read our
            <a href="https://www.smartphonesplus.com/guide-to-removing-icloud-google-and-samsung-accounts/"
                >Guide to Removing iCloud, Google, and Samsung Accounts</a
            >
            ***If you do not remove the iCloud, Google, or Samsung account
            from your device, the device will be returned to you or
            recycled, as it cannot be activated by another user. If you are
            having trouble removing your account, please contact our
            <a href="https://www.smartphonesplus.com/contact-us/">support team</a>.
            </p>
        </div>
        </div>
        <div class="accordian_data">
        <label for="title3" class="pdp__faq-title">
            Can I recycle my phone, tablet, or other tech?</label
        >
        <div class="content">
            <p>
            Yes! It is a collective effort to take care of our environment.
            We will accept devices you wish to recycle and will dispose of
            them properly at no cost to you.
            </p>
        </div>
        </div>
        <div class="accordian_data">
        <label for="title3" class="pdp__faq-title">
            Can I sell a device that has been reported lost or stolen?</label
        >
        <div class="content">
            <p>
            No. We cannot accept devices that have been reported as lost or
            stolen. Devices that have been reported should be turned into
            the police or the phone carrier associated with it so they can
            be returned to the rightful owner. If the device is yours that
            has been reported please contact your carrier to have the
            blacklist status removed. As part of our online trade-in
            process, each of our customers are required to contractually
            confirm the device is in fact theirs to sell and has not been
            reported as lost or stolen. We also remind customers of the same
            at several points in the process. The device you sell must be
            yours to sell and must not have been reported.
            </p>
        </div>
        </div>
    </div>

    <div class="accordian_left_div">
        <div class="accordian_data">
        <label for="title1" class="pdp__faq-title"
            >How do I know if my device can be repaired?</label
        >

        <div class="content">
            <p>
            Click on <a href="https://www.smartphonesplus.com/repair/">Start A Repair</a> and select your
            device. Then select from the repair options available for your
            device. You can also <a href="https://www.smartphonesplus.com/contact-us/">contact us</a> and
            speak directly with a technician for professional repair advice.
            </p>
        </div>
        </div>
        <div class="accordian_data">
        <label for="title2" class="pdp__faq-title"
            >Can I buy a used phone, tablet, or MacBook from
            SmartphonesPLUS?</label
        >

        <div class="content">
            <p>
                Yes! Click on <a href="https://buy.smartphonesplus.com/">Start Shopping</a> - we sell a wide
            variety of refurbished devices. Check out all of our
            <a href="https://buy.smartphonesplus.com/">product collections</a>.<br /><br />
            Some of our product collections include
            <a href="https://buy.smartphonesplus.com/collections/buy-iphone">Apple iPhone</a>, <a href="https://buy.smartphonesplus.com/collections/buy-samsung">Samsung Galaxy</a>, <a href="https://buy.smartphonesplus.com/collections/buy-ipad-tablet">Apple iPads</a>, <a href="https://buy.smartphonesplus.com/collections/buy-smartwatch">Smartwatches</a>,
            <a href="https://buy.smartphonesplus.com/collections/buy-android">Android Smartphones</a>, <a href="https://buy.smartphonesplus.com/collections/buy-macbook">MacBooks</a> and <a href="https://buy.smartphonesplus.com/collections/buy-accessories">accessories</a>.<br /><br />
            We also have a <a href="https://buy.smartphonesplus.com/collections/clearance">clearance</a> section for all of the best deals on our devices and accessories.
            </p>
        </div>
        </div>
        <div class="accordian_data">
        <label for="title3" class="pdp__faq-title"
            >Does SmartphonesPLUS offer a warranty on the devices they
            sell?</label
        >
        <div class="content">
            <p>
            Yes! We offer a 30-day warranty on every device we sell. We take
            quality very seriously. Every device we sell must have a 100%
            score on our rigorous 30+ point quality test. We test everything
            from the screen and battery to the vibration motor.
            </p>
        </div>
        </div>
        <div class="accordian_data">
        <label for="title3" class="pdp__faq-title"
            >Do I have to pay for shipping?</label
        >
        <div class="content">
            <p>
            Nope! Whether you are buying, selling, or repairing a device you
            never have to pay for standard shipping! If you need your order
            expedited feel free to <a href="/contact-us/">contact us</a>.
            </p>
        </div>
        </div>
        <div class="accordian_data">
        <label for="title3" class="pdp__faq-title"
            >Can I track my package/device while it's in transit?</label
        >
        <div class="content">
            <p>
            Yes, you will receive your package's tracking number by email
            and will be able to track the package along the way.
            </p>
        </div>
        </div>
    </div>
    </div>
</div>       


<!--script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script-->

<script>
    jQuery(document).ready(function($){
        $('.sellProductSlider').slick({
        arrow: true,
        infinite: false,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    arrow: true
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    // faq section
    $(document).on("click", ".pdp__faq-title", function() {
        $(this).next(".content").slideToggle();
        $(this).toggleClass("minus");
    });
    })
    
    
</script>

