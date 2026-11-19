<?php
session_start();
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $arquivo = __DIR__ . '/data/usuarios.csv';

    if (file_exists($arquivo) && ($fp = fopen($arquivo, 'r')) !== false) {
        fgetcsv($fp); // Pula o cabeçalho
        $autenticado = false;

        while (($dados = fgetcsv($fp)) !== false) {
            if (count($dados) >= 3) {
                if (strtolower($dados[1]) === strtolower($email) && $dados[2] === $senha) {
                    $_SESSION['usuario_nome']  = $dados[0];
                    $_SESSION['usuario_email'] = $dados[1];
                    $autenticado = true;
                    break;
                }
            }
        }
        fclose($fp);

        if ($autenticado) {
            if ($_SESSION['usuario_email'] === 'admin@charliepetz.com') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    } else {
        $erro = "Arquivo de usuários não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Charlie Petz</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <!-- Logo nova com link para o index.php -->
        <a href="index.php" class="logo-link">
            <div class="logo-area">
                <img src="imagens/logo-charlie.png" alt="Charlie Petz" class="logo-img">
            </div>
        </a>

        <div class="user-links">
            <a href="index.php">Voltar para a Loja</a>
            <a href="cadastro.php">Criar Conta</a>
        </div>
    </header>

    <main>
        <div class="form-box">
            <h2>Entrar na Conta</h2>

            <?php if ($erro): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                <button type="submit" class="btn-primary">Entrar</button>
            </form>

            <p style="text-align: center; margin-top: 15px; font-size: 0.9rem;">
                Ainda não tem cadastro? <a href="cadastro.php">Cadastre-se aqui</a>
            </p>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Charlie Petz - Amor e cuidado para o seu melhor amigo.</p>
    </footer>
</body>
</html>