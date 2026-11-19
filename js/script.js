let produtos = [];
let carrinho = JSON.parse(localStorage.getItem('charlie_petz_cart')) || [];

document.addEventListener('DOMContentLoaded', () => {
    atualizarContadorCarrinho();
    inicializarMascaraTelefone();
    inicializarValidacaoCadastro();

    if (document.getElementById('productList')) {
        carregarProdutos(() => {
            renderProducts(produtos);
            iniciarCarrosselOfertas(produtos);
        });
    }

    if (document.getElementById('ofertasList')) {
        carregarProdutos(() => renderDestaques(produtos));
    }

    if (document.getElementById('cartItems')) {
        renderCarrinho();
    }
});

/* ==========================================
   CONTROLE DA BARRA LATERAL POR CLIQUE
   ========================================== */
function toggleSidebar() {
    const sidebar = document.getElementById('meuSidebar');
    if (sidebar) {
        sidebar.classList.toggle('ativa');
    }
}

document.addEventListener('click', (event) => {
    const sidebar = document.getElementById('meuSidebar');
    const trigger = document.querySelector('.sidebar-trigger');
    
    if (sidebar && sidebar.classList.contains('ativa')) {
        if (!sidebar.contains(event.target) && !trigger.contains(event.target)) {
            sidebar.classList.remove('ativa');
        }
    }
});

/* ==========================================
   CARREGAMENTO DE PRODUTOS DO CSV
   ========================================== */
function carregarProdutos(callback) {
    if (produtos.length > 0) {
        if (callback) callback();
        return;
    }

    fetch('importar_csv.php')
        .then(res => {
            if (!res.ok) throw new Error('Erro ao carregar o CSV');
            return res.json();
        })
        .then(data => {
            produtos = data;
            if (callback) callback();
        })
        .catch(err => console.error('Erro:', err));
}

function calcularDescontoProduto(nome) {
    let hash = 0;
    for (let i = 0; i < nome.length; i++) {
        hash = nome.charCodeAt(i) + ((hash << 5) - hash);
    }
    return Math.abs(hash % 16) + 15; // Entre 15% e 30% OFF
}

function criarCardProduto(p) {
    const card = document.createElement('div');
    card.className = 'produto';
    const imgPath = p.imagem ? `imagens/${p.imagem}` : 'imagens/default.jpg';

    let badgeDescontoHtml = '';
    let precoHtml = `<div class="preco">R$ ${p.preco.toFixed(2).replace('.', ',')}</div>`;

    if (p.destaque === 'oferta') {
        const percentual = calcularDescontoProduto(p.nome);
        const precoAntigo = p.preco / (1 - percentual / 100);

        badgeDescontoHtml = `<span class="badge-desconto">-${percentual}% OFF</span>`;
        precoHtml = `
            <div class="preco-wrapper">
                <span class="preco-antigo">De R$ ${precoAntigo.toFixed(2).replace('.', ',')}</span>
                <div class="preco">Por R$ ${p.preco.toFixed(2).replace('.', ',')}</div>
            </div>
        `;
    }

    card.innerHTML = `
        ${badgeDescontoHtml}
        <div style="cursor: pointer;" onclick="window.location.href='produto.php?nome=' + encodeURIComponent('${p.nome.replace(/'/g, "\\'")}')">
            <img src="${imgPath}" alt="${p.nome}" onerror="this.src='imagens/default.jpg';">
            <span class="badge-categoria">${p.categoria}</span>
            <h3>${p.nome}</h3>
            <p>${p.descricao.substring(0, 60)}...</p>
        </div>
        <div>
            ${precoHtml}
            <button onclick="adicionarAoCarrinho('${p.nome.replace(/'/g, "\\'")}', ${p.preco})">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                Adicionar ao Carrinho
            </button>
        </div>
    `;
    return card;
}

/* ==========================================
   RENDERIZAÇÃO E BUSCA NA HOME
   ========================================== */
function renderProducts(lista) {
    const container = document.getElementById('productList');
    if (!container) return;
    container.innerHTML = '';

    if (lista.length === 0) {
        container.innerHTML = '<p>Nenhum produto encontrado.</p>';
        return;
    }

    lista.forEach(p => container.appendChild(criarCardProduto(p)));
}

function searchProducts() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    const termo = input.value.toLowerCase().trim();

    if (termo === '') {
        renderProducts(produtos);
        return;
    }

    const resultados = produtos.filter(p =>
        p.nome.toLowerCase().includes(termo) ||
        p.descricao.toLowerCase().includes(termo)
    );
    renderProducts(resultados);
}

function applyFilters() {
    const categoria = document.getElementById('categoryFilter').value;
    const filtrados = categoria === '' ? produtos : produtos.filter(p => p.categoria === categoria);
    renderProducts(filtrados);
}

