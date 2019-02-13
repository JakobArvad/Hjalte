<?php
include($_SERVER["DOCUMENT_ROOT"].'/class/mysql.class');
$DB=select * from images 




function DOSQL($query, $dbname){
        //echo $query .'<br />';
        $sql = new MySQL($dbname,"JA_Hjalte_reader","!reader!");
        $temp1 =$sql->Query($query);
        $sql->Close();
        return($temp1);
        }//dosql

?>
