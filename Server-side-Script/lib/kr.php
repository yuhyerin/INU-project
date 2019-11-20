<?php
    function encoding_detect(){
        $string="대상 스트링";
        if(true== mb_detect_encoding($string,"EUC-KR")){
            echo "EUC-KR","<br>";
            }else if(true == mb_detect_encoding($string,"UTF-8")){
            echo "UTF-8","<br>";
            }else{
                echo "Unknown En","<br>";
                }
     }
     function detectingEncoding($str){
     $encodingSet = array("EUC-KR","UTF-8","CP494");
     foreach($encodingSet as $v){
            $tmp = iconv($v,$v,$str);
            if(md5($tmp)==md5($str)) return $v;
      }
      return false;
      }

?>

<?php
/*
function speak($receive,$info){

    if($info=="시간"){
        $numrow = mysqli_num_rows($receive);
         //행(ROW) 수 만큼
        for($i=0; $i<$numrow; $i++){
            // mysql_fetch_array를 반복합니다.
            $row[$i]=mysqli_fetch_array($receive);
        }

        for($i=0;$i<$numrow;$i++){
            if($i==$numrow-1) break;

            //echo strtotime($row[$i][1]), strtotime($row[$i+1][0]);
            //$diff = ceil((strtotime($row[$i+1][0])-strtotime($row[i][1]))/(60*60)); //시간계산
           // echo "$row[$i+1][0]:",$row[$i+1][0],"$row[$i][1]:",$row[$i][1];
            $diff = ceil( (strtotime($row[$i+1][0])-strtotime($row[i][1]) )/3600 );
            echo "diff: ",$diff,"<br>";
            if($diff<1){
  //                  echo "diff : ",$diff,"<br>";
                   echo $row[$i][1],"에서"," ",$row[$i+1][0],"<br>";
            }

       //     echo $row[$i][1]," ",$row[$i+1][0],"<br>";
        }
        echo "비어 있습니다.%";
    }else if($info=="빈강의실"){
/*
    if(count($time)==2){
        while($row = mysqli_fetch_array($receive)){
            echo mb_substr($row[0],2,3),"호"," ";
        }
     }else{
*/
/*
        while($row=mysqli_fetch_array($receive)){
            $diff = (strtotime($row[0])-strtotime($row[1]))/(60*60);
            if($diff ==0){
                echo mb_substr($row[0],2,3),"호"," ";
            }
            else if($diff<1){
            echo mb_substr($row[0],2,3),"호가 "," ",mb_substr($row[1],0,2),"시에서 ",mb_substr($row[2],0,2),"시까지 ";
            }
  /*      }*/
  /*
    }
        echo "비어있습니다.%";


    }
}
*/
?>
