<?php
session_start();
$usuarioLogado = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Charlie Petz - Início</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="sidebar-trigger" onclick="toggleSidebar()">
        <svg class="ui-icon" style="margin: 0;" viewBox="0 0 24 24"><path d="M3 6h18v2H3V6zm0 5h18v2H3v-2zm0 5h18v2H3v-2z"/></svg>
        Menu
    </div>

    <div class="sidebar" id="meuSidebar">
        <h3>
            <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 13.5c-2.33 0-4.31 1.46-5.11 3.5h10.22c-.8-2.04-2.78-3.5-5.11-3.5zM8.5 11c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm7 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-10.5 3c.83 0 1.5-.67 1.5-1.5S5.83 11 5 11s-1.5.67-1.5 1.5S4.17 14 5 14zm14 0c.83 0 1.5-.67 1.5-1.5S19.83 11 19 11s-1.5.67-1.5 1.5.67 1.5 1.5 1.5z"/></svg>
            Charlie Petz
        </h3>
        <ul>
            <li>
                <a href="index.php">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                    Página Inicial
                </a>
            </li>
            <li>
                <a href="destaques.php">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M13.5.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67z"/></svg>
                    Ofertas e Mais Vendidos
                </a>
            </li>
            <li>
                <a href="cart.html">
                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    Meu Carrinho
                </a>
            </li>
            <?php if ($usuarioLogado): ?>
                <li>
                    <a href="logout.php">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
                        Sair (<?= htmlspecialchars($usuarioLogado) ?>)
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/></svg>
                        Login
                    </a>
                </li>
                <li>
                    <a href="cadastro.php">
                        <svg class="ui-icon" viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        Cadastro
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="sidebar-hint">Clique no botão "Menu" na lateral para abrir ou fechar.</div>
    </div>

    <header>
        <a href="index.php" class="logo-link">
            <div class="logo-area">
                <img src="imagens/logo-charlie.png" alt="Charlie Petz" class="logo-img">
            </div>
        </a>

        <nav>
            <input type="text" id="searchInput" placeholder="Buscar no catálogo..." oninput="searchProducts()">
            <button onclick="searchProducts()">Buscar</button>

            <select id="categoryFilter" onchange="applyFilters()">
                <option value="">Todas as Categorias</option>
                <option value="Caes">Cães</option>
                <option value="Gatos">Gatos</option>
                <option value="Aves">Aves</option>
            </select>
        </nav>

        <div class="user-links">
            <?php if ($usuarioLogado): ?>
                <span>Olá, <strong><?= htmlspecialchars($usuarioLogado) ?></strong>!</span>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="cadastro.php">Cadastro</a>
            <?php endif; ?>
            <a href="cart.html">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                Carrinho (<span id="cartCount">0</span>)
            </a>
        </div>
    </header>

    <main>
        <!-- Carrossel de Ofertas com Cascata de Patinhas -->
        <div class="banner-container" id="bannerCarrossel">
            <!-- Cascata de Patinhas no Fundo -->
            <div class="paw-cascade-bg" aria-hidden="true">
                <?php for ($i = 0; $i < 16; $i++): ?>
                <div class="paw-drop">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 13.5c-2.33 0-4.31 1.46-5.11 3.5h10.22c-.8-2.04-2.78-3.5-5.11-3.5zM8.5 11c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm7 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-10.5 3c.83 0 1.5-.67 1.5-1.5S5.83 11 5 11s-1.5.67-1.5 1.5S4.17 14 5 14zm14 0c.83 0 1.5-.67 1.5-1.5S19.83 11 19 11s-1.5.67-1.5 1.5.67 1.5 1.5 1.5z"/>
                    </svg>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Setinhas laterais SVG -->
            <button class="banner-btn-prev" onclick="mudarSlideManual(-1)" aria-label="Anterior">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <button class="banner-btn-next" onclick="mudarSlideManual(1)" aria-label="Próximo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>

            <div class="banner-slide active">
                <div class="banner-text-content">
                    <span class="banner-brand">CHARLIE PETZ &bull; OFERTA ESPECIAL</span>
                    <h2>OFERTAS<br>ESPECIAIS</h2>
                    <a href="destaques.php" class="btn-banner-oferta">Ver ofertas</a>
                </div>
                <div class="banner-image-content">
                    <img src="imagens/default.jpg" alt="Produto em Oferta">
                </div>
            </div>

            <div class="banner-indicators" id="bannerIndicators"></div>
        </div>

        <h2 class="section-title">
            <svg class="ui-icon-title" viewBox="0 0 24 24"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>
            Catálogo Completo
        </h2>
        <section id="productList"></section>
    </main>

    <footer>
        <p>&copy; 2026 Charlie Petz - Amor e cuidado para o seu melhor amigo.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>