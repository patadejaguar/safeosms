<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Solicita tu Crédito - Formoid bootstrap forms</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="blurBg-false" style="background-color: #EBEBEB">

	<script src="http://localhost/js/jscrypt/aes.js"></script>
	<script src="http://localhost/js/jscrypt/md5.js"></script>
	<script src="http://localhost/js/services.js.php"></script>

	<!-- Start Formoid form-->
	<link rel="stylesheet" href="./formoid1/formoid-solid-orange.css"
		type="text/css" />
	<script type="text/javascript" src="./formoid1/jquery.min.js"></script>

	<form class="formoid-solid-orange"
		style="background-color: #FFFFFF; font-size: 14px; font-family: 'Roboto', Arial, Helvetica, sans-serif; color: #34495E; max-width: 100%; min-width: 150px"
		method="post">
		<div class="title">
			<h2>Solicita tu Crédito</h2>
		</div>
		<div class="element-separator">
			<hr>
			<h3 class="section-break-title">Datos Personales</h3>
		</div>
		<div class="element-name">
			<label class="title"></label><span class="nameFirst"><input
				placeholder="Nombre Completo Nombres" type="text" size="8"
				name="name[first]" /><span class="icon-place"></span></span><span
				class="nameLast"><input placeholder="Nombre Completo Apellidos"
				type="text" size="14" name="name[last]" /><span class="icon-place"></span></span>
		</div>
		
		<div class="element-phone">
			<label class="title"><span class="required">*</span></label>
			<div class="item-cont">
				<input class="large" type="tel"
					pattern="[+]?[\.\s\-\(\)\*\#0-9]{3,}" maxlength="24" name="phone"
					required="required" placeholder="Numero Telefónico" value="" /><span
					class="icon-place"></span>
			</div>
		</div>
		<div class="element-email">
			<label class="title"></label>
			<div class="item-cont">
				<input class="large" type="email" name="email" value=""
					placeholder="Dirección de correo electrónico" /><span
					class="icon-place"></span>
			</div>
		</div>
		
		<div class="element-input">
			<label class="title"></label>
			<div class="item-cont">
				<input class="large" type="text" name="input"
					placeholder="Monto del Crédito Solicitado" /><span
					class="icon-place"></span>
			</div>
		</div>
				
		<div class="element-address">
			<label class="title"></label><span class="addr1"><input
				placeholder="Calle y número" type="text" name="address[addr1]" /><span
				class="icon-place"></span></span><span class="addr2"><input
				placeholder="Colonia" type="text" name="address[addr2]" /><span
				class="icon-place"></span></span><span class="city"><input
				placeholder="Ciudad" type="text" name="address[city]" /><span
				class="icon-place"></span></span>
				<!--  <span class="state"><input
				placeholder="Estado" type="text" name="address[state]" /><span
				class="icon-place"></span></span> -->
				<span class="zip"><input
				placeholder="Codigo Postal" type="text" maxlength="15"
				name="address[zip]" /><span class="icon-place"></span></span>
			<!-- <div class="country">

			</div>-->
		</div>
		<div class="element-separator">
			<hr>
			<h3 class="section-break-title">Datos del crédito</h3>
		</div>
		<div class="element-select">
			<label class="title"></label>
			<div class="item-cont">
				<div class="large">
					<span><select name="select">

							<option value="7-26">Semanal 6 Meses</option>
							<option value="7-52">Semanal 12 Meses</option>
							<option value="12-12">Quincenal 6 Meses</option>
							<option value="12-24">Quincenal 12 Meses</option>
					</select><i></i><span class="icon-place"></span></span>
				</div>
			</div>
		</div>
		<div class="element-input">
			<label class="title"></label>
			<div class="item-cont">
				<input class="large" type="text" name="input"
					placeholder="Monto del Crédito Solicitado" /><span
					class="icon-place"></span>
			</div>
		</div>
		<div class="submit">
			<input type="submit" value="Enviar!" />
		</div>
	</form>

	<script type="text/javascript" src="./formoid1/formoid-solid-orange.js"></script>
	<!-- Stop Formoid form-->



</body>
</html>