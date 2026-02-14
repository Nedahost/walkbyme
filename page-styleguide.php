<?php
/* Template Name: Style Guide */
get_header(); 
?>

<main class="site-main">
    <div class="wrapper content-wrapper">
        
        <!-- 1. TYPOGRAPHY -->
        <section class="style-section">
            <h2 class="style-section__title">1. Typography</h2>
            
            <div class="style-section__grid">
                <div>
                    <h1>Heading 1 - Ο Τίτλος σου</h1>
                    <h2>Heading 2 - Υπότιτλος Ενότητας</h2>
                    <h3>Heading 3 - Τίτλος Κάρτας</h3>
                    <h4>Heading 4 - Μικρότερος Τίτλος</h4>
                    <h5>Heading 5 - Λεπτομέρεια</h5>
                    <h6>Heading 6 - Πολύ μικρός τίτλος</h6>
                </div>
                <div>
                    <p class="text-muted">Παρατήρησε πώς οι τίτλοι μικραίνουν αυτόματα στο κινητό χάρη στο clamp()!</p>
                </div>
            </div>

            <div class="style-section__content">
                <p class="lead">Αυτό είναι ένα Lead Paragraph (εισαγωγικό).</p>
                <p>Αυτό είναι το βασικό κείμενο (Body). Χρησιμοποιούμε το <strong>Bold</strong> για έμφαση και το <em>Italic</em> για πλάγια γραφή. <a href="#">Αυτό είναι ένα link</a> μέσα στο κείμενο.</p>
                <p><small class="text-muted">Αυτό είναι μικρό κείμενο (small/caption).</small></p>
            </div>
        </section>

        <!-- 2. BUTTONS -->
        <section class="style-section">
            <h2 class="style-section__title">2. Buttons</h2>
            
            <div class="button-group">
                <button class="btn btn--primary">Primary</button>
                <button class="btn btn--secondary">Secondary</button>
                <button class="btn btn--outline">Outline</button>
            </div>
            
            <div class="button-group">
                <button class="btn btn--primary btn--sm">Small</button>
                <button class="btn btn--primary">Medium</button>
                <button class="btn btn--primary btn--lg">Large</button>
            </div>
            
            <button class="btn btn--primary btn--full">Full Width Button</button>
        </section>

        <!-- 3. COLORS -->
        <section class="style-section">
            <h2 class="style-section__title">3. Colors</h2>
            
            <div class="color-swatches">
                <div class="color-swatch color-swatch--primary">Primary</div>
                <div class="color-swatch color-swatch--secondary">Secondary</div>
                <div class="color-swatch color-swatch--success">Success</div>
                <div class="color-swatch color-swatch--error">Error</div>
            </div>
        </section>

        <!-- 4. ACCORDION -->
        <section class="style-section">
            <h2 class="style-section__title">4. Accordion</h2>
            <p>Ιδανικό για Q&A προϊόντων και Blog.</p>
            
            <div class="accordion">
                <div class="accordion__item">
                    <div class="accordion__title">
                        Πώς μπορώ να επιστρέψω ένα προϊόν;
                        <span class="accordion__icon"></span>
                    </div>
                    <div class="accordion__content">
                        <div class="accordion__inner">
                            Μπορείτε να επιστρέψετε το προϊόν εντός 14 ημερών...
                        </div>
                    </div>
                </div>
                
                <div class="accordion__item">
                    <div class="accordion__title">
                        Πόσο κοστίζουν τα μεταφορικά;
                        <span class="accordion__icon"></span>
                    </div>
                    <div class="accordion__content">
                        <div class="accordion__inner">
                            Τα μεταφορικά είναι δωρεάν για αγορές άνω των 50€.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. FORMS -->
        <section class="style-section">
            <h2 class="style-section__title">5. Forms</h2>
            
            <form class="form">
                <div class="form__group">
                    <label class="form__label">Email Address</label>
                    <input type="email" class="form__input" placeholder="name@example.com">
                </div>
                
                <div class="form__group">
                    <label class="form__label">Επιλογή</label>
                    <select class="form__select">
                        <option>Επιλογή 1</option>
                        <option>Επιλογή 2</option>
                    </select>
                </div>

                <div class="form__group form__group--checkbox">
                    <input type="checkbox" id="check1" class="form__checkbox">
                    <label for="check1" class="form__label">Αποδέχομαι τους όρους χρήσης</label>
                </div>
            </form>
        </section>

    </div>
</main>

<?php get_footer(); ?>