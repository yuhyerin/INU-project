<?php
require_once("data.php");

 ?>

<?php
function have_jongsung ($chr) {
	 static $no_jongsung = "가갸거겨고교구규그기개걔게계과괘궈궤괴귀긔까꺄꺼껴꼬꾜꾸뀨끄끼깨꺠께꼐꽈꽤꿔꿰꾀뀌끠나냐너녀노뇨누뉴느니내냬네녜놔놰눠눼뇌뉘늬다댜더뎌도됴두듀드디대댸데뎨돠돼둬뒈되뒤듸따땨떠뗘또뚀뚜뜌뜨띠때떄떼뗴똬뙈뚸뛔뙤뛰띄라랴러려로료루류르리래럐레례롸뢔뤄뤠뢰뤼릐마먀머며모묘무뮤므미매먜메몌뫄뫠뭐뭬뫼뮈믜바뱌버벼보뵤부뷰브비배뱨베볘봐봬붜붸뵈뷔븨빠뺘뻐뼈뽀뾰뿌쀼쁘삐빼뺴뻬뼤뽜뽸뿨쀄뾔쀠쁴사샤서셔소쇼수슈스시새섀세셰솨쇄숴쉐쇠쉬싀싸쌰써쎠쏘쑈쑤쓔쓰씨쌔썌쎄쎼쏴쐐쒀쒜쐬쒸씌아야어여오요우유으이애얘에예와왜워웨외위의자쟈저져조죠주쥬즈지재쟤제졔좌좨줘줴죄쥐즤짜쨔쩌쪄쪼쬬쭈쮸쯔찌째쨰쩨쪠쫘쫴쭤쮀쬐쮜쯰차챠처쳐초쵸추츄츠치채챼체쳬촤쵀춰췌최취츼카캬커켜코쿄쿠큐크키캐컈케켸콰쾌쿼퀘쾨퀴킈타탸터텨토툐투튜트티태턔테톄톼퇘퉈퉤퇴튀틔파퍄퍼펴포표푸퓨프피패퍠페폐퐈퐤풔풰푀퓌픠하햐허혀호효후휴흐히해햬헤혜화홰훠훼회휘희2459";
	 return mb_strpos($no_jongsung, $chr) === false ? true : false;
}

function select_marker ($word, $have_jongsung, $no_jongsung) {
  if(mb_strpos($word,"(")!==false)
		$last_chr = mb_substr($word,mb_strpos($word,"(")-1,1);
	else
	 	$last_chr = mb_substr($word, -1, 1);

	 return have_jongsung($last_chr) ?
			 $word.$have_jongsung :
			 $word.$no_jongsung;
}




function arr_del($arr,$value){
  $b = array_search($value,$arr);
  if($b!==FALSE)unset($arr[$b]);

  return $arr;

}


function liter($speak,$num1,$num2){
  //$num1에는 호관번호, $num2는 그 건물의 구체적인 장소
  for($i=0;$i<count($speak);$i++){
    if(is_numeric($speak[$i][$num1])&&($speak[$i-1][$num1]!=$speak[$i][$num1])){
    echo roomfordata($speak[$i][$num1]);
    echo "&";
    }
    echo $speak[$i][$num2];
    if($i==count($speak)-1)break;
    if($speak[$i][$num1]==$speak[$i+1][$num1])
      echo "*";
    if($speak[$i][$num1]!=$speak[$i+1][$num1])
      echo "^";
  }
}

