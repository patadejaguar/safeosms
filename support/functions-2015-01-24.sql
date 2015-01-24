SET GLOBAL log_bin_trust_function_creators = 1;



DELIMITER $$

DROP FUNCTION IF EXISTS `mask`$$

CREATE FUNCTION mask (unformatted_value BIGINT, format_string CHAR(32))
	RETURNS CHAR(32) DETERMINISTIC

BEGIN

DECLARE input_len TINYINT;
DECLARE output_len TINYINT;
DECLARE temp_char CHAR;


SET input_len = LENGTH(unformatted_value);
SET output_len = LENGTH(format_string);


WHILE ( output_len > 0 ) DO

SET temp_char = SUBSTR(format_string, output_len, 1);
IF ( temp_char = '#' ) THEN
IF ( input_len > 0 ) THEN
SET format_string = INSERT(format_string, output_len, 1, SUBSTR(unformatted_value, input_len, 1));
SET input_len = input_len - 1;
ELSE
SET format_string = INSERT(format_string, output_len, 1, '0');
END IF;
END IF;

SET output_len = output_len - 1;
END WHILE;

RETURN format_string;
END $$

DELIMITER ;



DELIMITER $$

DROP FUNCTION IF EXISTS `getReciboByMorphedAnterior`$$

CREATE FUNCTION `getReciboByMorphedAnterior`(XLRecibo CHAR(30)) RETURNS INT(20)
BEGIN
	DECLARE SLRecibo INT(20) DEFAULT 0;
		SET	XLRecibo	= REPLACE(XLRecibo, "TMP_", "");
		SET SLRecibo	= (SELECT field_id2 FROM general_tmp WHERE field_id1=XLRecibo);
		IF ISNULL(SLRecibo) THEN
			SET SLRecibo = getUltimoRecibo();
		END IF;
	RETURN SLRecibo;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getSucursalBySocio`$$

CREATE FUNCTION `getSucursalBySocio`(IDSocio BIGINT(20)) RETURNS CHAR(20)
BEGIN
	DECLARE IDSucursal CHAR(20) DEFAULT "matriz";
	SET IDSucursal = (SELECT sucursal FROM socios_general WHERE codigo=IDSocio LIMIT 0,1);
		IF ISNULL(IDSucursal) THEN
			SET IDSucursal = "otra";
		END IF;
	RETURN IDSucursal;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getSucursalByUser`$$

CREATE FUNCTION `getSucursalByUser`(UserCode INT(4)) RETURNS CHAR(20)
BEGIN
	DECLARE RetSucursal CHAR(20) DEFAULT "matriz";
	 SET RetSucursal =(SELECT sucursal FROM t_03f996214fba4a1d05a68b18fece8e71 WHERE idusuarios=UserCode);
		IF ISNULL(RetSucursal) THEN
			SET RetSucursal = "matriz";
		END IF;
	RETURN RetSucursal;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getUltimoRecibo`$$

CREATE FUNCTION `getUltimoRecibo`() RETURNS INT(20)
BEGIN
	
	DECLARE intUltimoRecibo INT(20) DEFAULT 0;
	SET intUltimoRecibo = (SELECT MAX(numerorecibo) FROM general_folios);
	SET intUltimoRecibo = intUltimoRecibo + 1;
	
	INSERT INTO general_folios(numerorecibo) VALUES (intUltimoRecibo);
	RETURN intUltimoRecibo;
    END$$

DELIMITER ;



DELIMITER $$

DROP FUNCTION IF EXISTS `getReciboByAnterior`$$

CREATE FUNCTION `getReciboByAnterior`(ReciboAnterior BIGINT(20)) RETURNS BIGINT(20)
BEGIN
	DECLARE ByRec BIGINT(20) DEFAULT 0;
	 SET ByRec = (SELECT field_id2 FROM general_tmp WHERE field_id1=ReciboAnterior );
		IF ISNULL(ByRec) THEN
			SET ByRec = 0;
		END IF;
	RETURN ByRec;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getMenuNameById`$$

CREATE FUNCTION `getMenuNameById`(mIndex INT(10)) RETURNS VARCHAR(45)
BEGIN
	DECLARE mNAME VARCHAR(45) DEFAULT "NINGUNO";
		SET mNAME = (SELECT menu_title FROM general_menu WHERE idgeneral_menu=mIndex);
		IF mNAME = "" OR ISNULL(mNAME) THEN
			SET mNAME = mIndex;
		END IF;

	RETURN mNAME;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getCompromisosByDocto`$$

CREATE FUNCTION `getCompromisosByDocto`(mSolicitud INT(10), mEstatus CHAR(20) ) RETURNS INT(4)
BEGIN
	RETURN (SELECT COUNT(idseguimiento_compromisos) AS 'exists' FROM seguimiento_compromisos	WHERE credito_comprometido= mSolicitud AND estatus_compromiso=mEstatus);
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getBooleanMX`$$

CREATE FUNCTION `getBooleanMX`(BOOLVALUE CHAR(20)) RETURNS CHAR(4)
BEGIN
	DECLARE MBOOLEAN CHAR(4) DEFAULT "NO";
	IF BOOLVALUE = "1" THEN
		SET MBOOLEAN = "SI";
	ELSE
		SET MBOOLEAN = "NO";
	END IF;
	RETURN MBOOLEAN;
    END$$

DELIMITER ;


DELIMITER $$

DROP FUNCTION IF EXISTS `getMorphosRecibo`$$

CREATE FUNCTION `getMorphosRecibo`(XLRecibo CHAR(30), NLRecibo INT(20) ) RETURNS INT(20)
BEGIN
	
	SET	XLRecibo	= REPLACE(XLRecibo, "TMP_", "");
	INSERT INTO general_tmp(field_id1, field_id2) VALUES(XLRecibo, NLRecibo);
	
	RETURN NLRecibo;
    END$$

DELIMITER ;


DELIMITER $$

DROP FUNCTION IF EXISTS `getVencimientoAdministrativo`$$

CREATE FUNCTION `getVencimientoAdministrativo`(fecha_ministracion DATE, fecha_ultimo_mvto DATE,
periocidad_de_pago INT(6), pagos_autorizados INT(10), dias_autorizados INT(10))
	RETURNS DATE
    BEGIN
	DECLARE DVencimiento DATE DEFAULT fecha_ultimo_mvto;
		IF fecha_ultimo_mvto = fecha_ministracion THEN
			SET DVencimiento = DATE_ADD(fecha_ministracion, INTERVAL ( (dias_autorizados - (periocidad_de_pago *  pagos_autorizados) ) + periocidad_de_pago) DAY);
		ELSE
			SET DVencimiento = DATE_ADD(fecha_ultimo_mvto, INTERVAL (periocidad_de_pago + 1)  DAY);
		END IF;
		RETURN DVencimiento;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getRiskClassified`$$

CREATE

    FUNCTION `getRiskClassified`(vDias INTEGER(6) )  RETURNS INTEGER(6)
    BEGIN
	DECLARE vClassified INTEGER(6) DEFAULT 1;
		IF ISNULL(vDias) THEN
			SET vClassified = 9999;
		END IF;

		IF ( vDias <= 1 ) THEN
		  SET vClassified = 1;
		END IF;

		IF ( vDias > 1 AND vDias <=7 ) THEN
		  SET vClassified = 7;
		END IF;

		IF ( vDias > 7 AND vDias <= 30) THEN
		  SET vClassified = 30;
		END IF;
		IF ( vDias > 30 AND vDias <= 60) THEN
		  SET vClassified = 30;
		END IF;
		IF ( vDias > 60 AND vDias <= 90 ) THEN
		  SET vClassified = 90;
		END IF;   
             		
		IF ( vDias > 90 AND vDias <= 120 ) THEN
		  SET vClassified = 120;
		END IF;

		IF ( vDias > 120 AND vDias <= 180 ) THEN
		  SET vClassified = 180;
		END IF;

		IF ( vDias > 180) THEN
		  SET vClassified = 9999;
		END IF;

	RETURN vClassified;
    END$$

DELIMITER ;

DELIMITER $$
DROP FUNCTION IF EXISTS `getInteresDevengadoNormal`$$

CREATE
    FUNCTION `getInteresDevengadoNormal`(vSaldoHistorico 	FLOAT(16,2), 
							vSaldoInsoluto 		FLOAT(16,2),
							vTasaInteres		FLOAT(6,4),
							vTipoDeCalculo 		INT(2),
							vFechaDeCalculo 	DATE,
							vFechaDeVencimiento 	DATE)
    RETURNS FLOAT(12,2)
    BEGIN
	DECLARE RINTERES FLOAT(12,2) DEFAULT 0;
	IF ( vFechaDeVencimiento >= vFechaDeCalculo ) THEN
		/* SI ES SALDO HISTORICO */
		IF vTipoDeCalculo = 1 THEN
			SET RINTERES = (vSaldoHistorico * vTasaInteres) / 360;
		ELSE
			SET RINTERES = (vSaldoInsoluto * vTasaInteres) / 360;
		END IF;
	END IF;
	RETURN RINTERES;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getUserByID`$$

