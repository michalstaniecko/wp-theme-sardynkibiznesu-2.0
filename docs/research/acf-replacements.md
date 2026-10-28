# Natywne zamienniki ACF w WordPress FSE (sardynkibiznesu-3.0)

Ticket: #13 (część mapy #11). Data: 2026-10-28. Docelowa wersja WP: **7.1.x** (najnowsza stabilna to 7.1.3 z 6.10.2026 — [wordpress.org/download/releases](https://wordpress.org/download/releases/)).

Założenie z #11: motyw 3.0 jest wyłącznie prezentacyjny, bez ACF; funkcjonalność (bloki z danymi, meta, opcje) żyje w pluginach.

## TL;DR

| Obszar | Zamiennik natywny | Migracja danych |
|---|---|---|
| Bloki ACF (`acf/sb-button`, `sardynkibiznesu20/faq`, `accordion`, `anchor-heading`, `podcast-buttons`, `countdown-timer`) | Natywne **dynamiczne** bloki (`block.json` + `render.php`) zarejestrowane w pluginie **pod tymi samymi nazwami**, z atrybutem `data` typu `object` | **Niepotrzebna na start** — `render.php` czyta istniejący `data` z komentarza bloku. Opcjonalnie później skrypt WP-CLI przepisujący `data` na czyste atrybuty |
| Post meta (price-compare, `card` w listing-with-recommended, one-time-offer) | `register_post_meta()` + `show_in_rest`; proste pola przez Block Bindings (`core/post-meta`), złożone (repeatery) jako jeden klucz `type: array/object` z JSON Schema edytowany w panelu `PluginDocumentSettingPanel` | Jednorazowy skrypt WP-CLI: spłaszczone klucze ACF → jeden klucz JSON. Do czasu migracji: adapter czytający format ACF przez `get_post_meta()` |
| Strona opcji (logo, newsletter, tracking, skrypty head/stopka) | Logo → natywne `site_logo`/`custom_logo` + blok `core/site-logo`. Reszta → **Settings API** (`register_setting` + strona w pluginie). `theme.json`/Global Styles **nie** nadają się do tokenów/skryptów | Skrypt: `options_<pole>` → nowe opcje pluginów |

## 1. Jak ACF przechowuje dane (stan obecny, ACF Pro 6.1.6 osadzony w motywie)

Źródło: kod ACF w `wp-content/themes/sardynkibiznesu-2.0/inc/advanced-custom-fields-pro/` (wersja `6.1.6`, nagłówek `acf.php`).

### 1.1 Bloki ACF — dane w komentarzu bloku

- ACF rejestruje dla każdego bloku atrybuty `name`, `data` (`object`), `align`, `mode` (+ opcjonalnie `alignText`, `alignContent`, …) — `acf_get_block_type_default_attributes()` w `pro/blocks.php:282`.
- Przy zapisie posta filtr `content_save_pre` (`acf_parse_save_blocks`, `pro/blocks.php:960`) przepisuje `data` z formatu kluczy pól na **spłaszczony format meta** przez `acf_setup_meta()` (`includes/local-meta.php` — `capture()` symuluje zapis i przechwytuje `acf/pre_update_metadata`).
- Wynikowy kształt (format „po nazwie”, jak w post meta, z ukrytymi referencjami `_nazwa` → klucz pola):

```html
<!-- wp:sardynkibiznesu20/faq {"name":"sardynkibiznesu20/faq","data":{
  "faq_title":"Najczęstsze pytania","_faq_title":"field_648e021cdee2b",
  "faq_questions":2,"_faq_questions":"field_648dea98ff107",
  "faq_questions_0_question":"…","_faq_questions_0_question":"field_648df445d21b3",
  "faq_questions_0_answer":"<p>…</p>","_faq_questions_0_answer":"field_648df46bd21b4",
  "faq_questions_1_question":"…", …},"mode":"edit"} /-->
```

