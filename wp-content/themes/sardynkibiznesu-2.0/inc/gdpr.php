<?php

add_action('wp_footer', 'gdpr_settings_bar');
function gdpr_settings_bar() {
  ?>
  <div class="gdpr__offcanvas offcanvas offcanvas-bottom h-auto" data-bs-backdrop="false" tabindex="-1"
       id="offcanvasGdpr"
       aria-labelledby="offcanvasGdprLabel">
    <div class=" text-center pt-3">
      <h5 class="offcanvas-title mb-0" id="offcanvasBottomLabel"><?php _e('STRONA WYKORZYSTUJE PLIKI COOKIE', 'sb'); ?>
        .</h5>
    </div>
    <div class="offcanvas-body pt-2 small">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <p class="small text-center">Strona internetowa Sardynkibiznesu.pl wykorzystuje pliki cookies. Pliki są
            wykorzystywane przez nas i naszych partnerów w przetwarzaniu danych w celach; statystycznych (mierzenia
            ruchu i skuteczności działań), reklamowych (personalizacji reklam) oraz funkcjonalnych (prawidłowego
            działania strony). W każdej chwili możesz zdecydować, które kategorie plików cookies chcesz wyłączyć. Więcej
            informacji o zakresie przetwarzania danych oraz Twoich uprawnieniach znajdziesz w naszej <a
              href="/polityka-prywatnosci" target="_blank">polityce prywatności</a>.</p>
          <div class="gdpr">
            <div class="gdpr__accordion-collapse accordion-collapse collapse">
              <div class="gdpr__checkbox-container">
                <div class="form-check form-switch">
                  <input type="checkbox" checked disabled class="form-check-input gdpr__checkbox"
                         name="gdpr-category-technical"
                         id="gdpr-category-technical"
                         value="technical"/>
                  <label class="form-check-label form-"
                         for="flexSwitchCheckDefault"><?php _e('Ciasteczka techniczne', 'sb') ?></label>
                  <p class="small">Techniczne Pliki Cookies są wykorzystywane do prawidłowego działania portalu i
                    bezpiecznej komunikacji zwrotnej z klientami. Ich użycie jest niezbędne do funkcjonowania serwisu
                    sardynkibiznesu.pl.</p>
                </div>
                <div class="form-check form-switch">
                  <input type="checkbox" class="form-check-input gdpr__checkbox" name="gdpr-category-analytic"
                         id="gdpr-category-analytic"
                         value="analytic"/>
                  <label class="form-check-label"
                         for="gdpr-category-analytic"><?php _e('Ciasteczka analityczne', 'sb') ?></label>
                  <p class="small">
                    Analityczne Pliki Cookies pozwalają zbierać informacje o interakcjach oraz zachowaniach użytkowników
                    w
                    naszym serwisie.
                  </p>
                </div>
                <div class="form-check form-switch">
                  <input type="checkbox" class="form-check-input gdpr__checkbox" name="gdpr-category-marketing"
                         id="gdpr-category-marketing"
                         value="marketing"/>
                  <label class="form-check-label"
                         for="gdpr-category-marketing"><?php _e('Ciasteczka marketingowe', 'sb') ?></label>
                  <p class="small">Reklamowe Pliki Cookies służą do dostosowania reklam oraz treści wyświetlanych
                    użytkownikowi na naszej stronie lub stronach naszych partnerów reklamowych.</p>
                </div>
              </div>
            </div>
            <div class="row align-items-end">
              <div class="col-auto me-auto my-2 my-lg-0">
                <a href="#" class="gdpr__settings-toggler"><i class="fa fa-gear"></i> Pokaż ustawienia cookies</a>
              </div>
              <div class="col-auto my-2 my-lg-0">
                <button class="btn btn-outline-danger gdpr__submit-selected d-none">Zatwierdź wybrane</button>
              </div>
              <div class="col-auto my-2 my-lg-0">
                <button class="btn btn-danger gdpr__submit-all">Zatwierdź wszystkie</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <?php
}
