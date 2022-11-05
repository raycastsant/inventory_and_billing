<?php

namespace backend\modules\Reportes\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\Inventario\models\Producto;
use backend\modules\Facturacion\models\ProductosOrdenVenta;


class GraficoGastosIngresosSearch extends Producto
{
    private $_query;
    private $_queryGastos;
    private $showGAstos;

    public $myPageSize;
    public $fechaDesde;
    public $fechaHasta;
    public $area;
    public $dayfilter;
    public $cliente;
    public $concepto;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipoproducto_id'], 'integer'],
            [['fechaDesde', 'fechaHasta', 'area', 'costo', 'ingreso', 'dayfilter', 'cliente', 'concepto'], 'safe'],
            ['fechaDesde', 'validateFechaDesde'],
        ];
    }

    public function validateFechaDesde($attribute, $params, $validator) {
        if( $this->fechaHasta!=null && strtotime($this->$attribute) > strtotime($this->fechaHasta) )
            $validator->addError($this, $attribute, 'La fecha DESDE debe ser menor');
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    private function validate_dayfilter() {
        if(!in_array($this->dayfilter, ['weekly', 'monthly', 'yearly']))
            $this->dayfilter = 'weekly';
    }

    private function executeQuery() {
        $query = new \yii\db\Query(); 

        switch ($this->dayfilter) {
            case 'yearly': {
                $query->select(['sum(costo) as costo', 'sum(ingreso) as ingreso', 'YEAR(fecha_cobrada) as fecha']);
                $query->groupBy(['fecha']);
                $query->orderBy('fecha desc');
                break;
            }
            case 'monthly': {
                $query->select(['sum(costo) as costo', 'sum(ingreso) as ingreso', 
                    'CONCAT( YEAR(fecha_cobrada), "-", MONTH(fecha_cobrada) ) as fecha']);
                    $query->groupBy(['fecha']);
                    $query->orderBy('YEAR(fecha_cobrada) desc, MONTH(fecha_cobrada) desc');
                break;
            }
            case 'weekly': {
                $query->select(['sum(costo) as costo', 'sum(ingreso) as ingreso',
                    'CONCAT( "(", YEAR(fecha_cobrada), "-", MONTH(fecha_cobrada), 
                    " Semana:", WEEK(fecha_cobrada, 5)- WEEK(DATE_SUB(fecha_cobrada, INTERVAL DAYOFMONTH(fecha_cobrada) - 1 DAY), 5)+1, ")" ) as fecha', 
                    'WEEK(fecha_cobrada, 5)- WEEK(DATE_SUB(fecha_cobrada, INTERVAL DAYOFMONTH(fecha_cobrada) - 1 DAY), 5)+1 as sem']);
                $query->groupBy(['fecha']);
                $query->orderBy('YEAR(fecha_cobrada) desc, MONTH(fecha_cobrada) desc, sem desc');
                break;
            }  
        }

        $query->from('view_ingresos');

        $query->andFilterWhere(['=', 'area_id', $this->area])
            ->andFilterWhere(['=', 'cliente_id', $this->cliente])
            ->andFilterWhere(['=', 'concepto', $this->concepto])
            ->andFilterWhere(['>=', 'fecha_cobrada', $this->fechaDesde])
            ->andFilterWhere(['<=', 'fecha_cobrada', $this->fechaHasta]);
            
        return $query;
    }

    private function executeQueryGastos() {
        $query = new \yii\db\Query(); 

        switch ($this->dayfilter) {
            case 'yearly': {
                $query->select(['sum(cantidad) as gasto', 'YEAR(fecha) as fechas_tring']);
                $query->groupBy(['fechas_tring']);
                $query->orderBy('fechas_tring desc');
                break;
            }
            case 'monthly': {
                $query->select(['sum(cantidad) as gasto', 
                    'CONCAT( YEAR(fecha), "-", MONTH(fecha) ) as fechas_tring']);
                $query->groupBy(['fechas_tring']);
                $query->orderBy('YEAR(fecha) desc, MONTH(fecha) desc');
                break;
            }
            case 'weekly': {
                $query->select(['sum(cantidad) as gasto',
                    'CONCAT( "(", YEAR(fecha), "-", MONTH(fecha), 
                    " Semana:", WEEK(fecha, 5)- WEEK(DATE_SUB(fecha, INTERVAL DAYOFMONTH(fecha) - 1 DAY), 5)+1, ")" ) as fechas_tring', 
                    'WEEK(fecha, 5)- WEEK(DATE_SUB(fecha, INTERVAL DAYOFMONTH(fecha) - 1 DAY), 5)+1 as sem']);
                $query->groupBy(['fechas_tring']);
                $query->orderBy('YEAR(fecha) desc, MONTH(fecha) desc, sem desc');
                break;
            }
        }

        $query->from('gastos');

        $query->andFilterWhere(['>=', 'fecha', $this->fechaDesde])
            ->andFilterWhere(['<=', 'fecha', $this->fechaHasta]);
            
        return $query;
    }

    public function search($params) {
        //!Importante, debe ir antes de la consulta!
        $this->load($params);
        
        $this->validate_dayfilter();
        $this->_query = $this->executeQuery();

        $this->showGAstos = true;
        if($this->cliente==null) {
            $this->_queryGastos = $this->executeQueryGastos();
        }
        else
            $this->showGAstos = false;
        
        $sdata = [];
        $categories = [];
        $series['beneficios'] = [];
        $series['ingresos'] = [];
        //$series['gastos'] = [];

        //Beneficios e Ingresos
        $costo = 0;
        $ingreso = 0;
        $beneficio = 0;
        foreach($this->_query->all() as $data){
            $costo = (double)$data['costo'];
            $ingreso = (double)$data['ingreso'];
            $categories[] = $data['fecha'];
            $series['beneficios'][] = round( ($ingreso-$costo), 2 );
            $series['ingresos'][] = round( $ingreso, 2 );
        }

        //Gastos
        if($this->showGAstos == true) {
            foreach($this->_queryGastos->all() as $data) {
                $categories[] = $data['fechas_tring'];
                $series['gastos'][] = round( (double)$data['gasto'], 2 );
            }
        }

        $dataProvider['categories'] = $categories;
        $dataProvider['data'] = $series;

        return $dataProvider;
    }

    public function getModelName() {
        return 'GastosIngresosSearch';
    }

   /*public static function getTotal($provider, $fieldName) {
        $total = 0;

        foreach($provider as $item)
            $total += $item[$fieldName];

        return $total;
    }*/

    public function getTotal($field) {
        if($field == 'gasto') {
            if($this->showGAstos == true)
                return $this->_queryGastos->sum($field);
        }
        else
            return $this->_query->sum($field);
        
        return 0;
    }
}
