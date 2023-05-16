(function ($) {
    'use strict';
    $( document ).ready( function( $ ) {
        // Sale price schedule.
        $( '._deal_quantity_field' ).each( function() {
            var $these_sale_dates = $( this );
            var sale_schedule_set = false;
            var $wrap = $these_sale_dates.closest( 'div, table' );

            $these_sale_dates.find( 'input' ).each( function() {
                if ( '' !== $( this ).val() ) {
                    sale_schedule_set = true;
                }
            });

            if ( sale_schedule_set ) {
                $wrap.find( '.sale_price_dates_fields' ).show();
                $wrap.find( '._deal_quantity_field' ).show();
                $wrap.find( '.sale_schedule' ).hide();
            } else {
                $wrap.find( '.sale_price_dates_fields' ).hide();
                $wrap.find( '._deal_quantity_field' ).hide();
                $wrap.find( '.sale_schedule' ).show();
            }
        });

        $( '#woocommerce-product-data' ).on( 'click', '.sale_schedule', function() {
            var $wrap = $( this ).closest( 'div, table' );

            $wrap.find( '._deal_quantity_field' ).show();

            return false;
        }).on( 'click', '.cancel_sale_schedule', function() {
            var $wrap = $( this ).closest( 'div, table' );

            $wrap.find( '._deal_quantity_field' ).hide();
            $wrap.find( '.deal-sold-counts' ).hide();
            $wrap.find( '._deal_quantity_field' ).find( 'input' ).val( '' );

            return false;
        });

        $('body').delegate(".input_datetime", 'hover', function(e){
            e.preventDefault();
            $(this).datepicker({
                defaultDate: "",
                dateFormat: "yy-mm-dd",
                numberOfMonths: 1,
                showButtonPanel: true,
            });
        });

        var product_featured_frame;
        $('body').on('click', '.video_thumbnail_button', function (e) {
            e.preventDefault();
            e.preventDefault();

            var self = $(this);

            if ( product_featured_frame ) {
                product_featured_frame.open();
                return;
            } else {
                wp.media.view.settings.post.id = 0;
                product_featured_frame = wp.media({
                    // Set the title of the modal.
                    title: ekommart_media.choose_featured_img,
                    button: {
                        text: ekommart_media.choose_file,
                    }
                });

                product_featured_frame.on('select', function() {
                    var selection = product_featured_frame.state().get('selection');

                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        // set the image hidden id
                        self.siblings('input.video_thumbnail').val(attachment.id);
                        // set the image
                        self.find('img').attr('src', attachment.url);
                    });
                });

                product_featured_frame.open();

            }
        });

    } );
})(jQuery);