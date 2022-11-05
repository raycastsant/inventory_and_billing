<?php

namespace backend\modules\Reportes\models;

use yii\base\Model;

class ActaEntrega extends Model
{
    public $cliente;
    public $no_contrato;
    public $marca;
    public $modelo;
    public $matricula;
    public $servicio;
    public $garantia;

    public function rules()
    {
        return [
            [['cliente', 'no_contrato', 'marca', 'modelo', 'matricula', 'servicio', 'garantia'], 'required'],
            // [['cliente', 'no_contrato', 'marca', 'modelo', 'matricula', 'servicio', 'garantia'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cliente' => 'Cliente',
            'no_contrato' => 'Contrato No',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'matricula' => 'Matrícula',
            'servicio' => 'Servicio realizado',
            'garantia' => 'Garantía',
        ];
    }
}
