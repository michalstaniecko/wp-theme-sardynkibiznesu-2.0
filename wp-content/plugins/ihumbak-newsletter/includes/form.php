<?php
/**
 * Formularz zapisu: the ihumbak/newsletter-form block and the legacy
 * [sardynki_getresponse_form] shortcode, both rendered by render_form().
 *
 * The shortcode is turned into a parsed ihumbak/newsletter-form block and passed
 * through render_block(), so it gets the same markup and the same per-block CSS
 * (plugin structural CSS + theme look) as the block.
 *
 * Works without JavaScript: a plain POST to admin-post.php (includes/subscribe.php).
 * With JavaScript the Interactivity store `ihumbak/newsletter` (assets/view.js) sends
 * the same form to the REST endpoint (includes/rest.php) and shows errors inline.
 * No nonce (pages are cached); the hidden settings are signed instead, see sign().
 */

namespace Ihumbak\Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const BLOCK       = 'ihumbak/newsletter-form';
const SHORTCODE   = 'sardynki_getresponse_form';
const ACTION      = 'ihumbak_newsletter_subscribe';
const LAYOUTS     = array( 'section', 'stacked', 'callout' );
const FIELDS      = array( 'email', 'name-email' );
const ERROR_PARAM = 'ihumbak_newsletter_error';
const FORM_PARAM  = 'ihumbak_newsletter_form';

/** Messages shown in the form's aria-live region, by error code. */
function messages(): array {
	return array(
		'invalid_email' => __( 'Podaj prawidłowy adres e-mail.', 'ihumbak-newsletter' ),
		'missing_name'  => __( 'Podaj imię.', 'ihumbak-newsletter' ),
		'error'         => __( 'Nie udało się zapisać. Spróbuj ponownie za chwilę.', 'ihumbak-newsletter' ),
	);
}

add_action(
	'init',
	static function (): void {
		$dir   = plugin_dir_path( IHUMBAK_NEWSLETTER_FILE );
		$asset = require $dir . 'assets/editor.asset.php';
		wp_register_script(
			'ihumbak-newsletter-editor',
			plugins_url( 'assets/editor.js', IHUMBAK_NEWSLETTER_FILE ),
			$asset['dependencies'],
			$asset['version'],
			array( 'in_footer' => true )
		);
		$view = require $dir . 'assets/view.asset.php';
		wp_register_script_module(
			'ihumbak-newsletter-view',
			plugins_url( 'assets/view.js', IHUMBAK_NEWSLETTER_FILE ),
			$view['dependencies'],
			$view['version']
		);
		register_block_type( $dir . 'blocks/newsletter-form' );
		add_shortcode( SHORTCODE, __NAMESPACE__ . '\shortcode' );
	}
);

/**
 * [sardynki_getresponse_form] → ihumbak/newsletter-form.
 * campaign_id = MailerLite group, type=widget = compact (callout) layout,
 * campaign_token is ignored. The legacy form always had name + e-mail.
 */
function shortcode( $atts, $content = '' ): string {
	$a = shortcode_atts(
		array(
			'type'              => '',
			'title'             => '',
			'name_placeholder'  => 'Imię',
			'email_placeholder' => 'Adres e-mail',
			'about_url'         => '',
			'about_label'       => '',
			'thankyou_url'      => '',
			'baner'             => '',
			'submit'            => 'Zapisz',
			'campaign_id'       => '',
			'campaign_token'    => '',
			'image'             => content_url( 'uploads/2020/11/strefa-sardynek-baner.jpg' ),
		),
		$atts,
		SHORTCODE
	);

	$widget = 'widget' === $a['type'];
	$title  = '' !== $a['title'] ? $a['title'] : ( $widget ? 'Zapisz się do newslettera!' : '' );
	$intro  = '';
	if ( '' !== $title ) {
		$intro .= '<p class="wp-block-ihumbak-newsletter-form__title">' . wp_kses_post( $title ) . '</p>';
	}
	if ( '' !== trim( (string) $content ) ) {
		$intro .= '<p class="wp-block-ihumbak-newsletter-form__text">' . wp_kses_post( trim( (string) $content ) ) . '</p>';
	}

	$attrs = array(
		'layout'           => $widget ? 'callout' : 'stacked',
		'fields'           => 'name-email',
		'groupId'          => sanitize_group( $a['campaign_id'] ),
		'submitLabel'      => (string) $a['submit'],
		'namePlaceholder'  => (string) $a['name_placeholder'],
		'emailPlaceholder' => (string) $a['email_placeholder'],
		'aboutUrl'         => (string) $a['about_url'],
		'aboutLabel'       => (string) $a['about_label'],
		'thankyouUrl'      => (string) $a['thankyou_url'],
		'image'            => $a['baner'] && 'false' !== $a['baner'] ? (string) $a['image'] : '',
		'legacy'           => true,
	);

	return render_block(
		array(
			'blockName'    => BLOCK,
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => $intro,
			'innerContent' => array( $intro ),
		)
	);
}

