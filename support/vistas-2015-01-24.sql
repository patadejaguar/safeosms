-- - 
-- -  Vistas de la DB
-- -
-- -

-- 27-enero/2014

DELIMITER $$

DROP VIEW IF EXISTS `recibos_datos_bancarios`$$
DROP TABLE IF EXISTS `recibos_datos_bancarios`$$

CREATE VIEW `recibos_datos_bancarios` AS (
SELECT
  `bancos_operaciones`.`recibo_relacionado` AS `recibo`,
  COUNT(`bancos_operaciones`.`idcontrol`)   AS `operaciones`,
  MAX(`bancos_operaciones`.`cuenta_bancaria`) AS `banco`,
  MAX(`bancos_operaciones`.`fecha_expedicion`) AS `fecha`,
  SUM(`bancos_operaciones`.`monto_real`)    AS `monto`
FROM `bancos_operaciones`
GROUP BY `bancos_operaciones`.`recibo_relacionado`)$$

DELIMITER ;


-- -  Vista de Usuarios
DELIMITER $$

DROP VIEW IF EXISTS `usuarios`$$
DROP TABLE IF EXISTS `usuarios`$$

CREATE  VIEW `usuarios` AS select `t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` AS 
`idusuarios`,`t_03f996214fba4a1d05a68b18fece8e71`.`f_28fb96d57b21090705cfdf8bc3445d2a` 
AS `nombreusuario`,`t_03f996214fba4a1d05a68b18fece8e71`.`f_34023acbff254d34664f94c3e08d836e` 
AS `contrasenna`,`t_03f996214fba4a1d05a68b18fece8e71`.`nombres` AS `nombres`,`t_03f996214fba4a1d05a68b18fece8e71`.`apellidopaterno` 
AS `apellidopaterno`,`t_03f996214fba4a1d05a68b18fece8e71`.`apellidomaterno` 
AS `apellidomaterno`,`t_03f996214fba4a1d05a68b18fece8e71`.`puesto` 
AS `puesto`,`t_03f996214fba4a1d05a68b18fece8e71`.`f_f2cd801e90b78ef4dc673a4659c1482d` 
AS `niveldeacceso`,`t_03f996214fba4a1d05a68b18fece8e71`.`periodo_responsable` 
AS `periodo_responsable`,`t_03f996214fba4a1d05a68b18fece8e71`.`estatus` 
AS `estatus`,
`t_03f996214fba4a1d05a68b18fece8e71`.`date_expire` AS `expira`,
`t_03f996214fba4a1d05a68b18fece8e71`.`sucursal`,

CONCAT(
`t_03f996214fba4a1d05a68b18fece8e71`.`apellidopaterno`,
' ',`t_03f996214fba4a1d05a68b18fece8e71`.`apellidomaterno`,
' ', `t_03f996214fba4a1d05a68b18fece8e71`.`nombres`) AS 'nombrecompleto',
`t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` AS `id`,
`t_03f996214fba4a1d05a68b18fece8e71`.`cuenta_contable_de_caja` AS `cuenta_contable_de_caja`,
`t_03f996214fba4a1d05a68b18fece8e71`.`codigo_de_persona` AS  `codigo_de_persona`
from `t_03f996214fba4a1d05a68b18fece8e71` 
$$

DELIMITER ;

-- -	Vista de Sumas Flujo de Efectivo
DELIMITER $$

DROP VIEW IF EXISTS `sumas_flujo_efectivo`$$
DROP TABLE IF EXISTS `sumas_flujo_efectivo`$$

CREATE VIEW `sumas_flujo_efectivo` AS select `creditos_flujoefvo`.`socio_flujo` AS `socio`,`creditos_flujoefvo`.`solicitud_flujo` AS `solicitud`,`creditos_flujoefvo`.`tipo_flujo` AS `tipo`,sum(`creditos_flujoefvo`.`afectacion_neta`) AS `sumas` from `creditos_flujoefvo` group by `creditos_flujoefvo`.`tipo_flujo`,`creditos_flujoefvo`.`solicitud_flujo`$$

DELIMITER ;

-- -	Vista de Sumas Movimientos de Poliza
DELIMITER $$

DROP VIEW IF EXISTS `suma_mvtos_poliza`$$
DROP TABLE IF EXISTS `suma_mvtos_poliza`$$

CREATE VIEW `suma_mvtos_poliza` AS (select `contable_movimientos`.`ejercicio` AS `ejercicio`,`contable_movimientos`.`periodo` AS `periodo`,`contable_movimientos`.`numeropoliza` AS `numeropoliza`,`contable_movimientos`.`tipopoliza` AS `tipopoliza`,sum(`contable_movimientos`.`importe`) AS `saldos`,`contable_movimientos`.`tipomovimiento` AS `tipomovimiento` from `contable_movimientos` group by `contable_movimientos`.`ejercicio`,`contable_movimientos`.`periodo`,`contable_movimientos`.`numeropoliza`,`contable_movimientos`.`tipopoliza`,`contable_movimientos`.`tipomovimiento`)$$

DELIMITER ;

-- -	Vista de Solicitudes
DELIMITER $$

DROP VIEW IF EXISTS `solicitudes`$$
DROP TABLE IF EXISTS `solicitudes`$$

