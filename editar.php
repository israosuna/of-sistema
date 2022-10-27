<?php 
session_start();
ob_start();

include 'template/header.php' ?>

<?php
    if(!isset($_GET['id_informacion'])){
        header('Location: index.php?mensaje=error');
        exit();
    }

    include_once 'model/conexion.php';
    $codigo = $_GET['id_informacion'];

    $sentencia = $bd->prepare("select * from informacion where id_informacion = ?;");
    $sentencia->execute([$codigo]);
    $persona = $sentencia->fetch(PDO::FETCH_OBJ);
    $fecha =  $persona->fecha;
    $modelList = get_all_models($bd);
    $all_shift_types = get_all_shift_type($bd);
    $all_shifts = get_all_shifts($bd);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Editar datos:
                </div>
                <form class="p-4" method="POST" action="editarProceso.php">
                <div class="mb-3">
                        <label class="form-label">Fecha: </label>
                        <input type="date"  class="form-control" name="txtNombre"    autofocus required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total: </label>
                        <input type="floatval" class="form-control" name="txtEdad" value="<?php echo $persona->total ?> " autofocus required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check Out: </label>
                        <input type="text" class="form-control" name="txtSigno" value ="<?php echo $persona->checkout ?>" autofocus required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model: </label>
                        <select id="Place" class="form-control" name="model" >
                                <option value="<?php echo $persona->id_model ?>" class="form-control" selected="selected"><?php echo get_model_name($persona->id_model,$bd); ?></option>
                                <?php
                                    if (! empty($modelList)) {
                                        foreach ($modelList as $model) {
                                            echo '<option value="' . $model->id_model . '">' . $model->name . '</option>';
                                        }
                                    }
                                ?>
                        </select>                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift: </label>
                        <select id="Place" class="form-control" name="shift" >
                                <option value="<?php echo $persona->id_shift ?>" class="form-control" selected="selected"><?php echo get_shift($persona->id_shift,$bd); ?></option>
                                <?php
                                    if (! empty($all_shifts)) {
                                        foreach ($all_shifts as $shift) {
                                            echo '<option value="' . $shift->id_shift . '">' . $shift->shift . '</option>';
                                        }
                                    }
                                ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift Type: </label>
                        <select id="Place" class="form-control" name="shift_type" >
                                <option value="<?php echo $persona->shift_type ?>" class="form-control" selected="selected"><?php echo get_shift_type($persona->shift_type,$bd); ?></option>
                                <?php
                                    if (! empty($all_shift_types)) {
                                        foreach ($all_shift_types as $shift_type) {
                                            echo '<option value="' . $shift_type->id_type_shift . '">' . $shift_type->type . '</option>';
                                        }
                                    }
                                ?>
                        </select>                    </div>
                    <div class="d-grid">
                        <input type="hidden" name="codigo" value="<?php echo $persona->id_informacion; ?>">
                        <input type="submit" class="btn btn-primary" value="Editar">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'template/footer.php' ?>