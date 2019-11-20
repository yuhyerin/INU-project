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
    `편의점`";
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


echo "매장명","*","주말운영여부","*","호관","*","건물명","*","호","&";
if($arrays_1==NULL){
  echo "-","*","-","*","-","*","-","*","-";
}else{
//문자열
for($i=0;$i<count($arrays_1);$i++){
  for($j=0;$j<count($arrays_1[$i]);$j++){
    if($arrays_1[$i][$j]==NULL)
      echo "없음";
    echo str_replace("&","과 ",$arrays_1[$i][$j]);
  if($j==count($arrays_1[$i])-1)break;
  echo "*";
  }
  if($i==count($arrays_1)-1)break;
  echo "&";
  }
}




echo "%";

//말하기
if(count($arrays_1)>5){
  $ran = mt_rand(0,2);
  switch($ran){
    case 0:
        echo "GS는 삼김이 맛있는뎁";
        break;
    case 1:
        echo "GS, CU, 이마트편의점 어디로 가실래요?>_<";
        break;
    case 2:
        echo "편의점 가실거에요? 저도 하나만요>_<!!";
        break;
      }
}else{
for($i=0;$i<count($arrays_1);$i++){
  echo $arrays_1[$i][2],"호관 ",$arrays_1[$i][3];
  if($i==count($arrays_1)-1)break;
  echo ", ";
  //echo ;
}
echo "에서 편의점을 열어요.";
}







// echo "^";
//
// //말하기
// $arrays_2[count($arrays_2)][0]="gg";
// if(count($arrays_2)>5){
//     echo "문의하신 주말 편의점에 관한 정보는 아래와 같습니다";
// }else{
// for($i=0;$i<count($arrays_2)-1;$i++){
//   if(strcmp($arrays_2[$i][0],$arrays_2[$i+1][0])==0)continue;/////////////////////////////이건 일단 보류;
//   echo $arrays_2[$i][1],"호관 ",$arrays_2[$i][2];
//   if($i==count($arrays_2)-2)break;
//   echo ", ";
//   //echo ;
// }
// echo "에서 주말에 편의점을 운영하고 있습니다.";
// }

 ?>
