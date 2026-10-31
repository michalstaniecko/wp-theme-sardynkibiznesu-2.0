# Tokeny motywu 3.0

`theme.json` jest jedynym źródłem tokenów w kodzie. Synchronizacja z design systemem „Sardynki Biznesu” (Claude Design, `tokens.json`) jest ręczna i jednokierunkowa: projekt → `theme.json`. Slugi są identyczne z nazwami w projekcie (bez prefiksów `space-`, `radius-`, `shadow-`, `duration-`).

Zasady użycia:

- W CSS używamy wyłącznie `--wp--preset--*` i `--wp--custom--*`. Bez aliasów (`--brand`, `--sb-brand`) i bez wartości wpisanych na sztywno.
- Zmienne komponentowe `--sb-<komponent>-*` definiujemy lokalnie na selektorze komponentu. Wtyczki mapują tokeny raz na swoim głównym selektorze z fallbackiem równym jasnej wartości z projektu, np. `--sb-toc-active: var(--wp--preset--color--brand, #cc0000)`.
- WordPress zamienia cyfrę po literze w slugu na `-cyfra` w nazwie zmiennej: slug `h1` → `--wp--preset--font-size--h-1`, klucz `custom.line-height.h1` → `--wp--custom--line-height--h-1`.

## Kolory: paleta edytora

| Nazwa w projekcie | Ścieżka w `theme.json` | Zmienna CSS | Wartość |
| --- | --- | --- | --- |
| `brand` | `settings.color.palette[brand]` | `--wp--preset--color--brand` | `#cc0000` |
| `brand-soft` | `settings.color.palette[brand-soft]` | `--wp--preset--color--brand-soft` | `#fef2f2` |
| `accent-soft` | `settings.color.palette[accent-soft]` | `--wp--preset--color--accent-soft` | `#ebf7fa` |
| `surface` | `settings.color.palette[surface]` | `--wp--preset--color--surface` | `#ffffff` |
| `surface-2` | `settings.color.palette[surface-2]` | `--wp--preset--color--surface-2` | `#f8f8f8` |
| `surface-inverse` | `settings.color.palette[surface-inverse]` | `--wp--preset--color--surface-inverse` | `#222222` |
| `text` | `settings.color.palette[text]` | `--wp--preset--color--text` | `#222222` |
| `text-secondary` | `settings.color.palette[text-secondary]` | `--wp--preset--color--text-secondary` | `#666666` |

## Kolory poza paletą

| Nazwa w projekcie | Ścieżka w `theme.json` | Zmienna CSS | Wartość |
| --- | --- | --- | --- |
| `on-brand` | `settings.custom.color.on-brand` | `--wp--custom--color--on-brand` | `#ffffff` |
| `brand-hover` | `settings.custom.color.brand-hover` | `--wp--custom--color--brand-hover` | `#a80000` |
| `brand-soft-strong` | `settings.custom.color.brand-soft-strong` | `--wp--custom--color--brand-soft-strong` | `#fee2e2` |
| `brand-on-inverse` | `settings.custom.color.brand-on-inverse` | `--wp--custom--color--brand-on-inverse` | `#ff6b6b` |
| `mark-gray` | `settings.custom.color.mark-gray` | `--wp--custom--color--mark-gray` | `#585657` |
| `accent` | `settings.custom.color.accent` | `--wp--custom--color--accent` | `#0097b2` |
| `accent-text` | `settings.custom.color.accent-text` | `--wp--custom--color--accent-text` | `#007a91` |
| `text-muted` | `settings.custom.color.text-muted` | `--wp--custom--color--text-muted` | `#919191` |
| `text-inverse` | `settings.custom.color.text-inverse` | `--wp--custom--color--text-inverse` | `#ffffff` |
| `surface-3` | `settings.custom.color.surface-3` | `--wp--custom--color--surface-3` | `#efefef` |
| `surface-inverse-2` | `settings.custom.color.surface-inverse-2` | `--wp--custom--color--surface-inverse-2` | `#333333` |
| `border` | `settings.custom.color.border` | `--wp--custom--color--border` | `#e1e1e1` |
| `border-card` | `settings.custom.color.border-card` | `--wp--custom--color--border-card` | `#eaf0f3` |
| `border-control` | `settings.custom.color.border-control` | `--wp--custom--color--border-control` | `#8a8a8a` |
| `focus` | `settings.custom.color.focus` | `--wp--custom--color--focus` | `#0097b2` |
| `success` | `settings.custom.color.success` | `--wp--custom--color--success` | `#1d7a3e` |
| `warning` | `settings.custom.color.warning` | `--wp--custom--color--warning` | `#9a5200` |
| `danger` | `settings.custom.color.danger` | `--wp--custom--color--danger` | `#b91c1c` |

