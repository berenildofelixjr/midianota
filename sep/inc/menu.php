<?php 
if(!$_GET['m']){
	?>
	<link rel="stylesheet" type="text/css" href="css/menu_anylinkmenu.css" />
	<script type="text/javascript" src="scripts/menu_menucontents.js"></script>
	<script type="text/javascript" src="scripts/menu_anylinkmenu.js"></script>
	<script type="text/javascript">
		anylinkmenu.init("menuanchorclass");
		var anylinkmenu2={divclass:'anylinkmenu', linktarget:'_new'};
		anylinkmenu2.items=[
			["a", "http://www.cnn.com/"],
			["s", "http://www.msnbc.com/"],
			["d", "http://www.google.com/"],
			["c", "http://news.bbc.co.uk"]
		];
		var anylinkmenu3={divclass:'anylinkmenu', linktarget:'_parent'};
		anylinkmenu3.items=[
			["Cadastro", "javascript:chamaForm('cadastro','liberar.php')"],
			["teste2", "http://www.msnbc.com/"],
			["0", "javascript:alert(0);"]
		];
	</script>
<?php    
	$cont_menu = 1;
	$sql_menus = mysql_query("SELECT codigo, menu, link FROM menus_prefeitura ORDER BY ordem");
	while(list($codmenu,$menu,$link) = mysql_fetch_array($sql_menus)) {
		$sql_submenus = mysql_query("
				SELECT
				  menus_prefeitura.link, submenus_prefeitura.menu, submenus_prefeitura.link
				FROM
				  menus_prefeitura 
				INNER JOIN
				  menus_prefeitura_submenus ON menus_prefeitura.codigo = menus_prefeitura_submenus.codmenu 
				INNER JOIN
				  submenus_prefeitura ON submenus_prefeitura.codigo = menus_prefeitura_submenus.codsubmenu
				WHERE
				  menus_prefeitura.codigo = $codmenu AND menus_prefeitura_submenus.visivel='S' AND nfe = 'S' $string
				ORDER BY
				  menus_prefeitura_submenus.ordem
		");
		if(mysql_num_rows($sql_submenus)){
			//Verifica o nivel de permissao do usuario
			$string = "";
			if($_SESSION['nivel_de_acesso'] == "M"){
				$string = " AND menus_prefeitura_submenus.nivel <> 'A'";
			}elseif($_SESSION['nivel_de_acesso'] == "B"){
				$string = " AND menus_prefeitura_submenus.nivel = 'B'";
			} 
			
			// submenu
			echo "
				<script> 
					var menu_content$cont_menu = {divclass:'anylinkmenu', linktarget:'_parent'};
					menu_content$cont_menu.items = [
			";
			while(list($menulink, $submenu, $submenulink) = mysql_fetch_array($sql_submenus)) {
			
				$menu_content[$cont_menu][] = "['$submenu',\"javascript:chamaForm('$menulink','$submenulink');\"]";
            
			/*	<li><a  class="submenua" id="submenua" onClick="chamaForm('<?php echo $menulink; ?>','<?php echo $submenulink; ?>')"><?php echo $submenu; ?></a></li>
			*/
				
            } // fim while submenus       
            echo implode(',',$menu_content[$cont_menu])."];</script>";
            ?>
            <a href="#" class="menuanchorclass menua" 	rel="menu_content<?php echo $cont_menu?>[mouseover]" rev="up"><?php echo $menu;?></a>
			<?php
		}//end if
		$cont_menu++;
	} // fim while
?>
<a href="logout.php" class="menua">Sair</a>

<form method="post" name="frmMenu" id="frmMenu">
	<input type="hidden" name="include" id="include" />
</form>
	<?php
}else{
?>
<ul id="nav">
<?php    
	$sql_menus = mysql_query("SELECT codigo, menu, link FROM menus_prefeitura ORDER BY ordem");
	while(list($codmenu,$menu,$link) = mysql_fetch_array($sql_menus)) {
		$sql_submenus = mysql_query("
				SELECT
				  menus_prefeitura.link, submenus_prefeitura.menu, submenus_prefeitura.link
				FROM
				  menus_prefeitura 
				INNER JOIN
				  menus_prefeitura_submenus ON menus_prefeitura.codigo = menus_prefeitura_submenus.codmenu 
				INNER JOIN
				  submenus_prefeitura ON submenus_prefeitura.codigo = menus_prefeitura_submenus.codsubmenu
				WHERE
				  menus_prefeitura.codigo = $codmenu AND menus_prefeitura_submenus.visivel='S' AND nfe = 'S' $string
				ORDER BY
				  menus_prefeitura_submenus.ordem
		");
		if(mysql_num_rows($sql_submenus)){
?>	
		<li><a class="menua" href="principal.php" target="_parent"><?php echo $menu; ?></a>
        	<ul>
			<?php
			//Verifica o nivel de permissão do usuario
			$string = "";
			if($_SESSION['nivel_de_acesso'] == "M"){
				$string = " AND menus_prefeitura_submenus.nivel <> 'A'";
			}elseif($_SESSION['nivel_de_acesso'] == "B"){
				$string = " AND menus_prefeitura_submenus.nivel = 'B'";
			} 
			
			// submenu

			while(list($menulink, $submenu, $submenulink) = mysql_fetch_array($sql_submenus)) {
            ?>
				<li><a  class="submenua" id="submenua" onClick="chamaForm('<?php echo $menulink; ?>','<?php echo $submenulink; ?>')"><?php echo $submenu; ?></a></li>
			<?php
            }       
            ?>
			</ul>
		</li> 
<?php
		}
	}
?>
	<li><a class="menua" href="logout.php">Sair</a></li>
</ul>

<form method="post" name="frmMenu" id="frmMenu">
	<input type="hidden" name="include" id="include" />
</form>
<?php }?>
