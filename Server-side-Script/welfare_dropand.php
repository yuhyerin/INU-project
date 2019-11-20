<?php
header('Content-Type: text/html; charset=UTF-8');
function have_jongsung2 ($chr) {
	 static $no_jongsung = "가갸거겨고교구규그기개걔게계과괘궈궤괴귀긔까꺄꺼껴꼬꾜꾸뀨끄끼깨꺠께꼐꽈꽤꿔꿰꾀뀌끠나냐너녀노뇨누뉴느니내냬네녜놔놰눠눼뇌뉘늬다댜더뎌도됴두듀드디대댸데뎨돠돼둬뒈되뒤듸따땨떠뗘또뚀뚜뜌뜨띠때떄떼뗴똬뙈뚸뛔뙤뛰띄라랴러려로료루류르리래럐레례롸뢔뤄뤠뢰뤼릐마먀머며모묘무뮤므미매먜메몌뫄뫠뭐뭬뫼뮈믜바뱌버벼보뵤부뷰브비배뱨베볘봐봬붜붸뵈뷔븨빠뺘뻐뼈뽀뾰뿌쀼쁘삐빼뺴뻬뼤뽜뽸뿨쀄뾔쀠쁴사샤서셔소쇼수슈스시새섀세셰솨쇄숴쉐쇠쉬싀싸쌰써쎠쏘쑈쑤쓔쓰씨쌔썌쎄쎼쏴쐐쒀쒜쐬쒸씌아야어여오요우유으이애얘에예와왜워웨외위의자쟈저져조죠주쥬즈지재쟤제졔좌좨줘줴죄쥐즤짜쨔쩌쪄쪼쬬쭈쮸쯔찌째쨰쩨쪠쫘쫴쭤쮀쬐쮜쯰차챠처쳐초쵸추츄츠치채챼체쳬촤쵀춰췌최취츼카캬커켜코쿄쿠큐크키캐컈케켸콰쾌쿼퀘쾨퀴킈타탸터텨토툐투튜트티태턔테톄톼퇘퉈퉤퇴튀틔파퍄퍼펴포표푸퓨프피패퍠페폐퐈퐤풔풰푀퓌픠하햐허혀호효후휴흐히해햬헤혜화홰훠훼회휘희2459";
	 return mb_strpos($no_jongsung, $chr) === false ? true : false;
}

function select_marker2 ($word, $have_jongsung, $no_jongsung) {
	 $last_chr = mb_substr($word, -1, 1);
	 return have_jongsung2($last_chr) ?
			 $word.$have_jongsung :
			 $word.$no_jongsung;
}

//unity에서 받는 변수들
$key=$_POST['key'];
// $key = $_GET['key'];
// $key = "플스방";
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
$sql = "SELECT DISTINCT * FROM `복지시설` WHERE `복지시설` NOT LIKE '복사점' AND `복지시설` LIKE '%".$key."%'";
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
// print_r($arrays);


//문자열
echo "복지시설","&","호관","&","건물","&","호","&","운영시간","^";
if($arrays==NULL){
  echo "-","&","-","&","-","&","-","&","-";
}else{
// print_r($arrays);
for($i=0;$i<count($arrays);$i++){
  echo $arrays[$i][0],"&",$arrays[$i][1],"&",$arrays[$i][2],"&",
  $arrays[$i][3],"&",substr($arrays[$i][4],0,5)," ~ ",substr($arrays[$i][5],0,5);
  if($i==count($arrays)-1)break;
  echo "^";
}
}

echo "%";
//말하기
if(count($arrays)>5){
  echo "복지시설에 대한 정보는 아래에 있어요.";
}else{
for($i=0;$i<count($arrays);$i++){
  echo select_marker2($arrays[$i][0], '은 ', '는 '),$arrays[$i][1],"호관 ",$arrays[$i][2],"에서 "
  ,substr($arrays[$i][4],0,2),"시 ",substr($arrays[$i][4],3,2),"분에서 ",
  substr($arrays[$i][5],0,2),"시 ",substr($arrays[$i][5],3,2),"분까지 ";
}echo "열어요";
}
 ?>
