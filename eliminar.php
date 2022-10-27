<?php 
    session_start();
    $_SESSION['id_usuario'];
    if(!isset($_GET['id_informacion'])){
        header('Location: index.php?mensaje=error');
        exit();
    }

    include 'model/conexion.php';
    $codigo = $_GET['id_informacion'];

    $sentencia = $bd->prepare("DELETE FROM informacion where id_informacion = ?;");
    $resultado = $sentencia->execute([$codigo]);

    if ($resultado === TRUE) {
        header('Location: index.php?mensaje=eliminado');
    } else {
        header('Location: index.php?mensaje=error');
    }
    
?>