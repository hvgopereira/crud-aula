<?php
session_start();
include 'db.php';

// Ações do usuário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $idade = (int)$_POST['idade'];
    $genero = $_POST['genero'];
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        $sql = "INSERT INTO users (nome, idade, genero) VALUES ('$nome', $idade, '$genero')";
        $conn->query($sql);
    } elseif ($action == 'edit') {
        $id = (int)$_POST['id'];
        $sql = "UPDATE users SET nome='$nome', idade=$idade, genero='$genero' WHERE id=$id";
        $conn->query($sql);
    }
    header("Location: index.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $sql = "DELETE FROM users WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

// Pesquisa
$searchTerm = $_GET['search'] ?? '';
$sql = "SELECT * FROM users WHERE nome LIKE '%$searchTerm%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=add"/>
    <link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>CRUD Simples</title>
</head>
<body>
    <h1>Cadastro de Usuários</h1>

    <div class="form-wrapper">
        <form method="POST" id="userForm">
            <input type="hidden" name="id" id="userId">
            <input type="text" name="nome" placeholder="Nome" required>
            <input type="number" name="idade" placeholder="Idade" required>
            <select name="genero" required>
                <option value="">Gênero</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
            </select>
            <button class="btn-add" type="submit" name="action" value="add">Adicionar <span class="material-symbols-outlined">
                    add
                </span>
            </button>
        </form>

        <form method="GET">
            <input type="text" name="search" placeholder="Pesquisar por nome" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button class="btn-search" type="submit">Pesquisar <span class="material-symbols-outlined">
                    search
                </span>
            </button>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>Nome</th>
                <th>Idade</th>
                <th>Gênero</th>
                <th>Ações</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['idade']); ?></td>
                    <td><?php echo htmlspecialchars($row['genero']); ?></td>
                    <td>
                        <button onclick="editUser(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nome']); ?>', <?php echo $row['idade']; ?>, '<?php echo $row['genero']; ?>')">Editar</button>
                        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        function editUser(id, nome, idade, genero) {
            document.getElementById('userId').value = id;
            document.getElementsByName('nome')[0].value = nome;
            document.getElementsByName('idade')[0].value = idade;
            document.getElementsByName('genero')[0].value = genero;
            document.querySelector('button[type="submit"]').name = 'action';
            document.querySelector('button[type="submit"]').value = 'edit';
        }
    </script>
</body>
</html>