/**
 * Global state of the `ihumbak/newsletter` store. The field getters are closures, so the
 * server prints aria-invalid / aria-describedby after a no-JS error exactly like the
 * client getters of the same name (assets/view.js) do after a JS submit.
 */
function interactivity_state(): void {
	$field = static function ( string $code, bool $describe ): \Closure {
		return static function () use ( $code, $describe ): ?string {
			$context = wp_interactivity_get_context( 'ihumbak/newsletter' );
			if ( ( $context['error'] ?? '' ) !== $code ) {
				return null;
			}
			return $describe ? (string) ( $context['messageId'] ?? '' ) : 'true';
		};
	};

	wp_interactivity_state(
		'ihumbak/newsletter',
		array(
			'endpoint'         => rest_url_subscribe(),
			'genericError'     => messages()['error'],
			'emailInvalid'     => $field( 'invalid_email', false ),
			'emailDescribedBy' => $field( 'invalid_email', true ),
			'nameInvalid'      => $field( 'missing_name', false ),
			'nameDescribedBy'  => $field( 'missing_name', true ),
		)
	);
}

/** Default title and text when the block has no inner blocks (e.g. in a template). */
function default_intro(): string {
	return '<h2 class="wp-block-heading">' . esc_html__( 'Zapisz się na newsletter', 'ihumbak-newsletter' ) . '</h2>'
		. '<p>' . esc_html__( 'Praktyczne porady dla małych firm: marketing, prawo i pieniądze. Raz na jakiś czas, prosto na Twoją skrzynkę.', 'ihumbak-newsletter' ) . '</p>';
}

/**
 * HMAC over the hidden form settings, so a POST cannot pick another group or
 * an arbitrary redirect, and the fill-time stamp cannot be forged.
 * Deterministic, so it survives page caching (unlike a nonce).
 */
function sign( string $group, string $thankyou, string $fields, string $ts ): string {
	return hash_hmac( 'sha256', implode( '|', array( $group, $thankyou, $fields, $ts ) ), wp_salt( 'nonce' ) . ACTION );
}

/** URL of the page being rendered, without our own result parameters. */
function current_url(): string {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- validated on use.
	$uri = remove_query_arg( array( ERROR_PARAM, FORM_PARAM ), (string) $uri );
	$home = wp_parse_url( home_url() );
	return $home['scheme'] . '://' . $home['host'] . ( isset( $home['port'] ) ? ':' . $home['port'] : '' ) . $uri;
}

/**
 * The one renderer of Formularz zapisu (block render.php and the shortcode).
 *
 * @param array  $attributes Block attributes (block.json).
 * @param string $intro      Inner blocks HTML (title and text); default copy when empty.
 */