CREATE VIEW `solicitudes` AS select SQL_CACHE `creditos_solicitud`.`numero_socio` AS `numero_socio`,`creditos_solicitud`.`numero_solicitud` AS `solicitud`,`creditos_modalidades`.`descripcion_modalidades` AS `modalidad`,`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `convenio`,`creditos_solicitud`.`fecha_solicitud` AS `fecha_solicitud`,`creditos_solicitud`.`monto_solicitado` AS `monto_solicitado`,format((`creditos_solicitud`.`dias_autorizados` / 30.41666666),0) AS `plazo`,`creditos_solicitud`.`fecha_vencimiento` AS `fecha_vencimiento`,`creditos_solicitud`.`saldo_actual` AS `saldo_actual`,`creditos_solicitud`.`saldo_vencido` AS `saldo_vencido`,`creditos_solicitud`.`interes_diario` AS `interes_diario`,`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad` from (((`creditos_solicitud` join `creditos_modalidades`) join `creditos_tipoconvenio`) join `creditos_periocidadpagos`) where ((`creditos_modalidades`.`idcreditos_modalidades` = `creditos_solicitud`.`tipo_credito`) and (`creditos_tipoconvenio`.`idcreditos_tipoconvenio` = `creditos_solicitud`.`tipo_convenio`) and (`creditos_periocidadpagos`.`idcreditos_periocidadpagos` = `creditos_solicitud`.`periocidad_de_pago`))$$

DELIMITER ;

-- -	Vista de Socios
DELIMITER $$

DROP VIEW IF EXISTS `socios`$$
DROP TABLE IF EXISTS `socios`$$

CREATE VIEW `socios` AS (select SQL_CACHE `socios_general`.`codigo` AS `codigo`,
TRIM(CONCAT(`socios_general`.`nombrecompleto`,_utf8' ',`socios_general`.`apellidopaterno`,_utf8' ',`socios_general`.`apellidomaterno`)) AS `nombre`,
`socios_general`.`cajalocal` AS `numero_caja_local`, `socios_general`.`dependencia` 
AS `iddependencia`,
`socios_aeconomica_dependencias`.`descripcion_dependencia` AS `dependencia`,
`socios_aeconomica_dependencias`.`nombre_corto` AS `alias_dependencia`,

`socios_genero`.`descripcion_genero` AS `genero`,`socios_tipoingreso`.`descripcion_tipoingreso` AS `tipo_ingreso`,
`socios_general`.`grupo_solidario`  AS 'grupo'
from (((`socios_general` join `socios_aeconomica_dependencias`) join `socios_genero`) join `socios_tipoingreso`) where ((`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` = `socios_general`.`dependencia`) and (`socios_genero`.`idsocios_genero` = `socios_general`.`genero`) and (`socios_tipoingreso`.`idsocios_tipoingreso` = `socios_general`.`tipoingreso`)))$$

DELIMITER ;

-- -	Vista de Reporte de la Federacion
DELIMITER $$

DROP VIEW IF EXISTS `reporte_federacion`$$
DROP TABLE IF EXISTS `reporte_federacion`$$

CREATE VIEW `reporte_federacion` AS select `creditos_solicitud`.`numero_socio` AS `socio`,`creditos_solicitud`.`numero_solicitud` AS `solicitud`,`creditos_modalidades`.`descripcion_modalidades` AS `modalidad`,`creditos_periocidadpagos`.`titulo_en_informe` AS `condiciones_de_pago`,`creditos_solicitud`.`fecha_ministracion` AS `fecha_de_otorgamiento`,`creditos_solicitud`.`monto_autorizado` AS `monto_original`,`creditos_solicitud`.`fecha_vencimiento` AS `fecha_de_vencimiento`,`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_nominal_anual`,`creditos_solicitud`.`pagos_autorizados` AS `numero_de_pagos`,`creditos_solicitud`.`saldo_actual` AS `saldo_insoluto`,`creditos_solicitud`.`fecha_ultimo_mvto` AS `fecha_ultimo_mvto`,`creditos_solicitud`.`periocidad_de_pago` AS `frecuencia`,`creditos_solicitud`.`estatus_actual` AS `estatus_actual` from ((`creditos_solicitud` join `creditos_modalidades`) join `creditos_periocidadpagos`) where ((`creditos_modalidades`.`idcreditos_modalidades` = `creditos_solicitud`.`tipo_credito`) and (`creditos_periocidadpagos`.`idcreditos_periocidadpagos` = `creditos_solicitud`.`periocidad_de_pago`) and (`creditos_solicitud`.`saldo_actual` >= 0.99) and (`creditos_solicitud`.`estatus_actual` <> 50))$$

DELIMITER ;

-- -	Vista de Recuperaciones Netas
DELIMITER $$

DROP VIEW IF EXISTS `recuperaciones_netas`$$
DROP TABLE IF EXISTS `recuperaciones_netas`$$

CREATE VIEW `recuperaciones_netas` AS select `operaciones_mvtos`.`docto_afectado` AS `solicitud`,sum(`operaciones_mvtos`.`afectacion_real`) AS `recuperado`,max(`operaciones_mvtos`.`fecha_operacion`) AS `fecha`,max(`operaciones_mvtos`.`periodo_socio`) AS `periodos`,count(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `mvtos` from `operaciones_mvtos` where (`operaciones_mvtos`.`tipo_operacion` = 120) group by `operaciones_mvtos`.`docto_afectado`$$

DELIMITER ;

-- -	Vista de Sumas de Operaciones
DELIMITER $$

DROP TABLE IF EXISTS `operaciones_sumas`$$
DROP VIEW IF EXISTS `operaciones_sumas`$$

CREATE VIEW `operaciones_sumas` AS select `operaciones_mvtos`.`socio_afectado` AS `socio`,`operaciones_mvtos`.`docto_afectado` AS `docto`,`operaciones_mvtos`.`tipo_operacion` AS `operacion`,`operaciones_tipos`.`descripcion_operacion` AS `concepto`,sum(`operaciones_mvtos`.`afectacion_real`) AS `monto` from (`operaciones_mvtos` join `operaciones_tipos`) where (`operaciones_tipos`.`idoperaciones_tipos` = `operaciones_mvtos`.`tipo_operacion`) group by `operaciones_mvtos`.`docto_afectado`$$

DELIMITER ;

-- -	Vista de Operaciones no Estadisticas.
DELIMITER $$

DROP TABLE IF EXISTS `operaciones_no_estadisticas`$$
DROP VIEW IF EXISTS `operaciones_no_estadisticas`$$

CREATE VIEW `operaciones_no_estadisticas` AS select `operaciones_mvtos`.`socio_afectado` AS `socio`,`operaciones_mvtos`.`docto_afectado` AS `documento`,`operaciones_mvtos`.`recibo_afectado` AS `recibo`,`operaciones_mvtos`.`fecha_afectacion` AS `fecha`,`operaciones_tipos`.`descripcion_operacion` AS `tipo_de_operacion`,`operaciones_mvtos`.`afectacion_real` AS `monto`,`operaciones_mvtos`.`detalles` AS `detalles` from (`operaciones_mvtos` join `operaciones_tipos`) where ((`operaciones_tipos`.`idoperaciones_tipos` = `operaciones_mvtos`.`tipo_operacion`) and (`operaciones_tipos`.`es_estadistico` = _utf8'0'))$$

DELIMITER ;

-- -	Vista de Detalle de Operaciones
DELIMITER $$

DROP TABLE IF EXISTS `operaciones_detalle`$$
DROP VIEW IF EXISTS `operaciones_detalle`$$

CREATE VIEW `operaciones_detalle` AS select `operaciones_mvtos`.`idoperaciones_mvtos` AS `operacion`,`operaciones_mvtos`.`socio_afectado` AS `socio`,`operaciones_mvtos`.`docto_afectado` AS `documento`,`operaciones_mvtos`.`recibo_afectado` AS `recibo`,`operaciones_mvtos`.`fecha_afectacion` AS `fecha`,`operaciones_tipos`.`descripcion_operacion` AS `tipo_de_operacion`,`operaciones_mvtos`.`afectacion_real` AS `monto`,`operaciones_mvtos`.`detalles` AS `detalles` from (`operaciones_mvtos` join `operaciones_tipos`) where (`operaciones_tipos`.`idoperaciones_tipos` = `operaciones_mvtos`.`tipo_operacion`)$$

DELIMITER ;

-- -	Vista de Detalle de Operaciones No Estadisticas
DELIMITER $$

DROP TABLE IF EXISTS `operaciones_detalle_ne`$$
DROP VIEW IF EXISTS `operaciones_detalle_ne`$$

CREATE VIEW `operaciones_detalle_ne` AS select `operaciones_mvtos`.`idoperaciones_mvtos` AS `operacion`,`operaciones_mvtos`.`socio_afectado` AS `socio`,`operaciones_mvtos`.`docto_afectado` AS `documento`,`operaciones_mvtos`.`recibo_afectado` AS `recibo`,`operaciones_mvtos`.`fecha_afectacion` AS `fecha`,`operaciones_tipos`.`descripcion_operacion` AS `tipo_de_operacion`,`operaciones_mvtos`.`afectacion_real` AS `monto`,`operaciones_mvtos`.`detalles` AS `detalles` from (`operaciones_tipos` join `operaciones_mvtos` on((`operaciones_tipos`.`idoperaciones_tipos` = `operaciones_mvtos`.`tipo_operacion`))) where (`operaciones_tipos`.`es_estadistico` = _utf8'0')$$

DELIMITER ;

-- -	Vista de Operaciones
DELIMITER $$

DROP TABLE IF EXISTS `operaciones`$$
DROP VIEW IF EXISTS `operaciones`$$

CREATE VIEW `operaciones` AS select `operaciones_mvtos`.`socio_afectado` AS `socio`,`operaciones_mvtos`.`docto_afectado` AS `documento`,`operaciones_mvtos`.`recibo_afectado` AS `recibo`,`operaciones_mvtos`.`fecha_afectacion` AS `fecha`,`operaciones_tipos`.`descripcion_operacion` AS `tipo_de_operacion`,`operaciones_mvtos`.`afectacion_real` AS `monto`,`operaciones_mvtos`.`detalles` AS `detalles` from (`operaciones_mvtos` join `operaciones_tipos`) where (`operaciones_tipos`.`idoperaciones_tipos` = `operaciones_mvtos`.`tipo_operacion`)$$

DELIMITER ;

-- -	Vista de Oficiales
DELIMITER $$

DROP TABLE IF EXISTS `oficiales`$$
DROP VIEW IF EXISTS `oficiales`$$

CREATE VIEW `oficiales` AS select `t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` 
AS `id`,concat(`t_03f996214fba4a1d05a68b18fece8e71`.`nombres`,_utf8' ',`t_03f996214fba4a1d05a68b18fece8e71`.`apellidopaterno`,_utf8' ',`t_03f996214fba4a1d05a68b18fece8e71`.`apellidomaterno`) 
AS `nombre_completo`,`t_03f996214fba4a1d05a68b18fece8e71`.`puesto` AS `puesto`,
`t_03f996214fba4a1d05a68b18fece8e71`.`sucursal`,
`t_03f996214fba4a1d05a68b18fece8e71`.`estatus`
FROM `t_03f996214fba4a1d05a68b18fece8e71`
$$

DELIMITER ;

-- -	Vista de Numero de Operaciones por Recibo
DELIMITER $$

DROP VIEW IF EXISTS `num_operaciones_por_rec`$$
DROP TABLE IF EXISTS `num_operaciones_por_rec`$$

CREATE VIEW `num_operaciones_por_rec` AS (select `operaciones_mvtos`.`recibo_afectado` AS `recibo_afectado`,count(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `nums_ops` from `operaciones_mvtos` group by `operaciones_mvtos`.`recibo_afectado`)$$

DELIMITER ;

-- -	Vista de Numero de Operaciones por Docto
DELIMITER $$

DROP TABLE IF EXISTS `num_operaciones_por_docto`$$
DROP VIEW IF EXISTS `num_operaciones_por_docto`$$

CREATE VIEW `num_operaciones_por_docto` AS (select `operaciones_mvtos`.`docto_afectado` AS `docto_afectado`,count(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `nums_ops` from `operaciones_mvtos` group by `operaciones_mvtos`.`docto_afectado`)$$

DELIMITER ;

-- -	Vista de Domicilios
DELIMITER $$

DROP VIEW IF EXISTS `domicilios`$$
DROP TABLE IF EXISTS `domicilios`$$

CREATE VIEW `domicilios` AS select `socios_vivienda`.`socio_numero` AS `socio_numero`,concat(_utf8'Calle ',`socios_vivienda`.`calle`,_utf8' Num. ',`socios_vivienda`.`numero_exterior`,_utf8'-',`socios_vivienda`.`numero_interior`,_utf8' Col. ',`socios_vivienda`.`colonia`,_utf8', ',`socios_vivienda`.`localidad`,_utf8'; Tel(s) : ',`socios_vivienda`.`telefono_residencial`,_utf8'; ',`socios_vivienda`.`telefono_movil`) AS `domicilio` from `socios_vivienda` where (`socios_vivienda`.`principal` = 1)$$

DELIMITER ;

-- -	Vista de Creditos No Castigados Conciliados
DELIMITER $$

DROP VIEW IF EXISTS `creditos_no_castigados_conciliados`$$
DROP TABLE IF EXISTS `creditos_no_castigados_conciliados`$$

CREATE VIEW `creditos_no_castigados_conciliados` AS (select `creditos_solicitud`.`numero_socio` AS `codigo`,`socios`.`nombre` AS `nombre`,`creditos_solicitud`.`numero_solicitud` AS `solicitud`,`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,`creditos_solicitud`.`fecha_ministracion` AS `fecha_ministracion`,`creditos_solicitud`.`saldo_conciliado` AS `saldo`,`creditos_solicitud`.`fecha_conciliada` AS `ultima_afectacion`,`creditos_solicitud`.`estatus_actual` AS `estado`,`socios`.`numero_caja_local` AS `caja` from ((`creditos_solicitud` join `creditos_tipoconvenio`) join `socios`) where ((`creditos_solicitud`.`estatus_actual` <> 50) and (`creditos_solicitud`.`saldo_actual` <> 0) and (`socios`.`codigo` = `creditos_solicitud`.`numero_socio`) and (`creditos_tipoconvenio`.`idcreditos_tipoconvenio` = `creditos_solicitud`.`tipo_convenio`)))$$

