# Research: minimalny toolchain dla motywu FSE i bloków we wtyczkach

Ticket: #14 (część mapy #11). Stan na 2026-10-28. Najnowsze stabilne wydanie WordPressa to **7.1.3** z 6 października 2026 ([wordpress.org/download/releases](https://wordpress.org/download/releases/)). Fragmenty kodu core cytowane niżej pochodzą z gałęzi `trunk` repozytorium `wordpress-develop` (7.2-beta). Odpowiednie API mają adnotacje `@since` ≤ 7.0, więc obowiązują też w 7.1.

## Pytanie

Jaki jest najmniejszy sensowny toolchain dla motywu FSE `sardynkibiznesu-3.0` i bloków we wtyczkach? Porównujemy trzy podejścia:

- zero-build: natywny CSS, ES modules, Script Modules, `viewScriptModule`, `render.php`, edytor bez JSX,
- `@wordpress/scripts`,
- lekkie alternatywy: esbuild i Lightning CSS.

Sprawdzamy też, co traci się bez buildu i jak budować CSS na tokenach z `theme.json`.

## TL;DR

1. **Motyw: zero-build.** Tokeny są w `theme.json`, a WordPress sam emituje z nich custom properties `--wp--preset--*` i `--wp--custom--*` na `:root`. Narzędzie, które „generuje CSS z tokenów”, nie jest potrzebne. Nasz CSS tylko używa `var(...)`. Natywny nesting i custom properties mają status Baseline. Motyw nie potrzebuje JS ani `package.json`.
2. **Bloki we wtyczkach: zero-build jako domyślna ścieżka.**
   - Bloki są dynamiczne (`render.php`).
   - Front korzysta z `viewScriptModule` i Interactivity API jako natywnych modułów ES.
   - Edytor to klasyczny skrypt bez JSX (`wp.element.createElement`, ewentualnie `htm`).
   - Zależności deklarujemy w **ręcznie napisanym** `*.asset.php`.
   - Najprostsze bloki mogą być w 100% PHP (`autoRegister`, WP 7.0+).
3. **Wyjście awaryjne: `@wordpress/scripts` per wtyczka**, nie własny esbuild. Stosujemy go tylko wtedy, gdy edytor bloku robi się złożony (RichText, InnerBlocks, rozbudowane panele) i JSX zaczyna realnie się opłacać. Główna wartość `wp-scripts` to automatyczny `*.asset.php` i mapowanie importów `@wordpress/*` na globalne `wp.*`. Przy esbuild trzeba by to odtworzyć ręcznie.
4. **Bez buildu tracimy niewiele:** JSX w edytorze, minifikację i autoprefixer. Minifikację w dużej mierze zastępuje kompresja gzip/brotli serwera, a autoprefixer dla naszych targetów jest praktycznie zbędny.

## 1. Co WordPress robi sam (i dlaczego motyw nie potrzebuje generatora CSS)

### Tokeny z `theme.json` jako custom properties

