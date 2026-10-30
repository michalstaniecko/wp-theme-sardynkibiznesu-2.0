<?php
/**
 * Komparator SEO 2.0 ↔ 3.0 i porównanie treści.
 *
 * Porównuje wyrenderowane strony dwóch adresów bazowych (A = 2.0, B = 3.0)
 * i kończy się kodem 1, gdy znajdzie różnicę blokującą przełączenie.
 * Instrukcja: docs/komparator-seo.md
 */

declare(strict_types=1);

const EXIT_OK = 0;
const EXIT_BLOCKING = 1;
const EXIT_USAGE = 2;

/** Content area of a post or page: `.entry-content` in 2.0, `core/post-content` in 3.0. */
const CONTENT_SELECTOR = '.entry-content, .wp-block-post-content';

main($argv);

function main(array $argv): void
{
    if (PHP_VERSION_ID < 80400 || !class_exists(\Dom\HTMLDocument::class) || !function_exists('curl_init')) {
        fwrite(STDERR, "Wymagane PHP 8.4+ z rozszerzeniami dom i curl.\n");
        exit(EXIT_USAGE);
    }
    $opt = parse_options($argv);
    $run = new Run($opt);
    $run->execute();
    $report = render_report($run);
    file_put_contents($opt['report'], $report);
    $blocking = count($run->blocking);
    echo ($blocking ? "Wynik: BLOKUJE ($blocking różnic blokujących)" : 'Wynik: OK') . "\n";
    echo 'Do przeglądu: ' . count($run->review) . "\n";
    echo "Raport: {$opt['report']}\n";
    exit($blocking ? EXIT_BLOCKING : EXIT_OK);
}

function usage(): string
{
    return <<<TXT
Użycie: php tools/komparator-seo/komparator.php --a=URL_2.0 --b=URL_3.0 [opcje]

  --a=URL            adres bazowy 2.0 (wzorzec), np. https://sardynkibiznesu.pl
  --b=URL            adres bazowy 3.0 (sprawdzany), np. https://staging.sardynkibiznesu.pl
  --auth-a=U:P       Basic Auth dla A
  --auth-b=U:P       Basic Auth dla B
  --old-slugs=PLIK   plik ze starymi slugami (jedna ścieżka na linię, # = komentarz)
  --search=FRAZA     fraza do sprawdzenia wyszukiwarki ?s= (domyślnie: biznes)
  --max-pages=N      najwyższy numer /page/N/ do sprawdzenia (domyślnie: do pierwszego nie-200 na A, max 50)
  --limit=N          sprawdź tylko N pierwszych URL-i z sitemap (do prób)
  --delay=MS         przerwa między żądaniami w ms (domyślnie 300)
  --report=PLIK      ścieżka raportu Markdown (domyślnie komparator-seo-RRRRMMDD-GGMM.md)

Kod wyjścia: 0 = brak różnic blokujących, 1 = są różnice blokujące, 2 = błąd użycia.

TXT;
}

function parse_options(array $argv): array
{
    $opt = [
        'a' => null, 'b' => null, 'auth-a' => null, 'auth-b' => null, 'old-slugs' => null,
        'search' => 'biznes', 'max-pages' => null, 'limit' => null, 'delay' => '300',
        'report' => 'komparator-seo-' . date('Ymd-Hi') . '.md',
    ];
    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            echo usage();
            exit(EXIT_OK);
        }
        if (!preg_match('/^--([a-z-]+)=(.*)$/s', $arg, $m) || !array_key_exists($m[1], $opt)) {
            fwrite(STDERR, "Nieznana opcja: $arg\n\n" . usage());
            exit(EXIT_USAGE);
        }
        $opt[$m[1]] = $m[2];
    }
    if (!$opt['a'] || !$opt['b']) {
        fwrite(STDERR, "Podaj --a i --b.\n\n" . usage());
        exit(EXIT_USAGE);
    }
    $opt['a'] = rtrim($opt['a'], '/');
    $opt['b'] = rtrim($opt['b'], '/');
    $opt['delay'] = max(0, (int) $opt['delay']);
    return $opt;
}

/* ------------------------------------------------------------------ HTTP */

final class Http
{
    private float $last = 0.0;

    public function __construct(private int $delayMs, private ?string $auth)
    {
    }