- Uwaga: bardzo stare wpisy (zapisane przed ACF 5.7.13 i nigdy nie zapisane ponownie) mogą mieć `data` w formacie „po kluczu” (`{"field_648e021cdee2b":"…"}`); ACF wykrywa to po tym, że pierwszy klucz jest kluczem pola (`is_request()` w `includes/local-meta.php:80`). Czytnik natywny powinien obsłużyć oba warianty (mapa klucz→nazwa z `acf-fields/*.json`).
- Bloki są dynamiczne: komentarz jest samozamykający (`/-->`), w `post_content` nie ma HTML-a, więc nie ma czego walidować po stronie edytora.

Pola bloków (z `acf-fields/*.json`):

| Blok | Pola |
|---|---|
| `acf/sb-button` (rejestracja `acf_register_block` w `inc/acf-blocks.php`, szablon `template-blocks/formatting-sb-button.php`) | `sb-block__button--label` (text), `sb-block__button--url` (relationship → tablica ID postów), `sb-block__url-outer` (text), `sb-block__button-color` (select) |
| `sardynkibiznesu20/faq` | `faq_title`, repeater `faq_questions` {`question`, `answer` (wysiwyg)} |
| `sardynkibiznesu20/accordion` | `accordion_title`, repeater `accordion_questions` {`question`, `answer` (wysiwyg)} |
| `sardynkibiznesu20/podcast-buttons` | repeater `button` {`type` (select), `url`} |
| `sardynkibiznesu20/anchor-heading` (plugin `ihumbak-anchor`, `blocks/anchor/block.json`) | `text`, `align` (button_group), `tag` (select) |
| `sardynkibiznesu20/countdown-timer` | `minutes` (number) |

### 1.2 Post meta

- Odczyt: `acf_get_metadata()` → `get_metadata( 'post', $id, $name )`; ukryte `_nazwa` przechowują klucz pola (`includes/acf-meta-functions.php:125`).
- Repeater: wartość pod nazwą repeatera = **liczba wierszy**; podpola pod `{repeater}_{i}_{podpole}` (`pro/fields/class-acf-field-repeater.php:355,411,544`). Zagnieżdżenie składa się rekurencyjnie.
- Group: podpola pod `{grupa}_{podpole}` (`includes/fields/class-acf-field-group.php:253`).
- Typy wartości surowych: image → ID załącznika, true_false → `"1"`/`"0"`, relationship → serializowana tablica ID, link → serializowana tablica `{title,url,target}`, wysiwyg → surowy HTML (ACF przy formatowaniu przepuszcza go przez filtry `acf_the_content`/wpautop — natywny render musi to odtworzyć: `wpautop()` + `do_shortcode()`).

Konkretne klucze w bazie:

- **price-compare** (CPT, `inc/price-compare/model/*`):
  - `head_product` = N, `head_product_{i}_name|price|label|link|image`
  - `section` = N, `section_{i}_title`, `section_{i}_icon`, `section_{i}_rows` = M, `section_{i}_rows_{j}_title|description`, `section_{i}_rows_{j}_values` = K, `section_{i}_rows_{j}_values_{k}_value_type|product_feature_text|product_feature_check`
- **listing-with-recommended** (`templates/listing-with-recommended.php`): `card` = N, `card_{i}_title|image|description|button`, `card_{i}_label_text|background|color`, `card_{i}_category_name|url`
- **one-time-offer** (strony): `enable_one_time_offer` (`"1"`/`"0"`), `cookie_name_one_time_offer`

### 1.3 Strona opcji

