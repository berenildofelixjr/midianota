<?php

function gera_xml($cadastro) {
  return "k";
};

if ($btExportar != "") {
    if (($cmbMes != "") || ($cmbAno != "")) {
        if ($cmbMes <= 9) {
            $cmbMes = "0" . $cmbMes;
        }
        $periodo = $cmbAno . "-" . $cmbMes;
        $sql = mysql_query("
        SELECT
            '' as assinatura,
            notas.numero as numero_nfe,
            notas.codverificacao as codigo_verificacao,
            notas.datahoraemissao as data_emissao_nfe,
            replace(substring( notas.datahoraemissao, 1, 7), '-', '') as competencia,
            '' as numero_nfe_substituida,
            notas.natureza_operacao as natureza_da_operacao,
            '' as regime_especial_tributacao,
            if((cadastro.codtipodeclaracao=1), 1, 2) as optante_simples_nacional,
            '2' as incentivador_cultural,
                notas.rps_numero as numero_rps,        
            '' as serie_rps,
            '' as tipo_rps,
            '' as data_emissao_rps,
            '' as outras_informacoes,

            notas.basecalculo as valor_servicos,
            '' as valor_deducoes,
            '' as valor_pis,
            notas.cofins as valor_cofins,
            notas.valorinss as valor_inss,
            '' as valor_ir,
            '' as valor_csll,
            notas_servicos.codservico as item_lista_servico,
            '' as codigo_cnae,
            '' as codigo_tributacao_município,
            notas.basecalculo as base_calculo,
            '' as aliquota_servicos,
            notas.valoriss as valor_iss,
            '' as valor_liquido_nfe,
            '' as outras_retencoes,
            '' as valor_credito,
                notas.necessita_iss_retido as iss_retido,
            '' as valor_iss_retido,
            '' as valor_desconto_incondicionado,
            '' as valor_desconto_condicionado,
            notas.discriminacao as discriminacao_servicos,
            notas.municipio_prestacao_servico as municipio_prestacao_servico,

            '' as inscricao_prestador,
            cadastro.razaosocial as razao_social_prestador,
            '' as nome_fantasia_prestador,
                cadastro.cnpj as cnpj_prestador,
            concat(cadastro.logradouro, ',', cadastro.complemento, ',', cadastro.bairro, ',', cadastro.`cep`,'-', cadastro.`uf`) as endereco_prestador,
            cadastro.numero,
            '' as complemento_endereço_prestador,
                cadastro.bairro as bairro_prestador,
            cadastro.municipio as cidade_prestador,
            cadastro.uf as uf_prestador,
            cadastro.cep as cep_prestador,
                '' as email_prestador,
            '' as telefone_prestador,

            notas.tomador_cnpjcpf as cpf_cnpj_tomador,
            if( (length(notas.tomador_cnpjcpf) = 14), '1', '2') as indicacao_cpf_cnpj,
            notas.tomador_inscrmunicipal as inscricao_municipal_tomador,
            '' as razao_social_tomador,
            notas.tomador_logradouro as endereco_tomador,
            notas.tomador_numero as numero_endereco_tomador,
            notas.tomador_complemento as complemento_endereco_tomador,
            notas.tomador_bairro as bairro_tomador,


            '' as razao_social_intermediario_servico,
            '' as inscricao_municipal_intermediario_servico,
            '' as cnpj_intermediario_sevico,

            '01108' as codigodo_municipio_gerador,
            '52' as uf_municipio_gerador,

            '' as codigo_obra,
            '' as art    
        FROM 
            notas
        INNER JOIN 
            notas_servicos ON notas_servicos.codnota = notas.codigo		
        INNER JOIN 
            servicos ON notas_servicos.codservico = servicos.codigo
        INNER JOIN 
            cadastro ON notas.codemissor = cadastro.codigo
        where
            cadastro.codtipo = 1
        and datahoraemissao LIKE '%$periodo%' AND notas.codemissor = '$CODIGO_DA_EMPRESA' ORDER BY datahoraemissao ");

        $arquivo = $CODIGO_DA_EMPRESA . "arquivo2012.xml";
        $fp = fopen("tmp/" . $arquivo, "w");
        
        $dom = new DOMDocument("1.0", "ISO-8859-1");
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $root = $dom->createElement("EnviarLoteRpsEnvio");
        $root->setAttribute("xmlns", "http://www.issnetonline.com.br/webserviceabrasf/vsd/servico_enviar_lote_rps_envio.xsd");
        $root->setAttribute("xmlns:tc", "http://www.issnetonline.com.br/webserviceabrasf/vsd/tipos_complexos.xsd");

        $LoteRps = $dom->createElement("LoteRps");
        $LoteRps->appendChild($dom->createElement("tc:NumeroLote", 1)); //TODO: incrementar automaticamente
        $LoteRps->appendChild($dom->createElement("tc:InscricaoMunicipal", 1)); //TODO: numero de inscrição municipal ??
        

        $ListaRps = $dom->createElement("tc:ListaRps");

        $qtde = 0;

        while ($cadastro = mysql_fetch_array($sql)) {
          $qtde++;

          $Rps = $dom->createElement("tc:Rps");
          $tcInfRps = $dom->createElement("tc:InfRps");

          $IdentificacaoRps = $dom->createElement("tc:IdentificacaoRps");
          $IdentificacaoRps->appendChild($dom->createElement("tc:Numero", $cadastro["numero_rps"])); //TODO: qual número é esse ?
          $IdentificacaoRps->appendChild($dom->createElement("tc:Serie", $cadastro["serie_rps"])); //TODO: qual número é esse ?
          $IdentificacaoRps->appendChild($dom->createElement("tc:Tipo", $cadastro["tipo_rps"])); //TODO: qual número é esse ?
          
          $tcInfRps->appendChild($IdentificacaoRps);

          $tcInfRps->appendChild($dom->createElement("tc:DataEmissao", $cadastro["data_emissao_nfe"])); //TODO: pegar data de emissão verdadeira
          $tcInfRps->appendChild($dom->createElement("tc:NaturezaOperacao", $cadastro["natureza_da_operacao"])); //TODO: aonde usa essa natureza de operação
          $tcInfRps->appendChild($dom->createElement("tc:RegimeEspecialTributacao", $cadastro["regime_especial_tributacao"])); //TODO: usar regime de tributação
          $tcInfRps->appendChild($dom->createElement("tc:OptanteSimplesNacional", $cadastro["optante_simples_nacional"])); //TODO: sim, não
          $tcInfRps->appendChild($dom->createElement("tc:IncentivadorCultural",  $cadastro["incentivador_cultural"])); //TODO: sim, não
          $tcInfRps->appendChild($dom->createElement("tc:Status", "data")); //TODO: sim, não


          $tcDadosServico = $dom->createElement("tc:Servico");

          $tcValores = $dom->createElement("tc:Valores");

          $tcValores->appendChild($dom->createElement("tc:ValorServicos", $cadastro["valor_servicos"])); //TODO: ajustar para valor dos serviços
          //$tcValores->appendChild($dom->createElement("tc:ValorDeducoes", $cadastro["valor_deducoes"])); //TODO: ajustar para valor de deduções
          //$tcValores->appendChild($dom->createElement("tc:ValorPis", $cadastro["valor_pis"])); //TODO: ajustar para valor do pis
          $tcValores->appendChild($dom->createElement("tc:ValorCofins", $cadastro["valor_cofins"])); //TODO: ajustar para valor do cofins
          $tcValores->appendChild($dom->createElement("tc:ValorInss", $cadastro["valor_inss"])); //TODO: ajustar para valor do Inss
          //$tcValores->appendChild($dom->createElement("tc:ValorIr", $cadastro["valor_ir"])); //TODO: ajustar para valor do Ir
          //$tcValores->appendChild($dom->createElement("tc:ValorCsll", $cadastro["valor_csll"])); //TODO: ajustar para valor do Csll
          $tcValores->appendChild($dom->createElement("tc:IssRetido", $cadastro["iss_retido"])); //TODO: ajustar para se o iss foi retido
          $tcValores->appendChild($dom->createElement("tc:ValorIss", $cadastro["valor_iss"])); //TODO: ajustar para o valor que o iss foi redito
          $tcValores->appendChild($dom->createElement("tc:ValorIssRetido", $cadastro["iss_retido"])); //TODO: ajustar para o valor que o iss foi redito
          //$tcValores->appendChild($dom->createElement("tc:OutrasRetencoes", $cadastro["outras_retencoes"])); //TODO: ajustar para o valor das outras retenções
          $tcValores->appendChild($dom->createElement("tc:BaseCalculo", $cadastro["base_calculo"])); //TODO: ajustar para o valor das outras retenções
          //$tcValores->appendChild($dom->createElement("tc:Aliquota", $cadastro["aliquota_servicos"])); //TODO: ajustar para o valor das outras retenções
          //$tcValores->appendChild($dom->createElement("tc:ValorLiquidoNfse", $cadastro["valor_liquido_nfe"])); //TODO: ajustar para o valor das outras retenções
          //$tcValores->appendChild($dom->createElement("tc:DescontoIncondicionado", $cadastro["valor_desconto_incondicionado"])); //TODO: ajustar para o valor das outras retenções
          //$tcValores->appendChild($dom->createElement("tc:DescontoCondicionado", $cadastro["valor_desconto_condicionado"])); //TODO: ajustar para o valor das outras retenções

          
          $tcDadosServico->appendChild($tcValores);

          $tcDadosServico->appendChild($dom->createElement("tc:ItemListaServico", "10")); //TODO: ajustar para o valor das outras retenções
          $tcDadosServico->appendChild($dom->createElement("tc:CodigoCnae", $cadastro["codigo_cnae"])); //TODO: ajustar para o valor das outras retenções
          $tcDadosServico->appendChild($dom->createElement("tc:CodigoTributacaoMunicipio", $cadastro["codigo_tributacao_município"]));
          $tcDadosServico->appendChild($dom->createElement("tc:Discriminacao", $cadastro["discriminacao_servicos"]));
          $tcDadosServico->appendChild($dom->createElement("tc:MunicipioPrestacaoServico", $cadastro["municipio_prestacao_servico"])); //TODO: ajustar para o valor das outras retenções

          $tcInfRps->appendChild($tcDadosServico);

          $tcIdentificacaoPrestador = $dom->createElement("tc:Prestador");
          
          $tcCpfCnpj = $dom->createElement("tc:CpfCnpj");
          $tcCpfCnpj->appendChild($dom->createElement("tc:Cnpj", $cadastro["cnpj_prestador"])); //TODO CNPJ ou CPF

          $tcIdentificacaoPrestador->appendChild($tcCpfCnpj);
          $tcIdentificacaoPrestador->appendChild($dom->createElement("tc:InscricaoMunicipal", $cadastro["inscricao_prestador"]));

          $tcInfRps->appendChild($tcIdentificacaoPrestador);

          $tcDadosTomador = $dom->createElement("tc:Tomador");
          $tcIdentificacaoTomador = $dom->createElement("tc:IdentificacaoTomador");

          $tcCpfCnpj = $dom->createElement("tc:CpfCnpj");

          if (strlen($cadastro["cpf_cnpj_tomador"]) == 14) {
            $tcCpfCnpj->appendChild($dom->createElement("tc:Cpf", $cadastro["cpf_cnpj_tomador"]));
          } else {
            $tcCpfCnpj->appendChild($dom->createElement("tc:Cnpj", $cadastro["cpf_cnpj_tomador"]));
          }

          $tcIdentificacaoTomador->appendChild($tcCpfCnpj);
          $tcIdentificacaoTomador->appendChild($dom->createElement("tc:InscricaoMunicipal", $cadastro["inscricao_municipal_tomador"]));
          
          $tcDadosTomador->appendChild($tcIdentificacaoTomador);
          $tcDadosTomador->appendChild($dom->createElement("tc:RazaoSocial", $cadastro["razao_social_tomador"]));

          $tcEndereco = $dom->createElement("tc:Endereco");
          $tcEndereco->appendChild($dom->createElement("tc:Endereco", $cadastro["endereco_tomador"]));
          $tcEndereco->appendChild($dom->createElement("tc:Numero", $cadastro["numero_endereco_tomador"]));
          $tcEndereco->appendChild($dom->createElement("tc:Complemento", $cadastro["complemento_endereco_tomador"]));
          $tcEndereco->appendChild($dom->createElement("tc:Bairro", $cadastro["bairro_tomador"]));
          /*$tcEndereco->appendChild($dom->createElement("tc:Cidade", 28));
          $tcEndereco->appendChild($dom->createElement("tc:Estado", 28));
          $tcEndereco->appendChild($dom->createElement("tc:Cep", 28));*/

          $tcDadosTomador->appendChild($tcEndereco);

          $tcInfRps->appendChild($tcDadosTomador);

          $Rps->appendChild($tcInfRps);
          $ListaRps->appendChild($Rps);
          
          
          /*$registros = $cadastro["assinatura"] . ";"
                        . $cadastro["numero_nfe"] . ";"
                        . $cadastro["codigo_verificacao"] . ";"
                        . $cadastro["data_emissao_nfe"] . ";"
                        . $cadastro["competencia"] . ";"
                        . $cadastro["numero_nfe_substituida"] . ";"
                        . $cadastro["natureza_da_operacao"] . ";"
                        . $cadastro["regime_especial_tributacao"] . ";"
                        . $cadastro["optante_simples_nacional"] . ";"
                        . $cadastro["incentivador_cultural"] . ";"
                        . $cadastro["numero_rps"] . ";"
                        . $cadastro["serie_rps"] . ";"
                        . $cadastro["tipo_rps"] . ";"
                        . $cadastro["data_emissao_rps"] . ";"
                        . $cadastro["outras_informacoes"] . ";"
                        . $cadastro["valor_servicos"] . ";"
                        . $cadastro["valor_deducoes"] . ";"
                        . $cadastro["valor_pis"] . ";"
                        . $cadastro["valor_cofins"] . ";"
                        . $cadastro["valor_inss"] . ";"
                        . $cadastro["valor_ir"] . ";"
                        . $cadastro["valor_csll"] . ";"
                        . $cadastro["item_lista_servico"] . ";"
                        . $cadastro["codigo_cnae"] . ";"
                        . $cadastro["codigo_tributacao_município"] . ";"
                        . $cadastro["base_calculo"] . ";"
                        . $cadastro["aliquota_servicos"] . ";"
                        . $cadastro["valor_iss"] . ";"
                        . $cadastro["valor_liquido_nfe"] . ";"
                        . $cadastro["outras_retencoes"] . ";"
                        . $cadastro["valor_credito"] . ";"
                        . $cadastro["iss_retido"] . ";"
                        . $cadastro["valor_iss_retido"] . ";"
                        . $cadastro["valor_desconto_incondicionado"] . ";"
                        . $cadastro["valor_desconto_condicionado"] . ";"
                        . $cadastro["discriminacao_servicos"] . ";"
                        . $cadastro["municipio_prestacao_servico"] . ";"
                        . $cadastro["inscricao_prestador"] . ";"
                        . $cadastro["razao_social_prestador"] . ";"
                        . $cadastro["nome_fantasia_prestador"] . ";"
                        . $cadastro["cnpj_prestador"] . ";"
                        . $cadastro["endereco_prestador"] . ";"
                        . $cadastro["numero"] . ";"
                        . $cadastro["complemento_endereco_prestador"] . ";"
                        . $cadastro["bairro_prestador"] . ";"
                        . $cadastro["cidade_prestador"] . ";"
                        . $cadastro["uf_prestador"] . ";"
                        . $cadastro["cep_prestador"] . ";"
                        . $cadastro["email_prestador"] . ";"
                        . $cadastro["telefone_prestador"] . ";"
                        . $cadastro["cpf_cnpj_tomador"] . ";"
                        . $cadastro["indicacao_cpf_cnpj"] . ";"
                        . $cadastro["inscricao_municipal_tomador"] . ";"
                        . $cadastro["razao_social_tomador"] . ";"
                        . $cadastro["endereco_tomador"] . ";"
                        . $cadastro["numero_endereco_tomador"] . ";"
                        . $cadastro["complemento_endereco_tomador"] . ";"
                        . $cadastro["bairro_tomador"] . ";"
                        . $cadastro["razao_social_intermediario_servico"] . ";"
                        . $cadastro["inscricao_municipal_intermediario_servico"] . ";"
                        . $cadastro["cnpj_intermediario_sevico"] . ";"
                        . $cadastro["codigodo_municipio_gerador"] . ";"
                        . $cadastro["uf_municipio_gerador"] . ";"
                        . $cadastro["codigo_obra"] . ";"
                        . $cadastro["art"] . ";\n";

                        fwrite($fp, $registros);*/
        }

        $LoteRps->appendChild($dom->createElement("tc:QuantidadeRps", $qtde));
        $LoteRps->appendChild($ListaRps);

        $root->appendChild($LoteRps);

        $dom->appendChild($root);
        fwrite($fp, $dom->saveXML());
        fclose($fp);
    } else {
        print("<script language=JavaScript>alert('Selecione um mês e um ano!!')</script>");
    }
}
?>
<form action="exportar.php" method="post" name="frmPagamento" >   
    <table border="0" align="center" cellpadding="0" cellspacing="1">
        <tr>
            <td width="10" height="10" bgcolor="#FFFFFF"></td>
            <td width="100" align="center" bgcolor="#FFFFFF" rowspan="3">Exportar Notas</td>
            <td width="470" bgcolor="#FFFFFF"></td>
        </tr>
        <tr>
            <td height="1" bgcolor="#CCCCCC"></td>
            <td bgcolor="#CCCCCC"></td>
        </tr>
        <tr>
            <td height="10" bgcolor="#FFFFFF"></td>
            <td bgcolor="#FFFFFF"></td>
        </tr>
        <tr>
            <td colspan="3" height="1" bgcolor="#CCCCCC"></td>
        </tr>
        <tr>
            <td height="60" colspan="3" bgcolor="#CCCCCC">
                <table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">	       
                    <tr>
                        <td align="left" width="30%">Período das Notas</td>
                        <td align="left" width="70%">
                            <select name="cmbMes" class="combo">
                                <option value="">== Mês ==</option>
                                <?php
                                $meses = array(1 => "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
                                foreach ($meses as $num => $mes) {
                                    echo "<option value='$num' ";
                                    if ($cmbMes == $num) {
                                        echo "selected=selected";
                                    }
                                    echo ">$mes</option>";
                                }
                                ?>
                            </select> / 
                            <select name="cmbAno" class="combo">
                                <option value="">==ANO==</option>
                                <?php
                                $ano = date("Y");
                                for ($x = 0; $x <= 4; $x++) {
                                    $year = $ano - $x;
                                    echo "<option value='$year' ";
                                    if ($cmbAno == $year) {
                                        echo "selected=selected";
                                    }
                                    echo ">$year</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>	  
                    <tr>
                        <td colspan="2" align="center"><input type="submit" value="Exportar" name="btExportar" class="botao" /></td>
                    </tr>
                    <td colspan="2" align="center">
                        <?php
                        if (($btExportar != "") && ($cmbMes != "") && ($cmbAno != "")) {
                            print("Exportação concluída com sucesso!<br>
	  <a href='../download?/emissor/tmp/$arquivo'><img src='../img/imgcsv.jpg' border='0'></a> &nbsp; 
	  <a href='../download?/emissor/tmp/$arquivo'>Clique aqui</a> para baixar o arquivo");
                        }
                        ?>

                    </td>
        </tr>   
    </table> 

</td>
</tr>
<tr>
    <td height="1" colspan="3" bgcolor="#CCCCCC"></td>
</tr>
</table>      
</form>
