<?php

session_name("emissor");
session_start();
if (!(isset($_SESSION["empresa"]))) {
    echo "
		<script>
		alert('Acesso Negado!');
		window.location='login.php';
		</script>
	";
} else {
    $botao = $_POST['btImportarXML'];
    $arquivo_xml = $_POST['txtArquivoNome'];
    if ($botao == "Importar Arquivo") {
        include("../include/conect.php");
        include("../funcoes/util.php");
        include("inc/funcao_logs.php");
        $sql = mysql_query("SELECT ultimanota FROM cadastro WHERE codigo = '$CODIGO_DA_EMPRESA'");
        list($UltimaNota) = mysql_fetch_array($sql);

        $sql = mysql_query("SELECT codigo FROM cadastro WHERE codigo = '$CODIGO_DA_EMPRESA'");
        list($codigoEmpresa) = mysql_fetch_array($sql);

        $xml = simplexml_load_file("importar/$arquivo_xml");
        $cont = 0;
        $inserir_tomador = "N";
        foreach ($xml->children() as $elemento => $valor) {

            $tomador_cnpjcpf = $xml->nota[$cont]->tomador_cnpjcpf;
            $sql_verifica_tomador = mysql_query("
				SELECT 
					nome,
					inscrmunicipal,
					logradouro,
					numero,
					complemento,
					bairro,
					cep,
					municipio,
					uf,
					email
				FROM 
					cadastro 
				WHERE 
					(cpf = '$tomador_cnpjcpf' OR cnpj = '$tomador_cnpjcpf')
			");
            if (mysql_num_rows($sql_verifica_tomador)) {
                $dadosTomador = mysql_fetch_array($sql_verifica_tomador);
                $tomador_inscrmunicipal = $dadosTomador['inscrmunicipal'];
                $tomador_nome = $dadosTomador['nome'];
                $tomador_logradouro = $dadosTomador['logradouro'];
                $tomador_numero = $dadosTomador['numero'];
                $tomador_complemento = $dadosTomador['complemento'];
                $tomador_bairro = $dadosTomador['bairro'];
                $tomador_cep = $dadosTomador['cep'];
                $tomador_municipio = $dadosTomador['municipio'];
                $tomador_uf = $dadosTomador['uf'];
                $tomador_email = $dadosTomador['email'];
            } else {
                $tomador_inscrmunicipal = $xml->nota[$cont]->tomador_inscrmunicipal;
                $tomador_nome = $xml->nota[$cont]->tomador_nome;
                $tomador_logradouro = $xml->nota[$cont]->tomador_logradouro;
                $tomador_numero = $xml->nota[$cont]->tomador_numero;
                $tomador_complemento = $xml->nota[$cont]->tomador_complemento;
                $tomador_bairro = $xml->nota[$cont]->tomador_bairro;
                $tomador_cep = $xml->nota[$cont]->tomador_cep;
                $tomador_municipio = $xml->nota[$cont]->tomador_municipio;
                $tomador_uf = $xml->nota[$cont]->tomador_uf;
                $tomador_email = $xml->nota[$cont]->tomador_email;
                $inserir_tomador = "S";
            }
            $discriminacao = $xml->nota[$cont]->discriminacao;
            $observacao = $xml->nota[$cont]->observacao;
            $aliqinss = $xml->nota[$cont]->aliqinss;
            $aliqirrf = $xml->nota[$cont]->aliqirrf;
            $valordeducoes = $xml->nota[$cont]->valordeducoes;
            $rps_numero = $xml->nota[$cont]->rps_numero;
            $rps_data = $xml->nota[$cont]->rps_data;
            $estado = $xml->nota[$cont]->estado;
            $deducaoirrf = $xml->nota[$cont]->deducaoirrf;

            $sql_verifica_rps = mysql_query("SELECT codigo FROM notas WHERE rps_numero = '$rps_numero' AND codemissor = '$CODIGO_DA_EMPRESA'");
            if (mysql_num_rows($sql_verifica_rps)) {
                Mensagem('A nota com o número de RPS $rps_numero, já foi emitida!');
                exit;
            }

            switch (strtolower($estado)) {
                case "normal":
                    $estado = "N";
                    break;
                case "escriturado":
                    $estado = "E";
                    break;
                case "cancelado":
                    $estado = "C";
                    break;
                case "boleto":
                    $estado = "B";
                    break;
            }

            $CaracteresAceitos = 'ABCDEFGHIJKLMNOPQRXTUVWXYZ';
            $max = strlen($CaracteresAceitos) - 1;
            $password = null;
            for ($i = 0; $i < 8; $i++) {
                $password .= $CaracteresAceitos{mt_rand(0, $max)};
                $carac = strlen($password);
                if ($carac == 4) {
                    $password .= "-";
                }
            }

            if ($inserir_tomador == "S") {
                $campo = tipoPessoa($tomador_cnpjcpf);
                $codTipoTomador = codtipo('tomador');
                $codTipoDec = coddeclaracao('DES Consolidada');
                $datainicio = date("Y-m-d");
                mysql_query("
					INSERT INTO
						cadastro
					SET
						nome              = '$tomador_nome',
						codtipo           = '$codTipoTomador',
						codtipodeclaracao = '$codTipoDec',
						razaosocial       = '$tomador_nome',
						$campo            = '$tomador_cnpjcpf',
						inscrmunicipal    = '$tomador_inscrmunicipal',
						logradouro        = '$tomador_logradouro',
						numero            = '$tomador_numero',
						complemento       = '$tomador_complemento',
						bairro            = '$tomador_bairro',
						cep               = '$tomador_cep',
						uf                = '$tomador_uf',
						email             = '$tomador_email',
						municipio         = '$tomador_municipio',
						estado            = 'A',
						datainicio        = '$datainicio'
				") or die(mysql_error());
            }

            $dataAtual = date("Y-m-d");
            $horaAtual = date("H:i:s");

            $sql_numero = mysql_query("SELECT ultimanota FROM cadastro WHERE codigo = '$CODIGO_DA_EMPRESA'");
            list($max_numero) = mysql_fetch_array($sql_numero);
            $max_numero++;

            mysql_query("
				INSERT INTO 
					notas 
				SET 
					numero = '$max_numero', 
					codverificacao = '$password', 
					codemissor = '$CODIGO_DA_EMPRESA', 
					rps_numero = '$rps_numero', 
					rps_data = '$rps_data',
					tomador_nome = '$tomador_nome', 
					tomador_cnpjcpf = '$tomador_cnpjcpf',
					tomador_inscrmunicipal = '$tomador_inscrmunicipal',		
					tomador_logradouro = '$tomador_logradouro',
					tomador_numero = '$tomador_numero',
					tomador_complemento = '$tomador_complemento',
					tomador_bairro = '$tomador_bairro',		 
					tomador_cep = '$tomador_cep', 
					tomador_municipio = '$tomador_municipio',
					tomador_uf = '$tomador_uf',
					tomador_email = '$tomador_email', 
					discriminacao = '$discriminacao',
					valortotal = NULL, 
					valordeducoes = '$valordeducoes', 
					basecalculo = NULL,
					valoriss = NULL, 
					credito = NULL, 
					estado = '$estado',
					datahoraemissao = '$dataAtual $horaAtual', 
					issretido = NULL, 
					valorinss = NULL, 
					aliqinss = '$aliqinss',
					valorirrf = NULL, 
					aliqirrf = '$aliqirrf', 
					deducao_irrf = '$deducaoirrf',
					total_retencao = NULL,
					observacao = '$observacao',
					tipoemissao = 'importada'
			") or die(mysql_error());

            $codUltimaNota = mysql_insert_id();

            $contServicos = 0;
            $totalBaseCalculo = 0;
            $totalISS = 0;
            $totalISSRetido = 0;
            foreach ($xml->nota[$cont]->codservico[$contServicos]->children() as $elemento2 => $valor2) {
                $codservico = $xml->nota[$cont]->codservico[$contServicos]->codservico;
                $basecalculo = $xml->nota[$cont]->codservico[$contServicos]->basecalculo;
                $issretido = $xml->nota[$cont]->codservico[$contServicos]->issretido;


                $sql_servicos = mysql_query("SELECT codigo, descricao, aliquota FROM servicos WHERE codservico = '$codservico'");
                list($servicoCodigo, $servicosDescricao, $servicoAliquota) = mysql_fetch_array($sql_servicos);


                if ($basecalculo > 0) {
                    $iss = ($basecalculo * $servicoAliquota) / 100;
                    if ($issretido > 0) {
                        if ($issretido > $iss) {
                            $issretido = $iss;
                        }
                    }
                }

                if ($servicosDescricao) {
                    $curtaDescricao = ResumeString($servicosDescricao, 50);

                    mysql_query("
						INSERT INTO
							notas_servicos
						SET
							codnota     = '$codUltimaNota',
							codservico  = '$servicoCodigo',
							basecalculo = '$basecalculo',
							issretido   = '$issretido',
							iss         = '$iss'
					") or die(mysql_error());


                    $totalBaseCalculo += floatval($basecalculo);
                    $totalISS += floatval($iss);
                    $totalISSRetido += floatval($issretido);
                }
                $contServicos++;
            }

            $valorINSS = ($totalBaseCalculo * $aliqinss) / 100;
            $valorIRRF = ($totalBaseCalculo * $aliqirrf) / 100;
            $valorTotalRetencoes = ($valorINSS + $valorIRRF) + $totalISSRetido;
            $valorTotalNota = ($totalBaseCalculo - $totalISSRetido) + $valordeducoes;


            if (strlen($tomador_cnpjcpf) == 14) {
                if ($totalISSRetido > 0) {
                    $tipo_pessoa = "PF";
                    $iss_retido = "S";
                } else {
                    $tipo_pessoa = "PF";
                    $iss_retido = "N";
                }
            } elseif (strlen($tomador_cnpjcpf) == 18) {
                if ($totalISSRetido > 0) {
                    $tipo_pessoa = "PJ";
                    $iss_retido = "S";
                } else {
                    $tipo_pessoa = "PJ";
                    $iss_retido = "N";
                }
            }

            $sql = mysql_query("SELECT credito, valor FROM nfe_creditos WHERE estado = 'A' AND tipopessoa LIKE '%$tipo_pessoa%' AND issretido = '$iss_retido' ORDER BY valor DESC");
            if (mysql_num_rows($sql) > 0) {
                while (list($nfe_cred, $nfe_valor) = mysql_fetch_array($sql)) {
                    if ($valortotal <= $nfe_valor) {
                        $credito = $nfe_cred;
                    }
                }
                if ($credito == "") {
                    $sql_max_cred = mysql_query("SELECT credito FROM nfe_creditos WHERE estado = 'A' ORDER BY valor DESC LIMIT 1");
                    list($cred_max) = mysql_fetch_array($sql_max_cred);
                    $credito = $cred_max;
                }
            } else {
                $credito = 0;
            }

            $valorCredito = ($totalISS * $credito) / 100;

            mysql_query("
				UPDATE 
					notas 
				SET
					valortotal     = '$valorTotalNota', 
					basecalculo    = '$totalBaseCalculo',
					valoriss       = '$totalISS', 
					credito        = '$valorCredito', 
					issretido      = '$totalISSRetido', 
					valorinss      = '$valorINSS', 
					valorirrf      = '$valorIRRF', 
					total_retencao = '$valorTotalRetencoes'
				WHERE
					codigo = '$codUltimaNota' 
			") or die(mysql_error());

            $sql = mysql_query("SELECT ultimanota FROM cadastro WHERE codigo = '$CODIGO_DA_EMPRESA'");
            list($ultimaNota) = mysql_fetch_array($sql);
            $ultimaNota += 1;

            $sql = mysql_query("UPDATE cadastro SET ultimanota = '$ultimaNota' WHERE codigo = '$CODIGO_DA_EMPRESA'") or die(mysql_error());

            $cont++;
        }
        unlink("importar/$arquivo_xml");
        add_logs('Importou Arquivo');
        print("<script language=JavaScript>alert('Importação efetuada com sucesso !');window.close();</script>");
    } else {
        print("Acesso Negado!!");
    }
}
?>