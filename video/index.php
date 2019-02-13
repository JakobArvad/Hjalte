<?php
include($_SERVER["DOCUMENT_ROOT"].'/class/Load_tpl.class');
include($_SERVER["DOCUMENT_ROOT"].'/class/mysql.class');

$tpl_main = new LoadTPL($_SERVER["DOCUMENT_ROOT"].'/Hjalte/template/','hjalte.tpl');
$tpl_footer = new LoadTPL($_SERVER["DOCUMENT_ROOT"].'/Hjalte/template/','footer.tpl');
// tilføj ting til html header
$tpl_main->SetContent('</head>','
	<script type="text/javascript" src="/js/video-js/video.js"></script>
	<link rel="stylesheet" type="text/css" href="/style/video-js/video-js.css" />
    <link rel="stylesheet" type="text/css" href="/Hjalte/css/Image_Grids.css" />
	<link rel="stylesheet" type="text/css" href="/style/video-js/videojs-resolution-switcher.css" />
	<script type="text/javascript" src="/js/video-js/videojs-resolution-switcher.js"></script>
	<style>
		.vjs-poster{
			background-color: #ffffff;
			}
		.vjs-paused .vjs-big-play-button,.vjs-paused.vjs-has-started .vjs-big-play-button {
			display: block;
			}
	</style>
</head>
');

$Image2Load='?what=NyesteVideoer';
if(@isset($_GET["Galleri"])){
	// skal der viset et galleri
	if(isset($_GET["GalleriID"])){
		$Image2Load='?what=GalleriById&GalleriID='.$_GET["GalleriID"];
	}
	// der skal viser year og eller month from year
	else{
		$Image2Load='?what=GalleriByYear&Year='.$_GET["Year"].'&Month='.$_GET["Month"];
		// se om der er et helt år eller bestemt måned i et år
	}

}//end if(@isset(GET["Galleri"])){


$tpl_main->SetContent('</head>','<script type="text/javascript">
function GetMore(what,when){
        if(what=="year"){
                what =\'GetVideo.php?\'+what+\'=\'+when;
                var response = $.ajax({ type: "GET",
                        url: what,
                        async: false
                        }).responseText;
                // Erstat data i wrapper_single div
                $( ".blog" ).html( response );
        };
        if(what=="more"){
                $( ".grid_2_page" ).hide( "slow" );
                parseInt(when);
                if (typeof MoreNumber === \'undefined\'){
                        parseInt (MoreNumber=when);
                        Hitcount=1;
                        }
                what =\'GetVideo.php?\'+what+\'=\'+MoreNumber+\'&hitcount=\'+Hitcount;
                var response = $.ajax({ type: "GET",
                        url: what,
                        async: false
                        }).responseText;
                $( ".blog" ).append( response );
                MoreNumber=parseInt(when) + parseInt(MoreNumber);
                Hitcount +=1;

        };//if(what=="more")

}

</script>

</head>
        ');

$tpl_main->SetContent('$(document).ready(function(){','$(document).ready(function(){
					var response = $.ajax({ 
					type: "GET",
					url: "GetVideo.php'.$Image2Load.'",
					async: false
			}).responseText;
			$( ".blog" ).html( response );
');




$DagbogContent ="";
$PageContent="";



// laver menuen venstreside
// Find all år og måneder i gallerier
$VideoYear=  DOSQL('SELECT distinct DATE_FORMAT(video.recorded,\'%Y\') as Year 
						FROM Hjalte_Video,video where video.id=Hjalte_Video.video_id 
						and video.private=0 
						order by Year desc','JA_Video');

// årstal med månder i
$GalleriMenuYear='<br /><h4 id="RightMenu">Årstal</h4>
                <ul class="sidebar">';
$FlereCount=0;
if(is_array($VideoYear))foreach($VideoYear as $id => $year){
        // for alle måneder der har billeder for på gældende år
        if($id<=1){
                $GalleriMenuYear .='
                <div class="hover"><li><a href="#" onclick="ToggleClass(\'MoreGalleriMenuYear_'.$VideoYear[$id]["Year"].'\');return false;">'.$VideoYear[$id]["Year"].'</a></li></div>
                        <div class="MoreGalleriMenuYear_'.$VideoYear[$id]["Year"].'" style="display:none; margin-left: 1em;">';
                $Month=DOSQL('SELECT distinct DATE_FORMAT(video.recorded,\'%M\') as Month 
							FROM Hjalte_Video,video where video.id=Hjalte_Video.video_id 
							and video.private=0 and DATE_FORMAT(video.recorded,\'%Y\')='.$VideoYear[$id]["Year"].' order by video.recorded asc','JA_Video');
                foreach($Month as $MonthId =>$Monthname){
                        $GalleriMenuYear .='
                                <div class="hover" style="left:250px;"><li><a href="?Galleri=1&Year='.$VideoYear[$id]["Year"].'&Month='.$Month[$MonthId]["Month"].'">'.$Month[$MonthId]["Month"].'</a></li></div>';

                }//foreach($Month as $MonthId =>$Monthname){
                $GalleriMenuYear .='
                                <div class="hover"><li><a href="?Galleri=1&Year='.$VideoYear[$id]["Year"].'&Month=All">Hele '.$VideoYear[$id]["Year"].'</a></li></div>';
                }//if($id<=1){
        if($id>=2){
                if($id==2){
                        $GalleriMenuYear .='<div class="hover"><li><a href="#" onclick="ToggleClass(\'SeeMoreGalleriMenuYear\');return false;">Se Flere</a></li></div>
                                <div class="SeeMoreGalleriMenuYear" style="display:none; margin-left: 1em;">';
                        }//if($id==2){
                $GalleriMenuYear .='<div class="hover"><li><a href="#" onclick="ToggleClass(\'MoreGalleriMenuYear_'.$VideoYear[$id]["Year"].'\');return false;">'.$VideoYear[$id]["Year"].'</a></li></div>
                        <div class="MoreGalleriMenuYear_'.$VideoYear[$id]["Year"].'" style="display:none; margin-left: 1em;">';
                $Month=DOSQL('SELECT distinct DATE_FORMAT(video.recorded,\'%M\') as Month 
							FROM Hjalte_Video,video where video.id=Hjalte_Video.video_id 
							and video.private=0 and DATE_FORMAT(video.recorded,\'%Y\')='.$VideoYear[$id]["Year"].' order by video.recorded asc','JA_Video');
                foreach($Month as $MonthId =>$Monthname){
                        $GalleriMenuYear .='
                                <div class="hover" style="left:250px;"><li><a href="?Galleri=1&Year='.$VideoYear[$id]["Year"].'&Month='.$Month[$MonthId]["Month"].'">'.$Month[$MonthId]["Month"].'</a></li></div>';
                                }//foreach($Month as $MonthId =>$Monthname){
                        $GalleriMenuYear .='
                                <div class="hover"><li><a href="?Galleri=1&Year='.$VideoYear[$id]["Year"].'&Month=All">Hele '.$VideoYear[$id]["Year"].'</a></li></div>';
                }//if($id>=2){
        $GalleriMenuYear .='</div>';

}// foreach

$GalleriMenuYear .='</div>
                </ul>
';

// End laver menuen venstreside


// add Rsidebar part 1 to content
$PageContent .='<!--------start-gave-Billeder----------->
<div class="blog">

        <!-- end div after last blog and before rsidebar -->
                                </div>
                        <!-- Rsidebar -->
                        <div class="rsidebar span_1_of_3">
                                <!--
                                        <div class="search_box">
                                                <form>
                                                        <input type="text" value="Søg i billeder" onfocus="this.value =\'\';" onblur="if (this.value == \'\') {this.value = \'Søg i billeder\';}"><input type="submit" value="">
                                                </form>
                                        </div>
                                -->
                <div class="Categories">
                                ';
// add Galleri Menu name  to Rsidebar
//$PageContent.=$GallerierMenuName;

// add Galleri Menu year  to Rsidebar
$PageContent.=$GalleriMenuYear;


// add Rsidebar part 2 to content
$PageContent .='
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

$tpl_main->SetContent('[CONTENT]',$PageContent);
echo $tpl_main->Create();
exit ($tpl_footer->Create());

function DOSQL($query, $dbname){
        $sql = new MySQL($dbname,"JA_Hjalte_reader","!reader!");
		//echo $query;
        $temp1 =$sql->Query($query);
        $sql->Close();
        return($temp1);
        }//dosql

?>
