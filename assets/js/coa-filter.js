/**
 * COA Library archive — live table filter (vanilla, no jQuery).
 * Filters rows by Batch Number OR Compound Name, instantly & case-insensitive.
 *
 * @package TruePharm_USA
 */
(function () {
	'use strict';

	function init() {
		var input = document.getElementById('batch-search');
		var table = document.querySelector('.coa-table');
		var noResults = document.getElementById('coa-noresults');
		if (!input || !table) {
			return;
		}

		var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));

		function filter() {
			var term = input.value.trim().toLowerCase();
			var visible = 0;

			rows.forEach(function (row) {
				var batch = (row.getAttribute('data-batch') || '').toLowerCase();
				var compound = (row.getAttribute('data-compound') || '').toLowerCase();
				var match = term === '' || batch.indexOf(term) !== -1 || compound.indexOf(term) !== -1;
				row.hidden = !match;
				if (match) {
					visible++;
				}
			});

			if (noResults) {
				if (visible === 0 && term !== '') {
					noResults.textContent = 'No results found for "' + input.value.trim() + '"';
					noResults.style.display = 'block';
				} else {
					noResults.style.display = 'none';
				}
			}
		}

		input.addEventListener('input', filter);
		// Prevent the search button / Enter from reloading the page.
		var form = input.closest('form');
		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				filter();
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
