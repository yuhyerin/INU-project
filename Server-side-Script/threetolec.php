<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받음
// $day = mb_substr($_POST['day'],0,1);
// $class = substr($_POST['class'],0,5);
$day = mb_substr($_POST['day'],0,1);
$class = substr($_POST['class'],0,5);
// echo "hi", $day,$class;
// $day = mb_substr("금요일",0,1);
// $class = substr("SH111호",0,5);
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
//$key = array("화","SH","505");//시험용 : 대진이가 보낸 파일에서 요일,건물이름,강의실을 보내줘야함.

if(strcmp($day,"오")==0){

  $sql = "SELECT
    `교과목명`,
    `담당교수명`,
    `시작교시`,
    `종료교시`
FROM
    `v_emptyLectureroom`
WHERE
    `요일` LIKE(
    SELECT CASE
        WEEKDAY(CURDATE() +0) WHEN '0' THEN '월' WHEN '1' THEN '화' WHEN '2' THEN '수' WHEN '3' THEN '목' WHEN '4' THEN '금' WHEN '5' THEN '토' WHEN '6' THEN '일'
        END AS DAYOFWEEK) AND `강의실` LIKE '".$class."'
ORDER BY
    `v_emptyLectureroom`.`시작시간` ASC";
}else{

  $sql = "SELECT `교과목명`,`담당교수명`,`시작교시`,`종료교시` FROM `v_emptyLectureroom` where `요일`='".$day."' && `강의실` ='".$class."' ORDER BY `시작시간`";
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
//문자열
  echo "교과목명","&","담담교수명","&","교시","^";
if($arrays==NULL){
  // echo "<br>1.:", print_r($arrays),"<br>";
  echo "-","&","-","&","-";
}else{
    // echo "<br>2.:", print_r($arrays),"<br>";
for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][0],"&",$arrays[$i][1],"&",$arrays[$i][2],"교시 ~ ",$arrays[$i][3],"교시";
  if($i==count($arrays)-1)break;
  echo "^";
  }
}

//말하기
echo "%";
for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][2],"교시에서 ",$arrays[$i][3],"교시에 ",$arrays[$i][0]," ";
  if($i==count($arrays)-1)break;
  echo ",";
}
if($arrays==NULL)echo "수업이 없어요.";
else echo "수업이 있어요";



 ?>
