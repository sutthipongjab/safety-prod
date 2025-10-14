import $ from "jquery";
import * as my from "../utils.js";
import * as form from "../_form.js";
import select2 from "select2";
import "select2/dist/css/select2.min.css";
var NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, empno, cextData, mode;
$(document).ready(async function () {
  NFRMNO = $(".formno").attr("NFRMNO");
  VORGNO = $(".formno").attr("VORGNO");
  CYEAR = $(".formno").attr("CYEAR");
  CYEAR2 = $(".formno").attr("CYEAR2");
  NRUNNO = $(".formno").attr("NRUNNO");
  empno = $(".user-data").attr("empno");
  cextData = $(".user-data").attr("cextData");
  mode = $(".user-data").attr("mode");
  if (mode == "2") {
    $("#remark-text").removeClass("hidden");
    $("#actions-Form").removeClass("hidden");
  } else {
    $("#remark-text").addClass("hidden");
    $("#actions-Form").addClass("hidden");
  }
  const flow = await form.showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
  $("#flow").html(flow.html);
});

/**
 * action form  approve, reject form
 */
$(document).on("click", "button[name='btnAction']", async function () {
  const action = $(this).val();
  const remark = $("#remark").val();
  // console.log('approve',action);

  // console.log(action, remark, empno);
  /*if(action == 'approve'){
        switch (cextData) {
            case '01':
                const cor = await correctiveAction();
                if(cor) return;
                break;
            case '02':
                const dcor = await correctiveDetail();
                console.log(dcor);

                if(dcor) return;

                break;
            case '03':
                const eva = await evaluate();
                if(eva) return;
                break;

            default:
                break;
        }
    }*/

  const formStatus = await form.doaction(
    NFRMNO,
    VORGNO,
    CYEAR,
    CYEAR2,
    NRUNNO,
    action,
    empno,
    remark
  );

  const path = window.location.host.includes("amecwebtest")
    ? "formtest"
    : "form";
  const redirectUrl = `http://webflow.mitsubishielevatorasia.co.th/${path}/workflow/WaitApv.asp`;
//   console.log(redirectUrl);

  if (formStatus.status == true) {
    window.location = redirectUrl;
  } else {
    my.showMessage("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");
    // my.sendmail(`KYT Form Error :: do action ${JSON.stringify(formStatus)}`);
    const mail = {...mailOpt};
    mail.BODY = [
        my.mailForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO,'Do action'),
        `KYT Form Error : do action ${JSON.stringify(formStatus)}`
    ];
    my.sendMail(mail);
  }
});
