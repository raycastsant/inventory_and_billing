<?php
use yii\helpers\Html;
?>

<table class="table table-striped table-bordered">
    <tr>
        <td style="text-align:center" colspan="2"><b>Un mes de garantía</b></td>
    </tr>
    <tr>
        <td style="text-align:center">CLIENTE</td>
        <td style="text-align:center">EJECUTOR</td>
    </tr>
    <tr>
        <td style="text-align:right">Nombre: ___________________________________</td>
        <td>Nombre: <?= $ceoInfo->nombre ?></td>
    </tr>
    <tr>
        <td style="text-align:right; vertical-align:bottom">Firma: ___________________________________</td>
        <td style="vertical-align:bottom">Firma: <?php //if($printfirma == 'true') echo Html::img('/InvFactServices/backend/web/images/Fa1.png'); ?></td>
    </tr>
    <tr>
        <td style="text-align:right">CI: ___________________________________</td>
        <td>CI: <?= $ceoInfo->ci ?></td>
    </tr>
    <tr>
        <td style="text-align:right">Fecha: ___________________________________</td>
        <td>Fecha: <?= date("d/m/Y") ?></td>
    </tr>
</table>