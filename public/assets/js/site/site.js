/* ==========================================================
   LUMING — comportamento do SITE PÚBLICO (JS puro, sem dependências).
   Carregado apenas no layout 'site'. O dashboard tem seu próprio JS.
   Cada bloco só age se encontrar o seu elemento na página.
   ========================================================== */
(function () {
  'use strict';

  /* ---------- Menu mobile (navbar) ---------- */
  var navbar = document.querySelector('[data-navbar]');
  if (navbar) {
    var toggle = navbar.querySelector('[data-menu-toggle]');
    var menu = navbar.querySelector('.nav-links');
    var iconOpen = navbar.querySelector('[data-icon-open]');
    var iconClose = navbar.querySelector('[data-icon-close]');

    var setMenu = function (aberto) {
      menu.classList.toggle('open', aberto);
      toggle.setAttribute('aria-expanded', String(aberto));
      toggle.setAttribute('aria-label', aberto ? 'Fechar menu' : 'Abrir menu');
      iconOpen.hidden = aberto;
      iconClose.hidden = !aberto;
    };

    toggle.addEventListener('click', function () {
      setMenu(!menu.classList.contains('open'));
    });
    menu.addEventListener('click', function (e) {
      if (e.target.closest('a')) setMenu(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setMenu(false);
    });
  }

  /* ---------- FAQ (acordeão: um item aberto por vez) ---------- */
  document.querySelectorAll('[data-faq]').forEach(function (lista) {
    lista.addEventListener('click', function (e) {
      var botao = e.target.closest('.faq-item > button');
      if (!botao) return;

      var item = botao.parentElement;
      var abrir = !item.classList.contains('open');

      lista.querySelectorAll('.faq-item').forEach(function (outro) {
        outro.classList.remove('open');
        outro.querySelector('button').setAttribute('aria-expanded', 'false');
      });

      if (abrir) {
        item.classList.add('open');
        botao.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ---------- Portfólio: filtro por ano ---------- */
  var filtros = document.querySelector('[data-portfolio-filters]');
  if (filtros) {
    var cards = document.querySelectorAll('.portfolio-card');

    filtros.addEventListener('click', function (e) {
      var botao = e.target.closest('button[data-filtro]');
      if (!botao) return;

      filtros.querySelectorAll('button').forEach(function (b) {
        b.classList.toggle('active', b === botao);
      });

      var filtro = botao.getAttribute('data-filtro');
      cards.forEach(function (card) {
        card.hidden = filtro !== 'todos' && card.getAttribute('data-ano') !== filtro;
      });
    });
  }

  /* ---------- Projeto: carrossel da galeria ---------- */
  document.querySelectorAll('[data-carousel]').forEach(function (carousel) {
    var palco = carousel.querySelector('[data-carousel-stage]');
    var miniaturas = Array.prototype.slice.call(carousel.querySelectorAll('.carousel-thumbnails button'));
    if (!palco || miniaturas.length === 0) return;

    var ativo = 0;

    var icone = function (paths, tamanho) {
      return '<svg xmlns="http://www.w3.org/2000/svg" width="' + tamanho + '" height="' + tamanho +
        '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
        paths + '</svg>';
    };

    var mostrar = function (indice) {
      ativo = (indice + miniaturas.length) % miniaturas.length;
      var dados = miniaturas[ativo].dataset;

      miniaturas.forEach(function (m, i) {
        m.classList.toggle('active', i === ativo);
        m.setAttribute('aria-selected', String(i === ativo));
      });

      palco.innerHTML = '';

      var midia;
      if (dados.tipo === 'video') {
        midia = document.createElement('video');
        midia.src = dados.src;
        midia.controls = true;
        midia.setAttribute('playsinline', '');
        midia.setAttribute('aria-label', dados.alt);
      } else {
        midia = document.createElement('img');
        midia.src = dados.src;
        midia.alt = dados.alt;
      }
      palco.appendChild(midia);

      if (miniaturas.length > 1) {
        var anterior = document.createElement('button');
        anterior.type = 'button';
        anterior.className = 'carousel-control carousel-prev';
        anterior.setAttribute('aria-label', 'Imagem anterior');
        anterior.innerHTML = icone('<path d="m15 18-6-6 6-6"/>', 20);
        anterior.addEventListener('click', function () { mostrar(ativo - 1); });

        var proxima = document.createElement('button');
        proxima.type = 'button';
        proxima.className = 'carousel-control carousel-next';
        proxima.setAttribute('aria-label', 'Próxima imagem');
        proxima.innerHTML = icone('<path d="m9 18 6-6-6-6"/>', 20);
        proxima.addEventListener('click', function () { mostrar(ativo + 1); });

        palco.appendChild(anterior);
        palco.appendChild(proxima);
      }

      if (dados.tipo === 'video') {
        var rotulo = document.createElement('span');
        rotulo.className = 'carousel-media-label';
        rotulo.innerHTML = icone('<polygon points="6 3 20 12 6 21 6 3"/>', 12) + ' vídeo';
        palco.appendChild(rotulo);
      }
    };

    miniaturas.forEach(function (m, i) {
      m.addEventListener('click', function () { mostrar(i); });
    });

    mostrar(0);
  });

  /* ---------- Formulário de contato: máscara de telefone (BR) ---------- */
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
})();
