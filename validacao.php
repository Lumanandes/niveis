<?php
// Verifica se houve POST e se o usuário ou a senha é(são) vazio(s)
if (!empty($_POST) && (empty($_POST["usuario"]) || empty($_POST["senha"]))) {
    header("Location: index.php");
    exit;
}

// Tenta se conectar ao servidor MySQL
$mysqli = new mysqli("localhost", "root", "root", "bdniveis");

// Verifica conexão
if ($mysqli->connect_errno) {
    echo "Falha ao conectar ao MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit;
}

$usuario = $mysqli->real_escape_string($_POST["usuario"]);
$senha = $mysqli->real_escape_string($_POST["senha"]);

// Consulta SQL para buscar o usuário pelo nome de usuário
$sql = "SELECT id, nome, nivel, senha FROM usuarios WHERE usuario = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    
    // Verifica se a senha digitada corresponde ao hash no banco de dados (usando sha1)
    if (sha1($senha) === $row['senha']) {
        // Senha correta, iniciar sessão
        session_start();
        $_SESSION["UsuarioID"] = $row["id"];
        $_SESSION["UsuarioNome"] = $row["nome"];
        $_SESSION["UsuarioNivel"] = $row["nivel"];

        // Verifica o nível de acesso e redireciona para a página adequada
        if ($_SESSION["UsuarioNivel"] == 1) {
            header("Location: paginadousuario.php");
            exit;


            
        } elseif ($_SESSION["UsuarioNivel"] == 2) {
            header("Location: restrito.php");
            exit;
        } else {
            header("Location: bemvindo.php");
            exit;
        }
    } else {
        // Senha incorreta
        echo "Senha incorreta!";
        exit;
    }
} else {
    // Usuário não encontrado
    echo "Usuário não encontrado!";
    exit;
}
?>
