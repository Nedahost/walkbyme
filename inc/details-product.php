<?php
/**
 * Custom Product Data Tab
 * Adds extra fields to the product data metabox in Admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Add the custom tab
add_action( 'woocommerce_product_write_panel_tabs', 'walkbyme_add_custom_product_tab' );

function walkbyme_add_custom_product_tab() {
    ?>
    <li class="custom_tab">
        <a href="#the_custom_panel">
            <span class="dashicons dashicons-list-view" style="margin-right:5px; vertical-align:text-bottom;"></span>
            <span><?php _e( 'Στοιχεία Προϊόντος', 'walkbyme' ); ?></span>
        </a>
    </li>
    <?php
}

// 2. Add the panel content
add_action( 'woocommerce_product_data_panels', 'walkbyme_render_custom_tab_panel' );

function walkbyme_render_custom_tab_panel() {
    ?>
    <div id="the_custom_panel" class="panel woocommerce_options_panel">
        <div class="options_group">
            <?php  
            // Σημείωση: Κρατάμε τα ίδια IDs (_text_field, _text_manufacturer, κλπ) 
            // για να ΜΗΝ χαθούν τα δεδομένα που έχεις ήδη περάσει στα προϊόντα.

            // Title for categories
            woocommerce_wp_text_input( array(
                'id'          => '_text_field', 
                'label'       => __( 'Τίτλος προϊόντων για τις κατηγορίες:', 'walkbyme' ),
                'placeholder' => __( 'Τίτλος προϊόντων για τις κατηγορίες...', 'walkbyme' ),
                'desc_tip'    => 'true',
                'description' => __( 'Αυτός ο τίτλος εμφανίζεται στα "cards" των προϊόντων αντί για τον κανονικό τίτλο, αν συμπληρωθεί.', 'walkbyme' ) 
            ));

            // Manufacturer
            woocommerce_wp_text_input( array(
                'id'          => '_text_manufacturer',
                'label'       => __( 'Κατασκευαστής:', 'walkbyme' ),
                'placeholder' => __( 'Κατασκευαστής...', 'walkbyme' ),
                'desc_tip'    => 'true',
                'description' => __( 'Το όνομα του κατασκευαστή.', 'walkbyme' ) 
            ));

            // Offer Type
            woocommerce_wp_text_input( array(
                'id'          => '_text_sales',
                'label'       => __( 'Τύπος Προσφοράς:', 'walkbyme' ),
                'placeholder' => __( 'Τύπος Προσφοράς...', 'walkbyme' ),
                'desc_tip'    => 'true',
                'description' => __( 'π.χ. "Super Offer" ή "Black Friday"', 'walkbyme' ) 
            ));

            // Homepage Offer Checkbox
            woocommerce_wp_checkbox( array( 
                'id'            => '_checkbox_sales', 
                'wrapper_class' => 'show_if_simple', 
                'label'         => __('Επιλογή Προσφοράς Αρχική Σελίδας', 'walkbyme' ), 
                'description'   => __( 'Τσεκάρουμε αν θέλουμε το προϊόν στην αρχική σελίδα ως προσφορά', 'walkbyme' ) 
            ));

            // Availability Select
            woocommerce_wp_select( array( 
                'id'      => '_select', 
                'label'   => __( 'Διαθέσιμο σε*', 'walkbyme' ), 
                'options' => array(
                    ''                    => __( 'Επιλέξτε διαθεσιμότητα...', 'walkbyme' ),
                    'Άμεσα διαθέσιμο'     => __( 'Άμεσα διαθέσιμο', 'walkbyme' ),
                    '4 - 7 Ημέρες'        => __( '4 - 7 Ημέρες', 'walkbyme' ),
                    'Κατόπιν Παραγγελίας' => __( 'Κατόπιν Παραγγελίας', 'walkbyme' )
                )
            ));     
            ?>
        </div>
    </div>
    <?php
}

// 3. Save Data (Optimized)
add_action( 'woocommerce_process_product_meta', 'walkbyme_save_custom_fields' );

function walkbyme_save_custom_fields( $post_id ) {
    
    // Security check: WooCommerce handles nonce check automatically in this hook, 
    // but standard sanitization is required.

    // 1. Title Field
    if ( isset( $_POST['_text_field'] ) ) {
        update_post_meta( $post_id, '_text_field', sanitize_text_field( $_POST['_text_field'] ) );
    }

    // 2. Manufacturer
    if ( isset( $_POST['_text_manufacturer'] ) ) {
        update_post_meta( $post_id, '_text_manufacturer', sanitize_text_field( $_POST['_text_manufacturer'] ) );
    }

    // 3. Sales Text
    if ( isset( $_POST['_text_sales'] ) ) {
        update_post_meta( $post_id, '_text_sales', sanitize_text_field( $_POST['_text_sales'] ) );
    }

    // 4. Checkbox (Sales)
    // Checkboxes are tricky: if unchecked, they are not sent in $_POST.
    $checkbox_value = isset( $_POST['_checkbox_sales'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_checkbox_sales', $checkbox_value );

    // 5. Select (Availability)
    if ( isset( $_POST['_select'] ) ) {
        update_post_meta( $post_id, '_select', sanitize_text_field( $_POST['_select'] ) );
    }
}