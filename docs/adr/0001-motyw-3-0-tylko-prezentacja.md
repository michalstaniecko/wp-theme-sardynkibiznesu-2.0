# Motyw 3.0 zawiera wyłącznie prezentację, funkcjonalność żyje we wtyczkach

Motyw `sardynkibiznesu-3.0` (FSE) zawiera tylko `theme.json`, szablony, części szablonów, wzorce i style. Wszystko, od czego zależy treść lub zachowanie serwisu — bloki niestandardowe, shortcode'y, CPT (porównywarka), schema SEO, tracking — trafia do wtyczek w tym samym repozytorium.

Powód: w 2.0 shortcode'y (`cmsmasters_*`, `sb_button`), bloki ACF i CPT porównywarki są zarejestrowane w motywie, więc treść tysięcy artykułów jest przywiązana do konkretnego motywu — każda zmiana motywu grozi zepsuciem treści. Rozdzielenie pozwala przełączyć lub przebudować motyw bez dotykania treści.

## Considered Options

- **Funkcjonalność w motywie (jak w 2.0)** — prostsze na start, ale powtarza problem, który ten redesign musi rozwiązać.
- **Prezentacja w motywie, funkcjonalność we wtyczkach** — wybrane.

## Consequences

- Wtyczki stylują swój markup tokenami motywu (zmienne CSS z `theme.json`) z wartościami awaryjnymi, a nie klasami motywu.
- Motyw 3.0 nie rejestruje shortcode'ów, bloków ani CPT.
