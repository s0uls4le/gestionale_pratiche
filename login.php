<?php 
include_once 'inc/db.config.php';
include_once 'inc/functions.php';
create_utenti($conn);

$nome_utente = $_POST['username-login'];
$password = $_POST['password-login'];

$sql = "SELECT psw, privilegi FROM utenti WHERE username = ? ";

$stmt = $conn -> prepare($sql);
$stmt -> bind_param('s', $nome_utente);

if($stmt->execute() === false){
    die("Errore" . $stmt->error);
}

$results = $stmt->get_result();

if($results->num_rows > 0){
    $row = $results->fetch_assoc();
    $password = $password . AUTH_SALT;

    if ( password_verify($password, $row['psw']) ) {
        $_SESSION['login'] = $row['privilegi'];
        unset($row['psw']);
        unset($password);
        $stmt->close();
        $conn->close();
        header('Location: visualizza_admin.php');
        exit;
    } else {
        $_SESSION['error'] = "Credenziali non valide.";
        $stmt->close();
        $conn->close();
        header('location: login_admin.php');
        exit;
    }
}
else{
    $_SESSION['error'] = "Credenziali non valide.";
    $stmt->close();
    $conn->close();
    header('location: login_admin.php');
    exit;
}