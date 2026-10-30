<?php
/**
 * Ustawienia → Newsletter (Settings API) and the getters the form and the client read.
 *
 * The API key may come from the SB_MAILERLITE_API_KEY constant in wp-config.php;
 * the constant wins over the option and the field is then read-only.
 */

namespace Ihumbak\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OPTION_API_KEY  = 'ihumbak_newsletter_api_key';
const OPTION_GROUP    = 'ihumbak_newsletter_group';
const OPTION_THANKYOU = 'ihumbak_newsletter_thankyou_page';
const OPTION_CLAUSE   = 'ihumbak_newsletter_clause';

const SETTINGS_PAGE  = 'ihumbak-newsletter';
const SETTINGS_GROUP = 'ihumbak_newsletter';

/** Placeholder in the clause replaced by the privacy policy URL (Ustawienia → Prywatność). */
const PRIVACY_PLACEHOLDER = '{polityka_prywatnosci}';

function default_clause(): string {
	return 'Zapisując się, zgadzasz się na otrzymywanie newslettera. Twoje imię i adres e-mail przetwarzamy tylko w tym celu, a wypisać możesz się w każdej chwili linkiem w każdej wiadomości. Szczegóły w <a href="' . PRIVACY_PLACEHOLDER . '">polityce prywatności</a>.';
}

function api_key(): string {
	if ( defined( 'SB_MAILERLITE_API_KEY' ) && '' !== (string) SB_MAILERLITE_API_KEY ) {
		return (string) SB_MAILERLITE_API_KEY;
	}
	return (string) get_option( OPTION_API_KEY, '' );
}

function default_group(): string {
	return (string) get_option( OPTION_GROUP, '' );
}

/** Strona podziękowania from the settings; home page when none is set. */
function thankyou_url(): string {
	$page = (int) get_option( OPTION_THANKYOU, 0 );
	$url  = $page && 'publish' === get_post_status( $page ) ? get_permalink( $page ) : '';
	return $url ? $url : home_url( '/' );
}

/** Clause printed under the submit button (limited HTML, privacy link resolved). */
function clause_html(): string {
	$clause = (string) get_option( OPTION_CLAUSE, default_clause() );
	if ( '' === trim( $clause ) ) {
		return '';
	}
	$privacy = get_privacy_policy_url();
	$clause  = str_replace( PRIVACY_PLACEHOLDER, esc_url( $privacy ? $privacy : home_url( '/polityka-prywatnosci/' ) ), $clause );
	return wp_kses( $clause, allowed_clause_html() );
}

function allowed_clause_html(): array {
	return array(
		'a'      => array(
			'href'   => true,
			'target' => true,
			'rel'    => true,
		),
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
	);
}

add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );
add_action( 'admin_menu', __NAMESPACE__ . '\add_settings_page' );

function register_settings(): void {
	register_setting(
		SETTINGS_GROUP,
		OPTION_API_KEY,
		array(
			'type'              => 'string',
			'sanitize_callback' => static fn( $v ) => trim( sanitize_text_field( (string) $v ) ),
			'default'           => '',
		)
	);
	register_setting(
		SETTINGS_GROUP,
		OPTION_GROUP,
		array(
			'type'              => 'string',
			'sanitize_callback' => __NAMESPACE__ . '\sanitize_group',
			'default'           => '',
		)
	);
	register_setting(
		SETTINGS_GROUP,
		OPTION_THANKYOU,
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		)
	);
	register_setting(
		SETTINGS_GROUP,
		OPTION_CLAUSE,
		array(
			'type'              => 'string',
			'sanitize_callback' => static fn( $v ) => trim( wp_kses( (string) $v, allowed_clause_html() ) ),
			'default'           => default_clause(),
		)
	);

	add_settings_section( 'ihumbak_newsletter_main', '', '__return_null', SETTINGS_PAGE );

	add_settings_field( OPTION_API_KEY, esc_html__( 'Klucz API MailerLite', 'ihumbak-newsletter' ), __NAMESPACE__ . '\field_api_key', SETTINGS_PAGE, 'ihumbak_newsletter_main', array( 'label_for' => OPTION_API_KEY ) );
	add_settings_field( OPTION_GROUP, esc_html__( 'Domyślna grupa (ID)', 'ihumbak-newsletter' ), __NAMESPACE__ . '\field_group', SETTINGS_PAGE, 'ihumbak_newsletter_main', array( 'label_for' => OPTION_GROUP ) );
	add_settings_field( OPTION_THANKYOU, esc_html__( 'Strona podziękowania', 'ihumbak-newsletter' ), __NAMESPACE__ . '\field_thankyou', SETTINGS_PAGE, 'ihumbak_newsletter_main', array( 'label_for' => OPTION_THANKYOU ) );
	add_settings_field( OPTION_CLAUSE, esc_html__( 'Klauzula informacyjna', 'ihumbak-newsletter' ), __NAMESPACE__ . '\field_clause', SETTINGS_PAGE, 'ihumbak_newsletter_main', array( 'label_for' => OPTION_CLAUSE ) );
}

