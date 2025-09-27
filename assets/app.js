(() => {

  const App = {
    qs: (sel, ctx = document) => ctx.querySelector(sel),
    qsa: (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel)),
    debounce(fn, wait = 300) {
      let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
    },
    formatCurrency(n, loc = 'es-PE', cur = 'PEN') {
      const v = Number(n || 0);
      return new Intl.NumberFormat(loc, { style: 'currency', currency: cur }).format(v);
    }
  };
  window.App = App;

  // Autocierre de flashes
  document.addEventListener('DOMContentLoaded', () => {
    App.qsa('.alert').forEach(a => {
      setTimeout(() => {
        a.classList.add('fade', 'show');
        setTimeout(() => a.remove(), 500);
      }, 3000);
    });
  });

  // Confirmaciones
  document.addEventListener('click', (e) => {
    const el = e.target.closest('[data-confirm]');
    if (!el) return;
    const msg = el.getAttribute('data-confirm') || '¿Estás seguro de realizar esta acción?';
    if (!confirm(msg)) e.preventDefault();
  });

  // Estado "Guardando..." en formularios con data-loading
  document.addEventListener('submit', (e) => {
    const form = e.target.closest('form[data-loading]');
    if (!form) return;
    if (form.dataset.submitting === '1') {
      e.preventDefault(); return;
    }
    form.dataset.submitting = '1';
    const submitBtn = form.querySelector('[type=submit]');
    if (submitBtn) {
      submitBtn.dataset.originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = 'Guardando...';
      submitBtn.disabled = true;
    }
  });

  // Marcar item activo en navbar (incluye items dentro de dropdown)
  document.addEventListener('DOMContentLoaded', () => {
    const c = new URLSearchParams(location.search).get('c') || 'dashboard';

    // Links directos
    App.qsa('.navbar-nav .nav-link').forEach(link => {
      const u = new URL(link.href, location.href);
      if (u.searchParams.get('c') === c) link.classList.add('active');
    });

    // Items dentro de dropdown (e.g., Configuración → Usuarios)
    const activeItem = App.qsa('.navbar-nav .dropdown-item').find(a => {
      const u = new URL(a.href, location.href);
      return u.searchParams.get('c') === c;
    });
    if (activeItem) {
      activeItem.classList.add('active');
      const toggle = activeItem.closest('.dropdown')?.querySelector('.nav-link.dropdown-toggle');
      if (toggle) toggle.classList.add('active');
    }
  });

  // Mostrar/ocultar campos según tipo de cliente
  function toggleTipoCliente() {
    const tipo = App.qs('#tipo');
    const boxNat = App.qs('#box-natural');
    const boxJur = App.qs('#box-juridica');
    if (!tipo || !boxNat || !boxJur) return;

    const apply = () => {
      if (tipo.value === 'NATURAL') {
        boxNat.style.display = '';
        boxJur.style.display = 'none';
      } else {
        boxNat.style.display = 'none';
        boxJur.style.display = '';
      }
    };
    tipo.addEventListener('change', apply);
    apply();
  }
  document.addEventListener('DOMContentLoaded', toggleTipoCliente);

  // Evitar navegación accidental en anchors vacíos,
  // pero permitir el toggle de dropdowns de Bootstrap.
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href="#"]');
    if (a && !a.hasAttribute('data-bs-toggle')) e.preventDefault();
  });

})();
