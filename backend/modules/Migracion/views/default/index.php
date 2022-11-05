<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<legend>
    Migración de datos. Tenga en cuenta que serán eliminados todos los datos de este sistema.
</legend>

<?php 
  //  if(!isset($action)) {
        echo Html::a('<span class="glyphicon glyphicon-share"></span> Importar base de datos', ['#'], [
            'class' => 'btn btn-warning',
            'id' => 'btnImport',
            /*'data' => [
                'confirm' => '¿Está seguro que desea Importar los datos?',
                //'method' => 'post',
            ],*/
        ]);
  //  }
    //else {  ?>
        <div id="progress" class="progress progress-striped active">
            <div id="progressBar" class="progress-bar" style="width: 0%;">0%</div>
        </div>
   <?php // }

    echo Html::a('SQL Server Test', ['test'], [
        'class' => 'btn btn-primary',
    ]);

    if(isset($dbsqlserver))
        print_r($dbsqlserver);
    else {
        echo "NO DATA";
    }

ob_start(); // output buffer the javascript to register later ?>
<script>

    var total = 0;
    var stcontrol = true;

    getTotalRecords();

    $('#btnImport').click(function() {
        $('#btnImport').toggleClass('hidden');
        var percent;
        var xhr;
        var updateFunction = function(e) {
                    console.log("lengthComputable "+e.lengthComputable);
                    percent = (e.loaded*100)/e.total; //e.currentTarget.response;
                    console.log(percent);
                    $("#progressBar")[0].innerHTML = Math.round(percent)+"%";
                    $("#progressBar")[0].style = "width: "+Math.round(percent)+"%;";
                };
        $.ajax({
                xhr: function() {
                        xhr = new window.XMLHttpRequest();
                        xhr.addEventListener("progress", updateFunction);
                       // xhr.upload.addEventListener("progress", updateFunction, true);
                    return xhr;
                }
                , type: 'post'
                , processData: false
                , contentType: "json"
                , contentLength: "10000"
                , url: '/InvFactServices/backend/web/migracion/default/import-ajax'
            });

       /* setInterval(function() {
            console.log();
        }, 1000 );*/
    });
    
    function getTotalRecords() {
        $.ajax({
            type: "post",
            url: '/InvFactServices/backend/web/migracion/default/get-count-ajax', 
            dataType: "json",
            success: function(count) {
                total = count;
            }
        });
    };

    /*function getStatus() {
        console.log("Getting...");
        stcontrol = false;
        $.ajax({
            type: "post",
            url: '/InvFactServices/backend/web/migracion/default/get-status', 
            dataType: "json",
            success: function(st) {
                console.log("Status: "+st);
                stcontrol = true;
               // $("#progressBar")[0].innerHTML = total;
            }
        });
    };*/

</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>