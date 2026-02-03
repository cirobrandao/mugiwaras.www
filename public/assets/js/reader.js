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
  const lastBtn = document.getElementById('readerLast');
  const status = document.getElementById('readerStatus');
  const fitMode = document.getElementById('readerFitMode');
  const zoomInput = document.getElementById('readerZoom');
  const progress = document.getElementById('readerProgress');
  const lightsBtn = document.getElementById('readerLights');
  const modeSelect = document.getElementById('readerMode');
  const modeSelectMobile = document.getElementById('readerModeMobile');
  const overlay = document.getElementById('readerOverlay');
  const firstBtn = document.getElementById('readerFirst');
  const wheelToggle = document.getElementById('readerWheel');
  const wheelWrap = wheelToggle ? wheelToggle.closest('.form-check') : null;
  const wrap = document.getElementById('readerWrap');
  const scrollTopBtn = document.getElementById('scrollTopBtn');
  let scrollMode = modeSelect ? modeSelect.value === 'scroll' : (modeSelectMobile ? modeSelectMobile.value === 'scroll' : false);
  let wheelPaging = wheelToggle ? wheelToggle.checked : true;
  const isMobile = window.matchMedia && window.matchMedia('(max-width: 768px)').matches;

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
    if (!pageInput || !pageTotal) return;
    if (scrollMode) {
      pageInput.closest('.input-group')?.classList.add('d-none');
    } else {
      pageInput.closest('.input-group')?.classList.remove('d-none');
    }
  };
  let saveTimer = null;

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
    if (!img) return;
    const mode = fitMode ? fitMode.value : 'height';
    img.style.width = '';
    img.style.height = '';
    img.style.maxWidth = '';
    img.style.maxHeight = '';
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
  };

  const applyZoom = () => {
    if (!img) return;
    const z = zoomInput ? parseInt(zoomInput.value || '100', 10) : 100;
    img.style.transform = `scale(${z / 100})`;
    img.style.transformOrigin = 'center top';
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
    if (prev) prev.disabled = page <= 0;
    if (next) next.disabled = page >= total - 1;
    if (lastBtn) lastBtn.disabled = page >= total - 1;
    if (progress) progress.style.width = `${total > 0 ? Math.round(((page + 1) / total) * 100) : 0}%`;
    setStatus('Carregando...');
    applyFitMode();
    applyZoom();
    scheduleSaveProgress();
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

  const renderScrollMode = () => {
    if (!readerEl || !img) return;
    readerEl.classList.toggle('scroll-mode', scrollMode);
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
    if (modeSelectMobile) {
      modeSelectMobile.value = 'page';
    }
    scrollMode = false;
    if (wheelWrap) wheelWrap.classList.add('d-none');
    syncWheelToggle();
    syncPageGuide();
    renderScrollMode();
  };

  if (fitMode) {
    fitMode.addEventListener('change', () => {
      applyFitMode();
    });
  }
  if (zoomInput) {
    zoomInput.addEventListener('input', applyZoom);
  }

  if (img) {
    img.addEventListener('load', () => setStatus(''));
    img.addEventListener('error', () => setStatus('Falha ao carregar a página.'));
    img.addEventListener('click', (e) => {
      if (scrollMode) return;
      if (e.shiftKey) {
        if (page > 0) {
          page -= 1;
          update();
        }
        return;
      }
      if (page < total - 1) {
        page += 1;
        update();
      }
    });
  }

  if (prev) prev.addEventListener('click', () => {
    if (page > 0) {
      page -= 1;
      update();
    }
  });
  if (next) next.addEventListener('click', () => {
    if (page < total - 1) {
      page += 1;
      update();
    }
  });

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
      if (page > 0) {
        page -= 1;
        update();
      }
    }
    if (e.key === 'ArrowRight') {
      if (page < total - 1) {
        page += 1;
        update();
      }
    }
    if (e.key === 'Home') {
      e.preventDefault();
      page = 0;
      update();
    }
    if (e.key === 'PageUp') {
      if (page > 0) {
        page -= 1;
        update();
      }
    }
    if (e.key === 'PageDown' || e.key === ' ') {
      e.preventDefault();
      if (page < total - 1) {
        page += 1;
        update();
      }
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

  update();

  if (modeSelect) {
    modeSelect.addEventListener('change', () => {
      scrollMode = modeSelect.value === 'scroll';
      if (modeSelectMobile) modeSelectMobile.value = modeSelect.value;
      syncWheelToggle();
      syncPageGuide();
      if (scrollTopBtn) { if (scrollMode) setTimeout(() => { try { updateScrollTopVisibility(); } catch(e){} }, 80); else scrollTopBtn.classList.add('d-none'); }
      if (scrollMode) {
        renderScrollMode();
      } else {
        readerEl.innerHTML = '';
        readerEl.appendChild(img);
        update();
      }
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
      if (modeSelect) modeSelect.value = modeSelectMobile.value;
      syncWheelToggle();
      syncPageGuide();
      if (scrollTopBtn) { if (scrollMode) setTimeout(() => { try { updateScrollTopVisibility(); } catch(e){} }, 80); else scrollTopBtn.classList.add('d-none'); }
      if (scrollMode) {
        renderScrollMode();
      } else {
        readerEl.innerHTML = '';
        readerEl.appendChild(img);
        update();
      }
    });
  }

  if (wheelToggle) {
    wheelToggle.addEventListener('change', () => {
      wheelPaging = wheelToggle.checked;
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
        page += 1;
        update();
      } else if (e.deltaY < -10 && page > 0) {
        page -= 1;
        update();
      }
    }, { passive: false });
    readerEl.addEventListener('scroll', () => {
      if (!scrollMode || !readerEl || total <= 0) return;
      const ratio = readerEl.scrollTop / Math.max(1, readerEl.scrollHeight - readerEl.clientHeight);
      const newPage = Math.min(total - 1, Math.max(0, Math.floor(ratio * total)));
      if (newPage !== page) {
        page = newPage;
        scheduleSaveProgress();
        if (info) info.textContent = `${page + 1} / ${total}`;
        if (pageInput) pageInput.value = String(page + 1);
        if (pageTotal) pageTotal.textContent = `/ ${total}`;
        if (progress) progress.style.width = `${Math.round(((page + 1) / total) * 100)}%`;
      }
      // update scroll-top overlay visibility while scrolling
      try { updateScrollTopVisibility(); } catch (e) {}
      
    });
  }


  if (lightsBtn) {
    lightsBtn.addEventListener('click', () => {
      document.body.classList.toggle('lights-off');
    });
  }

  // Intercept favorite forms in the reader to toggle UI immediately without full reload
  const favForms = Array.from(document.querySelectorAll('form[action$="/libraries/favorite"]'));
  favForms.forEach((form) => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const btn = form.querySelector('button[type="submit"]');
      const actionInput = form.querySelector('input[name="action"]');
      const csrfInput = form.querySelector('input[name="_csrf"]');
      if (!btn || !actionInput) return;
      const formData = new FormData(form);
      fetch(form.action, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
      }).then(() => {
        // toggle UI state regardless of server response (server will persist)
        const isNowFavorited = actionInput.value === 'add';
        if (isNowFavorited) {
          // we just added -> switch to remove
          actionInput.value = 'remove';
          btn.classList.remove('btn-outline-warning');
          btn.classList.add('btn-warning');
          btn.dataset.favorited = '1';
        } else {
          // we just removed -> switch to add
          actionInput.value = 'add';
          btn.classList.remove('btn-warning');
          btn.classList.add('btn-outline-warning');
          btn.dataset.favorited = '0';
        }
      }).catch(() => {
        // ignore errors — state will update on full reload
      });
    });

      // Adjust reader title box so its border touches left/right buttons exactly
      const adjustTitleBounds = () => {
        const title = document.querySelector('.reader-title');
        const backBtn = document.querySelector('.reader-back');
        const nextBtn = document.querySelector('.reader-next');
        if (!title) return;
        // reset
        title.style.left = '';
        title.style.right = '';
        title.style.maxWidth = '';
        const header = document.querySelector('.reader-header');
        if (!header) return;
        const headerRect = header.getBoundingClientRect();
        const gap = 6; // small gap so border doesn't overlap button edges
        // prefer to center the title box; set its explicit width to span between buttons
        if (backBtn && nextBtn) {
          const backRect = backBtn.getBoundingClientRect();
          const nextRect = nextBtn.getBoundingClientRect();
          const available = Math.max(80, Math.floor(nextRect.left - backRect.right - gap * 2));
          title.style.width = available + 'px';
          title.style.left = '50%';
          title.style.transform = 'translate(-50%, -50%)';
        } else if (backBtn) {
          const backRect = backBtn.getBoundingClientRect();
          const available = Math.max(80, Math.floor(headerRect.right - backRect.right - 24));
          title.style.width = available + 'px';
          title.style.left = '50%';
          title.style.transform = 'translate(-50%, -50%)';
        } else if (nextBtn) {
          const nextRect = nextBtn.getBoundingClientRect();
          const available = Math.max(80, Math.floor(nextRect.left - headerRect.left - 24));
          title.style.width = available + 'px';
          title.style.left = '50%';
          title.style.transform = 'translate(-50%, -50%)';
        } else {
          title.style.left = '50%';
          title.style.transform = 'translate(-50%, -50%)';
          title.style.width = '';
        }
      };
      // run on load and resize
      setTimeout(adjustTitleBounds, 60);
      window.addEventListener('resize', () => {
        setTimeout(adjustTitleBounds, 60);
      });
  });
});
