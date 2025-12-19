<?php
/**
 * The template for displaying the footer
 */
?>

</main><?php 
// Marketing Section (Newsletter & Services)
// Only show on non-transactional pages
if (!is_checkout() && !is_product() && !is_cart()) { ?>
    <div class="outermarketing">
        <div class="wrapper">
            
            <section class="outer_newsletter" aria-labelledby="newsletter-heading">
                <h4 id="newsletter-heading"><?php esc_html_e('Εγγραφή στο Newsletter', 'walkbyme'); ?></h4>
                <p><?php esc_html_e('Κάνε εγγραφή τώρα για να μαθαίνεις πρώτος τις προσφορές μας, τα συναρπαστικά σχέδια, τις καινούργιες αφίξεις και πολλά άλλα.', 'walkbyme'); ?></p>
                <div class="klaviyo-form-container">
                    <div class="klaviyo-form-S7Kwjh"></div>
                </div>
            </section>
            
            <section id="outerexperiences" aria-labelledby="experiences-heading">
                <h2 id="experiences-heading" class="visually-hidden"><?php esc_html_e('Οι Υπηρεσίες μας', 'walkbyme'); ?></h2>
                <ul class="experience-list">
                    <li class="experience-item">
                        <h4><?php esc_html_e('Επιδιόρθωση και συντήρηση', 'walkbyme'); ?></h4>
                        <p><?php esc_html_e('Επιδιόρθωση και συντήρηση των κοσμημάτων για τη διατήρηση της ποιότητας τους.', 'walkbyme'); ?></p>
                    </li>
                    <li class="experience-item">
                        <h4><?php esc_html_e('Εγγύηση ποιότητας', 'walkbyme'); ?></h4>
                        <p><?php esc_html_e('Δυνατότητα επιστροφής για επισκευή ή αντικατάσταση εντός εγγύησης.', 'walkbyme'); ?></p>
                    </li>
                    <li class="experience-item">
                        <h4><?php esc_html_e('Συμβουλές στυλ', 'walkbyme'); ?></h4>
                        <p><?php esc_html_e('Συμβουλές για το πώς να φορέσετε και να συνδυάσετε τα κοσμήματά σας.', 'walkbyme'); ?></p>
                    </li>
                    <li class="experience-item">
                        <h4><?php esc_html_e('Εξυπηρέτηση πελατών', 'walkbyme'); ?></h4>
                        <p><?php esc_html_e('Άμεση απάντηση σε ερωτήσεις και επίλυση προβλημάτων.', 'walkbyme'); ?></p>
                    </li>
                </ul>
            </section>
        </div>
    </div>
<?php } ?>

<footer id="colophon" class="site-footer">
    <div class="wrapper">
        <div class="footer_details">
            <section class="footer-widgets">
                
                <?php 
                // 1. Dynamic Columns (Τα 3 Μενού)
                // Ελέγχουμε και τυπώνουμε τα 3 sidebars στη σειρά
                for ($i = 1; $i <= 3; $i++) {
                    if ( is_active_sidebar('footer-' . $i) ) {
                        dynamic_sidebar('footer-' . $i);
                    }
                }
                ?>
                
                <div class="module_cat widget_nav_menu">
                    <h3 class="widgettitle"><?php esc_html_e('Walk By Me', 'walkbyme'); ?></h3>
                    <div class="contact-info">
                        <ul>
                            <li><address><?php esc_html_e('Ελασσώνος 16', 'walkbyme'); ?></address></li>
                            <li><address><?php esc_html_e('121 37 Περιστέρι, Ελλάδα', 'walkbyme'); ?></address></li>
                            <li><a href="tel:+306975686473">+30 697 5686 473</a></li>
                            <li><a href="mailto:info@walkbyme.gr">info@walkbyme.gr</a></li>
                            <li>
                                <ul class="social-links">
                                    <?php 
                                    $socials = [
                                        'facebook'  => 'https://www.facebook.com/profile.php?id=100088213807890',
                                        'instagram' => 'https://www.instagram.com/walkby_me/',
                                        'tiktok'    => 'https://www.tiktok.com/@walkbyme_jewellery?lang=el-GR',
                                        'pinterest' => 'https://gr.pinterest.com/walkbyme/'
                                    ];
                                    foreach ($socials as $network => $link) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($network)); ?>">
                                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/' . $network . '.png'); ?>" 
                                                     alt="<?php echo esc_attr($network); ?>" width="24" height="24" loading="lazy" />
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>

            </section>
        </div>
    </div>
</footer>

<div class="powerby">
    <div class="wrapper">
        <div class="copyright">
            <ul>
                <li>&copy; 2023 - <?php echo esc_html(date('Y')); ?> <?php esc_html_e('Walk By Me - all rights reserved.', 'walkbyme'); ?></li>
                <li>
                    <?php esc_html_e('Product Photography by Angel Koulogiannis', 'walkbyme'); ?> | 
                    <a href="https://www.nedahost.gr" target="_blank" rel="noopener"><?php esc_html_e('Design & Development by Nedahost', 'walkbyme'); ?></a>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>    
</html>