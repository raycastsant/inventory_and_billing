<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>

<?= $this->render('_print-header-pdf', ['ceoInfo' => $ceoInfo]); ?>
<br>
<table class="table table-striped">
    <tr>
        <td style="text-align:center;">
            <h4>ACTA DE ENTREGA</h4>
        </td>
    </tr>
</table>
<table class="table table-striped table-bordered">
    <tr>
        <td colspan="2">
            <b>Cliente:</b> <?= $model->cliente ?>
        </td>
        <td>
            <b>Contrato No:</b> <?= $model->no_contrato ?>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="height:220px; vertical-align:top;">
            <b>Servicio prestado:</b> <br>
            <?= $model->servicio ?>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="height:220px; vertical-align:top;">
            <b>Garantía:</b> <br>
            <?= $model->garantia ?>
        </td>
    </tr>
    <tr>
        <td style="width:50%; min-width:50%;">
            Recibe: EL CLIENTE
        </td>
        <td colspan="2">
            Entrega: EL PRESTADOR
        </td>
    </tr>
    <tr>
        <td style="width:50%; min-width:50%;">
            Nombre:
        </td>
        <td colspan="2">
            Nombre: <?= $ceoInfo->nombre ?>
        </td>
    </tr>
    <tr>
        <td style="width:50%; min-width:50%;">
            Fecha:
        </td>
        <td colspan="2">
            Fecha: <?= Date("d/m/Y"); ?>
        </td>
    </tr>
    <tr>
        <td style="width:50%; min-width:50%;">
            Firma:
        </td>
        <td colspan="2">
            Firma: <?php //if($printfirma == 'true') echo Html::img('/InvFactServices/backend/web/images/Fa1.png'); 
                    ?>
        </td>
    </tr>
</table>