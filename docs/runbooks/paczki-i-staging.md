# Paczki zip i odświeżenie stagingu

Dwa skrypty operacyjne ze spec #36 (ticket #62, plan w #35):

- `bin/build-packages`: paczki zip motywu i wtyczek z tagu gita.
- `bin/staging-refresh`: baza stagingu nadpisana kopią produkcji.

Warunki brzegowe: hosting współdzielony z SSH i WP-CLI, staging na subdomenie tego samego serwera (#39), bez automatyzacji wdrożeń. `deploy.yml` / `rollback.yml` nie są używane.

Ścieżki w przykładach (do potwierdzenia na serwerze):

| | |
|---|---|
| produkcja | `/home/jawagabu/domains/sardynkibiznesu.pl/public_html` |
| staging | `/home/jawagabu/domains/staging.sardynkibiznesu.pl/public_html` |
| paczki (poza webrootem) | `/home/jawagabu/deploy/<tag>/` |
| skrypty | `/home/jawagabu/bin/` |

## 1. Paczki z tagu

Lokalnie, w repozytorium:

```bash
git tag v3.0.0                  # tag na commicie do wdrożenia
bin/build-packages --list v3.0.0
bin/build-packages v3.0.0       # zestaw 3.0
bin/build-packages v3.0.1 ihumbak-toc   # tylko wybrane katalogi
```

- Bez nazw powstaje zestaw 3.0: motyw `sardynkibiznesu-3.0` oraz wtyczki `ihumbak-content-elements`, `ihumbak-toc`, `ihumbak-newsletter`, `ihumbak-site-blocks`, `ihumbak-tracking`, `ihumbak-tax-calculator`. Nazwa to katalog w `wp-content/themes/` albo `wp-content/plugins/`.
- Wynik: `dist/<tag>/<nazwa>-<tag>.zip` oraz `dist/<tag>/SHA256SUMS`. Katalog `dist/` jest ignorowany przez git.
- Zip zawiera dokładnie pliki z tagu (`git archive`), w jednym katalogu głównym o nazwie motywu lub wtyczki. Niezacommitowane zmiany nie trafiają do paczki.
- Odmowa (kod `1`): nazwa nie jest tagiem (gałąź, commit), w tagu nie ma takiego motywu ani wtyczki, zip dla tego tagu już istnieje. Paczki raz zbudowanej z tagu się nie nadpisuje. Poprawka to nowy tag.

## 2. Wgranie przez SFTP

Cały katalog `dist/<tag>/` trafia do `~/deploy/<tag>/` na serwerze, poza webrootem (nie do `public_html`). Poprzednich katalogów nie usuwamy: są paczkami do rollbacku.

Na serwerze (SSH) sprawdź, czy pliki doszły w całości:

```bash
cd ~/deploy/v3.0.0 && sha256sum -c SHA256SUMS
```

## 3. Instalacja

Najpierw staging, potem produkcja. Dla każdej paczki:

```bash
WP="wp --path=/home/jawagabu/domains/sardynkibiznesu.pl/public_html"
$WP theme install ~/deploy/v3.0.0/sardynkibiznesu-3.0-v3.0.0.zip --force
$WP plugin install ~/deploy/v3.0.0/ihumbak-toc-v3.0.0.zip --force
# … pozostałe wtyczki zestawu
```

- `--force` podmienia cały katalog motywu lub wtyczki. Pliki usunięte w nowej wersji znikają.
- **Instalacja nigdy niczego nie aktywuje.** Nigdy nie dodawaj `--activate`. Przełączenie motywu i zestawów wtyczek robi wyłącznie skrypt przełączenia.
- Ponowna instalacja nie zmienia stanu: aktywny motyw lub wtyczka zostaje aktywna, nieaktywna zostaje nieaktywna.
- Po instalacji: `$WP theme list` / `$WP plugin list` pokazują nową wersję. Gdy LiteSpeed jest aktywny, `$WP litespeed-purge all`.

## 4. Rollback kodu

Rollback kodu to ponowna instalacja poprzedniej paczki tym samym poleceniem:

```bash
$WP plugin install ~/deploy/v3.0.0/ihumbak-toc-v3.0.0.zip --force
```

Baza się nie zmienia. Rollback przełączenia (motyw 3.0 → 2.0) to osobny skrypt przełączenia `bin/switch-theme 2.0`, nie ta procedura (zob. [przelaczenie-3-0.md](przelaczenie-3-0.md)).

## 5. Odświeżenie stagingu

Skrypt działa na serwerze, przez SSH. Wgraj `bin/staging-refresh` do `~/bin/` (`chmod +x`).

Kiedy: na start testów i tuż przed ostateczną bramką.

```bash
~/bin/staging-refresh \
  --prod-path /home/jawagabu/domains/sardynkibiznesu.pl/public_html \
  --staging-path /home/jawagabu/domains/staging.sardynkibiznesu.pl/public_html \
  --prod-url https://sardynkibiznesu.pl \
  --staging-url https://staging.sardynkibiznesu.pl \
  --dry-run
```

Najpierw `--dry-run`: wykonuje tylko kontrole i wypisuje polecenia, nic nie zmienia. Potem to samo bez `--dry-run`. Skrypt prosi o wpisanie `tak`, a z `--yes` nie pyta.

Kroki:

1. **Kontrole (tylko odczyt).** Skrypt odmawia (kod `1`), gdy:
   - ścieżki są takie same;
   - staging łączy się z bazą produkcji (ten sam `DB_HOST`/`DB_NAME`);
   - prefiksy tabel są różne;
   - cel nie ma `WP_ENVIRONMENT_TYPE` = `staging` (stała w `wp-config.php` stagingu, #39);
   - `home` produkcji nie ma hosta z `--prod-url`.
2. **Eksport produkcji.** `wp db export --single-transaction` do `~/staging-refresh/prod-<data>.sql`, z prawami `600` (`--dump-dir` zmienia katalog). Produkcja jest tylko czytana. Gdy zrzut jest niekompletny, skrypt kończy się przed jakąkolwiek zmianą na stagingu.
3. **Import.** `wp db reset` na stagingu, potem import zrzutu.
4. **Domena.** `wp search-replace` po hoście, we wszystkich tabelach z prefiksem, bez kolumny `guid`: `//sardynkibiznesu.pl` i `//www.sardynkibiznesu.pl` → `//staging…`, także w postaci JSON (`\/\/`). Schemat adresu zostaje. Adresy e-mail (`@sardynkibiznesu.pl`) i ścieżki na dysku (`/domains/sardynkibiznesu.pl/`) się nie zmieniają. `home` i `siteurl` = `--staging-url`.
5. **Opcje stagingowe:**
   - `blog_public` = 0 („Zniechęcaj wyszukiwarki”): strony mają `noindex, nofollow`;
   - `comments_notify`, `moderation_notify` = 0;
   - LiteSpeed: `img_optm-auto`, `img_optm-cron`, `crawler`, `cdn-cloudflare` = 0 (bez optymalizacji obrazów z `uploads` produkcji, bez crawlera, bez czyszczenia strefy Cloudflare produkcji);
   - wpDiscuz: `sendMailToMentionedUsers`, `isNotifyOnCommentApprove`, `isFollowActive` = 0 oraz puste tabele `wc_comments_subscription` i `wc_follow_users`. wpDiscuz nie ma przełącznika, który wstrzymuje maile do subskrybentów, więc staging nie ma subskrybentów;
   - `wp cache flush`, a gdy LiteSpeed jest aktywny, `wp litespeed-purge all`.
6. Zrzut jest usuwany (`--keep-dump` go zostawia). Na koniec skrypt wypisuje `home`, `blog_public` i środowisko stagingu.

Skrypt nie dotyka plików: kod na stagingu instaluje się paczkami (p. 3), `uploads` to symlink do produkcji (#39).

Kod `2` oznacza błędne użycie (brak wymaganej opcji).

## Sprawdzenie lokalne (bez serwera)

Oba skrypty da się sprawdzić bez stagingu: dwie instancje WordPressa w Dockerze („produkcja” z lokalnym zrzutem i „staging”) i `WP_CLI="<polecenie wp w kontenerze>"`. Zmienna `WP_CLI` podmienia polecenie `wp`, a stdin i stdout muszą przechodzić (`docker compose run --rm -T cli wp`).
