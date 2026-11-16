<?php
/* ==========================================================
   HORSMANN PROYECTO VIDEOJUEGOS
   CONTINUAR PARTIDA: inicio de sesión
   ========================================================== */
// login.php - inicio de sesión con correo y contraseña
session_start();
require 'conexion.php';
require 'incluir/csrf.php';

if (isset($_SESSION['id'])) {
    header("Location: panel.php");
    exit;
}

$errores = [];
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!token_ok()) {
        $errores[] = "El formulario no es válido o venció. Probá de nuevo.";
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $clave = $_POST['clave'] ?? '';

        if ($email === '' || $clave === '') {
            $errores[] = "Escribí tu correo y tu contraseña.";
        } else {
            // busco al usuario por su correo
           $c = $pdo->prepare("SELECT id, nombre, password, rol, fecha_archivado FROM usuarios WHERE correo = ?");
$c->execute([$correo]);
$usuario = $c->fetch(PDO::FETCH_ASSOC);

            // password_verify compara lo que escribió con el hash de la base.
            // Uso el mismo mensaje si el correo no existe o si la clave está mal.
            if (!$usuario || !password_verify($clave, $usuario['password'])) {
                $errores[] = "Correo o contraseña incorrectos. ¡Probá de nuevo, todavía te quedan vidas!";
            } else if ($usuario['fecha_archivado'] !== null) {
                $errores[] = "Tu cuenta está archivada (Game Over). Hablá con un administrador para reactivarla.";
            } else {
                // login correcto: genero un id de sesión nuevo y guardo los datos
                session_regenerate_id(true);
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                if ($usuario['rol'] === 'admin') {
                    header("Location: admin_usuarios.php");
                } else {
                    header("Location: panel.php");
                }
                exit;
            }
        }
    }
}

$titulo = "Iniciar sesión";
require 'incluir/encabezado.php';
?>

<div class="caja">
    <h1>Continuar partida</h1>
    <p>Iniciá sesión con tu correo y tu contraseña.</p>
    <?php require 'incluir/errores.php'; ?>

    <form method="post" action="login.php">
        <?php echo campo_token(); ?>

        <label>Correo electrónico</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

        <label>Contraseña</label>
        <input type="password" name="clave">

        <button type="submit" class="boton">Jugar</button>
    </form>

    <p>¿No tenés cuenta? <a href="registro.php">Registrate acá</a></p>
</div>

<?php require 'incluir/pie.php'; ?>
