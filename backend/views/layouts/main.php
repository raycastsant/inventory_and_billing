<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use backend\assets\AppAsset;
use common\widgets\Alert;
use backend\components\UserRole;
use kartik\sidenav\SideNav;
use yii\helpers\Url;

Yii::$app->name = "TDEA";

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="shorcut icon" href="<?php echo Yii::$app->getHomeUrl(); ?>/favicon.ico" type="image/x-icon">
</head>
<body>
<?php $this->beginBody() ?>

<div id="contentIndex" class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl.'site/index',
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $user_id = Yii::$app->user->getId();
    $keys = array_keys(Yii::$app->authManager->getRolesByUser($user_id));
    $rol = "GUEST";
    if(isset($keys[0]))
        $rol = $keys[0];

    if (Yii::$app->user->isGuest) {
       // $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Entrar', 'icon' => 'user', 'url' => ['/site/login']];
    } else {
        //Mostrar el dashboard
        if(Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index' /*&&
            ($rol == UserRole::ROL_GESTOR_AREA || $rol == UserRole::ROL_SUPERVISOR) */) {
                $menuItems[] = '<li>'.Html::a('<span id="prods-baja-cant-info" class="badge info-nav-badge anim">0</span> Productos con baja existencia', ['index#prods-baja-cant'], ['class' => 'btn btn-default info-nav-button pull-right']).'</li>';
                $menuItems[] = '<li>&nbsp;</li>';
                $menuItems[] = '<li>'.Html::a('<span id="ofertas-por-fact-info" class="badge warning-nav-badge anim">0</span> Ofertas por facturar', ['index#ofertas-por-fact'], ['class' => 'btn btn-default warning-nav-button pull-right']).'</li>';
                $menuItems[] = '<li>&nbsp;</li>';
                $menuItems[] = '<li>'.Html::a('<span id="facturas-pend-info" class="badge green-nav-badge anim">0</span> Facturas por cobrar', ['index#facturas-pend'], ['class' => 'btn btn-default green-nav-button pull-right']).'</li>';
        }

        //User Info
        /*$menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Cerrar Sesión (<span class="glyphicon glyphicon-user"></span> ' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';*/

        $menuItems[] = '<li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="glyphicon glyphicon-user"></span> ' . Yii::$app->user->identity->username . ' <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="'.Url::toRoute('/site/reset-password').'" class="btn btn-link btn-link2">Cambiar contraseña <span class="glyphicon glyphicon-random"></span></a></li>
                                <li class="divider"></li>
                                <li>'
                                    . Html::beginForm(['/site/logout'], 'post')
                                    . Html::submitButton(
                                        'Cerrar Sesión   <span class="glyphicon glyphicon-off"></span>', ['class' => 'btn btn-link btn-link2']
                                    )
                                    . Html::endForm().
                                '</li>
                            </ul>
                        </li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <?php
                    $sideItems = [
                        ['label' => 'Inicio', 'icon' => 'home', 'url' => ['/site/index']],
                    ];
                
                    if(in_array(UserRole::ROL_INVENTARIO, $keys) || in_array(UserRole::ROL_SUPERVISOR, $keys)) {
                        $sideItems[] = [
                            'label' => 'Inventario',
                            'items' => [
                                        ['label' => 'Todos', 'url' => ['/inventario/productos/index'], 'icon' => 'asterisk'],
                                        ['label' => 'Existencias', 'url' => ['/inventario/productos/existencias'], 'icon' => 'list-alt'],
                                        ['label' => 'Órdenes de compra', 'url' => ['/inventario/orden-compra/index']],
                            ],
                        ];
                    }
                
                    if(in_array(UserRole::ROL_GESTOR_AREA, $keys) || in_array(UserRole::ROL_SUPERVISOR, $keys) || in_array(UserRole::ROL_JEFE_AREA, $keys)) { 
                        $sideItems[] = ['label' => 'Órdenes',
                                        'items' => [
                                                        ['label' => ' SERVICIOS', 'url' => ['/facturacion/ordenservicios/index'], 'icon' => 'cog'],
                                                        ['label' => 'VENTAS','url' => ['/facturacion/ordenventas/index'], 'icon' => 'barcode'],
                                                   ],
                                        ];
                        $sideItems[] = ['label' => 'Devoluciones',
                                        'items' => [
                                                        ['label' => 'Servicios', 'url' => ['/facturacion/devoluciones/index-servicios'], 'icon' => 'cog'],
                                                        ['label' => 'Ventas','url' => ['/facturacion/devoluciones/index-ventas'], 'icon' => 'barcode'],
                                                   ],
                                        ];
                   /* }
                
                    if($rol == UserRole::ROL_SUPERVISOR "" ) {*/
                        $sideItems[] = ['label' => 'Clientes', 'url' => ['/facturacion/clientes/index']];
                        $sideItems[] = ['label' => 'Vehículos', 'url' => ['/facturacion/vehiculos/index']];
                        $sideItems[] = ['label' => 'Trabajadores', 'url' => ['/facturacion/trabajadores/index']];
                    }
                
                    if($rol == UserRole::ROL_ADMIN) {
                        $sideItems[] = [
                                        'label' => 'Nomencladores',
                                        'items' => [
                                             //       '<li class="dropdown-header">INVENTARIO</li>',
                                                    ['label' => 'Áreas', 'url' => ['/nomencladores/areas/index']],
                                                    ['label' => 'Categorías de productos', 'url' => ['/inventario/tipoproductos/index']],
                                               //     '<li class="divider"></li>',
                                                  //  '<li class="dropdown-header">FACTURACIÓN</li>',
                                                    ['label' => 'Servicios', 'url' => ['/nomencladores/servicios/index']],
                                                    ['label' => 'Tipos de clientes', 'url' => ['/nomencladores/tipoclientes/index']],
                                                    ['label' => 'Unidades de medida', 'url' => ['/nomencladores/unidadmedidas/index']],
                                                    
                                        ],
                                        
                                    ];
                        $sideItems[] = ['label' => 'Usuarios', 'url' => ['/admin/user/index']];
                        $sideItems[] = ['label' => 'Monedas', 'url' => ['/facturacion/monedas/index']];
                        $sideItems[] = ['label' => 'Tasas de cambio', 'url' => ['/facturacion/moneda-cambios/index']];
                    }
                
                if($rol == UserRole::ROL_SUPERADMIN) {
                    $sideItems[] = [
                                    'label' => 'Administración',
                                    'items' => [
                                                ['label' => 'Usuarios', 'url' => ['/admin/user/index']],
                                                ['label' => 'Asignaciones', 'url' => ['/admin/assignment']],
                                                ['label' => 'Roles', 'url' => ['/admin/role']],
                                                ['label' => 'Permisos', 'url' => ['/admin/permission']],
                                                ['label' => 'Reglas', 'url' => ['/admin/rule']],
                                    ],
                                ];
                }

                if(count($keys) > 0) {
                    $sideItems[] = [
                        'label' => 'Reportes',
                        'items' => [
                                    ['label' => 'Empresas sin responsable', 'url' => ['/reportes/reportes/empresas-sin-responsable']],
                                    ['label' => 'Gastos e Ingresos', 'url' => ['/reportes/reportes/gastos-ingresos']],
                                    ['label' => 'Productos más vendidos', 'url' => ['/reportes/reportes/productos-mas-vendidos']],
                                    ['label' => 'Ventas por clientes', 'url' => ['/reportes/reportes/ventas-por-clientes']],
                                  //  ['label' => 'Imprimir Acta de Entrega', 'url' => ['/reportes/reportes/acta-entrega-form'], 'icon'=>'print'],
                                  //  ['label' => 'Imprimir Orden de Servicio', 'url' => ['/reportes/reportes/acta-servicio-form'], 'icon'=>'print'],
                        ],
                    ];
                  /*  $sideItems[] = ['label' => 'Imprimir Acta de Entrega', 'url' => ['/reportes/reportes/acta-entrega-form']];
                    $sideItems[] = ['label' => 'Imprimir Orden de Servicio', 'url' => ['/reportes/reportes/acta-servicio-form']];*/
                }

                    echo SideNav::widget([
                    'type' => SideNav::TYPE_PRIMARY,
                   // 'heading' => 'Options',
                    'items' => $sideItems, 
                ]); 
                ?>
            </div>
            <div class="col-md-10">
                <?php /*echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]); */
                
                $js = '$(".alert").animate({opacity: 3.0}, 4000).fadeOut("slow");' ;
                $this->registerJs($js, yii\web\View::POS_READY);
                ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right">Powered by NEXT1 Solutions</p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
