import $, { ajax } from "jquery";
import { createForm, deleteForm, showFlow, doaction } from "../_form.js";

const host = window.location.origin + "/safety/";

$(document).ready(async function () {
  const rowCount = $("tr.edit-row").length;
  console.log("Total Rows:", rowCount);

  const { NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, EMPNO } = getFormData();

  const flow = await showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
  $("#flow").html(flow.html);

  const jobController = await getEmpFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, "19");
  console.log(jobController);

  await handleCategoryAndSize();

  $(".apv_btn").click(async function () {
    const action = $(this).data("act");
    const remark = $("#remark").val();
    let isValid = true;
    let formData = [];
    $("tr.edit-row").each(function () {
      let qty = $(this).find(".qty").val();
      let category = $(this).find(".category").val();
      let size = $(this).find(".size").val();
      let reason = $(this).find(".reason").val();
      let urd_id = $(this).find(".urd-id").val();

      formData.push({
        qty: qty,
        category: category,
        size: size,
        reason: reason,
        id: urd_id,
      });

      if (!qty || !category || !size || !reason) {
        isValid = false;
        $(this).addClass("bg-red-100");
      } else {
        $(this).removeClass("bg-red-100");
      }
    });

    if (!isValid) {
      alert("กรุณากรอกข้อมูลให้ครบถ้วน!");
      return;
    }

    const result = await doaction(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, action, EMPNO, remark);

    if (jobController.length > 0 && action == "approve") {
      $.ajax({
        type: "POST",
        url: host + "ppe/form/webflow/update_request",
        data: { formData },
        success: function (response) {
          console.log(response);
        },
      });
      await updateConfirmation(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO);
    } else if (action == "reject") {
      $.ajax({
        type: "POST",
        url: host + "ppe/form/webflow/update_confirm",
        data: {
          NFRMNO: NFRMNO,
          VORGNO: VORGNO,
          CYEAR: CYEAR,
          CYEAR2: CYEAR2,
          NRUNNO: NRUNNO,
          CONFIRMED: "0",
        },
        success: function () {},
      });
    }

    handleRedirect();
    console.log(result);
  });

  $("#submit_edit").click(function () {
    let formData = [];
    let isValid = true;
    $("tr.edit-row").each(function () {
      let qty = $(this).find(".qty").val();
      let category = $(this).find(".category").val();
      let size = $(this).find(".size").val();
      let reason = $(this).find(".reason").val();
      let urd_id = $(this).find(".urd-id").val();

      formData.push({
        qty: qty,
        category: category,
        size: size,
        reason: reason,
        id: urd_id,
      });

      if (!qty || !category || !size || !reason) {
        isValid = false;
        $(this).addClass("bg-red-100");
      } else {
        $(this).removeClass("bg-red-100");
      }
    });

    if (!isValid) {
      alert("กรุณากรอกข้อมูลให้ครบถ้วน!");
      return;
    }
    
    $.ajax({
      type: "POST",
      url: host + "ppe/form/webflow/update_request",
      data: { formData },
      success: function (response) {
        // console.log(response);
        alert("บันทึกสำเร็จ");
        window.close();
      },
    });
  });

  $(".remark_btn").click(function () {
    const remark = $("#remark").val();
    if (!remark) {
      alert("กรุณากรอกข้อมูลให้ครบถ้วน!");
      return;
    }
    let formData = [];
    formData.push({
      remark: remark,
      NFRMNO: NFRMNO,
      VORGNO: VORGNO,
      CYEAR: CYEAR,
      CYEAR2: CYEAR2,
      NRUNNO: NRUNNO,
    });

    console.log("Form Data:", formData);
    $.ajax({
      type: "POST",
      url: host + "ppe/form/webflow/update_remark",
      data: { formData },
      success: function (response) {
        window.close();
      },
    });
  });
});

