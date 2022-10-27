<?php 
session_start();
include_once "model/conexion.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="print.css" media="print">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"  integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


</head>
<body>


<?php
    // Verifico la que exista la session para mostrar los datos
    $id = $_SESSION['id_usuario'];    
    if ($id==""){
        header("Location:login.php");

    }
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
<link rel="stylesheet" type="text/css" href="print.css" media="print">

      <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="card ">
                        <div class="card-header text-center " >
                           Detalied Info
                        </div>
                        <div class="p-3">
                        <div class="text">Dm'r Name  :  <?php echo $name->nombre; ?></div>
                        <div class="text">Date  :  <?php echo $month."/".$year ;?></div>
                        <div class="text">Dmr Commission : $ <?php echo round($comisiones,2);?></div>
                        <div class="text">Price per shift : $ <?php echo $price_per_shift; ?></div>
                        <div class="text">Price per Extra Shift : $ <?php echo $extra_shift_price; ?></div>
                        <div class="text">Total Extra Shift's : <?php echo $extra_shift_made;?></div>
                        <div class="text">Total Regular Shift's : <?php echo count($informacion) - $extra_shift_made;?></div>
                        <div class="text">Total base + extra : $ <?php echo count($informacion)*$price_per_shift;?></div>
                        <div class="text">Invoice Total : $ <?php echo round($invoice_total,2);?></div>
                        <div class="text"></div>
                        <input type="button" onclick="location.href='index.php'"  id="print-btn" class="btn btn-info" value="back">
                        <input type="button" onclick="window.print();" class="btn btn-danger" id="print-btn" value="Print">

                        </div>
                    </div>

                </div>
              
            </div>
            <div class="row justify-content-center">
                <div class="col-md-7">
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
              
            </div>
        </div>

        </body>
</html>