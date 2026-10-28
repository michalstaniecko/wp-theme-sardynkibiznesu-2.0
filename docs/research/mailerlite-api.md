# MailerLite API: zapis do newslettera

Research do #15 (część mapy #11). Stan na 2026-10-28.

## TL;DR

- `ihumbak-newsletter-form` używa już **nowego MailerLite** (`https://connect.mailerlite.com/api`) przez oficjalne SDK `mailerlite/mailerlite-php` v1.0.0. Nie korzysta z Classic API (`api.mailerlite.com/api/v2`).
- Zapis do newslettera to jedno wywołanie: `POST /api/subscribers` z polami `email`, `fields.name` i `groups: [<id grupy>]`. Endpoint działa jak upsert i niczego nie usuwa. Zwraca 201 (nowy subskrybent), 200 (subskrybent już istniał) albo 422 (błąd walidacji).
- Double opt-in nie ma osobnego parametru w API. Włącza się go przełącznikiem konta „Double opt-in for API and integrations”, a dla istniejących subskrybentów wymusza przez `status: "unconfirmed"`.
- Limit to 120 żądań/min globalnie; po przekroczeniu API zwraca 429 z nagłówkiem `Retry-After`.
- API nie ma pola „zgoda RODO”. Dowód zgody trzeba zbudować samemu z `opted_in_at`, `optin_ip` / `ip_address` i ewentualnie pola niestandardowego. Treść klauzuli najlepiej trzymać po stronie WP.
- GetResponse: kod nadal istnieje i jest **warunkowo** wywoływalny. Zależy to od opcji ACF `default_newsletter`, której wartości produkcyjnej nie da się sprawdzić z repo. Wszystkie formularze, łącznie z shortcode'em `sardynki_getresponse_form`, przekazują jednak ID grupy **MailerLite**. To mocna przesłanka, że produkcja używa MailerLite. W nowej wtyczce GetResponse można pominąć.

## 1. Stan obecny wtyczki (kod lokalny)

| Element | Plik | Co robi |
|---|---|---|
| SDK | `wp-content/plugins/ihumbak-newsletter-form/composer.json`, `composer.lock` | `mailerlite/mailerlite-php` **v1.0.0** + `symfony/http-client` + `nyholm/psr7` |
| Host API | `vendor/mailerlite/mailerlite-php/src/MailerLite.php` | `'host' => 'connect.mailerlite.com'`, `'api_path' => 'api'`, czyli nowe MailerLite |
| Wrapper | `inc/NewsletterMailerLite.php` | `subscribers->create(['email', 'fields' => ['name'], 'groups' => [$groupId]])`, czyli `POST /api/subscribers` |
| Endpoint AJAX | `getResponse/addContact.php` | akcja `add_to_get_response` (priv + nopriv). Funkcja `ih_add_subscription()` przełącza się na `get_field('default_newsletter', 'options')`: `mailerlite` → `add_to_mailer_lite()`, `getresponse` → `add_to_get_response()` |
| Klucz API MailerLite | ACF options `newsletter_api_key` | trzymany w bazie, nie w repo |
| ID grupy | ACF options `mailerlite_default_token` | mimo nazwy „token” to ID grupy MailerLite |
| Formularz pod wpisem | `inc/newsletter-form.php` (filtr `the_content`) | ukryte pole `campaignId` = `mailerlite_default_token` lub `getresponse_default_token`, zależnie od `default_newsletter` |
| Shortcode | `inc/getresponse-form.php` → `[sardynki_getresponse_form]` | `campaign_token` = `$a['campaign_id']`, domyślnie **`mailerlite_default_token`**. `action` formularza wskazuje `app.getresponse.com/add_subscriber.html`, ale JS (`getResponse/getresponse.js`) przechwytuje submit, zamienia `first_name`→`name` i `campaign_token`→`campaignId`, a potem wysyła AJAX na ten sam endpoint |
| Definicja pól ACF | `wp-content/themes/sardynkibiznesu-2.0/acf-fields/group_61a14f53d9d27.json` | `default_newsletter` (select: getresponse/mailerlite), `getresponse_default_token`, `mailerlite_default_token`, `getresponse_default_thankyou`, `newsletter_api_key` |

