// sidebar.js
// Small helper to make the admin sidebar submenu behavior robust:
// - Prevent clicks inside the submenu from triggering global body handlers
// - Provide toggle handling (keeps aria attributes & localStorage in sync)
// - Close submenu on Escape

document.addEventListener('DOMContentLoaded', function () {
  const parentLi = document.querySelector('.has-submenu');
  if (!parentLi) return;
  const toggle = parentLi.querySelector('.submenu-toggle');
  const submenu = parentLi.querySelector('.submenu');

  // Prevent clicks inside the submenu area from bubbling to global handlers
  // that might inadvertently close it. Use capture so we stop earlier.
  document.addEventListener('click', function (e) {
    if (e.target && e.target.closest && e.target.closest('.has-submenu')) {
      e.stopPropagation();
    }
  }, true);

  // Toggle logic: keep aria attributes and classes consistent
  function openSubmenu() {
    toggle.setAttribute('aria-expanded', 'true');
    submenu.removeAttribute('hidden');
    parentLi.classList.add('open');
    try { localStorage.setItem('adm_gestion_open', 'true'); } catch (err) {}
    const caret = toggle.querySelector('.caret'); if (caret) caret.textContent = '▴';
  }
  function closeSubmenu() {
    toggle.setAttribute('aria-expanded', 'false');
    submenu.setAttribute('hidden', '');
    parentLi.classList.remove('open');
    try { localStorage.setItem('adm_gestion_open', 'false'); } catch (err) {}
    const caret = toggle.querySelector('.caret'); if (caret) caret.textContent = '▾';
  }

  // Click handler for the toggle button
  toggle.addEventListener('click', function (ev) {
    ev.preventDefault();
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    if (expanded) closeSubmenu(); else openSubmenu();
  });

  // Keyboard: close submenu on Escape
  document.addEventListener('keydown', function (ev) {
    if (ev.key === 'Escape' || ev.key === 'Esc') {
      if (parentLi.classList.contains('open')) {
        closeSubmenu();
      }
    }
  });

  // Respect saved state (localStorage) - if main.js already handled this we won't override
  try {
    const wanted = localStorage.getItem('adm_gestion_open');
    if (wanted === 'true') openSubmenu();
  } catch (err) {}
});