/** MailerLite group IDs are numeric strings; anything else is dropped. */
function sanitize_group( $value ): string {
	$value = trim( (string) $value );
	return preg_match( '/^\d{1,30}$/', $value ) ? $value : '';
}

function add_settings_page(): void {
	add_options_page(
		__( 'Newsletter', 'ihumbak-newsletter' ),
		__( 'Newsletter', 'ihumbak-newsletter' ),
		'manage_options',
		SETTINGS_PAGE,
		__NAMESPACE__ . '\render_settings_page'
	);
}

function render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( SETTINGS_GROUP );
			do_settings_sections( SETTINGS_PAGE );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

function field_api_key(): void {
	if ( defined( 'SB_MAILERLITE_API_KEY' ) && '' !== (string) SB_MAILERLITE_API_KEY ) {
		echo '<p>' . esc_html__( 'Klucz jest ustawiony stałą SB_MAILERLITE_API_KEY w wp-config.php i ma pierwszeństwo przed tym polem.', 'ihumbak-newsletter' ) . '</p>';
		return;
	}
	printf(
		'<input type="password" id="%1$s" name="%1$s" value="%2$s" class="regular-text" autocomplete="off" spellcheck="false" />',
		esc_attr( OPTION_API_KEY ),
		esc_attr( (string) get_option( OPTION_API_KEY, '' ) )
	);
	echo '<p class="description">' . esc_html__( 'Klucz z MailerLite (Integrations → API). Bezpieczniej trzymać go w stałej SB_MAILERLITE_API_KEY w wp-config.php.', 'ihumbak-newsletter' ) . '</p>';
}

function field_group(): void {
	printf(
		'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" inputmode="numeric" pattern="\d+" />',
		esc_attr( OPTION_GROUP ),
		esc_attr( default_group() )
	);
	echo '<p class="description">' . esc_html__( 'ID grupy MailerLite, do której trafiają zapisy bez własnej grupy (blok: pole „ID grupy”, shortcode: campaign_id).', 'ihumbak-newsletter' ) . '</p>';
}

function field_thankyou(): void {
	wp_dropdown_pages(
		array(
			'name'              => OPTION_THANKYOU, // phpcs:ignore WordPress.Security.EscapeOutput -- constant.
			'id'                => OPTION_THANKYOU, // phpcs:ignore WordPress.Security.EscapeOutput -- constant.
			'selected'          => (int) get_option( OPTION_THANKYOU, 0 ),
			'show_option_none'  => esc_html__( '— strona główna —', 'ihumbak-newsletter' ),
			'option_none_value' => '0',
		)
	);
	echo '<p class="description">' . esc_html__( 'Strona, na którą trafia czytelnik po udanym zapisie (shortcode może ją zmienić atrybutem thankyou_url).', 'ihumbak-newsletter' ) . '</p>';
}

function field_clause(): void {
	printf(
		'<textarea id="%1$s" name="%1$s" rows="4" class="large-text">%2$s</textarea>',
		esc_attr( OPTION_CLAUSE ),
		esc_textarea( (string) get_option( OPTION_CLAUSE, default_clause() ) )
	);
	printf(
		'<p class="description">%s</p>',
		esc_html(
			sprintf(
				/* translators: %s: placeholder */
				__( 'Wyświetlana pod przyciskiem zapisu. Dozwolone: a, strong, em, br. %s zostaje zamienione na adres polityki prywatności (Ustawienia → Prywatność).', 'ihumbak-newsletter' ),
				PRIVACY_PLACEHOLDER
			)
		)
	);
}