### Czy GetResponse jest nadal „żywy”?

- **W kodzie tak.** `add_to_get_response()` i biblioteka `getResponse/getresponse-api-php-master/` (API v3) są ładowane przy każdym żądaniu. Ścieżka uruchamia się, gdy `default_newsletter === 'getresponse'`.
- **Najpewniej nie na produkcji.** Shortcode `sardynki_getresponse_form` (176 wystąpień w treściach) przekazuje domyślnie ID grupy MailerLite, więc z ustawieniem `getresponse` wysyłałby do GetResponse ID grupy MailerLite i nie działałby. Nazwa shortcode'u została po starej integracji. Wartość `default_newsletter` warto potwierdzić w panelu (Theme settings) albo w bazie (`wp_options`, `options_default_newsletter`).
- **Fallback bez JS jest zepsuty.** Bez JavaScriptu shortcode wysyła natywny POST do GetResponse z ID grupy MailerLite.
- **Bezpieczeństwo:** w `getResponse/addContact.php` jest **zahardkodowany klucz API GetResponse** (w historii gita). Nie przenosić go do nowej wtyczki, a klucz unieważnić w panelu GetResponse.
- Inne problemy, których nowa wtyczka nie powinna powielać: brak nonce, brak sanitizacji `$_POST['userForm']`, odpowiedź błędu zwraca do przeglądarki cały obiekt wyjątku (`'groups' => $error`), a `thankyou_url` przychodzi z ukrytego pola formularza.

**Wniosek dla shortcode'u w nowej wtyczce:** zachować tag `sardynki_getresponse_form` jako alias, bo jest 176 wystąpień w treściach, ale obsługiwać wyłącznie MailerLite.

## 2. Nowe MailerLite vs Classic

