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
        $field = array(
            'id' => '_text_field',
            'label' => __( 'Τίτλος προϊόντων για τις κατηγορίες:', 'walkbyme' ),
            'placeholder' => 'Τίτλος προϊόντων για τις κατηγορίες...',
            'desc_tip'    => 'true',
            'description' => __( 'Προσθήκη τίτλου', 'walkbyme' ) 
        );
        woocommerce_wp_text_input( $field );
        
        $kataskeuastis = array(
            'id' => '_text_manufacturer',
            'label' => __( 'Κατασκευαστής:', 'walkbyme' ),
            'placeholder' => 'Κατασκευαστής...',
            'desc_tip'    => 'true',
            'description' => __( 'Κατασκευαστής', 'walkbyme' ) 
        );
        woocommerce_wp_text_input( $kataskeuastis );
        
        $prosfores = array(
            'id' => '_text_sales',
            'label' => __( 'Τύπος Προφοράς:', 'walkbyme' ),
            'placeholder' => 'Τύπος Προφοράς...',
            'desc_tip'    => 'true',
            'description' => __( 'Τύπος Προφοράς', 'walkbyme' ) 
        );
        woocommerce_wp_text_input( $prosfores );
        
        
        $provliprosfores = array( 
                'id'            => '_checkbox_sales', 
                'wrapper_class' => 'show_if_simple', 
                'label'         => __('Επιλογή Προσοφράς Αρχική Σελίδας', 'woocommerce' ), 
                'description'   => __( 'Τσεκάρουμε αν θέλουμε το προϊόν στην αρχική σελίδα ως προσφορά', 'woocommerce' ) 
                );
        
        woocommerce_wp_checkbox($provliprosfores);
        
        $diathesimo =array( 
                'id'      => '_select', 
                'label'   => __( 'Διαθέσιμο σε*', 'woocommerce' ), 
                'options' => array(
                        'Διαθέσιμο'   => __( 'Διαθέσιμο', 'woocommerce' ),
                        '1 - 3 Ημέρες'   => __( '1 - 3 Ημέρες', 'woocommerce' ),
                        'Κατόπιν Παραγγελίας' => __( 'Κατόπιν Παραγγελίας', 'woocommerce' )
                        )
                );
        woocommerce_wp_select($diathesimo);     
        ?>
    </div>
  </div>
  <?php
}

add_action( 'woocommerce_process_product_meta', 'save_custom_field' );
function save_custom_field( $post_id ) {
    
    $text_field_value = isset($_POST['_text_field']) ? $_POST['_text_field'] : '';
    $text_kataskeuastis_value = isset($_POST['_text_manufacturer']) ? $_POST['_text_manufacturer'] : '';
    $text_prosfores_value = isset($_POST['_text_sales']) ? $_POST['_text_sales'] : '';
    $text_provliprosfores_value = isset($_POST['_checkbox_sales']) ? $_POST['_checkbox_sales'] : '';
    
    $text_diathesimo_value = isset($_POST['_select']) ? $_POST['_select'] : '';
    
    $product = wc_get_product( $post_id );

    $product->update_meta_data( '_text_field', $text_field_value );
    $product->update_meta_data( '_text_manufacturer', $text_kataskeuastis_value );
    $product->update_meta_data( '_text_sales', $text_prosfores_value );
    $product->update_meta_data( '_checkbox_sales', $text_provliprosfores_value );
    $product->update_meta_data( '_select', $text_diathesimo_value );
    
    $product->save();
}