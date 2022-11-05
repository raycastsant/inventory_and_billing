<?php

namespace backend\modules\Reportes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Facturacion\models\Cliente;


class EmpresasSinResponsableSearch extends Cliente
{
    public $myPageSize;
    public $tipoCliente;
    public $cid;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tipo_cliente_id'], 'integer'],
            [['cid', 'nombre', 'telefono', 'direccion', 'email', 'myPageSize'], 'safe'],
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
        //!Importante, debe ir antes de la consulta!
        $this->load($params);
        
        $query = new \yii\db\Query();     
        $query->select(['clientes.id as cid', 'clientes.nombre', 'clientes.telefono', 'clientes.email', 'clientes.direccion']);
        $query->from('clientes');
        $query->innerJoin('tipo_cliente', 'clientes.tipo_cliente_id=tipo_cliente.id');
        $query->andWhere(['tipo_cliente.nombre' => 'Empresa']);
        $query->andWhere('clientes.eliminado = false');
        $query->andWhere("clientes.id NOT IN (SELECT `clientes`.`id` FROM `clientes` INNER JOIN `tipo_cliente` ON clientes.tipo_cliente_id=tipo_cliente.id
                                INNER JOIN cliente_empresa_responsables ON clientes.id=cliente_empresa_responsables.cliente_id
                                WHERE (`tipo_cliente`.`nombre`='Empresa') AND (clientes.eliminado = false) ORDER BY `clientes`.`nombre`)");
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->pagination->pageSize = ($this->myPageSize !== NULL) ? $this->myPageSize : 10;

        $dataProvider->sort->attributes['nombre'] = [
            'asc' => ['clientes.nombre' => SORT_ASC],
            'desc' => ['clientes.nombre' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['telefono'] = [
            'asc' => ['telefono' => SORT_ASC],
            'desc' => ['telefono' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['email'] = [
            'asc' => ['email' => SORT_ASC],
            'desc' => ['email' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['direccion'] = [
            'asc' => ['direccion' => SORT_ASC],
            'desc' => ['direccion' => SORT_DESC],
        ];

        $dataProvider->sort->defaultOrder['nombre'] = SORT_ASC;

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'clientes.nombre', $this->nombre])
            ->andFilterWhere(['like', 'telefono', $this->telefono])
            ->andFilterWhere(['like', 'direccion', $this->direccion])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider; 
    }

    public function getModelName() {
        return 'EmpresasSinResponsableSearch';
    }
}
