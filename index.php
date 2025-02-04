<?php
session_start();
include 'db.php';

// Proteção contra SQL Injection
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = (int) $_POST['delete'];
        $sql = "DELETE FROM users WHERE id=$id";
        $conn->query($sql);
        header("Location: index.php");
        exit();
    }
    
    $nome = sanitize($_POST['nome']);
    $idade = (int) $_POST['idade'];
    $genero = sanitize($_POST['genero']);
    $action = $_POST['action'] ?? '';

    if ($action == 'add' && !empty($nome) && !empty($idade) && !empty($genero)) {
        $sql = "INSERT INTO users (nome, idade, genero) VALUES ('$nome', $idade, '$genero')";
        $conn->query($sql);
    } elseif ($action == 'edit' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $sql = "UPDATE users SET nome='$nome', idade=$idade, genero='$genero' WHERE id=$id";
        $conn->query($sql);
    }
    header("Location: index.php");
    exit();
}

$searchTerm = sanitize($_GET['search'] ?? '');
$sql = "SELECT * FROM users WHERE nome LIKE '%$searchTerm%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CRUD</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=person_edit_manage_accounts_delete_search_add_supervisor_account_person_hourglass_bottom_male_build" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
      <a href="https://github.com/hvgopereira">
         <img class="logo-hp" style="width: 80px;" src="logohp-web.png" title="Hugo Pereira" alt="Hugo Pereira">
      </a>
      <h1 class="title-crud">Cadastro de Usuários  <span class="material-symbols-outlined icon-title">
      supervisor_account
      </span></h1>
 
    <div class="form-wrapper">
        <form class="formulario" method="POST" id="userForm">
            <input type="hidden" name="id" id="userId">
            <input type="text" name="nome" placeholder="Nome" required>
            <input class="age-input" type="number" name="idade" placeholder="Idade" required>
            <select name="genero" required>
                <option value="">Gênero</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
                <option value="Outro">Outro</option>
            </select>
            <button class="btn-add" type="submit" name="action" value="add"> <span title="Adicionar" class="material-symbols-outlined">add</span></button>
        </form>
        
        <form method="GET">
            <input type="text" name="search" placeholder="Pesquisar por nome" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button title="Pesquisar" class="btn-search" type="submit"> <span class="material-symbols-outlined">search</span></button>
        </form>
    </div>

    <div class="table-wrapper">
        <table>
            <tr>
               <div class="testea">
                  <th>Nome<span class="material-symbols-outlined name-table">
                  person
                  </span> </th>
               </div>
                <th>Idade <span class="material-symbols-outlined age-table">
               hourglass_bottom
               </span></th>
                <th>Gênero <span class="material-symbols-outlined gender-table">
               male
               </span></th>
                <th class="btns-form">Ações <span class="material-symbols-outlined action-table">
            build
            </span></th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['idade']); ?></td>
                    <td><?php echo htmlspecialchars($row['genero']); ?></td>
                    <td class="btns-form">
                        <button class="edit" onclick="editUser(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nome']); ?>', <?php echo $row['idade']; ?>, '<?php echo $row['genero']; ?>')">
                           Editar <span class="material-symbols-outlined edit-icon">person_edit</span>
                        </button>
                        
                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?');" action="index.php">
                            <input type="hidden" name="delete" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-delete"><span class="material-symbols-outlined delete-icon">delete</span></button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Usuário <span class="material-symbols-outlined">manage_accounts</span></h2>
            <form class="form-edit" method="POST">
                <input type="hidden" name="id" id="editUserId">
                <input type="text" name="nome" id="editNome" placeholder="Nome" required>
                <input class="age-input" type="number" name="idade" id="editIdade" placeholder="Idade" required>
                <select name="genero" id="editGenero" required>
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                    <option value="Outro">Outro</option>
                </select>
                <button class="btn-edit" type="submit" name="action" value="edit">Salvar</button>
            </form>
        </div>
    </div>

    <script>
    const modal = document.getElementById('editModal');
    const closeModal = document.querySelector('.close');

    window.onload = function() {
        modal.style.display = 'none';
    }

    function editUser(id, nome, idade, genero) {
        document.getElementById('editUserId').value = id;
        document.getElementById('editNome').value = nome;
        document.getElementById('editIdade').value = idade;
        document.getElementById('editGenero').value = genero;
        modal.style.display = 'block';
    }

    closeModal.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
    </script>
</body>
</html>
