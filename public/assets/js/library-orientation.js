document.addEventListener('DOMContentLoaded', () => {
  const list = document.querySelector('.list-group');
  const toggle = document.getElementById('orientationToggleBtn');
  if (!list || !toggle) return;

  // Apply standardized rendering for each item using data attributes
  const items = Array.from(list.querySelectorAll('.list-group-item[data-item-title]'));
  items.forEach((it) => {
    const series = it.dataset.seriesTitle || '';
    const itemTitle = it.dataset.itemTitle || '';
    const link = it.querySelector('a');
    if (!link) return;
    // avoid duplicating
    if (link.querySelector('.display-item-title')) return;
    // create spans
    const itemSpan = document.createElement('span');
    itemSpan.className = 'display-item-title';
    itemSpan.textContent = itemTitle.replace(/_/g, ' ');
    const seriesSpan = document.createElement('span');
    seriesSpan.className = 'display-series-title text-muted';
    seriesSpan.textContent = series;
    // normalize link content: remove existing text nodes
    while (link.firstChild) link.removeChild(link.firstChild);
    link.appendChild(seriesSpan);
    link.appendChild(itemSpan);
  });

  const ORIENT_KEY = 'libraryOrientation';
  const className = 'orientation-item-first';
  const load = () => localStorage.getItem(ORIENT_KEY) || 'series-first';
  const save = (v) => localStorage.setItem(ORIENT_KEY, v);
  const updateUI = () => {
    const v = load();
    list.classList.toggle(className, v === 'item-first');
    toggle.textContent = v === 'item-first' ? 'Mostrar série primeiro' : 'Mostrar item primeiro';
  };

  toggle.addEventListener('click', () => {
    const next = load() === 'series-first' ? 'item-first' : 'series-first';
    save(next);
    updateUI();
  });

  updateUI();
});
