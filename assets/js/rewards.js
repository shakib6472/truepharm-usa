/**
 * TruePharm Rewards tab — redeem points + copy referral code (vanilla JS).
 *
 * @package TruePharm_USA
 */
(function () {
	'use strict';

	var ajax = window.tp_ajax || {};

	function initRedeem() {
		var btn = document.getElementById('tp-redeem-btn');
		var input = document.getElementById('tp-redeem-points');
		var result = document.getElementById('tp-redeem-result');
		var balanceEl = document.getElementById('tp-balance');
		if (!btn || !input || !ajax.ajax_url) {
			return;
		}

		function showResult(html, ok) {
			if (!result) {
				return;
			}
			result.hidden = false;
			result.className = 'redeem-result ' + (ok ? 'is-success' : 'is-error');
			result.innerHTML = html;
		}

		btn.addEventListener('click', function () {
			var points = parseInt(input.value, 10) || 0;
			btn.disabled = true;

			var body = 'action=tp_redeem_points'
				+ '&nonce=' + encodeURIComponent(ajax.nonce || '')
				+ '&points_to_redeem=' + encodeURIComponent(points);

			fetch(ajax.ajax_url, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body
			})
				.then(function (res) {
					return res.json();
				})
				.then(function (data) {
					if (data && data.success) {
						var d = data.data || {};
						showResult(
							'<strong>' + (d.message || 'Coupon created.') + '</strong>'
								+ '<span class="redeem-code">' + (d.code || '') + '</span>',
							true
						);
						if (balanceEl && typeof d.balance !== 'undefined') {
							balanceEl.firstChild
								? (balanceEl.childNodes[0].nodeValue = d.balance + ' ')
								: (balanceEl.textContent = d.balance + ' Pts');
						}
						input.max = (d.balance || 0);
					} else {
						showResult((data && data.data && data.data.message) ? data.data.message : 'Redemption failed.', false);
					}
				})
				.catch(function () {
					showResult('Something went wrong. Please try again.', false);
				})
				.finally(function () {
					btn.disabled = false;
				});
		});
	}

	function init() {
		initRedeem();
		// Copy-to-clipboard is handled globally by main.js (.tp-copy-btn).
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
