<?php
header('Content-Type: text/html; charset=UTF-8');
require_once("lib/subfunction.php");
//require_once('lib/buttonacting.php');

//unity에서 받는 변수들
$key=$_POST['code'];
 // $key = $_GET['code'];

//db에 연결




//

//$key = 'SF';//시험용 : 대진이가 건물이름을 보내줘야 함.

// echo $key;
// $conn = mysqli_connect('localhost','root','1111','EmptyLectureRoom');
// mysqli_set_charset($conn,"utf8");
// $sql = "SELECT `호관` FROM `building_TB` where `건물코드`='".$key."'";
//
// $ret = mysqli_query($conn,$sql) or die(mysqli_error($conn));
// $code = mysqli_fetch_array($ret);


$con = mysqli_connect('localhost','root','1111','TipInformation');
if(mysqli_connect_error($con)){
    echo "Mysql 접속 실패!!","<br>";
    echo "오류 원인 : ", mysqli_connect_error();
    return 0;
    }
// echo "접속 성공!","<br>";
//
mysqli_set_charset($con,"utf8"); // db 한글 처리
if($key==NULL){
  $sql_1="SELECT * FROM `무인프린트` WHERE `건물코드`='SH'";
}else{
$sql_1 = "SELECT * FROM `무인프린트` WHERE `건물코드` = '".$key."'";
}
$ret = mysqli_query($con,$sql_1);

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
  // echo $row[1];
}

//arrays>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>끝나고 생략
// print_r($arrays);


//문자열
echo "건물","&","위치","^";
if($arrays==NULL){
  echo "-","&","-";
}else{
for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][2],"&",$arrays[$i][3];
  if($i==count($arrays)-1)break;
    echo "^";
}
}
echo "%";
//말하기
if(count($arrays)>5){
  echo "무인프린트에 대한 정보는 아래에 있어요.";
}else{
echo $arrays[$i][2]," ";
for($i=0;$i<count($arrays)-1;$i++){
  echo select_marker($arrays[$i][3],'과 ','와 ');
}
echo $arrays[count($arrays)-1][3]."에 있어요.";
}
 ?>