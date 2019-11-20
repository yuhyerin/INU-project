using System;
using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.UI;

namespace InuBooster
{
    public class UIController : MonoBehaviour
    {
        Button _btn1;               // 빈강의실 찾기 버튼
        Button _btn2;               // 강의실 시간표 확인 버튼
        Button _btn3;               // 길찾기 버튼
        Button _btn4;               // 학교 이용 팁 버튼
        Button _returnButton;
        Button _submitButton;       // 조회하기 버튼

        Dropdown _dayDropdown;      // 요일
        Dropdown _startTimeDropdown;// 시작시간
        Dropdown _endTimeDropdown;  // 종료시간
        Dropdown _buildNoDropdown;  // 건물 이름
        Dropdown _classroomDropdown;// 강의실

        Text _dayText;              // 요일
        Text _startTimeText;        // 시작 시간
        Text _endTimeText;          // 종료 시간
        Text _buildNoText;          // 건물 이름
        Text _classroomText;        // 강의실

        private int _selectedMenu;

        // Start is called before the first frame update
        private void UI_Initializer()
        {
            _selectedMenu = 0;
            _btn1 = GameObject.Find("btn1").GetComponent<Button>();
            _btn2 = GameObject.Find("btn2").GetComponent<Button>();
            _btn3 = GameObject.Find("btn3").GetComponent<Button>();
            _btn4 = GameObject.Find("btn4").GetComponent<Button>();
            _returnButton = GameObject.Find("ReturnButton").GetComponent<Button>();
            _submitButton = GameObject.Find("SubmitButton").GetComponent<Button>();

            _dayDropdown = GameObject.Find("DayDropdown").GetComponent<Dropdown>();
            _startTimeDropdown = GameObject.Find("StartTimeDropdown").GetComponent<Dropdown>();
            _endTimeDropdown = GameObject.Find("EndTimeDropdown").GetComponent<Dropdown>();
            _buildNoDropdown = GameObject.Find("BuildNoDropdown").GetComponent<Dropdown>();
            _classroomDropdown = GameObject.Find("ClassroomDropdown").GetComponent<Dropdown>();

            _dayText = GameObject.Find("DayText").GetComponent<Text>();
            _startTimeText = GameObject.Find("StartTimeText").GetComponent<Text>();
            _endTimeText = GameObject.Find("EndTimeText").GetComponent<Text>();
            _buildNoText = GameObject.Find("BuildNoText").GetComponent<Text>();
            _classroomText = GameObject.Find("ClassroomText").GetComponent<Text>();

            _startTimeDropdown.ClearOptions();
            for (int i = 0; i < Enum.GetNames(typeof(InuBooster.Enumerators.Time)).Length - 1; i++)
            {
                _startTimeDropdown.options.Add(new Dropdown.OptionData(((InuBooster.Enumerators.Time)i + 9).ToString().Split('_')[1]));
            }

            _endTimeDropdown.ClearOptions();
            for (int i = 1; i < Enum.GetNames(typeof(InuBooster.Enumerators.Time)).Length; i++)
            {
                _endTimeDropdown.options.Add(new Dropdown.OptionData(((InuBooster.Enumerators.Time)i + 9).ToString().Split('_')[1]));
            }

            InitializeBuildingDropbox();

            _returnButton.gameObject.SetActive(false);
            _submitButton.gameObject.SetActive(false);

            _dayDropdown.gameObject.SetActive(false);
            _startTimeDropdown.gameObject.SetActive(false);
            _endTimeDropdown.gameObject.SetActive(false);
            _buildNoDropdown.gameObject.SetActive(false);
            _classroomDropdown.gameObject.SetActive(false);

            _dayText.gameObject.SetActive(false);
            _startTimeText.gameObject.SetActive(false);
            _endTimeText.gameObject.SetActive(false);
            _buildNoText.gameObject.SetActive(false);
            _classroomText.gameObject.SetActive(false);

            _btn1.onClick.AddListener(Btn1OnClickHandler);
            _btn2.onClick.AddListener(Btn2OnClickHandler);
            _btn3.onClick.AddListener(Btn3OnClickHandler);
            _btn4.onClick.AddListener(Btn4OnClickHandler);
            _returnButton.onClick.AddListener(ReturnButtonOnClickHandler);
            _submitButton.onClick.AddListener(SubmitButtonOnClickHandler);


            _startTimeDropdown.onValueChanged.AddListener(StartTimeDropdownOnValueChanged);
            _buildNoDropdown.onValueChanged.AddListener(BuildNoDropdownOnValueChanged);

        }