- `acf_add_options_page( 'menu_slug' => 'theme-general-settings' )` w `inc/acf.php`; `get_field( 'x', 'options' )` → `acf_decode_post_id('options')` → typ `option` → `get_option( "options_x" )` (`includes/acf-wp-functions.php`, `includes/acf-meta-functions.php:147`).
- Klucze w `wp_options`: `options_logo` (ID), `options_getresponse_default_token`, `options_mailerlite_default_token`, `options_getresponse_default_thankyou` (link → tablica), `options_default_newsletter`, `options_newsletter_api_key`, `options_facebook_pixel`, `options_google_analytics`, `options_google_tag_manager`, `options_custom_head_tags`, `options_custom_footer_scripts` (+ `_options_*` z kluczami pól).

## 2. Bloki — natywny zamiennik

### 2.1 Czy natywny blok może przejąć nazwę i dane bloku ACF? Tak.

- Nazwa bloku to dowolne `namespace/block-name` (małe litery, cyfry, myślniki, jeden ukośnik) — nie ma zastrzeżonej listy przestrzeni nazw ([block.json → name](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/)). Można więc zarejestrować natywny `acf/sb-button` i `sardynkibiznesu20/faq` bez ACF (pod warunkiem, że ACF nie jest aktywny — inaczej konflikt rejestracji).
- Parser bloków przekazuje **wszystkie** atrybuty z komentarza; `WP_Block_Type::prepare_attributes_for_render()` waliduje tylko zadeklarowane, a niezadeklarowane zostawia bez zmian ([`class-wp-block-type.php`](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/class-wp-block-type.php), komentarz „If the attribute is not defined by the block type, it cannot be validated” → `continue`). Mimo to **trzeba zadeklarować** `data` (`type: object`) w `block.json`, bo edytor przy ponownym zapisie gubi niezadeklarowane atrybuty.
- `render` w `block.json` (od WP 6.1) wskazuje `render.php` z dostępem do `$attributes`, `$content`, `$block` ([block.json → render](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/)). Wystarczy więc w `render.php` czytać `$attributes['data']['faq_title']` i rozwijać repeater po licznikach (`faq_questions`, `faq_questions_{i}_question`).
- Ponieważ bloki ACF zapisują tylko komentarz (brak HTML-a), natywny blok dynamiczny (`save` zwraca `null`) ładuje je w edytorze bez błędów walidacji.

Szkic (plugin, nie motyw):

```json
{
  "apiVersion": 3,
  "name": "sardynkibiznesu20/faq",
  "title": "FAQ",
  "category": "text",
  "attributes": {
    "data": { "type": "object", "default": {} },
    "mode": { "type": "string" },
    "name": { "type": "string" }
  },
  "render": "file:./render.php",
  "editorScript": "file:./index.js",
  "style": "file:./style.css"
}
```

```php
// render.php — helper wspólny dla wszystkich bloków
$rows = sb_acf_block_repeater( $attributes['data'] ?? [], 'faq_questions', [ 'question', 'answer' ] );
// sb_acf_block_repeater(): n = (int) $data[$name]; for i<n: $data["{$name}_{$i}_{$sub}"]
```

### 2.2 Edycja w edytorze

ACF dawał gotowy formularz. Bez ACF trzeba:

