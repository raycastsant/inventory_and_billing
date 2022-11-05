<?php
//use Mpdf\Mpdf;

//$mpdf = new Mpdf();
//$mpdf->WriteHTML("hello");
?>

<table class="table table-striped"> 
    <tr>
        <td><h3>Inventario de productos</h3><td>
        <td style="width:150px; text-align:right;"><i>Fecha: <?= date("d/m/Y") ?></i></td>
    </tr>
</table>

<?php
if(count($data) > 0) { ?>
    <table class="table table-striped table-bordered" style="font-size:11px;">
        <tr>
            <th style="background-color: #ddd;">#</th>
            <th style="background-color: #ddd;">Código</th>
            <th style="background-color: #ddd;">Nombre</th>
            <th style="background-color: #ddd;">Categoría</th>
            <th style="background-color: #ddd;">Existencia</th>
            <th style="background-color: #ddd;">UM</th>
            <th style="background-color: #ddd;text-align:right;">Precio</th>
            <th style="background-color: #ddd;text-align:right;">Costo</th> 
        </tr>
<?php   
        $i = 1;
        foreach($data as $prod) { ?>
           <tr>
                <td><?= $i?></td>
                <td><?= $prod['codigo'] ?></td>
                <td><?= $prod['nombre'] ?> </td>
                <td><?= $prod['tipoproducto']['tipo'] ?></td>
                <td><?= $prod['existencia'] ?></td>
                <td><?= $prod['unidadMedida']['unidad_medida'] ?></td>
                <td style='text-align:right;'><?= '$'.$prod['precio'] ?></td>
                <td style='text-align:right;'><?= '$'.$prod['costo'] ?></td>
            </tr>
    <?php
            $i++;
        } 
    ?>
    </table>
<?php
}
?>

       