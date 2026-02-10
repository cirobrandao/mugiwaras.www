document.addEventListener('DOMContentLoaded', () => {
  const readerEl = document.getElementById('reader');
  const canvas = document.getElementById('pdfCanvas');
  if (!readerEl || !canvas) return;
  if (!window.pdfjsLib) {
    const overlay = document.getElementById('readerOverlay');
    if (overlay) {
      overlay.textContent = 'Leitor de PDF indisponivel.';
      overlay.classList.remove('d-none');
    }
    return;
  }

  const pdfUrl = readerEl.dataset.pdfUrl || '';
  if (!pdfUrl) return;

  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

  const ctx = canvas.getContext('2d');
  const prev = document.getElementById('prevPage');
  const next = document.getElementById('nextPage');
  const pageInput = document.getElementById('pageNumber');
  const pageTotal = document.getElementById('pageTotal');
  const lastBtn = document.getElementById('readerLast');
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
  const pageCompact = document.getElementById('pageCompact');

  let pdfDoc = null;
  let page = 0;
  let total = 0;
  let rendering = false;
  let pendingPage = null;
  let scrollMode = modeSelect ? modeSelect.value === 'scroll' : (modeSelectMobile ? modeSelectMobile.value === 'scroll' : false);
  let wheelPaging = wheelToggle ? wheelToggle.checked : true;
  let saveTimer = null;
  let readMarked = false;

  const setStatus = (text) => {
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

  const getZoom = () => {
    const z = zoomInput ? parseInt(zoomInput.value || '100', 10) : 100;
    return Number.isFinite(z) ? z / 100 : 1;
  };

  const getTargetWidth = () => Math.max(200, (readerEl.clientWidth || window.innerWidth) - 20);
  const getTargetHeight = () => {
    const base = readerEl.clientHeight > 0 ? readerEl.clientHeight : Math.floor(window.innerHeight * 0.75);
    return Math.max(200, base - 20);
  };

  const computeScale = (pageObj, mode) => {
    const viewport = pageObj.getViewport({ scale: 1 });
    let scale = 1;
    if (mode === 'width') {
      scale = getTargetWidth() / viewport.width;
    } else if (mode === 'height') {
      scale = getTargetHeight() / viewport.height;
    }
    return scale * getZoom();
  };

  const updateUi = () => {
    if (pageInput) pageInput.value = String(page + 1);
    if (pageTotal) pageTotal.textContent = `/ ${total}`;
    if (pageCompact) pageCompact.textContent = `${page + 1}/${total}`;
    if (prev) prev.disabled = page <= 0;
    if (next) next.disabled = page >= total - 1;
    if (lastBtn) lastBtn.disabled = page >= total - 1;
    if (progress) progress.style.width = `${total > 0 ? Math.round(((page + 1) / total) * 100) : 0}%`;
  };

  const scheduleSaveProgress = () => {
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
    if (readMarked || total <= 0) return;
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

  const renderPage = (pageIndex) => {
    if (!pdfDoc || !ctx) return;
    if (rendering) {
      pendingPage = pageIndex;
      return;
    }
    rendering = true;
    setStatus('Carregando...');
    pdfDoc.getPage(pageIndex + 1)
      .then((pageObj) => {
        const mode = fitMode ? fitMode.value : 'height';
        const scale = computeScale(pageObj, mode);
        const viewport = pageObj.getViewport({ scale });
        canvas.width = Math.floor(viewport.width);
        canvas.height = Math.floor(viewport.height);
        return pageObj.render({ canvasContext: ctx, viewport }).promise;
      })
      .then(() => {
        setStatus('');
        rendering = false;
        updateUi();
        scheduleSaveProgress();
        markReadIfLast();
        if (pendingPage !== null) {
          const nextPage = pendingPage;
          pendingPage = null;
          renderPage(nextPage);
        }
      })
      .catch(() => {
        setStatus('Falha ao carregar o PDF.');
        rendering = false;
      });
  };

  const updateScrollTopVisibility = () => {
    if (!scrollTopBtn) return;
    if (!scrollMode) {
      scrollTopBtn.classList.add('d-none');
      return;
    }
    const canScroll = readerEl.scrollHeight > (readerEl.clientHeight + 20);
    const scrolled = readerEl.scrollTop > 80;
    if (canScroll || scrolled) {
      scrollTopBtn.classList.remove('d-none');
    } else {
      scrollTopBtn.classList.add('d-none');
    }
  };

  const renderAllPages = () => {
    if (!pdfDoc) return;
    readerEl.classList.add('scroll-mode');
    readerEl.innerHTML = '';
    const fragment = document.createDocumentFragment();
    const canvases = [];
    for (let i = 0; i < total; i += 1) {
      const pageCanvas = document.createElement('canvas');
      pageCanvas.className = 'pdf-canvas pdf-page-canvas';
      pageCanvas.dataset.pageIndex = String(i);
      fragment.appendChild(pageCanvas);
      canvases.push(pageCanvas);
    }
    readerEl.appendChild(fragment);
    setStatus('Carregando...');

    const renderNext = (index) => {
      if (index >= total) {
        setStatus('');
        updateUi();
        updateScrollTopVisibility();
        return;
      }
      pdfDoc.getPage(index + 1)
        .then((pageObj) => {
          const mode = fitMode ? fitMode.value : 'width';
          const effectiveMode = mode === 'height' ? 'width' : mode;
          const scale = computeScale(pageObj, effectiveMode);
          const viewport = pageObj.getViewport({ scale });
          const pageCanvas = canvases[index];
          pageCanvas.width = Math.floor(viewport.width);
          pageCanvas.height = Math.floor(viewport.height);
          const pageCtx = pageCanvas.getContext('2d');
          return pageObj.render({ canvasContext: pageCtx, viewport }).promise;
        })
        .then(() => renderNext(index + 1))
        .catch(() => {
          setStatus('Falha ao renderizar o PDF.');
        });
    };

    renderNext(0);
  };

  const goNext = () => {
    if (page < total - 1) {
      page += 1;
      renderPage(page);
    }
  };

  const goPrev = () => {
    if (page > 0) {
      page -= 1;
      renderPage(page);
    }
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

  const syncPageGuide = () => {
    if (!pageInput || !pageTotal) return;
    const group = pageInput.closest('.input-group');
    if (!group) return;
    if (scrollMode) {
      group.classList.add('d-none');
    } else {
      group.classList.remove('d-none');
    }
  };

  const loadPdf = (disableWorker = false) => {
    pdfjsLib.getDocument({ url: pdfUrl, withCredentials: true, disableWorker }).promise
      .then((doc) => {
        pdfDoc = doc;
        total = doc.numPages || 0;
        updateUi();
        if (scrollMode) {
          renderAllPages();
        } else {
          renderPage(page);
        }
      })
      .catch(() => {
        if (!disableWorker) {
          loadPdf(true);
          return;
        }
        setStatus('Falha ao carregar o PDF.');
      });
  };

  const initPage = () => {
    const last = parseInt(readerEl.dataset.lastPage || '0', 10);
    const params = new URLSearchParams(window.location.search);
    const paramPage = params.get('page');
    if (paramPage !== null) {
      const p = parseInt(paramPage, 10);
      if (!Number.isNaN(p)) page = clampPage(p);
    } else if (!Number.isNaN(last)) {
      page = clampPage(last);
    }
  };

  if (prev) prev.addEventListener('click', goPrev);
  if (next) next.addEventListener('click', goNext);
  if (firstBtn) firstBtn.addEventListener('click', () => { page = 0; renderPage(page); });
  if (lastBtn) lastBtn.addEventListener('click', () => { page = Math.max(0, total - 1); renderPage(page); });

  if (pageInput) {
    const onPageChange = () => {
      const value = parseInt(pageInput.value || '1', 10) - 1;
      page = clampPage(value);
      if (!scrollMode) renderPage(page);
    };
    pageInput.addEventListener('change', onPageChange);
    pageInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        onPageChange();
      }
    });
  }

  if (fitMode) {
    fitMode.addEventListener('change', () => {
      if (scrollMode) {
        renderAllPages();
      } else {
        renderPage(page);
      }
    });
  }

  if (zoomInput) {
    zoomInput.addEventListener('input', () => {
      if (scrollMode) {
        renderAllPages();
      } else {
        renderPage(page);
      }
    });
  }

  if (modeSelect) {
    modeSelect.addEventListener('change', () => {
      scrollMode = modeSelect.value === 'scroll';
      if (modeSelectMobile) modeSelectMobile.value = modeSelect.value;
      syncWheelToggle();
      syncPageGuide();
      if (scrollMode) {
        renderAllPages();
      } else {
        readerEl.classList.remove('scroll-mode');
        readerEl.innerHTML = '';
        readerEl.appendChild(canvas);
        renderPage(page);
      }
      updateScrollTopVisibility();
    });
  }

  if (modeSelectMobile) {
    modeSelectMobile.addEventListener('change', () => {
      scrollMode = modeSelectMobile.value === 'scroll';
      if (modeSelect) modeSelect.value = modeSelectMobile.value;
      syncWheelToggle();
      syncPageGuide();
      if (scrollMode) {
        renderAllPages();
      } else {
        readerEl.classList.remove('scroll-mode');
        readerEl.innerHTML = '';
        readerEl.appendChild(canvas);
        renderPage(page);
      }
      updateScrollTopVisibility();
    });
  }

  if (wheelToggle) {
    wheelToggle.addEventListener('change', () => {
      wheelPaging = wheelToggle.checked;
    });
  }

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
    if (!scrollMode || total <= 0) return;
    const ratio = readerEl.scrollTop / Math.max(1, readerEl.scrollHeight - readerEl.clientHeight);
    const newPage = Math.min(total - 1, Math.max(0, Math.floor(ratio * total)));
    if (newPage !== page) {
      page = newPage;
      updateUi();
      scheduleSaveProgress();
      markReadIfLast();
    }
    updateScrollTopVisibility();
  });

  canvas.addEventListener('click', (e) => {
    if (scrollMode) return;
    const rect = canvas.getBoundingClientRect();
    const isLeft = (e.clientX - rect.left) < rect.width / 2;
    if (isLeft) {
      goPrev();
    } else {
      goNext();
    }
  });

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
    if (scrollMode) {
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
        if (scrollMode) renderAllPages(); else renderPage(page);
      }
    }
    if (e.key === '-') {
      if (zoomInput) {
        zoomInput.value = String(Math.max(60, parseInt(zoomInput.value || '100', 10) - 5));
        if (scrollMode) renderAllPages(); else renderPage(page);
      }
    }
    if (e.key === 'ArrowLeft') {
      goPrev();
    }
    if (e.key === 'ArrowRight') {
      goNext();
    }
    if (e.key === 'Home') {
      e.preventDefault();
      page = 0;
      renderPage(page);
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
      renderPage(page);
    }
  });

  if (expandBtn) {
    expandBtn.addEventListener('click', () => {
      document.body.classList.toggle('reader-expanded');
      if (wrap) wrap.classList.toggle('is-expanded');
      setTimeout(() => updateScrollTopVisibility(), 60);
    });
  }

  if (scrollTopBtn) {
    scrollTopBtn.addEventListener('click', (e) => {
      e.preventDefault();
      readerEl.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  let resizeTimer = null;
  window.addEventListener('resize', () => {
    if (resizeTimer) window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(() => {
      if (scrollMode) {
        renderAllPages();
      } else {
        renderPage(page);
      }
    }, 150);
  });

  initPage();
  syncWheelToggle();
  syncPageGuide();
  loadPdf();
});
