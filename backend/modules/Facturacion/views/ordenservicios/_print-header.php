<?php

use yii\helpers\Html;
?>

<table style="font-size:10px;" autosize="1">
    <tr>
        <td rowspan="6" style="vertical-align:top">
            <img src='/InvFactServices/backend/web/images/logo_full.png' style="height:7%;">
            <!--    <img src='/InvFactServices/backend/web/images/logo_full.png' style="width:142px; height:73px;"> -->
        </td>
        <td><b><i></b></i></td>
        <td colspan="3"><b><i>PÁGUESE A: <?= $ceoInfo->nombre; ?></b></i></td>
    </tr>
    <tr>
        <th style="min-width:800px;"><b><i>NIT:</b></i></td>
        <td><i><?= $ceoInfo->ci; ?></i></td>
        <td><b><i>Dirección:</b></i></td>
        <td><i><?= $ceoInfo->direccion; ?></i></td>
    </tr>
    <tr>
        <td style="min-width:800px;"><b><i>Cuenta Bancaria CUC:</b></i></td>
        <td><i><?= $ceoInfo->cuenta_cuc; ?></i></td>
        <td><b><i>Teléfono:</b></i></td>
        <td><i><?= $ceoInfo->telefono; ?></i></td>
    </tr>
    <tr>
        <td style="min-width:800px;"><b><i>Cuenta Bancaria CUP:</b></i></td>
        <td><i><?= $ceoInfo->cuenta_mn; ?></i></td>
        <td><b><i>Email:</b></i></td>
        <td><i><?= $ceoInfo->email; ?></i></td>
    </tr>
    <tr>
        <td style="min-width:800px;"><b><i>Sucursal Bancaria:</b></i></td>
        <td><i><?= $ceoInfo->sucursal; ?></i></td>
        <td><b><i>FECHA <?= date('d/m/Y') ?></b></i></td>
        <td></td>
    </tr>
</table>

<table>
    <tr>
        <td style="min-width: 800px;"><b>Información de facturación:</b></td>
        <td><b><?php echo "Información: "; ?></b></td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td style="text-align:right"><b style="font-size:14px;"><?php echo $pname; ?></b></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;Nombre: <?= $model->cliente->nombre; ?></td>
        <td>&nbsp;&nbsp;&nbsp; <?php echo "Chapa: test"; ?></td>
        <td></td>
        <td style="text-align:right"><b style="font-size:14px;"><?php echo $model->codigo; ?></b></td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;Teléfono: <?= $model->cliente->telefono; ?></td>
        <td>&nbsp;&nbsp;&nbsp; <?php echo "Marca: test"; ?></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>&nbsp;&nbsp;&nbsp; <?php echo "Modelo: test"; ?></td>
        <td></td>
        <td></td>
    </tr>
</table>