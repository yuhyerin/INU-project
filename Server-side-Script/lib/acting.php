<?php
    require_once("subfunction.php");
    require_once("sql.php");
?>

<?php




function connect($db_host,$db_user,$db_password,$db_name){

    $con = mysqli_connect($db_host,$db_user,$db_password,$db_name);
    if(mysqli_connect_error($con)){
//        echo "Mysql 접속 실패!!","<br>";
//        echo "오류 원인 : ", mysqli_connect_error();
        return 0;
        }
//    echo "접속 성공!","<br>";
    return $con;

}

function analysis($string){
    $array = explode("_",$string);
    $result["day"]=ret_day($array);
    $result["time"]=ret_time($array);
    $result["building"]=ret_building($array);
    $result["place"]=ret_place($array);
    $result["table"]=ret_table($array);
    //echo "AA",print_r($array),print_r($result)["table"],"K";
    $result["key"]=ret_key($array,$result["table"]);
    $result["wh"]=ret_wh($array);
    return $result;
}



function sendDB($con,$result){
    for($i=0;$i<count($result["time"]);$i++){
        $time[$i]=num2time($result["time"][$i]);
        }
    $day = $result["day"];
    $building = $result["building"];
    if(strcmp($building,"건물없음")==0)$building=7;
    $place = $result["place"];//앞에 영어는 빼면 안될까? 데이터베이스 내에서
    $table = $result["table"];
    $key = $result["key"];
    $code = roomfordata($building);
    $place = $code.$place;

    //echo "a:",$day,"b:",$building,"c:",$place,"d:",$table,"e",print_r($time),"<br>";
//빈강의실 테이블 관련 질문
    if(strcmp($result["table"],"빈강의실")==0){
      $table="v_emptyLectureroom";
      if(strcmp($day,"오늘")==0){
        if((count($time)==1)&&(mb_strpos($place,"교실없음")!=false)){
          $sql = room1_sql($time,$table,$building);//$string = "1_시_에_빈강의실_어디_야_";
          $token = "빈강의실1";
        }else if(count($time)==2&&(mb_strpos($place,"교실없음")!=false)){
          $sql = room2_sql($time,$table,$building);//$string = "3_시_에서_4_시_까지_빈_강의실_있어_";
          $token = "빈강의실2";
        }else if(mb_strpos($place,"교실없음")==false){
          $sql = room3_sql($place,$table,$building);//$string = "505_호_빈_시간_언제_야_";
          $token = "빈강의실3";
        }
      }else if($day!="오늘"){
        if((count($time)==1)&&(mb_strpos($place,"교실없음")!=false)){
          $sql = room4_sql($time,$day,$table,$building);//$string = "수요일_1_시_에_빈강의실_어디_야_";
          $token = "빈강의실4";
        }else if(count($time)==2&&(mb_strpos($place,"교실없음")!=false)){

          $sql = room5_sql($time,$day,$table,$building);//$string = "월요일_3_시_에서_4_시_까지_빈강의실_있어";
          $token = "빈강의실5";
        }else if(mb_strpos($place,"교실없음")==false){
          $sql = room6_sql($place,$day,$table,$building);//$string = "수요일_505_호_빈_시간_언제_있어?";
          $token = "빈강의실6";
        }
      }
    }

//시간표 테이블 관련 질문
    if(strcmp($result["table"],"시간표")==0){
      $sql = timetable_sql($day,$place);
      $token = "시간표";
    }

//복사점 테이블 관련 질문
  if(strcmp($result["table"],"복사점")==0){
    $sql = copystore_sql();
    $token = "복사점";
  }

//와이파이 테이블 관련 질문
    if(strcmp($result["table"],"와이파이")==0){
      $sql = wifi_sql($key);
      $token = "와이파이";
    }
//놀이문화센터 테이블 관련 질문
    if(strcmp($result["table"],"놀이문화센터")==0){
      $keydata=keydata();
      $whdata = whdata();
      if(in_array($key,$keydata["놀이문화센터"]["대여물품"])&&(in_array($result["wh"],$whdata["언제"]))){
        $sql = play1_sql($key);
        $token = "놀이문화센터1";
      }else if(in_array($key,$keydata["놀이문화센터"]["대여물품"])&&(in_array($result["wh"],$whdata["어디서"]))){
        $sql = play2_sql($key);
        $token = "놀이문화센터2";
      }
      else{
              $sql = play0_sql();
              $token = "놀이문화센터0";
          }


    }
//무인프린트 테이블 관련 질문
    if(strcmp($result["table"],"무인프린트")==0){
      $keydata=keydata();
      $whdata = whdata();
      if(in_array($key,$keydata["무인프린트"]["호관"])){//&&(in_array($result["wh"],$whdata["어디서"]))){
        $sql = print1_sql($key);
        $token = "무인프린트1";
      }else if(in_array($key,$keydata["무인프린트"]["건물명"])){//&&(in_array($result["wh"],$whdata["어디서"]))){
        $sql = print2_sql($key);
        $token = "무인프린트2";
      }else{
      $sql = print0_sql();
      $token = "무인프린트0";
    }

    }
//음식점관련 테이블
    if(strcmp($result["table"],"음식점")==0){
      $keydata=keydata();
      $whdata = whdata();
    if(in_array($key,$keydata["음식점"]["매장명"])&&(in_array($result["wh"],$whdata["어디서"]))){
      // Q. 학생_식당_어디_있어?
      // Q. 쌀국수_집_어디_있어?
      // Q. 공씨네_어디_있어?
      $sql = cafeteria1_sql($key);
      $token = "음식점1";
    }else if(in_array($key,$keydata["음식점"]["매장명"])&&(in_array($result["wh"],$whdata["언제"]))){
      // Q. 토마토_도시락_언제_까지_해?
      // Q. 학생_식당_운영_시간_알려_줘
      $sql = cafeteria2_sql($key);
      $token = "음식점2";
    }else if(in_array($key,$keydata["음식점"]["주말운영여부"])){
      // Q, 주말_에_어디_서_먹을_수_있어?
      // Q. 주말_에_도_여는_식당_알려_줘

      $sql = cafeteria3_sql($key);
      $token="음식점3";
    }else if(in_array($key,$keydata["음식점"]["매장명"])&&(in_array($result["wh"],$whdata["언제"]))){
      //학식_말고_먹을_수_있는_데_있어?
      $sql = cafeteria4_sql($key);
      $token="음식점4";
    }else if(in_array($key,$keydata["음식점"]["주메뉴"])){
      // Q. 짜장면_먹고_싶다
      $sql = cafeteria5_sql($key);
      $token="음식점5";
    }else{
      $sql = cafeteria0_sql();
      $token = "음식점0";
    }

    }

    if(strcmp($result["table"],"여학생휴게실")==0){
      $keydata=keydata();
      $whdata = whdata();

      if(mb_strpos($key,"면")!=false){
        $keyA[0] = "수면실";
      }else $keyA[0]='';
      if(mb_strpos($key,"워")!=false){
          $keyA[1] = "샤워실";
        }else $keyA[1]='';
      if(mb_strpos($key,"돌")!=false){
            $keyA[2] = "온돌";
        }else $keyA[2]='';

      if(array_intersect($keyA,$keydata["여학생휴게실"])){
      $sql =restroom2_sql($keyA);
      $token = "여학생휴게실2";
    }else{
      $sql = restroom1_sql();
      $token = "여학생휴게실1";
    }
    }
    if(strcmp($result["table"],"자판기")==0){
        $keydata = keydata();
        $whdata =whdata();
        if(in_array($key,$keydata["자판기"])){
        $sql =vendingmachine2_sql($key);
        $token = "자판기2";
      }else{
        $sql = vendingmachine1_sql();
        $token = "자판기1";
      }
    }

    if(strcmp($result["table"],"제휴업체")==0){
      $sql =affiliate_sql();
      $token = "제휴업체";
    }


    if(strcmp($result["table"],"주차관리")==0){
      $sql =parking_sql();
      $token = "주차관리";
    }


    if(strcmp($result["table"],"카페")==0){

      $keydata = keydata();
      $whdata=whdata();
      //////////////카페에 조건 제대로 지정하기
      if(in_array($key,$keydata["카페"]["매장명"])&&(in_array($result["wh"],$whdata["어디서"]))){
        // Q. 팔공티_어딨어?
        // Q. 쥬씨_어디_에_있어?
        $sql =cafe2_sql($key);
        $token = "카페2";
      }else if(in_array($key,$keydata["카페"]["매장명"])&&(in_array($result["wh"],$whdata["언제"]))){
        // Q. 카페_드림_도서관점_운영_시간_알려_줘
        // Q. 샐러디_언제까지_해?
        $sql =cafe3_sql($key);
        $token = "카페3";
      }else if(in_array($key,$keydata["카페"]["주말운영시간"])){
        $sql =cafe4_sql($key);
        $token = "카페4";
      }else{
        $sql =cafe1_sql();
        $token = "카페1";
    }

    }

    if(strcmp($result["table"],"먹거리")==0){
      $keydata=keydata();
      $whdata=whdata();
      if(in_array($key,$keydata["먹거리"])){
        $sql =food2_sql($key);
        $token = "먹거리2";
      }else{
        $sql =food1_sql($key);
        $token = "먹거리1";
      }
    }

    if(strcmp($result["table"],"편의점")==0){
      $keydata=keydata();
      $whdata=whdata();

      if(in_array($key,$keydata["편의점"]["매장명"])&&(in_array($result["wh"],$whdata["어디서"]))){
        // Q. gs_편의점_자연대점_어디_있어?
        $sql =convinience1_sql($key);
        $token = "편의점1";
      } else if(in_array($key,$keydata["편의점"]["매장명"])&&(in_array($result["wh"],$whdata["언제"]))){
        // Q. 도서관_편의점_운영_시간_알려_줘
        // Q. 기념품_샵_언제_까지_해?
        $sql =convinience2_sql($key);
        $token = "편의점2";
      }else if(strcmp($result["wh"],"지금")==0){
        //Q. 지금(저녁)_도_이용_가능_한_편의점_있어?
        $sql =convinience3_sql();
        $token = "편의점3";
      }else if(in_array($key,$keydata["편의점"]["주말운영여부"])){
        //Q. 주말_에_도_여는_편의점_있어?
        $sql =convinience4_sql();
        $token = "편의점4";
      }else{
        $sql = convinience0_sql();
        $token = "편의점0";
      }
    }


    if(strcmp($result["table"],"복지시설")==0){
      $keydata=keydata();
      $whdata=whdata();
      if(in_array($key,$keydata["복지시설"]["복지시설"])&&(in_array($result["wh"],$whdata["언제"]))){
        //string="서점_알려줘";
        $sql =welfare1_sql($key);
        $token = "복지시설1";
      }else if(in_array($key,$keydata["복지시설"]["호관"])||in_array($key,$keydata["복지시설"]["건물명"])){
        //string="11호관_복지회관에 뭐뭐 있어?";
        $sql =welfare2_sql($key);
        $token = "복지시설2";
      }else if(in_array($key,$keydata["복지시설"]["복지시설"])&&(in_array($result["wh"],$whdata["어디서"]))){
        $sql = welfare3_sql($key);
        $token="복지시설3";
      }else{
        //$string = "복지_시설 알려줘";
        $sql =welfare0_sql($key);
        $token = "복지시설0";
      }

    }




//조회
    $ret = mysqli_query($con,$sql);
    if(!$ret){
        echo "조회가 실패되었어요. 실패 원인 :".mysqli_error($con);
        return;
    }

    return array($ret,$token);
//      return $ret;
}






