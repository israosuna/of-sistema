<?php
    session_start();
    ob_start();

    $_SESSION['id_usuario'];
    print_r($_POST);
    if(!isset($_POST['codigo'])){
        header('Location: index.php?mensaje=error');
    }

    include 'model/conexion.php';
    $fecha = $_POST["txtNombre"];
    $total = $_POST["txtEdad"];
    $checkout = $_POST["txtSigno"];
    $id_usuario = $_SESSION["id_usuario"];
    $id_modelo = $_POST["model"];
    $id_shift = $_POST["shift"];
    $id_shift_type = $_POST["shift_type"];
    $sentencia = $bd->prepare("UPDATE informacion SET fecha = ?, total = ?, checkout = ?, id_model =?,id_shift=?, shift_type=? where id_informacion = ? and id_usuario = ?;");
    $resultado = $sentencia->execute([$fecha, $total, $checkout,$id_modelo,$id_shift,$id_shift_type,$_POST['codigo'],$_SESSION['id_usuario']]);

    print_r($sentencia);
    if ($resultado === TRUE) {
        header('Location: index.php?mensaje=editado');
    } else {
        header('Location: index.php?mensaje=error');
        exit();
    }
  
?>