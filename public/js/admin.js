/* Divine Hospital – admin panel behaviour (no build step). */
(() => {
  const icons = JSON.parse(document.getElementById('admin-icons')?.textContent || '{}');
  const FILLED = ['whatsapp', 'star', 'quote'];
  const editor = document.querySelector('[data-editor]');
  const dirtyNote = document.querySelector('[data-dirty-note]');
  let uid = Date.now();
  let dirty = false;
  let submitting = false;

  const markDirty = () => {
    if (!editor || dirty) return;
    dirty = true;
    if (dirtyNote) dirtyNote.hidden = false;
  };

  const svg = (name) => {
    const attrs = FILLED.includes(name)
      ? 'fill="currentColor"'
      : 'fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"';
    return `<svg class="icon" viewBox="0 0 24 24" ${attrs} aria-hidden="true">${icons[name] || ''}</svg>`;
  };

  const rowsOf = (repeater) => repeater.querySelector(':scope > [data-rows]');

  const updateCount = (repeater) => {
    const count = rowsOf(repeater).children.length;
    const label = repeater.querySelector(':scope > .a-repeater__head [data-count]');
    if (label) label.textContent = `${count} ${count === 1 ? 'item' : 'items'}`;
  };

  const addRow = (repeater) => {
    const rows = rowsOf(repeater);
    const max = Number(repeater.dataset.max || 60);
    if (rows.children.length >= max) {
      alert(`You can add up to ${max} items here.`);
      return;
    }
    const template = repeater.querySelector(':scope > template[data-template]');
    rows.insertAdjacentHTML('beforeend', template.innerHTML.split(repeater.dataset.placeholder).join(`n${uid++}`));
    const row = rows.lastElementChild;
    row.open = true;
    row.querySelector('input:not([type=hidden]), textarea, select')?.focus();
    updateCount(repeater);
    markDirty();
  };

  document.addEventListener('click', (event) => {
    const add = event.target.closest('[data-add]');
    if (add) {
      addRow(add.closest('[data-repeater]'));
      return;
    }

    const remove = event.target.closest('[data-remove]');
    if (remove) {
      event.preventDefault();
      const row = remove.closest('[data-row]');
      const title = row.querySelector(':scope > summary [data-row-title]')?.textContent.trim();
      if (!confirm(`Remove “${title}”? It disappears from the website after you save.`)) return;
      const repeater = row.closest('[data-repeater]');
      row.remove();
      updateCount(repeater);
      markDirty();
      return;
    }

    const move = event.target.closest('[data-move]');
    if (move) {
      event.preventDefault();
      const row = move.closest('[data-row]');
      if (move.dataset.move === 'up' && row.previousElementSibling) {
        row.parentNode.insertBefore(row, row.previousElementSibling);
      } else if (move.dataset.move === 'down' && row.nextElementSibling) {
        row.parentNode.insertBefore(row.nextElementSibling, row);
      }
      move.focus();
      markDirty();
    }
  });

  document.addEventListener('input', (event) => {
    const target = event.target;
    if (target.closest('[data-editor]')) markDirty();

    const row = target.closest('[data-row]');
    if (row && target.name && target.name.endsWith(`[${row.dataset.titleField}]`)) {
      const text = target.tagName === 'SELECT' ? target.selectedOptions[0]?.text : target.value;
      row.querySelector(':scope > summary [data-row-title]').textContent = text?.trim() || 'New item';
    }
  });

  document.addEventListener('change', (event) => {
    const target = event.target;

    if (target.matches('[data-icon-select]')) {
      target.closest('.a-icon-select').querySelector('[data-icon-preview]').innerHTML = svg(target.value);
    }

    if (target.matches('[data-upload-input]') && target.files[0]) {
      const preview = target.closest('.a-upload').querySelector('[data-upload-preview]');
      preview.innerHTML = '';
      const img = document.createElement('img');
      img.src = URL.createObjectURL(target.files[0]);
      img.alt = '';
      preview.append(img);
    }

    if (target.matches('[data-autosubmit]')) {
      target.form.submit();
    }

    if (target.closest('[data-editor]')) markDirty();
  });

  // Required fields inside collapsed rows: open the rows so the browser can point at the problem.
  document.addEventListener('invalid', (event) => {
    let details = event.target.closest('details');
    while (details) {
      details.open = true;
      details = details.parentElement.closest('details');
    }
  }, true);

  document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form.dataset.confirm && !confirm(form.dataset.confirm)) {
      event.preventDefault();
      return;
    }
    if (form === editor) {
      submitting = true;
      const button = form.querySelector('[data-save]');
      if (button) {
        button.disabled = true;
        button.lastChild.textContent = ' Saving…';
      }
    }
  });

  addEventListener('beforeunload', (event) => {
    if (dirty && !submitting) event.preventDefault();
  });

  // Rows with validation errors are rendered open; bring the first error into view.
  document.querySelector('.has-error, .a-error')?.scrollIntoView({ block: 'center' });
})();
