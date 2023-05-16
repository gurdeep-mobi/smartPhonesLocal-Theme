<?php 
	 if ( is_product_category() ){
	    global $wp_query;
	    $cat = $wp_query->get_queried_object();
	    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
	    if ( $image ) {
	    	//echo '<img src="' . $image . '" alt="' . $cat->name . '" />';
		    echo '<div class="ekommart-breadcrumb" style="background-image: url(' . $image . ');">';
		    
		} else {

			echo '<div class="ekommart-breadcrumb">';

		} 



	} else { ?>
		<div class="ekommart-breadcrumb">

<?php } ?>

	<div class="col-full">
		<h1 class="breadcrumb-heading">
			<?php
			if (is_page()) {
				the_title();
			} elseif (is_single()) {
				printf("<span class='screen-reader-text'>%s </span>%s", get_the_title(), esc_html__('Blog Detail', 'ekommart'));
			} elseif (is_archive() && is_tax() && !is_category() && !is_tag()) {
				$tax_object = get_queried_object();
				echo esc_html($tax_object->name);
			} elseif (is_category()) {
				single_cat_title();
			} elseif (is_home()) {
				echo esc_html__('Blog', 'ekommart');
			} elseif (is_post_type_archive('product')) {
				woocommerce_page_title();
			} elseif (is_post_type_archive()) {
				$tax_object = get_queried_object();
				echo esc_html($tax_object->label);
			} elseif (is_tag()) {
				// Get tag information
				$term_id  = get_query_var('tag_id');
				$taxonomy = 'post_tag';
				$args     = 'include=' . esc_attr($term_id);
				$terms    = get_terms($taxonomy, $args);
				// Display the tag name
				if (isset($terms[0]->name)) {
					echo esc_html($terms[0]->name);
				}
			} elseif (is_day()) {
				echo esc_html__('Day Archives', 'ekommart');
			} elseif (is_month()) {
				echo get_the_time('F') . esc_html__(' Archives', 'ekommart');
			} elseif (is_year()) {
				echo get_the_time('Y') . esc_html__(' Archives', 'ekommart');
			}elseif(is_search()){
				esc_html_e('Search Results', 'ekommart');
			}elseif (is_author()) {
				global $author;
				if (!empty($author)) {
					$usermetadata = get_userdata($author);
					echo esc_html__('Author', 'ekommart') . ': ' . $usermetadata->display_name;
				}
			}
			?>
		</h1>
		<?php
		if (ekommart_is_woocommerce_activated()) {
			woocommerce_breadcrumb();
		} elseif (ekommart_is_bcn_nav_activated()) {
			bcn_display();
		}
		?>
	</div>
</div>