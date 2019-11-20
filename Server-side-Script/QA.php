<?php
    header('Content-Type: text/html; charset=UTF-8');
    require_once('lib/acting.php');
    require_once('lib/kr.php');
    require_once('lib/data.php');
    // $string = "8_호관_211_호_빈시간_언제_야";////////////////////////////////이거 해결 안됨1
    $string = $_POST['string'];
    $is_db=array("시간표","복사점","빈강의실","놀이문화센터","복지시설",
                  "음식점","여학생휴게실","와이파이","무인프린트","자판기","제휴업체",
                  "주차관리","카페","편의점");

    $result = analysis($string);

    // echo "string : ",$string,"<br>";
    // echo "!!result: ", $result["day"]," ",print_r($result["time"])," ",$result["building"]
    //     ," ",$result["place"]," ",$result["table"]," ",print_r($result["key"])," ",$result["wh"],"<br>";

    if(!in_array($result["table"],$is_db)){
      //db가 아닐때

        $not_sql=not_sql($string);
        echo buttonnum($not_sql[0]),"%%%";
        echo $not_sql[1];

    }else{
        //3. 데이터베이스 연결
        if((strcmp($result["table"],"빈강의실")==0)||(strcmp($result["table"],"시간표")==0)){
          $db_name = 'EmptyLectureRoom';
        }else{
          $db_name="TipInformation";
        }
        $con = connect('localhost','root','1111',$db_name);/////////////////////웹스톰에서 꼭 수정!
        mysqli_set_charset($con,"utf8"); // db 한글 처리

        //4.연결된 데이터베이스에 분석된 문자열에 알맞은 쿼리를 전송함
        $receive = sendDB($con,$result);

        //5. 인간처럼 자연스럽게 응답하도록 함
        speak($receive[0],$receive[1]);
      }
?>
