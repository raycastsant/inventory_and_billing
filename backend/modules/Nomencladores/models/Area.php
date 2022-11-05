<?php

namespace backend\modules\nomencladores\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "areas".
 *
 * @property int $id
 * @property string $nombre
 *
 * @property OrdenServicio[] $ordenServicios
 * @property OrdenVenta[] $ordenVentas
 * @property UserArea[] $userAreas
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'areas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'unique', 'message' => "El nombre del área ya existe"],
            [['nombre'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenServicios()
    {
        return $this->hasMany(OrdenServicio::className(), ['area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrdenVentas()
    {
        return $this->hasMany(OrdenVenta::className(), ['area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAreas()
    {
        return $this->hasMany(UserArea::className(), ['area_id' => 'id']);
    }
}
