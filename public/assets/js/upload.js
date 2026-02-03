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

  const maxBytes = parseInt((fileInput && fileInput.dataset.maxBytes) || (limitInfo && limitInfo.dataset.maxBytes) || '5368709120', 10);
  const maxFiles = parseInt((fileInput && fileInput.dataset.maxFiles) || (limitInfo && limitInfo.dataset.maxFiles) || '20', 10);

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
        uploadResult.innerHTML = '<div class="alert alert-danger">Máximo de 20 arquivos por envio.</div>';
      }
      if (uploadWrap) uploadWrap.classList.add('d-none');
      if (submitBtn) submitBtn.disabled = false;
      return;
    }

    const tooBig = files.some((f) => f.size > maxBytes || f.size <= 0);
    if (tooBig) {
      e.preventDefault();
      if (uploadResult) {
        uploadResult.innerHTML = '<div class="alert alert-danger">Arquivo inválido ou acima do limite de 5 GB.</div>';
      }
      if (uploadWrap) uploadWrap.classList.add('d-none');
      if (submitBtn) submitBtn.disabled = false;
      return;
    }

    e.preventDefault();
    if (submitBtn) submitBtn.disabled = true;

    const xhr = new XMLHttpRequest();
    const formData = new FormData(form);

    xhr.upload.addEventListener('loadstart', () => {
      if (uploadBar) {
        uploadBar.style.width = '0%';
        uploadBar.textContent = '0%';
      }
    });

    xhr.upload.addEventListener('progress', (evt) => {
      if (!evt.lengthComputable) return;
      const percent = Math.round((evt.loaded / evt.total) * 100);
      uploadBar.style.width = `${percent}%`;
      uploadBar.textContent = `${percent}%`;
      if (uploadWait) {
        uploadWait.classList.toggle('d-none', percent < 100);
      }
    });

    xhr.addEventListener('load', () => {
      if (uploadWait) uploadWait.classList.add('d-none');
      const contentType = (xhr.getResponseHeader('Content-Type') || '').toLowerCase();
      if (xhr.status >= 200 && xhr.status < 400) {
        if (contentType.includes('application/json')) {
          try {
            const data = JSON.parse(xhr.responseText || '{}');
            if (uploadResult) {
              const details = Array.isArray(data.errors) && data.errors.length ? `<div class="small text-muted mt-1">${data.errors.join('<br>')}</div>` : '';
              uploadResult.innerHTML = `<div class="alert alert-info">Enviados: ${data.ok || 0}, Enfileirados: ${data.queued || 0}, Falhas: ${data.failed || 0}.${details}</div>`;
            }
            if (fileInput) fileInput.value = '';
            if (uploadBar) {
              uploadBar.style.width = '0%';
              uploadBar.textContent = '0%';
            }
            updateLimit();
            if (submitBtn) submitBtn.disabled = false;
            refreshHistory();
            return;
          } catch (e) {
            // fallback
          }
        }
        if (xhr.responseURL) {
          window.location.href = xhr.responseURL;
          return;
        }
      }
      if (uploadResult) {
        let msg = 'Falha no upload.';
        if (xhr.status === 413) msg = 'Falha no upload: limite de 5 GB excedido.';
        if (contentType.includes('application/json')) {
          try {
            const data = JSON.parse(xhr.responseText || '{}');
            if (data.error === 'series_required') msg = 'Série é obrigatória.';
            if (data.error === 'limit') msg = 'Falha no upload: limite de 5 GB excedido.';
            if (data.error === 'max_files') msg = 'Máximo de 20 arquivos por envio.';
            if (data.error === 'category_required') msg = 'Selecione uma categoria válida.';
            if (Array.isArray(data.errors) && data.errors.length) {
              msg += `<div class="small text-muted mt-1">${data.errors.join('<br>')}</div>`;
            }
          } catch (e) {
            // ignore
          }
        }
        uploadResult.innerHTML = `<div class="alert alert-danger">${msg}</div>`;
      }
      if (submitBtn) submitBtn.disabled = false;
    });

    xhr.addEventListener('error', () => {
      if (uploadWait) uploadWait.classList.add('d-none');
      if (submitBtn) submitBtn.disabled = false;
    });

    xhr.open(form.method || 'POST', form.action);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send(formData);
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