        /*--------------------------------------- Button On Click Handler Start ---------------------------------------*/
        private void Btn1OnClickHandler()
        {
            _selectedMenu = 1;

            _btn1.gameObject.SetActive(false);
            _btn2.gameObject.SetActive(false);
            _btn3.gameObject.SetActive(false);
            _btn4.gameObject.SetActive(false);
            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);

            _dayDropdown.gameObject.SetActive(true);
            _startTimeDropdown.gameObject.SetActive(true);
            _endTimeDropdown.gameObject.SetActive(true);
            _buildNoDropdown.gameObject.SetActive(true);
            _classroomDropdown.gameObject.SetActive(false);

            _dayText.gameObject.SetActive(true);
            _startTimeText.gameObject.SetActive(true);
            _endTimeText.gameObject.SetActive(true);
            _buildNoText.gameObject.SetActive(true);
            _classroomText.gameObject.SetActive(false);
        }

        private void Btn2OnClickHandler()
        {
            _selectedMenu = 2;

            _btn1.gameObject.SetActive(false);
            _btn2.gameObject.SetActive(false);
            _btn3.gameObject.SetActive(false);
            _btn4.gameObject.SetActive(false);
            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);

            _dayDropdown.gameObject.SetActive(true);
            _startTimeDropdown.gameObject.SetActive(false);
            _endTimeDropdown.gameObject.SetActive(false);
            _buildNoDropdown.gameObject.SetActive(true);
            _classroomDropdown.gameObject.SetActive(true);

