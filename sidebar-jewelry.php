<aside id="secondary" class="widget-area">
    <?php if ( is_active_sidebar( 'sidebar-jewelry' ) ) : ?>
        <?php dynamic_sidebar( 'sidebar-jewelry' ); ?>
    <?php else : ?>
        <!-- Προτεινόμενα προϊόντα -->
        <section class="widget widget_featured_products">
            <h2 class="widget-title">Προτεινόμενα Κοσμήματα</h2>
            <?php
            // Εδώ θα προσθέσετε κώδικα για να εμφανίσετε προτεινόμενα προϊόντα
            // Για παράδειγμα, αν χρησιμοποιείτε WooCommerce:
            // echo do_shortcode('[products limit="3" columns="1" orderby="popularity"]');
            ?>
        </section>

        <!-- Κατηγορίες κοσμημάτων -->
        <section class="widget widget_categories">
            <h2 class="widget-title">Κατηγορίες Κοσμημάτων</h2>
            <ul>
                <?php
                wp_list_categories( array(
                    'title_li' => '',
                    'taxonomy' => 'product_cat', // Αν χρησιμοποιείτε WooCommerce
                ) );
                ?>
            </ul>
        </section>

        <!-- Πρόσφατα άρθρα -->
        <section class="widget widget_recent_entries">
            <h2 class="widget-title">Πρόσφατα Άρθρα</h2>
            <?php
            $recent_posts = wp_get_recent_posts(array(
                'numberposts' => 5,
                'post_status' => 'publish'
            ));
            echo '<ul>';
            foreach( $recent_posts as $recent ){
                echo '<li><a href="' . get_permalink($recent["ID"]) . '">' . $recent["post_title"] . '</a></li>';
            }
            echo '</ul>';
            ?>
        </section>

        <!-- Αναζήτηση -->
        <section class="widget widget_search">
            <?php get_search_form(); ?>
        </section>
    <?php endif; ?>
</aside>