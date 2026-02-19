/**
 * Elementor TOC anchor fix.
 * Injects ez-toc-section spans into headings that lack them (e.g. Elementor
 * underlined headings) so TOC jump links work. Only runs when TOC exists.
 * Pure JS, no jQuery.
 */
(function () {
	'use strict';

	function run() {
		var tocContainer = document.getElementById('ez-toc-container') || document.querySelector('.ez-toc-widget-sticky-container');
		if (!tocContainer) return;

		var nav = tocContainer.querySelector('nav');
		if (!nav) return;

		var tocLinks = nav.querySelectorAll('a.ez-toc-link');
		if (!tocLinks.length) return;

		var anchorIds = [];
		for (var i = 0; i < tocLinks.length; i++) {
			var href = tocLinks[i].getAttribute('href') || tocLinks[i].getAttribute('data-href') || '';
			var hashIdx = href.indexOf('#');
			var id = hashIdx >= 0 ? href.substring(hashIdx + 1) : '';
			try { id = decodeURIComponent(id); } catch (e) { /* keep id as-is */ }
			if (id) anchorIds.push(id);
		}
		if (!anchorIds.length) return;

		var allHeadings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
		var contentHeadings = [];
		for (var j = 0; j < allHeadings.length; j++) {
			var el = allHeadings[j];
			if (el.closest('#ez-toc-container') || el.closest('.ez-toc-widget-sticky-container')) continue;
			if (el.querySelector('span.ez-toc-section')) continue;
			contentHeadings.push(el);
		}
		if (!contentHeadings.length) return;

		function normalizeText(str) {
			if (typeof str !== 'string') return '';
			return str.replace(/\s+/g, ' ').trim();
		}

		if (contentHeadings.length === anchorIds.length) {
			for (var k = 0; k < contentHeadings.length; k++) {
				injectSpan(contentHeadings[k], anchorIds[k]);
			}
			return;
		}

		for (var m = 0; m < tocLinks.length; m++) {
			var linkText = normalizeText(tocLinks[m].textContent);
			var anchorId = anchorIds[m];
			for (var n = 0; n < contentHeadings.length; n++) {
				var h = contentHeadings[n];
				if (h._eztocMatched) continue;
				if (normalizeText(h.textContent) === linkText) {
					injectSpan(h, anchorId);
					h._eztocMatched = true;
					break;
				}
			}
		}
	}

	function injectSpan(heading, id) {
		if (heading.querySelector('span.ez-toc-section')) return;
		var span = document.createElement('span');
		span.className = 'ez-toc-section';
		span.id = id;
		heading.insertBefore(span, heading.firstChild);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}
})();