DELIMITER ;

-- -	Vista de Creditos No Castigados
DELIMITER $$

DROP VIEW IF EXISTS `creditos_no_castigados`$$
DROP TABLE IF EXISTS `creditos_no_castigados`$$

CREATE VIEW `creditos_no_castigados` AS (select `creditos_solicitud`.`numero_socio` AS `codigo`,`socios`.`nombre` AS `nombre`,`creditos_solicitud`.`numero_solicitud` AS `solicitud`,`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,`creditos_solicitud`.`fecha_ministracion` AS `fecha_ministracion`,`creditos_solicitud`.`saldo_actual` AS `saldo`,`creditos_solicitud`.`fecha_ultimo_mvto` AS `ultima_afectacion` from ((`creditos_solicitud` join `creditos_tipoconvenio`) join `socios`) where ((`creditos_solicitud`.`estatus_actual` <> 50) and (`creditos_solicitud`.`saldo_actual` <> 0) and (`socios`.`codigo` = `creditos_solicitud`.`numero_socio`) and (`creditos_tipoconvenio`.`idcreditos_tipoconvenio` = `creditos_solicitud`.`tipo_convenio`)))$$

DELIMITER ;

-- -	Vista de Creditos Datos Contables
-- -	Mod: 2001-10-05
-- -	Mod: 2012-05-10.- se Cambian los tipos de parametros
DELIMITER $$

DROP VIEW IF EXISTS `creditos_datos_contables`$$
DROP TABLE IF EXISTS `creditos_datos_contables`$$

CREATE VIEW `creditos_datos_contables` AS (

SELECT
  `creditos_solicitud`.`numero_solicitud`                  AS `numero_solicitud`,
  `creditos_solicitud`.`numero_socio`                      AS `numero_socio`,
  `creditos_tipoconvenio`.`capital_vigente_normal`         AS `capital_vigente_normal`,
  `creditos_tipoconvenio`.`capital_vigente_reestructurado` AS `capital_vigente_reestructurado`,
  `creditos_tipoconvenio`.`capital_vigente_renovado`       AS `capital_vigente_renovado`,
  `creditos_tipoconvenio`.`capital_vencido_normal`         AS `capital_vencido_normal`,
  `creditos_tipoconvenio`.`capital_vencido_reestructurado` AS `capital_vencido_reestructurado`,
  `creditos_tipoconvenio`.`capital_vencido_renovado`       AS `capital_vencido_renovado`,
  `creditos_tipoconvenio`.`interes_vigente_normal`         AS `interes_vigente_normal`,
  `creditos_tipoconvenio`.`interes_vigente_reestructurado` AS `interes_vigente_reestructurado`,
  `creditos_tipoconvenio`.`interes_vigente_renovado`       AS `interes_vigente_renovado`,
  `creditos_tipoconvenio`.`interes_vencido_normal`         AS `interes_vencido_normal`,
  `creditos_tipoconvenio`.`interes_vencido_reestructurado` AS `interes_vencido_reestructurado`,
  `creditos_tipoconvenio`.`interes_vencido_renovado`       AS `interes_vencido_renovado`,
  `creditos_tipoconvenio`.`interes_cobrado`                AS `interes_cobrado`,
  `creditos_tipoconvenio`.`moratorio_cobrado`              AS `moratorio_cobrado`

from (`creditos_tipoconvenio` join `creditos_solicitud` on((`creditos_tipoconvenio`.`idcreditos_tipoconvenio` = `creditos_solicitud`.`tipo_convenio`))))$$

DELIMITER ;

-- - Vista de Creditos
DELIMITER $$

DROP VIEW IF EXISTS `creditos`$$
DROP TABLE IF EXISTS `creditos`$$

CREATE VIEW `creditos` AS select `creditos_solicitud`.`numero_socio` 
AS `numero_socio`,`creditos_solicitud`.`numero_solicitud` 
AS `solicitud`,`creditos_modalidades`.`descripcion_modalidades` 
AS `modalidad`,`creditos_tipoconvenio`.`descripcion_tipoconvenio` 
AS `convenio`,`creditos_solicitud`.`fecha_ministracion` 
AS `fecha_ministracion`,`creditos_solicitud`.`monto_autorizado` AS `monto_autorizado`,
format((`creditos_solicitud`.`dias_autorizados` / 30.41666666),0) AS `plazo`,
`creditos_solicitud`.`fecha_vencimiento` 
AS `fecha_vencimiento`,`creditos_solicitud`.`saldo_actual` 
AS `saldo_actual`,`creditos_solicitud`.`saldo_vencido`
 AS `saldo_vencido`,`creditos_solicitud`.`interes_diario` 
 AS `interes_diario`,`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS 
 `periocidad`,`creditos_solicitud`.`sucursal` AS `sucursal`,
 `creditos_solicitud`.`estatus_actual` AS 'estatus'
 from (((`creditos_solicitud` join `creditos_modalidades`) join `creditos_tipoconvenio`) 
 join `creditos_periocidadpagos`) 
 where ((`creditos_modalidades`.`idcreditos_modalidades` = `creditos_solicitud`.`tipo_credito`) 
 and (`creditos_tipoconvenio`.`idcreditos_tipoconvenio` = `creditos_solicitud`.`tipo_convenio`) 
 and (`creditos_periocidadpagos`.`idcreditos_periocidadpagos` = `creditos_solicitud`.`periocidad_de_pago`))$$

DELIMITER ;

-- -	Vista de Captacion Cuentas Contables
DELIMITER $$

DROP VIEW IF EXISTS `captacion_datos_contables`$$
DROP TABLE IF EXISTS `captacion_datos_contables`$$

CREATE VIEW `captacion_datos_contables` AS (select `captacion_cuentas`.`numero_cuenta` AS `numero_cuenta`,
			`captacion_cuentas`.`numero_socio` AS `numero_socio`,`captacion_subproductos`.`contable_movimientos` AS 
			`contable_movimientos`,`captacion_subproductos`.`contable_intereses_por_pagar` AS `contable_intereses_por_pagar`,
			`captacion_subproductos`.`contable_gastos_por_intereses` AS `contable_gastos_por_intereses` 
			FROM (`captacion_subproductos` join `captacion_cuentas` on ((`captacion_subproductos`.`idcaptacion_subproductos` = `captacion_cuentas`.`tipo_subproducto`))))$$

DELIMITER ;

-- -	Vista de cajeros
DELIMITER $$

DROP VIEW IF EXISTS `cajeros`$$
DROP TABLE IF EXISTS `cajeros`$$

CREATE VIEW `cajeros` AS select `t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` AS `id`,concat(`t_03f996214fba4a1d05a68b18fece8e71`.`nombres`,_utf8' ',`t_03f996214fba4a1d05a68b18fece8e71`.`apellidopaterno`,_utf8' ',`t_03f996214fba4a1d05a68b18fece8e71`.`apellidomaterno`) AS `nombre_completo`,`t_03f996214fba4a1d05a68b18fece8e71`.`puesto` AS `puesto`,
`t_03f996214fba4a1d05a68b18fece8e71`.`cuenta_contable_de_caja` AS `cuenta_contable_de_caja` 

from `t_03f996214fba4a1d05a68b18fece8e71` where (`t_03f996214fba4a1d05a68b18fece8e71`.`estatus` = _utf8'activo')$$

DELIMITER ;

-- -	Vista de Suma de garantia Liquida
DELIMITER $$

DROP VIEW IF EXISTS `garantia_liquida`$$
DROP TABLE IF EXISTS `garantia_liquida`$$

CREATE

    VIEW `garantia_liquida` 
    AS
    (SELECT
		`operaciones_mvtos`.`docto_afectado`,
		SUM(`operaciones_mvtos`.`afectacion_real` *
		`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'
	FROM
		`operaciones_mvtos` `operaciones_mvtos`
			INNER JOIN `eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
					ON `operaciones_mvtos`.`tipo_operacion` =
					`eacp_config_bases_de_integracion_miembros`.`miembro`
	WHERE
		(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2500)
	GROUP BY
		`operaciones_mvtos`.`docto_afectado`,
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
	ORDER BY
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`) $$
DELIMITER ;
-- -  Vista de Intereses Devengados
DELIMITER $$
DROP VIEW IF EXISTS `interes_normal_devengado`$$
DROP TABLE IF EXISTS `interes_normal_devengado`$$

CREATE VIEW `interes_normal_devengado` AS 
(SELECT
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`periodo_mensual`      AS `periodo`,
	`operaciones_mvtos`.`periodo_anual`        AS `ejercicio`,
	DATE_FORMAT( CONCAT( `operaciones_mvtos`.`periodo_anual`, '-', `operaciones_mvtos`.`periodo_mensual`, '-01'), '%Y%m') AS 'indice',
	SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS `interes` 
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `eacp_config_bases_de_integracion_miembros` 
		`eacp_config_bases_de_integracion_miembros` 
		ON `operaciones_mvtos`.`tipo_operacion` = 
		`eacp_config_bases_de_integracion_miembros`.`miembro` 
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2100) 
GROUP BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`periodo_mensual`,
	`operaciones_mvtos`.`periodo_anual` 
ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`periodo_anual`,
	`operaciones_mvtos`.`periodo_mensual`)$$

DELIMITER ;
-- -  Vista de Intereses Devengados Sin ****ICA****
DELIMITER $$
DROP VIEW IF EXISTS `interes_normal_devengado_sin_ica`$$
DROP TABLE IF EXISTS `interes_normal_devengado_sin_ica`$$

CREATE VIEW `interes_normal_devengado_sin_ica` AS 
(SELECT
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`periodo_mensual`      AS `periodo`,
	`operaciones_mvtos`.`periodo_anual`        AS `ejercicio`,
	DATE_FORMAT( CONCAT( `operaciones_mvtos`.`periodo_anual`, '-', `operaciones_mvtos`.`periodo_mensual`, '-01'), '%Y%m') AS 'indice',
	SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS `interes` 
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `eacp_config_bases_de_integracion_miembros` 
		`eacp_config_bases_de_integracion_miembros` 
		ON `operaciones_mvtos`.`tipo_operacion` = 
		`eacp_config_bases_de_integracion_miembros`.`miembro` 
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2100) 
	AND
	(`operaciones_mvtos`.`tipo_operacion` != 451)
GROUP BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`periodo_mensual`,
	`operaciones_mvtos`.`periodo_anual` 
ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`periodo_anual`,
	`operaciones_mvtos`.`periodo_mensual`)$$
 
DELIMITER ;

-- -  Vista de Intereses Devengados SOLO ***** ICA *****
DELIMITER $$
DROP VIEW IF EXISTS `interes_normal_devengado_solo_ica`$$
DROP TABLE IF EXISTS `interes_normal_devengado_solo_ica`$$

CREATE VIEW `interes_normal_devengado_solo_ica` AS 
(SELECT
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`periodo_mensual`      AS `periodo`,
	`operaciones_mvtos`.`periodo_anual`        AS `ejercicio`,
	DATE_FORMAT( CONCAT( `operaciones_mvtos`.`periodo_anual`, '-', `operaciones_mvtos`.`periodo_mensual`, '-01'), '%Y%m') AS 'indice',
	SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS `interes` 
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `eacp_config_bases_de_integracion_miembros` 
		`eacp_config_bases_de_integracion_miembros` 
		ON `operaciones_mvtos`.`tipo_operacion` = 
		`eacp_config_bases_de_integracion_miembros`.`miembro` 
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2100) 
	AND
	(`operaciones_mvtos`.`tipo_operacion` = 451)
GROUP BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`periodo_mensual`,
	`operaciones_mvtos`.`periodo_anual` 
ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`periodo_anual`,
	`operaciones_mvtos`.`periodo_mensual`)$$

DELIMITER ;

-- -  Vista de Intereses Devengados por Cobrar
DELIMITER $$
DROP VIEW IF EXISTS `interes_devengado_por_cobrar`$$
DROP TABLE IF EXISTS `interes_devengado_por_cobrar`$$

CREATE VIEW `interes_devengado_por_cobrar` AS 
(SELECT
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`periodo_mensual`      AS `periodo`,
	`operaciones_mvtos`.`periodo_anual`        AS `ejercicio`,
	DATE_FORMAT( CONCAT( `operaciones_mvtos`.`periodo_anual`, '-', `operaciones_mvtos`.`periodo_mensual`, '-01'), '%Y%m') AS 'indice',
	SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS `interes` 
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `eacp_config_bases_de_integracion_miembros` 
		`eacp_config_bases_de_integracion_miembros` 
		ON `operaciones_mvtos`.`tipo_operacion` = 
		`eacp_config_bases_de_integracion_miembros`.`miembro` 
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2000) 
GROUP BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`periodo_mensual`,
	`operaciones_mvtos`.`periodo_anual` 
ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`periodo_anual`,
	`operaciones_mvtos`.`periodo_mensual`)$$

DELIMITER ;
-- -  Vistas creditos abonos acumulados
DELIMITER $$

DROP VIEW IF EXISTS `creditos_abonos_acumulados`$$
DROP TABLE IF EXISTS `creditos_abonos_acumulados`$$

CREATE VIEW `creditos_abonos_acumulados` AS (
    SELECT `operaciones_mvtos`.`socio_afectado` AS `socio_afectado`,
    `operaciones_mvtos`.`docto_afectado` AS `docto_afectado`,
    `docto_afectado` AS 'credito',
	MAX(`operaciones_mvtos`.`fecha_afectacion`)      AS `fecha`,
	MAX(`operaciones_mvtos`.`periodo_socio`)         AS `parcialidad`,
	COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `operaciones`,    
    SUM(`operaciones_mvtos`.`afectacion_real`) AS `total_abonado`, 
	SUM(`operaciones_mvtos`.`afectacion_real`) AS `monto` 
    FROM `operaciones_mvtos`
		WHERE
	(`operaciones_mvtos`.`tipo_operacion` = 120) 
	GROUP BY `operaciones_mvtos`.`socio_afectado`,`operaciones_mvtos`.`docto_afectado`
)$$

DELIMITER ;


-- -  Vistas creditos Letras Morosas
DELIMITER $$

DROP VIEW IF EXISTS `creditos_letras_morosas`$$
DROP TABLE IF EXISTS `creditos_letras_morosas`$$
CREATE

    VIEW `creditos_letras_morosas` 
	AS (
    SELECT socio_afectado, 
	docto_afectado,
    docto_afectado AS 'credito', 
	MIN(periodo_socio) AS 'periodo_inicial',
	MAX(periodo_socio) AS 'periodo_final',
	MIN(fecha_afectacion) AS 'fecha_inicial',
	MAX(fecha_afectacion) AS 'fecha_final',
	SUM(afectacion_real) AS 'monto' 
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `operaciones_tipos` `operaciones_tipos` 
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos` 
WHERE
	(`operaciones_tipos`.`integra_parcialidad` ='1')
	AND
	(`operaciones_mvtos`.`fecha_afectacion`<=CURDATE() )
	AND
	(`operaciones_mvtos`.`docto_neutralizador`=1 )
GROUP BY
	socio_afectado, 
	docto_afectado

HAVING monto >0.99

ORDER BY socio_afectado, docto_afectado

)$$

DELIMITER ;


-- -  Vistas Intereses calculados por Creditos ya Pagados


-- -  Vistas Intereses calculados por Creditos


-- Vista de Amotizaciones del SISBANCS.

DELIMITER $$

DROP VIEW IF EXISTS `sisbancs_suma_amorizaciones`$$
DROP TABLE IF EXISTS `sisbancs_suma_amorizaciones`$$



-- - Vistas Temporales ===================================

DELIMITER $$

DROP TABLE IF EXISTS `temp_captacion_por_socio`$$
DROP VIEW IF EXISTS `temp_captacion_por_socio`$$

CREATE VIEW `temp_captacion_por_socio` AS (select `captacion_cuentas`.`numero_socio` AS `numero_socio`,`captacion_cuentas`.`tipo_cuenta` AS `tipo_cuenta`,count(`captacion_cuentas`.`numero_cuenta`) AS `cuentas`,sum(`captacion_cuentas`.`saldo_cuenta`) AS `monto` from `captacion_cuentas` group by `captacion_cuentas`.`numero_socio`,`captacion_cuentas`.`tipo_cuenta`)$$

DELIMITER ;

-- - Uno

DELIMITER $$

DROP VIEW IF EXISTS `temp_sisbancs_depositos`$$
DROP TABLE IF EXISTS `temp_sisbancs_depositos`$$


DELIMITER ;

-- - Dos



-- - Vista de Parcialidades de los Creditos
DELIMITER $$

DROP VIEW IF EXISTS `creditos_parcialidades`$$
DROP TABLE IF EXISTS `creditos_parcialidades`$$

CREATE VIEW `creditos_parcialidades` AS (select sql_cache `operaciones_mvtos`.`socio_afectado` AS `socio`,`operaciones_mvtos`.`docto_afectado` AS `credito`,`operaciones_mvtos`.`periodo_socio` AS `parcialidad`,max(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha_de_pago`,
sum((`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)) AS `monto` 
from (`eacp_config_bases_de_integracion_miembros` join `operaciones_mvtos` on((`eacp_config_bases_de_integracion_miembros`.`miembro` = `operaciones_mvtos`.`tipo_operacion`))) where ((`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2601) 
and (`operaciones_mvtos`.`afectacion_real` > 0)) 
group by `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
`operaciones_mvtos`.`socio_afectado`,`operaciones_mvtos`.`docto_afectado`,
`operaciones_mvtos`.`periodo_socio` order by `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
`operaciones_mvtos`.`socio_afectado`,`operaciones_mvtos`.`docto_afectado`,
`operaciones_mvtos`.`periodo_socio`)$$

DELIMITER ;
-- -- Interes Devengado, tabla Temporal
DELIMITER $$

DROP VIEW IF EXISTS `interes`$$
DROP TABLE IF EXISTS `interes`$$

CREATE VIEW `interes` AS (SELECT `socios`.`codigo`, `socios`.`nombre`, `creditos_solicitud`.`numero_solicitud` AS `solicitud`, 
			`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `convenio`, `creditos_solicitud`.`fecha_ministracion`, 
`creditos_solicitud`.`monto_autorizado` AS `saldo_historico`, `creditos_solicitud`.`pagos_autorizados` AS `pagos`, 
`creditos_solicitud`.`periocidad_de_pago` AS `periocidad`, `creditos_solicitud`.`tipo_autorizacion`, `creditos_solicitud`.`fecha_conciliada` AS `ultima_operacion`,
 `creditos_solicitud`.`saldo_conciliado` AS `saldo_insoluto`, `creditos_solicitud`.`fecha_conciliada`, `creditos_solicitud`.`saldo_conciliado`,
 MAX(`interes_devengado_por_cobrar`.`ejercicio`) AS 'ejercicio', MAX(`interes_devengado_por_cobrar`.`periodo`) AS 'periodo', setNoMenorCero( SUM(`interes_devengado_por_cobrar`.`interes`) ) AS 'interes', `creditos_solicitud`.`interes_normal_devengado`, `creditos_solicitud`.`interes_normal_pagado`, `creditos_solicitud`.`sdo_int_ant` AS 'saldo_ica', setNoMenorCero( (`creditos_solicitud`.`interes_normal_devengado` - (`creditos_solicitud`.`interes_normal_pagado` + `creditos_solicitud`.`sdo_int_ant`) ) ) AS 'saldo_interes' FROM `socios` `socios` INNER JOIN `creditos_solicitud` `creditos_solicitud` ON `socios`.`codigo` = `creditos_solicitud`.`numero_socio` INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`. `idcreditos_tipoconvenio` INNER JOIN `interes_devengado_por_cobrar` `interes_devengado_por_cobrar` ON `creditos_solicitud`.`numero_solicitud` = `interes_devengado_por_cobrar`.`docto_afectado` WHERE (`interes_devengado_por_cobrar`.`indice` <= 200903) AND (`creditos_solicitud`.`saldo_conciliado` >0.99) AND (`creditos_solicitud`.`estatus_actual` != 50) GROUP BY `creditos_solicitud`.`numero_solicitud` ORDER BY `creditos_tipoconvenio`.`tipo_autorizacion` DESC, `creditos_solicitud`.`tipo_convenio`, `socios`.`codigo`, `creditos_solicitud`.`numero_solicitud` )$$

DELIMITER ;


-- - Migracion Otras Tablas
DELIMITER $$

DROP VIEW IF EXISTS `migracion_compac_creditos`$$
DROP TABLE IF EXISTS `migracion_compac_creditos`$$


DELIMITER ;


DELIMITER $$

DROP TABLE IF EXISTS `migracion_creditos_por_socio`$$
DROP VIEW IF EXISTS `migracion_creditos_por_socio`$$

CREATE VIEW `migracion_creditos_por_socio` AS (select `creditos_solicitud`.`numero_socio` AS `numero_socio`,
count(`creditos_solicitud`.`numero_solicitud`) AS `creditos`,sum(`creditos_solicitud`.`saldo_actual`) AS `saldo` 
from `creditos_solicitud` where (`creditos_solicitud`.`estatus_actual` <> 50) AND (`creditos_solicitud`.`saldo_actual` > 0.99)
group by `creditos_solicitud`.`numero_socio`)$$

DELIMITER ;

-- Vista de Compracion de Captacion cuentas A LA VISTA
DELIMITER $$

DROP VIEW IF EXISTS `captacion_saldos_comparados`$$
DROP TABLE IF EXISTS `captacion_saldos_comparados`$$

CREATE VIEW `captacion_saldos_comparados` AS (select `captacion_cuentas`.`numero_cuenta` AS `numero_cuenta`,
		`captacion_cuentas`.`numero_socio` AS `numero_socio`,`captacion_cuentas`.`tipo_cuenta` AS `tipo_cuenta`,
		`captacion_cuentas`.`saldo_cuenta` AS `saldo_cuenta`, 
		ROUND( SUM((`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) ), 2) AS `saldo_obtenido`,
		max(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha` from 
	((`operaciones_mvtos` join `eacp_config_bases_de_integracion_miembros` 
	on ((`operaciones_mvtos`.`tipo_operacion` = `eacp_config_bases_de_integracion_miembros`.`miembro`))) 
	join `captacion_cuentas` on((`operaciones_mvtos`.`docto_afectado` = `captacion_cuentas`.`numero_cuenta`))) where 
	((`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 3100) and (`captacion_cuentas`.`tipo_cuenta` = 10)) 
	group by `captacion_cuentas`.`numero_cuenta` order by `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`)$$

