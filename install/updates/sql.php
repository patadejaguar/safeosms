<?php
$sql				= array();
$sqlMenu			= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_type`, `menu_order`) VALUES ";
$version			= (isset($_REQUEST["version"])) ? intval($_REQUEST["version"]) : 0;
$out				= (isset($_REQUEST["out"])) ? intval($_REQUEST["out"]) : "json";

$sql["20140601"][]	= "$sqlMenu ('18554', '18550', 'Carga de Catalogo Contable', 'install/contabilidad_importar-catalogo.frm.php', 'command', '18562') ";
$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_rules` = '99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro' WHERE `idgeneral_menu` = '11021' ";
$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_parent` = '15000' , `menu_description` = '99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro' , `menu_order` = '1' WHERE `idgeneral_menu` = '11020' ";
$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_order` = '15050' WHERE `idgeneral_menu` = '15001'";
$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_order` = '2' WHERE `idgeneral_menu` = '15010'";
$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Productos Creditos' , `menu_image` = 'productos' WHERE `idgeneral_menu` = '3034' ";
$sql["20140601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_type`, `menu_order`) VALUES ('11314', '11300', 'Contabilidad.- Catalogo de Naturaleza', 'rptcontables/catalogo-de-naturaleza.rpt.php', '_blank', 'command', '13314')";
$sql["20140601"][]	= "INSERT INTO `t_03f996214fba4a1d05a68b18fece8e71` (`idusuarios`, `f_28fb96d57b21090705cfdf8bc3445d2a`, `f_34023acbff254d34664f94c3e08d836e`, `nombres`, `apellidopaterno`, `apellidomaterno`,
						`puesto`, `f_f2cd801e90b78ef4dc673a4659c1482d`, `periodo_responsable`, `usr_options`, `date_expire`, `codigo_de_persona`)
						VALUES ('100', 'remoteuser', MD5('remoteuserabcdefghijk'), 'USUARIO', 'DE', 'TAREAS REMOTAS', 'Usuarios de tareas remotas', '10', '100', '', '2018-02-13', '1')";
$sql["20140601"][]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'remoteuser' WHERE `nombre_del_parametro` = 'usuario_de_trabajos_automaticos' ";

$sql["20140601"][]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'remoteuserabcdefghijk' WHERE `nombre_del_parametro` = 'contrasenna_de_trabajos_automaticos' ";

$sql["20140601"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `ideacp_config_bases_de_integracion_miembros` = '362'";
$sql["20140601"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `ideacp_config_bases_de_integracion_miembros` = '363'";
$sql["20140601"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `ideacp_config_bases_de_integracion_miembros` = '364'";
$sql["20140601"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `ideacp_config_bases_de_integracion_miembros` = '365'";
$sql["20140601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_type`, `menu_order`) VALUES ('1024', '1020', 'Cuentas Corriente.- Carga Masiva', 'frmcaptacion/vista.carga-masiva.frm.php', 'tiny', 'command', '1024'); ";

$sql["20140601"][]	= "INSERT INTO `socios_tipoingreso` (`idsocios_tipoingreso`, `descripcion_tipoingreso`, `descripcion_detallada`, `parte_social`) VALUES ('101', 'SOCIO COOPERATIVISTA', 'Socio de Cooperativa', '5')";
$sql["20140601"][]	= "INSERT INTO `contable_centrodecostos` (`idcontable_centrodecostos`, `nombre_centrodecostos`) VALUES ('1', 'POR_DEFECTO') ";

$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_destination` = 'tiny' WHERE `idgeneral_menu` = '5050'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'for' WHERE `idsistema_lenguaje` = '526'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'for the' WHERE `idsistema_lenguaje` = '620'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'First' WHERE `idsistema_lenguaje` = '404'";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1003', '10', 'Contrato de Creditos con Aval', '')";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1503', '10', 'Pagare de Creditos con Aval', '') ";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1803', '10', 'Mandato de Credito con ', '') ";

$sql["20140601"][]	= "INSERT INTO `catalogo_creditos_productos_otros_parametros` (`nombre_del_parametro`, `descripcion_del_parametro`, `tipo_de_parametro`) VALUES ('CLAVE_DE_CONTRATO', 'CLAVE DE CONTRATO DENTRO DEL SISTEMA', 'OPCIONAL')";
$sql["20140601"][]	= "INSERT INTO `catalogo_creditos_productos_otros_parametros` (`nombre_del_parametro`, `descripcion_del_parametro`, `tipo_de_parametro`) VALUES ('CLAVE_DE_MANDATO', 'CLAVE DE MANDATO DENTRO DEL SISTEMA', 'OPCIONAL')";
$sql["20140601"][]	= "INSERT INTO `catalogo_creditos_productos_otros_parametros` (`nombre_del_parametro`, `descripcion_del_parametro`, `tipo_de_parametro`) VALUES ('CLAVE_DE_PAGARE', 'CLAVE DE PAGARE EN EL SISTEMA', 'OPCIONAL')";
$sql["20140601"][]	= "ALTER TABLE `creditos_productos_otros_parametros` CHANGE COLUMN `fecha_de_alta` `fecha_de_alta` DATE NULL DEFAULT '2014-01-01' ,	CHANGE COLUMN `fecha_de_expiracion` `fecha_de_expiracion` DATE NULL DEFAULT '2029-01-01'";

$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8001', '10', 'Ficha de Avales', '<table>\r\n  <tr>\r\n   <td>NOMBRE</td>\r\n   <td>aval_nombre_completo</td>\r\n   <td>LOCALIDAD :</td>\r\n   <td>aval_domicilio_localidad</td>\r\n  </tr>\r\n  <tr>\r\n   <td>DIRECCION  :</td>\r\n   <td>aval_direccion_calle_y_numero</td>\r\n   <td>ESTADO :</td>\r\n   <td>aval_direccion_estado</td>\r\n  </tr>\r\n  <tr>\r\n   <td>OCUPACION :</td>\r\n   <td>aval_ocupacion</td>\r\n   <td>FECHA DE NACIMIENTO :</td>\r\n   <td>aval_fecha_de_nacimiento</td>\r\n  </tr>\r\n  <tr>\r\n   <td >RFC :</td>\r\n   <td>aval_id_fiscal</td>\r\n   <td >LUGAR DE NACIMIENTO :</td>\r\n   <td>aval_lugar_de_nacimiento</td>\r\n  </tr>\r\n  <tr>\r\n   <td>EMPRESA:</td>\r\n   <td>aval_empresa_de_trabajo</td>\r\n   <td>ESTADO CIVIL:</td>\r\n   <td>aval_estado_civil</td>\r\n  </tr>\r\n  <tr>\r\n   <td>TIPO DE RELACION :</td>\r\n   <td>aval_tipo_de_relacion</td>\r\n   <td>PARENTESCO :</td>\r\n   <td>aval_tipo_de_parentesco</td>\r\n  </tr>\r\n  <tr>\r\n   <td>PORCENTAJE AVALADO :</td>\r\n   <td>aval_porcentaje_relacionado</td>\r\n   <td>&nbsp;</td>\r\n   <td>&nbsp;</td>\r\n  </tr>\r\n</table>')";
$sql["20140601"][]	= "ALTER TABLE `socios_relaciones` DROP PRIMARY KEY, ADD PRIMARY KEY (`idsocios_relaciones`, `socio_relacionado`, `numero_socio`, `credito_relacionado`, `codigo`, `tipo_relacion`)";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8002', '10', 'Formato de Firma de Avales', '<table class=\'firma\'>\r\n<tr>\r\n <td>POR SU PROPIO Y PERSONAL DERECHO. EN SU CALIDAD DE DEUDOR SOLIDARIO.</td>\r\n<tr>\r\n</tr>\r\n <td>\r\n  <br />\r\n  <br />\r\n  ________________________\r\n </td>\r\n<tr>\r\n</tr>\r\n <td>aval_nombre_completo</td>\r\n</tr>\r\n</table>\r\n')";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('5001', '10', 'Autorizacion para Central de Riesgos de Avales', '<table>\r\n <tbody>\r\n  <tr>\r\n   <td>&nbsp;\r\n   <h3>AUTORIZACI&Oacute;N DEL CLIENTE PARA REVISAR REPORTE DE CR&Eacute;DITO</h3>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td>\r\n   <p>Por este conducto autorizo expresamente a MAE DEL GOLFO S.A.P.I. de C.V. SOFOM E.N.R., para que durante la vigencia del presente contrato y por conducto de sus funcionarios facultados lleve a cabo investigaciones, sobre mi comportamiento crediticio o el de la Empresa que represento.</p>\r\n\r\n   <p>Asimismo, declaro que conozco la naturaleza y alcance de las sociedades de informaci&oacute;n crediticia y de la informaci&oacute;n contenida en los reportes de cr&eacute;dito y reporte de cr&eacute;dito especial, declaro que conozco la naturaleza y alcance de la informaci&oacute;n que se solicitar&aacute;, del uso que MAE DEL GOLFO S.A.P.I. de C.V SOFOM E.N.R. har&aacute; de tal informaci&oacute;n y de que est&aacute; podr&aacute; realizar consultas peri&oacute;dicas sobre mi historial o el de la empresa que represento, consintiendo que esta autorizaci&oacute;n se encuentre vigente por un per&iacute;odo de 3 a&ntilde;os contados a partir de su expedici&oacute;n y en todo caso durante el tiempo que se mantenga la relaci&oacute;n jur&iacute;dica.</p>\r\n\r\n   <p>En el caso de que la solicitante sea una Persona Moral, declaro bajo protesta de decir verdad Ser Representante Legal de la empresa mencionada en esta autorizaci&oacute;n; manifestando que a la fecha de la firma de la presente autorizaci&oacute;n los poderes no me han sido revocados, limitados, ni modificados en forma alguna.</p>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td>\r\n   <p>EL AVAL SOLIDARIO</p>\r\n   &nbsp;\r\n\r\n   <p>________________________</p>\r\n<p>aval_nombre_completo</p>\r\n   <p>Nombre y Firma/Denominaci&oacute;n o Raz&oacute;n Social</p>\r\n\r\n   <p>Estoy consciente y acepto que este documento quede bajo custodia de MAE DEL GOLFO S.A.P.I. de C.V SOFOM E.N.R y/o Sociedad de Informaci&oacute;n Crediticia ; mismo que se&ntilde;ala que las Sociedades s&oacute;lo podr&aacute;n proporcionar informaci&oacute;n a un usuario, cuando &eacute;ste cuente con la autorizaci&oacute;n expresa del cliente mediante su firma aut&oacute;grafa.</p>\r\n   </td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n\r\n<h4 class=nuevapagina>RECA: 5300-439-016711/01-03151-0614</h4>')";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`) VALUES ('5002', '10', 'Mandato de Descuento de Avales') ";
$sql["20140601"][]	= "ALTER TABLE `general_sucursales` ADD COLUMN `clave_de_persona` BIGINT(20) NULL COMMENT 'numero de persona en la tabla personas' AFTER `fax`";

$sql["20140601"][]	= "UPDATE `general_sucursales` SET `clave_de_persona` = '10000' WHERE `codigo_sucursal` = 'matriz'";

$sql["20140601"][]	= "UPDATE `personas_perfil_transaccional_tipos` SET `afectacion` = '1' WHERE `idpersonas_perfil_transaccional_tipos` = '152'";
$sql["20140601"][]	= "UPDATE `personas_perfil_transaccional_tipos` SET `afectacion` = '-1' WHERE `idpersonas_perfil_transaccional_tipos` = '102'";
$sql["20140601"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`) VALUES ('669', 'Actualizar el Sistema') ";
$sql["20140601"][]	= "ALTER TABLE `operaciones_recibos` ADD COLUMN `origen_aml` INT(4) NULL DEFAULT '0' COMMENT 'indice de origen aml' AFTER `unidades_en_moneda` ";
$sql["20140601"][]	= "ALTER TABLE `operaciones_recibos` CHANGE COLUMN `origen_aml` `origen_aml` INT(4) NOT NULL DEFAULT '0' COMMENT 'indice de origen aml' , DROP PRIMARY KEY, ADD PRIMARY KEY (`idoperaciones_recibos`, `numero_socio`, `docto_afectado`, `tipo_docto`, `origen_aml`) ";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Act of birth / Constituent' WHERE `idsistema_lenguaje` = '445'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Calendar Pay' WHERE `idsistema_lenguaje` = '455'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Phone Calls' WHERE `idsistema_lenguaje` = '465'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Credit Register' WHERE `idsistema_lenguaje` = '457'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'is' WHERE `idsistema_lenguaje` = '523'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Mark with' WHERE `idsistema_lenguaje` = '499'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Loans' WHERE `idsistema_lenguaje` = '507'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'settlement' WHERE `idsistema_lenguaje` = '381'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'City' WHERE `idsistema_lenguaje` = '379'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Name' WHERE `idsistema_lenguaje` = '397'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Internal' WHERE `idsistema_lenguaje` = '390'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Int' WHERE `idsistema_lenguaje` = '377'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'External' WHERE `idsistema_lenguaje` = '376'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Type' WHERE `idsistema_lenguaje` = '362'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Legal' WHERE `idsistema_lenguaje` = '361'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Pay' WHERE `idsistema_lenguaje` = '372'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'weighted' WHERE `idsistema_lenguaje` = '422'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Savings' WHERE `idsistema_lenguaje` = '417'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = '' WHERE `idsistema_lenguaje` = '416'";
$sql["20140601"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Consumer Tax' WHERE `idsistema_lenguaje` = '624'";

$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' WHERE `idgeneral_menu` = '5021'";
$sql["20140601"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `fecha_de_primer_pago` DATE NULL DEFAULT '2014-01-01' AFTER `fuente_de_fondeo` ";
$sql["20140601"][]	= "INSERT INTO `eacp_config_bases_de_integracion` (`codigo_de_base`, `descripcion`, `tipo_de_base`) VALUES ('2620', 'OPERACIONES DE INTERESES DEV EN PLANES DE PAGO', 'de_operaciones')";
$sql["20140601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `afectacion`) VALUES ('510', '2620', '120', 'ABONOS DE CAPITAL', -1)";
$sql["20140601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('511', '2620', '410', '0', 'FECHA DE PAGO')";
$sql["20140601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('512', '2620', '110', 'MINISTRACION')";
$sql["20140601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('513', '2620', '999', '0', 'FIN DE MES')";
$sql["20140601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('514', '2620', '411', '0', 'FECHA DE PAGO')";
$sql["20140601"][]	= "ALTER TABLE `creditos_sdpm_historico` CHANGE COLUMN `dias_transcurridos` `dias_transcurridos` INT(6) NULL DEFAULT '0' , ADD COLUMN `periodo` INT(4) NULL DEFAULT 1 AFTER `sucursal`";

$sql["20140601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('515', '2620', '140', '0', 'PAGOS DE INTERES') ";
$sql["20140601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_type`, `menu_order`) VALUES ('185591', '18550', 'Carga del LOGO', 'install/logo.upload.frm.php', 'tiny', 'command', '18559')";
$sql["20140601"][]	= "ALTER TABLE `socios_aeconomica_dependencias` ADD COLUMN `formato_de_envio` INT(6) NULL DEFAULT 4001 AFTER `producto_preferente`, ADD COLUMN `formato_de_relacion` INT(6) NULL DEFAULT 4501 AFTER `formato_de_envio`";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('4001', '10', 'FORMATO ESTANDAR DE RETENCIONES', '<!-- -->') ";
$sql["20140601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('4501', '10', 'FORMATO ESTANDAR DE RELACION EN RETENCIONES', '<!-- -->') ";
$sql["20140601"][]	= "ALTER TABLE `empresas_operaciones` ADD COLUMN `fecha_de_envio` DATE NULL DEFAULT NULL AFTER `observaciones`, ADD COLUMN `fecha_de_cobro` DATE NULL DEFAULT NULL AFTER `fecha_de_envio`	";
$sql["20140601"][]	= "UPDATE `general_menu` SET `menu_order` = '200' WHERE `idgeneral_menu` = '2061'; ";
$sql["20140601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_type`, `menu_order`) VALUES ('1067', '1060', 'Envios de Cobranza', 'frmcreditos/envios_de_cobranza.frm.php', 'command', '4')";
$sql["20140601"][]	= "ALTER TABLE `empresas_operaciones` DROP COLUMN `fecha_de_envio`, ADD COLUMN `fecha_inicial` DATE NULL DEFAULT NULL AFTER `fecha_de_cobro`, ADD COLUMN `fecha_final` DATE NULL DEFAULT NULL AFTER `fecha_inicial`	";

//$sql["20140601"][]	= "DROP TABLE IF EXISTS `empresas_cobranza`";
$sql["20140601"][]	= "CREATE TABLE IF NOT EXISTS `empresas_cobranza` (  `idempresas_cobranza` INT NOT NULL AUTO_INCREMENT,  `clave_de_nomina` INT NOT NULL,  `clave_de_credito` BIGINT(20) NOT NULL,  `parcialidad` INT NULL,  `monto_enviado` FLOAT(14,2) NULL,  PRIMARY KEY (`idempresas_cobranza`, `clave_de_nomina`, `clave_de_credito`)) ENGINE = InnoDB";
$sql["20140601"][]	= "ALTER TABLE `empresas_cobranza` CHANGE COLUMN `parcialidad` `parcialidad` INT(11) NULL DEFAULT 0 , CHANGE COLUMN `monto_enviado` `monto_enviado` FLOAT(14,2) NULL DEFAULT 0 ,ADD COLUMN `observaciones` VARCHAR(100) NULL AFTER `monto_enviado` ";
$sql["20140601"][]	= "ALTER TABLE `empresas_cobranza` ADD COLUMN `saldo_inicial` FLOAT(14,2) NULL DEFAULT 0 AFTER `observaciones` ";
$sql["20140601"][]	= "ALTER TABLE `empresas_cobranza` ADD COLUMN `estado` INT(2) NULL DEFAULT 1 AFTER `saldo_inicial` ";
$sql["20140601"][]	= "ALTER TABLE `entidad_configuracion` CHANGE COLUMN `nombre_del_parametro` `nombre_del_parametro` VARCHAR(80) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NOT NULL , ADD PRIMARY KEY (`nombre_del_parametro`)";
/*20140752*/
$sql["20140753"][]	= "ALTER TABLE `captacion_tasas` ADD COLUMN `subproducto` INT(4) NULL DEFAULT '0' COMMENT '0 = general' AFTER `dias_menor_a` ";
$sql["20140753"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `cuenta_contable`, `descripcion`, `tipo_operacion`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `codigo_de_valoracion`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `tasa_iva`, `nombre_corto`, `estatus`) VALUES ('147', 'COMISION POR APERTURA DE CREDITO', '\$cuenta = \"NO_CONTABILIZAR\";', 'Monto cobrado en la apertura de Creditos', '147', '1', '1', '0', '0', '1', '', '1', '1', '', '', '1', '0.16', 'COMISION X APERTURA', '1')";

$sql["20140753"][]	= "UPDATE `operaciones_tipos` SET `es_estadistico` = '0' WHERE `idoperaciones_tipos` = '147' ";
$sql["20140753"][]	= "UPDATE `operaciones_tipos` SET `afectacion_en_sdpm` = '0' , `integra_parcialidad` = '0' WHERE `idoperaciones_tipos` = '147' ";
$sql["20140753"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('518', '1000', '147', '0', 'COMISION X SEGUIMIENTO')";
$sql["20140753"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('1082', '1080', 'Cobro de Comisiones', 'frmcaja/cobro_de_comisiones.frm.php', 'command', '1082', '1082')";

$sql["20140753"][]	= "ALTER TABLE `socios_aeconomica` CHANGE COLUMN `localidad_ae` `localidad_ae` VARCHAR(50) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NOT NULL DEFAULT '' , CHANGE COLUMN `municipio_ae` `municipio_ae` VARCHAR(50) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NOT NULL DEFAULT '' , CHANGE COLUMN `estado_ae` `estado_ae` VARCHAR(40) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NOT NULL DEFAULT '' , CHANGE COLUMN `telefono_ae` `telefono_ae` VARCHAR(18) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NOT NULL DEFAULT '' , DROP PRIMARY KEY, ADD PRIMARY KEY (`idsocios_aeconomica`, `socio_aeconomica`, `dependencia_ae`)";

$sql["20140753"][]	= "ALTER TABLE `personas_documentacion` CHANGE COLUMN `archivo_de_documento` `archivo_de_documento` VARCHAR(200) NULL DEFAULT '' COMMENT 'id del archivo , ruta completa del servidor FTP' ";
$sql["20140753"][]	= "ALTER TABLE `personas_documentacion` CHANGE COLUMN `clave_de_persona` `clave_de_persona` BIGINT(20) NOT NULL DEFAULT '0' ,CHANGE COLUMN `tipo_de_documento` `tipo_de_documento` INT(11) NOT NULL DEFAULT '0' , DROP PRIMARY KEY, ADD PRIMARY KEY (`clave_de_control`, `clave_de_persona`, `tipo_de_documento`)";

$sql["20140754"][]	= "ALTER TABLE `contable_catalogorelacion` CHANGE COLUMN `cuentasuperior` `cuentasuperior` BIGINT(25) NOT NULL , CHANGE COLUMN `subcuenta` `subcuenta` BIGINT(25) NOT NULL ";

$sql["20140754"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('18555', '18550', 'Carga de Catalogo ContPaQi', 'install/contabilidad_import-catalogocw.frm.php', 'command', '18555', '18555')";
$sql["20140754"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('185558', '18550', 'Carga de Catalogo Otros', 'install/contabilidad_import-catalogo-otros.frm.php', 'command', '185558', '185558')";

$sql["20140755"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('501', '50', 'ESTADO DE POSICION FINANCIERA', '<!-- Balance General -->') ";
$sql["20140755"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('502', '50', 'ESTADO DE RESULTADOS', '<!-- Estado de Resultados -->\r\n') ";
$sql["20140755"][]	= "INSERT INTO `sistema_programacion_de_avisos` (`idprograma`, `nombre_del_aviso`, `forma_de_creacion`, `programacion`, `destinatarios`, `microformato`, `tipo_de_medios`, `intent_check`) VALUES ('10', 'ALERTA ELIMINAR RECIBO', 'SYS_ALERTA_POR_EVENTO', 'DATA.AL.ELIMINAR.RECIBO', 'CORREO:patadejaguar@gmail.com|', '{mensaje}\r\n\r\n{original}', ',MAIL', '')";

$sql["20140755"][]	= "ALTER TABLE `operaciones_recibos` CHANGE COLUMN `clave_de_moneda` `clave_de_moneda` VARCHAR(6) NULL DEFAULT 'MXN', ADD COLUMN `archivo_fisico` VARCHAR(200) NULL COMMENT 'Archivo fisico del recibo, almacenado en server ftp' AFTER `origen_aml`";

$sql["20140755"][]	= "ALTER TABLE `socios_general` ADD COLUMN `regimen_fiscal` INT(4) NULL DEFAULT 1 COMMENT '1 No Aplica' AFTER `descuento_preferente`";

$sql["20140756"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_type`, `menu_order`) VALUES ('2004', '18000', 'Buscar Recibos', 'utils/frmbuscarrecibos.php', 'tiny', 'Buscar Recibos de Operacion', 'command', '2004')";

$sql["20140756"][]	= "ALTER TABLE `operaciones_recibos` ADD COLUMN `persona_asociada` BIGINT(20) NULL DEFAULT 0 AFTER `archivo_fisico`";
$sql["20140756"][]	= "ALTER TABLE `personas_perfil_transaccional_tipos` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";

$sql["20140757"][]	= "ALTER TABLE  `aml_risk_register` ADD COLUMN `notas_de_checking` VARCHAR(200) NULL AFTER `metadata`";
$sql["20140757"][]	= "ALTER TABLE `general_sucursales` ADD COLUMN `clave_numerica` INT NULL DEFAULT 0 COMMENT 'clave de sucursal segun la cnbv' AFTER `clave_de_persona`";
$sql["20140757"][]	= "CREATE TABLE IF NOT EXISTS `sistema_equivalencias` (  `control` INT NOT NULL AUTO_INCREMENT,  `tabla` VARCHAR(40) NULL,  `original` VARCHAR(25) NULL,  `equivalencia` VARCHAR(25) NULL,  `notas` VARCHAR(40) NULL,  PRIMARY KEY (`control`)) ENGINE = InnoDB";		
$sql["20140757"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_type`, `menu_order`) VALUES ('11315', '11300', 'Catalogo de Tipos de Pago y Cobro', 'rpttesoreria/catalogo-de-formas.rpt.php', '_blank', 'command', '1315')";

$sql["20140757"][]	= "ALTER TABLE `aml_risk_register` ADD COLUMN `tipo_de_operacion` VARCHAR(4) NULL COMMENT 'Operacion segun la CBV' AFTER `notas_de_checking`";

$sql["20140757"][]	= "INSERT INTO `sistema_equivalencias` (`control`, `tabla`, `original`, `equivalencia`, `notas`) VALUES (1,'operaciones_recibostipo','1','08','PLD Otorgamiento de Credito'), (2,'operaciones_recibostipo','2','09','PLD pago de Credito'), (3,'operaciones_recibostipo','3','01','PLD Depositos'), (4,'operaciones_recibostipo','4','02','PLD Retiros'), (5,'operaciones_recibostipo','5','01','PLD Depositos'), (6,'operaciones_recibostipo','7','01','PLD Depositos'), (8,'operaciones_recibostipo','8','02','PLD Retiros'), (9,'operaciones_recibostipo','98','02','PLD Retiros'), (10,'operaciones_recibostipo','99','01','PLD Depositos'), (11,'operaciones_recibostipo','15','09','PLD Pago de Creditos'), (12,'operaciones_recibostipo','16','02','PLD Retiros'), (13,'operaciones_recibostipo','18','01','PLD Depositos'), (14,'operaciones_recibostipo','19','01','PLD Depositos'), (15,'operaciones_recibostipo','20','09','PLD Pago de Credito'), (16,'operaciones_recibostipo','21','10','PLD Pago de Primas de seguro'), (17,'operaciones_recibostipo','31','02','PLD Retiros'), (18,'operaciones_recibostipo','32','02','PLD Retiros'), (19,'operaciones_recibostipo','200','09','Pago de Creditos'), (21,'tesoreria_tipos_de_pago','efectivo','01','PLD Efectivo'), (22,'tesoreria_tipos_de_pago','efectivo.egreso','01','PLD Efectivo'), (23,'tesoreria_tipos_de_pago','cheque','02','PLD Documentos'), (24,'tesoreria_tipos_de_pago','cheque.ingreso','02','PLD Documentos'), (25,'tesoreria_tipos_de_pago','foraneo','02','PLD Documentos'), (26,'tesoreria_tipos_de_pago','transferencia','03','PLD Transferencia')";

$sql["20140757"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos_generales_saldos.php?', 'Reporte de Saldos de Credito', 'general_creditos', '10002', 'Saldos de Creditos sin filtros', '14')";

$sql["20140757"][]	= "ALTER TABLE `socios_tipoingreso` ADD COLUMN `nivel_de_riesgo` INT(4) NULL DEFAULT 10 AFTER `estado` ";

$sql["20140757"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos para Personas' , `menu_description` = '' , `menu_image` = 'catalogo' WHERE `idgeneral_menu` = '2050'";
$sql["20140757"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Tipos de Ingreso' , `menu_file` = 'frmsocios/socio_tipo_de_ingreso.frm.php' , `menu_description` = '' , `menu_image` = 'tipo' WHERE `idgeneral_menu` = '2053'";
$sql["20140757"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Colonias' WHERE `idgeneral_menu` = '2055' ";
$sql["20140757"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Regiones' , `menu_image` = 'region' WHERE `idgeneral_menu` = '2054'";
$sql["20140757"][]	= "CREATE TABLE IF NOT EXISTS `aml_risk_matrix` (`idaml_risk_matrix` INT NOT NULL AUTO_INCREMENT, `objeto_relacionado` VARCHAR(20) NULL COMMENT 'PERSONA OPERACION PRODUCTO PAIS REGION',  `clave_de_riesgo` INT NULL, `puntaje` FLOAT(14,6) NULL,  `descripcion` VARCHAR(100) NULL,  PRIMARY KEY (`idaml_risk_matrix`)) ENGINE = InnoDB";
$sql["20140757"][]	= "CREATE TABLE IF NOT EXISTS `personas_regimen_fiscal` (  `clave_de_regimen` INT NOT NULL,  `nombre_del_regimen` VARCHAR(100) NULL,  PRIMARY KEY (`clave_de_regimen`)) ENGINE = InnoDB";
$sql["20140757"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmcreditos/solicitud_de_credito.frm.php' , `menu_description` = 'Agregar Solicitud de Credito' , `menu_image` = 'credito' , `menu_order` = '0' , `menu_help_id` = '3001' WHERE `idgeneral_menu` = '3001'";
$sql["20140758"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptacumulados/rpt_saldos_creditos_mensuales.rpt.php?', 'Saldos de Credito por Mes', 'general_acumulados', '801', 'Este reporte muestra los saldos de la cartera por mes, del año que transcurre', '801')";

$sql["20140758"][]	= "UPDATE `general_menu` SET `menu_title` = 'Registro de Personas Fisicas' , `menu_file` = 'frmsocios/registro-personas_fisicas.frm.php' , `menu_description` = 'Registro de Personas Fisicas' , `menu_image` = 'personas' , `menu_order` = '1' WHERE `idgeneral_menu` = '2001' ";

$sql["20140758"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('2056', '2050', 'Catalogo de Regimen Fiscal', 'frmsocios/catalogo_regimen_fiscal.grid.php', 'tiny', '', 'catalogo', 'command', '2056', '2056') ";
$sql["20140758"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('20002', '2000', 'Registro de Personas Morales', 'frmsocios/registro-personas_morales.frm.php', '', 'grupos', 'command', '2', '20002')";
$sql["20140758"][]	= "UPDATE `general_menu` SET `menu_parent` = '2000' , `menu_title` = 'Registro de Grupos Solidarios' , `menu_file` = 'frmsocios/registro-grupos.frm.php' , `menu_image` = 'grupos' , `menu_order` = '3' WHERE `idgeneral_menu` = '2011'";

$sql["20140758"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('20100', '2000', 'Operaciones Auxiliares', '', '', 'otro', 'parent', '5')";
$sql["20140758"][]	= "UPDATE `general_menu` SET `menu_parent` = '20100' WHERE `idgeneral_menu` = '2005'";
$sql["20140758"][]	= "UPDATE `general_menu` SET `menu_parent` = '20100' WHERE `idgeneral_menu` = '2007'";
$sql["20140758"][]	= "UPDATE `general_menu` SET `menu_parent` = '20100' WHERE `idgeneral_menu` = '2002'";

$sql["20140758"][]	= "ALTER TABLE `personas_regimen_fiscal` ADD COLUMN `tipo_de_persona` INT(4) NULL COMMENT '1 fisica 3 moral' AFTER `nombre_del_regimen`";

$sql["20140758"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('100', 'ASALARIADOS', '1')";
$sql["20140758"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('200', 'PERSONAS FISICAS EN EL REGIMEN GENERAL DE LEY', '1')";
$sql["20140758"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('500', 'PERSONAS MORALES EN EL REGIMEN GENERAL DE LEY', '3')";
$sql["20140758"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('1', 'NINGUNO', '1')";
$sql["20140758"][]	= "ALTER TABLE `socios_vivienda` CHANGE COLUMN `codigo_postal` `codigo_postal` INT NOT NULL DEFAULT '24000' ";

$sql["20140759"][]	= "DELETE FROM general_colonias WHERE nombre_colonia LIKE '%ANOTE_EL_NOMBRE_COMPLETO%' ";

$sql["20140759"][]	= "INSERT INTO `personas_actividad_economica_tipos` (`clave_interna`, `clave_de_actividad`, `nombre_de_la_actividad`, `descripcion_detallada`, `productos`, `clasificacion`) VALUES ('10', '9800309', 'EMPLEADO_MIGRADO', '', '', 'CLASE')";
$sql["20140759"][]	= "INSERT INTO `personas_actividad_economica_tipos` (`clave_interna`, `clave_de_actividad`, `nombre_de_la_actividad`, `descripcion_detallada`, `productos`, `clasificacion`) VALUES ('11', '9800310', 'ADMINISTRATIVO_MIGRADO', '', '', 'CLASE')";
$sql["20140759"][]	= "INSERT INTO `personas_actividad_economica_tipos` (`clave_interna`, `clave_de_actividad`, `nombre_de_la_actividad`, `descripcion_detallada`, `productos`, `clasificacion`) VALUES ('40', '0100008', 'AGRICULTOR_MIGRADO', '', '', 'CLASE')";
$sql["20140759"][]	= "INSERT INTO `personas_actividad_economica_tipos` (`clave_interna`, `clave_de_actividad`, `nombre_de_la_actividad`, `descripcion_detallada`, `productos`, `clasificacion`) VALUES ('50', '8429012', 'EMPRESARIO_MIGRADO', '', '', 'CLASE')";
$sql["20140759"][]	= "INSERT INTO `personas_actividad_economica_tipos` (`clave_interna`, `clave_de_actividad`, `nombre_de_la_actividad`, `descripcion_detallada`, `productos`, `clasificacion`) VALUES ('99', '9999999', 'DESCONOCIDO_MIGRADO', '', '', 'CLASE')";
$sql["20140759"][]	= "ALTER TABLE `socios_tipoingreso` ADD COLUMN `tipo_de_persona` INT(2) NULL DEFAULT 0 COMMENT '1 = fisica 3 = moral 0 = ambos' AFTER `nivel_de_riesgo`";

$sql["20140760"][]	= "ALTER TABLE `socios_grupossolidarios` CHANGE COLUMN `grupo_solidario` `grupo_solidario` BIGINT(20) NULL DEFAULT NULL COMMENT 'Enlace socios General' ";
$sql["20140760"][]	= "ALTER TABLE `socios_grupossolidarios` CHANGE COLUMN `colonia_gruposolidario` `colonia_gruposolidario` INT NOT NULL DEFAULT 0 ";
$sql["20140760"][]	= "ALTER TABLE operaciones_mvtos ADD KEY (tipo_operacion)";
$sql["20140760"][]	= "INSERT INTO `sistema_lenguaje` (`idsistema_lenguaje`, `equivalente`, `traduccion`, `extension`) VALUES ('755', 'REGIMEN_FISCAL', 'Regimen Fiscal', '')";
$sql["20140760"][]	= "INSERT INTO `sistema_lenguaje` (`idsistema_lenguaje`, `equivalente`, `traduccion`, `extension`, `idioma`) VALUES ('756', 'REGIMEN_FISCAL', 'Tax Treatment', '', 'EN')";
$sql["20140760"][]	= "INSERT INTO `sistema_lenguaje` (`idsistema_lenguaje`, `equivalente`, `traduccion`, `extension`) VALUES ('757', 'REGIMEN_MATRIMONIAL', 'Regimen Matrimonial', '')";
$sql["20140760"][]	= "INSERT INTO `sistema_lenguaje` (`idsistema_lenguaje`, `equivalente`, `traduccion`, `extension`, `idioma`) VALUES ('758', 'REGIMEN_MATRIMONIAL', 'Regimen Matrimonial', '', 'EN')";
$sql["20140760"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Cashier Box' WHERE `idsistema_lenguaje` = '349'";

$sql["20140761"][]	= "CREATE TABLE IF NOT EXISTS `tesoreria_tipos_de_denominaciones` (`denominacion` INT(11) NOT NULL, `nombre` VARCHAR(45) NULL DEFAULT NULL, `valor_contra_uno` FLOAT(12,6) NULL DEFAULT NULL,`tipo_de_valor` VARCHAR(20) NULL COMMENT 'moneda, papel, documento', PRIMARY KEY (`denominacion`)) ENGINE = INNODB";
$sql["20140761"][]	= "CREATE TABLE IF NOT EXISTS `tesoreria_caja_arqueos` ( `codigo_de_arqueo` INT NOT NULL,  `codigo_de_caja` VARCHAR(100) NULL,  `fecha_de_arqueo` DATE NULL,  `valor_arqueado` FLOAT(16,3) NULL DEFAULT 0,  `numero_arqueado` FLOAT(16,3) NULL DEFAULT 0,  `monto_total_arqueado` FLOAT(16,3) NULL DEFAULT 0,  `hora_de_arqueo` BIGINT NULL,  `documento` VARCHAR(40) NULL,  `observaciones` VARCHAR(100) NULL,  `idusuario` INT NULL DEFAULT 99, `sucursal` VARCHAR(20) NULL DEFAULT 'matriz', `eacp` VARCHAR(20) NULL, PRIMARY KEY (`codigo_de_arqueo`)) ENGINE = INNODB";
$sql["20140761"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('1050', '1000', 'Catalogos de Tesoreria', '', '', 'tesoreria', 'parent', '99999', '1050')";
$sql["20140761"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`) VALUES ('1051', '1050', 'Catalogo de Valores y Equivalencia', 'frmtesoreria/catalogo.equivalencias.grid.php', 'tiny', '', 'equivalencias', 'command')";
$sql["20140761"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('1053', '1050', 'Catalogo de Monedas', 'frmtesoreria/catalogo.monedas.grid.php', 'tiny', '', 'monedas', 'command', '1053')";
$sql["20140761"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Bank Draft' WHERE `idsistema_lenguaje` = '533' ";
$sql["20140761"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Currents Accounts' WHERE `idsistema_lenguaje` = '416'";
$sql["20140761"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Cashier Register' WHERE `idsistema_lenguaje` = '349'";
$sql["20140761"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Out' WHERE `idsistema_lenguaje` = '645'";

$sql["20140761"][]	= "ALTER TABLE `captacion_cuentas` ADD COLUMN `recibo_de_inversion` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Ultimo recibo de inversion' AFTER `cuenta_de_intereses` ";
$sql["20140761"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`) VALUES ('3202', 'Poder Notarial', 'IPM')";
$sql["20140761"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('519', '20002', '3202', '0', 'PODER NOTARIAL')";

$sql["20140761"][]	= "CREATE TABLE IF NOT EXISTS `creditos_otros_datos` (`idcreditos_otros_datos` INT NOT NULL AUTO_INCREMENT, `clave_de_credito` BIGINT(20) NOT NULL DEFAULT 0, `fecha_de_expiracion` DATE, `clasificacion_de_parametro` VARCHAR(20) NULL, `clave_de_parametro` VARCHAR(20) NULL,  `valor_de_parametro` VARCHAR(100) NULL,  `descripcion_de_parametro` VARCHAR(100) NULL, `sucursal` VARCHAR(20) NULL DEFAULT 'matriz', PRIMARY KEY (`idcreditos_otros_datos`)) ENGINE = INNODB";
$sql["20140761"][]	= "ALTER TABLE `creditos_otros_datos` CHANGE `clave_de_parametro` `clave_de_parametro` VARCHAR(40) CHARSET utf8 COLLATE utf8_general_ci NULL";

$sql["20140761"][]	= "UPDATE `general_menu` SET `menu_order` = '20100' WHERE `idgeneral_menu` = '20100'";

$sql["20140902"][]	= "CREATE TABLE IF NOT EXISTS `sistema_equivalencias` (`control` INT NOT NULL AUTO_INCREMENT, `tabla` VARCHAR(60) NULL,  `clasificacion` VARCHAR(40) NULL,  `original` VARCHAR(60) NULL,  `equivalencia` VARCHAR(100) NULL,  `notas` VARCHAR(40) NULL,  PRIMARY KEY (`control`)) ENGINE = INNODB COMMENT = 'esta tabla almacenas equivalencia entre catalogo y externos'";
$sql["20140902"][]	= "ALTER TABLE `sistema_equivalencias` CHANGE COLUMN `tabla` `tabla` VARCHAR(60) NULL , CHANGE COLUMN `original` `original` VARCHAR(60) NULL , CHANGE COLUMN `equivalencia` `equivalencia` VARCHAR(100) NULL , ADD COLUMN `clasificacion` VARCHAR(40) NULL COMMENT 'Guarda equivalencias entre catalogo del sistema y externos' AFTER `notas`";

$sql["20140902"][]	= "UPDATE `sistema_equivalencias` SET clasificacion ='PLD.operaciones' WHERE ISNULL(clasificacion)";
$sql["20140902"][]	= "ALTER TABLE `bancos_operaciones` ADD COLUMN `cuenta_de_origen` BIGINT(20) NULL DEFAULT 0 COMMENT 'numero de cuenta bancaria de origen' AFTER `tipo_de_exhibicion`";
$sql["20140902"][]	= "ALTER TABLE `operaciones_recibos` ADD COLUMN `fecha_de_registro` DATE NULL DEFAULT '0000-00-00' AFTER `persona_asociada`";
$sql["20140902"][]	= "UPDATE `operaciones_recibos` SET fecha_de_registro=fecha_operacion WHERE fecha_de_registro='0000-00-00'";
$sql["20140902"][]	= "UPDATE `operaciones_recibos` SET fecha_de_registro=fecha_operacion WHERE fecha_de_registro='0000-00-00'";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185591','18550','Logo Import','install/logo.upload.frm.php','tiny','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','command','18559','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185592','9999','envionomina.svc.php','svc/envionomina.svc.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185593','9999','creditos.productos.frm.php','frmcreditos/creditos.productos.frm.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185594','9999','creditos_generales_saldos.php','rptcreditos/creditos_generales_saldos.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185595','9999','credito.svc.php','svc/credito.svc.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185596','9999','cuentas.svc.php','svc/cuentas.svc.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185597','9999','docs.explorer.php','frmutils/docs.explorer.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185598','9999','socios.docto.frm.php','frmsocios/socios.docto.frm.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185599','9999','rm.svc.php','svc/rm.svc.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185600','9999','solicitud_de_credito.validacion.frm.php','frmcreditos/solicitud_de_credito.validacion.frm.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185601','9999','solicitud_de_credito.2.frm.php','frmcreditos/solicitud_de_credito.2.frm.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185602','9999','creditos.datos-bancarios.frm.php','frmcreditos/creditos.datos-bancarios.frm.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185603','9999','castigo_de_cartera.frm.php','frmcreditos/castigo_de_cartera.frm.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";
$sql["20140902"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('185604','9999','update.json.php','install/update.json.php','principal','NO_DESCRIPTION','null.png','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','general','0','9999','false')";

$sql["20140902"][]	= "ALTER TABLE `socios_aeconomica_dependencias` ADD COLUMN `email_de_envio` VARCHAR(200) NULL COMMENT 'separado por comas' AFTER `nombre_corto`";
$sql["20140902"][]	= "ALTER TABLE `socios_aeconomica_dependencias` ADD COLUMN `producto_preferente` INT NULL DEFAULT 100 COMMENT 'Tipo de producto o convenio preferente' AFTER `email_de_envio`";
$sql["20140902"][]	= "ALTER TABLE `operaciones_mvtos` ADD UNIQUE INDEX `clave_de_operacion` (`idoperaciones_mvtos`)";
$sql["20140902"][]	= "ALTER TABLE `bancos_operaciones` ADD INDEX `indice_por_recibo` (`recibo_relacionado`)";

$sql["20140902"][]	= "ALTER TABLE `socios_general` DROP PRIMARY KEY, ADD PRIMARY KEY (`codigo`), ADD INDEX `empresa` (`dependencia`)";
$sql["20140902"][]	= "ALTER TABLE `socios_general` ADD UNIQUE INDEX `persona` (`codigo`)";
//2014.09.03
$sql["20140903"][]	= "UPDATE `general_menu` SET `menu_file` = REPLACE(`menu_file`, '/home/padio/Dropbox/htdocs/', '')";
$sql["20140903"][]	= "UPDATE `general_menu` SET `menu_file` = REPLACE(`menu_file`, '/var/www/', '')";
//$sql["20140903"][]	= "ALTER TABLE `operaciones_recibos` ADD INDEX `tipo_de_docto` (`tipo_docto`)";
$sql["20140904"][]	= "UPDATE `general_sucursales` SET `titular_de_cobranza` = '0' , `titular_de_seguimiento` = '0' , `titular_de_contabilidad` = '0' , `titular_de_inventarios` = '0' , `titular_de_control_interno` = '0' , `titular_de_nominas` = '0' , `titular_de_cumplimiento` = '0'";
$sql["20140904"][]	= "ALTER TABLE `general_sucursales` CHANGE COLUMN `gerente_sucursal` `gerente_sucursal` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `caja_local_residente` `caja_local_residente` INT(4) NULL DEFAULT 1 , CHANGE COLUMN `titular_de_cobranza` `titular_de_cobranza` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `titular_de_seguimiento` `titular_de_seguimiento` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `titular_de_contabilidad` `titular_de_contabilidad` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `titular_de_inventarios` `titular_de_inventarios` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `titular_de_control_interno` `titular_de_control_interno` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `titular_de_nominas` `titular_de_nominas` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `titular_de_cumplimiento` `titular_de_cumplimiento` BIGINT(20) NULL DEFAULT 1 , CHANGE COLUMN `codigo_postal` `codigo_postal` INT(11) NULL DEFAULT 1 ";

$sql["20140905"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_image`, `menu_type`) VALUES ('72201', '72000', 'Catalogo de Riesgos AML', 'frmpld/catalogo.riesgos.frm.php', 'catalogo', 'command')";
$sql["20140905"][]	= "CREATE TABLE IF NOT EXISTS `creditos_plan_de_pagos` ( `plan_de_pago` INT NOT NULL DEFAULT 1, `clave_de_credito` BIGINT(20) NOT NULL DEFAULT 1, `numero_de_parcialidad` INT NOT NULL DEFAULT 1, `tipo_de_tasa` INT NOT NULL DEFAULT 1, `periocidad` INT NOT NULL DEFAULT 1, `recibo` BIGINT(20) NOT NULL DEFAULT 1 COMMENT 'Ultimo recibo de pago', `fecha_de_registro` BIGINT(12) NULL DEFAULT 0, `fecha_de_pago` BIGINT(12) NULL DEFAULT 0, `capital` FLOAT(14,2) NULL DEFAULT 0, `interes` FLOAT(14,2) NULL DEFAULT 0, `moratorio` FLOAT(14,2) NULL DEFAULT 0, `impuesto` FLOAT(14,2) NULL DEFAULT 0, `otros` FLOAT(14,2) NULL DEFAULT 0, `otros_codigo` INT NULL DEFAULT 0, `fecha_de_ultimo_abono` BIGINT(12) NULL DEFAULT 0, `fecha_de_vencimiento` BIGINT(12) NULL DEFAULT 0, `saldo_inverso` FLOAT(16,2) NULL DEFAULT 0, `saldo_capital` FLOAT(16,2) NULL DEFAULT 0, `centro_de_trabajo` INT NOT NULL DEFAULT 1, PRIMARY KEY (`plan_de_pago`), INDEX `fk_creditos_plan_de_pagos_creditos1_idx` (`clave_de_credito` ASC), INDEX `fk_creditos_plan_de_pagos_entidad_centro_de_trabajo1_idx` (`centro_de_trabajo` ASC) ) ENGINE = INNODB";
$sql["20140905"][]	= "ALTER TABLE `creditos_plan_de_pagos` CHANGE `fecha_de_registro` `fecha_de_registro` DATE NULL, CHANGE `fecha_de_pago` `fecha_de_pago` DATE NULL, CHANGE `fecha_de_ultimo_abono` `fecha_de_ultimo_abono` DATE NULL, CHANGE `fecha_de_vencimiento` `fecha_de_vencimiento` DATE NULL, CHANGE `centro_de_trabajo` `centro_de_trabajo` VARCHAR(20) DEFAULT 'matriz' NOT NULL";

$sql["20140906"][]	= "ALTER TABLE `aml_risk_register` CHANGE COLUMN `firma_de_checking` `firma_de_checking` TINYTEXT NULL DEFAULT NULL , CHANGE COLUMN `notas_de_checking` `notas_de_checking` VARCHAR(200) NULL DEFAULT NULL COMMENT 'notas de rechazo o riesgos' , ADD COLUMN `razones_de_reporte` TEXT NULL AFTER `tipo_de_operacion`, ADD COLUMN `acciones_tomadas` TEXT NULL AFTER `razones_de_reporte`";
$sql["20140906"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'la' WHERE `idsistema_lenguaje` = '771'";

$sql["20140907"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `codigo_de_base` = '40500' ";
$sql["20140907"][]	= "ALTER TABLE `aml_alerts` ADD COLUMN `tercero_relacionado` BIGINT(20) NULL DEFAULT 1 COMMENT 'en los casos de personas o vinculadas con la operaciones' AFTER `tipo_de_documento`";
$sql["20140907"][]	= "ALTER TABLE `aml_risk_register` ADD COLUMN `tercero_relacionado` BIGINT(20) NULL DEFAULT 1 COMMENT 'en los casos de personas o vinculadas con la operaciones' AFTER `acciones_tomadas`";

$sql["20140907"][]	= "ALTER TABLE `aml_alerts` CHANGE COLUMN `notas_de_checking` `notas_de_checking` TEXT NULL DEFAULT NULL COMMENT 'Notas de la cancelacion' , CHANGE COLUMN `tipo_de_documento` `tipo_de_documento` INT(5) NULL DEFAULT '0' COMMENT '0 desconocido' ";
$sql["20140907"][]	= "ALTER TABLE `aml_risk_register` ADD COLUMN `mensajes_del_sistema` MEDIUMTEXT NULL COMMENT 'todo los logs copiados del sistema\n' AFTER `tercero_relacionado`";
$sql["20140907"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Personas. Inscripcion Persona Politicamente Expuesta' , `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '901010'";
$sql["20140907"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '101004'";
$sql["20140907"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101402'";
$sql["20140907"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '801009'";

$sql["20140907"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reporte Generales PLD' , `menu_description` = '' , `menu_image` = 'aml' WHERE `idgeneral_menu` = '71201'";

//$sql["20140907"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('71202', '71000', 'Reporte de Operaciones Relevantes', 'frmpld/reporte-operaciones-relevantes.frm.php', 'tiny', '', 'reporte', 'command', '2')";
//$sql["20140907"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('71203', '71000', 'Reporte de Operaciones Inusuales', 'frmpld/reporte-operaciones-inusuales.frm.php', 'tiny', '', 'reporte', 'command', '3')";
//$sql["20140907"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('71204', '71000', 'Reporte de Operaciones Preocupantes', 'frmpld/reporte-operaciones-preocupantes.frm.php', 'tiny', '', 'reporte', 'command', '4')";

$sql["20140908"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/operaciones_inusuales.rpt.php?', 'Reporte de Operaciones Inusuales', 'aml', '5102', '', '2')";
$sql["20140908"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/operaciones_preocupantes.rpt.php?', 'Reporte de Operaciones Internas Preocupantes', 'aml', '5103', '', '3')";

$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion` (`codigo_de_base`, `descripcion`, `tipo_de_base`) VALUES ('11000', 'OPERACIONES QUE SE FACTURAN', 'fiscal')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('535', '11000', '147', 'COM X APERTURA CRED')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('525', '11000', '140', 'INTERES')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('526', '11000', '141', 'INTERES MORATORIO')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('527', '11000', '142', '1', 'MINIMO')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('528', '11000', '143', '1', 'INTS VENCIDOS')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('529', '11000', '145', 'COM X COBRANZA')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('530', '11000', '146', 'COMISIONES VARIAS')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('531', '11000', '151', 'IVA INTS')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('532', '11000', '152', 'IVA OTROS CONCEPTOS')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('533', '11000', '301', '-1', 'APP BON')";
$sql["20140909"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('534', '11000', '303', '-1', 'AP BON Otros')";

$sql["20140909"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1000' WHERE `ideacp_config_bases_de_integracion_miembros` = '531' AND `codigo_de_base` = '11000' AND `miembro` = '151'";
$sql["20140909"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1000' WHERE `ideacp_config_bases_de_integracion_miembros` = '532' AND `codigo_de_base` = '11000' AND `miembro` = '152'";
$sql["20140909"][]	= "INSERT INTO `contable_centrodecostos` (`idcontable_centrodecostos`, `nombre_centrodecostos`) VALUES ('1', 'POR DEFECTO')";

$sql["20140909"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('5022', '5020', 'Perfil Contable de Polizas', 'frmcontabilidad/perfil_contable.grid.php', '', 'perfil', 'command', '5022')";
$sql["20140909"][]	= "ALTER TABLE `contable_polizas` ADD COLUMN `recibo_relacionado` BIGINT(20) NULL DEFAULT 1 COMMENT 'Recibo en sistema relacionado' AFTER `idusuario`, ADD INDEX `idrecibo` (`recibo_relacionado` ASC)";

$sql["20140909"][]	= "ALTER TABLE `contable_polizas` CHANGE COLUMN `ejercicio` `ejercicio` INT(10) NOT NULL DEFAULT 2014 , CHANGE COLUMN `periodo` `periodo` INT(10) NOT NULL DEFAULT 1 , CHANGE COLUMN `tipopoliza` `tipopoliza` INT(10) NOT NULL DEFAULT 1 , CHANGE COLUMN `numeropoliza` `numeropoliza` INT(10) NOT NULL DEFAULT 1 , CHANGE COLUMN `clase` `clase` INT(10) NULL DEFAULT 1 , CHANGE COLUMN `cargos` `cargos` DOUBLE(18,2) NULL DEFAULT 0 , CHANGE COLUMN `abonos` `abonos` DOUBLE(18,2) NULL DEFAULT 0 , ADD COLUMN `codigo_unico` INT NOT NULL AUTO_INCREMENT AFTER `recibo_relacionado`, DROP PRIMARY KEY, ADD PRIMARY KEY (`codigo_unico`), ADD INDEX `codigo_pol` (`ejercicio` ASC, `periodo` ASC, `tipopoliza` ASC, `numeropoliza` ASC)";
$sql["20140909"][]	= "CREATE TABLE IF NOT EXISTS `operaciones_archivo_de_facturas` ( `uuid` VARCHAR(200) NOT NULL,  `clave_de_recibo` BIGINT(25) NOT NULL DEFAULT 1,  `contenido` LONGTEXT NULL,  PRIMARY KEY (`uuid`)) ENGINE = INNODB";
$sql["20140909"][]	= "ALTER TABLE `operaciones_archivo_de_facturas` ADD COLUMN `impreso` LONGTEXT NULL DEFAULT NULL AFTER `contenido`";

$sql["20140910"][]	= "ALTER TABLE `contable_movimientos` ADD COLUMN `clave_unica` INT NOT NULL AUTO_INCREMENT AFTER `abono`, DROP PRIMARY KEY, ADD PRIMARY KEY (`clave_unica`), ADD INDEX `por_poliza` (`ejercicio` ASC, `periodo` ASC, `tipopoliza` ASC, `numeropoliza` ASC), ADD INDEX `por_operacion` (`ejercicio` ASC, `periodo` ASC, `tipopoliza` ASC, `numeropoliza` ASC, `numeromovimiento` ASC), ADD INDEX `por_cuenta` (`numerocuenta` ASC)";
$sql["20140910"][]	= "ALTER TABLE `contable_movimientos` CHANGE COLUMN `tipomovimiento` `tipomovimiento` INT NOT NULL DEFAULT '0'";

$sql["20140911"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('5023', '5020', 'Contabilidad de Recibos', 'frmcontabilidad/recibos_con_poliza.frm.php', '', 'recibo', 'command', '5023')";
$sql["20140911"][]	= "INSERT INTO `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`) VALUES ('105', '5', '704', 'APOST VOLUNTARIAS POR DONACIONES', '-1')";
$sql["20140911"][]	= "UPDATE `contable_catalogotipos` SET `nombre_del_tipo` = 'Orden Deudora' WHERE `idcontable_catalogotipos` = 'OD'";

$sql["20140911"][]	= "ALTER TABLE `tesoreria_cajas` ADD COLUMN `total_cobrado` FLOAT(16,4) NULL DEFAULT 0 COMMENT 'suma de todos los cobros de caja' AFTER `fondos_arqueados`, ADD INDEX `porusuario` (`idusuario` ASC)";
$sql["20140911"][]	= "UPDATE `general_menu` SET `menu_destination` = 'tiny' WHERE `menu_parent` = '5050' OR `menu_parent` = '5060' ";

$sql["20140912"][]	= "INSERT INTO `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`) VALUES ('106', '5', '706', 'PARTE SOCIAL POR DONACIONES', '-1')";
$sql["20140912"][]	= "INSERT INTO `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`) VALUES ('107', '5', '711', 'FONDO DE CONT POR DONACIONES', '-1')";

$sql["20140913"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Numero de Personas Registradas por Genero' , `order_index` = '1' WHERE `idreport` = '125'";
$sql["20140913"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptacumulados/socios_por_genero.rpt.php?credito=true&' , `descripcion_reports` = 'Monto Colocado por Genero' , `order_index` = '2' WHERE `idreport` = '119'";
$sql["20140913"][]	= "UPDATE `general_utilerias` SET `descripcion_utileria` = 'Actualiza es Estado de Todos los Credito hasta una fecha determinada.' , `describe_param_1` = 'FECHA_DE_CORTE' WHERE `idgeneral_utilerias` = '853'";

$sql["20140914"][]	= "INSERT INTO `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`) VALUES ('9901', '99', '9200', 'BANCOS CONTROLADO', '1') , ('9902', '99', '9100', 'EFECTIVO CONTROLADO', '1'), ('9903', '99', '9101', 'EFECTIVO EN DOCTO FORANEOS', '1'), ('9904', '99', '9201', 'DESCUENTO CHEQUE INTERNO CONTROLADO', '1') , ('9905', '99', '147', 'COMISIONES COBRADAS', '-1')";
$sql["20140914"][]	= "ALTER TABLE `contable_polizas_proforma`    CHANGE `idusuario` `idusuario` INT(4) DEFAULT 99  NULL";
$sql["20140914"][]	= "UPDATE `operaciones_tipos` SET `cuenta_contable` = '\$cuenta = getCuentaContablePorBanco(\$cuenta_bancaria);' WHERE `idoperaciones_tipos` = '9200'";
$sql["20140914"][]	= "UPDATE `operaciones_tipos` SET `cuenta_contable` = '\$cuenta = getCuentaContablePorBanco(\$cuenta_bancaria);' WHERE `idoperaciones_tipos` = '9201' ";
$sql["20140914"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = '' WHERE `idoperaciones_tipos` = '231'";
$sql["20140914"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = '' WHERE `idoperaciones_tipos` = '230'";
//$sql["20140914"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if (!isset(\$Cuenta)){\$Cuenta = new  cCuentaDeCaptacion(\$docto);} \$Cuenta->init(false, true); \$Cuenta->setUpdateSaldoByMvtos();' WHERE `idoperaciones_tipos` = '230'";
$sql["20140914"][]	= "INSERT INTO `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`) VALUES ('9907', '99', '145', 'COM X SEGUIMIENTO', '-1'),('9908', '99', '146', 'COM VARIAS', '-1')";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '55' AND `codigo_de_base` = '1001' AND `miembro` = '412'";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '53' AND `codigo_de_base` = '1001' AND `miembro` = '410'";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '52' AND `codigo_de_base` = '1001' AND `miembro` = '142' ";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '54' AND `codigo_de_base` = '1001' AND `miembro` = '411'; ";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '69' AND `codigo_de_base` = '1001' AND `miembro` = '1005'";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '68' AND `codigo_de_base` = '1001' AND `miembro` = '803'";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '66' AND `codigo_de_base` = '1001' AND `miembro` = '801' ";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '65' AND `codigo_de_base` = '1001' AND `miembro` = '601' ";
$sql["20140914"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '64' AND `codigo_de_base` = '1001' AND `miembro` = '600'";
$sql["20140914"][]	= "UPDATE `entidad_configuracion` SET `tipo` = 'aml' WHERE `nombre_del_parametro` = 'monto_minimo_para_reportar_operaciones'";
$sql["20140914"][]	= "UPDATE `entidad_configuracion` SET `tipo` = 'personas' WHERE `tipo` = 'socios'";
$sql["20140914"][]	= "UPDATE `entidad_configuracion` SET `tipo` = 'personas' WHERE `nombre_del_parametro` = 'grupo_por_defecto'";
$sql["20140914"][]	= "UPDATE `entidad_configuracion` SET `tipo` = 'operaciones' WHERE `nombre_del_parametro` = 'documento_por_defecto'";
//==========================================================================================================================================

$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '1' WHERE `idreport` = '30' ";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '2' WHERE `idreport` = '106' ";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '1' WHERE `idreport` = '1'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '2' WHERE `idreport` = '2'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '3' WHERE `idreport` = '141'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '4' WHERE `idreport` = '118'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '5' WHERE `idreport` = '116'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '1' WHERE `idreport` = '46'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '2' WHERE `idreport` = '47'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '2' WHERE `idreport` = '124'";
$sql["20140915"][]	= "UPDATE `general_reports` SET `order_index` = '3' WHERE `idreport` = '124'";

$sql["20140916"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- Estado de resultados -->\r\n<table cellpadding=\"1\" cellspacing=\"1\" style=\"width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td colspan=\"4\">\r\n   <h1 style=\"text-align:center\">ESTADO DE RESULTADOS</h1>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"4\">\r\n   <h3 style=\"text-align:center\">Al variable_fecha_larga_actual</h3>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">\r\n   <h2>INGRESOS</h2>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">variable_ficha_ingresos</td>\r\n  </tr>\r\n  <tr>\r\n   <td>TOTAL EGRESOS:</td>\r\n   <td style=\"text-align:right\">variable_total_ingresos</td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">\r\n   <h2>EGRESOS</h2>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">variable_ficha_egresos</td>\r\n  </tr>\r\n  <tr>\r\n   <td>TOTAL EGRESOS:</td>\r\n   <td style=\"text-align:right\">variable_total_egresos</td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td>\r\n   <h3>RESULTADO DEL PERIODO:</h3>\r\n   </td>\r\n   <td style=\"text-align:right\">variable_resultado_del_periodo</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n' WHERE `idgeneral_contratos` = '502'";
$sql["20140916"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- Balance general -->\r\n<table cellpadding=\"1\" cellspacing=\"1\" style=\"width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td colspan=\"4\">\r\n   <h1 style=\"text-align:center\">ESTADO DE POCISI&Oacute;N FINANCIERA</h1>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"4\">\r\n   <h3 style=\"text-align:center\">Al variable_fecha_larga_actual</h3>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\" style=\"width:50%\" >\r\n   <h2>ACTIVO</h2>\r\n   </td>\r\n   <td colspan=\"2\" rowspan=\"1\" style=\"width:50%\">\r\n   <h2>PASIVO</h2>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"8\">variable_ficha_activo\r\n   <h3>&nbsp;</h3>\r\n   </td>\r\n   <td colspan=\"2\" rowspan=\"1\">variable_ficha_pasivo</td>\r\n  </tr>\r\n  <tr>\r\n   <td>TOTAL PASIVO:</td>\r\n   <td style=\"text-align:right\">variable_total_pasivo</td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">\r\n   <h2>CAPITAL</h2>\r\n   </td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\"2\" rowspan=\"1\">variable_ficha_capital</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Resultados del Periodo:</td>\r\n   <td style=\"text-align:right\">variable_resultado_del_periodo</td>\r\n  </tr>\r\n  <tr>\r\n   <td>\r\n   <h3>TOTAL CAPITAL:</h3>\r\n   </td>\r\n   <td style=\"text-align:right\">variable_total_capital</td>\r\n  </tr>\r\n  <tr>\r\n   <td>&nbsp;</td>\r\n   <td style=\"text-align:right\">&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td>\r\n   <h3>TOTAL ACTIVO:</h3>\r\n   </td>\r\n   <td style=\"text-align:right\">variable_total_activo</td>\r\n   <td>\r\n   <h3>TOTAL PASIVO MAS CAPITAL:</h3>\r\n   </td>\r\n   <td style=\"text-align:right\">variable_pasivo_mas_capital</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n\r\n<p><br />\r\n<!-- Balance General --></p>\r\n\r\n<p>&nbsp;</p>\r\n' WHERE `idgeneral_contratos` = '501'";
$sql["20140916"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8801', '80', 'Acuse de Consulta en Lista Negra', '<!-- Consulta OFAC -->')";
$sql["20140916"][]	= "INSERT INTO `socios_relacionestipos` (`idsocios_relacionestipos`, `descripcion_relacionestipos`, `subclasificacion`, `descripcion_larga`, `tipo_relacion`, `puntuacion_en_credit_scoring`) VALUES ('511', 'AKA Alias de', '1', 'AKA Lista SDN', '511', '0')";
$sql["20140916"][]	= "INSERT INTO `socios_relacionestipos` (`idsocios_relacionestipos`, `descripcion_relacionestipos`, `subclasificacion`, `descripcion_larga`, `tipo_relacion`, `puntuacion_en_credit_scoring`) VALUES ('512', 'FKA Formalmente Conocido como', '1', 'FKA Lista SDN', '512', '0')";

$sql["20140916"][]	= "INSERT INTO `socios_consanguinidad` (`idsocios_consanguinidad`, `descripcion_consanguinidad`) VALUES ('98', 'MISMO')";

$sql["20140917"][]	= "UPDATE `personas_domicilios_paises` SET `nombre_oficial` = 'ESPAÑA' WHERE `clave_de_control` = 'ES'";
$sql["20140917"][]	= "UPDATE `personas_domicilios_paises` SET `nombre_oficial` = 'SANTA SEDE / ESTADO DE LA CIUDAD DEL VATICANO' WHERE `clave_de_control` = 'VA'";
$sql["20140917"][]	= "UPDATE `personas_domicilios_paises` SET `nombre_oficial` = 'ISLAS ALAND' WHERE `clave_de_control` = 'AX'";

$sql["20140918"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<h1 style=\"text-align: center;\"><br />\r\n<!-- Consulta OFAC -->S.A.F.E. Open Source Microfinance System.</h1>\r\n\r\n<h3 style=\"text-align: center;\">SDN/OFAC B&uacute;squeda de Personas &quot;<strong>Specially Designated Nationals List</strong>&quot;.</h3>\r\n\r\n<hr />\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\nvariable_listado_de_cedulas\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<hr />\r\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" style=\"height:132px; width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td>Fecha de Carga:</td>\r\n   <td>&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Fecha de Publicaci&oacute;n:</td>\r\n   <td>2014/10/31</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Total Registros:</td>\r\n   <td>5819</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Responsable:</td>\r\n   <td>http://www.opencorebanking.com/</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Contacto:</td>\r\n   <td>admin@opencorebanking.com</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n\r\n' WHERE `idgeneral_contratos` = '8801'";
$sql["20140918"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `codigo_de_base`= 20001 OR `codigo_de_base`= 20002";

$sql["20140919"][]	= "INSERT INTO `socios_regimenvivienda` (`idsocios_regimenvivienda`, `descipcion_regimenvivienda`, `tipo_regimen`) VALUES ('5', 'VIVE CON FAMILIARES', '5')";
$sql["20140919"][]	= "DROP TABLE IF EXISTS `aml_risk_matrix`";
$sql["20140919"][]	= "CREATE TABLE IF NOT EXISTS `aml_riesgo_matrices` (  `idaml_riesgo_matrices` INT NOT NULL AUTO_INCREMENT,  `nombre_de_la_matriz` VARCHAR(40) NULL COMMENT 'PERSONA OPERACION PRODUCTO PAIS REGION\n',  `tipo_de_persona` INT NULL COMMENT 'Fisica Moral',  `pais_de_origen` VARCHAR(4) NULL DEFAULT 'MX',  `clave_de_actividad` BIGINT(20) NULL COMMENT 'id de actividad economica',  `producto_nivel_riesgo` INT NULL COMMENT '0 a 100',   `riesgo_resultante` INT NULL COMMENT '0  a 100',   PRIMARY KEY (`idaml_riesgo_matrices`)) ENGINE = INNODB ";



$sql["20140920"][]	= "INSERT INTO `socios_relacionestipos` (`idsocios_relacionestipos`, `descripcion_relacionestipos`, `subclasificacion`, `descripcion_larga`, `tipo_relacion`, `puntuacion_en_credit_scoring`, `requiere_domicilio`, `requiere_actividadeconomica`) VALUES ('551', 'PROVEEDOR DE RECURSOS', '1', 'Persona que provee los recursos directos para la operacion', '551', '0', '1', '1')";
$sql["20140920"][]	= "INSERT INTO `socios_relacionestipos` (`idsocios_relacionestipos`, `descripcion_relacionestipos`, `subclasificacion`, `descripcion_larga`, `tipo_relacion`, `puntuacion_en_credit_scoring`, `requiere_domicilio`, `requiere_actividadeconomica`) VALUES ('552', 'PROPIETARIO REAL', '1', 'Persona que obtiene los beneficios de la operacion', '552', '0', '1', '1')";
//$sql["20140920"][]	= "";


$sql["20140920"][]	= "DELETE FROM general_log WHERE text_log LIKE '%		= %' ";
$sql["20140921"][]	= "ALTER TABLE `socios_aeconomica_dependencias` CHANGE COLUMN `dias_de_avisos` `dias_de_avisos` VARCHAR(150) NULL DEFAULT '' COMMENT 'dias de aviso formato : periocidad1=dia1,dia2|periocidad2=dia1,dia2' , ADD COLUMN `dias_de_pago_nomina` VARCHAR(150) NULL COMMENT 'dias de pago de la nomina formato : periocidad1=dia1,dia2|periocidad2=dia1,dia2' AFTER `formato_de_relacion`, ADD COLUMN `dias_de_liquidacion` VARCHAR(150) NULL COMMENT 'dias en que deben pagar a la financiera formato : periocidad1=dia1,dia2|periocidad2=dia1,dia2' AFTER `dias_de_pago_nomina` ";

$sql["20140922"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('11317', '11300', 'Catalogo de Codigos de Error', 'rptsecurity/codigos_de_error.rpt.php', '_blank', '', 'reporte', 'command', '11317', '11317')";
$sql["20140922"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('2011', 'Recibo Eliminado', 'common')";

$sql["20140922"][]	= "ALTER TABLE `socios_aeconomica` ADD COLUMN `domicilio_vinculado` INT NULL DEFAULT 1 COMMENT 'domicilio vinculado en el sistema' AFTER `numero_de_seguridad_social`, ADD COLUMN `ae_clave_de_localidad` BIGINT(20) NULL DEFAULT 1 COMMENT 'clave de localidad segun uif' AFTER `domicilio_vinculado`, ADD COLUMN `ae_codigo_postal` INT NULL DEFAULT 0 COMMENT 'codigo postal' AFTER `ae_clave_de_localidad`";
$sql["20140922"][]	= "ALTER TABLE `socios_aeconomica` ADD INDEX `codigo_postal` (`ae_codigo_postal` ASC), ADD INDEX `localidad` (`ae_clave_de_localidad` ASC), ADD INDEX `iddomicilio` (`domicilio_vinculado` ASC), ADD INDEX `idactividad` (`tipo_aeconomica` ASC)";

$sql["20141100"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('13001', 'Personas. Purgar Domicilio en Actividad Economica', 'Corrige el Domicilio de la Actividad Economica por Empresa o por Datos de vivienda, se recomienda correr la 880 primero.')";

$sql["20141101"][]	= "CREATE TABLE IF NOT EXISTS `aml_riesgo_perfiles` (  `idaml_riesgo_perfiles` INT NOT NULL,  `objeto_de_origen` VARCHAR(50) NULL COMMENT 'tabla de origen',  `campo_de_origen` VARCHAR(50) NULL COMMENT 'campo de la tabla con el valor',  `valor_de_origen` VARCHAR(50) NULL COMMENT 'valor id del campo',   `nivel_de_riesgo` INT(4) NULL,   PRIMARY KEY (`idaml_riesgo_perfiles`)) ENGINE = InnoDB";
$sql["20141101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('72202', '72000', 'Perfiles de Riesgo', 'frmpld/perfiles-de-riesgo.frm.php', 'tiny', '', 'riesgo', 'command', '2', '72202')";

$sql["20141101"][]	= "ALTER TABLE `socios_consanguinidad` ADD COLUMN `grado_de_consanguinidad` INT(2) NULL DEFAULT 1 AFTER `descripcion_consanguinidad`, ADD COLUMN `grado_de_afinidad` INT(2) NULL DEFAULT 0 AFTER `grado_de_consanguinidad`";
$sql["20141101"][]	= "ALTER TABLE`socios_relacionestipos` ADD COLUMN `tiene_vinculo_patrimonial` INT(4) NULL DEFAULT 0 COMMENT 'si mantiene algun vinculo patrimonial' AFTER `requiere_validacion`";


$sql["20141102"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('21101', 'Creditos. Purgar Primera Fecha de Abono', '')";
$sql["20141102"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('8201', 'Personas.AML Generar Perfil por Salario', '')";
$sql["20141102"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('8202', 'Personas.AML Actualizar Nivel de Riesgo', '')";

$sql["20141103"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/aud2-base-de-personas-morales.rpt.php?', 'AUD2.- Reporte de Personas Morales', 'aml', '5104', '', '40')";
$sql["20141103"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/aud2-base-de-personas-fisicas.rpt.php?', 'AUD2.- Reporte de Personas Fisicas', 'aml', '5105', '', '41')";

//============================================================================ 2014-11-04 Diana
$sql["20141104"][]	= "ALTER TABLE `socios_aeconomica_tipos` CHANGE COLUMN `idsocios_aeconomica_tipos` `idsocios_aeconomica_tipos` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' , CHANGE COLUMN `nombre_taeconomica` `nombre_taeconomica` VARCHAR(200) NOT NULL DEFAULT '' ";
$sql["20141104"][]	= "DELETE FROM `socios_aeconomica_tipos`";
$sql["20141104"][]	= "INSERT INTO `socios_aeconomica_tipos` (`idsocios_aeconomica_tipos`, `nombre_taeconomica`) SELECT `clave_interna`,`nombre_de_la_actividad` FROM `personas_actividad_economica_tipos`";
$sql["20141104"][]	= "UPDATE `socios_tipoingreso` SET `descripcion_detallada` = 'Personas Politicamente Expuestas' , `nivel_de_riesgo` = '100' WHERE `idsocios_tipoingreso` = '501'";
$sql["20141104"][]	= "UPDATE `socios_tipoingreso` SET `nivel_de_riesgo` = '100' WHERE `idsocios_tipoingreso` = '510'";
$sql["20141104"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('2026', '2020', 'Catalogo de Actividades Economicas', 'rptsocios/actividades.catalogo.rpt.php', '_blank', '', 'reporte', 'command', '2026')";

$sql["20141104"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Reporte de Operaciones Relevantes' , `aplica` = 'aml.legal' WHERE `idreport` = '5101'";
$sql["20141104"][]	= "UPDATE `general_reports` SET `aplica` = 'aml.legal' WHERE `idreport` = '5102' ";
$sql["20141104"][]	= "UPDATE `general_reports` SET `aplica` = 'aml.legal' WHERE `idreport` = '5103' ";
$sql["20141104"][]	= "ALTER TABLE `personas_actividad_economica_tipos` ADD COLUMN `nivel_de_riesgo` INT(4) NULL DEFAULT 1 COMMENT 'Nivel de Riesgo AML' AFTER `clave_de_superior`";

$sql["20141105"][]	= "ALTER TABLE `personas_actividad_economica_tipos` ADD COLUMN `califica_para_pep` INT(2) NULL DEFAULT 0 COMMENT 'asigna si es pep 1SI 0NO' AFTER `nivel_de_riesgo`";
$sql["20141105"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_type`, `menu_order`) VALUES ('188501', '18550', 'Carga de Actividades Economicas', 'install/actividades.upload.frm.php', 'tiny', '', 'command', '185501')";
$sql["20141105"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('71202', '71000', 'Reportes a Entidad Supervisora', 'rptpld/reportes-legales.frm.php', 'tiny', '', 'reportes', 'command', '2', '71202') ";
$sql["20141105"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/alertas-no-reportadas.rpt.php?', 'AL1.- Alertas Descartadas', 'aml', '5106', '', '42')";
$sql["20141105"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/alertas-pendientes.rpt.php?', 'AL1.- Alertas Pendientes de Dictamen', 'aml', '5107', '', '43')";
$sql["20141105"][]	= "ALTER TABLE `aml_alerts` ADD COLUMN `resultado_de_checking` INT(2) NULL DEFAULT 0 COMMENT '0Descartado 1ARiesgo' AFTER `tercero_relacionado`";
$sql["20141105"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/alertas-confirmadas.rpt.php?', 'AL1.- Alertas Confirmadas como Riesgo PLD', 'aml', '5108', '', '44')";
$sql["20141105"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/riesgos-reportados.rpt.php?', 'AL1.- Riesgos PLD Reportados a la Autoridad', 'aml', '5109', '', '45')";
$sql["20141105"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/riesgos-no-reportados.rpt.php?', 'AL1.- Riesgos PLD No Reportados a la Autoridad', 'aml', '5110', '', '46')";

$sql["20141106"][]	= "ALTER TABLE `personas_domicilios_paises` CHANGE `es_considerado_riesgo` `es_considerado_riesgo` INT(11) DEFAULT 0 NULL COMMENT 'nivel de riego'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'AF'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'AL'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'AO'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'AG'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'AR'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'BD'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'KH'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'CU'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'IQ'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'KW'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'KG'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'LA'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'NA'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'NP'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'NI'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'SD'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'TJ'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'VN'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'ZW'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'MN'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'MA'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'NG'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'DZ'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'EC'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'ET'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'ID'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'KE'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'MM'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'PK'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'SY'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'TZ'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'TR'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '50' WHERE `clave_de_control` = 'YE'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'KP'";
$sql["20141106"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'IR'";


$sql["20141107"][]	= "CREATE TABLE `personas_relaciones_recursivas` ( `clave_interna` INT NOT NULL AUTO_INCREMENT,  `persona` BIGINT(25) NULL,  `relacion` BIGINT(20) NULL,  `nivel` INT(4) NULL DEFAULT 1,  PRIMARY KEY (`clave_interna`), INDEX `personas` (`persona` ASC, `relacion` ASC))";

$sql["20141108"][]	= "INSERT INTO `tesoreria_tipos_de_pago` (`tipo_de_pago`, `tipo_de_movimiento`, `descripcion`, `descripcion_completa`) VALUES ('ninguno', '0', 'Ninguno', 'Operacion sin Flujo de Efectivo')";
$sql["20141108"][]	= "ALTER TABLE `personas_relaciones_recursivas` CHANGE COLUMN `relacion` `relacion` BIGINT(25) NULL DEFAULT NULL , ADD COLUMN `proxy` BIGINT(25) NULL AFTER `nivel`";
$sql["20141108"][]	= "ALTER TABLE `personas_relaciones_recursivas` ADD INDEX `todos` (`persona` ASC, `relacion` ASC, `proxy` ASC)";
$sql["20141108"][]	= "UPDATE `general_menu` SET `menu_file` = 'rptsocios/personas_en_baja.rpt.php' , `menu_destination` = '_blank' , `menu_image` = 'personas' WHERE `idgeneral_menu` = '2025'";
$sql["20141108"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reporte de Personas en Baja' WHERE `idgeneral_menu` = '2025'";
$sql["20141108"][]	= "UPDATE `general_menu` SET `menu_title` = 'Baja/Bloqueo de Personas' WHERE `idgeneral_menu` = '2007'";
$sql["20141108"][]	= "ALTER TABLE `socios_baja` ADD COLUMN `fecha_de_vencimiento` DATE NULL COMMENT 'fecha en que deja de tener efecto la baja' AFTER `fecha_de_documento`";
$sql["20141108"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('8203', 'Personas.AML Generar Arbol de Relaciones', '')";


$sql["20141109"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`, `describe_param_1`, `describe_param_2`) VALUES ('9001', 'Cambia una Sucursal a Otra', '', 'DE_SUCURSAL', 'A_SUCURSAL')";

$sql["20141110"][]	= "ALTER TABLE `contable_polizas_proforma` CHANGE COLUMN `idusuario` `idusuario` INT(6) NULL DEFAULT '99'";
$sql["20141110"][]	= "UPDATE general_menu SET menu_destination='tiny' WHERE menu_parent=5010";
$sql["20141111"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`, `describe_param_1`, `describe_param_2`) VALUES ('13002', 'Utilizar Localidades por Colonias', '', 'LIMPIAR COLONIAS SN', '')";

$sql["20141112"][]	= "INSERT INTO `eacp_config_bases_de_integracion` (`codigo_de_base`, `descripcion`, `tipo_de_base`) VALUES ('7022', 'OPERACIONES QUE SON BONIFICACIONES', 'de_operaciones')";
$sql["20141112"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('538', '7022', '801', '-1', 'BON INT MOR')";
$sql["20141112"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('539', '7022', '802', '-1', 'BON INTS')";
$sql["20141112"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('540', '7022', '803', '-1', 'BON VARIAS')";

$sql["20141113"][]	= "CREATE TABLE `personas_relaciones_recursivas` ( `clave_interna` INT NOT NULL AUTO_INCREMENT,  `persona` BIGINT(25) NULL,  `relacion` BIGINT(20) NULL,  `nivel` INT(4) NULL DEFAULT 1,  PRIMARY KEY (`clave_interna`), INDEX `personas` (`persona` ASC, `relacion` ASC))";
$sql["20141113"][]	= "ALTER TABLE `personas_relaciones_recursivas` CHANGE COLUMN `relacion` `relacion` BIGINT(25) NULL DEFAULT NULL , ADD COLUMN `proxy` BIGINT(25) NULL AFTER `nivel`";
$sql["20141113"][]	= "ALTER TABLE `personas_relaciones_recursivas` ADD INDEX `todos` (`persona` ASC, `relacion` ASC, `proxy` ASC)";
$sql["20141113"][]	= "UPDATE operaciones_recibos SET origen_aml=101 WHERE (tipo_pago='efectivo' OR tipo_pago='transferencia') AND origen_aml=0 OR origen_aml = NULL";
$sql["20141113"][]	= "DROP TABLE IF EXISTS`relaciones_recursivas`";

$sql["20141114"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('71103', '71000', 'Personas con Actividades de Alto Riesgo', 'rptpld/personas-con_actividades_riesgosas.rpt.php', '', 'reportes', 'command', '71103', '71103')";
$sql["20141114"][]	= "UPDATE `general_menu` SET `menu_destination` = '_blank' WHERE `idgeneral_menu` = '71103'";
$sql["20141114"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('71104', '71000', 'Personas que por su Actividad son PEP', 'rptpld/personas-con_actividades_pep.rpt.php', '_blank', '', 'reportes', 'command', '71104', '71104')";
$sql["20141114"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptempresas/empresas-saldo_por_fechas.rpt.php?', 'Saldos de Credito a una Fecha Determinada', 'empresas', '9105', '', '12')";

$sql["20141114"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('71105', '71000', 'Personas PEP y Relacionados', 'rptpld/personas-pep.rpt.php', '_blank', '', 'reportes', 'command', '71105', '71105')";
$sql["20141114"][]	= "INSERT INTO `tesoreria_monedas` (`clave_de_moneda`, `nombre_de_la_moneda`, `quivalencia_en_moneda_local`, `pais_de_origen`) VALUES ('IRR', 'RIAL IRANI', '1.75', 'IR')";
$sql["20141114"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('71106', '71000', 'Lista de Propietarios Reales', 'rptpld/personas-propietarios_reales.rpt.php', '_blank', '', 'reportes', 'command', '71106', '71106')";
$sql["20141114"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('71107', '71000', 'Lista de Proveedores de Recursos', 'rptpld/personas-proveedores_de_recursos.rpt.php', '_blank', '', 'reportes', 'command', '71107', '71107')";

$sql["20141115"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('72203', '72000', 'Carga de Personas PEP Federales', 'frmpld/pep-federales.upload.frm.php', '_tiny', '', 'export', 'command', '72203', '72203')";

$sql["20141116"][]	= "UPDATE socios_general SET nivel_de_riesgo_aml=10 WHERE nivel_de_riesgo_aml <=10";
$sql["20141116"][]	= "UPDATE socios_general SET lugarnacimiento=\"\" WHERE TRIM(lugarnacimiento) =\",\" ";
$sql["20141116"][]	= "";
$sql["20141116"][]	= "ALTER TABLE `tesoreria_tipos_de_pago` ADD COLUMN `equivalente_aml` INT(5) NULL DEFAULT 0 AFTER `descripcion_completa`";
$sql["20141116"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('2058', '2050', 'Catalogo de Paises', 'frmsocios/catalogo_paises.grid.php', 'tiny', '', 'pais', 'command', '2057', '2057')";
$sql["20141116"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `equivalente_aml` = '101' WHERE `tipo_de_pago` = 'efectivo'";
$sql["20141116"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `equivalente_aml` = '151' WHERE `tipo_de_pago` = 'efectivo.egreso'";
$sql["20141116"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `equivalente_aml` = '401' WHERE `tipo_de_pago` = 'cheque'";
$sql["20141116"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `equivalente_aml` = '101' WHERE `tipo_de_pago` = 'cheque.ingreso'";
$sql["20141116"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `equivalente_aml` = '401' WHERE `tipo_de_pago` = 'foraneo'";
$sql["20141116"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `equivalente_aml` = '201' WHERE `tipo_de_pago` = 'transferencia'";
$sql["20141116"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `equivalente_aml` = '201' WHERE `tipo_de_pago` = 'transferencia.egreso'";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Reportadas. Superiores a 10000 USD  Mensual' , `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '912100'";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Reportadas. Superiores A 50,000 USD  Mensual' , `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '912101' ";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Reportadas. Exhibiciones Individuales  mayores a 500 USD' , `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '912102'";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Reportadas. Personas Fisicas Superiores a 300,000 Mensual' , `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '912201'";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Reportadas. Personas Morales superiores a 500,000 Mensual' , `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '912202'";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Fraccionadas.- Operaciones Acumuladas superiores a 100 000 MXN' , `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '912301'";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Fraccionadas.- Operaciones Acumuladas superiores a 1000000 MXN' , `tipo_de_riesgo` = '911' WHERE `clave_de_control` = '912302'";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET forma_de_reportar='C' WHERE tipo_de_riesgo= 911";
$sql["20141116"][]	= "INSERT INTO `aml_risk_catalog` (`clave_de_control`, `descripcion`, `tipo_de_riesgo`, `valor_ponderado`, `unidades_ponderadas`, `unidad_de_medida`, `forma_de_reportar`, `frecuencia_de_chequeo`, `fundamento_legal`) VALUES ('910000', 'Operaciones Relevantes Reportadas', '912', '100', '100000', 'USD', 'I', 'I', '')";
$sql["20141116"][]	= "DELETE FROM `entidad_configuracion` WHERE `nombre_del_parametro` = 'monto_minimo_para_reportar_operaciones'";
$sql["20141116"][]	= "CREATE TABLE IF NOT EXISTS `tesoreria_valoracion_dolar` (  `idcontrol` INT NOT NULL AUTO_INCREMENT, `fecha_de_existencia` DATE NULL,  `valor` FLOAT(12,6) NULL,  `fecha_de_carga` DATE NULL,  PRIMARY KEY (`idcontrol`)) ENGINE = InnoDB";
$sql["20141116"][]	= "CREATE TABLE IF NOT EXISTS `tesoreria_valoracion_udi` (  `idcontrol` INT NOT NULL AUTO_INCREMENT,  `fecha_de_existencia` DATE NULL,  `valor` FLOAT(12,6) NULL,  `fecha_de_carga` DATE NULL,  PRIMARY KEY (`idcontrol`)) ENGINE = InnoDB";
$sql["20141116"][]	= "ALTER TABLE `aml_risk_register` ADD COLUMN `reporte_inmediato` INT(3) NULL COMMENT '0 false 1 true' AFTER `mensajes_del_sistema`";
$sql["20141116"][]	= "UPDATE `aml_risk_register` SET reporte_inmediato=0";
$sql["20141116"][]	= "UPDATE `aml_risk_catalog` SET `unidades_ponderadas` = '10000' WHERE `clave_de_control` = '910000'";
$sql["20141116"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<div style=\"text-align: left;\">\r\n<hr /><span style=\"font-family:courier new,courier,monospace\">Fecha:</span>variable_fecha_larga_actual</div>\r\n\r\n<div style=\"text-align: left;\"><span style=\"font-family:courier new,courier,monospace\">Hora:</span>variable_hora_actual</div>\r\n\r\n<div style=\"text-align: left;\"><span style=\"font-family:courier new,courier,monospace\">Ciudad:</span>variable_ciudad_de_la_entidad</div>\r\n\r\n<div style=\"text-align: left;\"><span style=\"font-family:courier new,courier,monospace\">C. Oficial de Cumplimento:</span>variable_oficial</div>\r\n\r\n<div style=\"text-align: left;\">&nbsp;</div>\r\n\r\n<h2 style=\"text-align: center;\"><span style=\"font-family:courier new,courier,monospace\"><strong>Alerta de Posible Riesgo AML</strong></span></h2>\r\n\r\n<div style=\"text-align: right;\">\r\n<hr /><span style=\"font-family:courier new,courier,monospace\">Numero de Persona Involucrada:</span>variable_persona_nombre_completo</div>\r\n\r\n<div style=\"text-align: right;\"><span style=\"font-family:courier new,courier,monospace\">Numero de Documento Involucrado:</span>variable_documento_codigo</div>\r\n\r\n<hr />\r\n<p style=\"text-align: right;\"><span style=\"font-family:courier new,courier,monospace\">Fecha de Generaci&oacute;n:</span>variable_docto_fecha</p>\r\n\r\n<div style=\"text-align: right;\"><span style=\"font-family:courier new,courier,monospace\">Hora de Generaci&oacute;n:</span>variable_docto_hora</div>\r\n\r\n<div style=\"text-align: right;\"><span style=\"font-family:courier new,courier,monospace\">Nivel de Riesgo ponderado del Sistema: </span>variable_nivel_de_riesgo%</div>\r\n\r\n<div style=\"text-align: right;\"><span style=\"font-family:courier new,courier,monospace\">Tipo de Riesgo:</span>variable_tipo_de_riesgo</div>\r\n\r\n<div style=\"text-align: right;\"><span style=\"font-family:courier new,courier,monospace\">Clasificaci&oacute;n:</span>variable_clasificacion_de_riesgo</div>\r\n\r\n<div style=\"text-align: right;\"><span style=\"font-family:courier new,courier,monospace\">V&iacute;nculo de Alerta en el Sistema: &nbsp;<a href=\"variable_url_del_sistemafrmpld/estatus_de_alerta.frm.php?remote=true&amp;id=variable_codigo_de_alerta\">variable_codigo_de_alerta</a></span></div>\r\n\r\n<hr />\r\n<p style=\"text-align: right;\"><strong><span style=\"font-family:courier new,courier,monospace\">Notas:</span></strong></p>\r\n\r\n<hr />\r\n<p><span style=\"font-family:courier new,courier,monospace\">variable_mensaje_de_alerta</span></p>\r\n\r\n<hr />\r\n<div style=\"text-align: justify;\"><span style=\"font-family:courier new,courier,monospace\"><em>Este es un Aviso Automatizado por el Sistema de Alertas de SAFE-OSMS, como posible riesgo marcado, en ning&uacute;n caso se considera como un riesgo definitivo por lo que se necesita de su intervenci&oacute;n para verificarlo.</em></span></div>\r\n\r\n<hr />\r\n<p><span style=\"font-family:courier new,courier,monospace\">Sistema de Alertas.- SAFE-OSMS</span><br />\r\n<span style=\"font-family:courier new,courier,monospace\">C&oacute;digo de rastreo: variable_codigo_de_alerta</span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n' WHERE `idgeneral_contratos` = '800'";
$sql["20141116"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`) VALUES ('../rptpld/operaciones_reporte24horas.rpt.php?', 'Reporte de Operaciones 24 Horas', 'aml.legal', '5111', '')";
$sql["20141116"][]	= "CREATE TABLE IF NOT EXISTS `aml_tipos_de_operacion` (`tipo_de_operacion_aml` INT NOT NULL, `nombre_de_la_operacion` VARCHAR(50) NULL, `descripcion` VARCHAR(200) NULL, PRIMARY KEY (`tipo_de_operacion_aml`)) ENGINE = InnoDB COMMENT = 'Tipos de operacion segun catalogo UIF' ";
$sql["20141116"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('8', 'Otorgamiento de Credito', '')";
$sql["20141116"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('9', 'Pago de Credito', '')";
$sql["20141116"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('27', 'Pago de rentas Arrendamiento Financiero', '')";
$sql["20141116"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('40', 'Dispocision de Credito', '')";
$sql["20141116"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('41', 'Liquidacion de Credito', 'Se deberá utilizar cuando el credito sea pagado en su totalidad, ya sea como último pago o como una operación de prepago')";
$sql["20141116"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('45', 'Pago de Servicios', '')";
$sql["20141116"][]	= "CREATE TABLE IF NOT EXISTS`aml_instrumentos_financieros` (  `tipo_de_instrumento` INT NOT NULL,  `nombre_de_instrumento` VARCHAR(50) NULL,   `descripcion` VARCHAR(200) NULL,   PRIMARY KEY (`tipo_de_instrumento`)) ENGINE = InnoDB COMMENT = 'Catalogo de Instrumentos segun la UIF'";
$sql["20141116"][]	= "INSERT INTO `aml_instrumentos_financieros` (`tipo_de_instrumento`, `nombre_de_instrumento`, `descripcion`) VALUES ('01', 'EFECTIVO', '')";
$sql["20141116"][]	= "INSERT INTO `aml_instrumentos_financieros` (`tipo_de_instrumento`, `nombre_de_instrumento`, `descripcion`) VALUES ('3', 'Transferencias', '')";
$sql["20141116"][]	= "INSERT INTO `aml_instrumentos_financieros` (`tipo_de_instrumento`, `nombre_de_instrumento`, `descripcion`) VALUES ('10', 'Cheques', '')";
$sql["20141116"][]	= "INSERT INTO `aml_instrumentos_financieros` (`tipo_de_instrumento`, `nombre_de_instrumento`, `descripcion`) VALUES ('11', 'Bienes', 'Se deberá utilizar cuando se utilice cualquier mercancia ó bien como medio de pago')";
$sql["20141116"][]	= "INSERT INTO `aml_instrumentos_financieros` (`tipo_de_instrumento`, `nombre_de_instrumento`, `descripcion`) VALUES ('8', 'Derechos', 'Se deberá utilizar cuando se otorguen derechos como medio de pago')";

$sql["20150102"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8802', '80', 'Acuse de Consulta en Lista PEP', '<!-- Consulta PEP -->\r\n<h1 style=\"text-align: center;\">S.A.F.E. Open Source Microfinance System.</h1>\r\n\r\n<h3 style=\"text-align: center;\">IFAI Buscador de Persona</h3>\r\n\r\n<hr />\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\nvariable_listado_de_cedulas\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<hr />\r\n<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" style=\"height:132px; width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td>Fecha de Carga:</td>\r\n   <td>&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Sitio original</td>\r\n   <td><a href=\"http://portaltransparencia.gob.mx/pot/fichaOpenData.do?method=fichaOpenData&fraccion=directorio\">Datos Abiertos Portal de Transparencia</a></td>\r\n  </tr>\r\n  <tr>\r\n   <td>Total Registros:</td>\r\n   <td>5819</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Responsable:</td>\r\n   <td>http://www.opencorebanking.com/</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Contacto:</td>\r\n   <td>admin@opencorebanking.com</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n<p>&nbsp;</p>')";
$sql["20150102"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- Consulta PEP -->\r\n<h1 style=\"text-align:center\">S.A.F.E. Open Source Microfinance System.</h1>\r\n\r\n<h3 style=\"text-align: center;\">IFAI Buscador de Persona con posibilidad de PEP</h3>\r\n\r\n<hr />\r\n<table style=\"width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td>Cadena buscada (Nombres/Primer Apellido/Segundo Apellido)</td>\r\n   <td>variable_item_buscado</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n\r\n\r\n<h2>Personas encontradas con coincidencias</h2>\r\n\r\n<p>variable_listado_de_cedulas</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<hr />\r\n<table style=\"width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td>Fecha de Carga:</td>\r\n   <td>&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Sitio original</td>\r\n   <td><a href=\"http://portaltransparencia.gob.mx/pot/fichaOpenData.do?method=fichaOpenData&fraccion=directorio\">Datos Abiertos Portal de Transparencia</a></td>\r\n  </tr>\r\n  <tr>\r\n   <td>Total Registros:</td>\r\n   <td>ND</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Responsable:</td>\r\n   <td>http://www.opencorebanking.com/</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Contacto:</td>\r\n   <td>admin@opencorebanking.com</td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\'2\'>Cadena de consulta</td>\r\n  <tr>\r\n<td colspan=\'2\'>variable_cadena_consulta</td>\r\n  </tr>\r\n\r\n </tbody>\r\n</table>\r\n\r\n<p>&nbsp;</p>' WHERE `idgeneral_contratos` = '8802'";
$sql["20150102"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<!-- Consulta OFAC -->\r\n<h1 style=\"text-align:center\">S.A.F.E. Open Source Microfinance System.</h1>\r\n\r\n<h3 style=\"text-align:center\">SDN/OFAC B&uacute;squeda de Personas &quot;<strong>Specially Designated Nationals List</strong>&quot;.</h3>\r\n\r\n<hr />\r\n<table style=\"width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td>Cadena buscada (Nombres/Primer Apellido/Segundo Apellido)</td>\r\n   <td>variable_item_buscado</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n\r\n<h2>Personas encontradas con coincidencias</h2>\r\n\r\n<p>variable_listado_de_cedulas</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n\r\n<hr />\r\n<table style=\"width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <td>Fecha de Carga:</td>\r\n   <td>&nbsp;</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Fecha de Publicaci&oacute;n:</td>\r\n   <td>2014/10/31</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Total Registros:</td>\r\n   <td>5819</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Responsable:</td>\r\n   <td>http://www.opencorebanking.com/</td>\r\n  </tr>\r\n  <tr>\r\n   <td>Contacto:</td>\r\n   <td>admin@opencorebanking.com</td>\r\n  </tr>\r\n  <tr>\r\n   <td colspan=\'2\'>Cadena de consulta</td>\r\n  <tr>\r\n<td colspan=\'2\'>variable_cadena_consulta</td>\r\n  </tr>\r\n\r\n </tbody>\r\n</table>\r\n\r\n<p>&nbsp;</p>' WHERE `idgeneral_contratos` = '8801'";

$sql["20150103"][]	= "ALTER TABLE `operaciones_recibos` ADD COLUMN `periodo_de_documento` INT(4) NULL DEFAULT 0 COMMENT 'Parcialidad de credito, numero de reinversión, etc' AFTER `fecha_de_registro`";

$sql["20150104"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rpt_edos_cuenta/rptestadocuentacredito.php?' , `explicacion` = '' WHERE `idreport` = '1'";
$sql["20150104"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rpt_edos_cuenta/rptedoctacredito.php?' , `descripcion_reports` = 'Estado de Cuenta con movimientos estadisticos' , `explicacion` = '' , `order_index` = '120' WHERE `idreport` = '2'";
$sql["20150104"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcreditos/rpt_intereses_devengados.php?' , `explicacion` = '' , `order_index` = '50' WHERE `idreport` = '116'";
$sql["20150104"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcreditos/estado_de_cuenta_de_intereses.rpt.php?' , `explicacion` = '' , `order_index` = '20' WHERE `idreport` = '118'";
$sql["20150104"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rpt_edos_cuenta/historial_de_saldos.rpt.php?' , `explicacion` = '' , `order_index` = '15' WHERE `idreport` = '141'";
$sql["20150104"][]	= "UPDATE creditos_solicitud SET dias_autorizados=plazo_en_dias WHERE periocidad_de_pago=360 AND saldo_actual >0 AND dias_autorizados >0";
$sql["20150104"][]	= "UPDATE creditos_solicitud SET fecha_vencimiento = DATE_ADD(fecha_ministracion, INTERVAL dias_autorizados DAY) WHERE periocidad_de_pago=360 AND saldo_actual >0 AND dias_autorizados >0";

$sql["20150106"][]	= "CREATE TABLE IF NOT EXISTS `creditos_destino_detallado` (  `idcreditos_destino_detallado` INT NOT NULL,  `clave_de_presupuesto` BIGINT(25) NULL COMMENT 'entero del día en que se hizo el presupuesto',  `clave_de_persona` BIGINT(25) NULL,  `clave_de_empresa` BIGINT(25) NULL COMMENT 'clave de empresa o proveedor',  `clave_de_destino` INT NULL COMMENT 'destino aplicacion del recurso',  `fecha_de_pago` DATE NULL,  `monto` FLOAT(16,3) NULL DEFAULT 0,  `observaciones` VARCHAR(100) NULL,  PRIMARY KEY (`idcreditos_destino_detallado`)) ENGINE = InnoDB";

$sql["20150107"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1100', '10', 'Formato de Solicitud de Credito', '<fieldset>\r\n<table>\r\n <tbody>\r\n  <tr>\r\n   <th class=\"der\" width=\"25%\">Lugar</th>\r\n   <td width=\"75%\">variable_lugar_actual</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\"der\">Fecha Actual</th>\r\n   <td>variable_fecha_larga_actual</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\"der\">Fecha de Solicitud</th>\r\n   <td>variable_fecha_de_solicitud</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n</fieldset>\r\n<!-- Ficha de Socio -->\r\nvariable_informacion_del_socio\r\n\r\n<!-- Info del Credito -->\r\nvariable_informacion_extendida_del_credito\r\n<!-- Avales -->\r\nvariable_lista_de_avales_con_domicilio\r\n<!-- garantias -->\r\nvariable_listado_de_garantias\r\n\r\n<!-- Relaciones de la Persona -->\r\n\r\n<!-- Relacion Patrimonial -->\r\nvariable_persona_lista_de_bienes\r\n<!-- Flujo de efectivo -->\r\nvariable_credito_estado_flujo_efectivo\r\n')";

$sql["20150108"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<table class=\'firma\'>\r\n <tbody>\r\n  <tr>\r\n   <th class=\'izq\'>NOMBRE</th>\r\n   <td>aval_nombre_completo</td>\r\n   <th class=\'izq\'>LOCALIDAD :</th>\r\n   <td>aval_domicilio_localidad</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\'izq\'>DIRECCION :</th>\r\n   <td>aval_direccion_calle_y_numero</td>\r\n   <th class=\'izq\'>ESTADO :</th>\r\n   <td>aval_direccion_estado</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\'izq\'>OCUPACION :</th>\r\n   <td>aval_ocupacion</td>\r\n   <th class=\'izq\'>FECHA DE NACIMIENTO :</th>\r\n   <td>aval_fecha_de_nacimiento</td>\r\n  </tr>\r\n  <tr>\r\n   <th  class=\'izq\'>variable_nombre_id_fiscal:</th>\r\n   <td>aval_id_fiscal</td>\r\n   <th class=\'izq\'>LUGAR DE NACIMIENTO :</th>\r\n   <td>aval_lugar_de_nacimiento</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\'izq\'>EMPRESA:</th>\r\n   <td>aval_empresa_de_trabajo</td>\r\n   <th class=\'izq\'>ESTADO CIVIL:</th>\r\n   <td>aval_estado_civil</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\'izq\'>TIPO DE RELACION:</th>\r\n   <td>aval_tipo_de_relacion</td>\r\n   <th class=\'izq\'>PARENTESCO :</th>\r\n   <td>aval_tipo_de_parentesco</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\'izq\'>PORCENTAJE AVALADO :</th>\r\n   <td>aval_porcentaje_relacionado</td>\r\n   <td>&nbsp;</td>\r\n   <td>&nbsp;</td>\r\n  </tr>\r\n    </tbody>\r\n</table>\r\n<br/>' WHERE `idgeneral_contratos` = '8001'";

$sql["20150109"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('300501', '3005', 'Presupuesto por Aplicacion', 'frmcreditos/presupuesto-de-credito.frm.php', 'tiny', '', 'destino', 'command', '300501', '300501')";
$sql["20150109"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('3005', '3000', 'Presupuesto de Creditos', '', 'principal', '', 'presupuesto', 'parent', '3005', '3005')";
$sql["20150109"][]	= "ALTER TABLE `creditos_destino_detallado` CHANGE COLUMN `clave_de_persona` `clave_de_persona` BIGINT(25) NULL DEFAULT 0 , CHANGE COLUMN `clave_de_empresa` `clave_de_empresa` BIGINT(25) NULL DEFAULT 0 COMMENT 'clave de empresa o proveedor' , CHANGE COLUMN `clave_de_destino` `clave_de_destino` INT(11) NULL DEFAULT 0 COMMENT 'destino aplicacion del recurso' , CHANGE COLUMN `fecha_de_pago` `fecha_de_pago` DATE NULL DEFAULT 0 , ADD COLUMN `sucursal` VARCHAR(20) NULL DEFAULT 'matriz' AFTER `observaciones`, ADD COLUMN `idusuario` INT NULL DEFAULT 1 AFTER `sucursal`";
$sql["20150109"][]	= "ALTER TABLE `creditos_destino_detallado` CHANGE COLUMN `idcreditos_destino_detallado` `idcreditos_destino_detallado` INT(11) NOT NULL AUTO_INCREMENT ";

$sql["20150110"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('3005', '3000', 'Presupuesto de Creditos', '', 'principal', '', 'presupuesto', 'parent', '3005', '3005')";
$sql["20150110"][]	= "ALTER TABLE `seguimiento_compromisos` CHANGE `estatus_compromiso` `estatus_compromiso` VARCHAR(20) CHARSET utf8 COLLATE utf8_general_ci DEFAULT 'pendiente' NULL COMMENT 'pendiente cumplido no_cumplido cancelado'";

$sql["20150111"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<fieldset>\r\n<table>\r\n <tbody>\r\n  <tr>\r\n   <th class=\"der\" width=\"25%\">Lugar</th>\r\n   <td width=\"75%\">variable_lugar_actual</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\"der\">Fecha Actual</th>\r\n   <td>variable_fecha_larga_actual</td>\r\n  </tr>\r\n  <tr>\r\n   <th class=\"der\">Fecha de Solicitud</th>\r\n   <td>variable_fecha_de_solicitud</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n</fieldset>\r\n<!-- Ficha de Socio -->\r\nvariable_informacion_del_socio\r\n\r\n<!-- Info del Credito -->\r\nvariable_informacion_extendida_del_credito\r\n<!-- Avales -->\r\nvariable_listado_de_avales\r\n<!-- garantias -->\r\nvariable_listado_de_garantias\r\n\r\n<!-- Relaciones de la Persona -->\r\n\r\n<!-- Relacion Patrimonial -->\r\nvariable_persona_lista_de_bienes\r\n<!-- Flujo de efectivo -->\r\nvariable_credito_estado_flujo_efectivo\r\n' WHERE `idgeneral_contratos` = '1100'";
$sql["20150111"][]	= "INSERT INTO `creditos_destinos` (`idcreditos_destinos`, `descripcion_destinos`, `destino_credito`, `tasa_de_iva`) VALUES ('98', 'DESTINO DETALLADO', '98', '0.16')";

$sql["20150111"][]	= "ALTER TABLE `socios_aeconomica_dependencias`  ADD COLUMN `comision_por_encargo` FLOAT(12,6) NULL DEFAULT 0 COMMENT 'Porcentaje de la comision de cobrar por los creditos u operaciones ' AFTER `dias_de_liquidacion`";
$sql["20150111"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('1075', '1070', 'Retiro de Empresas', 'frmcaja/empresas-pagos.frm.php', 'Modulo de retiros de Empresas', 'dinero', 'command', '1075', '1075')";
$sql["20150111"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `operacion_origen` BIGINT(25) NULL DEFAULT 1 COMMENT 'Credito, recibo, linea de credito, etc, etc que vincula al credito' AFTER `fecha_de_primer_pago`, ADD COLUMN `tipo_de_origen` INT NULL COMMENT '1  ninguno usar iDE_CREDITO iDE_CAPATCIOn etc' AFTER `operacion_origen`";
$sql["20150111"][]	= "CREATE TABLE IF NOT EXISTS `personas_consulta_centralriesgos` (`clave_interna` INT NOT NULL AUTO_INCREMENT, `persona` BIGINT(25) NULL, `fecha_de_consulta` DATE NULL DEFAULT '2015-01-01', `fecha_de_vencimiento` DATE NULL DEFAULT '2015-01-01', `calificacion` FLOAT(16,6) NULL DEFAULT 0, `observaciones` VARCHAR(100) NULL, `sucursal` VARCHAR(20) NULL DEFAULT 'matriz', `idusuario` INT NULL DEFAULT 1, PRIMARY KEY (`clave_interna`)) ENGINE = InnoDB";
$sql["20150111"][]	= "CREATE TABLE IF NOT EXISTS `creditos_presupuestos` (`clave_de_presupuesto` INT NOT NULL AUTO_INCREMENT, `clave_de_persona` BIGINT(25) NULL DEFAULT 1, `fecha_de_elaboracion` DATE NULL, `total_presupuesto` FLOAT(12,2) NULL DEFAULT 0, `sucursal` VARCHAR(20) NULL DEFAULT 'matriz', `idusuario` INT NULL DEFAULT 1, PRIMARY KEY (`clave_de_presupuesto`)) ENGINE = InnoDB";

$sql["20150112"][]	= "ALTER TABLE  `creditos_presupuestos` ADD COLUMN `estado_actual` INT(2) NULL DEFAULT 0 COMMENT '1 Aplicado 0 Pendiente' AFTER `idusuario`";
$sql["20150112"][]	= "ALTER TABLE `creditos_presupuestos` ADD COLUMN `notas` VARCHAR(100) NULL AFTER `estado_actual`";

$sql["20150113"][]	= "ALTER TABLE `creditos_destino_detallado` ADD COLUMN `estado_actual` INT(2) NULL DEFAULT 0 COMMENT '0 pendiente de Pago 1 Pagado' AFTER `idusuario`, ADD COLUMN `credito_vinculado` BIGINT(25) NULL DEFAULT 1 COMMENT 'Credito en el que fue destinado el presupuesto' AFTER `estado_actual`";
$sql["20150113"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Domicilio' WHERE `idsistema_lenguaje` = '92'";
$sql["20150113"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Address data' WHERE `idsistema_lenguaje` = '434'";

$sql["20150114"][]	= "ALTER TABLE `creditos_destino_detallado` ADD COLUMN `cheque_de_pago` BIGINT(12) NULL DEFAULT 0 AFTER `credito_vinculado`, ADD COLUMN `notas_del_pago` VARCHAR(50) NULL AFTER `cheque_de_pago`";
$sql["20150114"][]	= "ALTER TABLE `bancos_operaciones` CHANGE COLUMN `numero_de_documento` `numero_de_documento` VARCHAR(20) NULL DEFAULT '0' COMMENT 'Numero de Cheque, referencia Bancaria, etc' ";

$sql["20150115"][]	= "ALTER TABLE `general_colonias` CHANGE `estado_colonia` `estado_colonia` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NULL, CHANGE `ciudad_colonia` `ciudad_colonia` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NULL, CHANGE `municipio_colonia` `municipio_colonia` VARCHAR(100) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NULL, ENGINE=INNODB";

$sql["20150116"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('100', '10', 'Solicitud de Ingreso', '')";
$sql["20150116"][]	= "ALTER TABLE `creditos_destino_detallado` ADD COLUMN `cheque_de_pago` BIGINT(12) NULL DEFAULT 0 AFTER `credito_vinculado`, ADD COLUMN `notas_del_pago` VARCHAR(50) NULL AFTER `cheque_de_pago`";
$sql["20150116"][]	= "ALTER TABLE `bancos_operaciones` CHANGE COLUMN `numero_de_documento` `numero_de_documento` VARCHAR(20) NULL DEFAULT '0' COMMENT 'Numero de Cheque, referencia Bancaria, etc' ";

$sql["20150117"][]	= "ALTER TABLE `tesoreria_cajas_movimientos` CHANGE COLUMN `codigo_de_caja` `codigo_de_caja` VARCHAR(100) NULL DEFAULT '' COMMENT 'Clave de caja', CHANGE COLUMN `idusuario` `idusuario` INT(10) NULL DEFAULT 1 COMMENT 'Clave de cajero' , CHANGE COLUMN `documento` `documento` BIGINT(20) NULL DEFAULT 0 COMMENT 'Numero de credito o cuenta Bancaria' , CHANGE COLUMN `recibo` `recibo` BIGINT(25) NULL DEFAULT 0 COMMENT 'recibo de origen', CHANGE COLUMN `tipo_de_movimiento` `tipo_de_movimiento` INT(4) NULL DEFAULT 0 COMMENT '1 ingreso -1 egreso 0 ninguno' , CHANGE COLUMN `tipo_de_exposicion` `tipo_de_exposicion` VARCHAR(25) NULL DEFAULT 'ninguno' COMMENT 'efectivo multiple' , CHANGE COLUMN `monto_del_movimiento` `monto_del_movimiento` FLOAT(12,2) NULL DEFAULT 0 COMMENT 'Monto original' , CHANGE COLUMN `monto_recibido` `monto_recibido` FLOAT(12,2) NULL DEFAULT 0 , CHANGE COLUMN `monto_en_cambio` `monto_en_cambio` FLOAT(12,2) NULL DEFAULT 0 , CHANGE COLUMN `banco` `banco` INT(4) NULL DEFAULT '999' COMMENT 'banco de origen, si aplica' , CHANGE COLUMN `numero_de_cheque` `numero_de_cheque` VARCHAR(20) NULL DEFAULT '0' COMMENT 'Numero de cheque si aplica' , CHANGE COLUMN `sucursal` `sucursal` VARCHAR(20) NULL DEFAULT 'matriz' , CHANGE COLUMN `documento_descontado` `documento_descontado` BIGINT(20) NULL DEFAULT '0' COMMENT 'En los casos de Creditos o Cuenta de Captacion de origen' , CHANGE COLUMN `unidades_de_moneda` `unidades_de_moneda` FLOAT(16,4) NULL DEFAULT '0.0000' COMMENT 'Unidades originales de la moneda' ";
$sql["20150117"][]	= "ALTER TABLE `tesoreria_cajas_movimientos` ADD INDEX `recibo` (`recibo` ASC, `idusuario` ASC), ADD INDEX `idpersona` (`persona` ASC, `recibo` ASC, `idusuario` ASC, `codigo_de_caja` ASC)";
$sql["20150117"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `descripcion` = 'Efectivo.- Ingreso' WHERE `tipo_de_pago` = 'efectivo' ";
$sql["20150117"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `descripcion` = 'Efectivo.- Egreso' WHERE `tipo_de_pago` = 'efectivo.egreso'";

$sql["20150117"][]	= "CREATE TABLE IF NOT EXISTS `aml_personas_descartadas` (`idaml_personas_descartadas` INT NOT NULL AUTO_INCREMENT, `clave_de_persona` BIGINT(20) NULL DEFAULT 1, `clave_de_oficial` INT(6) NULL DEFAULT 0, `clave_de_motivo` INT(4) NULL DEFAULT 1, `fecha_de_captura` DATE NULL, `fecha_de_vencimiento` DATE NULL, `descripcion_del_motivo` VARCHAR(100) NULL, PRIMARY KEY (`idaml_personas_descartadas`), INDEX `personas` (`clave_de_persona` ASC, `clave_de_oficial` ASC)) ENGINE = InnoDB";
$sql["20150117"][]	= "ALTER TABLE `aml_personas_descartadas` CHANGE COLUMN `descripcion_del_motivo` `descripcion_del_motivo` TEXT NULL DEFAULT NULL ";

$sql["20150118"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('70021', '700', 'Poliza de Cheque', '')";
$sql["20150118"][]	= "UPDATE `general_menu` SET `menu_file` = 'rptsecurity/usuarios.rpt.php' , `menu_description` = '' , `menu_image` = 'reporte' WHERE `idgeneral_menu` = '10022'";

$sql["20150302"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Acuse de Entrega de Garantia' , `texto_del_contrato` = '<!-- contenido -->\r\n<h1>Resguardo</h1>\r\nvariable_credito_garantiareal1_ficha' WHERE `idgeneral_contratos` = '152'";
$sql["20150302"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '188511'";
$sql["20150302"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '349'";
$sql["20150302"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '350' ";
$sql["20150302"][]	= "UPDATE `general_menu` SET `menu_parent` = '1050' , `menu_file` = 'frmoperaciones/tipos_de_recibo.frm.php' , `menu_image` = 'recibos' , `menu_order` = '1504' WHERE `idgeneral_menu` = '11203'";
$sql["20150302"][]	= "ALTER TABLE `contable_polizas_perfil` CHANGE COLUMN `tipo_de_recibo` `tipo_de_recibo` INT(11) NOT NULL DEFAULT 0 ,CHANGE COLUMN `tipo_de_operacion` `tipo_de_operacion` INT(11) NOT NULL , CHANGE COLUMN `operacion` `operacion` INT(11) NULL DEFAULT 0 , ADD COLUMN `formula_posterior` TEXT NULL DEFAULT '' COMMENT 'Formula post modificatoria' AFTER `operacion`";
$sql["20150302"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmcontabilidad/centro_de_costos.grid.php' WHERE `idgeneral_menu` = '5081'";
$sql["20150302"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcontables/rpt_intereses_devengados_mensual.php?', `explicacion` = '' WHERE `idreport` = '39' ";
$sql["20150302"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcontables/rpt_cedula_informe_mensual_x_estatus.php?', `explicacion` = '' WHERE `idreport` = '40'";
$sql["20150302"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcontables/rpt_cedula_informe_mensual_x_estatus_caja.php?', `explicacion` = '' WHERE `idreport` = '44'";
$sql["20150302"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '40' ";
$sql["20150302"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '44' ";
$sql["20150302"][]	= "DELETE FROM operaciones_mvtos WHERE tipo_operacion=420 AND (SELECT COUNT(*) FROM creditos_solicitud WHERE numero_solicitud = operaciones_mvtos.docto_afectado AND estatus_actual >= 98) > 0 ";


$sql["20150303"][]	= "CREATE TABLE IF NOT EXISTS `creditos_destino_detallado` (  `idcreditos_destino_detallado` INT NOT NULL,  `clave_de_presupuesto` BIGINT(25) NULL COMMENT 'entero del día en que se hizo el presupuesto',  `clave_de_persona` BIGINT(25) NULL,  `clave_de_empresa` BIGINT(25) NULL COMMENT 'clave de empresa o proveedor',  `clave_de_destino` INT NULL COMMENT 'destino aplicacion del recurso',  `fecha_de_pago` DATE NULL,  `monto` FLOAT(16,3) NULL DEFAULT 0,  `observaciones` VARCHAR(100) NULL,  PRIMARY KEY (`idcreditos_destino_detallado`)) ENGINE = InnoDB";
$sql["20150303"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '5091'";
$sql["20150303"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '5093'";
$sql["20150303"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '5099'";
$sql["20150303"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '5094'";
$sql["20150303"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '5096' ";
$sql["20150303"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '5097' ";
$sql["20150303"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptseguimiento/ingresos-por-oficial.rpt.php?', 'Reporte de Ingresos por Oficial', 'operaciones_por_oficial', '4001', '', '1')";
$sql["20150303"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reporte de Operaciones' WHERE `idgeneral_menu` = '18412'";
$sql["20150303"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes de Colocacion' , `menu_order` = '1' WHERE `idgeneral_menu` = '18410'";
$sql["20150303"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes por Oficial' , `menu_description` = '' , `menu_image` = 'oficial' , `menu_help_id` = '18400' WHERE `idgeneral_menu` = '18400'";

$sql["20150304"][]	= "DELETE FROM general_log WHERE text_log LIKE '%UPDATE operaciones_mvtos%' ";

$sql["20150401"][]	= "UPDATE `general_menu` SET `menu_title` = 'Agregar Notas a Personas' WHERE `idgeneral_menu` = '2005'";
$sql["20150401"][]	= "CALL proc_lenguaje_cambiar_palabras('Ministracion', 'Desembolso')";
$sql["20150401"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `fecha_vencimiento` `fecha_vencimiento` DATE NOT NULL DEFAULT '0000-00-00' COMMENT 'Fecha de vencimiento de contrato' , CHANGE COLUMN `periodo_solicitudes` `periodo_solicitudes` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'clave de periodo de sesiones de credito' , CHANGE COLUMN `destino_credito` `destino_credito` INT(10) UNSIGNED NOT NULL DEFAULT '99' COMMENT 'id del destino del credito' , CHANGE COLUMN `ultimo_periodo_afectado` `ultimo_periodo_afectado` INT(4) NOT NULL DEFAULT '0' COMMENT 'Numero de la ultima parcialidad afectada' , CHANGE COLUMN `cadena_heredada` `cadena_heredada` VARCHAR(200) NOT NULL DEFAULT '' COMMENT 'Notas de migracion' , CHANGE COLUMN `tasa_ahorro` `tasa_ahorro` FLOAT(8,5) NULL DEFAULT '0.00000' COMMENT 'Tasa de ahorro condicionado por los pagos o montos' , CHANGE COLUMN `descripcion_aplicacion` `descripcion_aplicacion` VARCHAR(150) NOT NULL DEFAULT 'N/A' COMMENT 'descripcion del destino del recurso' , CHANGE COLUMN `fecha_ministracion` `fecha_ministracion` DATE NOT NULL DEFAULT '2005-12-31' COMMENT 'Fecha de desembolso real o propuesto' , CHANGE COLUMN `fecha_revision` `fecha_revision` DATE NULL DEFAULT '2006-01-01' COMMENT 'Fecha de revision, puede ser la misma que la conciliada' , CHANGE COLUMN `fecha_castigo` `fecha_castigo` DATE NULL DEFAULT '2006-12-04' COMMENT 'Fecha de castigo' , CHANGE COLUMN `saldo_conciliado` `saldo_conciliado` FLOAT(12,2) NULL DEFAULT '0.00' COMMENT 'Saldo revisado' , CHANGE COLUMN `notas_auditoria` `notas_auditoria` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Notas de revision' , CHANGE COLUMN `fecha_conciliada` `fecha_conciliada` DATE NULL DEFAULT '2006-12-04' COMMENT 'Fecha en que se revisa el credito' , CHANGE COLUMN `sucursal` `sucursal` VARCHAR(20) NULL DEFAULT 'MATRIZ' , CHANGE COLUMN `eacp` `eacp` VARCHAR(15) NOT NULL DEFAULT 'EN_TRAMITE' COMMENT 'clave de entidad DEPRECATED' ,CHANGE COLUMN `fecha_vencimiento_dinamico` `fecha_vencimiento_dinamico` DATE NULL DEFAULT '2008-08-01' COMMENT 'Fecha de vencimiento que se mueve con los pagos' , CHANGE COLUMN `tipo_de_calculo_de_interes` `tipo_de_calculo_de_interes` INT(2) NULL DEFAULT '2' COMMENT '1 Base saldo historico 2 BAse saldo Insolutos' , CHANGE COLUMN `causa_de_mora` `causa_de_mora` INT(4) NOT NULL DEFAULT '99' COMMENT 'ID de Causa de Mora' , CHANGE COLUMN `estatus_de_negociacion` `estatus_de_negociacion` ENUM('ninguno','reestructurado','renovado') NOT NULL DEFAULT 'ninguno' COMMENT 'TODO Cambiar' , CHANGE COLUMN `perfil_de_intereses` `perfil_de_intereses` INT(4) NULL DEFAULT '1' COMMENT 'Formula persona de calculo de interes' , CHANGE COLUMN `fuente_de_fondeo` `fuente_de_fondeo` INT(4) NULL DEFAULT '1' COMMENT 'Origen del recursos PROPIO FIRA ETC' , CHANGE COLUMN `fecha_de_primer_pago` `fecha_de_primer_pago` DATE NULL DEFAULT '2014-01-01' COMMENT 'Fecha de Primer pago en planes o de vencimiento en pago unico' ";
$sql["20150401"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `tipo_de_origen` `tipo_de_origen` INT(11) NULL DEFAULT 1 COMMENT '1  ninguno usar iDE_CREDITO iDE_CAPATCIOn etc' ,ADD COLUMN `tipo_de_dias_de_pago` INT(10) NULL DEFAULT 1 COMMENT 'Modalidad en que se fija los dias de pago NATURALES PREDETERMINADOS ESPECIALES' AFTER `tipo_de_origen`";
$sql["20150401"][]	= "UPDATE `general_menu` SET `menu_parent` = '18800' , `menu_title` = 'Listado de Empresas' , `menu_file` = 'rptempresas/lista-de-empresas.rpt.php' , `menu_description` = '' , `menu_image` = 'reporte' , `menu_order` = '11309' WHERE `idgeneral_menu` = '11309'";
$sql["20150401"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `monto_solicitado` `monto_solicitado` FLOAT(16,3) NOT NULL DEFAULT '0.00' , CHANGE COLUMN `monto_autorizado` `monto_autorizado` FLOAT(16,3) NOT NULL DEFAULT '0.00' , CHANGE COLUMN `saldo_actual` `saldo_actual` FLOAT(16,3) NOT NULL DEFAULT '0.00' , CHANGE COLUMN `saldo_vencido` `saldo_vencido` FLOAT(16,3) NOT NULL DEFAULT '0.00' ";
$sql["20150401"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `monto_solicitado` `monto_solicitado` DOUBLE(16,3) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `monto_autorizado` `monto_autorizado` DOUBLE(16,3) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `saldo_actual` `saldo_actual` DOUBLE(16,3) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `saldo_vencido` `saldo_vencido` DOUBLE(16,3) NOT NULL DEFAULT '0.000'";
$sql["20150401"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `monto_solicitado` `monto_solicitado` DOUBLE(16,2) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `monto_autorizado` `monto_autorizado` DOUBLE(16,2) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `saldo_actual` `saldo_actual` DOUBLE(16,2) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `saldo_vencido` `saldo_vencido` DOUBLE(16,2) NOT NULL DEFAULT '0.000'";
$sql["20150401"][]	= "ALTER TABLE `operaciones_mvtos` CHANGE COLUMN `afectacion_real` `afectacion_real` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `afectacion_cobranza` `afectacion_cobranza` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `afectacion_contable` `afectacion_contable` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `valor_afectacion` `valor_afectacion` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `saldo_anterior` `saldo_anterior` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `saldo_actual` `saldo_actual` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `afectacion_estadistica` `afectacion_estadistica` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `tasa_asociada` `tasa_asociada` FLOAT(8,4) NULL DEFAULT '0.00' COMMENT 'Tasa que se Asocia al Mvto' ,CHANGE COLUMN `sucursal` `sucursal` VARCHAR(20) NULL DEFAULT 'matriz'";
$sql["20150401"][]	= "ALTER TABLE `aml_risk_catalog` CHANGE COLUMN `valor_ponderado` `valor_ponderado` DOUBLE(18,6) NULL DEFAULT '0.000000' ,CHANGE COLUMN `unidades_ponderadas` `unidades_ponderadas` DOUBLE(18,4) NULL DEFAULT '0.0000' COMMENT 'Numero de unidades a reportar' ";
$sql["20150401"][]	= "ALTER TABLE `aml_risk_register` CHANGE COLUMN `escore` `escore` DOUBLE(18,6) NULL DEFAULT '0.000000' ,CHANGE COLUMN `monto_total_relacionado` `monto_total_relacionado` DOUBLE(18,3) NULL DEFAULT 0 ";
$sql["20150401"][]	= "ALTER TABLE `aml_risk_types` CHANGE COLUMN `valor_ponderado` `valor_ponderado` DOUBLE(18,6) NULL DEFAULT NULL ";
$sql["20150401"][]	= "ALTER TABLE `bancos_cuentas` CHANGE COLUMN `saldo_actual` `saldo_actual` DOUBLE(16,2) NOT NULL ";
$sql["20150401"][]	= "ALTER TABLE `bancos_operaciones` CHANGE COLUMN `monto_descontado` `monto_descontado` DOUBLE(16,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `monto_real` `monto_real` DOUBLE(16,2) NULL DEFAULT '0.00' , CHANGE COLUMN `idusuario` `idusuario` INT(10) UNSIGNED NULL DEFAULT NULL ,CHANGE COLUMN `usuario_autorizo` `usuario_autorizo` INT(10) UNSIGNED NULL DEFAULT '0' , CHANGE COLUMN `tipo_de_exhibicion` `tipo_de_exhibicion` VARCHAR(20) NULL DEFAULT 'efectivo' COMMENT 'transferencia efectivo' ";
$sql["20150401"][]	= "ALTER TABLE `captacion_cuentas` CHANGE COLUMN `tipo_cuenta` `tipo_cuenta` INT(10) UNSIGNED NOT NULL DEFAULT '99' ,CHANGE COLUMN `saldo_cuenta` `saldo_cuenta` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `idusuario` `idusuario` INT(10) UNSIGNED NOT NULL DEFAULT '99' ,CHANGE COLUMN `dias_invertidos` `dias_invertidos` INT(10) NULL DEFAULT '0' COMMENT 'Numero de Dias Invertidos' ,CHANGE COLUMN `origen_cuenta` `origen_cuenta` INT(10) NOT NULL DEFAULT '0' COMMENT '1=programas Federales, 3=Grupos Solidarios' ,CHANGE COLUMN `tipo_titulo` `tipo_titulo` INT(10) NULL DEFAULT '1' COMMENT '1=Nominativa, 2=Constancia' ,CHANGE COLUMN `tipo_subproducto` `tipo_subproducto` INT(10) NOT NULL DEFAULT '99' ,CHANGE COLUMN `saldo_conciliado` `saldo_conciliado` DOUBLE(16,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `ultimo_sdpm` `ultimo_sdpm` DOUBLE(16,3) NULL DEFAULT '0.000' ,CHANGE COLUMN `oficial_de_captacion` `oficial_de_captacion` INT(10) UNSIGNED NOT NULL DEFAULT '99' ";
$sql["20150401"][]	= "ALTER TABLE `captacion_sdpm_historico` CHANGE COLUMN `cuenta` `cuenta` BIGINT(25) NOT NULL DEFAULT '0' ,CHANGE COLUMN `monto` `monto` DOUBLE(14,2) NOT NULL ,CHANGE COLUMN `numero_de_socio` `numero_de_socio` BIGINT(25) NOT NULL DEFAULT '1' ";
$sql["20150401"][]	= "ALTER TABLE `matriz`.`captacion_tasas` CHANGE COLUMN `idcaptacion_tasas` `idcaptacion_tasas` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,CHANGE COLUMN `tasa_efectiva` `tasa_efectiva` FLOAT(8,4) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `modalidad_cuenta` `modalidad_cuenta` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,CHANGE COLUMN `monto_mayor_a` `monto_mayor_a` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `monto_menor_a` `monto_menor_a` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `dias_mayor_a` `dias_mayor_a` INT(5) NULL DEFAULT '0' ,CHANGE COLUMN `dias_menor_a` `dias_menor_a` INT(5) NULL DEFAULT '0' ,CHANGE COLUMN `subproducto` `subproducto` INT(10) NULL DEFAULT '0' COMMENT '0 = general'";
$sql["20150401"][]	= "ALTER TABLE `contable_polizas_proforma` CHANGE COLUMN `monto` `monto` DOUBLE(16,2) NOT NULL ";
$sql["20150401"][]	= "ALTER TABLE `creditos_presupuestos` CHANGE COLUMN `total_presupuesto` `total_presupuesto` DOUBLE(16,2) NULL DEFAULT '0.00'";
$sql["20150401"][]	= "ALTER TABLE `creditos_reconvenio` CHANGE COLUMN `monto_reconvenido` `monto_reconvenido` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `tasa_reconvenida` `tasa_reconvenida` FLOAT(6,3) NOT NULL DEFAULT '0.000' ,CHANGE COLUMN `interes_diario_re` `interes_diario_re` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `interes_pendiente` `interes_pendiente` DOUBLE(16,2) NULL DEFAULT '0.00' ";
$sql["20150401"][]	= "ALTER TABLE `creditos_tipoconvenio` CHANGE COLUMN `creditos_mayores_a` `creditos_mayores_a` DOUBLE(16,2) NULL DEFAULT '0.000' COMMENT 'Creditos Mayores a, para hacer valida la garantia' ,CHANGE COLUMN `maximo_otorgable` `maximo_otorgable` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ";
$sql["20150401"][]	= "ALTER TABLE `empresas_cobranza` CHANGE COLUMN `monto_enviado` `monto_enviado` DOUBLE(14,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `saldo_inicial` `saldo_inicial` DOUBLE(14,2) NULL DEFAULT '0.00' ";
$sql["20150401"][]	= " ALTER TABLE `operaciones_recibos` CHANGE COLUMN `total_operacion` `total_operacion` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `unidades_en_moneda` `unidades_en_moneda` DOUBLE(16,4) NULL DEFAULT '0.0000' ";
$sql["20150401"][]	= "ALTER TABLE `personas_perfil_transaccional` CHANGE COLUMN `cantidad_maxima` `cantidad_maxima` DOUBLE(18,3) NULL DEFAULT NULL ,CHANGE COLUMN `cantidad_calculada` `cantidad_calculada` DOUBLE(18,3) NULL DEFAULT NULL ";
$sql["20150401"][]	= "ALTER TABLE `socios_aeconomica` CHANGE COLUMN `monto_percibido_ae` `monto_percibido_ae` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ";
$sql["20150401"][]	= "ALTER TABLE `socios_aportaciones` CHANGE COLUMN `monto_aportacion` `monto_aportacion` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `capital` `capital` DOUBLE(16,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `interes` `interes` DOUBLE(16,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `total_ministrado` `total_ministrado` DOUBLE(16,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `total_recuperado` `total_recuperado` DOUBLE(16,2) NULL DEFAULT '0.00' ";
$sql["20150401"][]	= "ALTER TABLE `socios_patrimonio` CHANGE COLUMN `monto_patrimonio` `monto_patrimonio` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `afectacion_patrimonio` `afectacion_patrimonio` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ";
$sql["20150401"][]	= "ALTER TABLE `socios_relaciones` CHANGE COLUMN `monto_relacionado` `monto_relacionado` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ";
$sql["20150401"][]	= "ALTER TABLE `socios_scoring_simple` CHANGE COLUMN `numero_socio` `clave_de_persona` BIGINT(20) NULL DEFAULT 1 ,CHANGE COLUMN `fecha_de_calificacion` `fecha_de_calificacion` DATE NULL DEFAULT 0 ,CHANGE COLUMN `puntaje_de_proveedores` `puntaje_de_proveedores` FLOAT(6,4) NULL DEFAULT 0 ,CHANGE COLUMN `puntaje_de_clientes` `puntaje_de_clientes` FLOAT(6,4) NULL DEFAULT 0 ,CHANGE COLUMN `puntaje_de_organizacion` `puntaje_de_organizacion` FLOAT(6,4) NULL DEFAULT 0 ,CHANGE COLUMN `puntaje_de_fuerza_laboral` `puntaje_de_fuerza_laboral` FLOAT(6,4) NULL DEFAULT 0 ,CHANGE COLUMN `puntaje_capacidad_de_pago` `puntaje_capacidad_de_pago` FLOAT(6,4) NULL DEFAULT 0 ,CHANGE COLUMN `puntaje_caracter` `puntaje_caracter` FLOAT(6,4) NULL DEFAULT 0 ,CHANGE COLUMN `puntaje_factor_macro` `puntaje_factor_macro` FLOAT(6,4) NULL DEFAULT 0 ,ADD COLUMN `credito_relacionado` BIGINT(25) NULL DEFAULT 1 AFTER `clave_de_persona`,ADD COLUMN `clave_de_parametro` VARCHAR(25) NULL DEFAULT '' COMMENT 'CLAVE_DE_PARAMETRO' AFTER `credito_relacionado`";
$sql["20150401"][]	= "ALTER TABLE `tesoreria_caja_arqueos` CHANGE COLUMN `valor_arqueado` `valor_arqueado` DOUBLE(16,2) NULL DEFAULT '0.000' ,CHANGE COLUMN `numero_arqueado` `numero_arqueado` DOUBLE(16,2) NULL DEFAULT '0.000' ,CHANGE COLUMN `monto_total_arqueado` `monto_total_arqueado` DOUBLE(16,2) NULL DEFAULT '0.000' ";
$sql["20150401"][]	= "ALTER TABLE `tesoreria_cajas` CHANGE COLUMN `fondos_arqueados` `fondos_arqueados` DOUBLE(16,2) NULL DEFAULT '0.0000' ,CHANGE COLUMN `total_cobrado` `total_cobrado` DOUBLE(16,2) NULL DEFAULT '0.0000' COMMENT 'suma de todos los cobros de caja' ";
$sql["20150401"][]	= "ALTER TABLE `tesoreria_cajas_movimientos` CHANGE COLUMN `monto_del_movimiento` `monto_del_movimiento` DOUBLE(16,2) NULL DEFAULT '0.00' COMMENT 'Monto original' ,CHANGE COLUMN `monto_recibido` `monto_recibido` DOUBLE(16,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `monto_en_cambio` `monto_en_cambio` DOUBLE(16,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `unidades_de_moneda` `unidades_de_moneda` DOUBLE(16,4) NULL DEFAULT '0.0000' COMMENT 'Unidades originales de la moneda' ";
$sql["20150401"][]	= "DELETE FROM general_log WHERE text_log LIKE \"%SELECT operaciones_mvtos.periodo_socio AS 'parcialidad'%\" ";
$sql["20150401"][]	= "DELETE FROM general_log WHERE text_log LIKE \"%`socios_relacionestipos`.`descripcion_relacionestipos` AS 'relacion'%\" ";
$sql["20150401"][]	= "DELETE FROM general_log WHERE text_log LIKE \"%Error :You have an error in your SQL syntax; check the manual that corresponds%\" ";

$sql["20150402"][]	= "UPDATE `entidad_configuracion` SET `tipo` = 'personas' WHERE `nombre_del_parametro` = 'edad_productiva_maxima' ";
$sql["20150402"][]	= "UPDATE `entidad_configuracion` SET `tipo` = 'personas' WHERE `nombre_del_parametro` = 'edad_productiva_minima' ";

$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('542', '1000', '147', 'COM X APERTURA')";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('543', '1001', '147', 'COM X APERTURA') ";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('544', '2002', '147', 'COM X APERTURA') ";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('545', '2611', '147', '1', 'COM X APERTURA')";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('546', '7002', '147', '1', 'COM X APERTURA') ";
$sql["20150402"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `codigo_de_base` = '7003' WHERE `ideacp_config_bases_de_integracion_miembros` = '546' AND `codigo_de_base` = '7002' AND `miembro` = '147'";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('547', '7013', '147', '1', 'COM X APERTURA')";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`) VALUES ('548', '10000', '14')";
$sql["20150402"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `miembro` = '147' , `afectacion` = '1' , `descripcion_de_la_relacion` = 'COM X APERTURA' WHERE `ideacp_config_bases_de_integracion_miembros` = '548' AND `codigo_de_base` = '10000' AND `miembro` = '14'";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('549', '10001', '147', '1', 'COM X APERTURA')";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('550', '15000', '147', '1', 'COM X APERTURA') ";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('551', '30200', '147', '1', 'COM X APERTURA') ";

//$sql["20150402"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `operacion_origen` BIGINT(25) NULL DEFAULT 1 COMMENT 'Credito, recibo, linea de credito, etc, etc que vincula al credito' AFTER `fecha_de_primer_pago`, ADD COLUMN `tipo_de_origen` INT NULL COMMENT '1  ninguno usar iDE_CREDITO iDE_CAPATCIOn etc' AFTER `operacion_origen`";

$sql["20150402"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Modulo' WHERE `idsistema_lenguaje` = '753'";
$sql["20150402"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos por Puntos de Atencion' , `explicacion` = 'Creditos por Puntos de Atencion o Caja Locales' , `order_index` = '9999' WHERE `idreport` = '57'";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '1' WHERE `idreport` = '173'";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '50' WHERE `idreport` = '60' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '10' WHERE `idreport` = '173' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '11' WHERE `idreport` = '59' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '12' WHERE `idreport` = '64' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '51' WHERE `idreport` = '103' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '52' WHERE `idreport` = '104' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '30' WHERE `idreport` = '174' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '20' WHERE `idreport` = '1001' ";
$sql["20150402"][]	= "UPDATE `general_reports` SET `order_index` = '16' WHERE `idreport` = '10001'";
$sql["20150402"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcreditos/creditos-liquidados.rpt.php?' WHERE `idreport` = '10001'";
$sql["20150402"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-dias_en_vencidos.rpt.php?', 'Reporte de Rango de Dias de Creditos Vencidos', 'general_creditos', '1002', '', '21')";
$sql["20150402"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-dias_en_impago.rpt.php?', 'Reporte de Rango de Dias de Impago', 'general_creditos', '1003', '', '22')";
$sql["20150402"][]	= "CREATE TABLE IF NOT EXISTS `catalogos_tipo_de_dispersion` (  `tipo_de_dispersion` INT NOT NULL AUTO_INCREMENT,   `descripcion` VARCHAR(100) NULL,  PRIMARY KEY (`tipo_de_dispersion`)) ENGINE = InnoDB COMMENT = 'Tipo de dispersion de recursos' ";
$sql["20150402"][]	= "INSERT INTO `catalogos_tipo_de_dispersion` (`tipo_de_dispersion`, `descripcion`) VALUES ('101', 'Deduccion por Planilla') ";
$sql["20150402"][]	= "INSERT INTO `catalogos_tipo_de_dispersion` (`tipo_de_dispersion`, `descripcion`) VALUES ('100', 'Pago en Ventanilla') ";
$sql["20150402"][]	= "INSERT INTO `catalogos_tipo_de_dispersion` (`tipo_de_dispersion`, `descripcion`) VALUES ('300', 'Pago en Tarjeta o Debito') ";
$sql["20150402"][]	= "INSERT INTO `catalogos_tipo_de_dispersion` (`tipo_de_dispersion`, `descripcion`) VALUES ('310', 'Cheque') ";
$sql["20150402"][]	= "ALTER TABLE `socios_aeconomica` CHANGE COLUMN `tipo_aeconomica` `tipo_aeconomica` BIGINT(20) UNSIGNED NOT NULL DEFAULT '99' COMMENT 'clave de actividad segun la UIF' ,CHANGE COLUMN `sector_economico` `sector_economico` BIGINT(20) UNSIGNED NOT NULL DEFAULT '99' COMMENT 'Sector economico segun la SCIAN' ,ADD COLUMN `notas_de_verificacion` VARCHAR(150) NULL DEFAULT '' AFTER `ae_codigo_postal`, ADD COLUMN `fecha_de_ingreso` VARCHAR(45) NULL COMMENT 'Fecha de Ingreso a la Empresa' AFTER `notas_de_verificacion`, ADD COLUMN `empleado_tipo_de_dispersion` INT(4) NULL DEFAULT 100 COMMENT 'Tipo de Pago de su Salario catalogos_tipo_de_dispersion 100 = pago en ventanilla' AFTER `fecha_de_ingreso` ";
$sql["20150402"][]	= "INSERT INTO `catalogos_tipo_de_dispersion` (`tipo_de_dispersion`, `descripcion`) VALUES ('1', 'DESCONOCIDO') ";
$sql["20150402"][]	= "CREATE TABLE IF NOT EXISTS `personas_rango_de_ingresos` (  `idpersonas_rango_de_ingresos` INT NOT NULL AUTO_INCREMENT,  `descripcion` VARCHAR(45) NULL,  `limite_inferior` FLOAT(12,4) NULL DEFAULT 0 COMMENT 'Veces salario minimo',  `limite_superior` FLOAT(12,4) NULL DEFAULT 0,  PRIMARY KEY (`idpersonas_rango_de_ingresos`)) ENGINE = InnoDB COMMENT = 'Tabla de rango de ingresos'";
$sql["20150402"][]	= "insert into `personas_rango_de_ingresos` (`idpersonas_rango_de_ingresos`, `descripcion`, `limite_inferior`, `limite_superior`) values('1','Muy Bajos','1.0000','2.0000')";
$sql["20150402"][]	= "insert into `personas_rango_de_ingresos` (`idpersonas_rango_de_ingresos`, `descripcion`, `limite_inferior`, `limite_superior`) values('2','Bajos','2.0001','5.0000')";
$sql["20150402"][]	= "insert into `personas_rango_de_ingresos` (`idpersonas_rango_de_ingresos`, `descripcion`, `limite_inferior`, `limite_superior`) values('3','Medios','5.0001','10.0000')";
$sql["20150402"][]	= "insert into `personas_rango_de_ingresos` (`idpersonas_rango_de_ingresos`, `descripcion`, `limite_inferior`, `limite_superior`) values('4','Altos','10.0001','20.0000')";
$sql["20150402"][]	= "insert into `personas_rango_de_ingresos` (`idpersonas_rango_de_ingresos`, `descripcion`, `limite_inferior`, `limite_superior`) values('5','Muy Altos','20.0001','9999.0000')";
$sql["20150402"][]	= "UPDATE  `operaciones_recibostipo` SET `path_formato` = '../rpt_formatos/recibo_de_prestamo.rpt.php?recibo=' WHERE `idoperaciones_recibostipo` = '1' ";
$sql["20150402"][]	= "ALTER TABLE `socios_general` CHANGE COLUMN `titulo_personal` `titulo_personal` VARCHAR(40) NULL DEFAULT 'NA' ";
$sql["20150402"][]	= "ALTER TABLE `socios_relaciones` ADD COLUMN `dato_extra_1` VARCHAR(100) NULL DEFAULT '' AFTER `calificacion_del_referente`, ADD COLUMN `dato_extra_2` VARCHAR(100) NULL DEFAULT '' AFTER `dato_extra_1`";
$sql["20150402"][]	= "ALTER TABLE `socios_relaciones` ADD COLUMN `dato_extra_3` VARCHAR(100) NULL DEFAULT '' AFTER `dato_extra_2`";
$sql["20150402"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('552', '1001', '141', 'INTERES MORATORIO')";
$sql["20150402"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1' WHERE `ideacp_config_bases_de_integracion_miembros` = '552'";
$sql["20150402"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `codigo_de_base` = '7013' , `descripcion_de_la_relacion` = 'INTS MORATORIOS' WHERE `ideacp_config_bases_de_integracion_miembros` = '306' AND `codigo_de_base` = '7012' AND `miembro` = '141'";

$sql["20150403"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '527'";
$sql["20150403"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmcreditos/plan_de_pagos.frm.php' WHERE `idgeneral_menu` = '3004'";
$sql["20150403"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '305'";
$sql["20150403"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '302' ";
$sql["20150403"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '300' ";
$sql["20150403"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '188517'";
$sql["20150403"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '185'";

$sql["20150502"][]	= "INSERT INTO `aml_personas_descartadas` (`idaml_personas_descartadas`, `clave_de_oficial`, `fecha_de_captura`, `fecha_de_vencimiento`, `descripcion_del_motivo`) VALUES ('100', '99', '2012-01-01', '2029-01-01', 'PUBLICO GENERAL')";
$sql["20150502"][]	= "UPDATE operaciones_recibos SET persona_asociada= (SELECT persona_asociada FROM creditos_solicitud WHERE numero_solicitud = operaciones_recibos.docto_afectado ) WHERE tipo_docto = 1 AND persona_asociada <= 99 ";
$sql["20150502"][]	= "INSERT INTO `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`, `formula_posterior`) VALUES ('1099', '1', '99', 'PAGO NINGUNO', '1', '')";
$sql["20150502"][]	= "INSERT INTO `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`, `formula_posterior`) VALUES ('2099', '2', '99', 'PAGO NINGUNO', '1', '')";
$sql["20150502"][]	= "UPDATE `contable_polizas_perfil` SET `operacion` = '-1' WHERE `idcontable_poliza_perfil` = '1099'";
$sql["20150502"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('100', 'FORM', 'CREDITOS.TODOS_REQUIEREN_DOMICILIO', '', '', '\$valor=true;', '')";
$sql["20150502"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('101', 'FORM', 'CREDITOS.TODOS_REQUIEREN_ACTIVIDAD_ECON', '', '', '\$valor=true;', '')";
$sql["20150502"][]	= "ALTER TABLE `general_estados` ADD COLUMN `codigo_postal_inicial` INT NULL DEFAULT 0 AFTER `clave_en_sic`, ADD COLUMN `codigo_postal_final` INT NULL DEFAULT 0 AFTER `codigo_postal_inicial`, ADD COLUMN `operacion_habilitada` INT(2) NULL DEFAULT 1 AFTER `codigo_postal_final`";

$sql["20150503"][]	= "UPDATE `general_menu` SET `menu_parent` = '11000' , `menu_order` = '0' WHERE `idgeneral_menu` = '10000'";
$sql["20150503"][]	= "UPDATE `general_menu` SET `menu_title` = 'Soporte' WHERE `idgeneral_menu` = '15000'";
$sql["20150503"][]	= "ALTER TABLE `socios_general` ADD COLUMN `nacionalidad_extranjera` INT(4) NULL DEFAULT 0 COMMENT 'valor 0 falso 1 verdadero' AFTER `regimen_fiscal`";
$sql["20150503"][]	= "ALTER TABLE `bancos_operaciones` CHANGE `tipo_de_exhibicion` `tipo_de_exhibicion` varchar(20) COLLATE 'utf8_general_ci' NULL DEFAULT 'efectivo' AFTER `clave_de_moneda`";
$sql["20150503"][]	= "UPDATE bancos_operaciones SET tipo_de_exhibicion='transferencia' WHERE tipo_de_exhibicion='transferen'";
$sql["20150503"][]	= "ALTER TABLE `socios_patrimonio` CHANGE `solicitud_relacionada` `solicitud_relacionada` BIGINT(20) DEFAULT 1 NULL COMMENT 'Numero de Credito o Captacion relacionado', CHANGE `estatus_actual` `estatus_actual` INT(4) UNSIGNED DEFAULT 99 NOT NULL COMMENT 'Estado en que lo presenta o se muestra', CHANGE `codigo` `codigo` INT(10) DEFAULT 1 NOT NULL COMMENT 'DEPRECTATED', CHANGE `fecha_de_alta` `fecha_de_alta` DATE NOT NULL COMMENT 'Fecha de registro'";

$sql["20150504"][]	= "ALTER TABLE `contable_polizas_proforma` ADD COLUMN `cuenta_alternativa` BIGINT(25) NULL DEFAULT 0 COMMENT 'Cuenta en el caso de existir' AFTER `sucursal` ";
$sql["20150504"][]	= "ALTER TABLE `contable_polizas_perfil` CHANGE COLUMN `tipo_de_recibo` `tipo_de_recibo` INT(4) NOT NULL ,CHANGE COLUMN `tipo_de_operacion` `tipo_de_operacion` INT(6) NOT NULL ,ADD COLUMN `cuenta_alternativa` BIGINT(25) NULL DEFAULT 0 COMMENT 'Cuenta forzada a la aplicacion' AFTER `formula_posterior`";

$sql["20150505"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `tipo_de_lugar_de_pago` INT(4) NULL DEFAULT 1 COMMENT '1 Por defecto 2 ventanilla 3 medios electronicos. Donde van a Pagar' AFTER `tipo_de_dias_de_pago`, ADD COLUMN `tipo_de_dispersion` INT(4) NULL DEFAULT 1 COMMENT 'forma en que dispersara el recurso' AFTER `tipo_de_lugar_de_pago`";
$sql["20150505"][]	= "CREATE TABLE IF NOT EXISTS `creditos_tipo_de_dispersion` ( `idcreditos_tipo_de_dispersion` INT NOT NULL AUTO_INCREMENT,  `descripcion` VARCHAR(100) NULL, `equivalente_en_tesoreria` VARCHAR(40) NULL DEFAULT 'ninguno' COMMENT 'Cual es el equivalente en tesoeria', PRIMARY KEY (`idcreditos_tipo_de_dispersion`)) ENGINE = InnoDB";
$sql["20150505"][]	= "INSERT INTO `creditos_tipo_de_dispersion` (`idcreditos_tipo_de_dispersion`, `descripcion`) VALUES ('1', 'POR_DEFECTO')";
$sql["20150505"][]	= "INSERT INTO `creditos_tipo_de_dispersion` (`idcreditos_tipo_de_dispersion`, `descripcion`, `equivalente_en_tesoreria`) VALUES ('2', 'Cheque', 'cheque')";
$sql["20150505"][]	= "INSERT INTO `creditos_tipo_de_dispersion` (`idcreditos_tipo_de_dispersion`, `descripcion`, `equivalente_en_tesoreria`) VALUES ('3', 'Transferencia', 'transferencia.egreso')";
$sql["20150505"][]	= "INSERT INTO `creditos_tipo_de_dispersion` (`idcreditos_tipo_de_dispersion`, `descripcion`, `equivalente_en_tesoreria`) VALUES ('4', 'Pasivo Interno', 'documento.egreso')";

$sql["20150505"][]	= "ALTER TABLE `operaciones_recibos` ADD COLUMN `cuenta_bancaria` BIGINT(20) NULL DEFAULT 0 COMMENT 'Cuenta Bancaria asociada' AFTER `periodo_de_documento`";
$sql["20150505"][]	= "ALTER TABLE `socios_memotipos` CHANGE COLUMN `tipo_memo` `tipo_memo` INT(4) NOT NULL AUTO_INCREMENT , ADD PRIMARY KEY (`tipo_memo`)";
$sql["20150505"][]	= "ALTER TABLE `operaciones_archivo_de_facturas` CHANGE COLUMN `clave_de_recibo` `clave_de_recibo` BIGINT(20) NOT NULL DEFAULT '1' ";

$sql["20150506"][]	= "ALTER TABLE `sistema_programacion_de_avisos` CHANGE COLUMN `programacion` `programacion` VARCHAR(100) NULL DEFAULT '' COMMENT 'En que evento se dispara el proceso'";
$sql["20150506"][]	= "ALTER TABLE `general_sucursales` ADD COLUMN `centro_de_costo` INT(11) NULL DEFAULT '0' AFTER `clave_numerica` ";
$sql["20150506"][]	= "ALTER TABLE `contable_centrodecostos` ADD COLUMN `equivalente` VARCHAR(10) NULL DEFAULT '00' COMMENT 'Equivalente en Catalogo externo' AFTER `nombre_centrodecostos`";
$sql["20150506"][]	= "ALTER TABLE `creditos_lineas` CHANGE COLUMN `monto_linea` `monto_linea` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,CHANGE COLUMN `monto_hipoteca` `monto_hipoteca` DOUBLE(16,2) NOT NULL DEFAULT '0.00' ,ADD COLUMN `fecha_ultima_operacion` DATE NULL DEFAULT '0000-00-00' AFTER `eacp`,ADD COLUMN `saldo_disponible` DOUBLE(16,2) NULL DEFAULT 0 AFTER `fecha_ultima_operacion`,ADD COLUMN `oficia_de_credito` INT NULL DEFAULT 99 COMMENT 'Oficial de Credito Asignado' AFTER `saldo_disponible`,ADD COLUMN `fecha_de_cancelacion` DATE NULL DEFAULT '0000-00-00' AFTER `oficia_de_credito`,ADD COLUMN `razones_de_cancelacion` VARCHAR(100) NULL DEFAULT '' AFTER `fecha_de_cancelacion`";
$sql["20150506"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '3022'";
$sql["20150506"][]	= "UPDATE `general_menu` SET `menu_title` = 'Lineas de Credito' WHERE `idgeneral_menu` = '3021'";
$sql["20150506"][]	= "ALTER TABLE `creditos_lineas` CHANGE COLUMN `oficia_de_credito` `oficial_de_credito` INT(11) NULL DEFAULT '99' COMMENT 'Oficial de Credito Asignado' ";
$sql["20150506"][]	= "DELETE FROM general_utilerias WHERE nombre_utilerias LIKE 'Migracion.-%'";
$sql["20150506"][]	= "UPDATE general_utilerias SET nombre_utilerias = REPLACE(nombre_utilerias, 'Socios.-', 'Personas.-')";
$sql["20150506"][]	= "UPDATE `general_utilerias` SET `nombre_utilerias` = 'Personas.Domicilio.- Utilizar Localidades por Colonias' WHERE `idgeneral_utilerias` = '13002'";
$sql["20150506"][]	= "UPDATE `general_utilerias` SET `nombre_utilerias` = 'Personas.Domicilio.- Purgar Domicilio en Actividad Economica' WHERE `idgeneral_utilerias` = '13001'";
$sql["20150506"][]	= "UPDATE `general_utilerias` SET `nombre_utilerias` = 'Sistema.- Actualizar el Sistema' , `descripcion_utileria` = 'Actualiza el Sistema conforme a los parámetros configurados como HOST' WHERE `idgeneral_utilerias` = '669'";
$sql["20150506"][]	= "UPDATE `general_utilerias` SET `describe_param_1` = 'APLICAR: SI/NO' WHERE `idgeneral_utilerias` = '669'";
$sql["20150506"][]	= "UPDATE general_utilerias SET nombre_utilerias = REPLACE(nombre_utilerias, 'General.-', 'Sistema.-')";
$sql["20150506"][]	= "UPDATE `general_utilerias` SET `nombre_utilerias` = 'Creditos.- Purgar Primera Fecha de Abono' WHERE `idgeneral_utilerias` = '21101'";
$sql["20150506"][]	= "UPDATE  `general_utilerias` SET `nombre_utilerias` = 'Sistema.- Cambia una Sucursal a Otra' WHERE `idgeneral_utilerias` = '9001'";
$sql["20150506"][]	= "ALTER TABLE `creditos_plan_de_pagos` CHANGE COLUMN `plan_de_pago` `plan_de_pago` INT(11) NOT NULL AUTO_INCREMENT ,CHANGE COLUMN `centro_de_trabajo` `sucursal` VARCHAR(20) NOT NULL DEFAULT 'matriz' COMMENT 'Sucursal' ";
$sql["20150506"][]	= "ALTER TABLE `creditos_plan_de_pagos` DROP COLUMN `fecha_de_vencimiento`,DROP COLUMN `fecha_de_ultimo_abono`,DROP COLUMN `fecha_de_registro`,DROP COLUMN `recibo`,DROP COLUMN `periocidad`,DROP COLUMN `tipo_de_tasa`";
$sql["20150506"][]	= "ALTER TABLE `creditos_plan_de_pagos` DROP COLUMN `moratorio`";
$sql["20150506"][]	= "ALTER TABLE `creditos_plan_de_pagos` DROP COLUMN `saldo_capital`";
$sql["20150506"][]	= "ALTER TABLE `creditos_plan_de_pagos` CHANGE COLUMN `capital` `capital` DOUBLE(14,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `interes` `interes` FLOAT(12,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `impuesto` `impuesto` FLOAT(12,2) NULL DEFAULT '0.00' ,CHANGE COLUMN `otros` `otros` FLOAT(12,2) NULL DEFAULT '0.00' ,ADD COLUMN `ahorro` FLOAT(12,2) NULL DEFAULT 0 AFTER `sucursal`,DROP INDEX `fk_creditos_plan_de_pagos_creditos1_idx` ,ADD INDEX `fk_creditos_plan_de_pagos_creditos1_idx` (`clave_de_credito` ASC, `numero_de_parcialidad` ASC),DROP INDEX `fk_creditos_plan_de_pagos_entidad_centro_de_trabajo1_idx` ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Estado de Cuenta General' WHERE `idreport` = '1'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos. Estado de Cuenta con Operaciones Estadisticas' WHERE `idreport` = '2'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Personas.- Estado de Cuenta de Creditos' WHERE `idreport` = '3'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'PA.- Lista de Creditos' WHERE `idreport` = '32' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Personas.- Expediente' WHERE `idreport` = '31'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Depositos Ordinarios.- Estado de Cuenta' WHERE `idreport` = '30'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Depositos a Plazo.- Estado de Cuenta' WHERE `idreport` = '26'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'PA.- Lista de Personas con aportaciones al capital' WHERE `idreport` = '14'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'PA.- Lista de Personas por Punto de acceso' WHERE `idreport` = '9'";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '65' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '45' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Grupos.- Estado de Cuenta de Creditos' WHERE `idreport` = '4'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Grupos.- Lista de Integrantes' WHERE `idreport` = '5'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Lista de Personas por Punto de acceso' WHERE `idreport` = '9'";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '12'";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '13' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '15' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '16' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '17' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '18' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '22' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '23' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '33' ";
$sql["20150506"][]	= "DELETE FROM `general_reports` WHERE `idreport` = '43' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Reporte General' WHERE `idreport` = '173'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Garantias.- Pendientes de Entrega' WHERE `idreport` = '104'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Garantias.- Historico de Entregas' WHERE `idreport` = '103' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Garantias.- Garantias en Resguardo' WHERE `idreport` = '60' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Reporte por Estados de Creditos' WHERE `idreport` = '59'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Listado por Puntos de Atencion' WHERE `idreport` = '57' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Tabulador por Dias de Mora' WHERE `idreport` = '1001'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Tabulador por dias vencidos' WHERE `idreport` = '1002'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Tabulador por dias de Impago' WHERE `idreport` = '1003'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Operaciones.- Historico de Operaciones' WHERE `idreport` = '8001' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Operaciones.- Historico de Recibos' WHERE `idreport` = '132'";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Operaciones.- Empresas.- Ingresos' WHERE `idreport` = '176' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Operaciones.- Personas.- Ingresos' WHERE `idreport` = '177' ";
$sql["20150506"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Grupos.- Listado General' , `explicacion` = '' WHERE `idreport` = '42'";
$sql["20150506"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-letras-pendientes-de-pago.rpt.php?', 'Creditos.- Cuotas Pendientes de Pago', 'general_creditos', '1004', 'Saldos de Creditos sin filtros', '17')";
$sql["20150506"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptseguimiento/creditos-letras-pendientes-de-pago.rpt.php?' , `aplica` = 'seguimiento' WHERE `idreport` = '1004'";
$sql["20150506"][]	= "CALL proc_lenguaje_cambiar_palabras('Seguimiento de creditos', 'Gestion de Cobranza')";
$sql["20150506"][]	= "CALL proc_lenguaje_cambiar_palabras('Seguimiento', 'Gestion de Cobranza')";
$sql["20150506"][]	= "CALL proc_lenguaje_cambiar_palabras('Gestion de Cobranza de Creditos', 'Gestion de Cobranza')";


$sql["20150601"][]	= "UPDATE `general_structure` SET `control` = 'text' WHERE `index_struct` = '1450'";
$sql["20150601"][]	= "UPDATE `general_structure` SET `control` = 'text' WHERE `index_struct` = '1451'";
$sql["20150601"][]	= "UPDATE `general_structure` SET `sql_select` = 'SELECT nombre AS \'clave\',nombre FROM general_estados' WHERE `index_struct` = '1450'";
$sql["20150601"][]	= "UPDATE `general_structure` SET `valor` = '|0@NO|1@SI|' , `titulo` = 'Es Domicilio Principal?' WHERE `index_struct` = '1457'";

$sql["20150603"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-rechazados.rpt.php?', 'Creditos.- Rechazados', 'general_creditos', '10003', '', '10003')";
$sql["20150603"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-no-autorizados.rpt.php?', 'Creditos.- No autorizados', 'general_creditos', '10004', '', '10004')";
$sql["20150603"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-no-desembolsados.rpt.php?', 'Creditos.- No desembolsados', 'general_creditos', '10005', '', '10005')";

$sql["20150604"][]	= "CALL `proc_colonias_activas`()";
$sql["20150604"][]	= "ALTER TABLE `creditos_tipoconvenio` CHANGE COLUMN `pre_modificador_de_interes` `pre_modificador_de_interes` MEDIUMTEXT NULL DEFAULT '' COMMENT 'premodificador cuando ya es activo' ,CHANGE COLUMN `pos_modificador_de_interes` `pos_modificador_de_interes` MEDIUMTEXT NULL DEFAULT '' COMMENT 'pos modificador cuando ya esta activo' ,CHANGE COLUMN `pre_modificador_de_ministracion` `pre_modificador_de_ministracion` MEDIUMTEXT NULL DEFAULT '' COMMENT 'pre modificadores antes de Ministrarse' ,CHANGE COLUMN `pre_modificador_de_autorizacion` `pre_modificador_de_autorizacion` MEDIUMTEXT NULL DEFAULT '' COMMENT 'pre modificador cuando va autorizarse' ,CHANGE COLUMN `pre_modificador_de_vencimiento` `pre_modificador_de_vencimiento` MEDIUMTEXT NULL DEFAULT '' COMMENT 'pre modifcadores de vencimiento' ,CHANGE COLUMN `pre_modificador_de_solicitud` `pre_modificador_de_solicitud` MEDIUMTEXT NULL DEFAULT '' COMMENT 'pre modifcadores al momento de guardarse como solicitud' ";
$sql["20150604"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('10001', '10', 'Ficha de Personas.- General', '<!-- contenido -->')";
$sql["20150604"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('20101', '20', 'Estado de Cuenta Individual de Credito', '<!-- contenido -->')";

$sql["20150701"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('1105', 'Contabilidad.- Reestablecer Contabilidad', 'Reestablecer la contabilidad a ceros')";
$sql["20150701"][]	= "ALTER TABLE `contable_polizas_proforma` ADD COLUMN `fecha` DATE NULL DEFAULT '2015-01-01' AFTER `cuenta_alternativa`";
$sql["20150701"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`,`describe_param_1`) VALUES ('1106', 'Contabilidad.- Generar las Polizas de un dia', 'Generar las polizas de un dia especifico', 'FECHA_ACTUAL')";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes por Puntos de Atencion' , `menu_description` = 'Reportes por Puntos de Atencion' WHERE `idgeneral_menu` = '2024' ";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reporte - Lista de Puntos de Atencion' , `menu_description` = 'Reporte - Lista de Puntos de Atencion' WHERE `idgeneral_menu` = '99103'";
$sql["20150701"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcajaslocales/rpt_socios_xcajalocal.php?' WHERE `idreport` = '9'";
$sql["20150701"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcajaslocales/rpt_aportacion_socios_x_caja.php?' WHERE `idreport` = '14'";
$sql["20150701"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptcajaslocales/rpt_creditos_por_caja_local.php?' , `explicacion` = '' WHERE `idreport` = '32'";
$sql["20150701"][]	= "UPDATE `general_reports` SET `explicacion` = '' WHERE `idreport` = '14'";
$sql["20150701"][]	= "UPDATE `general_reports` SET `explicacion` = '' WHERE `idreport` = '9'";
$sql["20150701"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('404', 'Modulo no activo.', 'common')";
$sql["20150701"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('405', 'Bajo construccion. Estamos trabajando para mejorar.', 'common')";
$sql["20150701"][]	= "CREATE TABLE IF NOT EXISTS `personas_pagos_perfil` ( `idpersonas_aportaciones_perfil` INT NOT NULL AUTO_INCREMENT,  `persona` BIGINT(25) NULL DEFAULT 1 COMMENT '1 Defecto',  `documento` BIGINT(25) NULL DEFAULT 1 COMMENT '1 Defecto',  `tipo_de_documento` INT(4) NULL DEFAULT 1 COMMENT '1 NINGUNO',  `periocidad` INT(4) NULL DEFAULT 30 COMMENT '1 NINGUNO iDE_CREDITO',  `tipo_de_operacion` INT(6) NULL DEFAULT 0 COMMENT 'segun la tabla de operaciones mvtos',  `monto` DOUBLE(12,2) NULL DEFAULT 0.00,  `observaciones` VARCHAR(100) NULL DEFAULT '',  `sucursal` VARCHAR(20) NULL DEFAULT 'matriz',  PRIMARY KEY (`idpersonas_aportaciones_perfil`),  INDEX `idops` (`persona` ASC, `tipo_de_operacion` ASC)) ENGINE = InnoDB";
$sql["20150701"][]	= "CREATE TABLE IF NOT EXISTS `entidad_pagos_perfil` (  `identidad_pagos_perfil` INT NOT NULL AUTO_INCREMENT,  `tipo_de_ingreso` INT(4) NULL DEFAULT 0 COMMENT 'tipo de ingreso a la entidad',  `tipo_de_operacion` INT(6) NULL DEFAULT 0,  `periocidad` INT(4) NULL DEFAULT 30,  `monto` DOUBLE(12,2) NULL DEFAULT 0,  PRIMARY KEY (`identidad_pagos_perfil`)) ENGINE = InnoDB COMMENT = 'perfil de pagos y aportaciones inicial por tipo de ingreso'";
$sql["20150701"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99073'";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reporte.- Lista de Tipo de Operacion' , `menu_file` = 'rptcatalogos/operaciones_tipos.rpt.php' , `menu_image` = 'reporte' WHERE `idgeneral_menu` = '11301' ";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Agregar Llamadas' , `menu_file` = 'frmseguimiento/llamadas.frm.php' , `menu_image` = 'telefono' WHERE `idgeneral_menu` = '4009'";
$sql["20150701"][]	= "ALTER TABLE `seguimiento_notificaciones` DROP COLUMN `tipo_credito`, DROP COLUMN `domicilio_completo`, DROP COLUMN `procedimiento_proximo`, ADD COLUMN `hora` TIME NULL AFTER `grupo_relacionado`, ADD COLUMN `usuario` INT NULL AFTER `hora`, ADD COLUMN `canal_de_envio` VARCHAR(20) NULL COMMENT 'SMS personal' AFTER `usuario`, ADD COLUMN `formato` INT NULL COMMENT 'numero de formato utilizado' AFTER `canal_de_envio`";
$sql["20150701"][]	= "ALTER TABLE `seguimiento_notificaciones` DROP COLUMN `fecha_vencimiento`";
$sql["20150701"][]	= "ALTER TABLE `seguimiento_notificaciones` CHANGE COLUMN `fecha_notificacion` `fecha_notificacion` DATE NULL DEFAULT '0000-00-00' ,CHANGE COLUMN `capital` `capital` DOUBLE(12,2) NULL DEFAULT 0 ,CHANGE COLUMN `interes` `interes` DOUBLE(12,2) NULL DEFAULT 0 ,CHANGE COLUMN `moratorio` `moratorio` DOUBLE(12,2) NULL DEFAULT 0 ,CHANGE COLUMN `otros_cargos` `otros_cargos` DOUBLE(12,2) NULL DEFAULT 0 ,CHANGE COLUMN `total` `total` FLOAT(12,2) NULL DEFAULT 0 ,CHANGE COLUMN `usuario` `usuario` INT(11) NULL DEFAULT 1 ,CHANGE COLUMN `canal_de_envio` `canal_de_envio` VARCHAR(20) NULL DEFAULT 'personal' COMMENT 'SMS personal' ,CHANGE COLUMN `formato` `formato` INT(11) NULL DEFAULT 0 COMMENT 'numero de formato utilizado' ,ADD COLUMN `impuestos` DOUBLE(12,2) NULL AFTER `otros_cargos`";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Calendario de Gestion' , `menu_file` = 'frmseguimiento/calendario.frm.php' WHERE `idgeneral_menu` = '4008'";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Calendario de atrasos' , `menu_file` = 'frmseguimiento/calendario.atrasos.frm.php' , `menu_description` = 'Calendario de Pagos Atrasados' , `menu_order` = '1' WHERE `idgeneral_menu` = '4006'";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_order` = '2' WHERE `idgeneral_menu` = '4030'";
$sql["20150701"][]	= "ALTER TABLE `creditos_tipoconvenio` ADD COLUMN `omitir_seguimiento` INT(4) NULL DEFAULT '0' COMMENT '0 NO 1 SI' AFTER `tipo_en_sistema`";
$sql["20150701"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `fecha_de_proximo_pago` DATE NULL DEFAULT '0000-00-00' COMMENT 'Fecha en proxima letra o de pago de capital' AFTER `tipo_de_dispersion`, ADD COLUMN `omitir_seguimiento` INT(2) NULL DEFAULT '0' COMMENT '0 NO 1 SI, indica si se omite el seguimiento de creditos' AFTER `fecha_de_proximo_pago` ";
$sql["20150701"][]	= "ALTER TABLE `seguimiento_notificaciones` CHANGE COLUMN `estatus_notificacion` `estatus_notificacion` VARCHAR(20) NULL DEFAULT 'pendiente'";
$sql["20150701"][]	= "ALTER TABLE `seguimiento_llamadas` CHANGE COLUMN `estatus_llamada` `estatus_llamada` VARCHAR(20) NULL DEFAULT 'pendiente'";
$sql["20150701"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('3001', '30', 'Microformato SMS Notificacion', 'Señor(a): variable_persona_nombre_completo se le recuerda su pago pendiente por variable_notificacion_total en pagos atrasados.')";
$sql["20150701"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '4012'";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Operaciones Masivas' WHERE `idgeneral_menu` = '4014' ";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_title` = 'Agregar Notificacion' , `menu_file` = 'frmseguimiento/notificaciones.add.frm.php' , `menu_image` = 'notificacion' , `menu_order` = '1' WHERE `idgeneral_menu` = '4011' ";
$sql["20150701"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '4020' ";
$sql["20150701"][]	= "UPDATE `general_menu` SET `menu_parent` = '4030' WHERE `idgeneral_menu` = '4021' ";

$sql["20150701"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Contraseña' WHERE `idsistema_lenguaje` = '293'";
$sql["20150701"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Este Prestamo lo liquidará otra persona.' WHERE `idsistema_lenguaje` = '893'";
$sql["20150701"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Este Prestamo lo usará otra persona.' WHERE `idsistema_lenguaje` = '895'";
$sql["20150701"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Configuración' WHERE `idsistema_lenguaje` = '737'";
$sql["20150701"][]	= "ALTER TABLE `general_estados` ADD COLUMN `codigo_postal_inicial` INT NULL DEFAULT 0 AFTER `clave_en_sic`, ADD COLUMN `codigo_postal_final` INT NULL DEFAULT 0 AFTER `codigo_postal_inicial`, ADD COLUMN `operacion_habilitada` INT(2) NULL DEFAULT 1 AFTER `codigo_postal_final`";

$sql["20150702"][]	= "ALTER TABLE `bancos_entidades` ADD COLUMN `clave_alfanumerica` VARCHAR(10) NULL DEFAULT '' AFTER `pais_de_origen`, ADD COLUMN `nombre_corto` VARCHAR(35) NULL DEFAULT '' AFTER `clave_alfanumerica`";
$sql["20150702"][]	= "ALTER TABLE `bancos_entidades` CHANGE COLUMN `nombre_de_la_entidad` `nombre_de_la_entidad` VARCHAR(150) NOT NULL ";
$sql["20150702"][]	= "UPDATE `general_contratos` SET `idgeneral_contratos` = '30020' WHERE `idgeneral_contratos` = '0' ";
$sql["20150702"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `tasa_cat` FLOAT(6,2) NULL DEFAULT 0 AFTER `omitir_seguimiento`";
$sql["20150702"][]	= "ALTER TABLE `captacion_cuentas` ADD COLUMN `tasa_gat` FLOAT(6,2) NULL DEFAULT 0 AFTER `recibo_de_inversion`";
$sql["20150702"][]	= "ALTER TABLE `seguimiento_compromisos` ADD COLUMN `monto_comprometido` DOUBLE(14,2) NULL DEFAULT 0 COMMENT 'Monto que necesita pagar o prometio pagar' AFTER `lugar_de_compromiso`";
$sql["20150702"][]	= "UPDATE `creditos_solicitud` SET `tipo_de_origen`=1 WHERE ISNULL(`tipo_de_origen`)";

$sql["20150703"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `fecha_ultimo_capital` DATE NULL DEFAULT '0000-00-00' COMMENT 'Fecha de ultimo pago de capital' AFTER `tasa_cat`";
$sql["20150703"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('40200', '4000', 'Generador de Gestion', 'frmseguimiento/generador.frm.php', 'Genera Notificaciones, Llamadas y Compromisos', 'seguimiento', 'command', '3', '40200')";
$sql["20150703"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('20010', '20', 'Ficha General de Credito', '')";
$sql["20150703"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('20011', '20', 'Ficha Mini de Credito', '<table><tr>\r\n<th class=\'izq\'>Persona</th>\r\n<td class=\'mny\'>variable_numero_de_socio</td>\r\n<th class=\'izq\'>Nombre</th>\r\n<td>variable_nombre_del_socio</td>\r\n</tr><tr>\r\n<th class=\'izq\'>Credito</th>\r\n<td class=\'mny\'>variable_credito_clave</td>\r\n<th class=\'izq\'>Descripcion</th>\r\n<td>variable_credito_descripcion_corta</td>\r\n</tr></table>')";
$sql["20150703"][]	= "INSERT INTO `socios_memotipos` (`tipo_memo`, `descripcion_memo`) VALUES ('12', 'Memo para Cobranza')";
$sql["20150703"][]	= "ALTER TABLE `socios_memo` ADD COLUMN `archivado` INT(2) NULL DEFAULT '1' COMMENT '0 NO 1 SI' AFTER `eacp`";
$sql["20150703"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('20201', '20', 'Contrato de Intercambio Legal', '')";

$sql["20150704"][]	= "ALTER TABLE `socios_general` ADD COLUMN `xclasificacion` INT(4) NULL DEFAULT '0' COMMENT 'Clasificacion 1 Auxiliar' AFTER `nacionalidad_extranjera`,ADD COLUMN `yclasificacion` INT(4) NULL DEFAULT '0' COMMENT 'Clasificacion 2 Auxiliar' AFTER `xclasificacion`,ADD COLUMN `zclasificacion` INT(4) NULL DEFAULT '0' COMMENT 'Clasificacion 3 Auxiliar' AFTER `yclasificacion`";
$sql["20150704"][]	= "CREATE TABLE IF NOT EXISTS `personas_xclasificacion` (`idpersonas_xclasificacion` INT(4) NOT NULL AUTO_INCREMENT, `descripcion_xclasificacion` VARCHAR(80) NULL,  PRIMARY KEY (`idpersonas_xclasificacion`)) ENGINE = INNODB COMMENT = 'Tabla de Clasificacion Auxiliar 1'";
$sql["20150704"][]	= "CREATE TABLE IF NOT EXISTS `personas_yclasificacion` (`idpersonas_yclasificacion` INT(4) NOT NULL AUTO_INCREMENT, `descripcion_yclasificacion` VARCHAR(80) NULL,   PRIMARY KEY (`idpersonas_yclasificacion`)) ENGINE = INNODB COMMENT = 'Tabla de Clasificacion Auxiliar 2'";
$sql["20150704"][]	= "CREATE TABLE IF NOT EXISTS `personas_zclasificacion` (`idpersonas_zclasificacion` INT(4) NOT NULL AUTO_INCREMENT, `descripcion_zclasificacion` VARCHAR(80) NULL, PRIMARY KEY (`idpersonas_zclasificacion`)) ENGINE = InnoDB COMMENT = 'Tabla de Clasificacion Auxiliar 3'";
$sql["20150704"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('20511', '2050', 'Clasificador 1', 'frmsocios/xcatalogo.frm.php', 'tiny', 'Clasificador de Personas 1', 'personas', 'command', '20511') ";
$sql["20150704"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('20512', '2050', 'Clasificador 2', 'frmsocios/ycatalogo.frm.php', 'tiny', 'Clasificador de Personas 2', 'personas', 'command', '20512')";
$sql["20150704"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('20513', '2050', 'Clasificador 3', 'frmsocios/zcatalogo.frm.php', 'tiny', 'Clasificador de Personas 3', 'personas', 'command', '20513') ";
$sql["20150704"][]	= "ALTER TABLE `personas_xclasificacion` ADD COLUMN `xclasificacion_etiquetas` VARCHAR(40) NULL DEFAULT '' AFTER `descripcion_xclasificacion`";
$sql["20150704"][]	= "ALTER TABLE `personas_yclasificacion` ADD COLUMN `yclasificacion_etiquetas` VARCHAR(40) NULL DEFAULT '' AFTER `descripcion_yclasificacion`";
$sql["20150704"][]	= "ALTER TABLE `personas_zclasificacion` ADD COLUMN `zclasificacion_etiquetas` VARCHAR(40) NULL DEFAULT '' AFTER `descripcion_zclasificacion`";
$sql["20150704"][]	= "CREATE TABLE IF NOT EXISTS `personas_datos_extranjero` (  `idpersonas_datos_extranjero` INT NOT NULL AUTO_INCREMENT,  `clave_de_persona` BIGINT(25) NOT NULL,  `clave_permiso_de_residencia` VARCHAR(100) NULL DEFAULT '',  `fecha_de_inicio_residencia` DATE NULL DEFAULT '0000-00-00',  `fecha_de_vencimiento` DATE NULL DEFAULT '2029-12-31',  PRIMARY KEY (`idpersonas_datos_extranjero`)) ENGINE = InnoDB";
$sql["20150704"][]	= "CREATE TABLE IF NOT EXISTS `personas_datos_colegiacion` (  `idpersonas_datos_colegiacion` INT NOT NULL AUTO_INCREMENT,  `clave_de_persona` BIGINT(25) NULL,  `dia_de_pago` VARCHAR(10) NULL COMMENT 'Dia del mes',  `tipo_de_lugar_de_pago` INT(4) NULL DEFAULT '1' COMMENT 'origen del pago o forma de descuento',  `tipo_de_afiliacion` INT(6) NULL DEFAULT '0' COMMENT 'Mismo que tipo de memebresia',  `datos_de_emergencia` VARCHAR(100) NULL COMMENT 'en caso de emergencia llamar a',  `grado_academico` INT(4) NULL DEFAULT '0',  PRIMARY KEY (`idpersonas_datos_colegiacion`)) ENGINE = InnoDB";
$sql["20150704"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('12001', '10', 'Ficha de CAJA_LOCAL', '')";
$sql["20150704"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<table style=\"width:100%\">\r\n <tbody>\r\n  <tr>\r\n   <th>C&oacute;digo</th>\r\n   <td>variable_cajalocal_clave</td>\r\n   <th>Regi&oacute;n</th>\r\n   <td>variable_region_nombre</td>\r\n  </tr>\r\n  <tr>\r\n   <th>Nombre</th>\r\n   <td>variable_nombre_caja_local</td>\r\n   <td>&nbsp;</td>\r\n   <td>&nbsp;</td>\r\n  </tr>\r\n </tbody>\r\n</table>\r\n' WHERE `idgeneral_contratos` = '12001'";

$sql["20150705"][]	= "ALTER TABLE `entidad_pagos_perfil` CHANGE COLUMN `tipo_de_ingreso` `tipo_de_membresia` INT(4) NULL DEFAULT '0' COMMENT 'tipo de mebresia a la entidad'";
$sql["20150705"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_help_id`) VALUES ('7024', '7020', 'Editar Pagos por Membresia', 'frmsocios/pagos_por_membresia.frm.php', 'tiny', '', 'pago', 'command', '7024')";
$sql["20150705"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('4060', 'El Servidor FTP no se encuentra Activo', 'common')";
$sql["20150705"][]	= "UPDATE `operaciones_tipos` SET `formula_de_calculo`='' WHERE ISNULL(`formula_de_calculo`)";
$sql["20150705"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion`='' WHERE ISNULL(`formula_de_cancelacion`)";
$sql["20150705"][]	= "ALTER TABLE `operaciones_tipos` CHANGE COLUMN `importancia_de_neutralizacion` `importancia_de_neutralizacion` INT(4) NULL DEFAULT '0' COMMENT 'Orden en que se Cancela' ";
$sql["20150705"][]	= "ALTER TABLE `operaciones_tipos` CHANGE COLUMN `mvto_que_afecta` `mvto_que_afecta` INT(4) NULL DEFAULT '99' COMMENT 'los numeros del 5000 al 5999 estan reservados a empresas especiales. 5101 5102 5103'";
$sql["20150705"][]	= "ALTER TABLE `entidad_pagos_perfil` ADD COLUMN `prioridad` INT(3) NULL DEFAULT 0 AFTER `monto`";
$sql["20150705"][]	= "DELETE FROM general_log WHERE text_log LIKE '%SELECT DISTINCT socios_relaciones%'";
$sql["20150705"][]	= "DELETE FROM general_log WHERE text_log LIKE '%INSERT INTO tesoreria_cajas%'";
$sql["20150705"][]	= "DELETE FROM general_log WHERE text_log LIKE '%INSERT INTO `general_contratos`%'";
$sql["20150705"][]	= "DELETE FROM general_log WHERE text_log LIKE '%SQL[ INSERT INTO%'";
$sql["20150705"][]	= "DELETE FROM general_log WHERE text_log LIKE '%SQL[ ALTER TABLE%'";
$sql["20150705"][]	= "DELETE FROM general_log WHERE text_log LIKE '%INSERT INTO contable_movimientos%'";
$sql["20150705"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('103', 'FORM', 'RECIBOS.SIN_VERSION_IMPRESA', '', '', '\$valor=true;', '');";
$sql["20150705"][]	= "ALTER TABLE `socios_relacionestipos` ADD COLUMN `mostrar` INT(2) NULL DEFAULT '1' AFTER `tiene_vinculo_patrimonial`";
$sql["20150705"][]	= "CREATE TABLE IF NOT EXISTS `personas_membresia_tipo` (  `idpersonas_membresia_tipo` INT(4) NOT NULL AUTO_INCREMENT,  `descripcion_membresia_tipo` VARCHAR(80) NULL,   PRIMARY KEY (`idpersonas_membresia_tipo`)) ENGINE = InnoDB COMMENT = 'Tipos de mebresias'";
$sql["20150705"][]	= "INSERT INTO `personas_membresia_tipo` (`idpersonas_membresia_tipo`, `descripcion_membresia_tipo`) VALUES ('1', 'AFILIACION NORMAL')";
$sql["20150705"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('20514', '2050', 'Tipos de Membresía', 'frmsocios/tipo_de_membresia.frm.php', 'tiny', 'Tipos de Membresia', 'personas', 'command', '20514'";
$sql["20150705"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/desembolsos_por_fechas.rpt.php?', 'Creditios.- Desembolsos por Fechas', 'general_creditos', '10006', '', '1006')";
$sql["20150705"][]	= "ALTER TABLE `creditos_tipoconvenio` ADD COLUMN `nombre_corto` VARCHAR(20) NULL DEFAULT '' AFTER `omitir_seguimiento`";
$sql["20150705"][]	= "UPDATE `creditos_tipoconvenio` SET `nombre_corto`= UPPER(TRIM(REPLACE(`descripcion_tipoconvenio`, 'CREDITO', '')))";
$sql["20150705"][]	= " UPDATE `general_reports` SET `idgeneral_reports` = '../rptacumulados/cartera_total_por_oficial.rpt.php?' WHERE `idreport` = '114' ";

$sql["20150801"][]	= "CREATE TABLE IF NOT EXISTS `creditos_productos_costos` (  `idcreditos_productos_costos` INT NOT NULL AUTO_INCREMENT,  `clave_de_producto` INT NULL,  `clave_de_operacion` INT NULL DEFAULT '0',  `unidades` FLOAT(8,4) NULL,  `unidad_de_medida` INT(2) NULL DEFAULT '0' COMMENT '0 Peso 1 Porcentaje',  `editable` INT(2) NULL DEFAULT '0',  PRIMARY KEY (`idcreditos_productos_costos`)) ENGINE = InnoDB COMMENT = 'Tabla de comisiones y cosas por el estilo'";
$sql["20150801"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('155','PAGO GASTOS DE ADMINISTRACION','0','0','\$cuenta = CUENTA_DE_CUADRE;','CARGOS POR GASTOS DE ADMINISTRACION','999','9303','0','1','99','0','0','0','0','0','0','0','','ninguna','0','0','','','0','0','0.000','GASTOS ADMIN','1')";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PLANEACION DE CREDITOS(DEP)' , `estatus` = '0' WHERE `idoperaciones_tipos` = '112' ";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'DESEMBOLSO DE CREDITO' WHERE `idoperaciones_tipos` = '110'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'TRASP.-CARTERA VIGENTE-VENCIDA' WHERE `idoperaciones_tipos` = '111'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'TRASP.-CARTERA VIGENTE-REESTRUCTURAS' WHERE `idoperaciones_tipos` = '113'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'TRASP.-CARTERA VENCIDA-VIGENTE' WHERE `idoperaciones_tipos` = '114'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'TRASP.-CARTERA VIGENTE A MORA' WHERE `idoperaciones_tipos` = '115' ";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO DE COMISION POR APERTURA DE CRED' WHERE `idoperaciones_tipos` = '147'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO DE COMISIONES POR SEGUIMIENTO' WHERE `idoperaciones_tipos` = '145'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'OPERACIONESDE REINVERSION' WHERE `idoperaciones_tipos` = '223'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO DE INTERESES COBRADOS POR ANTICIPADO' WHERE `idoperaciones_tipos` = '351'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO PARTE SOCIAL CORRIENTE' WHERE `idoperaciones_tipos` = '701'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO APORT. SOCIAL VOLUNTARIA' WHERE `idoperaciones_tipos` = '702'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO PARTE SOCIAL PERMANENTE' WHERE `idoperaciones_tipos` = '703' ";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO DEP. EN GARANTIA LIQUIDA(DEP)' WHERE `idoperaciones_tipos` = '901'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'PAGO MULTAS A FAVOR DE TERCEROS' WHERE `idoperaciones_tipos` = '1002'";
$sql["20150801"][]	= "UPDATE  `general_menu` SET `menu_file` = 'frmtipos/operaciones_tipos.lista.frm.php' , `menu_image` = 'operaciones' WHERE `idgeneral_menu` = '3035'";
$sql["20150801"][]	= "CREATE TABLE IF NOT EXISTS `personas_checklist` (`idpersonas_checklist` INT NOT NULL AUTO_INCREMENT, `clave_de_persona` BIGINT(20) NOT NULL DEFAULT 0, `fecha_de_checklist` DATE NULL, `entregaa` INT(2) NULL DEFAULT 0, `entregab` INT(2) NULL DEFAULT 0, `entregac` INT(2) NULL DEFAULT 0, `entregad` INT(2) NULL DEFAULT 0, `entregae` INT(2) NULL DEFAULT 0, `entregaf` INT(2) NULL DEFAULT 0, `entregag` INT(2) NULL DEFAULT 0, `entregah` INT(2) NULL DEFAULT 0, `entregai` INT(2) NULL DEFAULT 0, `entregaj` INT(2) NULL DEFAULT 0, PRIMARY KEY (`idpersonas_checklist`)) ENGINE = InnoDB";
$sql["20150801"][]	= "ALTER TABLE `personas_checklist` ADD COLUMN `entregak` INT(2) NULL DEFAULT '0' AFTER `entregaj`,ADD COLUMN `entregal` INT(2) NULL DEFAULT '0' AFTER `entregak`,ADD COLUMN `entregam` INT(2) NULL DEFAULT '0' AFTER `entregal`,ADD COLUMN `entregan` INT(2) NULL DEFAULT '0' AFTER `entregam`,ADD COLUMN `entregao` INT(2) NULL DEFAULT '0' AFTER `entregan`,ADD COLUMN `entregap` INT(2) NULL DEFAULT '0' AFTER `entregao`,ADD COLUMN `entregaq` INT(2) NULL DEFAULT '0' AFTER `entregap`,ADD COLUMN `entregar` INT(2) NULL DEFAULT '0' AFTER `entregaq`,ADD COLUMN `entregas` INT(2) NULL DEFAULT '0' AFTER `entregar`,ADD COLUMN `entregat` INT(2) NULL DEFAULT '0' AFTER `entregas`";

$sql["20150801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes' WHERE `idgeneral_menu` = '5070'";
$sql["20150801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Cuentas' WHERE `idgeneral_menu` = '5071'";
$sql["20150801"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('5072', '5070', 'Reportes e-contables', 'frmcontabilidad/envios.econtabilidad.frm.php', 'tiny', 'reporte', 'command', '5070', '5070')";
$sql["20150801"][]	= "UPDATE `general_menu` SET `menu_destination` = 'tiny' WHERE `idgeneral_menu` = '72203'";
$sql["20150801"][]	= "CREATE TABLE IF NOT EXISTS `contable_equivalencias` (`idcontable_equivalencias` INT NOT NULL, `equivalencia` VARCHAR(25) NULL, `nombre_equivalencias` VARCHAR(120) NULL, `origen_equivalencia` INT(2) NULL, `tipo_de_cuenta` VARCHAR(3) NULL COMMENT 'AA Activo acredora PA Pasivo Acreedora', PRIMARY KEY (`idcontable_equivalencias`)) ENGINE = InnoDB ";
$sql["20150801"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcontables/ebalanza.rpt.php?', 'XML.- Balanza de Comprobación Anual', 'econtabilidad', '50001', '', '1')";
$sql["20150801"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcontables/epolizas.rpt.php?', 'XML.- Polizas Contables', 'econtabilidad', '50002', '', '2')";
$sql["20150801"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcontables/ecatalogo.rpt.php?', 'XML.- Catalogo Contable', 'econtabilidad', '50003', '', '3')";
$sql["20150801"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcontables/eauxiliares.rpt.php?', 'XML.- Auxiliar del Catalogo', 'econtabilidad', '50004', '', '4')";

$sql["20150801"][]	= "CREATE TABLE IF NOT EXISTS `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva` INT NOT NULL AUTO_INCREMENT, `tipo_de_credito` INT(4) NULL DEFAULT 0 COMMENT 'consumo comercial', `limite_inferior` INT(5) NULL DEFAULT 0, `limite_superior` INT(5) NULL DEFAULT 0, `tasa_de_reserva` FLOAT(8,4) NULL DEFAULT 0, PRIMARY KEY (`idcreditos_nievelesdereserva`)) ENGINE = InnoDB";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_superior`, `tasa_de_reserva`) VALUES ('1', '1', '7', '0.01')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('2', '1', '8', '15', '0.05')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('3', '1', '16', '30', '0.10')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('4', '1', '31', '60', '0.25') ";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('5', '1', '61', '90', '0.5') ";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('6', '1', '91', '120', '0.75')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('7', '1', '121', '180', '1')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('8', '1', '181', '9999', '1.1')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_superior`, `tasa_de_reserva`) VALUES ('9', '2', '7', '0.01')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('10', '2', '8', '15', '0.05')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('11', '2', '16', '30', '0.10')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('12', '2', '31', '60', '0.25') ";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('13', '2', '61', '90', '0.5') ";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('14', '2', '91', '120', '0.75')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('15', '2', '121', '180', '1')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('16', '2', '181', '9999', '1.1')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_superior`, `tasa_de_reserva`) VALUES ('17', '3', '7', '0.01')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('18', '3', '8', '15', '0.05')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('19', '3', '16', '30', '0.1') ";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('20', '3', '31', '60', '0.25')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('21', '3', '61', '90', '0.5') ";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('22', '3', '91', '120', '0.75')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('23', '3', '121', '180', '1')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('24', '3', '181', '9999', '1.1')";
$sql["20150801"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`, `limite_inferior`, `limite_superior`, `tasa_de_reserva`) VALUES ('25', '99', '1', '9999', '1')";
$sql["20150801"][]	= "UPDATE `creditos_nievelesdereserva` SET `limite_inferior` = '1' WHERE `idcreditos_nievelesdereserva` = '17'";
$sql["20150801"][]	= "UPDATE `creditos_nievelesdereserva` SET `limite_inferior` = '1' WHERE `idcreditos_nievelesdereserva` = '9'";
$sql["20150801"][]	= "UPDATE `creditos_nievelesdereserva` SET `limite_inferior` = '1' WHERE `idcreditos_nievelesdereserva` = '1' ";
$sql["20150801"][]	= "UPDATE `general_menu` SET `menu_parent` = '7020' , `menu_title` = 'Bases del Sistema' , `menu_description` = '' , `menu_image` = 'panel' WHERE `idgeneral_menu` = '11015' ";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion` (`codigo_de_base`, `descripcion`, `tipo_de_base`) VALUES ('101', 'ESTADO DE CUENTA DE APORTACIONES', 'de_operaciones')";

$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('553', '101', '701', 'PAGO PARTE SOCIAL PERMANENTE', '100')";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('554', '101', '702', 'PAGO APORTVOLUNTARIA', '100')";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('555', '101', '703', 'PAGO PP')";
$sql["20150801"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `descripcion_de_la_relacion` = 'PAGO PARTE SOCIAL CORRIENTE' WHERE `ideacp_config_bases_de_integracion_miembros` = '553' AND `codigo_de_base` = '101' AND `miembro` = '701'";
$sql["20150801"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `descripcion_de_la_relacion` = 'PAGO PARTE SOCIAL PERMANENTE' , `subclasificacion` = '100' WHERE `ideacp_config_bases_de_integracion_miembros` = '555' AND `codigo_de_base` = '101' AND `miembro` = '703'";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('556', '101', '704', 'APORTACIONES POR DONACION', '100')";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('557', '101', '705', 'PARTES SOCIALES EN CUENTA CONCENTRADORA', '100')";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('558', '101', '706', 'APORT CORRIENTE POR DONACION', '100')";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('559', '101', '707', 'PARTE SOCIAL A CONCENTRADORA', '100')";
$sql["20150801"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('560', '101', '902', 'APORT FONDO DEFUNCION', '100')";
$sql["20150801"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `recibo_ultimo_capital` BIGINT(25) NULL DEFAULT 0 AFTER `fecha_ultimo_capital`";
$sql["20150801"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `cuenta_contable`, `descripcion`, `tipo_operacion`, `afectacion_en_recibo`, `constituye_fondo_automatico`, `integra_vencido`, `codigo_de_valoracion`, `formula_de_calculo`, `formula_de_cancelacion`, `preservar_movimiento`, `nombre_corto`, `estatus`) VALUES ('156', 'PAGO SEGURO DE PRESTAMO', '\$cuenta  = CUENTA_DE_CUADRE;', 'Pagos a Seguro de Prestamo', '156', '1', '0', '0', '', '', '', '0', 'SEGURO CREDITO', '1')";
$sql["20150801"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('3002', '30', 'Formato de Compromiso', '<!-- contenido -->')";
$sql["20150801"][]	= "ALTER TABLE `bancos_operaciones` ADD COLUMN `documento_de_origen` BIGINT(20) NULL DEFAULT '0' COMMENT 'Numero de Cuenta Credito etc que lo origina' AFTER `cuenta_de_origen`";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){ \$Credito = new cCredito(\$docto, \$socio); \$Credito->initCredito(); }\r\n\$msg  = \$Credito->setReestructurarIntereses(false, false, true);' WHERE `idoperaciones_tipos` = '140'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){ \$Credito = new cCredito(\$docto, \$socio);\$Credito->initCredito(); }\r\n\$msg  = \$Credito->setReestructurarIntereses(false, false, true);' WHERE `idoperaciones_tipos` = '140'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){ \$Credito = new cCredito(\$docto, \$socio);\$Credito->initCredito(); }\r\n\$msg  = \$Credito->setReestructurarIntereses(false, false, true);' WHERE `idoperaciones_tipos` = '141'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ) {\r\n \$Credito= new cCredito(\$docto, \$socio);\r\n \$Credito->initCredito();\r\n}\r\n\$DCred  = \$Credito->getDatosDeCredito();\r\n\$monto  = \$monto + \$DCred[\"saldo_actual\"];\r\n\$Actualizado = array( \"saldo_actual\" => \$monto);\r\n\$Credito->setUpdate(\$Actualizado);\r\n\$msg  = \$Credito->setReestructurarIntereses(false, false, true);' WHERE `idoperaciones_tipos` = '120'";
$sql["20150801"][]	= "UPDATE  `operaciones_tipos` SET `nombre_corto` = 'SEGURO DEFUNC' WHERE `idoperaciones_tipos` = '902'";
$sql["20150801"][]	= "UPDATE `operaciones_tipos` SET `nombre_corto` = 'AHORRO' WHERE `idoperaciones_tipos` = '220'";
$sql["20150801"][]	= "ALTER TABLE `personas_datos_colegiacion` ADD COLUMN `numero_de_colegiacion` INT(10) NULL DEFAULT '0' AFTER `grado_academico`";
$sql["20150801"][]	= "ALTER TABLE `entidad_pagos_perfil` ADD COLUMN `rotacion` VARCHAR(10) NULL DEFAULT '' COMMENT 'periodo de rotacion puede ser mes, fecha, etc' AFTER `prioridad` ";
$sql["20150801"][]	= "ALTER TABLE `t_03f996214fba4a1d05a68b18fece8e71` CHANGE COLUMN `codigo_de_persona` `codigo_de_persona` BIGINT(20) NULL DEFAULT '1' COMMENT 'Numero de socio o persona asociada' ,ADD COLUMN `alias` VARCHAR(20) NULL DEFAULT '' AFTER `codigo_de_persona` ";
$sql["20150902"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('561', '7013', '155', 'GASTOS DE ADMON')";
$sql["20150902"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('562', '7013', '156', '1', 'SEGURO PRESTAMO')";
$sql["20150902"][]	= "DROP TABLE IF EXISTS `personas_pagos_perfil`";
$sql["20150902"][]	= "ALTER TABLE  `entidad_pagos_perfil` ADD COLUMN `fecha_de_aplicacion` DATE NULL DEFAULT '2015-01-01' COMMENT 'Fecha en que surten efectos los cambios' AFTER `rotacion`";
$sql["20150902"][]	= "CREATE TABLE IF NOT EXISTS  `personas_pagos_perfil` (`idpersonas_pagos_perfil` INT NOT NULL AUTO_INCREMENT,  `clave_de_persona` BIGINT(25) NULL DEFAULT 0,  `tipo_de_operacion` INT(6) NULL DEFAULT 0,  `periocidad` INT(4) NULL DEFAULT 30,  `monto` DOUBLE(12,2) NULL DEFAULT 0,  `prioridad` INT(2) NULL DEFAULT 0,  `rotacion` VARCHAR(20) NULL DEFAULT '', `fecha_de_aplicacion` DATE NULL DEFAULT '2015-01-01' COMMENT 'fecha en que surten efectos los cambios',  PRIMARY KEY (`idpersonas_pagos_perfil`)) ENGINE = InnoDB COMMENT = 'perfil de pagos y aportaciones inicial por tipo de ingreso'";
$sql["20150902"][]	= "CREATE TABLE IF NOT EXISTS `personas_pagos_plan` (`idpersonas_aportaciones_plan` INT NOT NULL AUTO_INCREMENT,  `tipo_de_operacion` INT(6) NULL DEFAULT 0 COMMENT 'segun la tabla de operaciones mvtos',  `persona` BIGINT(25) NULL DEFAULT 1 COMMENT '1 Defecto',  `periodo` INT(4) NULL DEFAULT 0,  `ejercicio` INT(5) NULL DEFAULT 0,  `periocidad` INT(4) NULL DEFAULT 30 COMMENT '15 quincenal 30 mensual',  `monto` DOUBLE(12,2) NULL DEFAULT 0.00,  `observaciones` VARCHAR(100) NULL DEFAULT '',  `estado` INT(2) NULL DEFAULT 1 COMMENT '1 No cobrado 0 Cobrado',  `fecha_de_cancelacion` DATE NULL DEFAULT '0000-00-00',  PRIMARY KEY (`idpersonas_aportaciones_plan`), INDEX `idops` (`persona` ASC, `tipo_de_operacion` ASC)) ENGINE = InnoDB";
$sql["20150902"][]	= "ALTER TABLE `personas_pagos_plan` ADD COLUMN `tipo_de_membresia` INT(4) NULL DEFAULT 0 AFTER `fecha_de_cancelacion`";
$sql["20150902"][]	= "UPDATE `operaciones_tipos` SET `integra_parcialidad` = '1' WHERE `idoperaciones_tipos` = '413'";
$sql["20150902"][]	= "ALTER TABLE `socios_vivienda` ADD COLUMN `clave_de_municipio` INT(6) NULL DEFAULT '0' COMMENT 'id de municipio del pais' AFTER `nombre_de_pais`, ADD COLUMN `clave_de_entidadfederativa` INT(6) NULL DEFAULT '0' AFTER `clave_de_municipio`";
$sql["20150902"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('4032', '4030', 'Modificacion Masiva', 'frmseguimiento/creditos.operaciones-masivas.frm.php', '', 'editar', 'command', '4032', '4032')";
$sql["20150902"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('18561', '18550', 'Carga de Operaciones', 'install/operaciones.upload.frm.php', '', 'importar', 'command', '15561')";

$sql["20150903"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `estado_de_solicitud` INT(3) NULL DEFAULT '0' COMMENT 'Estado de la Solicitud de Credito' AFTER `recibo_ultimo_capital`,ADD COLUMN `clave_de_riesgo` INT(4) NULL DEFAULT '0' COMMENT 'clave de riesgo y equivalencia en SIC 0 NADA' AFTER `estado_de_solicitud`";
$sql["20150903"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `cuenta_contable`, `descripcion`, `tipo_operacion`, `class_efectivo`, `constituye_fondo_automatico`, `integra_vencido`, `codigo_de_valoracion`, `formula_de_calculo`, `formula_de_cancelacion`, `preservar_movimiento`, `nombre_corto`, `estatus`) VALUES ('354', 'CAPITALIZACION DE INTERESES', '\$cuenta = CUENTA_DE_CUADRE;', 'Convierte el Interes Devengado a Capital.', '354', '0', '0', '0', '', '', '', '0', 'CAPITAL INT', '1')";
$sql["20150903"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `bonificaciones_afectadas` DOUBLE(12,2) NULL DEFAULT '0' COMMENT 'Bonificaciones aplicadas o por aplicar' AFTER `clave_de_riesgo`,ADD COLUMN `gastoscobranza_afectadas` DOUBLE(12,2) NULL DEFAULT '0' COMMENT '' AFTER `bonificaciones_afectadas`";

$sql["20150904"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('1003','1000','Abono a Parcialidades','frmcaja/abonos-a-parcialidades.frm.php','principal','NO_DESCRIPTION','dinero','99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro','command','1003','1003','false')";

$sql["20150905"][]	= "INSERT `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) VALUES (414, 'PARCIALIDAD.- CONCEPTOS EN DESGLOSE', '3', '11', '\$cuenta = \"NO_CONTABILIZAR\";', '', '999', '414', '1', '1', '99', '1', '1', '0', '0', '1', '0', '0', '', 'ninguna', '1', '1', '', '', '29', '0', '0.000', '', '1');";
$sql["20150905"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('563', '1001', '414', 'PARC CARGOS DESGLOSADOS')";
$sql["20150905"][]	= "INSERT INTO `creditos_destinos` (`idcreditos_destinos`, `descripcion_destinos`, `destino_credito`) VALUES ('201', 'VIVIENDA.- MEJORAS', '201')";
$sql["20150905"][]	= "INSERT INTO `creditos_destinos` (`idcreditos_destinos`, `descripcion_destinos`, `destino_credito`, `tasa_de_iva`) VALUES ('202', 'VIVIENDA.- AUTOCONSTRUCCION', '202', '0')";
$sql["20150905"][]	= "ALTER TABLE `creditos_productos_costos` ADD COLUMN `en_plan` INT(2) NULL DEFAULT '0' COMMENT 'Indica si se cobraran en los Planes' AFTER `editable`";

$sql["20150905"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('564', '1001', '146', 'PAGOS APORT FORT')";
$sql["20150905"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('565', '1001', '156', 'PAGO SEGURO EN PLANES')";
$sql["20150905"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `ideacp_config_bases_de_integracion_miembros` = '518' AND `codigo_de_base` = '1000' AND `miembro` = '147'";
$sql["20150905"][]	= "CREATE TABLE IF NOT EXISTS `personas_membresia_tipo` (  `idpersonas_membresia_tipo` INT(4) NOT NULL AUTO_INCREMENT,  `descripcion_membresia_tipo` VARCHAR(80) NULL,   PRIMARY KEY (`idpersonas_membresia_tipo`)) ENGINE = InnoDB COMMENT = 'Tipos de mebresias'";
$sql["20150905"][]	= "INSERT INTO `personas_membresia_tipo` (`idpersonas_membresia_tipo`, `descripcion_membresia_tipo`) VALUES ('1', 'AFILIACION NORMAL')";


$sql["20150905"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `estado_de_solicitud` INT(3) NULL DEFAULT '0' COMMENT 'Estado de la Solicitud de Credito' AFTER `recibo_ultimo_capital`,ADD COLUMN `clave_de_riesgo` INT(4) NULL DEFAULT '0' COMMENT 'clave de riesgo y equivalencia en SIC 0 NADA' AFTER `estado_de_solicitud`";
$sql["20150905"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `cuenta_contable`, `descripcion`, `tipo_operacion`, `class_efectivo`, `constituye_fondo_automatico`, `integra_vencido`, `codigo_de_valoracion`, `formula_de_calculo`, `formula_de_cancelacion`, `preservar_movimiento`, `nombre_corto`, `estatus`) VALUES ('354', 'CAPITALIZACION DE INTERESES', '\$cuenta = CUENTA_DE_CUADRE;', 'Convierte el Interes Devengado a Capital.', '354', '0', '0', '0', '', '', '', '0', 'CAPITAL INT', '1')";
$sql["20150905"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `bonificaciones_afectadas` DOUBLE(12,2) NULL DEFAULT '0' COMMENT 'Bonificaciones aplicadas o por aplicar' AFTER `clave_de_riesgo`,ADD COLUMN `gastoscobranza_afectadas` DOUBLE(12,2) NULL DEFAULT '0' COMMENT '' AFTER `bonificaciones_afectadas`";


$sql["20151101"][]	= "ALTER TABLE `socios_general` CHANGE COLUMN `tipo_de_identificacion` `tipo_de_identificacion` INT(5) NULL DEFAULT '220' COMMENT '1 ife, 2 cartilla militar, 3 libreta de mar, 4 fm-3  CAMBIADO' ";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=2201 WHERE `tipo_de_identificacion`=1";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=2205 WHERE `tipo_de_identificacion`=2";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=2206 WHERE `tipo_de_identificacion`=3";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=2204 WHERE `tipo_de_identificacion`=4";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=210 WHERE `tipo_de_identificacion`=1";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=210 WHERE `tipo_de_identificacion`=2";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=210 WHERE `tipo_de_identificacion`=3";
$sql["20151101"][]	= "UPDATE `socios_general` SET `tipo_de_identificacion`=800 WHERE (SELECT COUNT(*) FROM `personas_documentacion_tipos` WHERE `clave_de_control`=`socios_general`.`tipo_de_identificacion`)<=0";

$sql["20151102"][]	= "ALTER TABLE `creditos_solicitud` ADD COLUMN `iva_interes` DOUBLE(12,2) NULL DEFAULT '0.00' COMMENT '' AFTER `gastoscobranza_afectadas`,ADD COLUMN `iva_otros` DOUBLE(12,2) NULL DEFAULT '0.00' COMMENT '' AFTER `iva_interes`, ADD COLUMN `saldo_exigible` DOUBLE(14,2) NULL DEFAULT '0.00' COMMENT '' AFTER `iva_otros`";
$sql["20151102"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-generales.ext.rpt.php?', 'Creditos.- Reporte con pagos', 'general_creditos', '1731', 'Reporte con todos los filtros', '11'); ";
$sql["20151102"][]	= "INSERT INTO  `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('566', '2620', '141', 'PAGO INTS MORATORIOS')";
$sql["20151102"][]	= "UPDATE  `eacp_config_bases_de_integracion_miembros` SET `afectacion` = '0' WHERE `ideacp_config_bases_de_integracion_miembros` = '566' AND `codigo_de_base` = '2620' AND `miembro` = '141'";
$sql["20151102"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('104', 'FORM', 'CREDITOS.ESTADO_DE_CUENTA.FICHA_SIMPLE', '', '', '\$valor=true;', '')";
$sql["20151102"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('105', 'FORM', 'CREDITOS.ESTADO_DE_CUENTA.VALIDADOR', '', '', '\$valor=false;', '')";
$sql["20151102"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('106', 'FORM', 'CREDITOS.ESTADO_DE_CUENTA.DETALLADO', '', '', '\$valor=true;', '')";
$sql["20151102"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('107', 'FORM', 'PERSONAS.RELS.SOLO_ACTIVOS', '', '', '\$valor=false;', '')";
$sql["20151102"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('108', 'FORM', 'CREDITOS.DESEMBOLSO_SIN_CHEQUE', '', '', '\$valor=false;', '')";

$sql["20151103"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('13001', '10', 'Firma del Principal', '<table class=\'firma\'>\r\n<tr>\r\n <td>POR SU PROPIO Y PERSONAL DERECHO.</td>\r\n<tr>\r\n</tr>\r\n <td>\r\n  <br /><br /><br />\r\n  ________________________\r\n </td>\r\n<tr>\r\n</tr>\r\n <td>variable_persona_nombre_completo</td>\r\n</tr>\r\n</table>')";

$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `tipo_autorizacion` `tipo_autorizacion` INT(4) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `periodo_solicitudes` `periodo_solicitudes` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `destino_credito` `destino_credito` INT(10) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `ultimo_periodo_afectado` `ultimo_periodo_afectado` INT(4) NOT NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `cadena_heredada` `cadena_heredada` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `tasa_ahorro` `tasa_ahorro` FLOAT(8,5) NULL DEFAULT '0.00000' COMMENT '' ,CHANGE COLUMN `grupo_asociado` `grupo_asociado` BIGINT(20) NOT NULL DEFAULT '999' COMMENT '' ,CHANGE COLUMN `fecha_ministracion` `fecha_ministracion` DATE NOT NULL DEFAULT '2005-12-31' COMMENT '' ,CHANGE COLUMN `fecha_revision` `fecha_revision` DATE NULL DEFAULT '2006-01-01' COMMENT '' ,CHANGE COLUMN `fecha_castigo` `fecha_castigo` DATE NULL DEFAULT '2006-12-04' COMMENT '' ,CHANGE COLUMN `saldo_conciliado` `saldo_conciliado` FLOAT(12,2) NULL DEFAULT '0.00' COMMENT '' ,CHANGE COLUMN `notas_auditoria` `notas_auditoria` VARCHAR(200) NULL DEFAULT NULL COMMENT '' ,CHANGE COLUMN `fecha_conciliada` `fecha_conciliada` DATE NULL DEFAULT '2006-12-04' COMMENT '' ,CHANGE COLUMN `eacp` `eacp` VARCHAR(15) NOT NULL DEFAULT 'EN_TRAMITE' COMMENT '' ,CHANGE COLUMN `tipo_de_pago` `tipo_de_pago` INT(4) UNSIGNED NOT NULL DEFAULT '2' COMMENT '' ,CHANGE COLUMN `tipo_de_calculo_de_interes` `tipo_de_calculo_de_interes` INT(2) NULL DEFAULT '2' COMMENT '' , CHANGE COLUMN `persona_asociada` `persona_asociada` BIGINT(20) NULL DEFAULT '0' COMMENT '' , CHANGE COLUMN `perfil_de_intereses` `perfil_de_intereses` INT(4) NULL DEFAULT '1' COMMENT '' , CHANGE COLUMN `fuente_de_fondeo` `fuente_de_fondeo` INT(4) NULL DEFAULT '1' COMMENT '' , CHANGE COLUMN `fecha_de_primer_pago` `fecha_de_primer_pago` DATE NULL DEFAULT '2014-01-01' COMMENT '' , CHANGE COLUMN `operacion_origen` `operacion_origen` BIGINT(25) NULL DEFAULT '1' COMMENT '' , CHANGE COLUMN `tipo_de_origen` `tipo_de_origen` INT(11) NULL DEFAULT '1' COMMENT '' , CHANGE COLUMN `tipo_de_dias_de_pago` `tipo_de_dias_de_pago` INT(10) NULL DEFAULT '1' COMMENT '' , CHANGE COLUMN `tipo_de_lugar_de_pago` `tipo_de_lugar_de_pago` INT(4) NULL DEFAULT '1' COMMENT '' , CHANGE COLUMN `tipo_de_dispersion` `tipo_de_dispersion` INT(4) NULL DEFAULT '1' COMMENT '' , CHANGE COLUMN `omitir_seguimiento` `omitir_seguimiento` INT(2) NULL DEFAULT '0' COMMENT '' , CHANGE COLUMN `fecha_ultimo_capital` `fecha_ultimo_capital` DATE NULL DEFAULT '0000-00-00' COMMENT '' , CHANGE COLUMN `estado_de_solicitud` `estado_de_solicitud` INT(3) NULL DEaFAULT '0' COMMENT '' , CHANGE COLUMN `clave_de_riesgo` `clave_de_riesgo` INT(4) NULL DEFAULT '0' COMMENT '' , CHANGE COLUMN `bonificaciones_afectadas` `bonificaciones_afectadas` DOUBLE(12,2) NULL DEFAULT '0.00' COMMENT '' ;";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` DROP COLUMN `estatus_de_negociacion`";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `observacion_solicitud` `observacion_solicitud` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `cadena_heredada` `cadena_heredada` VARCHAR(100) NOT NULL DEFAULT '' COMMENT '' ";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `fecha_vencimiento` `fecha_vencimiento` DATE NOT NULL DEFAULT '0000-00-00' COMMENT '' ,CHANGE COLUMN `descripcion_aplicacion` `descripcion_aplicacion` VARCHAR(150) NOT NULL DEFAULT 'N/A' COMMENT '' ,CHANGE COLUMN `fecha_vencimiento_dinamico` `fecha_vencimiento_dinamico` DATE NULL DEFAULT '2008-08-01' COMMENT '' , CHANGE COLUMN `causa_de_mora` `causa_de_mora` INT(4) NOT NULL DEFAULT '99' COMMENT '' , CHANGE COLUMN `fecha_de_proximo_pago` `fecha_de_proximo_pago` DATE NULL DEFAULT '0000-00-00' COMMENT '' ";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `operacion_origen` `operacion_origen` BIGINT(20) NULL DEFAULT '1' COMMENT ''";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `cadena_heredada` `cadena_heredada` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '' , CHANGE COLUMN `notas_auditoria` `notas_auditoria` VARCHAR(80) NULL DEFAULT NULL COMMENT ''";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` DROP COLUMN `fecha_revision`, DROP COLUMN `cadena_heredada`";


$sql["20151104"][]	= "UPDATE `creditos_solicitud` SET `notas_auditoria`=''";
$sql["20151104"][]	= "DELETE FROM `general_structure` WHERE `index_struct` = '421'";
$sql["20151104"][]	= "DELETE FROM `general_structure` WHERE `index_struct` = '429'";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `docto_autorizacion` `docto_autorizacion` VARCHAR(82) NOT NULL DEFAULT 'NO_AUTORIZADO' COMMENT '' ,CHANGE COLUMN `numero_pagos` `numero_pagos` INT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `periocidad_de_pago` `periocidad_de_pago` INT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `pagos_autorizados` `pagos_autorizados` INT(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `destino_credito` `destino_credito` INT(5) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `observacion_solicitud` `observacion_solicitud` VARCHAR(98) NOT NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `descripcion_aplicacion` `descripcion_aplicacion` VARCHAR(98) NOT NULL DEFAULT 'N/A' COMMENT '' ,CHANGE COLUMN `notas_auditoria` `notas_auditoria` VARCHAR(55) NULL DEFAULT NULL COMMENT '' ,CHANGE COLUMN `tipo_de_origen` `tipo_de_origen` INT(5) NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `tipo_de_dias_de_pago` `tipo_de_dias_de_pago` INT(3) NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `recibo_ultimo_capital` `recibo_ultimo_capital` BIGINT(20) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `bonificaciones_afectadas` `bonificaciones` DOUBLE(12,2) NULL DEFAULT '0.00' COMMENT '' ,CHANGE COLUMN `gastoscobranza_afectadas` `gastoscbza` DOUBLE(12,2) NULL DEFAULT '0.00' COMMENT '' ";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `estado_de_solicitud` `estat_sol` INT(3) NULL DEFAULT '0' COMMENT ''";
$sql["20151104"][]	= "ALTER TABLE `operaciones_mvtos` CHANGE COLUMN `detalles` `detalles` VARCHAR(100) NULL DEFAULT NULL COMMENT '' , CHANGE COLUMN `cadena_heredada` `cadena_heredada` VARCHAR(50) NULL DEFAULT NULL COMMENT '' ";
$sql["20151104"][]	= "ALTER TABLE `creditos_solicitud` CHANGE COLUMN `clave_de_riesgo` `criesgo` INT(4) NULL DEFAULT '0' COMMENT ''";

$sql["20151105"][]	= "ALTER TABLE `creditos_solicitud` DROP COLUMN `saldo_exigible`";
$sql["20151105"][]	= "UPDATE `creditos_tipoconvenio` SET `code_valoracion_javascript`=''";

$sql["20151105"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){\$Credito= new cCredito(\$docto, \$socio); \$Credito->init();\r\n}; \$Credito->setReestructurarIntereses(false, false, true);' WHERE `idoperaciones_tipos` = '120'; ";
$sql["20151105"][]	= "ALTER TABLE `creditos_solicitud` CHANGE `tipo_autorizacion` `tipo_autorizacion` int(4) unsigned NOT NULL DEFAULT '99' AFTER `estatus_actual`, CHANGE `periodo_solicitudes` `periodo_solicitudes` int(10) unsigned NOT NULL DEFAULT '0' AFTER `dias_autorizados`, CHANGE `ultimo_periodo_afectado` `ultimo_periodo_afectado` int(4) NOT NULL DEFAULT '0' AFTER `saldo_vencido`, CHANGE `tasa_ahorro` `tasa_ahorro` float(8,5) NULL DEFAULT '0.00000' AFTER `observacion_solicitud`, CHANGE `grupo_asociado` `grupo_asociado` bigint(20) NOT NULL DEFAULT '999' AFTER `tasa_ahorro`,CHANGE `fecha_ministracion` `fecha_ministracion` date NOT NULL DEFAULT '2005-12-31' AFTER `descripcion_aplicacion`,CHANGE `fecha_castigo` `fecha_castigo` date NULL DEFAULT '2006-12-04' AFTER `oficial_seguimiento`,CHANGE `saldo_conciliado` `saldo_conciliado` float(12,2) NULL DEFAULT '0.00' AFTER `fecha_castigo`,CHANGE `fecha_conciliada` `fecha_conciliada` date NULL DEFAULT '2006-12-04' AFTER `notas_auditoria`,CHANGE `eacp` `eacp` varchar(15) COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'EN_TRAMITE' AFTER `sucursal`,CHANGE `tipo_de_pago` `tipo_de_pago` int(4) unsigned NOT NULL DEFAULT '2' AFTER `interes_normal_devengado`,CHANGE `tipo_de_calculo_de_interes` `tipo_de_calculo_de_interes` int(2) NULL DEFAULT '2' AFTER `fecha_vencimiento_dinamico`,CHANGE `persona_asociada` `persona_asociada` bigint(20) NULL DEFAULT '0' AFTER `causa_de_mora`,CHANGE `perfil_de_intereses` `perfil_de_intereses` int(4) NULL DEFAULT '1' AFTER `persona_asociada`,CHANGE `fuente_de_fondeo` `fuente_de_fondeo` int(4) NULL DEFAULT '1' AFTER `perfil_de_intereses`,CHANGE `fecha_de_primer_pago` `fecha_de_primer_pago` date NULL DEFAULT '2014-01-01' AFTER `fuente_de_fondeo`,CHANGE `tipo_de_lugar_de_pago` `tipo_de_lugar_de_pago` int(4) NULL DEFAULT '1' AFTER `tipo_de_dias_de_pago`,CHANGE `tipo_de_dispersion` `tipo_de_dispersion` int(4) NULL DEFAULT '1' AFTER `tipo_de_lugar_de_pago`,CHANGE `omitir_seguimiento` `omitir_seguimiento` int(2) NULL DEFAULT '0' AFTER `fecha_de_proximo_pago`,CHANGE `fecha_ultimo_capital` `fecha_ultimo_capital` date NULL DEFAULT '0000-00-00' AFTER `tasa_cat`;";

$sql["20151106"][]	= "ALTER TABLE `socios_general` CHANGE COLUMN `personalidad_juridica` `personalidad_juridica` INT(4) NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `tipo_de_identificacion` `tipo_de_identificacion` INT(5) NULL DEFAULT '220' COMMENT '' ,CHANGE COLUMN `documento_de_identificacion` `documento_de_identificacion` VARCHAR(18) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `nivel_de_riesgo_aml` `nivel_de_riesgo_aml` INT(4) NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `clave_de_firma_electronica` `clave_de_firma_electronica` VARCHAR(100) NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `regimen_fiscal` `regimen_fiscal` INT(4) NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `nacionalidad_extranjera` `nacionalidad_extranjera` INT(4) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `xclasificacion` `xclasificacion` INT(4) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `yclasificacion` `yclasificacion` INT(4) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `zclasificacion` `zclasificacion` INT(4) NULL DEFAULT '0' COMMENT '' ,ADD COLUMN `sitioweb` VARCHAR(80) NULL DEFAULT '' COMMENT '' AFTER `zclasificacion`";
$sql["20151106"][]	= "DELETE FROM `general_structure` WHERE `tabla`='entidad_pagos_perfil'";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','identidad_pagos_perfil','primary_key','int','11',NULL,'Clave','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','tipo_de_membresia','0','int','4',NULL,'Tipo de membresia','select','SELECT idpersonas_membresia_tipo, descripcion_membresia_tipo FROM personas_membresia_tipo','derecha','1',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','tipo_de_operacion','0','int','6',NULL,'Tipo de operacion','select','SELECT idoperaciones_tipos, descripcion_operacion   FROM operaciones_tipos','derecha','2',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','periocidad','30','int','4',NULL,'Periocidad','select','SELECT idcreditos_periocidadpagos, descripcion_periocidadpagos FROM creditos_periocidadpagos','derecha','3',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','monto','0.00','double','25',NULL,'Monto','text',NULL,'derecha','4',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','prioridad','0','int','3',NULL,'Prioridad','text',NULL,'derecha','5',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','rotacion','','varchar','10',NULL,'Rotacion','text',NULL,'derecha','6',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "insert into `general_structure` ( `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('entidad_pagos_perfil','fecha_de_aplicacion','2015-01-01','date','0',NULL,'Fecha de aplicacion','text',NULL,'derecha','7',NULL,NULL,'','normalfield','');";
$sql["20151106"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' WHERE `idgeneral_menu` = '7024'";

$sql["20151201"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8003', '10', 'Ficha Firma Avales 3', '')";
$sql["20151201"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8004', '10', 'Ficha Info Avales 4', '')";

$sql["20151202"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('102', '10', 'Ficha de Credito.- Caratula', '')";
$sql["20151202"][]	= "ALTER TABLE `creditos_productos_otros_parametros` CHANGE COLUMN `valor_del_parametro` `valor_del_parametro` VARCHAR(100) NULL DEFAULT NULL COMMENT ''";

$sql["20151202"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('901', 'Creditos.- Correccion de Ultimos Pagos', '')";

$sql["20151203"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Personas. Coincidencias en Lista Negra' WHERE `clave_de_control` = '901001'";
$sql["20151203"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Personas. Persona con vinculos a otra en Lista Negra' WHERE `clave_de_control` = '901002'";
$sql["20151203"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones. Con Personas en Lista Negra' WHERE `clave_de_control` = '101501'";
$sql["20151203"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones. Con partes relacionadas con personas en Lista Negra' WHERE `clave_de_control` = '101502'";
$sql["20151203"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones. Con personas Politicamente Expuestas' WHERE `clave_de_control` = '101510'";
$sql["20151203"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('811', '80', 'PLD.- Dictamen de Alertas', '')";
$sql["20151203"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('812', '80', 'PLD.- Dictamen de Riesgos', '')";

$sql["20151205"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('2027', '2020', 'Reporte de Paises', 'rptsocios/catalogo.paises.rpt.php', '_blank', '', 'report', 'command', '2027', '2027')";
$sql["20151205"][]	= "UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = 1 WHERE `es_considerado_riesgo`<=0";
$sql["20151205"][]	= "UPDATE `empresas_operaciones` SET `fecha_inicial`=DATE_ADD(`fecha_de_operacion`,INTERVAL 1 DAY) WHERE ISNULL(`fecha_inicial`)";
$sql["20151205"][]	= "UPDATE `empresas_operaciones` SET `fecha_final`=DATE_ADD(`fecha_inicial`,INTERVAL `periodo_marcado` DAY) WHERE ISNULL(`fecha_final`)";
$sql["20151205"][]	= "UPDATE `empresas_operaciones` SET `fecha_de_cobro`=DATE_ADD(`fecha_final`,INTERVAL 1 DAY) WHERE ISNULL(`fecha_de_cobro`)";


$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE TABLA='trabajador_inasistencias' ";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE TABLA='cajeros' ";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE TABLA='creditos'";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE TABLA='creditos_lineas'";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='creditos_no_castigados';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='creditos_no_castigados_conicliados';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='domicilios';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='eacp_config_common';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='eacp_config_control_interno';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='eacp_config_creditos';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='eacp_config_domicilio';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='eacp_config_porcentajes_de_reserva';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='operaciones_no_estadisticas';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='operaciones_sumas';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='recuperaciones_netas';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='reporte_federacion';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='sisbancs_amortizaciones';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='sisbancs_temp_creditos';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='sisbancs_temp_depositos';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='sisbancs_temp_inversion';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='socios';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='socios_scoring_simple';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='solicitudes';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='sumas_flujo_efectivo';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='suma_mvtos_poliza';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_anuales';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_credito_al_salario';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_credito_al_salario_anual';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_deduccion_isr_salarios';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_deduccion_isr_salarios_anual';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_isr_salarios';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_subsidio_al_empleo';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_subsidio_isr_salarios';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tarifas_subsidio_isr_salarios_anual';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='tcb_prestamos_movimientos';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='temp_captacion_por_socio';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='temp_sisbancs_depositos';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='trabajador_conceptos';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='trabajador_general';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='trabajador_historico_salarios';";
$sql["20160101"][]	= "DELETE FROM `general_structure` WHERE tabla='empresas_cobranza' OR tabla='empresas_operaciones' OR tabla='aml_alerts' ";

$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','idempresas_cobranza','primary_key','int','11','','clave','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','clave_de_nomina','','int','11','','Clave de Nomina','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','clave_de_credito','','bigint','20','','Clave de Credito','text',NULL,'derecha','1',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','parcialidad','0','int','11','','Parcialidad','text',NULL,'derecha','3',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','monto_enviado','0.00','double','29','','Monto','text',NULL,'derecha','4',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','observaciones','','varchar','100','','Observaciones','text',NULL,'derecha','6',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','saldo_inicial','0.00','double','29','','Saldo inicial','text',NULL,'derecha','5',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_cobranza','estado','1','int','2','','Estado','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','idempresas_operaciones','primary_key','int','11',NULL,'Clave de Nomina','text',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','clave_de_empresa','99','int','11',NULL,'Empresa','select','SELECT idsocios_aeconomica_dependencias, descripcion_dependencia  FROM socios_aeconomica_dependencias','derecha','1',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','periodo_marcado','0','int','11',NULL,'Periodo','text',NULL,'derecha','4',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','tipo_de_operacion','1','int','11',NULL,'Operacion','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','fecha_de_operacion','','date','0',NULL,'Fecha de operacion','text',NULL,'derecha','20',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','monto','0.00','float','25',NULL,'Monto','text',NULL,'derecha','5',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','oficial','99','int','11',NULL,'Oficial','select','SELECT id, nombre_completo  FROM oficiales','derecha','6',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','periocidad','7','int','11',NULL,'Periocidad','select','SELECT idcreditos_periocidadpagos, descripcion_periocidadpagos FROM creditos_periocidadpagos','derecha','3',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','observaciones','','varchar','150',NULL,'Observaciones','text',NULL,'derecha','30',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','fecha_de_cobro','','date','0',NULL,'Fecha de cobro','text',NULL,'derecha','21',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','fecha_inicial','','date','0',NULL,'Fecha inicial','text',NULL,'derecha','22',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('empresas_operaciones','fecha_final','','date','0',NULL,'Fecha final','text',NULL,'derecha','23',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','clave_de_control','primary_key','int','11',NULL,'Clave de control','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','tipo_de_aviso','1','int','11',NULL,'Tipo de aviso','select','SELECT clave_de_control, descripcion FROM aml_risk_catalog','derecha','1',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','persona_de_destino','1','bigint','20',NULL,'Oficial','select','SELECT id, nombre_completo  FROM oficiales','derecha','2',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','documento_relacionado','1','bigint','20',NULL,'Documento','text',NULL,'derecha','4',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','persona_de_origen','1','bigint','20',NULL,'Persona de Origen','text',NULL,'derecha','5',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','fecha_de_origen','0','bigint','20',NULL,'Fecha de origen','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','fecha_de_checking','0','bigint','20',NULL,'Fecha de checking','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','hora_de_proceso','0','bigint','20',NULL,'Hora de proceso','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','medio_de_envio','','varchar','20',NULL,'Medio de envio','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','estado_en_sistema','1','int','11',NULL,'Estado en sistema','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','riesgo_calificado','0','int','11',NULL,'Riesgo','select','SELECT clave_de_control, nombre_del_nivel FROM aml_risk_levels','derecha','10',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','mensaje','','mediumtext','0',NULL,'Mensaje','textarea',NULL,'derecha','11',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','usuario','1','int','11',NULL,'Usuario de Origen','select','SELECT id, nombre_completo  FROM oficiales','derecha','3',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','sucursal','1','int','11',NULL,'Sucursal','select','SELECT clave_numerica, nombre_sucursal FROM general_sucursales','derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','entidad','1','int','11',NULL,'Entidad','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','fecha_de_registro','0','bigint','20',NULL,'Fecha de registro','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','notas_de_checking','','text','0',NULL,'Notas de Dictamen','textarea',NULL,'derecha','16',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','tipo_de_documento','0','int','5',NULL,'Tipo de documento','hidden',NULL,'derecha','17',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','tercero_relacionado','1','bigint','20',NULL,'Persona Relacionada','text',NULL,'derecha','6',NULL,NULL,'','normalfield','');";
$sql["20160101"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('aml_alerts','resultado_de_checking','0','int','2','Si esta verificado','Resultado de checking','hidden',NULL,'derecha','0',NULL,NULL,'','normalfield','');";

$sql["20160102"][]	= "INSERT INTO  `contable_polizas_perfil` (`idcontable_poliza_perfil`, `tipo_de_recibo`, `tipo_de_operacion`, `descripcion`, `operacion`, `formula_posterior`) VALUES ('9909', '2', '156', 'SEGURO PREST', '-1', '')";


$sql["20160201"][]	= "ALTER TABLE `personas_datos_extranjero` ADD COLUMN `pais_de_nacionalidad` VARCHAR(5) NULL DEFAULT '' COMMENT '' AFTER `fecha_de_vencimiento`";
$sql["20160201"][]	= "ALTER TABLE `personas_domicilios_paises` ADD COLUMN `gentilicio` VARCHAR(40) NULL DEFAULT '' COMMENT '' AFTER `clave_alfanumerica` ";

$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'AFGANOS' WHERE `clave_de_control` = 'AF'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'ARMENIOS' WHERE `clave_de_control` = 'AM'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'ANGOLEÑOS' WHERE `clave_de_control` = 'AO'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'ARGENTINOS' WHERE `clave_de_control` = 'AR'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'NORCOREANOS' WHERE `clave_de_control` = 'KP'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'IRANI' WHERE `clave_de_control` = 'IR'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'INDONESIO' WHERE `clave_de_control` = 'ID'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'NICARAGUENSE' WHERE `clave_de_control` = 'NI'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'TURCOS' WHERE `clave_de_control` = 'TR'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'NEPALI' WHERE `clave_de_control` = 'NP' ";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'BANGLADI' WHERE `clave_de_control` = 'BD'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'ARGELINO' WHERE `clave_de_control` = 'DZ'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'TANZANIO' WHERE `clave_de_control` = 'TZ' ";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'NIGERIANO' WHERE `clave_de_control` = 'NG'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'TAYIKIZTANO' WHERE `clave_de_control` = 'TJ'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'SUDANI' WHERE `clave_de_control` = 'SD'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'KENIATA' WHERE `clave_de_control` = 'KE'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'MONGOL' WHERE `clave_de_control` = 'MN' ";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'MYANMAR' WHERE `clave_de_control` = 'MM'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'SIRIO' WHERE `clave_de_control` = 'SY'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'CUBANO' WHERE `clave_de_control` = 'CU'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'MARROQUI' WHERE `clave_de_control` = 'MA'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'IRAQUI' WHERE `clave_de_control` = 'IQ'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'VIETNAMITA' WHERE `clave_de_control` = 'VN'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'ALBANO' WHERE `clave_de_control` = 'AL'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'CAMBOYANO' WHERE `clave_de_control` = 'KH'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'YEMENI' WHERE `clave_de_control` = 'YE'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'KIRGUISTANO' WHERE `clave_de_control` = 'KG'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'ETIOPI' WHERE `clave_de_control` = 'ET'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'KUWAITI' WHERE `clave_de_control` = 'KW'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'PAKISTANI' WHERE `clave_de_control` = 'PK'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio` = 'ECUATORIANO' WHERE `clave_de_control` = 'EC'";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `gentilicio`=`nombre_oficial` WHERE `gentilicio`=''";
$sql["20160201"][]	= "UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' , `gentilicio` = 'PANAMEÑO' WHERE `clave_de_control` = 'PA'; ";
$sql["20160201"][]	= "ALTER TABLE  `personas_perfil_transaccional` CHANGE COLUMN `clave_de_persona` `clave_de_persona` BIGINT(20) NULL DEFAULT 1 COMMENT '' ,CHANGE COLUMN `fecha_de_registro` `fecha_de_registro` BIGINT(20) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `fecha_de_vencimiento` `fecha_de_vencimiento` BIGINT(20) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `clave_de_tipo_de_perfil` `clave_de_tipo_de_perfil` INT(11) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `pais_de_origen` `pais_de_origen` VARCHAR(6) NULL DEFAULT 'MXN' COMMENT 'visa prosa etc' ,CHANGE COLUMN `maximo_de_operaciones` `maximo_de_operaciones` INT(11) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `cantidad_maxima` `cantidad_maxima` DOUBLE(18,3) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `operaciones_calculadas` `operaciones_calculadas` INT(6) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `cantidad_calculada` `cantidad_calculada` DOUBLE(18,3) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `fecha_de_calculo` `fecha_de_calculo` BIGINT(20) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `afectacion` `afectacion` INT(11) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `observaciones` `observaciones` VARCHAR(100) NULL DEFAULT '' COMMENT '' ,ADD COLUMN `recurso_origen` VARCHAR(100) NULL DEFAULT '' COMMENT '' AFTER `observaciones`,ADD COLUMN `recurso_aplicacion` VARCHAR(100) NULL DEFAULT '' COMMENT '' AFTER `recurso_origen` ";
$sql["20160201"][]	= "ALTER TABLE  `personas_perfil_transaccional` CHANGE COLUMN `afectacion` `afectacion` INT(3) NULL DEFAULT '0' COMMENT ''";
$sql["20160201"][]	= "ALTER TABLE `socios_relacionestipos` CHANGE COLUMN `descripcion_relacionestipos` `descripcion_relacionestipos` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `descripcion_larga` `descripcion_larga` VARCHAR(100) NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `puntuacion_en_credit_scoring` `puntos_en_scoring` FLOAT(6,2) NULL DEFAULT 0 COMMENT '' ,ADD COLUMN `checar_aml` INT(2) NULL DEFAULT '1' COMMENT '' AFTER `mostrar`";
$sql["20160201"][]	= "CREATE TABLE IF NOT EXISTS `entidad_calificacion` ( `identidad_calificacion` INT NOT NULL AUTO_INCREMENT COMMENT '',  `tipo_de_objeto` INT(4) NULL COMMENT '400 ide_recibo ide_credito',  `clave_de_documento` BIGINT(20) NULL DEFAULT 0 COMMENT 'clave de persona, credito, etc',  `fecha_de_revision` DATE NULL COMMENT '',  `usuario` INT(6) NULL COMMENT '',  `topico` VARCHAR(100) NULL COMMENT 'regla de negocio',  `cumple` INT(2) NULL COMMENT '0 no 1 si',  `tiempo` INT(10) NULL COMMENT 'tiempo unix de revision',  `vencimiento` INT(10) NULL COMMENT 'tiempo unix de vencimiento',  `riesgo` FLOAT(6,3) NULL DEFAULT 0 COMMENT 'Puntos de riesgo',   PRIMARY KEY (`identidad_calificacion`)  COMMENT '') ENGINE = InnoDB COMMENT = 'Califica si no si existe los requisitos'";
$sql["20160201"][]	= "ALTER TABLE `operaciones_mvtos` CHANGE COLUMN `estatus_mvto` `estatus_mvto` INT(3) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `periodo_cobranza` `periodo_cobranza` INT(5) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `periodo_mensual` `periodo_mensual` INT(3) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `periodo_semanal` `periodo_semanal` INT(3) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `detalles` `detalles` VARCHAR(80) NULL DEFAULT NULL COMMENT '' ,CHANGE COLUMN `docto_neutralizador` `docto_neutralizador` BIGINT(20) NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `cadena_heredada` `cadena_heredada` VARCHAR(20) NULL DEFAULT NULL COMMENT '' ,CHANGE COLUMN `tasa_asociada` `tasa_asociada` FLOAT(7,4) NULL DEFAULT '0.0000' COMMENT '' ,CHANGE COLUMN `dias_asociados` `dias_asociados` INT(4) NULL DEFAULT '0' COMMENT '' ";

$sql["20160201"][]	= "ALTER TABLE `sistema_lenguaje` CHANGE COLUMN `extension` `extension` VARCHAR(50) NULL DEFAULT NULL COMMENT '' ";
$sql["20160201"][]	= "CREATE TABLE IF NOT EXISTS `sistema_mensajes` ( `idsistema_mensajes` INT NOT NULL AUTO_INCREMENT COMMENT '',  `topico` VARCHAR(80) NULL COMMENT 'Clave en Cadena',  `mensaje` VARCHAR(150) NULL COMMENT 'mensajes del Sistema',  PRIMARY KEY (`idsistema_mensajes`)  COMMENT '') ENGINE = INNODB";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200901', 'CREDITO_FALTA_AVALES', 'El Credito Debe tener igual numero de avales')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200902', 'CREDITO_FALTA_GARANTIAS', 'El Credito debe tener completa sus garantias')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200903', 'CREDITO_SIN_GRUPO', 'El Credito debe tener un Grupo valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200904', 'CREDITO_SIN_PLAN', 'El Credito debe tener un plan de pagos')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200905', 'CREDITO_SIN_CUENTAAHORRO', 'El Credito debe tener una Cuenta Corriente')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200906', 'CREDITO_FALLA_CUENTAAHORRO', 'El Credito debe tener una Cuenta Corriente valida')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200907', 'CREDITO_FALLA_OFICIAL', 'El Credito debe tener un Oficial Valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200908', 'CREDITO_ESTADO_INCORRECTO', 'El Credito debe tener un Estado valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200909', 'CREDITO_NPAGOS_INCORRECTO', 'El Credito debe tener un numero de pagos Valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200910', 'CREDITO_FALLA_PAGOMIN', 'El Credito debe tener un minimo de pagos')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200911', 'CREDITO_FALLA_PAGOMAX', 'El Credito debe tener un maximo de Pagos')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200912', 'CREDITO_FALLA_DESTINO', 'El credito debe tener un Destino valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200913', 'CREDITO_FALLA_DESTINO_IVA', 'El Credito debe tener un impuesto igual al Destino del Credito')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200914', 'CREDITO_FALLA_FECHA_PERIODO', 'El Credito tiene una Fecha Mayor al Periodo de Credito')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200915', 'CREDITO_FALTA_PERIODO', 'El Credito debe tener un periodo de Credito Valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200900', 'CREDITO_FALLA_PERSONA', 'El Credito Debe Tener una Persona Valida')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100901', 'PERSONA_FALTA_GRUPO', 'La Persona debe tener un Grupo valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100902', 'PERSONA_FALTA_CAJALOCAL', 'La persona debe tener un Centro de Trabajo Valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100903', 'PERSONA_FALTA_EMPRESA', 'La persona debe tener un Empresa Valida')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100904', 'PERSONA_FALTA_OFICIAL', 'La Persona debe tener un Oficial Valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100905', 'PERSONA_FALTA_REP_LEGAL', 'La Persona debe tener Representante Legal')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100906', 'PERSONA_FALTA_DOMICILIO', 'La persona debe tener un Domicilio')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100907', 'PERSONA_FALLA_DOMICILIO', 'La persona debe tener un Domicilio Valido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100908', 'PERSONA_FALTA_ACT_ECONOM', 'La persona debe tener una Actividad Economica')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100909', 'PERSONA_FALLA_ACT_ECONOM', 'La Persona debe tener una Actividad Economica Valida')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100910', 'PERSONA_FALTA_TIPO_INGRESO', 'La Persona debe tener un Tipo de Ingreso')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100911', 'PERSONA_FALLA_AML1', 'El Riesgo de la Persona debe ser igual al Sistema o estar excluido')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100912', 'PERSONA_FALLA_AML2', 'La persona debe tener al menos un Perfil Transaccional')";
$sql["20160201"][]	= "UPDATE `general_utilerias` SET `describe_param_1` = 'CORREGIR: SI/NO' WHERE `idgeneral_utilerias` = '8202'";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100913', 'PERSONA_DOMICILIO_INCOMP', 'El Domiclio de la persona debe estar completo')";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100914', 'PERSONA_FALTA_DEXTRA', 'Los Datos de Extranjero deben estar completos')";
$sql["20160201"][]	= "ALTER TABLE `socios_aeconomica` CHANGE COLUMN `tipo_aeconomica` `tipo_aeconomica` BIGINT(20) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `sector_economico` `sector_economico` BIGINT(20) UNSIGNED NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `idusuario` `idusuario` INT(8) NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `puesto` `puesto` VARCHAR(65) NULL DEFAULT 'NA' COMMENT '' ,CHANGE COLUMN `oficial_de_verificacion` `oficial_de_verificacion` INT(10) NOT NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `estado_actual` `estado_actual` INT(3) NOT NULL DEFAULT '99' COMMENT '' ,CHANGE COLUMN `domicilio_vinculado` `domicilio_vinculado` INT(11) NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `ae_clave_de_localidad` `ae_clave_de_localidad` BIGINT(20) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `ae_codigo_postal` `ae_codigo_postal` INT(8) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `fecha_de_ingreso` `fecha_de_ingreso` DATE NULL DEFAULT '0000-00-00' COMMENT '' ,CHANGE COLUMN `empleado_tipo_de_dispersion` `empleado_tipo_de_dispersion` INT(4) NULL DEFAULT '100' COMMENT '' ";
$sql["20160201"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100915', 'PERSONA_DOM_CP_VALIDO', 'El Codigo Postal Debe existir y ser valido')";
$sql["20160201"][]	= "INSERT INTO `general_municipios` (`idgeneral_municipios`, `clave_de_entidad`, `nombre_del_municipio`, `habitantes`, `indice_de_marginacion`, `grado_de_marginacion`) VALUES ('99999', '99', 'EXTRANJERO', '0', '0', 'Bajo')";
$sql["20160201"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('20011', 'El Credito Esta Incompleto en requisitos o Datos', 'common'); ";
$sql["20160201"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-solicitados.rpt.php?', 'Creditos.- Solicitados', 'general_creditos', '10007', '', '10007')";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Desembolsos por Fechas' , `order_index` = '10006' WHERE `idreport` = '10006'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Saldos' WHERE `idreport` = '10002'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Liquidaciones por Fechas' , `explicacion` = '' WHERE `idreport` = '10001'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Ingresos por Fechas' WHERE `idreport` = '174'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Operaciones por Fechas' WHERE `idreport` = '64'";
$sql["20160201"][]	= "ALTER TABLE `creditos_estatus` CHANGE COLUMN `descripcion_estatus` `descripcion_estatus` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `estatus_actual` `estatus_actual` INT(4) NULL DEFAULT '0' COMMENT 'DEP' ,CHANGE COLUMN `titulo_general` `titulo_general` VARCHAR(40) NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `orden_clasificacion` `orden_clasificacion` INT(4) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `respetar_plan_de_pagos` `respetar_plan_de_pagos` ENUM('0','1') NULL DEFAULT '1' COMMENT '' ,ADD COLUMN `tit_solicitados` VARCHAR(30) NULL COMMENT '' AFTER `respetar_plan_de_pagos`,ADD COLUMN `tit_autorizados` VARCHAR(30) NULL COMMENT '' AFTER `tit_solicitados`";
$sql["20160201"][]	= "ALTER TABLE `creditos_estatus` DROP COLUMN `tit_autorizados`,CHANGE COLUMN `tit_solicitados` `tit_proceso` VARCHAR(30) NULL DEFAULT '' COMMENT ''";
$sql["20160201"][]	= "UPDATE `creditos_estatus` SET `tit_proceso` = 'OPERANDO' WHERE `idcreditos_estatus` = '10'";
$sql["20160201"][]	= "UPDATE `creditos_estatus` SET `tit_proceso` = 'OPERANDO' WHERE `idcreditos_estatus` = '20'";
$sql["20160201"][]	= "UPDATE `creditos_estatus` SET `tit_proceso` = 'OPERANDO'  WHERE `idcreditos_estatus` = '30'";
$sql["20160201"][]	= "UPDATE `creditos_estatus` SET `tit_proceso` = 'FINALIZADO' WHERE `idcreditos_estatus` = '50'";
$sql["20160201"][]	= "UPDATE `creditos_estatus` SET `tit_proceso` = 'AUTORIZADO' WHERE `idcreditos_estatus` = '98'";
$sql["20160201"][]	= "UPDATE `creditos_estatus` SET `tit_proceso` = 'EN PROCESO' WHERE `idcreditos_estatus` = '99'";
$sql["20160201"][]	= "NSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-autorizados.rpt.php?', 'Creditos.- Autorizados', 'general_creditos', '10008', '', '10008')";
$sql["20160201"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu`=3006";
$sql["20160201"][]	= "CREATE TABLE IF NOT EXISTS `creditos_montos` (  `idcreditos_montos` INT NOT NULL AUTO_INCREMENT COMMENT '',  `clave_de_credito` BIGINT(20) NULL DEFAULT 0 COMMENT '',  `sucursal` VARCHAR(20) NULL DEFAULT 'matriz' COMMENT '',  `marca_tiempo` INT(10) NULL DEFAULT 0 COMMENT 'Marca de tiempo Unix',  `marca_acceso` INT(10) NULL DEFAULT 0 COMMENT 'Marca de acceso Unix',  `interes_n_dev` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Interes Normal Devengado',  `interes_n_pag` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Interes Normal Pagado\n',  `interes_m_dev` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Interes Moratorio Devengado',  `interes_m_pag` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Interes Moratorio Pagado',  `interes_n_corr` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Interes Normal Corriente',  `interes_m_corr` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Interes Moratorio Corriente',  `cargos_cbza` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Cargos de Cobranza',  `imptos_int_n` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Impuestos Interes Normal',  `imptos_int_m` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Impuestos Interes Moratorio',  `imptos_otros` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '',  `penas` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '',  `bonificaciones` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '',  `capital_exigible` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '',  `f_primer_atraso` DATE NULL DEFAULT '0000-00-00' COMMENT 'Fecha de Primer Atraso',  `f_ultimo_atraso` DATE NULL DEFAULT '0000-00-00' COMMENT 'Capital exigible a la Fecha de calculo',  `otros1_id` INT(5) NULL DEFAULT 0 COMMENT 'Clave de otros',  `otros1_m` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Monto de Otros',  `otros2_id` INT(5) NULL DEFAULT 0 COMMENT '',  `otros2_m` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '',  `otros_nc` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Otros No clasificados',  `usuario` INT(6) NULL DEFAULT 0 COMMENT 'Usuario que genera',  `guardar` INT(2) NULL DEFAULT 0 COMMENT 'Indica si el calculo se guarda por el Usuario',  `t_iva_int_n` FLOAT(8,3) NULL DEFAULT 0 COMMENT 'Tasa Interes Normal',  `t_iva_m` FLOAT(8,3) NULL DEFAULT 0 COMMENT 'Tasa Iva Ints Moratorio',  `t_iva_o` FLOAT(8,3) NULL DEFAULT 0 COMMENT 'tasa Iva Otros\n',  `otros_si` DOUBLE(12,2) NULL DEFAULT 0 COMMENT 'Otros Sin Iva',   PRIMARY KEY (`idcreditos_montos`)  COMMENT '') ENGINE = INNODB";
$sql["20160201"][]	= "INSERT INTO `creditos_montos` (`clave_de_credito`,`sucursal`,`marca_tiempo`,`marca_acceso`,`interes_n_dev`,`interes_n_pag`,`interes_m_dev`,`interes_m_pag`,`cargos_cbza`,`bonificaciones`,`imptos_int_n`,`imptos_otros`,`t_iva_int_n`,`t_iva_m`,`t_iva_o`,`usuario`) SELECT `numero_solicitud`,`sucursal`,UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), `interes_normal_devengado`,`interes_normal_pagado`,`interes_moratorio_devengado`,`interes_moratorio_pagado`,`gastoscbza`,`bonificaciones`,`iva_interes`,`iva_otros`,`getTasaIVAPorApp`(`destino_credito`),`getTasaIVAGeneral`(), `getTasaIVAGeneral`(),`idusuario` FROM `creditos_solicitud`";
$sql["20160201"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('1013', '1010', 'Operaciones con Cajas', 'frmtesoreria/cajas.operaciones.frm.php', '', 'cajas', 'command', '1013', '1013')";
$sql["20160201"][]	= "ALTER TABLE `creditos_sdpm_historico` CHANGE COLUMN `fecha_actual` `fecha_actual` DATE NULL DEFAULT '0000-00-00' COMMENT '' ,CHANGE COLUMN `fecha_anterior` `fecha_anterior` DATE NULL DEFAULT '0000-00-00' COMMENT '' ,CHANGE COLUMN `monto_calculado` `monto_calculado` DOUBLE(18,2) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `saldo` `saldo` DOUBLE(18,2) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `interes_normal` `interes_normal` DOUBLE(14,2) NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `interes_moratorio` `interes_moratorio` DOUBLE(14,2) NULL DEFAULT '0' COMMENT '' ";

$sql["20160201"][]	= "UPDATE `general_menu` SET `menu_title` = 'Credito Individual' , `menu_description` = 'Reporte de Credito Individual' WHERE `idgeneral_menu` = '3072'";
$sql["20160201"][]	= "UPDATE `general_utilerias` SET `describe_param_1` = 'NUMERO_DE_CREDITO' , `describe_param_2` = 'SOLO_CON_SALDO: SI/NO' WHERE `idgeneral_utilerias` = '889'";
$sql["20160201"][]	= "UPDATE `general_utilerias` SET `describe_param_2` = 'INCLUIR_TODOS: SI/NO' , `describe_param_3` = 'NA' WHERE `idgeneral_utilerias` = '857'";

$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Saldo por Mes' WHERE `idreport` = '801'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Colocacion por producto' WHERE `idreport` = '35'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Solicitudes por Producto' WHERE `idreport` = '36'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Monto recuperado' WHERE `idreport` = '37'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Sumas Acumuladas' , `explicacion` = '' WHERE `idreport` = '38'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Operaciones.- Suma de operaciones' WHERE `idreport` = '55'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Personas.- Numero por Punto de Acceso' WHERE `idreport` = '56'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Concentracion por Destino' WHERE `idreport` = '112'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Concentracion por Oficial' WHERE `idreport` = '114'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Concentracion por Sucursal' WHERE `idreport` = '117'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Suma colocado por Genero de Persona' WHERE `idreport` = '119'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Personas.- Personas registradas por genero' WHERE `idreport` = '125'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Personas.- Suma por Tipo de Ingreso' WHERE `idreport` = '126'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Concentracion por Estado' WHERE `idreport` = '127'";
$sql["20160201"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Creditos.- Concentracion por periodicidad' WHERE `idreport` = '164'";
$sql["20160201"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptacumulados/creditos-acumulados_por_modalidad.rpt.php?', 'Creditos.- Saldos mensuales por Modalidad', 'general_acumulados', '99111', 'Muestra por Modalidad CONSUMO, VIVIENDA, COMERCIAL, los creditos mediante Fecha Final del reporte.', '99111')";
$sql["20160201"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptacumulados/creditos-acumulados_por_entidadfed.rpt.php?', 'Creditos.- Concentracion por Entidad Federal', 'general_acumulados', '99112', '', '99112')";

$sql["20160201"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) VALUES ('603', 'CARGO DE PENAS POR ATRASO', '1', '0', '\$cuenta = CUENTA_DE_CUADRE;', 'Cargos por Atrasos en penas', '999', '603', '1', '1', '99', '0', '1', '0', '0', '1', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0', '', '1')";
$sql["20160201"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) VALUES ('148', 'PAGO DE PENAS Y SANCIONES', '0', '0', '\$cuenta = \"5202\";', 'Monto cobrado por atrasos', '99', '148', '1', '1', '99', '1', '1', '0', '0', '0', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0.160', 'PAGO PENAS', '1')";
$sql["20160201"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('567', '7020', '148', 'PAGO PENAS Y SANCIONES'); ";
$sql["20160201"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('568', '1001', '148', 'PAGO DE PENAS Y SANCIONES'); ";
$sql["20160201"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('569', '1000', '148', 'PAGO DE PENAS Y SANCIONES'); ";
$sql["20160201"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('570', '2002', '148', 'PAGO DE PENAS Y SANCIONES');";
$sql["20160201"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('571', '7013', '148', 'PAGO DE PENAS Y SANCIONES');";
$sql["20160201"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('572', '10000', '148', 'PAGO DE PENAS Y SANCIONES');";
$sql["20160201"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('573', '10001', '148', 'PAGO DE PENAS Y SANCIONES');";
$sql["20160201"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `codigo_de_base`=8001;";
$sql["20160201"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `codigo_de_base`=8002;";
$sql["20160201"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `codigo_de_base`=8003;";
$sql["20160201"][]	= "DELETE FROM `eacp_config_bases_de_integracion` WHERE `codigo_de_base` = '8001'; ";
$sql["20160201"][]	= "DELETE FROM `eacp_config_bases_de_integracion` WHERE `codigo_de_base` = '8002'; ";
$sql["20160201"][]	= "DELETE FROM `eacp_config_bases_de_integracion` WHERE `codigo_de_base` = '8003'; ";
$sql["20160201"][]	= "UPDATE `eacp_config_bases_de_integracion` SET `descripcion` = 'REPORTE DE INGRESOS' WHERE `codigo_de_base` = '10001';";
$sql["20160201"][]	= "UPDATE `eacp_config_bases_de_integracion` SET `descripcion` = 'TODA OPERACION QUE PUEDE SER GRAVADA' WHERE `codigo_de_base` = '7020';";
$sql["20160201"][]	= "ALTER TABLE `general_formulas` CHANGE COLUMN `estructura_de_la_formula` `estructura_de_la_formula` TEXT NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `code_type` `code_type` ENUM('php', 'js', 'human', 'null', 'mysql') NULL DEFAULT 'php' COMMENT '' ,CHANGE COLUMN `description_short` `description_short` VARCHAR(50) NULL DEFAULT '' COMMENT ''";
$sql["20160201"][]	= "INSERT INTO  `general_formulas` (`aplicado_a`, `estructura_de_la_formula`, `code_type`) VALUES ('sql_mora_x_letra', '(letras.capital * DATEDIFF(getFechaDeCorte(), fecha_de_pago) * (creditos_solicitud.tasa_moratorio ))/getDivisorDeInteres()', 'mysql')";
$sql["20160201"][]	= "ALTER TABLE `creditos_productos_costos` ADD COLUMN `exigencia` INT(2) NULL DEFAULT '0' COMMENT 'Exigencia total en pago total' AFTER `en_plan`";
$sql["20160201"][]	= "INSERT INTO `general_formulas` (`aplicado_a`, `estructura_de_la_formula`) VALUES ('php_pena_x_letra', 'if (\$DIAS_PENA >  0){\$moratorio=0;\$penas=((\$SALDO_ACTUAL-\$MONTO_PROXIMO_PAGO)*(\$TASA_NORMAL/360)*\$DIAS_PENA);}')";
$sql["20160201"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('1051', 'Credito Modificado', 'common')";
$sql["20160201"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('1010', 'Credito Eliminado', 'common')";

$sql["20160301"][]	= "ALTER TABLE `operaciones_tipos` CHANGE COLUMN `idoperaciones_tipos` `idoperaciones_tipos` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'los numeros del 5000 al 5999 estan reservados a empresas especiales. 5101 5102 5103' ,CHANGE COLUMN `descripcion` `descripcion` VARCHAR(100) NULL DEFAULT NULL COMMENT '' ,CHANGE COLUMN `tipo_operacion` `tipo_operacion` INT(4) NULL DEFAULT '0' COMMENT 'DEPRECATED' ,CHANGE COLUMN `visible_reporte` `visible_reporte` INT(4) NULL DEFAULT '1' COMMENT 'DEPRECATED' ,CHANGE COLUMN `estatus` `estatus` INT(2) NOT NULL COMMENT '1activo' ";
$sql["20160301"][]	= "UPDATE  `operaciones_tipos` SET `recibo_que_afecta`=999 WHERE `recibo_que_afecta`=99";
$sql["20160301"][]	= "UPDATE  `operaciones_tipos` SET `recibo_que_afecta`=98 WHERE `idoperaciones_tipos`=353 OR `idoperaciones_tipos`=1010 OR `idoperaciones_tipos`=1011";
$sql["20160301"][]	= "UPDATE  `operaciones_tipos` SET `recibo_que_afecta`=99 WHERE `idoperaciones_tipos`=146 OR `idoperaciones_tipos`=147 OR `idoperaciones_tipos`=148 OR `idoperaciones_tipos`=152 OR `idoperaciones_tipos`=155 OR `idoperaciones_tipos`=156 OR `idoperaciones_tipos`=1002 OR `idoperaciones_tipos`=1003 OR `idoperaciones_tipos`=1004 OR `idoperaciones_tipos`=1006";
$sql["20160301"][]	= "UPDATE  `operaciones_tipos` SET `tasa_iva`=`getTasaIVAGeneral`()  WHERE `idoperaciones_tipos`=146 OR `idoperaciones_tipos`=147 OR `idoperaciones_tipos`=148 OR `idoperaciones_tipos`=152 OR `idoperaciones_tipos`=155 OR `idoperaciones_tipos`=156 OR `idoperaciones_tipos`=1002 OR `idoperaciones_tipos`=1006";
$sql["20160301"][]	= "UPDATE  `operaciones_tipos` SET `tasa_iva`=0  WHERE  `idoperaciones_tipos`=1003 OR `idoperaciones_tipos`=1004";

$sql["20160301"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`) VALUES ('1008', '1000', 'Cobranza de Cuotas', 'frmcaja/cobranza.membresias.frm.php', '', 'moneda', 'command', '1008')";



$sql["20160401"][]	= "ALTER TABLE `creditos_lineas` ADD COLUMN `tasa` FLOAT(6,3) NULL DEFAULT 0 COMMENT '' AFTER `razones_de_cancelacion`,ADD COLUMN `periocidad` INT(4) NULL DEFAULT 30 COMMENT '' AFTER `tasa`";
$sql["20160401"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes por Empresa' , `menu_image` = 'empresas' , `menu_order` = '12023' , `menu_help_id` = '2023' WHERE `idgeneral_menu` = '2023'";
$sql["20160401"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '2023'";
$sql["20160401"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('999', 'OTROS', '1')";
$sql["20160401"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('160','ANUALIDAD DE CREDITO','0','0','\$cuenta = CUENTA_DE_CUADRE;','ANUALIDAD DE CREDITO','999','160','0','1','99','0','0','0','0','0','0','0','','ninguna','0','0','','','0','0','0.000','ANUALIDAD','1')";
$sql["20160401"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `ideacp_config_bases_de_integracion_miembros` = '571' WHERE `ideacp_config_bases_de_integracion_miembros` = '570' AND `codigo_de_base` = '7013' AND `miembro` = '148'";
$sql["20160401"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `ideacp_config_bases_de_integracion_miembros` = '572' WHERE `ideacp_config_bases_de_integracion_miembros` = '570' AND `codigo_de_base` = '10000' AND `miembro` = '148'";
$sql["20160401"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `ideacp_config_bases_de_integracion_miembros` = '573' WHERE `ideacp_config_bases_de_integracion_miembros` = '570' AND `codigo_de_base` = '10001' AND `miembro` = '148'";
$sql["20160401"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('575','7020','160','1.0000','PAGO DE ANUALIDAD CRED','0')";
$sql["20160401"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('576','1001','160','1.0000','PAGO DE ANUALIDAD CRED','0')";
$sql["20160401"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('577','1000','160','1.0000','PAGO DE ANUALIDAD CRED','0')";
$sql["20160401"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('578','2002','160','1.0000','PAGO DE ANUALIDAD CRED','0')";
$sql["20160401"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('579','7013','160','1.0000','PAGO DE ANUALIDAD CRED','0')";
$sql["20160401"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('580','10000','160','1.0000','PAGO DE ANUALIDAD CRED','0')";
$sql["20160401"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('581','10001','160','1.0000','PAGO DE ANUALIDAD CRED','0')";
$sql["20160401"][]	= "UPDATE  `operaciones_tipos` SET `afectacion_en_recibo` = '1' WHERE `idoperaciones_tipos` = '160'";
$sql["20160401"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `ideacp_config_bases_de_integracion_miembros` = '567' AND `codigo_de_base` = '7020' AND `miembro` = '148'";
$sql["20160401"][]	= "DELETE FROM `eacp_config_bases_de_integracion_miembros` WHERE `ideacp_config_bases_de_integracion_miembros` = '575' AND `codigo_de_base` = '7020' AND `miembro` = '160'";
$sql["20160401"][]	= "UPDATE `operaciones_tipos` SET `integra_parcialidad` = '1' WHERE `idoperaciones_tipos` = '148'";
$sql["20160401"][]	= "UPDATE `operaciones_tipos` SET `integra_parcialidad` = '1' WHERE `idoperaciones_tipos` = '160'";
$sql["20160401"][]	= "UPDATE `general_menu` SET `menu_order` = '1' WHERE `idgeneral_menu` = '7021'";
$sql["20160401"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`) VALUES ('24', 'FORM', 'PERSONAS.USAR_DATOSCOLEGIACION', '', '', '\$valor=false;')";
$sql["20160401"][]	= "INSERT INTO `personas_xclasificacion` (`idpersonas_xclasificacion`, `descripcion_xclasificacion`, `xclasificacion_etiquetas`) VALUES ('1', 'XCLASIFICACION', 'X'); ";
$sql["20160401"][]	= "UPDATE `socios_grupossolidarios` SET `direccion_gruposolidario` = 'NA' , `representante_numerosocio` = '1' , `representante_nombrecompleto` = '' WHERE `idsocios_grupossolidarios` = '99'; ";
$sql["20160401"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptgrupos/rpt_estado_cuenta_grupo.php?' WHERE `idreport` = '4'";
$sql["20160401"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptgrupos/rpt_listado_socios_xgrupo.php?' WHERE `idreport` = '5'";
$sql["20160401"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptgrupos/rpt_listado_de_grupos.php?' WHERE `idreport` = '42'";
$sql["20160401"][]	= "INSERT INTO `personas_yclasificacion` (`idpersonas_yclasificacion`, `descripcion_yclasificacion`, `yclasificacion_etiquetas`) VALUES ('1', 'YCLASIFICACION', 'Y')";
$sql["20160401"][]	= "INSERT INTO `personas_zclasificacion` (`idpersonas_zclasificacion`, `descripcion_zclasificacion`, `zclasificacion_etiquetas`) VALUES ('1', 'ZCLASIFICACION', 'Z')";

$sql["20160402"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Comision' WHERE `idsistema_lenguaje` = '1069'";
$sql["20160402"][]	= "UPDATE `sistema_lenguaje` SET `traduccion` = 'Parametro' WHERE `idsistema_lenguaje` = '837'";

$sql["20160402"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('556', 'FORM', 'PERSONAS.ACTIVIDAD_ECONOMICA.SIN_DOMICILIO', '', '', '\$valor=false;', '')";
$sql["20160402"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('557', 'FORM', 'CREDITOS.TASAS_PUEDEN_CERO', '', '', '\$valor=false;', '')";

$sql["20160402"][]	= "INSERT INTO `general_formulas` (`aplicado_a`, `estructura_de_la_formula`, `description_short`) VALUES ('php_interes_pago_flat_mod', '', 'Modificador del Interes en pagos FLAT')";
$sql["20160402"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('25', 'FORM', 'PERSONAS.INGRESO.SIN_DNI', '', '', '\$valor=false;', '')";
$sql["20160402"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('558', 'FORM', 'PERSONAS.INGRESO.NO_VALIDAR_DNI', '', '', '\$valor=false;', '')";
$sql["20160402"][]	= "CALL `sp_personas_estadisticas`";
$sql["20160402"][]	= "INSERT INTO  `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('559', 'FORM', 'CREDITOS.PLAN_SIN_ANUALIDAD', '', '', '\$valor=true;', '')";
$sql["20160402"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('560', 'FORM', 'CREDITOS.PLAN_CON_PAGESP', '', '', '\$valor=false;', '')";

$sql["20160403"][]	= "UPDATE `general_reports` SET `aplica` = 'seguimiento' WHERE `idreport` = '1001'";
$sql["20160403"][]	= "UPDATE `general_reports` SET `aplica` = 'seguimiento' WHERE `idreport` = '1002'";
$sql["20160403"][]	= "UPDATE `general_reports` SET `aplica` = 'seguimiento' WHERE `idreport` = '1003'";

$sql["20160404"][]	= "ALTER TABLE `personas_datos_colegiacion` CHANGE COLUMN `numero_de_colegiacion` `numero_de_colegiacion` VARCHAR(15) NULL DEFAULT '0' COMMENT ''";
$sql["20160404"][]	= "UPDATE `operaciones_tipos` SET `cuenta_contable` = '\$cuenta = CUENTA_DE_CUADRE;' WHERE `idoperaciones_tipos` = '160'";
$sql["20160404"][]	= "INSERT INTO  `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('559', 'FORM', 'CREDITOS.PLAN_SIN_ANUALIDAD', '', '', '\$valor=true;', '')";
$sql["20160404"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('560', 'FORM', 'CREDITOS.PLAN_CON_PAGESP', '', '', '\$valor=false;', '')";
$sql["20160404"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('561', 'FORM', 'CREDITOS.PLAN_SIN_OPCIONES', '', '', '\$valor=true;', '')";

$sql["20160405"][]	= "CREATE TABLE IF NOT EXISTS `creditos_preclientes` (  `idcontrol` INT NOT NULL AUTO_INCREMENT COMMENT '',  `nombres` VARCHAR(200) NULL COMMENT '',  `apellido1` VARCHAR(25) NULL COMMENT '',  `apellido2` VARCHAR(25) NULL COMMENT '',  `rfc` VARCHAR(20) NULL COMMENT '',  `curp` VARCHAR(20) NULL COMMENT '',  `telefono` INT(20) NULL COMMENT '',  `fecha_de_registro` DATE NULL COMMENT '',  `producto` INT NULL DEFAULT 99 COMMENT '',  `periocidad` INT NULL DEFAULT 99 COMMENT '',  `pagos` INT NULL DEFAULT 99 COMMENT '',  `aplicacion` INT NULL DEFAULT 99 COMMENT '',  `notas` TEXT NULL COMMENT '',  `monto` DOUBLE(16,2) NULL COMMENT '',   PRIMARY KEY (`idcontrol`)  COMMENT '') ENGINE = InnoDB";
$sql["20160405"][]	= "CREATE TABLE IF NOT EXISTS `creditos_eventos` (  `idcontrol` INT NOT NULL AUTO_INCREMENT COMMENT '',  `personas` BIGINT(20) NULL COMMENT '',  `credito` BIGINT(20) NULL COMMENT '',  `sucursal` VARCHAR(20) NULL COMMENT '',  `idusuario` INT NULL COMMENT '',  `fecha` DATE NULL COMMENT '',  `tiempo` INT NULL COMMENT '',  `evento` VARCHAR(40) NULL COMMENT '', PRIMARY KEY (`idcontrol`)  COMMENT '') ENGINE = InnoDB";
$sql["20160405"][]	= "CREATE TABLE IF NOT EXISTS `sistema_avisos_db` (  `idsistema_avisos_db` INT NOT NULL AUTO_INCREMENT COMMENT '',  `creado` INT NULL DEFAULT 0 COMMENT 'UNIX TIME',  `enviado` INT NULL DEFAULT 0 COMMENT '',  `canal` VARCHAR(10) NULL COMMENT 'mail, sms',  `origen` VARCHAR(45) NULL COMMENT '',  `destinatarios` TEXT NULL COMMENT '',  `titulo` VARCHAR(100) NULL COMMENT '',  `contenido` TEXT NULL COMMENT '',  `comando_attach` VARCHAR(200) NULL COMMENT '',  `comando_enviar` VARCHAR(200) NULL COMMENT '',  `idusuario` INT(6) NULL DEFAULT 0 COMMENT '',  `adjunto1` VARCHAR(150) NULL COMMENT '',  `adjunto2` VARCHAR(150) NULL COMMENT '',  `adjunto3` VARCHAR(150) NULL COMMENT '',  PRIMARY KEY (`idsistema_avisos_db`)  COMMENT '') ENGINE = InnoDB";

$sql["20160405"][]	= "INSERT INTO `sistema_programacion_de_avisos` (`idprograma`, `nombre_del_aviso`, `forma_de_creacion`, `programacion`, `destinatarios`, `microformato`, `tipo_de_medios`, `intent_check`, `intent_command`) VALUES ('500', 'ESTADO DE CUENTA', 'SYS_ALERTA_POR_EVENTO', 'CREDITOS.CUANDO_EDOCTA_EXEC', '', '', ',MAIL', '', 'rpt_edos_cuenta/rptestadocuentacredito.php?credito={credito}&mail={email}'); ";

$sql["20160407"][]	= "ALTER TABLE `creditos_tipo_de_pago` ADD COLUMN `con_capital` INT(2) NULL DEFAULT 1 COMMENT 'Incluye Capital 1 SI' AFTER `descripcion`";
$sql["20160407"][]	= "UPDATE `creditos_tipo_de_pago` SET `con_capital` = '0' WHERE `idcreditos_tipo_de_pago` = '3'";
$sql["20160407"][]	= "UPDATE `creditos_tipo_de_pago` SET `con_capital` = '0' WHERE `idcreditos_tipo_de_pago` = '6'";
$sql["20160407"][]	= "INSERT INTO `creditos_periocidadpagos` (`idcreditos_periocidadpagos`, `descripcion_periocidadpagos`, `periocidad_de_pago`) VALUES ('365', 'ANUAL', '365')";
$sql["20160407"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('20521', '2050', 'Catalogo de Actividades Economicas', 'frmsocios/personas.catalogo-actividades.frm.php', 'Agregar Actividades Economicas', 'catalogo', 'command', '2052', '2052')";

$sql["20160408"][]	= "ALTER TABLE `socios_aeconomica` ADD COLUMN `clave_scian` BIGINT(20) NULL DEFAULT '0' COMMENT '' AFTER `empleado_tipo_de_dispersion`,ADD COLUMN `descripcion` VARCHAR(100) NULL DEFAULT '' COMMENT '' AFTER `clave_scian`";
$sql["20160408"][]	= "UPDATE `general_menu` SET `menu_rules` = '3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw' WHERE `idgeneral_menu` = '1003'";
$sql["20160408"][]	= "INSERT INTO `tesoreria_tipos_de_pago` (`tipo_de_pago`, `tipo_de_movimiento`, `descripcion`, `descripcion_completa`, `equivalente_aml`) VALUES ('tarj.debito.ingreso', '1', 'Tarjeta de Debito', 'Pago con Tarjeta de Debito', '401')";
$sql["20160408"][]	= "INSERT INTO `tesoreria_tipos_de_pago` (`tipo_de_pago`, `tipo_de_movimiento`, `descripcion`, `descripcion_completa`, `equivalente_aml`) VALUES ('tarj.credito.ingreso', '1', 'Tarjeta de Credito', 'Pago con Tarjeta de Credito', '401')";
$sql["20160408"][]	= "ALTER TABLE `tesoreria_tipos_de_pago` CHANGE COLUMN `tipo_de_movimiento` `tipo_de_movimiento` INT(4) NULL DEFAULT 0 COMMENT 'ingreso egreso' ,ADD COLUMN `activo` INT(2) NULL DEFAULT '1' COMMENT '' AFTER `equivalente_aml`";
$sql["20160408"][]	= "CREATE TABLE IF NOT EXISTS `personas_morales_anx` (  `idpersonas_morales_anx` INT NOT NULL AUTO_INCREMENT COMMENT '',  `persona` BIGINT(20) NULL COMMENT '',  `idregistro_publico` VARCHAR(20) NULL COMMENT '',  `fecha_de_constitucion` DATE NULL COMMENT '',  `idacta_constitucion` VARCHAR(50) NULL COMMENT '',  `idpoder_representante` VARCHAR(20) NULL COMMENT '',  `fechapoder_representante` DATE NULL COMMENT '',  `nombre_notario` VARCHAR(100) NULL COMMENT '',  `clave_notaria` VARCHAR(20) NULL DEFAULT '' COMMENT '',  `idregistro1` VARCHAR(20) NULL DEFAULT '' COMMENT '',  `idregistro2` VARCHAR(20) NULL DEFAULT '' COMMENT '',  PRIMARY KEY (`idpersonas_morales_anx`)  COMMENT '') ENGINE = INNODB";

$sql["20160409"][]	= "CREATE TABLE `personas_ae_scian` ( `clave_interna` bigint(20) NOT NULL,  `clave_de_actividad` varchar(20) DEFAULT '',  `nombre_de_la_actividad` varchar(200) DEFAULT NULL,  `clasificacion` varchar(20) DEFAULT NULL,  `clave_de_superior` bigint(20) DEFAULT '0',  `nivel_de_riesgo` int(4) DEFAULT '1' COMMENT 'Nivel de Riesgo AML',  `califica_para_pep` int(2) DEFAULT '0' COMMENT 'asigna si es pep 1SI 0NO',  PRIMARY KEY (`clave_interna`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$sql["20160409"][]	= "UPDATE `general_menu` SET `menu_title` = 'Lista Actividades Economicas' WHERE `idgeneral_menu` = '2026'";
$sql["20160409"][]	= "UPDATE `general_menu` SET `menu_title` = 'Lista de Puntos de Atencion' WHERE `idgeneral_menu` = '99103'";
$sql["20160409"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('2028', '2020', 'Lista Actividades SCIAN', 'rptsocios/scian.catalogo.rpt.php', '_blank', '', 'reporte', '2@rw,99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro', 'command', '2028', '2028', 'false')";

$sql["20160501"][]	= "ALTER TABLE `personas_ae_scian` DROP COLUMN `califica_para_pep`,DROP COLUMN `nivel_de_riesgo`,ADD COLUMN `clave_aml` VARCHAR(20) NULL COMMENT 'clave relacionada en AML' AFTER `clave_de_superior`";
$sql["20160501"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('1001', 'MSG_NO_DATA', 'El Campo o Formulario contiene datos no validos'); ";
$sql["20160501"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('1002', 'MSG_DATA_REQUIRED', 'El Campo Necesita un valor, no puede estar vacio.'); ";
$sql["20160508"][]	= "INSERT INTO `creditos_destinos` (`idcreditos_destinos`, `descripcion_destinos`, `destino_credito`) VALUES ('7100', 'VEHICULOS EN ARRENDAMIENTO PURO', '7000')";
$sql["20160508"][]	= "INSERT INTO `creditos_destinos` (`idcreditos_destinos`, `descripcion_destinos`, `destino_credito`) VALUES ('7101', 'EQUIPO GPRS EN ARRENDAMIENTO PURO', '7101')";
$sql["20160508"][]	= " UPDATE `creditos_destinos` SET `destino_credito` = '7100' WHERE `idcreditos_destinos` = '7100'";

$sql["20160508"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Productos' WHERE `idgeneral_menu` = '3073'";
$sql["20160508"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes Detallados' WHERE `idgeneral_menu` = '3071'";
$sql["20160508"][]	= "UPDATE `general_menu` SET `menu_title` = 'Gestion de Cobranza' WHERE `idgeneral_menu` = '4090' ";
$sql["20160508"][]	= "UPDATE `general_menu` SET `menu_order` = '0' , `menu_help_id` = '3070' WHERE `idgeneral_menu` = '3071'";
$sql["20160508"][]	= "UPDATE `general_menu` SET `menu_order` = '1' WHERE `idgeneral_menu` = '4090'";


$sql["20160601"][]	= "UPDATE `general_menu` SET `menu_file` = 'rptcreditos/catalogo-productos.rpt.php' WHERE `idgeneral_menu` = '3073'";
$sql["20160601"][]	= "UPDATE `general_menu` SET `menu_title` = 'Creditos Duplicados' WHERE `idgeneral_menu` = '3076'";
$sql["20160601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('3077', '3070', 'Creds. Nomina X2', 'rptcreditos/nomina-x2.rpt.php', '_blank', 'Reporte de Creditos de Nomina Duplicados', 'nomina', 'command', '3077', '3077')";
$sql["20160601"][]	= "UPDATE `general_menu` SET `menu_file` = 'rptcreditos/creditos-duplicados.rpt.php' , `menu_image` = 'doble' WHERE `idgeneral_menu` = '3076'";
$sql["20160601"][]	= "UPDATE `general_menu` SET `menu_destination` = '_blank' WHERE `idgeneral_menu` = '3073' ";

$sql["20160601"][]	= "ALTER TABLE `empresas_cobranza` ADD COLUMN `recibo` BIGINT(20) NULL DEFAULT 0 COMMENT '' AFTER `estado`";
$sql["20160601"][]	= "ALTER TABLE `empresas_cobranza` ADD COLUMN `tiempocobro` INT(11) NULL DEFAULT 0 COMMENT '' AFTER `recibo`";
$sql["20160601"][]	= "ALTER TABLE `empresas_cobranza` ADD INDEX `operativo1` (`clave_de_credito` ASC, `parcialidad` ASC, `recibo` ASC)  COMMENT ''";
$sql["20160601"][]	= "UPDATE `empresas_cobranza` SET `recibo`= `setNoMenorCero`(getElementInStr(getElementInStr(`observaciones`, \"[\",2), \"]\",1)) ,`tiempocobro` =UNIX_TIMESTAMP(getElementInStr(getElementInStr(`observaciones`, \"[\",3), \"]\",1))";
$sql["20160601"][]	= "UPDATE `empresas_cobranza` SET  `estado`= 0 WHERE `recibo`>0";
$sql["20160601"][]	= "UPDATE `empresas_cobranza`, `operaciones_recibos` SET `recibo`= `operaciones_recibos`.`idoperaciones_recibos`, `tiempocobro`=UNIX_TIMESTAMP(`operaciones_recibos`.`fecha_operacion`), `estado`=0 WHERE `empresas_cobranza`.`recibo`=0 AND `operaciones_recibos`.`docto_afectado`=`empresas_cobranza`.`clave_de_credito` AND  `operaciones_recibos`.`periodo_de_documento`=`empresas_cobranza`.`parcialidad` AND `operaciones_recibos`.`tipo_docto`= 2";

$sql["20160602"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('199', 'Persona eliminada', 'common')";

$sql["20160603"][]	= "ALTER TABLE `creditos_garantias` ADD COLUMN `caracteristica1` VARCHAR(50) NULL COMMENT 'color' AFTER `eacp`,ADD COLUMN `caracteristica2` VARCHAR(40) NULL COMMENT 'annio' AFTER `caracteristica1`,ADD COLUMN `caracteristica3` VARCHAR(40) NULL COMMENT 'modelo' AFTER `caracteristica2` ";
$sql["20160603"][]	= "ALTER TABLE `creditos_garantias` CHANGE COLUMN `idcreditos_garantias` `idcreditos_garantias` INT(8) UNSIGNED NOT NULL COMMENT '' ";
$sql["20160603"][]	= "ALTER TABLE `creditos_garantias` CHANGE COLUMN `caracteristica1` `caracteristica1` VARCHAR(40) NULL DEFAULT NULL COMMENT 'color'";
$sql["20160603"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('562', 'FORM', 'CREDITOS.PLAN_CON_CEROS', '', '', '\$valor=false;', '')";
$sql["20160603"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){\$Credito= new cCredito(\$docto, \$socio); \$Credito->init(); }; \$Credito->setReestructurarIntereses(false, false, true); if ( \$Credito->getNumeroDePlanDePagos()>0){ \$xPP = new cParcialidadDeCredito(\$Credito->getClaveDePersona(), \$Credito->getClaveDeCredito(), \$parcialidad); \$xPP->setClaveDePlan(\$Credito->getNumeroDePlanDePagos()); \$xPP->setActualizarCapital(\$monto);}' WHERE `idoperaciones_tipos` = '120'; ";
$sql["20160603"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){\$Credito= new cCredito(\$docto, \$socio); \$Credito->init(); }; \$Credito->setReestructurarIntereses(false, false, true); if ( \$Credito->getNumeroDePlanDePagos()>0){ \$xPP = new cParcialidadDeCredito(\$Credito->getClaveDePersona(), \$Credito->getClaveDeCredito(), \$parcialidad); \$xPP->setClaveDePlan(\$Credito->getNumeroDePlanDePagos()); \$xPP->setActualizarInteres(\$monto);}' WHERE `idoperaciones_tipos` = '140'; ";
$sql["20160603"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){\$Credito= new cCredito(\$docto, \$socio); \$Credito->init(); }; if (\$Credito->getNumeroDePlanDePagos()>0){ \$xPP = new cParcialidadDeCredito(\$Credito->getClaveDePersona(), \$Credito->getClaveDeCredito(), \$parcialidad); \$xPP->setClaveDePlan(\$Credito->getNumeroDePlanDePagos()); \$xPP->setActualizarIVA(\$monto); }' WHERE `idoperaciones_tipos` = '151'; ";
$sql["20160603"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('563', 'REPORT', 'RECIBOS.REPORTE.USAR.FECHA_REAL', '', '', '\$valor = true;', '')";
$sql["20160603"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) VALUES ('117', 'DISPOSICION DE CREDITO', '2', '1', '\$cuenta = \$cartera[\"capital_vigente_normal\"];', 'Operacion de Aumento de Credito', '999', '117', '1', '1', '99', '0', '0', '0', '0', '1', '0', '0', '', 'ninguna', '0', '0', '', 'if ( !isset(\$Credito) ) { \$Credito = new cCredito(\$docto, \$socio);\r\n \$Credito->initCredito(); }', '0', '0', '0.000', '', '1') ";
$sql["20160603"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) VALUES ('998', 'SALDOS POR CAMBIO DE TASA', '0', '0', '\$cuenta = \"NO_CONTABILIZAR\";', 'Saldo al Cambio de tasa', '999', '998', '0', '0', '99', '0', '0', '0', '0', '0', '0', '0', '', 'ninguna', '0', '1', '', '', '0', '0', '0.000', '', '1')";
$sql["20160603"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('582', '1000', '117', 'DISPOSICION DE CREDITO')";
$sql["20160603"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('583', '1010', '117', 'DISPOSICION DE CREDITO') ";
$sql["20160603"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('584', '2005', '117', 'DISPOSICION DE CREDITO') ";
$sql["20160603"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('585', '2620', '117', 'DISPOSICION DE CREDITO') ";
$sql["20160603"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`) VALUES ('586', '2620', '998', '0', 'CAMBIO DE TASA DE INTERES')";
$sql["20160603"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('587', '30100', '117', 'DISPOSICION DE CREDITO')";

$sql["20160604"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('1083', '1080', 'Disposicion de Credito', 'frmcaja/creditos-disposicion.frm.php', 'Disposiciones de Credito', 'dinero', 'command', '2', '1083')";
$sql["20160604"][]	= "UPDATE `general_menu` SET `menu_order` = '3' WHERE `idgeneral_menu` = '1082'";
$sql["20160604"][]	= "UPDATE `general_menu` SET `menu_order` = '1' WHERE `idgeneral_menu` = '1081'";

$sql["20160605"][]	= "INSERT INTO `operaciones_recibostipo` (`idoperaciones_recibostipo`, `descripcion_recibostipo`, `detalles_del_concepto`, `subclasificacion`, `nombre_sublasificacion`, `mostrar_en_corte`, `tipo_poliza_generada`, `afectacion_en_flujo_efvo`, `path_formato`, `origen`) VALUES ('102', 'CREDITOS.- DISPOSICIONES', 'Recibo de Disposicion de Credito', '0', '', '0', '2', 'ninguna', '../rpt_formatos/recibo_de_prestamo.rpt.php?formato=201&recibo=', 'colocacion')";
$sql["20160605"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `estatus`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('201', '20', 'alta', 'Recibo de Disposicion', 'variable_encabezado_de_reporte\r\n\r\n<h1>RECIBO DE DISPOSICION</h1>\r\n\r\n<h2 style=\"text-align:right\">BUENO POR <strong>variable_monto_del_recibo</strong></h2>\r\n\r\n<p>RECIBI DE LA SOCIEDAD MERCANTIL <strong><em>variable_nombre_de_la_sociedad</em></strong>, LA CANTIDAD DE <strong>variable_monto_del_recibo</strong>&nbsp; SON : ( <strong>variable_monto_del_recibo_en_letras</strong> ) POR CONCEPTO DE : NUEVA DISPOSICI&Oacute;N.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4 style=\"text-align:right\">variable_lugar a variable_docto_fecha_larga_actual</h4>\r\n\r\n<h2>RECIB&Iacute;</h2>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>_____________________</h4>\r\n\r\n<h4><strong>variable_nombre_del_socio</strong></h4>\r\n\r\n<p>variable_pie_de_reporte</p>\r\n')";
$sql["20160605"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '1010' WHERE `ideacp_config_bases_de_integracion_miembros` = '582'";
$sql["20160605"][]	= "DELETE FROM `general_structure` WHERE `tabla`='socios_memo'";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','numero_solicitud','1','int','20','','Numero de Credito','text','NA','derecha','2','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','fecha_memo','0000-00-00','date','0','','Fecha de Memo','text','NA','derecha','1','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','texto_memo','','varchar','100','','Texto de Memo','textarea','NA','derecha','5','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','tipo_memo','99','int','0','','Tipo memo','select','SELECT * FROM `socios_memotipos`','derecha','0','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','idsocios_memo','','int','0','','Clave','hidden','NA','derecha','0','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','numero_socio','1','int','20','','Clave de Persona','hidden','NA','derecha','0','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','numero_gposolidario','1','int','20','','Clave de Grupo','hidden','NA','derecha','0','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','idusuario','99','int','5','','usurio','hidden','NA','derecha','0','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','sucursal','MATRIZ','varchar','20','','Sucursal','hidden','NA','derecha','0','','','0','normalfield','')";
$sql["20160605"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('socios_memo','eacp','','varchar','20','','Eacp','hidden','NA','derecha','0','','','0','normalfield','')";

$sql["20160605"][]	= "ALTER TABLE `creditos_montos` ADD COLUMN `dispocision` DOUBLE(16,2) NULL DEFAULT 0 COMMENT '' AFTER `otros_si`";
$sql["20160605"][]	= "ALTER TABLE `creditos_montos` ADD COLUMN `saldo_plan` DOUBLE(16,2) NULL DEFAULT 0 COMMENT 'Monto pendiente en Plan de Pago' AFTER `dispocision`, ADD COLUMN `abonos_ops` DOUBLE(16,2) NULL DEFAULT 0 COMMENT 'Abonos en operaciones' AFTER `saldo_plan`";

$sql["20160606"][]	= "ALTER TABLE `creditos_montos` ADD COLUMN `periodo_min` INT(5) NULL DEFAULT 0 COMMENT 'Letra Inicial pendiente de pago' AFTER `abonos_ops`,ADD COLUMN `periodo_max` INT(5) NULL DEFAULT 0 COMMENT 'Ultimo periodo pendiente' AFTER `periodo_min`,ADD COLUMN `periodo_last` INT(5) NULL DEFAULT 0 COMMENT 'Ultimo periodo Pagado' AFTER `periodo_max`";
$sql["20160607"][]	= "ALTER TABLE `creditos_montos` ADD COLUMN `bon_int` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '' AFTER `periodo_last`,ADD COLUMN `bon_mora` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '' AFTER `bon_int`,ADD COLUMN `bon_otros` DOUBLE(12,2) NULL DEFAULT 0 COMMENT '' AFTER `bon_mora`";

$sql["20160607"][]	= "UPDATE `operaciones_tipos` SET `integra_parcialidad` = '1' WHERE `idoperaciones_tipos` = '802'";
$sql["20160607"][]	= "UPDATE `operaciones_tipos` SET `integra_parcialidad` = '1' WHERE `idoperaciones_tipos` = '801'";
$sql["20160607"][]	= "UPDATE `operaciones_tipos` SET `integra_parcialidad` = '1' WHERE `idoperaciones_tipos` = '803'";

$sql["20160610"][]	= "ALTER TABLE `creditos_montos` CHANGE COLUMN `saldo_plan` `saldo_plan` DOUBLE(16,2) NULL DEFAULT '0.00' COMMENT 'Monto pendiente de capital Plan de Pago', ADD COLUMN `sdo_exig_fut` DOUBLE(16,2) NULL DEFAULT 0 COMMENT 'Saldo exigible futuro' AFTER `bon_otros`, ADD COLUMN `sdo_exig_act` DOUBLE(16,2) NULL DEFAULT 0 COMMENT 'Saldo Exigible Actual' AFTER `sdo_exig_fut`";

$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '179'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '173'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '183'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '184'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '186'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '187'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '188'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '189'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '190'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '191'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '192'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '194'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '195'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '196'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '198'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '199'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '203'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '204'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '205'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '206'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '207'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '208'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '209'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '210'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '211'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '212'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '213'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '214'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '215'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '216'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '217'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '218'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '223'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '224'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '225'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '227'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '228'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '229'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '234'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '235'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '236'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '237'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '318'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '328'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '329'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '330'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '331'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '332'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '333'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '336'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '337'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '338'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '359'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '360'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '361'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '362'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '363'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '364'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '365'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '366'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '367'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '368'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '369'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '370'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '371'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '372'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '373'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '374'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '375'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '376'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '378'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '379'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '380'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '381'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '437'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '472'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '483'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '491'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '496'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '2060'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99328'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '202'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '219'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '173'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '183'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '184'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '186'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '187'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '188'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '189'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '190'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '191'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '192'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '194'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '195'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '196'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '198'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '199'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '203'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '204'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '205'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '206'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '207'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '208'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '209'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '210'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '211'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '212'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '213'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '214'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '215'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '216'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '217'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '218'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '223'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '224'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '225'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '227'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '228'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '229'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '234'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '235'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '236'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '237'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '318'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '328'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '329'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '330'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '331'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '332'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '333'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '336'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '337'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '338'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '359'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '360'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '361'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '362'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '363'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '364'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '365'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '366'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '367'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '368'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '369'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '370'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '371'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '372'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '373'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '374'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '375'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '376'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '378'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '379'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '380'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '381'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '437'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '472'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '483'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '491'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '496'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '1060'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '72000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '18000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '71000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '5010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '5020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '4010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' , `menu_description` = 'Reportes Legales-Prudenciales' WHERE `idgeneral_menu` = '12000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_file` = '' WHERE `idgeneral_menu` = '1010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Catalogos del Sistema Relacionados con Personas' WHERE `idgeneral_menu` = '2050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-sliders' WHERE `idgeneral_menu` = '11010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Registro de Empresas' , `menu_image` = 'fa-building' WHERE `idgeneral_menu` = '2060'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-database' WHERE `idgeneral_menu` = '11010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Cierre de dia, operaciones, contable, tesoreria, etc.' , `menu_image` = 'fa-hourglass-end' WHERE `idgeneral_menu` = '11020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-lock' WHERE `idgeneral_menu` = '10000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Operaciones Especiales no clasificadas' , `menu_image` = 'fa-settings' WHERE `idgeneral_menu` = '11030'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Reportes relacionados a Operaciones y Recibos' , `menu_image` = 'fa-print' WHERE `idgeneral_menu` = '18200'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Catalogos Relacionados con tesoreria' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '1050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Permisos y Usuarios del Sistema' , `menu_image` = 'fa-lock' WHERE `idgeneral_menu` = '10010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Operaciones con Empresas' , `menu_image` = 'fa-building' WHERE `idgeneral_menu` = '1060'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Reportes Relacionados a la seguridad del sistema' , `menu_image` = 'fa-search' WHERE `idgeneral_menu` = '10020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-calendar-o' WHERE `idgeneral_menu` = '1030'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Reportes de Catalogos del sistema' , `menu_image` = 'fa-file-o' WHERE `idgeneral_menu` = '11300'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_image` = 'fa-bank' WHERE `idgeneral_menu` = '9000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Cobranza de otros productos' , `menu_description` = 'Cobranza de otros productos y servicios' , `menu_image` = 'fa-money' WHERE `idgeneral_menu` = '1070'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Catalogos Relacionados con AML - PLD - FT' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '72000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo de Prevencion de Lavado de Dinero' , `menu_image` = 'fa-eye' WHERE `idgeneral_menu` = '7000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Reportes relacionados con PLD-AML-FT' , `menu_image` = 'fa-file-o' WHERE `idgeneral_menu` = '71000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Menu de reportes' , `menu_image` = 'fa-area-chart' WHERE `idgeneral_menu` = '18000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Reportes Individuales de Personas' , `menu_image` = 'fa-user' WHERE `idgeneral_menu` = '18500'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-credit-card' WHERE `idgeneral_menu` = '8000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Operaciones con Creditos' , `menu_description` = 'Operaciones con Creditos' , `menu_image` = 'fa-money' WHERE `idgeneral_menu` = '1080'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Cobranza de Otros' WHERE `idgeneral_menu` = '1070'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Reportes relacionados a Bancos' , `menu_image` = '' WHERE `idgeneral_menu` = '9050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Reportes Depreciados' WHERE `idgeneral_menu` = '11100'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-area-chart' WHERE `idgeneral_menu` = '9050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-coffee' WHERE `idgeneral_menu` = '11100'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos de Captacion' , `menu_destination` = '' , `menu_description` = 'Catalogos de datos relacioandos con captacion' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '8030'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Operaciones con Garantias Reales' , `menu_image` = 'fa-car' WHERE `idgeneral_menu` = '7010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Acceso a configuracion del Sistema' , `menu_image` = 'fa-sliders' WHERE `idgeneral_menu` = '7020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Reportes Relacionados con Empresas' , `menu_image` = 'fa-area-chart' WHERE `idgeneral_menu` = '18800'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Reportes Relacionods con Captacion' , `menu_image` = 'fa-area-chart' WHERE `idgeneral_menu` = '8050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo de Migracion al Sistema' , `menu_image` = 'fa-server' WHERE `idgeneral_menu` = '18550'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Reportes relacionados a la Tesoreria' , `menu_image` = 'fa-area-chart' WHERE `idgeneral_menu` = '18300'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-balance-scale ' WHERE `idgeneral_menu` = '5000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de reportes' WHERE `idgeneral_menu` = '18000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de Soporte y Ayuda' , `menu_image` = 'fa-info' WHERE `idgeneral_menu` = '15000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Gestion de Cobranza Activa' , `menu_image` = 'fa-object-group' WHERE `idgeneral_menu` = '4000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de Credito' , `menu_image` = 'fa-credit-card' WHERE `idgeneral_menu` = '3000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de personas' WHERE `idgeneral_menu` = '2000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de captacion, Ahorro e Inversion' WHERE `idgeneral_menu` = '8000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de Herramientas del Sistema' , `menu_image` = 'fa-cogs' WHERE `idgeneral_menu` = '11000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de Tesoreria' , `menu_image` = 'fa-money' WHERE `idgeneral_menu` = '1000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-users' WHERE `idgeneral_menu` = '2000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-asterisk' WHERE `idgeneral_menu` = '1040'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Masivo de Parcialidades' , `menu_description` = 'Amortizaciones Masivas de Parcialidades' , `menu_image` = 'fa-money' WHERE `idgeneral_menu` = '1003'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1002'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1003'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Cobros de Conceptos relacionados a Cuotas' , `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1008'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Operaciones Relacionadas a la Tesoreria' , `menu_image` = 'fa-lock' WHERE `idgeneral_menu` = '1010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Depositos a la Vista o Cuenta Corriente' WHERE `idgeneral_menu` = '1020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Permitir el acceso a las operaciones de Caja' , `menu_image` = 'fa-lock' WHERE `idgeneral_menu` = '1011'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Arqueo y Cierre de las operaciones de caja' , `menu_image` = 'fa-lock' WHERE `idgeneral_menu` = '1012'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-unlock' WHERE `idgeneral_menu` = '1011'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Operaciones con Sesiones' , `menu_description` = 'Operaciones con Sesiones de Caja' , `menu_image` = 'fa-archive' WHERE `idgeneral_menu` = '1013'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_description` = 'Carga batch de Depositos y Retiros' , `menu_image` = 'fa-magic' WHERE `idgeneral_menu` = '1024'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1021'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1022'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Compra y venta de Acciones' , `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1032'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1038'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1039'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1041'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Valores' , `menu_destination` = 'principal' , `menu_description` = 'Catalogo de Valores y Equivalencia' , `menu_image` = 'fa-plug' WHERE `idgeneral_menu` = '1051'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_description` = 'Catalogo de Monedas y Valor' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '1053'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Recibos' , `menu_description` = 'Catalogo de Recibos de Operacion' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '11203'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Registro de Pagos provenientes de Empresas' , `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '2061'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description`='' WHERE ISNULL(`menu_description`)";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99072'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99075'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '542'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99247'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99245'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99244'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '585'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '540'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99248'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar`='true'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination`='', `menu_description`='', `menu_order`=0, `menu_help_id`=0, `menu_showin_toolbar`='false', `menu_title`='' WHERE `menu_file` LIKE '%404.php' OR `menu_file` LIKE '%inicio.php' OR `menu_file` LIKE '%index.xul.php' OR `menu_file` LIKE '%index.php'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description`='' WHERE `menu_description`='NO_DESCRIPTION'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '11035'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '1091'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = '' WHERE `idgeneral_menu` = '134'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = '' WHERE `idgeneral_menu` = '133'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '99317'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '99318'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '99319'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '99320'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '99321'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '99322'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '99323'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Movimientos Bancarios' WHERE `idgeneral_menu` = '9004'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '18510'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-usd' WHERE `idgeneral_menu` = '1090'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Respaldos de la Base de Datos' , `menu_image` = 'fa-database' WHERE `idgeneral_menu` = '11400'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos del Sistema' WHERE `idgeneral_menu` = '11300'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes Por Persona' WHERE `idgeneral_menu` = '18500'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-area-chart' WHERE `idgeneral_menu` = '7050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '5010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '11400'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '20100'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '6020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Catalogo Contable' , `menu_image` = 'fa-balance-scale' WHERE `idgeneral_menu` = '5010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo de Polizas Contables' WHERE `idgeneral_menu` = '5020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo del catalogo Contable' WHERE `idgeneral_menu` = '5010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '5020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo para Administracion de Avisos y Alerta' , `menu_image` = 'fa-info' WHERE `idgeneral_menu` = '40100'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '6050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo para Administracion de Notificaciones Extrajudiciales' , `menu_image` = 'fa-briefcase' WHERE `idgeneral_menu` = '4010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo para Administracion de Impuestos' , `menu_image` = 'fa-bank' WHERE `idgeneral_menu` = '5040'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo para Administracion de Creditos' WHERE `idgeneral_menu` = '3000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo para Reportes Auxiliares' , `menu_image` = 'fa-balance-scale' WHERE `idgeneral_menu` = '5050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-list' WHERE `idgeneral_menu` = '5010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '5050'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo para Generar Presupuestos de Credito' , `menu_image` = 'fa-list-ol' WHERE `idgeneral_menu` = '3005'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo para efectuar operaciones Masivas de Credito' , `menu_image` = 'fa-list-ul' WHERE `idgeneral_menu` = '4030'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos de Sistema' , `menu_description` = 'Modulo para Administracion de Catalogos del Sistema' , `menu_image` = 'fa-cog' WHERE `idgeneral_menu` = '11200'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulos de Administracion de Sesiones del Comite de Credito' , `menu_image` = 'fa-check-circle-o' WHERE `idgeneral_menu` = '3010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulos de Reportes de Estados Financieros' , `menu_image` = 'fa-balance-scale' WHERE `idgeneral_menu` = '5060'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo para Administracion de Lineas de Credito' , `menu_image` = 'fa-plug' WHERE `idgeneral_menu` = '3020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo de Reportes' , `menu_image` = 'fa-files-o' WHERE `idgeneral_menu` = '5070'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo de Reportes Legales-Prudenciales' , `menu_image` = 'fa-gavel' WHERE `idgeneral_menu` = '12000'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Operaciones con Grupos' , `menu_description` = 'Modulo para Operaciones con Grupos Solidarios' , `menu_image` = 'fa-users' WHERE `idgeneral_menu` = '2010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos de Colocacion' , `menu_description` = 'Modulos de Administracion de catalogos de Colocacion' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '3030'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos de Contabilidad' , `menu_description` = 'Modulos de Administracion de catalogos Contables' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '5080'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de Reportes de Creditos' , `menu_image` = 'fa-files-o' WHERE `idgeneral_menu` = '3070'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulos de Administracion de Cuentas a la Vista' WHERE `idgeneral_menu` = '1020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' WHERE `idgeneral_menu` = '1010'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reportes de Grupos' , `menu_destination` = '' , `menu_description` = 'Modulo de Reportes de Grupos' , `menu_image` = 'fa-files-o' WHERE `idgeneral_menu` = '2030'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulos de Reportes de Personas' , `menu_image` = 'fa-users' WHERE `idgeneral_menu` = '2020'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo para Ejecucion de Procesos Contables' , `menu_image` = 'fa-cogs' WHERE `idgeneral_menu` = '5090'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_destination` = '' , `menu_description` = 'Modulo de Reportes por Oficiales de Credito' , `menu_image` = 'fa-files-o' WHERE `idgeneral_menu` = '18400'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar` = 'false' WHERE `idgeneral_menu` = '99318'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar` = 'false' WHERE `idgeneral_menu` = '99317'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar` = 'false' WHERE `idgeneral_menu` = '99319'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar` = 'false' WHERE `idgeneral_menu` = '99322'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar` = 'false' WHERE `idgeneral_menu` = '99321'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar` = 'false' WHERE `idgeneral_menu` = '99320'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300544'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185600'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de Administracion de Productos de Credito' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '3073'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300545'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185601'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300546'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185602'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300547'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185603'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300548'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185604'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos de Tipos de Persona' , `menu_description` = 'Administracion del Tipo de Ingreso de Personas' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2053'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300549'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185605'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Regiones de Personas' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2054'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300550'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185606'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Colonias en Domicilio' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2055'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Bases del Sistema' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '11015'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300551'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185607'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Tipos de regimenes Fiscales' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2056'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300552'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185608'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Tipos de Riesgo AML' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '72201'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300553'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185609'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300554'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185610'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300555'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185611'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300556'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185612'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300557'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185613'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300558'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185614'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300559'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185615'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300560'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185616'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300561'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185617'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300562'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185618'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300563'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185619'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300564'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185620'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300565'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185621'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300566'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185622'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300567'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185623'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300568'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185624'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300569'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185625'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99354'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300570'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185626'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99355'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300571'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185627'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99356'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300572'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185628'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99357'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300573'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99358'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300574'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300575'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99360'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300576'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99359'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99361'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300577'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99362'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300578'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99363'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300579'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99364'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300580'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99365'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300581'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99366'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300582'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99367'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99369'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title`='' WHERE `menu_title` LIKE '%.php'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_showin_toolbar`='false' WHERE `menu_description`=''";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Tareas del Sistema' WHERE `idgeneral_menu` = '99079'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'dev.- Visor de Reportes' WHERE `idgeneral_menu` = '99074'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99074'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '188514'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Calendario de Tareas Iniciales' WHERE `idgeneral_menu` = '99010'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '544'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Busqueda de Grupos' WHERE `idgeneral_menu` = '546'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Busqueda de garantias' WHERE `idgeneral_menu` = '99097'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Busqueda de Creditos' WHERE `idgeneral_menu` = '435'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '434'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Busqueda de Letras / Parcialidades' WHERE `idgeneral_menu` = '170'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Editor de recibos de operacion' WHERE `idgeneral_menu` = '541'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Busqueda de Cuentas de captacion' WHERE `idgeneral_menu` = '169'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '603'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '541'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99071'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '433'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99144'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '99076'";
$sql["20160610"][]	= "DELETE FROM `general_menu` WHERE `idgeneral_menu` = '427'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Busqueda de Compromisos' WHERE `idgeneral_menu` = '422'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_title` = 'Personas.- perfil transaccional' WHERE `idgeneral_menu` = '185622'";
$sql["20160610"][]	= "UPDATE `general_menu` SET `menu_description`= (SELECT `descripcion_reports` FROM `general_reports` WHERE `idgeneral_reports` LIKE CONCAT('%',`general_menu`.`menu_file`) LIMIT 0,1) WHERE `menu_description`=''";


$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1012','PAGO DE SALARIOS','0','0','\$cuenta = \"190245\";','','98','1012','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','SALARIOS','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1013','PAGO DE RENTA','0','0','\$cuenta = \"190245\";','','98','1013','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','RENTA','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1014','PAGO ENERGIA ELECTRICA','0','0','\$cuenta = \"190245\";','','98','1014','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','ENERGIA','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1015','PAGO DE AGUA POTABLE','0','0','\$cuenta = \"190245\";','','98','1015','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','AGUA','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1016','PAGO DE INTERNET','0','0','\$cuenta = \"190245\";','','98','1016','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','INTERNET','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1017','PAGO IMPUESTOS MUNICIPALES','0','0','\$cuenta = \"190245\";','','98','1017','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','IMPTOS MUN','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1018','PAGO COMISIONES EXTERNAS','0','0','\$cuenta = \"190245\";','','98','1018','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','COM EXTERNAS','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1019','GASTOS POR JUNTA DIRECTIVA','0','0','\$cuenta = \"190245\";','','98','1019','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','GTOS DIRECCION','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1020','GASTOS POR JUNTA VIGILANCIA','0','0','\$cuenta = \"190245\";','','98','1020','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','GTOS VIGILANCIA','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1021','GASTOS DE ASAMBLEA','0','0','\$cuenta = \"190245\";','','98','1021','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','GTOS ASAMBLEA','1')";
$sql["20160611"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`) values('1099','GASTOS VARIOS','0','0','\$cuenta = \"190245\";','','98','1099','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.000','GASTOS VARIOS','1')";

$sql["20160611"][]	= "UPDATE `operaciones_mvtos` SET `afectacion_cobranza`=0, `afectacion_contable`=0,`periodo_contable`=0, `periodo_cobranza`=0,`periodo_seguimiento`=0,`periodo_mensual`=0,`periodo_semanal`=0, `periodo_anual`=0";
$sql["20160611"][]	= "ALTER TABLE `operaciones_mvtos` CHANGE COLUMN `afectacion_cobranza` `afectacion_cobranza` TINYINT NULL DEFAULT '0.00' COMMENT '' ,CHANGE COLUMN `afectacion_contable` `afectacion_contable` TINYINT NULL DEFAULT '0.00' COMMENT '' ,CHANGE COLUMN `valor_afectacion` `valor_afectacion` FLOAT(4,2) NOT NULL DEFAULT '0.00' COMMENT '' ,CHANGE COLUMN `codigo_eacp` `codigo_eacp` VARCHAR(15) NULL DEFAULT 'EN_TRAMITE' COMMENT '' ,CHANGE COLUMN `periodo_contable` `periodo_contable` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `periodo_cobranza` `periodo_cobranza` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `periodo_seguimiento` `periodo_seguimiento` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `periodo_mensual` `periodo_mensual` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `periodo_semanal` `periodo_semanal` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `periodo_anual` `periodo_anual` TINYINT UNSIGNED NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `docto_neutralizador` `docto_neutralizador` BIGINT(20) UNSIGNED NULL DEFAULT '1' COMMENT '' ,CHANGE COLUMN `tasa_asociada` `tasa_asociada` FLOAT(7,4) UNSIGNED NULL DEFAULT '0.0000' COMMENT '' ,CHANGE COLUMN `dias_asociados` `dias_asociados` INT(4) UNSIGNED NULL DEFAULT '0' COMMENT '' ,CHANGE COLUMN `grupo_asociado` `grupo_asociado` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '' ,DROP PRIMARY KEY, ADD PRIMARY KEY (`idoperaciones_mvtos`, `recibo_afectado`, `socio_afectado`, `docto_afectado`, `tipo_operacion`, `grupo_asociado`, `periodo_socio`)  COMMENT ''";
$sql["20160611"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){ \$Credito = new cCredito(\$docto, \$socio);\$Credito->initCredito(); }\r\n' WHERE `idoperaciones_tipos` = '141'";
$sql["20160611"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){\$Credito= new cCredito(\$docto, \$socio); \$Credito->init(); }; if ( \$Credito->getNumeroDePlanDePagos()>0){ \$xPP = new cParcialidadDeCredito(\$Credito->getClaveDePersona(), \$Credito->getClaveDeCredito(), \$parcialidad); \$xPP->setClaveDePlan(\$Credito->getNumeroDePlanDePagos()); \$xPP->setActualizarCapital(\$monto);}' WHERE `idoperaciones_tipos` = '120'";
$sql["20160611"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){\$Credito= new cCredito(\$docto, \$socio); \$Credito->init(); }; if ( \$Credito->getNumeroDePlanDePagos()>0){ \$xPP = new cParcialidadDeCredito(\$Credito->getClaveDePersona(), \$Credito->getClaveDeCredito(), \$parcialidad); \$xPP->setClaveDePlan(\$Credito->getNumeroDePlanDePagos()); \$xPP->setActualizarInteres(\$monto);}' WHERE `idoperaciones_tipos` = '140'";

$sql["20160612"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('911', 'Cambio de Password', 'security')";
$sql["20160612"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('564', 'FORM', 'CREDITOS.PLAN_SIN_AJUSTE_FINAL', '', '', '\$valor=false;', '')";


$sql["20160801"][]	= "UPDATE `general_niveles` SET task_events =''";

$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Registro de usarios' , `menu_image` = 'fa-users' , `menu_order` = '100' , `menu_help_id` = '10001' WHERE `idgeneral_menu` = '10001'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_description` = 'Asignar Permisos en el Sistema' , `menu_image` = 'fa-check-square' , `menu_order` = '200' , `menu_help_id` = '10003' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10003'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_description` = 'Respaldar o Restaurar Permisos' , `menu_image` = 'fa-download' , `menu_order` = '300' , `menu_help_id` = '10016' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10016'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_type` = 'command' WHERE `idgeneral_menu` = '10003'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Respaldo/Restaurar Permisos' WHERE `idgeneral_menu` = '10016'";
$sql["20160801"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('10004', '10010', 'Editar Usuarios', 'frmsecurity/usuarios-edicion.frm.php', 'Edicion de usuarios existentes', 'fa-user-times', 'command', '101', '10004', 'true')";

$sql["20160801"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '9'";
$sql["20160801"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '14'";
$sql["20160801"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '5'";
$sql["20160801"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '99'";

$sql["20160801"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('400', 'Usted no tiene permisos para Acceder a el Modulo', 'security')";
$sql["20160801"][]	= "UPDATE `general_error_codigos` SET `description_error` = 'Usted no tiene permisos del Sistema para este Modulo' WHERE `idgeneral_error_codigos` = '999'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Analizar Bitacoras del Sistema' , `menu_destination` = 'principal' , `menu_description` = 'Leer y ver Bitacoras de SAFE-OSMS' , `menu_image` = 'fa-eye' , `menu_type` = 'command' , `menu_order` = '200' , `menu_help_id` = '10024' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10024'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_order` = '300' , `menu_help_id` = '10023' WHERE `idgeneral_menu` = '10023'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Bitacoras del Servidor' , `menu_description` = 'Leer Bitacoras del SO/Servidor' , `menu_image` = 'fa-bug' WHERE `idgeneral_menu` = '10023'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reporte de Usuarios' , `menu_description` = 'Reporte de Usuario del Sistema' , `menu_image` = 'fa-user-secret' , `menu_order` = '500' , `menu_help_id` = '10022' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10022'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Eventos del Sistema' , `menu_image` = 'fa-video-camera' , `menu_order` = '100' , `menu_help_id` = '10021' WHERE `idgeneral_menu` = '10021'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Bitacoras del Sistema' WHERE `idgeneral_menu` = '10024'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Asignar Causas de Mora' , `menu_description` = 'Asignar motivos de vencimiento de Creditos' , `menu_image` = 'fa-indent' , `menu_order` = '3' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '4031'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_description` = 'Asignar Oficiales de Credito' , `menu_image` = 'fa-users' , `menu_order` = '1' , `menu_help_id` = '4021' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '4021'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_description` = 'Edicion masiva de datos de Credito' , `menu_image` = 'fa-list' , `menu_order` = '4' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '4032'";
$sql["20160801"][]	= "UPDATE `general_menu` SET `menu_title` = 'Operaciones Masivas' , `menu_help_id` = '4030' WHERE `idgeneral_menu` = '4030'; ";
$sql["20160801"][]	= "CREATE TABLE IF NOT EXISTS `personas_ae_scian` (  `clave_interna` bigint(20) NOT NULL,  `clave_de_actividad` varchar(20) DEFAULT '',  `nombre_de_la_actividad` varchar(200) DEFAULT NULL,  `clasificacion` varchar(20) DEFAULT NULL,  `clave_de_superior` bigint(20) DEFAULT '0',  `clave_aml` varchar(20) DEFAULT NULL COMMENT 'clave relacionada en AML',  PRIMARY KEY (`clave_interna`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$sql["20160801"][]	= "ALTER TABLE `creditos_plan_de_pagos` ADD COLUMN `penas` FLOAT(12,2) NULL DEFAULT 0 COMMENT '' AFTER `ahorro`,ADD COLUMN `gtoscbza` FLOAT(12,2) NULL DEFAULT 0 COMMENT '' AFTER `penas`,ADD COLUMN `mora` FLOAT(12,2) NULL DEFAULT 0 COMMENT '' AFTER `gtoscbza`";

$sql["20160801"][]	= "DELETE FROM `general_structure`  WHERE `tabla`='operaciones_recibos'";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','idoperaciones_recibos','primary_key','bigint','20','','Numero de Recibo','text','NA','derecha','1','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','fecha_operacion','0000-00-00','date','0','','Fecha de Operacion','text','NA','derecha','2','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','tipo_docto','99','int','4','','Tipo de Recibo','select','SELECT idoperaciones_recibostipo, descripcion_recibostipo     FROM operaciones_recibostipo','derecha','3','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','tipo_pago','ninguno','varchar','35','','Tipo de pago','select','SELECT `tipo_de_pago`,`descripcion` FROM `tesoreria_tipos_de_pago`','derecha','4','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','docto_afectado','0','bigint','20','','Documento','text','NA','derecha','5','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','grupo_asociado','99','int','10','','Grupo Solidario','select','SELECT `idsocios_grupossolidarios`,`nombre_gruposolidario` FROM `socios_grupossolidarios`','derecha','7','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','persona_asociada','0','bigint','20','','Empresa','select','SELECT `idsocios_aeconomica_dependencias`,`descripcion_dependencia` FROM `socios_aeconomica_dependencias`','derecha','8',NULL,NULL,'','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','clave_de_moneda','MXN','varchar','10','Clave de Moneda del Recibo','Moneda','select','SELECT * FROM tesoreria_monedas','derecha','11','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','unidades_en_moneda','0','float','20','Unidades Originales','Unidades Originales','text','','derecha','12','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cuenta_bancaria','0','bigint','20','','Cuenta bancaria','text','SELECT * FROM `bancos_cuentas`','derecha','20',NULL,NULL,'','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cheque_afectador','N/A','varchar','20','','Numero de Cheque','text','NA','derecha','30','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','recibo_fiscal','N/A','varchar','15','','Recibo Impreso','text','NA','derecha','31','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','observacion_recibo','','varchar','200','','Observaciones','text','NA','derecha','51','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cadena_distributiva','N/A','varchar','100','','Descripcion','hidden','NA','derecha','55','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','archivo_fisico','','varchar','200','','Archivo fisico','hidden',NULL,'derecha','100',NULL,NULL,'','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','eacp','EN_TRAMITE','varchar','20','','Entidad','hidden','NA','derecha','106','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','fecha_de_registro','0000-00-00','date','0','','Fecha de registro','hidden',NULL,'derecha','107',NULL,NULL,'','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','idusuario','99','int','4','','usuario','hidden','NA','derecha','111','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','indice_origen','99','int','4','','Clave de Origen','hidden','NA','derecha','112','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','numero_socio','0','int','10','','Clave de Persona','hidden','NA','derecha','113','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','origen_aml','0','int','4','','Origen AML','hidden',NULL,'derecha','115',NULL,NULL,'','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','periodo_de_documento','0','int','4','','Periodo','text',NULL,'derecha','6',NULL,NULL,'','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','sucursal','MATRIZ','varchar','10','','Sucursal','hidden','NA','derecha','119','','','0','normalfield','')";
$sql["20160801"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','total_operacion','0.00','float','25','','Total','text','NA','derecha','122','','','0','normalfield','')";
$sql["20160801"][]	= "INSERT INTO `general_formulas` (`aplicado_a`, `estructura_de_la_formula`) VALUES ('php_mora_x_letra', '\$moratorio = ( \$BASE_MORA * \$DIAS_MORA * \$TASA_MORA ) / 360;'); ";

$sql["20160802"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptacumulados/concentracion_de_cartera_por_producto.php?', 'Creditos.- Concentracion por Producto', 'general_acumulados', '99113', '', '2')";
$sql["20160802"][]	= "UPDATE `general_reports` SET `idgeneral_reports` = '../rptacumulados/concentracion_de_cartera_por_aplicacion.php?' , `explicacion` = '' WHERE `idreport` = '112'";
$sql["20160802"][]	= "UPDATE `general_menu` SET `menu_file` = 'rptacumulados/concentracion_de_cartera_por_aplicacion.php' , `menu_description` = '' , `menu_image` = '' WHERE `idgeneral_menu` = '99114'";

$sql["20160802"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('20102', 'Nomina no Guardada', 'common')";
$sql["20160802"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('20103', 'Nomina Guardada', 'common')";
$sql["20160802"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('20104', 'Nomina Eliminada', 'common')";

$sql["20160802"][]	= "UPDATE `operaciones_mvtos` SET `socio_afectado` = (SELECT `numero_socio` FROM `creditos_solicitud` WHERE `numero_solicitud`= `operaciones_mvtos`.`docto_afectado` LIMIT 0,1 ) WHERE `socio_afectado`=`docto_afectado` AND `docto_afectado`>1";
$sql["20160802"][]	= "UPDATE `operaciones_tipos` SET `formula_de_cancelacion` = 'if ( !isset(\$Credito) ){\$Credito= new cCredito(\$docto, \$socio); \$Credito->init(); }; if ( \$Credito->getNumeroDePlanDePagos()>0){ \$xPP = new cParcialidadDeCredito(\$Credito->getClaveDePersona(), \$Credito->getClaveDeCredito(), \$parcialidad); \$xPP->setClaveDePlan(\$Credito->getNumeroDePlanDePagos()); \$xPP->setActualizarCapital(\$monto); if ( \$PAGOS_SIN_CAPITAL == true){ \$xPP->setActualInteresPropCred(\$socio, \$docto, \$parcialidad, \$monto, \$Credito->getSaldoActual(), true);} }' WHERE `idoperaciones_tipos` = '120'; ";

$sql["20160802"][]	= "DELETE FROM `general_structure` WHERE `tabla` = 't_03f996214fba4a1d05a68b18fece8e71'";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','idusuarios','','int','0',NULL,'Idusuarios','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','f_28fb96d57b21090705cfdf8bc3445d2a','','varchar','60',NULL,'Nombre','text','NA','derecha','2','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','f_34023acbff254d34664f94c3e08d836e','','varchar','40',NULL,'Password','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','nombres','','varchar','40',NULL,'Nombres','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','apellidopaterno','','varchar','40',NULL,'Apellidopaterno','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','apellidomaterno','','varchar','40',NULL,'Apellidomaterno','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','puesto','','varchar','40',NULL,'Puesto','text','NA','derecha','3','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','f_f2cd801e90b78ef4dc673a4659c1482d','1','int','4',NULL,'Nivel','select','SELECT * FROM `general_niveles` WHERE `idgeneral_niveles` !=99','derecha','4','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','periodo_responsable','0','int','4',NULL,'Periodo responsable','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','estatus','|baja|activo|suspension','enum','20',NULL,'Estatus','text','NA','derecha','5','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','sucursal','MATRIZ','varchar','20',NULL,'Sucursal','select','SELECT * FROM `general_sucursales`','derecha','6','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','usr_options','','text','100',NULL,'Opciones','text','NA','derecha','20','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','date_expire','','date','0',NULL,'Expira','text','NA','derecha','8','','','0','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','cuenta_contable_de_caja','CUENTA_DE_CUADRE','varchar','25',NULL,'Cuenta de Caja','text',NULL,'derecha','7',NULL,NULL,'','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','codigo_de_persona','1','bigint','20',NULL,'Clave de Persona','text',NULL,'derecha','1',NULL,NULL,'','normalfield','')";
$sql["20160802"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','alias','','varchar','20',NULL,'Alias','text',NULL,'derecha','200',NULL,NULL,'','normalfield','')";

$sql["20160802"][]	= "INSERT INTO  `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcaptacion/captacion-echale.rpt.php?', 'Captacion.- Echale.- Reporte de Captacion', 'general_captacion', '20048', '', '5')";
$sql["20160802"][]	= "UPDATE  `general_structure` SET `control` = 'hidden' WHERE `tabla` = '`t_03f996214fba4a1d05a68b18fece8e71`' AND   `campo` ='estatus' ";
$sql["20160802"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = '`t_03f996214fba4a1d05a68b18fece8e71`' AND   `campo` ='usr_options' ";
$sql["20160802"][]	= "ALTER TABLE `personas_morales_anx` ADD COLUMN `activo` INT(2) NULL DEFAULT 0 COMMENT '' AFTER `idregistro2`,ADD COLUMN `fecha_de_baja` DATE NULL COMMENT '' AFTER `activo`";

$sql["20160802"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('11', 'DESEMPLEADO', '1')";
$sql["20160802"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('12', 'MENOR DE EDAD', '1')";
$sql["20160802"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('13', 'INFORMAL', '1')";
$sql["20160802"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`) VALUES ('201', 'PF EN EL REG DE INTEGRACION FISCAL', '1')";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_del_regimen` = 'PF EN EL REGIMEN GENERAL DE LEY' WHERE `clave_de_regimen` = '200'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_del_regimen` = 'PM EN EL REGIMEN GENERAL DE LEY' WHERE `clave_de_regimen` = '500'";
$sql["20160802"][]	= "ALTER TABLE `personas_regimen_fiscal` CHANGE COLUMN `nombre_del_regimen` `nombre_del_regimen` VARCHAR(80) NULL DEFAULT NULL COMMENT '' ,ADD COLUMN `nombre_legal` VARCHAR(150) NULL COMMENT '' AFTER `tipo_de_persona`";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'NA' WHERE `clave_de_regimen` = '1'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'NA' WHERE `clave_de_regimen` = '11'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'NA' WHERE `clave_de_regimen` = '12'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'NA' WHERE `clave_de_regimen` = '13'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'REGIMEN DE SALARIOS' WHERE `clave_de_regimen` = '100'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'PERSONAS FISICAS EN EL REGIMEN GENERAL DE LEY' WHERE `clave_de_regimen` = '200'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'PERSONAS MORALES EN EL REGIMEN GENERAL DE LEY' WHERE `clave_de_regimen` = '500'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'NA' WHERE `clave_de_regimen` = '999'";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_legal` = 'PERSONAS FISICAS EN EL REGIMEN DE INTEGRACION FISCAL' WHERE `clave_de_regimen` = '201'";
$sql["20160802"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`, `nombre_legal`) VALUES ('202', 'PF EL REGIMEN DE HONORARIOS', '1', 'PERSONAS FISICAS EN EL REGIMEN DE HONORARIOS')";
$sql["20160802"][]	= "INSERT INTO `personas_regimen_fiscal` (`clave_de_regimen`, `nombre_del_regimen`, `tipo_de_persona`, `nombre_legal`) VALUES ('203', 'PF EN EL REGIMEN DE ARRENDAMIENTO', '1', 'PERSONAS FISICAS EN EL REGIMEN DE ARRENDAMIENTO')";
$sql["20160802"][]	= "UPDATE `personas_regimen_fiscal` SET `nombre_del_regimen` = 'PF EN EL REGIMEN DE HONORARIOS' WHERE `clave_de_regimen` = '202'";
$sql["20160802"][]	= "ALTER TABLE `personas_morales_anx` ADD COLUMN `notaria_poder` VARCHAR(10) NULL COMMENT '' AFTER `fecha_de_baja`,ADD COLUMN `notario_poder` VARCHAR(100) NULL COMMENT '' AFTER `notaria_poder`";
$sql["20160802"][]	= "ALTER TABLE `socios_vivienda` CHANGE COLUMN `estado_actual` `estado_actual` INT(3) NOT NULL DEFAULT '99' COMMENT '99 no verificado 1 verificado 0 baja'";
$sql["20160802"][]	= "ALTER TABLE `socios_aeconomica` CHANGE COLUMN `estado_actual` `estado_actual` INT(3) NOT NULL DEFAULT '99' COMMENT '0 Baja'";


$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300544'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185600'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_description` = 'Modulo de Administracion de Productos de Credito' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '3073'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300545'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185601'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300546'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185602'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300547'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185603'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300548'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185604'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogos de Tipos de Persona' , `menu_description` = 'Administracion del Tipo de Ingreso de Personas' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2053'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300549'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185605'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Regiones de Personas' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2054'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300550'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185606'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Colonias en Domicilio' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2055'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Bases del Sistema' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '11015'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300551'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185607'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Tipos de regimenes Fiscales' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2056'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300552'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185608'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_description` = 'Administracion de Tipos de Riesgo AML' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '72201'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300553'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185609'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300554'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185610'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300555'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185611'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300556'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185612'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300557'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185613'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300558'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185614'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300559'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185615'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300560'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185616'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300561'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185617'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300562'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185618'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300563'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185619'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300564'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185620'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300565'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185621'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300566'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185622'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300567'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185623'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300568'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185624'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300569'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185625'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99354'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300570'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185626'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99355'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300571'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185627'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99356'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300572'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '185628'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99357'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300573'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99358'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300574'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300575'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99360'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300576'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99359'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99361'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300577'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99362'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300578'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99363'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300579'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99364'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300580'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99365'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300581'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99366'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '300582'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99367'";
$sql["20160803"][]	= "UPDATE `general_menu` SET `menu_title` = '' WHERE `idgeneral_menu` = '99369'";


$sql["20160804"][]	= "ALTER TABLE `creditos_plan_de_pagos` ADD COLUMN `descuentos` FLOAT(12,2) NULL DEFAULT '0.00' COMMENT '' AFTER `mora`";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_parent` = '1000' , `menu_image` = 'fa-ellipsis-v',`menu_description` = 'Otros Cobros' , `menu_order` = '601' WHERE `idgeneral_menu` = '1073'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '900' WHERE `idgeneral_menu` = '1010'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '901' WHERE `idgeneral_menu` = '1050'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '800' WHERE `idgeneral_menu` = '9000'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '100' WHERE `idgeneral_menu` = '1003'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '101' WHERE `idgeneral_menu` = '1002'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '210' WHERE `idgeneral_menu` = '1020'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '220' WHERE `idgeneral_menu` = '1030'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '300' WHERE `idgeneral_menu` = '1040'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '500' WHERE `idgeneral_menu` = '1060'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '620' WHERE `idgeneral_menu` = '1090'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '610' WHERE `idgeneral_menu` = '1070'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '510' WHERE `idgeneral_menu` = '1080'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '520' WHERE `idgeneral_menu` = '1060'";
$sql["20160804"][]	= "UPDATE `general_menu` SET `menu_order` = '301' WHERE `idgeneral_menu` = '1008'";

$sql["20160804"][]	= "CREATE TABLE IF NOT EXISTS `entidad_creditos_proyecciones` (  `identidad_proyeccion` INT NOT NULL AUTO_INCREMENT,  `periocidad` INT(4) NOT NULL DEFAULT 30,  `fecha_inicial` DATE NULL,  `fecha_final` DATE NULL,  `sucursal` VARCHAR(20) NULL DEFAULT 'todas' COMMENT 'sucursal sin aplica',  `idusuario` INT(6) NULL DEFAULT 0 COMMENT 'Usuaio quien lo genera',  `capital` DOUBLE(19,2) NULL DEFAULT 0,  `interes` DOUBLE(19,2) NULL DEFAULT 0,  `iva` DOUBLE(19,2) NULL DEFAULT 0,  `ahorros` DOUBLE(19,2) NULL DEFAULT 0,  `total` DOUBLE(19,2) NULL DEFAULT 0,  `tipo` INT(4) NULL DEFAULT 1 COMMENT '1 automatico',  `descripcion` VARCHAR(60) NULL,  PRIMARY KEY (`identidad_proyeccion`) ) ENGINE = InnoDB";
$sql["20160804"][]	= "ALTER TABLE `entidad_creditos_proyecciones` ADD COLUMN `clave` VARCHAR(40) NULL AFTER `descripcion`";

$sql["20160804"][]	= "ALTER TABLE `entidad_creditos_proyecciones` ADD COLUMN `otros` DOUBLE(19,2) NULL DEFAULT 0 AFTER `clave`";

$sql["20160805"][]	= "ALTER TABLE `operaciones_tipos` ADD COLUMN `precio` FLOAT(12,2) NULL DEFAULT 0 COMMENT '' AFTER `estatus`";
$sql["20160805"][]	= "CREATE TABLE IF NOT EXISTS `operaciones_promociones` (  `idoperaciones_promociones` INT NOT NULL AUTO_INCREMENT,  `tipo_promocion` INT(2) NULL DEFAULT 1 COMMENT '1 descuento base ',  `fecha_inicial` DATE NULL,  `fecha_final` DATE NULL,  `tipo_operacion` INT(8) NULL DEFAULT 0 COMMENT 'tipo de operacion que aplica',  `condiciones` TEXT NULL,  `num_items` INT(4) NULL DEFAULT 0,  `descuento` FLOAT(6,4) NULL DEFAULT 0,  `sucursal` VARCHAR(20) NULL DEFAULT 'todas',  PRIMARY KEY (`idoperaciones_promociones`) ) ENGINE = INNODB";
$sql["20160805"][]	= "CREATE TABLE IF NOT EXISTS  `personas_consulta_lista` ( `idpersonas_consulta_lista` INT NOT NULL,  `persona` BIGINT(20) NULL,  `fecha` DATE NULL,  `tiempo` INT(10) NULL,  `url` TEXT NULL,  `tipo` VARCHAR(15) NULL COMMENT 'peps bloqueados',  `proveedor` VARCHAR(15) NULL COMMENT 'interno quienesquien',  `idusuario` INT(8) NULL,  PRIMARY KEY (`idpersonas_consulta_lista`) ) ENGINE = INNODB";
$sql["20160805"][]	= "ALTER TABLE `entidad_calificacion` ADD INDEX `sseach1` (`clave_de_documento` ASC, `tipo_de_objeto` ASC, `topico` ASC) ";
$sql["20160805"][]	= "ALTER TABLE `personas_perfil_transaccional` ADD INDEX `sseachr1` (`clave_de_persona` ASC, `clave_de_tipo_de_perfil` ASC, `pais_de_origen` ASC) ";
$sql["20160805"][]	= "ALTER TABLE `personas_documentacion` ADD INDEX `sseach1` (`clave_de_persona` ASC, `tipo_de_documento` ASC)";
$sql["20160805"][]	= "ALTER TABLE `tesoreria_monedas` CHANGE COLUMN `quivalencia_en_moneda_local` `quivalencia_en_moneda_local` FLOAT(8,4) NULL DEFAULT '0.0000' ,CHANGE COLUMN `pais_de_origen` `pais_de_origen` VARCHAR(6) NULL DEFAULT 'MX' ";
$sql["20160805"][]	= "ALTER TABLE `tesoreria_monedas` ADD INDEX `sseach1` (`pais_de_origen` ASC, `clave_de_moneda` ASC) ";



$sql["20160805"][]	= "UPDATE `general_niveles` SET task_events =''";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Registro de usarios' , `menu_image` = 'fa-users' , `menu_order` = '100' , `menu_help_id` = '10001' WHERE `idgeneral_menu` = '10001'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_description` = 'Asignar Permisos en el Sistema' , `menu_image` = 'fa-check-square' , `menu_order` = '200' , `menu_help_id` = '10003' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10003'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_description` = 'Respaldar o Restaurar Permisos' , `menu_image` = 'fa-download' , `menu_order` = '300' , `menu_help_id` = '10016' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10016'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_type` = 'command' WHERE `idgeneral_menu` = '10003'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Respaldo/Restaurar Permisos' WHERE `idgeneral_menu` = '10016'";
$sql["20160805"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('10004', '10010', 'Editar Usuarios', 'frmsecurity/usuarios-edicion.frm.php', 'Edicion de usuarios existentes', 'fa-user-times', 'command', '101', '10004', 'true')";

$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '9'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '14'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '5'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;' WHERE `idgeneral_niveles` = '99'";

$sql["20160805"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('400', 'Usted no tiene permisos para Acceder a el Modulo', 'security')";
$sql["20160805"][]	= "UPDATE `general_error_codigos` SET `description_error` = 'Usted no tiene permisos del Sistema para este Modulo' WHERE `idgeneral_error_codigos` = '999'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Analizar Bitacoras del Sistema' , `menu_destination` = 'principal' , `menu_description` = 'Leer y ver Bitacoras de SAFE-OSMS' , `menu_image` = 'fa-eye' , `menu_type` = 'command' , `menu_order` = '200' , `menu_help_id` = '10024' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10024'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_order` = '300' , `menu_help_id` = '10023' WHERE `idgeneral_menu` = '10023'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Bitacoras del Servidor' , `menu_description` = 'Leer Bitacoras del SO/Servidor' , `menu_image` = 'fa-bug' WHERE `idgeneral_menu` = '10023'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Reporte de Usuarios' , `menu_description` = 'Reporte de Usuario del Sistema' , `menu_image` = 'fa-user-secret' , `menu_order` = '500' , `menu_help_id` = '10022' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '10022'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Eventos del Sistema' , `menu_image` = 'fa-video-camera' , `menu_order` = '100' , `menu_help_id` = '10021' WHERE `idgeneral_menu` = '10021'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Bitacoras del Sistema' WHERE `idgeneral_menu` = '10024'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Asignar Causas de Mora' , `menu_description` = 'Asignar motivos de vencimiento de Creditos' , `menu_image` = 'fa-indent' , `menu_order` = '3' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '4031'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_description` = 'Asignar Oficiales de Credito' , `menu_image` = 'fa-users' , `menu_order` = '1' , `menu_help_id` = '4021' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '4021'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_description` = 'Edicion masiva de datos de Credito' , `menu_image` = 'fa-list' , `menu_order` = '4' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '4032'";
$sql["20160805"][]	= "UPDATE `general_menu` SET `menu_title` = 'Operaciones Masivas' , `menu_help_id` = '4030' WHERE `idgeneral_menu` = '4030'; ";
$sql["20160805"][]	= "CREATE TABLE IF NOT EXISTS `personas_ae_scian` (  `clave_interna` bigint(20) NOT NULL,  `clave_de_actividad` varchar(20) DEFAULT '',  `nombre_de_la_actividad` varchar(200) DEFAULT NULL,  `clasificacion` varchar(20) DEFAULT NULL,  `clave_de_superior` bigint(20) DEFAULT '0',  `clave_aml` varchar(20) DEFAULT NULL COMMENT 'clave relacionada en AML',  PRIMARY KEY (`clave_interna`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
$sql["20160805"][]	= "ALTER TABLE `creditos_plan_de_pagos` ADD COLUMN `penas` FLOAT(12,2) NULL DEFAULT 0 COMMENT '' AFTER `ahorro`,ADD COLUMN `gtoscbza` FLOAT(12,2) NULL DEFAULT 0 COMMENT '' AFTER `penas`,ADD COLUMN `mora` FLOAT(12,2) NULL DEFAULT 0 COMMENT '' AFTER `gtoscbza`";

$sql["20160805"][]	= "DELETE FROM `general_structure`  WHERE `tabla`='operaciones_recibos'";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','idoperaciones_recibos','primary_key','bigint','20','','Numero de Recibo','text','NA','derecha','1','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','fecha_operacion','0000-00-00','date','0','','Fecha de Operacion','text','NA','derecha','2','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','tipo_docto','99','int','4','','Tipo de Recibo','select','SELECT idoperaciones_recibostipo, descripcion_recibostipo     FROM operaciones_recibostipo','derecha','3','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','tipo_pago','ninguno','varchar','35','','Tipo de pago','select','SELECT `tipo_de_pago`,`descripcion` FROM `tesoreria_tipos_de_pago`','derecha','4','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','docto_afectado','0','bigint','20','','Documento','text','NA','derecha','5','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','grupo_asociado','99','int','10','','Grupo Solidario','select','SELECT `idsocios_grupossolidarios`,`nombre_gruposolidario` FROM `socios_grupossolidarios`','derecha','7','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','persona_asociada','0','bigint','20','','Empresa','select','SELECT `idsocios_aeconomica_dependencias`,`descripcion_dependencia` FROM `socios_aeconomica_dependencias`','derecha','8',NULL,NULL,'','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','clave_de_moneda','MXN','varchar','10','Clave de Moneda del Recibo','Moneda','select','SELECT * FROM tesoreria_monedas','derecha','11','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','unidades_en_moneda','0','float','20','Unidades Originales','Unidades Originales','text','','derecha','12','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cuenta_bancaria','0','bigint','20','','Cuenta bancaria','text','SELECT * FROM `bancos_cuentas`','derecha','20',NULL,NULL,'','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cheque_afectador','N/A','varchar','20','','Numero de Cheque','text','NA','derecha','30','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','recibo_fiscal','N/A','varchar','15','','Recibo Impreso','text','NA','derecha','31','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','observacion_recibo','','varchar','200','','Observaciones','text','NA','derecha','51','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cadena_distributiva','N/A','varchar','100','','Descripcion','hidden','NA','derecha','55','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','archivo_fisico','','varchar','200','','Archivo fisico','hidden',NULL,'derecha','100',NULL,NULL,'','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','eacp','EN_TRAMITE','varchar','20','','Entidad','hidden','NA','derecha','106','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','fecha_de_registro','0000-00-00','date','0','','Fecha de registro','hidden',NULL,'derecha','107',NULL,NULL,'','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','idusuario','99','int','4','','usuario','hidden','NA','derecha','111','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','indice_origen','99','int','4','','Clave de Origen','hidden','NA','derecha','112','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','numero_socio','0','int','10','','Clave de Persona','hidden','NA','derecha','113','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','origen_aml','0','int','4','','Origen AML','hidden',NULL,'derecha','115',NULL,NULL,'','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','periodo_de_documento','0','int','4','','Periodo','text',NULL,'derecha','6',NULL,NULL,'','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','sucursal','MATRIZ','varchar','10','','Sucursal','hidden','NA','derecha','119','','','0','normalfield','')";
$sql["20160805"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','total_operacion','0.00','float','25','','Total','text','NA','derecha','122','','','0','normalfield','')";
$sql["20160805"][]	= "INSERT INTO `general_formulas` (`aplicado_a`, `estructura_de_la_formula`) VALUES ('php_mora_x_letra', '\$moratorio = ( \$BASE_MORA * \$DIAS_MORA * \$TASA_MORA ) / 360;'); ";

$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_ELIMINAR_RECIBOS=true;' WHERE `idgeneral_niveles` = '4'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_ELIMINAR_RECIBOS=true;' WHERE `idgeneral_niveles` = '5'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_ELIMINAR_RECIBOS=true;' WHERE `idgeneral_niveles` = '6'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;PUEDE_ELIMINAR_RECIBOS=true;' WHERE `idgeneral_niveles` = '9'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;PUEDE_ELIMINAR_RECIBOS=true;' WHERE `idgeneral_niveles` = '14'";
$sql["20160805"][]	= "UPDATE `general_niveles` SET `rules_by_user` = 'PUEDE_EDITAR_USUARIOS=true;PUEDE_AGREGAR_USUARIOS=true;PUEDE_ELIMINAR_RECIBOS=true;' WHERE `idgeneral_niveles` = '99'";

$sql["20160805"][]	= "ALTER TABLE `empresas_operaciones` CHANGE COLUMN `clave_de_empresa` `clave_de_empresa` INT(8) NULL DEFAULT '99' ,CHANGE COLUMN `periodo_marcado` `periodo_marcado` INT(4) NULL DEFAULT '0' ,CHANGE COLUMN `tipo_de_operacion` `tipo_de_operacion` INT(2) NULL DEFAULT '1' ,CHANGE COLUMN `oficial` `oficial` INT(8) NULL DEFAULT '99' ,CHANGE COLUMN `periocidad` `periocidad` INT(4) NULL DEFAULT '7' ,ADD INDEX `sseach1` (`clave_de_empresa` ASC, `periodo_marcado` ASC, `tipo_de_operacion` ASC) ";
$sql["20160805"][]	= "ALTER TABLE `socios_relaciones` DROP PRIMARY KEY,ADD PRIMARY KEY (`idsocios_relaciones`), ADD INDEX `sseach1` (`socio_relacionado` ASC, `credito_relacionado` ASC, `tipo_relacion` ASC, `numero_socio` ASC, `consanguinidad` ASC)";

$sql["20160902"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('20105', 'Envio Letra en Nomina duplicada', 'common')";
$sql["20160902"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `ideacp_config_bases_de_integracion_miembros` = '588' WHERE `codigo_de_base` = '5002' AND `miembro` = '53'";
$sql["20160902"][]	= "ALTER TABLE `eacp_config_bases_de_integracion_miembros` DROP PRIMARY KEY, ADD PRIMARY KEY (`ideacp_config_bases_de_integracion_miembros`),ADD INDEX `mme` (`codigo_de_base` ASC, `miembro` ASC, `subclasificacion` ASC)";
$sql["20160902"][]	= "ALTER TABLE  `operaciones_mvtos` DROP INDEX `sseach1` , ADD INDEX `sseach1` (`docto_afectado` ASC, `socio_afectado` ASC, `recibo_afectado` ASC, `tipo_operacion` ASC, `periodo_socio` ASC)";
$sql["20160902"][]	= "ALTER TABLE `operaciones_mvtos` ADD INDEX `tipoops` (`tipo_operacion` ASC, `idoperaciones_mvtos` ASC)";
$sql["20160902"][]	= "ALTER TABLE `creditos_sdpm_historico` DROP PRIMARY KEY, ADD PRIMARY KEY (`idcreditos_sdpm_historico`) ,ADD INDEX `bydocto` (`numero_de_credito` ASC, `periodo` ASC, `tipo_de_operacion` ASC, `numero_de_socio` ASC) ";
$sql["20160902"][]	= "ALTER TABLE `creditos_montos` ADD INDEX `bycred` (`clave_de_credito` ASC)";

$sql["20160903"][]	= "ALTER TABLE `socios_aeconomica_dependencias` DROP PRIMARY KEY,ADD PRIMARY KEY (`idsocios_aeconomica_dependencias`) ,ADD INDEX `peerp` (`idsocios_aeconomica_dependencias` ASC, `clave_de_persona` ASC)";
$sql["20160903"][]	= "ALTER TABLE `socios_tipoingreso` CHANGE COLUMN `idsocios_tipoingreso` `idsocios_tipoingreso` INT(5) UNSIGNED NOT NULL DEFAULT '0' ,CHANGE COLUMN `descripcion_detallada` `descripcion_detallada` VARCHAR(100) NULL DEFAULT NULL";
$sql["20160903"][]	= "ALTER TABLE `socios_general` DROP INDEX `persona` ,ADD INDEX `persona` (`codigo` ASC, `tipoingreso` ASC, `dependencia` ASC, `grupo_solidario` ASC) ";

$sql["20160903"][]	= "ALTER TABLE `bancos_operaciones` DROP INDEX `indice_por_recibo` ,ADD INDEX `indice_por_recibo` (`recibo_relacionado` ASC, `idcontrol` ASC)";
$sql["20160903"][]	= "ALTER TABLE `bancos_operaciones` ADD INDEX `idexmax` (`recibo_relacionado` ASC, `cuenta_bancaria` ASC, `numero_de_socio` ASC, `documento_de_origen` ASC, `idcontrol` ASC)";
$sql["20160903"][]	= "ALTER TABLE `operaciones_recibos` ADD COLUMN `operaciones_reciboscol` VARCHAR(45) NULL  AFTER `cuenta_bancaria`,ADD COLUMN `montohist` DOUBLE(16,2) NULL DEFAULT '0' AFTER `operaciones_reciboscol`";
$sql["20160903"][]	= "ALTER TABLE `personas_consulta_lista` CHANGE COLUMN `persona` `persona` BIGINT(20) NULL DEFAULT 0 ,CHANGE COLUMN `tiempo` `tiempo` INT(10) NULL DEFAULT 0 ,CHANGE COLUMN `proveedor` `proveedor` VARCHAR(15) NULL DEFAULT 'interno' COMMENT 'interno quienesquien' ,CHANGE COLUMN `idusuario` `idusuario` INT(8) NULL DEFAULT 0 ,ADD COLUMN `coincidente` INT(2) NULL DEFAULT 0 AFTER `idusuario`,ADD COLUMN `razones` VARCHAR(100) NULL DEFAULT '' AFTER `coincidente`";
$sql["20160903"][]	= "CREATE TABLE IF NOT EXISTS `aml_listanegra_int` ( `clave_interna` INT NOT NULL AUTO_INCREMENT,  `persona` BIGINT(25) NULL,  `fecha_de_registro` DATE NULL DEFAULT '2015-01-01',  `fecha_de_vencimiento` DATE NULL DEFAULT '2015-01-01',  `riesgo` INT(4) NULL DEFAULT 0,  `observaciones` VARCHAR(100) NULL,  `sucursal` VARCHAR(20) NULL DEFAULT 'matriz',  `idusuario` INT(6) NULL DEFAULT 1,  `idmotivo` INT(4) NULL COMMENT 'Clave de motivo de Registro',  PRIMARY KEY (`clave_interna`) ) ENGINE = INNODB";

$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `index_struct` = '1975'";
$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `index_struct` = '1976'";
$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `index_struct` = '1977'";
$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'select' , `sql_select` = 'SELECT `clave_de_control`,`nombre_oficial` FROM `personas_domicilios_paises`' WHERE `index_struct` = '1974'";
$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'select' , `sql_select` = 'SELECT `clave_unica`, `nombre_de_la_localidad` FROM `catalogos_localidades`' WHERE `index_struct` = '1973'";
$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `index_struct` = '1972'";
$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `index_struct` = '1971'";
$sql["20160905"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `index_struct` = '1970'";
$sql["20160905"][]	= "ALTER TABLE `operaciones_recibos` DROP PRIMARY KEY,ADD PRIMARY KEY (`idoperaciones_recibos`) ,ADD INDEX `bysoc` (`numero_socio` ASC, `idoperaciones_recibos` ASC, `docto_afectado` ASC, `tipo_docto` ASC, `persona_asociada` ASC, `grupo_asociado` ASC) ,ADD INDEX `bycred` (`docto_afectado` ASC, `idoperaciones_recibos` ASC, `tipo_docto` ASC, `periodo_de_documento` ASC, `origen_aml` ASC, `clave_de_moneda` ASC, `tipo_pago` ASC, `cuenta_bancaria` ASC, `persona_asociada` ASC, `grupo_asociado` ASC, `idusuario` ASC)";

$sql["20160906"][]	= "ALTER TABLE `operaciones_recibos` DROP COLUMN `operaciones_reciboscol`";
$sql["20160906"][]	= "UPDATE `socios_memo` SET `archivado`=0";

$sql["20160907"][]	= "ALTER TABLE `creditos_rechazados` ADD COLUMN `claverechazo` INT(3) NULL DEFAULT 0 AFTER `notas`,ADD INDEX `idcred` (`numero_de_credito` ASC, `idcreditos_rechazados` ASC, `claverechazo` ASC)";
$sql["20160907"][]	= "ALTER TABLE `socios_memo` DROP PRIMARY KEY,ADD PRIMARY KEY (`idsocios_memo`) ,ADD INDEX `idxp` (`numero_socio` ASC, `numero_solicitud` ASC, `tipo_memo` ASC, `archivado` ASC, `numero_gposolidario` ASC)";

$sql["20160907"][]	= "ALTER TABLE `general_menu` ADD INDEX `idxp` (`menu_parent` ASC, `menu_rules` ASC, `idgeneral_menu` ASC) ,ADD INDEX `idf` (`menu_rules` ASC, `menu_file` ASC, `idgeneral_menu` ASC)";
$sql["20160907"][]	= "CREATE TABLE IF NOT EXISTS `entidad_reportes_props` ( `identidad_reportes_props` INT NOT NULL AUTO_INCREMENT,  `idconfiguracion` VARCHAR(20) NULL,  `omitir` VARCHAR(200) NULL,  `titulo` VARCHAR(100) NULL,  `pretareas` VARCHAR(100) NULL COMMENT 'codigo sql de pretareas',  PRIMARY KEY (`identidad_reportes_props`) ) ENGINE = INNODB";
$sql["20160907"][]	= "INSERT INTO `entidad_reportes_props` (`identidad_reportes_props`, `idconfiguracion`) VALUES ('1', 'OPERACIONES-INGRESOS')";

$sql["20160909"][]	= "INSERT INTO  `entidad_reportes_props` (`identidad_reportes_props`, `idconfiguracion`) VALUES ('2', 'CORTE-RECIBOS')";

$sql["20160910"][]	= "UPDATE `general_structure` SET `control` = 'text' , `sql_select` = '' WHERE `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'unidades_de_moneda'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `sql_select` = 'SELECT clave_de_moneda, nombre_de_la_moneda FROM tesoreria_monedas' WHERE  `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'moneda_de_operacion'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `titulo` = 'Moneda',`control` = 'select' WHERE `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'moneda_de_operacion'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `descripcion` = 'Documento Descontado o de Origen' , `titulo` = 'Documento Origen' WHERE `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'documento_descontado'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'hora'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `tipo` = 'int' , `longitud` = '4' , `descripcion` = 'Tipo de Operacion en caja' , `titulo` = 'Operacion' , `control` = 'hidden' WHERE `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'tipo_de_movimiento'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `longitud` = '8' , `descripcion` = '' , `titulo` = 'Clave' , `control` = 'hidden' WHERE `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'idtesoreria_cajas_movimientos'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `longitud` = '20' , `descripcion` = 'Clave de recibo de Origen' , `control` = 'hidden' WHERE  `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'recibo'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `descripcion` = '' , `control` = 'hidden' WHERE  `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'persona'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `descripcion` = 'Tipo de Pago' , `titulo` = 'Tipo de Pago' , `control` = 'select' , `sql_select` = 'SELECT tipo_de_pago,descripcion FROM tesoreria_tipos_de_pago' WHERE  `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'tipo_de_exposicion'";
$sql["20160910"][]	= "UPDATE `general_structure` SET `control` = 'select' , `sql_select` = 'SELECT idbancos_entidades, nombre_de_la_entidad FROM bancos_entidades'  WHERE  `tabla` = 'tesoreria_cajas_movimientos' AND `campo` = 'banco'";


$sql["20160911"][]	= "DELETE FROM `general_structure`  WHERE `tabla`='operaciones_recibos'";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','idoperaciones_recibos','primary_key','bigint','20','','Numero de Recibo','text','NA','derecha','1','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','fecha_operacion','0000-00-00','date','0','','Fecha de Operacion','text','NA','derecha','2','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','tipo_docto','99','int','4','','Tipo de Recibo','select','SELECT idoperaciones_recibostipo, descripcion_recibostipo     FROM operaciones_recibostipo','derecha','3','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','tipo_pago','ninguno','varchar','35','','Tipo de pago','select','SELECT `tipo_de_pago`,`descripcion` FROM `tesoreria_tipos_de_pago`','derecha','4','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','docto_afectado','0','bigint','20','','Documento','text','NA','derecha','5','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','grupo_asociado','99','int','10','','Grupo Solidario','select','SELECT `idsocios_grupossolidarios`,`nombre_gruposolidario` FROM `socios_grupossolidarios`','derecha','7','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','persona_asociada','0','bigint','20','','Empresa','select','SELECT `idsocios_aeconomica_dependencias`,`descripcion_dependencia` FROM `socios_aeconomica_dependencias`','derecha','8',NULL,NULL,'','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','clave_de_moneda','MXN','varchar','10','Clave de Moneda del Recibo','Moneda','select','SELECT * FROM tesoreria_monedas','derecha','11','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','unidades_en_moneda','0','float','20','Unidades Originales','Unidades Originales','text','','derecha','12','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cuenta_bancaria','0','bigint','20','','Cuenta bancaria','text','SELECT * FROM `bancos_cuentas`','derecha','20',NULL,NULL,'','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cheque_afectador','N/A','varchar','20','','Numero de Cheque','text','NA','derecha','30','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','recibo_fiscal','N/A','varchar','15','','Recibo Impreso','text','NA','derecha','31','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','observacion_recibo','','varchar','200','','Observaciones','text','NA','derecha','51','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','cadena_distributiva','N/A','varchar','100','','Descripcion','hidden','NA','derecha','55','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','archivo_fisico','','varchar','200','','Archivo fisico','hidden',NULL,'derecha','100',NULL,NULL,'','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','eacp','EN_TRAMITE','varchar','20','','Entidad','hidden','NA','derecha','106','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','fecha_de_registro','0000-00-00','date','0','','Fecha de registro','hidden',NULL,'derecha','107',NULL,NULL,'','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','idusuario','99','int','4','','usuario','hidden','NA','derecha','111','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','indice_origen','99','int','4','','Clave de Origen','hidden','NA','derecha','112','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','numero_socio','0','int','10','','Clave de Persona','hidden','NA','derecha','113','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','origen_aml','0','int','4','','Origen AML','hidden',NULL,'derecha','115',NULL,NULL,'','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','periodo_de_documento','0','int','4','','Periodo','text',NULL,'derecha','6',NULL,NULL,'','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','sucursal','MATRIZ','varchar','10','','Sucursal','hidden','NA','derecha','119','','','0','normalfield','')";
$sql["20160911"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('operaciones_recibos','total_operacion','0.00','float','25','','Total','text','NA','derecha','122','','','0','normalfield','')";

$sql["20160911"][]	= "ALTER TABLE `personas_documentacion` CHANGE COLUMN `tipo_de_documento` `tipo_de_documento` INT(4) NOT NULL DEFAULT '0' ,CHANGE COLUMN `fecha_de_carga` `fecha_de_carga` INT(11) NULL DEFAULT '0' ,CHANGE COLUMN `fecha_de_verificacion` `fecha_de_verificacion` INT(11) NULL DEFAULT '0' ,CHANGE COLUMN `oficial_que_verifico` `oficial_que_verifico` INT(6) NULL DEFAULT '0' ,CHANGE COLUMN `resultado_de_la_verificacion` `resultado_de_la_verificacion` INT(2) NULL DEFAULT '0' COMMENT '0 pendiente 1 real -1 falso' ,CHANGE COLUMN `numero_de_pagina` `numero_de_pagina` VARCHAR(10) NULL DEFAULT '1' ,CHANGE COLUMN `usuario` `usuario` INT(6) NULL DEFAULT '1' ,ADD COLUMN `vencimiento` DATE NULL AFTER `documento_relacionado`,DROP PRIMARY KEY,ADD PRIMARY KEY (`clave_de_control`) ,DROP INDEX `sseach1` ,ADD INDEX `sseach1` (`clave_de_persona` ASC, `tipo_de_documento` ASC, `documento_relacionado` ASC) ";
$sql["20160911"][]	= "ALTER TABLE `socios_relaciones` DROP PRIMARY KEY,ADD PRIMARY KEY (`idsocios_relaciones`), ADD INDEX `sseach1` (`socio_relacionado` ASC, `credito_relacionado` ASC, `tipo_relacion` ASC, `numero_socio` ASC, `consanguinidad` ASC)";
$sql["20160911"][]	= "ALTER TABLE `creditos_preclientes` ADD COLUMN `idpersona` BIGINT(20) NULL DEFAULT 0 AFTER `monto`,ADD COLUMN `idcredito` BIGINT(20) NULL DEFAULT 0 AFTER `idpersona`,ADD INDEX `idpc` (`idcontrol` ASC, `idpersona` ASC, `idcredito` ASC) ";
$sql["20160911"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('102', 'Persona Editada', 'common')";

$sql["20160912"][]	= "UPDATE `personas_documentacion` SET `vencimiento`='2018-01-01'";
$sql["20160912"][]	= "ALTER TABLE `personas_consulta_lista` CHANGE COLUMN `idpersonas_consulta_lista` `idpersonas_consulta_lista` INT(11) NOT NULL AUTO_INCREMENT ,ADD INDEX `ss12` (`persona` ASC, `idusuario` ASC, `idpersonas_consulta_lista` ASC)";
$sql["20160912"][]	= "ALTER TABLE `personas_consulta_lista` ADD COLUMN `textocoincidente` VARCHAR(150) NULL DEFAULT '' AFTER `razones`";
$sql["20160912"][]	= "ALTER TABLE `aml_listanegra_int` CHANGE COLUMN `idmotivo` `idmotivo` INT(8) NULL DEFAULT 0 COMMENT 'Clave de motivo de Registro' ,ADD COLUMN `estatus` INT(2) NULL DEFAULT 1 COMMENT '1 Activo' AFTER `idmotivo`";
$sql["20160912"][]	= "DELETE FROM `general_structure` WHERE `tabla` = 't_03f996214fba4a1d05a68b18fece8e71'";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','idusuarios','','int','0',NULL,'Idusuarios','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','f_28fb96d57b21090705cfdf8bc3445d2a','','varchar','60',NULL,'Nombre','text','NA','derecha','2','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','f_34023acbff254d34664f94c3e08d836e','','varchar','40',NULL,'Password','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','nombres','','varchar','40',NULL,'Nombres','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','apellidopaterno','','varchar','40',NULL,'Apellidopaterno','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','apellidomaterno','','varchar','40',NULL,'Apellidomaterno','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','puesto','','varchar','40',NULL,'Puesto','text','NA','derecha','3','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','f_f2cd801e90b78ef4dc673a4659c1482d','1','int','4',NULL,'Nivel','select','SELECT * FROM `general_niveles` WHERE `idgeneral_niveles` !=99','derecha','4','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','periodo_responsable','0','int','4',NULL,'Periodo responsable','hidden','NA','derecha','200','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','estatus','|baja|activo|suspension','enum','20',NULL,'Estatus','text','NA','derecha','5','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','sucursal','MATRIZ','varchar','20',NULL,'Sucursal','select','SELECT * FROM `general_sucursales`','derecha','6','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','usr_options','','text','100',NULL,'Opciones','text','NA','derecha','20','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','date_expire','','date','0',NULL,'Expira','text','NA','derecha','8','','','0','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','cuenta_contable_de_caja','CUENTA_DE_CUADRE','varchar','25',NULL,'Cuenta de Caja','text',NULL,'derecha','7',NULL,NULL,'','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','codigo_de_persona','1','bigint','20',NULL,'Clave de Persona','text',NULL,'derecha','1',NULL,NULL,'','normalfield','')";
$sql["20160912"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('t_03f996214fba4a1d05a68b18fece8e71','alias','','varchar','20',NULL,'Alias','text',NULL,'derecha','200',NULL,NULL,'','normalfield','')";
$sql["20160912"][]	= "UPDATE  `general_structure` SET `control` = 'hidden' WHERE `tabla` = 't_03f996214fba4a1d05a68b18fece8e71' AND   `campo` ='estatus' ";
$sql["20160912"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = 't_03f996214fba4a1d05a68b18fece8e71' AND   `campo` ='usr_options' ";

$sql["20160912"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('100100', 'PERSONA_BLOQUEADA', 'La Persona Esta Bloqueada para Hacer operaciones')";
$sql["20160912"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('3040', '3000', 'Arrendamiento Puro', '', '', 'Modulo de Arrendamiento Puro', 'fa-car', 'parent', '3040', '3040', 'true')";
$sql["20160912"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('3041', '3040', 'Cotizador de Creditos', 'frmarrendamiento/cotizador.frm.php', 'Cotizador de Creditos en Arrendamiento Puro', 'fa-flag-checkered', 'command', '3041', '3041', 'true')";
$sql["20160912"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-building' WHERE `idgeneral_menu` = '7010'";

$sql["20160912"][]	= "UPDATE `aml_instrumentos_financieros` SET `descripcion` = 'Se debera utilizar cuando se otorguen derechos como medio de pago' WHERE `tipo_de_instrumento` = '8'";
$sql["20160912"][]	= "UPDATE `aml_instrumentos_financieros` SET `descripcion` = 'Se debera utilizar cuando se utilice cualquier mercancia bien como medio de pago' WHERE `tipo_de_instrumento` = '11'";
$sql["20160912"][]	= "CREATE TABLE IF NOT EXISTS `vehiculos_usos`( `idvehiculos_usos` INT NOT NULL,  `descripcion_uso` VARCHAR(50) NULL,  `limitededucible` FLOAT(12,2) NULL DEFAULT 0,  PRIMARY KEY (`idvehiculos_usos`)) ENGINE = INNODB";
$sql["20160912"][]	= "INSERT INTO `vehiculos_usos` (`idvehiculos_usos`, `descripcion_uso`, `limitededucible`) VALUES ('100', 'Personal', '6000')";
$sql["20160912"][]	= "INSERT INTO `vehiculos_usos` (`idvehiculos_usos`, `descripcion_uso`, `limitededucible`) VALUES ('200', 'De Carga', '999999999')";

$sql["20160913"][]	= "ALTER TABLE `creditos_preclientes` CHANGE COLUMN `monto` `monto` DOUBLE(16,2) NULL DEFAULT 0 ,ADD COLUMN `email` VARCHAR(40) NULL DEFAULT '' AFTER `monto`";
$sql["20160913"][]	= "ALTER TABLE  `creditos_preclientes` CHANGE COLUMN `telefono` `telefono` BIGINT(20) NULL DEFAULT 0";
$sql["20160913"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('3006', '3000', 'Pre-Solicitudes', 'frmcreditos/creditos-preclientes.frm.php', 'Lista de Pre Solicitudes de Credito', 'fa-shopping-basket', 'command', '3006', '3006', 'true')";
$sql["20160913"][]	= "ALTER TABLE `creditos_preclientes` ADD COLUMN `idorigen` INT(4) NULL DEFAULT '1' COMMENT '1 pagina' AFTER `idcredito`";
$sql["20160913"][]	= "ALTER TABLE `creditos_preclientes` ADD COLUMN `idestado` INT(2) NULL DEFAULT 1 COMMENT '1 activo' AFTER `idorigen`";
$sql["20160913"][]	= "ALTER TABLE `creditos_preclientes` ADD COLUMN `idoficial` INT(8) NULL DEFAULT 0 AFTER `idestado`";


$sql["20160914"][]	= "ALTER TABLE `personas_consulta_lista` CHANGE COLUMN `idpersonas_consulta_lista` `idpersonas_consulta_lista` INT(11) NOT NULL AUTO_INCREMENT ,ADD INDEX `ss12` (`persona` ASC, `idusuario` ASC, `idpersonas_consulta_lista` ASC)";
$sql["20160914"][]	= "ALTER TABLE `personas_consulta_lista` ADD COLUMN `textocoincidente` VARCHAR(150) NULL DEFAULT '' AFTER `razones`";
$sql["20160914"][]	= "ALTER TABLE `aml_listanegra_int` CHANGE COLUMN `idmotivo` `idmotivo` INT(8) NULL DEFAULT 0 COMMENT 'Clave de motivo de Registro' ,ADD COLUMN `estatus` INT(2) NULL DEFAULT 1 COMMENT '1 Activo' AFTER `idmotivo`";


$sql["20161001"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = 'operaciones_mvtos' AND `tabla` = 'afectacion_cobranza'";
$sql["20161001"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = 'operaciones_mvtos' AND `tabla` = 'afectacion_contable'";
$sql["20161001"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = 'operaciones_mvtos' AND `tabla` = 'fecha_operacion'";
$sql["20161001"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = 'operaciones_mvtos' AND `tabla` = 'fecha_afectacion'";
$sql["20161001"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla` = 'operaciones_mvtos' AND `tabla` = 'recibo_afectado'";

$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `vehiculos_segmento` ( `idvehiculos_segmento` INT NOT NULL AUTO_INCREMENT,  `nombre_segmento` VARCHAR(100) NULL,  PRIMARY KEY (`idvehiculos_segmento`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `vehiculos_marcas` (  `idvehiculos_marcas` INT NOT NULL AUTO_INCREMENT,  `nombre_marca` VARCHAR(80) NULL,  PRIMARY KEY (`idvehiculos_marcas`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `vehiculos_usos` (  `idvehiculos_usos` INT NOT NULL,  `descripcion_uso` VARCHAR(50) NULL,  `limitededucible` DOUBLE(18,2) NULL DEFAULT 0,  PRIMARY KEY (`idvehiculos_usos`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_comisiones` ( `idleasing_comisiones` INT NOT NULL AUTO_INCREMENT,  `tipo_de_originador` INT(4) NULL DEFAULT 0,  `tasa_comision` FLOAT(6,4) NULL DEFAULT 0,  PRIMARY KEY (`idleasing_comisiones`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `vehiculos_tenencia` (  `idvehiculos_tenencia` INT NOT NULL AUTO_INCREMENT,  `entidadfederativa` INT(4) NULL DEFAULT 0,  `cobrogestoria` FLOAT(10,2) NULL DEFAULT 0,  `placas` FLOAT(10,2) NULL DEFAULT 0,  `tenencia` FLOAT(6,4) NULL DEFAULT 0,  `limitetenencia` DOUBLE(12,2) NULL DEFAULT 0,  PRIMARY KEY (`idvehiculos_tenencia`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_tipo_rac` (  `idleasing_tipo_rac` INT NOT NULL,  `nombre_tipo_rac` VARCHAR(40) NULL,  PRIMARY KEY (`idleasing_tipo_rac`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_tasas` (  `idleasing_tasas` INT NOT NULL AUTO_INCREMENT,  `plazomin` INT(4) NULL DEFAULT 0,  `plazomax` INT(4) NULL DEFAULT 0,  `tipo_de_rac` INT(4) NULL DEFAULT 0,  `tasa_ofrecida` FLOAT(6,4) NULL DEFAULT 0,  PRIMARY KEY (`idleasing_tasas`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_escenarios` (  `idleasing_escenarios` INT NOT NULL AUTO_INCREMENT,  `frecuencia` INT(4) NOT NULL DEFAULT 30,  `plazo` INT(4) NOT NULL DEFAULT 12,  `descripcion_escenario` VARCHAR(40) NULL DEFAULT '',  PRIMARY KEY (`idleasing_escenarios`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `vehiculos_gps` (  `idvehiculos_gps` INT NOT NULL AUTO_INCREMENT,   `nombre_gps` VARCHAR(40) NULL,   PRIMARY KEY (`idvehiculos_gps`) ) ENGINE = InnoDB COMMENT = 'Paquetes GPS'";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_originadores` (  `idleasing_originadores` INT NOT NULL AUTO_INCREMENT,  `tipo_de_originador` INT(4) NULL DEFAULT 1,  `nombre_originador` VARCHAR(100) NULL,  `rfc_originador` VARCHAR(15) NULL,  `clave_de_persona` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Clave de persona en el caso de existir',  `clave_banco` INT(8) NULL DEFAULT 0,  `cuenta_clabe` VARCHAR(40) NULL,  `cuenta_bancaria` VARCHAR(40) NULL,  `frecuencia_de_pago` INT(6) NULL DEFAULT 7,  `email_de_contacto` VARCHAR(40) NULL,  PRIMARY KEY (`idleasing_originadores`) ) ENGINE = INNODB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_usuarios` (  `idleasing_usuarios` INT NOT NULL AUTO_INCREMENT,  `originador` INT(8) NULL DEFAULT 0,  `nombre` VARCHAR(10) NOT NULL DEFAULT '',   `pin` VARCHAR(40) NULL,   `correo_electronico` VARCHAR(80) NULL,   PRIMARY KEY (`idleasing_usuarios`) ) ENGINE = InnoDB COMMENT = 'usuarios de el cotizador'";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_plazos` (  `idleasing_plazos` INT NOT NULL AUTO_INCREMENT,  `frecuencia` INT(4) NULL DEFAULT 30 COMMENT 'semanal quincenal',  `limite_inferior` INT(4) NULL DEFAULT 0,  `limite_superior` INT(4) NULL DEFAULT 0,  PRIMARY KEY (`idleasing_plazos`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_residual` (  `idleasing_residual` INT NOT NULL AUTO_INCREMENT,  `frecuencia` INT(4) NULL DEFAULT 30 COMMENT 'semanal quincenal',  `limite_inferior` INT(4) NULL DEFAULT 0,  `limite_superior` INT(4) NULL DEFAULT 0,  `porciento_residual` FLOAT(6,4) NULL DEFAULT 0,   PRIMARY KEY (`idleasing_residual`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `originacion_leasing` (  `idoriginacion_leasing` INT NOT NULL AUTO_INCREMENT,  `fecha_origen` DATE NULL DEFAULT '0000-00-00',  `persona` BIGINT(20) NULL DEFAULT 0 COMMENT 'persona asignada',  `credito` BIGINT(20) NULL DEFAULT 0 COMMENT 'persona asignada',  `marca` INT(4) NULL DEFAULT 1 COMMENT '1 por defecto',  `modelo` VARCHAR(100) NULL,  `annio` VARCHAR(6) NULL,  `tipo_leasing` INT(2) NULL DEFAULT 1 COMMENT '1 puro 2 financiero',  `tipo_uso` INT(4) NULL DEFAULT 0,  `tipo_rac` INT(4) NULL DEFAULT 0,  `tipo_gps` INT(4) NULL DEFAULT 1 COMMENT 'clave de equipo en renta',  `originador` INT(8) NULL DEFAULT 0 COMMENT 'puede ser el ID de Agencia o Empleado',  `suboriginador` INT(8) NULL DEFAULT 0 COMMENT 'A quien se destina la comision o persona que origina',  `precio_vehiculo` DOUBLE(18,2) NULL DEFAULT 0 COMMENT 'Precio con Impuestos Incluidos',  `monto_aliado` DOUBLE(18,2) NULL DEFAULT 0 COMMENT 'Equipo Aliado',  `monto_accesorios` DOUBLE(18,2) NULL DEFAULT 0,  `monto_anticipo` DOUBLE(18,2) NULL DEFAULT 0,  `monto_tenencia` DOUBLE(18,2) NULL,  `monto_garantia` DOUBLE(18,2) NULL DEFAULT 0 COMMENT 'Garantia Extendida Financiada',  `monto_mtto` DOUBLE(18,2) NULL DEFAULT 0,  `comision_originador` FLOAT(6,4) NULL DEFAULT 0,  `comision_apertura` FLOAT(6,4) NULL DEFAULT 0,  `tasa_iva` FLOAT(6,4) NULL DEFAULT 0,  `tasa_compra` FLOAT(6,4) NULL DEFAULT 0,  `financia_seguro` INT(2) NULL DEFAULT 0,  `financia_tenencia` INT(2) NULL DEFAULT 0,  `domicilia` INT(2) NULL DEFAULT 0 COMMENT 'Si domicilia el Pago',  `paso_proceso` INT(4) NULL DEFAULT 0 COMMENT 'Pasos del proceso de Credito',  `describe_aliado` VARCHAR(150) NULL DEFAULT '',  `usuario` INT(8) NULL DEFAULT 0 COMMENT 'usuario que origina',  PRIMARY KEY (`idoriginacion_leasing`) ,  UNIQUE INDEX `tipo_leasing_UNIQUE` (`tipo_leasing` ASC) ) ENGINE = INNODB";
$sql["20161003"][]	= "INSERT INTO `vehiculos_segmento` (`idvehiculos_segmento`, `nombre_segmento`) VALUES ('1', 'Sedan')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_segmento` (`idvehiculos_segmento`, `nombre_segmento`) VALUES ('2', 'Pickup')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_segmento` (`idvehiculos_segmento`, `nombre_segmento`) VALUES ('3', 'Minivan')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_segmento` (`idvehiculos_segmento`, `nombre_segmento`) VALUES ('4', 'Deportivo')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_segmento` (`idvehiculos_segmento`, `nombre_segmento`) VALUES ('5', 'Carga')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_segmento` (`idvehiculos_segmento`, `nombre_segmento`) VALUES ('6', 'Premier')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_segmento` (`idvehiculos_segmento`, `nombre_segmento`) VALUES ('7', 'SUV')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) VALUES ('1', 'Acura')";
$sql["20161003"][]	= "INSERT INTO `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) VALUES ('2', 'Alfa Romeo')";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `creditos_notarios` (  `idcreditos_notarios` INT NOT NULL AUTO_INCREMENT,  `nombre_notario` VARCHAR(100) NULL,  `direccion` VARCHAR(150) NULL,  `numero_notario` VARCHAR(4) NULL,  PRIMARY KEY (`idcreditos_notarios`) ) ENGINE = InnoDB";
$sql["20161003"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('30342', '3040', 'Catalogo de Originadores', 'frmarrendamiento/originadores.frm.php', 'Catalogo de Originadores', 'fa-building-o', 'command', '3042', '3042')";
$sql["20161003"][]	= "INSERT INTO `leasing_originadores` (`idleasing_originadores`, `nombre_originador`, `rfc_originador`, `clave_de_persona`, `clave_banco`, `cuenta_clabe`, `cuenta_bancaria`, `email_de_contacto`) VALUES ('1', 'NINGUNO', '', '1', '1', '0', '0', '')";
$sql["20161003"][]	= "INSERT INTO `leasing_escenarios` (`idleasing_escenarios`, `descripcion_escenario`) VALUES ('1', '12 Mensualidades')";
$sql["20161003"][]	= "INSERT INTO `leasing_escenarios` (`idleasing_escenarios`, `plazo`, `descripcion_escenario`) VALUES ('2', '24', '24 Mensualidades')";
$sql["20161003"][]	= "INSERT INTO `leasing_escenarios` (`idleasing_escenarios`, `plazo`, `descripcion_escenario`) VALUES ('3', '36', '36 Mensualidades') ";
$sql["20161003"][]	= "INSERT INTO `leasing_escenarios` (`idleasing_escenarios`, `plazo`, `descripcion_escenario`) VALUES ('4', '48', '48 Mensualidades') ";
$sql["20161003"][]	= "ALTER TABLE `leasing_originadores` ADD COLUMN `tipo_de_comision` INT(4) NULL DEFAULT 1 AFTER `email_de_contacto`, ADD COLUMN `comision` FLOAT(6,4) NULL DEFAULT 0 AFTER `tipo_de_comision`";
$sql["20161003"][]	= "CREATE TABLE IF NOT EXISTS `leasing_originadores_tipos` (  `idleasing_originadores_tipos` INT NOT NULL AUTO_INCREMENT,  `nombre_tipo_originador` VARCHAR(40) NULL,  PRIMARY KEY (`idleasing_originadores_tipos`) ) ENGINE = INNODB";
$sql["20161003"][]	= "INSERT INTO `leasing_originadores_tipos` (`idleasing_originadores_tipos`, `nombre_tipo_originador`) VALUES ('1', 'Agencia')";
$sql["20161003"][]	= "INSERT INTO `leasing_originadores_tipos` (`idleasing_originadores_tipos`, `nombre_tipo_originador`) VALUES ('2', 'Promotor Externo')";
$sql["20161003"][]	= "INSERT INTO `leasing_originadores_tipos` (`idleasing_originadores_tipos`, `nombre_tipo_originador`) VALUES ('3', 'Broker/Franquicia')";
$sql["20161003"][]	= "INSERT INTO `leasing_originadores_tipos` (`idleasing_originadores_tipos`, `nombre_tipo_originador`) VALUES ('4', 'Prospeccion Directa')";

$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('3043', '3040', 'Originadores.- Usuarios', 'frmarrendamiento/originadores-usuarios.frm.php', 'Usuarios de Originadores', 'fa-users', 'command', '3043', '3043')";
$sql["20161004"][]	= "INSERT INTO `leasing_usuarios` (`idleasing_usuarios`, `originador`, `nombre`, `pin`, `correo_electronico`) VALUES ('1', '1', 'NINGUNO', '0101', 'tasks@opencorebanking.com')";
$sql["20161004"][]	= "ALTER TABLE `leasing_usuarios` ADD COLUMN `estatus` INT(2) NULL DEFAULT 1 COMMENT '1 Activo' AFTER `correo_electronico`";
$sql["20161004"][]	= "UPDATE `leasing_usuarios` SET `estatus` = '0' WHERE `idleasing_usuarios` = '1'";
$sql["20161004"][]	= "ALTER TABLE `leasing_usuarios` ADD COLUMN `administrador` INT(2) NULL DEFAULT 0 COMMENT '0 no' AFTER `estatus`";

$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `nombre_cliente` VARCHAR(150) NULL DEFAULT ''  AFTER `usuario`, ADD COLUMN `nombre_atn` VARCHAR(150) NULL DEFAULT ''  AFTER `nombre_cliente`";
$sql["20161004"][]	= "INSERT INTO `leasing_tipo_rac` (`idleasing_tipo_rac`, `nombre_tipo_rac`) VALUES ('1', 'Pequeña') ";
$sql["20161004"][]	= "INSERT INTO `leasing_tipo_rac` (`idleasing_tipo_rac`, `nombre_tipo_rac`) VALUES ('2', 'Mediana')";
$sql["20161004"][]	= "INSERT INTO `leasing_tipo_rac` (`idleasing_tipo_rac`, `nombre_tipo_rac`) VALUES ('3', 'Grande')";
$sql["20161004"][]	= "INSERT INTO `leasing_tipo_rac` (`idleasing_tipo_rac`, `nombre_tipo_rac`) VALUES ('4', 'Transnacional')";
$sql["20161004"][]	= "INSERT INTO  `vehiculos_gps` (`idvehiculos_gps`, `nombre_gps`) VALUES ('1', 'NINGUNO')";
$sql["20161004"][]	= "CREATE TABLE IF NOT EXISTS `vehiculos_gps_costeo` (  `idvehiculos_gps_costeo` INT NOT NULL AUTO_INCREMENT ,  `tipo_de_gps` INT(4) NULL DEFAULT 0 ,  `limite_inferior` INT(4) NULL DEFAULT 0 ,  `limite_superior` INT(4) NULL DEFAULT 0 ,  `monto_gps` DOUBLE(18,2) NULL DEFAULT 0 ,  PRIMARY KEY (`idvehiculos_gps_costeo`)  ) ENGINE = InnoDB";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `oficial` INT(8) NULL DEFAULT 0  AFTER `nombre_atn`,ADD COLUMN `total_credito` DOUBLE(18,2) NULL DEFAULT 0  AFTER `oficial`";

$sql["20161004"][]	= "INSERT INTO `leasing_plazos` (`idleasing_plazos`, `limite_superior`) VALUES ('1', '12')";
$sql["20161004"][]	= "INSERT INTO `leasing_plazos` (`idleasing_plazos`, `limite_inferior`, `limite_superior`) VALUES ('2', '13', '24')";
$sql["20161004"][]	= "INSERT INTO `leasing_plazos` (`idleasing_plazos`,`limite_inferior`, `limite_superior`) VALUES ('3', '25', '36')";
$sql["20161004"][]	= "INSERT INTO `leasing_plazos` (`idleasing_plazos`, `limite_inferior`, `limite_superior`) VALUES ('4', '37', '48')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('1', '12', '1', '27')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomin`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('2', '13', '24', '1', '26')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomin`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('3', '25', '36', '1', '25')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomin`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('4', '37', '48', '1', '24')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('5', '12', '2', '23')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomin`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('6', '13', '24', '2', '22')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomin`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('7', '25', '36', '2', '21')";
$sql["20161004"][]	= "INSERT INTO `leasing_tasas` (`idleasing_tasas`, `plazomin`, `plazomax`, `tipo_de_rac`, `tasa_ofrecida`) VALUES ('8', '37', '48', '2', '20')";
$sql["20161004"][]	= "INSERT INTO `leasing_residual` (`idleasing_residual`, `limite_superior`, `porciento_residual`) VALUES ('1', '12', '80')";
$sql["20161004"][]	= "INSERT INTO `leasing_residual` (`idleasing_residual`, `limite_inferior`, `limite_superior`, `porciento_residual`) VALUES ('2', '13', '24', '70')";
$sql["20161004"][]	= "INSERT INTO `leasing_residual` (`idleasing_residual`, `limite_inferior`, `limite_superior`, `porciento_residual`) VALUES ('3', '25', '36', '50')";
$sql["20161004"][]	= "INSERT INTO `leasing_residual` (`idleasing_residual`, `limite_inferior`, `limite_superior`, `porciento_residual`) VALUES ('4', '37', '48', '10')";
$sql["20161004"][]	= "CREATE TABLE IF NOT EXISTS `leasing_financiero` (  `idleasing_financiero` INT NOT NULL AUTO_INCREMENT,  `describe_financiero` VARCHAR(40) NULL,  `frecuencia` INT(4) NULL DEFAULT 30,  `limite_inferior` INT(4) NULL DEFAULT 0,  `limite_superior` INT(4) NULL DEFAULT 0,  `tasa_financiero` FLOAT(6,4) NULL DEFAULT 0,  PRIMARY KEY (`idleasing_financiero`) ) ENGINE = InnoDB COMMENT = 'Tabla de costos financieros'";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('3044', '3040', 'Costos Financieros', 'frmarrendamiento/leasing-financiero.frm.php', 'Costos y gastos Financieros por Plazo', 'fa-money', 'command', '3044', '3044', 'true')";
$sql["20161004"][]	= "INSERT INTO `leasing_financiero` (`idleasing_financiero`, `describe_financiero`, `limite_superior`, `tasa_financiero`) VALUES ('1', 'Margen Casa', '12', '65')";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (3045, 3040, 'Valor Residual', 'frmarrendamiento/leasing-residual.frm.php', 'Valor Residual', 'fa-arrows-v', 'command', '3045', '3045', 'true') ";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (3046, 3040, 'Vehiculos Paquetes GPS', 'frmarrendamiento/vehiculos-gps.frm.php', 'Vehiculos Paquetes GPS', 'fa-cart-plus', 'command', '3046', '3046', 'true'); ";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (3047, 3040, 'Costeo Paquetes GPS', 'frmarrendamiento/vehiculos-gps-costeo.frm.php', 'Costeo Paquetes GPS', 'fa-usd', 'command', '3047', '3047', 'true')";
$sql["20161004"][]	= "ALTER TABLE `vehiculos_gps_costeo` ADD COLUMN `tipo_de_gps` INT(4) NULL DEFAULT 0 AFTER `monto_gps`";
$sql["20161004"][]	= "ALTER TABLE `leasing_comisiones` ADD COLUMN `comision_ejecutivo` FLOAT(6,4) NULL DEFAULT 0  AFTER `tasa_comision`,ADD COLUMN `comision_regional` FLOAT(6,4) NULL DEFAULT 0  AFTER `comision_ejecutivo`";
$sql["20161004"][]	= "ALTER TABLE `vehiculos_gps_costeo` ADD COLUMN `frecuencia` INT(4) NULL DEFAULT 30  AFTER `tipo_de_gps`";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (3048, 3040, 'Vehiculos Usos', 'frmarrendamiento/vehiculos-usos.frm.php', 'Vehiculos Usos', 'fa-car', 'command', '3048', '3048', 'true'); ";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (3049, 3040, 'Vehiculos Pagos', 'frmarrendamiento/vehiculos-tenencia.frm.php', 'Vehiculos Pagos', 'fa-usd', 'command', '3049', '3049', 'true'); ";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (304010, 3040, 'Vehiculos Marcas', 'frmarrendamiento/vehiculos-marcas.frm.php', 'Vehiculos Marcas', 'fa-car', 'command', '304010', '304010', 'true') ";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (304011, 3040, 'Arrendamiento Comisiones', 'frmarrendamiento/leasing-comisiones.frm.php', 'Arrendamiento Comisiones', 'fa-percent', 'command', '304011', '304011', 'true')";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `segmento` INT(4) NULL DEFAULT 0 AFTER `total_credito`";
$sql["20161004"][]	= "UPDATE `general_menu` SET `menu_title` = 'Lista de Creditos' , `menu_file` = 'frmarrendamiento/lista-de-solicitudes.frm.php' , `menu_description` = 'Lista de Creditos en Proceso' , `menu_image` = 'fa-list' WHERE `idgeneral_menu` = '3041'";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `entidadfederativa` INT(4) NULL DEFAULT 0 COMMENT 'Entidad en que transita el vehiculo' AFTER `segmento`";
$sql["20161004"][]	= "ALTER TABLE  `originacion_leasing` ADD COLUMN `plazo` INT(4) NULL DEFAULT 0 AFTER `entidadfederativa`";
$sql["20161004"][]	= "UPDATE `general_menu` SET `menu_title` = 'Arrendamiento' WHERE `idgeneral_menu` = '3040'";
$sql["20161004"][]	= "ALTER TABLE  `leasing_tasas` DROP COLUMN `plazomax`,DROP COLUMN `plazomin`,ADD COLUMN `limite_inferior` INT(4) NULL DEFAULT 0  AFTER `tasa_ofrecida`,ADD COLUMN `limite_superior` INT(4) NULL DEFAULT 0  AFTER `limite_inferior`";
$sql["20161004"][]	= "ALTER TABLE `leasing_tasas` ADD COLUMN `frecuencia` INT(4) NULL DEFAULT 30  AFTER `limite_superior`";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (30412, 3040, 'TASAS', 'frmarrendamiento/leasing-tasas.frm.php', 'Tasas de Interes', 'fa-percent', 'command', '30412', '30412', 'true')  ";
$sql["20161004"][]	= "ALTER TABLE  `leasing_originadores` ADD COLUMN `meta` DOUBLE(18,2) NULL DEFAULT 0  AFTER `comision`, ADD COLUMN `frecuencia_meta` INT(4) NULL DEFAULT 0  AFTER `meta`";
$sql["20161004"][]	= "ALTER TABLE `leasing_comisiones` ADD COLUMN `bono` DOUBLE(18,2) NULL DEFAULT 0 AFTER `comision_regional`";
$sql["20161004"][]	= "CREATE TABLE IF NOT EXISTS `leasing_tipo_comision` (  `idleasing_tipo_comision` INT NOT NULL AUTO_INCREMENT ,  `nombre_tipo_comision` VARCHAR(40) NULL ,  PRIMARY KEY (`idleasing_tipo_comision`)  ) ENGINE = InnoDB";
$sql["20161004"][]	= "UPDATE `general_menu` SET `idgeneral_menu` = '30401', `menu_order` = '30401' , `menu_help_id` = '30401' WHERE `idgeneral_menu` = '304010'";
$sql["20161004"][]	= "UPDATE `general_menu` SET `idgeneral_menu` = '30402',`menu_order` = '30402' , `menu_help_id` = '30402' WHERE `idgeneral_menu` = '304011'";
$sql["20161004"][]	= "INSERT INTO `leasing_tipo_comision` (`idleasing_tipo_comision`, `nombre_tipo_comision`) VALUES ('1', 'FIJA')";
$sql["20161004"][]	= "ALTER TABLE `t_03f996214fba4a1d05a68b18fece8e71` CHANGE COLUMN `f_28fb96d57b21090705cfdf8bc3445d2a` `f_28fb96d57b21090705cfdf8bc3445d2a` VARCHAR(62) NOT NULL  ,CHANGE COLUMN `f_34023acbff254d34664f94c3e08d836e` `f_34023acbff254d34664f94c3e08d836e` VARCHAR(62) NOT NULL DEFAULT ''  ,CHANGE COLUMN `nombres` `nombres` VARCHAR(40) NOT NULL DEFAULT ''  ,CHANGE COLUMN `apellidopaterno` `apellidopaterno` VARCHAR(40) NULL DEFAULT NULL  ,CHANGE COLUMN `apellidomaterno` `apellidomaterno` VARCHAR(40) NULL DEFAULT NULL  ,CHANGE COLUMN `puesto` `puesto` VARCHAR(40) NOT NULL DEFAULT 'NOTVALID'  ,CHANGE COLUMN `f_f2cd801e90b78ef4dc673a4659c1482d` `f_f2cd801e90b78ef4dc673a4659c1482d` INT(4) UNSIGNED NOT NULL DEFAULT '1',CHANGE COLUMN `sucursal` `sucursal` VARCHAR(15) NULL DEFAULT 'matriz'  , CHANGE COLUMN `cuenta_contable_de_caja` `cuenta_contable_de_caja` VARCHAR(25) NULL DEFAULT 'CUENTA_DE_CUADRE'";
$sql["20161004"][]	= "UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET `f_34023acbff254d34664f94c3e08d836e`=SHA1(UNHEX(SHA1(`f_34023acbff254d34664f94c3e08d836e`))) WHERE LENGTH(`f_34023acbff254d34664f94c3e08d836e`) < 40";
$sql["20161004"][]	= "ALTER TABLE `general_niveles` CHANGE COLUMN `idgeneral_niveles` `idgeneral_niveles` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT  ,CHANGE COLUMN `work_time_range` `work_time_range` VARCHAR(10) NULL DEFAULT NULL COMMENT '8-16' ,ADD COLUMN `initpage` VARCHAR(25) NULL DEFAULT 'index.xul.php'  AFTER `rules_by_user`";
$sql["20161004"][]	= "ALTER TABLE `t_03f996214fba4a1d05a68b18fece8e71` ADD INDEX `consex` (`idusuarios` ASC, `codigo_de_persona` ASC, `f_f2cd801e90b78ef4dc673a4659c1482d` ASC, `estatus` ASC) ";
$sql["20161004"][]	= "ALTER TABLE `tesoreria_cajas` DROP INDEX `porusuario` ,ADD INDEX `porusuario` (`idusuario` ASC, `idtesoreria_cajas` ASC, `estatus` ASC)";
$sql["20161004"][]	= "ALTER TABLE `operaciones_recibos` ADD INDEX `aml1` (`numero_socio` ASC, `docto_afectado` ASC, `clave_de_moneda` ASC,  `tipo_pago` ASC, `idoperaciones_recibos` ASC)";
$sql["20161004"][]	= "UPDATE `general_menu` SET `menu_title` = 'Perfiles de Riesgo Extra' , `menu_destination` = 'principal' , `menu_description` = 'Perfiles personalizados' , `menu_image` = 'fa-cogs' WHERE `idgeneral_menu` = '72202'";
$sql["20161004"][]	= "ALTER TABLE `aml_riesgo_perfiles` CHANGE COLUMN `idaml_riesgo_perfiles` `idaml_riesgo_perfiles` INT(11) NOT NULL AUTO_INCREMENT";
$sql["20161004"][]	= "UPDATE `general_menu` SET `menu_parent` = '1050' , `menu_description` = 'Configurar membresias' , `menu_image` = 'fa-users' , `menu_order` = '7024' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '7024'";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (72204, 72000, 'Matriz de Riesgo', 'frmpld/matriz-de-riesgo.frm.php', 'Matriz de Riesgo', 'fa-sort-amount-asc', 'command', '72204', '72204', 'true') ";
$sql["20161004"][]	= "DROP TABLE IF EXISTS `aml_riesgo_matrices`";
$sql["20161004"][]	= "CREATE TABLE IF NOT EXISTS  `aml_riesgo_matrices` ( `idaml_riesgo_matrices` INT NOT NULL AUTO_INCREMENT , `nombre` VARCHAR(40) NULL COMMENT 'Nombre descriptivo', `clasificacion` VARCHAR(40) NULL COMMENT 'PERSONA OPERACION PRODUCTO PAIS REGION',  `descripcion` VARCHAR(100) NULL ,  `clave_riesgo` INT(10) NULL DEFAULT 0 COMMENT 'clave de riesgo que origina',  `riesgo` INT(4) NULL DEFAULT 0 ,  `define` VARCHAR(20) NULL COMMENT 'SISTEMA, USUARIO',  `estatus` INT(2) NULL COMMENT 'AML_PERSONA_MORA',  `usuario` INT(6) NULL DEFAULT 0 COMMENT 'usuario que define',  `tiempo` INT(10) NULL DEFAULT 0 COMMENT 'tiempo de actualizacion',  `finalizador` INT(2) NULL DEFAULT 0 COMMENT 'define si es un parametro final',  PRIMARY KEY (`idaml_riesgo_matrices`)  ) ENGINE = INNODB COMMENT = 'Matriz de Riesgo'";
$sql["20161004"][]	= "ALTER TABLE `aml_alerts` CHANGE COLUMN `fecha_de_origen` `fecha_de_origen` INT(11) NULL DEFAULT '0'  ,CHANGE COLUMN `fecha_de_checking` `fecha_de_checking` INT(11) NULL DEFAULT '0'  ,CHANGE COLUMN `hora_de_proceso` `hora_de_proceso` INT(11) NULL DEFAULT '0'  ,CHANGE COLUMN `medio_de_envio` `medio_de_envio` VARCHAR(20) NULL DEFAULT 'MAIL' COMMENT 'SMS MAIL SYSTEM' ,CHANGE COLUMN `usuario` `usuario` INT(8) NULL DEFAULT '1'  ,CHANGE COLUMN `sucursal` `sucursal` INT(4) NULL DEFAULT '1'  ,CHANGE COLUMN `fecha_de_registro` `fecha_de_registro` INT(11) NULL DEFAULT '0'  ,ADD COLUMN `usuario_checking` INT(8) NULL DEFAULT '0'  AFTER `resultado_de_checking`;";
$sql["20161004"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('900116', 'GENERAL_FALTA_MAIL', 'El Correo Electronico debe ser Valido')";
$sql["20161004"][]	= "UPDATE `general_menu` SET `menu_rules` = REPLACE(`menu_rules`, '@ro', '@rw')";
$sql["20161004"][]	= "ALTER TABLE `general_menu`  CHANGE `menu_rules` `menu_rules` VARCHAR(200) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw' NULL";
$sql["20161004"][]	= "ALTER TABLE `general_niveles` CHANGE COLUMN `initpage` `initpage` VARCHAR(100) NULL DEFAULT 'index.xul.php' ";
$sql["20161004"][]	= "ALTER TABLE `general_niveles` ADD COLUMN `taskspage` VARCHAR(100) NULL DEFAULT 'utils/frm_calendar_tasks.php' AFTER `initpage`";
$sql["20161004"][]	= "ALTER TABLE `seguimiento_notificaciones` DROP PRIMARY KEY, ADD PRIMARY KEY (`idseguimiento_notificaciones`)  ,ADD INDEX `bypers` (`socio_notificado` ASC, `numero_solicitud` ASC, `idseguimiento_notificaciones` ASC, `grupo_relacionado` ASC, `oficial_de_seguimiento` ASC, `estatus_notificacion` ASC, `canal_de_envio` ASC) ";
$sql["20161004"][]	= "ALTER TABLE `leasing_usuarios` ADD COLUMN `idusuario` VARCHAR(8) NULL DEFAULT 0 COMMENT 'clave de usuario asignada' AFTER `administrador`";
$sql["20161004"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('403', 'Usuario Invalido - Inactivo. Consulte al Administrador', 'security')";
$sql["20161004"][]	= "ALTER TABLE `leasing_usuarios` CHANGE COLUMN `nombre` `nombre` VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Nombre completo'";
$sql["20161004"][]	= "ALTER TABLE  `originacion_leasing` ADD UNIQUE INDEX `idoriginacion_leasing_UNIQUE` (`idoriginacion_leasing` ASC)  , DROP INDEX `tipo_leasing_UNIQUE` ";
$sql["20161004"][]	= "ALTER TABLE  `originacion_leasing` ADD COLUMN `tasa_credito` FLOAT(6,4) NULL DEFAULT '0'  AFTER `plazo`, ADD COLUMN `tasa_tiie` FLOAT(6,4) NULL DEFAULT '0'  AFTER `tasa_credito`";
$sql["20161004"][]	= "DROP TABLE IF EXISTS `tesoreria_valoracion_dolar`";
$sql["20161004"][]	= "DROP TABLE IF EXISTS `tesoreria_valoracion_udi`";
$sql["20161004"][]	= "CREATE TABLE IF NOT EXISTS `tesoreria_valoracion_diaria` (  `idcontrol` INT NOT NULL AUTO_INCREMENT ,  `denominacion` VARCHAR(10) NULL COMMENT 'Tipo de Valor UDIS CETES TIIE',  `fecha` DATE NULL ,  `valor` FLOAT(11,6) NULL ,  `tiempo` INT(11) NULL DEFAULT 0 ,  `usuario` INT(8) NULL DEFAULT 0 ,  PRIMARY KEY (`idcontrol`)  ,  INDEX `natsu` (`denominacion` ASC, `idcontrol` ASC, `usuario` ASC)  ) ENGINE = InnoDB";
$sql["20161004"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (1054, 1050, 'Valores Diarios', 'frmtesoreria/valores-diarios.frm.php', 'Valores Diarios', 'fa-line-chart', 'command', '1054', '1054', 'true') ";
$sql["20161004"][]	= "ALTER TABLE `tesoreria_monedas` CHANGE COLUMN `clave_de_moneda` `clave_de_moneda` VARCHAR(6) NOT NULL  ,CHANGE COLUMN `pais_de_origen` `pais_de_origen` VARCHAR(4) NULL DEFAULT 'MX'  ,ADD COLUMN `instrumento` INT(4) NULL DEFAULT 1 COMMENT 'Instrumento Financiero 1 Eefectivo' AFTER `pais_de_origen`, ADD COLUMN `simbolo` VARCHAR(4) NULL DEFAULT ''  AFTER `instrumento`;";
$sql["20161004"][]	= "INSERT INTO `tesoreria_monedas` (`clave_de_moneda`, `nombre_de_la_moneda`, `quivalencia_en_moneda_local`, `instrumento`, `simbolo`) VALUES ('TIIE', 'TIIE', '5.1155', '11', 'TIIE')";
$sql["20161004"][]	= "INSERT INTO `tesoreria_monedas` (`clave_de_moneda`, `nombre_de_la_moneda`, `quivalencia_en_moneda_local`, `instrumento`, `simbolo`) VALUES ('CETES', 'CETES', '4.68', '11', 'CETES')";
$sql["20161004"][]	= "INSERT INTO `tesoreria_monedas` (`clave_de_moneda`, `nombre_de_la_moneda`, `quivalencia_en_moneda_local`, `instrumento`, `simbolo`) VALUES ('UDIS', 'UDIS', '5.510594', '11', 'UDIS')";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_gps` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `tasa_tiie`";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_directo` DOUBLE(18,2) NULL DEFAULT '0' COMMENT 'Monto no financiado' AFTER `monto_gps`";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_seguro` DOUBLE(18,2) NULL DEFAULT '0' AFTER `monto_directo`";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_placas` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_seguro`,ADD COLUMN `monto_gestoria` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_placas`,ADD COLUMN `monto_notario` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_gestoria`";
$sql["20161004"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_residual` DOUBLE(18,2) NULL DEFAULT '0'AFTER `monto_notario` ";
$sql["20161004"][]	= "ALTER TABLE  `socios_otros_parametros` ADD INDEX `idpersona` (`clave_de_persona` ASC, `clave_del_parametro` ASC, `idsocios_otros_parametros` ASC, `idusuario` ASC)";

$sql["20161101"][]	= "CREATE TABLE IF NOT EXISTS `sistema_eliminados` ( `idsistema_eliminados` INT NOT NULL AUTO_INCREMENT ,  `tipoobjeto` INT(4) NULL COMMENT '400 recibo etc',  `contenido` LONGTEXT NULL ,  PRIMARY KEY (`idsistema_eliminados`)  ,  INDEX `idxmm` (`idsistema_eliminados` ASC, `tipoobjeto` ASC)  ) ENGINE = InnoDB";
$sql["20161101"][]	= "ALTER TABLE `sistema_eliminados` CHANGE COLUMN `tipoobjeto` `tipoobjeto` INT(4) NULL DEFAULT 0 COMMENT '400 recibo etc' ,ADD COLUMN `idusuario` INT(6) NULL DEFAULT 0  AFTER `contenido`,ADD COLUMN `tiempo` INT(11) NULL DEFAULT 0  AFTER `idusuario`";
$sql["20161101"][]	= "ALTER TABLE `captacion_tasas` CHANGE COLUMN `tasa_efectiva` `tasa_efectiva` FLOAT(6,4) NULL DEFAULT '0.000'  ,CHANGE COLUMN `modalidad_cuenta` `modalidad_cuenta` INT(4) UNSIGNED NULL DEFAULT '0'  ,CHANGE COLUMN `monto_mayor_a` `monto_mayor_a` DOUBLE(18,2) NULL DEFAULT '0.00'  ,CHANGE COLUMN `monto_menor_a` `monto_menor_a` DOUBLE(18,2) NULL DEFAULT '0.00'  ,CHANGE COLUMN `subproducto` `subproducto` INT(6) NULL DEFAULT '0' COMMENT '0 = general' ,ADD INDEX `IDXTASAS` (`modalidad_cuenta` ASC, `subproducto` ASC, `idcaptacion_tasas` ASC)";

$sql["20161102"][]	= "ALTER TABLE `captacion_subproductos` CHANGE COLUMN `algoritmo_modificador_del_interes` `algoritmo_modificador_del_interes` TEXT NULL DEFAULT ''  ,ADD COLUMN `estatus` INT(2) NOT NULL DEFAULT 1 COMMENT '1 activo' AFTER `algoritmo_modificador_del_interes`,ADD INDEX `icd` (`tipo_de_cuenta` ASC, `idcaptacion_subproductos` ASC, `destino_del_interes` ASC, `metodo_de_abono_de_interes` ASC, `estatus` ASC)";
$sql["20161102"][]	= "ALTER TABLE `captacion_subproductos` CHANGE COLUMN `idcaptacion_subproductos` `idcaptacion_subproductos` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT";

$sql["20161103"][]	= "INSERT INTO  `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/alertas-dictaminadas.rpt.php?', 'AL1.- Alertas Dictaminadas', 'aml', '5112', '', '1')";
$sql["20161103"][]	= "INSERT INTO  `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptpld/alertas-todas.rpt.php?', 'AL1.- Reporte de Alertas', 'aml', '5113', '', '2')";
$sql["20161103"][]	= "ALTER TABLE `socios_aeconomica_dependencias` CHANGE COLUMN `fecha_de_envio` `fecha_de_envio` DATE NULL DEFAULT '0000-00-00'  ,CHANGE COLUMN `oficial_que_cierra` `oficial_que_cierra` INT(8) NULL DEFAULT '0'  ,CHANGE COLUMN `comision_por_encargo` `comision_por_encargo` FLOAT(6,3) NULL DEFAULT '0.000000' COMMENT 'Porcentaje de la comision de cobrar por los creditos u operaciones ' ,ADD COLUMN `tasa_preferente` FLOAT(6,3) NULL DEFAULT '0'  AFTER `comision_por_encargo`";
$sql["20161103"][]	= "ALTER TABLE `socios_aeconomica_dependencias` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' COMMENT '1 activo' AFTER `tasa_preferente`";
$sql["20161103"][]	= "ALTER TABLE `socios_vivienda` DROP PRIMARY KEY,ADD PRIMARY KEY (`idsocios_vivienda`)  ,ADD INDEX `idx` (`socio_numero` ASC, `codigo_postal` ASC, `idsocios_vivienda` ASC, `tipo_domicilio` ASC, `tipo_regimen` ASC, `clave_de_localidad` ASC, `clave_de_pais` ASC, `estado_actual` ASC, `clave_de_municipio` ASC, `clave_de_entidadfederativa` ASC, `principal` ASC, `idusuario` ASC, `oficial_de_verificacion` ASC)";

$sql["20161104"][]	= "ALTER TABLE `operaciones_recibos` DROP PRIMARY KEY,ADD PRIMARY KEY (`idoperaciones_recibos`)  ,DROP INDEX `aml1` ,ADD INDEX `aml1` (`numero_socio` ASC, `docto_afectado` ASC, `clave_de_moneda` ASC, `tipo_pago` ASC, `idoperaciones_recibos` ASC, `origen_aml` ASC, `tipo_docto` ASC, `persona_asociada` ASC, `idusuario` ASC, `periodo_de_documento` ASC, `cuenta_bancaria` ASC, `grupo_asociado` ASC)";
$sql["20161104"][]	= "ALTER TABLE `sistema_lenguaje` ADD INDEX `iddx` (`equivalente` ASC, `idsistema_lenguaje` ASC, `idioma` ASC)";
$sql["20161104"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `cuota_vehiculo` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_residual`,ADD COLUMN `cuota_aliado` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_vehiculo`,ADD COLUMN `cuota_accesorios` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_aliado`,ADD COLUMN `cuota_tenencia` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_accesorios`,ADD COLUMN `cuota_mtto` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_tenencia`,ADD COLUMN `cuota_gps` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_mtto`,ADD COLUMN `cuota_seguro` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_gps`,ADD COLUMN `monto_comision` DOUBLE(18,2) NULL DEFAULT '0' COMMENT 'Monto de comision de apertura' AFTER `cuota_seguro`,ADD COLUMN `monto_originador` DOUBLE(18,2) NULL DEFAULT '0' COMMENT 'Monto pagado al Originador' AFTER `monto_comision`";


$sql["20161105"][]	= "CREATE TABLE IF NOT EXISTS `creditos_datos_originacion` (  `idcreditos_datos_originacion` INT NOT NULL AUTO_INCREMENT ,  `credito` BIGINT(25) NOT NULL DEFAULT 0 ,  `tipo_originacion` INT(6) NULL DEFAULT 1 COMMENT '1 por defecto',  `clave_vinculada` BIGINT(25) NULL DEFAULT 0 ,  `tiempo` INT(11) NULL DEFAULT 0 ,  `idusuario` INT(8) NULL DEFAULT 0 ,  PRIMARY KEY (`idcreditos_datos_originacion`)  ,  INDEX `idc` (`credito` ASC, `tipo_originacion` ASC, `clave_vinculada` ASC, `idcreditos_datos_originacion` ASC)  ) ENGINE = InnoDB";

$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD UNIQUE INDEX `idoriginacion_leasing_UNIQUE` (`idoriginacion_leasing` ASC)  , DROP INDEX `tipo_leasing_UNIQUE` ";
$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `tasa_credito` FLOAT(6,4) NULL DEFAULT '0'  AFTER `plazo`, ADD COLUMN `tasa_tiie` FLOAT(6,4) NULL DEFAULT '0'  AFTER `tasa_credito`";
$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_gps` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `tasa_tiie`";
$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_directo` DOUBLE(18,2) NULL DEFAULT '0' COMMENT 'Monto no financiado' AFTER `monto_gps`";
$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_seguro` DOUBLE(18,2) NULL DEFAULT '0' AFTER `monto_directo`";
$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_placas` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_seguro`,ADD COLUMN `monto_gestoria` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_placas`,ADD COLUMN `monto_notario` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_gestoria`";
$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `monto_residual` DOUBLE(18,2) NULL DEFAULT '0'AFTER `monto_notario` ";
$sql["20161106"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `cuota_vehiculo` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `monto_residual`,ADD COLUMN `cuota_aliado` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_vehiculo`,ADD COLUMN `cuota_accesorios` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_aliado`,ADD COLUMN `cuota_tenencia` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_accesorios`,ADD COLUMN `cuota_mtto` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_tenencia`,ADD COLUMN `cuota_gps` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_mtto`,ADD COLUMN `cuota_seguro` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `cuota_gps`,ADD COLUMN `monto_comision` DOUBLE(18,2) NULL DEFAULT '0' COMMENT 'Monto de comision de apertura' AFTER `cuota_seguro`,ADD COLUMN `monto_originador` DOUBLE(18,2) NULL DEFAULT '0' COMMENT 'Monto pagado al Originador' AFTER `monto_comision`";

$sql["20161107"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `cuota_garantia` DOUBLE(18,2) NULL DEFAULT '0' COMMENT '' AFTER `monto_originador`";
$sql["20161107"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `es_moral` INT(2) NULL DEFAULT '0' COMMENT 'Es persona moral' AFTER `cuota_garantia`";
$sql["20161107"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' AFTER `es_moral`";

$sql["20161107"][]	= "insert into `creditos_tipoconvenio` (`idcreditos_tipoconvenio`, `descripcion_tipoconvenio`, `tasa_ahorro`, `tipo_convenio`, `razon_garantia`, `creditos_mayores_a`, `porciento_garantia_liquida`, `monto_fondo_obligatorio`, `porcentaje_otro_credito`, `aplica_gastos_notariales`, `numero_creditos_maximo`, `dias_maximo`, `pagos_maximo`, `tipo_autorizacion`, `nivel_riesgo`, `porcentaje_ica`, `estatus_predeterminado`, `leyenda_docto_autorizacion`, `interes_normal`, `interes_moratorio`, `tolerancia_dias_no_pago`, `maximo_otorgable`, `tolerancia_dias_primer_abono`, `numero_avales`, `nivel_autorizacion_oficial`, `code_valoracion_javascript`, `minimo_otorgable`, `descripcion_completa`, `oficial_seguimiento`, `valoracion_php`, `tipo_de_credito`, `php_monto_maximo`, `tipo_de_convenio`, `tipo_de_garantia`, `estatus`, `tasa_iva`, `contable_cartera_vigente`, `contable_cartera_vencida`, `contable_intereses_devengados`, `contable_intereses_anticipados`, `contable_intereses_cobrados`, `contable_intereses_moratorios`, `iva_incluido`, `comision_por_apertura`, `codigo_de_contrato`, `contable_cartera_castigada`, `path_del_contrato`, `tipo_de_integracion`, `contable_intereses_vencidos`, `base_de_calculo_de_interes`, `capital_vencido_renovado`, `capital_vencido_reestructurado`, `capital_vencido_normal`, `capital_vigente_renovado`, `capital_vigente_reestructurado`, `capital_vigente_normal`, `interes_cobrado`, `moratorio_cobrado`, `interes_vencido_renovado`, `interes_vencido_reestructurado`, `interes_vencido_normal`, `interes_vigente_renovado`, `interes_vigente_reestructurado`, `interes_vigente_normal`, `tipo_de_interes`, `aplica_mora_por_cobranza`, `pre_modificador_de_interes`, `pos_modificador_de_interes`, `pre_modificador_de_ministracion`, `pre_modificador_de_autorizacion`, `pre_modificador_de_vencimiento`, `pre_modificador_de_solicitud`, `clave_de_tipo_de_producto`, `perfil_de_interes`, `fuente_de_fondeo_predeterminado`, `tipo_de_periocidad_preferente`, `numero_de_pagos_preferente`, `tipo_en_sistema`, `omitir_seguimiento`, `nombre_corto`) values('500','ARRENDAMIENTO PURO','0.00000','2021','0.00000','0.00','0.00000','0.0000','0.00000','0','9','1900','160','1','1','0.00000','99','','0.48000','0.48000','45','10000000.00','18','0','6','','1000.00','ARRENDAMIENTO','99','','1','\$monto_maximo = \$producto_monto_maximo;','1','todas','activo','0.16000','00','145005','00','CUENTA_DE_CUADRE','510410','510450','0','0.00000','0','510410','../rpt_formatos/rptcontratocredito.php?solicitud=','1','510410','2','141005','141805','145005','141005','141805','140205','510410','510450','510410','510410','510410','510410','510410','510410','0','1','','','','\$TASA_MORATORIO=\$TASA_INTERES*2;','','','PP','99','1','30','8','200','0','LEASING PURO')";

$sql["20161109"][]	= "ALTER TABLE `socios_general` CHANGE COLUMN `dependientes_economicos` `dependientes_economicos` INT(4) NULL DEFAULT '0',CHANGE COLUMN `pais_de_origen` `pais_de_origen` VARCHAR(4) NULL DEFAULT 'MX',ADD COLUMN `idinterna` VARCHAR(20) NULL DEFAULT ''  AFTER `sitioweb`";

$sql["20161110"][]	= "ALTER TABLE `creditos_estatus` CHANGE COLUMN `descripcion_estatus` `descripcion_estatus` VARCHAR(30) NOT NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `estatus_actual` `estatus_actual` INT(4) NULL DEFAULT '0' COMMENT 'DEP' ,CHANGE COLUMN `titulo_general` `titulo_general` VARCHAR(40) NULL DEFAULT '' COMMENT '' ,CHANGE COLUMN `orden_clasificacion` `orden_clasificacion` INT(4) NULL DEFAULT 0 COMMENT '' ,CHANGE COLUMN `respetar_plan_de_pagos` `respetar_plan_de_pagos` ENUM('0','1') NULL DEFAULT '1' COMMENT '' ,ADD COLUMN `tit_solicitados` VARCHAR(30) NULL COMMENT '' AFTER `respetar_plan_de_pagos`,ADD COLUMN `tit_autorizados` VARCHAR(30) NULL COMMENT '' AFTER `tit_solicitados`";

$sql["20161111"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('565', 'FORM', 'PERSONAS.BUSQUEDA.ID_INTERNA', '', '', '\$valor=false;', '')";


$sql["20161112"][]	= "UPDATE `entidad_configuracion` SET `tipo` = 'colocacion' WHERE `nombre_del_parametro` = 'dias_maximo_de_mora_a_calcular'";
$sql["20161112"][]	= "UPDATE `general_structure` SET `control`='hidden' WHERE `tabla`='operaciones_mvtos' AND `campo`='afectacion_contable'";
$sql["20161112"][]	= "UPDATE `general_structure` SET `control`='hidden' WHERE `tabla`='operaciones_mvtos' AND `campo`='afectacion_cobranza'";
$sql["20161112"][]	= "UPDATE `general_structure` SET `titulo`='Cancelado con' WHERE `tabla`='operaciones_mvtos' AND `campo`='docto_neutralizador'";
$sql["20161112"][]	= "UPDATE `general_structure` SET `titulo`='Valor Estadistico' WHERE `tabla`='operaciones_mvtos' AND `campo`='afectacion_estadistica'";
$sql["20161112"][]	= "UPDATE `general_structure` SET `titulo`='Naturaleza' WHERE `tabla`='operaciones_mvtos' AND `campo`='valor_afectacion'";
$sql["20161112"][]	= "UPDATE `general_structure` SET `order_index`='999' WHERE `tabla`='operaciones_mvtos' AND `campo`='valor_afectacion'";
$sql["20161112"][]	= "UPDATE `general_structure` SET `titulo`='Monto' WHERE `tabla`='operaciones_mvtos' AND `campo`='afectacion_real'";

$sql["20161113"][]	= "ALTER TABLE `aml_riesgo_matrices` ADD COLUMN `ocurrencia` INT(4) NULL DEFAULT '1' COMMENT 'Probabibilidad de que ocurra' AFTER `finalizador`";

$sql["20161120"][]	= "ALTER TABLE `aml_riesgo_matrices` ADD COLUMN `impacto` FLOAT(8,4) NULL DEFAULT '1' COMMENT 'Valor del Impacto' AFTER `ocurrencia`";
$sql["20161120"][]	= "CREATE TABLE IF NOT EXISTS`riesgos_probabilidad` (  `idriesgos_probabilidad` INT(4) ZEROFILL NOT NULL AUTO_INCREMENT ,  `nombre_probabilidad` VARCHAR(20) NULL ,  `multiplo` FLOAT(6,4) NULL DEFAULT 1 COMMENT 'Multiplicador de impacto',  PRIMARY KEY (`idriesgos_probabilidad`)  ) ENGINE = InnoDB";
$sql["20161120"][]	= "INSERT INTO `riesgos_probabilidad` (`idriesgos_probabilidad`, `nombre_probabilidad`) VALUES ('1', 'Casi Certeza')";
$sql["20161120"][]	= "INSERT INTO `riesgos_probabilidad` (`idriesgos_probabilidad`, `nombre_probabilidad`) VALUES ('2', 'Probable')";
$sql["20161120"][]	= "INSERT INTO `riesgos_probabilidad` (`idriesgos_probabilidad`, `nombre_probabilidad`) VALUES ('3', 'Moderado')";
$sql["20161120"][]	= "INSERT INTO `riesgos_probabilidad` (`idriesgos_probabilidad`, `nombre_probabilidad`, `multiplo`) VALUES ('4', 'Improbable', '1.25')";
$sql["20161120"][]	= "INSERT INTO `riesgos_probabilidad` (`idriesgos_probabilidad`, `nombre_probabilidad`, `multiplo`) VALUES ('5', 'Raro', '1.5')";
$sql["20161120"][]	= "CREATE TABLE IF NOT EXISTS `riesgos_consecuencias` (`idriesgos_consecuencias` INT NOT NULL AUTO_INCREMENT , `nombre_consecuencia` VARCHAR(40) NULL ,  `multiplo` FLOAT(6,4) NULL DEFAULT '1' ,  PRIMARY KEY (`idriesgos_consecuencias`)  ) ENGINE = InnoDB";
$sql["20161120"][]	= "INSERT INTO `riesgos_consecuencias` (`idriesgos_consecuencias`, `nombre_consecuencia`, `multiplo`) VALUES ('1', 'Insignificantes', '0.25') ";
$sql["20161120"][]	= "INSERT INTO `riesgos_consecuencias` (`idriesgos_consecuencias`, `nombre_consecuencia`, `multiplo`) VALUES ('2', 'Menores', '0.5')";
$sql["20161120"][]	= "INSERT INTO `riesgos_consecuencias` (`idriesgos_consecuencias`, `nombre_consecuencia`, `multiplo`) VALUES ('3', 'Moderadas', '1') ";
$sql["20161120"][]	= "INSERT INTO `riesgos_consecuencias` (`idriesgos_consecuencias`, `nombre_consecuencia`, `multiplo`) VALUES ('4', 'Mayores', '1.5') ";
$sql["20161120"][]	= "INSERT INTO `riesgos_consecuencias` (`idriesgos_consecuencias`, `nombre_consecuencia`, `multiplo`) VALUES ('5', 'Catastroficas', '1.75')";
$sql["20161120"][]	= "ALTER TABLE  `aml_riesgo_matrices` CHANGE COLUMN `ocurrencia` `probabilidad` INT(4) NULL DEFAULT '1' COMMENT 'Probabibilidad de que ocurra' ,ADD COLUMN `consecuencia` INT(4) NULL DEFAULT '1' COMMENT 'nivel de consecuencias' AFTER `impacto`";
$sql["20161120"][]	= "INSERT INTO `aml_risk_catalog` (`clave_de_control`, `descripcion`, `tipo_de_riesgo`, `valor_ponderado`, `unidades_ponderadas`, `unidad_de_medida`, `fundamento_legal`) VALUES ('801010', 'Personas. Inscripcion de Personas', '903', '1', '1', 'EVENTO', '')";
$sql["20161120"][]	= "ALTER TABLE `aml_riesgo_matrices` ADD INDEX `idx` (`clave_riesgo` ASC, `probabilidad` ASC, `consecuencia` ASC, `clasificacion` ASC, `idaml_riesgo_matrices` ASC)";
$sql["20161120"][]	= "INSERT INTO `aml_risk_catalog` (`clave_de_control`, `descripcion`, `tipo_de_riesgo`, `valor_ponderado`, `unidades_ponderadas`, `unidad_de_medida`, `frecuencia_de_chequeo`, `fundamento_legal`) VALUES ('801011', 'Personas. Nacionalidad/Residencia en Paises Riesgosos', '903', '1', '1', 'EVENTO', 'I', '')";
$sql["20161120"][]	= "CREATE TABLE IF NOT EXISTS  `riesgos_chequeo` ( `idriesgos_chequeo` INT NOT NULL AUTO_INCREMENT ,  `nombre_chequeo` VARCHAR(20) NULL ,  `eq_aml` VARCHAR(3) NULL DEFAULT '' ,  PRIMARY KEY (`idriesgos_chequeo`)  ,  INDEX `idx1` (`eq_aml` ASC, `idriesgos_chequeo` ASC)  ) ENGINE = InnoDB";
$sql["20161120"][]	= "CREATE TABLE IF NOT EXISTS  `riesgos_medidas` (  `idriesgos_medidas` INT NOT NULL AUTO_INCREMENT ,  `nombre_medida` VARCHAR(20) NULL ,  `eq_aml` VARCHAR(3) NULL DEFAULT '' ,  PRIMARY KEY (`idriesgos_medidas`)  ,  INDEX `idx1` (`idriesgos_medidas` ASC, `eq_aml` ASC)  ) ENGINE = InnoDB";
$sql["20161120"][]	= "ALTER TABLE `riesgos_medidas` CHANGE `eq_aml` `eq_aml` VARCHAR(10) CHARSET utf8 COLLATE utf8_general_ci DEFAULT '' NULL";

$sql["20161120"][]	= "INSERT INTO `riesgos_chequeo` (`idriesgos_chequeo`, `nombre_chequeo`, `eq_aml`) VALUES ('1', 'Calificado', 'C')";
$sql["20161120"][]	= "INSERT INTO `riesgos_chequeo` (`idriesgos_chequeo`, `nombre_chequeo`, `eq_aml`) VALUES ('2', 'Inmediato', 'I')";
$sql["20161120"][]	= "INSERT INTO `riesgos_chequeo` (`idriesgos_chequeo`, `nombre_chequeo`, `eq_aml`) VALUES ('3', 'Diario', 'D')";
$sql["20161120"][]	= "INSERT INTO `riesgos_medidas` (`idriesgos_medidas`, `nombre_medida`, `eq_aml`) VALUES ('2', 'Moneda Local', 'MXN')";
$sql["20161120"][]	= "INSERT INTO `riesgos_medidas` (`idriesgos_medidas`, `nombre_medida`, `eq_aml`) VALUES ('3', 'Dolares Americanos', 'USD')";
$sql["20161120"][]	= "INSERT INTO `riesgos_medidas` (`idriesgos_medidas`, `nombre_medida`, `eq_aml`) VALUES ('1', 'Por Evento', 'EVENTO')";

$sql["20161120"][]	= "ALTER TABLE `riesgos_probabilidad` CHANGE COLUMN `idriesgos_probabilidad` `idriesgos_probabilidad` INT UNSIGNED NOT NULL AUTO_INCREMENT";
$sql["20161120"][]	= "CREATE TABLE IF NOT EXISTS `riesgos_reporte` (  `idriesgos_reporte` INT NOT NULL AUTO_INCREMENT ,  `nombre_reporte` VARCHAR(20) NULL ,  `eq_aml` VARCHAR(3) NULL DEFAULT '' ,  PRIMARY KEY (`idriesgos_reporte`)  ,  INDEX `idx` (`eq_aml` ASC, `idriesgos_reporte` ASC)  ) ENGINE = INNODB";
$sql["20161120"][]	= "INSERT INTO `riesgos_reporte` (`idriesgos_reporte`, `nombre_reporte`, `eq_aml`) VALUES ('1', 'Directo', 'I')";
$sql["20161120"][]	= "INSERT INTO `riesgos_reporte` (`idriesgos_reporte`, `nombre_reporte`, `eq_aml`) VALUES ('2', 'Validado', 'C')";
$sql["20161120"][]	= "INSERT INTO `entidad_niveles_de_riesgo` (`clave_de_nivel`, `nombre_del_nivel`) VALUES ('1', 'MUY BAJO')";
$sql["20161120"][]	= "INSERT INTO `entidad_niveles_de_riesgo` (`clave_de_nivel`, `nombre_del_nivel`) VALUES ('25', 'MEDIO BAJO')";
$sql["20161120"][]	= "INSERT INTO `entidad_niveles_de_riesgo` (`clave_de_nivel`, `nombre_del_nivel`) VALUES ('75', 'MEDIO ALTO')";
$sql["20161120"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (70052, 7000, 'Lista Negra Interna', 'frmpld/lista-negra-interna.frm.php', 'Lista Negra Interna', 'fa-ban', 'command', '70052', '70052', 'true') ";
$sql["20161120"][]	= "ALTER TABLE `personas_actividad_economica_tipos` ADD COLUMN `scian` VARCHAR(20) NULL DEFAULT '' COMMENT '' AFTER `califica_para_pep`";
$sql["20161120"][]	= "UPDATE `creditos_tipo_de_calculo_de_interes` SET `descripcion_del_tipo_de_calculo_de_interes` = 'SALDO ORIGINAL' WHERE `idcreditos_tipo_de_calculo_de_interes` = '1'";
$sql["20161120"][]	= "UPDATE `creditos_tipo_de_calculo_de_interes` SET `descripcion_del_tipo_de_calculo_de_interes` = 'SALDO INSOLUTO' WHERE `idcreditos_tipo_de_calculo_de_interes` = '2'";

$sql["20161120"][]	= "CREATE TABLE IF NOT EXISTS  `personas_pagos_perfil` (`idpersonas_pagos_perfil` INT NOT NULL AUTO_INCREMENT,  `clave_de_persona` BIGINT(25) NULL DEFAULT 0,  `tipo_de_operacion` INT(6) NULL DEFAULT 0,  `periocidad` INT(4) NULL DEFAULT 30,  `monto` DOUBLE(12,2) NULL DEFAULT 0,  `prioridad` INT(2) NULL DEFAULT 0,  `rotacion` VARCHAR(20) NULL DEFAULT '', `fecha_de_aplicacion` DATE NULL DEFAULT '2015-01-01' COMMENT 'fecha en que surten efectos los cambios',  PRIMARY KEY (`idpersonas_pagos_perfil`)) ENGINE = InnoDB COMMENT = 'perfil de pagos y aportaciones inicial por tipo de ingreso'";

$sql["20161120"][]	= "ALTER TABLE `personas_pagos_perfil` ADD COLUMN `membresia` INT(4) NULL DEFAULT '0' COMMENT '' AFTER `fecha_de_aplicacion`, ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' COMMENT '' AFTER `membresia`";
$sql["20161120"][]	= "ALTER TABLE `personas_pagos_perfil` ADD COLUMN `finalizador` INT(2) NULL DEFAULT '0' AFTER `estatus`";
$sql["20161120"][]	= "ALTER TABLE `captacion_cuentas` ADD INDEX `nnew` (`numero_cuenta` ASC, `numero_socio` ASC, `tipo_cuenta` ASC, `tipo_subproducto` ASC, `estatus_cuenta` ASC, `numero_grupo` ASC, `numero_solicitud` ASC, `cuenta_de_intereses` ASC, `origen_cuenta` ASC, `tipo_titulo` ASC)  COMMENT ''";
$sql["20161120"][]	= "ALTER TABLE `captacion_cuentas` DROP PRIMARY KEY,ADD PRIMARY KEY (`numero_cuenta`)";
$sql["20161120"][]	= "ALTER TABLE `captacion_cuentas` ADD INDEX `idf` (`numero_socio` ASC, `tipo_cuenta` ASC, `tipo_subproducto` ASC, `numero_cuenta` ASC)";
$sql["20161120"][]	= "ALTER TABLE `general_folios` DROP PRIMARY KEY,ADD PRIMARY KEY (`idgeneral_folios`)  ,ADD INDEX `idxp1` (`numerorecibo` ASC, `idgeneral_folios` ASC)  ";
$sql["20161120"][]	= "ALTER TABLE `contable_polizas_proforma` DROP PRIMARY KEY, ADD PRIMARY KEY (`idcontable_polizas_proforma`)  ,ADD INDEX `idcx` (`numero_de_recibo` ASC, `tipo_de_mvto` ASC, `socio` ASC, `documento` ASC, `idcontable_polizas_proforma` ASC, `idusuario` ASC, `banco` ASC, `cuenta_alternativa` ASC)";
$sql["20161120"][]	= "ALTER TABLE `creditos_productos_costos` ADD INDEX `idc` (`clave_de_producto` ASC, `clave_de_operacion`ASC, `idcreditos_productos_costos` ASC)";

$sql["20161201"][]	= "DELETE FROM `general_structure` WHERE `tabla`='originacion_leasing'";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','idoriginacion_leasing','primary_key','int','11','','Clave','text','','derecha','0',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','fecha_origen','0000-00-00','date','0','','Fecha','text','','derecha','1',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','persona','0','bigint','20',NULL,'Persona','text',NULL,'derecha','2',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','credito','0','bigint','20',NULL,'Credito','text',NULL,'derecha','3',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','marca','1','int','4',NULL,'Marca','text',NULL,'derecha','4',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','modelo','','varchar','100','','Modelo','text','','derecha','5',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','annio','','varchar','6','','AÃ±o','text','','derecha','6',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tipo_leasing','1','int','2','','Tipo de Leasing','text','','derecha','7',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tipo_uso','0','int','4','','Tipo de Uso','text','','derecha','8',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tipo_rac','0','int','4','','Tipo de Rac','text','','derecha','9',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tipo_gps','1','int','4','','Tipo de GPS','text','','derecha','10',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','originador','0','int','8',NULL,'Originador','text',NULL,'derecha','11',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','suboriginador','0','int','8',NULL,'Suboriginador','text',NULL,'derecha','12',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','precio_vehiculo','0.00','double','37','','Precio Vehiculo','text','','derecha','13',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_aliado','0.00','double','37','','Monto Aliado','text','','derecha','14',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_accesorios','0.00','double','37','','Accesorios','text','','derecha','15',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_anticipo','0.00','double','37','','Anticipo','text','','derecha','16',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_tenencia','','double','37','','Tenencia','text','','derecha','17',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_garantia','0.00','double','37','','Garantia','text','','derecha','18',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_mtto','0.00','double','37','','Mantenimiento','text','','derecha','19',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','comision_originador','0.0000','float','13','','Tasa Comision Originador','text','','derecha','20',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','comision_apertura','0.0000','float','13','','Tasa Comision Apertura','text','','derecha','21',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tasa_iva','0.0000','float','13','','Tasa de IVA','text','','derecha','22',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tasa_compra','0.0000','float','13','','Tasa de Compra','text','','derecha','23',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','financia_seguro','0','int','2','','Seguro Financiado','text','','derecha','24',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','financia_tenencia','0','int','2','','Tenencia Financiada','text','','derecha','25',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','domicilia','0','int','2',NULL,'Domicilia','text',NULL,'derecha','26',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','paso_proceso','0','int','4','','Proceso Actual','hidden','','derecha','27',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','describe_aliado','','varchar','150',NULL,'Describe aliado','textarea',NULL,'derecha','28',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','usuario','0','int','8','','Usuario','hidden','','derecha','29',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','nombre_cliente','','varchar','150','','Nombre cliente','text','','derecha','30',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','nombre_atn','','varchar','150','','Nombre atn','text','','derecha','31',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','oficial','0','int','8',NULL,'Oficial','text',NULL,'derecha','32',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','total_credito','0.00','double','37',NULL,'Total credito','text',NULL,'derecha','33',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','segmento','0','int','4',NULL,'Segmento','text',NULL,'derecha','34',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','entidadfederativa','0','int','4',NULL,'Entidadfederativa','text',NULL,'derecha','35',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','plazo','0','int','4',NULL,'Plazo','text',NULL,'derecha','36',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tasa_credito','0.0000','float','13','','Tasa de Financiamiento','text','','derecha','37',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','tasa_tiie','0.0000','float','13','','Tasa de TIIE','text','','derecha','38',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_gps','0.00','double','37',NULL,'Monto gps','text',NULL,'derecha','39',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_directo','0.00','double','37','','Total pago directo','text','','derecha','40',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_seguro','0.00','double','37',NULL,'Monto seguro','text',NULL,'derecha','41',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_placas','0.00','double','37',NULL,'Monto placas','text',NULL,'derecha','42',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_gestoria','0.00','double','37','','Gestoria','text','','derecha','43',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_notario','0.00','double','37',NULL,'Monto notario','text',NULL,'derecha','44',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_residual','0.00','double','37',NULL,'Monto residual','text',NULL,'derecha','45',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_vehiculo','0.00','double','37','','Cuota Renta','text','','derecha','46',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_aliado','0.00','double','37','','Cuota Aliado','text','','derecha','47',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_accesorios','0.00','double','37','','Cuota Accesorios','text','','derecha','48',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_tenencia','0.00','double','37','','Cuota Tenencia','text','','derecha','49',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_mtto','0.00','double','37','','Cuota Mantenimiento','text','','derecha','50',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_gps','0.00','double','37','','Cuota GPS','text','','derecha','51',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_seguro','0.00','double','37','','Cuota Seguro','text','','derecha','52',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_comision','0.00','double','37','','Comision','text','','derecha','53',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','monto_originador','0.00','double','37',NULL,'Monto originador','text',NULL,'derecha','54',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','cuota_garantia','0.00','double','37','','Cuota Garantia','text','','derecha','55',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','es_moral','0','int','2',NULL,'Es moral','text',NULL,'derecha','56',NULL,NULL,'','normalfield','')";
$sql["20161201"][]	= "insert into `general_structure` (`tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) values('originacion_leasing','estatus','1','int','2',NULL,'Estatus','text',NULL,'derecha','57',NULL,NULL,'','normalfield','')";


$sql["20161201"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (70053, 7000, 'Lista de No Vigiladas', 'frmpld/lista-blanca-interna.frm.php', 'Lista de Omitidos', 'fa-money', 'command', '70053', '70053', 'true')";
$sql["20161201"][]	= "ALTER TABLE `tesoreria_tipos_de_pago` CHANGE COLUMN `descripcion_completa` `descripcion_completa` VARCHAR(100) NULL DEFAULT NULL  ,ADD COLUMN `formato` VARCHAR(50) NULL DEFAULT ''  AFTER `activo`";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-efectivo.frm.php' WHERE `tipo_de_pago` = 'efectivo'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-cheques-internos.frm.php' WHERE `tipo_de_pago` = 'cheque.ingreso'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'pago-cheques-internos.frm.php' WHERE `tipo_de_pago` = 'cheque'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-transferencia.frm.php' WHERE `tipo_de_pago` = 'transferencia'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'pago-transferencia.frm.php' WHERE `tipo_de_pago` = 'transferencia.egreso'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-cheques.frm.php' WHERE `tipo_de_pago` = 'foraneo'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-cargo-documento.frm.php' WHERE `tipo_de_pago` = 'descuento'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-cargo-documento.frm.php' WHERE `tipo_de_pago` = 'documento'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-general.frm.php' WHERE `tipo_de_pago` = 'tarj.credito.ingreso'";
$sql["20161201"][]	= "UPDATE `tesoreria_tipos_de_pago` SET `formato` = 'cobro-general.frm.php' WHERE `tipo_de_pago` = 'tarj.debito.ingreso'";
$sql["20161201"][]	= "ALTER TABLE `tesoreria_tipos_de_pago` ADD COLUMN `eq_contable` INT(6) NULL DEFAULT '99' COMMENT 'Equivalente contable' AFTER `formato`";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9100' WHERE `tipo_de_pago` = 'efectivo'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9100' WHERE `tipo_de_pago` = 'efectivo.egreso'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9200' WHERE `tipo_de_pago` = 'cheque'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9100' WHERE `tipo_de_pago` = 'cheque.ingreso'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9200' WHERE `tipo_de_pago` = 'transferencia'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9200' WHERE `tipo_de_pago` = 'transferencia.egreso'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9100' WHERE `tipo_de_pago` = 'foraneo'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9201' WHERE `tipo_de_pago` = 'documento'";
$sql["20161201"][]	= " UPDATE `tesoreria_tipos_de_pago` SET `eq_contable` = '9201' WHERE `tipo_de_pago` = 'descuento'";

$sql["20161202"][]	= "INSERT INTO `general_structure` (`index_struct`, `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`, `tab_num`, `css_class`, `input_events`) VALUES (NULL, 'operaciones_recibos', 'montohist', '0.00', 'float', '25', '', 'Saldo Final', 'text', '', 'derecha', '123', '', '', '0', 'normalfield', '')";

$sql["20161204"][]	= "UPDATE `creditos_destinos` SET `tasa_de_iva` = '0' WHERE `idcreditos_destinos` = '7100'";
$sql["20161204"][]	= "UPDATE `creditos_destinos` SET `tasa_de_iva` = '0' WHERE `idcreditos_destinos` = '7101'";
$sql["20161204"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES ('157', 'PAGO SEGURO DE VEHICULAR', '0', '0', '\$cuenta  = CUENTA_DE_CUADRE;', 'Pagos a Seguro de Vehiculos en Arrendamiento', '99', '156', '1', '1', '99', '1', '0', '0', '0', '0', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0.160', 'SEGURO VEHICULO', '1', '0.00')";
$sql["20161204"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES ('171', 'PAGO TENENCIA FINANCIADA', '0', '0', '\$cuenta  = CUENTA_DE_CUADRE;', 'Pagos De Tenencia Financiada', '99', '156', '1', '1', '99', '1', '0', '0', '0', '0', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0.160', 'TENENCIA FIN', '1', '0.00')";
$sql["20161204"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES ('172', 'PAGO MTTO PREV', '0', '0', '\$cuenta  = CUENTA_DE_CUADRE;', 'Pagos De Mantenimiento Preventivo', '99', '156', '1', '1', '99', '1', '0', '0', '0', '0', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0.160', 'MTTO PREVENTIVO', '1', '0.00')";
$sql["20161204"][]	= "insert into `creditos_productos_costos` (`clave_de_producto`, `clave_de_operacion`, `unidades`, `unidad_de_medida`, `editable`, `en_plan`, `exigencia`) values('500','157','0.0000','0','0','0','1')";
$sql["20161204"][]	= "insert into `creditos_productos_costos` (`clave_de_producto`, `clave_de_operacion`, `unidades`, `unidad_de_medida`, `editable`, `en_plan`, `exigencia`) values('500','172','0.0000','0','0','0','1')";
$sql["20161204"][]	= "insert into `creditos_productos_costos` (`clave_de_producto`, `clave_de_operacion`, `unidades`, `unidad_de_medida`, `editable`, `en_plan`, `exigencia`) values('500','171','0.0000','0','0','0','1')";

$sql["20161205"][]	= "CREATE TABLE IF NOT EXISTS  `aml_riesgo_producto` (  `idaml_riesgo_producto` INT NOT NULL AUTO_INCREMENT ,  `tipo_de_producto` INT(3) NULL ,  `clave_de_producto` INT(6) NULL ,  `nivel_de_riesgo` INT(4) NULL ,  `observaciones` VARCHAR(50) NULL ,  PRIMARY KEY (`idaml_riesgo_producto`)  ,  INDEX `idxp` (`clave_de_producto` ASC, `tipo_de_producto` ASC, `nivel_de_riesgo` ASC, `idaml_riesgo_producto` ASC)  ) ENGINE = InnoDB";
$sql["20161205"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (72206, 72000, 'Riesgo por Producto', 'frmpld/riesgo-producto.frm.php', 'RIESGO POR PRODUCTO', 'fa-cube', 'command', '72206', '72206', 'true') ";
$sql["20161205"][]	= "INSERT INTO `aml_risk_catalog` (`clave_de_control`, `descripcion`, `tipo_de_riesgo`, `valor_ponderado`, `unidades_ponderadas`, `unidad_de_medida`, `forma_de_reportar`, `frecuencia_de_chequeo`, `fundamento_legal`) VALUES ('911101', 'Operaciones. Operaciones en Paises de Alto Riesgo', '911', '100', '1.0000', 'EVENTO', 'C', 'D', '')";

$sql["20161206"][]	= "INSERT INTO `riesgos_reporte` (`idriesgos_reporte`, `nombre_reporte`, `eq_aml`) VALUES ('1', 'Directo', 'I')";
$sql["20161206"][]	= "INSERT INTO `riesgos_reporte` (`idriesgos_reporte`, `nombre_reporte`, `eq_aml`) VALUES ('2', 'Validado', 'C')";
$sql["20161206"][]	= "INSERT INTO `entidad_niveles_de_riesgo` (`clave_de_nivel`, `nombre_del_nivel`) VALUES ('1', 'MUY BAJO')";
$sql["20161206"][]	= "INSERT INTO `entidad_niveles_de_riesgo` (`clave_de_nivel`, `nombre_del_nivel`) VALUES ('10', 'BAJO')";
$sql["20161206"][]	= "INSERT INTO `entidad_niveles_de_riesgo` (`clave_de_nivel`, `nombre_del_nivel`) VALUES ('25', 'MEDIO BAJO')";
$sql["20161206"][]	= "INSERT INTO `entidad_niveles_de_riesgo` (`clave_de_nivel`, `nombre_del_nivel`) VALUES ('75', 'MEDIO ALTO')";

$sql["20161207"][]	= "TRUNCATE `idaml_riesgo_matrices`";

$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('1','PERSONA_RIESGO_ACTIVIDAD','PERSONA','Riesgo de Actividad heredado','901501','50','SISTEMA','1','99','1481134158','1','2','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('2','PERSONA_RIESGO_EXTRANJERO','PERSONA','Riesgo por ser extranjero','801009','25','SISTEMA','1','99','1481134643','0','4','1.0000','1')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('3','PERSONA_RIESGO_EN_EXCEPCION','PERSONA','Riesgo si la persona está en excepcion','801010','1','SISTEMA','1','99','1481134683','0','2','1.0000','1')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('4','PERSONA_RIESGO_PAIS','PERSONA','Riesgo de Pais','801011','50','SISTEMA','1','99','1481134727','1','4','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('5','PERSONA_SIN_ACTIVIDAD','PERSONA','Personas sin Actividad Economica','801006','25','SISTEMA','1','99','1481134788','0','1','1.0000','1')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('6','PERSONA_RIESGO_ES_NACIONAL','PERSONA','Riesgo de Personas Nacionales','801006','1','SISTEMA','1','99','1481134839','0','1','1.0000','1')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('7','PERSONA_MORAL_SIN_REPRESENTANTE','PERSONA','Persona Moral Sin Representante','801005','50','SISTEMA','1','99','1481134881','0','2','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('8','PERSONA_SIN_PERFIL_T','PERSONA','Persona no tiene perfil Transaccional','801005','50','SISTEMA','1','99','1481134946','0','2','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('9','PERSONA_RIESGO_DOM_PAIS','PERSONA','Riesgo Domicilio pais','801011','50','SISTEMA','1','99','1481155756','1','2','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('10','PERSONA_RIESGO_SIN_DOM','PERSONA','Personas que no Tienen Domicilio','801005','50','SISTEMA','1','99','1481135185','0','2','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('11','PERSONA_RIESGO_PAIS','PERSONA','Riesgo de Pais','801011','50','SISTEMA','1','99','1481135843','1','4','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('12','OPERACION_CON_EXTRANJERO','OPERACION','Operacion con persona extranjera','101005','10','SISTEMA','1','99','1481135940','0','4','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('13','OPERACION_INSTRUMENTO_RIESGO','OPERACION','Instrumentos Riesgosos','101005','10','SISTEMA','1','99','1481136007','0','2','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('14','OPERACION_RIESGO_PAIS_ORIGEN','OPERACION','Riesgo de Pais de Origen de recursos','911101','50','SISTEMA','1','99','1481136077','1','5','1.0000','3')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('15','OPERACION_RIESGO_LOC_ORIGEN','OPERACION','Riesgo en Localidades con Riesgo Medio','911101','50','SISTEMA','1','99','1481136149','0','3','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('16','OPERACION_PERSONA_ALTO_RIESGO','OPERACION','Operaciones con Personas de Alto Riesgo','101005','100','SISTEMA','1','99','1481136216','1','4','1.0000','3')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('17','OPERACION_PERSONA_BLOQUEADA','OPERACION','Operaciones con Personas Bloqueadas','101501','100','SISTEMA','1','99','1481136291','1','5','1.0000','5')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('18','OPERACION_PERSONA_PEPS','OPERACION','Operaciones con PEPS','101510','50','SISTEMA','1','99','1481136330','0','4','1.0000','2')";
$sql["20161207"][]	= "insert into `aml_riesgo_matrices` (`idaml_riesgo_matrices`, `nombre`, `clasificacion`, `descripcion`, `clave_riesgo`, `riesgo`, `define`, `estatus`, `usuario`, `tiempo`, `finalizador`, `probabilidad`, `impacto`, `consecuencia`) values('19','PERSONA_RIESGO_ES_PEP','PERSONA','Cuando por su actividad o manifestacion es PEP','901010','50','SISTEMA','1','99','1481157664','1','2','1.0000','2')";

$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('1','200','500','10','')";
$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('3','200','201','1','')";
$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('4','200','100','1','')";
$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('6','200','2018','25','')";
$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('7','200','200','25','')";
$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('8','200','300','25','')";
$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('10','200','104','1','')";
$sql["20161207"][]	= "insert into `aml_riesgo_producto` (`idaml_riesgo_producto`, `tipo_de_producto`, `clave_de_producto`, `nivel_de_riesgo`, `observaciones`) values('11','200','2012','1','')";

$sql["20161207"][]	= "ALTER TABLE `personas_documentacion_tipos` ADD COLUMN `vigencia_dias` INT(6) NULL DEFAULT 90 COMMENT '' AFTER `clasificacion`, ADD COLUMN `almacen` INT(2) NULL DEFAULT 1 COMMENT 'Indica si se puede almacenar' AFTER `vigencia_dias`";
$sql["20161207"][]	= "ALTER TABLE `personas_documentacion` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' COMMENT '' AFTER `vencimiento`";
$sql["20161207"][]	= "INSERT INTO `aml_risk_catalog` (`clave_de_control`, `descripcion`, `tipo_de_riesgo`, `valor_ponderado`, `unidades_ponderadas`, `unidad_de_medida`, `forma_de_reportar`, `frecuencia_de_chequeo`, `fundamento_legal`) VALUES ('911101', 'Operaciones. Operaciones en Paises de Alto Riesgo', '911', '100', '1.0000', 'EVENTO', 'C', 'D', '')";
$sql["20161207"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('1', 'Por definir', 'Por definir')";
$sql["20161207"][]	= "INSERT INTO `aml_instrumentos_financieros` (`tipo_de_instrumento`, `nombre_de_instrumento`, `descripcion`) VALUES ('7', 'Por definir', 'Por definir')";
$sql["20161207"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (20514, 2050, 'Tipos de Documentacion', 'frmsocios/catalogo-documentacion.frm.php', 'Tipos de Documentacion', 'fa-list-alt', 'command', '20514', '20514', 'true')";

$sql["20161207"][]	= "ALTER TABLE `personas_documentacion_tipos` ADD COLUMN `checklist` VARCHAR(20) NULL DEFAULT '' COMMENT 'relacion con checklist' AFTER `almacen`";
$sql["20161207"][]	= "ALTER TABLE `personas_documentacion_tipos` CHANGE COLUMN `clave_de_control` `clave_de_control` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' ";
$sql["20161207"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' WHERE `idgeneral_menu` = '72203' ";


$sql["20161208"][]	= "UPDATE `general_menu` SET `menu_title` = 'Registro de usuarios' WHERE `idgeneral_menu` = '10001'";
$sql["20161208"][]	= "ALTER TABLE `personas_consulta_lista` ADD COLUMN `contenido` MEDIUMTEXT NULL COMMENT '' AFTER `textocoincidente`";


$sql["20161209"][]	= "ALTER TABLE `general_log` ADD COLUMN `idpersona` BIGINT(20) NULL DEFAULT '0' COMMENT '' AFTER `ip_public`,ADD INDEX `idf` (`idpersona` ASC, `usr_log` ASC, `idgeneral_log` ASC)  COMMENT ''";
$sql["20161209"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('103', 'Persona No encontrada en Listas', 'common')";

$sql["20161210"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('566', 'VALIDACION', 'OMITE.PERSONA_FALTA_ACT_ECONOM', '', '', '\$valor=false;', '')";
$sql["20161210"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`) VALUES ('567', 'VALIDACION', 'OMITE.PERSONA_FALLA_ACT_ECONOM', '', '', '\$valor=false;')";

$sql["20161211"][]	= "UPDATE `aml_risk_types` SET `nombre_del_riesgo` = 'Alertas de Seguimiento' WHERE `clave_de_control` = '903'";
$sql["20161211"][]	= "UPDATE `aml_risk_types` SET `nombre_del_riesgo` = 'DEPRECATED' WHERE `clave_de_control` = '902'";
$sql["20161211"][]	= "UPDATE `aml_risk_types` SET `nombre_del_riesgo` = 'Alertas de Control' WHERE `clave_de_control` = '901'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '901001'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101002'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '801006'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Personas. Posible Documento FalsoDocumento Falso Registrado' , `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '801007'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Personas. Posible Documento Falso' WHERE `clave_de_control` = '801007'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Personas. Usuario. Posible Docto Falso' , `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '801008'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '901' WHERE `clave_de_control` = '912301'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '901' WHERE `clave_de_control` = '912302'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '901' WHERE `clave_de_control` = '912102'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '901' WHERE `clave_de_control` = '912201'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '901' WHERE `clave_de_control` = '912202'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '901' WHERE `clave_de_control` = '912101'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101502'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101501'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101510'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101005'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '901' WHERE `clave_de_control` = '101003'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '901002'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101001'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '901501'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101004'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '101111'";
$sql["20161211"][]	= "UPDATE `aml_risk_catalog` SET `tipo_de_riesgo` = '903' WHERE `clave_de_control` = '912100'";

$sql["20161211"][]	= "DELETE FROM `aml_risk_types` WHERE `clave_de_control` = '902' ";
$sql["20161211"][]	= "TRUNCATE `entidad_calificacion` ";


$sql["20161212"][]	= "ALTER TABLE `creditos_garantias` CHANGE COLUMN `monto_valuado` `monto_valuado` DOUBLE(12,2) NOT NULL DEFAULT '0.00'  ,CHANGE COLUMN `observaciones` `observaciones` VARCHAR(80) NOT NULL  ,CHANGE COLUMN `documento_presentado` `documento_presentado` VARCHAR(150) NOT NULL  ,CHANGE COLUMN `descripcion` `descripcion` VARCHAR(150) NULL DEFAULT 'NA' COMMENT 'modelo' ,CHANGE COLUMN `caracteristica1` `caracteristica1` VARCHAR(40) NULL DEFAULT '' COMMENT 'serie del chasis' ,CHANGE COLUMN `caracteristica2` `caracteristica2` VARCHAR(40) NULL DEFAULT '' COMMENT 'serie del motor' ,CHANGE COLUMN `caracteristica3` `caracteristica3` VARCHAR(40) NULL DEFAULT '' COMMENT 'color' ,ADD COLUMN `caracteristica4` VARCHAR(40) NULL DEFAULT ''  AFTER `caracteristica3`,ADD COLUMN `marca` INT(6) NULL DEFAULT 0 COMMENT '0 = ninguno' AFTER `caracteristica4`,ADD COLUMN `extras` VARCHAR(100) NULL DEFAULT '' COMMENT 'datos extras equipo aliado' AFTER `marca`,DROP PRIMARY KEY,ADD PRIMARY KEY (`idcreditos_garantias`)  , ADD INDEX `idcge` (`solicitud_garantia` ASC, `idcreditos_garantias` ASC, `tipo_garantia` ASC, `socio_garantia` ASC, `tipo_valuacion` ASC, `propietario` ASC, `marca` ASC) ";
$sql["20161212"][]	= "ALTER TABLE `creditos_garantias` ADD COLUMN `domicilio_vinculado` INT NULL DEFAULT 0 COMMENT 'ID de domicilio Vinculado' AFTER `extras`";
$sql["20161212"][]	= "UPDATE `creditos_garantiasestatus` SET `descripcion_garantiasestatus` = 'DEVUELTO' WHERE `idcreditos_garantiasestatus` = '3'";

$sql["20161213"][]	= "ALTER TABLE `creditos_garantias` CHANGE COLUMN `idcreditos_garantias` `idcreditos_garantias` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT";

$sql["20161214"][]	= "ALTER TABLE `leasing_usuarios` ADD COLUMN `telefono` VARCHAR(15) NULL DEFAULT '' AFTER `idusuario`";

$sql["20170101"][]	= "UPDATE `general_contratos` SET `tipo_contrato`=200 WHERE `tipo_contrato`=10";
$sql["20170101"][]	= "UPDATE `general_contratos` SET `tipo_contrato`=300 WHERE `tipo_contrato`=20";
$sql["20170101"][]	= "UPDATE `general_contratos` SET `tipo_contrato`=500 WHERE `tipo_contrato`=50";
$sql["20170101"][]	= "UPDATE `general_contratos` SET `tipo_contrato`=510 WHERE `tipo_contrato`=80";
$sql["20170101"][]	= "UPDATE `general_contratos` SET `tipo_contrato`=100 WHERE `tipo_contrato`=30";

$sql["20170101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1901', '200', 'Arrendamiento.- propuesta', '<!-- contenido -->')";
$sql["20170101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1902', '200', 'Arrendamiento.- Carta de Bienvenida', '<!-- contenido -->')";
$sql["20170101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1903', '200', 'Arrendamiento.- Pago Inicial', '<!-- Contenido -->')";
$sql["20170101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1904', '200', 'Arrendamiento.- Domiciliacion', '<!-- Contenido -->')";
$sql["20170101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1905', '200', 'Arrendamiento.- Anexo 1', '<!-- contenido -->')";
$sql["20170101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('1900', '200', 'Arrendamiento.- Presupuesto', '<!-- contenido -->')";

$sql["20170102"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `renta_deposito` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `estatus`, ADD COLUMN `renta_proporcional` DOUBLE(18,2) NULL DEFAULT '0'  AFTER `renta_deposito`";
$sql["20170102"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `renta_extra` DOUBLE(18,2) NULL DEFAULT '0.00' COMMENT '' AFTER `renta_proporcional`";
$sql["20170102"][]	= "INSERT INTO `catalogos_tipo_de_dispersion` (`tipo_de_dispersion`, `descripcion`) VALUES ('320', 'Domiciliado por Banca')";
$sql["20170102"][]	= "ALTER TABLE `catalogos_tipo_de_dispersion` ADD COLUMN `requiere_extras` INT(2) NULL DEFAULT '0' COMMENT '' AFTER `descripcion`";

$sql["20170102"][]	= "ALTER TABLE `operaciones_mvtos` DROP PRIMARY KEY,ADD PRIMARY KEY (`idoperaciones_mvtos`)  COMMENT ''";
$sql["20170102"][]	= "ALTER TABLE `operaciones_mvtos`  DROP INDEX `clave_de_operacion`";
$sql["20170102"][]	= "ALTER TABLE `operaciones_mvtos` DROP INDEX `tipoops` ,DROP INDEX `tipo_operacion` ";
$sql["20170102"][]	= "ALTER TABLE `operaciones_mvtos` ADD INDEX `bydocto` (`docto_afectado` ASC, `tipo_operacion` ASC, `idoperaciones_mvtos` ASC, `recibo_afectado` ASC, `periodo_socio` ASC)  COMMENT '',ADD INDEX `bypersona` (`socio_afectado` ASC, `tipo_operacion` ASC, `idoperaciones_mvtos` ASC, `recibo_afectado` ASC, `periodo_socio` ASC)  COMMENT '',ADD INDEX `byops` (`tipo_operacion` ASC, `docto_afectado` ASC, `socio_afectado` ASC, `recibo_afectado` ASC, `idoperaciones_mvtos` ASC, `periodo_socio` ASC)  COMMENT ''";
$sql["20170102"][]	= "ALTER TABLE `eacp_config_bases_de_integracion_miembros` DROP INDEX `mme` ,ADD INDEX `mme` (`miembro` ASC, `codigo_de_base` ASC)  COMMENT '',ADD INDEX `mmc` (`miembro` ASC, `subclasificacion` ASC, `codigo_de_base` ASC)  COMMENT ''";
$sql["20170102"][]	= "ALTER TABLE `eacp_config_bases_de_integracion_miembros` DROP INDEX `mme` ,ADD INDEX `mme` (`miembro` ASC, `codigo_de_base` ASC, `ideacp_config_bases_de_integracion_miembros` ASC)  COMMENT '',DROP INDEX `mmc` ,ADD INDEX `mmc` (`miembro` ASC, `subclasificacion` ASC, `codigo_de_base` ASC, `afectacion` ASC, `ideacp_config_bases_de_integracion_miembros` ASC)  COMMENT ''";
$sql["20170102"][]	= "ALTER TABLE `eacp_config_bases_de_integracion_miembros` DROP INDEX `mmc`";
$sql["20170102"][]	= "ALTER TABLE `eacp_config_bases_de_integracion_miembros` DROP INDEX `mme` ,ADD INDEX `mme` (`miembro` ASC, `ideacp_config_bases_de_integracion_miembros` ASC, `codigo_de_base` ASC, `subclasificacion` ASC, `afectacion` ASC)  COMMENT ''";
$sql["20170102"][]	= "ALTER TABLE `eacp_config_bases_de_integracion_miembros` DROP INDEX `mme` , ADD INDEX `mme` (`codigo_de_base` ASC, `miembro` ASC, `ideacp_config_bases_de_integracion_miembros` ASC, `subclasificacion` ASC, `afectacion` ASC)  COMMENT ''";
$sql["20170102"][]	= "ALTER TABLE `operaciones_mvtos` DROP INDEX `bydocto` ,ADD INDEX `bydocto` (`docto_afectado` ASC, `tipo_operacion` ASC, `idoperaciones_mvtos` ASC, `recibo_afectado` ASC, `periodo_socio` ASC, `socio_afectado` ASC)  COMMENT '', DROP INDEX `bypersona` , ADD INDEX `bypersona` (`socio_afectado` ASC, `tipo_operacion` ASC, `idoperaciones_mvtos` ASC, `recibo_afectado` ASC, `periodo_socio` ASC, `docto_afectado` ASC)  COMMENT '';";
$sql["20170102"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (7025, 7020, 'Reglas de la Entidad', 'frmsecurity/entidad-reglas.frm.php', 'Reglas de la Entidad', 'fa-list', 'command', '7025', '7025', 'true') ";
$sql["20170102"][]	= "ALTER TABLE `entidad_reglas` ADD COLUMN `valor` INT(2) NULL DEFAULT '0' COMMENT '' AFTER `metadata`";
$sql["20170102"][]	= "UPDATE `entidad_reglas` SET `valor`= 1 WHERE `reglas` LIKE  '%true%' ";
$sql["20170102"][]	= "UPDATE `entidad_reglas` SET `reglas`=''";

$sql["20170103"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('568', 'FORM', 'PERSONAS.USAR.DATOS_DE_ACCIDENTE', '', '', '', '')";
$sql["20170103"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('569', 'FORM', 'PERSONAS.CONSULTA.GSW.DEFECTO', '', '', '', '', '1') ";
$sql["20170103"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('570', 'FORM', 'PERSONAS.OPERAR.CON_ALTO_RIESGO', '', '', '', '', '1') ";

$sql["20170103"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (10025, 10020, 'Objetos Eliminados', 'frmsecurity/eliminados.frm.php', 'Objetos Eliminados', 'fa-code', 'command', '10025', '10025', 'true') ";

$sql["20170104"][]	= "ALTER TABLE `creditos_productos_costos` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' COMMENT '' AFTER `exigencia`";
$sql["20170104"][]	= "ALTER TABLE `creditos_productos_costos` ADD COLUMN `aplicar_desde` DATE NULL COMMENT '' AFTER `estatus`, ADD COLUMN `aplicar_hasta` DATE NULL COMMENT '' AFTER `aplicar_desde`";

$sql["20170105"][]	= "ALTER TABLE `personas_documentacion` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' COMMENT '' AFTER `vencimiento`";
$sql["20170105"][]	= "INSERT INTO `aml_risk_catalog` (`clave_de_control`, `descripcion`, `tipo_de_riesgo`, `valor_ponderado`, `unidades_ponderadas`, `unidad_de_medida`, `forma_de_reportar`, `frecuencia_de_chequeo`, `fundamento_legal`) VALUES ('911101', 'Operaciones. Operaciones en Paises de Alto Riesgo', '911', '100', '1.0000', 'EVENTO', 'C', 'D', '')";
$sql["20170105"][]	= "INSERT INTO `aml_tipos_de_operacion` (`tipo_de_operacion_aml`, `nombre_de_la_operacion`, `descripcion`) VALUES ('1', 'Por definir', 'Por definir')";
$sql["20170105"][]	= "INSERT INTO `aml_instrumentos_financieros` (`tipo_de_instrumento`, `nombre_de_instrumento`, `descripcion`) VALUES ('7', 'Por definir', 'Por definir')";
$sql["20170105"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (20514, 2050, 'Tipos de Documentacion', 'frmsocios/catalogo-documentacion.frm.php', 'Tipos de Documentacion', 'fa-list-alt', 'command', '20514', '20514', 'true')";
$sql["20170105"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' WHERE `idgeneral_menu` = '72203' ";
$sql["20170105"][]	= "ALTER TABLE `personas_documentacion_tipos` ADD COLUMN `vigencia_dias` INT(6) NULL DEFAULT 90 COMMENT '' AFTER `clasificacion`, ADD COLUMN `almacen` INT(2) NULL DEFAULT 1 COMMENT 'Indica si se puede almacenar' AFTER `vigencia_dias`";
$sql["20170105"][]	= "ALTER TABLE `personas_documentacion_tipos` ADD COLUMN `checklist` VARCHAR(20) NULL DEFAULT '' COMMENT 'relacion con checklist' AFTER `almacen`";
$sql["20170105"][]	= "ALTER TABLE `personas_documentacion_tipos` CHANGE COLUMN `clave_de_control` `clave_de_control` INT(11) NOT NULL AUTO_INCREMENT COMMENT '' ";


$sql["20170106"][]	= "UPDATE `socios_relacionestipos` SET `descripcion_larga` = SUBSTRING(`descripcion_larga`, 0, 79)";
$sql["20170106"][]	= "ALTER TABLE `socios_relacionestipos` CHANGE COLUMN `idsocios_relacionestipos` `idsocios_relacionestipos` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT  ,CHANGE COLUMN `descripcion_larga` `descripcion_larga` VARCHAR(80) NULL DEFAULT ''  ,CHANGE COLUMN `requiere_domicilio` `requiere_domicilio` INT(2) NULL DEFAULT '0'  ,CHANGE COLUMN `requiere_actividadeconomica` `requiere_actividadeconomica` INT(2) NULL DEFAULT '0'  ,CHANGE COLUMN `requiere_validacion` `requiere_validacion` INT(2) NULL DEFAULT '0'  ,CHANGE COLUMN `tiene_vinculo_patrimonial` `tiene_vinculo_patrimonial` INT(2) NULL DEFAULT '0' , ADD COLUMN `tags` VARCHAR(50) NULL DEFAULT '' COMMENT 'tags de filtros' AFTER `checar_aml`";
$sql["20170106"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (20515, 2050, 'Catalogo Tipos de Relacion', 'frmsocios/catalogo-tipos-relacion.frm.php', 'Catalogo Tipos de Relacion', 'fa-list', 'command', '20515', '20515', 'true') ";
$sql["20170106"][]	= "ALTER TABLE `general_contratos` ADD COLUMN `tags` VARCHAR(40) NULL DEFAULT '' COMMENT 'etiquetas de clasificacion' AFTER `texto_del_contrato`";
$sql["20170106"][]	= "ALTER TABLE `general_contratos` ADD COLUMN `ruta` VARCHAR(120) NULL DEFAULT '' COMMENT 'URL del formato' AFTER `tags`";
$sql["20170106"][]	= "UPDATE `general_contratos` SET `tags` = '281,todas' , `ruta` = '../rpt_formatos/credito.carta-bienvenida.rpt.php?' WHERE `idgeneral_contratos` = '1902'";
$sql["20170106"][]	= "UPDATE `general_contratos` SET `tags` = '281' , `ruta` = '../rpt_formatos/credito.pago-inicial.rpt.php?' WHERE `idgeneral_contratos` = '1903'";
$sql["20170106"][]	= "UPDATE `general_contratos` SET `tags` = '281,todas' , `ruta` = '../rpt_formatos/credito.domiciliar.rpt.php?' WHERE `idgeneral_contratos` = '1904'";
$sql["20170106"][]	= "UPDATE `general_contratos` SET `tags` = '281' , `ruta` = '../rpt_formatos/credito.anexo-a.rpt.php?' WHERE `idgeneral_contratos` = '1905'";
$sql["20170106"][]	= "ALTER TABLE `socios_patrimonioestatus` CHANGE COLUMN `estatus_actual` `estatus_actual` INT(4) NULL DEFAULT NULL COMMENT 'DEPRECATED' ,CHANGE COLUMN `estado_presentado` `estado_presentado` INT(4) NULL DEFAULT NULL COMMENT 'DEPRECATED' ,ADD COLUMN `tags` VARCHAR(50) NULL DEFAULT '' COMMENT 'tags de clasificacion' AFTER `estado_presentado`";
$sql["20170106"][]	= "INSERT INTO `socios_patrimonioestatus` (`idsocios_patrimonioestatus`, `descripcion_patrimonioestatus`, `estatus_actual`, `estado_presentado`, `tags`) VALUES ('11', 'NUEVO', '11', '11', '281'); ";
$sql["20170106"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('2013', '2000', 'Registro de Proveedores', 'frmsocios/registro-personas_morales.frm.php?tipodeingreso=905', 'Registro de Proveedores', 'fa-hospital-o', 'command', '4', '2013', 'true')";
$sql["20170106"][]	= "INSERT INTO `socios_tipoingreso` (`idsocios_tipoingreso`, `descripcion_tipoingreso`, `descripcion_detallada`) VALUES ('905', 'PROVEEDOR', 'Proveedor de la Financiera')";
$sql["20170106"][]	= "CREATE TABLE IF NOT EXISTS  `personas_proveedores` (`idpersonas_proveedores` INT NOT NULL AUTO_INCREMENT COMMENT '',`persona` BIGINT(20) NULL DEFAULT '0' COMMENT '', PRIMARY KEY (`idpersonas_proveedores`)  COMMENT '') ENGINE = InnoDB";
$sql["20170106"][]	= "UPDATE `operaciones_recibos`,`tesoreria_tipos_de_pago` SET `operaciones_recibos`.`origen_aml` = `tesoreria_tipos_de_pago`.`equivalente_aml` WHERE `operaciones_recibos`.`tipo_pago` = `tesoreria_tipos_de_pago`.`tipo_de_pago` AND `operaciones_recibos`.`origen_aml`=0";

$sql["20170106"][]	= "ALTER TABLE `leasing_tasas` ADD COLUMN `comision_apertura` FLOAT(6,4) NULL DEFAULT '0' COMMENT '' AFTER `frecuencia`";
$sql["20170106"][]	= "ALTER TABLE `leasing_originadores` ADD COLUMN `direccion` VARCHAR(150) NULL DEFAULT '' COMMENT '' AFTER `frecuencia_meta`,ADD COLUMN `telefono` VARCHAR(15) NULL DEFAULT '' COMMENT '' AFTER `direccion`, ADD COLUMN `email_contacto` VARCHAR(25) NULL DEFAULT '' COMMENT '' AFTER `telefono`";
$sql["20170106"][]	= "ALTER TABLE `leasing_originadores` DROP COLUMN `email_contacto`";
$sql["20170106"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('571', 'FORM', 'CREDITOS.PLAN_PAGOS.SDO_FINAL_CAP', '', '', '', '')";
$sql["20170106"][]	= "CREATE TABLE IF NOT EXISTS `creditos_productos_etapas` (`idcreditos_productos_etapas` INT NOT NULL AUTO_INCREMENT COMMENT '',  `producto` INT(8) NULL COMMENT '',  `etapa` INT(4) NULL DEFAULT '1' COMMENT 'de 1 a x',  `nombre` VARCHAR(80) NULL DEFAULT '' COMMENT '',  `tags` VARCHAR(50) NULL DEFAULT '' COMMENT '',  `permisos` VARCHAR(100) NULL DEFAULT '' COMMENT '1@rw,2@rw',  PRIMARY KEY (`idcreditos_productos_etapas`)  COMMENT '') ENGINE = INNODB";
$sql["20170106"][]	= "ALTER TABLE `creditos_productos_etapas` ADD COLUMN `orden` INT(3) NULL DEFAULT 0 COMMENT '' AFTER `permisos`";

$sql["20170107"][]	= "CREATE TABLE IF NOT EXISTS  `creditos_etapas` (  `idcreditos_etapas` INT NOT NULL AUTO_INCREMENT COMMENT '',  `descripcion` VARCHAR(50) NULL COMMENT '',  PRIMARY KEY (`idcreditos_etapas`)  COMMENT '') ENGINE = INNODB";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('1', 'Registrado')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('100', 'Atendido sin Oficial')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('101', 'Oficial Asignado')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('102', 'Persona Asignada') ";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('103', 'Credito Asignado') ";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('99', 'Credito Solicitado')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('98', 'Credito Autorizado') ";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('991', 'Credito a Revision')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('989', 'Credito rechazado')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('988', 'Credito rechado con Observaciones')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('987', 'Credito autorizado con Modificaciones')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('10', 'Credito Vigente')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('30', 'Credito con Atrasos')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('20', 'Credito Vencido')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('50', 'Credito castigado')";
$sql["20170107"][]	= "INSERT INTO `creditos_etapas` (`idcreditos_etapas`, `descripcion`) VALUES ('501', 'Enviado a Desembolso')";


$sql["20170108"][]	= "ALTER TABLE `creditos_garantias` CHANGE COLUMN `documento_presentado` `documento_presentado` VARCHAR(100) NOT NULL COMMENT '' ,CHANGE COLUMN `descripcion` `descripcion` VARCHAR(100) NULL DEFAULT 'NA' COMMENT 'modelo' ,ADD COLUMN `caracteristica5` VARCHAR(40) NULL DEFAULT '' COMMENT 'numero de placas' AFTER `domicilio_vinculado`";
$sql["20170108"][]	= "ALTER TABLE `creditos_garantias` ADD COLUMN `tipo_origen` INT(4) NULL DEFAULT 0 COMMENT '' AFTER `caracteristica5`,ADD COLUMN `clave_origen` BIGINT(20) NULL DEFAULT 0 COMMENT '' AFTER `tipo_origen`";
$sql["20170108"][]	= "CREATE TABLE IF NOT EXISTS `creditos_productos_req` (`idcreditos_productos_req` INT NOT NULL AUTO_INCREMENT ,  `producto` INT(8) NULL ,  `tipo_req` INT(4) NULL COMMENT 'Tipo de requerimiento',  `descripcion` VARCHAR(50) NULL ,  PRIMARY KEY (`idcreditos_productos_req`)  ) ENGINE = InnoDB";
$sql["20170108"][]	= "CREATE TABLE IF NOT EXISTS `originacion_requisitos` (`idoriginacion_requisitos` INT NOT NULL AUTO_INCREMENT ,  `requisito` INT NULL ,  `ruta` VARCHAR(150) NULL ,  PRIMARY KEY (`idoriginacion_requisitos`)  ) ENGINE = InnoDB";
$sql["20170108"][]	= "CREATE TABLE IF NOT EXISTS `creditos_productos_promo` (`idcreditos_productos_promo` INT NOT NULL AUTO_INCREMENT ,  `tipo_promocion` INT(2) NULL DEFAULT 1 COMMENT '1 descuento base ',  `fecha_inicial` DATE NULL ,  `fecha_final` DATE NULL ,  `tipo_operacion` INT(8) NULL DEFAULT 0 COMMENT 'tipo de operacion que aplica',  `condiciones` TEXT NULL ,  `num_items` INT(4) NULL DEFAULT 0 ,  `descuento` FLOAT(6,4) NULL DEFAULT 0 COMMENT 'Tasa de descuento',  `precio` DOUBLE(12,2) NULL DEFAULT '0' ,  `sucursal` VARCHAR(20) NULL DEFAULT 'todas' ,  `estatus` INT(2) NULL DEFAULT '1' ,  PRIMARY KEY (`idcreditos_productos_promo`)  ) ENGINE = InnoDB";
$sql["20170108"][]	= "ALTER TABLE `creditos_productos_promo` ADD COLUMN `producto` INT(8) NULL DEFAULT '0' AFTER `estatus`";

$sql["20170109"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES ('246', 'PAGO DE CARGOS VARIOS', '0', '0', '\$cuenta = \"3110002001\";', '', '99', '146', '1', '1', '99', '0', '0', '0', '0', '1', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0.160', 'PAGO C VARIOS', '1', '0.00')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('589','2002','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('590','7003','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('591','10000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('592','10001','246','1.0000','PAGO DE COM. VARIAS 2','10001')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('593','1000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('594','30200','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('595','15000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('596','11000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170109"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('597','1001','246','1.0000','PAGO DE COM. VARIAS 2','0')";

$sql["20170201"][]	= "ALTER TABLE `socios_aeconomica` DROP INDEX `codigo_postal` ,ADD INDEX `codigo_postal` (`ae_codigo_postal` ASC, `idsocios_aeconomica` ASC),DROP INDEX `localidad` ,ADD INDEX `localidad` (`ae_clave_de_localidad` ASC, `idsocios_aeconomica` ASC),DROP INDEX `iddomicilio` ,ADD INDEX `iddomicilio` (`domicilio_vinculado` ASC, `idsocios_aeconomica` ASC),DROP INDEX `idactividad` ,ADD INDEX `idactividad` (`tipo_aeconomica` ASC, `idsocios_aeconomica` ASC),ADD INDEX `idp` (`socio_aeconomica` ASC, `dependencia_ae` ASC, `idsocios_aeconomica` ASC) ";
$sql["20170201"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('40300', '4000', 'Mercadeo', '', 'Modulo de Mercadeo', 'fa-shopping-bag', 'parent', '40300', '40300', 'true')";
$sql["20170201"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('40301', '40300', 'Enviar Folleto', 'frmseguimiento/enviar-folleto.frm.php', 'Enviar folleto preterminado', 'fa-envelope-o', 'command', '40301', '40301', 'true')";
$sql["20170201"][]	= "CREATE TABLE IF NOT EXISTS `mercadeo_campana` (  `idmercadeo_campana` INT NOT NULL AUTO_INCREMENT,  `nombre` VARCHAR(45) NULL,  `fecha_inicial` DATE NULL,  `fecha_final` DATE NULL,  `oficial` INT(8) NULL,  `idusuario` INT(8) NULL,  PRIMARY KEY (`idmercadeo_campana`) ) ENGINE = INNODB";
$sql["20170201"][]	= "CREATE TABLE IF NOT EXISTS `mercadeo_envios` (  `idmercadeo_envios` INT NOT NULL AUTO_INCREMENT,  `estatus` INT(2) NULL DEFAULT 1,  `persona` BIGINT(20) NULL,  `tiempo` INT NULL,  `campana` INT(8) NULL,  PRIMARY KEY (`idmercadeo_envios`) ,  INDEX `idcampana` (`campana` ASC, `idmercadeo_envios` ASC) ) ENGINE = INNODB";
$sql["20170201"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (40302, 40300, 'Campannia', 'frmseguimiento/campannia.frm.php', 'Campannia', 'fa-users', 'command', '40300', '40300', 'true') ";
$sql["20170201"][]	= "INSERT INTO `mercadeo_campana` (`idmercadeo_campana`, `nombre`, `fecha_inicial`, `fecha_final`, `oficial`, `idusuario`) VALUES ('1', 'Campaña de Prueba', '2017-01-01', '2017-12-31', '99', '99')";
$sql["20170201"][]	= "DELETE FROM `operaciones_recibos` WHERE `idoperaciones_recibos`=499675 AND `tipo_docto`=12";
$sql["20170201"][]	= "ALTER TABLE `operaciones_recibos` DROP PRIMARY KEY,ADD PRIMARY KEY (`idoperaciones_recibos`) ,ADD INDEX `bysoc` (`numero_socio` ASC, `idoperaciones_recibos` ASC, `docto_afectado` ASC, `tipo_docto` ASC, `persona_asociada` ASC, `grupo_asociado` ASC) ,ADD INDEX `bycred` (`docto_afectado` ASC, `idoperaciones_recibos` ASC, `tipo_docto` ASC, `periodo_de_documento` ASC, `origen_aml` ASC, `clave_de_moneda` ASC, `tipo_pago` ASC, `cuenta_bancaria` ASC, `persona_asociada` ASC, `grupo_asociado` ASC, `idusuario` ASC)";
$sql["20170201"][]	= "ALTER TABLE `operaciones_recibos` DROP PRIMARY KEY,ADD PRIMARY KEY (`idoperaciones_recibos`)  ,DROP INDEX `aml1` ,ADD INDEX `aml1` (`numero_socio` ASC, `docto_afectado` ASC, `clave_de_moneda` ASC, `tipo_pago` ASC, `idoperaciones_recibos` ASC, `origen_aml` ASC, `tipo_docto` ASC, `persona_asociada` ASC, `idusuario` ASC, `periodo_de_documento` ASC, `cuenta_bancaria` ASC, `grupo_asociado` ASC)";

$sql["20170202"][]	= "DELETE FROM `general_menu` WHERE `menu_file` LIKE '%/index%' AND `idgeneral_menu` != 128 AND `idgeneral_menu`!=129";
$sql["20170202"][]	= "UPDATE `general_menu` SET `menu_file` = 'inicio.php' WHERE `idgeneral_menu` = '233' ";
$sql["20170202"][]	= "DELETE FROM `general_menu` WHERE `menu_file` LIKE '%/inicio%' ";
$sql["20170202"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`) VALUES ('26', '2')";
$sql["20170202"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`) VALUES ('27', '1')";
$sql["20170202"][]	= "INSERT INTO `creditos_nievelesdereserva` (`idcreditos_nievelesdereserva`, `tipo_de_credito`) VALUES ('28', '3')";

$sql["20170203"][]	= "CALL proc_lenguaje_cambiar_palabras('Empresas', 'Empleadores')";
$sql["20170203"][]	= "CALL proc_lenguaje_cambiar_palabras('Empresa', 'Empleador')";
$sql["20170203"][]	= "ALTER TABLE `socios_tipoingreso` CHANGE COLUMN `idsocios_tipoingreso` `idsocios_tipoingreso` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT";

$sql["20170203"][]	= "CREATE TABLE IF NOT EXISTS `sistema_catalogo` ( `idsistema_catalogo` INT NOT NULL AUTO_INCREMENT ,  `clave` VARCHAR(10) NULL ,  `descripcion` VARCHAR(40) NULL ,  `tags` VARCHAR(60) NULL ,  `objeto` INT(6) NULL ,  PRIMARY KEY (`idsistema_catalogo`)  ,  INDEX `idx` (`clave` ASC, `objeto` ASC, `idsistema_catalogo` ASC)  ) ENGINE = InnoDB";
$sql["20170203"][]	= "ALTER TABLE `creditos_etapas` ADD COLUMN `tags` VARCHAR(40) NULL DEFAULT ''  AFTER `descripcion`";
$sql["20170203"][]	= "ALTER TABLE `leasing_residual` CHANGE COLUMN `porciento_residual` `porciento_residual` FLOAT(6,4) NULL DEFAULT '0.0000' COMMENT 'inicial' , ADD COLUMN `porciento_final` FLOAT(6,4) NULL DEFAULT '0' COMMENT 'final' AFTER `porciento_residual`";
$sql["20170203"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `residuales` VARCHAR(60) NULL COMMENT 'resiidual formato PERIODICIDAD separador  PLAZO separador TASA coma' AFTER `renta_extra`";
$sql["20170203"][]	= "UPDATE `general_menu` SET `menu_title` = 'Editar Datos de la Microfinanciera' WHERE `idgeneral_menu` = '7021'";
$sql["20170203"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tags`, `objeto`) VALUES ('1', '1', 'Documental', 'tipo_requisito', '200')";
$sql["20170203"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tags`, `objeto`) VALUES ('2', '2', 'Procesal', 'tipo_requisito', '200')";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'administracion,todas' WHERE `idcreditos_etapas` = '10'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'administracion,todas' WHERE `idcreditos_etapas` = '20'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'administracion,todas' WHERE `idcreditos_etapas` = '30'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'administracion,todas' WHERE `idcreditos_etapas` = '98'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'administracion,todas' WHERE `idcreditos_etapas` = '99'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'originacion,todas' WHERE `idcreditos_etapas` = '501'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'originacion,todas' WHERE `idcreditos_etapas` = '987'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'originacion,todas' WHERE `idcreditos_etapas` = '988'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'originacion,todas' WHERE `idcreditos_etapas` = '989'";
$sql["20170203"][]	= "UPDATE `creditos_etapas` SET `tags` = 'originacion,todas' WHERE `idcreditos_etapas` = '991'";
$sql["20170203"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tags`, `objeto`) VALUES ('3', '2001', 'Registro de Domicilio', 'procesal,requisitos,domicilio,persona,todas', '200')";
$sql["20170203"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tags`, `objeto`) VALUES ('4', '2002', 'Registro de Actividad', 'procesal,requisitos,actividad,persona,todas', '200')";
$sql["20170203"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tags`, `objeto`) VALUES ('5', '2003', 'Registro de Referencias', 'procesal,requisitos,referencias,persona,todas', '200')";
$sql["20170203"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tags`, `objeto`) VALUES ('6', '1', 'Descuento Pago', 'tipo_promociones', '200')";
$sql["20170203"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tags`, `objeto`) VALUES ('7', '2', 'Productos Gratis', 'tipo_promociones', '200')";
$sql["20170203"][]	= "ALTER TABLE `creditos_productos_req` CHANGE COLUMN `tipo_req` `tipo_req` INT(4) NULL DEFAULT NULL COMMENT 'Tipo de requerimiento 1 documental 2 procesal' ,ADD COLUMN `numero` INT(4) NULL DEFAULT 1 AFTER `descripcion`,ADD COLUMN `ruta_validacion` VARCHAR(150) NULL DEFAULT '../svc/validad.svc.php' AFTER `numero`, ADD COLUMN `escore` FLOAT(6,3) NULL DEFAULT 0 AFTER `ruta_validacion`";
$sql["20170203"][]	= "ALTER TABLE `creditos_productos_req` ADD COLUMN `etapa` VARCHAR(15) NULL DEFAULT '' AFTER `escore`,ADD COLUMN `requerido` INT(2) NULL DEFAULT '1' AFTER `etapa`";
$sql["20170203"][]	= "ALTER TABLE `creditos_productos_req` ADD COLUMN `clave` INT(6) NULL DEFAULT 0 AFTER `requerido`";
$sql["20170203"][]	= "ALTER TABLE `sistema_catalogo` CHANGE COLUMN `tags` `tags` VARCHAR(60) NULL DEFAULT '' ,CHANGE COLUMN `objeto` `objeto` INT(6) NULL DEFAULT 0 COMMENT 'objeto iDE_Credito' ,ADD COLUMN `tabla_virtual` VARCHAR(25) NULL DEFAULT '' AFTER `objeto`,ADD COLUMN `clave_superior` VARCHAR(10) NULL DEFAULT '' AFTER `tabla_virtual` ";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'tipo_requisito' WHERE `idsistema_catalogo` = '1'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'tipo_requisito' WHERE `idsistema_catalogo` = '2'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'requisitos' WHERE `idsistema_catalogo` = '3'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'requisitos' WHERE `idsistema_catalogo` = '4'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `clave_superior` = '2' WHERE `idsistema_catalogo` = '3'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `clave_superior` = '2' WHERE `idsistema_catalogo` = '4'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'requisitos' , `clave_superior` = '2' WHERE `idsistema_catalogo` = '5'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'requisitos' , `clave_superior` = '1' WHERE `idsistema_catalogo` = '8'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'requisitos' , `clave_superior` = '1' WHERE `idsistema_catalogo` = '9'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'tipo_promociones' WHERE `idsistema_catalogo` = '7'";
$sql["20170203"][]	= "UPDATE `sistema_catalogo` SET `tabla_virtual` = 'tipo_promociones' WHERE `idsistema_catalogo` = '6'";
$sql["20170203"][]	= "ALTER TABLE `personas_documentacion_tipos` CHANGE COLUMN `vigencia_dias` `vigencia_dias` INT(4) NULL DEFAULT '90' ,CHANGE COLUMN `checklist` `checklist` VARCHAR(10) NULL DEFAULT '' COMMENT 'relacion con checklist entregax' ,ADD COLUMN `tags` VARCHAR(60) NULL DEFAULT '' AFTER `checklist`";

$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pf' WHERE `clave_de_control` = '110'";
$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pm' WHERE `clave_de_control` = '120'";
$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pf' WHERE `clave_de_control` = '210'";
$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pf' WHERE `clave_de_control` = '220'";
$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pf,pm' WHERE `clave_de_control` = '230'";
$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pf,pm' WHERE `clave_de_control` = '310'";
$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pm' WHERE `clave_de_control` = '3202'";
$sql["20170203"][]	= "UPDATE `personas_documentacion_tipos` SET `tags` = 'originacion,todas,pm' WHERE `clave_de_control` = '3201'";
$sql["20170203"][]	= "ALTER TABLE  `personas_documentacion_tipos` ADD COLUMN `estatus` INT(2) NULL DEFAULT 1 AFTER `tags` ";


$sql["20170301"][]	= "ALTER TABLE `creditos_datos_originacion` ADD COLUMN `monto_vinculado` DOUBLE(18,2) NULL DEFAULT '0' AFTER `idusuario`";


$sql["20170301"][]	= "CREATE TABLE IF NOT EXISTS `sistema_permisos` (  `idsistema_permisos` INT NOT NULL AUTO_INCREMENT,  `accion` VARCHAR(50) NULL,  `permisos` VARCHAR(200) NULL DEFAULT '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw', PRIMARY KEY (`idsistema_permisos`)) ENGINE = INNODB";
$sql["20170301"][]	= "ALTER TABLE  `sistema_permisos`  ADD INDEX `idx` (`idsistema_permisos` ASC, `accion` ASC)";
$sql["20170301"][]	= "ALTER TABLE `entidad_configuracion` ADD INDEX `idxcf` (`nombre_del_parametro` ASC, `valor_del_parametro` ASC)";
$sql["20170301"][]	= "ALTER TABLE `creditos_productos_otros_parametros` ADD INDEX `idcpop` (`clave_del_parametro` ASC, `valor_del_parametro` ASC, `clave_del_producto` ASC, `idcreditos_productos_otros_parametros` ASC)";
$sql["20170301"][]	= "UPDATE `general_estados` SET `clave_en_sic` = 'COAH' WHERE `idgeneral_estados` = '139'";
$sql["20170301"][]	= "ALTER TABLE `t_03f996214fba4a1d05a68b18fece8e71` ADD COLUMN `corporativo` INT(2) NULL DEFAULT '0' AFTER `alias` ";
$sql["20170301"][]	= "ALTER TABLE `sistema_permisos` ADD COLUMN `descripcion` VARCHAR(60) NULL DEFAULT '' AFTER `permisos`";
$sql["20170301"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('572', 'FORM', 'RECIBOS.AL.ELIMINAR.BACKUP_IMP', '', '', '', '')";
$sql["20170301"][]	= "INSERT INTO `sistema_programacion_de_avisos` (`idprograma`, `nombre_del_aviso`, `forma_de_creacion`, `programacion`, `destinatarios`, `microformato`, `tipo_de_medios`, `intent_check`, `intent_command`) VALUES ('14', 'RESPALDOAL ELIMINAR RECIBO', 'SYS_ALERTA_POR_EVENTO', 'DATA.ANTES.ELIMINAR.RECIBO', 'CORREO:|', '{mensaje}\r\n\r\n{original}', ',MAIL', '', 'rpt_formatos/recibo.rpt.php?recibo={idrecibo}&mail={mail}')";
$sql["20170301"][]	= "ALTER TABLE `general_niveles` CHANGE COLUMN `idgeneral_niveles` `idgeneral_niveles` INT(4) UNSIGNED NOT NULL , ADD COLUMN `tipo_sistema` INT(3) NULL DEFAULT 0 AFTER `taskspage` ";
$sql["20170301"][]	= "UPDATE `general_niveles` SET `tipo_sistema`=`idgeneral_niveles` WHERE `tipo_sistema`=0 ";
$sql["20170301"][]	= "UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET `corporativo` = '1' WHERE `idusuarios` = '99'";
$sql["20170301"][]	= "UPDATE `general_menu` SET `menu_title` = 'Datos de la Microfinanciera' WHERE `idgeneral_menu` = '7021' ";
$sql["20170301"][]	= "ALTER TABLE `sistema_permisos` CHANGE COLUMN `accion` `accion` VARCHAR(50) NULL DEFAULT '' ,CHANGE COLUMN `permisos` `denegado` VARCHAR(200) NULL DEFAULT '' ,ADD COLUMN `tipo_objeto` VARCHAR(20) NULL DEFAULT '' COMMENT 'FORM por ejemplo' AFTER `descripcion`,ADD COLUMN `nombre_objeto` VARCHAR(40) NULL DEFAULT '' AFTER `tipo_objeto`,DROP INDEX `idx` , ADD INDEX `idx` (`idsistema_permisos` ASC, `accion` ASC, `tipo_objeto` ASC, `nombre_objeto` ASC)";
$sql["20170301"][]	= "INSERT INTO `general_niveles` (`idgeneral_niveles`, `descripcion_del_nivel`, `task_events`, `work_time_range`, `rules_by_user`, `initpage`, `taskspage`, `tipo_sistema`) VALUES ('41', 'Cajero Sucursal', '', NULL, 'PUEDE_ELIMINAR_RECIBOS=true;', 'index.xul.php', 'utils/frm_calendar_tasks.php', '4')";
$sql["20170301"][]	= "INSERT INTO `general_niveles` (`idgeneral_niveles`, `descripcion_del_nivel`, `task_events`, `work_time_range`, `rules_by_user`, `initpage`, `taskspage`, `tipo_sistema`) VALUES ('71', 'Oficial Credito Sucursal', '', NULL, NULL, 'index.xul.php', 'utils/frm_calendar_tasks.php', '7')";
$sql["20170301"][]	= "INSERT INTO `general_niveles` (`idgeneral_niveles`, `descripcion_del_nivel`, `task_events`, `work_time_range`, `rules_by_user`, `initpage`, `taskspage`, `tipo_sistema`) VALUES ('81', 'Oficial Captacion Sucursal', '', NULL, NULL, 'index.xul.php', 'utils/frm_calendar_tasks.php', '8')";
$sql["20170301"][]	= "INSERT INTO `general_niveles` (`idgeneral_niveles`, `descripcion_del_nivel`, `task_events`, `work_time_range`, `rules_by_user`, `initpage`, `taskspage`, `tipo_sistema`) VALUES ('31', 'Administrativo Sucursal', '', NULL, NULL, 'index.xul.php', 'utils/frm_calendar_tasks.php', '3')";
$sql["20170301"][]	= "UPDATE `general_menu` SET `menu_rules`=CONCAT(`menu_rules`, ',31@rw') WHERE `menu_rules` LIKE '%,3@rw%' OR `menu_rules` LIKE '3@rw%'  AND (`menu_rules` NOT LIKE '%,31@rw%')";
$sql["20170301"][]	= "UPDATE `general_menu` SET `menu_rules`=CONCAT(`menu_rules`, ',41@rw') WHERE `menu_rules` LIKE '%,4@rw%' OR `menu_rules` LIKE '4@rw%'  AND (`menu_rules` NOT LIKE '%,41@rw%')";
$sql["20170301"][]	= "UPDATE `general_menu` SET `menu_rules`=CONCAT(`menu_rules`, ',71@rw') WHERE `menu_rules` LIKE '%,7@rw%' OR `menu_rules` LIKE '7@rw%'  AND (`menu_rules` NOT LIKE '%,71@rw%') ";
$sql["20170301"][]	= "UPDATE `general_menu` SET `menu_rules`=CONCAT(`menu_rules`, ',81@rw') WHERE (`menu_rules` LIKE '%,8@rw%' OR `menu_rules` LIKE '8@rw%') AND (`menu_rules` NOT LIKE '%,81@rw%')";
$sql["20170301"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (10005, 10010, 'Permisos de Informacion', 'frmsecurity/sistema-permisos.frm.php', 'Permisos de Informacion', 'fa-calendar-plus-o', 'command', '10005', '10005', 'true') ";
$sql["20170301"][]	= "ALTER TABLE `creditos_productos_promo` CHANGE `descuento` `descuento` FLOAT(6,2) DEFAULT 0.0000 NULL COMMENT 'Tasa de descuento'";
$sql["20170301"][]	= "INSERT INTO `tesoreria_tipos_de_pago` (`tipo_de_pago`, `tipo_de_movimiento`, `descripcion`, `descripcion_completa`, `equivalente_aml`, `activo`, `formato`, `eq_contable`) VALUES ('promocion', '0', 'Promocion', 'Operaciones Pagadas con Promociones', '0', '1', '', '99')";
$sql["20170301"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('1004', '1000', 'Cobrar Promociones', 'frmcaja/cobrar-promociones.frm.php', 'principal', 'Cobros de Promociones', 'fa-gift', '4@rw,9@rw,99@rw,5@rw,41@rw', 'command', '602', '1074', 'true')";

$sql["20170302"][]	= "ALTER TABLE `creditos_origenflujo` CHANGE COLUMN `idcreditos_origenflujo` `idcreditos_origenflujo` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT";
$sql["20170302"][]	= "ALTER TABLE `creditos_origenflujo` ADD INDEX `idxcofe` (`idcreditos_origenflujo` ASC, `tipo` ASC)";
$sql["20170302"][]	= "ALTER TABLE `creditos_flujoefvo` DROP PRIMARY KEY,ADD PRIMARY KEY (`idcreditos_flujoefvo`), ADD INDEX `idxfe` (`solicitud_flujo` ASC, `tipo_flujo` ASC, `origen_flujo` ASC, `periocidad_flujo` ASC, `idcreditos_flujoefvo` ASC, `socio_flujo` ASC)";

$sql["20170401"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('20101', '20100', 'Edicion Masiva de Personas', 'frmsocios/personas-editar-masivo.frm.php', 'principal', 'Edicion batch de personas', 'fa-object-group', '3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,41@rw,71@rw', 'command', '2011', '20101', 'true')";
$sql["20170401"][]	= "INSERT INTO `socios_tipoingreso` (`idsocios_tipoingreso`, `descripcion_tipoingreso`, `descripcion_detallada`, `estado`) VALUES ('99', 'DESCONOCIDO', 'Tipo de Ingreso no Asignado', '0')";



$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('11', 'Ganaderia, Agricultura, Aprovecghamiento Forestal, Pesca', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('21', 'Mineria', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('22', 'Generacion, transporte y Distribucion de energ´ia, Agua y Gas al Consumir Final', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('23', 'Construccion', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('31', 'Industrias Manufactureras', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('32', 'Industrias Manufactureras', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('33', 'Industrias Manufactureras', 'scian,mx')";
$sql["20170402"][]	= "ALTER TABLE `socios_aeconomica_sector` CHANGE COLUMN `idsocios_aeconomica_sector` `idsocios_aeconomica_sector` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT ,CHANGE COLUMN `descripcion_aeconomica_sector` `descripcion_aeconomica_sector` VARCHAR(100) NOT NULL DEFAULT '' ,ADD COLUMN `tags` VARCHAR(20) NULL AFTER `descripcion_aeconomica_sector`, ADD INDEX `idxs` (`idsocios_aeconomica_sector` ASC)";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('43', 'Comercio al por Menor', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('46', 'Comercio al por Mayor', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('48', 'Transportes, Correo y Almacenamiento', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('49', 'Transportes, Correo y Almacenamiento', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('51', 'Informacion en Medios Masivos', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('52', 'Servicios Financieros y de Seguros', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('53', 'Servicios Inmobiliarios y renta de intangibles', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('54', 'Servicios Profesionales, Cientificos y Tecnicos', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('55', 'Corporativos', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('56', 'Servicios de Apoyo a Negocios, manejo de residuos', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('61', 'Servicios Educativos', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('62', 'Servicios de Salud y de Asistencia Social', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('71', 'Servicios de Hospedaje, Preparacion de Alimentos', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('81', 'Otros servicios, excepto gubernamentales', 'scian,mx')";
$sql["20170402"][]	= "INSERT INTO `socios_aeconomica_sector` (`idsocios_aeconomica_sector`, `descripcion_aeconomica_sector`, `tags`) VALUES ('93', 'Actividades Gubernamentales', 'scian,mx')";
$sql["20170402"][]	= "ALTER TABLE `personas_ae_scian` CHANGE COLUMN `nombre_de_la_actividad` `nombre_de_la_actividad` VARCHAR(200) NULL DEFAULT '' ,CHANGE COLUMN `clasificacion` `clasificacion` VARCHAR(20) NULL DEFAULT '' ,CHANGE COLUMN `clave_aml` `clave_aml` VARCHAR(20) NULL DEFAULT '9999999' COMMENT 'clave relacionada en AML' ,ADD COLUMN `sector` INT(3) NULL DEFAULT '81' AFTER `clave_aml`, ADD INDEX `icdsx` (`clave_de_actividad` ASC, `clave_aml` ASC, `sector` ASC, `clave_interna` ASC)";

$sql["20170402"][]	= "UPDATE `personas_ae_scian` SET `sector` = SUBSTR(`clave_de_actividad`,1,2)";
$sql["20170402"][]	= "UPDATE `personas_actividad_economica_tipos`  SET `scian` = 812990  WHERE ISNULL(`scian` ) OR `scian`=0";
$sql["20170402"][]	= "UPDATE `socios_aeconomica` SET `tipo_aeconomica`= 99 WHERE `tipo_aeconomica` < 10";
$sql["20170402"][]	= "UPDATE `socios_aeconomica` SET `clave_scian`= (SELECT `scian` FROM `personas_actividad_economica_tipos` WHERE `personas_actividad_economica_tipos`.`clave_de_actividad`=`socios_aeconomica`.`tipo_aeconomica` LIMIT 0,1) WHERE ISNULL(`clave_scian`) OR `clave_scian`= 0";
$sql["20170402"][]	= "UPDATE `socios_aeconomica` SET `tipo_aeconomica`= (SELECT `personas_actividad_economica_tipos`.`clave_de_actividad` FROM `personas_actividad_economica_tipos` WHERE `clave_interna`=`socios_aeconomica`.`tipo_aeconomica` LIMIT 0,1) WHERE `tipo_aeconomica` <= 100";
$sql["20170402"][]	= "UPDATE `socios_aeconomica` SET `clave_scian`= 812990 WHERE ISNULL(`clave_scian`) OR `clave_scian`<=0";
$sql["20170402"][]	= "UPDATE `socios_aeconomica` SET  `sector_economico`=(SELECT `sector` FROM `personas_ae_scian` WHERE `personas_ae_scian`.`clave_de_actividad`=`socios_aeconomica`.`clave_scian` LIMIT 0,1)";
$sql["20170402"][]	= "UPDATE `socios_aeconomica` SET `antiguedad_ae`=365 WHERE `antiguedad_ae`<365";
$sql["20170402"][]	= "UPDATE `socios_aeconomica` SET `fecha_de_ingreso`= DATE_SUB(`fecha_alta`, INTERVAL 365 DAY) WHERE ISNULL(`fecha_de_ingreso`) OR `fecha_de_ingreso`='0000-00-00'";

$sql["20170404"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('21102', 'Creditos.- Purgar Montos', 'Elimina montos duplicados')";
$sql["20170404"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('21103', 'Creditos.- Purgar Saldos', 'Cuadra Operaciones y Genera saldos en montos')";
$sql["20170404"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('9002', 'Sistema.- Cambia de MyISAM a INNODB', '')";

$sql["20170404"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/reporte-vintage.rpt.php?', 'Creditos.- Vintage', 'general_creditos', '10008', 'Reporte Vintage de Creditos', '10008')";
$sql["20170404"][]	= "ALTER TABLE `socios_aeconomica_sector` CHANGE COLUMN `idsocios_aeconomica_sector` `idsocios_aeconomica_sector` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT ,CHANGE COLUMN `descripcion_aeconomica_sector` `descripcion_aeconomica_sector` VARCHAR(100) NOT NULL DEFAULT '' ,ADD COLUMN `tags` VARCHAR(20) NULL AFTER `descripcion_aeconomica_sector`, ADD INDEX `idxs` (`idsocios_aeconomica_sector` ASC)";
$sql["20170404"][]	= "UPDATE `socios_aeconomica_sector` SET `tags`='' WHERE ISNULL(`tags`)";

$sql["20170404"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('573', 'FORM', 'PERSONAS.ACTIVIDAD_ECONOMICA.SIN_SCIAN', '', '', '', '')";
$sql["20170404"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `tags`) VALUES ('311', 'Comprobante Domicilio Fiscal', 'DG', 'originacion,todas,pf,pm')";
$sql["20170404"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `tags`) VALUES ('501', 'Ultimos 3 Estados de Cta. Bancarios', 'DG', 'originacion,todas,pf,pm'); ";
$sql["20170404"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `tags`) VALUES ('502', 'Formato Conoce a tu Cliente', 'DG', 'originacion,todas,pf,pm')";
$sql["20170404"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `tags`) VALUES ('233', 'Estados Financieros De Ejercicio Ant.', 'IPM', 'originacion,todas,pm')";
$sql["20170404"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `tags`) VALUES ('234', 'Estados Fin. c/Analiticas 3 Meses Ants.', 'IPM', 'originacion,todas,pm')";
$sql["20170404"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `tags`) VALUES ('235', 'Carta Autorizacion Consulta Buro', 'DG', 'originacion,todas,pm,pf')";
$sql["20170404"][]	= "ALTER TABLE `socios_relaciones` CHANGE COLUMN `nombres` `nombres` VARCHAR(100) NOT NULL DEFAULT '' ,CHANGE COLUMN `apellido_paterno` `apellido_paterno` VARCHAR(40) NOT NULL DEFAULT '' ,CHANGE COLUMN `apellido_materno` `apellido_materno` VARCHAR(40) NOT NULL DEFAULT '' ,CHANGE COLUMN `telefono_residencia` `telefono_residencia` VARCHAR(20) NULL DEFAULT '' ,CHANGE COLUMN `telefono_movil` `telefono_movil` VARCHAR(20) NULL DEFAULT '' ,CHANGE COLUMN `observaciones` `observaciones` VARCHAR(80) NULL DEFAULT NULL ,CHANGE COLUMN `idusuario` `idusuario` INT(6) NULL DEFAULT '99' ,CHANGE COLUMN `dependiente` `dependiente` INT(2) UNSIGNED NOT NULL DEFAULT '2' COMMENT '1=SI 2=NO' ,CHANGE COLUMN `sucursal` `sucursal` VARCHAR(10) NULL DEFAULT 'matriz' ,CHANGE COLUMN `eacp` `eacp` VARCHAR(12) NULL DEFAULT 'EN_TRAMITE'";

$sql["20170404"][]	= "CALL `proc_creditos_abonos_totales`()";


$sql["20170501"][]	= "ALTER TABLE `aml_alerts` ADD COLUMN `envio_rms` INT(10) NULL DEFAULT '0' AFTER `usuario_checking`";
$sql["20170501"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('574', 'SYSTEM', 'AML.RIESGO_AUTOENVIAR_RMS', '', '', '', '', '0')";

$sql["20170502"][]	= "ALTER TABLE `leasing_tasas` ADD COLUMN `tasa_marginal` FLOAT(6,4) NULL DEFAULT '0' AFTER `comision_apertura`";
$sql["20170502"][]	= "ALTER TABLE `originacion_leasing` CHANGE COLUMN `residuales` `residuales` VARCHAR(60) NULL DEFAULT '' COMMENT 'resiidual formato PERIODICIDAD separador  PLAZO separador TASA coma' ,ADD COLUMN `mail` VARCHAR(25) NULL DEFAULT '' AFTER `residuales`,ADD COLUMN `tel` VARCHAR(15) NULL DEFAULT '' AFTER `mail`";
$sql["20170502"][]	= "UPDATE `creditos_periocidadpagos` SET `descripcion_periocidadpagos` = 'DIARIO' WHERE `idcreditos_periocidadpagos` = '1' ";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('8', '1', 'Promotor', 'leasing_bonos_dest')";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('9', '2', 'Regional', 'leasing_bonos_dest')";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('10', '3', 'Referenciador', 'leasing_bonos_dest')";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('11', '4', 'Director de Promoción', 'leasing_bonos_dest')";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('12', '5', 'Director de Alianzas', 'leasing_bonos_dest') ";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('13', '6', 'Personal ABC Leasing', 'leasing_bonos_dest')";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('14', '7', 'Gastos de Operación', 'leasing_bonos_dest')";
$sql["20170502"][]	= "CREATE TABLE IF NOT EXISTS `leasing_bonos` (`idleasing_bonos` INT NOT NULL AUTO_INCREMENT,  `clave_leasing` INT NULL DEFAULT 0,  `tipo_bono` INT(4) NULL DEFAULT 0,  `tipo_destino` INT(6) NULL DEFAULT 0 COMMENT 'ejecutivo regional gerente',  `tasa_bono` FLOAT(6,4) NULL DEFAULT 0,  `monto_bono` DOUBLE(12,2) NULL DEFAULT 0,  `fecha` DATE NULL DEFAULT '2017-01-01',  `fecha_de_pago` DATE NULL DEFAULT '2017-01-01',  PRIMARY KEY (`idleasing_bonos`),  INDEX `dicx23` (`clave_leasing` ASC, `idleasing_bonos` ASC)) ENGINE = INNODB";
$sql["20170502"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('15', '8', 'Ejecutivo Int.', 'leasing_bonos_dest') ";
$sql["20170502"][]	= "ALTER TABLE `creditos_productos_req` CHANGE COLUMN `etapa` `etapa` VARCHAR(40) NULL DEFAULT '' COMMENT 'multiples etapas primarias' ,ADD COLUMN `etapa_id` INT(11) NULL DEFAULT '0' COMMENT 'etapa especifica' AFTER `clave`";
$sql["20170502"][]	= "ALTER TABLE `creditos_productos_req` CHANGE COLUMN `producto` `producto` INT(8) NULL DEFAULT 0 , CHANGE COLUMN `tipo_req` `tipo_req` INT(4) NULL DEFAULT 1 COMMENT 'Tipo de requerimiento 1 documental 2 procesal' , ADD COLUMN `opcional` INT(2) NULL DEFAULT '0' COMMENT '0 no 1 si' AFTER `etapa_id`";

$sql["20170601"][]	= "UPDATE `personas_documentacion_tipos` SET `checklist` = 'entregaa' WHERE `clave_de_control` = '210'";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('575', 'FORM', 'PERSONAS.USAR.CHECKLIST_DINAMICO', '', '', '', '', '1')";
$sql["20170601"][]	= "CREATE TABLE IF NOT EXISTS `leasing_activos` (  `idleasing_activos` INT NOT NULL AUTO_INCREMENT,  `clave_leasing` INT NULL,  `persona` BIGINT(20) NULL,  `credito` BIGINT(20) NULL,  `descripcion` VARCHAR(60) NULL,  `proveedor` BIGINT(20) NULL DEFAULT '1' COMMENT 'clave de persona de proveedor',  `fecha_compra` DATE NULL,  `fecha_registro` DATE NULL,  `fecha_mtto` DATE NULL COMMENT 'Fecha de proximo mtto',  `fecha_seguro` DATE NULL COMMENT 'Fecha de proximo seguro',  `tipo_activo` INT NULL,  `tipo_seguro` INT(4) NULL COMMENT 'cobertura amplia',  `tasa_depreciacion` FLOAT(6,3) NULL DEFAULT '0',  `valor_nominal` DOUBLE(18,2) NULL DEFAULT '0',  `serie` VARCHAR(20) NULL,  `factura` VARCHAR(20) NULL,  `placas` VARCHAR(20) NULL,  `motor` VARCHAR(20) NULL COMMENT 'numero de serie del motor',  `marca` INT(6) NULL DEFAULT '0',  `color` VARCHAR(25) NULL DEFAULT '',  PRIMARY KEY (`idleasing_activos`)) ENGINE = InnoDB";
$sql["20170601"][]	= "ALTER TABLE `creditos_productos_req` DROP COLUMN `opcional` ";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_title` = 'Tasas del Cotizador' WHERE `idgeneral_menu` = '30412'";
$sql["20170601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (30413, 3040, 'Flotilla', 'frmarrendamiento/leasing-activos.frm.php', 'Activos', 'fa-car', 'command', '30413', '30413', 'true') ";
$sql["20170601"][]	= "ALTER TABLE `leasing_activos` ADD COLUMN `valor_venta` DOUBLE(18,2) NULL DEFAULT '0' AFTER `color`,ADD COLUMN `valor_residual` DOUBLE(18,2) NULL DEFAULT '0' AFTER `valor_venta` ";
$sql["20170601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (2063, 2060, 'Proveedores', 'frmsocios/personas-proveedores.frm.php', 'Proveedores', 'fa-building', 'command', '2063', '2063', 'true') ";


$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('16', '100', 'Vehiculos Automotores', 'leasing_activos_tipos')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('17', '101', 'Vehiculos Accesorios', 'leasing_activos_tipo')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('18', '100', 'Seguro Anual', 'leasing_seguro_tipo')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('19', '99', 'Seguro No Pagado', 'leasing_seguro_tipo')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('20', '101', 'Seguro bianual', 'leasing_seguro_tipo')";

$sql["20170601"][]	= "ALTER TABLE `leasing_activos` ADD COLUMN `monto_anticipo` DOUBLE(18,2) NULL DEFAULT '0.00' AFTER `valor_residual`,ADD COLUMN `aseguradora` BIGINT(20) NULL DEFAULT 1 AFTER `monto_anticipo`";
$sql["20170601"][]	= "CREATE TABLE IF NOT EXISTS `personas_aseguradoras` ( `idpersonas_aseguradoras` INT NOT NULL AUTO_INCREMENT,  `persona` BIGINT(20) NULL,  `alias` VARCHAR(40) NULL,  PRIMARY KEY (`idpersonas_aseguradoras`)) ENGINE = INNODB";
$sql["20170601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (2064, 2060, 'Aseguradoras', 'frmsocios/personas-aseguradoras.frm.php', 'Aseguradoras', 'fa-building', 'command', '2064', '2064', 'true') ";
$sql["20170601"][]	= "ALTER TABLE `leasing_activos` ADD COLUMN `status` INT(4) NULL DEFAULT '1' COMMENT '1 Activo' AFTER `aseguradora`,ADD COLUMN `baja_id` INT(4) NULL DEFAULT '0' COMMENT 'clave de baja' AFTER `status`,ADD COLUMN `baja_fecha` DATE NULL DEFAULT '2019-12-01' AFTER `baja_id` ";
$sql["20170601"][]	= "CREATE TABLE IF NOT EXISTS `leasing_rentas` ( `idleasing_renta` INT NOT NULL,  `clave_leasing` INT NULL,  `credito` BIGINT(20) NULL,  `periodo` INT(4) NULL,  `fecha` DATE NULL DEFAULT '2017-01-01',  `deducible` DOUBLE(18,2) NULL DEFAULT '0',  `no_deducible` DOUBLE(18,2) NULL DEFAULT '0',  `iva_ded` DOUBLE(18,2) NULL DEFAULT '0',  `iva_no_ded` DOUBLE(18,2) NULL DEFAULT '0',  `total` DOUBLE(18,2) NULL DEFAULT '0',  `fecha_max` DATE NULL,  `clave_no_ded` INT(8) NULL DEFAULT 99 COMMENT 'id de movimiento no deducible',  `fecha_pago` DATE NULL DEFAULT '2017-01-01',  `recibo_pago` BIGINT(20) NULL DEFAULT '1',  PRIMARY KEY (`idleasing_renta`)) ENGINE = INNODB ";
$sql["20170601"][]	= "ALTER TABLE `leasing_rentas` CHANGE COLUMN `idleasing_renta` `idleasing_renta` INT(11) NOT NULL AUTO_INCREMENT";

$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('598', '2601', '414', 'Conceptos en Desglose')";

$sql["20170601"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) values('173','PAGO ACCESORIOS LEASING','0','0','\$cuenta  = CUENTA_DE_CUADRE;','PAGO ACCESORIOS LEASING','99','9303','1','1','99','1','0','0','0','0','0','0','','ninguna','0','0','','','0','0','0.160','ACCS LEASING','1','0.00')";
$sql["20170601"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) values('174','PAGO GTIA EXTENDIDA','0','0','\$cuenta  = CUENTA_DE_CUADRE;','PAGO GTIA EXTENDIDA','99','174','1','1','99','1','0','0','0','0','0','0','','ninguna','0','0','','','0','0','0.160','GTIA EXT LEAS','1','0.00')";


$sql["20170601"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES ('246', 'PAGO DE CARGOS VARIOS', '0', '0', '\$cuenta = \"3110002001\";', '', '99', '146', '1', '1', '99', '0', '0', '0', '0', '1', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0.160', 'PAGO C VARIOS', '1', '0.00')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('589','2002','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('590','7003','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('591','10000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('592','10001','246','1.0000','PAGO DE COM. VARIAS 2','10001')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('593','1000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('594','30200','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('595','15000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('596','11000','246','1.0000','PAGO DE COM. VARIAS 2','0')";
$sql["20170601"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('597','1001','246','1.0000','PAGO DE COM. VARIAS 2','0')";


$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('599', '1001', '157', 'Pago Seguro Vehiculo')";
$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('600', '1001', '171', 'Pago Tenencia')";
$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('601', '1001', '172', 'Pago Accesorios leas')";
$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('602', '1001', '173', 'Pago Acc leasing')";
$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('603', '1001', '174', 'Pago Gtia ext')";
$sql["20170601"][]	= "ALTER TABLE `leasing_rentas` ADD COLUMN `suma_pagos` DOUBLE(18,2) NULL DEFAULT '0' AFTER `recibo_pago`";

$sql["20170601"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES('175','PAGO RENTA PROP','0','0','\$cuenta  = CUENTA_DE_CUADRE;','PAGO RENTA PROP','99','175','1','1','99','1','0','0','0','0','0','0','','ninguna','0','0','','','0','0','0.160','PAGO RENT PROP','1','0.00')";
$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('604', '1001', '175', 'Pago Renta Prop')";
$sql["20170601"][]	= "ALTER TABLE `leasing_tasas` ADD COLUMN `tasa_vec` FLOAT(6,4) NULL DEFAULT '0.00' AFTER `tasa_marginal`";

$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_title` = 'Lista de Cotizaciones' WHERE `idgeneral_menu` = '3041' ";

$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '3'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '11'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '12'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '13'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '14'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '21'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '22'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '23'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '70'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '96'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '97'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '98'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '551'";
$sql["20170601"][]	= " UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '552'";

$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('576', 'FORM', 'CREDITOS.ARRENDAMIENTO.RESIDUAL_CON_ANT', '', '', '', '', '1')";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('577', 'FORM', 'CREDITOS.ARRENDAMIENTO.RESIDUAL_CON_IVA', '', '', '', '')";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('578', 'FORM', 'CREDITOS.ARRENDAMIENTO.NO_USAR_TIIE', '', '', '', '', '1')";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('579', 'FORM', 'CREDITOS.ARRENDAMIENTO.IVA_NO_INC', '', '', '', '')";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmseguimiento/configurar-alertas.frm.php' , `menu_description` = 'Lista de Alertas Configurados' , `menu_image` = 'fa-bell' , `menu_help_id` = '40100' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '40101'";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_parent` = '3000' , `menu_title` = 'Autorizacion de Creditos' , `menu_file` = 'frmcreditos/autorizacion-de-creditos.frm.php' , `menu_image` = 'fa-list-ol' WHERE `idgeneral_menu` = '3016'";
$sql["20170601"][]	= "CREATE TABLE IF NOT EXISTS `creditos_rechazos_tipo` (  `idcreditos_rechazos_tipo` INT NOT NULL AUTO_INCREMENT,  `descripcion` VARCHAR(45) NULL,  PRIMARY KEY (`idcreditos_rechazos_tipo`)) ENGINE = INNODB";

$sql["20170601"][]	= " INSERT INTO `creditos_rechazos_tipo` (`idcreditos_rechazos_tipo`, `descripcion`) VALUES ('100', 'Mal Historia Crediticio')";
$sql["20170601"][]	= " INSERT INTO `creditos_rechazos_tipo` (`idcreditos_rechazos_tipo`, `descripcion`) VALUES ('101', 'Sobreendeudamiento')";
$sql["20170601"][]	= " INSERT INTO `creditos_rechazos_tipo` (`idcreditos_rechazos_tipo`, `descripcion`) VALUES ('102', 'Capacidad de Pago')";
$sql["20170601"][]	= " INSERT INTO `creditos_rechazos_tipo` (`idcreditos_rechazos_tipo`, `descripcion`) VALUES ('103', 'No cumple requisitos')";
$sql["20170601"][]	= " INSERT INTO `creditos_rechazos_tipo` (`idcreditos_rechazos_tipo`, `descripcion`) VALUES ('103', 'Malas referencias')";
$sql["20170601"][]	= " INSERT INTO `creditos_rechazos_tipo` (`idcreditos_rechazos_tipo`, `descripcion`) VALUES ('104', 'Malas referencias')";
$sql["20170601"][]	= " INSERT INTO `creditos_rechazos_tipo` (`idcreditos_rechazos_tipo`, `descripcion`) VALUES ('999', 'Otros no Listado')";

$sql["20170601"][]	= "ALTER TABLE `creditos_rechazados` ADD COLUMN `idusuario` INT(8) NULL DEFAULT '0' AFTER `claverechazo`";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmcreditos/creditos-por-ministrar.frm.php' WHERE `idgeneral_menu` = '1081'";
$sql["20170601"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('99378','9999','creditos-solicitados.rpt.php','creditos-solicitados.rpt.php','principal','NO_DESCRIPTION','null.png','2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw','general','0','9999','false')";
$sql["20170601"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('99379','9999','creditos-rechazados.frm.php','creditos-rechazados.frm.php','principal','NO_DESCRIPTION','null.png','2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw','general','0','9999','false')";
$sql["20170601"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('99380','9999','frmcreditosministracion.php','frmcreditosministracion.php','principal','NO_DESCRIPTION','null.png','2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw','general','0','9999','false')";
$sql["20170601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES('99381','9999','frmcreditoslineas.php','frmcreditoslineas.php','principal','NO_DESCRIPTION','null.png','2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw','general','0','9999','false')";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmcreditos/creditos-lineas.frm.php' , `menu_image` = 'fa-braille' WHERE `idgeneral_menu` = '3021' ";


//$sql["20170601"][]	= "UPDATE `creditos_tipoconvenio` SET `php_monto_maximo` = '\$monto_maximo = \$monto_linea_credito;' WHERE `idcreditos_tipoconvenio` = '300'";

$sql["20170601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES('99382','9999','bancos_alta_a_cuentas.frm.php','bancos_alta_a_cuentas.frm.php','principal','NO_DESCRIPTION','null.png','2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw','general','0','9999','false')";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_title` = 'Cuentas Bancarias' , `menu_file` = 'frmbancos/lista-de-cuentas-bancarias.frm.php' , `menu_image` = 'fa-university' WHERE `idgeneral_menu` = '9002' ";
$sql["20170601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES('99383','9999','cuentas-bancarias.edit.frm.php','cuentas-bancarias.edit.frm.php','principal','NO_DESCRIPTION','null.png','2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw','general','0','9999','false')";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_title` = 'Catalogo de Empleadores' , `menu_image` = 'fa-building-o' WHERE `idgeneral_menu` = '2052' ";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('4502', '200', 'FORMATO PIE PAGINA ENVIO RETENCIONES', '<!-- contenido -->') ";
$sql["20170601"][]	= "CREATE TABLE IF NOT EXISTS `creditos_sic_notas` ( `idcreditos_sic_notas` INT NOT NULL AUTO_INCREMENT,  `credito` BIGINT(20) NOT NULL,  `clave_nota` VARCHAR(4) NULL,  `texto_nota` VARCHAR(50) NULL, `estatus` INT(4) NULL DEFAULT '0' COMMENT 'Estado Actual forzado', PRIMARY KEY (`idcreditos_sic_notas`)) ENGINE = INNODB";
$sql["20170601"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES (4033, 4000, 'Notas SIC', 'frmcreditos/nota-sic.frm.php', 'Notas SIC', 'fa-bug', 'command', '4033', '4033', 'true') ";


$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('21', 'IA', 'Cuenta Inactiva', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('22', 'IM', 'Integrante Causante de Mora', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('23', 'LC', 'Finiquito Menor al Acordado', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('24', 'LO', 'En Localizacion', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('25', 'PC', 'Cuenta en Cobranza', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('26', 'RI', 'Robo de Identidad', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('27', 'RF', 'Resolucion Jud. a fav cliente', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('28', 'RV', 'Cuenta Reest por Cliente', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('29', 'SG', 'Demandado', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('30', 'UP', 'Cuenta en quebranto', 'catalogo_notas_sic')";
$sql["20170601"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('31', 'VR', 'Dacion en Pago o Renta', 'catalogo_notas_sic')";

$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('580', 'FORM', 'CREDITOS.ARRENDAMIENTO.NO_RESIDUALES', '', '', '', '', '1')";

$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1906', '200', 'Arrendamiento.- Contrato 1', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1906')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1907', '200', 'Arrendamiento.- Pagare Rentas', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1907')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1908', '200', 'Arrendamiento.- Carta Poder', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1908')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1909', '200', 'Arrendamiento.- Carta Factura', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1909')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1910', '200', 'Arrendamiento.- Pagare Residual', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1910')";
$sql["20170601"][]	= "ALTER TABLE `leasing_activos` ADD COLUMN `serie_nal` VARCHAR(20) NULL DEFAULT '' COMMENT 'Serie Nacional Repuve' AFTER `baja_fecha` ";

$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1911', '200', 'Arrendamiento.- Caratula', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1911')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1912', '200', 'Arrendamiento.- Obligados Sol', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1912')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1913', '200', 'Arrendamiento.- Depositario', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1913')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1914', '200', 'Arrendamiento.- Tabla rentas', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1914')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1915', '200', 'Arrendamiento.- Entrega', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1915')";

$sql["20170601"][]	= "ALTER TABLE `personas_datos_colegiacion` ADD COLUMN `dato1` VARCHAR(40) NULL DEFAULT '' COMMENT 'vendedor' AFTER `numero_de_colegiacion` ";
$sql["20170601"][]	= "ALTER TABLE `general_structure` CHANGE COLUMN `tab_num` `tab_num` VARCHAR(20) NULL DEFAULT '' COMMENT 'ID DE TAB' ";
$sql["20170601"][]	= " INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('1916', '200', 'Arrendamiento.- Info Cliente', '<!-- contenido -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1916')";

$sql["20170601"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `cuota_iva` DOUBLE(18,2) NULL DEFAULT '0.00' AFTER `tel` ";
$sql["20170601"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES('176','PAGO IVA RENTAS','0','0','\$cuenta  = CUENTA_DE_CUADRE;','PAGO IVA RENTAS','99','176','1','1','99','1','0','0','0','0','0','0','','ninguna','0','0','','','0','0','0.160','UVA RENTAS','1','0.00')";
$sql["20170601"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('605', '1001', '176', 'Iva de Rentas')";
$sql["20170601"][]	= "CREATE TABLE IF NOT EXISTS `leasing_tramites_cat` ( `idleasing_tramites_cat` INT NOT NULL AUTO_INCREMENT,  `nombre_tramite` VARCHAR(50) NULL,  PRIMARY KEY (`idleasing_tramites_cat`)) ENGINE = INNODB";
$sql["20170601"][]	= "CREATE TABLE IF NOT EXISTS  `creditos_firmantes` (  `idcreditos_firmantes` INT NOT NULL AUTO_INCREMENT,  `credito` BIGINT(20) NULL,  `persona` BIGINT(20) NULL,  `rol_firmante` VARCHAR(50) NULL,  PRIMARY KEY (`idcreditos_firmantes`)) ENGINE = INNODB";

$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('100', 'Emplacamiento')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('101', 'Tramite de Placas')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('102', 'Reporte de Extravio')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('103', 'Reporte de Robo de Placas')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('104', 'Recuperacion del vehiculo')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('105', 'Pago de Multas')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('106', 'Pago de Sanciones')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('107', 'Solicitud de Re-expedicion de Tarjeta de Circ')";
$sql["20170601"][]	= "INSERT INTO `leasing_tramites_cat` (`idleasing_tramites_cat`, `nombre_tramite`) VALUES ('108', 'Refrendo Vehicular')";

$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('8005', '200', 'Ficha de Firmantes 1', '<!-- contenido -->', '', '')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('8006', '200', 'Ficha de Firmantes 2', '<!-- contenido -->', '', '')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('8007', '200', 'Ficha de Firmantes 3', '<!-- contenido -->', '', '')";
$sql["20170601"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`,`tags`,`ruta`) VALUES ('8008', '200', 'Ficha de Firmantes 4', '<!-- contenido -->', '', '')";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('581', 'FORM', 'CREDITOS.ARRENDAMIENTO.DIV_ANTICIPO', '', '', '', '')";
$sql["20170601"][]	= "INSERT INTO `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) VALUES ('3', 'FIAT')";
$sql["20170601"][]	= "INSERT INTO `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) VALUES ('4', 'Volkswagen')";
$sql["20170601"][]	= "INSERT INTO `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) VALUES ('5', 'Nissan')";
$sql["20170601"][]	= "INSERT INTO `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) VALUES ('6', 'Chevrolet')";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`) VALUES ('582', 'FORM', 'CREDITOS.ARRENDAMIENTO.DISABLE_FLD', '', '', '')";
$sql["20170601"][]	= "INSERT INTO `general_formulas` (`aplicado_a`, `estructura_de_la_formula`, `code_type`, `description_short`) VALUES ('js_leasing_cot_vars', '', 'js', 'Variables que Cambian los valores')";
$sql["20170601"][]	= "ALTER TABLE `sistema_eliminados` ADD COLUMN `persona` BIGINT(20) NULL DEFAULT '0' AFTER `tiempo`";
$sql["20170601"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('900117', 'OPERACION_COM_CON_IVA', 'Las comisiones y pagos deben llevar Impuestos')";
$sql["20170601"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200916', 'CREDITO_FECHA_MIN_NO_EQ', 'La fecha de hoy y ministracion no son iguales')";

$sql["20170601"][]	= "UPDATE  `general_menu` SET `menu_title` = 'Lista de Usuarios' WHERE `idgeneral_menu` = '10004' ";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('583', 'FORM', 'CREDITOS.PAGOS_LETRA_FIJA', '', '', '', '', '1')";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('584', 'FORM', 'CREDITOS.ARRENDAMIENTO.FRM_RESIPLE', '', '', '', '', '1')";
$sql["20170601"][]	= "UPDATE `general_estados` SET `nombre` = 'CIUDAD DE MEXICO' WHERE `clave_alfanumerica` = 'DF' ";
$sql["20170601"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('1003', 'PERSONA_YA_EXISTE', 'La persona existe en el Sistema') ";

$sql["20170601"][]	= "UPDATE `socios_viviendatipo` SET `descripcion_viviendatipo` = 'PARTICULAR' WHERE `idsocios_viviendatipo` = '1' ";
$sql["20170601"][]	= "UPDATE `socios_viviendatipo` SET `descripcion_viviendatipo` = 'FISCAL' WHERE `idsocios_viviendatipo` = '2' ";
$sql["20170601"][]	= "UPDATE `socios_viviendatipo` SET `descripcion_viviendatipo` = 'LABORAL' WHERE `idsocios_viviendatipo` = '3' ";
$sql["20170601"][]	= "ALTER TABLE `socios_vivienda` ADD COLUMN `construye` INT(2) NULL DEFAULT '0' COMMENT 'En construccion' AFTER `clave_de_entidadfederativa` ";

$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('585', 'SYSTEM', 'RN_USAR_REDIRECTS', '', '', '', '', '1')";
$sql["20170601"][]	= "UPDATE `general_menu` SET `menu_title` = 'Empresas' WHERE `idgeneral_menu` = '2060' ";
$sql["20170601"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('586', 'SYSTEM', 'RN_USAR_USAR_MENU_ALTER', '', '', '', '', '0')";



$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-cubes' WHERE `idgeneral_menu` = '1010'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_title` = 'Empleadores' WHERE `idgeneral_menu` = '2052'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-share' WHERE `idgeneral_menu` = '1067'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-reply' WHERE `idgeneral_menu` = '1063'";

$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-user-circle-o' WHERE `idgeneral_menu` = '2001'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-users' WHERE `idgeneral_menu` = '20002'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-user' WHERE `idgeneral_menu` = '2003'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-cubes' WHERE `idgeneral_menu` = '20100'";

$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' WHERE `idgeneral_menu` = '3040'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-id-card' WHERE `idgeneral_menu` = '3002'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-calendar' WHERE `idgeneral_menu` = '3004'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-address-card-o' WHERE `idgeneral_menu` = '3001'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' WHERE `idgeneral_menu` = '10000'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_order` = '10000' WHERE `idgeneral_menu` = '10000'";

$sql["20170901"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `vecs` VARCHAR(60) NULL DEFAULT '' AFTER `cuota_iva`, ADD COLUMN `tasas` VARCHAR(60) NULL DEFAULT '' COMMENT 'tasas modificadas' AFTER `vecs`";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' WHERE `idgeneral_menu` = '3040'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-id-card' WHERE `idgeneral_menu` = '3002'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-calendar' WHERE `idgeneral_menu` = '3004'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-address-card-o' WHERE `idgeneral_menu` = '3001'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' WHERE `idgeneral_menu` = '10000'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_order` = '10000' WHERE `idgeneral_menu` = '10000'";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200917', 'CREDITO_FALLA_ORG', 'El Credito no tiene Origen Valido')";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200918', 'CREDITO_FALLA_O_ARR', 'La cotizacion de Arrendamiento no existe!.')";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200919', 'CRED_ARRED_NOACT', 'El Activo y/o vehiculo no existe!.')";
$sql["20170901"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `montoajuste` DOUBLE(18,2) NULL DEFAULT '0' AFTER `tasas`";
$sql["20170901"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('587', 'SYSTEM', 'CREDITOS.ARRENDAMIENTO.AJUSTESERVS', '', '', '6000', '')";
$sql["20170901"][]	= "ALTER TABLE `creditos_montos` ADD COLUMN `ints_tot_calc` DOUBLE(16,2) NULL DEFAULT '0.00' AFTER `sdo_exig_act`";
$sql["20170901"][]	= "INSERT INTO `vehiculos_usos` (`idvehiculos_usos`, `descripcion_uso`, `limitededucible`) VALUES ('201', 'Utilitario', '1000000000.00')";

$sql["20170901"][]	= "DELETE FROM vehiculos_marcas";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('1','Acura')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('2','Alfa Romeo')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('3','Audi')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('4','Benz Camiones')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('5','Bmw')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('6','Buick')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('7','Cadillac')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('8','Chevrolet')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('9','Chrysler')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('10','Dodge')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('11','Faw')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('12','Ferrari')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('13','Fiat')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('14','Ford')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('15','Foton')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('16','General Motors')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('17','Hino')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('18','Honda')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('19','Hyundai')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('20','Infiniti')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('21','International')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('22','Isuzu')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('23','Jaguar')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('24','Jeep')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('25','Land Rover')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('26','Lexus')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('27','Lincoln')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('28','Maserati')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('29','Mazda')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('30','Mercedes Benz')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('31','Mercury')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('32','Mini')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('33','Mitsubishi')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('34','Nissan')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('35','Peugeot')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('36','Pontiac')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('37','Porsche')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('38','Renault')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('39','Saab')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('40','Seat')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('41','Smart')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('42','Subaru')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('43','Suzuki')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('44','Toyota')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('45','Volkswagen')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('46','Volvo')";
$sql["20170901"][]	= "insert into `vehiculos_marcas` (`idvehiculos_marcas`, `nombre_marca`) values('99','Marca fuera de catalogo')";
$sql["20170901"][]	= "ALTER TABLE  `originacion_leasing` CHANGE COLUMN `mail` `mail` VARCHAR(50) NULL DEFAULT '' ";

$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('510111', 'ALERTA_ENVIADO_RMS', 'La Alerta se envio al RMS')";
$sql["20170901"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla`='socios_aecomica' AND `campo`='localidad_ae' ";
$sql["20170901"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla`='socios_aecomica' AND `campo`='municipio_ae' ";
$sql["20170901"][]	= "UPDATE `general_structure` SET `control` = 'hidden' WHERE `tabla`='socios_aecomica' AND `campo`='estado_ae' ";
$sql["20170901"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('588', 'SYSTEM', 'PERSONAS.LISTA.IDENTIFICA_IFE', '', '', '210,2201', '', '1')";
$sql["20170901"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8009', '200', 'Ficha de Firmantes', '<!-- contenido -->')";
$sql["20170901"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8010', '200', 'Ficha de Avales 5', '<!-- contenido -->')";

$sql["20170901"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Ficha de Avales 1' WHERE `idgeneral_contratos` = '8001'";
$sql["20170901"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Ficha de Avales 2' WHERE `idgeneral_contratos` = '8002'";
$sql["20170901"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Ficha de Avales 3' WHERE `idgeneral_contratos` = '8003'";
$sql["20170901"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Ficha de Avales 4' WHERE `idgeneral_contratos` = '8004'";
$sql["20170901"][]	= "ALTER TABLE `general_menu` CHANGE COLUMN `menu_rules` `menu_rules` VARCHAR(200) NULL DEFAULT '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,31@rw,41@rw,71@rw' ";
$sql["20170901"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('11035', '11030', 'Cuadre de Recibos', 'frmoperaciones/recibos-cuadre.frm.php', 'Cuadre de Operaciones', 'fa-balance-scale', '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,31@rw,41@rw,71@rw', 'command', '11035', '11035', 'true')";
$sql["20170901"][]	= "ALTER TABLE `captacion_cuentas` CHANGE COLUMN `eacp` `eacp` VARCHAR(15) NOT NULL DEFAULT '' ";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200920', 'CREDITO_FALTA_GTIALIQ', 'La Garantia Liquida debe existir')";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('200921', 'CREDITO_FALLA_GTIALIQ', 'La Garantia Liquida debe ser Insuficiente')";
$sql["20170901"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('1084', '1080', 'Garantia Liquida', 'frmcreditos/creditos.garantia-liquida.frm.php', 'Cobro de Garantia Liquida', 'fa-bookmark', '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,31@rw,41@rw,71@rw', 'command', '1084', '1084', 'true'); ";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-money' WHERE `idgeneral_menu` = '1083'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-money' WHERE `idgeneral_menu` = '1081'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-minus-square' WHERE `idgeneral_menu` = '1082'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_description` = 'Cobro de Comisiones a Creditos' WHERE `idgeneral_menu` = '1082'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-plus-square' WHERE `idgeneral_menu` = '1083'"; 



$sql["20170901"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES ('177', 'PAGO DE GTOS LEGALES', '0', '0', '\$cuenta = \"\";', '', '99', '147', '1', '1', '99', '0', '0', '0', '0', '1', '0', '0', '', 'ninguna', '0', '0', '', '', '0', '0', '0.160', 'PAGO GTOS LEGAL', '1', '0.00')";

$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('606','2002','177','1.0000','PAGO GTOS LEGALES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('607','7003','177','1.0000','PAGO GTOS LEGALES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('608','10000','177','1.0000','PAGO GTOS LEGALES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('609','10001','177','1.0000','PAGO GTOS LEGALES','10001')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('610','1000','177','1.0000','PAGO GTOS LEGALES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('611','30200','177','1.0000','PAGO GTOS LEGALES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('612','15000','177','1.0000','PAGO GTOS LEGALES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('613','11000','177','1.0000','PAGO GTOS LEGALES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('614','1001','177','1.0000','PAGO GTOS LEGALES','0')";

$sql["20170901"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8011', '200', 'Ficha de Avales 6', '<!-- contenido -->')";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_rules` = '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,31@rw,41@rw,71@rw,81@rw,99@rw' WHERE `menu_rules` = '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw'";
$sql["20170901"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<table class=\'firma\'>\r\n<tr>\r\n <td>LA ACREDITADA.</td>\r\n<tr>\r\n</tr>\r\n <td>\r\n  <br /><br /><br />\r\n  ________________________\r\n </td>\r\n<tr>\r\n</tr>\r\n <td>var_persona_declara_completo</td>\r\n</tr>\r\n</table>' WHERE `idgeneral_contratos` = '13001'";
$sql["20170901"][]	= "ALTER TABLE `socios_relaciones` CHANGE COLUMN `eacp` `eacp` VARCHAR(15) NULL DEFAULT 'EN_TRAMITE' ";
$sql["20170901"][]	= "INSERT INTO `eacp_config_bases_de_integracion` (`codigo_de_base`, `descripcion`, `tipo_de_base`) VALUES ('10019', 'OPERACIONES.- ELIMINABLES', 'de_operaciones')";


$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('616','10019','410','1.0000','PARCIALIDAD.- CAPITAL PACTADO','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('617','10019','411','1.0000','PARCIALIDAD.- INTERES PACTADO','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('618','10019','412','1.0000','PARCIALIDAD.- AHORRO PACTADO','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values ('619','10019','413','1.0000','IVA POR INTERES PACTADO','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('620','10019','414','1.0000','PARC CARGOS DESGLOSADOS','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('621','10019','415','1.0000','INT. DEV. EN PERS S/PARC. NO PAG. PZO DE GRAC','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('622','10019','420','1.0000','INT. DEV. NORMAL DEVENGADO','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('623','10019','421','1.0000','INT. DEV. NORMAL EN PZO DE GRACIA','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('624','10019','431','1.0000','INT. DEV. MOR. EN PZO DE GRACIA','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('625','10019','432','1.0000','INT. DEV. MOR. S/CARTERA VENCIDA','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('626','10019','433','1.0000','INT. DEV. MOR. POR PERS. EN PARC. NO PAG','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('627','10019','434','1.0000','INT. DEV. MOR. S/CARTERA VENC.','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('628','10019','600','1.0000','CARGO DE COMISIONES VARIAS','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('629','10019','601','1.0000','CARGO DE COMISION X SEGUIMIENTO','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('630','10019','801','-1.0000','BON. SOBRE INT. MORATORIOS','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('631','10019','802','-1.0000','BON. SOBRE INT. DEVENGADOS','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('632','10019','803','-1.0000','BON. DE GASTOS DEL ABOGADO','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('633','10019','1005','1.0000','CARGOS DE INTERESES EXTRAORDINARIOS','1')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('634','10019','1201','1.0000','CARGO IVA POR INTERESES COBRADOS','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('635','10019','1202','1.0000','CARGO IVA POR OTROS CARGOS Y COMISIONES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('636','10019','1203','1.0000','CARGO IVA PENDIENTE DE COBRO POR INTERESES','0')";
$sql["20170901"][]	= "insert into `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `afectacion`, `descripcion_de_la_relacion`, `subclasificacion`) values('637','10019','1204','1.0000','CARGO IVA PENDIENTE DE COBRO POR OTROS CARGOS','0')";
$sql["20170901"][]	= "ALTER TABLE `general_menu` CHANGE COLUMN `menu_title` `menu_title` VARCHAR(45) NULL DEFAULT '' ,CHANGE COLUMN `menu_description` `menu_description` VARCHAR(150) NULL DEFAULT '' ";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('creditos-letras-de-pago.rpt.php')";
$sql["20170901"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptcreditos/creditos-letras-de-pago.rpt.php?', 'Creditos.- Letras Pendiente de Pago', 'general_creditos', '10009', 'Muestra todas las letras pendiente de pago por producto y fecha de pago', '10009')";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('900118', 'OPERACION_INMUTABLE', 'Esta operacion no se puede cambiar.') ";
$sql["20170901"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('402', '500', 'Ticket POS', '<!-- Contenido -->')";
$sql["20170901"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = 'Financiera XX\r\nDomicilio X por Y, Colonia n\r\nvariable_tipo_de_recibo\r\n========================================\r\nRecibo : variable_numero_de_recibo\r\nvariable_docto_fecha_larga_actual \r\n========================================\r\n[variable_numero_de_socio]variable_nombre_del_socio\r\n========================================\r\n---concepto_del_movimiento|monto_del_movimiento---\r\n========================================\r\nTotal : variable_monto_del_recibo\r\nSon: (variable_monto_del_recibo_en_letras)\r\n========================================\r\nNotas: variable_observacion_del_recibo\r\nPago: variable_datos_del_pago\r\n========================================\r\nCajero: variable_nombre_del_cajero\r\nTiempo: variable_marca_de_tiempo\r\n' WHERE `idgeneral_contratos` = '402' ";
$sql["20170901"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '10001' WHERE `ideacp_config_bases_de_integracion_miembros` = '581'";
$sql["20170901"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '10001' WHERE `ideacp_config_bases_de_integracion_miembros` = '573'";
$sql["20170901"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `subclasificacion` = '10001' WHERE `ideacp_config_bases_de_integracion_miembros` = '549'";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('operaciones_tipos.edit.frm.php')";
$sql["20170901"][]	= "ALTER TABLE `eacp_config_bases_de_integracion` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' AFTER `tipo_de_base` ";
$sql["20170901"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('638', '7003', '177', 'GASTOS LEGALES')";
$sql["20170901"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`) VALUES ('639', '7013', '177', 'GASTOS LEGALES')";
$sql["20170901"][]	= "INSERT INTO `eacp_config_bases_de_integracion_miembros` (`ideacp_config_bases_de_integracion_miembros`, `codigo_de_base`, `miembro`, `descripcion_de_la_relacion`, `subclasificacion`) VALUES ('640', '10001', '177', 'GASTOS LEGALES', '10001')";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_image` = 'fa-list-alt' , `menu_help_id` = '20512' WHERE `idgeneral_menu` = '20512'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_description` = 'Administracion de Paises' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2058'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' WHERE `idgeneral_menu` = '2056'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '20513'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '20514'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '20521'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-list-alt' WHERE `idgeneral_menu` = '2051'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `descripcion_larga` = SUBSTRING(`descripcion_larga`, 0, 79)";
$sql["20170901"][]	= "ALTER TABLE `socios_relacionestipos` CHANGE COLUMN `idsocios_relacionestipos` `idsocios_relacionestipos` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT  ,CHANGE COLUMN `descripcion_larga` `descripcion_larga` VARCHAR(80) NULL DEFAULT ''  ,CHANGE COLUMN `requiere_domicilio` `requiere_domicilio` INT(2) NULL DEFAULT '0'  ,CHANGE COLUMN `requiere_actividadeconomica` `requiere_actividadeconomica` INT(2) NULL DEFAULT '0'  ,CHANGE COLUMN `requiere_validacion` `requiere_validacion` INT(2) NULL DEFAULT '0'  ,CHANGE COLUMN `tiene_vinculo_patrimonial` `tiene_vinculo_patrimonial` INT(2) NULL DEFAULT '0' , ADD COLUMN `tags` VARCHAR(50) NULL DEFAULT '' COMMENT 'tags de filtros' AFTER `checar_aml`";
$sql["20170901"][]	= "INSERT INTO `socios_tipoingreso` (`idsocios_tipoingreso`, `descripcion_tipoingreso`, `descripcion_detallada`, `estado`) VALUES ('99', 'DESCONOCIDO', 'Tipo de Ingreso no Asignado', '0')";
$sql["20170901"][]	= "ALTER TABLE `socios_tipoingreso` CHANGE COLUMN `idsocios_tipoingreso` `idsocios_tipoingreso` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '3'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '11'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '12'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '13'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '14'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '21'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '22'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '23'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pf' WHERE `idsocios_relacionestipos` = '70'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '96'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '97'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'pm' WHERE `idsocios_relacionestipos` = '98'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '551'";
$sql["20170901"][]	= "UPDATE `socios_relacionestipos` SET `tags` = 'todas' WHERE `idsocios_relacionestipos` = '552'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmsocios/lista_pagos_por_membresia.frm.php' WHERE `idgeneral_menu` = '7024' ";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('pagos_por_membresia.edit.frm.php')";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('pagos_por_membresia.new.frm.php')";
//$sql["20170901"][]	= "SELECT setNuevoPermisoX('pagos_por_membresia.frm.php')";
$sql["20170901"][]	= "INSERT INTO `personas_membresia_tipo` (`idpersonas_membresia_tipo`, `descripcion_membresia_tipo`) VALUES ('2', 'AFILIACION SECUNDARIA')";
$sql["20170901"][]	= "UPDATE `entidad_pagos_perfil` SET `periocidad`=365 WHERE `periocidad`=360 ";
$sql["20170901"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('589', 'REPORT', 'RECIBOS.USAR_TICKETS', '', '', '', '')";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('900119', 'CUOTA_CONFIRMA_ACTUAL', '¿ Desea Actualizar los compromisos para esta Cuota ?')";
$sql["20170901"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('8204', 'Personas.- Eliminar personas que no existen en Importacion', '')";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' , `menu_title` = 'Recibos y Operaciones' , `menu_image` = 'fa-tasks' , `menu_help_id` = '11030' WHERE `idgeneral_menu` = '11030'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-pencil-square' WHERE `idgeneral_menu` = '11033'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-pencil-square' , `menu_help_id` = '11034' WHERE `idgeneral_menu` = '11034'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_description` = 'Panel de Control de recibos' , `menu_image` = 'fa-tasks' , `menu_help_id` = '11032' WHERE `idgeneral_menu` = '11032'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-list-alt' , `menu_help_id` = '11031' WHERE `idgeneral_menu` = '11031'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_help_id` = '11033' WHERE `idgeneral_menu` = '11033'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_description` = 'Recibo de Captura General' , `menu_image` = 'fa-align-justify' , `menu_help_id` = '11036' WHERE `idgeneral_menu` = '11036'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_description` = 'Reimpresion de Recibos' , `menu_image` = 'fa-print' , `menu_help_id` = '11037' WHERE `idgeneral_menu` = '11037'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' , `menu_description` = 'Buscar Recibos' , `menu_image` = 'fa-search' , `menu_help_id` = '11038' WHERE `idgeneral_menu` = '11038'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_title` = 'Editar Mvtos. de Personas' , `menu_description` = 'Editas Mvtos. de Personas' , `menu_image` = 'fa-user-plus' , `menu_help_id` = '11039' WHERE `idgeneral_menu` = '11039'"; 
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '11030' , `menu_title` = 'Tipos de Operacion' , `menu_description` = 'Tipos de Operacion' , `menu_image` = 'fa-cog' , `menu_help_id` = '3035' WHERE `idgeneral_menu` = '3035' ";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('bases_de_sistema.new.frm.php')";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('index.alt.php')";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('corte-por-cajeros-global.rpt.php')";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-search' WHERE `idgeneral_menu` = '2004'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-search' WHERE `idgeneral_menu` = '2006'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_destination` = 'principal' WHERE `idgeneral_menu` = '2004'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-window-close-o' WHERE `idgeneral_menu` = '15020'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-question-circle' WHERE `idgeneral_menu` = '15001'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-info' WHERE `idgeneral_menu` = '15010'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' WHERE `idgeneral_menu` = '2006'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_help_id` = '2004' WHERE `idgeneral_menu` = '2004'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '1800' WHERE `idgeneral_menu` = '2006'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' , `menu_help_id` = '2006' WHERE `idgeneral_menu` = '2006'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' , `menu_image` = 'fa-search' , `menu_help_id` = '2008' WHERE `idgeneral_menu` = '2008'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' WHERE `idgeneral_menu` = '2004'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_order` = '7000' WHERE `idgeneral_menu` = '7000'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_order` = '8002' , `menu_help_id` = '7000' WHERE `idgeneral_menu` = '7000'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_order` = '15001' WHERE `idgeneral_menu` = '2006'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_order` = '15002' WHERE `idgeneral_menu` = '2004'";
$sql["20170901"][]	= "UPDATE `general_menu` SET `menu_order` = '15003' WHERE `idgeneral_menu` = '2008'";
$sql["20170901"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rpttesoreria/corte-por-cajeros-global.rpt.php?', 'Reporte Global de Cajeros', 'caja_tesoreria', '11103', 'Reporte de Operaciones y Tipo de pago por Cajero', '5')";

$sql["20170901"][]	= "UPDATE `general_contratos` SET `texto_del_contrato` = '<table style=\"max-width:56mm\">\r\n<tr><td>\r\nvariable_nombre_de_la_entidad<br />\r\nvariable_domicilio_de_la_entidad\r\n</td></tr>\r\n<tr><td>\r\nvariable_tipo_de_recibo<br/>\r\n========================================\r\n</td></tr>\r\n\r\n<tr><td>\r\nRecibo : variable_numero_de_recibo\r\n</td></tr>\r\n<tr><td>\r\nvariable_docto_fecha_larga_actual <br/>\r\n========================================\r\n</td></tr>\r\n<tr><td>\r\n[variable_numero_de_socio]variable_nombre_del_socio<br/>\r\n========================================\r\n</td></tr>\r\n\r\n<tr><td>\r\n---concepto_del_movimiento|monto_del_movimiento---\r\n</td></tr>\r\n\r\n<tr><td>\r\n========================================<br/>\r\nTotal : variable_monto_del_recibo\r\n</td></tr>\r\n\r\n<tr><td>\r\nSon: (variable_monto_del_recibo_en_letras)<br/>\r\n========================================\r\n</td></tr>\r\n\r\n<tr><td>\r\nNotas: variable_observacion_del_recibo\r\n</td></tr>\r\n\r\n<tr><td>\r\nPago: variable_datos_del_pago<br/>\r\n========================================\r\n</td></tr>\r\n\r\n<tr><td>\r\nCajero: variable_nombre_del_cajero<br/>\r\nTiempo: variable_marca_de_tiempo\r\n</td></tr>\r\n\r\n</table>\r\n' WHERE `idgeneral_contratos` = '402'";

$sql["20170901"][]	= "CALL proc_lenguaje_cambiar_palabras('Persona Juridica', 'Persona Moral')";
$sql["20170901"][]	= "CALL proc_lenguaje_cambiar_palabras('Juridica', 'Moral')";
$sql["20170901"][]	= "ALTER TABLE `empresas_operaciones` ADD COLUMN `unid` VARCHAR(20) NULL DEFAULT '' COMMENT 'id relacionado' AFTER `fecha_final`";
$sql["20170901"][]	= "UPDATE `empresas_operaciones` SET `unid`=CONCAT(DATE_FORMAT(`fecha_final`, '%Y'), '-', `clave_de_empresa`, '-',`periocidad`, '-', `periodo_marcado`) WHERE `unid`=''";
$sql["20170901"][]	= "ALTER TABLE `operaciones_recibos` ADD COLUMN `tiempo` INT(11) NULL DEFAULT '0' AFTER `montohist` ";
$sql["20170901"][]	= "ALTER TABLE `tesoreria_tipos_de_pago` ADD COLUMN `admitidos` VARCHAR(200) NULL DEFAULT '2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,31@rw,41@rw,71@rw' AFTER `eq_contable`";
$sql["20170901"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `administrado` INT(2) NULL DEFAULT '0' COMMENT 'Está o No Administrado' AFTER `montoajuste`";
$sql["20170901"][]	= "ALTER TABLE `vehiculos_gps_costeo` ADD INDEX `idxbb` (`tipo_de_gps` ASC, `frecuencia` ASC, `limite_inferior` ASC, `limite_superior` ASC, `idvehiculos_gps_costeo` ASC)";
$sql["20170901"][]	= "ALTER TABLE `leasing_residual` ADD INDEX `outdx` (`frecuencia` ASC, `limite_inferior` ASC, `limite_superior` ASC, `idleasing_residual` ASC)";
$sql["20170901"][]	= "ALTER TABLE `general_estados` ADD INDEX `idvc` (`clave_numerica` ASC, `clave_alfanumerica` ASC, `clave_en_sic` ASC, `idgeneral_estados` ASC)";
$sql["20170901"][]	= "ALTER TABLE `vehiculos_marcas` ADD INDEX `idbc` (`idvehiculos_marcas` ASC) ";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('vincular.personas.frm.php')";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('referencias.del.svc.php')";
$sql["20170901"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('2002', 'CONFIRMA_BAJA', '¿ Confirma la Baja de este Registro ?')";
$sql["20170901"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('901', '900', 'Mail.- Aviso General', '<!-- -->')";
$sql["20170901"][]	= "INSERT INTO `general_error_codigos` (`idgeneral_error_codigos`, `description_error`, `type_err`) VALUES ('901', 'Registro de Nuevo Usuario', 'security')";
$sql["20170901"][]	= "INSERT INTO `sistema_programacion_de_avisos` (`idprograma`, `nombre_del_aviso`, `forma_de_creacion`, `programacion`, `destinatarios`, `microformato`, `tipo_de_medios`, `intent_check`, `intent_command`) VALUES ('15', 'PRECREDITOS.NUEVO_REGISTRO', 'SYS_ALERTA_POR_EVENTO', 'PRECREDITOS.NUEVO_REGISTRO', '', '', ',MAIL', '', '')";
$sql["20170901"][]	= "INSERT INTO `general_utilerias` (`idgeneral_utilerias`, `nombre_utilerias`, `descripcion_utileria`) VALUES ('902', 'Creditos.- Saldar Creditos en TEMP', 'Elimina saldos en la tabla temp')";
$sql["20170901"][]	= "SELECT setNuevoPermisoX('pc.svc.php')";

$sql["20171101"][]	= "SELECT setNuevoPermisoX('envios_de_cobranza.frm.php')";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmempresas/empresas-envios.frm.php' WHERE `idgeneral_menu` = '1067' ";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('empresas-con-nomina.svc.php')";
$sql["20171101"][]	= "ALTER TABLE `creditos_periocidadpagos` ADD COLUMN `estatusactivo` INT(2) NULL DEFAULT '1' AFTER `tolerancia_en_dias_para_vencimiento`";
$sql["20171101"][]	= "ALTER TABLE `aml_listanegra_int` ADD INDEX `xppi` (`persona` ASC, `riesgo` ASC,`estatus` ASC,`clave_interna` ASC)";

$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('591', 'FORM', 'CAPTACION.SIN.DIAS_PRE', '', '', '', '', '1')";
$sql["20171101"][]	= "ALTER TABLE `captacion_cuentasorigen` ADD COLUMN `estatusactivo` INT(2) NULL DEFAULT '1' AFTER `origen_cuenta`";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_title` = 'Nuevo Contrato a la Vista' , `menu_file` = 'frmcaptacion/frmcaptacioncuentas.php?clase=10' WHERE `idgeneral_menu` = '8001'";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('8003', '8000', 'Nuevo Contrato a Plazo', 'frmcaptacion/frmcaptacioncuentas.php?clase=20', 'Nueva cuenta a Plazo', 'fa-calendar', 'command', '8003', '8003')";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_title` = 'Panel de Cuentas de Captacion' , `menu_image` = 'fa-cogs' , `menu_order` = '8012' , `menu_help_id` = '8012' WHERE `idgeneral_menu` = '8002'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-book' , `menu_help_id` = '8001' WHERE `idgeneral_menu` = '8001'";
$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('592', 'FORM', 'CREDITOS.ARRENDAMIENTO.NOUSERS', '', '', '', '', '1')";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-user-o' WHERE `idgeneral_menu` = '18301'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_order` = '18301' , `menu_help_id` = '18301' WHERE `idgeneral_menu` = '18301'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_description` = 'Reportes por Cajeros Beta' , `menu_image` = 'fa-user-o' , `menu_type` = 'command' , `menu_order` = '18302' , `menu_help_id` = '18302' , `menu_showin_toolbar` = 'true' WHERE `idgeneral_menu` = '18302' ";
$sql["20171101"][]	= "UPDATE `general_reports` SET `aplica` = 'caja_tesoreria2' WHERE `idreport` = '11103'";
$sql["20171101"][]	= "insert into `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_rules`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) values('18302','18300','Reportes por Cajeros II','frmreports/frmreportsxcajero2.frm.php','principal','Reportes por Cajeros Beta','fa-user-o','2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,31@rw,41@rw,71@rw','command','18302','18302','true')";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('buscar.actividades-scian.frm.php')";

$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '0' , `menu_order` = '9000' , `menu_help_id` = '9000' WHERE `idgeneral_menu` = '9000'";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_destination`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('1120', '1000', 'Arrendamiento', '', '', 'Operaciones de Arrendamiento', 'fa-car', 'parent', '1120', '1120', 'true')";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('1121', '1120', 'Cobro de Rentas', 'frmcaja/cobrar-rentas.frm.php', 'Cobrar rentas', 'fa-calendar-plus-o', 'command', '1121', '1121', 'true')";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('1122', '1120', 'Cobro Inicial', 'frmcaja/cobrar-pago-inicial.frm.php', 'Cobrar Pago Inicial', 'fa-bars', 'command', '1122', '1122', 'true')";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('2009', '0', 'Buscar Creditos', 'frmcreditos/buscar-creditos.frm.php', 'Busqueda de Creditos Agil', 'fa-search', 'command', '15004', '2009')";
$sql["20171101"][]	= "UPDATE `creditos_tipoconvenio` SET `tipo_en_sistema` = '500' WHERE `idcreditos_tipoconvenio` = '500'";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('2003', 'CONFIRMA_ELIMINAR', '¿ Confirma eliminar el registro ?')";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('2004', 'MSG_MONTO_REQUIRED', '¡ Se necesita un monto mayor a cero !')";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('2005', 'MSG_CONCEPTO_EXISTE', '! El concepto ya existe !')";
$sql["20171101"][]	= "UPDATE `operaciones_tipos` SET `descripcion_operacion` = 'OPERACIONES DE REINVERSION' , `nombre_corto` = 'REINVERSIONES' WHERE `idoperaciones_tipos` = '223'";
$sql["20171101"][]	= "UPDATE `operaciones_tipos` SET `estatus` = '0' WHERE `idoperaciones_tipos` = '9301'";
$sql["20171101"][]	= "UPDATE `operaciones_tipos` SET `estatus` = '0' WHERE `idoperaciones_tipos` = '9302'";
$sql["20171101"][]	= "UPDATE `operaciones_tipos` SET `estatus` = '0' WHERE `idoperaciones_tipos` = '236'";
$sql["20171101"][]	= "UPDATE `operaciones_tipos` SET `estatus` = '0' WHERE `idoperaciones_tipos` = '235'";
$sql["20171101"][]	= "UPDATE `operaciones_tipos` SET `tipo_operacion` = '177' , `visible_reporte` = '' , `nombre_corto` = 'GTOS LEGALES' WHERE `idoperaciones_tipos` = '177'";
$sql["20171101"][]	= "insert into `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) values('178','ANTICIPO RENTAS','0','0','\$cuenta = \"\";','','99','178','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.160','ANT. RENTAS','1','0.00') ";
$sql["20171101"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES('179','RENTA EN DEPOSITO','0','0','\$cuenta = \"\";','','99','179','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.160','RENTA DEPOSITO','1','0.00')";
$sql["20171101"][]	= "INSERT INTO `operaciones_tipos` (`idoperaciones_tipos`, `descripcion_operacion`, `clasificacion`, `subclasificacion`, `cuenta_contable`, `descripcion`, `recibo_que_afecta`, `tipo_operacion`, `visible_reporte`, `class_efectivo`, `mvto_que_afecta`, `afectacion_en_recibo`, `afectacion_en_notificacion`, `producto_aplicable`, `constituye_fondo_automatico`, `integra_vencido`, `afectacion_en_sdpm`, `cargo_directo`, `codigo_de_valoracion`, `periocidad_afectada`, `integra_parcialidad`, `es_estadistico`, `formula_de_calculo`, `formula_de_cancelacion`, `importancia_de_neutralizacion`, `preservar_movimiento`, `tasa_iva`, `nombre_corto`, `estatus`, `precio`) VALUES('180','PAGO PLACAS LEASE','0','0','\$cuenta = \"\";','','99','180','1','1','99','0','0','0','0','1','0','0','','ninguna','0','0','','','0','0','0.160','PAGO PLACAS','1','0.00')";
$sql["20171101"][]	= "INSERT INTO `operaciones_recibostipo` (`idoperaciones_recibostipo`, `descripcion_recibostipo`, `detalles_del_concepto`, `subclasificacion`, `nombre_sublasificacion`, `mostrar_en_corte`, `tipo_poliza_generada`, `afectacion_en_flujo_efvo`, `path_formato`, `origen`) VALUES('301','PAGO INICIAL LEASE','Pago Inicial de Leasing','0','','1','1','aumento','../rpt_formatos/recibo.rpt.php?recibo=','otros')";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('11016', '7020', 'Opciones del Sistema', 'frmsystem/opciones.frm.php', 'Administracion de Opciones', 'fa-cogs', 'command', '11016', '11016', 'true')";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('2006', 'CONFIRMA_ACTUALIZACION', '¿ Confirma la Actualizacion de este Registro ?')";
$sql["20171101"][]	= "ALTER TABLE `entidad_configuracion` CHANGE COLUMN `tipo` `tipo` VARCHAR(40) NULL DEFAULT '' ,CHANGE COLUMN `descripcion_del_parametro` `descripcion_del_parametro` VARCHAR(200) NULL DEFAULT '' ,CHANGE COLUMN `valor_del_parametro` `valor_del_parametro` VARCHAR(200) NULL DEFAULT ''";

$sql["20171101"][]	= "SELECT setNuevoPermisoX('app-sync.php')";
$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('593', 'FORM', 'CREDITOS.NOMINA.SEMANAS.EXTRA', '', '', '', '')";
$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('594', 'FORM', 'CREDITOS.NOMINA.QUINCENA.EXTRA', '', '', '', '')";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('leasing-activos.baja.frm.php')";
$sql["20171101"][]	= "ALTER TABLE `leasing_activos` ADD COLUMN `annio` VARCHAR(10) NULL DEFAULT '' AFTER `serie_nal` ";
$sql["20171101"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('32', '100', 'Siniestro', 'leas_activo_baja_mot')";
$sql["20171101"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('33', '101', 'Robo', 'leas_activo_baja_mot')";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('3001', 'NOMINA_PERIODO_REQ', 'Se requiere un periodo de Nomina.')";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('1004', 'MSG_NO_NOTES', 'No existen Notas o Detalles.')";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('1005', 'MSG_NO_REGS', 'No hay registros en el Sistema.')";

$sql["20171101"][]	= "ALTER TABLE `t_03f996214fba4a1d05a68b18fece8e71` ADD COLUMN `pin_app` VARCHAR(10) NULL DEFAULT '' AFTER `corporativo` ";
$sql["20171101"][]	= "ALTER TABLE  `creditos_preclientes`  CHANGE COLUMN `idorigen` `idorigen` INT(4) NULL DEFAULT '1' COMMENT '1 pagina 2 app' ,ADD COLUMN `idexterno` VARCHAR(60) NULL DEFAULT '' AFTER `idoficial`";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('20102', '20100', 'Carga de Documentos', 'frmsocios/cargar-batch-documentos.frm.php', 'Carga Batch de Documentos', 'fa-file', 'command', '20102', '20102', 'true')";
$sql["20171101"][]	= "INSERT INTO `sistema_mensajes` (`idsistema_mensajes`, `topico`, `mensaje`) VALUES ('2007', 'MSG_CONFIRMA_IMPORTAR', '¿ Confirma Importar el Registro seleccionado ?')";



$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'AI'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'AG'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'AN'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'AW'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'BB'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'BZ'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'BM'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'BN'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'DM'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'BS'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `nombre_oficial` = 'ESPAÑA' , `gentilicio` = 'ESPAÑOL' WHERE `clave_de_control` = 'ES'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' WHERE `clave_de_control` = 'AE'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'BH'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'KW'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'QA'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'PR'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'GD'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'GI'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'GL'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'GU'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'HK'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'KY'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'NF'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'PM'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'CK'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'CC'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'FK'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'SB'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'TC'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'VG'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'VI'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'KI'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'MO'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'MT'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'MS'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'NU'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'PF'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AD'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'LI'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'MC'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'SZ'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'TO'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'JO'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AL'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AO'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'CV'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'CR'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'CY'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'GY'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'HN'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'MH'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'LR'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'MV'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'MU'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'NR'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'SC'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'TT'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'TN'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'VU'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'YE'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'UY'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'LK'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AS'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'VC'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'SH'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'LC'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'SM'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'OM'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'TK'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_paraiso_fiscal` = '1' , `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'TV'";

$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AE'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'DM'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'BZ'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'BS'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'BN'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'BM'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'BB'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AW'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AN'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AI'";
$sql["20171101"][]	= " UPDATE `personas_domicilios_paises` SET `es_considerado_riesgo` = '100' WHERE `clave_de_control` = 'AG'";
$sql["20171101"][]	= "UPDATE `aml_risk_catalog` SET `descripcion` = 'Operaciones Reportadas. Superiores a 7500 USD  Mensual' , `unidades_ponderadas` = '7500' WHERE `clave_de_control` = '912100'";

$sql["20171101"][]	= "UPDATE `tesoreria_monedas` SET `quivalencia_en_moneda_local` = '19.3433' WHERE `clave_de_moneda` = 'USD'";
$sql["20171101"][]	= "INSERT INTO `tesoreria_valoracion_diaria` (`idcontrol`, `denominacion`, `fecha`, `valor`, `tiempo`,`usuario`) VALUES ('2', 'USD', '2017-12-31', '19.3433', '1515771045', '99')";
$sql["20171101"][]	= "UPDATE `aml_risk_catalog` SET `unidades_ponderadas` = '7500' WHERE `clave_de_control` = '910000'";
$sql["20171101"][]	= "UPDATE `entidad_configuracion` SET `valor_del_parametro` = '19.3433' WHERE `nombre_del_parametro` = 'precio_del_dolar'";

$sql["20171101"][]	= "ALTER TABLE `socios_figura_juridica` ADD COLUMN `activo` INT(2) NULL DEFAULT '0' AFTER `tipo_de_integracion`";
$sql["20171101"][]	= "UPDATE `socios_figura_juridica` SET `activo` = '1' WHERE `idsocios_figura_juridica` = '2'";
$sql["20171101"][]	= "UPDATE `socios_figura_juridica` SET `activo` = '1' WHERE `idsocios_figura_juridica` = '1'";

$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('595', 'SYSTEM', 'CREDITOS.USAR_MORA_BD', '', '', '', '', '1')";

$sql["20171101"][]	= "UPDATE `eacp_config_bases_de_integracion_miembros` SET `codigo_de_base` = '7013' , `descripcion_de_la_relacion` = 'BONIFICACION DE MORA'  WHERE `codigo_de_base` = '7013' AND `miembro`=801 ";
$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('596', 'FORM', 'RECIBOS.COBRO_BLOQUEADO', '', '', '', '', '1')";
$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('597', 'SYSTEM', 'CREDITOS.PAG.PURGAR_SDPM', '', '', '', '', '1')";

$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('20103', '20100', 'App.- Importar Clientes', 'frmsocios/app-clientes.frm.php', 'Importar Clientes de la App', 'fa-refresh', 'command', '20103', '20103')";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('app-sync.svc.php')";
$sql["20171101"][]	= "ALTER TABLE `creditos_destinos` ADD COLUMN `estatusactivo` INT(2) NULL DEFAULT '1' AFTER `tasa_de_iva`";
$sql["20171101"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `opts` VARCHAR(100) NULL DEFAULT '' AFTER `administrado`,ADD COLUMN `noivarent` INT(2) NULL DEFAULT '0' COMMENT 'renta sin iva' AFTER `opts`";
$sql["20171101"][]	= "UPDATE `general_contratos` SET `tags` = '281' WHERE `idgeneral_contratos` = '1902'";
$sql["20171101"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Formato de Domiciliacion' WHERE `idgeneral_contratos` = '1904'";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('credito-generico.fmt.php')";
$sql["20171101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`, `tags`, `ruta`) VALUES ('813', '100', 'Formato Conoce a Tu Cliente', '<!-- ejemplo -->', 'todas', '../rpt_formatos/persona-generico.fmt.php?')";

$sql["20171101"][]	= "SELECT setNuevoPermisoX('personas-formatos.frm.php')";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('persona-generico.fmt.php')";
$sql["20171101"][]	= "UPDATE `general_contratos` SET `ruta` = '../rpt_formatos/persona-generico.fmt.php?forma=813&' WHERE `idgeneral_contratos` = '813'";
$sql["20171101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`, `tags`, `ruta`) VALUES ('10101', '100', 'Reporte de Verificacion Ocular', '<!-- Ejemplo -->', 'todas', '../rpt_formatos/persona-generico.fmt.php?forma=10101&')";
$sql["20171101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`, `tags`, `ruta`) VALUES ('103', '200', 'Notificacion de Aprobacion de Credito', '<!-- Texto -->', 'todas,evento=autorizacion', '../rpt_formatos/credito-generico.fmt.php?forma=103&')";

$sql["20171101"][]	= "SELECT setNuevoPermisoX('personas-proveedores.edit.frm.php')";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('personas-aseguradoras.edit.frm.php')";

$sql["20171101"][]	= "ALTER TABLE `personas_proveedores` ADD COLUMN `alias` VARCHAR(20) NULL DEFAULT '' AFTER `persona`,ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' AFTER `alias`";
$sql["20171101"][]	= "ALTER TABLE `personas_aseguradoras` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' AFTER `alias`";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('inactivo.svc.php')";

$sql["20171101"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`, `tags`, `ruta`) VALUES ('1917', '200', 'Tramite de Placas y Tenencia', '<!-- -->', '281', '../rpt_formatos/credito.arrendamiento.fmt.php?forma=1917&')";
$sql["20171101"][]	= "INSERT INTO `sistema_programacion_de_avisos` (`idprograma`, `nombre_del_aviso`, `forma_de_creacion`, `programacion`, `destinatarios`, `microformato`, `tipo_de_medios`, `intent_check`, `intent_command`) VALUES ('16', 'CREDITOS.CUANDO_SE_AUTORIZA', 'SYS_ALERTA_POR_EVENTO', 'CREDITOS.CUANDO_SE_AUTORIZA', 'OFICIALES:oficial_de_credito|PERSONAS:clave_de_persona', '{fecha}-{hora}\r\nEl Credito {clave_de_credito} de la persona {nombre_de_persona} ha sido autorizado por {vars_creds_monto_autorizado}.', ',MAIL', '', '')";
$sql["20171101"][]	= "SELECT setNuevoPermisoX('entidad-configuracion.edit.frm.php')";
$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('598', 'FORM', 'RECIBOS.BLOQ_FECHA_FUTURA', '', '', '', '', '1')";
$sql["20171101"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('599', 'FORM', 'RECIBOS.BLOQ_FECHA_ANTERIOR', '', '', '', '', '1')";

$sql["20171101"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `vigencia_dias`, `tags`, `estatus`) VALUES ('3001', 'Contrato a la Vista', 'DG', '300', 'todas', '0')";
$sql["20171101"][]	= "INSERT INTO `personas_documentacion_tipos` (`clave_de_control`, `nombre_del_documento`, `clasificacion`, `vigencia_dias`, `tags`) VALUES ('2001', 'Contrato de Credito', 'DG', '300', 'todas')";

$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '11000' WHERE `idgeneral_menu` = '40100'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_title` = 'Cambios a la Cartera' WHERE `idgeneral_menu` = '4030'";

$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_title` = 'Cargos a Creditos' WHERE `idgeneral_menu` = '4002' ";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('4100', '4000', 'Cargos, Castigos y Quitas', '', 'Cargos, descuentos, castigos, quitas, etc', 'fa-recycle', 'parent', '4100', '4100', 'true')";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4100' WHERE `idgeneral_menu` = '4003' ";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4100' WHERE `idgeneral_menu` = '4002' ";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4100' WHERE `idgeneral_menu` = '4033' ";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-minus-circle' WHERE `idgeneral_menu` = '4003'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-plus-circle' WHERE `idgeneral_menu` = '4002'";
$sql["20171101"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('4110', '4000', 'Acciones de Gestion', '', 'Agregar llamadas, compromisos, etc', 'fa-calendar-alt', 'parent', '4110', '4110', 'true')";

$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4110' WHERE `idgeneral_menu` = '4030'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4110' WHERE `idgeneral_menu` = '4010'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4110' WHERE `idgeneral_menu` = '4009'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4110' WHERE `idgeneral_menu` = '4005'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_image` = 'fa-handshake' WHERE `idgeneral_menu` = '4005'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_description` = 'Agregar LLamadas de Gestion' , `menu_image` = 'fa-phone' WHERE `idgeneral_menu` = '4009'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_title` = 'Compromisos' WHERE `idgeneral_menu` = '4005'"; 
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4110' , `menu_description` = 'Calificar las Referencias segun la Visita' , `menu_image` = 'fa-phone-volume' WHERE `idgeneral_menu` = '4007'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4110' , `menu_image` = 'fa-gears' WHERE `idgeneral_menu` = '40200'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4000' WHERE `idgeneral_menu` = '4030'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_parent` = '4110' WHERE `idgeneral_menu` = '4004'";
$sql["20171101"][]	= "UPDATE `general_menu` SET `menu_title` = 'Modificador en Batch' , `menu_image` = 'fa-gears' WHERE `idgeneral_menu` = '4014' ";

$sql["20180301"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('600', 'REPORTS', 'REPORTES.USAR_ENTIDADESFED', '', '', '', '')";
$sql["20180301"][]	= "ALTER TABLE `general_reports` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' AFTER `order_index`,ADD COLUMN `tags` VARCHAR(100) NULL DEFAULT '' AFTER `estatus` ";
$sql["20180301"][]	= "UPDATE `general_reports` SET `estatus` = '0' WHERE `idreport` = '115'";
$sql["20180301"][]	= "UPDATE `general_reports` SET `estatus` = '0' WHERE `idreport` = '166'";
$sql["20180301"][]	= "UPDATE `general_reports` SET `descripcion_reports` = 'Operaciones.- Ingresos detallados' WHERE `idreport` = '175' ";
$sql["20180301"][]	= "SELECT setNuevoPermisoX('creditos-letras-pendientes-de-pago-v101.rpt.php')";
$sql["20180301"][]	= "INSERT INTO `general_reports` (`idgeneral_reports`, `descripcion_reports`, `aplica`, `idreport`, `explicacion`, `order_index`) VALUES ('../rptseguimiento/creditos-letras-pendientes-de-pago-v101.rpt.php?', 'Creditos.- Cuotas Pendientes de Pago con RC', 'seguimiento', '1005', 'Suma de Cuotas en Mora con Razones de Mora', '18')";
$sql["20180301"][]	= "UPDATE  `creditos_causa_de_vencimientos` SET `descripcion_de_la_causa` = 'SIN CLASIFICAR' WHERE `idcreditos_causa_de_vencimientos` = '99' ";
$sql["20180301"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`) VALUES ('4400', '4000', 'Catalogos de Segumiento', '', 'Catalogos de Segumiento', 'fa-table', 'parent', '4400', '4400')";
$sql["20180301"][]	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_description`, `menu_image`, `menu_type`, `menu_order`, `menu_help_id`, `menu_showin_toolbar`) VALUES ('4401', '4400', 'Razones de Vencimiento', 'frmsegumiento/razones-de-vencimiento.frm.php', 'Razones por la cual vencieron los Creditos', 'fa-ask', 'command', '4401', '4401', 'true')";
$sql["20180301"][]	= "UPDATE `general_menu` SET `menu_file` = 'frmseguimiento/razones-de-vencimiento.frm.php' , `menu_image` = 'fa-tasks' WHERE `idgeneral_menu` = '4401'";
//$sql["20180301"][]	= "SELECT setNuevoPermisoX('')";
$sql["20180301"][]	= "SELECT setNuevoPermisoX('razones-de-vencimiento.edit.frm.php')";
$sql["20180301"][]	= "SELECT setNuevoPermisoX('razones-de-vencimiento.new.frm.php')";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `tipo_contrato` = '120' WHERE `idgeneral_contratos` = '9'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `tipo_contrato` = '120' WHERE `idgeneral_contratos` = '10'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `tipo_contrato` = '120' WHERE `idgeneral_contratos` = '3001'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `tipo_contrato` = '120' WHERE `idgeneral_contratos` = '3002'";
$sql["20180301"][]	= "ALTER TABLE `seguimiento_notificaciones` ADD COLUMN `tiempo_entrega` INT NULL DEFAULT '0' AFTER `formato`,ADD COLUMN `idresultado` INT(3) NULL AFTER `tiempo_entrega`,ADD COLUMN `nota_entrega` VARCHAR(150) NULL DEFAULT '' AFTER `idresultado`";
$sql["20180301"][]	= "UPDATE `creditos_causa_de_vencimientos` SET `descripcion_de_la_causa` = 'EL SOCIO/CLIENTE SUFRIO UN EVENTO NO ESPERADO(ACCIDENTE)' WHERE `idcreditos_causa_de_vencimientos` = '2'";
$sql["20180301"][]	= "UPDATE `creditos_causa_de_vencimientos` SET `descripcion_de_la_causa` = 'EL SOCIO/CLIENTE SE QUEDO SIN LA FUENTE DE RECURSOS DEL PAGO(DESEMPLEO)' WHERE `idcreditos_causa_de_vencimientos` = '6'";
$sql["20180301"][]	= "UPDATE `creditos_causa_de_vencimientos` SET `descripcion_de_la_causa` = 'SOBREENDEDAMIENTO DEL SOCIO/CLIENTE O SIN CAPACIDAD DE PAGO' WHERE `idcreditos_causa_de_vencimientos` = '8'";
$sql["20180301"][]	= "UPDATE `creditos_causa_de_vencimientos` SET `descripcion_de_la_causa` = 'FALLECIMIENTO DEL SOCIO/CLIENTE' WHERE `idcreditos_causa_de_vencimientos` = '10'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Autorizacion para consulta SIC' , `tags` = 'todas' WHERE `idgeneral_contratos` = '11'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `estatus` = 'baja' WHERE `idgeneral_contratos` = '1004'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `estatus` = 'baja' WHERE `idgeneral_contratos` = '1504'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Avales.- Autorizacion Consulta SIC' WHERE `idgeneral_contratos` = '5001'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `ruta` = '../rpt_formatos/autorizacion-sic.rpt.php?' WHERE `idgeneral_contratos` = '11'";
$sql["20180301"][]	= "ALTER TABLE `leasing_activos` ADD COLUMN `segmento` INT(2) NULL DEFAULT '1' AFTER `annio`";
$sql["20180301"][]	= "INSERT INTO `socios_relacionestipos` (`idsocios_relacionestipos`, `descripcion_relacionestipos`, `subclasificacion`, `tipo_relacion`, `requiere_domicilio`, `requiere_actividadeconomica`, `tiene_vinculo_patrimonial`, `checar_aml`, `tags`) VALUES ('553', 'Persona Estudiada', '1', '553', '1', '1', '1', '0', 'todas')";
$sql["20180301"][]	= "INSERT INTO `socios_relacionestipos` (`idsocios_relacionestipos`, `descripcion_relacionestipos`, `subclasificacion`, `tipo_relacion`, `requiere_domicilio`, `requiere_actividadeconomica`, `tiene_vinculo_patrimonial`, `checar_aml`, `tags`) VALUES ('554', 'Persona a Facturar', '1', '554', '1', '1', '1', '0', 'todas')";
$sql["20180301"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`, `tags`, `ruta`) VALUES ('1111', '100', 'Autorizacion Consulta SIC PM', '<!-- Contenido -->', 'pm', '../rpt_formatos/persona-generico.fmt.php?forma=1111&')";
$sql["20180301"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`, `tags`, `ruta`) VALUES ('1112', '100', 'Autorizacion Consulta SIC PF', '<!-- Contenido -->', 'pf', '../rpt_formatos/persona-generico.fmt.php?forma=1112&')";
$sql["20180301"][]	= "ALTER TABLE `socios_patrimonio` ADD COLUMN `tamannio` INT(6) NULL DEFAULT '0' AFTER `fecha_de_alta`,ADD COLUMN `idtipounidad` INT(3) NULL DEFAULT '1' AFTER `tamannio` ";
$sql["20180301"][]	= "ALTER TABLE `socios_patrimonio` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' COMMENT '1 activo 0 inactivo' AFTER `idtipounidad` ";
$sql["20180301"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('34', '101', 'Pieza / Unidad', 'catalogo_unidades')";
$sql["20180301"][]	= "INSERT INTO `sistema_catalogo` (`idsistema_catalogo`, `clave`, `descripcion`, `tabla_virtual`) VALUES ('35', '102', 'Metro Cuadrado', 'catalogo_unidades')";
$sql["20180301"][]	= "ALTER TABLE `socios_patrimoniotipo` ADD COLUMN `unidad` INT(4) NULL DEFAULT '101' AFTER `tipo_patrimonio`";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '99'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '100'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '110'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '102' WHERE `idsocios_patrimoniotipo` = '120'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '130'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '140'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '150'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '210'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '211'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '220'";
$sql["20180301"][]	= "UPDATE `socios_patrimoniotipo` SET `unidad` = '101' WHERE `idsocios_patrimoniotipo` = '410'";
$sql["20180301"][]	= "UPDATE `socios_patrimonioestatus` SET `descripcion_patrimonioestatus` = 'POSITIVO' WHERE `idsocios_patrimonioestatus` = '10'";
$sql["20180301"][]	= "UPDATE `socios_patrimonioestatus` SET `descripcion_patrimonioestatus` = 'NEGATIVO' WHERE `idsocios_patrimonioestatus` = '20'";
$sql["20180301"][]	= "UPDATE `socios_patrimonioestatus` SET `descripcion_patrimonioestatus` = 'DESCONOCIDO' WHERE `idsocios_patrimonioestatus` = '99'";
$sql["20180301"][]	= "INSERT INTO `socios_patrimonioestatus` (`idsocios_patrimonioestatus`, `descripcion_patrimonioestatus`, `estatus_actual`, `estado_presentado`) VALUES ('21', 'CON OBSERVACIONES', '21', '21')";
$sql["20180301"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8012', '200', 'Ficha de Referencias 1', '<!-- Contenido -->')";
$sql["20180301"][]	= "INSERT INTO `general_contratos` (`idgeneral_contratos`, `tipo_contrato`, `titulo_del_contrato`, `texto_del_contrato`) VALUES ('8013', '200', 'Ficha de Referencias 2', '<!-- Contenido -->')"; 
$sql["20180301"][]	= "UPDATE `general_contratos` SET `tipo_contrato` = '100' WHERE `idgeneral_contratos` = '100'";
$sql["20180301"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Solicitud de Credito' WHERE `idgeneral_contratos` = '1100' ";
$sql["20180301"][]	= "SELECT setNuevoPermisoX('recibo.css.php')";
$sql["20180301"][]	= "UPDATE `socios_relacionestipos` SET `descripcion_relacionestipos` = 'REPRESENTANTE LEGAL' WHERE `idsocios_relacionestipos` = '14'";
$sql["20180301"][]	= "INSERT INTO `socios_relacionestipos` (`idsocios_relacionestipos`, `descripcion_relacionestipos`, `subclasificacion`, `tipo_relacion`, `checar_aml`, `tags`) VALUES ('15', 'REPRESENTANTE LEGAL MANCOMUNADO', '1', '15', '0', 'pm')"; 
$sql["20180301"][]	= "UPDATE `creditos_tipo_de_pago` SET `descripcion` = 'CUOTA FIJA' WHERE `idcreditos_tipo_de_pago` = '2'";
$sql["20180301"][]	= "UPDATE `creditos_tipo_de_pago` SET `descripcion` = 'INT. NATURAL' WHERE `idcreditos_tipo_de_pago` = '3'";
$sql["20180301"][]	= "UPDATE `creditos_tipo_de_pago` SET `descripcion` = 'CAPITAL FIJO' WHERE `idcreditos_tipo_de_pago` = '5'";
$sql["20180301"][]	= "UPDATE `creditos_tipo_de_pago` SET `descripcion` = 'INT. COMERCIAL' WHERE `idcreditos_tipo_de_pago` = '6'";
$sql["20180301"][]	= "UPDATE `creditos_tipo_de_pago` SET `descripcion` = 'PAGO FLAT' WHERE `idcreditos_tipo_de_pago` = '7'";
$sql["20180301"][]	= "UPDATE `creditos_tipo_de_pago` SET `descripcion` = 'PAGO VARIABLE' WHERE `idcreditos_tipo_de_pago` = '1'";
$sql["20180301"][]	= "ALTER TABLE `creditos_tipo_de_pago` ADD COLUMN `estatus` INT(2) NULL DEFAULT '1' AFTER `con_capital`";
$sql["20180301"][]	= "SELECT setNuevoPermisoX('referencia-bancaria.add.svc.php')";
$sql["20180301"][]	= "SELECT setNuevoPermisoX('referencia-comercial.add.svc.php')";
$sql["20180301"][]	= "ALTER TABLE `socios_general` ADD COLUMN `nss` VARCHAR(20) NULL DEFAULT '' AFTER `idinterna`";
$sql["20180301"][]	= "UPDATE `socios_general` SET `nss` = (SELECT `numero_de_seguridad_social` FROM `socios_aeconomica` WHERE `socios_aeconomica`.`socio_aeconomica`= `socios_general`.`codigo` AND `estado_actual`!=0 AND `numero_de_seguridad_social`!='' LIMIT 0,1) WHERE `nss` = '' OR ISNULL(`nss`) ";
$sql["20180301"][]	= "UPDATE `socios_general` SET `nss` = '' WHERE ISNULL(`nss`)";
$sql["20180301"][]	= "SELECT setNuevoPermisoX('edit-config.frm.php')";
//$sql["20180301"][]	= "ALTER TABLE `personas_domicilios_paises` ADD COLUMN `clavep_sic` VARCHAR(3) NULL DEFAULT '' AFTER `gentilicio`";
$sql["20180301"][]	= "ALTER TABLE `usuarios_web_notas` ADD COLUMN `tiempo` INT(11) NULL DEFAULT '0' AFTER `relevancia`";


$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Couchdb.- Nombre de la base de datos.' WHERE `nombre_del_parametro` = 'svc_db_couchdb'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Couchdb.- URI de la Base de Datos' WHERE `nombre_del_parametro` = 'svc_url_couchdb'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Couchdb.- Nombre de la Vista No Sync.' WHERE `nombre_del_parametro` = 'svc_vista_couchdb'"; 
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Direccion URL de las Actualizaciones' WHERE `nombre_del_parametro` = 'url_de_actualizaciones_automaticas'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Listas.- Direccion URL de Consulta GWS' WHERE `nombre_del_parametro` = 'url_de_consulta_gws'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Listas.- Direccion URL de Consulta PEP.- Interna' WHERE `nombre_del_parametro` = 'url_de_consulta_pep'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Listas.- Direccion URL de Consulta BLOQ.- Interna' WHERE `nombre_del_parametro` = 'url_de_consulta_sdn'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Listas.- Direccion URL de Consulta WIKI.- Interna' WHERE `nombre_del_parametro` = 'url_de_consulta_wiki'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Share.- Direccion URL de la Entidad Par' WHERE `nombre_del_parametro` = 'url_de_entidad_transmisora'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Otros.- Direccion URL de Otros Servicios Remotos' WHERE `nombre_del_parametro` = 'url_de_servicios_remotos'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'FTP.- Direccion IP4 del Servidor FTP' WHERE `nombre_del_parametro` = 'url_del_servidor_ftp'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Admin.- Password del Correo de Sistema' WHERE `nombre_del_parametro` = 'password_del_email_del_administrador'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Listas.- Password de Acceso al WIKI' WHERE `nombre_del_parametro` = 'password_de_consulta_wiki'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'Mercadeo.- Password del Correo de Mercadeo' WHERE `nombre_del_parametro` = 'password_de_mercadeo'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'FTP.- Password del Usuario FTP' WHERE `nombre_del_parametro` = 'password_de_usuario_ftp'";
$sql["20180302"][]	= "UPDATE `entidad_configuracion` SET `descripcion_del_parametro` = 'FTP.- Nombre del Usuario FTP' WHERE `nombre_del_parametro` = 'nombre_de_usuario_ftp'";


$sql["20180302"][]	= "ALTER TABLE `creditos_plan_de_pagos` ADD COLUMN `iva_castigos` FLOAT(12,2) NULL DEFAULT '0.00' AFTER `descuentos`,ADD COLUMN `total_base` DOUBLE(14,2) NULL DEFAULT '0.00' AFTER `iva_castigos`,ADD COLUMN `total_c_otros` DOUBLE(14,2) NULL DEFAULT '0.00' AFTER `total_base`,ADD COLUMN `total_c_castigos` DOUBLE(14,2) NULL DEFAULT '0.00' AFTER `total_c_otros`;";
$sql["20180302"][]	= "ALTER TABLE `creditos_plan_de_pagos` CHANGE COLUMN `otros_codigo` `otros_codigo` INT(11) NULL DEFAULT '414'";
$sql["20180302"][]	= "UPDATE `creditos_plan_de_pagos` SET `otros_codigo`=414 WHERE `otros_codigo`<>414 AND `otros`<=0";
$sql["20180302"][]	= "UPDATE `creditos_plan_de_pagos` SET `total_base`=`capital`+`interes`+`impuesto`";
$sql["20180302"][]	= "UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET pin_app=`idusuarios`";
$sql["20180302"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`, `valor`) VALUES ('601', 'SYSTEM', 'CREDITOS.NOMINA.NO_MORA', '', '', '', '', '1')";
$sql["20180302"][]	= "CREATE TABLE IF NOT EXISTS `operaciones_recibos_arch` ( `idoperaciones_recibos` BIGINT( 20 ) UNSIGNED AUTO_INCREMENT NOT NULL,`fecha_operacion` DATE NOT NULL DEFAULT '0000-00-00',`numero_socio` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '1',`docto_afectado` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '1',`tipo_docto` INT( 4 ) UNSIGNED NOT NULL DEFAULT '99',`total_operacion` DOUBLE( 16, 2 ) NOT NULL DEFAULT '0.00',`idusuario` INT( 4 ) UNSIGNED NOT NULL DEFAULT '99',`observacion_recibo` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,`cheque_afectador` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N/A',	`cadena_distributiva` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'N/A',`tipo_pago` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'efectivo',`indice_origen` INT( 4 ) NULL DEFAULT '99' COMMENT 'Origen del Recibo, mas bien su motivacion',	`grupo_asociado` BIGINT( 20 ) NULL DEFAULT '99',`recibo_fiscal` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',`sucursal` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'MATRIZ',`eacp` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'EN_TRAMITE',`clave_de_moneda` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'MXN',`unidades_en_moneda` DOUBLE( 16, 4 ) NULL DEFAULT '0.0000',`origen_aml` INT( 4 ) NOT NULL DEFAULT '0',`archivo_fisico` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,`persona_asociada` BIGINT( 20 ) NULL DEFAULT '0',`fecha_de_registro` DATE NULL DEFAULT '0000-00-00',`periodo_de_documento` INT( 4 ) NULL DEFAULT '0' , `cuenta_bancaria` BIGINT( 20 ) NULL DEFAULT '0' COMMENT 'Cuenta Bancaria asociada', `montohist` DOUBLE( 16, 2 ) NULL DEFAULT '0.00', `tiempo` INT( 11 ) NULL DEFAULT '0', PRIMARY KEY ( `idoperaciones_recibos`, `numero_socio`, `docto_afectado`, `tipo_docto`, `origen_aml` ) ) ENGINE = INNODB";
$sql["20180302"][]	= "CREATE TABLE IF NOT EXISTS `operaciones_mvtos_arch` (`idoperaciones_mvtos` BIGINT( 20 ) UNSIGNED AUTO_INCREMENT NOT NULL COMMENT 'Numero de Operacion',`fecha_operacion` DATE NOT NULL DEFAULT '0000-00-00',`fecha_afectacion` DATE NOT NULL DEFAULT '0000-00-00',`recibo_afectado` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '1',`socio_afectado` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '1',`docto_afectado` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '1',`tipo_operacion` INT( 4 ) UNSIGNED NOT NULL DEFAULT '99',`afectacion_real` DOUBLE( 16, 2 ) NOT NULL DEFAULT '0.00',`afectacion_cobranza` TINYINT( 4 ) NULL DEFAULT '0',`afectacion_contable` TINYINT( 4 ) NULL DEFAULT '0',`valor_afectacion` FLOAT( 4, 2 ) NOT NULL DEFAULT '0.00',`fecha_vcto` DATE NOT NULL DEFAULT '0000-00-00',`estatus_mvto` INT( 3 ) UNSIGNED NOT NULL DEFAULT '99',`codigo_eacp` VARCHAR( 15 )   NULL DEFAULT 'EN_TRAMITE',`periodo_socio` INT( 4 ) UNSIGNED NOT NULL DEFAULT '1',`periodo_contable` TINYINT( 3 ) UNSIGNED NULL DEFAULT '0',`periodo_cobranza` TINYINT( 3 ) UNSIGNED NULL DEFAULT '0',`periodo_seguimiento` TINYINT( 3 ) UNSIGNED NULL DEFAULT '0',`periodo_mensual` TINYINT( 3 ) UNSIGNED NULL DEFAULT '0',`periodo_semanal` TINYINT( 3 ) UNSIGNED NULL DEFAULT '0',`periodo_anual` TINYINT( 3 ) UNSIGNED NULL DEFAULT '0',`saldo_anterior` DOUBLE( 16, 2 ) NOT NULL DEFAULT '0.00',`saldo_actual` DOUBLE( 16, 2 ) NOT NULL DEFAULT '0.00',`detalles` VARCHAR( 80 )   NULL,`idusuario` INT( 4 ) NULL DEFAULT '99',`afectacion_estadistica` DOUBLE( 16, 2 ) NOT NULL DEFAULT '0.00',`docto_neutralizador` BIGINT( 20 ) UNSIGNED NULL DEFAULT '1',`cadena_heredada` VARCHAR( 20 )   NULL,`tasa_asociada` FLOAT( 7, 4 ) UNSIGNED NULL DEFAULT '0.0000',`dias_asociados` INT( 4 ) UNSIGNED NULL DEFAULT '0',`grupo_asociado` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0', `sucursal` VARCHAR( 20 )  NULL DEFAULT 'matriz', PRIMARY KEY ( `idoperaciones_mvtos` ) ) ENGINE = INNODB";
$sql["20180302"][]	= "INSERT INTO `socios_memotipos` (`tipo_memo`, `descripcion_memo`) VALUES ('14', 'Nota de recibo al Eliminar')";
$sql["20180302"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('602', 'FORM', 'CREDITOS.ARRENDAMIENTO.ANT.NOAPP', '', '', '', '')";
$sql["20180302"][]	= "ALTER TABLE `originacion_leasing` ADD COLUMN `com_agencia` FLOAT(6,4) NULL DEFAULT '0' AFTER `noivarent`,ADD COLUMN `gps_list` VARCHAR(60) NULL DEFAULT '' AFTER `com_agencia` ";
$sql["20180302"][]	= "ALTER TABLE `leasing_usuarios` ADD COLUMN `tasa_com` FLOAT(6,4) NULL DEFAULT '0' AFTER `telefono`";
$sql["20180302"][]	= "INSERT INTO `entidad_reglas` (`identidad_reglas`, `contexto`, `nombre`, `evento`, `sujetos`, `reglas`, `metadata`) VALUES ('603', 'FORM', 'CREDITOS.ARRENDAMIENTO.SUM.COMIS', '', '', '', '')";
$sql["20180302"][]	= "SELECT setNuevoPermisoX('personas.datos-pjuridicas.frm.php')";
$sql["20180302"][]	= "ALTER TABLE `originacion_leasing` CHANGE COLUMN `montoajuste` `montoajuste` FLOAT(10,2) NULL DEFAULT '0.00' ,ADD COLUMN `montocom_agen` FLOAT(10,2) NULL DEFAULT '0' AFTER `gps_list`";
$sql["20180302"][]	= "UPDATE `general_contratos` SET `titulo_del_contrato` = 'Arrendamiento.- Cotizacion' WHERE `idgeneral_contratos` = '1900'";

//$sql["20180302"][]	= "";
//$sql["20180302"][]	= "";

foreach ($sql as $idx => $cnt){
	if($idx >= $version){
		
	} else {
		unset($sql["$idx"]);
	}
}
if($out == "sql"){
	header('Content-type: plain/text');
	foreach ($sql as $idx => $cnt){
		foreach ($cnt as $subidx => $msql){
			echo "$msql;\r\n";
		}
	}
} else {
	header('Content-type: application/json');
	echo json_encode($sql);
}


?>