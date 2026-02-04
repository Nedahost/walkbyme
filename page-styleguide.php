<?php
/* Template Name: Style Guide */
get_header(); 
?>

<main class="site-main">
    <div class="wrapper content-wrapper"> <section class="style-section mb-6">
            <h2 class="h1 mb-4" style="border-bottom: 2px solid var(--clr-gray-200);">1. Typography</h2>
            
            <div class="grid grid-2-cols mb-4">
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

            <div class="mb-4">
                <p class="lead">Αυτό είναι ένα Lead Paragraph (εισαγωγικό). Είναι λίγο μεγαλύτερο από το κανονικό.</p>
                <p>Αυτό είναι το βασικό κείμενο (Body). Lorem ipsum dolor sit amet, consectetur adipiscing elit. Χρησιμοποιούμε το <strong>Bold</strong> για έμφαση και το <em>Italic</em> για πλάγια γραφή. <a href="#">Αυτό είναι ένα link</a> μέσα στο κείμενο.</p>
                <p class="text-small text-muted">Αυτό είναι μικρό κείμενο (small/caption) για επεξηγήσεις.</p>
            </div>
        </section>

        <section class="style-section mb-6">
            <h2 class="h1 mb-4" style="border-bottom: 2px solid var(--clr-gray-200);">2. Spacing Check</h2>
            <p>Ελέγχουμε τα κενά (Margins/Paddings) στο κινητό.</p>

            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--grid-gutter);">
                <div class="box p-4 bg-gray-100">Box 1 (Padding 4)</div>
                <div class="box p-4 bg-gray-100">Box 2 (Padding 4)</div>
                <div class="box p-4 bg-gray-100">Box 3 (Padding 4)</div>
                <div class="box p-4 bg-gray-100">Box 4 (Padding 4)</div>
            </div>
        </section>

        <section class="style-section mb-6">
            <h2 class="h1 mb-4" style="border-bottom: 2px solid var(--clr-gray-200);">3. Buttons</h2>
            
            <button class="btn btn--primary">Κουμπί</button>
            <button class="btn btn--primary btn--full btn--lg">Προσθήκη στο καλάθι</button>
            
        </section>

        <section class="style-section mb-6">
            <h2 class="h1 mb-4" style="border-bottom: 2px solid var(--clr-gray-200);">5. E-shop Elements</h2>
            
            <h3 class="h4 mb-3">Τιμές & Badges</h3>
            <div class="d-flex align-items-center mb-4" style="gap: 2rem;">
                <span class="price" style="font-size: 1.25rem; font-weight: bold; color: var(--clr-text);">25.00€</span>
                
                <div class="price-group">
                    <del style="color: var(--clr-text-muted); margin-right: 5px;">35.00€</del>
                    <span class="price ins" style="color: var(--clr-error); font-weight: bold; font-size: 1.25rem;">25.00€</span>
                </div>

                <span class="badge" style="background: var(--clr-error); color: white; padding: 2px 8px; font-size: 0.75rem; border-radius: 4px;">SALE</span>
                <span class="badge" style="background: var(--clr-success); color: white; padding: 2px 8px; font-size: 0.75rem; border-radius: 4px;">NEW</span>
                <span class="badge" style="background: var(--clr-gray-500); color: white; padding: 2px 8px; font-size: 0.75rem; border-radius: 4px;">OUT OF STOCK</span>
            </div>

            <h3 class="h4 mb-3">Product Card (Το βασικό "τουβλάκι")</h3>
            <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: var(--grid-gutter);">
                
                <div class="product-card" style="border: 1px solid var(--clr-border); padding: 1rem; border-radius: var(--border-radius);">
                    <div class="product-image mb-3" style="background: var(--clr-gray-200); height: 200px; display: flex; align-items: center; justify-content: center;">
                        <span class="text-muted">Product Image</span>
                    </div>
                    <div class="product-info">
                        <span class="text-muted text-small">Category</span>
                        <h3 class="h5 mt-1 mb-2"><a href="#" style="text-decoration: none; color: var(--clr-text);">Όνομα Προϊόντος</a></h3>
                        <div class="price-box mb-3">
                            <span class="price" style="color: var(--clr-primary); font-weight: bold;">45.00€</span>
                        </div>
                        <button class="btn btn-primary w-100">Προσθήκη στο καλάθι</button>
                    </div>
                </div>

            </div>
        </section>

        <section class="style-section mb-6">
            <h2 class="h1 mb-4" style="border-bottom: 2px solid var(--clr-gray-200);">6. Q&A & Accordions</h2>
            
            <p class="mb-3">Ιδανικό για Q&A προϊόντων και Blog.</p>
            
            <div class="accordion-wrapper" style="max-width: 600px;">
                
                <details class="accordion-item" style="border-bottom: 1px solid var(--clr-border); padding: 1rem 0;">
                    <summary style="cursor: pointer; font-weight: 600; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                        Πώς μπορώ να επιστρέψω ένα προϊόν;
                        <span class="icon">+</span>
                    </summary>
                    <div class="accordion-content mt-2 text-muted">
                        Μπορείτε να επιστρέψετε το προϊόν εντός 14 ημερών...
                    </div>
                </details>

                <details class="accordion-item" style="border-bottom: 1px solid var(--clr-border); padding: 1rem 0;">
                    <summary style="cursor: pointer; font-weight: 600; list-style: none; display: flex; justify-content: space-between; align-items: center;">
                        Πόσο κοστίζουν τα μεταφορικά;
                        <span class="icon">+</span>
                    </summary>
                    <div class="accordion-content mt-2 text-muted">
                        Τα μεταφορικά είναι δωρεάν για αγορές άνω των 50€.
                    </div>
                </details>

            </div>
        </section>

        <section class="style-section mb-6">
            <h2 class="h1 mb-4" style="border-bottom: 2px solid var(--clr-gray-200);">7. Forms & Inputs</h2>
            
            <div class="grid grid-2-cols" style="gap: var(--space-4);">
                <div>
                    <label class="d-block mb-1 font-weight-bold">Email Address</label>
                    <input type="email" placeholder="name@example.com" style="width: 100%; padding: 10px; border: 1px solid var(--clr-border); background: var(--clr-input-bg); color: var(--clr-text);">
                </div>
                
                <div>
                    <label class="d-block mb-1 font-weight-bold">Επιλογή</label>
                    <select style="width: 100%; padding: 10px; border: 1px solid var(--clr-border); background: var(--clr-input-bg); color: var(--clr-text);">
                        <option>Επιλογή 1</option>
                        <option>Επιλογή 2</option>
                    </select>
                </div>

                <div class="d-flex align-items-center" style="gap: 10px;">
                    <input type="checkbox" id="check1">
                    <label for="check1">Αποδέχομαι τους όρους χρήσης (Checkbox)</label>
                </div>
            </div>
        </section>

        <section class="style-section">
            <h2 class="h1 mb-4" style="border-bottom: 2px solid var(--clr-gray-200);">4. Colors (Theme Check)</h2>
            <div class="d-flex flex-wrap" style="gap: var(--space-3);">
                <div class="p-4 text-white bg-primary">Primary</div>
                <div class="p-4 text-white bg-secondary">Secondary</div>
                <div class="p-4 text-white bg-success">Success</div>
                <div class="p-4 text-white bg-error">Error</div>
                <div class="p-4 bg-background border">Background</div>
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?>