/* ==========================================================
   LUMING — comportamento do DASHBOARD (JS puro, sem dependências).
   Carregado apenas no layout 'dashboard'. O site público tem o seu próprio JS.
   Cada bloco só age se encontrar o seu elemento na página.
   ========================================================== */
(function () {
  'use strict';

  /* ---------- Sidebar recolhível (mobile) ---------- */
  var sidebar = document.getElementById('adm-sidebar');
  if (sidebar) {
    var overlay = document.querySelector('.adm-overlay');
    var botoesAbrir = document.querySelectorAll('[data-sidebar-open]');

    var setSidebar = function (aberta) {
      sidebar.classList.toggle('open', aberta);
      if (overlay) overlay.hidden = !aberta;
      botoesAbrir.forEach(function (botao) {
        botao.setAttribute('aria-expanded', String(aberta));
      });
    };

    botoesAbrir.forEach(function (botao) {
      botao.addEventListener('click', function () { setSidebar(true); });
    });
    if (overlay) overlay.addEventListener('click', function () { setSidebar(false); });
    sidebar.addEventListener('click', function (e) {
      if (e.target.closest('a')) setSidebar(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setSidebar(false);
    });
  }

  /* ---------- Modal de confirmação de exclusão ---------- */
  var modal = document.querySelector('[data-confirm]');
  if (modal) {
    var form = modal.querySelector('[data-confirm-form]');
    var texto = modal.querySelector('[data-confirm-text]');
    var botoesCancelar = modal.querySelectorAll('[data-confirm-cancel]');
    var ultimoFoco = null;

    var fechar = function () {
      modal.hidden = true;
      document.body.classList.remove('adm-modal-open');
      if (ultimoFoco) ultimoFoco.focus();
    };

    var abrir = function (botao) {
      ultimoFoco = botao;
      form.action = botao.getAttribute('data-action');

      texto.textContent = '';
      texto.appendChild(document.createTextNode('Você está prestes a excluir '));
      var nome = document.createElement('strong');
      nome.textContent = botao.getAttribute('data-nome') || 'este registro';
      texto.appendChild(nome);
      texto.appendChild(document.createTextNode('. Esta ação não pode ser desfeita.'));

      modal.hidden = false;
      document.body.classList.add('adm-modal-open');
      botoesCancelar[botoesCancelar.length - 1].focus();
    };

    document.addEventListener('click', function (e) {
      var botao = e.target.closest('[data-delete]');
      if (botao) {
        e.preventDefault();
        abrir(botao);
      }
    });

    botoesCancelar.forEach(function (botao) {
      botao.addEventListener('click', fechar);
    });
    modal.addEventListener('click', function (e) {
      if (e.target === modal) fechar();
    });

    document.addEventListener('keydown', function (e) {
      if (modal.hidden) return;

      if (e.key === 'Escape') {
        fechar();
        return;
      }

      // Mantém o foco dentro do modal enquanto ele estiver aberto.
      if (e.key === 'Tab') {
        var focaveis = modal.querySelectorAll('button, [href], input, select, textarea');
        var primeiro = focaveis[0];
        var ultimo = focaveis[focaveis.length - 1];

        if (e.shiftKey && document.activeElement === primeiro) {
          e.preventDefault();
          ultimo.focus();
        } else if (!e.shiftKey && document.activeElement === ultimo) {
          e.preventDefault();
          primeiro.focus();
        }
      }
    });

    // Evita enviar a exclusão duas vezes.
    form.addEventListener('submit', function () {
      var enviar = form.querySelector('button[type="submit"]');
      if (enviar) enviar.disabled = true;
    });
  }

  /* ---------- Toasts (somem sozinhos; clique para fechar) ---------- */
  document.querySelectorAll('[data-toast]').forEach(function (toast) {
    var remover = function () {
      toast.classList.add('hide');
      setTimeout(function () { toast.remove(); }, 350);
    };
    toast.addEventListener('click', remover);
    setTimeout(remover, 5000);
  });

  /* ---------- Listagens: busca na própria página ---------- */
  var normalizar = function (valor) {
    return String(valor).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  };

  document.querySelectorAll('[data-filter]').forEach(function (area) {
    var campo = area.querySelector('[data-filter-input]');
    var itens = area.querySelectorAll('[data-filter-item]');
    var vazio = area.querySelector('[data-filter-empty]');
    var contador = area.querySelector('[data-filter-count]');
    if (!campo) return;

    campo.addEventListener('input', function () {
      var termo = normalizar(campo.value.trim());
      var visiveis = 0;

      itens.forEach(function (item) {
        var base = item.getAttribute('data-filter-text') || item.textContent;
        var mostrar = termo === '' || normalizar(base).indexOf(termo) !== -1;
        item.hidden = !mostrar;
        if (mostrar) visiveis++;
      });

      if (contador) contador.textContent = String(visiveis);
      if (vazio) vazio.hidden = visiveis !== 0;
    });
  });

  /* ---------- Formulários: máscara de telefone (BR) ---------- */
  document.querySelectorAll('[data-mask-telefone]').forEach(function (campo) {
    campo.addEventListener('input', function () {
      var n = campo.value.replace(/\D/g, '').slice(0, 11);
      var r = n;

      if (n.length > 10) {
        r = '(' + n.slice(0, 2) + ') ' + n.slice(2, 7) + '-' + n.slice(7);
      } else if (n.length > 6) {
        r = '(' + n.slice(0, 2) + ') ' + n.slice(2, 6) + '-' + n.slice(6);
      } else if (n.length > 2) {
        r = '(' + n.slice(0, 2) + ') ' + n.slice(2);
      } else if (n.length > 0) {
        r = '(' + n;
      }

      campo.value = r;
    });
  });

  /* ---------- Formulários: evita duplo envio ---------- */
  document.querySelectorAll('.adm-form').forEach(function (formulario) {
    formulario.addEventListener('submit', function () {
      var enviar = formulario.querySelector('button[type="submit"]');
      if (enviar) {
        setTimeout(function () { enviar.disabled = true; }, 0);
      }
    });
  });
})();