CREATE FUNCTION `getUserByID`(UsrID VARCHAR(100) )
	RETURNS VARCHAR(100)
    BEGIN
	DECLARE NUser VARCHAR(100);
	SET NUser = (SELECT nombrecompleto FROM usuarios WHERE idusuarios = UsrID);
	IF ISNULL(NUser) THEN
		SET NUser = "_NO_REGISTRADO_";
	END IF;
	RETURN NUser;
    END$$

DELIMITER ;

-- - Funcion que retorna Cero si es menor a Cero

DELIMITER $$

DROP FUNCTION IF EXISTS `setNoMenorCero`$$

CREATE FUNCTION `setNoMenorCero`(mCantidad FLOAT(16,2) ) RETURNS FLOAT(16,2)
BEGIN
	IF (mCantidad < 0) THEN
		RETURN 0;
	ELSE
		RETURN mCantidad;
	END IF;
    END$$

DELIMITER ;
-- - Funcion para Obtener la Ultima Operacion
-- - Actualizado al 2011-oct-02
DELIMITER $$

DROP FUNCTION IF EXISTS `getUltimaOperacion`$$

CREATE FUNCTION `getUltimaOperacion`() RETURNS INT(20)
BEGIN
	
	DECLARE intUltOperacion INT(20) DEFAULT 0;
	DECLARE intNumsFolios INT(20) DEFAULT 0;
	SET intUltOperacion = (SELECT MAX(numerooperacion) FROM general_folios) + 1;
	SET intNumsFolios	= ( SELECT COUNT(idgeneral_folios) FROM general_folios );
	IF intNumsFolios > 4000 THEN
		SET intNumsFolios = ( SELECT setFoliosAlMaximo() );
	END IF;
	INSERT INTO general_folios(numerooperacion) VALUES (intUltOperacion);
	RETURN intUltOperacion;
    END$$

DELIMITER ;
-- - Función para Obtener la Suma por Socio de un Tipo de Captacion
DELIMITER $$
DROP FUNCTION IF EXISTS `getCaptacionSocioByTipo`$$
CREATE
    FUNCTION `getCaptacionSocioByTipo`(IDSocio BIGINT(20), mTipo TINYINT(3) ) RETURNS FLOAT(12,2)
    BEGIN
	DECLARE mMonto FLOAT(12,2);
	SET mMonto = (SELECT SUM(saldo_cuenta) AS 'monto' FROM captacion_cuentas WHERE numero_socio= IDSocio AND tipo_cuenta= mTipo GROUP BY numero_socio);
	IF ISNULL(mMonto) THEN
		SET mMonto = 0;
	END IF;
	RETURN mMonto;
    END$$

DELIMITER ;
-- - Funciones mas

-- - Funcion que obtiene el Monto del Credito COMPACW
DELIMITER $$

DROP FUNCTION IF EXISTS `getCreditosCompac`$$

CREATE FUNCTION `getCreditosCompac`(IDSocio BIGINT(20)) RETURNS FLOAT(12,2)
BEGIN
	DECLARE mCreditos FLOAT(12,2) DEFAULT 0;
	SET mCreditos = (SELECT saldos FROM migracion_compac_creditos WHERE numero_de_socio = IDSocio LIMIT 0,1);
		IF ISNULL(mCreditos) THEN
			SET mCreditos = 0;
		END IF;
	RETURN mCreditos;
    END$$

DELIMITER ;

-- - Funciones 02Oct2011
-- - Funcion que executa folios al Maximo
-- - Correccion: 09 de Abril de 2012
-- - @fix : Alta prioridad
DELIMITER $$


DROP FUNCTION IF EXISTS `setFoliosAlMaximo`$$

CREATE FUNCTION `setFoliosAlMaximo`() RETURNS TINYINT(1)
BEGIN
		DELETE FROM general_folios;
		INSERT INTO general_folios(
								numerooperacion, 
								numerocredito, 
								numerosocio, 
								numerocontrato , 
								numeroestadistico, 
								numerorecibo, 
								numerogposolidario , 
								polizacontable) 
							VALUES( 
								( SELECT MAX(idoperaciones_mvtos) FROM operaciones_mvtos ), 
								( SELECT MAX(numero_solicitud) FROM creditos_solicitud )+1, 
								( SELECT MAX(codigo) FROM socios_general )+1, 
								( SELECT MAX(numero_cuenta)  FROM captacion_cuentas )+1, 
								0, 
								( SELECT MAX(idoperaciones_recibos) FROM operaciones_recibos ), 
								( SELECT MAX(idsocios_grupossolidarios) FROM socios_grupossolidarios )+1,
								'');
	RETURN TRUE;
    END$$

DELIMITER ;
-- - Cuenta Contable Formateada
-- - Modificado el 17Oct2011
DELIMITER $$

DROP FUNCTION IF EXISTS `setCuentaFmt`$$

CREATE FUNCTION `setCuentaFmt`(vCUENTA CHAR(32)) RETURNS CHAR(32)
BEGIN
DECLARE mMask CHAR(32) DEFAULT '';
	SET mMask = (SELECT valor_del_parametro  FROM entidad_configuracion WHERE nombre_del_parametro = 'mascara_sql_de_cuenta_contable' LIMIT 0,1);
	RETURN mask(vCUENTA,mMask);
    END$$

DELIMITER ;

-- - Obtiene el recibo en un corte, el monto pagado
-- - 12mayo2012
DELIMITER $$

DROP FUNCTION IF EXISTS `getReciboEnCorte`$$

