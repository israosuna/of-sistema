<?php 
$contrasena = "";
$usuario = "";
$nombre_bd = "";

try {
	$bd = new PDO (
		'mysql:host=localhost;
		dbname='.$nombre_bd,
		$usuario,
		$contrasena,
		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
	);
} catch (Exception $e) {
	 "Problema con la conexion: ".$e->getMessage();
}

function filtering($month,$year,$model,$shift,$shift_type,$id_usuario,$bd){
	$and = 'select * from informacion where id_usuario = '.$id_usuario;
	if(!$model==''){
		$and.=' and id_model ='.$model;
	}
	if(!$shift==''){
		$and.=' and id_shift ='.$shift;
	}
	if(!$shift_type==''){
		$and.=' and shift_type ='.$shift_type;
	}
	if(!$month==''){
		$and.=' and month(fecha) ='.$month;
	}
	if(!$year==''){
		$and.=' and year(fecha) ='.$year;
	}
	$sentencia = $bd -> query($and." order by fecha");
	if($sentencia){
		$informacion = $sentencia->fetchAll(PDO::FETCH_OBJ);
		return $informacion;
	}else{
		header("Location:login.php");

	}
}

function get_user_name($id,$bd){
	$sentencia = $bd -> query("select nombre from person where id_usuario =".$id);
    $informacion = $sentencia->fetch(PDO::FETCH_OBJ);
	return $informacion;
}

function get_all_models($bd){
	$sentencia = $bd -> query("select * from model order by name");
    $informacion = $sentencia->fetchAll(PDO::FETCH_OBJ);
	return $informacion;
}
function get_all_shifts($bd){
	$sentencia = $bd -> query("select * from shift order by shift");
    $informacion = $sentencia->fetchAll(PDO::FETCH_OBJ);
	return $informacion;
}
function get_all_shift_type($bd){
	$sentencia = $bd -> query("select * from shift_type order by type");
    $informacion = $sentencia->fetchAll(PDO::FETCH_OBJ);
	return $informacion;
}
function get_model_name($modelid,$bd)
{
	$sentencia = $bd -> query("select * from model where id_model =".$modelid);
    $informacion = $sentencia->fetch(PDO::FETCH_OBJ);
	print( $informacion->name);
}
function get_shift($shift,$bd)
{
	$sentencia = $bd -> query("select * from shift where id_shift =".$shift);
    $informacion = $sentencia->fetch(PDO::FETCH_OBJ);
	print( $informacion->shift);
}

function get_shift_type($shifttype,$bd)
{
	$sentencia = $bd -> query("select * from shift_type where id_type_shift =".$shifttype);
    $informacion = $sentencia->fetch(PDO::FETCH_OBJ);
	print( $informacion->type);
}

function verf_email ($email,$bd){
	$sentencia = $bd -> query("SELECT * FROM person WHERE email ='".$email."'");
    $informacion = $sentencia->fetchAll(PDO::FETCH_OBJ);
	return count($informacion);
}

function get_usr_id ($email,$bd){
	$sentencia = $bd -> query("SELECT * FROM person WHERE email ='".$email."'");
    $informacion = $sentencia->fetch(PDO::FETCH_OBJ);
	return $informacion;
}

function count_extras($persona,$month,$year,$bd){
	$sentencia = $bd -> query("select count(*) as total_extras from informacion where id_usuario =".$persona." and shift_type=2 and year(fecha) =".$year." and month(fecha)=".$month);
    $informacion = $sentencia->fetch(PDO::FETCH_OBJ);
	
	return $informacion->total_extras;
}
?>