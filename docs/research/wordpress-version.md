# Wersja WordPressa i PHP dla motywu sardynkibiznesu-3.0

Research do issue #12 (część mapy #11). Stan na 2026-10-28. Wszystkie fakty pochodzą ze źródeł pierwotnych (wordpress.org, api.wordpress.org, developer.wordpress.org, make.wordpress.org).

## TL;DR

- **Najnowsza stabilna wersja WordPressa to 7.1.3** (potwierdzone). WordPress 7.1 „Mary Lou” wyszedł 19.08.2026.
- **PHP:** minimum 7.4 (od WP 7.0). Maksymalnie wspierane jest PHP 8.5. Zespół hostingowy rekomenduje PHP 8.4.x lub 8.5.x, a strona requirements mówi o „8.3 lub nowszym”.
- **Wszystkie API FSE, o które pyta ticket, są w core i stabilne od kilku wydań:** `theme.json` v3 (najnowszy, od 6.6), Block Bindings (6.5), Interactivity API i Script Modules (6.5), style variations, section styles (6.6) oraz `render` w block.json (6.1). WP 7.0 dodał jeszcze bloki rejestrowane wyłącznie w PHP (bez JS i bez buildu).
- **Wtyczki:** większość ma status „tested up to 7.1.3”. Ryzykowne są: **YASR** (tested 6.8.11, ostatnia aktualizacja 04.2025), **Spreaker Shortcode** (tested 6.6.10, ostatnia aktualizacja 09.2024), **Regenerate Thumbnails**, **WordPress Importer**, **JWT Auth** i **rest-api-comment** (tested 5.8, porzucona). Dwie wtyczki zostały zamknięte na wordpress.org: **widget-settings-importexport** i **wp-enable-webp**. Uwaga na **Yoast**: najnowsza wersja 28.8 wymaga WP ≥ 7.0, a na produkcji jest 18.3.

## 1. Wersja WordPressa

| Fakt | Źródło |
|---|---|
| API `version-check` zwraca ofertę `upgrade` = **7.1.3**, `php_version` = 7.4, `mysql_version` = 5.5.5 | https://api.wordpress.org/core/version-check/1.7/ |
| API `stable-check` oznacza jako `latest` tylko **7.1.3** | https://api.wordpress.org/core/stable-check/1.0/ |
| Inne gałęzie, które nadal dostają poprawki bezpieczeństwa (auto-update): 7.0.7, 6.9.10, 6.8.11, 6.7.10 … | https://api.wordpress.org/core/version-check/1.7/ |
| WP 7.1 „Mary Lou”: Field Guide z 05.08.2026, wydanie 19.08.2026 | https://make.wordpress.org/core/2026/08/05/wordpress-7-1-field-guide/ , https://wordpress.org/documentation/wordpress-version/version-7-1/ |

Wersja wskazana przez właściciela, **7.1.3, jest poprawna**.

## 2. Wymagania PHP i bazy danych

| Fakt | Źródło |
|---|---|
| Rekomendacja: „PHP version 8.3 or greater”, MySQL ≥ 8.0 lub MariaDB ≥ 10.11. Legacy: PHP 7.4+ i MySQL 5.5.5+ | https://wordpress.org/about/requirements/ |
| WP 7.1 jest w pełni zgodny z PHP 7.4, 8.0, 8.1 (wszystkie trzy są EOL, wspierane tylko dla kompatybilności wstecznej) oraz z 8.2, 8.3, 8.4 i 8.5. Dla nowych instalacji zalecane jest **8.4.x / 8.5.x**. PHP 8.2 ma EOL 31.12.2026 | https://make.wordpress.org/hosting/handbook/compatibility/version/7-1/ |
| Baza dla nowych instalacji: MySQL 8.4.x lub MariaDB 10.11/11.4/11.8. **MySQL 8.0 ma EOL od 30.04.2026** | jw. |
| WP 7.0 porzucił PHP 7.2 i 7.3, więc minimum to 7.4. Tabela zgodności: 7.0 i 7.1 obsługują PHP 7.4–8.5 | https://make.wordpress.org/core/2026/01/09/dropping-support-for-php-7-2-and-7-3/ , https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/ |

