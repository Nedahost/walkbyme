<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>

    <?php if (!is_checkout() && !is_product() && !is_cart()) { ?>
        <div class="wrapper"><!-- wrapper start -->
        
            <section class="outer_newsletter" aria-labelledby="newsletter-heading">
                <h4 id="newsletter-heading">
                    Εγγραφή στο Newsletter 
                </h4>
                <p>
                    Κάνε εγγραφή τώρα για να μαθαίνεις πρώτος τις προσφορές μας, τα συναρπαστικά σχέδια, τις καινούργιες αφίξεις και πολλά άλλα. 
                </p>
                <div class="klaviyo-form-S7Kwjh"></div>
            </section><!-- outer newsletter end -->
            
            <section id="outerexperiences" aria-labelledby="experiences-heading">
                <h2 id="experiences-heading" class="visually-hidden">Οι Υπηρεσίες μας</h2>
                <ul class="experience-list">
                    <li class="experience-item">
                        <h4>Επιδιόρθωση και συντήρηση των κοσμημάτων</h4>
                        <p>
                            Επιδιόρθωση και συντήρηση των κοσμημάτων για τη διατήρηση της ποιότητας τους και την επίλυση προβλημάτων. 
                        </p>
                    </li>
                    <li class="experience-item">
                        <h4>Εγγύηση ποιότητας</h4>
                        <p>
                            Αν υπάρχει υποιοδήποτε πρόβλημα με το κόσμημα εντός ενός συγκεκριμένου χρονικό διάστημα μπορείτε να το επιστρέψετε για επισκευή ή αντικατάσταση.
                        </p>
                    </li>
                    <li class="experience-item">
                        <h4>Συμβουλές για στυλ και τάσεις</h4>
                        <p>
                            Σας παρέχουμε συμβουλές για το πώς μπορείτε να φορέσετε και να συνδιάσετε ώστε να επιλέξετε τα κατάλληλα κοσμήματα για κάθε περίπτωση.
                        </p>
                    </li>
                    <li class="experience-item">
                        <h4>Εξυπηρέτηση πελατών υψηλής ποιότητας</h4>
                        <p>
                           Απαντάμε σε ερωτήσεις και επιλύουμαι προβλήματα που μπορεί να προκύψουν μετά την αγορά του κοσμήματος.
                        </p>
                    </li>
                </ul>
            </section><!-- outer experience end -->
            
        </div><!-- wrapper end -->
    <?php } ?>
    </main><!-- End of main content -->
    
    <footer class="site-footer">
        <div class="wrapper"><!-- wrapper start -->
            <div class="footer_details">
                <section class="footer-widgets">
                    <?php dynamic_sidebar('Footer first'); ?>
                    
                    <div class="module_cat widget_nav_menu">
                        <h3>Walk By Me</h3>
                        <div class="contact-info">
                            <ul>
                                <li><address>Ελασσώνος 16</address></li>
                                <li><address>121 37 Περιστέρι, Ελλάδα</address></li>
                                <li><a href="tel:+306975686473">+30 697 5686 473</a></li>
                                <li><a href="mailto:info@walkbyme.gr">info@walkbyme.gr</a></li>
                                <li>
                                    <ul class="social-links">
                                        <li>
                                            <a href="https://www.facebook.com/profile.php?id=100088213807890" target="_blank" rel="noopener noreferrer" aria-label="Ακολουθήστε μας στο Facebook">
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/facebook.png" alt="Facebook λογότυπο" width="24" height="24" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.instagram.com/walkby_me/" target="_blank" rel="noopener noreferrer" aria-label="Ακολουθήστε μας στο Instagram">
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/instagram.png" alt="Instagram λογότυπο" width="24" height="24" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.tiktok.com/@walkbyme_jewellery?lang=el-GR" target="_blank" rel="noopener noreferrer" aria-label="Ακολουθήστε μας στο TikTok">
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/tiktok.png" alt="TikTok λογότυπο" width="24" height="24" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://gr.pinterest.com/walkbyme/" target="_blank" rel="noopener noreferrer" aria-label="Ακολουθήστε μας στο Pinterest">
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/pinterest.png" alt="Pinterest λογότυπο" width="24" height="24" />
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <?php 
                    // Αν υπάρχουν επιπλέον footer widgets, θα μπορούσαν να προστεθούν εδώ
                    dynamic_sidebar('Footer second'); 
                    dynamic_sidebar('Footer third');
                    dynamic_sidebar('Footer fourth');
                    ?>
                </section>
            </div>
        </div><!-- wrapper end -->
    </footer>
    
    <div class="powerby"><!-- power by start -->
        <div class="wrapper"><!-- wrapper start -->
            <div class="copyright">
                <ul>
                    <li>&copy; 2023 - <?php echo date('Y'); ?> Walk By Me - all rights reserved.</li>
                    <li>Product Photography by Angel Koulogiannis | <a href="https://www.nedahost.gr" title="Nedahost Κατασκευή ιστοσελίδων" target="_blank" rel="noopener">Design & Development by Nedahost</a></li>
                </ul>
            </div>
        </div><!-- wrapper end -->
    </div><!-- power by end -->
    
    <?php wp_footer(); ?>
    
    <?php if (is_product() || is_checkout() || is_cart()) { ?>
    <!-- Προσθήκη τυχόν custom scripts για σελίδες προϊόντων, checkout ή καλάθι -->
    <script>
        // Τυχόν custom JavaScript για αυτές τις σελίδες
    </script>
    <?php } ?>
    
</body>    
</html>