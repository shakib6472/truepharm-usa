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
	 * 6. FAQ accordion + sidebar smooth scroll
	 * --------------------------------------------------------------- */
	function initFaq() {
		var questions = document.querySelectorAll('.faq-question');
		questions.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var item = btn.closest('.faq-item');
				var answer = item ? item.querySelector('.faq-answer') : null;
				if (!item || !answer) {
					return;
				}
				var isOpen = item.classList.toggle('active');
				btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
				answer.style.maxHeight = isOpen ? (answer.scrollHeight + 'px') : '';
			});
		});

		var navLinks = document.querySelectorAll('.faq-nav a');
		navLinks.forEach(function (link) {
			link.addEventListener('click', function (e) {
				var id = link.getAttribute('href');
				if (!id || id.charAt(0) !== '#') {
					return;
				}
				var target = document.querySelector(id);
				if (!target) {
					return;
				}
				e.preventDefault();
				navLinks.forEach(function (l) { l.classList.remove('active'); });
				link.classList.add('active');
				var top = target.getBoundingClientRect().top + window.pageYOffset - 100;
				window.scrollTo({ top: top, behavior: 'smooth' });
			});
		});
	}

	/* -----------------------------------------------------------------
	 * 7. Contact form (AJAX)
	 * --------------------------------------------------------------- */
	function initContact() {
		var form = document.getElementById('tp-contact-form');
		var ajax = window.tp_ajax || {};
		if (!form || !ajax.ajax_url) {
			return;
		}
		var msg = document.getElementById('tp-contact-msg');
		var button = form.querySelector('button[type="submit"]');

		function setMessage(text, type) {
			if (!msg) {
				return;
			}
			msg.textContent = text;
			msg.className = 'contact-msg ' + (type || '');
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (button) {
				button.disabled = true;
			}
			setMessage('', '');

			var data = new URLSearchParams();
			data.append('action', 'tp_submit_contact');
			data.append('nonce', ajax.nonce || '');
			['first_name', 'last_name', 'email', 'order_number', 'subject', 'message'].forEach(function (name) {
				var field = form.querySelector('[name="' + name + '"]');
				data.append(name, field ? field.value : '');
			});
			var token = form.querySelector('[name="cf-turnstile-response"]');
			if (token) {
				data.append('cf-turnstile-response', token.value);
			}

			fetch(ajax.ajax_url, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: data.toString()
			})
				.then(function (res) { return res.json(); })
				.then(function (resp) {
					if (resp && resp.success) {
						setMessage(resp.data && resp.data.message ? resp.data.message : 'Message sent.', 'success');
						form.reset();
					} else {
						setMessage(resp && resp.data && resp.data.message ? resp.data.message : 'Something went wrong.', 'error');
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
	 * 8. Copy-to-clipboard buttons (.tp-copy-btn[data-code])
	 * --------------------------------------------------------------- */
	function initCopyButtons() {
		document.querySelectorAll('.tp-copy-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var code = btn.getAttribute('data-code') || '';
				var done = function () {
					var original = btn.textContent;
					btn.textContent = 'Copied!';
					setTimeout(function () { btn.textContent = original; }, 1500);
				};
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(code).then(done).catch(done);
				} else {
					var tmp = document.createElement('textarea');
					tmp.value = code;
					document.body.appendChild(tmp);
					tmp.select();
					try { document.execCommand('copy'); } catch (err) {}
					document.body.removeChild(tmp);
					done();
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
		initFaq();
		initContact();
		initCopyButtons();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
