<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받는 변수들
$string = $_POST['string'];
// $string = $_GET['string'];
//$shower=$_POST['shower'];
//$stone=$_POST['stone'];
//$sleep=$_POST['sleep'];

// $string = "커피%음료%과자%";
$key = explode('%',$string);
//print_r($key);
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
if($key[0]==NULL){
    $sql = "SELECT `호관`, `건물`, `위치` FROM `자판기`";
}else if(count($key)==4){
  // echo "hi4";
  // print_r($key);
  $sql = "SELECT `호관`, `건물`, `위치` FROM `자판기` WHERE `".$key[0]."` LIKE 'o' AND `".$key[1]."` LIKE 'o' AND `".$key[2]."` LIKE 'o'";
}else if(count($key)==3){
  // echo "hi3";
  // print_r($key);
  $sql = "SELECT `호관`, `건물`, `위치` FROM `자판기` WHERE `".$key[0]."` LIKE 'o' AND `".$key[1]."` LIKE 'o'";
}else if(count($key)==2){
  // echo "hi2";
  // print_r($key);
  $sql = "SELECT `호관`, `건물`, `위치` FROM `자판기` WHERE `".$key[0]."` LIKE 'o'";
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
echo "호관","&","건물","&","위치","^";
if($arrays==NULL){
  echo "-","&","-","&","-";
}else{
// print_r($arrays);
for($i=0;$i<count($arrays);$i++){
  if($arrays[$i-1][0]!=$arrays[$i][0]){
    echo $arrays[$i][0],"&",$arrays[$i][1],"&";
  }
    echo $arrays[$i][2];

  if($i==count($arrays)-1)break;

  if($arrays[$i][0]!=$arrays[$i+1][0]){
    echo "^";
  }else{
    echo ",";
  }
  }
}

// for($i=0;$i<count($arrays);$i++){
//   if($arrays[$i-1][0]!=$arrays[$i][0])
//     echo $arrays[$i][0],"&",$arrays[$i][1],"&";
//   for($j=0;$j<count($arrays[$i]);$j++){
//     echo $arrays[$i][2];
//   if($j==count($arrays[$i])-1)break;
//   echo ",";
//   }
//   if($i==count($arrays)-1)break;
//   echo "^";
// }

echo "%";
//말하기
if(count($arrays)>6){
  $ran = mt_rand(0,1);
  switch($ran){
    case 0:
        echo "공부하다 목마를 때 자판기 음료수 한잔!! 자판기 위치 확인해보세요~";
        break;
    case 1:
        echo "저는 데자와 좋아해요 >_<";
        break;
      }
}else{
for($i=0;$i<count($arrays);$i++){
  if($arrays[$i-1][0]!=$arrays[$i][0]){
    echo $arrays[$i][1]," ";
  }
  echo $arrays[$i][2];
  if($i==count($arrays)-1)break;
  echo ", ";
}
echo "에 있어요.";
}
 ?>
