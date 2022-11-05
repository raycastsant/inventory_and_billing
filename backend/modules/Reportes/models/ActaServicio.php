<?php
namespace backend\modules\Reportes\models;

use yii\base\Model;

class ActaServicio extends Model
{
    public $cliente;
    public $no_contrato;
    public $marca;
    public $modelo;
    public $matricula;
    public $servicio;
    public $tiempo;
    public $precio;

    public function rules() {
        return [
            [['cliente', 'no_contrato', 'marca', 'modelo', 'matricula', 'servicio', 'tiempo', 'precio'], 'required'],
        ];
    }

       /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'cliente' => 'Cliente',
            'no_contrato' => 'Contrato No',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'matricula' => 'Matrícula',
            'servicio' => 'Servicio solicitado',
            'tiempo' => 'Tiempo estimado de ejecución',
            'precio' => 'Precio aproximado',
        ];
    }
}
?>