- Każdy preset dostaje custom property w postaci `--wp--preset--{type}--{slug}`, np. `--wp--preset--color--contrast`. WordPress może też generować klasy `.has-contrast-color` i `.has-contrast-background-color` ([Theme Handbook: Color](https://developer.wordpress.org/themes/global-settings-and-styles/settings/color/)).
- Wartości z `settings.custom` stają się `--wp--custom--{klucz}--{podklucz}`. camelCase zamienia się na kebab-case, a zagnieżdżenie na `--`. Przykład: `lineHeight.body` → `--wp--custom--line-height--body` ([Theme Handbook: Custom](https://developer.wordpress.org/themes/global-settings-and-styles/settings/custom/), [Block Editor Handbook: Global Settings & Styles](https://developer.wordpress.org/block-editor/how-to-guides/themes/global-settings-and-styles/)).
- Presety trafiają do globalnego arkusza, ładowanego na froncie i w edytorze ([Global Settings & Styles](https://developer.wordpress.org/block-editor/how-to-guides/themes/global-settings-and-styles/)). W kodzie core zmienne lądują na selektorze `ROOT_CSS_PROPERTIES_SELECTOR = ':root'`, a style globalne idą w opakowaniu `:root :where(...)` o niskiej specyficzności. Źródło: [`class-wp-theme-json.php`](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/class-wp-theme-json.php), stałe `ROOT_CSS_PROPERTIES_SELECTOR` i `ROOT_BLOCK_SELECTOR` oraz metoda budująca wrapper `:root :where()`. Starsze strony dokumentacji pokazują `body` jako selektor przykładów.

**Wniosek:** „generowanie CSS z tokenów” jest wbudowane w WordPress. Workflow wygląda tak:

1. Definiujemy paletę, skalę typografii (z `fluid`), skalę odstępów i layout w `theme.json`.
2. Tokeny, które nie są presetami (np. promienie, cienie, czasy animacji), kładziemy w `settings.custom`.
3. Arkusz motywu używa wyłącznie `var(--wp--preset--…)` i `var(--wp--custom--…)`.

Nie potrzeba Style Dictionary, generatora SCSS ani kroku eksportu. Jedno źródło prawdy obsługuje edytor, Global Styles i front.

**Ograniczenie:** custom properties nie działają w warunkach `@media`. Breakpointy trzeba zapisać literalnie w CSS, najlepiej zgodnie z `settings.layout.contentSize`/`wideSize`. Jest to cecha CSS, nie WordPressa ([MDN: Using CSS custom properties](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Cascading_variables/Using_custom_properties)). Container queries i `clamp()` (fluid typography z `theme.json`) ograniczają liczbę potrzebnych breakpointów.

### Ładowanie CSS w motywie blokowym

- Arkusz frontu ładujemy przez `wp_enqueue_style()` na `wp_enqueue_scripts`. WordPress nie ładuje go automatycznie w edytorze, do tego służy `add_editor_style()` na `after_setup_theme` ([Theme Handbook: Including Assets](https://developer.wordpress.org/themes/core-concepts/including-assets/)).
- Style per blok ładujemy przez `wp_enqueue_block_style( 'core/…', [ 'handle', 'src', 'path' ] )` na `init`. Ładują się tylko wtedy, gdy blok jest na stronie. Z podanym `path` są inline’owane w `<head>` ([Theme Handbook: Block Stylesheets](https://developer.wordpress.org/themes/features/block-stylesheets/)). Dzięki temu CSS jest dzielony per blok bez bundlera.
- Handbook zaleca `theme.json` jako pierwszy wybór do stylowania, a `styles.blocks.{block}.css` tylko dla małych fragmentów ([Block Stylesheets](https://developer.wordpress.org/themes/features/block-stylesheets/)).

## 2. Natywny CSS: czy wsparcie przeglądarek wystarcza?

| Funkcja | Status | Źródło |
|---|---|---|
| Custom properties | Baseline od lat | MDN |
| Nesting (`&`) | **Baseline widely available**, od grudnia 2023. Pełne wsparcie: Chrome/Edge 120, Firefox 117, Safari/iOS 17.2. Pełne wsparcie ma ok. 92,9% użytkowników globalnie (94,5% z częściowym) | [MDN: Nesting selector](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Selectors/Nesting_selector), [caniuse: css-nesting](https://caniuse.com/css-nesting) |
| `@layer` | **Baseline widely available**, od marca 2022 | [MDN: @layer](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@layer) |

Dla porównania `@wordpress/browserslist-config`, czyli target samego Gutenberga, to `> 1%`, `last 2 Chrome/Firefox/Safari/iOS/Edge/Opera versions` oraz `last 1 Android/ChromeAndroid` ([źródło](https://github.com/WordPress/gutenberg/blob/trunk/packages/browserslist-config/index.js)). Wszystkie te przeglądarki obsługują nesting i `@layer`.

### Pułapki natywnego nestingu względem Sass

Źródło: [MDN: Nesting selector](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Selectors/Nesting_selector).

- Nie ma sklejania stringów. `&__elem` i `&--mod` w stylu BEM nie działają, nazwy trzeba pisać w całości.
- `&` zachowuje się jak `:is()`, więc nie łączy się z pseudo-elementami (`::before`) jako rodzicem.
- Białe znaki mają znaczenie: `&:hover` to coś innego niż `:hover`.

### Pułapka `@layer` w WordPressie

Według MDN style **poza warstwą zawsze wygrywają** z warstwowymi (dla deklaracji bez `!important`). Style globalne WordPressa i CSS bloków core **nie są w żadnej warstwie**. W `class-wp-theme-json.php` nie ma ani jednego `@layer`. Jeśli schowamy cały CSS motywu w `@layer`, przegra on z każdym unlayered stylem core, mimo niskiej specyficzności `:root :where()`.

**Rekomendacja:** `@layer` stosujemy najwyżej wewnątrz własnego CSS, np. reset < komponenty < utilities. Nadpisania stylów core zostawiamy poza warstwami albo w ogóle ich unikamy na rzecz `theme.json`.

## 3. JavaScript bez buildu

### Front: Script Modules i Interactivity API

- `viewScriptModule` w `block.json` przyjmuje ścieżkę `file:./view.js` albo ID modułu. Pole jest dostępne od WP 6.5 ([Block metadata](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/)).
- Script Modules API to `wp_register_script_module( $id, $src, $deps, $version, $args )` i `wp_enqueue_script_module(...)`. WordPress generuje `importmap` i `modulepreload` dla zależności statycznych, a zależności mogą być `'import' => 'dynamic'` ([dev note 6.5](https://make.wordpress.org/core/2024/03/04/script-modules-in-6-5/)).
- W 6.9 doszedł parametr `$args` (`fetchpriority`, `in_footer`). W 7.0 doszły `wp_set_script_module_translations()` oraz zależności modułowe klasycznych skryptów w import mapie (`@since` w [`script-modules.php`](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/script-modules.php) i [`class-wp-script-modules.php`](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/class-wp-script-modules.php)). Dev note z 6.5 mówił, że modułom brakuje i18n. W 7.0 to już nieaktualne.
- W motywie blokowym import mapa i moduły są drukowane w `<head>` (`add_hooks()` w `class-wp-script-modules.php`).
- Core rejestruje m.in. `@wordpress/interactivity`, `@wordpress/interactivity-router` i `@wordpress/a11y` jako moduły ([`wp_default_script_modules()`](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/script-modules.php)). Interactivity API jest w core od 6.5 i wymaga `"supports": { "interactivity": true }` ([Interactivity API reference](https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/)).
- Dokumentacja Interactivity API pokazuje workflow z `wp-scripts --experimental-modules`. Mechanizm jest jednak natywny: `import { store } from '@wordpress/interactivity'` to bare specifier rozwiązywany przez import mapę WordPressa. Bundler nie jest do tego potrzebny.

**Kluczowy szczegół dla zero-build.** Jak `register_block_script_module_id()` w [`blocks.php`](https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/blocks.php) rejestruje moduł z `viewScriptModule`:

- Zależności czyta wyłącznie z pliku `view.asset.php` obok `view.js`. Bez tego pliku `dependencies` jest puste.
- Wtedy `@wordpress/interactivity` **nie trafi do import mapy**, a import się wysypie.

Rozwiązaniem jest ręcznie napisany, kilkulinijkowy plik:

```php
<?php // view.asset.php
return array(
	'dependencies' => array( '@wordpress/interactivity' ),
	'version'      => '1.0.0',
);
```

Ta sama funkcja automatycznie ustawia `fetchpriority: low` i `in_footer` dla bloków interaktywnych.

### Edytor: bez JSX

- Rejestracja bloku w edytorze to nadal **klasyczny skrypt** (`editorScript`). Core nie ma odpowiednika „editorScriptModule” w `block.json`: [Block metadata](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/) zna tylko `editorScript`. `wp_enqueue_block_editor_script_modules()` (6.9) ładuje jedynie moduł LaTeX→MathML core.
- Bez buildu pisze się w czystym JS na globalnym `wp` i **ręcznie zarządza zależnościami** ([JavaScript in the Block Editor](https://developer.wordpress.org/block-editor/getting-started/fundamentals/javascript-in-the-block-editor/)). Dla `editorScript` zależności także pochodzą z `index.asset.php`. Od 6.5 ten plik jest opcjonalny (`register_block_script_handle()`, komentarz „Asset file for blocks is optional”, [trac #60460](https://core.trac.wordpress.org/ticket/60460)). Bez niego jednak `wp-blocks`, `wp-element`, `wp-block-editor` i `wp-components` nie zostaną dołączone jako zależności. Dlatego ten plik również piszemy ręcznie, np. `array( 'dependencies' => array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ), 'version' => '1.0.0' )`. Gdy jest `wp-i18n` i `textdomain`, core sam podpina tłumaczenia.
- Zamiast JSX piszemy `const el = wp.element.createElement`. Przy dynamicznych blokach (`render.php`, `save` zwraca `null`) edytor to głównie `useBlockProps` + `InspectorControls` + `ServerSideRender`, czyli kilkadziesiąt linii `el(...)`.
- Opcjonalnie można użyć `htm` (Apache-2.0, < 600 B): składnia podobna do JSX w tagged templates, „no transpiler necessary”, `htm.bind(wp.element.createElement)` ([developit/htm](https://github.com/developit/htm)). Wymaga zvendorowania jednego pliku do wtyczki.

### Bloki w 100% PHP (WP 7.0+)

`register_block_type( …, [ 'supports' => [ 'autoRegister' => true ], 'render_callback' => … ] )` rejestruje blok bez żadnego JS ([dev note: PHP-only block registration](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/), [7.0 Field Guide](https://make.wordpress.org/core/2026/05/14/wordpress-7-0-field-guide/)):

- Edytor sam generuje Inspector Controls z atrybutów.
- Obsługiwane typy atrybutów to tylko string, number, integer i boolean, bez atrybutów sourced.
- Nie ma InnerBlocks ani RichText/mediów.
- Każda zmiana w edytorze to round-trip renderowania na serwerze.

Ta opcja pasuje do prostych bloków konfigurowalnych kilkoma polami, np. wariantu formularza newslettera czy ustawień spisu treści.

## 4. Porównanie wariantów

| | Zero-build | `@wordpress/scripts` | esbuild + Lightning CSS |
|---|---|---|---|
| Zależności npm | brak (opcjonalnie lint przez `npx`) | `@wordpress/scripts` 37.0.0: 49 bezpośrednich zależności, webpack 5, Babel, Sass, PostCSS; Node ^22.22 / ^24.15 / ≥26 ([package.json](https://github.com/WordPress/gutenberg/blob/trunk/packages/scripts/package.json)) | 2 pakiety (natywne binaria) |
| JSX w edytorze | nie (`createElement` / `htm`) | tak (Babel) | tak (`jsxFactory`, ale JSX w `.js` trzeba włączyć loaderem) ([esbuild content types](https://esbuild.github.io/content-types/)) |
| `*.asset.php` (zależności, wersja) | ręcznie | **automatycznie** (dependency-extraction plugin) | brak, trzeba ręcznie albo napisać plugin; importy `@wordpress/*` trzeba ręcznie mapować na `wp.*` jako external |
| Script Modules / `viewScriptModule` | natywnie | nadal za flagą `--experimental-modules` ([docs wp-scripts](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-scripts/)) | `format: esm` + external `@wordpress/interactivity` |
| Kopiowanie `block.json`/`render.php` do `build/` | n/d (źródło = dystrybucja) | automatycznie | ręcznie |
| Minifikacja JS/CSS | brak | terser + cssnano | tak |
| Autoprefixer | brak | tak (`@wordpress/postcss-plugins-preset`: `postcss-import` + `autoprefixer` z browserslist WP) | esbuild i Lightning CSS dodają prefiksy wg `target` ([esbuild](https://esbuild.github.io/content-types/), [Lightning CSS](https://lightningcss.dev/docs.html)) |
| Obniżanie nestingu dla starych przeglądarek | nie | nie domyślnie (brak `postcss-nesting` w presecie) | tak, wg targetu |
| Lint/format | osobno | wbudowane (`lint-js`, `lint-style`, `format`) | osobno |
| Utrzymanie | żadne | aktualizacje dużego drzewa zależności | mała konfiguracja, ale własna |

### Co realnie tracimy bez buildu

1. **JSX:** ból tylko przy złożonych edytorach. Dla bloków dynamicznych z panelem ustawień `createElement`/`htm` wystarcza.
2. **Minifikację:** strata kilku-kilkunastu procent rozmiaru po kompresji. Serwer i tak kompresuje gzip/brotli, a CSS bloków jest ładowany per blok. Do pomiaru w ramach budżetu wydajności (mapa #11, „Budżet wydajności”).
3. **Autoprefixing:** przy targecie zgodnym z browserslistem WP nowoczesne właściwości (nesting, custom properties, grid, `clamp`) nie wymagają prefiksów. Ryzyko jest marginalne.
4. **Automatyczny `*.asset.php` i cache-busting:** zastępujemy go ręcznym plikiem. Wersję możemy brać z `filemtime()` przy rejestracji ręcznej albo z `version` w `block.json` (core używa go jako fallbacku).
5. **Bundlowanie wielu plików:** w przeglądarce działają natywne `import` między modułami. Dla klasycznego skryptu edytora trzymamy jeden plik na blok.
6. **Lint:** `@wordpress/eslint-plugin` / `stylelint` można odpalać przez `npx` w CI bez kroku buildu.

## 5. Rekomendacja

**Domyślnie zero-build, `@wordpress/scripts` jako udokumentowane wyjście awaryjne per wtyczka.**

- **Motyw `sardynkibiznesu-3.0`:**
  - Pliki: `theme.json` (wszystkie tokeny), `style.css` (nagłówek + bazowy CSS na `var(--wp--…)` z natywnym nestingiem), `assets/css/blocks/core-*.css` przez `wp_enqueue_block_style()`, `add_editor_style()`.
  - Bez `package.json`. Zgodnie z ADR-0001 motyw nie ma bloków ani JS.
- **Wtyczki z blokami (newsletter, spis treści):**
  - Każdy blok to katalog z `block.json` (`apiVersion: 3`), `render.php`, `edit.js` + ręczny `edit.asset.php`, opcjonalnie `view.js` + ręczny `view.asset.php` (`@wordpress/interactivity`) oraz `style.css`.
  - Najprostsze bloki rejestrujemy jako PHP-only (`autoRegister`, WP ≥ 7.0).
- **Kiedy przejść na `@wordpress/scripts`:** gdy edytor bloku wymaga RichText, InnerBlocks, wielu komponentów lub współdzielonego kodu, tak że `createElement` zaczyna szkodzić czytelności. Robimy to per wtyczka, bez zmiany struktury `block.json`. Nie rekomendujemy własnego esbuild: oszczędza zależności, ale wymaga samodzielnego odtworzenia mapowania `@wordpress/*` → `wp.*` i generowania `*.asset.php`, czyli dokładnie tego, co daje `wp-scripts`.
- **Lightning CSS:** niepotrzebny. Rozważyć tylko, jeśli pomiary w budżecie wydajności pokażą, że minifikacja CSS daje istotny zysk. Wtedy wystarczy jednorazowe `lightningcss --minify` w kroku release, bez zmiany źródeł.

## Otwarte kwestie / do decyzji w mapie

- Wymagana minimalna wersja WP dla wtyczek: ≥ 7.0 (PHP-only blocks, i18n modułów) czy ≥ 6.5 (`viewScriptModule`)? Ponieważ celem jest najnowszy WP (7.1.x), rekomendujemy `Requires at least: 7.0`.
- Konwencja breakpointów: literalne wartości w CSS zgodne z `layout.contentSize`/`wideSize`, bo custom properties nie działają w `@media`.
- Czy włączyć lint w CI (`npx @wordpress/scripts lint-style` bez instalacji w repo)? To decyzja poza zakresem tego researchu.