DELIMITER ;



-- Vista de creditos acumulados para comparacion

DELIMITER $$


DROP VIEW IF EXISTS `temp_sisbancs_creditos`$$
DROP TABLE IF EXISTS `temp_sisbancs_creditos`$$



DELIMITER ;

-- Vista de Pagos Acumulados por creditos.- migracion
DELIMITER $$


DROP VIEW IF EXISTS `migracion_tcb_prestamos_mvtos`$$
DROP TABLE IF EXISTS `migracion_tcb_prestamos_mvtos`$$



DELIMITER ;

-- 
-- Vista de Acumulacion de SDPM por Creditos
-- @date 2010-11-21
-- @nivel critico
-- @since v1.9.42 Rev 20

DELIMITER $$


DROP VIEW IF EXISTS `creditos_sdpm_acumulado`$$
DROP TABLE IF EXISTS `creditos_sdpm_acumulado`$$

CREATE  VIEW `creditos_sdpm_acumulado` AS (
SELECT
  `creditos_sdpm_historico`.`numero_de_socio`   AS `socio`,
  `creditos_sdpm_historico`.`numero_de_credito` AS `credito`,
  MAX(`creditos_sdpm_historico`.`fecha_actual`) AS `fechaActual`,
  MAX(`creditos_sdpm_historico`.`fecha_anterior`) AS `fechaAnterior`,
  SUM(`creditos_sdpm_historico`.`dias_transcurridos`) AS `dias`,
  SUM(`creditos_sdpm_historico`.`monto_calculado`) AS `monto`,
  AVG(`creditos_sdpm_historico`.`saldo`)        AS `saldo`,
  SUM(`creditos_sdpm_historico`.`interes_normal`) AS `interesesNormales`,
  SUM(`creditos_sdpm_historico`.`interes_moratorio`) AS `InteresesMoratorios`
FROM `creditos_sdpm_historico`
GROUP BY `creditos_sdpm_historico`.`numero_de_credito`)$$

