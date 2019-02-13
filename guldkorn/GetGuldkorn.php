<?php

include($_SERVER["DOCUMENT_ROOT"].'/class/Load_tpl.class');
include($_SERVER["DOCUMENT_ROOT"].'/class/mysql.class');

//Finder alle ting i dagbogen ud fra valgte år
if (isset($_GET["year"]) && !empty($_GET["year"])){
	$Month="";
	if(isset($_GET["month"])){
		$Month=' and DATE_FORMAT(`dato`, \'%M\')="'.$_GET["month"].'" ';
		}
	$Dagbog=DOSQL('SELECT `id`, `guldkorn`, DATE_FORMAT(`dato`, \'%b. %Y\') as `monthyear`, 
					DATE_FORMAT(`dato`, \'%d\') as `dag`, `privat`, \'\' as paging 
					FROM guldkorn where dato<=DATE_FORMAT(now(), \'%Y-%m-%d %H:%i:%S\') and privat=0 and YEAR(dato) = '.$_GET["year"].' '.$Month.' order by dato desc',"JA_Hjalte");
	if(is_array($Dagbog)) ReturnIt($Dagbog,false);
	}// // if (isset($_GET["year"]) && !empty($_GET["year"]))


// finde de næste X ting fra dagbog. Der er altid hentet 10 som standard
if (isset($_GET["more"]) && !empty($_GET["more"])){
	// er der trykke med "flere knappen"
	if(isset($_GET["hitcount"])){
		if($_GET["hitcount"]==1){
				$LimitVar1=10;
				$LimitVar2=$_GET["more"];
			}
		// skal der hentes de første x rækker
		elseif($_GET["hitcount"]=="top"){
			$LimitVar1=0;
			$LimitVar2=$_GET["more"];
		}// elseif
		else{
				$LimitVar1 = 10 +$_GET["more"] - ($_GET["more"] / $_GET["hitcount"]) ;
		$LimitVar2 = $_GET["more"] / $_GET["hitcount"] ;
		}
	}//if(isset($_GET[hitcount]))
	
	$Dagbog=DOSQL('SELECT `id`, `guldkorn`, DATE_FORMAT(`dato`, \'%b. %Y\') as `monthyear`, 
					DATE_FORMAT(`dato`, \'%d\') as `dag`, `privat`, \'\' as paging 
					FROM guldkorn where dato<=DATE_FORMAT(now(), \'%Y-%m-%d %H:%i:%S\') and privat=0 
					order by dato desc limit '.$LimitVar1.','.$LimitVar2 .'',"JA_Hjalte");
					
	if(is_array($Dagbog)) ReturnIt($Dagbog,"yes");
}// // if (isset($_GET["year"]) && !empty($_GET["year"]))
	
function ReturnIt($ToReturn,$Paging){
	
	if($Paging=="yes"){
	$Paging='
	<!-- start preview and next button -->
			<div class="grid_2_page">
				<ul>
					<!-- <li class="preview"><a href="#"><span> </span></a></li>
					<li><a href="#">Previous Post </a></li> -->
					<li><a href="#" id="more" onclick="GetMore(\'more\',\'10\');return false;">Flere</a></li>
					<li class="next"><a href="#"  onclick="GetMore(\'more\',\'10\');return false;"><span> </span></a></li>
					<div class="clear"> </div>
				</ul>
			</div>
			<!-- End preview and next button -->
			';
	}
	else $Paging="";
	for($i=0;$i<count($ToReturn);$i++){
		if($i==(count($ToReturn)-1)) 	$ToReturn[$i]["paging"]=$Paging;
		echo '<!-- start Blog content -->
				<div class="wrapper_top">
					<div class="grid_1 alpha">
						<div class="date">
							<span>'.$ToReturn[$i]["dag"].'</span>'.$ToReturn[$i]["monthyear"].'
						</div>
					</div>
					<div class="content span_2_of_single">
						<h5 class="blog_title"><a href="#"></a></h5>
						<div class="links">
						<h3 class="comments">Af<a href="#l">&nbsp;Jakob Arvad</a></h3>
						<!--    <h3 class="tags">Tags: <a href="#">Design</a>,<a href="#">Creative</a>,<a href="#">wordpress theme</a></h3>  	-->
						<div class="clear"> </div>
					</div>
					<div class="content">
						<div class="span-1-of-1">
							<!--	<a href="#"><img class="m_img"  src="images/sb1.jpg" alt=""/></a> -->
						</div>
						<div class="span-1-of-2">
							<p>'.$ToReturn[$i]["guldkorn"].'</p>
						</div>
						<div class="clear"> </div>
					</div>	
				'.$ToReturn[$i]["paging"].'
				</div>
			</div>
			<div class="clear"> </div>
			<!-- End Blog content -->
			<br />
			<br />
			<br />
			';
		} // for 
	
	}//function ReturnIt($ToReturn){


function DOSQL($query, $dbname){
	$sql = new MySQL($dbname,"JA_Hjalte_reader","!reader!");
	$temp1 =$sql->Query($query);
	$sql->Close();
	return($temp1);
	}//dosql

?>
