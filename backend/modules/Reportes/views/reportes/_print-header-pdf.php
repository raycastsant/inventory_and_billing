<?php
use yii\helpers\Html;
?>

<table style="font-size:12px">
    <tr>
        <td rowspan="6"><img src='/InvFactServices/backend/web/images/logo.png' style="width:180px; height:180px;"></td>
        <td style="vertical-align:bottom; width:10px; min-width:10px;"><h1>TDEA</h1></td>
        <td style="vertical-align:bottom"><h5>TALLER DAVID ELECTRICISTA AUTOMOTRIZ</h5></td><
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td colspan="2">TCP <?=$ceoInfo->nombre; ?></td>
    </tr>
    <tr>
        <td colspan="2">ACTIVIDAD: <?=$ceoInfo->actividad; ?></td>
    </tr>
    <tr>
        <td colspan="2">REGIMEN ESPECIAL- <?=$ceoInfo->regimen; ?></td>
    </tr>
    <tr>
        <td colspan="2">NIT: <?=$ceoInfo->ci; ?></td>
    </tr>
</table>