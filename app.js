// app.js - simple client-side partial loader with History API and animations
const app = document.getElementById('app');

async function loadPage(url, addToHistory = true, title = '', initName = '') {
	try {
		if (!app) return;
		// normalize URL relative to the site root to avoid duplication
		const fullUrl = new URL(url, location.origin + '/').toString();

		app.classList.add('fade-out');
		await new Promise(r => setTimeout(r, 180));

		const res = await fetch(fullUrl, { cache: 'no-store' });
		if (!res.ok) throw new Error(res.statusText);
		const html = await res.text();
		app.innerHTML = html;

		// update title if provided
		if (title) document.title = title;

		// call page init if provided
		if (initName && typeof window[initName] === 'function') {
			try { window[initName](); } catch (e) { console.error('init error', e); }
		}

		// active link highlighting
		highlightActiveLink(fullUrl);

		app.classList.remove('fade-out');
		app.classList.add('fade-in');
		setTimeout(() => app.classList.remove('fade-in'), 300);

		if (addToHistory) history.pushState({ url: fullUrl, title, initName }, '', fullUrl);
	} catch (err) {
		console.error('Error cargando página:', err);
		if (app) app.innerHTML = '<p class="text-red-600">Error cargando contenido.</p>';
	}
}

function highlightActiveLink(fullUrl) {
	document.querySelectorAll('a[data-link].active').forEach(el => el.classList.remove('active'));
	const links = Array.from(document.querySelectorAll('a[data-link]'));
	for (const link of links) {
		try {
			const linkFull = new URL(link.getAttribute('href'), location.href).toString();
			if (linkFull === fullUrl) { link.classList.add('active'); break; }
		} catch (e) { }
	}
}

document.addEventListener('click', (e) => {
	const a = e.target.closest('a[data-link]');
	if (!a) return;
	e.preventDefault();
	const href = a.getAttribute('href');
	const title = a.dataset.title || '';
	const initName = a.dataset.init || a.dataset.initName || '';
	if (href) loadPage(href, true, title, initName);
});

window.addEventListener('popstate', (evt) => {
	const state = evt.state || {};
	const url = state.url || 'dist/content/home.html';
	loadPage(url, false, state.title || '', state.initName || '');
});

window.addEventListener('DOMContentLoaded', () => {
	if (!history.state) history.replaceState({ url: 'dist/content/home.html' }, '', location.pathname);
	const current = (history.state && history.state.url) ? history.state.url : 'dist/content/home.html';
	highlightActiveLink(new URL(current, location.href).toString());
});
