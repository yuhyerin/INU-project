<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//db에 연결
$con = mysqli_connect('localhost','root','1111','TipInformation');
//$con = mysqli_connect('localhost','root','1111','');


//
if(mysqli_connect_error($con)){
    echo "Mysql 접속 실패!!","<br>";
    echo "오류 원인 : ", mysqli_connect_error();
    return 0;
    }
//echo "접속 성공!","<br>";
//

mysqli_set_charset($con,"utf8"); // db 한글 처리
//$key = 'SF';//시험용 : 대진이가 건물이름을 보내줘야 함.
$sql = "SELECT * FROM `제휴업체`";
$ret = mysqli_query($con,$sql);

//
if(!$ret){
    echo "조회가 실패되었습니다. 실패 원인 :".mysqli_error($con);
    return;
}
//


$arrays = array();

// 테이블 정보를 배열안에 다 넣음
while($row = mysqli_fetch_array($ret)){
  $array = array();
  for($i = 0;$i <= (count($row)/2)-1;$i++){
    array_push($array,$row[$i]);
    }
    array_push($arrays,$array);
}

//arrays>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>끝나고 생략
//print_r($arrays);


//문자열
echo "제휴업체","&","위치","&","혜택","^";
if($arrays==NULL){
  echo "-","&","-","&","-";
}else{
for($i=0;$i<count($arrays);$i++){
  for($j=0;$j<count($arrays[$i]);$j++){
  echo str_replace("%","퍼센트",$arrays[$i][$j]);
  if($j==count($arrays[$i])-1)break;
  echo "&";
  }
  if($i==count($arrays)-1)break;
  echo "^";
}
}

echo "%";

//말하기
echo "우리학교 학생들을 위한 혜택이 있는 제휴업체에요 개꿀>_<!!!!";
  // $ran = mt_rand(0,1);
  // switch($ran){
  //   case 0:
  //       echo "제휴업체 짱많죠?";
  //       break;
  //   case 1:
  //       echo "인천대생을 위한 특별한 혜택!";
  //       break;
  //     }
 ?>
