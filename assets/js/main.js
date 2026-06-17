/**
 * TruePharm USA — global front-end behaviour.
 * Vanilla JS only (no jQuery).
 *
 * @package TruePharm_USA
 */
(function () {
	'use strict';

	var settings = window.TruePharmData || {};

	/* -----------------------------------------------------------------
	 * 1. Scroll reveal (IntersectionObserver, with graceful fallback)
	 * --------------------------------------------------------------- */
	function initReveal() {
		var els = document.querySelectorAll('.reveal');
		if (!els.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			els.forEach(function (el) {
				el.classList.add('in');
			});
			return;
		}

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('in');
						io.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
		);

		els.forEach(function (el) {
			io.observe(el);
		});
	}

	/* -----------------------------------------------------------------
	 * 2. Slide-in menu panel
	 * --------------------------------------------------------------- */
	function initMenu() {
		var burger = document.getElementById('hamburger');
		var panel = document.getElementById('menuPanel');
		var overlay = document.getElementById('menuOverlay');
		var closeBtn = document.getElementById('menuClose');

		if (!burger || !panel || !overlay) {
			return;
		}

		function openMenu() {
			burger.classList.add('open');
			panel.classList.add('open');
			overlay.classList.add('open');
			burger.setAttribute('aria-expanded', 'true');
			panel.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
		}

		function closeMenu() {
			burger.classList.remove('open');
			panel.classList.remove('open');
			overlay.classList.remove('open');
			burger.setAttribute('aria-expanded', 'false');
			panel.setAttribute('aria-hidden', 'true');
			document.body.style.overflow = '';
		}

		burger.addEventListener('click', function () {
			if (panel.classList.contains('open')) {
				closeMenu();
			} else {
				openMenu();
			}
		});

		overlay.addEventListener('click', closeMenu);

		if (closeBtn) {
			closeBtn.addEventListener('click', closeMenu);
		}

		// Close when any menu link is clicked.
		panel.querySelectorAll('.menu-links a').forEach(function (link) {
			link.addEventListener('click', closeMenu);
		});

		// Close on Escape.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' || e.key === 'Esc') {
				closeMenu();
			}
		});
	}

	/* -----------------------------------------------------------------
	 * 3. Live cart count (WooCommerce fragments + Store API refresh)
	 *
	 * The classic add-to-cart button is handled automatically by
	 * WooCommerce's own cart-fragments script, which swaps the
	 * <span class="tp-cart-count"> fragment we register in PHP.
	 *
	 * For block-based add-to-cart (and as a vanilla safety net), we
	 * also refresh the badge from the WooCommerce Store API.
	 * --------------------------------------------------------------- */
	function setCartCount(count) {
		var badges = document.querySelectorAll('.tp-cart-count');
		badges.forEach(function (badge) {
			badge.textContent = String(count);
			if (count > 0) {
				badge.removeAttribute('hidden');
			} else {
				badge.setAttribute('hidden', 'hidden');
			}
		});
	}

	function refreshCartCount() {
		if (!settings.storeCartUrl || !('fetch' in window)) {
			return;
		}

		fetch(settings.storeCartUrl, {
			method: 'GET',
			credentials: 'include',
			headers: { 'Content-Type': 'application/json' }
		})
			.then(function (res) {
				return res.ok ? res.json() : null;
			})
			.then(function (data) {
				if (data && typeof data.items_count !== 'undefined') {
					setCartCount(parseInt(data.items_count, 10) || 0);
				}
			})
			.catch(function () {
				/* Silent — the server-rendered badge remains accurate. */
			});
	}

	function initCart() {
		// Block-based add-to-cart dispatches native events on document.body.
		document.body.addEventListener('wc-blocks_added_to_cart', refreshCartCount);
		document.body.addEventListener('wc-blocks_removed_from_cart', refreshCartCount);

		// Expose for theme AJAX add-to-cart wired in later phases.
		window.TruePharm = window.TruePharm || {};
		window.TruePharm.refreshCartCount = refreshCartCount;
		window.TruePharm.setCartCount = setCartCount;
	}

	/* -----------------------------------------------------------------
	 * 4. Featured products carousel (prev/next arrows)
	 * --------------------------------------------------------------- */
	function initCarousel() {
		var car = document.getElementById('carousel');
		var prev = document.getElementById('carPrev');
		var next = document.getElementById('carNext');
		if (!car || !prev || !next) {
			return;
		}

		function step() {
			var card = car.querySelector('.product-card');
			return card ? card.offsetWidth + 24 : 324;
		}

		prev.addEventListener('click', function () {
			car.scrollBy({ left: -step(), behavior: 'smooth' });
		});
		next.addEventListener('click', function () {
			car.scrollBy({ left: step(), behavior: 'smooth' });
		});
	}

	/* -----------------------------------------------------------------
	 * 5. Newsletter subscribe (AJAX, no reload)
	 * --------------------------------------------------------------- */
	function initNewsletter() {
		var form = document.getElementById('tp-news-form');
		var ajax = window.tp_ajax || {};
		if (!form || !ajax.ajax_url) {
			return;
		}

		var email = document.getElementById('tp-news-email');
		var msg = document.getElementById('tp-news-msg');
		var button = form.querySelector('button[type="submit"]');

		function setMessage(text, type) {
			if (!msg) {
				return;
			}
			msg.textContent = text;
			msg.className = 'news-msg ' + (type || '');
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();

			var value = email ? email.value.trim() : '';
			if (value === '' || value.indexOf('@') === -1) {
				setMessage('Please enter a valid email address.', 'error');
				return;
			}

			if (button) {
				button.disabled = true;
			}
			setMessage('', '');

			var body = 'action=tp_newsletter'
				+ '&nonce=' + encodeURIComponent(ajax.nonce || '')
				+ '&email=' + encodeURIComponent(value);

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
						setMessage(data.data && data.data.message ? data.data.message : 'Subscribed!', 'success');
						form.reset();
					} else {
						setMessage(data && data.data && data.data.message ? data.data.message : 'Something went wrong.', 'error');
					}
				})
				.catch(function () {
					setMessage('Something went wrong. Please try again.', 'error');
				})
				.finally(function () {
					if (button) {
						button.disabled = false;
					}
				});
		});
	}

	/* -----------------------------------------------------------------
	 * Boot
	 * --------------------------------------------------------------- */
	function init() {
		initReveal();
		initMenu();
		initCart();
		initCarousel();
		initNewsletter();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
