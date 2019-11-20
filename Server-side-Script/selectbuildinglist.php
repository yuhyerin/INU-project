<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받음

//db에 연결
$con = mysqli_connect('localhost','root','1111','EmptyLectureRoom');

//
if(mysqli_connect_error($con)){
    echo "Mysql 접속 실패!!","<br>";
    echo "오류 원인 : ", mysqli_connect_error();
    return 0;
    }
//echo "접속 성공!","<br>";
//

mysqli_set_charset($con,"utf8"); // db 한글 처리

$sql = "SELECT DISTINCT
    lectureroom_TB.건물코드,
    building_TB.호관,
    building_TB.건물이름
FROM
    lectureroom_TB,
    building_TB
WHERE
    lectureroom_TB.건물코드 = building_TB.건물코드";
$ret = mysqli_query($con,$sql);

//
if(!$ret){
    echo "조회가 실패되었습니다. 실패 원인 :".mysqli_error($con);
    return;
}
//


$arrays = array();

//    echo "<br>",var_dump($row),count($row),"<br>","<br>";
while($row = mysqli_fetch_array($ret)){
  $array = array();
  for($i = 0;$i <= (count($row)/2)-1;$i++){
    array_push($array,$row[$i]);
    }
    array_push($arrays,$array);
}

for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][1],"호관 ",$arrays[$i][2],"(",$arrays[$i][0],")";
  if($i==count($arrays)-1)break;
  echo "^";
}



 ?>
