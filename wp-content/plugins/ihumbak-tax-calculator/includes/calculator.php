<?php
/**
 * Kalkulator VAT: the [ihumbak_tax_calculator] shortcode, rendered by render_calculator().
 *
 * PHP only renders the form; it never calculates. assets/view.js validates the amount
 * and fills in the results with assets/calc.js. Without JavaScript a <noscript> message
 * explains that the calculator needs it, and the form cannot be sent: it has no action
 * and its only submit button is rendered disabled (view.js enables it on init).
 *
 * render_calculator() is the single render path, a seam for a future ihumbak/tax-calculator block.
 */

namespace Ihumbak\TaxCalculator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SHORTCODE = 'ihumbak_tax_calculator';
const RATES     = array( 23, 8, 5 );

add_shortcode( SHORTCODE, __NAMESPACE__ . '\shortcode' );

/** [ihumbak_tax_calculator]: attributes are accepted and ignored (the standard filter still runs). */
function shortcode( $atts = array(), $content = '' ): string {
	shortcode_atts( array(), $atts, SHORTCODE );

	return render_calculator();
}

/** Rate label, e.g. "23 %" (no-break space before the sign). */
function rate_label( int $rate ): string {
	/* translators: %d: VAT rate in percent. */
	return sprintf( __( '%d %%', 'ihumbak-tax-calculator' ), $rate );
}

/**
 * Every user-facing text view.js needs: validation messages by parseAmount() error code,
 * the status sentence and the rate labels.
 */
function messages(): array {
	$rates = array();
	foreach ( RATES as $rate ) {
		$rates[ (string) $rate ] = rate_label( $rate );
	}

	return array(
		'empty'     => __( 'Wpisz kwotę.', 'ihumbak-tax-calculator' ),
		'format'    => __( 'Wpisz kwotę w formacie 1234,56.', 'ihumbak-tax-calculator' ),
		'decimals'  => __( 'Kwota może mieć najwyżej dwa miejsca po przecinku.', 'ihumbak-tax-calculator' ),
		'zero'      => __( 'Kwota musi być większa od zera.', 'ihumbak-tax-calculator' ),
		'too_large' => __( 'Kwota może wynosić najwyżej 999 999 999 999,99 zł.', 'ihumbak-tax-calculator' ),
		/* translators: 1: netto amount, 2: VAT rate, 3: VAT amount, 4: brutto amount. */
		'status'    => __( 'Netto %1$s, VAT %2$s %3$s, brutto %4$s.', 'ihumbak-tax-calculator' ),
		'rates'     => $rates,
	);
}

/**
 * The calculator markup. Every id and radio group name carries the instance number,
 * so several calculators on one page work independently.
 *
 * @param array $args Unused; reserved for the future block wrapper.
 */