CREATE FUNCTION `getReciboEnCorte`(IdRecibo BIGINT(20) ) RETURNS FLOAT
BEGIN
DECLARE mMonto FLOAT(16,2) DEFAULT 0;
	SET mMonto = (SELECT pagado  FROM tesoreria_recibos_pagados WHERE recibo = IdRecibo LIMIT 0,1);
	IF ISNULL(mMonto) THEN
	SET mMonto = 0;
	END IF;
	RETURN mMonto;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getEsCancelado`$$

CREATE FUNCTION `getEsCancelado`(ClaveDeCredito BIGINT(20) )
    RETURNS INT

   BEGIN
	DECLARE intIsC INT(4) DEFAULT 0;
	SET intIsC = (SELECT COUNT(`idcreditos_rechazados`) FROM `creditos_rechazados` WHERE `numero_de_credito` = ClaveDeCredito);
	RETURN intIsC;
	
    END$$

DELIMITER ;

-- 08-octubre-2013
DELIMITER $$

DROP FUNCTION IF EXISTS `getFechaPrimeraLetra`$$

CREATE FUNCTION `getFechaPrimeraLetra`(vCredito BIGINT) RETURNS DATE
BEGIN
	DECLARE DVencimiento DATE DEFAULT CURDATE();
	RETURN (SELECT fecha_de_pago FROM primeras_letras WHERE docto_afectado = vCredito LIMIT 0,1);
    END$$

DELIMITER ;

-- 11Octubre2013
DELIMITER $$

DROP FUNCTION IF EXISTS `getFechaMX`$$

CREATE FUNCTION `getFechaMX`(mFecha DATE) RETURNS VARCHAR(20) CHARSET latin1
BEGIN
	RETURN DATE_FORMAT(mFecha, "%d/%b/%y");
    END$$

DELIMITER ;

-- SET lc_time_names = 'es_MX';

-- comp SELECT * FROM creditos_solicitud WHERE (SELECT COUNT(idcreditos_destinos) FROM creditos_destinos WHERE idcreditos_destinos=creditos_solicitud.destino_credito) =0


DELIMITER $$

DROP FUNCTION IF EXISTS `getFechaByInt`$$

CREATE FUNCTION `getFechaByInt`(mFecha BIGINT) RETURNS DATE
BEGIN
	RETURN FROM_UNIXTIME(mFecha);
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `getFechaMXByInt`$$

CREATE FUNCTION `getFechaMXByInt`(mFecha BIGINT) RETURNS VARCHAR(20) CHARSET latin1
BEGIN
	RETURN DATE_FORMAT(FROM_UNIXTIME(mFecha), "%d/%b/%y");
    END$$

DELIMITER ;

-- --------------------------------------------------------------------


DELIMITER $$

DROP FUNCTION IF EXISTS `getSaldoInverso`$$

CREATE FUNCTION `getSaldoInverso`(ActualID BIGINT, AnteriorID BIGINT, SaldoArrastrado FLOAT(16,2), AbonoActual FLOAT(16,2))
    RETURNS FLOAT(16,2)

    BEGIN
	DECLARE Saldo FLOAT(16,2) DEFAULT 0;
	
	IF ISNULL(AnteriorID) THEN
		SET AnteriorID	= ActualID;
		SET SaldoArrastrado = NULL;
	END IF;
	IF AnteriorID <> ActualID THEN
		SET AnteriorID	= ActualID;
		SET SaldoArrastrado = NULL;
	END IF;
	
	IF ISNULL(SaldoArrastrado) THEN
		SET SaldoArrastrado	= (SELECT letra FROM letras_pendientes WHERE docto_afectado=ActualID LIMIT 0,1 );
	END IF;
	
	SET Saldo		= SaldoArrastrado - AbonoActual;	
	
	IF Saldo < 0 THEN
		SET @SaldoActual	= NULL;
		SET @CreditoActual	= NULL;
		SET Saldo		= 0;
	ELSE
		SET @SaldoActual	= Saldo;
		SET @CreditoActual	= ActualID;
	END IF;
			

	RETURN Saldo;
    END$$

DELIMITER ;

-- ----------------------

DELIMITER $$


DROP FUNCTION IF EXISTS `getParcialidadPorFecha`$$


CREATE FUNCTION `getParcialidadPorFecha`(ParcialidadID INT, VariacionID INT, FechaActual DATE, FechaPago DATE, FechaMinistracion DATE )
    RETURNS INT
    BEGIN
	DECLARE mPARC INT DEFAULT 1;
	IF ParcialidadID = 0 THEN
		IF FechaPago > FechaActual THEN
			IF FechaMinistracion > FechaActual THEN
				SET mPARC	= ParcialidadID + 1;
			ELSE
				SET mPARC	= ParcialidadID;
			END IF;
		END IF;
	ELSE 
		SET mPARC	= ParcialidadID + 1 + VariacionID;
	END IF;
	
	RETURN mPARC;
    END$$

DELIMITER ;


-- --------------------------------------------------------------------------------
-- Routine DDL
-- Note: comments before and after the routine body will not be stored by the server
-- --------------------------------------------------------------------------------
DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_saldos_al_cierre`$$

CREATE PROCEDURE `sp_saldos_al_cierre` (vDate DATE)
BEGIN

SELECT
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_solicitud`.`fecha_solicitud`,
	`creditos_solicitud`.`fecha_autorizacion`,
	`creditos_solicitud`.`monto_solicitado`,
	`creditos_solicitud`.`monto_autorizado`,
	`creditos_solicitud`.`numero_socio`,
	`creditos_solicitud`.`docto_autorizacion`,
	`creditos_solicitud`.`plazo_en_dias`,
	`creditos_solicitud`.`numero_pagos`,
	`creditos_solicitud`.`tasa_interes`,
	`creditos_solicitud`.`periocidad_de_pago`,
	`creditos_solicitud`.`tipo_credito`,
	`creditos_solicitud`.`estatus_actual`,
	`creditos_solicitud`.`tipo_autorizacion`,
	`creditos_solicitud`.`oficial_credito`,
	`creditos_solicitud`.`fecha_vencimiento`,
	`creditos_solicitud`.`pagos_autorizados`,
	`creditos_solicitud`.`dias_autorizados`,
	`creditos_solicitud`.`periodo_solicitudes`,
	`creditos_solicitud`.`destino_credito`,
	`creditos_solicitud`.`idusuario`,
	`creditos_solicitud`.`nivel_riesgo`,
	`creditos_solicitud`.`saldo_actual`,
	`creditos_solicitud`.`fecha_ultimo_mvto`,
	`creditos_solicitud`.`tipo_convenio`,
	`creditos_solicitud`.`interes_diario`,
	`creditos_solicitud`.`ultimo_periodo_afectado`,
	`creditos_solicitud`.`tasa_moratorio`,
	`creditos_solicitud`.`observacion_solicitud`,
	`creditos_solicitud`.`tasa_ahorro`,
	`creditos_solicitud`.`grupo_asociado`,
	`creditos_solicitud`.`descripcion_aplicacion`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`contrato_corriente_relacionado`,
	`creditos_solicitud`.`monto_parcialidad`,
	`creditos_solicitud`.`oficial_seguimiento`,
	`creditos_solicitud`.`sucursal`,
	`creditos_solicitud`.`interes_normal_devengado`,
	`creditos_solicitud`.`tipo_de_pago`,
	`creditos_solicitud`.`interes_normal_pagado`,
	`creditos_solicitud`.`interes_moratorio_devengado`,
	`creditos_solicitud`.`interes_moratorio_pagado`,
	`creditos_solicitud`.`fecha_mora`,
	`creditos_solicitud`.`fecha_vencimiento_dinamico`,
	`creditos_solicitud`.`causa_de_mora`,
	`creditos_solicitud`.`estatus_de_negociacion`,
	`creditos_solicitud`.`tipo_de_calculo_de_interes`,
	`creditos_solicitud`.`persona_asociada`,
	`creditos_solicitud`.`perfil_de_intereses`,
	`creditos_solicitud`.`fuente_de_fondeo`,
	`creditos_solicitud`.`fecha_de_primer_pago`,
	COUNT(`operaciones_mvtos`.`tipo_operacion`) AS `operaciones`,
	MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha`,
	SUM(`operaciones_mvtos`.`afectacion_real`)  AS `abonos`,
	(`creditos_solicitud`.`monto_autorizado` - SUM(`operaciones_mvtos`.`afectacion_real`))  AS `saldo`
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		RIGHT OUTER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `operaciones_recibos` `operaciones_recibos` 
			ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
			`idoperaciones_recibos` 
WHERE
	(`operaciones_mvtos`.`tipo_operacion` =120) AND
	(`operaciones_mvtos`.`fecha_afectacion` <= vDate) 
GROUP BY
	`operaciones_mvtos`.`docto_afectado`;


END$$

DELIMITER ;

/* Equivalencia de monedas */

DELIMITER $$
DROP FUNCTION IF EXISTS `getEquivalenciaDeMonedas`$$
CREATE
    FUNCTION `getEquivalenciaDeMonedas`(vMonto FLOAT(16,4), vMoneda VARCHAR(4))
    RETURNS FLOAT
    BEGIN
	DECLARE mValor FLOAT(12,4) DEFAULT 0.00;
	DECLARE mDollar FLOAT(12,4) DEFAULT 0.00;
	DECLARE mEquiv FLOAT(12,4) DEFAULT 0.00;
	SET mValor = (SELECT `quivalencia_en_moneda_local`  FROM `tesoreria_monedas` WHERE `clave_de_moneda` = vMoneda LIMIT 0,1);
	SET mDollar = (SELECT `quivalencia_en_moneda_local`  FROM `tesoreria_monedas` WHERE `clave_de_moneda` = 'USD' LIMIT 0,1);
	IF ISNULL(mValor) THEN
		SET mEquiv	= vMonto * mDollar;
	ELSE
		SET mEquiv	= vMonto * mValor;
	END IF;
	RETURN mEquiv;
    END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS getMonedaLocal$$
CREATE

    FUNCTION `getMonedaLocal`()
    RETURNS CHAR(4)
    BEGIN
	DECLARE mMon CHAR(4) DEFAULT 'MXN';
		SET mMon = (SELECT valor_del_parametro  FROM entidad_configuracion WHERE nombre_del_parametro = 'aml_clave_de_moneda_local' LIMIT 0,1);
	RETURN mMon;
    END$$

DELIMITER ;

DELIMITER $$


DROP FUNCTION IF EXISTS `getSaldoPendienteDesdeLetra`$$