function getFormData() {
  return {
    NFRMNO: $(".fmst").data("nfrmno"),
    VORGNO: $(".fmst").data("vorgno"),
    CYEAR: $(".fmst").data("cyear"),
    CYEAR2: $(".fmst").data("cyear2"),
    NRUNNO: $(".fmst").data("nrunno"),
    EMPNO: $(".fmst").data("empno"),
  };
}

async function handleCategoryAndSize() {
  $("tr.edit-row").each(async function () {
    let category = $(this).find(".category");
    let cat_val = category.val();
    let size = $(this).find(".size");
    let size_val = size.val();
    let reason = $(this).find(".reason");
    let reason_val = reason.val();

    // ดึงข้อมูลหมวดหมู่และเหตุผลพร้อมกัน
    const [categories, reasons] = await Promise.all([get_category(), get_reason()]);
    updateCategoryOptions(category, categories, cat_val);
    updateReasonOptions(reason, reasons, reason_val); // เพิ่มการอัปเดตเหตุผล

    if (cat_val) {
      await updateSizeOptions(size, cat_val, size_val);
    }

    category.on("change", async function () {
      const new_cat_val = $(this).val();
      await updateSizeOptions(size, new_cat_val);
    });
  });
}

function updateCategoryOptions(categoryElement, categories, selectedValue) {
  categoryElement.empty().append('<option value="">เลือกหมวดหมู่</option>');

  categories.forEach((category) => {
    const selected = category.CATID == selectedValue ? "selected" : "";
    categoryElement.append(`<option value="${category.CATID}" ${selected}>${category.CATNAME}</option>`);
  });
}

async function updateSizeOptions(sizeElement, cat_val, selectedSize = null) {
  sizeElement.empty().append('<option value="">เลือกขนาด</option>');
  const sizes = await getsize(cat_val);

  sizes.forEach((size) => {
    const selected = size.PROD_ID == selectedSize ? "selected" : "";
    sizeElement.append(`<option value="${size.PROD_ID}" ${selected}>${size.PROD_SIZES}</option>`);
  });
}

async function updateReasonOptions(reasonElement, reasons, selectedReason = null) {
  reasonElement.empty().append('<option value="">เลือกเหตุผล</option>');

  reasons.forEach((reason) => {
    console.log(reason);
    console.log(selectedReason);
    const selected = reason.RT_ID == selectedReason ? "selected" : "";
    reasonElement.append(`<option value="${reason.RT_ID}"${selected}>${reason.RT_DETAIL}</option>`);
  });
}

function handleRedirect() {
  if (new URLSearchParams(window.location.search).has("closing")) {
    window.close();
  } else {
    window.location.href = "http://webflow.mitsubishielevatorasia.co.th/form/workflow/WaitApv.asp";
  }
}

async function updateConfirmation(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO) {
  return $.ajax({
    type: "POST",
    url: host + "ppe/form/webflow/update_confirm",
    data: {
      NFRMNO: NFRMNO,
      VORGNO: VORGNO,
      CYEAR: CYEAR,
      CYEAR2: CYEAR2,
      NRUNNO: NRUNNO,
      CONFIRMED: "1",
    },
    success: function () {},
  });
}

function getEmpFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, STEP) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: host + "ppe/form/webflow/getEmpFlow",
      type: "POST",
      dataType: "JSON",
      data: {
        NFRMNO,
        VORGNO,
        CYEAR,
        CYEAR2,
        NRUNNO,
        STEP,
        STATUS: 3,
      },
      success: resolve,
      error: reject,
    });
  });
}

function getsize(id) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: host + "ppe/form/webflow/getSize",
      type: "POST",
      data: { catid: id },
      dataType: "JSON",
      success: resolve,
      error: reject,
    });
  });
}

function get_category() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: host + "ppe/form/webflow/getCategory",
      type: "GET",
      dataType: "JSON",
      success: resolve,
      error: reject,
    });
  });
}

function get_reason() {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: host + "ppe/form/webflow/getReason",
      type: "GET",
      dataType: "JSON",
      success: resolve,
      error: reject,
    });
  });
}
