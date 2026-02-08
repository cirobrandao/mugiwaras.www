// JS global
document.addEventListener('DOMContentLoaded', () => {
	const selects = document.querySelectorAll('[data-birth-select]');
	const target = document.querySelector('[data-birth-target]');
	const errorEl = document.querySelector('[data-birth-error]');
	const form = target ? target.closest('form') : null;
	const pad = (v) => String(v).padStart(2, '0');
	const updateBirth = () => {
		if (!target) return;
		const day = document.querySelector('[data-birth-select="day"]')?.value || '';
		const month = document.querySelector('[data-birth-select="month"]')?.value || '';
		const year = document.querySelector('[data-birth-select="year"]')?.value || '';
		if (day && month && year) {
			target.value = `${pad(day)}-${pad(month)}-${year}`;
			selects.forEach((sel) => sel.setCustomValidity(''));
			selects.forEach((sel) => sel.classList.remove('is-invalid'));
			if (errorEl) errorEl.style.display = 'none';
		} else {
			target.value = '';
			selects.forEach((sel) => sel.setCustomValidity('Selecione a data completa.'));
		}
	};
	selects.forEach((sel) => sel.addEventListener('change', updateBirth));
	if (form) {
		form.addEventListener('submit', (event) => {
			updateBirth();
			if (!target.value) {
				selects.forEach((sel) => sel.classList.add('is-invalid'));
				if (errorEl) errorEl.style.display = 'block';
				event.preventDefault();
			}
		});
	}
	updateBirth();
});

document.addEventListener('DOMContentLoaded', () => {
	const btn = document.querySelector('[data-support-track-copy]');
	const input = document.querySelector('[data-support-track-link]');
	if (!btn || !input) return;
	btn.addEventListener('click', async () => {
		try {
			await navigator.clipboard.writeText(input.value);
			const old = btn.textContent;
			btn.textContent = 'Copiado!';
			setTimeout(() => { btn.textContent = old || 'Copiar'; }, 1500);
		} catch (e) {
			input.select();
			document.execCommand('copy');
		}
	});
});

document.addEventListener('DOMContentLoaded', () => {
	const modal = document.getElementById('pdfViewerModal');
	const iframe = document.getElementById('pdfViewerFrame');
	const closeBtn = document.getElementById('pdfCloseBtn');
	const openBtn = document.getElementById('pdfOpenBtn');
	const dialog = document.getElementById('pdfViewerDialog');
	if (!modal || !iframe) return;
	const isIOS = /iphone|ipad|ipod/i.test(navigator.userAgent || '');

	const titleEl = document.getElementById('pdfViewerTitle');
	const openPdf = (url, title, itemEl) => {
		if (isIOS) {
			window.open(url, '_blank', 'noopener');
			return;
		}
		iframe.src = url;
		if (titleEl) {
			if (title) {
				titleEl.textContent = title;
			} else if (itemEl) {
				const series = itemEl.getAttribute('data-series-title') || '';
				const itemTitle = itemEl.getAttribute('data-item-title') || '';
				titleEl.textContent = [series, itemTitle].filter(Boolean).join(' - ');
			} else {
				titleEl.textContent = '';
			}
		}
		modal.classList.add('is-open');
		document.body.style.overflow = 'hidden';
	};
	const closePdf = () => {
		iframe.src = '';
		if (titleEl) titleEl.textContent = '';
		modal.classList.remove('is-open');
		document.body.style.overflow = '';
		if (dialog) {
			dialog.classList.remove('is-expanded');
		}
	};

	document.querySelectorAll('.open-pdf, [data-open-pdf]').forEach((btn) => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			const url = btn.getAttribute('data-url') || '';
			const title = btn.getAttribute('data-title') || '';
			const itemEl = btn.closest('.list-group-item');
			if (url) openPdf(url, title, itemEl);
		});
	});
	if (closeBtn) closeBtn.addEventListener('click', closePdf);
	modal.addEventListener('click', (e) => {
		if (e.target === modal) closePdf();
	});
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && modal.classList.contains('is-open')) closePdf();
	});
	if (openBtn) {
		openBtn.addEventListener('click', () => {
			if (!dialog) return;
			dialog.classList.add('is-expanded');
		});
	}
});

document.addEventListener('DOMContentLoaded', () => {
	const toggles = document.querySelectorAll('[data-sidebar-toggle]');
	const shell = document.querySelector('.app-shell');
	const sidebar = document.querySelector('.app-sidebar');
	if (toggles.length && shell) {
		toggles.forEach((btn) => {
			btn.addEventListener('click', () => {
				shell.classList.toggle('sidebar-open');
			});
		});
	}
	if (shell && sidebar) {
		document.addEventListener('pointerdown', (event) => {
			if (!shell.classList.contains('sidebar-open')) return;
			const target = event.target;
			if (sidebar.contains(target)) return;
			if (target && target.closest('[data-sidebar-toggle]')) return;
			shell.classList.remove('sidebar-open');
		}, true);
	}

	const sync = document.querySelector('[data-last-sync]');
	if (sync) {
		const now = new Date();
		sync.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
	}

	const topbar = document.querySelector('.app-topbar');
	const searchInput = document.querySelector('.topbar-search input');
	if (topbar && searchInput) {
		searchInput.addEventListener('focus', () => {
			topbar.classList.add('search-focused');
		});
		searchInput.addEventListener('blur', () => {
			topbar.classList.remove('search-focused');
		});
	}
});

document.addEventListener('DOMContentLoaded', () => {
	const storageKey = 'theme';
	const toggle = document.querySelector('[data-theme-toggle]');
	const icon = toggle ? toggle.querySelector('i') : null;
	const body = document.body;
	const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
	const stored = localStorage.getItem(storageKey);
	const initialTheme = stored || (prefersDark ? 'dark' : 'light');

	const applyTheme = (theme) => {
		const isDark = theme === 'dark';
		body.classList.toggle('theme-dark', isDark);
		if (icon) {
			icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
		}
		if (toggle) toggle.setAttribute('aria-pressed', String(isDark));
	};

	applyTheme(initialTheme);

	if (toggle) {
		toggle.addEventListener('click', () => {
			const next = body.classList.contains('theme-dark') ? 'light' : 'dark';
			localStorage.setItem(storageKey, next);
			applyTheme(next);
		});
	}
});

