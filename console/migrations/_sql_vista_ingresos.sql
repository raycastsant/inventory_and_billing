DROP VIEW IF EXISTS view_ingresos;
CREATE VIEW view_ingresos AS (
    SELECT SUM(productos.costo * productos_orden_venta.cantidad) AS costo,
        SUM(
            productos_orden_venta.cantidad * productos_orden_venta.precio
        ) AS ingreso,
        orden_ventas.area_id,
        orden_ventas.fecha_cobrada,
        'ventas' as concepto,
        orden_ventas.cliente_id
    FROM `productos`
        INNER JOIN `productos_orden_venta` ON productos.id = productos_orden_venta.producto_id
        INNER JOIN `orden_ventas` ON productos_orden_venta.orden_venta_id = orden_ventas.id
    WHERE orden_ventas.estado_orden_id = 4
        AND orden_ventas.eliminado = false
        AND fecha_cobrada <> '0000-00-00'
    GROUP BY orden_ventas.area_id,
        orden_ventas.fecha_cobrada,
        cliente_id
)
UNION
(
    SELECT SUM(
            productos.costo * productos_orden_servicios.cant_productos
        ) AS costo,
        SUM(
            productos_orden_servicios.cant_productos * productos_orden_servicios.precio
        ) AS ingreso,
        orden_servicios.area_id,
        orden_servicios.fecha_cobrada,
        'servicios' as concepto,
        orden_servicios.cliente_id
    FROM `productos`
        INNER JOIN `productos_orden_servicios` ON productos.id = productos_orden_servicios.producto_id
        INNER JOIN `orden_servicios` ON productos_orden_servicios.orden_id = orden_servicios.id
    WHERE orden_servicios.estado_orden_id = 4
        AND orden_servicios.eliminado = false
        AND fecha_cobrada <> '0000-00-00'
    GROUP BY orden_servicios.area_id,
        orden_servicios.fecha_cobrada,
        cliente_id
)
UNION
(
    SELECT 0 AS costo,
        SUM(servicio_trabajador.precio) AS ingreso,
        orden_servicios.area_id,
        orden_servicios.fecha_cobrada,
        'servicios' as concepto,
        orden_servicios.cliente_id
    FROM servicio_trabajador
        INNER JOIN `orden_servicios` ON servicio_trabajador.orden_servicio_id = orden_servicios.id
    WHERE orden_servicios.estado_orden_id = 4
        AND orden_servicios.eliminado = false
        AND fecha_cobrada <> '0000-00-00'
    GROUP BY orden_servicios.area_id,
        orden_servicios.fecha_cobrada,
        cliente_id
);