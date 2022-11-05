<?php

namespace backend\modules\facturacion\models;

use Yii;

/**
 * This is the model class for table "moneda_cambios".
 *
 * @property int $id
 * @property int $m1_id
 * @property int $m2_id
 * @property double $valor
 *
 * @property Moneda $m1
 * @property Moneda $m2
 */
class MonedaCambio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'moneda_cambios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['m1_id', 'm2_id', 'valor'], 'required'],
            [['m1_id', 'm2_id'], 'integer'],
            [['valor'], 'number'],
            [['m2_id'], 'validateM2'],
            [['m1_id'], 'exist', 'skipOnError' => true, 'targetClass' => Moneda::className(), 'targetAttribute' => ['m1_id' => 'id']],
            [['m2_id'], 'exist', 'skipOnError' => true, 'targetClass' => Moneda::className(), 'targetAttribute' => ['m2_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'm1_id' => 'Moneda 1',
            'm2_id' => 'Moneda 2',
            'valor' => 'Razón de cambio',
        ];
    }

    public function validateM2($attribute, $params, $validator) {
        if($this->$attribute == $this->m1_id)
            $validator->addError($this, $attribute, 'Las monedas no pueden ser las mismas');
        else 
        if($this->isNewRecord) {
            $exists = ($this->find()->andWhere(['m1_id'=>$this->m1_id, 'm2_id'=>$this->m2_id])->count() > 0);
            if($exists == true)
                $validator->addError($this, $attribute, 'La tasa de cambio ya existe. Seleccione otra moneda');
        }
        else {
            if($this->getOldAttribute('m1_id')!=$this->m1_id || $this->getOldAttribute('m2_id')!=$this->m2_id) {
                $exists = ($this->find()->andWhere(['m1_id'=>$this->m1_id, 'm2_id'=>$this->m2_id])->count() > 0);
                if($exists == true)
                    $validator->addError($this, $attribute, 'La tasa de cambio ya existe. Seleccione otra moneda');
            }
            //else
            //$validator->addError($this, $attribute, 'oldm1-  '.$this->getOldAttribute('m1_id').'   ,oldm2-  '.$this->getOldAttribute('m2_id').'-------- M1:'.$this->m1_id.'     ,M2:'.$this->m2_id);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getM1()
    {
        return $this->hasOne(Moneda::className(), ['id' => 'm1_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getM2()
    {
        return $this->hasOne(Moneda::className(), ['id' => 'm2_id']);
    }
}