## Typografia

| Nazwa w projekcie | Ścieżka w `theme.json` | Zmienna CSS | Wartość |
| --- | --- | --- | --- |
| `caption` | `settings.typography.fontSizes[caption]` | `--wp--preset--font-size--caption` | `12px` |
| `body-sm` | `settings.typography.fontSizes[body-sm]` | `--wp--preset--font-size--body-sm` | `14px` |
| `body` | `settings.typography.fontSizes[body]` | `--wp--preset--font-size--body` | `16px` (treść wpisu ≥1280: `20px`) |
| `h4` | `settings.typography.fontSizes[h4]` | `--wp--preset--font-size--h-4` | `18px` |
| `lead`, `body-lg` | `settings.typography.fontSizes[lead]` | `--wp--preset--font-size--lead` | `20px` (treść wpisu ≥1280: `22px`) |
| `h3` | `settings.typography.fontSizes[h3]` | `--wp--preset--font-size--h-3` | `22px` |
| `h2` | `settings.typography.fontSizes[h2]` | `--wp--preset--font-size--h-2` | `28px` (<768: `24px`) |
| `h1` | `settings.typography.fontSizes[h1]` | `--wp--preset--font-size--h-1` | `36px` (<768: `30px`) |
| `display` | `settings.typography.fontSizes[display]` | `--wp--preset--font-size--display` | `48px` (<768: `36px`) |
| rodzina `sans` | `settings.typography.fontFamilies[sans]` | `--wp--preset--font-family--sans` | Montserrat 400/500/600/700 + „Montserrat Fallback” |
| rodzina `mono` | `settings.typography.fontFamilies[mono]` | `--wp--preset--font-family--mono` | `ui-monospace, …` |
| interlinia `display` | `settings.custom.line-height.display` | `--wp--custom--line-height--display` | `56px` (<768: `44px`) |
| interlinia `h1` | `settings.custom.line-height.h1` | `--wp--custom--line-height--h-1` | `44px` (<768: `38px`) |
| interlinia `h2` | `settings.custom.line-height.h2` | `--wp--custom--line-height--h-2` | `36px` (<768: `32px`) |
| interlinia `h3` | `settings.custom.line-height.h3` | `--wp--custom--line-height--h-3` | `30px` |
| interlinia `h4` | `settings.custom.line-height.h4` | `--wp--custom--line-height--h-4` | `26px` |
| interlinia `lead` | `settings.custom.line-height.lead` | `--wp--custom--line-height--lead` | `32px` (treść wpisu ≥1280: `34px`) |
| interlinia `body` | `settings.custom.line-height.body` | `--wp--custom--line-height--body` | `26px` (treść wpisu ≥1280: `32px`) |
| interlinia `body-sm` | `settings.custom.line-height.body-sm` | `--wp--custom--line-height--body-sm` | `22px` |
| interlinia `caption` | `settings.custom.line-height.caption` | `--wp--custom--line-height--caption` | `18px` |
| tracking `display` | `settings.custom.letter-spacing.display` | `--wp--custom--letter-spacing--display` | `-0.02em` |
| tracking `h1` | `settings.custom.letter-spacing.h1` | `--wp--custom--letter-spacing--h-1` | `-0.015em` |
| tracking `h2` | `settings.custom.letter-spacing.h2` | `--wp--custom--letter-spacing--h-2` | `-0.01em` |
| tracking `overline` | `settings.custom.letter-spacing.overline` | `--wp--custom--letter-spacing--overline` | `0.08em` |

Fonty: pliki `assets/fonts/montserrat-{400,500,600,700}.woff2`, `@font-face` generowane przez core z `fontFace` (`font-display: swap`). `functions.php` preloaduje 400 (treść) i 700 (H1). Krój zastępczy „Montserrat Fallback” (`local(Arial)` z `size-adjust`, `ascent-override`, `descent-override`) jest w `style.css`.

## Odstępy

