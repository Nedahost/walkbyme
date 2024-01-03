<?php get_header(); ?>
    <div class="search-form-container">
        <form role="search" method="get" id="searchform" action="<?php echo home_url('/'); ?>">
            <div>
                <label class="screen-reader-text" for="s">Αναζήτηση για:</label>
                <input type="text" value="" name="s" id="s" placeholder="Αναζήτηση">
                <input type="submit" id="searchsubmit" value="Αναζήτηση">
            </div>
        </form>
    </div>
<?php get_footer();