<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>

    
    </main><!-- End of main content -->

    <?php if (!is_checkout() && !is_product() && !is_cart()) { ?>
    <div class="outermarketing"><!-- outer marketing start -->
        <div class="wrapper"><!-- wrapper start -->
        <section class="outer_newsletter" aria-labelledby="newsletter-heading">
            <h4 id="newsletter-heading">
                <?php esc_html_e('Εγγραφή στο Newsletter', 'walkbyme'); ?>
            </h4>
            <p>
                <?php esc_html_e('Κάνε εγγραφή τώρα για να μαθαίνεις πρώτος τις προσφορές μας, τα συναρπαστικά σχέδια, τις καινούργιες αφίξεις και πολλά άλλα.', 'walkbyme'); ?>
            </p>
            <div class="klaviyo-form-S7Kwjh"></div>
        </section><!-- outer newsletter end -->
        
        <section id="outerexperiences" aria-labelledby="experiences-heading">
            <h2 id="experiences-heading" class="visually-hidden">
                <?php esc_html_e('Οι Υπηρεσίες μας', 'walkbyme'); ?>
            </h2>
            <ul class="experience-list">
                <li class="experience-item">
                    <h4><?php esc_html_e('Επιδιόρθωση και συντήρηση των κοσμημάτων', 'walkbyme'); ?></h4>
                    <p>
                        <?php esc_html_e('Επιδιόρθωση και συντήρηση των κοσμημάτων για τη διατήρηση της ποιότητας τους και την επίλυση προβλημάτων.', 'walkbyme'); ?>
                    </p>
                </li>
                <li class="experience-item">
                    <h4><?php esc_html_e('Εγγύηση ποιότητας', 'walkbyme'); ?></h4>
                    <p>
                        <?php esc_html_e('Αν υπάρχει υποιοδήποτε πρόβλημα με το κόσμημα εντός ενός συγκεκριμένου χρονικό διάστημα μπορείτε να το επιστρέψετε για επισκευή ή αντικατάσταση.', 'walkbyme'); ?>
                    </p>
                </li>
                <li class="experience-item">
                    <h4><?php esc_html_e('Συμβουλές για στυλ και τάσεις', 'walkbyme'); ?></h4>
                    <p>
                        <?php esc_html_e('Σας παρέχουμε συμβουλές για το πώς μπορείτε να φορέσετε και να συνδιάσετε ώστε να επιλέξετε τα κατάλληλα κοσμήματα για κάθε περίπτωση.', 'walkbyme'); ?>
                    </p>
                </li>
                <li class="experience-item">
                    <h4><?php esc_html_e('Εξυπηρέτηση πελατών υψηλής ποιότητας', 'walkbyme'); ?></h4>
                    <p>
                        <?php esc_html_e('Απαντάμε σε ερωτήσεις και επιλύουμε προβλήματα που μπορεί να προκύψουν μετά την αγορά του κοσμήματος.', 'walkbyme'); ?>
                    </p>
                </li>
            </ul>
        </section><!-- outer experience end -->
        </div><!-- wrapper end -->
    </div><!-- outer marketing end -->
    <?php } ?>
    <footer class="site-footer">
        <div class="wrapper"><!-- wrapper start -->
            <div class="footer_details">
                <section class="footer-widgets">
                    
                        <?php dynamic_sidebar('Footer first'); ?>
                    
                    
                    <div class="module_cat widget_nav_menu">
                        <h3><?php esc_html_e('Walk By Me', 'walkbyme'); ?></h3>
                        <div class="contact-info">
                            <ul>
                                <li>
                                    <address><?php esc_html_e('Ελασσώνος 16', 'walkbyme'); ?></address>
                                </li>
                                <li>
                                    <address><?php esc_html_e('121 37 Περιστέρι, Ελλάδα', 'walkbyme'); ?></address>
                                </li>
                                <li>
                                    <a href="tel:+306975686473">+30 697 5686 473</a>
                                </li>
                                <li>
                                    <a href="mailto:info@walkbyme.gr">info@walkbyme.gr</a>
                                </li>
                                <li>
                                    <ul class="social-links">
                                        <li>
                                            <a href="https://www.facebook.com/profile.php?id=100088213807890" 
                                               target="_blank" 
                                               rel="noopener noreferrer" 
                                               aria-label="<?php esc_attr_e('Ακολουθήστε μας στο Facebook', 'walkbyme'); ?>">
                                                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/facebook.png" 
                                                     alt="<?php esc_attr_e('Facebook λογότυπο', 'walkbyme'); ?>" 
                                                     width="24" 
                                                     height="24" 
                                                     loading="lazy" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.instagram.com/walkby_me/" 
                                               target="_blank" 
                                               rel="noopener noreferrer" 
                                               aria-label="<?php esc_attr_e('Ακολουθήστε μας στο Instagram', 'walkbyme'); ?>">
                                                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/instagram.png" 
                                                     alt="<?php esc_attr_e('Instagram λογότυπο', 'walkbyme'); ?>" 
                                                     width="24" 
                                                     height="24" 
                                                     loading="lazy" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.tiktok.com/@walkbyme_jewellery?lang=el-GR" 
                                               target="_blank" 
                                               rel="noopener noreferrer" 
                                               aria-label="<?php esc_attr_e('Ακολουθήστε μας στο TikTok', 'walkbyme'); ?>">
                                                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/tiktok.png" 
                                                     alt="<?php esc_attr_e('TikTok λογότυπο', 'walkbyme'); ?>" 
                                                     width="24" 
                                                     height="24" 
                                                     loading="lazy" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://gr.pinterest.com/walkbyme/" 
                                               target="_blank" 
                                               rel="noopener noreferrer" 
                                               aria-label="<?php esc_attr_e('Ακολουθήστε μας στο Pinterest', 'walkbyme'); ?>">
                                                <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/pinterest.png" 
                                                     alt="<?php esc_attr_e('Pinterest λογότυπο', 'walkbyme'); ?>" 
                                                     width="24" 
                                                     height="24" 
                                                     loading="lazy" />
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <?php 
                    // Additional footer widgets with proper checks
                    if (is_active_sidebar('Footer second')) {
                        dynamic_sidebar('Footer second'); 
                    }
                    if (is_active_sidebar('Footer third')) {
                        dynamic_sidebar('Footer third');
                    }
                    if (is_active_sidebar('Footer fourth')) {
                        dynamic_sidebar('Footer fourth');
                    }
                    ?>
                </section>
            </div>
        </div><!-- wrapper end -->
    </footer>
    
    <div class="powerby"><!-- power by start -->
        <div class="wrapper"><!-- wrapper start -->
            <div class="copyright">
                <ul>
                    <li>
                        &copy; 2023 - <?php echo esc_html(date('Y')); ?> <?php esc_html_e('Walk By Me - all rights reserved.', 'walkbyme'); ?>
                    </li>
                    <li>
                        <?php esc_html_e('Product Photography by Angel Koulogiannis', 'walkbyme'); ?> | 
                        <a href="https://www.nedahost.gr" 
                           title="<?php esc_attr_e('Nedahost Κατασκευή ιστοσελίδων', 'walkbyme'); ?>" 
                           target="_blank" 
                           rel="noopener">
                            <?php esc_html_e('Design & Development by Nedahost', 'walkbyme'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div><!-- wrapper end -->
    </div><!-- power by end -->
    
    <?php wp_footer(); ?>
    
    <?php if (is_product() || is_checkout() || is_cart()) { ?>
        <!-- Custom scripts for specific pages -->
        <script>
            // Add any custom JavaScript for product, checkout, or cart pages here
            console.log('<?php echo esc_js(is_product() ? "Product page" : (is_checkout() ? "Checkout page" : "Cart page")); ?> loaded');
        </script>
    <?php } ?>
    
</body>    
</html>