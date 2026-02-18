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
	const ua = navigator.userAgent || '';
	const isIOS = /iphone|ipad|ipod/i.test(ua);

	const titleEl = document.getElementById('pdfViewerTitle');
	const openPdf = (url, title, itemEl) => {
		if (isIOS) {
			const opened = window.open(url, '_blank', 'noopener');
			if (!opened) {
				window.location.href = url;
			}
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
			const url = btn.getAttribute('data-url') || btn.getAttribute('href') || '';
			const title = btn.getAttribute('data-title') || '';
			const itemEl = btn.closest('.list-group-item, .series-item');
			
			// On desktop, always use modal popup
			if (!isIOS && url) {
				e.preventDefault();
				openPdf(url, title, itemEl);
			}
			// On mobile (iOS), let default behavior happen (opens in new tab)
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
	const sync = document.querySelector('[data-last-sync]');
	if (sync) {
		const now = new Date();
		sync.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
	}

	const navbar = document.querySelector('.navbar.app-navbar');
	const searchInput = document.querySelector('.navbar-search input');
	if (navbar && searchInput) {
		searchInput.addEventListener('focus', () => {
			navbar.classList.add('search-focused');
		});
		searchInput.addEventListener('blur', () => {
			navbar.classList.remove('search-focused');
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
		document.documentElement.setAttribute('data-theme', theme);
		if (icon) {
			icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
		}
		if (toggle) toggle.setAttribute('aria-pressed', String(isDark));
	};

	// Only apply if not already applied by inline script
	if (!body.classList.contains('theme-dark') && initialTheme === 'dark') {
		applyTheme(initialTheme);
	} else if (body.classList.contains('theme-dark') && initialTheme === 'light') {
		applyTheme(initialTheme);
	} else {
		// Just update icon without re-applying theme
		if (icon) {
			const isDark = body.classList.contains('theme-dark');
			icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
		}
		if (toggle) {
			toggle.setAttribute('aria-pressed', String(body.classList.contains('theme-dark')));
		}
	}

	if (toggle) {
		toggle.addEventListener('click', () => {
			const next = body.classList.contains('theme-dark') ? 'light' : 'dark';
			localStorage.setItem(storageKey, next);
			applyTheme(next);
		});
	}
});

document.addEventListener('DOMContentLoaded', () => {
	const revokeModal = document.getElementById('revokeModal');
	if (!revokeModal) return;
	const revokeForm = revokeModal.querySelector('form');
	const revokeConfirmBtn = document.getElementById('revokeConfirmBtn');
	const proofLink = document.getElementById('revokeProofLink');
	const setText = (id, value) => {
		const el = document.getElementById(id);
		if (el) el.textContent = value;
	};
	const setValue = (id, value) => {
		const el = document.getElementById(id);
		if (el) el.value = value;
	};
	const setPaymentData = (data) => {
		revokeModal.dataset.paymentId = data.id || '';
		revokeModal.dataset.user = data.user || '-';
		revokeModal.dataset.package = data.package || '-';
		revokeModal.dataset.months = data.months || '-';
		revokeModal.dataset.status = data.status || '-';
		revokeModal.dataset.created = data.created || '-';
		revokeModal.dataset.tier = data.tier || '-';
		revokeModal.dataset.subscription = data.subscription || '-';
		revokeModal.dataset.credits = data.credits || '0';
		revokeModal.dataset.proof = data.proof || '-';
	};

	document.addEventListener('click', (e) => {
		const btn = e.target.closest('[data-action="open-refund"]');
		if (!btn) return;
		const id = btn.getAttribute('data-payment-id') || '';
		setPaymentData({
			id,
			user: btn.getAttribute('data-revoke-user') || '-',
			package: btn.getAttribute('data-revoke-package') || '-',
			months: btn.getAttribute('data-revoke-months') || '-',
			status: btn.getAttribute('data-revoke-status') || '-',
			created: btn.getAttribute('data-revoke-created') || '-',
			tier: btn.getAttribute('data-revoke-tier') || '-',
			subscription: btn.getAttribute('data-revoke-subscription') || '-',
			credits: btn.getAttribute('data-revoke-credits') || '0',
			proof: btn.getAttribute('data-revoke-proof') || '-',
		});
		console.log('refund open payment_id:', id);
	});

	revokeModal.addEventListener('show.bs.modal', (event) => {
		const trigger = event && event.relatedTarget ? event.relatedTarget.closest('[data-action="open-refund"]') : null;
		if (trigger) {
			setPaymentData({
				id: trigger.getAttribute('data-payment-id') || '',
				user: trigger.getAttribute('data-revoke-user') || '-',
				package: trigger.getAttribute('data-revoke-package') || '-',
				months: trigger.getAttribute('data-revoke-months') || '-',
				status: trigger.getAttribute('data-revoke-status') || '-',
				created: trigger.getAttribute('data-revoke-created') || '-',
				tier: trigger.getAttribute('data-revoke-tier') || '-',
				subscription: trigger.getAttribute('data-revoke-subscription') || '-',
				credits: trigger.getAttribute('data-revoke-credits') || '0',
				proof: trigger.getAttribute('data-revoke-proof') || '-',
			});
		}
		const rawId = revokeModal.dataset.paymentId || '';
		const paymentId = parseInt(rawId, 10);
		console.log('revoke payment_id:', paymentId, 'raw:', rawId);
		setValue('revokeId', Number.isFinite(paymentId) ? paymentId : 0);
		setText('revokeUser', revokeModal.dataset.user || '-');
		setText('revokePackage', revokeModal.dataset.package || '-');
		setText('revokeMonths', revokeModal.dataset.months || '-');
		setText('revokeStatus', revokeModal.dataset.status || '-');
		setText('revokeCreated', revokeModal.dataset.created || '-');
		setText('revokeTier', revokeModal.dataset.tier || '-');
		setText('revokeSubscription', revokeModal.dataset.subscription || '-');
		setText('revokeCredits', revokeModal.dataset.credits || '0');
		setText('revokeProof', revokeModal.dataset.proof || '-');
		if (proofLink) {
			proofLink.style.display = 'none';
			proofLink.href = '#';
		}
		if (!Number.isFinite(paymentId) || paymentId <= 0) return;
		fetch(`/admin/payments/${paymentId}/details`, { method: 'GET', credentials: 'same-origin' })
			.then((res) => res.json())
			.then((data) => {
				if (!data || !data.ok || !data.payment) return;
				const p = data.payment;
				setText('revokeUser', p.username || '-');
				setText('revokePackage', p.package_title || (`#${p.package_id || ''}`));
				setText('revokeMonths', String(p.months || '-'));
				setText('revokeStatus', p.status || '-');
				setText('revokeCreated', p.created_at || '-');
				setText('revokeTier', p.access_tier || '-');
				setText('revokeSubscription', p.subscription_expires_at || '-');
				setText('revokeCredits', String(p.credits != null ? p.credits : '0'));
				if (p.proof_path) {
					setText('revokeProof', 'Sim');
					if (proofLink && p.proof_url) {
						proofLink.href = p.proof_url;
						proofLink.style.display = '';
					}
				} else {
					setText('revokeProof', 'Nao');
				}
			})
			.catch(() => {});
	});

	if (revokeConfirmBtn && revokeForm) {
		revokeConfirmBtn.addEventListener('click', () => {
			const rawId = revokeModal.dataset.paymentId || '';
			const paymentId = parseInt(rawId, 10);
			if (!Number.isFinite(paymentId) || paymentId <= 0) {
				window.alert('ID invalido no estorno (sem payment_id).');
				return;
			}
			const formData = new FormData(revokeForm);
			const submitUrl = `/admin/payments/${paymentId}/revoke`;
			console.log('refund submit payment_id:', paymentId, 'url:', submitUrl);
			fetch(submitUrl, { method: 'POST', credentials: 'same-origin', body: formData })
				.then((res) => res.json())
				.then((data) => {
					if (data && data.ok) {
						if (window.bootstrap) {
							const modal = window.bootstrap.Modal.getInstance(revokeModal);
							if (modal) modal.hide();
						}
						window.location.reload();
						return;
					}
					const msg = (data && data.message) ? data.message : 'Falha ao estornar.';
					window.alert(msg);
				})
				.catch(() => {
					window.alert('Falha ao estornar.');
				});
		});
	}
});

document.addEventListener('DOMContentLoaded', () => {
	const bars = document.querySelectorAll('.progress-bar[data-progress]');
	if (!bars.length) return;
	requestAnimationFrame(() => {
		bars.forEach((bar) => {
			const value = Number(bar.getAttribute('data-progress') || '0');
			const percent = Number.isFinite(value) ? Math.max(0, Math.min(100, value)) : 0;
			bar.style.width = percent + '%';
		});
	});
});

document.addEventListener('DOMContentLoaded', () => {
	const input = document.querySelector('#proofForm input[type="file"]');
	const btn = document.getElementById('proofSubmit');
	const err = document.getElementById('proofError');
	if (!input || !btn || !err) return;
	const max = 4 * 1024 * 1024;
	const allowed = ['image/jpeg', 'image/png', 'application/pdf', 'image/x-png'];
	input.addEventListener('change', () => {
		err.style.display = 'none';
		const file = input.files && input.files[0];
		if (!file) return;
		if (file.size > max) {
			err.textContent = 'Arquivo maior que 4MB.';
			err.style.display = 'block';
			return;
		}
		if (!allowed.includes(file.type)) {
			err.textContent = 'Tipo invalido. Envie JPG, PNG ou PDF.';
			err.style.display = 'block';
		}
	});
});

document.addEventListener('DOMContentLoaded', () => {
	const btn = document.getElementById('copyPixKey');
	const input = document.getElementById('pixKeyValue');
	if (!btn || !input) return;
	const copyViaExecCommand = (text) => {
		const ta = document.createElement('textarea');
		ta.value = text;
		ta.setAttribute('readonly', 'readonly');
		ta.style.position = 'fixed';
		ta.style.opacity = '0';
		ta.style.left = '-9999px';
		document.body.appendChild(ta);
		ta.focus();
		ta.select();
		ta.setSelectionRange(0, ta.value.length);
		const ok = document.execCommand('copy');
		document.body.removeChild(ta);
		return ok;
	};
	btn.addEventListener('click', async () => {
		const text = input.value || '';
		if (text === '') return;
		try {
			if (navigator.clipboard && window.isSecureContext) {
				await navigator.clipboard.writeText(text);
			} else {
				copyViaExecCommand(text);
			}
			btn.textContent = 'Copiado';
			setTimeout(() => {
				btn.textContent = 'Copiar';
			}, 2000);
		} catch (e) {
			const ok = copyViaExecCommand(text);
			btn.textContent = ok ? 'Copiado' : 'Falha';
			setTimeout(() => {
				btn.textContent = 'Copiar';
			}, 2000);
		}
	});
});

document.addEventListener('DOMContentLoaded', () => {
	const selectAllBulk = document.getElementById('selectAllBulk');
	const approveForm = document.getElementById('bulkApproveForm');
	const deleteForm = document.getElementById('bulkDeleteForm');
	const approveBtn = document.getElementById('bulkApproveBtn');
	const deleteBtn = document.getElementById('bulkDeleteBtn');
	const modalEl = document.getElementById('bulkActionModal');
	const modalTitle = document.getElementById('bulkActionTitle');
	const modalMessage = document.getElementById('bulkActionMessage');
	const modalList = document.getElementById('bulkActionList');
	const modalConfirm = document.getElementById('bulkActionConfirm');
	const modal = modalEl && window.bootstrap ? new window.bootstrap.Modal(modalEl) : null;
	const canUseModal = !!(modal && modalTitle && modalMessage && modalList && modalConfirm);

	if (selectAllBulk) {
		selectAllBulk.addEventListener('change', () => {
			document.querySelectorAll('.bulk-select-checkbox, .bulk-pending-checkbox').forEach((cb) => {
				cb.checked = selectAllBulk.checked;
			});
		});
	}

	const buildList = (items) => {
		modalList.innerHTML = '';
		items.forEach((item) => {
			const li = document.createElement('li');
			li.className = 'list-group-item d-flex justify-content-between align-items-center';
			li.textContent = item.label;
			const badge = document.createElement('span');
			badge.className = 'badge bg-light text-muted border';
			badge.textContent = '#' + item.id;
			li.appendChild(badge);
			modalList.appendChild(li);
		});
	};
	const collectSelected = (selector) => {
		return Array.from(document.querySelectorAll(selector)).map((cb) => ({
			id: cb.value,
			label: cb.getAttribute('data-label') || cb.value,
		}));
	};
	const showModal = (config) => {
		if (canUseModal) {
			modalTitle.textContent = config.title;
			modalMessage.textContent = config.message;
			buildList(config.items);
			modalConfirm.textContent = config.confirmLabel;
			modalConfirm.className = 'btn ' + config.confirmClass;
			modalConfirm.onclick = config.onConfirm;
			modalConfirm.disabled = config.items.length === 0;
			modal.show();
			return;
		}
		if (!config.items.length) {
			window.alert(config.message);
			return;
		}
		const list = config.items.map((item) => `#${item.id} ${item.label}`).join('\n');
		const message = `${config.message}\n\n${list}`;
		if (window.confirm(message)) {
			config.onConfirm();
		}
	};

	const buildAndSubmit = (form, items) => {
		form.querySelectorAll('input[name="ids[]"][data-generated="1"]').forEach((el) => el.remove());
		items.forEach((item) => {
			const hidden = document.createElement('input');
			hidden.type = 'hidden';
			hidden.name = 'ids[]';
			hidden.value = item.id;
			hidden.setAttribute('data-generated', '1');
			form.appendChild(hidden);
		});
		form.setAttribute('data-confirmed', '1');
		form.submit();
	};

	if (approveForm) {
		approveForm.addEventListener('submit', (event) => {
			if (approveForm.getAttribute('data-confirmed') === '1') return;
			event.preventDefault();
			const items = collectSelected('.bulk-pending-checkbox:checked');
			showModal({
				title: 'Confirmar liberacao',
				message: items.length ? 'Liberar os seguintes uploads pendentes?' : 'Nenhum pendente selecionado.',
				items,
				confirmLabel: 'Liberar',
				confirmClass: 'btn-success',
				onConfirm: () => buildAndSubmit(approveForm, items),
			});
		});
	}

	if (deleteForm) {
		deleteForm.addEventListener('submit', (event) => {
			if (deleteForm.getAttribute('data-confirmed') === '1') return;
			event.preventDefault();
			const items = collectSelected('.bulk-select-checkbox:checked');
			showModal({
				title: 'Confirmar remocao',
				message: items.length ? 'Remover os uploads selecionados?' : 'Nenhum upload selecionado.',
				items,
				confirmLabel: 'Remover',
				confirmClass: 'btn-danger',
				onConfirm: () => buildAndSubmit(deleteForm, items),
			});
		});
	}

	if (approveBtn) {
		approveBtn.addEventListener('click', (event) => {
			event.preventDefault();
			approveForm?.requestSubmit();
		});
	}

	if (deleteBtn) {
		deleteBtn.addEventListener('click', (event) => {
			event.preventDefault();
			deleteForm?.requestSubmit();
		});
	}
});

document.addEventListener('DOMContentLoaded', () => {
	const rows = document.querySelectorAll('[data-select-row]');
	if (!rows.length) return;
	rows.forEach((row) => {
		row.addEventListener('click', (event) => {
			const target = event.target;
			if (target && target.closest('a, button, input, select, textarea, label')) {
				return;
			}
			const checkbox = row.querySelector('.bulk-select-checkbox');
			if (!checkbox) return;
			checkbox.checked = !checkbox.checked;
			checkbox.dispatchEvent(new Event('change', { bubbles: true }));
		});
	});
});

// Mobile search toggle
document.addEventListener('DOMContentLoaded', () => {
	const searchBtn = document.querySelector('[data-mobile-search-toggle]');
	const searchForm = document.querySelector('.navbar-search');
	const closeBtn = document.querySelector('[data-mobile-search-close]');
	const searchInput = searchForm?.querySelector('input[name="q"]');

	if (!searchBtn || !searchForm) return;

	const openSearch = () => {
		searchForm.classList.add('active');
		setTimeout(() => searchInput?.focus(), 100);
	};

	const closeSearch = () => {
		searchForm.classList.remove('active');
		if (searchInput) searchInput.value = '';
	};

	searchBtn.addEventListener('click', openSearch);
	closeBtn?.addEventListener('click', closeSearch);

	// Fechar ao pressionar ESC
	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && searchForm.classList.contains('active')) {
			closeSearch();
		}
	});
});

