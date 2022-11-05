<?php

namespace backend\modules\Inventario\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Inventario\models\Producto;
use Yii;

/**
 * ProductoSearch represents the model behind the search form of `backend\modules\Inventario\models\Producto`.
 */
class ProductoSearch extends Producto
{
    private $query;
    
    public $myPageSize;
    public $tipoproducto;
    public $unidadMedida;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tipoproducto_id', 'unidad_medida_id'], 'integer'],
            [['nombre', 'codigo', 'desc', 'desc_ampliada', 'nombre_imagen', 'tipoproducto', 'unidadMedida', 'myPageSize'], 'safe'],
            [['costo', 'precio', 'existencia'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
       /* $almacen = 0;
        if(isset($params['almacen']))
            $almacen = $params['almacen'];*/
      
        $this->query = Producto::find();
        
        if( Yii::$app->controller->action->id == 'existencias' || 
            Yii::$app->controller->action->id == 'imprimir-existencias')  
        {
            $this->query->having(['and', ['>', 'existencia', 0], ['eliminado'=>false]]);
        }
        else
            $this->query->having(['eliminado'=>false]);

        $this->query->joinWith(['unidadMedida']);
        //$this->query->orderBy('codigo');

        if(isset($params['ProductoSearch']['tipoProducto'])) {
            $this->query->joinWith(['tipoproducto']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $this->query,
            /*'pagination' => [
                'pageSize' => ($this->myPageSize !== NULL) ? $this->myPageSize : 8,
            ],*/
        ]);

        $this->load($params);
        $dataProvider->pagination->pageSize = ($this->myPageSize !== NULL) ? $this->myPageSize : 10;

      /*  $dataProvider->sort->attributes['unidadMedida'] = [
            'asc' => ['unidad_medida.unidad_medida' => SORT_ASC],
            'desc' => ['unidad_medida.unidad_medida' => SORT_DESC],
        ];*/

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->defaultOrder['codigo'] = SORT_ASC;

         //Si envío desde el BUSCAR hago un OR
        if(isset($params['ProductoSearch']['or_value'])) {
            $orvalue = $params['ProductoSearch']['or_value'];

            $this->query->orFilterWhere(['like', 'nombre', $orvalue])
                ->orFilterWhere(['like', 'codigo', $orvalue])
                ->orFilterWhere(['like', 'desc', $orvalue])
                ->orFilterWhere(['like', 'desc_ampliada', $orvalue])
                //->orFilterWhere(['like', 'tipoproductos.tipo', $orvalue])
                ->orFilterWhere(['like', 'unidad_medida.unidad_medida', $orvalue]);
        }
        
        if( isset($params['ProductoSearch']['tipoProducto']) ) { 
            $this->query->andFilterWhere(['tipoproducto_id' => $params['ProductoSearch']['tipoProducto']]);  //$this->query->andFilterWhere(['like', 'tipoproductos.tipo', $params['ProductoSearch']['tipoProducto']]);

            // Si es desde el modelo del PAJAX hago un and
          /*  if(isset($params['ProductoSearch']['existencia']) && isset($params['ProductoSearch']['operator']))
                $query->andWhere([$params['ProductoSearch']['operator'], 'existencia',  $this->existencia]);
            else {*/
          /*      $query->andFilterWhere([
                    'id' => $this->id,
                    'costo' => $this->costo,
                    'precio' => $this->precio,
                    //'existencia' => $this->existencia,
                ]);
           // }
    
            $query->andFilterWhere(['like', 'nombre', $this->nombre])
                ->andFilterWhere(['like', 'codigo', $this->codigo])
                ->andFilterWhere(['like', 'desc', $this->desc])
                ->andFilterWhere(['like', 'desc_ampliada', $this->desc_ampliada]);
                //->andFilterWhere(['like', 'tipoproductos.tipo', $this->tipoproducto])
                //->andFilterWhere(['like', 'unidad_medida.unidad_medida', $this->unidadMedida]);*/
        }

        return $dataProvider;
    }

    public function getQueryData(){
        return $this->query->asArray()->All();
    }
}
