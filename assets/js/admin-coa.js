/**
 * COA admin — PDF media uploader (vanilla JS, uses the WP media modal).
 *
 * @package TruePharm_USA
 */
(function () {
	'use strict';

	function init() {
		var selectBtn = document.getElementById('tp_coa_pdf_select');
		var removeBtn = document.getElementById('tp_coa_pdf_remove');
		var input = document.getElementById('tp_coa_pdf');
		var current = document.getElementById('tp_coa_pdf_current');
		var link = document.getElementById('tp_coa_pdf_link');

		if (!selectBtn || !input || typeof window.wp === 'undefined' || !window.wp.media) {
			return;
		}

		var labels = window.tpCoaAdmin || {};
		var frame = null;

		selectBtn.addEventListener('click', function (e) {
			e.preventDefault();

			if (frame) {
				frame.open();
				return;
			}

			frame = window.wp.media({
				title: labels.title || 'Select or Upload a COA PDF',
				button: { text: labels.button || 'Use this PDF' },
				library: { type: 'application/pdf' },
				multiple: false
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				input.value = attachment.id;
				if (link) {
					link.textContent = attachment.title || attachment.filename || 'View PDF';
					link.href = attachment.url;
				}
				if (current) {
					current.classList.remove('is-empty');
				}
			});

			frame.open();
		});

		if (removeBtn) {
			removeBtn.addEventListener('click', function (e) {
				e.preventDefault();
				input.value = '';
				if (link) {
					link.textContent = '';
					link.href = '#';
				}
				if (current) {
					current.classList.add('is-empty');
				}
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
