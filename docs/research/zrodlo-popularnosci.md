# Źródło popularności wpisów („najczęściej czytane”)

Ticket: #28 (część mapy #11). Data: 2026-10-29. Kontekst: motyw FSE 3.0 na WP 7.1.x, PHP 8.3+, bez kroku budowania. Blok „najczęściej czytane” (globalny i dla kategorii) oraz sortowanie kategorii po popularności czytają **tylko post meta**. Tę meta wypełnia plugin (ADR-0001). Hosting stoi za LiteSpeed Cache (plugin `litespeed-cache` 6.5.4 wg `wp-plugins.csv`). GA4 działa przez GTM, zgody obsługuje CookieYes. Na stronie jest ok. 340 opublikowanych wpisów.

## TL;DR — rekomendacja

| Decyzja | Wybór |
|---|---|
| Źródło | **GA4 Data API (`runReport`)** wywoływane raz dziennie przez cron z pluginu `ihumbak-*`. Własny beacon JS odpada: wymaga tej samej zgody co GA4, a więc nie daje więcej danych, za to sami musielibyśmy zbudować filtrowanie botów, agregację i retencję |
| Metryka / okno | `screenPageViews` z ostatnich 30 dni: `startDate: "30daysAgo"`, `endDate: "yesterday"`. Pomijamy bieżący dzień, bo dane dzienne GA4 przetwarzają się do ok. 12 h |
| Meta | `_sb_popularity` — `integer`, single, `default: 0`, zapisane dla **każdego** opublikowanego wpisu (także 0) |
| Metadane synchronizacji | opcja `sb_popularity_sync` (autoload off): `{updated_at, window_days, source: "ga4", rows, matched, unmatched_top[], last_error}` |
| Częstotliwość | raz na dobę, ok. 05:00 Europe/Warsaw. Systemowy cron hostingu woła `wp-cron.php`, a w `wp-config.php` jest `DISABLE_WP_CRON` (strona za LiteSpeed rzadko odpala WP-Cron). Po zapisie czyścimy cache stron z blokiem przez tag LiteSpeed |
| Brak danych | Brak wiersza w GA → `0`. Remis rozstrzyga data (`date DESC`). Synchronizacja się nie udała → zostają poprzednie wartości, `last_error` zapisany, po 7 dniach bez sukcesu widać komunikat w kokpicie. Zanim pierwsza synchronizacja się uda (brak opcji) → blok sortuje po dacie |
| Uwierzytelnianie | **Konto usługi (service account) z kluczem JSON** i rolą Viewer w usłudze GA4. Token pobieramy flow JWT bearer (`openssl_sign` RS256 + `wp_remote_post`), bez Composera. OAuth użytkownika odpada |

## 1. Opcja A — GA4 Data API pobierane cronem

### 1.1 Zapytanie

`POST https://analyticsdata.googleapis.com/v1beta/properties/{PROPERTY_ID}:runReport`, scope `https://www.googleapis.com/auth/analytics.readonly` ([runReport](https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/properties/runReport)).

```json
{
  "dateRanges": [{ "startDate": "30daysAgo", "endDate": "yesterday" }],
  "dimensions": [{ "name": "hostName" }, { "name": "pagePath" }],
  "metrics": [{ "name": "screenPageViews" }],
  "dimensionFilter": { "filter": { "fieldName": "hostName", "stringFilter": { "value": "sardynkibiznesu.pl" } } },
  "orderBys": [{ "metric": { "metricName": "screenPageViews" }, "desc": true }],
  "limit": "10000",
  "returnPropertyQuota": true
}
```

