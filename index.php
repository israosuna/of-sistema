<?php 
session_start();
ob_start();
include 'template/header.php';
?>

<?php
    // Verifico la que exista la session para mostrar los datos
    $id = $_SESSION['id_usuario'];    
    if ($id=="" || !isset($id) || $id==NULL || $id==null){
        session_unset();
        session_destroy();
        header("Location:login.php");

    }
    include_once "model/conexion.php";
    $shift_type = $_POST['shift_type']??'';
    $shift = $_POST['shift']??'';
    $model = $_POST['model'] ??'';
    //Manejar la fecha y obtener lo que necesito para el filtro
    if (!isset($_POST['month'])){
        $_POST['month']= date('Y m');
    }
    $date = strtotime($_POST['month']);
    $year = date('Y',$date);
    $month = date('m',$date);
    $days_of_a_month = date("t",$month);

    

    if ($month == "01" and $year=='1970'){
        $month= date('m');
        $year=date('Y');
    }
    $informacion =filtering($month,$year,$model,$shift,$shift_type,$id,$bd);
    $name = get_user_name($id,$bd);
    $suma =0;
    $comisiones = 0;
    // cuantos extras hice 
    $extra_shift_made = count_extras($id,$month,$year,$bd);
    //Calculo del shift 1 turno para el total
    $base = 500;
    $price_per_shift = round($base/$days_of_a_month,2);
    $extra_shift_price = 20-$price_per_shift;
    $total_extras = $extra_shift_price*$extra_shift_made;
    $day_off_count = 0; 
    foreach( $informacion as $total){
        
        if ($total->total-($total->total*0.20)>300){
            $comisiones += ($total->total-($total->total*0.20)-300)*0.05;
            $suma+=$total->total;  
        }
        elseif ($total->total == -1){
            $day_off_count +=1;
            $comisiones+=0;

        }else{

            $comisiones+=0;
            $suma+=$total->total;    

        }

    }
    if (count($informacion)>0){
        $average = $suma/(count($informacion)-$day_off_count);
    }else{
        $average =0;
    }

    $invoice_total  = ((count($informacion)) * $price_per_shift) + $total_extras + $comisiones;
    $modelList = get_all_models($bd);
    $all_shift_types = get_all_shift_type($bd);
    $all_shifts = get_all_shifts($bd);