DELIMITER ;

-- END
--
-- Vista de mvtos a SDPM de creditos
-- @date 2011-09-21
-- @nivel critico
-- @since v1.9.42 rev 38
-- @since v2014.06.05 2014-07-02
--
DELIMITER $$

DROP VIEW IF EXISTS `creditos_mvtos_asdpm`$$
DROP TABLE IF EXISTS `creditos_mvtos_asdpm`$$
 
CREATE VIEW `creditos_mvtos_asdpm` AS (
SELECT SQL_CACHE
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_mvtos`.`socio_afectado`                         AS `socio`,
  `operaciones_mvtos`.`docto_afectado`                         AS `documento`,
  `operaciones_mvtos`.`recibo_afectado`                        AS `recibo`,
  `operaciones_mvtos`.`fecha_afectacion`                       AS `fecha`,
  `operaciones_mvtos`.`tipo_operacion`                         AS `operacion`,
  `operaciones_mvtos`.`afectacion_real`                        AS `monto`,
   `eacp_config_bases_de_integracion_miembros`.`afectacion` AS `afectacion`
FROM
 
        `operaciones_mvtos` `operaciones_mvtos`
                INNER JOIN `eacp_config_bases_de_integracion_miembros`
                `eacp_config_bases_de_integracion_miembros`
                ON `operaciones_mvtos`.`tipo_operacion` =
                `eacp_config_bases_de_integracion_miembros`.`miembro`
                        INNER JOIN `creditos_solicitud` `creditos_solicitud`
                        ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
                        `numero_solicitud`
 
WHERE ((`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2610)
                AND (`creditos_solicitud`.`periocidad_de_pago` =360)
                AND     (`creditos_solicitud`.`numero_solicitud` >1)
       )
ORDER BY `operaciones_mvtos`.`docto_afectado`,`operaciones_mvtos`.`fecha_afectacion`)$$

