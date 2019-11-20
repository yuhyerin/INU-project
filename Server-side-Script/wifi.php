<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

//unity에서 받는 변수들
//$key=$_POST['key'];

//db에 연결
$con = mysqli_connect('localhost','root','1111','TipInformation');
//$con = mysqli_connect('localhost','root','1111','');


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
$sql = "SELECT * FROM `와이파이`";
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

echo "Wifi","&","ID","&","Password","^";
if($arrays==NULL){
  echo "-","&","-","&","-";
}else{
//문자열
for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][0],"&";
  if($arrays[$i][1]==NULL) echo "없음";
  else echo $arrays[$i][1];
  echo "&",$arrays[$i][2];
  if($i==count($arrays)-1)
    break;
  echo "^";
}
}

echo "%";
//말하기
$ran = mt_rand(0,1);
switch($ran){
  case 0:
      echo "우리학교 와이파이는 3개가 있고 아이디와 비밀번호는 아래와 같아요~";
      break;
  case 1:
      echo "와이파이가 있긴한데… 사실 잘 안터져요… (ㅠ ㅠ)";
      break;

}



 ?>
