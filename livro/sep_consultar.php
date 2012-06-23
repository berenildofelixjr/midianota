<div id="DivAbas"></div>    
<table border="0" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
  <tr>
    <td width="18" align="left" background="img/form/cabecalho_fundo.jpg"><img src="img/form/cabecalho_icone.jpg" /></td>
    <td width="700" background="img/form/cabecalho_fundo.jpg" align="left" class="formCabecalho">&nbsp;Livro Digital - Consulta</td>  
    <td width="19" align="right" valign="top" background="img/form/cabecalho_fundo.jpg"><a href=""><img src="img/form/cabecalho_btfechar.jpg" width="19" height="21" border="0" /></a></td>
  </tr>
  <tr>
    <td width="18" background="img/form/lateralesq.jpg"></td>
    <td align="center">    
		<form method="post" id="frmLivro">
			<input type="hidden" name="include" id="include" value="<?php echo $_POST["include"];?>" />
			<fieldset>
				<legend>Consulta ao Livro Digital</legend>
			<table align="left">
				<tr>
					<td>CNPJ/CPF Prestador</td>
					<td><input type="text" name="txtCnpjPrestador" class="texto" /></td>
				</tr>
				<tr>
					<td>Periodo Inicial</td>
					<td>
						<?php
						$meses=array("1"=>"Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
						
						?>
						<select name="cmbMesIni" id="cmbMesIni">
							<option value=""></option>
							<?php
							for($ind=1;$ind<=12;$ind++){
								echo "<option value='$ind'>{$meses[$ind]}</option>";
							}
							?>
						</select>    
						<select name="cmbAnoIni" id="cmbAnoIni">
							<option value=""></option>
							<?php
							$year=date("Y");
							for ($h = 0; $h < 5; $h++) {
								$y = $year - $h;
								echo "<option value=\"$y\""; if($y == $ano){ echo " selected=\"selected\" ";} echo ">$y</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Periodo Final</td>
					<td>
						<select name="cmbMesFim" id="cmbMesFim">
							<option value=""></option>
							<?php
							for($ind=1;$ind<=12;$ind++){
								echo "<option value='$ind'>{$meses[$ind]}</option>";
							}
							?>
						</select>    
						<select name="cmbAnoFim" id="cmbAnoFim">
							<option value=""></option>
							<?php
							$year=date("Y");
							for ($h = 0; $h < 5; $h++) {
								$y = $year - $h;
								echo "<option value=\"$y\""; if($y == $ano){ echo " selected=\"selected\" ";} echo ">$y</option>";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="btnBuscar" value="Buscar" class="botao" onclick="btnBuscar_click(); return false;" />
					</td>
				</tr>
			</table>
			</fieldset>
			<div id="dvResultdoLivro"></div>
		</form>
        
    </td>
	<td width="19" background="img/form/lateraldir.jpg"></td>
  </tr>
  <tr>
    <td align="left" background="img/form/rodape_fundo.jpg"><img src="img/form/rodape_cantoesq.jpg" /></td>
    <td background="img/form/rodape_fundo.jpg"></td>
    <td align="right" background="img/form/rodape_fundo.jpg"><img src="img/form/rodape_cantodir.jpg" /></td>
  </tr>
</table>
<script type="text/javascript">
	function btnBuscar_click() {
		acessoAjax('../livro/sep_consultar.ajax.php','frmLivro','dvResultdoLivro');
	}
	
</script>