    /** One request without following redirects. */
    public function get(string $url): array
    {
        $wait = $this->delayMs / 1000 - (microtime(true) - $this->last);
        if ($wait > 0) {
            usleep((int) ($wait * 1e6));
        }
        $headers = [];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_USERAGENT => 'SB-Komparator-SEO/1.0',
            CURLOPT_ENCODING => '',
            CURLOPT_HEADERFUNCTION => function ($ch, $line) use (&$headers) {
                if (str_contains($line, ':')) {
                    [$k, $v] = explode(':', $line, 2);
                    $headers[strtolower(trim($k))] = trim($v);
                }
                return strlen($line);
            },
        ]);
        if ($this->auth) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->auth);
        }
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = $body === false ? curl_error($ch) : null;
        $this->last = microtime(true);
        return ['status' => $error ? 0 : $status, 'headers' => $headers, 'body' => $body === false ? '' : $body, 'error' => $error];
    }

    /** Follows redirects manually and records the chain. */
    public function fetch(string $url): array
    {
        $chain = [];
        for ($i = 0; $i < 10; $i++) {
            $r = $this->get($url);
            $chain[] = ['url' => $url, 'status' => $r['status']];
            $loc = $r['headers']['location'] ?? null;
            if ($r['status'] >= 300 && $r['status'] < 400 && $loc) {
                $url = resolve_url($url, $loc);
                continue;
            }
            return $r + ['chain' => $chain, 'final' => $url];
        }
        return $r + ['chain' => $chain, 'final' => $url, 'error' => 'za dużo przekierowań'];
    }
}

function resolve_url(string $base, string $rel): string
{
    if (preg_match('#^https?://#i', $rel)) {
        return $rel;
    }
    $p = parse_url($base);
    $origin = $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
    if (str_starts_with($rel, '//')) {
        return $p['scheme'] . ':' . $rel;
    }
    if (str_starts_with($rel, '/')) {
        return $origin . $rel;
    }
    $dir = preg_replace('#/[^/]*$#', '/', $p['path'] ?? '/');
    return $origin . $dir . $rel;
}

/** Path + query of a URL, used as the key shared by A and B. */
function url_key(string $url): string
{
    $p = parse_url($url);
    return ($p['path'] ?? '/') . (isset($p['query']) ? '?' . $p['query'] : '');
}

/* --------------------------------------------------------------- the run */

final class Run
{
    public array $opt;
    public Http $httpA;
    public Http $httpB;
    /** @var array<string,string> path => kind */
    public array $urls = [];
    /** @var list<array{url:string,check:string,a:string,b:string}> */
    public array $blocking = [];
    public array $review = [];
    public array $notes = [];
    public int $contentChecked = 0;

    public function __construct(array $opt)
    {
        $this->opt = $opt;
        $this->httpA = new Http($opt['delay'], $opt['auth-a']);
        $this->httpB = new Http($opt['delay'], $opt['auth-b']);
    }

    public function execute(): void
    {
        $this->collectUrls();
        foreach ($this->urls as $path => $kind) {
            $this->compareUrl($path, $kind);
        }
    }

    private function collectUrls(): void
    {
        $paths = [];
        foreach (['a' => $this->httpA, 'b' => $this->httpB] as $side => $http) {
            $base = $this->opt[$side];
            $found = sitemap_urls($http, $base . '/sitemap_index.xml');
            if ($found === null) {
                $this->notes[] = "Nie udało się pobrać sitemap z {$base}/sitemap_index.xml.";
                continue;
            }
            foreach ($found as $u) {
                $paths[url_key($u)] ??= 'sitemap';
            }
        }
        if ($this->opt['limit'] !== null) {
            $paths = array_slice($paths, 0, (int) $this->opt['limit'], true);
        }
        $this->urls = $paths;
    }

    private function compareUrl(string $path, string $kind): void
    {
        $a = page_facts($this->httpA->fetch($this->opt['a'] . $path), $this->opt['a'], true);
        $b = page_facts($this->httpB->fetch($this->opt['b'] . $path), $this->opt['b'], false);
        $this->compareSeo($path, $a, $b);
    }

    private function block(string $url, string $check, string $a, string $b): void
    {
        $this->blocking[] = compact('url', 'check', 'a', 'b');
    }

    private function toReview(string $url, string $check, string $a, string $b): void
    {
        $this->review[] = compact('url', 'check', 'a', 'b');
    }

