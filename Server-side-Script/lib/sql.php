<?php
  require_once("subfunction.php");

  function all_select($ret){
    $arrays = array();

//    echo "<br>",var_dump($row),count($row),"<br>","<br>";
    while($row = mysqli_fetch_array($ret)){
      $array = array();
      for($i = 0;$i <= (count($row)/2)-1;$i++){
        array_push($array,$row[$i]);
        }
        array_push($arrays,$array);
    }
    return $arrays;
  }




  function room1_sql($time,$table,$building){
      //$string = "1_시_에_빈강의실_어디_야_"; //(빈강의실,오늘,시)
      // echo print_r($time),$table,$building;



    $string = "SELECT `강의실`,min(`시작시간`),`종료시간`,`호관`,`요일`
           FROM
           (Select `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from
            (select `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from `".$table."`
                where `요일`=
           ( select case WEEKDAY(curdate()+0)
               when '0' then '월'
               when '1' then '화'
               when '2' then '수'
               when '3' then '목'
               when '4' then '금'
               when '5' then '토'
               when '6' then '일'
               end as dayofweek ) AND `호관` = '".$building."') as TABLE_A
           WHERE `강의실` not in
            (SELECT `강의실` from (SELECT `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from `".$table."` where `요일`=
           ( select case WEEKDAY(curdate()+0)
               when '0' then '월'
               when '1' then '화'
               when '2' then '수'
               when '3' then '목'
               when '4' then '금'
               when '5' then '토'
               when '6' then '일'
               end as dayofweek ) AND `호관` = '".$building."') as TABLE_B
                WHERE (`시작시간` >= '".$time[0]."' AND `시작시간` <= (select addtime('".$time[0]."','00:30:0000')))
                  OR (`종료시간` > '".$time[0]."' AND `종료시간` <  (select addtime('".$time[0]."','00:30:0000')))
               OR (`시작시간` < (select addtime('".$time[0]."','00:30:0000'))))
           ORDER BY `TABLE_A`.`강의실` ASC)AS TABLE_C
           GROUP BY `강의실`";
           return $string;
  }

    function room2_sql($time,$table,$building){
        //$string = "3_시_에서_4_시_까지_빈_강의실_있어_"; //(빈강의실,오늘,시시)

      $string ="select DISTINCT `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from
              (select `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from `".$table."`
                 where `요일`=
            ( select case WEEKDAY(curdate()+0)
                when '0' then '월'
                when '1' then '화'
                when '2' then '수'
                when '3' then '목'
                when '4' then '금'
                when '5' then '토'
                when '6' then '일'
                end as dayofweek ) AND `호관` = '".$building."') as TABLE_A
            WHERE `강의실` not in
              (SELECT `강의실` from (SELECT `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from `".$table."` where `요일`=
            ( select case WEEKDAY(curdate()+0)
                when '0' then '월'
                when '1' then '화'
                when '2' then '수'
                when '3' then '목'
                when '4' then '금'
                when '5' then '토'
                when '6' then '일'
                end as dayofweek ) AND `호관` = '".$building."') as TABLE_B
                 WHERE (`시작시간` > '".$time[0]."' AND `시작시간` < '".$time[1]."')
                  OR (`종료시간` > '".$time[0]."' AND `종료시간` < '".$time[1]."')
                  OR (`종료시간` > '".$time[1]."' AND `시작시간` < '".$time[0]."'))
            ORDER BY `TABLE_A`.`시작시간`  ASC";



      return $string;
    }

    function room3_sql($place,$table,$building){
        // $string = "505_호_빈_시간_언제_야_"; // (빈강의실,오늘)////////////////////
      $string ="SELECT `시작시간`,`종료시간`,`강의실`
             FROM `".$table."`
             WHERE `요일` LIKE ( select case WEEKDAY(curdate()+0)
                 when '0' then '월'
                 when '1' then '화'
                 when '2' then '수'
                 when '3' then '목'
                 when '4' then '금'
                 when '5' then '토'
                 when '6' then '일'
                 end as dayofweek )
                 AND `강의실` LIKE '".$place."' AND `호관`='".$building."'
             ORDER BY `".$table."`.`시작시간`  ASC";


      return $string;
    }

    function room4_sql($time,$day,$table,$building){
      //$string = "수요일_1_시_에_빈강의실_어디_야_";
      $string =  "SELECT `강의실`,min(`시작시간`),`종료시간`,`호관`,`요일`
              FROM
              (Select `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from
              	(select `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from `".$table."`
                   where `요일`='".$day."' AND `호관` = '".$building."') as TABLE_A
              WHERE `강의실` not in
              	(SELECT `강의실` from (SELECT `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from `".$table."` where `요일`= '".$day."'
              AND `호관` = '".$building."') as TABLE_B
                   WHERE (`시작시간` >= '".$time[0]."' AND `시작시간` <= (select addtime('".$time[0]."','0:30:0000')))
                   	OR (`종료시간` > '".$time[0]."' AND `종료시간` < (select addtime('".$time[0]."','0:30:0000')))
                  OR (`시작시간` < (select addtime('".$time[0]."','0:30:0000'))))
              ORDER BY `TABLE_A`.`강의실` ASC)AS TABLE_C
              GROUP BY `강의실`";



      return $string;
    }

    function room5_sql($time,$day,$table,$building){
        //$string = "월요일_3_시_에서_4_시_까지_빈강의실_있어"; // (빈강의실,요일,시시)
      $string = "select DISTINCT `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from
              (select `강의실`, `시작시간`,`종료시간`,`호관`,`요일` from `".$table."` WHERE `요일` LIKE '".$day."' AND `호관` = '".$building."') as TABLE_A
              WHERE `강의실` not in (SELECT `강의실` from (SELECT `강의실`, `시작시간`,`종료시간`,`호관`,`요일`
              from `".$table."` WHERE `요일` LIKE '".$day."' AND `호관` = '".$building."') as TABLE_B
              WHERE (`시작시간` > '".$time[0]."' AND `시작시간` < '".$time[1]."')
              OR (`종료시간` > '".$time[0]."' AND `종료시간` < '".$time[1]."')
              OR (`종료시간` > '".$time[1]."' AND `시작시간` < '".$time[0]."'))
              ORDER BY `TABLE_A`.`시작시간`  ASC";

      return $string;
    }

    function room6_sql($place,$day,$table,$building){
        //$string = "수요일_505_호_빈_시간_언제_있어?";
      $string ="SELECT `시작시간`,`종료시간`,`강의실` FROM
              `".$table."` WHERE `강의실` LIKE '".$place."' AND `호관`='".$building."' AND `요일` LIKE '".$day."'
              ORDER BY `".$table."`.`시작시간`  ASC";

      return $string;
    }

    function timetable_sql($day,$class){

      if(strcmp($day,"오늘")==0){

        $string = "SELECT
          `교과목명`,
          `담당교수명`,
          `시작교시`,
          `종료교시`,
          `요일`
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

        $string = "SELECT `교과목명`,`담당교수명`,`시작교시`,`종료교시`,`요일` FROM `v_emptyLectureroom` where `요일`='".$day."' && `강의실` ='".$class."' ORDER BY `시작시간`";
      }
      echo buttonnum("시간표");
      echo "%";
      $_POST['day']=$day; $_POST['class']=$class;
      include("threetolec.php");

      return $string;

    }

function copystore_sql(){
    $string ="SELECT * FROM `복지시설` WHERE `복지시설` LIKE '복사점'";

    echo buttonnum("복사점");
    echo "%";
    include("copystore.php");

    return $string;
}


function wifi_sql($key){
  //$string = "교내_와이파이_비밀번호_알려줘";
  if(strcmp($key,"유플러스")==0){
    $key = "U+village";
  }else if(strcmp($key,"inuyrs")==0){
    $key = "inu-wireless";
  }else if(strcmp($key,"에듀로")==0){
    $key = "eduroam";
  }

  $string = "SELECT * FROM `와이파이` WHERE `wi-fi` LIKE '".$key."'";

  return $string;
}



function play0_sql(){
  //$string = "교내_놀거리_알려줘";
  $string ="SELECT * FROM `놀이문화센터` WHERE `학적` LIKE '%재학생%'";

  return $string;
}

function play1_sql($key){
  $string = "SELECT `놀거리`,`시작시간`,`종료시간` FROM `놀이문화센터` WHERE `놀거리` LIKE '%".$key."%' AND `학적` LIKE '%재학생%'";

  return $string;
}

function play2_sql($key){
  $string = "SELECT `놀거리`,`위치` FROM `놀이문화센터` WHERE `놀거리` LIKE '%".$key."%' AND `학적` LIKE '%재학생%'";

  return $string;
}



function print0_sql(){
  //$string = "교내_프린트_하는_곳_알려줘";
  $string = "SELECT * FROM `무인프린트`";
  return $string;
}

function print1_sql($key){
  //$string="6호관_프린트_하는_곳_알려줘";
  $string = "SELECT * FROM `무인프린트` WHERE `호관` = '".$key."' ORDER BY `호관`";
  return $string;
}



function print2_sql($key){
  //$string = "도서관_프린트_하는_곳_알려줘";
  $string ="SELECT * FROM `무인프린트` WHERE `건물` LIKE '%".$key."%' ORDER BY `호관`";

  return $string;
}


function cafeteria0_sql(){
  // Q. 배고파_
  // Q. 먹을_데_알려_줘

  $string = "SELECT DISTINCT
    `매장명`,
    `건물`,
    `호`
FROM
    `음식점`
WHERE
    DAYOFWEEK(CURRENT_DATE) NOT IN(1, 7) AND IF(
        DAYOFWEEK(CURRENT_DATE) = 1 OR DAYOFWEEK(CURRENT_DATE) = 7,
        CURRENT_TIME BETWEEN `주말시작시간` AND `주말종료시간`,
        CURRENT_TIME BETWEEN `평일시작시간` AND `평일종료시간`
    )";
  return $string;
}




function cafeteria1_sql($key){
  // 쌀국수_집_어디_있어?
  if(strcmp($key,"학식")==0)$key="학생식당";
  if(strcmp($key,"기식")==0)$key="생활원식당";
  if(strcmp($key,"고집")==0)$key="고기굽는집";
  $string = "SELECT DISTINCT
    `매장명`,`호관`,`건물`,`호`
FROM
    `음식점`
WHERE
    `매장명` LIKE '%".$key."%'
";
return $string;

}

function cafeteria2_sql($key){
      // Q. 토마토_도시락_언제_까지_해?
      if(strcmp($key,"학식")==0)$key="학생식당";
      if(strcmp($key,"기식")==0)$key="생활원식당";
      if(strcmp($key,"고집")==0)$key="고기굽는집";
  $string = "SELECT
    `매장명`,`평일운영여부`,`평일시작시간`,`평일종료시간`
FROM
    `음식점`
WHERE
    `매장명` LIKE '%".$key."%'";
return $string;

}

function cafeteria3_sql($key){
  //$string = "주말에도 여는 식당 알려줘";
  if(strcmp($key,"주말")==0){
    $string = "SELECT DISTINCT `매장명`,`호관`,`호` FROM `음식점` WHERE `주말운영여부` NOT LIKE '휴점'";
  }else{
  $string = "SELECT DISTINCT `매장명`,`호관`,`호` FROM `음식점` WHERE `주말운영여부` NOT LIKE '".$key."'";
}
  return $string;
}


function cafeteria4_sql($key){
  // 쌀국수_집_어디_있어?
  $string = "SELECT
    *
FROM
    `음식점`
WHERE
    `분류` LIKE '휴게음식'
";
return $string;

}

function cafeteria5_sql($key){
  // 짜장면_먹고_싶다
  $string = "SELECT DISTINCT `주메뉴`,`건물`,`호`,`매장명`  FROM `음식점` WHERE `주메뉴` LIKE '%".$key."%'";
return $string;

}




function restroom1_sql(){
  //$string = "여휴_위치_알려줘";
  $string=" SELECT * FROM `여학생휴게실`";

  return $string;
}

function restroom2_sql($keys){
  //$string="수면실_있는_여휴_알려줘"; or "수면실 샤워실",
  $string = "SELECT * FROM `여학생휴게실` WHERE `내부시설` LIKE '%".$keys[0]."%' AND `내부시설` LIKE '%".$keys[1]."%' AND `내부시설` LIKE '%".$keys[2]."%'";

  return $string;
}

function vendingmachine1_sql(){
  //string="자판기_위치_알려줘";
  $string = "SELECT * FROM `자판기`";
  return $string;
}

function vendingmachine2_sql($key){
  //string = "커피(과자,음료)_자판기_위치_알려줘";
  $string = "SELECT * FROM `자판기` WHERE `".$key."` LIKE 'o'";
  return $string;
}

function affiliate_sql(){
  //$string = "제휴업체 알려줘";
  $string = "SELECT * FROM `제휴업체`";

  return $string;
}




function parking_sql(){
  //$string = ;

  return $string;
}

function cafe0_sql($key){
  // 문의하신 카페는~~
  $string = "SELECT * from  `카페`";
  return $string;
}

function cafe1_sql(){
  // Q. 교내_카페_알려줘
  // Q. 카페_뭐뭐_있어?
  $string = "SELECT
    `매장명`,
    `호관`,
    `건물`,
    `호`,
    `평일종료시간`
FROM
    `카페`
WHERE
    DAYOFWEEK(CURRENT_DATE) NOT IN(1, 7) AND IF(
        DAYOFWEEK(CURRENT_DATE) = 1 OR DAYOFWEEK(CURRENT_DATE) = 7,
        CURRENT_TIME BETWEEN `주말시작시간` AND `주말종료시간`,
        CURRENT_TIME BETWEEN `평일시작시간` AND `평일종료시간`
    )";
  return $string;
}

function cafe2_sql($key){
  // Q. 팔공티_어딨어?
  // Q. 쥬씨_어디_에_있어?
  if(strcmp($key,"그라지에")==0)$key="그라찌에";
  $string = "SELECT
    `매장명`,`호관`,`건물`,`호`
FROM
    `카페`
WHERE
    `매장명` LIKE '%".$key."%'";
  return $string;
}



function cafe3_sql($key){
  // Q. 카페_드림_도서관점_운영_시간_알려_줘
  // Q. 샐러디_언제까지_해?
  if(strcmp($key,"그라지에")==0)$key="그라찌에";
  $string = "SELECT
    `매장명`,`평일시작시간`,`평일종료시간`
FROM
    `카페`
WHERE
    `매장명` LIKE '%".$key."%'";
  return $string;
}

function cafe4_sql($key){
  //$string = "토요일_에도_여는_카페_는?";
  // Q. 주말_에_도_여는_카페_알려_줘

  if((strcmp($key,"토요일")==0)||(strcmp($key,"일요일")==0)){
  $string = "SELECT * FROM `카페` WHERE `주말운영여부` LIKE '%".$key."%'";
}else{
  $string = "SELECT * FROM `카페` WHERE `주말운영여부` NOT LIKE '휴점'";
}
  return $string;
}

function food1_sql(){

  //$string = "교내_먹거리_알려줘";
  $string = "SELECT * FROM `먹거리`";


  return $string;
}

function food2_sql($key){
//$string = "토요일(일요일)에_먹을_수_있는_데_알려줘";
$string = "SELECT * FROM `먹거리` WHERE `".$key."` LIKE 'o'";
return $string;
}
function convinience0_sql(){
        // Q. gs_편의점_자연대점_어디_있어?
  $string = "SELECT * FROM `편의점`";
  return $string;
}

function convinience1_sql($key){
        // Q. gs_편의점_자연대점_어디_있어?
        if(strcmp($key,"지에스")==0||strcmp($key,"지에스편의점")==0)$key="GS";
        if(strcmp($key,"씨유")==0||strcmp($key,"씨유편의점")==0)$key="CU";
  $string = "SELECT `매장명`, `건물`,`호관`,`호` FROM `편의점` WHERE `매장명` LIKE '%".$key."%'";
  return $string;
}

function convinience2_sql($key){
  // Q. 도서관_편의점_운영_시간_알려_줘
  // Q. 기념품_샵_언제_까지_해?
  if(strcmp($key,"지에스")==0||strcmp($key,"지에스편의점")==0)$key="GS";
  if(strcmp($key,"씨유")==0||strcmp($key,"씨유편의점")==0)$key="CU";
    $string = "SELECT `매장명`, `평일시작시간`,`평일종료시간` FROM `편의점` WHERE `매장명` LIKE '%".$key."%'";
  return $string;
}

function convinience3_sql(){
        //Q. 지금(저녁)_도_이용_가능_한_편의점_있어?
    $string = "SELECT
    `매장명`,
    `호관`,
    `건물`,
    `호`,
    `평일종료시간`
FROM
    `편의점`
WHERE
    DAYOFWEEK(CURRENT_DATE) NOT IN(1, 7) AND IF(
        DAYOFWEEK(CURRENT_DATE) = 1 OR DAYOFWEEK(CURRENT_DATE) = 7,
        CURRENT_TIME BETWEEN `주말시작시간` AND `주말종료시간`,
        CURRENT_TIME BETWEEN `평일시작시간` AND `평일종료시간`
    )";


  return $string;
}

function convinience4_sql(){
        //Q. 주말_에_도_여는_편의점_있어?
  $string = "SELECT * FROM `편의점` WHERE `주말운영여부` NOT LIKE '휴점'";
  return $string;
}

function welfare1_sql($key){
  // Q. 안경점_운영_시간_알려_줘
  // Q. 문방구_언제_까지_해?
  // $string = "SELECT * FROM `복지시설` WHERE `복지시설` LIKE '%".$key."%'";
  $string = "SELECT
    `복지시설`,`평일시작시간`,`평일종료시간`,`주말운영여부`
FROM
    `복지시설`
WHERE
    `복지시설` LIKE '%".$key."%'";


  return $string;
}

function welfare2_sql($key){
  //string="11호관 복지회관에 뭐뭐 있어?";
  echo $key;
  if(strcmp("복지",$key)==0){
    $string ="SELECT * FROM `복지시설` WHERE `건물` NOT LIKE '%".$key."%'";
  }else if(is_numeric($key)){
    $string = "SELECT * FROM `복지시설` WHERE `호관`='".$key."'";
  }else
      $string = "SELECT * FROM `복지시설` WHERE `건물` LIKE '%복지%'";
  return $string;
}

function welfare3_sql($key){

  $string = "SELECT
    *
FROM
    `복지시설`
WHERE
    `복지시설` LIKE '%".$key."%'
";
  return $string;
}




function welfare0_sql(){
  //$string = "복지_시설 알려줘";
  $string = "SELECT * FROM `복지시설`";

  return $string;
}

 ?>
