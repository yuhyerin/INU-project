using System;
using System.Collections;
using System.Collections.Generic;
using FrostweepGames.Plugins.GoogleCloud.SpeechRecognition;
using UnityEngine;
using UnityEngine.AI;
using UnityEngine.Networking;
using UnityEngine.UI;

namespace FrostweepGames.Plugins.GoogleCloud
{
    public class GoogleML : MonoBehaviour
    {
        // 0: RELEASE MODE, 1: FULL DEBUG MODE, 2: TOKEN DEBUG MODE
        static int DEBUG = 1;

        // Instance for Speech Recognition
        private FrostweepGames.Plugins.GoogleCloud.SpeechRecognition.GCSpeechRecognition _speechRecognition;
        // Instance for Natural Language Process
        private FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.GCNaturalLanguage _gcNaturalLanguage;
        // Instance for Text to Speech
        private FrostweepGames.Plugins.GoogleCloud.TextToSpeech.GCTextToSpeech _gcTextToSpeech;

        // Animator
        NavMeshAgent _agent;
        GameObject _unitychan;
        Animator _unityAnim;

        public Sprite[] img;

        private Camera[] _cam = new Camera[21];
        private GameObject[] _destination = new GameObject[15];
        private GameObject[] _building = new GameObject[15];

        private String _speechRecognitionResult;
        private String _naturalLanguageResult;
        private String _databaseResult;
        private String _textToSpeechString;

        private FrostweepGames.Plugins.GoogleCloud.TextToSpeech.Voice _currentVoice;

        public AudioSource audioSource;

        Button _startRecordButton;
        Image _speechRecognitionState;
        Toggle _isRuntimeDetectionToggle;
        InputField _contextPhrases;

        Button _btn1;
        Button _btn2;
        Button _btn3;
        Button _btn3_1;
        Button _btn3_2;
        Button _btn3_3;
        Button _btn4;
        Button _btn4_1;
        Button _btn4_2;
        Button _btn4_2_1;
        Button _btn4_2_2;
        Button _btn4_3;
        Button _btn4_3_1;
        Button _btn4_3_2;
        Button _btn4_3_3;
        Button _btn4_4;
        Button _btn4_4_1;
        Button _btn4_4_2;
        Button _btn4_4_3;
        Button _btn4_4_4;
        Button _btn4_5;
        Button _returnButton;
        Button _submitButton;

        Dropdown _dayDropdown;
        Dropdown _startTimeDropdown;
        Dropdown _endTimeDropdown;
        Dropdown _buildNoDropdown;
        Dropdown _WelfareDropdown;
        Dropdown _classroomDropdown;

        Text _dayText;
        Text _startTimeText;
        Text _endTimeText;
        Text _buildNoText;
        Text _WelfareText;
        Text _classroomText;

        Image _restToggle1;
        Image _restToggle2;
        Image _restToggle3;

        Image _INUTextBox;

        GameObject _panel;

        RawImage _minimapRawImage;

        private UnityEngine.Object[] particleEffect;
        private UnityEngine.Object floorInstance;

        private int _selectedMenu;
        private int target;
        private bool reached, unityTurn, showEffect;
        private int floor_count;
        private int pathCounter;

        /*===================================== START METHOD =====================================*/
        /*--------------------------------------- Default ----------------------------------------*/
        void Start()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Start");
            }
            //Initialize Camera
            for (int i = 0; i < Enum.GetNames(typeof(InuBooster.Enumerators.CameraName)).Length; i++)
            {
                _cam[i] = GameObject.Find(Enum.GetNames(typeof(InuBooster.Enumerators.CameraName))[i]).GetComponent<Camera>();
            }
            //Initialize Destination Object
            for (int i = 0; i < Enum.GetNames(typeof(InuBooster.Enumerators.Destination)).Length; i++)
            {
                _destination[i] = GameObject.Find(Enum.GetNames(typeof(InuBooster.Enumerators.Destination))[i]);
                _destination[i].transform.GetChild(0).gameObject.SetActive(false);
            }
            //Initialize Building Model Object
            for (int i = 0; i < 15; i++)
            {
                _building[i] = GameObject.Find(Enum.GetNames(typeof(InuBooster.Enumerators.BuildingName))[i]);
            }

            //Initalize Navigation Mesh Agent
            _unitychan = GameObject.Find("INU_Chan");
            _unityAnim = GameObject.Find("unitychan").GetComponent<Animator>();
            _agent = GameObject.Find("INU_Chan").GetComponent<NavMeshAgent>();

            target = 3;
            _agent.destination = _destination[target].transform.position;
            //_agent.stoppingDistance = 3;
            //Initial Camera Setting
            DisableAllCam();
            _cam[2].enabled = true;

            //Initialize User Interface
            UI_Initializer();

            //Initialize STT
            //Get Speech Recognition Instance from method
            _speechRecognition = FrostweepGames.Plugins.GoogleCloud.SpeechRecognition.GCSpeechRecognition.Instance;
            //Set Event Handler for STT
            _speechRecognition.RecognitionSuccessEvent += RecognitionSuccessEventHandler;
            _speechRecognition.NetworkRequestFailedEvent += SpeechRecognizedFailedEventHandler;
            _speechRecognition.LongRecognitionSuccessEvent += LongRecognitionSuccessEventHandler;

            //Initialize NLP
            //Get Natural Language Instance from method
            _gcNaturalLanguage = FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.GCNaturalLanguage.Instance;
            //Set Event Handler for NLP
            _gcNaturalLanguage.AnalyzeSyntaxSuccessEvent += _gcNaturalLanguage_AnalyzeSyntaxSuccessEvent;
            _gcNaturalLanguage.AnalyzeSyntaxFailedEvent += _gcNaturalLanguage_AnalyzeSyntaxFailedEvent;

            //Initialize TTS
            //Get Text to Speech Instance from method
            _gcTextToSpeech = FrostweepGames.Plugins.GoogleCloud.TextToSpeech.GCTextToSpeech.Instance;
            //Set Event Handler for TTS
            _gcTextToSpeech.SynthesizeSuccessEvent += _gcTextToSpeech_SynthesizeSuccessEvent;
            _gcTextToSpeech.SynthesizeFailedEvent += _gcTextToSpeech_SynthesizeFailedEvent;

            _speechRecognitionState.color = Color.white;
            _startRecordButton.interactable = true;