            _dayText.gameObject.SetActive(true);
            _startTimeText.gameObject.SetActive(false);
            _endTimeText.gameObject.SetActive(false);
            _buildNoText.gameObject.SetActive(true);
            _classroomText.gameObject.SetActive(true);
        }

        private void Btn3OnClickHandler()
        {
            _selectedMenu = 3;

            _btn1.gameObject.SetActive(false);
            _btn2.gameObject.SetActive(false);
            _btn3.gameObject.SetActive(false);
            _btn4.gameObject.SetActive(false);
            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);

            _dayDropdown.gameObject.SetActive(true);
            _startTimeDropdown.gameObject.SetActive(false);
            _endTimeDropdown.gameObject.SetActive(false);
            _buildNoDropdown.gameObject.SetActive(true);
            _classroomDropdown.gameObject.SetActive(true);

            _dayText.gameObject.SetActive(true);
            _startTimeText.gameObject.SetActive(false);
            _endTimeText.gameObject.SetActive(false);
            _buildNoText.gameObject.SetActive(true);
            _classroomText.gameObject.SetActive(true);
        }

        private void Btn4OnClickHandler()
        {
            _selectedMenu = 4;

            _btn1.gameObject.SetActive(false);
            _btn2.gameObject.SetActive(false);
            _btn3.gameObject.SetActive(false);
            _btn4.gameObject.SetActive(false);
            _returnButton.gameObject.SetActive(true);
            _submitButton.gameObject.SetActive(true);

            _dayDropdown.gameObject.SetActive(true);
            _startTimeDropdown.gameObject.SetActive(false);
            _endTimeDropdown.gameObject.SetActive(false);
            _buildNoDropdown.gameObject.SetActive(true);
            _classroomDropdown.gameObject.SetActive(true);

            _dayText.gameObject.SetActive(true);
            _startTimeText.gameObject.SetActive(false);
            _endTimeText.gameObject.SetActive(false);
            _buildNoText.gameObject.SetActive(true);
            _classroomText.gameObject.SetActive(true);
        }

        private void ReturnButtonOnClickHandler()
        {
            _selectedMenu = 0;

            _btn1.gameObject.SetActive(true);
            _btn2.gameObject.SetActive(true);
            _btn3.gameObject.SetActive(true);
            _btn4.gameObject.SetActive(true);
            _returnButton.gameObject.SetActive(false);
            _submitButton.gameObject.SetActive(false);

            _dayDropdown.gameObject.SetActive(false);
            _startTimeDropdown.gameObject.SetActive(false);
            _endTimeDropdown.gameObject.SetActive(false);
            _buildNoDropdown.gameObject.SetActive(false);
            _classroomDropdown.gameObject.SetActive(false);

            _dayText.gameObject.SetActive(false);
            _startTimeText.gameObject.SetActive(false);
            _endTimeText.gameObject.SetActive(false);
            _buildNoText.gameObject.SetActive(false);
            _classroomText.gameObject.SetActive(false);
        }

        private void SubmitButtonOnClickHandler()
        {
            switch (_selectedMenu)
            {
                case 1:
                    break;
                case 2:
                    StartFindEmptyRoom();
                    break;
                case 3:
                    break;
                case 4:
                    break;
            }
        }
        /*--------------------------------------- Button On Click Handler End ---------------------------------------*/

        /*--------------------------------------- Dropdown On Value Changed Start ---------------------------------------*/

        private void StartTimeDropdownOnValueChanged(int value)
        {
            _endTimeDropdown.ClearOptions();

            for (int i = value + 1; i < Enum.GetNames(typeof(InuBooster.Enumerators.Time)).Length; i++)
            {
                _endTimeDropdown.options.Add(new Dropdown.OptionData(((InuBooster.Enumerators.Time)i + 9).ToString().Split('_')[1]));
            }
        }

        private void BuildNoDropdownOnValueChanged(int value)
        {
            Debug.Log(_buildNoDropdown.options[_buildNoDropdown.value].text);
            string buildingCode = _buildNoDropdown.options[_buildNoDropdown.value].text.Split(')')[0].Split('(')[1].Trim();
            InitializeClassroomDropbox(buildingCode);
        }
        /*--------------------------------------- Dropdown On Value Changed End ---------------------------------------*/


        /*--------------------------------------- Coroutine Method Start ---------------------------------------*/

        private void InitializeBuildingDropbox()
        {
            StartCoroutine(GetBuildingDropdownItem());
        }

        private void InitializeClassroomDropbox(string buildingCode)
        {
            StartCoroutine(GetClassroomDropdownItem(buildingCode));
        }
        private void StartFindEmptyRoom()
        {
            StartCoroutine(FindEmptyRoom());
        }
        /*--------------------------------------- Coroutine Method End ---------------------------------------*/

        /*--------------------------------------- DB Access Method Start ---------------------------------------*/

        private IEnumerator GetBuildingDropdownItem()
        {
            WWWForm form = new WWWForm();

            WWW www = new WWW("http://13.209.233.253//selectbuildinglist.php", form);
            yield return www;
            string result = www.text;
            string[] resultArray = result.Split('^');

            _buildNoDropdown.ClearOptions();
            for (int i = 0; i < resultArray.Length; i++)
            {
                _buildNoDropdown.options.Add(new Dropdown.OptionData(resultArray[i]));
            }
        }

        private IEnumerator GetClassroomDropdownItem(string buildingCode)
        {
            WWWForm form = new WWWForm();
            Debug.Log(buildingCode);
            form.AddField("building", buildingCode);

            WWW www = new WWW("http://13.209.233.253//nametoroom.php", form);
            yield return www;
            string result = www.text;
            string[] resultArray = result.Split(',');
            Debug.Log(result);

            _classroomDropdown.ClearOptions();
            for (int i = 0; i < resultArray.Length; i++)
            {
                _classroomDropdown.options.Add(new Dropdown.OptionData(resultArray[i]));
            }
        }

        private IEnumerator FindEmptyRoom()
        {
            Enum.TryParse(_dayDropdown.options[_dayDropdown.value].text, out InuBooster.Enumerators.DayOfTheWeek myStatus);
            Debug.Log(myStatus.ToString());
            string building_name = _buildNoDropdown.options[_buildNoDropdown.value].text.Split(')')[0].Split('(')[1].Trim();
            Debug.Log(building_name);
            string class_name = building_name + _classroomDropdown.options[_classroomDropdown.value].text;
            Debug.Log(class_name);

            WWWForm form = new WWWForm();
            form.AddField("day", myStatus.ToString());
            form.AddField("building", building_name);
            form.AddField("class", class_name);

            WWW www = new WWW("http://13.209.233.253//threetolec.php", form);
            yield return www;
            string result = www.text;
            Debug.Log(result);
        }
        /*--------------------------------------- DB Access Method End ---------------------------------------*/

    }
}