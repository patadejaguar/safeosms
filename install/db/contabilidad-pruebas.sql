/*
-- Archivo de modificaciones y datos de ejemplo
-- SAFE Open Source Microfinance System.
-- http://www.opencorebanking.com/
-- Modulo de Contabilidad
*/


UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'true' WHERE `nombre_del_parametro` = 'contabilidad_en_migracion'; 
UPDATE `entidad_configuracion` SET `valor_del_parametro` = '1-1-2-2-2' WHERE `nombre_del_parametro` = 'mascara_de_cuenta_contable'; 
UPDATE `entidad_configuracion` SET `valor_del_parametro` = '#-#-##-##-##' WHERE `nombre_del_parametro` = 'mascara_sql_de_cuenta_contable'; 


UPDATE creditos_tipoconvenio 
    SET 
    capital_vigente_normal='140205', 
    contable_cartera_vencida='145005', 
    interes_vigente_normal='160310', 
    contable_intereses_anticipados='CUENTA_DE_CUADRE', 
    contable_intereses_cobrados='510410', 
    contable_intereses_moratorios='510450', 
    contable_cartera_castigada='CUENTA_DE_CUADRE', 
    contable_intereses_vencidos='CUENTA_DE_CUADRE',
    capital_vencido_renovado='141005', 
    capital_vencido_reestructurado='141805',
     capital_vencido_normal='145005', 
     capital_vigente_renovado='141005', 
     capital_vigente_reestructurado='141805', 
     capital_vigente_normal='140205', 
     interes_cobrado='510410', 
     moratorio_cobrado='510450', 
     interes_vencido_renovado='1161510', 
     interes_vencido_reestructurado='1161510', 
     interes_vencido_normal='160310', 
     interes_vigente_renovado='160310', 
     interes_vigente_reestructurado='160310', 
     interes_vigente_normal='160310';
     


UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"519090\";' WHERE `idoperaciones_tipos` = '1002'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"259090\";' WHERE `idoperaciones_tipos` = '1001'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"259090\";' WHERE `idoperaciones_tipos` = '1000'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = CUENTA_DE_CUADRE;' WHERE `idoperaciones_tipos` = '235'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = CUENTA_DE_CUADRE;' WHERE `idoperaciones_tipos` = '9301'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"250405\";' WHERE `idoperaciones_tipos` = '233'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"250405\";' WHERE `idoperaciones_tipos` = '234'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"250390\";' WHERE `idoperaciones_tipos` = '902'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"330105\"' WHERE `idoperaciones_tipos` = '703'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"31010201\";' WHERE `idoperaciones_tipos` = '701'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"330105\";' WHERE `idoperaciones_tipos` = '703'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"330115\";' WHERE `idoperaciones_tipos` = '702'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"3103\";' WHERE `idoperaciones_tipos` = '701'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"330105\";' WHERE `idoperaciones_tipos` = '710'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"5404\";' WHERE `idoperaciones_tipos` = '145'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"5404\";' WHERE `idoperaciones_tipos` = '600'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"5404\";' WHERE `idoperaciones_tipos` = '146'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"549090\";' WHERE `idoperaciones_tipos` = '1006'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"4203\";' WHERE `idoperaciones_tipos` = '301'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"4203\";' WHERE `idoperaciones_tipos` = '801'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"4203\";' WHERE `idoperaciones_tipos` = '802'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"4203\";' WHERE `idoperaciones_tipos` = '803'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"4203\";' WHERE `idoperaciones_tipos` = '303'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"5202\";' WHERE `idoperaciones_tipos` = '147'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"190245\"; ' WHERE `idoperaciones_tipos` = '1010'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"190245\"; /*$cuenta = CUENTA_DE_CUADRE;*/' WHERE `idoperaciones_tipos` = '1011'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"330110\"; /*$cuenta = CUENTA_DE_CUADRE;*/' WHERE `idoperaciones_tipos` = '704'; 

UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_intereses_por_pagar\"];' WHERE `idoperaciones_tipos` = '251'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_intereses_por_pagar\"];' WHERE `idoperaciones_tipos` = '500'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_movimientos\"];' WHERE `idoperaciones_tipos` = '220'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_movimientos\"];' WHERE `idoperaciones_tipos` = '221'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_movimientos\"];' WHERE `idoperaciones_tipos` = '222'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_movimientos\"];' WHERE `idoperaciones_tipos` = '230'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_movimientos\"];' WHERE `idoperaciones_tipos` = '231'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $captacion[\"contable_movimientos\"];' WHERE `idoperaciones_tipos` = '232'; 

UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"capital_vencido_normal\"];' WHERE `idoperaciones_tipos` = '111'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"capital_vigente_normal\"];' WHERE `idoperaciones_tipos` = '110'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"capital_vigente_normal\"];' WHERE `idoperaciones_tipos` = '120'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"capital_vigente_normal\"];' WHERE `idoperaciones_tipos` = '114'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"capital_vigente_normal\"];' WHERE `idoperaciones_tipos` = '115'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"interes_vigente_normal\"];' WHERE `idoperaciones_tipos` = '140'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"interes_vigente_normal\"];' WHERE `idoperaciones_tipos` = '420'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"interes_vigente_normal\"];' WHERE `idoperaciones_tipos` = '421'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = $cartera[\"interes_vigente_normal\"];' WHERE `idoperaciones_tipos` = '143'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"190245\";' WHERE `idoperaciones_tipos` = '1003'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"190245\";' WHERE `idoperaciones_tipos` = '1004'; 
UPDATE `operaciones_tipos` SET `cuenta_contable` = '$cuenta = \"NO_CONTABILIZAR\";' WHERE `idoperaciones_tipos` = '236'; 


UPDATE `sistema_programacion_de_avisos` SET `destinatarios` = 'CORREO:patadejaguar@gmail.com|'; 





UPDATE captacion_subproductos 
    SET contable_movimientos='210105', contable_intereses_por_pagar='269005', contable_gastos_por_intereses='410105', contable_cuentas_castigadas='250190';


INSERT INTO `contable_centrodecostos` (`idcontable_centrodecostos`, `nombre_centrodecostos`) VALUES ('1', 'POR DEFECTO');



UPDATE `entidad_configuracion` SET `valor_del_parametro` = '85990000' WHERE `nombre_del_parametro` = 'cuenta_de_cuadre'; 
UPDATE `entidad_configuracion` SET `valor_del_parametro` = '25059000' WHERE `nombre_del_parametro` = 'cuenta_contable_iva_en_intereses'; 
UPDATE `entidad_configuracion` SET `valor_del_parametro` = '25059000' WHERE `nombre_del_parametro` = 'cuenta_contable_iva_en_otros'; 
UPDATE `entidad_configuracion` SET `valor_del_parametro` = '11010500' WHERE `nombre_del_parametro` = 'cuenta_contable_de_efectivo'; 

UPDATE `contable_catalogo` SET `numero` = '85990000' , `tipo` = 'OD' WHERE `numero` = '0'; 

UPDATE `contable_catalogo` SET `nombre` = 'CUENTA DE CUADRE' WHERE `numero` = '85990000'; 

INSERT INTO `contable_catalogo` (`numero`, `equivalencia`, `nombre`, `tipo`, `ctamayor`, `afectable`, `centro_de_costo`, `fecha_de_alta`, `digitoagrupador`) VALUES ('85990000', '_CUADRE', 'CUENTA DE CUADRE', 'OD', '3', '1', '1', '2014-01-01', '4'); 


UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET `cuenta_contable_de_caja`= (SELECT `valor_del_parametro`  FROM `entidad_configuracion` WHERE `nombre_del_parametro` = 'cuenta_contable_de_efectivo' LIMIT 0,1);


UPDATE `captacion_subproductos`
SET `contable_movimientos` ='210135',
`contable_intereses_por_pagar` = '210135',
`contable_gastos_por_intereses` = '410115',
`contable_cuentas_castigadas` = '410590';

UPDATE `bancos_cuentas` SET `codigo_contable` = '110215';






UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'false' WHERE `nombre_del_parametro` = 'contabilidad_en_migracion'; 


UPDATE `entidad_configuracion` SET `valor_del_parametro` = '25059000' WHERE `nombre_del_parametro` = 'cuenta_contable_iva_en_intereses'; 
UPDATE `entidad_configuracion` SET `valor_del_parametro` = '25059000' WHERE `nombre_del_parametro` = 'cuenta_contable_iva_en_otros'; 


UPDATE creditos_tipoconvenio 
    SET 
    capital_vigente_normal='140205', 
    contable_cartera_vencida='145005', 
    interes_vigente_normal='510410', 
    contable_intereses_anticipados='CUENTA_DE_CUADRE', 
    contable_intereses_cobrados='510410', 
    contable_intereses_moratorios='510450', 
    contable_cartera_castigada='510410', 
    contable_intereses_vencidos='510410',
    capital_vencido_renovado='141005', 
    capital_vencido_reestructurado='141805',
     capital_vencido_normal='145005', 
     capital_vigente_renovado='141005', 
     capital_vigente_reestructurado='141805', 
     capital_vigente_normal='140205', 
     interes_cobrado='510410', 
     moratorio_cobrado='510450', 
     interes_vencido_renovado='510410', 
     interes_vencido_reestructurado='510410', 
     interes_vencido_normal='510410', 
     interes_vigente_renovado='510410', 
     interes_vigente_reestructurado='510410', 
     interes_vigente_normal='510410';

-- ---------------
-- ---------------
-- ---------------

