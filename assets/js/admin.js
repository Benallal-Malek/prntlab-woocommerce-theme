(() => {
  'use strict';
  const form = document.getElementById('prntlab-import-form'); if (!form) return;
  const cfg = window.prntlabImport; const button = form.querySelector('button'); const status = document.getElementById('prntlab-import-status'); const bar = document.getElementById('prntlab-import-progress'); const log = document.getElementById('prntlab-import-log');
  form.addEventListener('submit', async event => {
    event.preventDefault(); button.disabled = true; bar.style.display = 'block'; bar.value = 0; log.replaceChildren();
    const settings = new FormData(form);
    try {
      for (const [index, step] of cfg.steps.entries()) {
        status.textContent = step.label + '…';
        const body = new FormData(); body.set('action', 'prntlab_import_step'); body.set('nonce', cfg.nonce); body.set('step', step.id);
        for (const name of ['set_home', 'set_currency']) if (settings.has(name)) body.set(name, '1');
        const response = await fetch(cfg.url, { method: 'POST', credentials: 'same-origin', body });
        const result = await response.json(); if (!result.success) throw new Error(result.data?.message || 'Сервер отклонил импорт.');
        const item = document.createElement('li'); item.textContent = '✓ ' + result.data.message; log.append(item); item.scrollIntoView({ block: 'nearest' });
        bar.value = Math.round((index + 1) / cfg.steps.length * 100);
      }
      status.textContent = 'Готово. Откройте главную и проверьте каталог. Если демо не назначено главной, выберите «Главная PRNTLAB» в настройках чтения.';
      button.textContent = 'Проверить / повторить импорт';
    } catch (error) { status.textContent = 'Импорт остановлен: ' + error.message + ' Исправьте причину и запустите повторно — существующие объекты сохранятся.'; }
    finally { button.disabled = false; }
  });
})();