- **Proste bloki** (`anchor-heading`, `countdown-timer`, `sb-button`): rozważyć **PHP-only block registration** z WP 7.0 (`'supports' => [ 'autoRegister' => true ]` + `render_callback`) — inspektor (DataForm) generuje się automatycznie, ale **tylko dla atrybutów skalarnych** (`string`, `number`, `integer`, `boolean`, `enum`); brak tablic/obiektów, obrazków, InnerBlocks i `save` ([dev note: PHP-only block registration](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/), [WP 7.0 Field Guide](https://make.wordpress.org/core/2026/05/14/wordpress-7-0-field-guide/)). To wymaga płaskich atrybutów, więc oznacza migrację `data` → atrybuty (patrz 2.3). Relationship (wybór posta) nie ma auto-kontrolki — dla `sb-button` lepiej własny `edit` w JS z `LinkControl`.
- **Repeatery (FAQ, accordion, podcast-buttons)**: najbardziej natywne jest przejście na **InnerBlocks** (np. `sb/faq` > `sb/faq-item` z `RichText` pytania i odpowiedzi; podcast-buttons → `core/buttons` z wariacjami). Alternatywa: własny `edit` w React z listą pól na atrybucie `array`.
- WP 7.0 wymaga `"role": "content"` na atrybutach treści, by były edytowalne w trybie `contentOnly` wzorców ([WP 7.0 Field Guide → pattern editing](https://make.wordpress.org/core/2026/03/15/pattern-editing-in-wordpress-7-0/)).

### 2.3 Migracja treści (opcjonalna, etap 2)

- **Block deprecations** nie pomogą: działają w obrębie tej samej nazwy bloku i opierają się na dopasowaniu wyniku `save` do zapisanego HTML-a; dokumentacja traktuje „nowy blok o innej nazwie” jako alternatywę dla deprecacji ([Deprecation](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-deprecation/)). Do tego działają tylko po stronie klienta, przy otwarciu posta.
- **Block transforms** (`from` / `type: block`) są ręczne (użytkownik klika w toolbarze) i działają tylko w JS ([Transforms](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-transforms/)) — przy ~1,5 tys. `acf/sb-button` nierealne.
- **Rekomendowane**: komenda WP-CLI w pluginie: `parse_blocks()` → przepisanie `attrs` (np. `data` → `label`, `postId`, `url`, `color`; repeater → `innerBlocks`) → `serialize_blocks()` → `wp_update_post()`; idempotentna, z `--dry-run`. Dzięki 2.1 można ją uruchomić **po** starcie 3.0, a nie w dniu przełączenia.
- `acf/sb-button`: `relationship` przechowuje ID posta, a render liczy `get_permalink()` w locie. Zamiana na statyczny `core/button` z URL-em zamroziłaby linki (zmiana sluga = martwy link). Rekomendacja: zostawić dynamiczny blok `sb/button` z atrybutem `postId` (+ `url` zewnętrzny, `color`), ewentualnie jako wariacja `core/button` z bindingiem do własnego źródła (`register_block_bindings_source`, `get_value_callback` zwraca `get_permalink( $args['postId'] )`) — `core/button` wspiera bindowanie `url` i `text` ([Block Bindings](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/)).

## 3. Post meta — natywny zamiennik

### 3.1 Rejestracja

- `register_post_meta( $post_type, $key, $args )` → `register_meta( 'post', … )` ([register_post_meta](https://developer.wordpress.org/reference/functions/register_post_meta/)). Argumenty: `type` (`string|boolean|integer|number|array|object`; `array`/`object` od 5.3), `single`, `default` (5.5), `sanitize_callback`, `auth_callback`, `show_in_rest` (bool lub `['schema'=>…]`), `revisions_enabled` (6.4), `label` (6.7) ([register_meta](https://developer.wordpress.org/reference/functions/register_meta/)).
- Dla `type: array` w REST **wymagane** `show_in_rest.schema.items`, inaczej `_doing_it_wrong` i rejestracja się nie udaje ([register_meta](https://developer.wordpress.org/reference/functions/register_meta/)). Dla `object` należy podać `schema.properties`.
- CPT musi mieć `supports => custom-fields`, żeby meta były widoczne w edytorze/REST ([register_meta → show_in_rest](https://developer.wordpress.org/reference/functions/register_meta/)).

### 3.2 Block Bindings (WP 6.5+)

- Źródło `core/post-meta` wiąże atrybut bloku z meta przez `args.key` ([Block Bindings](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/)). Warunki w kodzie źródła (`wp-includes/block-bindings/post-meta.php`): meta zarejestrowana z `show_in_rest`, **nie chroniona** (klucz nie może zaczynać się od `_`), post publiczny lub użytkownik ma `read_post`; zwracane jest `get_post_meta( $id, $key, true )` — czyli **wartość skalarna**.
- Domyślnie wspierane atrybuty: `core/paragraph.content`, `core/heading.content`, `core/image` (`id,url,title,alt,caption`), `core/button` (`url,text,linkTarget,rel`), `core/navigation-link.url`, `core/navigation-submenu.url`, `core/post-date.datetime`. Od 6.9 filtr `block_bindings_supported_attributes(_{$block_type})` pozwala dopisać atrybuty własnych bloków; od 7.0 te same atrybuty działają w Pattern Overrides ([Block Bindings](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/), [Pattern Overrides w 7.0](https://make.wordpress.org/core/2026/03/16/pattern-overrides-in-wp-7-0-support-for-custom-blocks/)). 7.1 dodało bindowanie `core/list-item` ([Roadmap 7.1](https://make.wordpress.org/core/2026/06/19/roadmap-to-7-1/)).
- Edycja wartości z edytora: źródło musi mieć `canUserEditValue` + `setValues` (rejestracja JS `registerBlockBindingsSource`, 6.7+) ([Block Bindings](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/)).
- **Wniosek**: Bindings rozwiązują pola skalarne (np. tytuł/cena w szablonie), **nie** repeatery. Nie ma natywnego „repeatera” w core — ani w Bindings, ani w PHP-only blocks.

### 3.3 Rekomendacja per funkcja

| Funkcja | Zamiennik |
|---|---|
| one-time-offer | `register_post_meta( 'page', 'sb_one_time_offer_enabled', type boolean )` + `sb_one_time_offer_cookie` (string), `show_in_rest`; UI: `PluginDocumentSettingPanel` z `ToggleControl`/`TextControl` (`useEntityProp`). Kod w pluginie, nie w motywie |
| listing-with-recommended `card` | Najlepiej **przenieść do treści**: blok `sb/product-card` (lub wzorzec z `core/group`+`core/image`+`core/heading`+`core/button`) w `post_content`. Szablon FSE renderuje `core/post-content`; repeater znika |
| price-compare (zagnieżdżone repeatery) | Jeden klucz `sb_price_compare_table`, `type: object`, `single: true`, `show_in_rest.schema` opisujący `{ products: [ {name,price,label,link,image} ], sections: [ {title,icon,rows:[ {title,description,values:[ {type,text,check} ]} ]} ] }`. UI: własny panel React (DataForm/kontrolki `@wordpress/components`) albo blok `sb/price-compare-table` trzymający dane w atrybutach w `post_content` CPT. Modele w `inc/price-compare/model/*` przenieść do pluginu i zmienić tylko warstwę odczytu |

### 3.4 Czytanie istniejących meta ACF bez ACF

Format jest jawny (sekcja 1.2), więc adapter to kilkanaście linii:

```php
function sb_read_acf_repeater( int $post_id, string $name, array $subs ): array {
    $n = (int) get_post_meta( $post_id, $name, true );
    $rows = [];
    for ( $i = 0; $i < $n; $i++ ) {
        foreach ( $subs as $sub => $nested ) {          // $nested = null | array (zagnieżdżony repeater)
            $key = "{$name}_{$i}_{$sub}";
            $rows[ $i ][ $sub ] = is_array( $nested )
                ? sb_read_acf_repeater( $post_id, $key, $nested )
                : get_post_meta( $post_id, $key, true );
        }
    }
    return $rows;
}
// price-compare: sb_read_acf_repeater( $id, 'section', [ 'title'=>null, 'icon'=>null,
//   'rows'=>[ 'title'=>null, 'description'=>null, 'values'=>[ 'value_type'=>null, 'product_feature_text'=>null, 'product_feature_check'=>null ] ] ] );
// group "head" → prefiks 'head_product'
```

Uwaga: get_post_meta() deserializuje relationship/link automatycznie; obrazki to ID (→ `wp_get_attachment_image()`); true_false to `"1"`.

Ścieżka: adapter jako fallback (nowy klucz pusty → czytaj format ACF) + komenda WP-CLI migrująca do nowego klucza; po migracji usunąć fallback i (osobno, po backupie) stare klucze `section_*`, `_section_*` itd.

## 4. Strona opcji — natywny zamiennik

| Pole ACF | Zamiennik | Uzasadnienie |
|---|---|---|
| `logo` | Natywne logo witryny: opcja `site_logo` / theme mod `custom_logo` + blok `core/site-logo` w części szablonu `header.html` (core rejestruje `site_logo` przez `register_setting` w `wp-includes/blocks/site-logo.php`) | Edycja wprost w Site Editorze; motyw nie potrzebuje żadnej opcji |
| tokeny newslettera (`getresponse_*`, `mailerlite_*`, `default_newsletter`, `newsletter_api_key`) | Settings API w pluginie `ihumbak-newsletter-form` | Sekrety — nie mogą trafić do `theme.json`/Global Styles (te są publiczne w CSS/REST) |
| `facebook_pixel`, `google_analytics`, `google_tag_manager` | Settings API w pluginie `ihumbak-analytics` | Funkcjonalność, nie prezentacja |
| `custom_head_tags`, `custom_footer_scripts` | Settings API w pluginie (np. `ihumbak-analytics` lub osobny), wypis przez `wp_head`/`wp_footer`; zapis tylko dla `unfiltered_html` | Surowy HTML/JS; w FSE nie ma natywnego miejsca na skrypty |

- `register_setting( $group, $name, $args )`: `type` (w tym `array`/`object`), `label` (6.6), `description`, `sanitize_callback`, `default`, `show_in_rest` (z `schema`; dla `array` wymagane `schema.items`) ([register_setting](https://developer.wordpress.org/reference/functions/register_setting/)). Ekran: `add_options_page()` + `add_settings_section/field()` lub strona React czytająca `/wp/v2/settings` (wymaga `manage_options` — `WP_REST_Settings_Controller::get_item_permissions_check`). Uwaga: ACF dawał `capability => edit_posts`; Settings API domyślnie wymaga `manage_options` — świadomie zaostrzyć.
- **Nie** do REST: tokeny API — rejestrować bez `show_in_rest` (lub z `show_in_rest => false`), żeby nie wystawiać ich edytorom przez REST.
- `theme.json` / Global Styles: tylko ustawienia prezentacyjne (kolory, typografia, layout) — nie zastępują opcji ACF.
- Site Editor: od 7.0 pakiet `@wordpress/boot` pozwala pluginom budować własne strony w Site Editorze, a 7.1 dodało filtrowanie ekranów ([WP 7.0 Field Guide](https://make.wordpress.org/core/2026/05/14/wordpress-7-0-field-guide/), [Filtering Site Editor Screens 7.1](https://make.wordpress.org/core/2026/07/31/filtering-site-editor-screens-in-wordpress-7-1/)). To opcja na przyszłość; dla kilku pól klasyczna strona Settings API jest prostsza i stabilniejsza.
- Migracja: `update_option( 'sb_newsletter_settings', [ … get_option( 'options_mailerlite_default_token' ) … ] )` itd.; `options_logo` → `set_theme_mod( 'custom_logo', $id )`/`update_option( 'site_logo', $id )`.

## 5. Rekomendacja

1. **Plugin `ihumbak-blocks` (nazwa robocza)** rejestruje natywne dynamiczne bloki pod **dotychczasowymi nazwami** (`acf/sb-button`, `sardynkibiznesu20/*`) z atrybutem `data: object` i helperem czytającym spłaszczony format ACF. Dzięki temu w dniu przełączenia na 3.0 istniejąca treść renderuje się bez migracji, a ACF można wyłączyć. Bloku `anchor-heading` dotyczy to samo w `ihumbak-anchor`.
2. **Edycja nowych instancji**: FAQ/accordion/podcast-buttons → InnerBlocks; sb-button → własny `edit` (lub `core/button` + własne źródło Bindings dla permalinku); anchor-heading/countdown → PHP-only `autoRegister` po spłaszczeniu atrybutów.
3. **WP-CLI migracja treści** (`parse_blocks`/`serialize_blocks`) jako osobny, późniejszy krok — przepisuje `data` na docelowe atrybuty/InnerBlocks; po niej usunąć obsługę `data`.
4. **Post meta**: `register_post_meta` + `show_in_rest` (+ schema), UI w `PluginDocumentSettingPanel`; price-compare jako jeden klucz JSON; `card` przenieść do treści; adapter czytający ACF jako fallback do czasu migracji.
5. **Opcje**: logo → `site_logo`/`core/site-logo`; reszta → Settings API w odpowiednich pluginach `ihumbak-*`; nic z tego w `theme.json`.

Ryzyka / do potwierdzenia przy implementacji:

- Rejestracja `acf/sb-button` bez ACF koliduje, jeśli ACF zostanie aktywny w okresie przejściowym — przełączenie musi być atomowe (dezaktywacja ACF + aktywacja pluginu).
- Wysiwyg w `data`: odtworzyć formatowanie ACF (`wpautop`, shortcody, oEmbed) — porównać render 2.0 vs 3.0 na próbce wpisów.
- Policzyć w bazie wpisy z `data` w formacie „po kluczu” (`"data":{"field_`) zanim zrezygnuje się z mapy klucz→nazwa.
- PHP-only blocks (7.0): znany błąd — kontekst `postId` tylko przy pierwszym renderze (Gutenberg #78619) ([dev note](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/)).

## Źródła

- WordPress releases: https://wordpress.org/download/releases/
- Block Bindings API: https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/
- `core/post-meta` source (kod): https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/block-bindings/post-meta.php
- `WP_Block_Type::prepare_attributes_for_render` (kod): https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/class-wp-block-type.php
- `register_post_meta`: https://developer.wordpress.org/reference/functions/register_post_meta/
- `register_meta`: https://developer.wordpress.org/reference/functions/register_meta/
- `register_setting`: https://developer.wordpress.org/reference/functions/register_setting/
- REST settings controller (kod): https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/rest-api/endpoints/class-wp-rest-settings-controller.php
- Site logo block (kod): https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/blocks/site-logo.php
- block.json metadata: https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/
- Block deprecation: https://developer.wordpress.org/block-editor/reference-guides/block-api/block-deprecation/
- Block transforms: https://developer.wordpress.org/block-editor/reference-guides/block-api/block-transforms/
- WP 7.0 Field Guide: https://make.wordpress.org/core/2026/05/14/wordpress-7-0-field-guide/
- PHP-only block registration (7.0): https://make.wordpress.org/core/2026/03/03/php-only-block-registration/
- Pattern Overrides for custom blocks (7.0): https://make.wordpress.org/core/2026/03/16/pattern-overrides-in-wp-7-0-support-for-custom-blocks/
- WP 7.1 Field Guide: https://make.wordpress.org/core/2026/08/05/wordpress-7-1-field-guide/
- Filtering Site Editor Screens (7.1): https://make.wordpress.org/core/2026/07/31/filtering-site-editor-screens-in-wordpress-7-1/
- Roadmap 7.1: https://make.wordpress.org/core/2026/06/19/roadmap-to-7-1/
- ACF Pro 6.1.6 (kod źródłowy w repo): `wp-content/themes/sardynkibiznesu-2.0/inc/advanced-custom-fields-pro/` — `pro/blocks.php`, `includes/local-meta.php`, `includes/acf-meta-functions.php`, `includes/acf-wp-functions.php`, `pro/fields/class-acf-field-repeater.php`, `includes/fields/class-acf-field-group.php`
