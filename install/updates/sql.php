<?php
$sql				= array();
$sqlMenu			= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_type`, `menu_order`) VALUES ";
$version			= (isset($_REQUEST["version"])) ? intval($_REQUEST["version"]) : 0;

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
//$sql["20141116"][]	= "";

foreach ($sql as $idx => $cnt){
	if($version > $idx ){
		unset($sql["$idx"]);
	}
}
header('Content-type: application/json');
echo json_encode($sql);
?>