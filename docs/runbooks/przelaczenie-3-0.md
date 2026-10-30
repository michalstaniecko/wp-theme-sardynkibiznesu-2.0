# Przełączenie 2.0 ↔ 3.0

Runbook okna przełączenia ze spec #36 (ticket #63, plan w #35). Skrypt: `bin/switch-theme`.

Zasada: przełączenie **nie zmienia treści w bazie**. Skrypt zmienia tylko motyw, zestawy wtyczek, opcje strony głównej, status dwóch przygotowanych stron, status jednej reguły Redirection i własną opcję stanu `sb_theme_switch`. Dlatego rollback (`bin/switch-theme 2.0`) jest natychmiastowy.

Ścieżki jak w [paczki-i-staging.md](paczki-i-staging.md):

| | |
|---|---|
| produkcja | `/home/jawagabu/domains/sardynkibiznesu.pl/public_html` |
| staging | `/home/jawagabu/domains/staging.sardynkibiznesu.pl/public_html` |
| skrypty | `/home/jawagabu/bin/` |
| kopie PK0 | `/home/jawagabu/backup/` |

W przykładach:

```bash
SITE=/home/jawagabu/domains/sardynkibiznesu.pl/public_html
WP="wp --path=$SITE"
```

## 1. Skrypt `bin/switch-theme`

Działa na serwerze, przez SSH. Wgraj `bin/switch-theme` do `~/bin/` (`chmod +x`).

```bash
~/bin/switch-theme 3.0 --path $SITE --front-page <id> --blog-page <id> [--dry-run] [--yes]
~/bin/switch-theme 2.0 --path $SITE [--dry-run] [--yes]
~/bin/switch-theme status --path $SITE
```

Zawsze najpierw `--dry-run`: wykonuje tylko kontrole i wypisuje zmiany, nic nie zmienia. Potem to samo bez `--dry-run`. Skrypt prosi o wpisanie `tak`, a z `--yes` nie pyta. Kody wyjścia: `0` gotowe, `1` odmowa albo błąd, `2` błędne użycie.

**Kontrole (tylko odczyt), przy których skrypt odmawia (kod `1`):**

- pod `--path` nie ma WordPressa albo brakuje motywu `sardynkibiznesu-2.0` / `sardynkibiznesu-3.0`;
- wtyczka Redirection nie jest aktywna, reguły `^/page/(\d+)/?$` nie ma, jest więcej niż jedna albo jej cel to nie `301 /blog/page/$1/`;
- kierunek `3.0`:
  - serwis jest już na 3.0 albo powrót na 2.0 jest niedokończony;
  - aktywny motyw to nie `sardynkibiznesu-2.0`;
  - brakuje którejś wtyczki zestawu 3.0;
  - `ihumbak-tax-calculator` nie jest zainstalowana albo nie jest aktywna;
  - `--front-page` / `--blog-page` to nie strony, to ta sama strona, strona jest w koszu;
  - strona `/blog/` ma rodzica albo slug inny niż `blog`, albo slug `blog` jest zajęty przez inną treść;
- kierunek `2.0`: nie było przełączenia na 3.0 (brak opcji `sb_theme_switch`).

**Kierunek `3.0`:**

0. Zapisuje stan sprzed przełączenia w opcji `sb_theme_switch`: czas przełączenia (GMT), motyw, `show_on_front` / `page_on_front` / `page_for_posts`, statusy obu stron, status reguły i to, które wtyczki z obu zestawów były aktywne.
1. Wyłącza aktywne wtyczki zestawu 2.0: `ihumbak-newsletter-form`, `ihumbak-anchor`, `ihumbak-anchor-2`, `ihumbak-analytics`, `ihumbak-shortcodes`.
2. Włącza zestaw 3.0: `ihumbak-content-elements`, `ihumbak-toc`, `ihumbak-newsletter`, `ihumbak-site-blocks`, `ihumbak-tracking`. Szóstej wtyczki z paczek, `ihumbak-tax-calculator`, skrypt nie przełącza w żadnym kierunku: działa pod 2.0 i 3.0 (`/kalkulator-vat/`), więc ma być aktywna już przed oknem.
3. Włącza motyw `sardynkibiznesu-3.0`, ustawia `show_on_front=page`, `page_on_front`, `page_for_posts`, publikuje obie strony i włącza regułę Redirection `/page/N/` → `/blog/page/N/`.
4. Czyści cache LiteSpeed (`wp litespeed-purge all`).

