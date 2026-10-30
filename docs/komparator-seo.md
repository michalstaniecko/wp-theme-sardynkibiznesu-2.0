# Komparator SEO 2.0 ↔ 3.0

Skrypt `bin/komparator-seo` porównuje wyrenderowane strony dwóch adresów bazowych: **A** (motyw 2.0, wzorzec) i **B** (motyw 3.0, sprawdzany). Gdy znajdzie regresję SEO albo regresję treści, kończy się kodem `1` i blokuje przełączenie (spec #36, ticket #60). Uruchamia się go ręcznie, z dowolnego komputera z dostępem do obu adresów.

## Wymagania

- PHP 8.4+ z rozszerzeniami `dom` i `curl` (parser HTML5 `Dom\HTMLDocument`). Bez Composera i bez zależności.
- Bez lokalnego PHP: `docker run --rm -v "$PWD":/app -w /app php:8.4-cli php bin/komparator-seo …` (obraz `php:8.4-cli` ma `dom` i `curl`).

## Uruchomienie

```bash
bin/komparator-seo \
  --a=https://sardynkibiznesu.pl \
  --b=https://staging.sardynkibiznesu.pl --auth-b=uzytkownik:haslo \
  --old-slugs=stare-slugi.txt
```

| Opcja | Znaczenie |
|---|---|
| `--a=URL` / `--b=URL` | adresy bazowe 2.0 i 3.0 (bez końcowego `/`) |
| `--auth-a=U:P` / `--auth-b=U:P` | Basic Auth (staging) |
| `--old-slugs=PLIK` | stare slugi: jedna ścieżka na linię, np. `/stary-slug/`; `#` zaczyna komentarz |
| `--search=FRAZA` | fraza dla `/?s=` (domyślnie `biznes`) |
| `--max-pages=N` | sprawdza `/page/2/`…`/page/N/`; bez tej opcji do pierwszej strony, której 2.0 nie zwraca z 200 (max 50) |
| `--limit=N` | tylko N pierwszych URL-i z sitemap, do prób |
| `--delay=MS` | przerwa między żądaniami do jednego adresu (domyślnie 300 ms) |
| `--report=PLIK` | raport Markdown (domyślnie `komparator-seo-RRRRMMDD-GGMM.md` w bieżącym katalogu, ignorowany przez git) |

Postęp idzie na stderr, na stdout trafia wynik i ścieżka raportu. Kod wyjścia: `0` = brak różnic blokujących, `1` = są różnice blokujące, `2` = błąd użycia.

Pełny przebieg to ok. 900 URL-i × 2 adresy. Przy domyślnej przerwie trwa kilkanaście minut i nie obciąża serwera. Na produkcji nie zmniejszaj `--delay`.

## Jakie URL-e

- wszystkie z sitemap Yoasta (`/sitemap_index.xml`) obu adresów, porównywane po ścieżce;
- opublikowane wpisy i strony spoza sitemap (np. `noindex`) z REST API 2.0 (`/wp-json/wp/v2/posts|pages`);
- lista specjalna: `/feed/`, `/page/N/`, nieistniejący adres (404), `/?s=FRAZA`, stare slugi z pliku.

## Co porównuje

Adres bazowy w wartościach (canonical, przekierowania) jest zastąpiony przez `{BASE}`, więc różne domeny A i B nie dają różnic.

| Sprawdzenie | Gdzie | Blokuje? |
|---|---|---|
| status i łańcuch przekierowań | każdy URL | tak |
| `<title>`, canonical, `meta robots` | każdy URL | tak |
| `meta description` | każdy URL | nie, do przeglądu |
| typy w grafie JSON-LD | każdy URL | nie, do przeglądu |
| `@graph` zawiera wyłącznie obiekty | 3.0 | tak |
| liczba H1 = 1 | 3.0 | tak |
| Kotwice (`id` nagłówków w treści) | wpisy i strony | brak lub zmiana: tak; nowe w 3.0: do przeglądu |
| `/feed/` zwraca 200 | 3.0 | tak (2.0 zwraca dziś 500) |
| `/page/N/` → 301 na `/blog/page/N/` → 200 | 3.0 | tak |

**Kotwice v1.** W 2.0 `id` starych Kotwic nadaje w przeglądarce `ihumbak-anchor.js`, tylko na stronach ze starym Spisem treści. Komparator powtarza ten skrypt na stronie 2.0 (tekst nagłówka, białe znaki na `-`, usunięte znaki spoza `[a-zA-Z0-9-]`), więc wzorzec to Kotwice, które widzi czytelnik. W 3.0 każda Kotwica z 2.0 musi istnieć co do znaku, na tym samym poziomie nagłówka.

## Porównanie treści

Dla każdego opublikowanego wpisu i strony porównuje obszar treści (`.entry-content` w 2.0, `.wp-block-post-content` w 3.0). Każda niezgodność blokuje:

- **surowe shortcode'y** w tekście 3.0 (np. `[sb_button …]`; nazwy z `_`/`-` oraz `sardynka`, `acf`, `caption` i inne z core);
- **jeden nagłówek zamiast zagnieżdżonego**: w HTML 3.0 nie ma `<hX>` bezpośrednio z `<hY>` w środku;
- **Przycisk**: liczba Przycisków w 3.0 (`.wp-block-button`) równa liczbie w 2.0 (`sb_button`, `cmsmasters_button`, `acf/sb-button` i `core/button`), bez starego markupu. Dodatkowo w całym przebiegu każdy styl (`is-style-*`) ma jeden kształt markupu (tagi i klasy bez stylu i kolorów), więc trzy zapisy dają identyczny markup;
- **Notka**: liczba `.wp-block-group.is-style-neutral` w 3.0 równa liczbie Notek w 2.0, bez `.cmsmasters_notice`;
- **Formularz zapisu**: tyle formularzy z polem e-mail co w 2.0 i każdy wysyła `POST` do `admin-post.php` (alias `[sardynki_getresponse_form]` działa bez JS). Komparator niczego nie wysyła.

Selektory są stałymi na początku skryptu. Jeśli wtyczki 3.0 zmienią klasy, popraw je tam.

## Raport

Markdown z wynikiem (OK / BLOKUJE), podsumowaniem według sprawdzenia, tabelą różnic blokujących, tabelą do przeglądu i listą sprawdzonych URL-i. Każdy wiersz ma URL, sprawdzenie i wartość z 2.0 i 3.0. Zasada rollbacku z runbooka: blokujące różnice SEO na więcej niż pojedynczych URL-ach oznaczają wycofanie.

## Kiedy uruchamiać

- przed przełączeniem: A = produkcja (2.0), B = staging (3.0);
- po przełączeniu (PK2): B = produkcja (3.0), A = kopia z 2.0, np. staging przełączony skryptem z powrotem na `2.0`.
