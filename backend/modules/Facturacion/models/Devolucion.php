<?php

namespace backend\modules\facturacion\models;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "devoluciones".
 *
 * @property int $id
 * @property int $parcial
 * @property string $fecha
 * @property string $is_venta
 *
 * @property DevolucionServicio[] $devolucionServicios
 * @property DevolucionVenta[] $devolucionVentas
 */
class Devolucion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'devoluciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parcial'], 'integer'],
            [['fecha', 'ordenId', 'is_venta'], 'safe'],
           // [['causa'], 'required'],
            //[['causa'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parcial' => 'Parcial',
            'fecha' => 'Fecha',
           // 'causa' => 'Causa',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevolucionServicios()
    {
        return $this->hasMany(DevolucionServicio::className(), ['devolucion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDevolucionVentas()
    {
        return $this->hasMany(DevolucionVenta::className(), ['devolucion_id' => 'id']);
    }

    public function listDevolucionVentas() {
        return DevolucionVenta::find()->andWhere(['devolucion_id' => $this->id])->all();
    }

    public function listDevolucionServicios() {
        return DevolucionServicio::find()->andWhere(['devolucion_id' => $this->id])->all();
    }

    public function getOrden() {
        if($this->is_venta) {
            if (($model = OrdenVenta::findOne($this->ordenId)) !== null) {
                return $model;
            }
        }
        else {
            if (($model = OrdenServicio::findOne($this->ordenId)) !== null) {
                return $model;
            }
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    public function getOrdenById($id) {
        if($this->is_venta) {
            if (($model = OrdenVenta::findOne($id)) !== null) {
                return $model;
            }
        }
        else {
            if (($model = OrdenServicio::findOne($id)) !== null) {
                return $model;
            }
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