?>
<div class="container-fluid bg-info">
          <div class="row">
              <div class="col-md">
                  <header class="py-3">
                  <h3 class="text-center">Hola <?php echo $name->nombre; ?> <a href="logout.php"> salir </a> </h3> 
              </div>
          </div>
      </div>
        <div class="container mt-5">
            <div class="row justify-content-center mb-2">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-header text-center bold">
                                Model Earning:
                            </div>
                                <div class="p-1">
                                    <input type="text" readonly  class="form-control text-center" value="<?php echo "$ ".$suma ?>" name="txtNombre" autofocus required>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center bold">
                            Dmr's Commission:
                        </div>
                            <div class="p-1">
                                <input type="text" readonly  class="form-control text-center" value="<?php echo "$ ".round($comisiones,2)  ?>" name="txtNombre" autofocus required>
                            </div>
                    </div>
                    </div>
                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center bold">
                            Average:
                        </div>
                            <div class="p-1">
                                <input type="text" readonly  class="form-control text-center" value="$ <?php echo round($average,2) ?>" name="txtNombre" autofocus required>
                            </div>
                    </div>
                    </div>
                    <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center bold">
                            Invoice Total:
                        </div>
                            <div class="p-1">
                                <input type="text" readonly  class="form-control text-center" value="$ <?php echo  round($invoice_total,2) ?>" name="txtNombre" autofocus required>
                            </div>
                    </div>
                    </div>
            </div>
        </div>
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <!-- inicio alerta -->
                    <?php 
                        if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'falta'){
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Fill all the fields.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                        }
                    ?>


                    <?php 
                        if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'registrado'){
                    ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Done!</strong> New check out Added!.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                        }
                    ?>   
                    
                    

                    <?php 
                        if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'error'){
                    ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Try Again!.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                        }
                    ?>   



                    <?php 
                        if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'editado'){
                    ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Updated!</strong> Your checkout was updated!.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                        }
                    ?> 


                    <?php 
                        if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'eliminado'){
                    ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Deleted!</strong> Your checkout it's now GONE!💀
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                        }
                    ?> 

                    <!-- fin alerta -->

                    <div class="card ">
                        <div class="card-header text-center " >
                           Checkouts Details
                        </div>
                        <div class="p-3">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">Date</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Commission</th>
                                        <th scope="col">Checkout</th>
                                        <th scope="col">Model</th>
                                        <th scope="col">Shift</th>
                                        <th scope="col">Shift Type</th>
                                        <th scope="col" colspan="2">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php 
                                        foreach($informacion as $dato){ 
                                    ?>

                                    <tr>
                                        <td><?php echo $dato->fecha; ?></td>
                                        <td><?php if ($dato->total == -1)
                                                        {
                                                            echo "--";
                                                        }else echo "$".$dato->total; ?></td>
                                        <td><?php if ($dato->total == -1)
                                                        {
                                                            echo "--";
                                                        }elseif ($dato->total-($dato->total*0.20)>300)
                                                        {
                                                            echo "$".round(($dato->total-($dato->total*0.20)-300)*0.05,2);
                                                        }else echo "$0";?></td>
                                        <td><?php echo $dato->checkout; ?></td>
                                        <td><?php echo get_model_name($dato->id_model,$bd); ?></td>
                                        <td><?php echo get_shift($dato->id_shift,$bd); ?></td>
                                        <td><?php echo get_shift_type($dato->shift_type,$bd); ?></td>
                                        <td><a class="text-success" href="editar.php?id_informacion=<?php echo $dato->id_informacion; ?>"><i class="bi bi-pencil-square"></i></a></td>
                                        <td><a onclick="return confirm('This record will be deleted, ok?');" class="text-danger" href="eliminar.php?id_informacion=<?php echo $dato->id_informacion; ?>"><i class="bi bi-trash"></i></a></td>
                                    </tr>

                                    <?php 
                                        }
                                    ?>

                                </tbody>
                            </table>
                            
                        </div>
                    </div>

                </div>
                <div class="col-md-4">
                    <div class="card mb-2">       
                        <div class="card-header text-center ">
                            Add Checkout:
                        </div>
                        <form class="p-4" method="POST" action="registrar.php">
                            <div class="mb-3">
                                <label class="form-label">Date: </label>
                                <input type="date"  class="form-control" name="txtNombre" autofocus required>
                              <!--   <label class="form-label">Is it your Day Off? : </label>
                               <input type="checkbox" id="checkBox" onclick="
                                        document.getElementById('total').disabled=this.checked; 
                                        document.getElementById('checkout').disabled=this.checked; 
                                        document.getElementById('total').value=-1 ; 
                                        document.getElementById('checkout').value ='Day Off';
                                "> -->
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total (-1 for day off): </label>
                                <input type="floatval" class="form-control" id ="total" value = "" name="txtEdad" autofocus required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Check Out: </label>
                                <input type="text" class="form-control" id ="checkout" value = "" name="txtSigno" autofocus required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Models: </label>
                                <select id="Place" class="form-control" name="model" >
                                <option value="0" class="form-control" selected="selected">Select Model</option>
                                <?php
                                    if (! empty($modelList)) {
                                        foreach ($modelList as $model) {
                                            echo '<option value="' . $model->id_model . '">' . $model->name . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shift: </label>
                                <select id="Place" class="form-control" name="shift" >
                                <option value="0" class="form-control" selected="selected">Select Shift</option>
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
                                <option value="0" class="form-control" selected="selected">Select Shift Type</option>
                                <?php
                                    if (! empty($all_shift_types)) {
                                        foreach ($all_shift_types as $shift_type) {
                                            echo '<option value="' . $shift_type->id_type_shift . '">' . $shift_type->type . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="d-grid">
                                <input type="hidden" name="oculto" value="1">
                                <input type="submit" class="btn btn-primary" value="Add Checkout">
                            </div>
                        </form>
                    </div>
                    <div class="card mb-2">
                        <div class="card-header text-center">
                            Filters
                        </div>
                    <form class="p-3" method="POST" action="index.php">
                            <div class="mb-3">
                                <label class="form-label">Date: </label>
                                <input type="month"  class="form-control" value="<?php echo date("Y-m")?>" name="month" autofocus required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Models: </label>
                                <select id="Place" class="form-control" name="model" >
                                <option value="0" class="form-control" selected="selected">Select Model</option>
                                <?php
                                    if (! empty($modelList)) {
                                        foreach ($modelList as $model) {
                                            echo '<option value="' . $model->id_model . '">' . $model->name . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shift: </label>
                                <select id="Place" class="form-control" name="shift" >
                                <option value="0" class="form-control" selected="selected">Select Shift</option>
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
                                <option value="0" class="form-control" selected="selected">Select Shift Type</option>
                                <?php
                                    if (! empty($all_shift_types)) {
                                        foreach ($all_shift_types as $shift_type) {
                                            echo '<option value="' . $shift_type->id_type_shift . '">' . $shift_type->type . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="d-grid">
                                
                                <input type="hidden" name="oculto" value="1">
                                <input type="submit" class="btn btn-primary" value="Filter">
                            </div>
                        </form>
                        <form class="p-3" method="POST" action="imprimir.php">
                            <div class="mb-3">
                                <label class="form-label">Date: </label>
                                <input type="month"  class="form-control" value="<?php echo date("Y-m")?>" name="month" autofocus required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Models: </label>
                                <select id="Place" class="form-control" name="model" >
                                <option value="0" class="form-control" selected="selected">Select Model</option>
                                <?php
                                    if (! empty($modelList)) {
                                        foreach ($modelList as $model) {
                                            echo '<option value="' . $model->id_model . '">' . $model->name . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shift: </label>
                                <select id="Place" class="form-control" name="shift" >
                                <option value="0" class="form-control" selected="selected">Select Shift</option>
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
                                <option value="0" class="form-control" selected="selected">Select Shift Type</option>
                                <?php
                                    if (! empty($all_shift_types)) {
                                        foreach ($all_shift_types as $shift_type) {
                                            echo '<option value="' . $shift_type->id_type_shift . '">' . $shift_type->type . '</option>';
                                        }
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="d-grid">
                                
                                <input type="hidden" name="oculto" value="1">
                                <input type="submit" class="btn btn-danger" value="Print Details">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<?php include 'template/footer.php' ?>