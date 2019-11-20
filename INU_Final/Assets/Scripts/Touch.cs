using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class Touch : MonoBehaviour
{ 
    public AudioClip voice_02;
    public Animator animator;
    public AudioSource univoice;

    // Start is called before the first frame update
    void Start()
    {
        animator = GetComponent<Animator>();
        univoice = GetComponent<AudioSource>();
    }

    // Update is called once per frame
    void Update()
    {
        animator.SetBool("Touch", false);
        animator.SetBool("TouchHead", false);
        animator.SetBool("Hands", false);

        Ray ray;
        RaycastHit hit;
        GameObject hitObject;

        if(Input.GetMouseButtonDown(0))
        {
            //마우스 커서위치에서 카메라 화면 안쪽을 향해 레이를 쏜다
            ray = Camera.main.ScreenPointToRay(Input.mousePosition);

            if(Physics.Raycast(ray, out hit, 100))
            {
                hitObject = hit.collider.gameObject;
                if(hitObject.gameObject.tag == "Head")
                {
                    animator.SetBool("TouchHead", true);

                }
                else if (hitObject.gameObject.tag == "Breast")
                {
                    animator.SetBool("Touch", true);
                    univoice.clip = voice_02;
                    univoice.Play();
                }
                else if (hitObject.gameObject.tag == "Hands")
                {
                    animator.SetBool("Hands", true);

                }

            }
        }
    }
}