            pathCounter = 0;
            // Set Default LanguageCode: Korean
            _speechRecognition.SetLanguage(Enumerators.LanguageCode.ko_KR);
            // Set Default Voice: Korean
            _gcTextToSpeech.GetVoices(new FrostweepGames.Plugins.GoogleCloud.TextToSpeech.GetVoicesRequest()
            {
                languageCode = _gcTextToSpeech.PrepareLanguage((FrostweepGames.Plugins.GoogleCloud.TextToSpeech.Enumerators.LanguageCode)9)
            });
        }
        private void OnDestroy()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - OnDestroy");
            }
            _speechRecognition.RecognitionSuccessEvent -= RecognitionSuccessEventHandler;
            _speechRecognition.NetworkRequestFailedEvent -= SpeechRecognizedFailedEventHandler;
            _speechRecognition.LongRecognitionSuccessEvent -= LongRecognitionSuccessEventHandler;

            _gcNaturalLanguage.AnalyzeSyntaxSuccessEvent -= _gcNaturalLanguage_AnalyzeSyntaxSuccessEvent;
            _gcNaturalLanguage.AnalyzeSyntaxFailedEvent -= _gcNaturalLanguage_AnalyzeSyntaxFailedEvent;

            _gcTextToSpeech.SynthesizeSuccessEvent -= _gcTextToSpeech_SynthesizeSuccessEvent;
            _gcTextToSpeech.SynthesizeFailedEvent -= _gcTextToSpeech_SynthesizeFailedEvent;
        }
        void Update()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Update");
            }
            Debug.Log("_selectedMenu: " + _selectedMenu);
            Debug.Log("target: " + target);
            Debug.Log("_contextPhrases.text: " + _contextPhrases.text);
            if (_selectedMenu == 1000)
            {
                if (Input.GetMouseButtonDown(0))
                { // if left button pressed...
                    Ray ray = _cam[target + 6].ScreenPointToRay(Input.mousePosition);
                    RaycastHit hit;
                    if (Physics.Raycast(ray, out hit))
                    {
                        if (hit.transform.tag == "Floor")
                        {
                            _selectedMenu = 1001;
                            floorInstance = Instantiate(hit.transform.gameObject, GameObject.Find("Land").transform.position, hit.transform.rotation, GameObject.Find("Sex").transform);
                            DisableAllCam();
                            _cam[5].enabled = true;

                            HideAllUI();
                            _returnButton.gameObject.SetActive(true);

                        }
                    }
                }
            }
            _contextPhrases.text =
                "reached: " + reached + "\n" +
                "_agent.remainingDistance: " + _agent.remainingDistance + "\n " +
                "_agent.stoppingDistance: " + _agent.stoppingDistance + "\n" +
                "unityTurn: " + unityTurn;
            if (!reached)
            {
                if (target > 0)
                {
                    if (!_agent.pathPending)
                    {
                        if (_agent.remainingDistance <= _agent.stoppingDistance + 10)
                        {
                            if (!_agent.hasPath || _agent.velocity.sqrMagnitude == 0f)
                            {
                                unityTurn = true;
                                reached = true;
                            }
                        }
                    }
                }
            }

            if (showEffect)
            {
                MakeTransparent(target); // 투명하게
                showEffect = false;
            }
            if (reached)
            {
                if (unityTurn)
                {
                    Vector3 vec = _building[target].transform.position - _unitychan.transform.position;
                    vec.Normalize();
                    _unitychan.transform.rotation = Quaternion.Lerp(_unitychan.transform.rotation, Quaternion.LookRotation(vec), Time.deltaTime * 1);
                    StartCoroutine(WaitForIt());
                }
            }
            _unityAnim.SetFloat("Speed", _agent.velocity.magnitude);
        }
        /*----------------------------------------- STT ------------------------------------------*/
        private void ApplySpeechContextPhrases()
        {
            string[] phrases = _contextPhrases.text.Trim().Split(","[0]);

            if (phrases.Length > 0)
                _speechRecognition.SetContext(new List<string[]>() { phrases });
        }
        private void RecognitionSuccessEventHandler(RecognitionResponse obj, long requestIndex)
        {
            if (!_isRuntimeDetectionToggle.isOn)
            {
                _startRecordButton.interactable = true;
                _speechRecognitionState.color = Color.green;
                _startRecordButton.image.overrideSprite = img[3];
            }

            if (obj != null && obj.results.Length > 0)
            {
                _speechRecognitionResult = obj.results[0].alternatives[0].transcript;
                _contextPhrases.text = "음성인식 결과: " + _speechRecognitionResult;

                var words = obj.results[0].alternatives[0].words;

                if (words != null)
                {
                    string times = string.Empty;

                    foreach (var item in obj.results[0].alternatives[0].words)
                        times += "<color=green>" + item.word + "</color> -  start: " + item.startTime + "; end: " + item.endTime + "\n";

                    _speechRecognitionResult += "\n" + times;
                }

                string other = "";

                foreach (var result in obj.results)
                {
                    foreach (var alternative in result.alternatives)
                    {
                        if (obj.results[0].alternatives[0] != alternative)
                            other += "_" + alternative.transcript;
                    }
                }

                _speechRecognitionResult += other;
                AnalyzeSyntax(_speechRecognitionResult.Split('_')[0], FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.Enumerators.Language.ko);
            }
            else
            {
                //음성 인식 실패시 바로 음성으로 출력
                _contextPhrases.text = "음성인식 실패!";
                _textToSpeechString = "뭐라구 하는지 모르게써여";
                Synthesize();
            }
        }
        private void LongRecognitionSuccessEventHandler(OperationResponse operation, long index)
        {
            if (!_isRuntimeDetectionToggle.isOn)
            {
                _startRecordButton.interactable = true;
                _speechRecognitionState.color = Color.green;
                _startRecordButton.image.overrideSprite = img[3];
            }

            if (operation != null && operation.response.results.Length > 0)
            {
                _speechRecognitionResult = operation.response.results[0].alternatives[0].transcript;
                _contextPhrases.text = "음성인식 결과: " + _speechRecognitionResult;

                string other = "";

                foreach (var result in operation.response.results)
                {
                    foreach (var alternative in result.alternatives)
                    {
                        if (operation.response.results[0].alternatives[0] != alternative)
                            other += "_" + alternative.transcript;
                    }
                }

                _speechRecognitionResult += other;
                _speechRecognitionResult += "_" +
                    (operation.metadata.lastUpdateTime - operation.metadata.startTime).TotalSeconds + "s";

                AnalyzeSyntax(_speechRecognitionResult.Split('_')[0], FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.Enumerators.Language.ko);
            }
            else
            {
                // 음성 인식 실패시 바로 음성으로 출력
                _contextPhrases.text = "음성인식 실패!";
                _textToSpeechString = "뭐라구 하는지 모르게써여";
                Synthesize();
            }
        }

        private void SpeechRecognizedFailedEventHandler(string obj, long requestIndex)
        {
            //_speechRecognitionResult = "Speech Recognition failed with error: " + obj;
            _speechRecognitionResult = "%Error%";
            _contextPhrases.text = "음성인식 실패!";
            _textToSpeechString = "호에에엥 미안해요 뭐라구 하는지 모르게써여";
            _unityAnim.SetBool("Sad", true);
            StartCoroutine(SadToWait());
            StartCoroutine(DisableTextBox());
            if (!_isRuntimeDetectionToggle.isOn)
            {
                _speechRecognitionState.color = Color.green;
                _startRecordButton.image.overrideSprite = img[3];
                _startRecordButton.interactable = true;
            }
            // 음성 인식 결과를 로그로 출력
            _startRecordButton.image.overrideSprite = img[0];
            Synthesize();
        }
        /*----------------------------------------- NLP ------------------------------------------*/
        private void AnalyzeSyntax(string text, FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.Enumerators.Language lang)
        {
            _gcNaturalLanguage.Annotate(new FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.AnalyzeSyntaxRequest()
            {
                encodingType = FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.Enumerators.EncodingType.UTF8,
                document = new FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.LocalDocument()
                {
                    content = text,
                    language = _gcNaturalLanguage.PrepareLanguage(lang),
                    type = FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.Enumerators.DocumentType.PLAIN_TEXT
                }
            });
        }
        private void _gcNaturalLanguage_AnalyzeSyntaxSuccessEvent(FrostweepGames.Plugins.GoogleCloud.NaturalLanguage.AnalyzeSyntaxResponse obj)
        {
            string result = string.Empty;
            _naturalLanguageResult = "";
            foreach (var item in obj.tokens)
            {
                _naturalLanguageResult += item.text.content + "_";
            }

            _contextPhrases.text = "자연어처리 결과: " + _naturalLanguageResult;
            CallSelectDB();
        }
        private void _gcNaturalLanguage_AnalyzeSyntaxFailedEvent(string obj)
        {
            _contextPhrases.text = "자연어를 처리하는 과정에서 오류가 발생했어요";
        }
        /*----------------------------------------- TTS ------------------------------------------*/
        private void Synthesize()
        {
            string content = _textToSpeechString;
            _contextPhrases.text = content;
            if (string.IsNullOrEmpty(content) || _currentVoice == null)
            {
                _currentVoice = new FrostweepGames.Plugins.GoogleCloud.TextToSpeech.Voice();

                _currentVoice.name = "ko-KR-Standard-A";
                _currentVoice.ssmlGender = (FrostweepGames.Plugins.GoogleCloud.TextToSpeech.Enumerators.SsmlVoiceGender)3;
                _currentVoice.naturalSampleRateHertz = 22050;
            }
            _gcTextToSpeech.Synthesize(content, new FrostweepGames.Plugins.GoogleCloud.TextToSpeech.VoiceConfig()
            {
                gender = (FrostweepGames.Plugins.GoogleCloud.TextToSpeech.Enumerators.SsmlVoiceGender)2,
                languageCode = "ko_KR",
                name = "ko-KR-Standard-A"
            },
            true, 1.0, 1.0, _currentVoice.naturalSampleRateHertz);
            _INUTextBox.gameObject.SetActive(true);
            Text _INUText = (Text)_INUTextBox.transform.GetChild(0).gameObject.GetComponent<Text>();
            _INUText.text = _textToSpeechString;
        }
        private void _gcTextToSpeech_SynthesizeSuccessEvent(FrostweepGames.Plugins.GoogleCloud.TextToSpeech.PostSynthesizeResponse response)
        {
            audioSource.clip = _gcTextToSpeech.GetAudioClipFromBase64(response.audioContent, FrostweepGames.Plugins.GoogleCloud.TextToSpeech.Constants.DEFAULT_AUDIO_ENCODING);
            audioSource.Play();
        }
        private void _gcTextToSpeech_SynthesizeFailedEvent(string error)
        {

        }
        /*-------------------------------------- Coroutine ---------------------------------------*/
        private void CallSelectDB()
        {
            StartCoroutine(SelectDB());
        }
        private void InitializeBuildingDropbox()
        {
            StartCoroutine(GetBuildingDropdownItem());
        }
        private void InitializeBuildingDropbox4Printer()
        {
            StartCoroutine(GetBuildingDropdownItem4Printer());
        }
        private void InitializeClassroomDropbox(string buildingCode)
        {
            StartCoroutine(GetClassroomDropdownItem(buildingCode));
        }
        private void StartFindEmptyRoom()
        {
            StartCoroutine(FindEmptyRoom());
        }
        private void StartFindClassRoom()
        {
            StartCoroutine(FindClassRoom());
        }
        private void InitializeWelfareDropbox()
        {
            StartCoroutine(GetWelfareDropwonItem());
        }
        /*-------------------------------------- DB Access ---------------------------------------*/
        IEnumerator SelectDB()
        {
            WWWForm form = new WWWForm();

            form.AddField("full_string", _speechRecognitionResult.Split('_')[0]);
            form.AddField("string", _naturalLanguageResult);

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_VOICE_PROCESSING, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();

            switch (_databaseResult.Split('%')[0].Trim())
            {
                case "11":    // 빈 강의실
                    _selectedMenu = 11;

                    DisableAllCam();
                    _cam[2].enabled = true;

                    GuideProcessing(_databaseResult);
                    showEffect = true;
                    break;
                case "21":
                    _selectedMenu = 21;

                    DisableAllCam();
                    _cam[2].enabled = true;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);
                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "41":    // 시간표
                    _selectedMenu = 41;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);
                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "421":   // 와이파이
                    _selectedMenu = 421;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "4221":  // 무인 프린터
                    _selectedMenu = 4221;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "431":   // 제휴업체
                    _selectedMenu = 431;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "4321":  // 복지시설
                    _selectedMenu = 4321;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "4331":  // 여휴
                    _selectedMenu = 4331;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "441":   // 편의점
                    _selectedMenu = 441;
                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "442":   // 음식점
                    _selectedMenu = 442;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "443":   // 카페
                    _selectedMenu = 443;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "444":   // 자판기
                    _selectedMenu = 444;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "45":    // 놀이시설
                    _selectedMenu = 45;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);

                    _textToSpeechString = _databaseResult.Split('%')[3];
                    CreatePanel(_databaseResult.Split('%')[1]);
                    Synthesize();
                    break;
                case "7777":
                    _textToSpeechString = _databaseResult.Split('%')[3];
                    Synthesize();
                    StartCoroutine(DisableTextBox());
                    break;
                case "7778":
                    _textToSpeechString = _databaseResult.Split('%')[3];
                    Synthesize();
                    _unityAnim.SetBool("TouchHead", true);
                    StartCoroutine(TouchHeadToWait());

                    StartCoroutine(DisableTextBox());
                    break;
                case "7779":
                    _textToSpeechString = _databaseResult.Split('%')[3];
                    Synthesize();
                    _unityAnim.SetBool("Sad", true);
                    StartCoroutine(SadToWait());

                    StartCoroutine(DisableTextBox());
                    break;
                default:
                    break;
            }
            _startRecordButton.image.overrideSprite = img[0];
        }
        private IEnumerator GetBuildingDropdownItem()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_BUILDING_LIST, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            string[] resultArray = _databaseResult.Split('^');

            _buildNoDropdown.ClearOptions();
            for (int i = 0; i < resultArray.Length; i++)
            {
                _buildNoDropdown.options.Add(new Dropdown.OptionData(resultArray[i]));
            }
            _buildNoDropdown.value = 3;
            _buildNoDropdown.GetComponentInChildren<Text>().text = resultArray[_buildNoDropdown.value];
        }
        private IEnumerator GetClassroomDropdownItem(string buildingCode)
        {
            WWWForm form = new WWWForm();
            form.AddField("building", buildingCode);
            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_CLASSROOM_LIST, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            string[] resultArray = _databaseResult.Split('*');

            _classroomDropdown.ClearOptions();
            for (int i = 0; i < resultArray.Length; i++)
            {
                _classroomDropdown.options.Add(new Dropdown.OptionData(resultArray[i]));
            }
            _classroomDropdown.value = 0;
            _classroomDropdown.GetComponentInChildren<Text>().text = resultArray[_classroomDropdown.value];
        }
        private IEnumerator FindEmptyRoom()
        {
            Enum.TryParse(_dayDropdown.options[_dayDropdown.value].text, out InuBooster.Enumerators.DayOfTheWeek myStatus);
            string building_name = _buildNoDropdown.options[_buildNoDropdown.value].text.Split(')')[0].Split('(')[1].Trim();
            string start_time = _startTimeDropdown.options[_startTimeDropdown.value].text;
            string end_time = _endTimeDropdown.options[_endTimeDropdown.value].text;

            WWWForm form = new WWWForm();
            form.AddField("day", myStatus.ToString());
            form.AddField("building", building_name);
            form.AddField("start_time", start_time);
            form.AddField("end_time", end_time);

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_EMPTY_ROOM_LIST, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1].Trim();

            SetUIToMenu1000();
            GuideProcessing(_databaseResult);
            Synthesize();
        }
        private IEnumerator FindClassRoom()
        {
            Enum.TryParse(_dayDropdown.options[_dayDropdown.value].text, out InuBooster.Enumerators.DayOfTheWeek myStatus);
            string building_name = _buildNoDropdown.options[_buildNoDropdown.value].text.Split(')')[0].Split('(')[1].Trim();
            string class_name = building_name + _classroomDropdown.options[_classroomDropdown.value].text;

            WWWForm form = new WWWForm();
            form.AddField("day", myStatus.ToString());
            form.AddField("class", class_name);

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_SCHEDULE, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator GetWifiInfo()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_WIFI_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator GetCopyInfo()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_COPY_STORE_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator GetBuildingDropdownItem4Printer()
        {
            _buildNoDropdown.ClearOptions();

            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_PRINTER_BUILDING_LIST, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            string[] resultArray = _databaseResult.Split('^');

            _buildNoDropdown.ClearOptions();
            for (int i = 0; i < resultArray.Length; i++)
            {
                _buildNoDropdown.options.Add(new Dropdown.OptionData(resultArray[i]));
            }
            _buildNoDropdown.value = 2;
            _buildNoDropdown.GetComponentInChildren<Text>().text = resultArray[_buildNoDropdown.value];
        }
        private IEnumerator GetPrinterInfo()
        {
            string building_name = _buildNoDropdown.options[_buildNoDropdown.value].text.Split(')')[0].Split('(')[1].Trim();
            WWWForm form = new WWWForm();
            form.AddField("code", building_name);
            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_PRINTER_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator GetAffiliateInfo()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_AFFILIATE_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator Get4_4_1Info()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_CONVINIENCE_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator Get4_4_2Info()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_CAFETERIA_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator Get4_4_3Info()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_CAFE_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator Get4_4_4Info()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_VENDING_MACHINE_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator Get4_5Info()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_PLAY_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator GetWelfareDropwonItem()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_WELFARE_LIST, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();

            string[] resultArray = _databaseResult.Split('^');

            _WelfareDropdown.ClearOptions();
            for (int i = 0; i < resultArray.Length; i++)
            {
                _WelfareDropdown.options.Add(new Dropdown.OptionData(resultArray[i]));
            }
            _WelfareDropdown.value = 0;
            _WelfareDropdown.GetComponentInChildren<Text>().text = resultArray[_WelfareDropdown.value];
        }
        private IEnumerator GetWelfareInfo()
        {
            string welfare_key = _WelfareDropdown.options[_WelfareDropdown.value].text.Trim();
            WWWForm form = new WWWForm();
            form.AddField("key", welfare_key);


            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_WELFARE_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        private IEnumerator GetRestInfo()
        {
            string str = "";
            if (_restToggle1.GetComponentInChildren<Toggle>().isOn)
            {
                str += _restToggle1.GetComponentInChildren<Text>().text;
                str += "%";
            }
            if (_restToggle2.GetComponentInChildren<Toggle>().isOn)
            {
                str += _restToggle2.GetComponentInChildren<Text>().text;
                str += "%";
            }
            if (_restToggle3.GetComponentInChildren<Toggle>().isOn)
            {
                str += _restToggle3.GetComponentInChildren<Text>().text;
                str += "%";
            }
            WWWForm form = new WWWForm();
            form.AddField("string", str);

            WWW www = new WWW(InuBooster.Constants.BOOSTER_SERVER + InuBooster.Constants.URI_GET_REST_INFO, form);
            yield return www;
            Debug.Log("www.text: " + www.text);
            _databaseResult = www.text.Trim();
            _textToSpeechString = _databaseResult.Split('%')[1];
            CreatePanel(_databaseResult.Split('%')[0]);
            Synthesize();
        }
        /*------------------------------------------ UI ------------------------------------------*/
        private void UI_Initializer()
        {
            //Get UI Component from the Scene(for Voice Recognition)
            _speechRecognitionState = GameObject.Find("Image_RecordState").GetComponent<Image>();
            _isRuntimeDetectionToggle = GameObject.Find("Toggle_IsRuntime").GetComponent<Toggle>();
            _contextPhrases = GameObject.Find("InputField_SpeechContext").GetComponent<InputField>();

            Canvas canvas = GameObject.Find("Canvas").GetComponent<Canvas>();
            //Get Button Component from the Scene(for Graphical User Interface)
            _startRecordButton = Instantiate(Resources.Load("Prefabs\\Button_StartRecord", typeof(Button)) as Button, canvas.transform);
            _btn1 = Instantiate(Resources.Load("Prefabs\\btn1", typeof(Button)) as Button, canvas.transform);
            _btn2 = Instantiate(Resources.Load("Prefabs\\btn2", typeof(Button)) as Button, canvas.transform);
            _btn3 = Instantiate(Resources.Load("Prefabs\\btn3", typeof(Button)) as Button, canvas.transform);
            _btn3_1 = Instantiate(Resources.Load("Prefabs\\btn3_1", typeof(Button)) as Button, canvas.transform);
            _btn3_2 = Instantiate(Resources.Load("Prefabs\\btn3_2", typeof(Button)) as Button, canvas.transform);
            _btn3_3 = Instantiate(Resources.Load("Prefabs\\btn3_3", typeof(Button)) as Button, canvas.transform);
            _btn4 = Instantiate(Resources.Load("Prefabs\\btn4", typeof(Button)) as Button, canvas.transform);
            _btn4_1 = Instantiate(Resources.Load("Prefabs\\btn4_1", typeof(Button)) as Button, canvas.transform);
            _btn4_2 = Instantiate(Resources.Load("Prefabs\\btn4_2", typeof(Button)) as Button, canvas.transform);
            _btn4_2_1 = Instantiate(Resources.Load("Prefabs\\btn4_2_1", typeof(Button)) as Button, canvas.transform);
            _btn4_2_2 = Instantiate(Resources.Load("Prefabs\\btn4_2_2", typeof(Button)) as Button, canvas.transform);
            _btn4_3 = Instantiate(Resources.Load("Prefabs\\btn4_3", typeof(Button)) as Button, canvas.transform);
            _btn4_3_1 = Instantiate(Resources.Load("Prefabs\\btn4_3_1", typeof(Button)) as Button, canvas.transform);
            _btn4_3_2 = Instantiate(Resources.Load("Prefabs\\btn4_3_2", typeof(Button)) as Button, canvas.transform);
            _btn4_3_3 = Instantiate(Resources.Load("Prefabs\\btn4_3_3", typeof(Button)) as Button, canvas.transform);
            _btn4_4 = Instantiate(Resources.Load("Prefabs\\btn4_4", typeof(Button)) as Button, canvas.transform);
            _btn4_4_1 = Instantiate(Resources.Load("Prefabs\\btn4_4_1", typeof(Button)) as Button, canvas.transform);
            _btn4_4_2 = Instantiate(Resources.Load("Prefabs\\btn4_4_2", typeof(Button)) as Button, canvas.transform);
            _btn4_4_3 = Instantiate(Resources.Load("Prefabs\\btn4_4_3", typeof(Button)) as Button, canvas.transform);
            _btn4_4_4 = Instantiate(Resources.Load("Prefabs\\btn4_4_4", typeof(Button)) as Button, canvas.transform);
            _btn4_5 = Instantiate(Resources.Load("Prefabs\\btn4_5", typeof(Button)) as Button, canvas.transform);
            _returnButton = Instantiate(Resources.Load("Prefabs\\ReturnButton", typeof(Button)) as Button, canvas.transform);
            _submitButton = Instantiate(Resources.Load("Prefabs\\SubmitButton", typeof(Button)) as Button, canvas.transform);

            //Get Dropdown Component from the Scene(for Graphical User Interface)
            _dayDropdown = Instantiate(Resources.Load("Prefabs\\DayDropdown", typeof(Dropdown)) as Dropdown, canvas.transform);
            _startTimeDropdown = Instantiate(Resources.Load("Prefabs\\StartTimeDropdown", typeof(Dropdown)) as Dropdown, canvas.transform);
            _endTimeDropdown = Instantiate(Resources.Load("Prefabs\\EndTimeDropdown", typeof(Dropdown)) as Dropdown, canvas.transform);
            _buildNoDropdown = Instantiate(Resources.Load("Prefabs\\BuildNoDropdown", typeof(Dropdown)) as Dropdown, canvas.transform);
            _WelfareDropdown = Instantiate(Resources.Load("Prefabs\\WelfareDropdown", typeof(Dropdown)) as Dropdown, canvas.transform);
            _classroomDropdown = Instantiate(Resources.Load("Prefabs\\ClassroomDropdown", typeof(Dropdown)) as Dropdown, canvas.transform);


            //Get Text Component from the Scene(for Graphical User Interface)
            _dayText = Instantiate(Resources.Load("Prefabs\\DayText", typeof(Text)) as Text, canvas.transform);
            _startTimeText = Instantiate(Resources.Load("Prefabs\\StartTimeText", typeof(Text)) as Text, canvas.transform);
            _endTimeText = Instantiate(Resources.Load("Prefabs\\EndTimeText", typeof(Text)) as Text, canvas.transform);
            _buildNoText = Instantiate(Resources.Load("Prefabs\\BuildNoText", typeof(Text)) as Text, canvas.transform);
            _WelfareText = Instantiate(Resources.Load("Prefabs\\WelfareText", typeof(Text)) as Text, canvas.transform);
            _classroomText = Instantiate(Resources.Load("Prefabs\\ClassroomText", typeof(Text)) as Text, canvas.transform);

            _restToggle1 = Instantiate(Resources.Load("Prefabs\\RestToggle1Image", typeof(Image)) as Image, canvas.transform);
            _restToggle2 = Instantiate(Resources.Load("Prefabs\\RestToggle2Image", typeof(Image)) as Image, canvas.transform);
            _restToggle3 = Instantiate(Resources.Load("Prefabs\\RestToggle3Image", typeof(Image)) as Image, canvas.transform);

            _INUTextBox = Instantiate(Resources.Load("Prefabs\\INUTextBox", typeof(Image)) as Image, canvas.transform);

            _minimapRawImage = GameObject.Find("MinimapRawImage").GetComponent<RawImage>();

            //Initialize Menu Indicater
            _selectedMenu = 0;

            //Initialize Start Time Dropdown
            _startTimeDropdown.ClearOptions();
            for (int i = 0; i < Enum.GetNames(typeof(InuBooster.Enumerators.Time)).Length - 1; i++)
            {
                _startTimeDropdown.options.Add(new Dropdown.OptionData(((InuBooster.Enumerators.Time)i + 9).ToString().Split('_')[1]));
            }
            _startTimeDropdown.value = 0;
            _startTimeDropdown.GetComponentInChildren<Text>().text = ((InuBooster.Enumerators.Time)_startTimeDropdown.value + 9).ToString().Split('_')[1];

            //Initialize End Time Dropdown
            _endTimeDropdown.ClearOptions();
            for (int i = 1; i < Enum.GetNames(typeof(InuBooster.Enumerators.Time)).Length; i++)
            {
                _endTimeDropdown.options.Add(new Dropdown.OptionData(((InuBooster.Enumerators.Time)i + 9).ToString().Split('_')[1]));
            }
            _endTimeDropdown.value = 0;
            _endTimeDropdown.GetComponentInChildren<Text>().text = ((InuBooster.Enumerators.Time)_endTimeDropdown.value + 9).ToString().Split('_')[1];

            //Initialize Building Dropdown
            InitializeBuildingDropbox();

            //Display Initial Interface
            HideAllUI();
            _btn1.gameObject.SetActive(true);
            _btn2.gameObject.SetActive(true);
            _btn3.gameObject.SetActive(true);
            _btn4.gameObject.SetActive(true);

            //Add OnClick Event Handler of all Button
            _btn1.onClick.AddListener(Btn1OnClickHandler);
            _btn2.onClick.AddListener(Btn2OnClickHandler);
            _btn3.onClick.AddListener(Btn3OnClickHandler);
            _btn3_1.onClick.AddListener(Btn3_1OnClickHandler);
            _btn3_2.onClick.AddListener(Btn3_2OnClickHandler);
            _btn3_3.onClick.AddListener(Btn3_3OnClickHandler);
            _btn4.onClick.AddListener(Btn4OnClickHandler);
            _btn4_1.onClick.AddListener(Btn4_1OnClickHandler);
            _btn4_2.onClick.AddListener(Btn4_2OnClickHandler);
            _btn4_2_1.onClick.AddListener(Btn4_2_1OnClickHandler);
            _btn4_2_2.onClick.AddListener(Btn4_2_2OnClickHandler);
            _btn4_3.onClick.AddListener(Btn4_3OnClickHandler);
            _btn4_3_1.onClick.AddListener(Btn4_3_1OnClickHandler);
            _btn4_3_2.onClick.AddListener(Btn4_3_2OnClickHandler);
            _btn4_3_3.onClick.AddListener(Btn4_3_3OnClickHandler);
            _btn4_4.onClick.AddListener(Btn4_4OnClickHandler);
            _btn4_4_1.onClick.AddListener(Btn4_4_1OnClickHandler);
            _btn4_4_2.onClick.AddListener(Btn4_4_2OnClickHandler);
            _btn4_4_3.onClick.AddListener(Btn4_4_3OnClickHandler);
            _btn4_4_4.onClick.AddListener(Btn4_4_4OnClickHandler);
            _btn4_5.onClick.AddListener(Btn4_5OnClickHandler);
            _returnButton.onClick.AddListener(ReturnButtonOnClickHandler);
            _submitButton.onClick.AddListener(SubmitButtonOnClickHandler);
            _startRecordButton.onClick.AddListener(StartRecordButtonOnClickHandler);


            //Add OnValueChanged Event Handler of all Dropdown
            _startTimeDropdown.onValueChanged.AddListener(StartTimeDropdownOnValueChanged);
            _buildNoDropdown.onValueChanged.AddListener(BuildNoDropdownOnValueChanged);
        }
        private void HideAllUI()
        {
            _btn1.gameObject.SetActive(false);
            _btn2.gameObject.SetActive(false);
            _btn3.gameObject.SetActive(false);
            _btn3_1.gameObject.SetActive(false);
            _btn3_2.gameObject.SetActive(false);
            _btn3_3.gameObject.SetActive(false);
            _btn4.gameObject.SetActive(false);
            _btn4_1.gameObject.SetActive(false);
            _btn4_2.gameObject.SetActive(false);
            _btn4_2_1.gameObject.SetActive(false);
            _btn4_2_2.gameObject.SetActive(false);
            _btn4_3.gameObject.SetActive(false);
            _btn4_3_1.gameObject.SetActive(false);
            _btn4_3_2.gameObject.SetActive(false);
            _btn4_3_3.gameObject.SetActive(false);
            _btn4_4.gameObject.SetActive(false);
            _btn4_4_1.gameObject.SetActive(false);
            _btn4_4_2.gameObject.SetActive(false);
            _btn4_4_3.gameObject.SetActive(false);
            _btn4_4_4.gameObject.SetActive(false);
            _btn4_5.gameObject.SetActive(false);
            _returnButton.gameObject.SetActive(false);
            _submitButton.gameObject.SetActive(false);

            _dayDropdown.gameObject.SetActive(false);
            _startTimeDropdown.gameObject.SetActive(false);
            _endTimeDropdown.gameObject.SetActive(false);
            _buildNoDropdown.gameObject.SetActive(false);
            _WelfareDropdown.gameObject.SetActive(false);
            _classroomDropdown.gameObject.SetActive(false);

            _dayText.gameObject.SetActive(false);
            _startTimeText.gameObject.SetActive(false);
            _endTimeText.gameObject.SetActive(false);
            _buildNoText.gameObject.SetActive(false);
            _WelfareText.gameObject.SetActive(false);
            _classroomText.gameObject.SetActive(false);

            _restToggle1.gameObject.SetActive(false);
            _restToggle2.gameObject.SetActive(false);
            _restToggle3.gameObject.SetActive(false);

            _INUTextBox.gameObject.SetActive(false);

            _minimapRawImage.gameObject.SetActive(false);

            DestroyPanel();
        }
        private void SetInteractableAllButton(bool value)
        {
            _btn1.interactable = value;
            _btn2.interactable = value;
            _btn3.interactable = value;
            _btn3_1.interactable = value;
            _btn3_2.interactable = value;
            _btn3_3.interactable = value;
            _btn4.interactable = value;
            _btn4_1.interactable = value;
            _btn4_2.interactable = value;
            _btn4_2_1.interactable = value;
            _btn4_2_2.interactable = value;
            _btn4_3.interactable = value;
            _btn4_3_1.interactable = value;
            _btn4_3_2.interactable = value;
            _btn4_3_3.interactable = value;
            _btn4_4.interactable = value;
            _btn4_4_1.interactable = value;
            _btn4_4_2.interactable = value;
            _btn4_4_3.interactable = value;
            _btn4_4_4.interactable = value;
            _btn4_5.interactable = value;
            _returnButton.interactable = value;
            _submitButton.interactable = value;
            _startRecordButton.interactable = value;
        }
        private void SetUIToMenu0()
        {
            DisableAllCam();
            _cam[2].enabled = true;
            _selectedMenu = 0;

            HideAllUI();

            _btn1.gameObject.SetActive(true);
            _btn2.gameObject.SetActive(true);
            _btn3.gameObject.SetActive(true);
            _btn4.gameObject.SetActive(true);
        }
        private void SetUIToMenu1000()
        {
            _selectedMenu = 1000;
            DisableAllCam();
            _cam[target + 6].enabled = true;
            Destroy(floorInstance);
            HideAllUI();
            _INUTextBox.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);
        }
        private void CreatePanel(string contents)
        {
            int n_rows = contents.Split('^').Length;
            int n_cols = contents.Split('^')[0].Split('&').Length;

            int n_rows_fir = 0, n_cols_fir = 0;

            GameObject canvas = GameObject.Find("Canvas");
            Canvas c = canvas.GetComponent<Canvas>();
            _panel = new GameObject("Panel");
            _panel.AddComponent<CanvasRenderer>();
            Image panel_img = _panel.AddComponent<Image>();
            panel_img.color = new Color(1.0f, 1.0f, 1.0f, 0.65f);
            _panel.transform.SetParent(canvas.transform, false);
            _panel.GetComponent<RectTransform>().anchoredPosition = new Vector2(-206.0f, -45.0f);
            _panel.GetComponent<RectTransform>().sizeDelta = new Vector2(1400, 50 * n_rows);

            float width = 0.0f, horizontal_offset = 0;

            Text text_prefab = Resources.Load("TextPrefabs", typeof(Text)) as Text;
            switch (_selectedMenu)
            {
                case 21:
                    for (int i = 0; i < n_rows; i++)
                    {
                        if (contents.Split('^')[i].Equals(""))
                            break;
                        for (int j = 0; j < n_cols; j++)
                        {
                            if (contents.Split('^')[i].Split('&')[j] == "-")
                            {
                                _unityAnim.SetBool("Sad", true);
                                StartCoroutine(SadToWait());
                            }

                            width = 400.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);
                            cell.text = contents.Split('^')[i].Split('&')[j];

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 500.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 500;
                                    width = 400.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 900;
                                    width = 500.0f;
                                    break;
                            }
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 41:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            width = 400.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);
                            cell.text = contents.Split('^')[i].Split('&')[j];

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 500.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 500;
                                    width = 400.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 900;
                                    width = 500.0f;
                                    break;
                            }
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 421:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            width = 300.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);
                            cell.text = contents.Split('^')[i].Split('&')[j];

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 300.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 300;
                                    width = 300.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 600;
                                    width = 300.0f;
                                    break;
                                case 3:
                                    horizontal_offset = 900;
                                    width = 500.0f;
                                    break;
                            }
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 4221:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            width = 600.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);
                            cell.text = contents.Split('^')[i].Split('&')[j];

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 700.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 700;
                                    width = 700.0f;
                                    break;
                            }
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 431:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            width = 400.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);
                            cell.text = contents.Split('^')[i].Split('&')[j];

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 400.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 400;
                                    width = 500.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 900;
                                    width = 500.0f;
                                    break;
                            }
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 4321:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            width = 400.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);
                            cell.text = contents.Split('^')[i].Split('&')[j];

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 300.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 300;
                                    width = 200.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 500;
                                    width = 300.0f;
                                    break;
                                case 3:
                                    horizontal_offset = 800;
                                    width = 200.0f;
                                    break;
                                case 4:
                                    horizontal_offset = 1000;
                                    width = 400.0f;
                                    break;
                            }
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 4331:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            width = 300.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);
                            cell.text = contents.Split('^')[i].Split('&')[j];

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 200.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 200;
                                    width = 500.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 700;
                                    width = 200.0f;
                                    break;
                                case 3:
                                    horizontal_offset = 900;
                                    width = 500.0f;
                                    break;
                            }
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 441:
                    n_rows_fir = contents.Split('^')[0].Split('&').Length;
                    n_cols_fir = contents.Split('^')[0].Split('&')[0].Split('*').Length;
                    DestroyPanel();
                    _panel = new GameObject("Panel");
                    _panel.AddComponent<CanvasRenderer>();
                    panel_img = _panel.AddComponent<Image>();
                    panel_img.color = new Color(1.0f, 1.0f, 1.0f, 0.65f);
                    _panel.transform.SetParent(canvas.transform, false);
                    _panel.GetComponent<RectTransform>().anchoredPosition = new Vector2(-206.0f, -45.0f);
                    _panel.GetComponent<RectTransform>().sizeDelta = new Vector2(1400, 50 * n_rows_fir);
                    //int n_rows_sec = contents.Split('^')[1].Split('&').Length;
                    //int n_cols_sec = contents.Split('^')[1].Split('&')[0].Split('*').Length;
                    for (int i = 0; i < n_rows_fir; i++)
                    {
                        for (int j = 0; j < n_cols_fir; j++)
                        {
                            width = 300.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 400.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 400;
                                    width = 600.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 1000;
                                    width = 100.0f;
                                    break;
                                case 3:
                                    horizontal_offset = 1100;
                                    width = 200.0f;
                                    break;
                                case 4:
                                    horizontal_offset = 1300;
                                    width = 100.0f;
                                    break;
                            }
                            cell.text = contents.Split('^')[0].Split('&')[i].Split('*')[j];
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 442:
                    n_rows_fir = contents.Split('^')[0].Split('&').Length;
                    n_cols_fir = contents.Split('^')[0].Split('&')[0].Split('*').Length;
                    DestroyPanel();
                    _panel = new GameObject("Panel");
                    _panel.AddComponent<CanvasRenderer>();
                    panel_img = _panel.AddComponent<Image>();
                    panel_img.color = new Color(1.0f, 1.0f, 1.0f, 0.65f);
                    _panel.transform.SetParent(canvas.transform, false);
                    _panel.GetComponent<RectTransform>().anchoredPosition = new Vector2(-206.0f, -45.0f);
                    _panel.GetComponent<RectTransform>().sizeDelta = new Vector2(1400, 50 * n_rows_fir);

                    //int n_rows_sec = contents.Split('^')[1].Split('&').Length;
                    //int n_cols_sec = contents.Split('^')[1].Split('&')[0].Split('*').Length;
                    for (int i = 0; i < n_rows_fir; i++)
                    {
                        for (int j = 0; j < n_cols_fir; j++)
                        {
                            width = 300.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 400.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 400;
                                    width = 400.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 800;
                                    width = 200.0f;
                                    break;
                                case 3:
                                    horizontal_offset = 1000;
                                    width = 200.0f;
                                    break;
                                case 4:
                                    horizontal_offset = 1200;
                                    width = 200.0f;
                                    break;
                            }
                            cell.text = contents.Split('^')[0].Split('&')[i].Split('*')[j];
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 443:
                    n_rows_fir = contents.Split('^')[0].Split('&').Length;
                    n_cols_fir = contents.Split('^')[0].Split('&')[0].Split('*').Length;
                    DestroyPanel();
                    _panel = new GameObject("Panel");
                    _panel.AddComponent<CanvasRenderer>();
                    panel_img = _panel.AddComponent<Image>();
                    panel_img.color = new Color(1.0f, 1.0f, 1.0f, 0.65f);
                    _panel.transform.SetParent(canvas.transform, false);
                    _panel.GetComponent<RectTransform>().anchoredPosition = new Vector2(-206.0f, -45.0f);
                    _panel.GetComponent<RectTransform>().sizeDelta = new Vector2(1400, 50 * n_rows_fir);

                    //int n_rows_sec = contents.Split('^')[1].Split('&').Length;
                    //int n_cols_sec = contents.Split('^')[1].Split('&')[0].Split('*').Length;
                    for (int i = 0; i < n_rows_fir; i++)
                    {
                        for (int j = 0; j < n_cols_fir; j++)
                        {
                            width = 300.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 400.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 400;
                                    width = 400.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 800;
                                    width = 200.0f;
                                    break;
                                case 3:
                                    horizontal_offset = 1000;
                                    width = 200.0f;
                                    break;
                                case 4:
                                    horizontal_offset = 1200;
                                    width = 200.0f;
                                    break;
                            }
                            cell.text = contents.Split('^')[0].Split('&')[i].Split('*')[j];
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 444:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            width = 400.0f;
                            Text cell = Instantiate(text_prefab, _panel.transform);

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 300.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 300;
                                    width = 350.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 650;
                                    width = 750.0f;
                                    break;
                            }
                            cell.text = contents.Split('^')[i].Split('&')[j];
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
                case 45:
                    for (int i = 0; i < n_rows; i++)
                    {
                        for (int j = 0; j < n_cols; j++)
                        {
                            Text cell = Instantiate(text_prefab, _panel.transform);

                            switch (j)
                            {
                                case 0:
                                    horizontal_offset = 0;
                                    width = 250.0f;
                                    break;
                                case 1:
                                    horizontal_offset = 250;
                                    width = 250.0f;
                                    break;
                                case 2:
                                    horizontal_offset = 500;
                                    width = 400.0f;
                                    break;
                                case 3:
                                    horizontal_offset = 900;
                                    width = 250.0f;
                                    break;
                                case 4:
                                    horizontal_offset = 1150;
                                    width = 250.0f;
                                    break;
                            }
                            cell.text = contents.Split('^')[i].Split('&')[j];
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Top, 50.0f * i, 50);
                            cell.GetComponent<RectTransform>().SetInsetAndSizeFromParentEdge(RectTransform.Edge.Left, horizontal_offset, width);
                        }
                    }
                    break;
            }
        }
        private void DestroyPanel()
        {
            Destroy(_panel);
        }
        /*---------------------------------------- Camera ----------------------------------------*/
        private void DisableAllCam()
        {
            for (int i = 0; i < Enum.GetNames(typeof(InuBooster.Enumerators.CameraName)).Length; i++)
            {
                _cam[i].enabled = false;
            }
            //Minimap Camera Always On Air
            _cam[0].enabled = true;
        }
        /*------------------------------------- Event Handler ------------------------------------*/
        private void StartRecordButtonOnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - StartRecordButtonOnClickHandler");
            }
            _contextPhrases.text = "";
            InitializeBuildingDropbox();
            //When Recording
            if (_speechRecognitionState.color == Color.red)
            {
                ApplySpeechContextPhrases();
                _speechRecognitionState.color = Color.yellow;
                _startRecordButton.image.overrideSprite = img[2];
                _contextPhrases.text = "음성인식 처리중...";
                _speechRecognition.StopRecord();
            }
            else
            {
                _speechRecognitionState.color = Color.red;
                _startRecordButton.image.overrideSprite = img[1];
                _contextPhrases.text = "음성인식 시작";
                _speechRecognitionResult = string.Empty;
                _speechRecognition.StartRecord(_isRuntimeDetectionToggle.isOn);
            }
        }
        private void Btn1OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn1OnClickHandler");
            }
            _selectedMenu = 1;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();

            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);

            _dayDropdown.gameObject.SetActive(true);
            _startTimeDropdown.gameObject.SetActive(true);
            _endTimeDropdown.gameObject.SetActive(true);
            _buildNoDropdown.gameObject.SetActive(true);

            _dayText.gameObject.SetActive(true);
            _startTimeText.gameObject.SetActive(true);
            _endTimeText.gameObject.SetActive(true);
            _buildNoText.gameObject.SetActive(true);
        }
        private void Btn2OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn2OnClickHandler");
            }
            _selectedMenu = 2;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();

            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);

            _dayDropdown.gameObject.SetActive(true);
            _buildNoDropdown.gameObject.SetActive(true);
            _classroomDropdown.gameObject.SetActive(true);

            _dayText.gameObject.SetActive(true);
            _buildNoText.gameObject.SetActive(true);
            _classroomText.gameObject.SetActive(true);
        }
        private void Btn3OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn3OnClickHandler");
            }
            _selectedMenu = 3;

            HideAllUI();

            DisableAllCam();
            _cam[2].enabled = true;

            _btn3_1.gameObject.SetActive(true);
            _btn3_2.gameObject.SetActive(false);
            _btn3_3.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);
        }
        private void Btn3_1OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn3_1OnClickHandler");
            }
            _selectedMenu = 31;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            _buildNoDropdown.gameObject.SetActive(true);
            _buildNoText.gameObject.SetActive(true);
            _minimapRawImage.gameObject.SetActive(true);
        }
        private void Btn3_2OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn3_2OnClickHandler");
            }
            _selectedMenu = 32;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
        }
        private void Btn3_3OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn3_3OnClickHandler");
            }
            _selectedMenu = 33;

            DisableAllCam();
            _cam[1].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
        }
        private void Btn4OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4OnClickHandler");
            }
            if (_selectedMenu == 0)
            {
                _unityAnim.SetBool("Tip", true);
                StartCoroutine(TipToWait());
            }

            _selectedMenu = 4;

            DisableAllCam();
            _cam[3].enabled = true;

            HideAllUI();
            _btn4_1.gameObject.SetActive(true);
            _btn4_2.gameObject.SetActive(true);
            _btn4_3.gameObject.SetActive(true);
            _btn4_4.gameObject.SetActive(true);
            _btn4_5.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);


        }
        private void Btn4_1OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_1OnClickHandler");
            }
            _selectedMenu = 41;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(GetWifiInfo());
        }
        private void Btn4_2OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_2OnClickHandler");
            }
            _selectedMenu = 42;

            DisableAllCam();
            _cam[3].enabled = true;

            HideAllUI();
            _btn4_2_1.gameObject.SetActive(true);
            _btn4_2_2.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);

        }
        private void Btn4_2_1OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_2_1OnClickHandler");
            }
            _selectedMenu = 421;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(GetCopyInfo());

        }
        private void Btn4_2_2OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_2_2OnClickHandler");
            }
            _selectedMenu = 422;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _buildNoText.gameObject.SetActive(true);
            _buildNoDropdown.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);
            InitializeBuildingDropbox4Printer();
        }
        private void Btn4_3OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_3OnClickHandler");
            }
            _selectedMenu = 43;

            DisableAllCam();
            _cam[3].enabled = true;

            HideAllUI();
            _btn4_3_1.gameObject.SetActive(true);
            _btn4_3_2.gameObject.SetActive(true);
            _btn4_3_3.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);

        }
        private void Btn4_3_1OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_3_1OnClickHandler");
            }
            _selectedMenu = 431;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(GetAffiliateInfo());

        }
        private void Btn4_3_2OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_3_2OnClickHandler");
            }
            _selectedMenu = 432;

            DisableAllCam();
            _cam[3].enabled = true;

            HideAllUI();
            _WelfareDropdown.gameObject.SetActive(true);
            _WelfareText.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);
            InitializeWelfareDropbox();

        }
        private void Btn4_3_3OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_3_3OnClickHandler");
            }
            _selectedMenu = 433;

            DisableAllCam();
            _cam[3].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);
            _restToggle1.gameObject.SetActive(true);
            _restToggle2.gameObject.SetActive(true);
            _restToggle3.gameObject.SetActive(true);
        }
        private void Btn4_4OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_4OnClickHandler");
            }
            _selectedMenu = 44;

            DisableAllCam();
            _cam[3].enabled = true;

            HideAllUI();
            _btn4_4_1.gameObject.SetActive(true);
            _btn4_4_2.gameObject.SetActive(true);
            _btn4_4_3.gameObject.SetActive(true);
            _btn4_4_4.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);

        }
        private void Btn4_4_1OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_4_1OnClickHandler");
            }
            _selectedMenu = 441;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(Get4_4_1Info());
        }
        private void Btn4_4_2OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_4_2OnClickHandler");
            }
            _selectedMenu = 442;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(Get4_4_2Info());
        }
        private void Btn4_4_3OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_4_3OnClickHandler");
            }
            _selectedMenu = 443;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(Get4_4_3Info());
        }
        private void Btn4_4_4OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_4_4OnClickHandler");
            }
            _selectedMenu = 444;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(Get4_4_4Info());
        }
        private void Btn4_5OnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - Btn4_5OnClickHandler");
            }
            _selectedMenu = 45;

            DisableAllCam();
            _cam[4].enabled = true;

            HideAllUI();
            _returnButton.gameObject.SetActive(true);
            StartCoroutine(Get4_5Info());

        }
        private void ReturnButtonOnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - ReturnButtonOnClickHandler");
            }
            _contextPhrases.text = "";
            switch (_selectedMenu)
            {
                case 1:
                case 2:
                case 3:
                case 4:
                    DisableAllCam();
                    _cam[3].enabled = true;
                    SetUIToMenu0();
                    break;
                case 11:
                    Btn1OnClickHandler();
                    break;
                case 21:
                    Btn2OnClickHandler();
                    break;
                case 31:
                case 32:
                case 33:
                    Btn3OnClickHandler();
                    break;
                case 41:
                case 42:
                case 43:
                case 44:
                case 45:
                    Btn4OnClickHandler();
                    break;
                case 421:
                case 422:
                    InitializeBuildingDropbox();
                    Btn4_2OnClickHandler();
                    break;
                case 431:
                case 432:
                case 433:
                    Btn4_3OnClickHandler();
                    break;
                case 4221:
                    Btn4_2_2OnClickHandler();
                    break;
                case 4321:
                    Btn4_3_2OnClickHandler();
                    break;
                case 4331:
                    Btn4_3_3OnClickHandler();
                    break;
                case 441:
                    Btn4_4OnClickHandler();
                    break;
                case 442:
                    Btn4_4OnClickHandler();
                    break;
                case 443:
                    Btn4_4OnClickHandler();
                    break;
                case 444:
                    Btn4_4OnClickHandler();
                    break;
                case 1000:
                    ResetBuilding(target);
                    DestroyEffect();
                    SetUIToMenu0();
                    break;
                case 1001:
                    SetUIToMenu1000();
                    break;
                default:
                    SetUIToMenu0();
                    break;
            }
        }
        private void SubmitButtonOnClickHandler()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - SubmitButtonOnClickHandler");
            }
            switch (_selectedMenu)
            {
                case 1:
                    _selectedMenu = 11;

                    DisableAllCam();
                    _cam[2].enabled = true;

                    StartFindEmptyRoom();
                    showEffect = true;
                    break;
                case 2:
                    _selectedMenu = 21;

                    //DisableAllCam();
                    //_cam[2].enabled = true;

                    StartFindClassRoom();
                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);
                    break;
                case 422:
                    _selectedMenu = 4221;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);
                    StartCoroutine(GetPrinterInfo());
                    break;
                case 432:
                    _selectedMenu = 4321;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);
                    StartCoroutine(GetWelfareInfo());
                    break;
                case 433:
                    _selectedMenu = 4331;

                    DisableAllCam();
                    _cam[4].enabled = true;

                    HideAllUI();
                    _returnButton.gameObject.SetActive(true);
                    StartCoroutine(GetRestInfo());
                    break;
            }
        }
        private void StartTimeDropdownOnValueChanged(int value)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - StartTimeDropdownOnValueChanged");
            }
            _endTimeDropdown.ClearOptions();

            for (int i = value + 1; i < Enum.GetNames(typeof(InuBooster.Enumerators.Time)).Length; i++)
            {
                _endTimeDropdown.options.Add(new Dropdown.OptionData(((InuBooster.Enumerators.Time)i + 9).ToString().Split('_')[1]));
            }
            _endTimeDropdown.value = 0;
            Debug.Log(((InuBooster.Enumerators.Time)_endTimeDropdown.value + 9).ToString().Split('_')[1]);
            _endTimeDropdown.GetComponentInChildren<Text>().text = ((InuBooster.Enumerators.Time)value + 10).ToString().Split('_')[1];

        }
        private void BuildNoDropdownOnValueChanged(int value)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - BuildNoDropdownOnValueChanged");
            }
            switch (_selectedMenu)
            {
                case 422:
                    break;
                case 31:
                default:
                    string buildingCode = _buildNoDropdown.options[_buildNoDropdown.value].text.Split(')')[0].Split('(')[1].Trim();
                    InitializeClassroomDropbox(buildingCode);

                    if (Enum.IsDefined(typeof(InuBooster.Enumerators.BuildingName), buildingCode))
                    {
                        target = (int)(InuBooster.Enumerators.BuildingName)System.Enum.Parse(typeof(InuBooster.Enumerators.BuildingName), buildingCode);
                        reached = false;
                    }

                    if (target >= 0)
                    {
                        _agent.destination = _destination[target].transform.position;
                        reached = false;
                    }
                    break;
            }

        }
        /*-------------------------------------- GUI Effect --------------------------------------*/
        private void GuideProcessing(string str)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - GuideProcessing");
            }
            Debug.Log("str: " + str);
            string tempstr;
            string[] resultArray;
            if (str.Split('%').Length > 2)
            {
                Debug.Log("if statements");
                tempstr = str.Split('%')[1];
                Debug.Log("tempstr: " + tempstr);
                _textToSpeechString = str.Split('%')[2];
                resultArray = str.Split('%')[1].Split('^')[0].Split('&')[1].Split('*');
                Debug.Log("resultArray: " + resultArray);
            }
            else
            {
                Debug.Log("else statements");
                tempstr = str.Split('%')[0];
                Debug.Log("tempstr: " + tempstr);
                _textToSpeechString = str.Split('%')[1];
                resultArray = str.Split('%')[0].Split('^')[0].Split('&')[1].Split('*');
                Debug.Log("resultArray: " + resultArray);
            }

            if (!tempstr.Equals(""))
            {
                if (Enum.IsDefined(typeof(InuBooster.Enumerators.BuildingName), tempstr.Split('^')[0].Split('&')[0].Trim()))
                {
                    target = (int)(InuBooster.Enumerators.BuildingName)System.Enum.Parse(typeof(InuBooster.Enumerators.BuildingName), tempstr.Split('^')[0].Split('&')[0].Trim());
                    if (target >= 0)
                    {
                        _selectedMenu = 1000;
                        //SetInteractableAllButton(false);
                        StartCoroutine(StartSpawnEffect(resultArray));
                        reached = false;
                    }
                }

            }
            Synthesize();
        }
        private void ChangeUI()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - ChangeUI");
            }
            DisableAllCam();
            _cam[target + 6].enabled = true;

            HideAllUI();
            _INUTextBox.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(true);
        }
        private void MakeTransparent(int build_no)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - MakeTransparent");
            }
            Material newMat = Resources.Load("Building_t", typeof(Material)) as Material;
            floor_count = 0;
            SetInteractableAllButton(false);
            for (int i = _building[build_no].transform.childCount - 3; i >= 0; i--)
            {
                _building[build_no].transform.GetChild(i).GetComponent<Renderer>().material = newMat;
                if (i != 0)
                {
                    StartCoroutine(MoveFromTo(build_no, _building[build_no].transform.GetChild(i).gameObject.transform,
                                              _building[build_no].transform.GetChild(i).gameObject.transform.position,
                                              _building[build_no].transform.GetChild(i).gameObject.transform.position + new Vector3(0, 8 * i, 0),
                                              10.0f, i));
                }
            }
        }
        IEnumerator MoveFromTo(int build_no, Transform objectToMove, Vector3 a, Vector3 b, float speed, int n)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - MoveFromTo");
            }
            yield return new WaitForSeconds(2.0f);
            if (_selectedMenu == 1000)
            {
                ChangeUI();
            }
            _agent.destination = _destination[target].transform.position;
            float step = (speed / (a - b).magnitude) * Time.fixedDeltaTime;
            float t = 0;
            while (t <= 1.0f)
            {
                t += step; // Goes from 0 to 1, incrementing by step each time
                objectToMove.position = Vector3.Lerp(a, b, t); // Move objectToMove closer to b
                yield return new WaitForFixedUpdate();         // Leave the routine and return here in the next frame
            }

            objectToMove.position = b;
            floor_count++;
            int i = _building[build_no].transform.childCount - 3;
            if (i == n)
            {
                if (objectToMove.position == b)
                {
                    SetInteractableAllButton(true);
                }
            }
            
        }
        private void ResetBuilding(int build_no)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - ResetBuilding");
            }
            Material newMat = Resources.Load("Building", typeof(Material)) as Material;
            for (int i = _building[build_no].transform.childCount - 3; i >= 0; i--)
            {
                _building[build_no].transform.GetChild(i).GetComponent<Renderer>().material = newMat;
                SetInteractableAllButton(false);
                if (i != 0)
                {
                    StartCoroutine(MoveFromTo(build_no, _building[build_no].transform.GetChild(i).gameObject.transform,
                                              _building[build_no].transform.GetChild(i).gameObject.transform.position,
                                              _building[build_no].transform.GetChild(i).gameObject.transform.position + new Vector3(0, -8 * i, 0),
                                              10.0f, i));
                }
            }
        }
        private IEnumerator StartSpawnEffect(string[] roomList)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - StartSpawnEffect");
            }
            yield return new WaitForSeconds(0.1f);
            SpawnEffect(roomList);
        }
        private void SpawnEffect(string[] classroomList)
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - SpawnEffect");
            }
            particleEffect = new UnityEngine.Object[classroomList.Length];
            for (int i = 0; i < classroomList.Length; i++)
            {
                if (classroomList[i].Contains("f"))
                {
                    UnityEngine.Object res = Resources.Load("magic_ring_02");
                    particleEffect[i] = Instantiate(res,
                        GameObject.Find(classroomList[i].Split('f')[0]).transform.position,
                        Quaternion.Euler(-90, 0, 0), GameObject.Find(classroomList[i].Split('f')[0]).transform);
                }
                else
                {
                    UnityEngine.Object res = Resources.Load("teleporter");
                    particleEffect[i] = Instantiate(res,
                        GameObject.Find(classroomList[i]).transform.position,
                        Quaternion.Euler(-90, 0, 0), GameObject.Find(classroomList[i]).transform);
                }
            }
        }
        private void DestroyEffect()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - DestroyEffect");
            }
            try
            {
                for (int i = 0; i < particleEffect.Length; i++)
                {
                    Destroy(particleEffect[i]);
                }
            }
            catch (NullReferenceException e)
            {
                Debug.Log("NullReferenceException: Object reference not set to an instance of an object");
            }
        }
        /*====================================== END METHOD ======================================*/

        IEnumerator WaitForIt()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - WaitForIt");
            }
            yield return new WaitForSeconds(3.0f);
            unityTurn = false;
        }

        IEnumerator TipToWait()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - TipToWait");
            }
            yield return new WaitForSeconds(3.0f);

            _unityAnim.SetBool("Tip", false);
        }
        IEnumerator SadToWait()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - SadToWait");
            }
            yield return new WaitForSeconds(3.0f);

            _unityAnim.SetBool("Sad", false);
        }
        IEnumerator TouchHeadToWait()
        {

            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - TouchHeadToWait");
            }
            yield return new WaitForSeconds(3.0f);

            _unityAnim.SetBool("TouchHead", false);
        }
        IEnumerator DisableTextBox()
        {
            if (DEBUG == 1)
            {
                Debug.Log("Method Invocation - DisableTextBox");
            }
            yield return new WaitForSeconds(3.0f);

            _INUTextBox.gameObject.SetActive(false);
        }
    }
}
