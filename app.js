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

		// push state BEFORE DOM update so page scripts can read the URL
		if (addToHistory) history.pushState({ url: fullUrl, title, initName }, '', fullUrl);

		app.innerHTML = html;

		app.querySelectorAll('script').forEach(oldScript => {
			const newScript = document.createElement('script');
			Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
			newScript.textContent = oldScript.textContent;
			oldScript.parentNode.replaceChild(newScript, oldScript);
		});

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

function mostrarToast(mensaje) {
	var t = document.createElement('div');
	t.textContent = mensaje;
	Object.assign(t.style, {
		position:'fixed', bottom:'24px', right:'24px',
		background:'#16a34a', color:'white',
		padding:'16px 24px', borderRadius:'12px',
		boxShadow:'0 10px 25px rgba(0,0,0,0.2)',
		zIndex:'9999', fontWeight:'500', fontSize:'14px',
		transition:'all 0.3s ease',
		transform:'translateY(20px)', opacity:'0'
	});
	document.body.appendChild(t);
	requestAnimationFrame(function() {
		t.style.transform = 'translateY(0)'; t.style.opacity = '1';
	});
	setTimeout(function() {
		t.style.transform = 'translateY(20px)'; t.style.opacity = '0';
		setTimeout(function() { t.remove(); }, 300);
	}, 3000);
}

document.addEventListener('click', (e) => {
	const a = e.target.closest('a[data-link]');
	if (!a) return;
	if (a.dataset.restricted === 'true') {
		e.preventDefault();
		mostrarToast('Este módulo no está permitido para tu usuario');
		return;
	}
	e.preventDefault();
	const href = a.getAttribute('href');
	const title = a.dataset.title || '';
	const initName = a.dataset.init || a.dataset.initName || '';
	if (href) loadPage(href, true, title, initName);
});

document.addEventListener('click', (e) => {
	const el = e.target.closest('[data-restricted="true"]');
	if (el && !el.matches('a[data-link]')) {
		e.preventDefault();
		if (el.tagName === 'A') {
			mostrarToast('Este módulo no está permitido para tu usuario');
		}
	}
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
