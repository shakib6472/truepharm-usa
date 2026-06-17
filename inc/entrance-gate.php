<?php
/**
 * Entrance Gate — age / research-compliance modal.
 *
 * Shown until the visitor accepts, after which the tp_compliance_agreed cookie
 * (30 days) suppresses it. Acceptance is recorded server-side via AJAX.
 *
 * @package TruePharm_USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Cookie name + lifetime. */
define( 'TP_GATE_COOKIE', 'tp_compliance_agreed' );
define( 'TP_GATE_COOKIE_DAYS', 30 );

/**
 * Whether the gate should render for the current request.
 */
function tp_gate_should_show(): bool {
	if ( is_admin() ) {
		return false;
	}
	if ( ! empty( $_COOKIE[ TP_GATE_COOKIE ] ) ) {
		return false;
	}
	return (bool) apply_filters( 'tp_gate_should_show', true );
}

/**
 * Render the gate modal + its inline controller in the footer.
 */
function tp_render_entrance_gate(): void {
	if ( ! tp_gate_should_show() ) {
		return;
	}

	$gate = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'tp_gate' ),
		'cookie'   => TP_GATE_COOKIE,
		'days'     => TP_GATE_COOKIE_DAYS,
		'exit_url' => apply_filters( 'tp_gate_exit_url', 'https://www.google.com' ),
	);
	?>
	<div class="gate-overlay" id="complianceGate" role="dialog" aria-modal="true" aria-labelledby="gateTitle">
		<div class="gate-box">

			<div class="gate-logo">
				<?php
				if ( has_custom_logo() ) {
					echo get_custom_logo();
				} else {
					echo '<strong class="logo-text">' . esc_html( get_bloginfo( 'name' ) ) . '</strong>';
				}
				?>
			</div>

			<h1 class="gate-title" id="gateTitle"><?php esc_html_e( 'Restricted Laboratory Portal', 'truepharm' ); ?></h1>

			<div class="gate-warning">
				<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
				<?php esc_html_e( 'Strict Research Compliance', 'truepharm' ); ?>
			</div>

			<div class="gate-text">
				<p><?php esc_html_e( 'Welcome to TruePharm USA. Access to this platform is restricted exclusively to qualified researchers and institutional representatives.', 'truepharm' ); ?></p>
				<p><?php esc_html_e( 'By entering this site, you officially acknowledge and agree to the following terms:', 'truepharm' ); ?></p>
				<ul class="gate-list">
					<li><strong><?php esc_html_e( 'You are 21 years of age or older.', 'truepharm' ); ?></strong></li>
					<li><?php printf( /* translators: %s: emphasised "formulas". */ esc_html__( 'All %s sold here are strictly for in-vitro laboratory research purposes only.', 'truepharm' ), '<strong>' . esc_html__( 'formulas', 'truepharm' ) . '</strong>' ); ?></li>
					<li><?php printf( /* translators: %s: emphasised "NOT". */ esc_html__( 'These compounds are %s intended for human consumption, veterinary use, or therapeutic application.', 'truepharm' ), '<strong>' . esc_html__( 'NOT', 'truepharm' ) . '</strong>' ); ?></li>
					<li><?php esc_html_e( 'You are a qualified professional equipped to handle and utilize these materials safely.', 'truepharm' ); ?></li>
				</ul>
			</div>

			<div class="gate-actions">
				<button class="btn-agree" id="gateAgree" type="button">
					<span class="metallic-text-warm"><?php esc_html_e( 'I Agree &amp; Confirm Age (Enter)', 'truepharm' ); ?></span>
				</button>
				<button class="btn-exit" id="gateExit" type="button"><?php esc_html_e( 'I Disagree (Leave Site)', 'truepharm' ); ?></button>
			</div>

		</div>
	</div>

	<script>
	(function () {
		var GATE = <?php echo wp_json_encode( $gate ); ?>;
		var overlay = document.getElementById('complianceGate');
		var agree = document.getElementById('gateAgree');
		var exit = document.getElementById('gateExit');
		if (!overlay || !agree || !exit) { return; }

		document.body.style.overflow = 'hidden';

		function dismiss() {
			overlay.style.display = 'none';
			document.body.style.overflow = '';
		}

		function setLocalCookie() {
			var d = new Date();
			d.setTime(d.getTime() + (GATE.days * 24 * 60 * 60 * 1000));
			document.cookie = GATE.cookie + '=1; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
		}

		agree.addEventListener('click', function () {
			agree.disabled = true;
			// Belt-and-suspenders: set the cookie client-side immediately...
			setLocalCookie();
			// ...and record acceptance server-side.
			if ('fetch' in window) {
				var body = 'action=tp_set_gate_cookie&nonce=' + encodeURIComponent(GATE.nonce);
				fetch(GATE.ajax_url, {
					method: 'POST',
					credentials: 'include',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: body
				}).then(function () { dismiss(); }).catch(function () { dismiss(); });
			} else {
				dismiss();
			}
		});

		exit.addEventListener('click', function () {
			window.location.href = GATE.exit_url;
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'tp_render_entrance_gate' );
// Also respond to the explicit header hook placed in Phase 1 (no double-render:
// the cookie/output guard runs once and wp_footer is the canonical injection point).

/**
 * AJAX: record gate acceptance by setting the compliance cookie.
 */
function tp_set_gate_cookie(): void {
	check_ajax_referer( 'tp_gate', 'nonce' );

	$expire = time() + ( TP_GATE_COOKIE_DAYS * DAY_IN_SECONDS );
	setcookie( TP_GATE_COOKIE, '1', $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );
	$_COOKIE[ TP_GATE_COOKIE ] = '1';

	wp_send_json_success();
}
add_action( 'wp_ajax_tp_set_gate_cookie', 'tp_set_gate_cookie' );
add_action( 'wp_ajax_nopriv_tp_set_gate_cookie', 'tp_set_gate_cookie' );
