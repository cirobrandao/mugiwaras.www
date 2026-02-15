document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[action$="/upload"]');
  if (!form) return;

  const fileInput = form.querySelector('input[type="file"]');
  const limitBar = document.getElementById('limitBar');
  const limitInfo = document.getElementById('limitInfo');
  const uploadBar = document.getElementById('uploadBar');
  const uploadWrap = document.getElementById('uploadProgressWrap');
  const uploadResult = document.getElementById('uploadResult');
  const submitBtn = document.getElementById('uploadSubmit');
  const uploadWait = document.getElementById('uploadWait');

  const maxBytes = parseInt((fileInput && fileInput.dataset.maxBytes) || (limitInfo && limitInfo.dataset.maxBytes) || '209715200', 10);
  const maxFiles = parseInt((fileInput && fileInput.dataset.maxFiles) || (limitInfo && limitInfo.dataset.maxFiles) || '100', 10);

  const formatBytes = (bytes) => {
    if (bytes >= 1024 ** 3) return `${(bytes / (1024 ** 3)).toFixed(2)} GB`;
    if (bytes >= 1024 ** 2) return `${(bytes / (1024 ** 2)).toFixed(2)} MB`;
    if (bytes >= 1024) return `${(bytes / 1024).toFixed(2)} KB`;
    return `${bytes} B`;
  };

  const updateLimit = () => {
    if (!fileInput || !limitBar || !limitInfo) return;
    const files = Array.from(fileInput.files || []);
    const total = files.reduce((sum, f) => sum + (f.size || 0), 0);
    const percent = Math.min(100, Math.round((total / maxBytes) * 100));
    limitBar.style.width = `${percent}%`;
    limitBar.classList.toggle('bg-danger', total > maxBytes);
    limitInfo.textContent = `${formatBytes(total)} / ${formatBytes(maxBytes)} · ${files.length} / ${maxFiles} arquivos`;
    // update upload log with current selection summary only when files selected
    const log = document.getElementById('uploadLog');
    if (log && files.length > 0) {
      const summary = `Selecionados: ${files.length} arquivos · ${formatBytes(total)}`;
      const el = document.createElement('div'); el.className = 'entry'; el.textContent = summary; log.prepend(el);
    }
  };

  if (fileInput) {
    fileInput.addEventListener('change', updateLimit);
    updateLimit();
  }

  form.addEventListener('submit', (e) => {
    if (!fileInput || !uploadBar) return;
    const files = Array.from(fileInput.files || []);
    if (!files.length) return;

    if (uploadResult) uploadResult.innerHTML = '';
    if (uploadWrap) uploadWrap.classList.remove('d-none');
    if (uploadWait) uploadWait.classList.add('d-none');

    if (files.length > maxFiles) {
      e.preventDefault();
      if (uploadResult) {
        uploadResult.innerHTML = `<div class="alert alert-danger">Máximo de ${maxFiles} arquivos por envio.</div>`;
      }
      if (uploadWrap) uploadWrap.classList.add('d-none');
      if (submitBtn) submitBtn.disabled = false;
      return;
    }

    const tooBig = files.some((f) => f.size > maxBytes || f.size <= 0);
    if (tooBig) {
      e.preventDefault();
      if (uploadResult) {
        uploadResult.innerHTML = '<div class="alert alert-danger">Arquivo inválido ou acima do limite de 200 MB.</div>';
      }
      if (uploadWrap) uploadWrap.classList.add('d-none');
      if (submitBtn) submitBtn.disabled = false;
      return;
    }

    e.preventDefault();
    if (submitBtn) submitBtn.disabled = true;
    if (fileInput) fileInput.disabled = true;

    // Sequential upload when multiple files selected so we can show file-by-file status
    const totalBytes = files.reduce((s, f) => s + (f.size || 0), 0);
    let uploadedBytes = 0;

    const logEl = document.getElementById('uploadLog');

    const sendFile = (index) => {
      const file = files[index];
      const fd = new FormData();
      // copy non-file fields from form
      Array.from(form.elements).forEach((el) => {
        if (!el.name) return;
        if (el.type === 'file') return;
        if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
          if (el.type === 'checkbox' || el.type === 'radio') {
            if (!el.checked) return;
          }
          fd.append(el.name, el.value);
        }
      });
      fd.append('file[]', file, file.name);

      const xhrf = new XMLHttpRequest();
      xhrf.upload.addEventListener('loadstart', () => {
        if (uploadBar) { uploadBar.style.width = '0%'; uploadBar.textContent = '0%'; }
        if (logEl) { const e = document.createElement('div'); e.className = 'entry'; e.textContent = `Enviando (${index + 1}/${files.length}): ${file.name}`; logEl.prepend(e); }
      });
      xhrf.upload.addEventListener('progress', (evt) => {
        if (!evt.lengthComputable) return;
        const currentPercent = evt.loaded;
        const percent = Math.round(((uploadedBytes + currentPercent) / Math.max(1, totalBytes)) * 100);
        if (uploadBar) { uploadBar.style.width = `${percent}%`; uploadBar.textContent = `${percent}%`; }
        if (uploadWait) uploadWait.classList.toggle('d-none', percent < 100);
      });
      xhrf.addEventListener('load', () => {
        let msg = '';
        if (xhrf.status >= 200 && xhrf.status < 400) {
          msg = 'Concluído';
        } else {
          msg = `Falha (${xhrf.status})`;
        }
        uploadedBytes += file.size || 0;
        if (logEl) { const e = document.createElement('div'); e.className = 'entry'; e.textContent = `${file.name} - ${msg}`; logEl.prepend(e); }
        // proceed to next file or finish
        if (index + 1 < files.length) {
          sendFile(index + 1);
        } else {
          // finished all
            if (uploadBar) { uploadBar.style.width = '100%'; uploadBar.textContent = '100%'; }
            if (uploadWait) uploadWait.classList.add('d-none');
            if (fileInput) { fileInput.value = ''; fileInput.disabled = false; }
            updateLimit();
            if (submitBtn) submitBtn.disabled = false;
          refreshHistory();
          if (logEl) { const e = document.createElement('div'); e.className = 'entry'; e.textContent = 'Envio concluído.'; logEl.prepend(e); }
        }
      });
      xhrf.addEventListener('error', () => {
        if (logEl) { const e = document.createElement('div'); e.className = 'entry'; e.textContent = `Erro de rede ao enviar ${file.name}`; logEl.prepend(e); }
        // continue with next file
        uploadedBytes += file.size || 0;
        if (index + 1 < files.length) sendFile(index + 1);
        else {
          if (submitBtn) submitBtn.disabled = false;
          if (fileInput) fileInput.disabled = false;
          refreshHistory();
        }
      });
      xhrf.open(form.method || 'POST', form.action);
      xhrf.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhrf.send(fd);
    };

    // start sequential upload
    sendFile(0);
  });

  const refreshHistory = () => {
    const historyWrap = document.getElementById('uploadHistory');
    if (!historyWrap) return;
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then((res) => res.text())
      .then((html) => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const updated = doc.getElementById('uploadHistory');
        if (updated) {
          historyWrap.innerHTML = updated.innerHTML;
        }
      })
      .catch(() => {
        // ignore refresh errors
      });
  };
});
