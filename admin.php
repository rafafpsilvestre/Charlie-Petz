<?php
session_start();

if (!isset($_SESSION['usuario_email']) || $_SESSION['usuario_email'] !== 'admin@charliepetz.com') {
    header('Location: login.php');
    exit;
}

$arquivo = __DIR__ . '/data/usuarios.csv';

if (isset($_GET['excluir']) && file_exists($arquivo)) {
    $indiceExcluir = intval($_GET['excluir']);
    $linhas = [];

    $fp = fopen($arquivo, 'r');
    while (($dados = fgetcsv($fp)) !== false) {
        $linhas[] = $dados;
    }
    fclose($fp);

    $linhaAlvo = $indiceExcluir + 1;
    if ($linhaAlvo > 1 && isset($linhas[$linhaAlvo])) {
        unset($linhas[$linhaAlvo]);
        $fp = fopen($arquivo, 'w');
        foreach ($linhas as $linha) {
            fputcsv($fp, $linha);
        }
        fclose($fp);
    }

    header('Location: admin.php');
    exit;
}

$clientes = [];
if (file_exists($arquivo)) {
    $fp = fopen($arquivo, 'r');
    $primeira = true;
    while (($dados = fgetcsv($fp)) !== false) {
        if ($primeira) {
            $primeira = false;
            continue;
        }
        if (count($dados) >= 6) {
            $clientes[] = $dados;
        }
    }
    fclose($fp);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel CRUD - Charlie Petz</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
        th, td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background-color: #1e3c72; color: white; }
        tr:hover { background-color: #f8fafc; }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo-link">
            <div class="logo-area">
                <img src="imagens/logo-charlie.png" alt="Charlie Petz" class="logo-img">
            </div>
        </a>

        <div class="user-links">
            <a href="index.php">Ver Loja</a>
            <a href="cadastro.php">+ Novo Cadastro</a>
            <a href="logout.php">Sair</a>
        </div>
    </header>

    <main>
        <h2 class="section-title">
            <svg class="ui-icon-title" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            Clientes e Pets Cadastrados
        </h2>

        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Nome do Pet</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Nenhum cliente cadastrado ainda.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $index => $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c[0]) ?></td>
                            <td><?= htmlspecialchars($c[1]) ?></td>
                            <td><?= htmlspecialchars($c[3]) ?></td>
                            <td><?= htmlspecialchars($c[4]) ?></td>
                            <td><?= htmlspecialchars($c[5]) ?></td>
                            <td>
                                <?php if ($c[1] === 'admin@charliepetz.com'): ?>
                                    <span style="color: #64748b; font-size: 0.85rem;">Admin Principal</span>
                                <?php else: ?>
                                    <a href="admin.php?excluir=<?= $index ?>" 
                                       class="btn-remove" 
                                       style="text-decoration: none; font-size: 0.85rem;"
                                       onclick="return confirm('Tem certeza que deseja excluir este cadastro?');">
                                       Excluir
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy; 2026 Charlie Petz - Painel de Gerenciamento.</p>
    </footer>
</body>
</html>