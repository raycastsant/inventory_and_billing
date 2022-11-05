<?php

namespace backend\modules\Facturacion;

class ESTADOS
{
    const ABIERTO = 'ABIERTA';   //Estado de Oferta
    const CANCELADO = 'CANCELADO';  //Se cancela la Orden
    const FACTURADO = 'FACTURADO';  //Se crea la factura pero aun no esta cobrada
    const COBRADO = 'COBRADO';  //Orden Facturada y Cobrada
}
