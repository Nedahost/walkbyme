<?php
/**
 * The template for displaying the footer
 */
?>

</main><?php if (!is_checkout() && !is_product() && !is_cart()) { ?>
<div class="outermarketing"><div class="wrapper"><section class="outer_newsletter" aria-labelledby="newsletter-heading">
            <h4 id="newsletter-heading">
                <?php esc_html_e('Εγγραφή στο Newsletter', 'walkbyme'); ?>
            </h4>
            <p>
                <?php esc_html_e('Κάνε εγγραφή τώρα για να μαθαίνεις πρώτος τις προσφορές μας, τα συναρπαστικά σχέδια, τις καινούργιες αφίξεις και πολλά άλλα.', 'walkbyme'); ?>
            </p>
            <div class="klaviyo-form-container">
                <div class="klaviyo-form-S7Kwjh"></div>
            </div>
        </section><section id="outerexperiences" aria-labelledby="experiences-heading">
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
                        <?php esc_html_e('Αν υπάρχει οποιοδήποτε πρόβλημα με το κόσμημα εντός ενός συγκεκριμένου χρονικού διαστήματος μπορείτε να το επιστρέψετε για επισκευή ή αντικατάσταση.', 'walkbyme'); ?>
                    </p>
                </li>
                <li class="experience-item">
                    <h4><?php esc_html_e('Συμβουλές για στυλ και τάσεις', 'walkbyme'); ?></h4>
                    <p>
                        <?php esc_html_e('Σας παρέχουμε συμβουλές για το πώς μπορείτε να φορέσετε και να συνδυάσετε ώστε να επιλέξετε τα κατάλληλα κοσμήματα για κάθε περίπτωση.', 'walkbyme'); ?>
                    </p>
                </li>
                <li class="experience-item">
                    <h4><?php esc_html_e('Εξυπηρέτηση πελατών υψηλής ποιότητας', 'walkbyme'); ?></h4>
                    <p>
                        <?php esc_html_e('Απαντάμε σε ερωτήσεις και επιλύουμε προβλήματα που μπορεί να προκύψουν μετά την αγορά του κοσμήματος.', 'walkbyme'); ?>
                    </p>
                </li>
            </ul>
        </section></div></div><?php } ?>

<footer id="colophon" class="site-footer">
    <div class="wrapper"><div class="footer_details">
            <section class="footer-widgets">
                
                <?php if ( is_active_sidebar('Footer first') ) : ?>
                    <div class="footer-widget-area">
                        <?php dynamic_sidebar('Footer first'); ?>
                    </div>
                <?php endif; ?>
                
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
                                    <?php 
                                    $socials = [
                                        'facebook' => 'https://www.facebook.com/profile.php?id=100088213807890',
                                        'instagram' => 'https://www.instagram.com/walkby_me/',
                                        'tiktok' => 'https://www.tiktok.com/@walkbyme_jewellery?lang=el-GR',
                                        'pinterest' => 'https://gr.pinterest.com/walkbyme/'
                                    ];
                                    
                                    foreach ($socials as $network => $link) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($link); ?>" 
                                               target="_blank" 
                                               rel="noopener noreferrer" 
                                               aria-label="<?php echo esc_attr(sprintf(__('Ακολουθήστε μας στο %s', 'walkbyme'), ucfirst($network))); ?>">
                                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/' . $network . '.png'); ?>" 
                                                     alt="<?php echo esc_attr(ucfirst($network) . ' λογότυπο'); ?>" 
                                                     width="24" 
                                                     height="24" 
                                                     loading="lazy" />
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <?php 
                $extra_sidebars = ['Footer second', 'Footer third', 'Footer fourth'];
                foreach ($extra_sidebars as $sidebar) {
                    if (is_active_sidebar($sidebar)) {
                        dynamic_sidebar($sidebar);
                    }
                }
                ?>
            </section>
        </div>
    </div></footer>

<div class="powerby"><div class="wrapper"><div class="copyright">
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
    </div></div><?php wp_footer(); ?>
</body>    
</html>