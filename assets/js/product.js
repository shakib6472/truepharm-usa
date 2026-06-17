/**
 * Single product interactions (vanilla JS, WooCommerce native wc-ajax endpoints).
 *
 * - Variant pill selection (price + variation_id)
 * - Gallery thumbnail swap
 * - Quantity +/- buttons
 * - Tab switching
 * - Bundle option selection
 * - Add to cart + bundle add via wc-ajax=add_to_cart
 *
 * @package TruePharm_USA
 */
(function () {
	'use strict';

	var settings = window.tp_product || {};

	function ajaxUrl() {
		if (!settings.wc_ajax_url) {
			return '';
		}
		return settings.wc_ajax_url.replace('%%endpoint%%', 'add_to_cart');
	}

	/* Replace WooCommerce cart fragments returned by the AJAX response. */
	function applyFragments(fragments) {
		if (!fragments) {
			return;
		}
		Object.keys(fragments).forEach(function (selector) {
			var nodes = document.querySelectorAll(selector);
			nodes.forEach(function (node) {
				var tmp = document.createElement('div');
				tmp.innerHTML = fragments[selector];
				if (tmp.firstElementChild) {
					node.replaceWith(tmp.firstElementChild);
				}
			});
		});
	}

	function flashButton(btn, label) {
		if (!btn) {
			return;
		}
		var span = btn.querySelector('span') || btn;
		var original = span.textContent;
		btn.classList.add('added');
		span.textContent = label;
		setTimeout(function () {
			span.textContent = original;
			btn.classList.remove('added');
			btn.disabled = false;
		}, 1800);
	}

	/* Add a product (or specific variation) to the cart via wc-ajax. */
	function addToCart(productId, quantity, btn) {
		var url = ajaxUrl();
		if (!url || !productId) {
			return;
		}

		if (btn) {
			btn.disabled = true;
		}

		var body = 'product_id=' + encodeURIComponent(productId)
			+ '&quantity=' + encodeURIComponent(quantity || 1);

		fetch(url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (data) {
				if (data && data.error && data.product_url) {
					window.location = data.product_url;
					return;
				}
				if (data && data.fragments) {
					applyFragments(data.fragments);
				}
				if (window.TruePharm && typeof window.TruePharm.refreshCartCount === 'function') {
					window.TruePharm.refreshCartCount();
				}
				flashButton(btn, settings.added_text || 'Added ✓');
			})
			.catch(function () {
				if (btn) {
					btn.disabled = false;
				}
			});
	}

	/* Resolve the product/variation id to add for the current selection. */
	function resolveProductId(details) {
		if (!details) {
			return 0;
		}
		if (details.getAttribute('data-type') === 'variable') {
			var hidden = document.getElementById('tp-variation-id');
			return hidden ? parseInt(hidden.value, 10) || 0 : 0;
		}
		return parseInt(details.getAttribute('data-product_id'), 10) || 0;
	}

	function currentQty() {
		var input = document.getElementById('tp-qty');
		var q = input ? parseInt(input.value, 10) : 1;
		return q > 0 ? q : 1;
	}

	/* --------------------------------------------------------------- */
	function initVariants() {
		var wrap = document.getElementById('tp-variants');
		var priceEl = document.getElementById('tp-price');
		var hidden = document.getElementById('tp-variation-id');
		if (!wrap) {
			return;
		}

		wrap.querySelectorAll('.variant-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				if (btn.disabled) {
					return;
				}
				wrap.querySelectorAll('.variant-btn').forEach(function (b) {
					b.classList.remove('active');
				});
				btn.classList.add('active');
				if (priceEl && btn.getAttribute('data-price')) {
					priceEl.innerHTML = btn.getAttribute('data-price');
				}
				if (hidden) {
					hidden.value = btn.getAttribute('data-variation_id') || '0';
				}
			});
		});
	}

	function initGallery() {
		var main = document.getElementById('tp-main-image');
		var thumbs = document.querySelectorAll('.thumbnail');
		if (!main || !thumbs.length) {
			return;
		}
		thumbs.forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				var large = thumb.getAttribute('data-large');
				if (large) {
					main.src = large;
					main.removeAttribute('srcset');
				}
				thumbs.forEach(function (t) {
					t.classList.remove('active');
				});
				thumb.classList.add('active');
			});
		});
	}

	function initQty() {
		var input = document.getElementById('tp-qty');
		if (!input) {
			return;
		}
		document.querySelectorAll('.qty-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var val = parseInt(input.value, 10) || 1;
				var max = parseInt(input.max, 10) || 99;
				var min = parseInt(input.min, 10) || 1;
				if (btn.getAttribute('data-step') === 'up') {
					val = Math.min(max, val + 1);
				} else {
					val = Math.max(min, val - 1);
				}
				input.value = val;
			});
		});
	}

	function initTabs() {
		var buttons = document.querySelectorAll('.tab-btn');
		if (!buttons.length) {
			return;
		}
		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var target = btn.getAttribute('data-tab');
				buttons.forEach(function (b) {
					b.classList.remove('active');
				});
				document.querySelectorAll('.tab-panel').forEach(function (panel) {
					panel.classList.remove('active');
				});
				btn.classList.add('active');
				var panel = document.getElementById(target);
				if (panel) {
					panel.classList.add('active');
				}
			});
		});
	}

	function initBundle() {
		var wrap = document.getElementById('tp-bundle');
		if (!wrap) {
			return;
		}
		wrap.querySelectorAll('.bundle-opt').forEach(function (opt) {
			opt.addEventListener('click', function () {
				wrap.querySelectorAll('.bundle-opt').forEach(function (o) {
					o.classList.remove('active');
					var r = o.querySelector('input[type="radio"]');
					if (r) {
						r.checked = false;
					}
				});
				opt.classList.add('active');
				var radio = opt.querySelector('input[type="radio"]');
				if (radio) {
					radio.checked = true;
				}
			});
		});
	}

	function initAddToCart() {
		var details = document.querySelector('.product-details');
		if (!details) {
			return;
		}

		var addBtn = document.getElementById('tp-add-to-cart');
		var bundleBtn = document.getElementById('tp-add-bundle');

		function ensureSelection() {
			var id = resolveProductId(details);
			if (details.getAttribute('data-type') === 'variable' && !id) {
				window.alert(settings.select_text || 'Please select a vial size first.');
				return 0;
			}
			return id;
		}

		if (addBtn) {
			addBtn.addEventListener('click', function () {
				var id = ensureSelection();
				if (id) {
					addToCart(id, currentQty(), addBtn);
				}
			});
		}

		if (bundleBtn) {
			bundleBtn.addEventListener('click', function () {
				var id = ensureSelection();
				if (!id) {
					return;
				}
				var active = document.querySelector('#tp-bundle .bundle-opt.active');
				var qty = active ? parseInt(active.getAttribute('data-qty'), 10) : 0;
				addToCart(id, qty || 1, bundleBtn);
			});
		}
	}

	function init() {
		initVariants();
		initGallery();
		initQty();
		initTabs();
		initBundle();
		initAddToCart();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
