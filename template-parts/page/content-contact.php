<section id="outercontact"><!-- outer contact start -->
    <h1>
        <?php the_title(); ?>
    </h1>
    <div class="contact">
        <div class="contactdetails" style="text-align: center;"><!-- contact details start -->
            
            <b>Τηλέφωνα επικοινωνίας:</b> +30 697 5686 473 
                
        </div><!-- contact details end -->
        <div class="contactform"><!-- contact form start -->
        <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
    <input type="hidden" name="action" value="custom_contact_form">
    <label for="name">Name:</label>
    <input type="text" name="name" required><br>

    <label for="surname">Surname:</label>
    <input type="text" name="surname" required><br>

    <label for="phone">Phone:</label>
    <input type="tel" name="phone" required><br>

    <label for="message">Message:</label>
    <textarea name="message" rows="4" required></textarea><br>

    <label for="title">Title:</label>
    <select name="title">
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="couple">Couple</option>
    </select><br>

    <input type="submit" value="Submit">
</form>

        </div><!-- contact form end -->
        <div class="clear_0"></div>
    </div>
    <div class="clear_0"></div>
</section><!-- outer contact end -->
<div class="clear_0"></div>