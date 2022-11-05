<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\modules\Facturacion\models\ClienteEmpresaResponsable;
use kartik\select2\Select2;
?>

<div class="cliente-form">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
    <?= $model->errorSummary($form); ?>

    <div class="tabbable">
        <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab">Datos</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab1">
                <br>
                <!-- Form de los datos principales de los clientes -->
                <div id="ClientesForm" class="well">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model->cliente, 'tipo_cliente_id')->widget(Select2::class, [
                                'data' =>  $tipoclientes,
                                'language' => 'es',
                                'pluginOptions' => [
                                    'allowClear' => false
                                ],
                            ])->label('Categoría');
                            ?>
                            <?= $form->field($model->cliente, 'fax')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model->cliente, 'nombre')->textInput(['maxlength' => true]) ?>
                            <?= $form->field($model->cliente, 'email')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model->cliente, 'telefono')->textInput(['maxlength' => true])->label('Teléfono') ?>
                            <?= $form->field($model->cliente, 'direccion')->textArea(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>

                <!-- Form de seleccion de los responsables en caso de que el cliente sea empresa -->
                <div id="responsablesForm" class="well hidden">
                    <fieldset>
                        <legend>Responsables de la Empresa</legend>
                        <?php
                        $ce_responsable = new ClienteEmpresaResponsable();
                        $ce_responsable->loadDefaultValues();
                        echo '<div class="table-responsive">';
                        echo '<table id="responsables" class="table table-condensed table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>Nombre y Apellidos</th>';
                        echo '<th>Teléfono</th>';
                        echo '<th>CI</th>';
                        echo '<th>Email</th>';
                        echo '<td>' . Html::a('Insertar', 'javascript:void(0);', [
                            'id' => 'add-responsable-btn',
                            'class' => 'btn btn-primary btn-small'
                        ]) . '</td>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        foreach ($model->empresaresponsables as $key => $_responsable) {
                            echo '<tr>';
                            echo $this->render('_form-responsables-empresa', [
                                'key' => $_responsable->isNewRecord ? (strpos($key, 'new') !== false ? $key : 'new' . $key) : $_responsable->id,
                                'form' => $form,
                                'responsable' => $_responsable,
                            ]);
                            echo '</tr>';
                        }

                        echo '<tr id="new-responsable-block" style="display: none;">';
                        echo $this->render('_form-responsables-empresa', [
                            'key' => '__id__',
                            'form' => $form,
                            'responsable' => $ce_responsable,
                        ]);

                        echo '</tr>';
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                        ?>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ob_start(); // output buffer the javascript to register later 
    ?>
    <script>
        //-------------------------- Scripts de los responsables de las Empresas ----------------------------//
        // add responsable_k button
        var responsable_k = <?php echo isset($key) ? str_replace('new', '', $key) : 0; ?>;
        $('#add-responsable-btn').on('click', function() {
            responsable_k += 1;
            $('#responsables').find('tbody')
                .append('<tr>' + $('#new-responsable-block').html().replace(/__id__/g, 'new' + responsable_k) + '</tr>');
        });

        // remove responsable_k button
        $(document).on('click', '.remove-responsable-btn', function() {
            $(this).closest('tbody tr').remove();
        });

        var $nameInput = $("cliente-nombre");
        var $label = $("label[for='cliente-nombre']");

        $('#cliente-tipo_cliente_id').change(function() {
            if ($('#cliente-tipo_cliente_id>option:selected').text() == 'Empresa') {
                $('#responsablesForm').removeClass('hidden');
                $label[0].innerHTML = "Nombre";
            } else {
                $('#responsablesForm').addClass('hidden');
                $label[0].innerHTML = "Nombre y Apellidos";
            }
        });

        if ($('#cliente-tipo_cliente_id>option:selected').text() == 'Empresa' && $('#responsablesForm').hasClass('hidden')) {
            $('#responsablesForm').removeClass('hidden');
            $label[0].innerHTML = "Nombre";
        } else {
            $label[0].innerHTML = "Nombre y Apellidos";
        }
    </script>
    <?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>

    <?php ActiveForm::end(); ?>
</div>