- `limit` domyślnie wynosi 10 000, maksimum to 250 000 wierszy na żądanie. Stronicowanie robimy przez `offset` ([runReport](https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/properties/runReport)). Przy ok. 340 wpisach i innych ścieżkach (strony, kategorie, `/page/2/`) wystarczy jedno żądanie.
- Daty względne (`NdaysAgo`, `yesterday`, `today`) liczą się w strefie czasowej raportowania usługi ([DateRange](https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/DateRange)). Strefa usługi powinna więc być ustawiona na Europe/Warsaw (punkt na checkliście).
- `screenPageViews`: „The number of app screens or web pages your users viewed. Repeated views of a single page or screen are counted.” ([API schema](https://developers.google.com/analytics/devguides/reporting/data/v1/api-schema)). Wybieramy liczbę odsłon, a nie `activeUsers`, bo metryki użytkowników częściej podlegają progom (pkt 1.5). Ranking i tak jest względny.
- Filtr po `hostName` odcina odsłony z kopii testowych, proxy i tłumaczy, jeśli trafiają do tej samej usługi.

### 1.2 Uwierzytelnianie: konto usługi kontra OAuth

| | Konto usługi (JSON) | OAuth użytkownika |
|---|---|---|
| Interakcja | brak; cron sam podpisuje JWT | jednorazowa zgoda w przeglądarce, potem refresh token |
| Ryzyko wygaśnięcia | klucz nie wygasa (o ile nie ustawimy wygaśnięcia) | refresh token wygasa m.in. po 7 dniach, jeśli aplikacja ma status „Testing”, po 6 miesiącach nieużywania, po cofnięciu dostępu i po przekroczeniu limitu 100 tokenów na konto i klienta ([OAuth 2.0](https://developers.google.com/identity/protocols/oauth2)) |
| Dostęp w GA4 | e-mail konta usługi (`…@….iam.gserviceaccount.com`) dodany w Admin → Property access management | dostęp konta Google, które wyraziło zgodę |
| Ryzyko | wyciek klucza działa jak hasło („not subject to any additional sign-in verifications”) ([best practices](https://docs.cloud.google.com/iam/docs/best-practices-for-managing-service-account-keys)) | wyciek refresh tokena, zależność od konkretnej osoby |

**Wybór: konto usługi.** Działa bez człowieka i bez wygasających tokenów. Środki ostrożności według [Google](https://docs.cloud.google.com/iam/docs/best-practices-for-managing-service-account-keys): klucz nigdy w repozytorium, rotacja, w GA4 tylko rola Viewer. Uwaga: w organizacjach Google Cloud utworzonych 3 maja 2024 r. lub później ograniczenia „Disable service account key creation/upload” są domyślnie włączone (tamże). Jeśli projekt należy do takiej organizacji, właściciel musi zrobić wyjątek dla tego projektu. Projekt na prywatnym koncie, bez organizacji, nie podlega politykom organizacji.

Flow bez biblioteki ([Service accounts](https://developers.google.com/identity/protocols/oauth2/service-account)): JWT z nagłówkiem `RS256` i claimami `iss` (e-mail konta usługi), `scope`, `aud = https://oauth2.googleapis.com/token`, `iat` i `exp` (maks. +1 h). Podpisujemy go kluczem prywatnym i wysyłamy `POST https://oauth2.googleapis.com/token` z `grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer` i `assertion=<JWT>`. Odpowiedź zawiera `access_token` z `expires_in` (3600). Token trzymamy w transiencie na ok. 55 min. Google zaleca bibliotekę kliencką zamiast ręcznego JWT (tamże). Oficjalny pakiet PHP wymaga jednak Composera i dużego drzewa zależności, a to kłóci się z decyzją „bez kroku budowania”. Ręczny podpis to ok. 40 linii z `openssl_sign()`, do tego test jednostkowy na stałym kluczu.

Przechowywanie klucza: plik JSON poza webrootem i stała w `wp-config.php` (np. `SB_GA4_CREDENTIALS_FILE`, `SB_GA4_PROPERTY_ID`). Klucz nie trafia do `wp_options` ani do REST.

### 1.3 Limity (tokeny)

Usługi standardowe ([Quotas](https://developers.google.com/analytics/devguides/reporting/data/v1/quotas)): 200 000 tokenów na usługę dziennie, 40 000 na usługę na godzinę, 14 000 na projekt i usługę na godzinę, 10 równoczesnych żądań, 10 błędów serwera na projekt i usługę na godzinę, 120 „potentially thresholded requests” na godzinę. Według dokumentacji „most requests will charge 10 or fewer tokens”. Koszt rośnie m.in. z liczbą wierszy, długością zakresu dat i wymiarami o wysokiej kardynalności (wprost wymieniony `pagePath`). Jedno dzienne żądanie (nawet kilkadziesiąt tokenów) to ułamek promila limitu. `returnPropertyQuota: true` zwraca `propertyQuota` z `consumed`/`remaining`, więc warto to logować. `batchRunReports` mieści do 5 raportów ([batchRunReports](https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/properties/batchRunReports)), ale tu go nie potrzebujemy.

### 1.4 Świeżość danych

Standardowa usługa, kategoria „Normal”: dane intraday po 2–6 h, dane dzienne po ok. 12 h, realtime po kilku minutach, ale z ograniczonym zestawem wymiarów ([Data freshness](https://support.google.com/analytics/answer/11198161)). Dlatego okno kończy się na `yesterday`, a cron chodzi rano. Wczorajsze dane mogą być jeszcze nieco niepełne, co przy oknie 30 dni nie ma znaczenia.

### 1.5 Progi (thresholding), próbkowanie, wiersz „(other)”

- Progi GA4 ukrywają dane, z których dałoby się zidentyfikować użytkownika. Dotyczą zwłaszcza danych demograficznych (także w wywołaniach API) i zapytań wyszukiwania. Nie da się ich wyłączyć. Pomaga szerszy zakres dat ([Data thresholds](https://support.google.com/analytics/answer/9383630)). Nasze zapytanie nie zawiera demografii.
- Odpowiedź ma `metadata.subjectToThresholding`, `samplingMetadatas` (brak = brak próbkowania) i `dataLossFromOtherRow` (true = część kombinacji wpadła do wiersza „(other)”) ([ResponseMetaData](https://developers.google.com/analytics/devguides/reporting/data/v1/rest/v1beta/ResponseMetaData)). Plugin zapisuje te trzy flagi w `sb_popularity_sync`. Gdy któraś jest ustawiona, ostrzega w kokpicie i mimo to zapisuje wartości.
- Ruch znanych botów GA4 wyklucza automatycznie, na podstawie badań Google i listy IAB. Tego nie da się wyłączyć ([Known bot-traffic exclusion](https://support.google.com/analytics/answer/9888366)). To duża zaleta wobec opcji B.

### 1.6 Mapowanie `pagePath` → ID wpisu

- `pagePath` to „The portion of the URL between the hostname and query string”, czyli **bez query stringa** (`/store/contact-us`). `pagePathPlusQueryString` zawiera query string ([API schema](https://developers.google.com/analytics/devguides/reporting/data/v1/api-schema)). Używamy `pagePath`, więc `?utm_…` i `?fbclid=` sklejają się same.
- `url_to_postid( home_url( $path ) )` zwraca ID albo `0`. Funkcja obcina query string i `#kotwicę`, ignoruje ukośniki na początku i końcu (`/wpis` = `/wpis/`), dopasowuje schemat i `www.` do `home_url()`, a dla obcego hosta zwraca `0`. Działa tylko przy „ładnych” permalinkach ([url_to_postid](https://developer.wordpress.org/reference/functions/url_to_postid/)). Ścieżki stronicowania wpisu (`/wpis/2/`) przechodzą przez reguły przepisywania i wskazują ten sam wpis.
- Kilka ścieżek może dawać to samo ID (stary i nowy wariant, stronicowanie, warianty z ukośnikiem i bez), więc **sumujemy** po ID. Zostawiamy tylko `post_type = post` i `post_status = publish`.
- Przekierowania: `url_to_postid` nie zna starych slugów ani reguł pluginu Redirection (aktywny wg `wp-plugins.csv`). Dla ścieżek z `0` robimy drugą próbę po meta `_wp_old_slug` (ostatni segment ścieżki), a resztę odkładamy jako `unmatched_top` (np. 20 ścieżek z największą liczbą odsłon) do ręcznego przejrzenia. Przy oknie 30 dni stare adresy mają marginalny ruch.
- Do sprawdzenia na prawdziwych danych: czy GA4 zwraca w `pagePath` polskie znaki w slugach zdekodowane, czy jako `%C5%82…`. Normalizator powinien próbować obu wariantów (`rawurldecode`/`rawurlencode` segmentów). W tym badaniu nie zweryfikowano tego w dokumentacji.
- Wyniki mapowania cache'ujemy w pamięci na czas jednego przebiegu (ok. kilkaset wywołań `url_to_postid` raz na dobę to pomijalny koszt).

### 1.7 Luka z powodu zgód (Consent Mode)

- **Basic**: tagi Google są blokowane, dopóki użytkownik nie zareaguje na baner. Przy odmowie „no data is transferred to Google at all – not even the consent status”. **Advanced**: tagi ładują się od razu, a przy `denied` „send measurements without cookies” ([Consent mode concepts](https://developers.google.com/tag-platform/security/concepts/consent-mode)).
- W CookieYes tryb advanced włącza się opcją „Allow Google tags to fire before consent”. Kategoria `analytics` odpowiada `analytics_storage` ([CookieYes GCM](https://www.cookieyes.com/documentation/implementing-google-consent-mode-using-cookieyes/)).
- Modelowanie behawioralne wymaga m.in. co najmniej 1000 zdarzeń dziennie z `denied` przez 7 dni i co najmniej 1000 dziennych użytkowników z `granted`. Modelowane dane widać tylko przy tożsamości raportowania „Blended”. Artykuł nie mówi, czy Data API je zwraca, a eksport (BigQuery) ich nie obejmuje ([Behavioral modeling](https://support.google.com/analytics/answer/11161109)).
- Wniosek: GA4 liczy w praktyce **odsłony osób, które zgodziły się na analitykę** (i ewentualnie dane modelowane). Bezwzględne liczby są zaniżone, ale ranking jest wiarygodny, o ile odsetek zgód nie różni się mocno między wpisami. Do bloku „najczęściej czytane” to wystarcza. Liczb nie pokazujemy czytelnikom jako „X odsłon”.
- Kod 2.0 (`inc/tracking-codes.php`) nie ładuje GTM zalogowanym użytkownikom, więc wizyty redakcji nie zawyżają licznika.

## 2. Opcja B — własny beacon JS do endpointu REST

### 2.1 Czy działa za cache LiteSpeed

- Strona z cache się wyświetla, a JS z niej wysyła osobne żądanie, więc cache strony nie przeszkadza samemu beaconowi. `navigator.sendBeacon()` zawsze wysyła **POST**. Najpewniej wysyłać go przy `visibilitychange` ([MDN sendBeacon](https://developer.mozilla.org/en-US/docs/Web/API/Navigator/sendBeacon)).
- LiteSpeed Cache ma domyślnie włączoną opcję „Cache REST API” („allows requests made through WordPress REST API calls to be cached”) oraz listę „Do Not Cache URIs” ([LSCWP Cache](https://docs.litespeedtech.com/lscache/lscwp/cache/)). Oficjalna dokumentacja nie opisuje wprost, jak traktowany jest POST. Według zespołu wsparcia pluginu domyślnie cache'owane są tylko GET-y (wątek na forum, źródło wtórne). Dla pewności endpoint trzeba dopisać do „Do Not Cache URIs” albo wołać w nim `do_action( 'litespeed_control_set_nocache', … )` ([LSCWP API](https://docs.litespeedtech.com/lscache/lscwp/api/)).
- Przykład z repozytorium: `ihumbak-analytics` już dziś wysyła na każdej stronie POST do `admin-ajax.php` (`fcapi_page_view`), więc na tym hostingu wzorzec da się zastosować.

### 2.2 Nonce na stronach z cache

- Strona z cache zawiera nonce sprzed wielu godzin. Nonce w nagłówku `X-WP-Nonce`, który już nie przechodzi weryfikacji, kończy się błędem `rest_cookie_invalid_nonce` (403). Brak nonce'a oznacza `wp_set_current_user( 0 )` i dalsze, anonimowe przetwarzanie ([rest_cookie_check_errors](https://developer.wordpress.org/reference/functions/rest_cookie_check_errors/), [REST auth](https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/)).
- LiteSpeed obsługuje domyślne nonce WP jako bloki ESI z cache 12 h (`litespeed_nonce` dla własnych) ([LSCWP API](https://docs.litespeedtech.com/lscache/lscwp/api/)). ESI wymaga jednak wsparcia serwera, a przy nonce'ach osadzonych w inline JS znane są problemy.
- Wniosek: licznik byłby **bez nonce'a**: publiczna trasa z `permission_callback => '__return_true'` (tak dokumentacja każe oznaczać publiczne endpointy, [Adding custom endpoints](https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/)). To z kolei oznacza endpoint otwarty dla każdego, czyli łatwy do zaśmiecenia.

### 2.3 Obciążenie zapisów i agregacja

- `update_post_meta` przy każdej odsłonie to zapis do `wp_postmeta` przy każdym wyświetleniu strony z cache. Do tego dochodzi wyścig read-modify-write i brak okna czasowego (licznik rośnie w nieskończoność).
- Okno 30 dni wymaga własnej tabeli kubełków dziennych `(post_id, day, views)` z `INSERT … ON DUPLICATE KEY UPDATE views = views + 1`. Dzienny cron sumowałby kubełki do `_sb_popularity` i usuwał starsze niż 30 dni. Alternatywa: bufor w object cache (w 2.0 jest drop-in `object-cache.php`) zrzucany co kilka minut. Przy restarcie cache część odsłon przepada.

### 2.4 Boty

Własny licznik nie ma listy IAB. Musielibyśmy sami dopisać filtr User-Agent, limit na IP (transient), pomijanie zalogowanych, odrzucanie żądań bez nagłówka `Origin`/`Referer` własnej domeny i deduplikację w sesji. Wymóg wykonania JS odsiewa część prostych crawlerów, ale nie przeglądarki headless. Publiczny endpoint bez nonce'a łatwo nabić skryptem.

### 2.5 RODO / ePrivacy — czy licznik bez cookies jest dozwolony bez zgody

- EDPB, Wytyczne 2/2023 w sprawie zakresu technicznego art. 5 ust. 3 dyrektywy ePrivacy, wersja ostateczna 2.0 przyjęta 7.10.2024 ([EDPB](https://www.edpb.europa.eu/our-work-tools/our-documents/guidelines/guidelines-22023-technical-scope-art-53-eprivacy-directive_en), [PDF](https://www.edpb.europa.eu/system/files/2024-10/edpb_guidelines_202302_technical_scope_art_53_eprivacydirective_v2_en_0.pdf)):
  - pkt 33: „Additional examples would include JavaScript code, where the accessing entity instructs the browser of the user to send asynchronous requests with the targeted information. Such access clearly falls within the scope of Article 5(3) ePD”;
  - pkt 51: przy dynamicznie budowanych pikselach śledzących „it is the distribution of the applicative logic (usually a JavaScript code) that constitutes the instruction”;
  - pkt 9–12: art. 5 ust. 3 dotyczy „information”, niezależnie od tego, czy to dane osobowe.
- W Polsce art. 5 ust. 3 wdraża art. 399 Prawa komunikacji elektronicznej (obowiązuje od 10.11.2024; wcześniej art. 173 Prawa telekomunikacyjnego). Zgoda nie jest wymagana tylko przy przesyłaniu komunikatu i przy usłudze, której użytkownik zażądał ([tekst art. 399, lexlege](https://lexlege.pl/komunik-elektr/art-399/), źródło wtórne. Pierwotne: Dz.U. 2024 poz. 1221). Pomiar oglądalności nie jest tam wymieniony wprost jako wyjątek.
- Wniosek inżynierski (to nie porada prawna): beacon JS **mieści się w zakresie** art. 5 ust. 3 nawet bez cookies i `sessionStorage`. Najbezpieczniej odpalać go dopiero po zgodzie w kategorii `analytics` CookieYes (`getCkyConsent().categories.analytics`, dostępne po zdarzeniu `cookieyes_banner_loaded`, [CookieYes API](https://www.cookieyes.com/documentation/retrieving-consent-data-using-api-getckyconsent/)). Wtedy beacon liczy **tę samą populację co GA4 w trybie basic**, więc pod względem pokrycia danych nic nie zyskujemy. Odpalanie licznika bez zgody to decyzja prawna właściciela, a nie techniczna.

## 3. Porównanie

| Kryterium | A: GA4 Data API | B: własny beacon |
|---|---|---|
| Działa za LiteSpeed | tak (dane zbiera GA, cron po stronie serwera) | tak, ale endpoint musi być wyłączony z cache |
| Populacja (zgody) | zgody na analitykę (+ ewentualnie dane modelowane) | ta sama, jeśli beacon czeka na zgodę |
| Boty | filtr GA (IAB), nie do wyłączenia | do napisania samemu, endpoint otwarty |
| Zapisy w DB | ok. 340 `update_post_meta` raz na dobę | zapis przy każdej odsłonie + tabela kubełków + retencja |
| Świeżość | do D-1 (~12 h przetwarzania) | prawie na bieżąco (niepotrzebne do rankingu) |
| Zależności | konto Google Cloud, klucz konta usługi, sieć do Google | brak zewnętrznych |
| Kod do utrzymania | JWT + 1 zapytanie + mapowanie URL | JS + REST + tabela + agregacja + antyspam + bramka zgód |
| Ryzyko | wyciek klucza (rola Viewer = tylko odczyt), zmiany API | nabijanie licznika, wzrost bazy |

**Rekomendacja: A.** Opcja B przydaje się tylko wtedy, gdy właściciel nie chce lub nie może dać dostępu do GA4, albo GA4 zostanie wyłączone. Wtedy kontrakt `_sb_popularity` się nie zmienia, wymieniamy tylko „dostawcę” w pluginie. Dlatego plugin powinien mieć interfejs źródła (`PopularitySource::fetch(): array<int,int>`) i jedną implementację GA4.

## 4. Kontrakt meta i zachowanie

- `register_post_meta( 'post', '_sb_popularity', [ 'type' => 'integer', 'single' => true, 'default' => 0, 'show_in_rest' => false, 'auth_callback' => '__return_false' ] )`. Podkreślnik na początku robi z niej meta chronioną (niewidoczną w „Własnych polach”). Nie da się jej podpiąć przez Block Bindings `core/post-meta`, ale bloki czytają ją w PHP, więc to nie przeszkadza.
- Zapytanie bloku: `meta_key => '_sb_popularity'`, `orderby => [ 'meta_value_num' => 'DESC', 'date' => 'DESC' ]`. Dokumentacja WP_Query wymaga `meta_key` przy `meta_value_num` ([WP_Query](https://developer.wordpress.org/reference/classes/wp_query/)). Taki warunek łączy tabelę meta, więc wpisy bez klucza wypadają z wyników (zachowanie silnika zapytań, nie zostało tu sprawdzone w kodzie źródłowym). Dlatego sync **zapisuje 0 każdemu opublikowanemu wpisowi**, który nie dostał wartości, a hook `publish_post` dopisuje 0 nowym wpisom.
- Sync robimy atomowo: najpierw pobieramy i mapujemy całość, a meta zapisujemy dopiero po udanym przebiegu. Przy błędzie API niczego nie nadpisujemy.
- Po zapisie czyścimy cache: blok przy renderze dodaje tag (`do_action( 'litespeed_tag_add', 'sb_popularity' )`), a sync woła `do_action( 'litespeed_purge', 'sb_popularity' )` zamiast `litespeed_purge_all` ([LSCWP API](https://docs.litespeedtech.com/lscache/lscwp/api/)).
- Cron: WP-Cron uruchamia się tylko przy ładowaniu strony, bez gwarancji czasu ([Cron](https://developer.wordpress.org/plugins/cron/)). Strony z cache LiteSpeed nie uruchamiają PHP, więc zalecamy `define( 'DISABLE_WP_CRON', true );` i systemowy cron wołający `wp-cron.php` ([Hooking WP-Cron into the system task scheduler](https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/)). Bez tego daily event też się wykona, tylko o nieprzewidywalnej porze. Dodatkowo komenda WP-CLI `wp sb popularity sync` do ręcznego uruchomienia i testów.
- Okno i źródło trzymamy w filtrach (`sb_popularity_window_days`, domyślnie 30), żeby dało się je zmienić bez zmiany kontraktu.

## 5. Gotowe pluginy (tylko jako punkt odniesienia)

- **Post Views Counter** 1.8.1, 100 000+ instalacji, testowany do WP 7.1.4. Liczy w trybach PHP, JavaScript i REST API. Zgodność z cache stron („count through cached pages”) i „Fast AJAX” są w wersji Pro. Do odstępu między liczeniami używa cookie first-party ([wordpress.org](https://wordpress.org/plugins/post-views-counter/)). To odpowiednik opcji B ze wszystkimi jej kompromisami.
- **Jetpack Stats**: darmowy dla każdego typu strony, logi z IP przechowuje Automattic przez 28 dni ([Jetpack](https://jetpack.com/support/jetpack-stats/)). Wymaga połączenia z WordPress.com i dodaje kolejnego zewnętrznego odbiorcę danych. Nie wnosi nic ponad GA4, które już działa.

## 6. Checklista dla właściciela (do provisioningu)

- [ ] Podać **Property ID** usługi GA4 (liczbowy, Admin → Property details). To nie jest Measurement ID `G-…` ani ID kontenera GTM.
- [ ] Sprawdzić w GA4 **strefę czasową raportowania** (Europe/Warsaw) i potwierdzić produkcyjny **hostName** (z `www` czy bez).
- [ ] W Google Cloud Console utworzyć (lub wskazać) projekt i **włączyć „Google Analytics Data API”**.
- [ ] Utworzyć **konto usługi** (np. `sb-popularity`) i **klucz JSON**. Jeśli tworzenie kluczy blokuje polityka organizacji, zrobić wyjątek dla tego projektu.
- [ ] W GA4 → Admin → **Property access management** dodać e-mail konta usługi z rolą **Viewer**.
- [ ] Przekazać plik JSON bezpiecznym kanałem (nie przez repozytorium, issue ani e-mail w treści). Wgrać go na serwer **poza webroot** i dopisać do `wp-config.php` stałe `SB_GA4_PROPERTY_ID` i `SB_GA4_CREDENTIALS_FILE`.
- [ ] W panelu hostingu dodać **systemowy cron** (np. co 15 min `wp-cron.php` albo raz dziennie WP-CLI) i ustawić `DISABLE_WP_CRON`.
- [ ] Sprawdzić w CookieYes, czy Consent Mode działa w trybie **basic czy advanced** (opcja „Allow Google tags to fire before consent”). To mówi, jak duża jest luka w danych.
- [ ] Wpisać do kalendarza **rotację klucza** (np. co 12 miesięcy) i wiedzieć, jak go unieważnić w IAM w razie wycieku.

## 7. Otwarte kwestie poza tym ticketem

- `wp-content/plugins/ihumbak-analytics` (aktywny) ładuje na każdej stronie, bez bramki zgód, Facebook Pixel, nieżyjący tag Universal Analytics `UA-62814019-14` i POST `fcapi_page_view` do `admin-ajax.php`. W repozytorium nie ma handlera tej akcji. Równolegle GTM ładuje się z `inc/tracking-codes.php`. Ten dług trzeba uporządkować przed 3.0 (zgodność z CookieYes, usunięcie UA) i to zasługuje na osobny ticket w mapie.