/* ==========================================
   RENDERIZAÇÃO E BUSCA NA PÁGINA DE DESTAQUES
   ========================================== */
function renderDestaques(lista) {
    const ofertasContainer = document.getElementById('ofertasList');
    const maisVendidosContainer = document.getElementById('maisVendidosList');

    if (ofertasContainer) ofertasContainer.innerHTML = '';
    if (maisVendidosContainer) maisVendidosContainer.innerHTML = '';

    lista.forEach(p => {
        if (p.destaque === 'oferta' && ofertasContainer) {
            ofertasContainer.appendChild(criarCardProduto(p));
        }
        if (p.destaque === 'mais_vendido' && maisVendidosContainer) {
            maisVendidosContainer.appendChild(criarCardProduto(p));
        }
    });
}

function searchDestaques() {
    const input = document.getElementById('searchDestaques');
    if (!input) return;
    const termo = input.value.toLowerCase().trim();

    if (termo === '') {
        renderDestaques(produtos);
        return;
    }

    const filtrados = produtos.filter(p =>
        (p.destaque === 'oferta' || p.destaque === 'mais_vendido') &&
        (p.nome.toLowerCase().includes(termo) || p.descricao.toLowerCase().includes(termo))
    );
    renderDestaques(filtrados);
}

/* ==========================================
   MÁSCARA DE TELEFONE E VALIDAÇÃO
   ========================================== */
