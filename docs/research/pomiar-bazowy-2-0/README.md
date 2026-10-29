# Pomiar bazowy 2.0: Core Web Vitals, waga strony, dostępność

Ticket: [Pomiar bazowy 2.0: Core Web Vitals i dostępność](https://github.com/michalstaniecko/wp-theme-sardynkibiznesu-2.0/issues/32).

## Metoda

- Produkcja `https://sardynkibiznesu.pl`, 2026-10-29, Lighthouse 12.8.2 (CLI, headless Chrome 156), lokale `pl`.
- Mobile: domyślna emulacja Lighthouse (Moto G Power, throttling **symulowany** 4G/4× CPU), **3 przebiegi, mediana**. Desktop: `--preset=desktop`, 1 przebieg.
- Kategorie: performance, accessibility (axe-core w Lighthouse), best-practices, SEO.
- Uruchomione z jednej maszyny i sieci (nie z serwerów Google), bez zgody w banerze CookieYes (stan nowego gościa).
- **Dane terenowe (CrUX) nie zostały pobrane**: bezkluczowy limit PageSpeed Insights API był wyczerpany (`429 Queries per day`), a CrUX API wymaga klucza. Zob. „Braki”.

URL-e:

| Klucz | URL | Dlaczego |
|---|---|---|
| `home` | `/` | strona główna |
| `post-toc` | `/dingsy/co-musi-zobaczyc-algorytm-zeby-zrobic-zasiegi/` | najdłuższy spis treści (39 kotwic) wśród 100 ostatnich wpisów |
| `category` | `/kategorie/dingsy/` | największa kategoria (113 wpisów) |
| `page` | `/polityka-prywatnosci/` | strona statyczna, tekst |
| `page-umowy` | `/umowy/` | strona z kartami produktów (przyszły wzorzec karty produktu) |

## Wyniki: mobile (mediana z 3)

| URL | Perf | A11y | BP | SEO | LCP | FCP | TBT | CLS | SI | Raport |
|---|---|---|---|---|---|---|---|---|---|---|
| home | 64 | 92 | 100 | 100 | 6,7 s | 2,2 s | 115 ms | 0 | 4,0 s | [home-mobile-3](home-mobile-3.html) |
| post-toc | 72 | 89 | 100 | 92 | 7,2 s | 2,6 s | 96 ms | 0 | 3,1 s | [post-toc-mobile-2](post-toc-mobile-2.html) |
| category | 73 | 89 | 100 | 100 | 6,3 s | 2,4 s | 96 ms | 0 | 3,5 s | [category-mobile-2](category-mobile-2.html) |
| page | 77 | 89 | 100 | 85 | 5,8 s | 1,6 s | 104 ms | 0 | 2,5 s | [page-mobile-2](page-mobile-2.html) |
| page-umowy | 78 | 96 | 100 | 100 | 5,9 s | 1,3 s | 92 ms | 0 | 2,3 s | [page-umowy-mobile-2](page-umowy-mobile-2.html) |

## Wyniki: desktop (1 przebieg)

| URL | Perf | A11y | LCP | FCP | TBT | CLS | TTFB | Raport |
|---|---|---|---|---|---|---|---|---|
| home | 95 | 92 | 1,4 s | 0,7 s | 0 | 0 | 34 ms | [home-desktop-1](home-desktop-1.html) |
| post-toc | 93 | 89 | 1,6 s | 0,8 s | 0 | 0 | 2879 ms¹ | [post-toc-desktop-1](post-toc-desktop-1.html) |
| category | 96 | 89 | 1,3 s | 0,6 s | 0 | 0 | 1051 ms¹ | [category-desktop-1](category-desktop-1.html) |
| page | 97 | 89 | 1,3 s | 0,4 s | 0 | 0 | 976 ms¹ | [page-desktop-1](page-desktop-1.html) |
| page-umowy | 98 | 96 | 1,2 s | 0,3 s | 0 | 0 | 944 ms¹ | [page-umowy-desktop-1](page-umowy-desktop-1.html) |

¹ Pierwsze trafienie wariantu desktop poza cache LiteSpeed (mobile, z cache: 27–38 ms). TTFB bez cache ~1–3 s to czas generowania strony przez PHP.

## Waga strony (mobile, transfer)

| URL | Razem | Żądania | JS | CSS | Fonty | Obrazy | HTML | Zewnętrzne |
|---|---|---|---|---|---|---|---|---|
| home | 2404 KB | 119 | 777 KB / 29 | 73 KB / 12 | 660 KB / 40 | 824 KB / 25 | 55 KB | 675 KB / 23 |
| post-toc | 2037 KB | 127 | 791 KB / 31 | 113 KB / 18 | 724 KB / 46 | 311 KB / 19 | 82 KB | 675 KB / 23 |
| category | 2313 KB | 118 | 774 KB / 29 | 68 KB / 12 | 660 KB / 40 | 743 KB / 24 | 52 KB | 675 KB / 23 |
| page | 1601 KB | 102 | 775 KB / 30 | 69 KB / 12 | 660 KB / 40 | 42 KB / 7 | 40 KB | 674 KB / 21 |
| page-umowy | 1629 KB | 105 | 777 KB / 30 | 68 KB / 11 | 660 KB / 40 | 83 KB / 11 | 25 KB | 674 KB / 21 |

Desktop różni się tylko obrazami (home: 1989 KB obrazów, razem 3550 KB).

## Obserwacje

- **LCP to opóźnienie renderowania, nie pobieranie.** We wpisie (mediana): TTFB ~0,5 s, opóźnienie wczytania 0, czas wczytania 0, **opóźnienie renderowania ~5,9 s**. Element LCP to wszędzie tekst (lead/tytuł), nie obraz. Przyczyny nie zostały rozstrzygnięte; widać:
  - dokument HTML pobierany **dwa razy** (LiteSpeed Guest Mode: `guest.vary.php`, potem przeładowanie strony po ~0,5 s),
  - **13–18 plików CSS** z `litespeed/css/` + `fonts.googleapis.com/css`, po dwa sygnalizowane jako blokujące renderowanie,
  - opóźniony JS LiteSpeed (`litespeed_load_delayed_js`).
- **Fonty: 40 plików / 660 KB na każdej stronie.** Montserrat w **18 wariantach** (100–900 + italic, `latin-ext_latin`, ~28 KB każdy), wszystkie z wysokim priorytetem, do tego FontAwesome 4.7 (75 KB) **i** FontAwesome 5.13 (`fa-solid` 77 KB + `fa-regular`) oraz arkusz Google Fonts. Lighthouse nie zgłasza problemu z `font-display`.
- **JS zewnętrzny ≈ 675 KB z ~777 KB całego JS**: `gtag/js` (GA4) 199 KB, `gtm.js` 161 KB, `fbevents.js` 112 KB + konfiguracja Pixela 104 KB, CookieYes 66 KB, MailerLite `universal.js` 15 KB (+ `universal.css`). Własny JS (LiteSpeed-łączony + PhotoSwipe) to ~100 KB. Lighthouse: ~213 KB nieużywanego JS.
- **TBT niskie (~100 ms mobile, 0 desktop), CLS 0 wszędzie**: problemem jest LCP/FCP, nie interaktywność ani stabilność układu (lab; INP wymaga danych terenowych).
- Convert Pro ładuje `cp-popup.min.css` (niski priorytet). YASR i Spreaker nie pojawiły się jako osobne zasoby na badanych URL-ach.

## Dostępność (Lighthouse/axe, automatycznie)

| URL | Wynik | Niezaliczone audyty (liczba elementów) |
|---|---|---|
| home | 92 | `color-contrast` ×91, `link-in-text-block` ×1 |
| post-toc | 89 | `color-contrast` ×18, `link-name` ×2, `link-in-text-block` ×1 |
| category | 89 | `color-contrast` ×94, `link-name` ×15, `link-in-text-block` ×1 |
| page | 89 | `color-contrast` ×10, `link-in-text-block` ×11, `link-name` ×1 |
| page-umowy | 96 | `color-contrast` ×1 |

- Kontrast: szare meta/breadcrumb (`#888888` 3,45:1, `#919191` 3,15:1 na białym; minimum AA to 4,5:1), także „ukryj” w spisie treści (`.ihumbak-anchor-menu-hide`).
- `link-name`: linki-ikony bez nazwy dostępnej (np. ikona Facebooka, link do serwisu w ramce autora; na kategorii 15 takich linków).
- `link-in-text-block`: linki w tekście odróżnione tylko kolorem (np. `style="color:#c00"` w treści).
- Audyt automatyczny nie pokrywa obsługi klawiaturą, kolejności fokusu ani czytnika ekranu: nie było testów ręcznych.

## Braki

- **CrUX (dane terenowe LCP/INP/CLS, p75)**: brak. Potrzebny klucz Google API (PageSpeed Insights API albo CrUX API) albo ręczne sprawdzenie na <https://pagespeed.web.dev/> dla 5 URL-i + originu. Bez tego budżet 3.0 opiera się na danych laboratoryjnych.
- Raporty JSON i pozostałe przebiegi mobile nie są w repo (tylko mediana + desktop jako HTML).