function roomtofloor($array){
  $floor=array(0,0,0,0,0);
  for($i=0;$i<count($array);$i++){
    switch(substr($array[$i][0],2,1)){
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
  return $floor;
}

function roomfordata($building){

  if(strcmp($building,"건물없음")==0)
    $building = '7';

  $string = "SELECT `건물코드` FROM `building_TB` WHERE `호관`='".$building."'";

    $conn = connect('localhost','root','1111','EmptyLectureRoom');
    mysqli_set_charset($conn,"utf8"); // db 한글 처리
    //조회
      $ret = mysqli_query($conn,$string);
      if(!$ret){
         echo "조회가 실패되었습니다. 실패 원인 :".mysqli_error($conn);
          return;
      }

      $code = all_select($ret)[0][0];

      //echo $building,$code;
      return $code;

}

function buildingforcode($building){
  $string ="SELECT `건물코드` FROM `building_TB` where `건물이름`='%".$building."%'";

    $con = connect('localhost','root','1111','EmptyLectureRoom');
    mysqli_set_charset($con,"utf8"); // db 한글 처리
    $ret = mysqli_query($con,$string);
    $code = all_select($ret);

    return $code;
}


function select_array($array,$colarray){
  for($i=0;$i<count($array);$i++){
    for($j=0;$j<count($colarray);$j++)
      $result[$i][$j]=$array[$i][$colarray[$j]];
  }
  return $result;
}

function array_tounique($array){

  $result=array();
  for($i=0;$i<count($array);$i++){
    if($array[$i]==NULL){
      continue;
    }else if(!in_array($array[$i],$result))
      array_push($result,$array[$i]);
  }
  return $result;
}

function time_diff($time1,$time2,$unit){
  if($unit == "h"){
    $r = strtotime("2000-01-01".$time1)-strtotime("2000-01-01".$time2);
    $r = ceil($r/(60*60));
  }else if($unit =="m"){
    $r = strtotime("2000-01-01".$time1)-strtotime("2000-01-01".$time2);
    $r = ceil($r/(60));
  }else{
    $r = strtotime("2000-01-01".$time1)-strtotime("2000-01-01".$time2);
  }

  return $r;

}


function num2time($num){
    switch($num){
        case 1:
            return "13:00:0000";
        case 2:
            return "14:00:0000";
        case 3:
            return "15:00:0000";
        case 4:
            return "16:00:0000";
        case 5:
            return "17:00:0000";
        case 6:
            return "18:00:0000";
        case 7:
            return "19:00:0000";
        case 8:
            return "20:00:0000";
        case 9:
            return "09:00:0000";
        case 10:
            return "10:00:0000";
        case 11:
            return "11:00:0000";
        case 12:
            return "12:00:0000";
        case 13:
            return "13:00:0000";
        //
        case 14:
            return "14:00:0000";
        //
        case 15:
            return "15:00:0000";
        //
        case 16:
            return "16:00:0000";
        //
        case 17:
            return "17:00:0000";
        //
        case 18:
            return "18:00:0000";
        //
        case 19:
            return "19:00:0000";
        //
        case 20:
            return "20:00:0000";
        //
        case 21:
            return "21:00:0000";
        //
        case 22:
            return "22:00:0000";
        //
        case 23:
            return "23:00:0000";
        //
        case 24:
            return "24:00:0000";
        //
        case "오전1":
            return "01:00:0000";
        //
        case "오후1":
            return "13:00:0000";
        //
        case "오전2":
            return "02:00:0000";
        //
        case "오후2":
            return "14:00:0000";
        //
        case "오전3":
            return "03:00:0000";
        //
        case "오후3":
            return "15:00:0000";
        //
        case "오전4":
            return "04:00:0000";
        //
        case "오후4":
            return "16:00:0000";
        //
        case "오전5":
            return "05:00:0000";
        //
        case "오후5":
            return "17:00:0000";
        //
        case "오전6":
            return "06:00:0000";
        //
        case "오후6":
            return "18:00:0000";
        //
        case "오전7":
            return "07:00:0000";
        //
        case "오후7":
            return "19:00:0000";
        //
        case "오전8":
            return "08:00:0000";
        //
        case "오후8":
            return "20:00:0000";
        //
        case "오전9":
            return "09:00:0000";
        //
        case "오후9":
            return "21:00:0000";
        //
        case "오전10":
            return "10:00:0000";
        //
        case "오후10":
            return "22:00:0000";
        //
        case "오전11":
            return "11:00:0000";
        //
        case "오후11":
            return "23:00:0000";
        //
        case "오전12":
            return "24:00:0000";
        //
        case "오후12":
            return "12:00:0000";
        //
        default:
            return NULL;
      }
}

function tounique($receive){
        $i=0;
        $dab=array();
        while($row=mysqli_fetch_array($receive)){
            $dab[$i]=$row[0];
            $i=$i+1;
        }
        return array_unique($dab);
}



function ret_day($array){
  $day = "요일";
  for($i=0;$i<count($array);$i++){

    if(mb_strpos($array[$i],$day)!==false){
      return mb_substr($array[$i],0,1,'utf-8');
    }
  }
  return "오늘";
}



function ret_time($array){
  $time = array();

  for($i=1;$i<count($array);$i++){
  if((mb_strpos($array[$i],"시")!==false && is_numeric($array[$i-1]))
              &&((strcmp($array[$i-2],"오전")==0)|(strcmp($array[$i-2],"오후")==0))){
                array_push($time,$array[$i-2].$array[$i-1]);
  }else if(mb_strpos($array[$i],"시")!==false && is_numeric($array[$i-1])){
        array_push($time,$array[$i-1]);
    }
  }
  if(empty($time))
    return "시간없음";
  return $time;
}


function ret_building($array){
  for($i=1;$i<count($array);$i++){
    if(strcmp($array[$i],"호관")==0 && is_numeric($array[$i-1])){
      $building = $array[$i-1];
      return $building;
    }
  }
return "건물없음";

}


function ret_place($array){
  for($i=1;$i<count($array);$i++){
    if(strcmp($array[$i],"호")==0 && is_numeric($array[$i-1])){
        $place=$array[$i-1];
        return $place;
    }
  }
  return "교실없음";
}

function ret_wh($array){
  $wh=whdata();
  if(array_intersect($wh["언제"],$array)){
    return implode(array_intersect($array,$wh["언제"]));
  }else if(array_intersect($wh["어디서"],$array)){
    return implode(array_intersect($array,$wh["어디서"]));
  }else{
    return "의문사없음";
  }
}
/*
$info["무인프린트"]=array();
$info["복지시설"]=array();
$info["식당"]=array();
$info["여학생휴게실"]=array();
$info["와이파이"]=array();
$info["자판기"]=array();
$info["제휴업체"]=array();
$info["주차관리"]=array();
$info["카페"]=array();
$info["휴게음식"]=array();
$info["와이파이"]=array("inu-wireless","edurom","lgu+","wifi");
*/






function ret_table($array){
  $table = data();

  if(array_intersect($table["와이파이"],$array)){
    return "와이파이";
  }else if(array_intersect($table["시간표"],$array)){
    return "시간표";
  }else if(array_intersect($table["복사점"],$array)){
    return "복사점";
  }
  else if(array_intersect($table["빈강의실"],$array)){
      return "빈강의실";
  // }else if(array_intersect($table["먹거리"],$array)){
  //   return "먹거리";
  }else if(array_intersect($table["놀이문화센터"],$array)){
      return "놀이문화센터";
  }else if(array_intersect($table["무인프린트"],$array)){
      return "무인프린트";
  }else if(array_intersect($table["음식점"],$array)){
      return "음식점";
  }else if(array_intersect($table["여학생휴게실"],$array)){
      return "여학생휴게실";
  }else if(array_intersect($table["자판기"],$array)){
      return "자판기";
  }else if(array_intersect($table["제휴업체"],$array)){
      return "제휴업체";
  }else if(array_intersect($table["주차관리"],$array)){
      return "주차관리";
  }else if(array_intersect($table["카페"],$array)){
      return "카페";
  }else if(array_intersect($table["복지시설"],$array)){
      return "복지시설";
  }else if(array_intersect($table["편의점"],$array)){
      return "편의점";
  }
  else{
    return "다시 말해주실 수 있으세요?";
    }
  }

function ret_key($array,$table){
  $key = keydata();
//  print_r($key["와이파이"]);
//  print_r($array);
  //echo implode(array_intersect($key["와이파이"],$array));
  if(strcmp($table,"와이파이")==0){
    return implode(array_intersect($array,$key["와이파이"]));
  }else if(strcmp($table,"빈강의실")==0){
      return implode(array_intersect($array,$key["빈강의실"]));
  }else if(strcmp($table,"놀이문화센터")==0){

    if(array_intersect($array,$key["놀이문화센터"]["대여물품"])){
      return implode(array_intersect($array,$key["놀이문화센터"]["대여물품"]));
    }

  }else if(strcmp($table,"무인프린트")==0){
    //print_r($array);
    if(array_intersect($array,$key["무인프린트"]["호관"])){
      return implode(array_intersect($array,$key["무인프린트"]["호관"]));
    }else if(array_intersect($array,$key["무인프린트"]["건물명"])){
      return implode(array_intersect($array,$key["무인프린트"]["건물명"]));
    }else{
      return "키워드 없음";
    }

  }else if(strcmp($table,"음식점")==0){

  if(array_intersect($array,$key["음식점"]["주말운영여부"])){
    return implode(array_intersect($array,$key["음식점"]["주말운영여부"]));
  }else  if(array_intersect($array,$key["음식점"]["매장명"])){
      return implode(array_intersect($array,$key["음식점"]["매장명"]));
    }else  if(array_intersect($array,$key["음식점"]["주메뉴"])){
          return implode(array_intersect($array,$key["음식점"]["주메뉴"]));
        }else{
      return "키워드 없음";
    }

  }else if(strcmp($table,"여학생휴게실")==0){
      return implode(array_intersect($array,$key["여학생휴게실"]));
  }else if(strcmp($table,"자판기")==0){
      return implode(array_intersect($array,$key["자판기"]));
  }else if(strcmp($table,"제휴업체")==0){
      return implode(array_intersect($array,$key["제휴업체"]));
  }else if(strcmp($table,"주차관리")==0){
      return implode(array_intersect($array,$key["주차관리"]));
  }else if(strcmp($table,"카페")==0){

    if(array_intersect($array,$key["카페"]["주말운영시간"])){
      return implode(array_intersect($array,$key["카페"]["주말운영시간"]));
    }else  if(array_intersect($array,$key["카페"]["매장명"])){
        return implode(array_intersect($array,$key["카페"]["매장명"]));
      }else{
        return "키워드 없음";
      }

  }else if(strcmp($table,"복지시설")==0){
    // print_r($array);
    if(array_intersect($array,$key["복지시설"]["복지시설"])){
      return implode(array_intersect($array,$key["복지시설"]["복지시설"]));
    }else  if(array_intersect($array,$key["복지시설"]["건물명"])){
        return implode(array_intersect($array,$key["복지시설"]["건물명"]));
      }else if(array_intersect($array,$key["복지시설"]["호관"])){
        return implode(array_intersect($array,$key["복지시설"]["호관"]));
      }
      else{
        return "키워드 없음";
      }

  }else if(strcmp($table,"편의점")==0){

    if(array_intersect($array,$key["편의점"]["매장명"])){
      return implode(array_intersect($array,$key["편의점"]["매장명"]));
    }else  if(array_intersect($array,$key["편의점"]["주말운영여부"])){
        return implode(array_intersect($array,$key["편의점"]["주말운영여부"]));
    }else{
        return "키워드 없음";
      }
      return implode(array_intersect($array,$key["편의점"]));


  }else if(strcmp($table,"먹거리")==0){
      return implode(array_intersect($array,$key["먹거리"]));
  }
  else{
    return "키워드 없음";
    }

}

function buttonnum($token){

  if(strpos($token,"강의실")!==false){
    //빈강의실6
    $num ="11";
  }else if(strpos($token,"이파이")!==false){
    //와이파이1
    $num ="41";
  }else if(strpos($token,"프린트")!==false){
    //무인프린트1
    $num ="4221";
  }else if(strpos($token,"휴업체")!==false){
    //제휴업체
    $num ="431";
  }else if(strpos($token,"지시설")!==false){
    //복지시설1
    $num ="4321";
  }else if(strpos($token,"휴게실")!==false){
    //여학생휴게실1
    $num ="4331";
  }else if(strpos($token,"의점")!==false){
    //편의점1
    $num ="441";
  }else if(strpos($token,"식점")!==false){
    //음식점1
    $num ="442";
  }else if(strpos($token,"페")!==false){
    //카페1
    $num ="443";
  }else if(strpos($token,"판기")!==false){
    //자판기1
    $num ="444";
  }else if(strpos($token,"이문화")!==false){
    //놀이문화센터1
    $num ="45";
  }else if(strpos($token,"간표")!==false){
    //???????????시간표
    $num ="21";
  }else if(strpos($token,"사점")!==false){
    //?????????????복사점
    $num ="421";
  }else if(strcmp($token,"인사")==0){
    $num = "7778";
  }else if(strcmp($token,"기타")){
    $num="7777";
  }else{
    $num="7779";
  }


  return $num;

}


function buttontable($token){
  if(strpos($token,"/")!=false){
    $token = explode("/",$token);
    $parameter = $token[1];
    $token = $token[0];
  }


  if(strpos($token,"강의실")!==false){
    //빈강의실6
  }else if(strpos($token,"이파이")!==false){
    //와이파이1
    include("wifi.php");
  }else if(strpos($token,"프린트")!==false){
    //무인프린트1
    $_POST['code']=$parameter;
    include("print.php");
  }else if(strpos($token,"휴업체")!==false){
    //제휴업체
    include("affiliate.php");
  }else if(strpos($token,"지시설")!==false){
    //복지시설1
    include("welfare_dropand.php");
  }else if(strpos($token,"휴게실")!==false){
    //여학생휴게실1
        include("restroom.php");
  }else if(strpos($token,"의점")!==false){
    //편의점1
        include("convinience.php");
  }else if(strpos($token,"식점")!==false){
    //음식점1
    include("cafeteria.php");
  }else if(strpos($token,"페")!==false){
    //카페1
    include("cafe.php");
  }else if(strpos($token,"판기")!==false){
    //자판기1
    include("vendingmachine.php");
  }else if(strpos($token,"이문화")!==false){
    //놀이문화센터1
    include("play.php");
  }else if(strpos($token,"간표")!==false){
    //???????????시간표
    include("timetable.php");
  }else if(strpos($token,"사점")!==false){
    //?????????????복사점
    include("copystore.php");
  }

}





?>
