<?php

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

            '' as cpf_cnpj_tomador,
            if( (length(notas.tomador_cnpjcpf) = 14), '1', '2') as indicacao_cpf_cnpj,
            '' as inscricao_municipal_tomador,
            '' as razao_social_tomador,
            '' as endereco_tomador,
            '' as numero_endereco_tomador,
            '' as complemento_endereco_tomador,
            '' as bairro_tomador,


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

        $arquivo = $CODIGO_DA_EMPRESA . "arquivo2012.csv";
        $fp = fopen("tmp/" . $arquivo, "w");
        $cabecario = "assinatura;numero_nfe;codigo_verificacao;data_emissao_nfe;".
                    "competencia;numero_nfe_substituida;natureza_da_operacao;regime_especial_tributacao;".
                "optante_simples_nacional;incentivador_cultural;numero_rps;serie_rps;".
                "tipo_rps;data_emissao_rps;outras_informacoes;valor_servicos;valor_deducoes;valor_pis;valor_cofins;valor_inss;".
                "valor_ir;valor_csll;item_lista_servico;codigo_cnae;codigo_tributacao_município;base_calculo;".
                "aliquota_servicos;valor_iss;valor_liquido_nfe;outras_retencoes;valor_credito;iss_retido;valor_iss_retido;".
                "valor_desconto_incondicionado;valor_desconto_condicionado;discriminacao_servicos;municipio_prestacao_servico;".
                "inscricao_prestador;razao_social_prestador;nome_fantasia_prestador;cnpj_prestador;endereco_prestador;".
                "numero;complemento_endereco_prestador;bairro_prestador;cidade_prestador;uf_prestador;cep_prestador;".
                "email_prestador;telefone_prestador;cpf_cnpj_tomador;indicacao_cpf_cnpj;inscricao_municipal_tomador;".
                "razao_social_tomador;endereco_tomador;numero_endereco_tomador;complemento_endereco_tomador;bairro_tomador;".
                "razao_social_intermediario_servico;inscricao_municipal_intermediario_servico;cnpj_intermediario_sevico;".
                "codigodo_municipio_gerador;uf_municipio_gerador;codigo_obra;art;\n";
          
        fwrite($fp, $cabecario);

        while ($cadastro = mysql_fetch_array($sql)) {
          
             $registros = $cadastro["assinatura"] . ";"
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

            fwrite($fp, $registros);
        }
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
