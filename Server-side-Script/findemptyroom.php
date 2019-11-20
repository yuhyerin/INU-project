<?php
require_once("lib/subfunction.php");
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받음
$day=$_POST['day'];
$building=$_POST['building'];
$start_time=$_POST['start_time'];
$end_time=$_POST['end_time'];

// $day = "월요일";
// $start_time = "9시";
// $building = "SF";
// $end_time="14시";
// $day=$_GET['day'];
// $building=$_GET['building'];
// $start_time=$_GET['start_time'];
// $end_time=$_GET['end_time'];

$day = mb_substr($day,0,1);
// $start_time = num2time($start_time);
// $end_time = num2time($end_time);
$start_time = str_replace('시','',$start_time);
$end_time = str_replace('시','',$end_time);

if(strlen($start_time)==1)
  $start_time='0'.$start_time;
if(strlen($end_time)==1)
  $end_time='0'.$end_time;
// echo $start_time,$end_time,"hi";

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
//$key = 'SF';//시험용 : 대진이가 건물이름을 보내줘야 함.


$sql = "SELECT `호관` FROM `building_TB` where `건물코드`='".$building."'";
$one = mysqli_query($con,$sql);
$two = mysqli_fetch_array($one);
// echo $two[0],"hi";
/////////////////////////////////////////////
$string = "SELECT DISTINCT
    `강의실`
FROM
    (
    SELECT
        *
    FROM
        `v_emptyLectureroom`
    WHERE
        `호관` = '".$two[0]."'
) a
LEFT JOIN(
    SELECT
        *
    FROM
        `v_emptyLectureroom`
    WHERE
        `호관` = '".$two[0]."' AND `요일` LIKE '".$day."'
) b USING (`강의실`)
WHERE
    b.`강의실` IS NULL
ORDER BY
    `강의실` ASC";

// echo $string;
$return = mysqli_query($con,$string);
if(!$return){
    echo "조회가 실패되었습니다. 실패 원인 :".mysqli_error($con);
    return;
}
//


$full_arrays = array();

// 테이블 정보를 배열안에 다 넣음
while($row = mysqli_fetch_array($return)){
    array_push($full_arrays,$row[0]);
}




$sql = "SELECT DISTINCT
    `강의실`,`시작시간`,`종료시간`
FROM
    (
    SELECT
        `강의실`,
        `시작시간`,
        `종료시간`
    FROM
        `v_emptyLectureroom`
    WHERE
        `요일` LIKE '".$day."' AND `호관` = '".$two[0]."'
) AS TABLE_A
WHERE
    `강의실` NOT IN(
    SELECT
        `강의실`
    FROM
        (
        SELECT
            `강의실`,
            `시작시간`,
            `종료시간`
        FROM
            `v_emptyLectureroom`
        WHERE
            `요일` LIKE '".$day."' AND `호관` = '".$two[0]."'
    ) AS TABLE_B
WHERE
    (
        `시작시간` > '".$start_time.":00:0000' AND `시작시간` < '".$end_time.":00:0000'
    ) OR(
        `종료시간` > '".$start_time.":00:0000' AND `종료시간` < '".$end_time.":00:0000'
    ) OR(
        `종료시간` > '".$end_time.":00:0000' AND `시작시간` < '".$start_time.":00:0000'
    )
)
ORDER BY
    `TABLE_A`.`강의실` ASC";

$ret = mysqli_query($con,$sql);

// echo $sql;
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

$full_arrays=implode("f/",$full_arrays);
$full_arrays=explode("/",$full_arrays);
$full_arrays[count($full_arrays)-1]=$full_arrays[count($full_arrays)-1]."f";
// print_r($full_arrays);
$not_array=array();
// $arrays[-1][0]="hi";
for($i=0;$i<count($arrays);$i++){
  if(strcmp($arrays[$i-1][0],$arrays[$i][0])==0)
    continue;
    array_push($not_array,$arrays[$i][0]);
}
// print_r($not_array);

$result_array = array_merge($full_arrays,$not_array);
sort($result_array);

//문자열
// echo substr($arrays[0][0],0,2);
echo $building;
echo "&";
for($i=0;$i<count($result_array);$i++){
  if(strpos($result_array[$i],'f')!==false)
    echo substr($result_array[$i],2,3),"호f";
  else
    echo substr($result_array[$i],2,3),"호";
  if($i==count($result_array)-1)
    break;
  echo "*";
}
echo "^";
echo "%";
//말하기
if($result_array==NULL||$result_array[0]=='f'){
  echo "해당조건의 빈강의실이 없어요.";
}
else if(count($result_array)<=6){
  for($i=0;$i<count($result_array);$i++){
    echo substr($result_array[$i],2,3),"호";
    if($i==count($result_array)-1)break;
    echo ", ";
  }
  echo "가 비어 있어요.";
}else{
  $floor=array(0,0,0,0,0);
  for($i=0;$i<count($result_array);$i++){
    switch(substr($result_array[$i],2,1)){
      case 1:
        $floor[0]++;
        break;
      case 2:
        $floor[1]++;
        break;
      //
      case 3:
        $floor[2]++;
        break;
      //
      case 4:
        $floor[3]++;
        break;
      //
      case 5:
        $floor[4]++;
        break;
    }
  }

  for($num=0;$num<5;$num++){
    if($floor[$num]!=0)
      if($floor[$num]==NULL)continue;
      echo $num+1,"층에 ",$floor[$num],"개";
      if($num==4)break;
      echo ", ";
  }

echo "의 빈강의실이 있어요.";
}



 ?>
