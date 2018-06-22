DROP TABLE IF EXISTS `fly_ultimos_recibos`;
CREATE TEMPORARY TABLE `fly_ultimos_recibos` (INDEX(`credito`))
AS (
SELECT
	`operaciones_recibos`.`docto_afectado`        AS `credito`,
	MAX(`operaciones_recibos`.`fecha_operacion`)       AS `fecha`,
	MAX(`operaciones_recibos`.`idoperaciones_recibos`) AS `recibo`
FROM
	`operaciones_recibos` `operaciones_recibos` 
WHERE
	(`operaciones_recibos`.`tipo_docto` =2)
GROUP BY `operaciones_recibos`.`docto_afectado`);

UPDATE `creditos_solicitud`,`fly_ultimos_recibos` SET `fecha_ultimo_capital` = `fly_ultimos_recibos`.`fecha`, `recibo_ultimo_capital`=`fly_ultimos_recibos`.`recibo` WHERE `fly_ultimos_recibos`.`credito`=`creditos_solicitud`.`numero_solicitud`;



