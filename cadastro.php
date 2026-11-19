<?php
session_start();

$arquivo = __DIR__ . '/data/usuarios.csv';
$tiposPet = ['Cachorro', 'Gato', 'Passaro', 'Outro'];

$erros   = [];
$sucesso = false;
$dados   = ['nome' => '', 'email' => '', 'senha' => '', 'telefone' => '', 'pet_nome' => '', 'pet_tipo' => ''];

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $erros[] = 'Sessão expirada. Recarregue a página e tente de novo.';
    }

    foreach ($dados as $campo => $valor) {
        $dados[$campo] = trim($_POST[$campo] ?? '');
    }

    if ($dados['nome'] === '' || $dados['email'] === '' || $dados['senha'] === '') {
        $erros[] = 'Preencha nome, e-mail e senha corretamente.';
    }

    if (empty($erros) && file_exists($arquivo)) {
        $fp = fopen($arquivo, 'r');
        fgetcsv($fp);
        while (($linha = fgetcsv($fp)) !== false) {
            if (isset($linha[1]) && strtolower($linha[1]) === strtolower($dados['email'])) {
                $erros[] = 'Este e-mail já está cadastrado.';
                break;
            }
        }
        fclose($fp);
    }

    if (empty($erros)) {
        $criarCabecalho = !file_exists($arquivo) || filesize($arquivo) === 0;
        $fp = fopen($arquivo, 'a');
        if ($criarCabecalho) {
            fputcsv($fp, ['Nome', 'Email', 'Senha', 'Telefone', 'Pet_Nome', 'Pet_Tipo']);
        }
        fputcsv($fp, [
            $dados['nome'],
            $dados['email'],
            $dados['senha'],
            $dados['telefone'],
            $dados['pet_nome'],
            $dados['pet_tipo']
        ]);
        fclose($fp);

        $sucesso = true;
        $dados = ['nome' => '', 'email' => '', 'senha' => '', 'telefone' => '', 'pet_nome' => '', 'pet_tipo' => ''];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Charlie Petz</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <!-- Logo corrigida apontando para index.php -->
        <a href="index.php" class="logo-link">
            <div class="logo-area">
                <img src="imagens/logo-charlie.png" alt="Charlie Petz" class="logo-img">
            </div>
        </a>

        <div class="user-links">
            <a href="index.php">Voltar para a Loja</a>
            <a href="login.php">Já tenho conta (Login)</a>
        </div>
    </header>

    <main>
        <div class="form-box">
            <h2>Criar Conta e Cadastrar Pet</h2>

            <?php if (!empty($erros)): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 18px;">
                    <?php foreach ($erros as $erro): ?>
                        <p><?= e($erro) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 8px; margin-bottom: 18px; text-align: center;">
                    <p>Cadastro realizado com sucesso!</p>
                    <p><a href="login.php" style="font-weight: bold; color: #15803d;">Clique aqui para fazer Login</a></p>
                </div>
            <?php endif; ?>

            <form action="cadastro.php" method="POST" id="formCadastro">
                <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">

                <label for="nome">Seu Nome Completo:</label>
                <input type="text" id="nome" name="nome" value="<?= e($dados['nome']) ?>" placeholder="Ex: Rafaela Silva" required>

                <label for="email">Seu E-mail:</label>
                <input type="email" id="email" name="email" value="<?= e($dados['email']) ?>" placeholder="rafaela@email.com" required>

                <label for="senha">Crie uma Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Mínimo 4 caracteres" required>

                <label for="telefone">Telefone / WhatsApp:</label>
                <input type="text" id="telefone" name="telefone" value="<?= e($dados['telefone']) ?>" placeholder="(11) 99999-9999" maxlength="15" required>

                <label for="pet_nome">Nome do seu Pet:</label>
                <input type="text" id="pet_nome" name="pet_nome" value="<?= e($dados['pet_nome']) ?>" placeholder="Ex: Charlie" required>

                <label for="pet_tipo">Tipo de Pet:</label>
                <select id="pet_tipo" name="pet_tipo" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($tiposPet as $tipo): ?>
                        <option value="<?= e($tipo) ?>" <?= $dados['pet_tipo'] === $tipo ? 'selected' : '' ?>>
                            <?= e($tipo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-primary">Finalizar Cadastro</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Charlie Petz - Amor e cuidado para o seu melhor amigo.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>