CREATE  FUNCTION `getSaldoPendienteDesdeLetra`(CreditID BIGINT(20), PeriodoID INT(4) ) RETURNS FLOAT(16,2)
BEGIN
	DECLARE mMONTO FLOAT(16,2);
	SET mMONTO = (SELECT SUM(letra) FROM letras WHERE docto_afectado=CreditID AND periodo_socio > PeriodoID);
	IF ISNULL(mMONTO) THEN
		SET mMONTO = 0;
	END IF;
	RETURN mMONTO;
    END$$

DELIMITER ;


DELIMITER $$
DROP PROCEDURE IF EXISTS `proc_listado_de_ingresos`$$

CREATE PROCEDURE `proc_listado_de_ingresos`()
BEGIN

DROP VIEW IF EXISTS listado_de_ingresos;
DROP TABLE IF EXISTS listado_de_ingresos;

CREATE TABLE listado_de_ingresos AS  
SELECT 
  `socios`.`iddependencia`                     AS `clave_empresa`,
  `socios`.`dependencia`                       AS `empresa`,
  `socios`.`codigo`                            AS `codigo`,
  `socios`.`nombre`                            AS `nombre`,
  `creditos_solicitud`.`tipo_convenio`         AS `producto`,
  `creditos_solicitud`.`numero_solicitud`      AS `credito`,
  `operaciones_mvtos`.`fecha_operacion`         AS `fecha`,
  `operaciones_tipos`.`tipo_operacion`         AS `clave_de_operacion`,
  `operaciones_tipos`.`descripcion_operacion`  AS `operacion`,
  (CASE WHEN (`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2003) THEN (`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) ELSE 0 END) AS `capital`,
  (CASE WHEN (`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2110) THEN (`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) ELSE 0 END) AS `interes_normal`,
  (CASE WHEN (`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2210) THEN (`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) ELSE 0 END) AS `interes_moratorio`,

  (CASE WHEN (`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 7021) THEN (`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) ELSE 0 END) AS `iva`,

  (CASE WHEN (`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 10001) THEN (`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) ELSE 0 END) AS `otros`,
  `operaciones_recibos`.`tipo_pago`            AS `tipo_de_pago`,
  `operaciones_mvtos`.`periodo_socio`          AS `parcialidad`,
	`creditos_solicitud`.`periocidad_de_pago`      AS `periocidad`,
`recibos_datos_bancarios`.`banco` 
FROM 

	`operaciones_recibos` `operaciones_recibos` 
		LEFT OUTER JOIN `recibos_datos_bancarios` `recibos_datos_bancarios` 
		ON `operaciones_recibos`.`idoperaciones_recibos` = 
		`recibos_datos_bancarios`.`recibo` 
			INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
			ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
			`idoperaciones_recibos` 
				INNER JOIN `creditos_solicitud` `creditos_solicitud` 
				ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
				`numero_solicitud` 
					INNER JOIN `socios` `socios` 
					ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
						INNER JOIN `operaciones_tipos` `operaciones_tipos` 
						ON `operaciones_mvtos`.`tipo_operacion` = 
						`operaciones_tipos`.`idoperaciones_tipos` 
							INNER JOIN 
							`eacp_config_bases_de_integracion_miembros` 
							`eacp_config_bases_de_integracion_miembros` 
							ON `operaciones_tipos`.`idoperaciones_tipos` = 
							`eacp_config_bases_de_integracion_miembros`.
							`miembro` 
								INNER JOIN `operaciones_recibostipo` 
								`operaciones_recibostipo` 
								ON `operaciones_recibos`.`tipo_docto` = 
								`operaciones_recibostipo`.
								`idoperaciones_recibostipo`

