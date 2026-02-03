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

