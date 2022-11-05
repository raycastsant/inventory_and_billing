<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\components\Helper;
use yii\bootstrap\Modal;

    //Modal que muestra el Zoom de la imagen
    Modal::begin([
        'header' => '<h4 id="zoomHeader"></h4>',
        'toggleButton' => ['label'=>'', 'id'=>'modal_launch', 'style'=>'display:none'],
        'id' => 'modal_img',
        'closeButton'=>['id'=>'close_modal'],
        ]); 
        echo '<img id="zoom_img" class="file-preview-image kv-preview-data file-zoom-detail" 
                src="" alt="" 
                style="width: auto; height: auto; max-width: 100%; max-height: 100%;">';
    Modal::end();
?>

<table class="table table-striped table-bordered">
    <tr>
        <th style="background-color: #ddd;">Imagen</th>
        <th style="background-color: #ddd;">Producto</th>
        <th style="background-color: #ddd;">Código</th>
        <th style="background-color: #ddd;">Cantidad</th>
    </tr>
<?php   
    foreach($list as $dev) { ?>
        <tr>
            <td><?php
                    if(strlen($dev->producto->nombre_imagen) > 0) {
                        $h = @fopen(Url::base(true).'//uploads//'.$dev->producto->nombre_imagen, 'r');
                        if($h)
                            echo Helper::ImgThumbailWidget(Yii::$app->request->baseUrl.'//uploads//'.$dev->producto->nombre_imagen, $dev->producto->id,
                                                            $dev->producto->codigo.', '.$dev->producto->nombre, 'i'.$dev->producto->id, "70px", "70px"); 
                    }
                    ?> </td>
            <td><?= $dev->producto->nombre ?> </td>
            <td><?= Html::a($dev->producto->codigo, 
                    Url::toRoute('/inventario/productos/view').'?id='.$dev->producto->id, 
                    ['target' => '_blank']) ?> </td>
            <td><?= $dev->cantidad ?> </td>
<?php   } ?>
        </tr>
</table>

<?php
    $this->registerJs("
    $(document).on('click', '.kv-file-zoom', function () {
        var id = $(this)[0].id;
        if(id != null) {
            $('#zoomHeader')[0].innerHTML = $('#i'+id)[0].title;
            $('#zoom_img')[0].src = $('#i'+id)[0].src;
            $('#modal_launch').click();
        }
    });
        ",  yii\web\View::POS_READY);
?>