    private function compareSeo(string $path, array $a, array $b): void
    {
        if ($a['chain'] !== $b['chain']) {
            $this->block($path, 'status / przekierowania', $a['chain'], $b['chain']);
        }
        if (!$a['html'] || !$b['html']) {
            return;
        }
        foreach (['title', 'canonical', 'robots'] as $key) {
            if ($a[$key] !== $b[$key]) {
                $this->block($path, $key, (string) $a[$key], (string) $b[$key]);
            }
        }
        if ($a['description'] !== $b['description']) {
            $this->toReview($path, 'description', (string) $a['description'], (string) $b['description']);
        }
        if ($b['h1'] !== 1) {
            $this->block($path, 'liczba H1', (string) $a['h1'], (string) $b['h1']);
        }
        if ($a['jsonldTypes'] !== $b['jsonldTypes']) {
            $this->toReview($path, 'JSON-LD typy', implode(', ', $a['jsonldTypes']), implode(', ', $b['jsonldTypes']));
        }
        if ($b['graphNonObjects'] > 0) {
            $this->block($path, '@graph tylko obiekty', 'nie-obiekty: ' . $a['graphNonObjects'], 'nie-obiekty: ' . $b['graphNonObjects']);
        }
        if ($a['anchors'] !== null) {
            $missing = array_values(array_diff($a['anchors'], $b['anchors'] ?? []));
            $added = array_values(array_diff($b['anchors'] ?? [], $a['anchors']));
            $addedText = $added ? 'nowe w 3.0: ' . implode(', ', $added) : '';
            if ($missing) {
                $this->block($path, 'Kotwice', 'brak w 3.0: ' . implode(', ', $missing), $addedText);
            } elseif ($added) {
                $this->toReview($path, 'Kotwice', '', $addedText);
            }
        }
    }
}

/* ------------------------------------------------------------- sitemaps */

/** @return list<string>|null */
function sitemap_urls(Http $http, string $url, int $depth = 0): ?array
{
    $r = $http->fetch($url);
    if ($r['status'] !== 200 || $depth > 2) {
        return null;
    }
    $doc = new DOMDocument();
    if (!@$doc->loadXML($r['body'])) {
        return null;
    }
    $out = [];
    foreach ($doc->getElementsByTagNameNS('*', 'sitemap') as $sm) {
        foreach ($sm->getElementsByTagNameNS('*', 'loc') as $loc) {
            $out = array_merge($out, sitemap_urls($http, trim($loc->textContent), $depth + 1) ?? []);
        }
    }
    foreach ($doc->getElementsByTagNameNS('*', 'url') as $u) {
        foreach ($u->getElementsByTagNameNS('*', 'loc') as $loc) {
            $out[] = trim($loc->textContent);
        }
    }
    return $out;
}

/* ---------------------------------------------------------------- facts */

/** Replaces the site's own origin with a neutral marker so A and B values compare. */
function neutral(?string $value, string $base): ?string
{
    if ($value === null) {
        return null;
    }
    $origin = preg_replace('#^(https?://[^/]+).*$#', '$1', $base);
    return str_replace($origin, '{BASE}', $value);
}

function page_facts(array $r, string $base, bool $emulateV1Anchors): array
{
    $chain = implode(' → ', array_map(
        fn($c) => $c['status'] . ' ' . neutral($c['url'], $base),
        array_slice($r['chain'], 1)
    ));
    $f = [
        'status' => $r['status'],
        'chain' => $r['chain'][0]['status'] . ($chain !== '' ? ' → ' . $chain : '') . ($r['error'] ? ' (' . $r['error'] . ')' : ''),
        'final' => $r['final'],
        'html' => false,
    ];
    $type = $r['headers']['content-type'] ?? '';
    if ($r['body'] === '' || !str_contains($type, 'html')) {
        return $f;
    }
    $doc = \Dom\HTMLDocument::createFromString($r['body'], LIBXML_NOERROR, 'UTF-8');
    $attr = fn(string $sel, string $name) => $doc->querySelector($sel)?->getAttribute($name);
    $f['html'] = true;
    $f['title'] = trim($doc->querySelector('title')?->textContent ?? '');
    $f['description'] = $attr('meta[name="description"]', 'content');
    $f['canonical'] = neutral($attr('link[rel="canonical"]', 'href'), $base);
    $f['robots'] = $attr('meta[name="robots"]', 'content');
    $f['h1'] = $doc->querySelectorAll('h1')->length;
    [$f['jsonldTypes'], $f['graphNonObjects']] = jsonld_facts($doc);
    if ($emulateV1Anchors) {
        emulate_v1_anchors($doc);
    }
    $content = $doc->querySelector(CONTENT_SELECTOR);
    $f['anchors'] = $content ? heading_anchors($content) : null;
    return $f;
}