- Konta założone **przed 22 marca 2022** to MailerLite Classic, późniejsze to nowe MailerLite. API V1/V2 (`api.mailerlite.com`) „only work with MailerLite Classic and will eventually be deprecated”. [developers.mailerlite.com](https://developers.mailerlite.com/)
- Classic nie dostaje nowych integracji. Nie ma ogłoszonej daty wyłączenia płatnych kont Classic, a darmowe konta Classic zamknięto w lutym 2024. [MailerLite vs Legacy (Classic) FAQ](https://www.mailerlite.com/help/new-mailerlite-faq), [Closure of Classic Free accounts](https://www.mailerlite.com/help/discontinuation-of-free-plans-in-mailerlite-classic)
- Skoro obecna wtyczka działa na `connect.mailerlite.com`, konto jest już w nowym MailerLite. **Nowa wtyczka powinna celować wyłącznie w `connect.mailerlite.com/api`.** Klucze Classic tam nie działają (wniosek z rozdzielenia platform; dokumentacja nie mówi tego wprost).

## 3. Zapis subskrybenta: nowe API

### Autoryzacja i nagłówki

Źródło: [developers.mailerlite.com/docs](https://developers.mailerlite.com/docs/)

- Base URL: `https://connect.mailerlite.com/api`
- `Authorization: Bearer <API_KEY>`. Bez poprawnego tokena API zwraca 401 `{"message": "Unauthenticated."}`. Klucz jest przypisany do użytkownika, który go wygenerował, i przestaje działać po usunięciu tego użytkownika z konta. Najlepiej wygenerować klucz z konta „technicznego” lub właściciela.
- `Content-Type: application/json`, `Accept: application/json`
- Wersjonowanie datą w nagłówku `X-Version` (np. `X-Version: 2038-01-19`). Bez nagłówka API używa najnowszej wersji. **Rekomendacja:** przypiąć `X-Version` do daty wdrożenia, żeby zmiany API nie zepsuły formularza po cichu.

### Endpoint zapisu

Źródło: [docs/subscribers](https://developers.mailerlite.com/docs/subscribers.html)

`POST /api/subscribers`

```json
{
  "email": "jan@example.com",
  "fields": { "name": "Jan" },
  "groups": ["<GROUP_ID>"],
  "status": "active | unconfirmed",
  "ip_address": "203.0.113.5",
  "opted_in_at": "2026-10-28 12:00:00",
  "optin_ip": "203.0.113.5"
}
```

- **Upsert, niedestrukcyjny:** „If a subscriber already exists, it will be updated with new values… omitting fields or groups will not remove them.” Subskrybent zostaje dopisany do podanych grup i nie jest usuwany z pozostałych. Na `PUT /api/subscribers/{id}` jest odwrotnie: lista grup zastępuje dotychczasową, więc do formularza **nie używać PUT**.
- Statusy: `active`, `unsubscribed`, `unconfirmed`, `bounced`, `junk`.
- `resubscribe: true` pozwala ponownie zapisać kogoś, kto wcześniej się wypisał. Subskrybentów `unsubscribed`, `bounced` ani `junk` nie da się reaktywować przez API („abuse prevention”), tylko przez aplikację, formularz albo landing MailerLite. Formularz na stronie powinien wtedy pokazać neutralny komunikat sukcesu.
- Odpowiedzi: **201** (utworzono), **200** (już istniał, zaktualizowano), **422** (walidacja).
- Alternatywa: `POST /api/subscribers/{subscriber_id}/groups/{group_id}`. Wymaga istniejącego ID subskrybenta, więc to dwa wywołania zamiast jednego. [docs/groups](https://developers.mailerlite.com/docs/groups.html)
- Lista grup do ustawień wtyczki: `GET /api/groups` (filtr `filter[name]`, paginacja `page`/`limit`).

### Pola niestandardowe

Źródło: [docs/fields](https://developers.mailerlite.com/docs/fields.html)

- `fields` to obiekt, którego klucze muszą odpowiadać istniejącym polom (domyślnym lub niestandardowym). Obecna wtyczka używa `name`.
- Lista pól: `GET /api/fields`. Tworzenie: `POST /api/fields` z `name` i `type`. Typy: `text`, `number`, `date`. Nazwa ma maks. 255 znaków. Z przykładu w dokumentacji wynika, że klucz powstaje z nazwy („new field” → `new_field`); dokumentacja nie opisuje tej reguły wprost.
- Pola warto zakładać ręcznie w panelu MailerLite, a we wtyczce tylko mapować klucze.

### Double opt-in

Źródło: [How to use double opt-in](https://www.mailerlite.com/help/how-to-use-double-opt-in-when-collecting-subscribers)

- Double opt-in włącza się w panelu: Account settings → Subscribe settings → **„Double opt-in for API and integrations” = ON**. API nie ma flagi `double_optin` per request.
- Wszyscy subskrybenci dodani przez API dostają ten sam e-mail potwierdzający. Edycja jego treści wymaga płatnego planu.
- Zachowanie dla istniejących subskrybentów (kolumna API): `active` → brak maila DOI, `unconfirmed` → mail DOI, `unsubscribed` → brak, `deleted` → brak. Żeby wysłać DOI do istniejącego `active`/`unsubscribed`, trzeba podać `"status": "unconfirmed"`.
- Subskrybenci `unconfirmed` nie uruchamiają automatyzacji, dopóki nie potwierdzą zapisu. [What are unconfirmed subscribers](https://www.mailerlite.com/help/what-are-unconfirmed-subscribers)
- **Nie zweryfikowano:** jaki status dostaje nowy subskrybent bez pola `status`, gdy DOI dla API jest włączone (prawdopodobnie `unconfirmed`). Do sprawdzenia na koncie testowym.

### Limity

Źródło: [developers.mailerlite.com/docs](https://developers.mailerlite.com/docs/)

- **120 żądań/min** globalnie na konto. Po przekroczeniu API zwraca **429** z nagłówkami `X-RateLimit-Limit`, `X-RateLimit-Remaining` i `Retry-After`. Endpointy importu mają osobny limit 5/min.
- Przy formularzu na stronie limitu raczej nie osiągniemy. Problemem mogą być boty, więc warto dodać honeypot lub rate-limit po stronie WP (transient per IP).

### Obsługa błędów

| Kod | Znaczenie | Komunikat w formularzu |
|---|---|---|
| 200/201 | OK | sukces lub przekierowanie na stronę podziękowania |
| 401 | zły lub nieważny klucz | ogólny błąd; zalogować po stronie serwera, nie pokazywać użytkownikowi |
| 404 | brak zasobu (np. zła grupa w ścieżce) | ogólny błąd + log |
| 422 | `{"message": "...", "errors": {"email": ["..."]}}` | pokazać błąd pola, głównie zły e-mail |
| 429 | rate limit | „spróbuj za chwilę”; opcjonalnie jedna ponowna próba po `Retry-After` |
| 5xx | błąd po stronie MailerLite | ogólny błąd + log |

Do przeglądarki nie wysyłamy surowej odpowiedzi API ani obiektu wyjątku, w przeciwieństwie do dzisiejszego `add_to_mailer_lite()`.

## 4. RODO / zgody

- API nie ma dedykowanego pola „consent”. Dostępne są: `opted_in_at`, `optin_ip`, `ip_address`, `subscribed_at` ([docs/subscribers](https://developers.mailerlite.com/docs/subscribers.html)). W połączeniu z double opt-in, gdzie MailerLite sam zapisuje potwierdzenie, daje to rozliczalny dowód zgody.
- Rekomendacja dla nowej wtyczki:
  1. Checkbox zgody (niezaznaczony domyślnie) z treścią klauzuli i linkiem do polityki prywatności. Bez zaznaczenia serwer odrzuca żądanie.
  2. Wysyłać `ip_address` / `optin_ip` (IP z `REMOTE_ADDR`) i `opted_in_at` (UTC, format `Y-m-d H:i:s`).
  3. Opcjonalnie pole niestandardowe typu `text`, np. `consent_version` z wersją lub hashem klauzuli, żeby było wiadomo, na jaką treść zgody ktoś się zapisał.
  4. Włączyć „Double opt-in for API and integrations”. To decyzja biznesowa: DOI zmniejsza konwersję, ale wzmacnia dowód zgody.
- Wypis obsługuje MailerLite (link w każdym mailu), więc wtyczka nie musi tego robić.

## 5. Implikacje dla nowej wtyczki (FSE, bez jQuery, bez ACF)

- Klient HTTP: `wp_remote_post()` z WP zamiast SDK. Pozbywamy się `vendor/` (symfony/http-client, nyholm/psr7) i Composera; potrzebne są tylko 1–2 endpointy.
- Ustawienia (klucz API, ID grupy, URL podziękowania, `X-Version`) przez Settings API / `register_setting` zamiast ACF options. Klucz API można też trzymać w stałej w `wp-config.php`.
- Endpoint: REST route (`register_rest_route`) z nonce i `fetch()` po stronie JS zamiast `admin-ajax` + jQuery.
- Shortcode `sardynki_getresponse_form` jako alias renderujący ten sam formularz. Atrybuty `campaign_id` i `campaign_token` ignorować albo mapować na ID grupy MailerLite.
- Cały kod GetResponse (biblioteka, `add_to_get_response()`, opcje `getresponse_*`, `default_newsletter`) **do usunięcia**. Przedtem trzeba potwierdzić, że produkcyjnie `default_newsletter = mailerlite`.

## Otwarte kwestie

1. Wartość produkcyjna `options_default_newsletter`: potwierdzić w bazie lub panelu.
2. Czy na koncie jest włączone „Double opt-in for API and integrations”: sprawdzić w panelu MailerLite.
3. Domyślny status nowego subskrybenta przy włączonym DOI dla API bez parametru `status`: przetestować na koncie testowym.
4. Unieważnienie zahardkodowanego klucza GetResponse.

## Źródła

- https://developers.mailerlite.com/
- https://developers.mailerlite.com/docs/
- https://developers.mailerlite.com/docs/subscribers.html
- https://developers.mailerlite.com/docs/groups.html
- https://developers.mailerlite.com/docs/fields.html
- https://www.mailerlite.com/help/how-to-use-double-opt-in-when-collecting-subscribers
- https://www.mailerlite.com/help/what-are-unconfirmed-subscribers
- https://www.mailerlite.com/help/new-mailerlite-faq
- https://www.mailerlite.com/help/discontinuation-of-free-plans-in-mailerlite-classic
- Kod: `wp-content/plugins/ihumbak-newsletter-form/` (pliki wymienione w sekcji 1)