function render_form( array $attributes, string $intro ): string {
	static $count = 0;
	++$count;

	$layout = in_array( $attributes['layout'] ?? '', LAYOUTS, true ) ? $attributes['layout'] : 'section';
	$fields = in_array( $attributes['fields'] ?? '', FIELDS, true ) ? $attributes['fields'] : 'name-email';
	$group  = sanitize_group( $attributes['groupId'] ?? '' );
	$thanks = esc_url_raw( (string) ( $attributes['thankyouUrl'] ?? '' ) );
	$id     = 'ihumbak-newsletter-' . $count;
	$ts     = (string) time();

	if ( '' === trim( wp_strip_all_tags( $intro ) ) && empty( $attributes['legacy'] ) ) {
		$intro = default_intro();
	}

	// Result of a no-JS submission, read from the redirect back to this page.
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display only.
	$error = isset( $_GET[ FORM_PARAM ], $_GET[ ERROR_PARAM ] ) && $id === $_GET[ FORM_PARAM ]
		? sanitize_key( wp_unslash( $_GET[ ERROR_PARAM ] ) )
		: '';
	// phpcs:enable
	$messages = messages();
	$message  = '' === $error ? '' : ( $messages[ $error ] ?? $messages['error'] );
	$msg_id   = $id . '-message';

	$wrapper = get_block_wrapper_attributes(
		array(
			'id'    => $id,
			'class' => 'ihumbak-newsletter-form--' . $layout . ' ihumbak-newsletter-form--' . $fields,
		)
	);

	interactivity_state();
	$context = wp_interactivity_data_wp_context(
		array(
			'error'     => $error,
			'message'   => $message,
			'messageId' => $msg_id,
			'sending'   => false,
		)
	);

	// aria-invalid / aria-describedby come from the directives (context error → state getters).
	$field_invalid = static function ( string $field ): string {
		return ' data-wp-bind--aria-invalid="state.' . $field . 'Invalid" data-wp-bind--aria-describedby="state.' . $field . 'DescribedBy"';
	};

	$submit = '' !== ( $attributes['submitLabel'] ?? '' ) ? $attributes['submitLabel'] : __( 'Zapisz się', 'ihumbak-newsletter' );
	$clause = clause_html();

	ob_start();
	?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- core-escaped. ?> data-wp-interactive="ihumbak/newsletter" <?php echo $context; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by core. ?> data-wp-on-window--pageshow="actions.pageshow">
	<?php if ( ! empty( $attributes['image'] ) ) : ?>
	<img class="wp-block-ihumbak-newsletter-form__image" src="<?php echo esc_url( $attributes['image'] ); ?>" alt="" loading="lazy" decoding="async" />
	<?php endif; ?>
	<?php if ( '' !== $intro ) : ?>
	<div class="wp-block-ihumbak-newsletter-form__intro"><?php echo $intro; // phpcs:ignore WordPress.Security.EscapeOutput -- inner blocks / kses'd shortcode copy. ?></div>
	<?php endif; ?>
	<form class="wp-block-ihumbak-newsletter-form__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-wp-on--submit="actions.submit">
		<?php if ( 'name-email' === $fields ) : ?>
		<p class="wp-block-ihumbak-newsletter-form__field wp-block-ihumbak-newsletter-form__field--name">
			<label class="wp-block-ihumbak-newsletter-form__label" for="<?php echo esc_attr( $id ); ?>-name"><?php esc_html_e( 'Imię', 'ihumbak-newsletter' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $id ); ?>-name" name="name" required maxlength="100" autocomplete="given-name" placeholder="<?php echo esc_attr( $attributes['namePlaceholder'] ?? __( 'Imię', 'ihumbak-newsletter' ) ); ?>"<?php echo $field_invalid( 'name' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> />
		</p>
		<?php endif; ?>
		<p class="wp-block-ihumbak-newsletter-form__field wp-block-ihumbak-newsletter-form__field--email">
			<label class="wp-block-ihumbak-newsletter-form__label" for="<?php echo esc_attr( $id ); ?>-email"><?php esc_html_e( 'Adres e-mail', 'ihumbak-newsletter' ); ?></label>
			<input type="email" id="<?php echo esc_attr( $id ); ?>-email" name="email" required maxlength="254" autocomplete="email" placeholder="<?php echo esc_attr( $attributes['emailPlaceholder'] ?? __( 'Adres e-mail', 'ihumbak-newsletter' ) ); ?>"<?php echo $field_invalid( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> />
		</p>
		<p class="wp-block-ihumbak-newsletter-form__hp" aria-hidden="true">
			<label><?php esc_html_e( 'Nie wypełniaj tego pola', 'ihumbak-newsletter' ); ?> <input type="text" name="ihn_website" value="" tabindex="-1" autocomplete="off" /></label>
		</p>
		<input type="hidden" name="action" value="<?php echo esc_attr( ACTION ); ?>" />
		<input type="hidden" name="ihn_form" value="<?php echo esc_attr( $id ); ?>" />
		<input type="hidden" name="ihn_return" value="<?php echo esc_url( current_url() ); ?>" />
		<input type="hidden" name="ihn_group" value="<?php echo esc_attr( $group ); ?>" />
		<input type="hidden" name="ihn_thankyou" value="<?php echo esc_url( $thanks ); ?>" />
		<input type="hidden" name="ihn_fields" value="<?php echo esc_attr( $fields ); ?>" />
		<input type="hidden" name="ihn_ts" value="<?php echo esc_attr( $ts ); ?>" />
		<input type="hidden" name="ihn_sig" value="<?php echo esc_attr( sign( $group, $thanks, $fields, $ts ) ); ?>" />
		<p class="wp-block-ihumbak-newsletter-form__submit">
			<button type="submit" class="wp-block-button__link wp-element-button" data-wp-bind--disabled="context.sending"><?php echo esc_html( $submit ); ?></button>
		</p>
		<p class="wp-block-ihumbak-newsletter-form__message" id="<?php echo esc_attr( $msg_id ); ?>" aria-live="polite" data-wp-text="context.message"><?php echo esc_html( $message ); ?></p>
		<?php if ( '' !== $clause ) : ?>
		<p class="wp-block-ihumbak-newsletter-form__clause"><?php echo $clause; // phpcs:ignore WordPress.Security.EscapeOutput -- kses'd in clause_html(). ?></p>
		<?php endif; ?>
	</form>
	<?php if ( ! empty( $attributes['aboutUrl'] ) ) : ?>
	<p class="wp-block-ihumbak-newsletter-form__about"><a href="<?php echo esc_url( $attributes['aboutUrl'] ); ?>"><strong><?php echo esc_html( '' !== ( $attributes['aboutLabel'] ?? '' ) ? $attributes['aboutLabel'] : $attributes['aboutUrl'] ); ?></strong></a></p>
	<?php endif; ?>
</div>
	<?php
	return (string) ob_get_clean();
}
