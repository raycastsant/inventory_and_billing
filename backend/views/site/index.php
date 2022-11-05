<?php

use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use kartik\date\DatePicker;

$this->title = 'IB-Facturación-Inventario';
?>

<div class="site-index">
    <!-- Estadistica Ordenes -->
    <div id="ordenes-charts" class="row">
        <legend>Órdenes por día</legend>
        <div class="col-md-6 well" style="max-width:700px">
            <?php
            $selectOptions = '<option value="weekly" selected>Semanal</option>
                              <option value="monthly">Mensual</option>
                              <option value="yearly">Anual</option>';
            ?>
            <div class="row">
                <div class="col-md-6" style="max-width:150px;">
                    <label for="chartServsFilter">Cantidad</label>
                    <select id="chartServsFilter" class="form-control form-control-sm">
                        <?= $selectOptions ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <?php
                    echo '<label>A partir de:</label>';
                    echo DatePicker::widget([
                        'name' => 'chartServicesFrom',
                        'id' => 'chartServicesFrom',
                        'value' => date('Y-m-d', strtotime('-30 days')),
                        'options' => ['placeholder' => 'Seleccionar ...'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'autoclose' => true,
                        ]
                    ]); ?>
                </div>
            </div>
            <div id="chart-servicios"></div>
            <?php
            $this->registerJs('var servicesChart;
                    function loadChartServicios(chart) {
                        var filter = $("#chartServsFilter").val();
                        var from = $("#chartServicesFrom").val();
                        $.getJSON("' . Url::toRoute('/reportes/reportes/estadistica-ordenes-servicios?filter=') . '"+filter+"&from="+from, function (jsondata) {
                            chart.series[0].setData(jsondata.sdata);
                            chart.axes[0].setCategories(jsondata.categories);
                        });
                    }');
            // $charServData = ReportesController::EstadisticaOrdenesServicios();
            echo Highcharts::widget([
                'options' => [
                    'chart' => [
                        'type' => 'areaspline',
                        'events' => [
                            'load' => new JsExpression("function () {
                                    servicesChart = this;
                                    loadChartServicios(servicesChart);

                                    setInterval(function () {
                                        loadChartServicios(servicesChart);
                                    }, 5000); 
                                }"),
                        ],
                    ],
                    'credits' => false,
                    'title' => false, //['text' => 'Servicios'],
                    'xAxis' => [
                        'categories' => [] //$charServData['categories']
                    ],
                    'plotOptions' => [
                        'areaspline' => ['dataLabels' => ['enabled' => 'true']],
                    ],
                    'series' => [['name' => 'Órdenes de Servicio', 'data' => [], 'events' => ['legendItemClick' => 'function() {return false;}']]],
                ]
            ]); ?>
        </div>

        <div class="col-md-6 well" style="max-width:700px">
            <div class="row">
                <div class="col-md-6" style="max-width:150px;">
                    <label for="chartVentsFilter">Cantidad</label>
                    <select id="chartVentsFilter" class="form-control form-control-sm">
                        <?= $selectOptions ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <?php
                    echo '<label>A partir de:</label>';
                    echo DatePicker::widget([
                        'name' => 'chartVentasFrom',
                        'id' => 'chartVentasFrom',
                        'value' => date('Y-m-d', strtotime('-30 days')),
                        'options' => ['placeholder' => 'Seleccionar ...'],
                        'pluginOptions' => [
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'autoclose' => true,
                        ]
                    ]); ?>
                </div>
            </div>
            <div id="chart-ventas"></div>
            <?php
            $this->registerJs('var ventasChart;
                    function loadChartVentas(chart) {
                        var filter = $("#chartVentsFilter").val();
                        var from = $("#chartVentasFrom").val();
                        $.getJSON("' . Url::toRoute('/reportes/reportes/estadistica-ordenes-ventas?filter=') . '"+filter+"&from="+from, function (jsondata) {
                        chart.series[0].setData(jsondata.sdata);
                        chart.axes[0].setCategories(jsondata.categories);
                    });
                }');
            //$charServData = ReportesController::EstadisticaOrdenesVentas();
            echo Highcharts::widget([
                'options' => [
                    'chart' => [
                        'type' => 'areaspline',
                        'events' => [
                            'load' => new JsExpression("function () {
                                    ventasChart = this;
                                    loadChartVentas(ventasChart);

                                    setInterval(function () {
                                        loadChartVentas(ventasChart);
                                    }, 5000); 
                                }"),
                        ],
                    ],
                    'credits' => false,
                    'title' => false, ['text' => 'Ventas'],
                    'xAxis' => [
                        'categories' => [], //$charServData['categories']
                    ],
                    'plotOptions' => [
                        'areaspline' => ['dataLabels' => ['enabled' => 'true']]
                    ],
                    'series' => [['name' => 'Órdenes de Venta', 'data' => [], 'events' => ['legendItemClick' => 'function() {return false;}'], 'color' => '#FF0215']],
                ]
            ]); ?>
        </div>
    </div>

    <!-- Productos de baja existencia -->
    <h3 id="prods-baja-cant"><span class="label info-nav-badge">Productos con baja existencia en almacén</span>
        <span id="prods-baja-cant-title" class="label info-nav-badge">0</span>
    </h3>
    <div class="well">
        <div class="table-responsive" style="max-height:650px">
            <table id="tproductos" class="display table table-striped table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th><a href="#prods-baja-cant">Código</a></th>
                        <th><a href="#prods-baja-cant">Nombre</a></th>
                        <th><a href="#prods-baja-cant">Existencia</a></th>
                        <th><a href="#prods-baja-cant">Cantidad Mínima</a></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Ofertas pendientes -->
    <div id="ofertas-por-fact" class="row">
        <h3><span class="label warning-nav-badge">Ofertas pendientes por factura</span>
            <span id="ofertas-por-fact-title" class="label warning-nav-badge">0</span>
        </h3>
        <div class="col-md-5 well" style="max-width:700px">
            <h4><span class="label warning-nav-badge">Servicios</span>
                <span id="servicios-por-fact-title" class="label warning-nav-badge">0</span>
            </h4>
            <div class="table-responsive" style="max-height:700px">
                <table id="tofertas-servicios" class="display table table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><a href="#ofertas-por-fact">Código</a></th>
                            <th><a href="#ofertas-por-fact">Fecha creada</a></th>
                            <th><a href="#ofertas-por-fact">Cliente</a></th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="col-md-2" style="max-width:50px">
        </div>
        <div class="col-md-5 well" style="max-width:700px">
            <h4><span class="label warning-nav-badge">Ventas</span>
                <span id="ventas-por-fact-title" class="label warning-nav-badge">0</span>
            </h4>
            <div class="table-responsive" style="max-height:700px">
                <table id="tofertas-ventas" class="display table table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><a href="#ofertas-por-fact">Código</a></th>
                            <th><a href="#ofertas-por-fact">Fecha creada</a></th>
                            <th><a href="#ofertas-por-fact">Cliente</a></th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- Facturas pendientes -->
    <div id="facturas-pend" class="row">
        <h3><span class="label green-nav-badge">Facturas pendientes por cobrar</span>
            <span id="facturas-pend-title" class="label green-nav-badge">0</span>
        </h3>
        <div class="col-md-5 well" style="max-width:700px">
            <h4><span class="label green-nav-badge">Servicios</span>
                <span id="servicios-pend-title" class="label green-nav-badge">0</span>
            </h4>
            <div class="table-responsive" style="max-height:800px">
                <table id="tfacturas-servicios" class="display table table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><a href="#facturas-pend">Código</a></th>
                            <th><a href="#facturas-pend">Fecha creada</a></th>
                            <th><a href="#facturas-pend">Cliente</a></th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="col-md-2" style="max-width:50px">
        </div>
        <div class="col-md-5 well" style="max-width:700px">
            <h4><span class="label green-nav-badge">Ventas</span>
                <span id="ventas-pend-title" class="label green-nav-badge">0</span>
            </h4>
            <div class="table-responsive" style="max-height:800px">
                <table id="tfacturas-ventas" class="display table table-striped table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><a href="#facturas-pend">Código</a></th>
                            <th><a href="#facturas-pend">Fecha creada</a></th>
                            <th><a href="#facturas-pend">Cliente</a></th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); // output buffer the javascript to register later 
?>
<script>
    // chartOrdenServicios()
    var bajoStock, O_ventasPendientes, O_servPendientes, F_ventasPendientes, F_servPendientes = 0;

    fill_productos();
    fill_ofertas_servicios();
    fill_facturas_servicios();

    function fill_productos(filter = '') {
        $('#tproductos').DataTable({
            "processing": true,
            "serverSide": false,
            "order": [],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Filas _MENU_",
                "info": "Mostrando _START_ - _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 - 0 de 0 registros",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "infoPostFix": "",
                "search": "Buscar",
                "loadingRecords": "Cargando...",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay datos",
                "bSort": true,
                "paginate": {
                    "first": "Primero",
                    "previous": "«",
                    "next": "»",
                    "last": "Último",
                },
            },
            "ajax": {
                url: "<?= Url::toRoute('/inventario/productos/productos-bajo-stock-ajax'); ?>",
                type: "POST",
                /* data:{
                    filterVal:filter,
                }*/
            },
            "initComplete": function(settings, json) {
                //console.log(document.getElementById('prods-baja-cant-info'));
                bajoStock = json['recordsFiltered'];
                document.getElementById('prods-baja-cant-info').innerText = bajoStock;
                document.getElementById('prods-baja-cant-title').innerText = bajoStock;
            }
        });

        //  acomodarFiltrosDataTable('tproductos');
    };

    var ofertasCount = 0;

    function fill_ofertas_ventas(filter = '') {
        $('#tofertas-ventas').DataTable({
            "processing": true,
            "serverSide": false,
            "order": [],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Filas _MENU_",
                "info": "Mostrando _START_ - _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 - 0 de 0 registros",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "infoPostFix": "",
                "search": "Buscar",
                "loadingRecords": "Cargando...",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay datos",
                "bSort": true,
                "paginate": {
                    "first": "Primero",
                    "previous": "«",
                    "next": "»",
                    "last": "Último",
                },
            },
            "ajax": {
                url: "<?= Url::toRoute('/facturacion/ordenventas/get-ofertas-ajax'); ?>",
                type: "POST",
            },
            "initComplete": function(settings, json) {
                O_ventasPendientes = parseInt(json['recordsFiltered']);
                ofertasCount += O_ventasPendientes;
                document.getElementById('ventas-por-fact-title').innerText = O_ventasPendientes; //parseInt(json['recordsFiltered']);
                document.getElementById('ofertas-por-fact-info').innerText = ofertasCount;
                document.getElementById('ofertas-por-fact-title').innerText = ofertasCount;
            }
        });

        // acomodarFiltrosDataTable('tofertas-ventas');
    };

    function fill_ofertas_servicios(filter = '') {
        $('#tofertas-servicios').DataTable({
            "processing": true,
            "serverSide": false,
            "order": [],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Filas _MENU_",
                "info": "Mostrando _START_ - _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 - 0 de 0 registros",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "infoPostFix": "",
                "search": "Buscar",
                "loadingRecords": "Cargando...",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay datos",
                "bSort": true,
                "paginate": {
                    "first": "Primero",
                    "previous": "«",
                    "next": "»",
                    "last": "Último",
                },
            },
            "ajax": {
                url: "<?= Url::toRoute('/facturacion/ordenservicios/get-ofertas-ajax'); ?>",
                type: "POST",
            },
            "initComplete": function(settings, json) {
                O_servPendientes = parseInt(json['recordsFiltered']);
                ofertasCount += O_servPendientes //parseInt(json['recordsFiltered']);
                document.getElementById('servicios-por-fact-title').innerText = O_servPendientes; //parseInt(json['recordsFiltered']);
                fill_ofertas_ventas();
            }
        });

        // acomodarFiltrosDataTable('tofertas-servicios');
    };

    var factCount = 0;

    function fill_facturas_ventas(filter = '') {
        $('#tfacturas-ventas').DataTable({
            "processing": true,
            "serverSide": false,
            "order": [],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Filas _MENU_",
                "info": "Mostrando _START_ - _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 - 0 de 0 registros",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "infoPostFix": "",
                "search": "Buscar",
                "loadingRecords": "Cargando...",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay datos",
                "bSort": true,
                "paginate": {
                    "first": "Primero",
                    "previous": "«",
                    "next": "»",
                    "last": "Último",
                },
            },
            "ajax": {
                url: "<?= Url::toRoute('/facturacion/ordenventas/get-facturas-pendientes-ajax'); ?>",
                type: "POST",
            },
            "initComplete": function(settings, json) {
                F_ventasPendientes = parseInt(json['recordsFiltered']);
                factCount += F_ventasPendientes; //parseInt(json['recordsFiltered']);
                document.getElementById('facturas-pend-info').innerText = factCount;
                document.getElementById('facturas-pend-title').innerText = factCount;
                document.getElementById('ventas-pend-title').innerText = F_ventasPendientes; // parseInt(json['recordsFiltered']);
            }
        });

        //   acomodarFiltrosDataTable('tfacturas-ventas');
    };

    function fill_facturas_servicios(filter = '') {
        $('#tfacturas-servicios').DataTable({
            "processing": true,
            "serverSide": false,
            "order": [],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Filas _MENU_",
                "info": "Mostrando _START_ - _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 - 0 de 0 registros",
                "infoFiltered": "(Filtrado de _MAX_ registros)",
                "infoPostFix": "",
                "search": "Buscar",
                "loadingRecords": "Cargando...",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay datos",
                "bSort": true,
                "paginate": {
                    "first": "Primero",
                    "previous": "«",
                    "next": "»",
                    "last": "Último",
                },
            },
            "ajax": {
                url: "<?= Url::toRoute('/facturacion/ordenservicios/get-facturas-pendientes-ajax'); ?>",
                type: "POST",
            },
            "initComplete": function(settings, json) {
                F_servPendientes = parseInt(json['recordsFiltered']);
                factCount += F_servPendientes; //parseInt(json['recordsFiltered']);
                document.getElementById('servicios-pend-title').innerText = F_servPendientes; //parseInt(json['recordsFiltered']);
                fill_facturas_ventas();
            }
        });

        // acomodarFiltrosDataTable('tfacturas-servicios');
    };

    //Selectores del grafico de servicios
    $("#chartServsFilter").on("change", function() {
        loadChartServicios(servicesChart);
    });
    $("#chartServicesFrom").on("change", function() {
        loadChartServicios(servicesChart);
    });

    //Selectores del grafico de ventas
    $("#chartVentsFilter").on("change", function() {
        loadChartVentas(ventasChart);
    });
    $("#chartVentasFrom").on("change", function() {
        loadChartVentas(ventasChart);
    });

    //Chequeo de los productos de bajo stock 
    function checkStock() {
        $.ajax({
            type: "post",
            url: "<?= Url::toRoute('/inventario/productos/stock-compare') ?>",
            dataType: "json",
            success: function(stock) {
                if (stock != bajoStock) {
                    $('#tproductos').DataTable().destroy();
                    fill_productos();
                }
            }
        });
    };

    //Chequeo de las ofertas de servicios pendientes
    function checkOfertasServicios() {
        $.ajax({
            type: "post",
            url: "<?= Url::toRoute('/facturacion/ordenservicios/ofertas-count') ?>",
            dataType: "json",
            success: function(data) {
                if (data != O_servPendientes) {
                    $('#tofertas-servicios').DataTable().destroy();
                    $('#tofertas-ventas').DataTable().destroy();
                    ofertasCount = 0;
                    fill_ofertas_servicios();
                }
            }
        });
    };

    //Chequeo de las ofertas de ventas pendientes
    function checkOfertasVentas() {
        $.ajax({
            type: "post",
            url: "<?= Url::toRoute('/facturacion/ordenventas/ofertas-count') ?>",
            dataType: "json",
            success: function(data) {
                if (data != O_ventasPendientes) {
                    $('#tofertas-servicios').DataTable().destroy();
                    $('#tofertas-ventas').DataTable().destroy();
                    ofertasCount = 0;
                    fill_ofertas_servicios();
                }
            }
        });
    };

    //Chequeo de las facturas de servicios pendientes por cobrar
    function checkFacturasServicios() {
        $.ajax({
            type: "post",
            url: "<?= Url::toRoute('/facturacion/ordenservicios/facturas-count') ?>",
            dataType: "json",
            success: function(data) {
                if (data != F_servPendientes) {
                    $('#tfacturas-servicios').DataTable().destroy();
                    $('#tfacturas-ventas').DataTable().destroy();
                    factCount = 0;
                    fill_facturas_servicios();
                }
            }
        });
    };

    //Chequeo de las facturas de ventas pendientes por cobrar
    function checkFacturasVentas() {
        $.ajax({
            type: "post",
            url: "<?= Url::toRoute('/facturacion/ordenventas/facturas-count') ?>",
            dataType: "json",
            success: function(data) {
                if (data != F_ventasPendientes) {
                    $('#tfacturas-servicios').DataTable().destroy();
                    $('#tfacturas-ventas').DataTable().destroy();
                    factCount = 0;
                    fill_facturas_servicios();
                }
                /* else
                     console.log("Check for Facturas Ventas Pendientes without changes");*/
            }
        });
    };

    setInterval(function() {
        checkStock();

        checkOfertasServicios();
        checkOfertasVentas();

        checkFacturasServicios();
        checkFacturasVentas();

        $('.anim').toggleClass('magictime puffIn');
    }, 7000);
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>