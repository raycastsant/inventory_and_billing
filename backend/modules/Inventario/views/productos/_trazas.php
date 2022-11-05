<?php
use backend\modules\Seguridad\controllers\TrazasProductosController;
use yii\grid\GridView;
use yii\widgets\Pjax;

if(isset($model)) {
    $data = TrazasProductosController::getProductoTrazas($model->id);
    ?>
        <div class="table-responsive">
            <table id="ttrazas" class="display table table-striped table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><a href="#">Usuario</a></th>
                    <th><a href="#">Fecha</a></th>
                    <th><a href="#">Descripción</a></th> 
                </tr>
            </thead>
        <?php foreach($data as $row) {
                echo "<tr>";
                echo "<td>".$row['user']['username']."</td>";
                echo "<td>".$row['fecha']."</td>";
                echo "<td>".$row['descripcion']."</td>";
                echo "</tr>";
                }  ?>   
            </table>
        </div>
<?php
    /*$dataProvider = TrazasProductosController::getProductoTrazas($model->id);

    Pjax::begin();

    echo GridView::widget([
        'dataProvider' => $dataProvider,
     //   'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'user',
                'label'=>'Usuario',
                'value'=>'user.username'
            ],
            'fecha',
            [
                'attribute'=>'descripcion',
                'label'=>'Descripción'
            ],
        ],
    ]); 
    
    Pjax::end();*/
    ob_start(); // output buffer the javascript to register later ?>
    <script>
        fill_datatable();
        function fill_datatable(filter = '') {
            table = $('#ttrazas').DataTable(getEsDatatableConfig());
        };
        </script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); 
}
?>