**Kierunek `2.0`** odwraca każdy krok do stanu zapisanego w opcji: status reguły, opcje strony głównej, statusy stron (z powrotem szkice), motyw 2.0, wyłącza wtyczki 3.0, włącza te wtyczki 2.0, które były aktywne przed przełączeniem (`ihumbak-analytics` na produkcji nie jest aktywna, więc zostaje wyłączona), czyści cache. Na koniec wypisuje **wpisy zmienione od przełączenia, które zawierają bloki `ihumbak/*`**: ID, typ, status, datę zmiany, tytuł i link do edycji. Strony główna i `/blog/` nie są na liście, bo wracają do szkiców.

**`status`** tylko czyta: pokazuje motyw i stan przełączenia, a po przełączeniu na 3.0 tę samą listę wpisów z blokami `ihumbak/*`. Przydaje się w cotygodniowym przeglądzie (p. 6).

**Przerwany przebieg** (błąd, zerwane SSH): uruchom to samo polecenie jeszcze raz. Skrypt widzi niedokończony stan, wznawia i nie nadpisuje stanu sprzed przełączenia. Kroki są powtarzalne. Wznowienie `3.0` wymaga tych samych `--front-page` / `--blog-page`.

**Purge LiteSpeed** z WP-CLI prosi serwer WWW o wyczyszczenie przez HTTP. Gdy to się nie uda, skrypt wypisuje `UWAGA` i kończy przełączenie. Wtedy wyczyść cache ręcznie: wp-admin → LiteSpeed Cache → Toolbox → Purge All.

`wp cache flush` nie jest potrzebny: wszystkie zmiany idą przez API WordPressa.

## 2. Przygotowanie przed oknem (niewidoczne dla 2.0)

Kilka dni przed oknem. 2.0 działa bez zmian.

- [ ] Etap A zakończony: 2.0 na WP 7.1.3 i PHP 8.4, po 1–2 tygodniach obserwacji.
- [ ] Paczki 3.0 zainstalowane, nieaktywne ([paczki-i-staging.md](paczki-i-staging.md), p. 1–3). Wyjątek: `ihumbak-tax-calculator` zostaje aktywna, bo działa też pod 2.0.
- [ ] Site Editor w podglądzie motywu: `/wp-admin/site-editor.php?wp_theme_preview=sardynkibiznesu-3.0`. Części szablonów, menu `wp_navigation`, Sekcje strony głównej.
- [ ] Strona główna i `/blog/` jako **szkice** (typ strona). `/blog/`: slug `blog` (panel „Odnośnik” w edytorze), bez rodzica. Zanotuj oba ID:

  ```bash
  $WP post list --post_type=page --post_status=draft --fields=ID,post_title,post_name
  ```

