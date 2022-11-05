<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="modal-content animated bounceInTop">

<?php $form = ActiveForm::begin([ 'enableClientValidation'=>true, 'id'=>'new-product-ajax-form', 
                                  'options'=>['enctype' => 'multipart/form-data'],  
               /* 'validationUrl' => Yii::$app->urlManager->createUrl('inventario/productos/producto-validate-ajax')*/]);   ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title text-left">Agregar nuevo producto</h4>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success', 'id'=>'submitBtn']) ?>
    </div>
    <div id="errors" style="color:red">
    </div>

    <div class="modal-body well">       
 
        <?= $this->render('_form_fields', ['form'=>$form, 'model'=>$model, 'tipoproductos'=>$tipoproductos, 
                          'unidad_medidas'=>$unidad_medidas, 'isajax'=>true]); ?>
    </div>

    
    <br>
    <?php ActiveForm::end(); ?>

    <?php ob_start(); // output buffer the javascript to register later ?>
        <script>
            //Funcion de envio del formulario de creacion de un nuevo producto
            $(document).ready(function () { 
                $("#new-product-ajax-form").on('beforeSubmit', function (event) { 
                    event.preventDefault();            
                    var form_data = new FormData($('#new-product-ajax-form')[0]);
                    $.ajax({
                        url: $("#new-product-ajax-form").attr('action'), 
                        dataType: 'JSON',  
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data, //$(this).serialize(),                      
                        type: 'post',                        
                        beforeSend: function() {
                        },
                        success: function(response){ 
                            if(response.status != 'error') {           
                                //$('.field-producto-precio')[0].className = 'form-group field-producto-precio has-success';
                                $('#NewProductWindow').modal('hide');
                                document.getElementById('tableProdReloader').click();
                            }
                            else {
                                console.log("Errors: "+response.errors);

                                var costo = parseFloat($('#producto-costo').val());
                                var precio = parseFloat($('#producto-precio').val());
                                if(costo >= precio) {
                                    var $div = $('.field-producto-precio')[0];
                                    $div.className = 'form-group field-producto-precio has-error';
                                    $div.children[2].innerHTML = "El precio debe ser mayor que el costo";
                                }

                                if(response.errors!=null && response.errors.codigo!=null){
                                    var $divCodigo = $('.field-producto-codigo')[0];
                                    $divCodigo.className = 'form-group field-producto-codigo has-error';
                                    $divCodigo.children[2].innerHTML = response.errors.codigo;
                                }
                            }
                        },
                        complete: function() {
                        },
                        error: function (data) {
                           // toastr.warning("","Ocurrió un error al enviar el formulario. Inténtelo nuevamente");    
                           console.log(data);
                        }
                        });                
                    return false;
                });
            });      

            /*$('#submitBtn').on('click', function(e) {
                if($('#producto-costo') > $('#producto-precio')) {
                    e.preventDefault();
                }
                else
                window.alert('dsfdf');
            });*/
        </script>
    <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>

</div>


