<?php

include($_SERVER["DOCUMENT_ROOT"].'/class/Load_tpl.class');
include($_SERVER["DOCUMENT_ROOT"].'/class/mysql.class');

// finde de næste X ting fra dagbog. Der er altid hentet 10 som standard
if(isset($_GET["what"])){
$QuerySort="";	
	// Når det er muligt at have billedere der ikke er med i et galleri 
//skal nedenstående laves ellers skal der bare vises det nyeste galleri
//find ud af om der skal vises det nyeste galleri eller om det er billedere der skal vises
// nyeste billede
/*
$NyesteBillede= DOSQL('SELECT pictures.id, pictures.exif,pictures.filename FROM Hjalte_Pics,pictures WHERE 1
                                and Hjalte_Pics.pics_id=pictures.id order by pictures.exif  desc  limit 1','JA_Pics');

// Nyeste billede fra galleri
$NyesteBilledeGalleri= DOSQL('SELECT Hjalte_Pics_Main.Hjalte_Main_id
                                                        FROM Hjalte_Pics_Main, Hjalte_Pics_Main.Hjalte_Main_id <>52 ','JA_Pics');

*/
// Find nyeste galleri der ikke hedder  kreativ 
// lave en tabel med gallerier der skal excludes
// nyesete galleri, forudsætter at der er mindst et ud over kreativ
// $NyeGalleri = dosql('SELECT id,name FROM `Hjalte_Main` where id<>(SELECT id FROM `Hjalte_Main` WHERE Hjalte_Main.name LIKE "kreativ%" ) order by SortOrder desc limit 1', 'JA_Pics');


if($_GET["what"]=="NyesteVideoer"){
	
	$NyeGalleriBilleder=dosql('SELECT Hjalte_Video.video_id , video.filename ,video.recorded ,video.laengde,
								case video.comment when "null" then \'\' else video.comment END as comment,video.spilletid ,
								"Nyeste" as name
								FROM Hjalte_Video,video where video.id=Hjalte_Video.video_id and 
								video.private=0 order by video.recorded desc limit 8','JA_Video');	

}//if($_GET["what"]=="NyesteVideoer"){

if($_GET["what"]=="GalleriByYear"){
	if($_GET["Month"]=="All"){ 
		$QuerySort=$_GET["Year"];
		$GalleriName='\'Hele år '.$_GET["Year"].'\'';
		}
		
	else{	
		$QuerySort=$_GET["Year"].' and DATE_FORMAT( video.recorded , \'%M\' )=\''.$_GET["Month"].'\'';
		$GalleriName='\''.$_GET["Month"] .' år '.$_GET["Year"].'\'';
		}
	
		$NyeGalleriBilleder=dosql('SELECT Hjalte_Video.video_id , video.filename ,video.recorded ,video.laengde,
		case video.comment when "null" then \'\' else video.comment END as comment,video.spilletid, 
								'.$GalleriName.' as name FROM Hjalte_Video,video where video.id=Hjalte_Video.video_id and 
								video.private=0 and DATE_FORMAT( video.recorded , \'%Y\' )='.$QuerySort.'  order by video.recorded desc','JA_Video');	
                                
}//if($_GET["what"]=="GalleriByYear"){





	if(is_array($NyeGalleriBilleder)){
		ReturnIt($NyeGalleriBilleder);
	}
	else echo $NyeGalleriBilleder;
}// if (isset($_GET["more"]) && !empty($_GET["more"]))
	
function ReturnIt($ToReturn){
	
// finder path mm til billeder
$BilledeConf=DOSQL('SELECT `SDFolder`,`ThumbFolder`,`RootPath`,`HDFolder` FROM `Config` where 1 order by id DESC limit 1  ',"JA_Video");
	echo '<!--------start-gave-ideer----------->
		<div class="blog">
			<div class="main" style="border-bottom: 0px #FFFFFF";>
				<div class="wrap">
					<div class="single-top">
						<div class="wrapper_single">
							<!--  content loaded by ajax -->
							<div class="about-top">
								<div class="clear"> </div>
							</div>
							<div class="about-bottom">
								<div class="about-topgrids"></div>
								<div class="about-histore">
								<h5 style="color: #00A0B0;font-size: 3em;" >'.ucfirst( $ToReturn[0]["name"]).'</h5><a href="#RightMenu" > <span class="jump2RMenu"> </span> </a>
								
									<div class="historey-lines">
										<div id="gallery">
											';

				


	for($i=0;$i<count($ToReturn);$i++){
		
		
		echo ' 
							<div class="about">
		<h3 class="heading" style="margin: 0.5em 0em;">'.$ToReturn[$i]["comment"].'</h3>
		</div>
				<video style="background-color:#ffffff;" id="videoid_'.$ToReturn[$i]["video_id"].'" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="metadata" 
				poster="/Hjalte/GetImage.php?path='.base64_encode($BilledeConf[0]["RootPath"]).'&img='.base64_encode(substr($ToReturn[$i]["filename"], 0,strrpos($ToReturn[$i]["filename"],'.')).'.jpeg').'&size='.$BilledeConf[0]["SDFolder"].'"  data-setup=\'{ "plugins" : { "videoJsResolutionSwitcher" : { "default" : "low" },"fluid":true,"aspectRatio":"16:9" }} \'>
				
					<source src="/Video/'.$BilledeConf[0]["SDFolder"].'/'.substr($ToReturn[$i]["filename"], 0,strrpos($ToReturn[$i]["filename"],'.')).'.mp4'.'" type="video/mp4" label="SD" res="480" />
					<source src="/Video/'.$BilledeConf[0]["HDFolder"].'/'.substr($ToReturn[$i]["filename"], 0,strrpos($ToReturn[$i]["filename"],'.')).'.mp4'.'" type="video/mp4" label="HD" res="1080" />
					<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video</p>
				</video>
		<div class="about-topgrid1">
			<h5 class="heading" style="padding: 0em 0em;">Optaget: '.$ToReturn[$i]["recorded"].' længde: '.$ToReturn[$i]["spilletid"].'</h5>
		</div>
		<div class="clear"></div> 
			
		';

		
	} // for 
	echo '
		</div>
		<!--/#four-columns-->	
	</div>
			<div class="clear"> </div>
		</div>
		<div class="clear"></div> 
	</div>
	<br />
	<br />
	
	';
	}//function ReturnIt($ToReturn){


function DOSQL($query, $dbname){
	//echo $query .'<br />';
	$sql = new MySQL($dbname,"JA_Hjalte_reader","!reader!");
	$temp1 =$sql->Query($query);
	$sql->Close();
	return($temp1);
	}//dosql

?>
