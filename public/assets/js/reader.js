/**
 * Reader.js - Premium CBZ/Manga Reader
 * 
 * Features:
 * - Page mode: Single page navigation with preloading
 * - Scroll mode: Continuous scroll with lazy loading
 * - Mobile support: Swipe gestures and tap zones
 * - Keyboard shortcuts:
 *   - Arrow Left/Right: Navigate pages (respects RTL/LTR)
 *   - Home/End: First/Last page (page mode) or scroll to top/bottom (scroll mode)
 *   - Page Up/Down, Space: Navigate pages
 *   - +/=: Zoom in
 *   - -/_: Zoom out
 *   - 0: Reset zoom to 100%
 *   - F: Toggle fullscreen
 *   - Escape: Exit fullscreen
 * - Auto-save reading progress
 * - Image retry on load failure
 * - RTL (manga) and LTR support
 */

document.addEventListener('DOMContentLoaded', () => {
  const readerEl = document.getElementById('reader');
  const total = readerEl ? parseInt(readerEl.dataset.total || '0', 10) : (window.__READER && window.__READER.total) || 0;
  let page = 0;
  const img = document.getElementById('readerImage');
  const pagesHost = document.getElementById('readerPagesHost');
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
  const expandBtnMobile = document.getElementById('readerExpandMobile');
  const exitFullscreenBtn = document.getElementById('readerExitFullscreen');
  const modeSelectMobile = document.getElementById('readerModeMobile');
  const modeToggleBtn = document.getElementById('readerModeToggle');
  const mobileNextChapterTop = document.getElementById('readerNextChapterTop');
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
  const readerFooter = document.getElementById('readerFooter');
  const prevChapterUrl = readerEl ? (readerEl.dataset.previousChapterUrl || '') : '';
  const nextChapterUrl = readerEl ? (readerEl.dataset.nextChapterUrl || '') : '';
  const isRtl = readerEl ? (readerEl.dataset.direction || 'rtl') === 'rtl' : false;
  let scrollMode = modeSelectMobile
    ? modeSelectMobile.value === 'scroll'
    : (modeToggleBtn ? modeToggleBtn.dataset.mode === 'scroll' : false);
  let wheelPaging = wheelToggle ? wheelToggle.checked : true;
  const isMobile = window.matchMedia && window.matchMedia('(max-width: 768px)').matches;

  // Mobile controls elements
  const mobileControlsPage = document.querySelector('.mobile-controls-page');
  const mobileControlsScroll = document.querySelector('.mobile-controls-scroll');
  
  // Mobile tap zones
  const tapZoneLeft = document.getElementById('tapZoneLeft');
  const tapZoneRight = document.getElementById('tapZoneRight');

  // Touch/Swipe support for mobile
  let touchStartX = 0;
  let touchStartY = 0;
  let touchEndX = 0;
  let touchEndY = 0;
  const minSwipeDistance = 50; // minimum distance for swipe to register

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

  // Toast notification system for reader actions
  let toastTimeout = null;
  const showToast = (message, duration = 2000) => {
    let toast = document.getElementById('readerToast');
    if (!toast) {
      toast = document.createElement('div');
      toast.id = 'readerToast';
      toast.className = 'reader-toast';
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.classList.add('show');
    
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
      toast.classList.remove('show');
    }, duration);
  };

  const loadSavedSettings = () => {
    try {
      const fit = getCookie('reader_fit_mode');
      if (fit && fitMode) fitMode.value = fit;
      const mode = getCookie('reader_mode');
      if (mode && modeSelectMobile) modeSelectMobile.value = mode;
      if (mode && modeToggleBtn) modeToggleBtn.dataset.mode = mode === 'scroll' ? 'scroll' : 'page';
      const zoom = getCookie('reader_zoom');
      if (zoom && zoomInput) zoomInput.value = String(Math.min(160, Math.max(60, parseInt(zoom, 10) || 100)));
      const wheel = getCookie('reader_wheel');
      if (wheel !== null && wheelToggle) wheelToggle.checked = (wheel === '1' || wheel === 'true');
      // update derived state
      scrollMode = modeSelectMobile
        ? modeSelectMobile.value === 'scroll'
        : (modeToggleBtn ? modeToggleBtn.dataset.mode === 'scroll' : scrollMode);
      wheelPaging = wheelToggle ? wheelToggle.checked : wheelPaging;
      syncModeToggleUi();
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

  const syncMobileControls = () => {
    if (!isMobile) return;
    // Toggle mobile controls based on scroll mode
    if (scrollMode) {
      // Show chapter navigation controls in scroll mode
      if (mobileControlsPage) mobileControlsPage.style.display = 'none';
      if (mobileControlsScroll) mobileControlsScroll.style.display = 'block';
    } else {
      // Show page navigation controls in page mode
      if (mobileControlsPage) mobileControlsPage.style.display = 'block';
      if (mobileControlsScroll) mobileControlsScroll.style.display = 'none';
    }
    // Toggle tap zones visibility based on scroll mode
    if (tapZoneLeft) tapZoneLeft.style.display = scrollMode ? 'none' : 'block';
    if (tapZoneRight) tapZoneRight.style.display = scrollMode ? 'none' : 'block';
    if (mobileNextChapterTop) mobileNextChapterTop.classList.toggle('d-none', scrollMode);
  };

  const syncModeToggleUi = () => {
    if (!modeToggleBtn) return;
    modeToggleBtn.dataset.mode = scrollMode ? 'scroll' : 'page';
    modeToggleBtn.textContent = isMobile
      ? (scrollMode ? 'Scroll' : 'Página')
      : (scrollMode ? 'Modo: Scroll' : 'Modo: Página');
    modeToggleBtn.setAttribute('aria-label', scrollMode ? 'Alternar para modo Página' : 'Alternar para modo Scroll');
  };

  const updateScrollTopVisibility = () => {
    if (!scrollTopBtn || !readerEl) return;
    if (!scrollMode) {
      scrollTopBtn.classList.add('d-none');
      return;
    }
    const getActiveScrollContainer = () => {
      if (!scrollMode || !readerEl) return window;
      if (document.fullscreenElement === readerEl) return readerEl;
      if (wrap && wrap.classList.contains('is-expanded')) return wrap;
      return window;
    };

    const scroller = getActiveScrollContainer();
    let canScroll = false;
    let scrolled = false;

    if (scroller === window) {
      canScroll = readerEl.scrollHeight > (window.innerHeight + 20);
      scrolled = window.scrollY > (readerEl.offsetTop + 80);
    } else {
      canScroll = scroller.scrollHeight > (scroller.clientHeight + 20);
      scrolled = scroller.scrollTop > 80;
    }
    if (canScroll || scrolled) {
      scrollTopBtn.classList.remove('d-none');
    } else {
      scrollTopBtn.classList.add('d-none');
    }
  };
  const syncPageGuide = () => {
    const isReaderExpanded = Boolean(
      (readerEl && document.fullscreenElement === readerEl) ||
      (wrap && wrap.classList.contains('is-expanded'))
    );
    if (scrollMode) {
      pageGuide?.classList.add('d-none');
      pageCompact?.classList.add('d-none');
      endActions?.classList.remove('d-none');
      if (readerFooter) {
        readerFooter.classList.toggle('scroll-pinned', isMobile);
        readerFooter.classList.toggle('d-none', !isMobile);
      }
      if (wrap) wrap.classList.toggle('has-pinned-footer', false);
      // hide footer prev/next buttons in scroll mode
      if (prevFooter) prevFooter.classList.add('d-none');
      if (nextFooter) nextFooter.classList.add('d-none');
    } else {
      pageGuide?.classList.remove('d-none');
      pageCompact?.classList.toggle('d-none', !isReaderExpanded);
      endActions?.classList.add('d-none');
      if (readerFooter) {
        readerFooter.classList.remove('scroll-pinned');
        readerFooter.classList.remove('d-none');
      }
      if (wrap) wrap.classList.remove('has-pinned-footer');
      // show footer prev/next buttons in page mode (desktop only)
      if (prevFooter) prevFooter.classList.remove('d-none');
      if (nextFooter) nextFooter.classList.remove('d-none');
    }
  };
  let saveTimer = null;
  let readMarked = false;
  let imageRetryCount = 0;
  const maxImageRetries = 3;

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

  const getPageModeMaxHeight = () => {
    if (!readerEl) return 520;
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    const toolbarEl = readerEl.querySelector('.reader-toolbar-modern');
    const headerEl = document.querySelector('.reader-modern-header');
    const footerVisible = readerFooter && !readerFooter.classList.contains('scroll-pinned');

    const toolbarHeight = toolbarEl ? toolbarEl.getBoundingClientRect().height : 0;
    const headerHeight = headerEl ? headerEl.getBoundingClientRect().height : 0;
    const footerHeight = footerVisible ? readerFooter.getBoundingClientRect().height : 0;
    const verticalGutters = 28;

    return Math.max(280, Math.round(viewportHeight - headerHeight - toolbarHeight - footerHeight - verticalGutters));
  };

  const getPageModeMaxWidth = () => {
    if (!readerEl) return Math.max(280, (window.innerWidth || 1280) - 24);
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth || readerEl.clientWidth || 0;
    const readerWidth = readerEl.clientWidth || viewportWidth;
    const usableWidth = Math.min(viewportWidth - 24, readerWidth - 12);
    return Math.max(280, Math.round(usableWidth));
  };

  const clampPage = (value) => {
    if (Number.isNaN(value)) return 0;
    if (value < 0) return 0;
    if (value > total - 1) return Math.max(0, total - 1);
    return value;
  };

  const preloadImage = (pageNum) => {
    if (pageNum < 0 || pageNum >= total || scrollMode) return;
    const baseUrl = readerEl ? readerEl.dataset.baseUrl : (window.__READER && window.__READER.baseUrl);
    if (!baseUrl) return;
    const query = readerEl ? (readerEl.dataset.query || '') : (window.__READER && window.__READER.query) || '';
    const preloadImg = new Image();
    preloadImg.src = `${baseUrl}/${pageNum}${query}`;
  };

  const preloadAdjacentPages = () => {
    if (scrollMode) return; // no need to preload in scroll mode as all images are rendered
    // preload next and previous pages for smoother navigation
    preloadImage(page + 1);
    preloadImage(page - 1);
  };

  const applyFitMode = () => {
    if (!img || !readerEl) return;
    const mode = fitMode ? fitMode.value : 'height';
    const targets = scrollMode
      ? Array.from((pagesHost || readerEl).querySelectorAll('img'))
      : [img];

    targets.forEach((target) => {
      target.style.width = '';
      target.style.height = '';
      target.style.maxWidth = '';
      target.style.maxHeight = '';
    });

    // Page mode: keep page fixed and constrain height to viewport (show original by default)
    if (!scrollMode) {
      const pageMaxHeight = getPageModeMaxHeight();
      const pageMaxWidth = getPageModeMaxWidth();
      readerEl.classList.add('page-fixed');
      readerEl.style.display = 'flex';
      readerEl.style.justifyContent = 'center';
      readerEl.style.alignItems = 'center';
      readerEl.style.overflow = 'hidden';
      // keep original dimensions but prevent overflow
      if (mode === 'width') {
        img.style.width = '100%';
        img.style.height = 'auto';
        img.style.maxHeight = `${pageMaxHeight}px`;
        img.style.maxWidth = `${pageMaxWidth}px`;
      } else if (mode === 'height') {
        img.style.maxHeight = `${pageMaxHeight}px`;
        img.style.width = 'auto';
        img.style.maxWidth = `${pageMaxWidth}px`;
      } else {
        img.style.width = 'auto';
        img.style.height = 'auto';
        img.style.maxHeight = `${pageMaxHeight}px`;
        img.style.maxWidth = `${pageMaxWidth}px`;
      }
    } else {
      // Scroll mode: regular behaviour
      readerEl.classList.remove('page-fixed');
      readerEl.style.display = '';
      readerEl.style.justifyContent = '';
      readerEl.style.alignItems = '';
      readerEl.style.overflow = '';
      if (mode === 'height') {
        targets.forEach((target) => {
          target.style.maxHeight = '80vh';
          target.style.width = 'auto';
          target.style.maxWidth = '100%';
        });
      } else if (mode === 'original') {
        targets.forEach((target) => {
          target.style.width = 'auto';
          target.style.height = 'auto';
          target.style.maxWidth = '100%';
        });
      } else {
        targets.forEach((target) => {
          target.style.width = '100%';
          target.style.height = 'auto';
          target.style.maxWidth = '100%';
        });
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
    let z = zoomInput ? parseInt(zoomInput.value || '100', 10) : 100;

    if (!scrollMode && readerEl) {
      const pageMaxHeight = getPageModeMaxHeight();
      const pageMaxWidth = getPageModeMaxWidth();
      const currentScaleMatch = (img.style.transform || '').match(/scale\(([^)]+)\)/);
      const currentScale = currentScaleMatch ? (parseFloat(currentScaleMatch[1]) || 1) : 1;
      const rect = img.getBoundingClientRect();
      const baseWidth = Math.max(1, rect.width / currentScale);
      const baseHeight = Math.max(1, rect.height / currentScale);
      const maxScaleByWidth = pageMaxWidth / baseWidth;
      const maxScaleByHeight = pageMaxHeight / baseHeight;
      const maxAllowedZoom = Math.max(60, Math.floor(Math.min(maxScaleByWidth, maxScaleByHeight) * 100));

      if (z > maxAllowedZoom) {
        z = maxAllowedZoom;
        if (zoomInput) zoomInput.value = String(z);
      }
    }

    const targets = scrollMode
      ? Array.from((pagesHost || readerEl).querySelectorAll('img'))
      : [img];
    targets.forEach((target) => {
      target.style.transform = `scale(${z / 100})`;
      target.style.transformOrigin = 'center top';
    });
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
    
    // Add loading state
    if (img) img.classList.add('loading');
    
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
    preloadAdjacentPages(); // preload next/prev pages for smoother navigation
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
    const host = pagesHost || readerEl;
    readerEl.classList.toggle('scroll-mode', scrollMode);
    // Toggle scroll-active class on wrap container for CSS styling
    if (wrap) wrap.classList.toggle('scroll-active', scrollMode);
    // ensure any page-mode inline styles are cleared when switching to scroll
    try { applyFitMode(); } catch (e) {}
    if (!scrollMode) {
      // restore page mode host/state
      if (readerEl) {
        readerEl.scrollTop = 0;
      }
      host.innerHTML = '';
      host.appendChild(img);
      update();
      return;
    }
    host.innerHTML = '';
    for (let i = 0; i < total; i += 1) {
      const im = document.createElement('img');
      const baseUrl = readerEl.dataset.baseUrl;
      const query = readerEl.dataset.query || '';
      im.src = `${baseUrl}/${i}${query}`;
      im.alt = `Página ${i + 1}`;
      im.loading = 'lazy'; // Enable native lazy loading for better performance
      im.classList.add('reader-scroll-image');
      host.appendChild(im);
    }
    setStatus('');
    applyFitMode();
    applyZoom();
    // compute visibility for scroll-top overlay after images render
    setTimeout(() => { if (typeof updateScrollTopVisibility === 'function') updateScrollTopVisibility(); }, 80);
  };

  const applyMobileMode = () => {
    if (!isMobile) return;
    document.body.classList.add('reader-mobile');
    scrollMode = modeSelectMobile
      ? modeSelectMobile.value === 'scroll'
      : (modeToggleBtn ? modeToggleBtn.dataset.mode === 'scroll' : false);
    if (wheelWrap) wheelWrap.classList.add('d-none');
    syncWheelToggle();
    syncMobileControls();
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
    img.addEventListener('load', () => {
      setStatus('');
      if (img) img.classList.remove('loading');
      imageRetryCount = 0; // reset retry count on successful load
    });
    img.addEventListener('error', () => {
      if (imageRetryCount < maxImageRetries) {
        imageRetryCount++;
        setStatus(`Tentando recarregar... (${imageRetryCount}/${maxImageRetries})`);
        // Retry loading the image after a short delay
        setTimeout(() => {
          if (img) {
            const currentSrc = img.src;
            img.src = ''; // Clear src to force reload
            img.src = currentSrc + (currentSrc.includes('?') ? '&' : '?') + '_retry=' + Date.now();
          }
        }, 1000 * imageRetryCount); // Exponential backoff
      } else {
        setStatus('Falha ao carregar a página. Tente novamente.');
        if (img) img.classList.remove('loading');
        imageRetryCount = 0;
      }
    });
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

  if (prevPageMobile) {
    prevPageMobile.addEventListener('click', (e) => { e.preventDefault(); goPrev(); });
    prevPageMobile.addEventListener('touchend', (e) => { e.preventDefault(); goPrev(); }, { passive: false });
  }
  if (nextPageMobile) {
    nextPageMobile.addEventListener('click', (e) => { e.preventDefault(); goNext(); });
    nextPageMobile.addEventListener('touchend', (e) => { e.preventDefault(); goNext(); }, { passive: false });
  }

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

  // Swipe gesture support for mobile in page mode
  const handleSwipe = () => {
    if (scrollMode) return; // disable swipe in scroll mode
    const deltaX = touchEndX - touchStartX;
    const deltaY = touchEndY - touchStartY;
    // Only register horizontal swipes (ignore vertical scrolling)
    if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > minSwipeDistance) {
      if (deltaX > 0) {
        // Swipe right
        if (isRtl) goNext(); else goPrev();
      } else {
        // Swipe left
        if (isRtl) goPrev(); else goNext();
      }
    }
  };

  if (isMobile && img) {
    img.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
      touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });

    img.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      touchEndY = e.changedTouches[0].screenY;
      handleSwipe();
    }, { passive: true });
  }
 

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
    const getActiveScrollContainer = () => {
      if (!scrollMode || !readerEl) return window;
      if (document.fullscreenElement === readerEl) return readerEl;
      if (wrap && wrap.classList.contains('is-expanded')) return wrap;
      return window;
    };
    if (scrollMode && readerEl) {
      const scroller = getActiveScrollContainer();
      if (e.key === 'Home') {
        e.preventDefault();
        if (scroller === window) {
          window.scrollTo({ top: Math.max(0, readerEl.offsetTop - 8), behavior: 'smooth' });
        } else {
          scroller.scrollTo({ top: 0, behavior: 'smooth' });
        }
        return;
      }
      if (e.key === 'End') {
        e.preventDefault();
        if (scroller === window) {
          const readerBottom = readerEl.offsetTop + readerEl.scrollHeight;
          window.scrollTo({ top: Math.max(0, readerBottom - window.innerHeight), behavior: 'smooth' });
        } else {
          scroller.scrollTo({ top: scroller.scrollHeight, behavior: 'smooth' });
        }
        return;
      }
      if (e.key === 'PageDown' || e.key === ' ') {
        e.preventDefault();
        if (scroller === window) {
          window.scrollBy({ top: window.innerHeight * 0.9, behavior: 'smooth' });
        } else {
          scroller.scrollBy({ top: scroller.clientHeight * 0.9, behavior: 'smooth' });
        }
        return;
      }
      if (e.key === 'PageUp') {
        e.preventDefault();
        if (scroller === window) {
          window.scrollBy({ top: -window.innerHeight * 0.9, behavior: 'smooth' });
        } else {
          scroller.scrollBy({ top: -(scroller.clientHeight * 0.9), behavior: 'smooth' });
        }
        return;
      }
    }
    if (e.key === '+' || e.key === '=') {
      e.preventDefault();
      if (zoomInput) {
        const newZoom = Math.min(160, parseInt(zoomInput.value || '100', 10) + 5);
        zoomInput.value = String(newZoom);
        setCookie('reader_zoom', String(zoomInput.value));
        applyZoom();
        showToast(`Zoom: ${newZoom}%`, 1500);
      }
    }
    if (e.key === '-' || e.key === '_') {
      e.preventDefault();
      if (zoomInput) {
        const newZoom = Math.max(60, parseInt(zoomInput.value || '100', 10) - 5);
        zoomInput.value = String(newZoom);
        setCookie('reader_zoom', String(zoomInput.value));
        applyZoom();
        showToast(`Zoom: ${newZoom}%`, 1500);
      }
    }
    if (e.key === '0') {
      e.preventDefault();
      // Reset zoom to 100%
      if (zoomInput) {
        zoomInput.value = '100';
        setCookie('reader_zoom', '100');
        applyZoom();
        showToast('Zoom resetado: 100%', 1500);
      }
    }
    if (e.key === 'f' || e.key === 'F') {
      e.preventDefault();
      // Toggle fullscreen
      if (expandBtn) {
        expandBtn.click();
        showToast(document.fullscreenElement ? 'Modo tela cheia' : 'Tela cheia desativada', 1500);
      }
    }
    if (e.key === 'ArrowLeft') {
      e.preventDefault();
      if (isRtl) goNext(); else goPrev();
    }
    if (e.key === 'ArrowRight') {
      e.preventDefault();
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
  syncMobileControls();
  if (scrollMode) {
    renderScrollMode();
  }

  const applyReaderModeChange = () => {
    setCookie('reader_mode', scrollMode ? 'scroll' : 'page');
    if (modeSelectMobile) modeSelectMobile.value = scrollMode ? 'scroll' : 'page';
    syncModeToggleUi();
    syncWheelToggle();
    syncMobileControls();
    syncPageGuide();
    applyScrollDefaults();
    if (scrollTopBtn) { if (scrollMode) setTimeout(() => { try { updateScrollTopVisibility(); } catch(e){} }, 80); else scrollTopBtn.classList.add('d-none'); }
    renderScrollMode();
    showToast(scrollMode ? 'Modo Scroll ativado' : 'Modo Página ativado', 2000);
    renderChapterControls();
  };

  if (modeSelectMobile) {
    modeSelectMobile.addEventListener('change', () => {
      scrollMode = modeSelectMobile.value === 'scroll';
      applyReaderModeChange();
    });
  }

  if (modeToggleBtn) {
    modeToggleBtn.addEventListener('click', () => {
      scrollMode = !scrollMode;
      applyReaderModeChange();
    });
  }
  
  // scroll-top button behavior (overlay)
  if (scrollTopBtn) {
    scrollTopBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const getActiveScrollContainer = () => {
        if (!scrollMode || !readerEl) return window;
        if (document.fullscreenElement === readerEl) return readerEl;
        if (wrap && wrap.classList.contains('is-expanded')) return wrap;
        return window;
      };
      const scroller = getActiveScrollContainer();
      if (scrollMode && readerEl) {
        if (scroller === window) {
          window.scrollTo({ top: Math.max(0, readerEl.offsetTop - 8), behavior: 'smooth' });
        } else {
          scroller.scrollTo({ top: 0, behavior: 'smooth' });
        }
      } else if (readerEl) {
        readerEl.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
    setTimeout(() => { try { if (typeof updateScrollTopVisibility === 'function') updateScrollTopVisibility(); } catch (e) {} }, 120);
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
    const syncScrollModeProgress = () => {
      if (!scrollMode || !readerEl || total <= 0) return;
      const getActiveScrollContainer = () => {
        if (document.fullscreenElement === readerEl) return readerEl;
        if (wrap && wrap.classList.contains('is-expanded')) return wrap;
        return window;
      };
      const scroller = getActiveScrollContainer();
      let ratio = 0;
      if (scroller === window) {
        const readerTop = readerEl.offsetTop;
        const maxScroll = Math.max(1, readerEl.scrollHeight - window.innerHeight);
        const relative = Math.max(0, window.scrollY - readerTop);
        ratio = Math.min(1, relative / maxScroll);
      } else {
        const maxScroll = Math.max(1, scroller.scrollHeight - scroller.clientHeight);
        ratio = Math.min(1, Math.max(0, scroller.scrollTop / maxScroll));
      }
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
    };

    readerEl.addEventListener('scroll', () => {
      if (!scrollMode) return;
      syncScrollModeProgress();
    });

    if (wrap) {
      wrap.addEventListener('scroll', () => {
        if (!scrollMode) return;
        syncScrollModeProgress();
      }, { passive: true });
    }

    window.addEventListener('scroll', () => {
      if (!scrollMode) return;
      syncScrollModeProgress();
    }, { passive: true });
  }

  window.addEventListener('resize', () => {
    applyFitMode();
    if (scrollMode) {
      try { updateScrollTopVisibility(); } catch (e) {}
    }
  }, { passive: true });

  window.addEventListener('orientationchange', () => {
    setTimeout(() => {
      applyFitMode();
      if (scrollMode) {
        try { updateScrollTopVisibility(); } catch (e) {}
      }
    }, 120);
  });


  if (expandBtn || expandBtnMobile) {
    const expandButtons = [expandBtn, expandBtnMobile].filter(Boolean);
    const syncExpandButtonState = (active) => {
      expandButtons.forEach((button) => {
        if (!button) return;
        const icon = button.querySelector('i');
        if (icon) {
          icon.classList.remove('bi-arrows-fullscreen', 'bi-fullscreen-exit');
          icon.classList.add(active ? 'bi-fullscreen-exit' : 'bi-arrows-fullscreen');
        }
        button.setAttribute('title', active ? 'Sair da tela cheia' : 'Tela cheia');
        button.setAttribute('aria-label', active ? 'Sair da tela cheia' : 'Tela cheia');
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
      if (exitFullscreenBtn) {
        exitFullscreenBtn.classList.toggle('d-none', !active);
      }
    };

    const toggleExpand = () => {
      const container = readerEl || wrap || document.documentElement;
      const enterFallbackExpanded = () => {
        document.body.classList.add('reader-expanded');
        if (wrap) wrap.classList.add('is-expanded');
        syncExpandButtonState(true);
        syncPageGuide();
      };
      const exitFallbackExpanded = () => {
        document.body.classList.remove('reader-expanded');
        if (wrap) wrap.classList.remove('is-expanded');
        syncExpandButtonState(false);
        syncPageGuide();
      };

      if (document.fullscreenElement) {
        document.exitFullscreen().catch(() => {
          if (document.body.classList.contains('reader-expanded')) {
            exitFallbackExpanded();
          } else {
            enterFallbackExpanded();
          }
        });
      } else if (container && container.requestFullscreen) {
        container.requestFullscreen().then(() => {
          enterFallbackExpanded();
        }).catch(() => {
          if (document.body.classList.contains('reader-expanded')) {
            exitFallbackExpanded();
          } else {
            enterFallbackExpanded();
          }
        });
      } else {
        if (document.body.classList.contains('reader-expanded')) {
          exitFallbackExpanded();
        } else {
          enterFallbackExpanded();
        }
      }
      setTimeout(() => {
        if (typeof updateScrollTopVisibility === 'function') updateScrollTopVisibility();
      }, 60);
    };

    expandButtons.forEach((button) => {
      if (!button) return;
      button.addEventListener('click', (e) => {
        e.preventDefault();
        toggleExpand();
      });
    });

    if (exitFullscreenBtn) {
      exitFullscreenBtn.addEventListener('click', (e) => {
        e.preventDefault();
        toggleExpand();
      });
    }

    document.addEventListener('fullscreenchange', () => {
      const active = Boolean(document.fullscreenElement);
      document.body.classList.toggle('reader-expanded', active);
      if (wrap) wrap.classList.toggle('is-expanded', active);
      syncExpandButtonState(active);
      syncPageGuide();
      setTimeout(() => {
        if (typeof updateScrollTopVisibility === 'function') updateScrollTopVisibility();
      }, 60);
    });

    // initial button state
    syncExpandButtonState(Boolean(document.fullscreenElement || document.body.classList.contains('reader-expanded')));
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
