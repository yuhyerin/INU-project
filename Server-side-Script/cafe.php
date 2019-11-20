<?php
header('Content-Type: text/html; charset=UTF-8');


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
    `카페`";
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

//arrays>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>끝나고 생략
//////////print_r($arrays);


//평일 문자열
echo "매장명","*","주말운영여부","*","호관","*","건물명","*","호","&";
if($arrays_1==NULL){
  echo "-","*","-","*","-","*","-","*","-";
}else{
for($i=0;$i<count($arrays_1);$i++){
  for($j=0;$j<count($arrays_1[$i]);$j++){
    echo str_replace('&','and',$arrays_1[$i][$j]);
  if($j==count($arrays_1[$i])-1)break;
  echo "*";
  }
  if($i==count($arrays_1)-1)break;
  echo "&";
  }
}



echo "%";
if(count($arrays_1)>5){
  $ran = mt_rand(0,3);
  switch($ran){
    case 0:
        echo "나도 아이스아메리카노(ㅠ ㅠ)";
        break;
    case 1:
        echo "나는 팔공티 당도 50퍼가 딱 좋더라~";
        break;
    case 2:
        echo "스낵바(카페테리아)에서 토스트랑 와플 꿀맛이에요 >_<!";
        break;
    case 3:
        echo "쥬씨가서 딸바 한잔 먹고싶다~";
        break;
      }
}else if(count($arrays_1)==0){
    echo "현재시각 운영하는 카페는 없어요";
}else{
$arrays_1[count($arrays_1)][2]="gg";
for($i=0;$i<count($arrays_1)-1;$i++){
  if(strcmp($arrays_1[$i][2],$arrays_1[$i+1][2])==0)continue;
  echo $arrays_1[$i][2];
  if($i==count($arrays_1)-2)break;
  echo ", ";
  //echo ;
}
echo "에서 평일에 카페를 열어요";
}










 ?>
