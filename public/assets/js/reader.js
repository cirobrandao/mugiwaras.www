document.addEventListener('DOMContentLoaded', () => {
  const readerEl = document.getElementById('reader');
  const total = readerEl ? parseInt(readerEl.dataset.total || '0', 10) : (window.__READER && window.__READER.total) || 0;
  let page = 0;
  const img = document.getElementById('readerImage');
  const info = document.getElementById('pageInfo');
  const prev = document.getElementById('prevPage');
  const next = document.getElementById('nextPage');
  const pageInput = document.getElementById('pageNumber');
  const pageTotal = document.getElementById('pageTotal');
  const prevFooter = document.getElementById('prevPageFooter');
  const nextFooter = document.getElementById('nextPageFooter');
  const lastBtn = document.getElementById('readerLast');
  const status = document.getElementById('readerStatus');
  const fitMode = document.getElementById('readerFitMode');
  const zoomInput = document.getElementById('readerZoom');
  const progress = document.getElementById('readerProgress');
  const expandBtn = document.getElementById('readerExpand');
  const modeSelect = document.getElementById('readerMode');
  const modeSelectMobile = document.getElementById('readerModeMobile');
  const overlay = document.getElementById('readerOverlay');
  const firstBtn = document.getElementById('readerFirst');
  const wheelToggle = document.getElementById('readerWheel');
  const wheelWrap = wheelToggle ? wheelToggle.closest('.form-check') : null;
  const wrap = document.getElementById('readerWrap');
  const scrollTopBtn = document.getElementById('scrollTopBtn');
  const bottomTopBtn = document.getElementById('readerBottomTop');
  const pageCompact = document.getElementById('pageCompact');
  const pageGuide = document.getElementById('readerPageGuide');
  const endActions = document.getElementById('readerEndActions');
  const prevChapterUrl = readerEl ? (readerEl.dataset.previousChapterUrl || '') : '';
  const nextChapterUrl = readerEl ? (readerEl.dataset.nextChapterUrl || '') : '';
  const isRtl = readerEl ? (readerEl.dataset.direction || 'rtl') === 'rtl' : false;
  let scrollMode = modeSelect ? modeSelect.value === 'scroll' : (modeSelectMobile ? modeSelectMobile.value === 'scroll' : false);
  let wheelPaging = wheelToggle ? wheelToggle.checked : true;
  const isMobile = window.matchMedia && window.matchMedia('(max-width: 768px)').matches;

  // Cookie helpers for storing reader preferences
  const setCookie = (name, value, days = 365) => {
    try {
      const d = new Date();
      d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
      document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)};expires=${d.toUTCString()};path=/`;
    } catch (e) {}
  };
  const getCookie = (name) => {
    try {
      const match = document.cookie.match(new RegExp('(?:^|; )' + encodeURIComponent(name) + '=([^;]*)'));
      return match ? decodeURIComponent(match[1]) : null;
    } catch (e) {
      return null;
    }
  };

  const loadSavedSettings = () => {
    try {
      const fit = getCookie('reader_fit_mode');
      if (fit && fitMode) fitMode.value = fit;
      const mode = getCookie('reader_mode');
      if (mode && modeSelect) modeSelect.value = mode;
      if (mode && modeSelectMobile) modeSelectMobile.value = mode;
      const zoom = getCookie('reader_zoom');
      if (zoom && zoomInput) zoomInput.value = String(Math.min(160, Math.max(60, parseInt(zoom, 10) || 100)));
      const wheel = getCookie('reader_wheel');
      if (wheel !== null && wheelToggle) wheelToggle.checked = (wheel === '1' || wheel === 'true');
      // update derived state
      scrollMode = modeSelect ? modeSelect.value === 'scroll' : (modeSelectMobile ? modeSelectMobile.value === 'scroll' : scrollMode);
      wheelPaging = wheelToggle ? wheelToggle.checked : wheelPaging;
    } catch (e) {}
  };

  const syncWheelToggle = () => {
    if (!wheelToggle) return;
    if (scrollMode) {
      wheelPaging = false;
      wheelToggle.checked = false;
      wheelToggle.disabled = true;
      if (wheelWrap) wheelWrap.classList.add('d-none');
    } else {
      wheelToggle.disabled = false;
      if (wheelWrap) wheelWrap.classList.remove('d-none');
      wheelPaging = wheelToggle.checked;
    }
  };

  const updateScrollTopVisibility = () => {
    if (!scrollTopBtn || !readerEl) return;
    if (!scrollMode) {
      scrollTopBtn.classList.add('d-none');
      return;
    }
    // show when the reader content is scrollable (height exceeds viewport) — or if user scrolled
    const canScroll = readerEl.scrollHeight > (readerEl.clientHeight + 20);
    const scrolled = readerEl.scrollTop > 80;
    if (canScroll || scrolled) {
      scrollTopBtn.classList.remove('d-none');
    } else {
      scrollTopBtn.classList.add('d-none');
    }
  };
  const syncPageGuide = () => {
    if (scrollMode) {
      pageGuide?.classList.add('d-none');
      pageCompact?.classList.add('d-none');
      endActions?.classList.remove('d-none');
      // hide footer prev/next buttons in scroll mode
      if (prevFooter) prevFooter.classList.add('d-none');
      if (nextFooter) nextFooter.classList.add('d-none');
    } else {
      pageGuide?.classList.remove('d-none');
      pageCompact?.classList.add('d-none');
      endActions?.classList.add('d-none');
      // show footer prev/next buttons in page mode (desktop only)
      if (prevFooter) prevFooter.classList.remove('d-none');
      if (nextFooter) nextFooter.classList.remove('d-none');
    }
  };
  let saveTimer = null;
  let readMarked = false;

  const setStatus = (text) => {
    if (status) status.textContent = text || '\u00A0';
    if (overlay) {
      if (text) {
        overlay.textContent = text;
        overlay.classList.remove('d-none');
      } else {
        overlay.classList.add('d-none');
      }
    }
  };

  const clampPage = (value) => {
    if (Number.isNaN(value)) return 0;
    if (value < 0) return 0;
    if (value > total - 1) return Math.max(0, total - 1);
    return value;
  };

  const applyFitMode = () => {
    if (!img || !readerEl) return;
    const mode = fitMode ? fitMode.value : 'height';
    img.style.width = '';
    img.style.height = '';
    img.style.maxWidth = '';
    img.style.maxHeight = '';
    // Page mode: keep page fixed and constrain height to viewport (show original by default)
    if (!scrollMode) {
      readerEl.classList.add('page-fixed');
      readerEl.style.display = 'flex';
      readerEl.style.justifyContent = 'center';
      readerEl.style.alignItems = 'center';
      readerEl.style.overflow = 'hidden';
      // keep original dimensions but prevent overflow
      if (mode === 'width') {
        img.style.width = '100%';
        img.style.height = 'auto';
        img.style.maxHeight = 'calc(100vh - 160px)';
      } else if (mode === 'height') {
        img.style.maxHeight = 'calc(100vh - 160px)';
        img.style.width = 'auto';
      } else {
        img.style.width = 'auto';
        img.style.height = 'auto';
        img.style.maxHeight = 'calc(100vh - 160px)';
        img.style.maxWidth = 'none';
      }
    } else {
      // Scroll mode: regular behaviour
      readerEl.classList.remove('page-fixed');
      readerEl.style.display = '';
      readerEl.style.justifyContent = '';
      readerEl.style.alignItems = '';
      readerEl.style.overflow = '';
      if (mode === 'height') {
        img.style.maxHeight = '80vh';
        img.style.width = 'auto';
      } else if (mode === 'original') {
        img.style.width = 'auto';
        img.style.height = 'auto';
      } else {
        img.style.width = '100%';
        img.style.height = 'auto';
      }
    }
  };

  const applyScrollDefaults = () => {
    if (!fitMode) return;
    // When in page mode (no scroll) fix the page and show original-ish sizing by default
    if (!scrollMode) {
      fitMode.value = 'original';
      applyFitMode();
    }
  };

  const renderChapterControls = () => {
    // Chapter controls removed: if an existing element was injected previously, remove it.
    const existing = document.getElementById('chapterControls');
    if (existing) existing.remove();
    // intentionally do not recreate chapter controls here
  };

  const applyZoom = () => {
    if (!img) return;
    const z = zoomInput ? parseInt(zoomInput.value || '100', 10) : 100;
    img.style.transform = `scale(${z / 100})`;
    img.style.transformOrigin = 'center top';
  };

  const goNext = () => {
    if (page < total - 1) {
      page += 1;
      update();
    }
  };

  const goPrev = () => {
    if (page > 0) {
      page -= 1;
      update();
    }
  };

  const update = () => {
    if (!img) return;
    const baseUrl = readerEl ? readerEl.dataset.baseUrl : (window.__READER && window.__READER.baseUrl);
    if (baseUrl) {
      const query = readerEl ? (readerEl.dataset.query || '') : (window.__READER && window.__READER.query) || '';
      img.src = `${baseUrl}/${page}${query}`;
    } else {
      img.src = `${window.location.origin}${window.location.pathname}/page/${page}`;
    }
    if (info) info.textContent = `${page + 1} / ${total}`;
    if (pageInput) pageInput.value = String(page + 1);
    if (pageTotal) pageTotal.textContent = `/ ${total}`;
    if (pageCompact) pageCompact.textContent = `${page + 1}/${total}`;
    if (prev) prev.disabled = page <= 0;
    if (next) next.disabled = page >= total - 1;
    if (prevFooter) prevFooter.disabled = page <= 0;
    if (nextFooter) nextFooter.disabled = page >= total - 1;
    if (lastBtn) lastBtn.disabled = page >= total - 1;
    if (progress) progress.style.width = `${total > 0 ? Math.round(((page + 1) / total) * 100) : 0}%`;
    setStatus('Carregando...');
    applyFitMode();
    applyZoom();
    scheduleSaveProgress();
    markReadIfLast();
    if (typeof updateMobileDisplay === 'function') updateMobileDisplay();
  };

  const scheduleSaveProgress = () => {
    if (!readerEl) return;
    const contentId = parseInt(readerEl.dataset.contentId || '0', 10);
    const csrf = readerEl.dataset.csrf || '';
    if (!contentId || !csrf) return;
    if (saveTimer) window.clearTimeout(saveTimer);
    saveTimer = window.setTimeout(() => {
      const form = new FormData();
      form.append('_csrf', csrf);
      form.append('id', String(contentId));
      form.append('page', String(page));
      fetch('/libraries/progress', { method: 'POST', body: form, credentials: 'same-origin' }).catch(() => {});
    }, 300);
  };

  const markReadIfLast = () => {
    if (!readerEl || readMarked || total <= 0) return;
    if (page < total - 1) return;
    const contentId = parseInt(readerEl.dataset.contentId || '0', 10);
    const csrf = readerEl.dataset.csrf || '';
    if (!contentId || !csrf) return;
    const form = new FormData();
    form.append('_csrf', csrf);
    form.append('id', String(contentId));
    form.append('read', '1');
    readMarked = true;
    fetch('/libraries/read', { method: 'POST', body: form, credentials: 'same-origin' }).catch(() => {
      readMarked = false;
    });
  };

  const renderScrollMode = () => {
    if (!readerEl || !img) return;
    readerEl.classList.toggle('scroll-mode', scrollMode);
    // ensure any page-mode inline styles are cleared when switching to scroll
    try { applyFitMode(); } catch (e) {}
    if (!scrollMode) {
      update();
      return;
    }
    readerEl.innerHTML = '';
    for (let i = 0; i < total; i += 1) {
      const im = document.createElement('img');
      const baseUrl = readerEl.dataset.baseUrl;
      const query = readerEl.dataset.query || '';
      im.src = `${baseUrl}/${i}${query}`;
      im.alt = `Página ${i + 1}`;
      readerEl.appendChild(im);
    }
    setStatus('');
    // compute visibility for scroll-top overlay after images render
    setTimeout(() => { if (typeof updateScrollTopVisibility === 'function') updateScrollTopVisibility(); }, 80);
  };

  const applyMobileMode = () => {
    if (!isMobile) return;
    document.body.classList.add('reader-mobile');
    if (modeSelectMobile && modeSelect) {
      modeSelectMobile.value = modeSelect.value;
    }
    scrollMode = modeSelect ? modeSelect.value === 'scroll' : (modeSelectMobile ? modeSelectMobile.value === 'scroll' : false);
    if (wheelWrap) wheelWrap.classList.add('d-none');
    syncWheelToggle();
    syncPageGuide();
    applyScrollDefaults();
    renderScrollMode();
  };

  if (fitMode) {
    fitMode.addEventListener('change', () => {
      setCookie('reader_fit_mode', fitMode.value);
      applyFitMode();
    });
  }
  if (zoomInput) {
    zoomInput.addEventListener('input', () => {
      setCookie('reader_zoom', String(zoomInput.value));
      applyZoom();
    });
  }

  if (img) {
    img.addEventListener('load', () => setStatus(''));
    img.addEventListener('error', () => setStatus('Falha ao carregar a página.'));
    img.addEventListener('click', (e) => {
      if (scrollMode) return;
      if (e.shiftKey) {
        goPrev();
        return;
      }
      const rect = img.getBoundingClientRect();
      const isLeft = (e.clientX - rect.left) < rect.width / 2;
      if (isRtl) {
        if (isLeft) goNext(); else goPrev();
      } else {
        if (isLeft) goPrev(); else goNext();
      }
    });
  }

  if (prev) prev.addEventListener('click', () => {
    goPrev();
  });
  if (next) next.addEventListener('click', () => {
    goNext();
  });
  if (prevFooter) prevFooter.addEventListener('click', () => { goPrev(); });
  if (nextFooter) nextFooter.addEventListener('click', () => { goNext(); });

  // Mobile controls support
  const prevPageMobile = document.getElementById('prevPageMobile');
  const nextPageMobile = document.getElementById('nextPageMobile');
  const readerProgressMobile = document.getElementById('readerProgressMobile');
  const pageNumberDisplay = document.getElementById('pageNumberDisplay');
  const pageTotalDisplay = document.getElementById('pageTotalDisplay');
  const tapZoneLeft = document.getElementById('tapZoneLeft');
  const tapZoneRight = document.getElementById('tapZoneRight');

  if (prevPageMobile) prevPageMobile.addEventListener('click', () => { goPrev(); });
  if (nextPageMobile) nextPageMobile.addEventListener('click', () => { goNext(); });

  // Mobile tap zones (tap left side = prev, tap right side = next)
  // For RTL (manga), reverse the behavior
  if (tapZoneLeft) {
    tapZoneLeft.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (isRtl) {
        goNext(); // RTL: left tap goes to next page
      } else {
        goPrev(); // LTR: left tap goes to previous page
      }
    });
  }
  if (tapZoneRight) {
    tapZoneRight.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      if (isRtl) {
        goPrev(); // RTL: right tap goes to previous page
      } else {
        goNext(); // LTR: right tap goes to next page
      }
    });
  }

  // Update mobile displays alongside main update
  const updateMobileDisplay = () => {
    if (pageNumberDisplay) pageNumberDisplay.textContent = String(page + 1);
    if (pageTotalDisplay) pageTotalDisplay.textContent = String(total);
    if (readerProgressMobile) readerProgressMobile.style.width = `${total > 0 ? Math.round(((page + 1) / total) * 100) : 0}%`;
    if (prevPageMobile) prevPageMobile.disabled = page <= 0;
    if (nextPageMobile) nextPageMobile.disabled = page >= total - 1;
  };
 

  if (pageInput) {
    const onPageChange = () => {
      const value = parseInt(pageInput.value || '1', 10) - 1;
      page = clampPage(value);
      update();
    };
    pageInput.addEventListener('change', onPageChange);
    pageInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        onPageChange();
      }
    });
  }

  document.addEventListener('keydown', (e) => {
    const tag = (document.activeElement && document.activeElement.tagName || '').toLowerCase();
    if (tag === 'input' || tag === 'textarea' || tag === 'select') return;
    if (e.key === 'Escape') {
      if (document.body.classList.contains('reader-expanded')) {
        document.body.classList.remove('reader-expanded');
        if (wrap) wrap.classList.remove('is-expanded');
      }
      return;
    }
    if (scrollMode && readerEl) {
      if (e.key === 'Home') {
        e.preventDefault();
        readerEl.scrollTo({ top: 0, behavior: 'smooth' });
        return;
      }
      if (e.key === 'End') {
        e.preventDefault();
        readerEl.scrollTo({ top: readerEl.scrollHeight, behavior: 'smooth' });
        return;
      }
      if (e.key === 'PageDown' || e.key === ' ') {
        e.preventDefault();
        readerEl.scrollBy({ top: readerEl.clientHeight * 0.9, behavior: 'smooth' });
        return;
      }
      if (e.key === 'PageUp') {
        e.preventDefault();
        readerEl.scrollBy({ top: -readerEl.clientHeight * 0.9, behavior: 'smooth' });
        return;
      }
    }
    if (e.key === '+') {
      if (zoomInput) {
        zoomInput.value = String(Math.min(160, parseInt(zoomInput.value || '100', 10) + 5));
        applyZoom();
      }
    }
    if (e.key === '-') {
      if (zoomInput) {
        zoomInput.value = String(Math.max(60, parseInt(zoomInput.value || '100', 10) - 5));
        applyZoom();
      }
    }
    if (e.key === 'ArrowLeft') {
      if (isRtl) goNext(); else goPrev();
    }
    if (e.key === 'ArrowRight') {
      if (isRtl) goPrev(); else goNext();
    }
    if (e.key === 'Home') {
      e.preventDefault();
      page = 0;
      update();
    }
    if (e.key === 'PageUp') {
      goPrev();
    }
    if (e.key === 'PageDown' || e.key === ' ') {
      e.preventDefault();
      goNext();
    }
    if (e.key === 'End') {
      e.preventDefault();
      page = Math.max(0, total - 1);
      update();
    }
  });

  if (readerEl) {
    const last = parseInt(readerEl.dataset.lastPage || '0', 10);
    const params = new URLSearchParams(window.location.search);
    const paramPage = params.get('page');
    if (paramPage !== null) {
      const p = parseInt(paramPage, 10);
      if (!Number.isNaN(p)) page = clampPage(p);
    } else if (!Number.isNaN(last)) {
      page = clampPage(last);
    }
  }

  // load saved reader settings from cookies (fit, mode, zoom, wheel)
  loadSavedSettings();

  update();
  applyScrollDefaults();
  syncPageGuide();
  renderChapterControls();
  syncWheelToggle();
  if (scrollMode) {
    renderScrollMode();
  }

  if (modeSelect) {
    modeSelect.addEventListener('change', () => {
      scrollMode = modeSelect.value === 'scroll';
      setCookie('reader_mode', modeSelect.value);
      if (modeSelectMobile) modeSelectMobile.value = modeSelect.value;
      syncWheelToggle();
      syncPageGuide();
      applyScrollDefaults();
      if (scrollTopBtn) { if (scrollMode) setTimeout(() => { try { updateScrollTopVisibility(); } catch(e){} }, 80); else scrollTopBtn.classList.add('d-none'); }
      if (scrollMode) {
        renderScrollMode();
      } else {
        readerEl.innerHTML = '';
        readerEl.appendChild(img);
        update();
      }
      renderChapterControls();
    });
  }
  // scroll-top button behavior (overlay)
  if (scrollTopBtn) {
    scrollTopBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (readerEl) readerEl.scrollTo({ top: 0, behavior: 'smooth' });
      else window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    setTimeout(() => { try { if (typeof updateScrollTopVisibility === 'function') updateScrollTopVisibility(); } catch (e) {} }, 120);
  }
  if (modeSelectMobile) {
    modeSelectMobile.addEventListener('change', () => {
      scrollMode = modeSelectMobile.value === 'scroll';
      setCookie('reader_mode', modeSelectMobile.value);
      if (modeSelect) modeSelect.value = modeSelectMobile.value;
      syncWheelToggle();
      syncPageGuide();
      applyScrollDefaults();
      if (scrollTopBtn) { if (scrollMode) setTimeout(() => { try { updateScrollTopVisibility(); } catch(e){} }, 80); else scrollTopBtn.classList.add('d-none'); }
      if (scrollMode) {
        renderScrollMode();
      } else {
        readerEl.innerHTML = '';
        readerEl.appendChild(img);
        update();
      }
      renderChapterControls();
    });
  }

  if (wheelToggle) {
    wheelToggle.addEventListener('change', () => {
      wheelPaging = wheelToggle.checked;
      setCookie('reader_wheel', wheelToggle.checked ? '1' : '0');
    });
  }

  if (firstBtn) {
    firstBtn.addEventListener('click', () => {
      page = 0;
      update();
      syncWheelToggle();
      syncPageGuide();
      if (readerEl) readerEl.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  if (lastBtn) {
    lastBtn.addEventListener('click', () => {
      page = Math.max(0, total - 1);
      update();
      syncWheelToggle();
      syncPageGuide();
      if (readerEl) readerEl.scrollTo({ top: readerEl.scrollHeight, behavior: 'smooth' });
    });
  }

  if (readerEl) {
    readerEl.addEventListener('wheel', (e) => {
      if (scrollMode || !wheelPaging) return;
      e.preventDefault();
      if (e.deltaY > 10 && page < total - 1) {
        goNext();
      } else if (e.deltaY < -10 && page > 0) {
        goPrev();
      }
    }, { passive: false });
    readerEl.addEventListener('scroll', () => {
      if (!scrollMode || !readerEl || total <= 0) return;
      const ratio = readerEl.scrollTop / Math.max(1, readerEl.scrollHeight - readerEl.clientHeight);
      const newPage = Math.min(total - 1, Math.max(0, Math.floor(ratio * total)));
      if (newPage !== page) {
        page = newPage;
        scheduleSaveProgress();
        markReadIfLast();
        if (info) info.textContent = `${page + 1} / ${total}`;
        if (pageInput) pageInput.value = String(page + 1);
        if (pageTotal) pageTotal.textContent = `/ ${total}`;
        if (pageCompact) pageCompact.textContent = `${page + 1}/${total}`;
        if (progress) progress.style.width = `${Math.round(((page + 1) / total) * 100)}%`;
        if (typeof updateMobileDisplay === 'function') updateMobileDisplay();
      }
      // update scroll-top overlay visibility while scrolling
      try { updateScrollTopVisibility(); } catch (e) {}
      
    });
  }


  if (expandBtn) {
    expandBtn.addEventListener('click', () => {
      document.body.classList.toggle('reader-expanded');
      if (wrap) wrap.classList.toggle('is-expanded');
      setTimeout(() => {
        if (typeof updateScrollTopVisibility === 'function') updateScrollTopVisibility();
      }, 60);
    });
  }

  if (bottomTopBtn) {
    bottomTopBtn.addEventListener('click', (e) => {
      e.preventDefault();
      if (readerEl) {
        readerEl.scrollTo({ top: 0, behavior: 'smooth' });
      }
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  const adjustTitleBounds = () => {
    const title = document.querySelector('.reader-title');
    if (!title) return;
    title.style.left = '';
    title.style.right = '';
    title.style.maxWidth = '';
    title.style.width = '';
    title.style.transform = '';
  };
  setTimeout(adjustTitleBounds, 60);
});
