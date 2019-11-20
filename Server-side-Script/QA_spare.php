<?php
    header('Content-Type: text/html; charset=UTF-8');
    require_once('lib/acting.php');
    require_once('lib/kr.php');

    // $string = "8_호관_211_호_빈시간_언제_야";////////////////////////////////이거 해결 안됨1
    $string = $_GET['string'];


    $result = analysis($string);
    echo "!!result: ", $result["day"]," ",print_r($result["time"])," ",$result["building"]
        ," ",$result["place"]," ",$result["table"]," ",print_r($result["key"])," ",$result["wh"],"<br>";

    // 3. 데이터베이스 연결
    if((strcmp($result["table"],"빈강의실")==0)||(strcmp($result["table"],"시간표")==0)){
      $db_name = 'EmptyLectureRoom';
    }else{
      $db_name="TipInformation";
    }
    $con = connect('localhost','root','1111',$db_name);/////////////////////웹스톰에서 꼭 수정!
    mysqli_set_charset($con,"utf8"); // db 한글 처리


    //4.연결된 데이터베이스에 분석된 문자열에 알맞은 쿼리를 전송함
    $receive = sendDB($con,$result);
  //  echo "<br>",var_dump($receive),"<br>","<br>";
    //5. 인간처럼 자연스럽게 응답하도록 함

    speak($receive[0],$receive[1]);
//    speak($receive,$result["info"]);

?>