- [ ] Ustawienia `ihumbak-newsletter` i `ihumbak-tracking` wpisane z góry (import newslettera z ACF działa przy aktywacji, sprawdź po przełączeniu).
- [ ] **Cron systemowy zamiast WP-Cron** (popularność wpisów, #53). `ihumbak-site-blocks` co dzień o 05:00 czasu strony pobiera odsłony z GA4 dla „Najczęściej czytane” i sortowania kategorii. Strony z cache LiteSpeed rzadko uruchamiają WP-Cron, więc na produkcji:
  - w `wp-config.php`: `define( 'DISABLE_WP_CRON', true );`
  - w crontabie konta (DirectAdmin → Cron Jobs) wywołanie zaległych zadań co 5 minut. To obejmuje synchronizację o 05:00 i pozostałe zadania WP, np. zaplanowane wpisy, które bez tego przestałyby się publikować:

    ```cron
    */5 * * * * cd /home/jawagabu/domains/sardynkibiznesu.pl/public_html && wp cron event run --due-now --quiet
    ```

    Bez WP-CLI na serwerze: `*/5 * * * * curl -s -o /dev/null https://sardynkibiznesu.pl/wp-cron.php?doing_wp_cron`.
  - sprawdzenie (strefa czasowa serwera może być inna niż strony, liczy się czas strony): `$WP cron event list --hook=ihumbak_site_blocks_popularity_sync` pokazuje najbliższe uruchomienie o 05:00. Następnego dnia `$WP option get sb_popularity_sync` ma świeże `updated_at`. Ręcznie: `$WP sb popularity sync`.
- [ ] Reguła Redirection (Narzędzia → Przekierowania → Dodaj nowe), **wyłączona**:
  - źródłowy URL `^/page/(\d+)/?$`, opcja „Regex” włączona;
  - docelowy URL `/blog/page/$1/` (dokładnie tak, ścieżka bez domeny);
  - kod 301;
  - po zapisaniu: „Wyłącz”.
- [ ] Wersja robocza GTM: Pixel tylko za zgodą `ad_storage` (jeśli używany), `affiliate_click`, selektor `faq_opened`.
- [ ] Kontrole właściciela: ostatnie zdarzenie Pixela, konwersje Ads (ototax/ifirma), Convertiser, ShareASale, Impact.
- [ ] Przeróbki na stagingu: karty produktu (`/start`, `/umowy`) i 2 nagłówki `anchor-heading`.
- [ ] **Pełna próba na stagingu** (świeże `bin/staging-refresh`, potem przygotowanie jak wyżej na stagingu):
  - [ ] `switch-theme 3.0 --dry-run`, potem `switch-theme 3.0`;
  - [ ] PK1 i PK2 na stagingu (komparator SEO, `bin/perf-a11y-check`, ręczne scenariusze a11y): wszystko przechodzi;
  - [ ] Convert Pro (płatna, poza repo, lokalnie nie do zmierzenia; #39, #68): `bin/perf-a11y-check` z włączoną wtyczką, wynik per URL. `/wp-includes/` liczy się jako zasób własny, więc jQuery dociągnięte przez wtyczkę wraca do budżetu JS (≤ 40 KB, 0 blokujących);
  - [ ] Wpis z komentarzami przy pełnym zestawie produkcyjnym, w tym Convert Pro (#70): jeśli Convert Pro ładuje jQuery, wpDiscuz nie pobiera go drugi raz, a komentarze (lista, dodanie, odpowiedź, głosowanie, „załaduj więcej”, wejście na `#comment-N`) działają jak przed leniwym ładowaniem;
  - [ ] `switch-theme 2.0`: 2.0 wraca, lista wpisów się wypisuje;
  - [ ] `switch-theme 3.0` jeszcze raz (drugie przełączenie po rollbacku też działa).
- [ ] Ostatnie `bin/staging-refresh` i bramki tuż przed oknem.
- [ ] Termin: dzień roboczy, rano. Deweloper i właściciel dostępni przez cały dzień.

## 3. Okno przełączenia

Bez trybu konserwacji. Bez zamrażania treści poza samym oknem (redaktorzy nie publikują między PK0 a PK1).

1. **PK0, kopia:**

   ```bash
   mkdir -p ~/backup && cd ~/backup
   $WP db export pk0-$(date +%Y%m%d-%H%M).sql --single-transaction
   tar --exclude=wp-content/uploads -czf pk0-wp-content-$(date +%Y%m%d-%H%M).tar.gz -C $SITE wp-content
   ```

   Sprawdź, że plik `.sql` kończy się `-- Dump completed` i że archiwum ma rozsądny rozmiar.

2. **Przełączenie:**

   ```bash
   ~/bin/switch-theme 3.0 --path $SITE --front-page <id> --blog-page <id> --dry-run
   ~/bin/switch-theme 3.0 --path $SITE --front-page <id> --blog-page <id>
   ```

3. Ręczna przeróbka 7 bloków FAQ/akordeonu na `core/accordion` / `core/details`.
4. Publikacja przygotowanej wersji GTM.
5. **PK1, smoke test** (w oknie prywatnym, bez logowania, potem zalogowany):
   - [ ] każdy typ widoku zwraca 200 i wygląda poprawnie: strona główna, `/blog/` i `/blog/page/2/`, wpis, strona, kategoria, tag, autor, wyszukiwarka, 404 (status 404), `/feed/`;
   - [ ] `/page/2/` → 301 na `/blog/page/2/`;
   - [ ] wpisy ze starymi elementami: Przycisk w 3 zapisach, Notka, spis treści, YASR, Spreaker;
   - [ ] zapis do Newslettera na prawdziwym adresie: komunikat, Strona podziękowania, kontakt w MailerLite;
   - [ ] logowanie i edycja wpisu (zapis, podgląd).
6. **PK2:** komparator SEO (`bin/komparator-seo`, [docs/komparator-seo.md](../komparator-seo.md)) i `bin/perf-a11y-check https://sardynkibiznesu.pl` na produkcji.
7. Ponowny skan CookieYes i konfiguracja kategorii ciasteczek.
8. Zgłoszenie sitemap w Search Console.

**Role:** deweloper robi PK0, skrypt, bramki automatyczne, a11y i test Newslettera. Właściciel robi przegląd wizualny, kontrole kont (GTM, Meta, Ads, CookieYes) i daje „go” po PK2.

## 4. Rollback natychmiastowy

Bez dyskusji, gdy po przełączeniu wystąpi którekolwiek:

- 5xx albo biały ekran na którymkolwiek typie widoku;
- niedziałający zapis do Newslettera;
- blokujące różnice komparatora SEO na produkcji (status, tytuł, canonical, robots, Kotwice, H1) na więcej niż pojedynczych URL-ach;
- brak możliwości logowania lub edycji.

```bash
~/bin/switch-theme 2.0 --path $SITE --dry-run
~/bin/switch-theme 2.0 --path $SITE
```

Po rollbacku:

- [ ] `/`, wpis i `/page/2/` zwracają 200 w wyglądzie 2.0;
- [ ] wpisy z listy skryptu poprawione ręcznie (bloki `ihumbak/*` w 2.0 się nie renderują);
- [ ] wersja GTM cofnięta do poprzedniej;
- [ ] przyczyna opisana w issue, poprawka nową paczką, ponowna próba na stagingu i nowe okno.

Przerobione bloki FAQ zostają jako core: w 2.0 działają, tylko bez stylu.

Zrzut PK0 to ostatnia deska ratunku, nie zwykły rollback: przywrócenie bazy cofa treści i komentarze z czasu po przełączeniu.

**Naprawa na bieżąco** (nowa paczka, [paczki-i-staging.md](paczki-i-staging.md) p. 3–4, bez rollbacku): błędy wizualne, pojedyncze URL-e, Lighthouse gorszy niż na stagingu, tracking.

## 5. Po każdej aktualizacji WP

- Automatyczne aktualizacje core tylko dla wersji minor. Wersje major i aktualizacje wtyczek najpierw na stagingu.
- Lista kontrolna:
  - [ ] akordeon w nakładce mobilnej (filtr `render_block_core/navigation`, kontekst `overlayOpenedBy`);
  - [ ] klawiatura w nagłówku i nakładce;
  - [ ] `ihumbak/search-toggle` / `search-bar`;
  - [ ] Spis treści w wariancie `rail`.

## 6. Okres rollbacku: 30 dni

- 2.0, stare wtyczki i ich zipy zostają na serwerze. `bin/switch-theme 2.0` działa przez cały okres.
- Treści: bez zakazów redakcyjnych.
- **Co tydzień** (zapisz wynik w issue #36):
  - [ ] Search Console: 404 i błędy indeksowania, strony wykluczone, Core Web Vitals;
  - [ ] GA4: sesje organiczne wobec tego samego okresu sprzed przełączenia;
  - [ ] `~/bin/switch-theme status --path $SITE`: ile wpisów trzeba by poprawić po rollbacku.
- Decyzję o późnym rollbacku podejmuje właściciel.

## 7. Porządki po 30 dniach

- Repo, jeden commit: motyw `sardynkibiznesu-2.0`, toolchain 2.0, `deploy.yml` / `rollback.yml`, wtyczki `ihumbak-newsletter-form`, `ihumbak-anchor*`, `ihumbak-analytics`, aktualizacja `CLAUDE.md` i skilli.
- Serwer: usunięcie tych katalogów i zipów 2.0.
- Baza bez zmian. Opcja `sb_theme_switch` może zostać albo zostać usunięta (`$WP option delete sb_theme_switch`), gdy rollback nie wchodzi już w grę.

## Sprawdzenie lokalne (bez serwera)

Skrypt da się sprawdzić na lokalnym WordPressie ze zrzutem produkcji w Dockerze. Zmienna `WP_CLI` podmienia polecenie `wp`, a stdin musi przechodzić, bo skrypt przekazuje kod PHP przez `wp eval-file -`. Przykład: `WP_CLI="docker exec -i <kontener-cli> wp" bin/switch-theme 3.0 --path /var/www/html …`.
