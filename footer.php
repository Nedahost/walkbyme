    <?php if ( !is_checkout() && !is_product() && !is_cart()) { ?>
        <div class="wrapper"><!-- wrapper start -->
        
            <section class="outer_newsletter"><!-- outer newsletter start -->
                <h4>
                    Εγγραφή στο Newsletter 
                </h4>
                <p>
                    Κάνε εγγραφή τώρα για να μαθαίνεις πρώτος τις προσφορές μας, τα συναρπαστικά σχέδια , τις καινούργιες αφίξεις και πολλά άλλα. 
                </p>
                <div class="klaviyo-form-S7Kwjh"></div>
            </section><!-- outer newsletter end -->
            

            <div id="outerexperiences"><!-- outer experience start -->
                <ul>
                    <li>
                        <h4>
                        Επιδιόρθωση και συντήρηση των κοσμημάτων
                        </h4>
                        <p>
                            Επιδιόρθωση  και συντήρηση των κοσμημάτων για τη διατήρηση της ποιότητας τους και την επίλυση προβλημάτων. 
                        </p>
                    </li>
                    <li>
                        <h4>
                            Εγγύηση ποιότητας
                        </h4>
                        <p>
                            Αν υπάρχει υποιοδήποτε πρόβλημα με το κόσμημα εντός ενός συγκεκριμένου χρονικό διάστημα μπορείτε να το επιστρέψετε για επισκευή ή αντικατάσταση.
                        </p>
                    </li>
                    <li>
                        <h4>
                            Συμβουλές για στυλ και τάσεις
                        </h4>
                        <p>
                            Σας παρέχουμε συμβουλές για το πώς μπορείτε να φορέσετε και να συνδιάσετε ώστε να επιλέξετε τα κατάλληλα κοσμήματα για κάθε περίπτωση.
                        </p>
                    </li>
                    <li>
                        <h4>
                            Εξυπηρέτηση πελατών υψηλής ποιότητας
                        </h4>
                        <p>
                           Απαντάμε σε ερωτήσεις και επιλύουμαι προβλήματα που μπορεί να προκύψουν μετά την αγορά του κοσμήματος.
                        </p>
                    </li>
                </ul>
            </div><!-- outer experience end -->
            
        </div><!-- wrapper end -->
        <?php } ?>
    </main>
    
        
    <footer>
        <div class="wrapper"><!-- wrapper start -->

            <div class="footer_details">
               

                <section>
                    <?php dynamic_sidebar( 'Footer first' ); ?>
                    <div class="module_cat widget_nav_menu">
                        <h3>Walk By Me</h3>
                        <div>
                            <ul>
                                <li><!-- Ελασσώνος 16 --></li>
                                <li>121 37 Περιστέρι, Ελλάδα </li>
                                <li><!-- +30 697 5686 473 --></li>
                                <li>info@walkbyme.gr</li>
                                <li>
                                    <ul>
                                        <li>
                                            <a href="https://www.facebook.com/profile.php?id=100088213807890" target="_blank" rel="noopener noreferrer" title="Facebook">
                                                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/facebook.png" alt="Facebook account"  />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.instagram.com/walkby_me/" target="_blank" rel="noopener noreferrer" title="Instagram">
                                                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/instagram.png" alt="Instagram account" />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.tiktok.com/@walkbyme_jewellery?lang=el-GR" target="_blank" rel="noopener noreferrer" title="TikTok">
                                                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/tiktok.png" alt="TikTok account"  />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://gr.pinterest.com/walkbyme/" target="_blank" rel="noopener noreferrer" title="pinterest">
                                                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/pinterest.png" alt="Pinterest account"  />
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                
            </div>
        </div><!-- wrapper end -->
    </footer>
    <div class="powerby"><!-- power by start -->
        <div class="wrapper"><!-- wrapper start -->
            <div class="copyright">
                <ul>
                    <li>&copy; 2023 - <?php echo date('Y');  ?> Walk By Me - all rights reserved.</li>
                    <li> <!-- Product Photography by Angel Koulogiannis | <a href="https://www.nedahost.gr" title="Nedahost Κατασκευη ιστοσελιδων" target="_blank" > Design & Development by Nedahost </a> --></li>
                </ul>
            </div>
        </div><!-- wrapper end -->
    </div><!-- power by end -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/filters.js"></script>
        <?php wp_footer(); ?>
    </body>    
</html> 