function speak($ret,$token){
      // echo "!!token:",$token,"<br>";
  switch($token){

    case "빈강의실1":
      $speak = all_select($ret);
      // echo "||speaking:",print_r($speak),"||<br>";


      //수업 아예 없는 강의실 가져오기
      $_POST['building']=$speak[0][3];
      $_POST['day']=$speak[0][4];
      include("fullemptyroom.php");
      $speak_full=$_POST['fullemptyroom'];
      $speak_full = implode("f/",$speak_full);
      $speak_full = explode("/",$speak_full);

      //수업있는 강의실 뭉치기
      $speak_not = array();
      for($i=0;$i<count($speak);$i++){
        array_push($speak_not,$speak[$i][0]);
      }
      //수업있는 강의실 풀빈강의실 합치기
      $speak_data = array_merge($speak_not,$speak_full);
                  sort($speak_data);
      $result = array();
      for($i=0;$i<count($speak_data);$i++){
        $tmp=array();
        array_push($tmp,$speak_data[$i]);
        array_push($result,$tmp);
      }
      $speak_data = $result;
      //보내줘야할 전체 데이터들
      echo buttonnum($token);
      echo "%";

      echo substr($speak[0][0],0,2);
      echo "&";
      for($i=0;$i<count($speak_data);$i++){
        if(strpos($speak_data[$i][0],'f')!==false)
          echo substr($speak_data[$i][0],2,3),"호f";
        else
          echo substr($speak_data[$i][0],2,3),"호";
        if($i==count($speak_data)-1)break;
        echo "*";
      }
      echo "^";
     echo "%";
     if($speak==NULL){
         echo "비어있지 않아요";
         break;
     }

     if(count($speak)<6){
      for($i=0;$i<count($speak);$i++){
        echo substr($speak[$i][0],2,3),"호는 ",substr($speak[$i][1],0,2),"시",substr($speak[$i][1],3,2),"분까지";
        if($i==count($speak)-1)break;
        echo ", ";
      }
    }else{
      // print_r($speak_data);
      $floor = roomtofloor($speak_data);
      for($num=0;$num<5;$num++){
          echo $num+1,"층에 ",$floor[$num],"개";
          if($num==4)break;
          echo ", ";
      }
    }
          echo " 비어있어요.";

      break;

  case "빈강의실2":
    $speak = all_select($ret);
    // echo "||speaking:",print_r($speak),"||<br>";


    //수업 아예 없는 강의실 가져오기
    $_POST['building']=$speak[0][3];
    $_POST['day']=$speak[0][4];
    include("fullemptyroom.php");
    $speak_full=$_POST['fullemptyroom'];
    $speak_full = implode("f/",$speak_full);
    $speak_full = explode("/",$speak_full);

    //수업있는 강의실 뭉치기
    $speak_not = array();
    for($i=0;$i<count($speak);$i++){
      if(in_array($speak[$i][0],$speak_not))continue;
      array_push($speak_not,$speak[$i][0]);
    }
    //수업있는 강의실 풀빈강의실 합치기
    $speak_data = array_merge($speak_not,$speak_full);
    sort($speak_data);

    $result = array();
    for($i=0;$i<count($speak_data);$i++){
      $tmp=array();
      array_push($tmp,$speak_data[$i]);
      array_push($result,$tmp);
    }
    $speak_data = $result;
    //줘야할 데이터
    echo buttonnum($token);
    echo "%";

    echo substr($speak[0][0],0,2);
    echo "&";
    for($i=0;$i<count($speak_data);$i++){
      if(strpos($speak_data[$i][0],'f')!==false)
        echo substr($speak_data[$i][0],2,3),"호f";
      else
        echo substr($speak_data[$i][0],2,3),"호";
      if($i==count($speak_data)-1)break;
      echo "*";
    }
    echo "^";
   echo "%";
   if($speak_data==NULL){
       echo "비어있지 않아요";
       break;
   }

   if(count($speak_data)<6){
    for($i=0;$i<count($speak_data);$i++){
      echo substr($speak_data[$i][0],2,3),"호";
        if($i==(count($speak_data)-1))break;
        echo ", ";
    }
  }else{
    $floor = roomtofloor($speak_data);
    for($num=0;$num<5;$num++){
        echo $num+1,"층에 ",$floor[$num],"개";
        if($num==4)break;
        echo ", ";
    }
  }


        echo " 비어있어요.";


    break;


    case "빈강의실3":
    $speak = all_select($ret);
    // echo "||speaking:",print_r($speak),"||<br>";

    echo buttonnum($token);
    echo "%";

  echo substr($speak[0][2],0,2);
  echo "&";
  echo substr($speak[0][2],2,3);
  echo "^";
   echo "%";
   if($speak==NULL){
       echo "%비어있지 않아요";
       break;
   }
          for($i=0;$i<count($speak)-1;$i++){
            if(time_diff($speak[$i+1][0],$speak[$i][1],"m")>=60){
            echo substr($speak[$i][1],0,2),"시",substr($speak[$i][1],3,2),"분에서 ";
            echo substr($speak[$i+1][0],0,2),"시",substr($speak[$i+1][0],3,2),"분까지 ";
            // echo substr($speak[$i][1],0,2),"시",substr($speak[$i][1],3,2),"분에서 ";
            // echo substr($speak[$i+1][0],0,2),"시",substr($speak[$i+1][0],3,2),"분까지";
            }
          }
          if(count($speak)==1)
            echo substr($speak[0][1],0,2),"시",substr($speak[0][1],3,2),"분부터 쭉 ";
          else
            echo substr($speak[count($speak)-1][1],0,2),"시",substr($speak[count($speak)-1][1],3,2),"분부터 쭉 ";
            echo "비어있어요.";
      break;

    case "빈강의실4"://$string = "수요일_1_시_에_빈강의실_어디_야_";
      $speak = all_select($ret);
      // echo "||speaking:",print_r($speak),"||<br>";


      //수업 아예 없는 강의실 가져오기
      $_POST['building']=$speak[0][3];
      $_POST['day']=$speak[0][4];
      include("fullemptyroom.php");
      $speak_full=$_POST['fullemptyroom'];
      $speak_full = implode("f/",$speak_full);
      $speak_full = explode("/",$speak_full);

      //수업있는 강의실 뭉치기
      $speak_not = array();
      for($i=0;$i<count($speak);$i++){
        array_push($speak_not,$speak[$i][0]);
      }
      //수업있는 강의실 풀빈강의실 합치기
      $speak_data = array_merge($speak_not,$speak_full);
                  sort($speak_data);
      $result = array();
      for($i=0;$i<count($speak_data);$i++){
        $tmp=array();
        array_push($tmp,$speak_data[$i]);
        array_push($result,$tmp);
      }
      $speak_data = $result;
      //보내줘야할 전체 데이터들
      echo buttonnum($token);
      echo "%";

      echo substr($speak[0][0],0,2);
      echo "&";
      for($i=0;$i<count($speak_data);$i++){
        if(strpos($speak_data[$i][0],'f')!==false)
          echo substr($speak_data[$i][0],2,3),"호f";
        else
          echo substr($speak_data[$i][0],2,3),"호";
        if($i==count($speak_data)-1)break;
        echo "*";
      }
      echo "^";
     echo "%";
     if($speak==NULL){
         echo "비어있지 않아요";
         break;
     }

     if(count($speak)<6){
      for($i=0;$i<count($speak);$i++){
        echo substr($speak[$i][0],2,3),"호는 ",substr($speak[$i][1],0,2),"시",substr($speak[$i][1],3,2),"분까지";
        if($i==count($speak)-1)break;
        echo ", ";
      }
    }else{
      // print_r($speak_data);
      $floor = roomtofloor($speak_data);
      for($num=0;$num<5;$num++){
          echo $num+1,"층에 ",$floor[$num],"개";
          if($num==4)break;
          echo ", ";
      }
    }
          echo " 비어있어요.";

      break;
    case "빈강의실5":
      $speak = all_select($ret);
      // echo "||speaking:",print_r($speak),"||<br>";


      //수업 아예 없는 강의실 가져오기
      $_POST['building']=$speak[0][3];
      $_POST['day']=$speak[0][4];
      include("fullemptyroom.php");
      $speak_full=$_POST['fullemptyroom'];
      $speak_full = implode("f/",$speak_full);
      $speak_full = explode("/",$speak_full);

      //수업있는 강의실 뭉치기
      $speak_not = array();
      for($i=0;$i<count($speak);$i++){
        if(in_array($speak[$i][0],$speak_not))continue;
        array_push($speak_not,$speak[$i][0]);
      }
      //수업있는 강의실 풀빈강의실 합치기
      $speak_data = array_merge($speak_not,$speak_full);
      sort($speak_data);

      $result = array();
      for($i=0;$i<count($speak_data);$i++){
        $tmp=array();
        array_push($tmp,$speak_data[$i]);
        array_push($result,$tmp);
      }
      $speak_data = $result;
      //줘야할 데이터
      echo buttonnum($token);
      echo "%";

      echo substr($speak[0][0],0,2);
      echo "&";
      for($i=0;$i<count($speak_data);$i++){
        if(strpos($speak_data[$i][0],'f')!==false)
          echo substr($speak_data[$i][0],2,3),"호f";
        else
          echo substr($speak_data[$i][0],2,3),"호";
        if($i==count($speak_data)-1)break;
        echo "*";
      }
      echo "^";
     echo "%";
     if($speak_data==NULL){
         echo "비어있지 않아요";
         break;
     }

     if(count($speak_data)<6){
      for($i=0;$i<count($speak_data);$i++){
        echo substr($speak_data[$i][0],2,3),"호";
          if($i==(count($speak_data)-1))break;
          echo ", ";
      }
    }else{
      $floor = roomtofloor($speak_data);
      for($num=0;$num<5;$num++){
          echo $num+1,"층에 ",$floor[$num],"개";
          if($num==4)break;
          echo ", ";
      }
    }


          echo " 비어있어요.";


      break;


    case "빈강의실6":
      $speak = all_select($ret);
      // echo "||speaking:",print_r($speak),"||<br>";

        echo buttonnum($token);
        echo "%";

      echo substr($speak[0][2],0,2);
      echo "&";
      echo substr($speak[0][2],2,3);
      echo "^";
     echo "%";

     if($speak==NULL){
         echo "%비어있지 않아요";
         break;
       }

      if(strcmp(substr($speak[0][0],0,2),'09')!=0 & strcmp(substr($speak[0][0],0,2),'10')!=0){
      echo "9시부터",substr($speak[0][0],0,2),"시까지";
        }
      for($i=0;$i<count($speak)-1;$i++){
        if(time_diff($speak[$i+1][0],$speak[$i][1],"m")>=60){
        echo substr($speak[$i][1],0,2),"시",substr($speak[$i][1],3,2),"분에서 ";
        echo substr($speak[$i+1][0],0,2),"시",substr($speak[$i+1][0],3,2),"분까지";
        if($i==(count($speak)-1))break;
        echo ", ";
        }
      }
      if(count($speak)==1)
        echo substr($speak[0][1],0,2),"시",substr($speak[0][1],3,2),"분부터 쭉 ";
      else
        echo substr($speak[count($speak)-1][1],0,2),"시",substr($speak[count($speak)-1][1],3,2),"분부터 쭉 ";
          echo "비어있어요.";

      break;

    case "시간표":
      $speak = all_select($ret);
      // print_r($speak);
      echo "%";
      if($speak==NULL){
        echo "수업이 없어요.";
        break;
      }else{
      // print_r($speak);
      echo "오늘이 ",$speak[0][4],"요일이니깐.. ";
      echo $speak[0][2],"교시에서 ",$speak[count($speak)-1][3],"교시까지 ";
      echo "수업이 있고 자세한 사항은 다음과 같아요.";
      }
      break;

      case "복사점":
        $speak = all_select($ret);


        if($speak==NULL){
          echo "복사점이 없어요.";
          break;
        }

        echo "%";
        for($i=0;$i<count($speak);$i++){
          echo $speak[$i][2]," ",$speak[$i][3],"호";
          if($i==(count($speak)-1))break;
          echo "와 ";
        }
        echo "에 있어요.";
        // echo $speak[0][2],"교시에서 ",$speak[count($speak)-1][3],"교시까지 ";
        // echo "수업이 있고 자세한 사항은 다음과 같아요.";

        break;

    case "복사점":
      $speak = all_select($ret);

      echo "%";


      break;

    case "와이파이":
      $speak = all_select($ret);
      // print_r($speak);
      echo buttonnum($token);
      echo "%";
      buttontable($token);
      echo "%";
      if($speak==NULL){
        echo "와이파이에 대한 정보는 아래에 있어요.";
      }else{
      echo "이 와이파이 비밀번호는 ",$speak[0][2],"이에요";
      }
      break;
    case "놀이문화센터0":
      $speak = all_select($ret);
      // print_r($speak);
      echo buttonnum($token);
      echo "%";
      buttontable($token);
      echo "%";
        echo "놀이에 대한 정보는 아래에 있어요.";
//      echo "%";
      break;

    case "놀이문화센터1":
      $speak = all_select($ret);
      // print_r($speak);
      echo buttonnum($token);
      echo "%";
      buttontable($token);
      echo "%";
      if($speak==NULL){
        echo "놀이에 대한 정보는 아래에 있어요.";
      }else{
          echo select_marker($speak[0][0], '은 ', '는 '),//$speak[0][0],"는 ",
              substr($speak[0][1],0,2),"시",substr($speak[0][1],3,2),"분에서 ",
              substr($speak[0][2],0,2),"시",substr($speak[0][2],3,2),"분까지 ",
              "이용할 수 있어요.";
            }
      break;
    //
    case "놀이문화센터2":
      $speak = all_select($ret);
      // print_r($speak);
      echo buttonnum($token);
      echo "%";
      buttontable($token);
      echo "%";
      // print_r($speak);
      echo select_marker($speak[0][0], '은 ', '는 '), $speak[0][1],"에 있어요.";
      break;

    case "무인프린트0":
        $speak = all_select($ret);
        // print_r($speak);

        echo buttonnum($token);
        echo "%";
        buttontable($token);
        echo "%";
       //  for($i=0;$i<count($speak);$i++){
       //    echo $speak[$i][2],$speak[$i][3];
       //  if($i==count($speak)-1)break;
       //    echo ", ";
       //  }
       // echo "에서 프린트 할수 있어요.";
       echo "프린트에 대한 정보는 아래에 있어요";
        break;


    case "무인프린트1":
            $speak = all_select($ret);
            // print_r($speak);
            echo buttonnum($token);
            echo "%";
            $token=$token."/".$speak[0][1];
            buttontable($token);
            echo "%";
            if($speak==NULL){
                echo "프린트에 대한 정보는 아래에 있어요.";
            }else{
           echo $speak[0][2]," ";
            for($i=0;$i<count($speak);$i++){
              echo $speak[$i][3];
              if($i==count($speak)-1)break;
              echo ", ";
            }echo "에서 프린트 할수 있어요.";
          }
        break;


      case "무인프린트2":
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";
                $token=$token."/".$speak[0][1];
                buttontable($token);
                echo "%";
                if($speak==NULL){
                    echo "프린트에 대한 정보는 아래에 있어요.";
                }else{
               echo $speak[0][2];
                for($i=0;$i<count($speak);$i++){
                  echo " ",$speak[$i][3];
                  if($i==count($speak)-1)break;
                  echo ", ";
                }echo "에서 프린트 할수 있어요.";
              }
          break;
          case "음식점0":
              $speak = all_select($ret);
              // print_r($speak);
              echo buttonnum($token);
              echo "%";

              buttontable($token);
              echo "%";
              // echo "음식점에 대한 정보는 아래에 있어요.";
              if($speak==NULL){
                echo "지금 열려있는 음식점은 없어요";
              }else{
              echo "지금이 ",substr(date("H:i:s"),0,2),"시 ",substr(date("H:i:s"),3,2),"분이니까 ";
              for($i=0;$i<count($speak)-1;$i++){
                echo str_replace("&","앤",$speak[$i][0]);
                if($i==count($speak)-1)break;
                echo ", ";
              }
              echo select_marker($speak[count($speak)-1][0], '이', '가')," 있어요.";
            }
              break;
          case "음식점1":
              $speak = all_select($ret);
              // print_r($speak);

              echo buttonnum($token);
              echo "%";

              buttontable($token);
              echo "%";
              echo select_marker($speak[0][0], '은 ', '는 '),$speak[0][2]," ",$speak[0][3],"호에 있어요." ;
              break;

        case "음식점2":
            $speak = all_select($ret);
            // echo print_r($speak),"<br>";
            // print_r($speak_3);
            echo buttonnum($token);
            echo "%";

            buttontable($token);
            echo "%";
            // print_r($speak);
            echo select_marker($speak[0][0], '은 ', '는 ');
            for($i=0;$i<count($speak);$i++){
            echo substr($speak[$i][2],0,2),"시",substr($speak[$i][2],3,2),"분에서 ";
            echo substr($speak[$i][3],0,2),"시",substr($speak[$i][3],3,2),"분까지 ";
            }
            echo "운영하고 있어요.";
            break;
        //
        case "음식점3":
            $speak = all_select($ret);
            // echo print_r($speak),"<br>";
            echo buttonnum($token);
            echo "%";

            buttontable($token);
            echo "%";
            echo "주말에 가능한 식당으로 ";
            for($i=0;$i<count($speak)-1;$i++){
                echo str_replace("&","앤",$speak[$i][0]);
              if($i==count($speak)-1)break;
              echo ", ";

            }
            echo str_replace("&","앤",select_marker($speak[count($speak)-1][0], '을 ', '를 ')),"운영하고 있어요.";
            break;
        //
        case "음식점4":
            $speak = all_select($ret);
            // echo print_r($speak),"<br>";
            echo buttonnum($token);
            echo "%";

            buttontable($token);
            echo "%";
            echo "주말에 가능한 식당으로 ";
            for($i=0;$i<count($speak);$i++){
              if($speak[$i-1][0]!=$speak[$i][0]){
                echo str_replace("&","와",$speak[$i][0]);
              echo ", ";
            }
            }
            echo str_replace("&","앤",select_marker($speak[count($speak)-1][0], '을 ', '를 ')),"운영하고 있어요.";
            break;
        //
        case "음식점5":
            $speak = all_select($ret);
            // echo print_r($speak),"<br>";
            echo buttonnum($token);
            echo "%";

            buttontable($token);
            echo "%";
            echo select_marker($speak[0][0], '은 ', '는 ')," ";
            for($i=0;$i<count($speak);$i++){
                echo $speak[$i][1]," ",$speak[$i][2];
                if(is_numeric($speak[$i][2])){
                  echo "호 ";
                }else {
                  echo " ";
                }
                echo $speak[$i][3];
              if($i==count($speak)-1)break;
                echo ", ";
            }
            echo "에서 먹을 수 있어요.";
            break;


            case "여학생휴게실1":
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";

                buttontable($token);
                echo "%";
                if($speak==NULL){
                    echo "휴게실에 대한 정보는 아래에 있어요.";
                }else{
                for($i=0;$i<count($speak);$i++){
                echo $speak[$i][1];
                if($i==count($speak)-1)break;
                echo ", ";
              }
              echo "에 있어요.";
            }
                break;
          case "여학생휴게실2":
              $speak = all_select($ret);
              // print_r($speak);
              echo buttonnum($token);
              echo "%";

              buttontable($token);
              echo "%";
              if($speak==NULL){
                  echo "휴게실에 대한 정보는 아래에 있어요.";
              }else{
              for($i=0;$i<count($speak);$i++){
              echo $speak[$i][1];
              if($i==count($speak)-1)break;
              echo ", ";
            }
            echo "에 있어요.";
          }
              break;


          case "자판기1":
              $speak = all_select($ret);
              echo buttonnum($token);
              echo "%";

              buttontable($token);
              echo "%";
              // for($i=0;$i<count($speak);$i++){
              //   echo $speak[$i][1],$speak[$i][2];
              //   if($i==count($speak)-1)break;
              //   echo ", ";
              // }
            // echo "에 있어요.";
            echo "자판기에 대한 정보는 아래와 같아요.";
//              echo "%";
              break;
          case "자판기2":
            $speak = all_select($ret);
            // print_r($speak);
            echo buttonnum($token);
            echo "%";
            buttontable($token);
            echo "%";
            if($speak==NULL){
              echo "자판기에 대한 정보는 아래에 있어요.";
            }else{
            for($i=0;$i<count($speak);$i++){
              echo $speak[$i][1],$speak[$i][2];
              if($i==count($speak)-1)break;
              echo ", ";
            }
          echo "에 있어요.";
            }
            break;
//
          case "제휴업체":
                $speak = all_select($ret);
                //print_r($speak);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                if($speak==NULL){
                  echo "제휴업체에 대한 정보는 아래에 있어요.";
                }else{
                for($i=0;$i<count($speak);$i++){
                  echo $speak[$i][0];
                  if($i==count($speak)-1)break;
                  echo ", ";
                }
                echo "이 있어요.";
              }
                break;
          case "카페1":
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                if($speak==NULL){
                  echo "지금 열려있는 카페는 없어요";
                }else{
                  echo "카페에 대한 정보는 아래에 있어요";
                  // echo "음.. 지금이 ",substr(date("H:i:s"),0,2),"시 ",substr(date("H:i:s"),3,2),"분이니까 ";
                  // for($i=0;$i<count($speak);$i++){
                  //   echo $speak[$i][0];
                  //   // `매장명`,
                  //   // `호관`,
                  //   // `건물`,
                  //   // `호`,
                  //   // `평일종료시간`
                  //   if($i==count($speak)-1)break;
                  //   echo ", ";
                  // }
                  // echo "지금 열려 있어요.";
                }



                break;
          case "카페2":
            $speak = all_select($ret);
            // print_r($speak);
            $speak_2 = select_array($speak,array(1,3));
            echo buttonnum($token);
            echo "%";
            buttontable($token);
            echo "%";
            // print_r($speak);
            echo str_replace("&","앤",select_marker($speak[0][0], '은 ', '는 '));
            for($i=0;$i<count($speak);$i++){
              echo $speak[$i][2]," ",$speak[$i][3],"호 ";
            }
            echo "에 있어요.";
              break;
          case "카페3":
          $speak = all_select($ret);
          // print_r($speak);
          echo buttonnum($token);
          echo "%";
          buttontable($token);
          echo "%";
          for($i=0;$i<count($speak);$i++){
            echo str_replace("&","앤",select_marker($speak[$i][0], '은 ', '는 ')),substr($speak[$i][1],0,2),"시",substr($speak[$i][1],3,2),"분에서 ";
            echo substr($speak[$i][2],0,2),"시",substr($speak[$i][2],3,2),"분까지 ";
          }
          echo " 운영하고 있어요.";

              break;

          //
          case "카페4":
          $speak = all_select($ret);
          // print_r($speak);
          echo buttonnum($token);
          echo "%";
          buttontable($token);
          echo "%";
          for($i=0;$i<count($speak)-1;$i++){
            echo str_replace("&","앤",$speak[$i][0]);
            if($i==count($speak)-1)break;
            echo ", ";
          }
          echo str_replace("&","앤",select_marker($speak[count($speak)-1][0], '을 ', '를 ')),"운영하고 있어요.";

              break;
          //
          case "편의점0":
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                echo "편의점에 대한 정보는 아래에 있어요. ";
                break;

          case "편의점1":
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                echo "해당 편의점은 ";
                for($i=0;$i<count($speak);$i++){
                  echo $speak[$i][1]," ",$speak[$i][3];
                  if(is_numeric($speak[$i][3]))
                    echo "호";
                  if($i==(count($speak)-1))break;
                  echo ", ";
                }echo "에 있어요.";
                break;
          case "편의점2":
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                if($speak==NULL){
                  echo "편의점에 대한 정보는 아래에 있어요.";
                }else{
                for($i=0;$i<count($speak);$i++){
                  echo str_replace("&","앤",select_marker($speak[$i][0], '은 ', '는 '));
                  echo substr($speak[$i][1],0,2),"시",substr($speak[$i][1],3,2),"분에서 ";
                  echo substr($speak[$i][2],0,2),"시",substr($speak[$i][2],3,2),"분까지 ";
                  if($i==count($speak)-1)break;
                  echo ", ";
                }
                echo "운영하고 있어요.";
              }
                break;
          case "편의점3":
                $speak = all_select($ret);
                // print_r($speak);
                $speak_2 = select_array($speak,array(1,3));
                echo buttonnum($token);
                echo "%";
                // print_r($speak_2);
                buttontable($token);
                echo "%";
                if($speak==NULL){
                  echo "지금 열려있는 편의점이 없어요";
                }else{
                echo "음.. 지금이 ",substr(date("H:i:s"),0,2),"시 ",substr(date("H:i:s"),3,2),"분이니까 ";
                for($i=0;$i<count($speak);$i++){
                  echo $speak[$i][2];
                  if($i==count($speak)-1)break;
                  echo ", ";
                }
                echo "에 있는 편의점이 지금 열려 있어요.";
              }
                break;
          case "편의점4":
//                echo "%";
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                  if($speak==NULL){
                    echo "편의점에 대한 정보는 아래에 있어요.";
                  }else{
                echo "주말에는 ";
                for($i=0;$i<count($speak);$i++){
                  echo $speak[$i][2]," ",$speak[$i][3];
                  if(is_numeric($speak[$i][3])){
                    echo "호, ";
                  }else{
                    if($i==count($speak)-1)break;
                      echo ", ";
                  }
                }
                echo "에 있는 편의점이 열려 있어요.";
              }
              //   $start_time=array();$end_time=array();
              //   for($i=0;$i<count($speak);$i++){
              //     array_push($start_time,substr($speak[$i][4],0,2));
              //     array_push($end_time,substr($speak[$i][5],0,2));
              //   }
              //   echo "모든 편의점이 ",max($start_time),"시에서 ",min($end_time),"시까지";
              //   echo " 공통적으로 운영하고, 자세한 내용은 표와 같아요.";
              // }
                break;
          //
          case "복지시설0":
                $speak = all_select($ret);
                // echo print_r($speak),"<br>";
                $speak_2 = select_array($speak,array(1,2,3));
                $speak_2 = array_tounique($speak_2);
                // print_r($speak_2);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                  echo "복지시설에 대한 정보는 아래에 있어요.";
                break;
          case "복지시설1":
                $speak = all_select($ret);
                // print_r($speak);
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                // print_r($speak);
                echo select_marker($speak[0][0], '은 ', '는 ');
                echo substr($speak[0][1],0,2),"시",substr($speak[0][1],3,2),"분에서 ";
                echo substr($speak[0][2],0,2),"시",substr($speak[0][2],3,2),"분까지 ";
                echo "이용가능하고 주말에는 ";
                if(strcmp($speak[0][3],"휴점")==0){
                  echo "쉬어요.";
                }else{
                  echo $speak[0][3]," 이용 할 수 있어요";
                }
                break;
          case "복지시설2":
                $speak = all_select($ret);
                // print_r($speak);;
                echo buttonnum($token);
                echo "%";
                buttontable($token);
                echo "%";
                if($speak==NULL){
                  echo "복지시설에 대한 정보는 아래에 있어요.";
                }else{
                for($i=0;$i<count($speak)-1;$i++){
                  echo $speak[$i][0];
                  echo ", ";
                }
                echo select_marker($speak[count($speak)-1][0],'이 ','가 '),"있어요.";
              }
                break;

              //
              case "복지시설3":
                    $speak = all_select($ret);
                    // print_r($speak);
                    echo buttonnum($token);
                    echo "%";
                    buttontable($token);
                    echo "%";

                    echo select_marker($speak[0][0], '은 ', '는 '),$speak[0][2]," ",$speak[0][3],"호에 있어요.";
                    break;

          // case "XX":
          //       $speak = all_select($ret);
          //       print_r($speak);
          //       echo "%";
          //
          //       echo "%";
          //       break;

    default:
      $ran = mt_rand(0,4);
      echo "%";
      if($ran==0)
        echo "잘 모르겠어요. 다시 질문해 주세요.";
      else if($ran==1)
        echo "잘 안들리니 크게 좀..";
      else if($ran==2)
        echo "뭐라하는지 이해가 안가네요";
      else if($ran==3)
        echo "please speak repeat to me line by line";
      else if($ran==4)
        echo "알아 먹을 수 있게 말을 하란 말이야";
      break;
  }
}


?>
