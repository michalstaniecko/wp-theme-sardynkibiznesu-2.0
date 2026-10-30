# Lista kontrolna po aktualizacji WordPressa

Stała lista dla motywu 3.0 (spec #36, historyjka 101). Automatyczne aktualizacje są tylko dla wersji minor. Wersje major i wtyczki najpierw trafiają na staging. Tam, przed wdrożeniem na produkcję, przejdź całą listę.

Sprawdzaj w Chrome i Safari, na desktopie (≥ 1280 px) i na telefonie (≤ 400 px). Rób to raz jako gość i raz po zalogowaniu (pasek admina).

## 1. Akordeon nakładki menu (ryzyko: filtr zależny od wnętrza core)

Filtr `render_block_core/navigation` w `ihumbak-site-blocks` (`includes/navigation-overlay.php`) opiera się na wewnętrznym kontekście Interactivity bloku `core/navigation`. Chodzi o `overlayOpenedBy`, `_isWithinOverlayTemplatePart`, `toggleMenuOnClick` i `handleMenuFocusout`.

- [ ] Poniżej 1024 px (sprawdź też 1023 px i 800 px) zamiast listy menu widać przycisk menu.
- [ ] Przycisk otwiera pełnoekranową nakładkę (`role="dialog"`). Nakładka ma logo, przycisk X, menu oraz przyciski „Zapisz się na newsletter” i „Panel — zaloguj się”.
- [ ] Po otwarciu wszystkie sekcje są zwinięte, a każdy przycisk sekcji ma `aria-expanded="false"`.
- [ ] Kliknięcie sekcji rozwija tylko ją (`aria-expanded="true"`).
- [ ] Kliknięcie **innej sekcji poniżej** otwartej rozwija tę sekcję i zwija poprzednią. To ten sam przypadek, który naprawia `data-wp-on--mousedown` na przyciskach sekcji.
- [ ] Trzeci poziom (np. Twórczość → Przydatne programy) rozwija się wewnątrz drugiego.
- [ ] Klawiatura w nakładce: Tab przechodzi po sekcjach, a Enter i Spacja je rozwijają. Fokus nie wychodzi poza nakładkę.
- [ ] Escape zamyka nakładkę i oddaje fokus przyciskowi menu. Gdy jakaś sekcja jest otwarta, pierwszy Escape zwija tę sekcję, a dopiero drugi zamyka nakładkę (zachowanie core).
- [ ] W źródle strony każde `li.wp-block-navigation-submenu` w nakładce ma w `data-wp-context` własne `"overlayOpenedBy":{"click":false,…}`.

## 2. Klawiatura i mysz w nagłówku (desktop)

- [ ] Pierwszy Tab pokazuje skip link „Przejdź do treści”, który przenosi do `<main>`.
- [ ] Kolejność Tab: logo, pozycje menu, przycisk z chevronem przy rodzicu (rodzic to dwa przystanki), wyszukiwanie, „Newsletter”.
- [ ] Fokus jest widoczny (turkusowy obrys) na każdym elemencie.
- [ ] Najechanie myszą na rodzica otwiera podmenu, a trzeci poziom otwiera się obok podmenu.
- [ ] Enter na przycisku z chevronem otwiera podmenu, a Escape je zamyka i oddaje fokus przyciskowi.
- [ ] Nagłówek jest przyklejony. Po przewinięciu dostaje cień (`data-state="scrolled"`). Po zalogowaniu stoi pod paskiem admina, a nie pod nim schowany.

## 3. Pasek wyszukiwania

- [ ] Z JS ikona lupy to przycisk z `aria-expanded`. Kliknięcie otwiera pasek pod nagłówkiem i od razu daje fokus w polu.
- [ ] Pasek ma plakietki „Popularne”, a każda prowadzi do wyników wyszukiwania.
- [ ] Escape zamyka pasek i oddaje fokus przyciskowi lupy.
- [ ] Rozwinięte podmenu desktopu leży nad otwartym paskiem.
- [ ] Bez JS (wyłączony w przeglądarce) lupa jest linkiem do `/?s=`, a pasek jest niewidoczny.

## 4. Rail spisu treści

- [ ] We wpisie z długim spisem treści na desktopie rail w Kolumnie bocznej wskazuje nagłówek, który właśnie czytasz, i pokazuje postęp.
- [ ] Kliknięcie pozycji płynnie przewija do nagłówka, a nagłówek nie chowa się pod przyklejonym nagłówkiem strony.
- [ ] Na telefonie dolny pasek pokazuje bieżący nagłówek i krok („2/6”) oraz otwiera listę nagłówków.

## 5. Edytor

- [ ] W Site Editorze części `header` i `navigation-overlay` otwierają się bez komunikatu „Ten blok zawiera nieoczekiwaną lub nieprawidłową treść”.
