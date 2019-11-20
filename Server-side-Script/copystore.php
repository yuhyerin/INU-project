<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받는 변수들

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
$sql = "SELECT * FROM `복지시설` WHERE `복지시설` LIKE '복사점'";
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
echo "호관","&","건물","&","호","&","운영시간","^";
if($arrays==NULL){
  echo "-","&","-","&","-","&","-";
}else{
for($i=0;$i<count($arrays);$i++){
  for($j=1;$j<count($arrays[$i])-4;$j++){
  if($j==(count($arrays[$i])-5)){
      echo substr($arrays[$i][$j],0,5)," ~ ",substr($arrays[$i][$j+1],0,5);
  }else
  echo $arrays[$i][$j],"&";
  }
  if($i==count($arrays)-1)break;
  echo "^";
  }
}
echo "%";

//말하기
if(count($arrays)>5){
  $ran = mt_rand(0,1);
  switch($ran){
    case 0:
        echo "복지회관 1층 복사점에서는 아이디를 만들어서 충전식으로 사용할수 있어용 참고하세요~";
        break;
    case 1:
        echo "도서관 지하에 있는 복사점은 현금을 챙기시는게 좋아요!!>_< 흑백 1페이지에 50원~";
        break;
      }
}else{
// for($i=0;$i<count($arrays);$i++){
//   echo $arrays[$i][1],"호관 ",$arrays[$i][2]," ",$arrays[$i][3],"호";
//   if($i==count($arrays)-1)break;
//   echo "와 ";
// }
// echo "에 복사점이 있어요.";
// -> 원본
$ran = mt_rand(0,1);
switch($ran){
  case 0:
      echo "복지회관 1층 복사점에서는 아이디를 만들어서 충전식으로 사용할수 있어용 참고하세요~";
      break;
  case 1:
      echo "도서관 지하에 있는 복사점은 현금을 챙기시는게 좋아요!!>_< 흑백 1페이지에 50원~";
      break;

}
}
 ?>
