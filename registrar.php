<?php
    session_start();
    //print_r($_POST);
    if(empty($_POST["oculto"]) || empty($_POST["txtNombre"]) || empty($_POST["txtEdad"]) || empty($_POST["txtSigno"])|| empty($_POST["model"])|| empty($_POST["shift"])|| empty($_POST["shift_type"])){
        header('Location: index.php?mensaje=falta');
        exit();
    }

    include_once 'model/conexion.php';
    $fecha = $_POST["txtNombre"];
    $total = $_POST["txtEdad"];
    $checkout = $_POST["txtSigno"];
    $id_usuario = $_SESSION["id_usuario"];
    $id_modelo = $_POST["model"];
    $id_shift = $_POST["shift"];
    $id_shift_type = $_POST["shift_type"];
    $sentencia = $bd->prepare("INSERT INTO informacion(fecha,total,checkout,id_usuario,id_model,id_shift,shift_type) VALUES (?,?,?,?,?,?,?);");
    $resultado = $sentencia->execute([$fecha,$total,$checkout,$id_usuario,$id_modelo,$id_shift,$id_shift_type]);

    if ($resultado === TRUE) {
        header('Location: index.php?mensaje=registrado');
    } else {
        header('Location: index.php?mensaje=error');
        exit();
    }
    
?>