<?php

include($_SERVER["DOCUMENT_ROOT"].'/class/Load_tpl.class');
include($_SERVER["DOCUMENT_ROOT"].'/class/mysql.class');

$tpl_main = new LoadTPL($_SERVER["DOCUMENT_ROOT"].'/Hjalte/template/','hjalte.tpl');
$tpl_footer = new LoadTPL($_SERVER["DOCUMENT_ROOT"].'/Hjalte/template/','footer.tpl');


$ToLoad='?more=10&hitcount=top';

if(isset($_GET["year"]) && !empty($_GET["year"])){
	$ToLoad='?year='.$_GET["year"];
	// skal der vises fra en bestem måned
	if(isset($_GET["month"])){
		$ToLoad .='&month='.$_GET["month"];
	}

}//end if(@isset(GET["year"])){
// tilføj ting til html header
$tpl_main->SetContent('</head>','<script type="text/javascript">
function GetMore(what,when){
	if(what=="year"){
		what =\'GetGuldkorn.php?\'+what+\'=\'+when;
		var response = $.ajax({ type: "GET",   
			url: what,   
			async: false
			}).responseText;
		// Erstat data i wrapper_single div 
		$( ".wrapper_single" ).html( response );
	};
	if(what=="more"){
		$( ".grid_2_page" ).hide( "slow" );
		parseInt(when);
		if (typeof MoreNumber === \'undefined\'){
			parseInt (MoreNumber=when);
			Hitcount=1;
			}
		what =\'GetGuldkorn.php?\'+what+\'=\'+MoreNumber+\'&hitcount=\'+Hitcount;
		var response = $.ajax({ type: "GET",   
			url: what,   
			async: false
			}).responseText;
		$( ".wrapper_single" ).append( response );
		MoreNumber=parseInt(when) + parseInt(MoreNumber);
		Hitcount +=1;
		
	};//if(what=="more")
	
}//function GetMore


</script>
</head>
	');

$tpl_main->SetContent('$(document).ready(function(){','$(document).ready(function(){
					var response = $.ajax({ type: "GET",   
					url: "GetGuldkorn.php'.$ToLoad.'",   
					async: false
					}).responseText;
					$( ".wrapper_single" ).html( response );
					');
$DagbogContent ="";


// Find all year with something in dagbog
$AArstal=  DOSQL('SELECT DISTINCT DATE_FORMAT( `dato` , \'%Y\' ) AS `Year`
					FROM guldkorn WHERE 1 AND dato <= curdate( ) AND privat =0 ORDER BY dato DESC','JA_Hjalte');

// Placeholder til årstal right-side "menu"
// to do vis måneder inden under hvert år
$AArstalContent ='';
/*
if(is_array($AArstal)){ 
	for($i=0;$i<count($AArstal);$i++){
			if($i<=4){
				$AArstalContent .='<div class="hover"><li><a href="#" class="scrollToTop" onclick="GetMore(\'year\',\''.$AArstal[$i]["dkdato"].'\');return false;">'.$AArstal[$i]["dkdato"].'</a></li></div>
				';	
			}
			if($i>=5){
				if($i==5){
					$AArstalContent .='<div class="hover"><li><a href="#" onclick="ToggleClass(\'MoreGuldkornAarstal\');return false;">Se flere</a></li></div>
						<div class="MoreGuldkornAarstal" style="display:none;">';
				}
				$AArstalContent .='<div class="hover"><li><a href="#" class="scrollToTop" onclick="GetMore(\'year\',\''.$AArstal[$i]["dkdato"].'\');return false;">'.$AArstal[$i]["dkdato"].'</a></li></div>
				';
				if($i==(count($AArstal)-1)){$AArstalContent .='</div>';}
				}//if($i=>5)
			
		;          
	}// for
};
*/
if(is_array($AArstal)){
	foreach($AArstal as $id => $year){
		if($id<=1){
			    $AArstalContent .='
                <div class="hover"><li><a href="#" onclick="ToggleClass(\'MoreMenuYear_'.$AArstal[$id]["Year"].'\');return false;">'.$AArstal[$id]["Year"].'</a></li></div>
                        <div class="MoreMenuYear_'.$AArstal[$id]["Year"].'" style="display:none; margin-left: 1em;">';
                $Month=DOSQL('SELECT DISTINCT DATE_FORMAT( `dato` , \'%M\' ) AS `Month`
					FROM guldkorn WHERE 1 AND dato <= curdate( ) AND privat =0 and DATE_FORMAT(dato , \'%Y\') ='.$AArstal[$id]["Year"].' ORDER BY dato DESC','JA_Hjalte');
                foreach($Month as $MonthId =>$Monthname){
                        $AArstalContent .='
                                <div class="hover" style="left:250px;"><li><a href="?year='.$AArstal[$id]["Year"].'&month='.$Month[$MonthId]["Month"].'">'.$Month[$MonthId]["Month"].'</a></li></div>';

                }//foreach($Month as $MonthId =>$Monthname){
                $AArstalContent .='
                                <div class="hover"><li><a href="?year='.$AArstal[$id]["Year"].'" >Hele '.$AArstal[$id]["Year"].'</a></li></div>';
                }//if($id<=1){
        if($id>=2){
                if($id==2){
                        $AArstalContent .='<div class="hover"><li><a href="#" onclick="ToggleClass(\'SeeMoreGuldkornMenuYear\');return false;">Se Flere</a></li></div>
                                <div class="SeeMoreGuldkornMenuYear" style="display:none; margin-left: 1em;">';
                        }//if($id==2){
                $AArstalContent .='<div class="hover"><li><a href="#" onclick="ToggleClass(\'MoreMenuYear_'.$AArstal[$id]["Year"].'\');return false;">'.$AArstal[$id]["Year"].'</a></li></div>
                        <div class="MoreMenuYear_'.$AArstal[$id]["Year"].'" style="display:none; margin-left: 1em;">';
                $Month=DOSQL('SELECT DISTINCT DATE_FORMAT( `dato` , \'%M\' ) AS `Month`
					FROM guldkorn WHERE 1 AND dato <= curdate( ) AND privat =0 and DATE_FORMAT(dato , \'%Y\') ='.$AArstal[$id]["Year"].' ORDER BY dato DESC','JA_Hjalte');
                foreach($Month as $MonthId =>$Monthname){
                        $AArstalContent .='
                                <div class="hover" style="left:250px;"><li><a href="?year='.$AArstal[$id]["Year"].'&Month='.$Month[$MonthId]["Month"].'">'.$Month[$MonthId]["Month"].'</a></li></div>';
                                }//foreach($Month as $MonthId =>$Monthname){
                        $AArstalContent .='
                                <div class="hover"><li><a href="?year='.$AArstal[$id]["Year"].'">Hele '.$AArstal[$id]["Year"].'</a></li></div>';
                }//if($id>=2){
        $AArstalContent .='</div>';		
	}// foreach
};



$DagbogContent .='<!--------start-blog----------->
<div class="blog">
	<div class="main">
		<a href="#RightMenu" > <span class="jump2RMenu"> </span> </a>
		  	<div class="wrap">
				<div class="single-top">
					<div class="wrapper_single">
					<!--  content loaded by ajax -->
			';

// add Rsidebar part 1 to content
$DagbogContent .='	<!-- end div after last blog and before rsidebar -->
				</div>
			<!-- Rsidebar -->
			<div class="rsidebar span_1_of_3">
			<!-- 
				<div class="search_box">
					<form>
					<input type="text" value="Søg i guldkorn" onfocus="this.value =\'\';" onblur="if (this.value == \'\') {this.value = \'Søg i guldkorn\';}"><input type="submit" value="">
				    </form>
			 	</div>
			-->
              	<div class="Categories">
			 		<h4 id="RightMenu">Årstal</h4>
				 	<ul class="sidebar">
					';
// add årstal to Rsidebar
$DagbogContent.=$AArstalContent;

// add Rsidebar part 2 to content
$DagbogContent .='</ul>
		        </div>
		        <div class="tags">
		        	<!-- <h4>Top 6 Tags</h4>
		        	<ul>
		        		<li><a href="#">Top 1</a></li>
		        		<li><a href="#">Top 2</a></li>
		        		<li><a href="#">Top 3</a></li>
		        		<li><a href="#">Top 4</a></li>
		        		<li><a href="#">Top 5</a></li>
		        		<li><a href="#">Top 6</a></li>
		        		<div class="clear"> </div>
		        	</ul> -->
		        </div>
			</div>
				<div class="clear"> </div>
			<!-- End Rsidebar -->	
			';
			

// maybe do more here			
			
// need 4 closing div tags
$DagbogContent .='</div>
		</div>
	</div>
</div>
';

$tpl_main->SetContent('[CONTENT]',$DagbogContent);
echo $tpl_main->Create();
exit ($tpl_footer->Create());

function DOSQL($query, $dbname){
	$sql = new MySQL($dbname,"JA_Hjalte_reader","!reader!");
	//echo "<br>" .$query;
	$temp1 =$sql->Query($query);
	$sql->Close();
	return($temp1);
	}//dosql

?>