function inicializarMascaraTelefone() {
    const inputTelefone = document.getElementById('telefone');
    if (!inputTelefone) return;

    inputTelefone.addEventListener('input', (e) => {
        let valor = e.target.value.replace(/\D/g, '');
        if (valor.length > 11) valor = valor.slice(0, 11);

        if (valor.length > 6) {
            valor = valor.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (valor.length > 2) {
            valor = valor.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        } else if (valor.length > 0) {
            valor = valor.replace(/^(\d*)/, '($1');
        }

        e.target.value = valor;
    });
}

function inicializarValidacaoCadastro() {
    const formCadastro = document.getElementById('formCadastro');
    if (!formCadastro) return;

    formCadastro.addEventListener('submit', (e) => {
        const senha = document.getElementById('senha').value.trim();
        const telefone = document.getElementById('telefone').value.trim();

        if (senha.length < 4) {
            e.preventDefault();
            alert('A senha deve ter pelo menos 4 caracteres.');
            return;
        }

        if (telefone.length < 14) {
            e.preventDefault();
            alert('Por favor, digite um número de telefone válido com DDD.');
            return;
        }
    });
}

/* ==========================================
   CARRINHO DE COMPRAS E QUANTIDADE
   ========================================== */
function adicionarAoCarrinho(nome, preco) {
    carrinho.push({ nome, preco });
    localStorage.setItem('charlie_petz_cart', JSON.stringify(carrinho));
    atualizarContadorCarrinho();
    alert(`"${nome}" foi adicionado ao carrinho!`);
}

function adicionarComQuantidade(nome, preco, irParaCarrinho) {
    const inputQtd = document.getElementById('qtdProduto');
    const quantidade = inputQtd ? parseInt(inputQtd.value) || 1 : 1;

    for (let i = 0; i < quantidade; i++) {
        carrinho.push({ nome, preco });
    }

    localStorage.setItem('charlie_petz_cart', JSON.stringify(carrinho));
    atualizarContadorCarrinho();

    if (irParaCarrinho) {
        window.location.href = 'cart.html';
    } else {
        alert(`${quantidade}x "${nome}" adicionado(s) ao carrinho com sucesso!`);
    }
}

function atualizarContadorCarrinho() {
    const contador = document.getElementById('cartCount');
    if (contador) contador.innerText = carrinho.length;
}

function renderCarrinho() {
    const container = document.getElementById('cartItems');
    const totalEl = document.getElementById('cartTotal');
    if (!container) return;
    container.innerHTML = '';
    let total = 0;

    if (carrinho.length === 0) {
        container.innerHTML = '<p>Seu carrinho está vazio.</p>';
        if (totalEl) totalEl.innerText = 'Total: R$ 0.00';
        return;
    }

    carrinho.forEach((item, index) => {
        total += item.preco;
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.innerHTML = `
            <span><strong>${item.nome}</strong> - R$ ${item.preco.toFixed(2)}</span>
            <button class="btn-remove" onclick="removerDoCarrinho(${index})">Remover</button>
        `;
        container.appendChild(div);
    });

    if (totalEl) totalEl.innerText = `Total: R$ ${total.toFixed(2)}`;
}

function removerDoCarrinho(index) {
    carrinho.splice(index, 1);
    localStorage.setItem('charlie_petz_cart', JSON.stringify(carrinho));
    renderCarrinho();
    atualizarContadorCarrinho();
}

function finalizarCompra() {
    if (carrinho.length === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }
    alert('Compra finalizada com sucesso! Obrigado por comprar na Charlie Petz.');
    carrinho = [];
    localStorage.removeItem('charlie_petz_cart');
    renderCarrinho();
    atualizarContadorCarrinho();
}

/* ==========================================
   CARROSSEL DE OFERTAS DINÂMICO (CHARLIE PETZ)
   ========================================== */
let bannerIndex = 0;
let bannerInterval = null;
let timeoutRetomada = null;
let ofertasCarrossel = [];
let isPaused = false;

function iniciarCarrosselOfertas(listaProdutos) {
    ofertasCarrossel = listaProdutos.filter(p => p.destaque === 'oferta');
    if (ofertasCarrossel.length === 0) {
        ofertasCarrossel = listaProdutos.slice(0, 4);
    }

    const container = document.getElementById('bannerCarrossel');
    if (!container || ofertasCarrossel.length === 0) return;

    container.addEventListener('mouseenter', pausarCarrossel);
    container.addEventListener('mouseleave', retomarCarrossel);
    container.addEventListener('click', () => {
        pausarCarrossel();
        clearTimeout(timeoutRetomada);
        timeoutRetomada = setTimeout(retomarCarrossel, 8000);
    });

    mudarSlide(0);
    iniciarIntervaloCarrossel();
}

function mudarSlide(index) {
    const ofertasCarrosselContainer = document.getElementById('bannerCarrossel');
    if (!ofertasCarrosselContainer || ofertasCarrossel.length === 0) return;

    bannerIndex = (index + ofertasCarrossel.length) % ofertasCarrossel.length;
    const produtoAtual = ofertasCarrossel[bannerIndex];
    const imgPath = produtoAtual.imagem ? `imagens/${produtoAtual.imagem}` : 'imagens/default.jpg';
    const percentual = calcularDescontoProduto(produtoAtual.nome);
    const precoAntigo = produtoAtual.preco / (1 - percentual / 100);

    const slideArea = ofertasCarrosselContainer.querySelector('.banner-slide');
    if (slideArea) {
        slideArea.classList.remove('active');
        void slideArea.offsetWidth; // Reinicia a animação CSS

        slideArea.innerHTML = `
            <div class="banner-text-content">
                <span class="banner-brand">CHARLIE PETZ &bull; -${percentual}% OFF</span>
                <h2>${produtoAtual.nome}</h2>
                <div class="banner-price-group">
                    <span class="banner-old-price">De R$ ${precoAntigo.toFixed(2).replace('.', ',')}</span>
                    <div class="banner-price-tag">Por R$ ${produtoAtual.preco.toFixed(2).replace('.', ',')}</div>
                </div>
                <a href="produto.php?nome=${encodeURIComponent(produtoAtual.nome)}" class="btn-banner-oferta">
                    Ver oferta
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="banner-image-content">
                <img src="${imgPath}" alt="${produtoAtual.nome}" onerror="this.src='imagens/default.jpg';">
            </div>
        `;

        slideArea.classList.add('active');
    }

    atualizarIndicadores();
}

function atualizarIndicadores() {
    const indicatorsContainer = document.getElementById('bannerIndicators');
    if (!indicatorsContainer) return;

    indicatorsContainer.innerHTML = '';
    ofertasCarrossel.forEach((_, idx) => {
        const bar = document.createElement('div');
        bar.className = `indicator-bar ${idx === bannerIndex ? 'active' : ''} ${isPaused ? 'paused' : ''}`;
        bar.innerHTML = `<div class="indicator-progress"></div>`;
        bar.onclick = (e) => {
            e.stopPropagation();
            mudarSlide(idx);
            reiniciarIntervaloCarrossel();
        };
        indicatorsContainer.appendChild(bar);
    });
}

function mudarSlideManual(direcao) {
    mudarSlide(bannerIndex + direcao);
    reiniciarIntervaloCarrossel();
}

function iniciarIntervaloCarrossel() {
    clearInterval(bannerInterval);
    if (isPaused) return;

    bannerInterval = setInterval(() => {
        mudarSlide(bannerIndex + 1);
    }, 6000);
}

function reiniciarIntervaloCarrossel() {
    iniciarIntervaloCarrossel();
}

function pausarCarrossel() {
    isPaused = true;
    clearInterval(bannerInterval);
    document.querySelectorAll('.indicator-bar').forEach(bar => bar.classList.add('paused'));
}

function retomarCarrossel() {
    isPaused = false;
    document.querySelectorAll('.indicator-bar').forEach(bar => bar.classList.remove('paused'));
    iniciarIntervaloCarrossel();
}