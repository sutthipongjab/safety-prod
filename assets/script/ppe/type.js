import $ from "jquery";
import {showMessage} from "../utils.js";
$(document).ready(function () {
  var host = window.location.origin + "/safety/";
  console.log(host);
  $.ajax({
    type: "GET",
    url: host + "ppe/admin/products/getType",
    dataType: "JSON",
    success: function (response) {
      console.log(response);
      $("#category").append(`<option value="">เลือกหมวดหมู่</option>`);
      response.forEach((item) => {
        $("#category").append(`<option value="${item.CATID}">${item.CATNAME}</option>`);
      });
    },
  });

  //   $(document).on("click", ".mainmenu", function () {
  //     // console.log($(this).closest("ul"));
  //     var details = $(this).find("details").css({"color": "red", "border": "2px solid red"});
  //     console.log(details);

  //     details.removeAttr('open');
  //     // if (details) {
  //     //     console.log($(details).attr("open"));
  //     //     if(details.open){
  //     //         details.open = !details.open;
  //     //     }else{
  //     //         details.open = true;
  //     //     }
  //     // }
  //   });

  $("details").on("toggle", function () {
    if (this.open) {
      $("details").not(this).removeAttr("open");
    }
  });

  $("form").on("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (data) {
        showMessage("Insert Data Successfully!!", "success");
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
      },
    });
  });
});