| Nazwa w projekcie | Ścieżka w `theme.json` | Zmienna CSS | Wartość |
| --- | --- | --- | --- |
| `space-1` | `settings.spacing.spacingSizes[1]` | `--wp--preset--spacing--1` | `4px` |
| `space-2` | `settings.spacing.spacingSizes[2]` | `--wp--preset--spacing--2` | `8px` |
| `space-3` | `settings.spacing.spacingSizes[3]` | `--wp--preset--spacing--3` | `12px` |
| `space-4` | `settings.spacing.spacingSizes[4]` | `--wp--preset--spacing--4` | `16px` |
| `space-5` | `settings.spacing.spacingSizes[5]` | `--wp--preset--spacing--5` | `20px` |
| `space-6` | `settings.spacing.spacingSizes[6]` | `--wp--preset--spacing--6` | `24px` |
| `space-8` | `settings.spacing.spacingSizes[8]` | `--wp--preset--spacing--8` | `32px` |
| `space-10` | `settings.spacing.spacingSizes[10]` | `--wp--preset--spacing--10` | `40px` |
| `space-12` | `settings.spacing.spacingSizes[12]` | `--wp--preset--spacing--12` | `48px` |
| `space-16` | `settings.spacing.spacingSizes[16]` | `--wp--preset--spacing--16` | `64px` |
| `space-24` | `settings.spacing.spacingSizes[24]` | `--wp--preset--spacing--24` | `96px` |

## Promienie i cienie

| Nazwa w projekcie | Ścieżka w `theme.json` | Zmienna CSS | Wartość |
| --- | --- | --- | --- |
| `radius-sm` | `settings.border.radiusSizes[sm]` | `--wp--preset--border-radius--sm` | `4px` |
| `radius-md` | `settings.border.radiusSizes[md]` | `--wp--preset--border-radius--md` | `8px` |
| `radius-lg` | `settings.border.radiusSizes[lg]` | `--wp--preset--border-radius--lg` | `12px` |
| `radius-xl` | `settings.border.radiusSizes[xl]` | `--wp--preset--border-radius--xl` | `16px` |
| `radius-full` | `settings.border.radiusSizes[full]` | `--wp--preset--border-radius--full` | `9999px` |
| `shadow-xs` | `settings.shadow.presets[xs]` | `--wp--preset--shadow--xs` | `0 1px 2px 0 rgba(0, 0, 0, 0.05)` |
| `shadow-sm` | `settings.shadow.presets[sm]` | `--wp--preset--shadow--sm` | `0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1)` |
| `shadow-md` | `settings.shadow.presets[md]` | `--wp--preset--shadow--md` | `0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1)` |
| `shadow-lg` | `settings.shadow.presets[lg]` | `--wp--preset--shadow--lg` | `0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1)` |

## Układ i ruch

| Nazwa w projekcie | Ścieżka w `theme.json` | Zmienna CSS | Wartość |
| --- | --- | --- | --- |
| `content-max` | `settings.layout.contentSize` | `--wp--style--global--content-size` | `680px` |
| `container-max` | `settings.layout.wideSize` | `--wp--style--global--wide-size` | `1200px` |
| `sidebar-width` | `settings.custom.layout.sidebar-width` | `--wp--custom--layout--sidebar-width` | `320px` |
| `header-height` | `settings.custom.layout.header-height` | `--wp--custom--layout--header-height` | `64px` |
| `breakpoint-md` | `settings.custom.layout.breakpoint-md` | `--wp--custom--layout--breakpoint-md` | `768px` (tylko dokumentacja) |
| `breakpoint-lg` | `settings.custom.layout.breakpoint-lg` | `--wp--custom--layout--breakpoint-lg` | `1024px` (tylko dokumentacja) |
| `breakpoint-xl` | `settings.custom.layout.breakpoint-xl` | `--wp--custom--layout--breakpoint-xl` | `1280px` (tylko dokumentacja) |
| `card-image-basis` | `settings.custom.layout.card-image-basis` | `--wp--custom--layout--card-image-basis` | `14rem` (obraz Karty produktu) |
| `card-body-basis` | `settings.custom.layout.card-body-basis` | `--wp--custom--layout--card-body-basis` | `18rem` (treść Karty produktu) |
| `card-stack-width` | `settings.custom.layout.card-stack-width` | `--wp--custom--layout--card-stack-width` | `32rem`, suma dwóch powyższych (tylko dokumentacja) |
| proporcje `media` | `settings.custom.aspect-ratio.media` | `--wp--custom--aspect-ratio--media` | `16 / 9` |
| `duration-fast` | `settings.custom.duration.fast` | `--wp--custom--duration--fast` | `150ms` (`0ms` przy `prefers-reduced-motion`) |
| `duration-base` | `settings.custom.duration.base` | `--wp--custom--duration--base` | `200ms` (`0ms` przy `prefers-reduced-motion`) |
| `duration-slow` | `settings.custom.duration.slow` | `--wp--custom--duration--slow` | `300ms` (`0ms` przy `prefers-reduced-motion`) |
| `easing` | `settings.custom.easing` | `--wp--custom--easing` | `cubic-bezier(0.2, 0, 0, 1)` |
| padding strony | `styles.spacing.padding` + `useRootPaddingAwareAlignments` | `--wp--style--root--padding-left` / `-right` | `spacing--6` (<768: `spacing--4`) |

