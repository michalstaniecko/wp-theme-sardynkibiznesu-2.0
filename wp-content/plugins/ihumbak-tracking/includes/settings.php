<?php
/**
 * Settings page (Settings API): Settings → Tracking.
 *
 * @package ihumbak-tracking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const IHUMBAK_TRACKING_SETTINGS_GROUP = 'ihumbak_tracking';
const IHUMBAK_TRACKING_SETTINGS_PAGE  = 'ihumbak-tracking';

add_action( 'admin_init', 'ihumbak_tracking_register_settings' );
add_action( 'admin_menu', 'ihumbak_tracking_add_settings_page' );

function ihumbak_tracking_register_settings(): void {
	register_setting(
		IHUMBAK_TRACKING_SETTINGS_GROUP,
		IHUMBAK_TRACKING_OPTION_GTM_ID,
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'ihumbak_tracking_sanitize_gtm_id',
		)
	);

	register_setting(
		IHUMBAK_TRACKING_SETTINGS_GROUP,
		IHUMBAK_TRACKING_OPTION_HEAD_TAGS,
		array(
			'type'              => 'string',
			'default'           => '',
			'sanitize_callback' => 'ihumbak_tracking_sanitize_head_tags',
		)
	);

	add_settings_section( 'ihumbak_tracking_main', '', '__return_false', IHUMBAK_TRACKING_SETTINGS_PAGE );

	add_settings_field(
		IHUMBAK_TRACKING_OPTION_GTM_ID,
		esc_html__( 'ID kontenera GTM', 'ihumbak-tracking' ),
		'ihumbak_tracking_render_gtm_id_field',
		IHUMBAK_TRACKING_SETTINGS_PAGE,
		'ihumbak_tracking_main',
		array( 'label_for' => IHUMBAK_TRACKING_OPTION_GTM_ID )
	);

	add_settings_field(
		IHUMBAK_TRACKING_OPTION_HEAD_TAGS,
		esc_html__( 'Tagi w <head>', 'ihumbak-tracking' ),
		'ihumbak_tracking_render_head_tags_field',
		IHUMBAK_TRACKING_SETTINGS_PAGE,
		'ihumbak_tracking_main',
		array( 'label_for' => IHUMBAK_TRACKING_OPTION_HEAD_TAGS )
	);
}

/**
 * Accepts "GTM-XXXXXXX" (case-insensitive, surrounding whitespace ignored) or empty.
 * Anything else is rejected and the previous value is kept.
 */
function ihumbak_tracking_sanitize_gtm_id( $value ): string {
	$value = strtoupper( trim( (string) $value ) );

	if ( '' === $value || preg_match( '/^GTM-[A-Z0-9]+$/', $value ) ) {
		return $value;
	}

	add_settings_error(
		IHUMBAK_TRACKING_OPTION_GTM_ID,
		'invalid_gtm_id',
		__( 'Nieprawidłowe ID kontenera GTM (oczekiwany format: GTM-XXXXXXX). Zachowano poprzednią wartość.', 'ihumbak-tracking' )
	);

	return (string) get_option( IHUMBAK_TRACKING_OPTION_GTM_ID, '' );
}

/**
 * Stored unchanged: the field exists to paste raw verification tags.
 * Only users allowed to post unfiltered HTML may change it.
 */
function ihumbak_tracking_sanitize_head_tags( $value ): string {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return (string) get_option( IHUMBAK_TRACKING_OPTION_HEAD_TAGS, '' );
	}

	return (string) $value;
}

function ihumbak_tracking_add_settings_page(): void {
	add_options_page(
		__( 'Tracking', 'ihumbak-tracking' ),
		__( 'Tracking', 'ihumbak-tracking' ),
		'manage_options',
		IHUMBAK_TRACKING_SETTINGS_PAGE,
		'ihumbak_tracking_render_settings_page'
	);
}

function ihumbak_tracking_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php esc_html_e( 'Wypisywane tylko na produkcji (wp_get_environment_type() = production) i tylko dla niezalogowanych. Zgody i wszystkie trackery konfiguruje się w GTM.', 'ihumbak-tracking' ); ?></p>
		<form action="options.php" method="post">
			<?php
			settings_fields( IHUMBAK_TRACKING_SETTINGS_GROUP );
			do_settings_sections( IHUMBAK_TRACKING_SETTINGS_PAGE );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

function ihumbak_tracking_render_gtm_id_field(): void {
	printf(
		'<input type="text" class="regular-text code" id="%1$s" name="%1$s" value="%2$s" placeholder="GTM-XXXXXXX" />',
		esc_attr( IHUMBAK_TRACKING_OPTION_GTM_ID ),
		esc_attr( (string) get_option( IHUMBAK_TRACKING_OPTION_GTM_ID, '' ) )
	);
}

function ihumbak_tracking_render_head_tags_field(): void {
	printf(
		'<textarea name="%1$s" id="%1$s" class="large-text code" rows="8">%2$s</textarea><p class="description">%3$s</p>',
		esc_attr( IHUMBAK_TRACKING_OPTION_HEAD_TAGS ),
		esc_textarea( (string) get_option( IHUMBAK_TRACKING_OPTION_HEAD_TAGS, '' ) ),
		esc_html__( 'Wypisywane bez zmian w <head> na każdej stronie, np. metatagi weryfikacji domeny.', 'ihumbak-tracking' )
	);
}
