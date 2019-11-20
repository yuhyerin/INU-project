<?php

// * 한글 글자가 종성을 갖고 있는지 판단해 주는 함수.
// * @param $chr
// * @return bool
// */
function have_jongsung ($chr) {
	 static $no_jongsung = "가갸거겨고교구규그기개걔게계과괘궈궤괴귀긔까꺄꺼껴꼬꾜꾸뀨끄끼깨꺠께꼐꽈꽤꿔꿰꾀뀌끠나냐너녀노뇨누뉴느니내냬네녜놔놰눠눼뇌뉘늬다댜더뎌도됴두듀드디대댸데뎨돠돼둬뒈되뒤듸따땨떠뗘또뚀뚜뜌뜨띠때떄떼뗴똬뙈뚸뛔뙤뛰띄라랴러려로료루류르리래럐레례롸뢔뤄뤠뢰뤼릐마먀머며모묘무뮤므미매먜메몌뫄뫠뭐뭬뫼뮈믜바뱌버벼보뵤부뷰브비배뱨베볘봐봬붜붸뵈뷔븨빠뺘뻐뼈뽀뾰뿌쀼쁘삐빼뺴뻬뼤뽜뽸뿨쀄뾔쀠쁴사샤서셔소쇼수슈스시새섀세셰솨쇄숴쉐쇠쉬싀싸쌰써쎠쏘쑈쑤쓔쓰씨쌔썌쎄쎼쏴쐐쒀쒜쐬쒸씌아야어여오요우유으이애얘에예와왜워웨외위의자쟈저져조죠주쥬즈지재쟤제졔좌좨줘줴죄쥐즤짜쨔쩌쪄쪼쬬쭈쮸쯔찌째쨰쩨쪠쫘쫴쭤쮀쬐쮜쯰차챠처쳐초쵸추츄츠치채챼체쳬촤쵀춰췌최취츼카캬커켜코쿄쿠큐크키캐컈케켸콰쾌쿼퀘쾨퀴킈타탸터텨토툐투튜트티태턔테톄톼퇘퉈퉤퇴튀틔파퍄퍼펴포표푸퓨프피패퍠페폐퐈퐤풔풰푀퓌픠하햐허혀호효후휴흐히해햬헤혜화홰훠훼회휘희2459";
	 return mb_strpos($no_jongsung, $chr) === false ? true : false;
}

// /**
// * 단어의 종성 여부에 따라 조사를 선택해 주는 함수.
// * syntax) select_marker(단어, 종성이_있는_경우_붙일_조사, 종성이_없는_경우_붙일_조사)
// * ex) select_marker('선생님', '이', '가');
// * ex) select_marker('선생님', '은', '는');
// * @param $word
// * @param $have_jongsung
// * @param $no_jongsung
// * @return string
// */
function select_marker ($word, $have_jongsung, $no_jongsung) {
	if(mb_strpos($word,"(")!==false)
		$last_chr = mb_substr($word,mb_strpos($word,"(")-1,1);
	else
	 	$last_chr = mb_substr($word, -1, 1);

	 return have_jongsung($last_chr) ?
			 $word.$have_jongsung :
			 $word.$no_jongsung;
}
// $st = "asdflj(salfjd)";
// echo strpos($st,"(");
$marxism_title = '빈강의실(게임)';
echo select_marker($marxism_title, '은', '는') . ' 정부나 기업의 후원을 일체 받지 않고, 참가자들의 참가비와 진보 사회 단체의 후원만으로 운영합니다.';


?>