## Edytor

Redaktor wybiera tylko presety motywu. Wyłączone: domyślna paleta, gradienty i duotone, kolory własne, domyślne rozmiary fontów, odstępy i cienie, własne rozmiary fontów i odstępów, `fontWeight`, `fontStyle`, `letterSpacing`, `textTransform`, `lineHeight`, `appearanceTools`, `fluid`.

## Świadome odstępstwa

- **Kolumna główna obok Kolumny bocznej nie ma limitu `content-max`.** Od 1024 px wypełnia swój tor siatki (816 px w układzie 1200 px), także tekst wpisu (#75). `content-max` obowiązuje dalej w `page-narrow`.
- **`body-lg` = `lead`.** Projekt ma dwa style o tej samej wartości 20/32; w `theme.json` jest jeden slug `lead`. Większa treść wpisu od 1280 px to nadpisanie zmiennych `body`/`lead` na `.wp-block-post-content` w `style.css`.
- **Tryb ciemny poza `theme.json`.** 3.0 jest tylko jasny; wartości dark zostają w projekcie. Później: jeden blok `@media (prefers-color-scheme: dark)` nadpisujący te same zmienne.
- **Kolory poza paletą** są w `custom.color`, więc redaktor ich nie wybiera; komponenty używają ich przez `--wp--custom--color--*`.
- **Style `nav`, `button`, `button-sm`, `overline`, `code`** nie mają slugów; to style komponentów (`styles.elements` / CSS komponentu) zbudowane z powyższych tokenów.
- **`fluid: false`.** Progi mobilne i ≥1280 są nadpisaniami zmiennych w `@media` w `style.css`, a nie typografią płynną.
- **Progi 768 / 1024 / 1280** są w `custom.layout` tylko dokumentacyjnie; w media queries wpisane literalnie (zmiennych CSS nie da się użyć w `@media`).
- **Próg `card-stack-width`** jest w `@container` wpisany literalnie, z tego samego powodu co progi w `@media`. Przy zmianie `card-image-basis` lub `card-body-basis` trzeba go poprawić ręcznie.
- **Tokeny `card-*` i `aspect-ratio.media`** nie pochodzą z projektu; wynikają z układu Karty produktu i obrazów kart.
- **Skala odstępów** zawiera tylko kroki z projektu (1–6, 8, 10, 12, 16, 24), bez wypełniania luk.
- **Nagłówki kolumn stopki** w projekcie mają `mark-gray` (2,2:1 na `surface-inverse`, poniżej WCAG AA); motyw używa `text-muted` (5:1).
- **Domyślne zmienne core.** WordPress nadal wypisuje w `:root` zmienne domyślnych presetów (np. `--wp--preset--color--vivid-red`) dla zgodności wstecznej, mimo wyłączenia ich w edytorze. Nie są częścią kontraktu i nie wolno ich używać.
- **Przycisk w stylu „Akcent”** ma tło `accent-text`, a nie `accent`: biały tekst na `accent` ma 3,4:1 (poniżej WCAG AA dla 14 px). Brak ciemniejszego tokenu akcentu, więc hover tylko podkreśla tekst.
- **Logo w stopce** stoi na jasnej „pigułce” (`surface`), bo projekt nie ma wersji odwróconej logo.
- **Notka** to „Callout neutral” z projektu (`surface-2`) z dodatkową krawędzią `brand` 4 px po lewej, bo samo szare tło za mało odróżniało ostrzeżenie od Ramki promocyjnej (`accent-soft`, `surface-inverse`).
