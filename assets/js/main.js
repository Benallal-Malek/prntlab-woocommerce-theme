(() => {
  'use strict';
  const cfg = window.prntlabConfig || {};
  const menu = document.getElementById('primary-navigation');
  const toggle = document.querySelector('.menu-toggle');
  const closeMenu = () => { menu?.classList.remove('is-open'); toggle?.setAttribute('aria-expanded', 'false'); document.body.classList.remove('menu-open'); };
  toggle?.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') !== 'true';
    toggle.setAttribute('aria-expanded', String(open)); toggle.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
    menu?.classList.toggle('is-open', open); document.body.classList.toggle('menu-open', open);
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeMenu(); toggle?.focus(); } });
  document.addEventListener('click', e => { if (menu?.classList.contains('is-open') && !e.target.closest('.site-header')) closeMenu(); });
  window.matchMedia('(min-width:951px)').addEventListener('change', e => { if (e.matches) closeMenu(); });
  const toast = document.getElementById('prntlab-toast'); let toastTimer;
  const showToast = message => { if (!toast) return; toast.textContent = message; toast.classList.add('is-visible'); clearTimeout(toastTimer); toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 2700); };
  const key = 'prntlab_wishlist_v1';
  const getSaved = () => { try { const value = JSON.parse(localStorage.getItem(key) || '[]'); return Array.isArray(value) ? [...new Set(value.map(Number).filter(id => Number.isSafeInteger(id) && id > 0))].slice(0, 48) : []; } catch { return []; } };
  const syncSaved = () => {
    const saved = getSaved();
    document.querySelectorAll('[data-wishlist-id]').forEach(button => { const active = saved.includes(Number(button.dataset.wishlistId)); button.classList.toggle('is-active', active); button.setAttribute('aria-pressed', String(active)); button.setAttribute('aria-label', active ? 'Удалить из избранного' : 'Добавить в избранное'); });
    document.querySelectorAll('.wishlist-count').forEach(node => { node.textContent = saved.length; node.hidden = !saved.length; });
  };
  const wishlistRoot = document.getElementById('wishlist-products');
  const loadWishlist = async () => {
    if (!wishlistRoot) return;
    const saved = getSaved();
    if (!saved.length) { wishlistRoot.innerHTML = '<div class="empty-state"><p>Здесь будут худи, которые вам понравились. Нажмите сердечко на карточке товара.</p></div>'; return; }
    try {
      const url = new URL(cfg.ajaxUrl, window.location.origin); url.searchParams.set('action', 'prntlab_wishlist'); url.searchParams.set('ids', saved.join(','));
      const response = await fetch(url, { credentials: 'same-origin' }); const data = await response.json();
      if (!data.success) throw new Error('wishlist');
      wishlistRoot.innerHTML = data.data.html; syncSaved();
      if (window.jQuery) window.jQuery(document.body).trigger('wc-enhanced-select-init');
    } catch { wishlistRoot.innerHTML = '<p>Не удалось загрузить избранное. Обновите страницу.</p>'; }
  };
  document.addEventListener('click', e => {
    const button = e.target.closest('[data-wishlist-id]'); if (!button) return;
    e.preventDefault(); const id = Number(button.dataset.wishlistId); if (!Number.isSafeInteger(id) || id <= 0) return;
    let saved = getSaved(); const active = saved.includes(id);
    if (!active && saved.length >= 48) { showToast('Можно сохранить до 48 товаров'); return; }
    saved = active ? saved.filter(item => item !== id) : [...saved, id];
    try { localStorage.setItem(key, JSON.stringify(saved)); } catch { showToast('Браузер не разрешает сохранить избранное'); return; }
    syncSaved(); showToast(active ? 'Удалено из избранного' : 'Добавлено в избранное');
    if (wishlistRoot) loadWishlist();
  });
  window.addEventListener('storage', e => { if (e.key === key) { syncSaved(); loadWishlist(); } });
  syncSaved(); loadWishlist();
  document.querySelectorAll('.newsletter-form').forEach(form => {
    form.addEventListener('submit', async e => {
      e.preventDefault(); if (!form.reportValidity()) return;
      const button = form.querySelector('button[type="submit"]'); const message = form.querySelector('.newsletter-message');
      if (button) button.disabled = true; if (message) message.textContent = 'Отправляем…';
      try {
        const response = await fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await response.json(); if (!data.success) throw new Error(data.data?.message || 'Не удалось отправить заявку.');
        if (message) message.textContent = data.data.message; form.reset();
      } catch (error) { if (message) message.textContent = error.message || 'Ошибка соединения. Попробуйте ещё раз.'; }
      finally { if (button) button.disabled = false; }
    });
  });
})();