WHERE ((`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 10001)
       AND (`operaciones_recibostipo`.`mostrar_en_corte` <> '0'))
ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,`operaciones_mvtos`.`fecha_operacion`,`socios`.`iddependencia`,`socios`.`nombre` ;

ALTER TABLE `listado_de_ingresos` ADD COLUMN `indice` INT(10) NOT NULL AUTO_INCREMENT AFTER `banco`, ADD PRIMARY KEY (`indice`);

ALTER TABLE `listado_de_ingresos` ADD INDEX `persona` (`codigo`), ADD INDEX `credito` (`credito`), ADD INDEX `empresa` (`clave_empresa`), ADD INDEX `banco` (`banco`);

END$$

DELIMITER ;

DELIMITER $$
DROP PROCEDURE IF EXISTS `proc_historial_de_pagos`$$

CREATE PROCEDURE `proc_historial_de_pagos`()
BEGIN

DROP VIEW IF EXISTS historial_de_pagos;
DROP TABLE IF EXISTS historial_de_pagos;

CREATE TABLE historial_de_pagos AS  
SELECT 
	`operaciones_mvtos`.`socio_afectado` AS `persona`,
	`operaciones_mvtos`.`docto_afectado` AS `credito` ,
	`operaciones_mvtos`.`periodo_socio`  AS `periodo` ,
	MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha`,
	SUM( IF(`tipo_operacion` =410, `operaciones_mvtos`.`afectacion_real`, 0)) AS `capital`,
	SUM( IF(`tipo_operacion` =120, `operaciones_mvtos`.`afectacion_real`, 0)) AS `pagos`,
	SUM( IF(`tipo_operacion` =140, `operaciones_mvtos`.`afectacion_real`, 0)) AS `interes_pagado`,
	SUM( IF(`tipo_operacion` =411, `operaciones_mvtos`.`afectacion_real`, 0)) AS `interes`
	

FROM
	`operaciones_mvtos` `operaciones_mvtos` 
WHERE
	(`operaciones_mvtos`.`tipo_operacion` =120 OR `operaciones_mvtos`.`tipo_operacion` =410 OR `operaciones_mvtos`.`tipo_operacion` =411 OR `operaciones_mvtos`.`tipo_operacion` =140   )
GROUP BY
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`periodo_socio`	
ORDER BY
	`operaciones_mvtos`.`docto_afectado`, 
	`operaciones_mvtos`.`periodo_socio` ;

ALTER TABLE `historial_de_pagos` ADD COLUMN `indice` INT(10) NOT NULL AUTO_INCREMENT AFTER `pagos`, ADD PRIMARY KEY (`indice`);

ALTER TABLE `historial_de_pagos` ADD INDEX `persona` (`persona`), ADD INDEX `credito` (`credito`), ADD INDEX `periodo` (`periodo`);

END$$

DELIMITER ;

DELIMITER $$

DROP FUNCTION IF EXISTS `func_DoubleMetaphone`$$
CREATE FUNCTION `func_DoubleMetaphone`(st VARCHAR(55)) RETURNS varchar(128) CHARSET utf8
    NO SQL
BEGIN
    DECLARE length, first, last, pos, prevpos, is_slavo_germanic SMALLINT;
    DECLARE pri, sec VARCHAR(45) DEFAULT '';
    DECLARE ch CHAR(1);
                    SET first = 3;
    SET length = CHAR_LENGTH(st);
    SET last = first + length -1;
    SET st = CONCAT(REPEAT('-', first -1), UCASE(st), REPEAT(' ', 5));  SET is_slavo_germanic = (st LIKE '%W%' OR st LIKE '%K%' OR st LIKE '%CZ%');     SET pos = first;        IF SUBSTRING(st, first, 2) IN ('GN', 'KN', 'PN', 'WR', 'PS') THEN
        SET pos = pos + 1;
    END IF;
        IF SUBSTRING(st, first, 1) = 'X' THEN
        SET pri = 'S', sec = 'S', pos = pos  + 1;   END IF;
        WHILE pos <= last DO
            SET prevpos = pos;
        SET ch = SUBSTRING(st, pos, 1);         CASE
        WHEN ch IN ('A', 'E', 'I', 'O', 'U', 'Y') THEN
            IF pos = first THEN                 SET pri = CONCAT(pri, 'A'), sec = CONCAT(sec, 'A'), pos = pos  + 1;             ELSE
                SET pos = pos + 1;
            END IF;
        WHEN ch = 'B' THEN
                        IF SUBSTRING(st, pos+1, 1) = 'B' THEN
                SET pri = CONCAT(pri, 'P'), sec = CONCAT(sec, 'P'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'P'), sec = CONCAT(sec, 'P'), pos = pos  + 1;             END IF;
        WHEN ch = 'C' THEN
                        IF (pos > (first + 1) AND SUBSTRING(st, pos-2, 1) NOT IN ('A', 'E', 'I', 'O', 'U', 'Y') AND SUBSTRING(st, pos-1, 3) = 'ACH' AND
               (SUBSTRING(st, pos+2, 1) NOT IN ('I', 'E') OR SUBSTRING(st, pos-2, 6) IN ('BACHER', 'MACHER'))) THEN
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                         ELSEIF pos = first AND SUBSTRING(st, first, 6) = 'CAESAR' THEN
                SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'S'), pos = pos  + 2;             ELSEIF SUBSTRING(st, pos, 4) = 'CHIA' THEN              SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;             ELSEIF SUBSTRING(st, pos, 2) = 'CH' THEN
                                IF pos > first AND SUBSTRING(st, pos, 4) = 'CHAE' THEN
                    SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'X'), pos = pos  + 2;                 ELSEIF pos = first AND (SUBSTRING(st, pos+1, 5) IN ('HARAC', 'HARIS') OR
                   SUBSTRING(st, pos+1, 3) IN ('HOR', 'HYM', 'HIA', 'HEM')) AND SUBSTRING(st, first, 5) != 'CHORE' THEN
                    SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                                 ELSEIF SUBSTRING(st, first, 4) IN ('VAN ', 'VON ') OR SUBSTRING(st, first, 3) = 'SCH'
                   OR SUBSTRING(st, pos-2, 6) IN ('ORCHES', 'ARCHIT', 'ORCHID')
                   OR SUBSTRING(st, pos+2, 1) IN ('T', 'S')
                   OR ((SUBSTRING(st, pos-1, 1) IN ('A', 'O', 'U', 'E') OR pos = first)
                   AND SUBSTRING(st, pos+2, 1) IN ('L', 'R', 'N', 'M', 'B', 'H', 'F', 'V', 'W', ' ')) THEN
                    SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                 ELSE
                    IF pos > first THEN
                        IF SUBSTRING(st, first, 2) = 'MC' THEN
                            SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                         ELSE
                            SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                         END IF;
                    ELSE
                        SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'X'), pos = pos  + 2;                     END IF;
                END IF;
                        ELSEIF SUBSTRING(st, pos, 2) = 'CZ' AND SUBSTRING(st, pos-2, 4) != 'WICZ' THEN
                SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'X'), pos = pos  + 2;                         ELSEIF SUBSTRING(st, pos+1, 3) = 'CIA' THEN
                SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'X'), pos = pos  + 3;                         ELSEIF SUBSTRING(st, pos, 2) = 'CC' AND NOT (pos = (first +1) AND SUBSTRING(st, first, 1) = 'M') THEN
                                IF SUBSTRING(st, pos+2, 1) IN ('I', 'E', 'H') AND SUBSTRING(st, pos+2, 2) != 'HU' THEN
                                        IF (pos = first +1 AND SUBSTRING(st, first) = 'A') OR
                       SUBSTRING(st, pos-1, 5) IN ('UCCEE', 'UCCES') THEN
                        SET pri = CONCAT(pri, 'KS'), sec = CONCAT(sec, 'KS'), pos = pos  + 3;                                       ELSE
                        SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'X'), pos = pos  + 3;                     END IF;
                ELSE
                    SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                 END IF;
            ELSEIF SUBSTRING(st, pos, 2) IN ('CK', 'CG', 'CQ') THEN
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;             ELSEIF SUBSTRING(st, pos, 2) IN ('CI', 'CE', 'CY') THEN
                                IF SUBSTRING(st, pos, 3) IN ('CIO', 'CIE', 'CIA') THEN
                    SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'X'), pos = pos  + 2;                 ELSE
                    SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'S'), pos = pos  + 2;                 END IF;
            ELSE 
                                IF SUBSTRING(st, pos+1, 2) IN (' C', ' Q', ' G') THEN
                    SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 3;                 ELSE
                    IF SUBSTRING(st, pos+1, 1) IN ('C', 'K', 'Q') AND SUBSTRING(st, pos+1, 2) NOT IN ('CE', 'CI') THEN
                        SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                     ELSE                        SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 1;                     END IF;
                END IF;
            END IF;
                            WHEN ch = 'D' THEN
            IF SUBSTRING(st, pos, 2) = 'DG' THEN
                IF SUBSTRING(st, pos+2, 1) IN ('I', 'E', 'Y') THEN                  SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'J'), pos = pos  + 3;                 ELSE
                    SET pri = CONCAT(pri, 'TK'), sec = CONCAT(sec, 'TK'), pos = pos  + 2;               END IF;
            ELSEIF SUBSTRING(st, pos, 2) IN ('DT', 'DD') THEN
                SET pri = CONCAT(pri, 'T'), sec = CONCAT(sec, 'T'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'T'), sec = CONCAT(sec, 'T'), pos = pos  + 1;             END IF;
        WHEN ch = 'F' THEN
            IF SUBSTRING(st, pos+1, 1) = 'F' THEN
                SET pri = CONCAT(pri, 'F'), sec = CONCAT(sec, 'F'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'F'), sec = CONCAT(sec, 'F'), pos = pos  + 1;             END IF;
        WHEN ch = 'G' THEN
            IF SUBSTRING(st, pos+1, 1) = 'H' THEN
                IF (pos > first AND SUBSTRING(st, pos-1, 1) NOT IN ('A', 'E', 'I', 'O', 'U', 'Y')) 
                    OR ( pos = first AND SUBSTRING(st, pos+2, 1) != 'I') THEN
                    SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                 ELSEIF pos = first AND SUBSTRING(st, pos+2, 1) = 'I' THEN
                     SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'J'), pos = pos  + 2;                                ELSEIF (pos > (first + 1) AND SUBSTRING(st, pos-2, 1) IN ('B', 'H', 'D') )
                   OR (pos > (first + 2) AND SUBSTRING(st, pos-3, 1) IN ('B', 'H', 'D') )
                   OR (pos > (first + 3) AND SUBSTRING(st, pos-4, 1) IN ('B', 'H') ) THEN
                    SET pos = pos + 2;              ELSE
                                        IF pos > (first + 2) AND SUBSTRING(st, pos-1, 1) = 'U'
                       AND SUBSTRING(st, pos-3, 1) IN ('C', 'G', 'L', 'R', 'T') THEN
                        SET pri = CONCAT(pri, 'F'), sec = CONCAT(sec, 'F'), pos = pos  + 2;                     ELSEIF pos > first AND SUBSTRING(st, pos-1, 1) != 'I' THEN
                        SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;           ELSE
              SET pos = pos + 1;
                    END IF;
                END IF;
            ELSEIF SUBSTRING(st, pos+1, 1) = 'N' THEN
                IF pos = (first +1) AND SUBSTRING(st, first, 1) IN ('A', 'E', 'I', 'O', 'U', 'Y') AND NOT is_slavo_germanic THEN
                    SET pri = CONCAT(pri, 'KN'), sec = CONCAT(sec, 'N'), pos = pos  + 2;                ELSE
                                        IF SUBSTRING(st, pos+2, 2) != 'EY' AND SUBSTRING(st, pos+1, 1) != 'Y'
                        AND NOT is_slavo_germanic THEN
                        SET pri = CONCAT(pri, 'N'), sec = CONCAT(sec, 'KN'), pos = pos  + 2;                    ELSE
                        SET pri = CONCAT(pri, 'KN'), sec = CONCAT(sec, 'KN'), pos = pos  + 2;                   END IF;
                END IF;
                        ELSEIF SUBSTRING(st, pos+1, 2) = 'LI' AND NOT is_slavo_germanic THEN
                SET pri = CONCAT(pri, 'KL'), sec = CONCAT(sec, 'L'), pos = pos  + 2;                        ELSEIF pos = first AND (SUBSTRING(st, pos+1, 1) = 'Y'
               OR SUBSTRING(st, pos+1, 2) IN ('ES', 'EP', 'EB', 'EL', 'EY', 'IB', 'IL', 'IN', 'IE', 'EI', 'ER')) THEN
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'J'), pos = pos  + 2;                         ELSEIF (SUBSTRING(st, pos+1, 2) = 'ER' OR SUBSTRING(st, pos+1, 1) = 'Y')
               AND SUBSTRING(st, first, 6) NOT IN ('DANGER', 'RANGER', 'MANGER')
               AND SUBSTRING(st, pos-1, 1) not IN ('E', 'I') AND SUBSTRING(st, pos-1, 3) NOT IN ('RGY', 'OGY') THEN
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'J'), pos = pos  + 2;                         ELSEIF SUBSTRING(st, pos+1, 1) IN ('E', 'I', 'Y') OR SUBSTRING(st, pos-1, 4) IN ('AGGI', 'OGGI') THEN
                                IF SUBSTRING(st, first, 4) IN ('VON ', 'VAN ') OR SUBSTRING(st, first, 3) = 'SCH'
                   OR SUBSTRING(st, pos+1, 2) = 'ET' THEN
                    SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                 ELSE
                                        IF SUBSTRING(st, pos+1, 4) = 'IER ' THEN
                        SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'J'), pos = pos  + 2;                     ELSE
                        SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'K'), pos = pos  + 2;                     END IF;
                END IF;
            ELSEIF SUBSTRING(st, pos+1, 1) = 'G' THEN
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 1;             END IF;
        WHEN ch = 'H' THEN
                        IF (pos = first OR SUBSTRING(st, pos-1, 1) IN ('A', 'E', 'I', 'O', 'U', 'Y')) 
                AND SUBSTRING(st, pos+1, 1) IN ('A', 'E', 'I', 'O', 'U', 'Y') THEN
                SET pri = CONCAT(pri, 'H'), sec = CONCAT(sec, 'H'), pos = pos  + 2;             ELSE                SET pos = pos + 1;          END IF;
        WHEN ch = 'J' THEN
                        IF SUBSTRING(st, pos, 4) = 'JOSE' OR SUBSTRING(st, first, 4) = 'SAN ' THEN
                IF (pos = first AND SUBSTRING(st, pos+4, 1) = ' ') OR SUBSTRING(st, first, 4) = 'SAN ' THEN
                    SET pri = CONCAT(pri, 'H'), sec = CONCAT(sec, 'H');                 ELSE
                    SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'H');                 END IF;
            ELSEIF pos = first AND SUBSTRING(st, pos, 4) != 'JOSE' THEN
                SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'A');             ELSE
                                IF SUBSTRING(st, pos-1, 1) IN ('A', 'E', 'I', 'O', 'U', 'Y') AND NOT is_slavo_germanic
                   AND SUBSTRING(st, pos+1, 1) IN ('A', 'O') THEN
                    SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'H');                 ELSE
                    IF pos = last THEN
                        SET pri = CONCAT(pri, 'J');                     ELSE
                        IF SUBSTRING(st, pos+1, 1) not IN ('L', 'T', 'K', 'S', 'N', 'M', 'B', 'Z')
                           AND SUBSTRING(st, pos-1, 1) not IN ('S', 'K', 'L') THEN
                            SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'J');                         END IF;
                    END IF;
                END IF;
            END IF;
            IF SUBSTRING(st, pos+1, 1) = 'J' THEN
                SET pos = pos + 2;
            ELSE
                SET pos = pos + 1;
            END IF;
        WHEN ch = 'K' THEN
            IF SUBSTRING(st, pos+1, 1) = 'K' THEN
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 1;             END IF;
        WHEN ch = 'L' THEN
            IF SUBSTRING(st, pos+1, 1) = 'L' THEN
                                IF (pos = (last - 2) AND SUBSTRING(st, pos-1, 4) IN ('ILLO', 'ILLA', 'ALLE'))
                   OR ((SUBSTRING(st, last-1, 2) IN ('AS', 'OS') OR SUBSTRING(st, last) IN ('A', 'O'))
                   AND SUBSTRING(st, pos-1, 4) = 'ALLE') THEN
                    SET pri = CONCAT(pri, 'L'), pos = pos  + 2;                 ELSE
                    SET pri = CONCAT(pri, 'L'), sec = CONCAT(sec, 'L'), pos = pos  + 2;                 END IF;
            ELSE
                SET pri = CONCAT(pri, 'L'), sec = CONCAT(sec, 'L'), pos = pos  + 1;             END IF;
        WHEN ch = 'M' THEN
            IF SUBSTRING(st, pos-1, 3) = 'UMB'
               AND (pos + 1 = last OR SUBSTRING(st, pos+2, 2) = 'ER')
               OR SUBSTRING(st, pos+1, 1) = 'M' THEN
                SET pri = CONCAT(pri, 'M'), sec = CONCAT(sec, 'M'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'M'), sec = CONCAT(sec, 'M'), pos = pos  + 1;             END IF;
        WHEN ch = 'N' THEN
            IF SUBSTRING(st, pos+1, 1) = 'N' THEN
                SET pri = CONCAT(pri, 'N'), sec = CONCAT(sec, 'N'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'N'), sec = CONCAT(sec, 'N'), pos = pos  + 1;             END IF;
                            WHEN ch = 'P' THEN
            IF SUBSTRING(st, pos+1, 1) = 'H' THEN
                SET pri = CONCAT(pri, 'F'), sec = CONCAT(sec, 'F'), pos = pos  + 2;             ELSEIF SUBSTRING(st, pos+1, 1) IN ('P', 'B') THEN               SET pri = CONCAT(pri, 'P'), sec = CONCAT(sec, 'P'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'P'), sec = CONCAT(sec, 'P'), pos = pos  + 1;             END IF;
        WHEN ch = 'Q' THEN
            IF SUBSTRING(st, pos+1, 1) = 'Q' THEN
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'K'), sec = CONCAT(sec, 'K'), pos = pos  + 1;             END IF;
        WHEN ch = 'R' THEN
                        IF pos = last AND not is_slavo_germanic
               AND SUBSTRING(st, pos-2, 2) = 'IE' AND SUBSTRING(st, pos-4, 2) NOT IN ('ME', 'MA') THEN
                SET sec = CONCAT(sec, 'R');             ELSE
                SET pri = CONCAT(pri, 'R'), sec = CONCAT(sec, 'R');             END IF;
            IF SUBSTRING(st, pos+1, 1) = 'R' THEN
                SET pos = pos + 2;
            ELSE
                SET pos = pos + 1;
            END IF;
        WHEN ch = 'S' THEN
                        IF SUBSTRING(st, pos-1, 3) IN ('ISL', 'YSL') THEN
                SET pos = pos + 1;
                        ELSEIF pos = first AND SUBSTRING(st, first, 5) = 'SUGAR' THEN
                SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'S'), pos = pos  + 1;             ELSEIF SUBSTRING(st, pos, 2) = 'SH' THEN
                                IF SUBSTRING(st, pos+1, 4) IN ('HEIM', 'HOEK', 'HOLM', 'HOLZ') THEN
                    SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'S'), pos = pos  + 2;                 ELSE
                    SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'X'), pos = pos  + 2;                 END IF;
                        ELSEIF SUBSTRING(st, pos, 3) IN ('SIO', 'SIA') OR SUBSTRING(st, pos, 4) = 'SIAN' THEN
                IF NOT is_slavo_germanic THEN
                    SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'X'), pos = pos  + 3;                 ELSE
                    SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'S'), pos = pos  + 3;                 END IF;
                                    ELSEIF (pos = first AND SUBSTRING(st, pos+1, 1) IN ('M', 'N', 'L', 'W')) OR SUBSTRING(st, pos+1, 1) = 'Z' THEN
                SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'X');                 IF SUBSTRING(st, pos+1, 1) = 'Z' THEN
                    SET pos = pos + 2;
                ELSE
                    SET pos = pos + 1;
                END IF;
            ELSEIF SUBSTRING(st, pos, 2) = 'SC' THEN
                                IF SUBSTRING(st, pos+2, 1) = 'H' THEN
                                        IF SUBSTRING(st, pos+3, 2) IN ('OO', 'ER', 'EN', 'UY', 'ED', 'EM') THEN
                                                IF SUBSTRING(st, pos+3, 2) IN ('ER', 'EN') THEN
                            SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'SK'), pos = pos  + 3;                        ELSE
                            SET pri = CONCAT(pri, 'SK'), sec = CONCAT(sec, 'SK'), pos = pos  + 3;                       END IF;
                    ELSE
                        IF pos = first AND SUBSTRING(st, first+3, 1) not IN ('A', 'E', 'I', 'O', 'U', 'Y') AND SUBSTRING(st, first+3, 1) != 'W' THEN
                            SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'S'), pos = pos  + 3;                         ELSE
                            SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'X'), pos = pos  + 3;                         END IF;
                    END IF;
                ELSEIF SUBSTRING(st, pos+2, 1) IN ('I', 'E', 'Y') THEN
                    SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'S'), pos = pos  + 3;                 ELSE
                    SET pri = CONCAT(pri, 'SK'), sec = CONCAT(sec, 'SK'), pos = pos  + 3;               END IF;
                        ELSEIF pos = last AND SUBSTRING(st, pos-2, 2) IN ('AI', 'OI') THEN
                SET sec = CONCAT(sec, 'S'), pos = pos  + 1;             ELSE
                SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'S');                 IF SUBSTRING(st, pos+1, 1) IN ('S', 'Z') THEN
                    SET pos = pos + 2;
                ELSE
                    SET pos = pos + 1;
                END IF;
            END IF;
        WHEN ch = 'T' THEN
            IF SUBSTRING(st, pos, 4) = 'TION' THEN
                SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'X'), pos = pos  + 3;             ELSEIF SUBSTRING(st, pos, 3) IN ('TIA', 'TCH') THEN
                SET pri = CONCAT(pri, 'X'), sec = CONCAT(sec, 'X'), pos = pos  + 3;             ELSEIF SUBSTRING(st, pos, 2) = 'TH' OR SUBSTRING(st, pos, 3) = 'TTH' THEN
                                IF SUBSTRING(st, pos+2, 2) IN ('OM', 'AM') OR SUBSTRING(st, first, 4) IN ('VON ', 'VAN ')
                   OR SUBSTRING(st, first, 3) = 'SCH' THEN
                    SET pri = CONCAT(pri, 'T'), sec = CONCAT(sec, 'T'), pos = pos  + 2;                 ELSE
                    SET pri = CONCAT(pri, '0'), sec = CONCAT(sec, 'T'), pos = pos  + 2;                 END IF;
            ELSEIF SUBSTRING(st, pos+1, 1) IN ('T', 'D') THEN
                SET pri = CONCAT(pri, 'T'), sec = CONCAT(sec, 'T'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'T'), sec = CONCAT(sec, 'T'), pos = pos  + 1;             END IF;
        WHEN ch = 'V' THEN
            IF SUBSTRING(st, pos+1, 1) = 'V' THEN
                SET pri = CONCAT(pri, 'F'), sec = CONCAT(sec, 'F'), pos = pos  + 2;             ELSE
                SET pri = CONCAT(pri, 'F'), sec = CONCAT(sec, 'F'), pos = pos  + 1;             END IF;
        WHEN ch = 'W' THEN
                        IF SUBSTRING(st, pos, 2) = 'WR' THEN
                SET pri = CONCAT(pri, 'R'), sec = CONCAT(sec, 'R'), pos = pos  + 2;             ELSEIF pos = first AND (SUBSTRING(st, pos+1, 1) IN ('A', 'E', 'I', 'O', 'U', 'Y')
                OR SUBSTRING(st, pos, 2) = 'WH') THEN
                                IF SUBSTRING(st, pos+1, 1) IN ('A', 'E', 'I', 'O', 'U', 'Y') THEN
                    SET pri = CONCAT(pri, 'A'), sec = CONCAT(sec, 'F'), pos = pos  + 1;                 ELSE
                    SET pri = CONCAT(pri, 'A'), sec = CONCAT(sec, 'A'), pos = pos  + 1;                 END IF;
                        ELSEIF (pos = last AND SUBSTRING(st, pos-1, 1) IN ('A', 'E', 'I', 'O', 'U', 'Y'))
               OR SUBSTRING(st, pos-1, 5) IN ('EWSKI', 'EWSKY', 'OWSKI', 'OWSKY')
               OR SUBSTRING(st, first, 3) = 'SCH' THEN
                SET sec = CONCAT(sec, 'F'), pos = pos  + 1;                                     ELSEIF SUBSTRING(st, pos, 4) IN ('WICZ', 'WITZ') THEN
                SET pri = CONCAT(pri, 'TS'), sec = CONCAT(sec, 'FX'), pos = pos  + 4;           ELSE                SET pos = pos + 1;
            END IF;
        WHEN ch = 'X' THEN
                        IF not(pos = last AND (SUBSTRING(st, pos-3, 3) IN ('IAU', 'EAU')
               OR SUBSTRING(st, pos-2, 2) IN ('AU', 'OU'))) THEN
                SET pri = CONCAT(pri, 'KS'), sec = CONCAT(sec, 'KS');           END IF;
            IF SUBSTRING(st, pos+1, 1) IN ('C', 'X') THEN
                SET pos = pos + 2;
            ELSE
                SET pos = pos + 1;
            END IF;
        WHEN ch = 'Z' THEN
                        IF SUBSTRING(st, pos+1, 1) = 'H' THEN
                SET pri = CONCAT(pri, 'J'), sec = CONCAT(sec, 'J'), pos = pos  + 1;             ELSEIF SUBSTRING(st, pos+1, 3) IN ('ZO', 'ZI', 'ZA')
               OR (is_slavo_germanic AND pos > first AND SUBSTRING(st, pos-1, 1) != 'T') THEN
                SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'TS');            ELSE
                SET pri = CONCAT(pri, 'S'), sec = CONCAT(sec, 'S');             END IF;
            IF SUBSTRING(st, pos+1, 1) = 'Z' THEN
                SET pos = pos + 2;
            ELSE
                SET pos = pos + 1;
            END IF;
        ELSE
            SET pos = pos + 1;      END CASE;
    IF pos = prevpos THEN
       SET pos = pos +1;
       SET pri = CONCAT(pri,'<didnt incr>');     END IF;
    END WHILE;
    IF pri != sec THEN
        SET pri = CONCAT(pri, ';', sec);
  END IF;
    RETURN (pri);
