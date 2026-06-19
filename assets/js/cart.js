/**
 * Cart page — AJAX quantity / remove / coupon (vanilla JS, no jQuery).
 * Routes through the theme's tp_cart_update handler and re-renders the
 * line rows + order summary in place. Native form POST is the no-JS fallback.
 *
 * @package TruePharm_USA
 */
(function () {
	'use strict';

	var ajax = window.tp_ajax || {};
	var section = document.querySelector('.cart-section');
	if (!section || !ajax.ajax_url) {
		return;
	}

	var enc = encodeURIComponent;

	function setBusy(on) {
		section.style.opacity = on ? '0.55' : '';
		section.style.pointerEvents = on ? 'none' : '';
	}

	function showNotices(html) {
		var box = document.getElementById('tp-cart-notices');
		if (!box) {
			box = document.createElement('div');
			box.id = 'tp-cart-notices';
			box.className = 'tp-cart-notices';
			section.insertBefore(box, section.firstChild);
		}
		box.innerHTML = (typeof html === 'string') ? html : '';
	}

	function render(data) {
		if (data.empty) {
			window.location.reload();
			return;
		}
		var items = document.getElementById('tp-cart-items');
		var summary = document.getElementById('tp-cart-summary-inner');
		if (items && typeof data.items === 'string') {
			items.innerHTML = data.items;
		}
		if (summary && typeof data.summary === 'string') {
			summary.innerHTML = data.summary;
		}
		showNotices(data.notices);
		if (window.TruePharm && typeof window.TruePharm.setCartCount === 'function') {
			window.TruePharm.setCartCount(parseInt(data.count, 10) || 0);
		}
	}

	function request(body) {
		setBusy(true);
		fetch(ajax.ajax_url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body
		})
			.then(function (r) { return r.json(); })
			.then(function (resp) { if (resp && resp.success) { render(resp.data); } })
			.catch(function () {})
			.finally(function () { setBusy(false); });
	}

	function base() {
		return 'action=tp_cart_update&nonce=' + enc(ajax.nonce || '');
	}

	function updateQty(input) {
		request(base() + '&do=qty&cart_item_key=' + enc(input.getAttribute('data-cart_key')) + '&quantity=' + enc(input.value));
	}

	// Clicks: +/- steppers, remove, apply coupon, (block native update_cart).
	section.addEventListener('click', function (e) {
		var step = e.target.closest('.qty-btn');
		if (step) {
			var input = step.parentNode.querySelector('.tp-qty');
			if (!input) { return; }
			var val = parseInt(input.value, 10) || 0;
			var max = parseInt(input.max, 10);
			if (step.getAttribute('data-step') === 'up') {
				val = (max && max > 0) ? Math.min(max, val + 1) : val + 1;
			} else {
				val = Math.max(0, val - 1);
			}
			input.value = val;
			updateQty(input);
			return;
		}

		var remove = e.target.closest('.tp-remove');
		if (remove) {
			e.preventDefault();
			request(base() + '&do=remove&cart_item_key=' + enc(remove.getAttribute('data-cart_key')));
			return;
		}

		var apply = e.target.closest('[name="apply_coupon"]');
		if (apply) {
			e.preventDefault();
			var code = document.getElementById('coupon_code');
			request(base() + '&do=coupon&coupon=' + enc(code ? code.value : ''));
			return;
		}

		var update = e.target.closest('[name="update_cart"]');
		if (update) {
			e.preventDefault(); // qty changes already auto-save via AJAX
		}
	});

	// Typing a quantity directly (debounced).
	var timer;
	section.addEventListener('input', function (e) {
		var input = e.target.closest('.tp-qty');
		if (input) {
			clearTimeout(timer);
			timer = setTimeout(function () { updateQty(input); }, 500);
		}
	});

	// Prevent a native reload when pressing Enter in the coupon field.
	var form = section.querySelector('.tp-cart-form');
	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var code = document.getElementById('coupon_code');
			if (code && code.value.trim() !== '') {
				request(base() + '&do=coupon&coupon=' + enc(code.value));
			}
		});
	}
})();