DELIMITER ;
-- END



-- SDPM de Planes de Pago
--
DELIMITER $$

DROP VIEW IF EXISTS `creditos_mvtos_asdpm_planes`$$
DROP TABLE IF EXISTS `creditos_mvtos_asdpm_planes`$$
 
CREATE VIEW `creditos_mvtos_asdpm_planes` AS (
SELECT SQL_CACHE
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_mvtos`.`socio_afectado`                         AS `socio`,
  `operaciones_mvtos`.`docto_afectado`                         AS `documento`,
  `operaciones_mvtos`.`recibo_afectado`                        AS `recibo`,
  `operaciones_mvtos`.`fecha_afectacion`                       AS `fecha`,
  `operaciones_mvtos`.`tipo_operacion`                         AS `operacion`,
  `operaciones_mvtos`.`afectacion_real`                        AS `monto`,
   `eacp_config_bases_de_integracion_miembros`.`afectacion` AS `afectacion`,
  `operaciones_mvtos`.`periodo_socio`                        AS `periodo`
FROM
 
        `operaciones_mvtos` `operaciones_mvtos`
                INNER JOIN `eacp_config_bases_de_integracion_miembros`
                `eacp_config_bases_de_integracion_miembros`
                ON `operaciones_mvtos`.`tipo_operacion` =
                `eacp_config_bases_de_integracion_miembros`.`miembro`
                        INNER JOIN `creditos_solicitud` `creditos_solicitud`
                        ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
                        `numero_solicitud`
 
WHERE ((`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2620)
                AND (`creditos_solicitud`.`periocidad_de_pago` != 360)
                AND     (`creditos_solicitud`.`numero_solicitud` >1)
       )
ORDER BY `operaciones_mvtos`.`docto_afectado`,`operaciones_mvtos`.`fecha_afectacion`)$$

DELIMITER ;
-- END



-- - Creditos proximas parcialidades
-- - Date: 2011-oct-06
-- - Date: 2012-01-16 : Se agrego parcialidades con saldo, para determinar cuantas faltan, independientemente de su proxima parcialidad

DELIMITER $$


DROP VIEW IF EXISTS `creditos_proximas_parcialidades`$$
DROP TABLE IF EXISTS `creditos_proximas_parcialidades`$$

CREATE VIEW `creditos_proximas_parcialidades` AS (
SELECT SQL_CACHE
  `operaciones_mvtos`.`socio_afectado`    AS `socio`,
  `creditos_solicitud`.`numero_solicitud` AS `credito`,
  `operaciones_mvtos`.`tipo_operacion`    AS `tipo_operacion`,
  MIN(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha_de_pago`,
  MIN(`operaciones_mvtos`.`periodo_socio`) AS `parcialidad`,
  SUM(`operaciones_mvtos`.`afectacion_real`) AS `capital_pendiente`,
  COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `parcialidades_con_saldo`,
  MIN(`operaciones_mvtos`.`saldo_actual`) AS `saldo_actual`,
  MIN(`operaciones_mvtos`.`saldo_anterior`) AS `saldo_anterior`
FROM (`operaciones_mvtos`
   JOIN `creditos_solicitud`
     ON ((`operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.`numero_solicitud`)))
WHERE ((`operaciones_mvtos`.`tipo_operacion` = 410)
       AND (`operaciones_mvtos`.`saldo_actual` < `creditos_solicitud`.`saldo_actual`)
       AND (`operaciones_mvtos`.`afectacion_real` > 0.99)
       AND (`operaciones_mvtos`.`periodo_socio` <> 1))
GROUP BY `operaciones_mvtos`.`docto_afectado`)$$

DELIMITER ;

-- - Vista de Folios de Polizas
DELIMITER $$

DROP VIEW IF EXISTS `general_folios_poliza`$$
DROP TABLE IF EXISTS `general_folios_poliza`$$

CREATE  VIEW `general_folios_poliza` AS (
SELECT
  `contable_polizas`.`ejercicio`  AS `ejercicio`,
  `contable_polizas`.`periodo`    AS `periodo`,
  `contable_polizas`.`tipopoliza` AS `tipo`,
  MAX(`contable_polizas`.`numeropoliza`) AS `numero`
FROM `contable_polizas`
GROUP BY `contable_polizas`.`ejercicio`,`contable_polizas`.`periodo`,`contable_polizas`.`tipopoliza`
ORDER BY `contable_polizas`.`ejercicio` DESC,`contable_polizas`.`periodo` DESC,`contable_polizas`.`tipopoliza` DESC)$$

DELIMITER ;
-- Vista recibos Pagados
-- 20Marzo2011
DELIMITER $$

DROP TABLE IF EXISTS `tesoreria_recibos_pagados`$$
DROP VIEW IF EXISTS `tesoreria_recibos_pagados`$$

CREATE VIEW `tesoreria_recibos_pagados` AS (
SELECT
  `tesoreria_cajas_movimientos`.`recibo` AS `recibo`,
  SUM(`tesoreria_cajas_movimientos`.`monto_del_movimiento`) AS `pagado`
FROM `tesoreria_cajas_movimientos` GROUP BY `tesoreria_cajas_movimientos`.`recibo`)$$

DELIMITER ;
--

-- - Datos contables: 10-mayo-2012
-- - Vista de datos contables por destino
DELIMITER $$


DROP TABLE IF EXISTS `creditos_datos_contables_por_destino`$$
DROP VIEW IF EXISTS `creditos_datos_contables_por_destino`$$

CREATE VIEW `creditos_datos_contables_por_destino` AS (
SELECT
  `creditos_solicitud`.`numero_solicitud`              AS `numero_solicitud`,
  `creditos_solicitud`.`numero_socio`                  AS `numero_socio`,
  `creditos_destinos`.`capital_vigente_normal`         AS `capital_vigente_normal`,
  `creditos_destinos`.`capital_vigente_reestructurado` AS `capital_vigente_reestructurado`,
  `creditos_destinos`.`capital_vigente_renovado`       AS `capital_vigente_renovado`,
  `creditos_destinos`.`capital_vencido_normal`         AS `capital_vencido_normal`,
  `creditos_destinos`.`capital_vencido_reestructurado` AS `capital_vencido_reestructurado`,
  `creditos_destinos`.`capital_vencido_renovado`       AS `capital_vencido_renovado`,
  `creditos_destinos`.`interes_vigente_normal`         AS `interes_vigente_normal`,
  `creditos_destinos`.`interes_vigente_reestructurado` AS `interes_vigente_reestructurado`,
  `creditos_destinos`.`interes_vigente_renovado`       AS `interes_vigente_renovado`,
  `creditos_destinos`.`interes_vencido_normal`         AS `interes_vencido_normal`,
  `creditos_destinos`.`interes_vencido_reestructurado` AS `interes_vencido_reestructurado`,
  `creditos_destinos`.`interes_vencido_renovado`       AS `interes_vencido_renovado`,
  `creditos_destinos`.`interes_cobrado`                AS `interes_cobrado`,
  `creditos_destinos`.`moratorio_cobrado`              AS `moratorio_cobrado`
FROM (`creditos_solicitud`
   JOIN `creditos_destinos`
     ON ((`creditos_solicitud`.`destino_credito` = `creditos_destinos`.`idcreditos_destinos`))))$$

DELIMITER ;

-- --------------------------------------------------

DELIMITER $$



DROP VIEW IF EXISTS `letras`$$
DROP TABLE IF EXISTS `letras`$$

CREATE  VIEW `letras` AS (
SELECT
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_mvtos`.`socio_afectado`                         AS `socio_afectado`,
  `operaciones_mvtos`.`docto_afectado`                         AS `docto_afectado`,
  `operaciones_mvtos`.`periodo_socio`                          AS `periodo_socio`,
  MIN(`operaciones_mvtos`.`fecha_afectacion`)                  AS `fecha_de_pago`,
MAX(`operaciones_mvtos`.`fecha_vcto`)                  AS `fecha_de_vencimiento`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 410) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `capital`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 411) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `interes`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 413) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `iva`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 412) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `ahorro`,

SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` < 410 OR `operaciones_mvtos`.`tipo_operacion` > 413) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `otros`,

  SUM((`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)) AS `letra`
FROM (`operaciones_mvtos`
   JOIN `eacp_config_bases_de_integracion_miembros`
     ON ((`operaciones_mvtos`.`tipo_operacion` = `eacp_config_bases_de_integracion_miembros`.`miembro`)))
WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 1001)
AND `operaciones_mvtos`.`tipo_operacion` != 420 
GROUP BY `operaciones_mvtos`.`docto_afectado`,`operaciones_mvtos`.`periodo_socio`
ORDER BY
`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
`operaciones_mvtos`.`docto_afectado`, `operaciones_mvtos`.`periodo_socio`
)$$

DELIMITER ;

-- 08-octubre-2013

DELIMITER $$


DROP VIEW IF EXISTS `primeras_letras`$$
DROP TABLE IF EXISTS `primeras_letras`$$

CREATE VIEW `primeras_letras` AS (
SELECT
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_mvtos`.`socio_afectado`                         AS `socio_afectado`,
  `operaciones_mvtos`.`docto_afectado`                         AS `docto_afectado`,
  MIN(`operaciones_mvtos`.`periodo_socio`)                          AS `periodo_socio`,
  MIN(`operaciones_mvtos`.`fecha_afectacion`)                          AS `fecha_de_pago`,
  SUM((`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)) AS `letra`