END
$$

DELIMITER ;



DELIMITER $$
DROP FUNCTION IF EXISTS `jaro_winkler_similarity`$$

CREATE FUNCTION `jaro_winkler_similarity`(
in1 VARCHAR(255),
in2 VARCHAR(255)
) RETURNS FLOAT
DETERMINISTIC
BEGIN
#finestra:= search window, curString:= scanning cursor for the original string, curSub:= scanning cursor for the compared string
DECLARE finestra, curString, curSub, maxSub, trasposizioni, prefixlen, maxPrefix INT;
DECLARE char1, char2 CHAR(1);
DECLARE common1, common2, old1, old2 VARCHAR(255);
DECLARE trovato BOOLEAN;
DECLARE returnValue, jaro FLOAT;
SET maxPrefix=6; #from the original jaro - winkler algorithm
SET common1="";
SET common2="";
SET finestra=(LENGTH(in1)+LENGTH(in2)-ABS(LENGTH(in1)-LENGTH(in2))) DIV 4
+ ((LENGTH(in1)+LENGTH(in2)-ABS(LENGTH(in1)-LENGTH(in2)))/2) MOD 2;
SET old1=in1;
SET old2=in2;

#calculating common letters vectors
SET curString=1;
WHILE curString<=LENGTH(in1) AND (curString<=(LENGTH(in2)+finestra)) DO
SET curSub=curstring-finestra;
IF (curSub)<1 THEN
SET curSub=1;
END IF;
SET maxSub=curstring+finestra;
IF (maxSub)>LENGTH(in2) THEN
SET maxSub=LENGTH(in2);
END IF;
SET trovato = FALSE;
WHILE curSub<=maxSub AND trovato=FALSE DO
IF SUBSTR(in1,curString,1)=SUBSTR(in2,curSub,1) THEN
SET common1 = CONCAT(common1,SUBSTR(in1,curString,1));
SET in2 = CONCAT(SUBSTR(in2,1,curSub-1),CONCAT("0",SUBSTR(in2,curSub+1,LENGTH(in2)-curSub+1)));
SET trovato=TRUE;
END IF;
SET curSub=curSub+1;
END WHILE;
SET curString=curString+1;
END WHILE;
#back to the original string
SET in2=old2;
SET curString=1;
WHILE curString<=LENGTH(in2) AND (curString<=(LENGTH(in1)+finestra)) DO
SET curSub=curstring-finestra;
IF (curSub)<1 THEN
SET curSub=1;
END IF;
SET maxSub=curstring+finestra;
IF (maxSub)>LENGTH(in1) THEN
SET maxSub=LENGTH(in1);
END IF;
SET trovato = FALSE;
WHILE curSub<=maxSub AND trovato=FALSE DO
IF SUBSTR(in2,curString,1)=SUBSTR(in1,curSub,1) THEN
SET common2 = CONCAT(common2,SUBSTR(in2,curString,1));
SET in1 = CONCAT(SUBSTR(in1,1,curSub-1),CONCAT("0",SUBSTR(in1,curSub+1,LENGTH(in1)-curSub+1)));
SET trovato=TRUE;
END IF;
SET curSub=curSub+1;
END WHILE;
SET curString=curString+1;
END WHILE;
#back to the original string
SET in1=old1;

