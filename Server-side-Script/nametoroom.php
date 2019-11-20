<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받음
$key=$_POST['building'];
// $key = "SF";
//db에 연결
$con = mysqli_connect('localhost','root','1111','EmptyLectureRoom');

//
if(mysqli_connect_error($con)){
    echo "Mysql 접속 실패!!","<br>";
    echo "오류 원인 : ", mysqli_connect_error();
    return 0;
    }
// echo "접속 성공!","<br>";
//

mysqli_set_charset($con,"utf8"); // db 한글 처리
//$key = 'SF';//시험용 : 대진이가 건물이름을 보내줘야 함.
$sql = "SELECT `강의실` FROM `lectureroom_TB` where `건물코드`='".$key."' group by `강의실`";
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
//말하기
for($i=0;$i<count($arrays);$i++){
  echo substr($arrays[$i][0],2,3),"호";
  if($i==count($arrays)-1)break;
  echo "*";
}



 ?>