/**
 * In 2.0 the v1 Kotwice are set in the browser by ihumbak-anchor.js, only when
 * the page has the old Spis treści. This repeats that script on the parsed page:
 * id = text with whitespace → "-", then everything outside [a-zA-Z0-9-] removed.
 */
function emulate_v1_anchors(\Dom\HTMLDocument $doc): void
{
    if (!$doc->querySelector('.ihumbak-anchor-menu-wrapper')) {
        return;
    }
    foreach ($doc->querySelectorAll('.ihumbak-anchor') as $el) {
        $el->setAttribute('id', v1_anchor_id($el->textContent));
    }
}

function v1_anchor_id(string $text): string
{
    // JavaScript \s: ASCII whitespace plus the Unicode space separators, NBSP and BOM.
    $id = preg_replace('/[\t\n\x{0B}\f\r \x{A0}\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}\x{FEFF}]/u', '-', $text);
    return preg_replace('/[^a-zA-Z0-9\-]/u', '', $id);
}

/** @return list<string> headings with an id, as "h2#id", in document order */
function heading_anchors(\Dom\Element $root): array
{
    $out = [];
    foreach ($root->querySelectorAll('h1[id], h2[id], h3[id], h4[id], h5[id], h6[id]') as $h) {
        $out[] = strtolower($h->localName) . '#' . $h->getAttribute('id');
    }
    return $out;
}

/** @return array{0:list<string>,1:int} sorted unique @type values and count of non-object @graph items */
function jsonld_facts(\Dom\HTMLDocument $doc): array
{
    $types = [];
    $nonObjects = 0;
    foreach ($doc->querySelectorAll('script[type="application/ld+json"]') as $script) {
        $data = json_decode($script->textContent, true);
        if (!is_array($data)) {
            continue;
        }
        $items = isset($data['@graph']) && is_array($data['@graph']) ? $data['@graph'] : (array_is_list($data) ? $data : [$data]);
        foreach ($items as $item) {
            if (!is_array($item) || array_is_list($item)) {
                $nonObjects++;
                continue;
            }
            foreach ((array) ($item['@type'] ?? []) as $t) {
                $types[] = (string) $t;
            }
        }
    }
    $types = array_values(array_unique($types));
    sort($types);
    return [$types, $nonObjects];
}

/* --------------------------------------------------------------- report */

function md_cell(string $s): string
{
    $s = str_replace(["\r", "\n", '|'], [' ', ' ', '\\|'], $s);
    return $s === '' ? '—' : (mb_strlen($s) > 200 ? mb_substr($s, 0, 197) . '…' : $s);
}

function md_table(array $rows): string
{
    if (!$rows) {
        return "Brak.\n";
    }
    $out = "| URL | Sprawdzenie | 2.0 (A) | 3.0 (B) |\n|---|---|---|---|\n";
    foreach ($rows as $r) {
        $out .= '| ' . md_cell($r['url']) . ' | ' . md_cell($r['check']) . ' | ' . md_cell($r['a']) . ' | ' . md_cell($r['b']) . " |\n";
    }
    return $out;
}

function render_report(Run $run): string
{
    $o = $run->opt;
    $blocking = count($run->blocking);
    $verdict = $blocking ? "**Wynik: BLOKUJE** — $blocking różnic blokujących przełączenie." : '**Wynik: OK** — brak różnic blokujących.';
    $out = "# Komparator SEO 2.0 ↔ 3.0\n\n";
    $out .= "- A (2.0): {$o['a']}\n- B (3.0): {$o['b']}\n- Data: " . date('Y-m-d H:i') . "\n";
    $out .= '- Sprawdzone URL-e: ' . count($run->urls) . "\n- Sprawdzone treści: {$run->contentChecked}\n\n";
    $out .= $verdict . "\n\n";
    foreach ($run->notes as $n) {
        $out .= "> $n\n";
    }
    $out .= "\n## Różnice blokujące\n\n" . md_table($run->blocking);
    $out .= "\n## Do przeglądu (nie blokują)\n\n" . md_table($run->review);
    $out .= "\n## Sprawdzone URL-e\n\n";
    foreach ($run->urls as $path => $kind) {
        $out .= "- `$path` ($kind)\n";
    }
    return $out;
}
