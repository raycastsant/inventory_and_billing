START TRANSACTION;
delete from clientes;
delete from servicios;
delete from productos;
delete from orden_servicios;
delete from orden_ventas;
delete from orden_compra;
delete from trabajador;
delete from gastos;
delete from devoluciones;
delete from trazas;
delete from trazas_productos;
delete from trazas_servicios;
delete from trazas_ventas;
update orden_series set valor=1;
COMMIT;