#calculating jaro metric
IF LENGTH(common1)<>LENGTH(common2)
THEN SET jaro=0;
ELSEIF LENGTH(common1)=0 OR LENGTH(common2)=0
THEN SET jaro=0;
ELSE
#calcolo la distanza di winkler
#passo 1: calcolo le trasposizioni
SET trasposizioni=0;
SET curString=1;
WHILE curString<=LENGTH(common1) DO
IF(SUBSTR(common1,curString,1)<>SUBSTR(common2,curString,1)) THEN
SET trasposizioni=trasposizioni+1;
END IF;
SET curString=curString+1;
END WHILE;
SET jaro=
(
LENGTH(common1)/LENGTH(in1)+
LENGTH(common2)/LENGTH(in2)+
(LENGTH(common1)-trasposizioni/2)/LENGTH(common1)
)/3;

END IF; #end if for jaro metric

#calculating common prefix for winkler metric
SET prefixlen=0;
WHILE (SUBSTRING(in1,prefixlen+1,1)=SUBSTRING(in2,prefixlen+1,1)) AND (prefixlen<6) DO
SET prefixlen= prefixlen+1;
END WHILE;


#calculate jaro-winkler metric
RETURN jaro+(prefixlen*0.1*(1-jaro));
END
$$
DELIMITER ;




