<?php
header('Content-Type: text/html; charset=UTF-8');
require_once('lib/subfunction.php');


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

//평일
//$key = 'SF';//시험용 : 대진이가 건물이름을 보내줘야 함.
$sql = "SELECT DISTINCT
    `매장명`,
    `주말운영여부`,
    `호관`,
    `건물`,
    `호`
FROM
    `음식점`";
$ret = mysqli_query($con,$sql);

//
if(!$ret){
    echo "조회가 실패되었습니다. 실패 원인 :".mysqli_error($con);
    return;
}
//


$arrays_1= array();

// 테이블 정보를 배열안에 다 넣음
while($row = mysqli_fetch_array($ret)){
  $array = array();
  for($i = 0;$i <= (count($row)/2)-1;$i++){
    array_push($array,$row[$i]);
    }
    array_push($arrays_1,$array);
}


echo "매장명","*","주말운영여부","*","호관","*","건물","*","호","&";
if($arrays_1==NULL){
  echo "-","*","-","*","-","*","-","*","-";
}else{
//문자열
for($i=0;$i<count($arrays_1);$i++){
  for($j=0;$j<count($arrays_1[$i]);$j++){
    echo str_replace('&','와',$arrays_1[$i][$j]);
  if($j==count($arrays_1[$i])-1)break;
  echo "*";
  }
  if($i==count($arrays_1)-1)break;
  echo "&";
  }
}





echo "%";

if(count($arrays_1)>5){
  $ran = mt_rand(0,2);
  switch($ran){
    case 0:
        echo "삼겹살이 땡긴다면 고기굽는집 고고!!>_<";
        break;
    case 1:
        echo "떡볶이 먹을까 햄버거 먹을까~";
        break;
    case 2:
        echo "학식이 질린다면 팝업스토어로 가보세요!!! 7호관 뒷편입니당!";
        break;

      }
}else if(count($arrays_1)==0){
  echo "현재시각 여는 음식점은 없어요.";
}
else{
echo "현재시각 여는 음식점으로 ";
//말하기
for($i=0;$i<count($arrays_1);$i++){
  echo $arrays_1[$i][3],"에서 ",$arrays_1[$i][0];
  if($i==count($arrays_1)-1)break;
  echo ", ";
  //echo ;
}
echo "이 있어요.";
}








 ?>