function render_calculator( array $args = array() ): string {
	static $instance = 0;
	++$instance;

	enqueue_assets();

	$id    = 'ihumbak-tax-calculator-' . $instance;
	$empty = __( '—', 'ihumbak-tax-calculator' );

	$modes = array(
		'brutto' => __( 'Brutto', 'ihumbak-tax-calculator' ),
		'netto'  => __( 'Netto', 'ihumbak-tax-calculator' ),
	);

	ob_start();
	?>
	<div class="ihumbak-tax-calculator" id="<?php echo esc_attr( $id ); ?>" data-ihumbak-tax-calculator data-toc-skip data-messages="<?php echo esc_attr( wp_json_encode( messages(), JSON_UNESCAPED_UNICODE ) ); ?>">
		<div class="ihumbak-tax-calculator__intro">
			<p><?php esc_html_e( 'Kalkulator pozwala obliczyć kwotę VAT oraz cenę netto i brutto.', 'ihumbak-tax-calculator' ); ?></p>
			<ol class="ihumbak-tax-calculator__steps">
				<li><?php esc_html_e( 'Wpisz kwotę.', 'ihumbak-tax-calculator' ); ?></li>
				<li><?php esc_html_e( 'Zaznacz, czy to kwota brutto, czy netto, i wybierz stawkę VAT.', 'ihumbak-tax-calculator' ); ?></li>
				<li><?php esc_html_e( 'Kliknij „Oblicz”.', 'ihumbak-tax-calculator' ); ?></li>
			</ol>
		</div>
		<noscript><p class="ihumbak-tax-calculator__noscript"><?php esc_html_e( 'Kalkulator VAT wymaga włączonego JavaScriptu. Włącz JavaScript w przeglądarce, aby obliczyć kwoty.', 'ihumbak-tax-calculator' ); ?></p></noscript>
		<form class="ihumbak-tax-calculator__form" novalidate>
			<div class="ihumbak-tax-calculator__field">
				<label class="ihumbak-tax-calculator__label" for="<?php echo esc_attr( $id ); ?>-amount"><?php esc_html_e( 'Kwota', 'ihumbak-tax-calculator' ); ?></label>
				<div class="ihumbak-tax-calculator__control">
					<input class="ihumbak-tax-calculator__input" id="<?php echo esc_attr( $id ); ?>-amount" type="text" inputmode="decimal" autocomplete="off" required aria-describedby="<?php echo esc_attr( $id ); ?>-hint">
					<span class="ihumbak-tax-calculator__suffix" aria-hidden="true"><?php esc_html_e( 'zł', 'ihumbak-tax-calculator' ); ?></span>
				</div>
				<p class="ihumbak-tax-calculator__hint" id="<?php echo esc_attr( $id ); ?>-hint"><?php esc_html_e( 'Np. 1234,56', 'ihumbak-tax-calculator' ); ?></p>
				<p class="ihumbak-tax-calculator__error" id="<?php echo esc_attr( $id ); ?>-error" hidden></p>
			</div>
			<fieldset class="ihumbak-tax-calculator__field">
				<legend class="ihumbak-tax-calculator__label"><?php esc_html_e( 'Kwota to', 'ihumbak-tax-calculator' ); ?></legend>
				<div class="ihumbak-tax-calculator__choices">
					<?php foreach ( $modes as $value => $label ) : ?>
						<label class="ihumbak-tax-calculator__choice"><input type="radio" name="<?php echo esc_attr( $id ); ?>-mode" value="<?php echo esc_attr( $value ); ?>"<?php checked( 'brutto', $value ); ?>> <?php echo esc_html( $label ); ?></label>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<fieldset class="ihumbak-tax-calculator__field">
				<legend class="ihumbak-tax-calculator__label"><?php esc_html_e( 'Stawka VAT', 'ihumbak-tax-calculator' ); ?></legend>
				<div class="ihumbak-tax-calculator__choices">
					<?php foreach ( RATES as $rate ) : ?>
						<label class="ihumbak-tax-calculator__choice"><input type="radio" name="<?php echo esc_attr( $id ); ?>-rate" value="<?php echo esc_attr( (string) $rate ); ?>"<?php checked( 23, $rate ); ?>> <?php echo esc_html( rate_label( $rate ) ); ?></label>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<button class="ihumbak-tax-calculator__submit" type="submit" disabled><?php esc_html_e( 'Oblicz', 'ihumbak-tax-calculator' ); ?></button>
		</form>
		<section class="ihumbak-tax-calculator__results" aria-labelledby="<?php echo esc_attr( $id ); ?>-results-title">
			<h2 class="ihumbak-tax-calculator__title" id="<?php echo esc_attr( $id ); ?>-results-title"><?php esc_html_e( 'Wynik', 'ihumbak-tax-calculator' ); ?></h2>
			<?php /* aria-live="off": <output> is an implicit live region; the one sentence in __status is what gets announced. */ ?>
			<dl class="ihumbak-tax-calculator__list">
				<div class="ihumbak-tax-calculator__row">
					<dt class="ihumbak-tax-calculator__term"><?php esc_html_e( 'Netto', 'ihumbak-tax-calculator' ); ?></dt>
					<dd class="ihumbak-tax-calculator__value"><output aria-live="off" data-result="netto"><?php echo esc_html( $empty ); ?></output></dd>
				</div>
				<div class="ihumbak-tax-calculator__row">
					<dt class="ihumbak-tax-calculator__term"><?php esc_html_e( 'VAT', 'ihumbak-tax-calculator' ); ?> <span class="ihumbak-tax-calculator__rate">(<span data-result="rate"><?php echo esc_html( rate_label( 23 ) ); ?></span>)</span></dt>
					<dd class="ihumbak-tax-calculator__value"><output aria-live="off" data-result="vat"><?php echo esc_html( $empty ); ?></output></dd>
				</div>
				<div class="ihumbak-tax-calculator__row ihumbak-tax-calculator__row--total">
					<dt class="ihumbak-tax-calculator__term"><?php esc_html_e( 'Brutto', 'ihumbak-tax-calculator' ); ?></dt>
					<dd class="ihumbak-tax-calculator__value"><output aria-live="off" data-result="brutto"><?php echo esc_html( $empty ); ?></output></dd>
				</div>
			</dl>
			<p class="ihumbak-tax-calculator__status" role="status"></p>
		</section>
	</div>
	<?php
	return (string) ob_get_clean();
}