-- 2014-12-12


DELIMITER $$

DROP PROCEDURE IF EXISTS `sp_clonar_actividades`$$

CREATE  PROCEDURE `sp_clonar_actividades`()
BEGIN

DELETE FROM `socios_aeconomica_tipos`;

INSERT INTO `socios_aeconomica_tipos` (`idsocios_aeconomica_tipos`, `nombre_taeconomica`) SELECT `clave_interna`,`nombre_de_la_actividad` FROM `personas_actividad_economica_tipos`;

END$$

DELIMITER ;

-- Base de recibos para AML

DELIMITER $$
DROP PROCEDURE IF EXISTS `proc_perfil_egresos_por_persona`$$

CREATE PROCEDURE `proc_perfil_egresos_por_persona`()
BEGIN

DROP VIEW IF EXISTS aml_perfil_egresos_por_persona;
DROP TABLE IF EXISTS aml_perfil_egresos_por_persona;

CREATE TABLE aml_perfil_egresos_por_persona AS  
(
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

) ;

ALTER TABLE `aml_perfil_egresos_por_persona` ADD COLUMN `indice` INT(10) NOT NULL AUTO_INCREMENT AFTER `recibo`, ADD PRIMARY KEY (`indice`);

ALTER TABLE `aml_perfil_egresos_por_persona` ADD INDEX `socio_afectado` (`socio_afectado`), ADD INDEX `recibo` (`recibo`);

END$$

DELIMITER ;

-- AML Operaciones por Nucleo

DELIMITER $$
DROP PROCEDURE IF EXISTS `proc_personas_operaciones_recursivas`$$

CREATE PROCEDURE `proc_personas_operaciones_recursivas`()
BEGIN

DROP VIEW IF EXISTS personas_operaciones_recursivas;
DROP TABLE IF EXISTS personas_operaciones_recursivas;

CREATE TABLE personas_operaciones_recursivas AS  
(
SELECT
    `personas_relaciones_recursivas`.`persona`     AS `persona`,
    `operaciones_recibos`.`idusuario`                    AS `usuario`,
       
    CEIL( COUNT(`operaciones_recibos`.`idoperaciones_recibos`) /
    (DATEDIFF( MAX(`operaciones_recibos`.`fecha_operacion`), MIN(`operaciones_recibos`.`fecha_operacion`))
     + 1)
     ) AS `operaciones`,
    CEIL(SUM(`operaciones_recibos`.`total_operacion`) /
    ( DATEDIFF( MAX(`operaciones_recibos`.`fecha_operacion`), MIN(`operaciones_recibos`.`fecha_operacion`)) + 1)
    ) AS `monto`
FROM
    `operaciones_recibos` `operaciones_recibos`
        INNER JOIN `personas_relaciones_recursivas`
        `personas_relaciones_recursivas`
        ON `operaciones_recibos`.`numero_socio` =
        `personas_relaciones_recursivas`.`persona`
WHERE
    (`operaciones_recibos`.`origen_aml` >0)        
    GROUP BY
        `personas_relaciones_recursivas`.`persona`,
        `operaciones_recibos`.`idusuario`

) ;

ALTER TABLE `personas_operaciones_recursivas` ADD COLUMN `indice` INT(10) NOT NULL AUTO_INCREMENT AFTER `monto`, ADD PRIMARY KEY (`indice`);

ALTER TABLE `personas_operaciones_recursivas` ADD INDEX `persona` (`persona`);

END$$

DELIMITER ;

-- ------------ function de ejercico actual
DELIMITER $$

DROP FUNCTION IF EXISTS `getEjercicioDeTrabajo`$$

CREATE FUNCTION `getEjercicioDeTrabajo`() RETURNS INT(11)
    NO SQL
    DETERMINISTIC
BEGIN
	IF ISNULL(@ejercicio) THEN
	SET @ejercicio = YEAR(NOW());
	END IF;
	RETURN @ejercicio;
    END$$

DELIMITER ;
-- Limite de UDIS
DELIMITER $$
DROP FUNCTION IF EXISTS `getLimitePersonasVigiladas`$$
CREATE
    FUNCTION `getLimitePersonasVigiladas`()
    RETURNS FLOAT(14,2)
    BEGIN
	DECLARE mUDI FLOAT(12,6) DEFAULT 0;
	DECLARE mLIMITE FLOAT(12,2) DEFAULT 0;    
	IF ISNULL(@limite_personas_vigiladas)  THEN

		SET mUDI = (SELECT valor_del_parametro  FROM entidad_configuracion WHERE nombre_del_parametro = 'valor_actual_de_la_udi' LIMIT 0,1);
		SET mLIMITE = (SELECT valor_del_parametro  FROM entidad_configuracion WHERE nombre_del_parametro = 'limite_inferior_para_personas_bloqueadas' LIMIT 0,1);
		SET @limite_personas_vigiladas = (mUDI * mLIMITE);
	END IF;
	RETURN @limite_personas_vigiladas;
    END$$

DELIMITER ;


-- ------------ function fecha de corte
DELIMITER $$

DROP FUNCTION IF EXISTS `getFechaDeCorte`$$

CREATE FUNCTION `getFechaDeCorte`() RETURNS DATE
    NO SQL
    DETERMINISTIC
BEGIN
	IF ISNULL(@fecha_de_corte) THEN
	SET @fecha_de_corte = NOW();
	END IF;
	RETURN @fecha_de_corte;
    END$$

DELIMITER ;


-- Divisor de  Interes
DELIMITER $$
DROP FUNCTION IF EXISTS `getDivisorDeInteres`$$
CREATE
    FUNCTION `getDivisorDeInteres`()
    RETURNS INT(4)
    BEGIN
	DECLARE mDIV INT(4) DEFAULT 0;
		IF ISNULL(@divisor_de_interes)  THEN

		SET mDIV = (SELECT valor_del_parametro  FROM entidad_configuracion WHERE nombre_del_parametro = 'divisor_en_dias_del_interes' LIMIT 0,1);

		SET @divisor_de_interes = mDIV;
	END IF;
	RETURN @divisor_de_interes;
    END$$

DELIMITER ;


-- -- Letras pendientes de pago
DELIMITER $$
DROP PROCEDURE IF EXISTS `proc_creditos_letras_pendientes`$$

CREATE PROCEDURE `proc_creditos_letras_pendientes`()
BEGIN

DROP VIEW IF EXISTS creditos_letras_pendientes;
DROP TABLE IF EXISTS creditos_letras_pendientes;

CREATE TABLE creditos_letras_pendientes AS  
(
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
CASE WHEN (`operaciones_mvtos`.`tipo_operacion` = 410  AND `operaciones_mvtos`.`fecha_afectacion` < getFechaDeCorte() ) THEN

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
GROUP BY `operaciones_mvtos`.`docto_afectado`

) ;

ALTER TABLE `creditos_letras_pendientes` ADD COLUMN `indice` INT(10) NOT NULL AUTO_INCREMENT AFTER `saldo_principal`, ADD PRIMARY KEY (`indice`);
ALTER TABLE `creditos_letras_pendientes` ADD INDEX `socio_afectado` (`socio_afectado`), ADD INDEX `docto_afectado` (`docto_afectado`);

END$$

DELIMITER ;