FROM (`operaciones_mvtos`
   JOIN `eacp_config_bases_de_integracion_miembros`
     ON ((`operaciones_mvtos`.`tipo_operacion` = `eacp_config_bases_de_integracion_miembros`.`miembro`)))
WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 1001) 
GROUP BY `operaciones_mvtos`.`docto_afectado`)$$

DELIMITER ;

--
-- 07-julio-2014

DELIMITER $$

DROP VIEW IF EXISTS `letras_pendientes`$$
DROP TABLE IF EXISTS `letras_pendientes`$$
DROP VIEW IF EXISTS `creditos_letras_pendientes_rt`$$
DROP TABLE IF EXISTS `creditos_letras_pendientes_rt`$$

CREATE  VIEW `creditos_letras_pendientes_rt` AS (
SELECT
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_mvtos`.`socio_afectado`                         AS `socio_afectado`,
  `operaciones_mvtos`.`docto_afectado`                         AS `docto_afectado`,
  `operaciones_mvtos`.`periodo_socio`                          AS `periodo_socio`,

MIN(`operaciones_mvtos`.`fecha_afectacion`)                  AS `fecha_de_pago`,
MAX(`operaciones_mvtos`.`fecha_vcto`)                  AS `fecha_de_vencimiento`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 410) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `capital`,

SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 411) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `interes`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 413) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `iva`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 412) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `ahorro`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` < 410 OR `operaciones_mvtos`.`tipo_operacion` > 413) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `otros`,

SUM((`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)) AS `letra`,


SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 410  AND `operaciones_mvtos`.`fecha_afectacion` < getFechaDeCorte()) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `capital_exigible`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 411 AND `operaciones_mvtos`.`fecha_afectacion` < getFechaDeCorte() ) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `interes_exigible`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 413  AND `operaciones_mvtos`.`fecha_afectacion` < getFechaDeCorte()) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `iva_exigible`,
SUM((CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 412  AND `operaciones_mvtos`.`fecha_afectacion` < getFechaDeCorte()) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `ahorro_exigible`,
SUM((CASE WHEN ((`operaciones_mvtos`.`tipo_operacion` < 410 OR `operaciones_mvtos`.`tipo_operacion` > 413)  AND `operaciones_mvtos`.`fecha_afectacion` < getFechaDeCorte() ) THEN (`operaciones_mvtos`.`afectacion_real`) ELSE 0 END)) AS `otros_exigible`,

SUM(
CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 410  AND `operaciones_mvtos`.`fecha_afectacion` < getFechaDeCorte()) THEN

(`operaciones_mvtos`.`afectacion_real`
* DATEDIFF(getFechaDeCorte(), `operaciones_mvtos`.`fecha_afectacion`)
* (`creditos_solicitud`.`tasa_moratorio` + `creditos_solicitud`.`tasa_interes`) ) / getDivisorDeInteres()
ELSE 
0
END

) AS `interes_moratorio`,

	`creditos_solicitud`.`monto_solicitado` AS `monto_original`,
	`creditos_solicitud`.`saldo_actual`     AS `saldo_principal` 
	
FROM 

	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `eacp_config_bases_de_integracion_miembros` 
			`eacp_config_bases_de_integracion_miembros` 
			ON `operaciones_mvtos`.`tipo_operacion` = 
			`eacp_config_bases_de_integracion_miembros`.`miembro`
			
     
WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 1001)
AND `operaciones_mvtos`.`tipo_operacion` != 420 
AND `creditos_solicitud`.`saldo_actual`  > 0

GROUP BY `operaciones_mvtos`.`docto_afectado`)$$

DELIMITER ;

-- 14Enero2014
DELIMITER $$

DROP VIEW IF EXISTS `dias_en_mora`$$
DROP TABLE IF EXISTS `dias_en_mora`$$

CREATE VIEW `dias_en_mora` AS (
SELECT
  `creditos_solicitud`.`numero_solicitud` AS `numero_solicitud`,
	`creditos_solicitud`.`saldo_actual`  AS `saldo`,
  `setNoMenorCero`(
(TO_DAYS(CURDATE()) - TO_DAYS(`creditos_solicitud`.`fecha_vencimiento_dinamico`)))  AS `dias_vencidos`,
  `setNoMenorCero`(
(TO_DAYS(CURDATE()) - TO_DAYS(`creditos_solicitud`.`fecha_mora`)))  AS `dias_morosos`
FROM `creditos_solicitud`)$$

DELIMITER ;



-- 28-febrero operaciones para AML




DELIMITER $$

DROP VIEW IF EXISTS `aml_perfil_ingresos_por_persona`$$
DROP TABLE IF EXISTS `aml_perfil_ingresos_por_persona`$$

CREATE VIEW `aml_perfil_ingresos_por_persona` AS (
SELECT
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_recibos`.`numero_socio`                         AS `socio_afectado`,
  DATE_FORMAT(`operaciones_recibos`.`fecha_operacion`,'%Y%m')  AS `periodo`,
  `operaciones_recibos`.`clave_de_moneda`                      AS `moneda`,
  `operaciones_recibos`.`tipo_pago`                      AS `tipo`,
  COUNT(`operaciones_recibos`.`idoperaciones_recibos`)         AS `operaciones`,
  SUM(`operaciones_recibos`.`unidades_en_moneda`)              AS `original`,
  ROUND(SUM(
	IF(UPPER(`operaciones_recibos`.`clave_de_moneda`) != getMonedaLocal(), getEquivalenciaDeMonedas(`operaciones_recibos`.`unidades_en_moneda`, `operaciones_recibos`.`clave_de_moneda`),
	`operaciones_recibos`.`total_operacion`)
	),2)                    AS `monto`,

  IF (`personas_perfil_transaccional_tipos`.`idpersonas_perfil_transaccional_tipos` IS NULL, LCASE(`operaciones_recibos`.`tipo_pago`), LCASE(`personas_perfil_transaccional_tipos`.`tipo_de_exhibicion` )) AS 'perfil',
	`operaciones_recibos`.`idoperaciones_recibos`                      AS `recibo`
FROM 

	`eacp_config_bases_de_integracion_miembros` 
	`eacp_config_bases_de_integracion_miembros` 
		INNER JOIN `operaciones_recibos` `operaciones_recibos` 
		ON `eacp_config_bases_de_integracion_miembros`.`miembro` = 
		`operaciones_recibos`.`tipo_docto` 
			LEFT OUTER JOIN `personas_perfil_transaccional_tipos` 
			`personas_perfil_transaccional_tipos` 
			ON `operaciones_recibos`.`origen_aml` = 
			`personas_perfil_transaccional_tipos`.
			`idpersonas_perfil_transaccional_tipos`
     
     
WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 30110)
GROUP BY `operaciones_recibos`.`numero_socio`,`operaciones_recibos`.`fecha_operacion`, `operaciones_recibos`.`tipo_pago`,`operaciones_recibos`.`clave_de_moneda`
ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
)$$

DELIMITER ;

-- 04 de marzo

DELIMITER $$
DROP VIEW IF EXISTS `personas_credito_maximo`$$
DROP TABLE IF EXISTS `personas_credito_maximo`$$

CREATE VIEW `personas_credito_maximo` AS (
SELECT
  `creditos_solicitud`.`numero_socio` AS `persona`,
  MAX(`creditos_solicitud`.`monto_autorizado`) AS `credito_maximo`
FROM `creditos_solicitud`
GROUP BY `creditos_solicitud`.`numero_socio`)$$

DELIMITER ;

-- 11-Marzo-2014

DELIMITER $$
DROP VIEW  IF EXISTS `creditos_recibos_pago_acumulados`$$
DELIMITER ;

DELIMITER $$
DROP VIEW  IF EXISTS `creditos_recibos_pago_emitidos`$$
DELIMITER ;



DELIMITER $$
DROP VIEW IF EXISTS `creditos_abonos_parciales`$$
DROP TABLE IF EXISTS `creditos_abonos_parciales`$$

