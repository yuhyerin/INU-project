<?php
header('Content-Type: text/html; charset=UTF-8');
//require_once('lib/buttonacting.php');

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
$sql = "SELECT
    a.`놀거리`,
    a.`학적`,
    a.`예치금`,
    b.`학적`,
    b.`예치금`,
    a.`시작시간`,
    a.`종료시간`,
    a.`위치`
FROM
    (
    SELECT
        *
    FROM
        `놀이문화센터`
    WHERE
        `학적` LIKE '재학생'
) a,
(
    SELECT
        `놀거리`,
        `학적`,
        `예치금`
    FROM
        `놀이문화센터`
    WHERE
        `학적` LIKE '휴학생'
) b
WHERE
    a.`놀거리` = b.`놀거리`

";
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
  for($i = 0;$i <= (count($row)/2);$i++){
    array_push($array,$row[$i]);
    }
    array_push($arrays,$array);
}

//arrays>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>끝나고 생략
// print_r($arrays);


//문자열
echo "놀거리","&","운영시간","&","위치","&",
    "재학생 예치금","&","휴학생 예치금","^";
    // print_r($arrays);
    if($arrays==NULL){
      echo "-","&","-","&","-","&","-","&","-";
    }else{
for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][0],"&",substr($arrays[$i][5],0,5)," ~ ",substr($arrays[$i][6],0,5),"&"
  ,$arrays[$i][7],"&",$arrays[$i][2],"&",$arrays[$i][4];
  if($i==count($arrays)-1)break;
  echo "^";
  }
}

echo "%";
$ran = mt_rand(0,2);
switch($ran){
  case 0:
      echo "노래방가서 스트레스 풀어볼까요?>_<";
      break;
  case 1:
      echo "저기요,,, 플스방 가보셨어요? 전 아직 안가봤어여(속닥)";
      break;
  case 2:
      echo "우리 공강때 같이 인라인 스케이트 타러가요~~";
      break;
}
 ?>
