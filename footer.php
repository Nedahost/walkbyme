        <div class="wrapper"><!-- wrapper start -->
        <?php if ( !is_checkout() ) { ?>
            <section class="outer_newsletter"><!-- outer newsletter start -->
                <h4>
                    Εγγραφή στο Newsletter 
                </h4>
                <p>
                    Κάνε εγγραφή τώρα για να μαθαίνεις πρώτος τις προσφορές μας, τα συναρπαστικά σχέδια , τις καινούργιες αφίξεις και πολλά άλλα. 
                </p>
                <div class="klaviyo-form-S7Kwjh"></div>
            </section><!-- outer newsletter end -->
            <?php } ?>
        </div><!-- wrapper end -->
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
                                <li>Ράλλη και Παράσχου </li>
                                <li>121 36 Περιστέρι, Ελλάδα </li>
                                <li>697</li>
                                <li>info@walkbyme.gr</li>
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
                    <li>&copy; <?php echo date('Y');  ?> Walk By Me - all rights reserved.</li>
                    <li><a href="https://www.nedahost.gr" alt="Nedahost Κατασκευη ιστοσελιδων" target="_blank" > Design & Development by Nedahost </a></li>
                </ul>
            </div>
        </div><!-- wrapper end -->
    </div><!-- power by end -->
        <?php wp_footer(); ?>
    </body>    
</html>