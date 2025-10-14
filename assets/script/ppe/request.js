import $ from "jquery";
import { createForm, deleteForm, showFlow } from "../_form.js";
var host = window.location.origin + "/safety/";

$(document).ready(function () {
  updateCartUI();
  setReportTable();
  getReason();
  fetchCategories();

  function fetchCategories() {
    $.ajax({
      type: "GET",
      url: host + "ppe/form/request/get_category",
      dataType: "JSON",
      success: function (response) {
        let cards = "";
        $.each(response, function (index, item) {
          if (item.CATANNUAL == 1) {
            cards += createCard(item);
          }
        });
        $("#card-container").html(cards);
        $.each(response, function (index, item) {
          getSizesForProduct(item.CATID);
        });
      },
    });
  }

  function createCard(item) {
    // console.log(item);
    return `
      <div class="card bg-base-100 shadow-xl my-1 flex flex-col">
        <figure class="p-2 h-40 w-full flex items-center justify-center bg-gray-100">
          <img class="h-full w-full object-contain" src="${item.CATIMAGE}" alt="${item.CATIMAGE}" />
        </figure>
        <div class="card-body flex flex-col flex-grow">
          <h2 class="card-title">${item.CATNAME}</h2>
          <p class="flex-grow">${item.CATDESC}</p>
          <div class="flex flex-col gap-2">
            <label for="size-${item.CATID}">เลือกขนาด/ประเภท</label>
            <div role="alert" id="alert-${item.CATID}" class="alert alert-error hidden">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>กรุณาเลือก !</span>
            </div>
            <select id="size-${item.CATID}" class="size-select select select-bordered"></select>
          </div>
          <div class="card-actions justify-between">
            <span style="color:red; max-width: 150px;">${item.CATREMARK === null ? "" : item.CATREMARK}</span>
            <button class="btn btn-primary add-to-cart" data-id="${item.CATID}" data-name="${item.CATNAME}" data-desc="${item.CATDESC}">เพิ่มลงตระกร้า</button>
          </div>
        </div>
      </div>
    `;
  }

  function setReportTable() {
    let data = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    let table = $("#report-table tbody");
    table.empty();
    data.forEach((item, index) => {
      table.append(`
        <tr>
          <td class="border px-4 py-2">${index + 1}</td>
          <td class="border px-4 py-2">${item.desc}</td>
          <td class="border px-4 py-2">${item.name}</td>
          <td class="border px-4 py-2">${item.size}</td>
          <td class="border px-4 py-2">${item.quantity}</td>
          <td class="border px-4 py-2"></td>
          <td class="border px-4 py-2">${item.reason}</td>
        </tr>
      `);
    });
  }

  function getSizesForProduct(catId) {
    $.ajax({
      type: "POST",
      url: host + "ppe/form/request/get_uniform_by_cat",
      data: { CATID: catId },
      dataType: "JSON",
      success: function (sizeResponse) {
        let sizeOptions = `<option value="">กรุณาเลือกขนาด/ประเภท</option>`;
        $.each(sizeResponse, function (sizeIndex, size) {
          const disabled = (size.PROD_REMAIN - size.PROD_ALOC) <= 0 ? "disabled style='background-color:#fcc6c6;'" : "";
          if (size.PROD_STATUS == 1) {
            sizeOptions += `<option value="${size.PROD_ID}"${disabled}>${size.PROD_SIZES}</option>`;
          }
        });
        $(`#size-${catId}`).html(sizeOptions);
      },
    });
  }

  $(document).on("click", ".add-to-cart", function () {
    const id = $(this).data("id");
    const name = $(this).data("name");
    const sizeid = $("#size-" + id).val();
    const size = $("#size-" + id + " option:selected").text();
    const desc = $(this).data("desc");

    if (!sizeid) {
      $("#alert-" + id).removeClass("hidden");
      $("#size-" + id).addClass("select-error");
      setTimeout(function () {
        $("#alert-" + id).addClass("hidden");
        $("#size-" + id).removeClass("select-error");
      }, 3000);
      return;
    }

    let cartItem = {
      id: id,
      sizeid: sizeid,
      desc: desc,
      name: name,
      size: size,
      quantity: "1",
      reason: "",
      reasonID: "",
    };

    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    const existingItemIndex = cart.findIndex((item) => item.id === id && item.sizeid === sizeid);

    if (existingItemIndex > -1) {
      cart[existingItemIndex].quantity = parseInt(cart[existingItemIndex].quantity) + 1;
    } else {
      cart.push(cartItem);
    }
    sessionStorage.setItem("ppe_cart", JSON.stringify(cart));
    updateCartUI();
  });

  function updateCartUI() {
    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    const cartContainer = $("#cart-list");

    cartContainer.empty();

    if (cart.length === 0) {
      cartContainer.append(`<li class="text-center text-gray-500 py-4">ไม่มีสินค้าในตะกร้า</li>`);
      return;
    }

    cart.forEach((item, index) => {
      const itemElement = $(`
        <li class="flex flex-col justify-between border-b p-4 my-3 hover:bg-gray-50 transition-colors duration-200">
          <div class="flex justify-between items-center mb-2">
            <span class="flex-grow text-gray-700 font-medium mr-4">${item.name} (${item.size})</span>
          </div>
          <div class="flex justify-between items-center">
            
            <span class="text-gray-500 font-medium" id="stock_${item.sizeid}"></span>
            <button class="remove-btn ml-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors duration-200" data-index="${index}">ลบ</button>
          </div>
          <div class="mt-2">
            <select class="w-full select select-bordered select-reason" data-index="${index}">
              <option value="">กรุณาเลือกเหตุผลการเบิก</option>
            </select>
          </div>
        </li>
      `);
      get_stock(item.sizeid);
      const data = JSON.parse(sessionStorage.getItem("reason")) || [];
      const select = itemElement.find("select");
      data.forEach((option) => {
        select.append(`<option value="${option.RT_ID}" ${item.reasonID == option.RT_ID ? "selected" : ""}>${option.RT_DETAIL}</option>`);
      });

      cartContainer.append(itemElement);
    });
  }

  function get_stock(id) {
    $.ajax({
      type: "POST",
      url: host + "ppe/form/request/get_uniform",
      data: { PROD_ID: id },
      dataType: "JSON",
      success: function (response) {
        // console.log(response);
        $("#stock_" + id).text("จำนวนคงเหลือ " + response[0].PROD_REMAIN);
      },
    });
  }

  $(document).on("click", ".increase-btn", function () {
    const index = $(this).data("index");
    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    cart[index].quantity = parseInt(cart[index].quantity) + 1;
    sessionStorage.setItem("ppe_cart", JSON.stringify(cart));
    updateCartUI();
  });

  $(document).on("click", ".decrease-btn", function () {
    const index = $(this).data("index");
    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    if (cart[index].quantity > 1) {
      cart[index].quantity -= 1;
      sessionStorage.setItem("ppe_cart", JSON.stringify(cart));
      updateCartUI();
    }
  });

  $(document).on("change", ".select-reason", function () {
    const index = $(this).data("index");
    const reason = $(this).find("option:selected").text();
    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    cart[index].reasonID = $(this).val();
    cart[index].reason = reason;
    sessionStorage.setItem("ppe_cart", JSON.stringify(cart));
    updateCartUI();
  });

  $(document).on("click", ".remove-btn", function () {
    const index = $(this).data("index");
    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    cart.splice(index, 1);
    sessionStorage.setItem("ppe_cart", JSON.stringify(cart));
    updateCartUI();
  });

  $(document).on("keyup", "#employee-id", function () {
    $("#name").val("");
    $("#department").val("");
    $("#submit_form").prop("disabled", true);
    if ($(this).val().length == 5) {
      $.ajax({
        type: "POST",
        url: host + "ppe/form/request/get_data_user",
        data: { empno: $(this).val() },
        dataType: "JSON",
        success: function (response) {
          $("#name").val(response[0].SEMPPRT + " " + response[0].STNAME);
          $("#department").val(response[0].SDEPT);
          $("#submit_form").prop("disabled", false);
        },
      });
    }
  });

  $("#checkout").click(function () {
    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    if (cart.length == 0) {
      alert("กรุณาเลือกสินค้าก่อนทำการเบิก");
      return;
    }

    for (let item of cart) {
      if (!item.reason || !item.reasonID) {
        alert("กรุณาเลือกเหตุผลการเบิกสำหรับสินค้าทุกชิ้น");
        return;
      }
    }

    window.location.href = host + "ppe/form/request/report";
  });

  $("#submit_form").click(async function (e) {
    e.preventDefault();
    const req = $("#employee-id").val();
    const key = $("#key").val();
    const remark = $("#remark").val();
    const NFRMNO = $(".form-mst").data("nfrmno");
    const VORGNO = $(".form-mst").data("vorgno");
    const CYEAR = $(".form-mst").data("cyear");
    const data = [];

    let cart = JSON.parse(sessionStorage.getItem("ppe_cart")) || [];
    cart.forEach(function (row) {
      const category = row.id;
      const product = row.sizeid;
      const size = row.sizeid;
      const remain = row.quantity;
      const qty = row.quantity;
      const request_type = row.reasonID;

      if (category && product && size && remain && qty && request_type) {
        data.push({ category, product, size, remain, qty, request_type });
      }
    });

    if (data.length == 0) {
      alert("กรุณาเลือกรายการอุปกรณ์ที่ต้องการเบิก");
      return;
    }

    const res = await createForm(NFRMNO, VORGNO, CYEAR, req, key, remark);
    if (res.status === true) {
      $.ajax({
        type: "POST",
        url: host + "ppe/form/request/insert_request",
        data: {
          data,
          NFRMNO,
          VORGNO,
          CYEAR,
          CYEAR2: res.message.cyear2,
          NRUNNO: res.message.runno,
          req,
          key,
        },
        success: function () {
          sessionStorage.removeItem("ppe_cart");
          window.location.href = host + "ppe/form/request/";
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.error("Error inserting product: ", textStatus, errorThrown);
        },
      });

      alert("บันทึกข้อมูลเรียบร้อย");
    }
  });

  $("#addRow").click(function () {
    let newRow = $("#tableBody tr:first").clone();
    newRow.find("input, select").val("");
    newRow.find(".remain").text("");
    $("#tableBody").append(newRow);
  });

  function getReason() {
    $.getJSON(host + "ppe/form/request/get_reason", function (data) {
      sessionStorage.setItem("reason", JSON.stringify(data));
    });
  }
});
