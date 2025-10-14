import $ from "jquery";
import * as my from "./../utils.js";
import * as form from "./../_form.js";
import select2 from "select2";
import "select2/dist/css/select2.min.css";

$(document).ready(async function () {
  const s2opt = { ...my.select2Option };
  s2opt.placeholder = "เลือกแผนก";
  $("#sec").select2(s2opt);
  s2opt.placeholder = "เลือกหมวดหมู่ความเสี่ยงที่พบ";
  $("#risk").select2(s2opt);
});

/**
 *  Select2 focus
 */
$(document).on("select2:open", function (e) {
  setTimeout(function () {
    const searchField = document.querySelector(".select2-search__field");
    if (searchField) {
      searchField.focus();
    } else {
      console.warn("Search field not found.");
    }
  }, 100);
});

// /**
//  * Required select2
//  */
var flagSelect = false;
$(document).on("change", "select.req", async function () {
  const select = $(this);
  // my.RequiredElement(select);

  if (flagSelect) {
    // หาก trigger มาจากโปรแกรม ไม่ต้องทำอะไร
    flagSelect = false;
    return;
  }
  my.RequiredElement(select);
});

/**
 * Request
 */
$(document).on("click", "#req-kyt", async function () {
  const btn = $(this);
  const frm = $("#kyt-form");
  let checkVal = true;
  btn.addClass("loaded");
  btn.find(".loading").removeClass("hidden");

  frm.find(".req").map(function (i, el) {
    console.log(el);

    if ($(el).val() == "") {
      checkVal = false;
      // if ($(el).is("select")) {
      my.RequiredElement($(el));
      // } else {
      //     $(el).addClass('input-error');
      // }
    }
  });

  if (!checkVal) {
    my.showMessage("กรุณากรอกข้อมูลให้ครบถ้วน", 'warning');
    btn.removeClass("loaded");
    btn.find(".loading").addClass("hidden");
    return false;
  }

  var formData = new FormData(frm[0]);

  //ตรวจสอบข้อมูล
  for (var pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
  }

  const NFRMNO = $(".form-info").attr("NFRMNO");
  const VORGNO = $(".form-info").attr("VORGNO");
  const CYEAR = $(".form-info").attr("CYEAR");
  const userno = $(".form-info").attr("userno");
  const reqno = $("#empno").val();
  // formData.append('NFRMNO', NFRMNO);
  // formData.append('VORGNO', VORGNO);
  // formData.append('CYEAR', CYEAR);
  formData.append("userno", userno);

  const formInfo = await form.createForm(NFRMNO, VORGNO, CYEAR, reqno, userno);

  for (const key in formInfo.message) {
    formData.append(key, formInfo.message[key]);
    // console.log(key + "=>>>>>>>" + formInfo.message[key]);
  }

  //console.log(formData);

  const item = await save(formData);
  if (item.status == true) {
    //itemstable = await createTable(item.data);
    my.showMessage("บันทึกข้อมูลสำเร็จ", "success");
    // window.location.href = `${my.host}kyt/follow`;
    frm[0].reset();
    $('#sec').val('').trigger('change');
    $('#risk').val('').trigger('change');
    my.removeClassError($('#sec'));
    my.removeClassError($('#risk'));
  } else {
    my.showMessage(item.message, "error");
  }

  btn.removeClass("loaded");
  btn.find(".loading").addClass("hidden");
  //$("#drawer-item").prop("checked", false);
});

/**
 * Back to
 */
$(document).on("click", "#cancel-kyt", function () {
  $("#kyt-form")[0].reset();
  // window.location.href = `${my.host}patrol/inspection`;
  //window.history.back();
});

/**
 * Save KYT Form
 * @param {array} data
 * @returns
 */
function save(data) {
  // console.log('data', data);
  return new Promise((resolve) => {
    $.ajax({
      url: `${my.host}kyt/Form/savekytfrm`,
      type: "post",
      dataType: "json",
      processData: false,
      contentType: false,
      data: data,
      beforeSend: function () {
        my.showpreload();
      },
      success: function (res) {
        resolve(res);
      },
      complete: function (xhr, status) {
        my.checkAuthen(xhr, status);
        my.hidepreload();
      },
    });
  });
}
