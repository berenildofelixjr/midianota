<?php 
$senha  = $_POST['txtSenha'];
$email  = $_POST['txtEmail'];
$codigo = $_POST['hdCod'];
if($arquivo !=""){
// Upload e redimensionamento da Imagem------------------------------------------

	error_reporting(E_ALL);

	$arquivo = Uploadimagem("arquivo","/var/www/prd/img/logos/",$codigo,1);
	if($arquivo != NULL){
		//Cria uma copia do arquivo pdf na pasta do issdigital
		$file = "/var/www/prd/img/logos/$arquivo";
		$newfile = "/var/www/prd/sep/img/logos/$arquivo";
		
		if(!copy($file, $newfile)) {
			echo "falha ao copiar $file...\n";
		}
		$sql = mysql_query("UPDATE cadastro SET logo = '$arquivo' WHERE nome = '$NOME'");
		$imagem = "Atualizada";
	}else{
		Mensagem("O logo deve ter, no m�ximo 100 pixels de altura por 100 pixels de largura");
		Redireciona("empresas.php");
	}
} 
//--------------------Update------------------------------  
$sql_mudanca = mysql_query("SELECT email, senha FROM cadastro WHERE codigo = '$codigo'");
list($email_mysql,$senha_mysql) = mysql_fetch_array($sql_mudanca);
if(($email != $email_mysql) || ($senha != $senha_mysql) || ($imagem == "Atualizada")){
	
	$query = "UPDATE cadastro SET email = '$email'";
	
	if ($senha) {
		$query .= ", senha = md5('$senha') ";
	}
	
	$query .= "WHERE nome = '$NOME'";
	
	$sql = mysql_query($query);
	echo "<script>alert('Empresa atualizada com sucesso');</script>";
	add_logs('Atualizou empresa');
}
?>
