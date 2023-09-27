

<?php 

/**
 * Template name: Contact page
 */


get_header(); ?>  
<?php if(!is_home()){ ?>
    <div class="outerbreadcrumb"><!-- outer breadcrumb start -->
        <div class="wrapper"><!-- wrapper start -->
            <?php if(function_exists('bcn_display'))
            {
                bcn_display();
            }?>
        </div><!-- wrapper end -->
    </div><!-- outer breadcrumb end -->
<?php } ?>

    <div class="wrapper"><!-- wrapper start -->
    <?php
if (isset($_POST['submitted'])) {
    $name = sanitize_text_field($_POST['contactName']);
    $email = sanitize_email($_POST['email']);
    $comments = esc_textarea($_POST['comments']);

    $nameError = '';
    $emailError = '';
    $commentError = '';

    if (empty($name)) {
        $nameError = 'Παρακαλώ δώστε όνομα';
    }

    if (empty($email)) {
        $emailError = 'Παρακαλώ δώστε email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = 'Παρακαλώ δώστε ένα έγκυρο email.';
    }

    if (empty($comments)) {
        $commentError = 'Παρακαλώ δώστε μήνυμα.';
    }

    if (empty($nameError) && empty($emailError) && empty($commentError)) {
        $to = 'info@walkbyme.gr'; // Εδώ βάλτε το δικό σας email
        $subject = 'Από ' . $name;
        $body = "Όνομα: $name \n\nEmail: $email \n\nΜήνυμα: $comments";
        $headers = "From: $name <$email>\r\n";
        $headers .= "Reply-To: $email\r\n";

        if (wp_mail($to, $subject, $body, $headers)) {
            $emailSent = true;
        } else {
            echo "Υπήρξε ένα πρόβλημα κατά την αποστολή του μηνύματος.";
        }
    }
}
?>

<div class="entry-content">
    <?php if (isset($emailSent) && $emailSent == true) { ?>
        <div class="thanks">
            <p>Ευχαριστούμε, Ολοκληρώθηκε με επιτυχία</p>
        </div>
    <?php } else { ?>
        <?php if (!empty($nameError) || !empty($emailError) || !empty($commentError)) { ?>
            <p class="error_mess">Παρακαλώ συμπληρώστε σωστά τα πεδία.</p>
        <?php } ?>

        <form action="<?php the_permalink(); ?>" id="contactForm" method="post">
            <ul class="contactform">
                <li>
                    <label for="contactName">Όνομα:</label><br />
                    <input type="text" name="contactName" id="contactName" value="<?php echo isset($_POST['contactName']) ? esc_attr($_POST['contactName']) : ''; ?>" class="required requiredField" />
                    <?php if (!empty($nameError)) { ?>
                        <br /><span class="error"><?= $nameError; ?></span>
                    <?php } ?>
                </li>

                <li>
                    <label for="email">Email:</label><br />
                    <input type="text" name="email" id="email" value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" class="required requiredField email_form" />
                    <?php if (!empty($emailError)) { ?>
                        <br /><span class="error"><?= $emailError; ?></span>
                    <?php } ?>
                </li>

                <li>
                    <label for="commentsText">Μήνυμα:</label><br />
                    <textarea name="comments" id="commentsText" rows="10" cols="52" class="required requiredField"><?php echo isset($_POST['comments']) ? esc_textarea($_POST['comments']) : ''; ?></textarea>
                    <?php if (!empty($commentError)) { ?>
                        <br /><span class="error"><?= $commentError; ?></span>
                    <?php } ?>
                </li>

                <li>
                    <input type="submit" class="button_send" value="Αποστολή"></input>
                </li>
            </ul>
            <input type="hidden" name="submitted" id="submitted" value="true" />
        </form>
    <?php } ?>
</div><!-- .entry-content -->

</div><!-- wrapper end -->
<?php get_footer();