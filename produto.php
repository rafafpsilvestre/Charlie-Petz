<?php
session_start();
$usuarioLogado = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : null;

// Pega o nome do produto passado pela URL
$nomeProduto = isset($_GET['nome']) ? trim($_GET['nome']) : '';

// Lê o CSV para encontrar o produto correspondente
$produtoEncontrado = null;
$csv_file = __DIR__ . '/data/produtos.csv';

if (file_exists($csv_file) && ($handle = fopen($csv_file, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); // Pula cabeçalho
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) < 6) continue;
        if (trim($data[0]) === $nomeProduto) {
            $produtoEncontrado = [
                "nome"      => trim($data[0]),
                "descricao" => trim($data[1]),
                "preco"     => floatval(str_replace(',', '.', $data[2])),
                "imagem"    => trim($data[3]),
                "categoria" => trim($data[4]),
                "destaque"  => trim($data[5]),
                "estoque"   => isset($data[6]) ? intval($data[6]) : rand(5, 20)
            ];
            break;
        }
    }
    fclose($handle);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $produtoEncontrado ? htmlspecialchars($produtoEncontrado['nome']) : 'Produto não encontrado' ?> - Charlie Petz</title>
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
                        Sair
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
        <div class="user-links">
            <a href="index.php">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Continuar Comprando
            </a>
            <a href="cart.html">
                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                Carrinho (<span id="cartCount">0</span>)
            </a>
        </div>
    </header>

    <main>
        <?php if (!$produtoEncontrado): ?>
            <div class="form-box" style="text-align: center;">
                <h2>Ops! Produto não encontrado.</h2>
                <p style="margin: 20px 0;">O produto que você tentou acessar não existe ou foi removido.</p>
                <a href="index.php" class="btn-primary" style="text-decoration: none; display: inline-block;">Voltar para a Loja</a>
            </div>
        <?php else: ?>
            <div class="detalhe-container" style="background: white; padding: 35px; border-radius: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); display: flex; flex-wrap: wrap; gap: 40px; margin-top: 30px;">
                <div style="flex: 1; min-width: 280px; text-align: center;">
                    <img src="imagens/<?= htmlspecialchars($produtoEncontrado['imagem']) ?>" alt="<?= htmlspecialchars($produtoEncontrado['nome']) ?>" style="width: 100%; max-height: 350px; object-fit: contain; border-radius: 10px; background: #f8fafc; padding: 10px;" onerror="this.src='imagens/default.jpg';">
                </div>

                <div style="flex: 1.5; min-width: 280px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <span class="badge-categoria"><?= htmlspecialchars($produtoEncontrado['categoria']) ?></span>
                        <h1 style="color: #0e2a5e; font-size: 2rem; margin: 10px 0;"><?= htmlspecialchars($produtoEncontrado['nome']) ?></h1>
                        <p style="color: #64748b; font-size: 1.05rem; line-height: 1.6; margin-bottom: 20px;"><?= htmlspecialchars($produtoEncontrado['descricao']) ?></p>
                        
                        <!-- Estoque e Ofertas Relâmpago -->
                        <div style="background: #f8fafc; border-left: 4px solid #ff9f1c; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px;">
                            <p style="font-weight: bold; color: #1e293b; margin-bottom: 5px; display: flex; align-items: center;">
                                <svg class="ui-icon" style="color: #0e2a5e;" viewBox="0 0 24 24"><path d="M21 16.5c0 .38-.21.71-.53.88l-7.9 4.44c-.16.12-.36.18-.57.18-.21 0-.41-.06-.57-.18l-7.9-4.44A.991.991 0 0 1 3 16.5v-9c0-.38.21-.71.53-.88l7.9-4.44c.16-.12.36-.18.57-.18.21 0 .41.06.57.18l7.9 4.44c.32.17.53.5.53.88v9zM12 4.15L6.04 7.5 12 10.85l5.96-3.35L12 4.15zM5 15.91l6 3.38v-6.71L5 9.21v6.7zm14 0v-6.7l-6 3.37v6.71l6-3.38z"/></svg>
                                Estoque Disponível: <span style="color: #16a34a; margin-left: 5px;"><?= $produtoEncontrado['estoque'] ?> unidades</span>
                            </p>
                            <?php if ($produtoEncontrado['destaque'] === 'oferta'): ?>
                                <p style="color: #dc2626; font-weight: bold; font-size: 0.95rem; display: flex; align-items: center;">
                                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                                    Oferta Relâmpago: Na compra deste item, leve a 2ª unidade com desconto especial!
                                </p>
                            <?php else: ?>
                                <p style="color: #2563eb; font-size: 0.9rem; display: flex; align-items: center;">
                                    <svg class="ui-icon" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    Condição especial para clientes cadastrados na Charlie Petz.
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Preço com De/Por e Desconto -->
                        <?php if ($produtoEncontrado['destaque'] === 'oferta'): ?>
                            <?php
                                // Cálculo seguro do percentual de desconto baseado no nome do produto
                                $hash = crc32($produtoEncontrado['nome']);
                                $percentual = abs($hash % 16) + 15; // Entre 15% e 30% OFF
                                $precoAntigo = $produtoEncontrado['preco'] / (1 - $percentual / 100);
                            ?>
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 2px;">
                                    <span style="color: #94a3b8; text-decoration: line-through; font-size: 1.05rem;">
                                        De R$ <?= number_format($precoAntigo, 2, ',', '.') ?>
                                    </span>
                                    <span style="background: #dc2626; color: white; font-size: 0.78rem; font-weight: 800; padding: 3px 8px; border-radius: 6px;">
                                        -<?= $percentual ?>% OFF
                                    </span>
                                </div>
                                <div class="preco" style="font-size: 2rem; margin-bottom: 0;">
                                    Por R$ <?= number_format($produtoEncontrado['preco'], 2, ',', '.') ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="preco" style="font-size: 2rem; margin-bottom: 20px;">
                                R$ <?= number_format($produtoEncontrado['preco'], 2, ',', '.') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sistema de Quantidade e Opções de Compra -->
                    <div style="background: #f1f5f9; padding: 20px; border-radius: 10px;">
                        <label for="qtdProduto" style="display: block; font-weight: 600; margin-bottom: 8px; color: #334155;">Quantidade desejada:</label>
                        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                            <input type="number" id="qtdProduto" value="1" min="1" max="<?= $produtoEncontrado['estoque'] ?>" style="width: 80px; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; text-align: center;">
                            
                            <button class="btn-primary" onclick="adicionarComQuantidade('<?= addslashes($produtoEncontrado['nome']) ?>', <?= $produtoEncontrado['preco'] ?>, false)" style="flex: 1; min-width: 160px; display: inline-flex; align-items: center; justify-content: center;">
                                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                Adicionar ao Carrinho
                            </button>
                            
                            <button class="btn-comprar-agora" onclick="adicionarComQuantidade('<?= addslashes($produtoEncontrado['nome']) ?>', <?= $produtoEncontrado['preco'] ?>, true)">
                                <svg class="ui-icon" viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
                                Comprar Agora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Charlie Petz - Amor e cuidado para o seu melhor amigo.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>