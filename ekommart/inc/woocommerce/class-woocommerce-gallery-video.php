<?php
/**
 * Ekommart WooCommerce Gallery Video Class
 *
 * @package  ekommart
 * @since    2.4.3
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Ekommart_WooCommerce_Video')) :

    /**
     * The Ekommart WooCommerce Gallery Video Class
     */
    class Ekommart_WooCommerce_Video {

        public function __construct() {
            if (ekommart_is_elementor_activated()) {
                add_filter('woocommerce_product_data_tabs', array($this, 'video_product_tabs'));
                add_filter('woocommerce_product_data_panels', array(
                    $this,
                    'video_options_product_tab_content'
                ));
                add_action('woocommerce_process_product_meta', array($this, 'save_video_option_fields'));

                add_action('woocommerce_product_thumbnails', array($this, 'template_single_product_video'), 30);
            }
        }

        public function video_product_tabs($tabs) {

            $tabs['video'] = array(
                'label'    => __('Product Video', 'ekommart'),
                'target'   => 'video_options',
                'class'    => array(),
                'priority' => 80,
            );

            return $tabs;

        }

        public function video_options_product_tab_content() {

            global $product;

            ?>
            <div id='video_options' class='panel woocommerce_options_panel'><?php

                ?>
                <div class='options_group'><?php

                    woocommerce_wp_text_input(array(
                        'id'    => '_video_select',
                        'label' => __('Url Video', 'ekommart'),
                    ));

                    $video_thumbnail_id = get_post_meta($_GET['post'], '_video_thumbnail', true);
                    $video_thumbnail    = '';

                    if ($video_thumbnail_id) {
                        $video_thumbnail = wp_get_attachment_image_url($video_thumbnail_id, 'thumbnail');
                    } else {
                        $video_thumbnail = wc_placeholder_img_src();
                    }
                    ?>
                    <p class="form-field _video_thumbnail_field ">
                        <label for="_video_thumbnail"><?php echo esc_html__('Video Thumbnail', 'ekommart'); ?></label>
                        <a href="#" class="video_thumbnail_button"><img height="100" width="100" src="<?php echo esc_attr($video_thumbnail); ?>" alt=""></a>
                        <input type="hidden" id="_video_thumbnail" name="_video_thumbnail" class="video_thumbnail" value="<?php echo esc_attr($video_thumbnail_id); ?>">
                    </p>
                </div>

            </div>
            <?php

        }

        public function save_video_option_fields($post_id) {
            if (isset($_POST['_video_select'])) {
                update_post_meta($post_id, '_video_select', esc_attr($_POST['_video_select']));
            }
            if (isset($_POST['_video_thumbnail'])) {
                update_post_meta($post_id, '_video_thumbnail', esc_attr($_POST['_video_thumbnail']));
            }
        }

        public function template_single_product_video() {
            global $product;
            $video = get_post_meta($product->get_id(), '_video_select', true);
            if (!$video) {
                return;
            }
            $video_thumbnail = get_post_meta($product->get_id(), '_video_thumbnail', true);
            if ($video_thumbnail) {
                $video_thumbnail = wp_get_attachment_image_url($video_thumbnail, 'thumbnail');
            } else {
                $video_thumbnail = wc_placeholder_img_src();
            }
            $video = wc_do_oembeds($video);
            echo '<div data-thumb="' . esc_url_raw($video_thumbnail) . '" class="woocommerce-product-gallery__image">
    <a>
        ' . $video . '

    </a>
</div>';
        }
    }

    return new Ekommart_WooCommerce_Video();

endif;
