<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받는 변수들
$string = $_POST['string'];
// $string = $_GET['string'];
// $string = "";
$key = explode('%',$string);
// print_r($key);
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
// echo count($key);

mysqli_set_charset($con,"utf8"); // db 한글 처리
//$key = 'SF';//시험용 : 대진이가 건물이름을 보내줘야 함.
if($key[0]==NULL){
  $sql = "SELECT * FROM `여학생휴게실`";
}
else if(count($key)>=3){
  $sql = "SELECT * FROM `여학생휴게실` WHERE `내부시설` LIKE '%".$key[0]."%' AND `내부시설` LIKE '%".$key[1]."%' AND `내부시설` LIKE '%".$key[2]."%'";
}else if(count($key)==2){
  $sql = "SELECT * FROM `여학생휴게실` WHERE `내부시설` LIKE '%".$key[0]."%' AND `내부시설` LIKE '%".$key[1]."%'";
}else if(count($key)==1){
  $sql = "SELECT * FROM `여학생휴게실` WHERE `내부시설` LIKE '%".$key[0]."%'";
}else{
  $sql = "SELECT * FROM `여학생휴게실`";
}

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
echo "호관","&","건물","&","호","&","내부시설","^";

if($arrays==NULL){
  echo "-","&","-","&","-","&","-";
}else{
for($i=0;$i<count($arrays);$i++){
  for($j=0;$j<count($arrays[$i]);$j++){
  echo $arrays[$i][$j];
  if($j==count($arrays[$i])-1)break;
  echo "&";
  }
  if($i==count($arrays)-1)break;
  echo "^";
  }
}
echo "%";

//말하기
if(count($arrays)>6){
        echo "편하게 쉴 수 있는 휴게실이에요 푹 쉬어요";
}else{
for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][1]," ",$arrays[$i][2],"호";
  if($i==count($arrays)-1)break;
  echo ", ";
}
echo "에 있어요.";
}
 ?>