CREATE VIEW `creditos_abonos_parciales` AS (

SELECT
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_mvtos`.`socio_afectado`                         AS `socio_afectado`,
  `operaciones_mvtos`.`docto_afectado`                         AS `docto_afectado`,
  `operaciones_mvtos`.`periodo_socio`                          AS `periodo_socio`,
  MAX(`operaciones_mvtos`.`fecha_afectacion`)                  AS `fecha_de_pago`,
  MAX(`operaciones_mvtos`.`fecha_vcto`)                        AS `fecha_de_vencimiento`,
  SUM((CASE WHEN (`subclasificacion` = 120) THEN (`operaciones_mvtos`.`afectacion_real` * `afectacion`) ELSE 0 END)) AS `capital`,
  SUM((CASE WHEN (`subclasificacion` = 140) THEN (`operaciones_mvtos`.`afectacion_real` * `afectacion`) ELSE 0 END)) AS `interes_normal`,
SUM((CASE WHEN (`subclasificacion` = 141) THEN (`operaciones_mvtos`.`afectacion_real` * `afectacion`) ELSE 0 END)) AS `interes_moratorio`,
SUM((CASE WHEN (`subclasificacion` = 0) THEN (`operaciones_mvtos`.`afectacion_real` * `afectacion`) ELSE 0 END)) AS `otros`,
SUM((CASE WHEN (`subclasificacion` = 151) THEN (`operaciones_mvtos`.`afectacion_real` * `afectacion`) ELSE 0 END)) AS `impuesto`,

SUM(`operaciones_mvtos`.`afectacion_real` * `afectacion`) AS `total`

FROM (`operaciones_mvtos`
   JOIN `eacp_config_bases_de_integracion_miembros`
     ON ((`operaciones_mvtos`.`tipo_operacion` = `eacp_config_bases_de_integracion_miembros`.`miembro`)))
WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 15000)
GROUP BY `operaciones_mvtos`.`docto_afectado`,`operaciones_mvtos`.`periodo_socio`

	ORDER BY
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
		`operaciones_mvtos`.`docto_afectado`,
		`operaciones_mvtos`.`periodo_socio`
)
$$

DELIMITER ;


-- Saldos Mensuales


DELIMITER $$
DROP VIEW IF EXISTS `creditos_saldo_mensuales`$$
DROP TABLE IF EXISTS `creditos_saldo_mensuales`$$

CREATE VIEW `creditos_saldo_mensuales` AS (
SELECT
`creditos_solicitud`.`numero_solicitud`,

	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`periocidad_de_pago`,
	`creditos_solicitud`.`tipo_convenio` ,

  COUNT(`operaciones_mvtos`.`tipo_operacion`) AS `operaciones`,
  MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha`,
  
IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-01-01') ) ,0, `monto_autorizado`) -

 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-01-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `enero`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-02-01')), 0, `monto_autorizado`)  - 

SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-02-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `febrero`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-03-01')),0, `monto_autorizado`)  - 

SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-03-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `marzo`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-04-01')),0, `monto_autorizado`)  - 

SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-04-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `abril`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-05-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-05-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `mayo`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-06-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-06-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `junio`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-07-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-07-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `julio`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-08-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-08-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `agosto`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-09-01')),0, `monto_autorizado`)  -
 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-09-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `septiembre`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-10-01')),0, `monto_autorizado`)  -
 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-10-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `octubre`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-11-01')),0, `monto_autorizado`)  -
 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-11-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `noviembre`,

IF(`fecha_ministracion` > LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-12-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-12-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `diciembre`

FROM 
	`operaciones_mvtos` `operaciones_mvtos` 
		RIGHT OUTER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud`

WHERE ((`operaciones_mvtos`.`tipo_operacion` = 120)
        OR (`operaciones_mvtos`.`tipo_operacion` = 110))
		AND `fecha_ministracion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-12-01'))	
GROUP BY `creditos_solicitud`.`numero_solicitud`)$$

DELIMITER ;


-- - creditos por ejercicio

DELIMITER $$

DROP VIEW IF EXISTS `creditos_saldos_por_ejercicio`$$
DROP TABLE IF EXISTS `creditos_saldos_por_ejercicio`$$

CREATE VIEW `creditos_saldos_por_ejercicio` AS (
SELECT
`creditos_solicitud`.`numero_solicitud`,

	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`periocidad_de_pago`,
	`creditos_solicitud`.`tipo_convenio` ,

  COUNT(`operaciones_mvtos`.`tipo_operacion`) AS `operaciones`,
  MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha`,
  
IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-01-01') ) ,0, `monto_autorizado`) -

 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-01-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `enero`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-02-01')), 0, `monto_autorizado`)  - 

SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-02-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `febrero`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-03-01')),0, `monto_autorizado`)  - 

SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-03-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `marzo`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-04-01')),0, `monto_autorizado`)  - 

SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-04-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `abril`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-05-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-05-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `mayo`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-06-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-06-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `junio`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-07-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-07-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `julio`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-08-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-08-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `agosto`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-09-01')),0, `monto_autorizado`)  -
 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-09-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `septiembre`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-10-01')),0, `monto_autorizado`)  -
 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-10-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `octubre`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-11-01')),0, `monto_autorizado`)  -
 SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-11-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `noviembre`,

IF(`fecha_ministracion` >= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-12-01')),0, `monto_autorizado`)  - 
SUM(IF(((`operaciones_mvtos`.`tipo_operacion` = 120) AND (`operaciones_mvtos`.`fecha_afectacion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-12-01')))),`operaciones_mvtos`.`afectacion_real`,0)) AS `diciembre`

FROM 
	`operaciones_mvtos` `operaciones_mvtos` 
		RIGHT OUTER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud`

WHERE ((`operaciones_mvtos`.`tipo_operacion` = 120)
        OR (`operaciones_mvtos`.`tipo_operacion` = 110))
		AND `fecha_ministracion` <= LAST_DAY(CONCAT(getEjercicioDeTrabajo(),'-12-01'))	
GROUP BY `creditos_solicitud`.`numero_solicitud`)$$

--  creditos Saldos a una fecha determinada



-- 28-junio Modificaciones
-- 20-Agosto Agregar IF
-- 24-septiembre Agrega recibo
-- 14-enero-2016 Agregar la version RT
DELIMITER $$

DROP VIEW IF EXISTS `aml_perfil_egresos_por_persona_rt`$$
DROP TABLE IF EXISTS `aml_perfil_egresos_por_persona_rt`$$

CREATE VIEW `aml_perfil_egresos_por_persona_rt` AS (
SELECT SQL_CACHE
  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
  `operaciones_recibos`.`numero_socio`                         AS `socio_afectado`,
  DATE_FORMAT(`operaciones_recibos`.`fecha_operacion`,'%Y%m')  AS `periodo`,
  `operaciones_recibos`.`clave_de_moneda`                      AS `moneda`,
  `operaciones_recibos`.`tipo_pago`                      AS `tipo`,
  COUNT(`operaciones_recibos`.`idoperaciones_recibos`)         AS `operaciones`,
  SUM(`operaciones_recibos`.`unidades_en_moneda`)              AS `original`,
  ROUND(SUM(
	IF(UPPER(`operaciones_recibos`.`clave_de_moneda`) != getMonedaLocal(), getEquivalenciaDeMonedas(`operaciones_recibos`.`unidades_en_moneda`, `operaciones_recibos`.`clave_de_moneda`),
	`operaciones_recibos`.`total_operacion`)
	),2)                 AS `monto`,

  IF (`personas_perfil_transaccional_tipos`.`idpersonas_perfil_transaccional_tipos` IS NULL, LCASE(`operaciones_recibos`.`tipo_pago`), LCASE(`personas_perfil_transaccional_tipos`.`tipo_de_exhibicion` )) AS 'perfil',
	`operaciones_recibos`.`idoperaciones_recibos`                      AS `recibo`
FROM 

	`eacp_config_bases_de_integracion_miembros` 
	`eacp_config_bases_de_integracion_miembros` 
		INNER JOIN `operaciones_recibos` `operaciones_recibos` 
		ON `eacp_config_bases_de_integracion_miembros`.`miembro` = 
		`operaciones_recibos`.`tipo_docto` 
			LEFT OUTER JOIN `personas_perfil_transaccional_tipos` 
			`personas_perfil_transaccional_tipos` 
			ON `operaciones_recibos`.`origen_aml` = 
			`personas_perfil_transaccional_tipos`.
			`idpersonas_perfil_transaccional_tipos`
     
     
WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 30210)
GROUP BY `operaciones_recibos`.`numero_socio`,
`operaciones_recibos`.`fecha_operacion`,

`operaciones_recibos`.`origen_aml`,


`operaciones_recibos`.`clave_de_moneda`
ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`

)$$


DELIMITER ;

-- Personas Vigiladas

DELIMITER $$
SET @limite_personas_vigiladas:=NULL$$
DROP VIEW IF EXISTS `aml_personas_vigiladas` $$
DROP VIEW IF EXISTS `aml_personas_no_vigiladas` $$
CREATE

    VIEW `aml_personas_vigiladas` 
    AS
(SELECT
	`socios_general`.`codigo`                    AS `persona`,
	`socios_figura_juridica`.`tipo_de_integracion`,
	`socios_general`.`regimen_fiscal`,
	SUM(`creditos_solicitud`.`monto_autorizado`) AS `obligaciones_contratadas`,
	SUM(`creditos_solicitud`.`saldo_actual`)     AS `obligaciones_Activas` 
FROM
	`creditos_solicitud` `creditos_solicitud` 
		RIGHT OUTER JOIN `socios_general` `socios_general` 
		ON `creditos_solicitud`.`numero_socio` = `socios_general`.`codigo` 
			INNER JOIN `socios_figura_juridica` `socios_figura_juridica` 
			ON `socios_general`.`personalidad_juridica` = 
			`socios_figura_juridica`.`idsocios_figura_juridica` 
WHERE
	((`socios_figura_juridica`.`tipo_de_integracion` = 3) OR
	(`socios_general`.`regimen_fiscal` = 200) )
	AND
	(`creditos_solicitud`.`saldo_actual` >0) 
GROUP BY
	`socios_general`.`codigo`
HAVING `obligaciones_contratadas` >= getLimitePersonasVigiladas());$$

DELIMITER ;



