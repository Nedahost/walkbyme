<?php
add_action( 'woocommerce_product_write_panel_tabs', 'my_custom_tab_action' );
/**
 * Adding a custom tab
 */
function my_custom_tab_action() {
  ?>
  <li class="custom_tab">
    <a href="#the_custom_panel">
      <span><?php _e( 'Στοιχεία Προϊόντος', 'walkbyme' ); ?></span>
    </a>
  </li>
  <?php
}

add_action( 'woocommerce_product_data_panels', 'custom_tab_panel' );

function custom_tab_panel() {
    ?>
    <div id="the_custom_panel" class="panel woocommerce_options_panel">
        <div class="options_group">
            <?php  
            woocommerce_wp_text_input( array(
                'id' => '_text_field',
                'label' => __( 'Τίτλος προϊόντων για τις κατηγορίες:', 'walkbyme' ),
                'placeholder' => 'Τίτλος προϊόντων για τις κατηγορίες...',
                'desc_tip'    => 'true',
                'description' => __( 'Προσθήκη τίτλου', 'walkbyme' ) 
            ));

            woocommerce_wp_text_input( array(
                'id' => '_text_manufacturer',
                'label' => __( 'Κατασκευαστής:', 'walkbyme' ),
                'placeholder' => 'Κατασκευαστής...',
                'desc_tip'    => 'true',
                'description' => __( 'Κατασκευαστής', 'walkbyme' ) 
            ));

            woocommerce_wp_text_input( array(
                'id' => '_text_sales',
                'label' => __( 'Τύπος Προσφοράς:', 'walkbyme' ),
                'placeholder' => 'Τύπος Προσφοράς...',
                'desc_tip'    => 'true',
                'description' => __( 'Τύπος Προσφοράς', 'walkbyme' ) 
            ));

            woocommerce_wp_checkbox( array( 
                'id'            => '_checkbox_sales', 
                'wrapper_class' => 'show_if_simple', 
                'label'         => __('Επιλογή Προσφοράς Αρχική Σελίδας', 'walkbyme' ), 
                'description'   => __( 'Τσεκάρουμε αν θέλουμε το προϊόν στην αρχική σελίδα ως προσφορά', 'walkbyme' ) 
            ));

            woocommerce_wp_select( array( 
                'id'      => '_select', 
                'label'   => __( 'Διαθέσιμο σε*', 'walkbyme' ), 
                'options' => array(
                    'Άμεσα διαθέσιμο'           => __( 'Άμεσα διαθέσιμο', 'walkbyme' ),
                    '4 - 7 Ημέρες'        => __( '4 - 7 Ημέρες', 'walkbyme' ),
                    'Κατόπιν Παραγγελίας' => __( 'Κατόπιν Παραγγελίας', 'walkbyme' )
                )
            ));     
            ?>
        </div>
    </div>
    <?php
}

add_action( 'woocommerce_process_product_meta', 'save_custom_field' );
function save_custom_field( $post_id ) {

    // Ensure fields are set and sanitize input
    $text_field_value = isset($_POST['_text_field']) ? sanitize_text_field( $_POST['_text_field'] ) : '';
    $text_kataskeuastis_value = isset($_POST['_text_manufacturer']) ? sanitize_text_field( $_POST['_text_manufacturer'] ) : '';
    $text_prosfores_value = isset($_POST['_text_sales']) ? sanitize_text_field( $_POST['_text_sales'] ) : '';
    $text_provliprosfores_value = isset($_POST['_checkbox_sales']) ? sanitize_text_field( $_POST['_checkbox_sales'] ) : '';
    $text_diathesimo_value = isset($_POST['_select']) ? sanitize_text_field( $_POST['_select'] ) : '';

    // Update the product meta data
    $product = wc_get_product( $post_id );

    $product->update_meta_data( '_text_field', $text_field_value );
    $product->update_meta_data( '_text_manufacturer', $text_kataskeuastis_value );
    $product->update_meta_data( '_text_sales', $text_prosfores_value );
    $product->update_meta_data( '_checkbox_sales', $text_provliprosfores_value );
    $product->update_meta_data( '_select', $text_diathesimo_value );

    $product->save();
}