Uwaga dla repo: `docker-compose.yml` używa obrazu `mysql:8.0`, który jest już EOL. Przy 3.0 warto przejść na `mysql:8.4` (albo MariaDB 11.4).

## 3. API FSE w WP 7.1

| API | Od wersji | Status w 7.1 | Źródło |
|---|---|---|---|
| `theme.json` **v3** | 6.6 | Najnowsza wersja schematu, nie ma v4 („living specification for version 3”, zmodyfikowana 14.10.2026) | https://developer.wordpress.org/block-editor/reference-guides/theme-json-reference/theme-json-living/ |
| `render` (plik PHP) w block.json | 6.1 | Stabilne. Plik dostaje `$attributes`, `$content` i `$block` | https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/ |
| block.json `apiVersion: 3` | 6.3 | Aktualny. Od WP 7.0 edytor postów działa w iframe, gdy wszystkie bloki mają v3 | jw. oraz https://make.wordpress.org/core/2026/02/24/iframed-editor-changes-in-wordpress-7-0/ |
| Bloki rejestrowane tylko w PHP (`supports.autoRegister` + render callback) | 7.0 | Nowe. Przeznaczone dla bloków renderowanych po stronie serwera, bez dużej interaktywności. Atrybuty mogą być tylko typu string, number, integer lub boolean | https://make.wordpress.org/core/2026/03/03/php-only-block-registration/ |
| Block Bindings API | 6.5 | W core. Własne bloki mogą się podpiąć filtrem `block_bindings_supported_attributes` (od 6.9). Od 7.0 Pattern Overrides działają z każdym blokiem obsługującym bindings | https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/ , https://make.wordpress.org/core/2026/03/16/pattern-overrides-in-wp-7-0-support-for-custom-blocks/ |
| Script Modules API (`wp_register_script_module`, `wp_enqueue_script_module`, import map) | 6.5 | W core. W 7.0 dodano możliwość zależności skryptów klasycznych od modułów (Trac #61500) | https://make.wordpress.org/core/2024/03/04/script-modules-in-6-5/ , https://make.wordpress.org/core/2026/05/14/wordpress-7-0-field-guide/ |
| Interactivity API | 6.5 | W core, ładowane przez `viewScriptModule`. W 7.0 doszły `watch()`, `data-wp-watch` i `state.url` ustawiane po stronie serwera | https://make.wordpress.org/core/2024/03/04/interactivity-api-dev-note/ , https://make.wordpress.org/core/2026/02/23/changes-to-the-interactivity-api-in-wordpress-7-0/ |
| `viewScriptModule` w block.json (ścieżka do modułu JS względem block.json) | 6.5 | Stabilne | https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/ |
| Style variations (`styles/*.json`) i **section styles** (block style variations dla kontenerów, `blockTypes` w JSON) | 6.6 | Stabilne | https://make.wordpress.org/core/2024/06/24/section-styles/ , https://developer.wordpress.org/news/2024/06/styling-sections-nested-elements-and-more-with-block-style-variations-in-wordpress-6-6/ |
| Nowości 7.1 w Global Styles: responsywne style bloków i konfigurowalne viewporty, pseudo-stany (`:hover` itp.), text-shadow, gradient tła, min-width | 7.1 | Nowe | https://make.wordpress.org/core/2026/08/05/responsive-block-styles-and-configurable-viewports-in-wordpress-7-1/ , https://make.wordpress.org/core/2026/08/05/pseudo-and-custom-style-states-in-wordpress-7-1/ , https://make.wordpress.org/core/2026/07/23/text-shadow-support-in-global-styles/ |

### Co to znaczy dla „minimalnego buildu”

- Block.json pozwala wskazać pliki przez `file:./style.css`, `file:./view.js` oraz `render: file:./render.php`. Dokumentacja nie wymaga kroku budowania. Build potrzebny jest tylko dla JSX/`index.js` edytora z importami `@wordpress/*`.
- Bloki tylko w PHP (WP 7.0) pozwalają zrobić proste bloki serwerowe **bez żadnego JS**. To dobry zamiennik bloków ACF.
- Interactivity API to moduł ES (`@wordpress/interactivity` jest w import mapie core). Kod `view.js` jako natywny moduł ES może importować go bez bundlera. To wniosek z opisu Script Modules i import mapy. Dokumentacja pokazuje głównie przykłady z `wp-scripts`, więc warto to potwierdzić prototypem.
- React 19 przesunięto poza 7.1: https://make.wordpress.org/core/2026/07/24/react-19-punted-beyond-wordpress-7-1-experiment-in-gutenberg/

## 4. Zgodność wtyczek z `wp-plugins.csv`

Dane z API katalogu wtyczek (`https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=<slug>`) pobrane 2026-10-28. „Tested” to deklaracja autora (pole *Tested up to*).

| Wtyczka | Status | Na stronie | Najnowsza | Wymaga WP | Tested | Wymaga PHP | Ost. aktualizacja | Ocena |
|---|---|---|---|---|---|---|---|---|
| wordpress-seo (Yoast) | active | 18.3 | 28.8 | **7.0** | 7.1.3 | 7.4 | 2026-10-27 | OK po aktualizacji (bardzo stara wersja na produkcji) |
| yet-another-stars-rating (YASR) | active | 3.4.13 | 3.4.15 | 4.7 | **6.8.11** | 5.4 | **2025-04-10** | Ryzyko: nietestowana na 7.x, słabo utrzymywana |
| spreaker-shortcode | active | 1.8.3 | 1.8.3 | 2.5 | **6.6.10** | – | **2024-09-16** | Ryzyko: nieutrzymywana. To prosty shortcode, który w FSE można zastąpić blokiem Embed lub Shortcode |
| gotmls | active | 4.23.73 | 4.23.92 | 3.3 | 7.1.3 | 5.6 | 2026-10-27 | OK |
| better-search-replace | inactive | 1.4.10 | 1.4.11 | 6.2 | 7.1.3 | **8.1** | 2026-08-25 | OK (wymaga PHP 8.1) |
| convertpro | active | 1.8.0 | – | – | – | – | – | Wtyczka komercyjna (Brainstorm Force). Slug `convertpro` na wordpress.org to **inna** wtyczka („EasyTest”), więc zgodność trzeba sprawdzić u producenta |
| convertpro-addon | active | 1.5.5 | – | – | – | – | – | Komercyjna, nie ma jej na wordpress.org. Sprawdzić u producenta |
| cookie-notice | active | 2.5.5 | 3.1.17 | 4.9.6 | 7.1.3 | 7.4 | 2026-10-15 | OK |
| enable-media-replace | active | 4.1.5 | 4.2.3 | 4.9.7 | 7.1.3 | 5.6 | 2026-10-26 | OK |
| jwt-authentication-for-wp-rest-api | active | 1.3.6 | 1.5.0 | 4.2 | **6.9.10** | 7.4 | 2026-02-18 | Prawdopodobnie OK, brak deklaracji dla 7.x |
| lightbox-photoswipe | active | 5.6.1 | 5.13.0 | 6.5 | 7.1.3 | – | 2026-08-30 | OK |
| litespeed-cache | active | 6.5.4 | 7.9.2 | 6.0 | 7.1.3 | 7.4 | 2026-10-20 | OK |
| loco-translate | active | 2.6.1 | 2.8.9 | 6.6 | 7.1.3 | 7.4 | 2026-09-30 | OK |
| wonderm00ns-simple-facebook-open-graph-tags | active | 3.3.4 | 3.5.2 | 5.0 | 7.1.3 | 7.0 | 2026-09-28 | OK (przy Yoast prawdopodobnie zbędna) |
| quick-adsense | inactive | 2.8.7 | 2.9.4 | 6.3 | 7.0.7 | 7.4 | 2026-08-03 | Nieaktywna |
| redirection | active | 5.2.3 | 5.10.1 | 6.7 | 7.1.3 | 7.4 | 2026-09-19 | OK |
| regenerate-thumbnails | active | 3.1.6 | 3.1.6 | 4.7 | **6.8.11** | 5.2.4 | 2025-08-20 | Niskie ryzyko, to narzędzie jednorazowe (alternatywa: `wp media regenerate`) |
| rest-api-comment | active | 1.0.2 | 1.0.2 | 5.5 | **5.8.18** | 7.0 | **2021-11-07** | Porzucona. Wymaga przeglądu lub zastąpienia |
| search-exclude | active | 2.4.3 | 2.6.8 | 4.7 | 7.1.3 | 5.6 | 2026-10-05 | OK |
| simple-local-avatars | active | 2.2.0 | 2.8.6 | 6.6 | 7.1.3 | 7.4 | 2026-08-19 | OK |
| widget-settings-importexport | active | 1.5.3 | – | – | – | – | – | **Zamknięta na wordpress.org**. W motywie FSE (bez klasycznych widgetów) zbędna |
| wordpress-importer | active | 0.8.3 | 0.9.7 | 5.2 | **6.8.11** | 7.2 | 2026-10-20 | Aktywnie rozwijana, niskie ryzyko |
| wpdiscuz | active | 7.6.27 | 7.6.73 | 6.0 | 7.1.3 | 7.4 | 2026-10-25 | OK |
| wp-enable-webp | active | 1.0 | – | – | – | – | – | **Zamknięta na wordpress.org**. WebP jest w core od 5.8, więc do usunięcia |
| ihumbak-* (7 szt.) | – | – | – | – | – | – | – | Własne, do sprawdzenia we własnym zakresie |

UpdraftPlus (wymieniona w tickecie) nie występuje w `wp-plugins.csv`. Dla porządku: wersja 1.26.14, tested 7.1.3, aktualizacja 2026-10-26, czyli OK.

Uwaga: na produkcji większość wtyczek jest mocno nieaktualna (np. Yoast 18.3 wobec 28.8). Przed przejściem na WP 7.1 trzeba je zaktualizować, a Yoast ≥ 28 wymaga WP ≥ 7.0.

## 5. Rekomendacja dla 3.0

- **Docelowo WordPress 7.1.x** (obecnie 7.1.3).
- Nagłówek `style.css` motywu: `Requires at least: 7.0` (bloki tylko w PHP, Pattern Overrides dla własnych bloków, nowe Interactivity API) oraz `Requires PHP: 8.3`. Na serwerze **PHP 8.4** zgodnie z rekomendacją zespołu hostingowego. Nie należy celować w 8.2, bo ma EOL 31.12.2026.
- `theme.json` w wersji **3**.
- Bloki: `block.json` z `apiVersion: 3`, `render: file:./render.php`, style przez `file:`. Interaktywność przez Interactivity API (`viewScriptModule`). Proste bloki serwerowe przez rejestrację tylko w PHP.
- Wtyczki przed migracją: zaktualizować wszystkie. Zastąpić lub usunąć `rest-api-comment`, `widget-settings-importexport` i `wp-enable-webp`. Przetestować YASR i Spreaker na stagingu z WP 7.1 albo zaplanować zamienniki. Zgodność Convert Pro sprawdzić u producenta.
- Lokalnie: `mysql:8.0` → `mysql:8.4` w `docker-compose.yml`.
