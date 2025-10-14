import $ from "jquery";
import {RequiredElement, showpreloader, thaionly, engonly, closeModal, scheme, showpreload, hidepreload} from "./utils.js";
const version = $("meta[name=app_version]").attr("content");
const at = {};
if($('.cmenu').attr('count') == 0){
    // $('#overlay').addClass('hidden');
    $('#mainMenu').addClass('hidden');
    $('#contents').removeClass('md:ml-64  lg:ml-80');
    $('#sidebar').removeClass('sm:w-64  lg:w-80').find('div').removeClass('px-3');
}
// showpreload();
$(document).ready(function () {
    const title = $('body').attr('menuTitle');
    if(title){
        const safety = $(`.${title}`);
        const detail = safety.closest('details');
        detail.prop('open',true);
        safety.addClass('bg-active rounded-md');
        
        const menuName = safety.find('.menu-name');
        // console.log('menu name : ',menuName ,title, detail, safety);
        
        $('#topic').text(menuName.text());
    }
    // hidepreload();
});

// async function setAuthen(id, data) {
//     const db = await getDatabaseInstance();
//     return new Promise((resolve, reject) => {
//       const transaction = db.transaction("authen", "readwrite");
//       const store = transaction.objectStore("authen");
//       const request = store.put({ id: id, data: data });
//       request.onsuccess = () => resolve("Links created successfully");
//       request.onerror = () => reject("Failed to create user");
//     });
// };

// /**
//  * Side menu
//  * ---------------------------------------
//  */
// $(document).on("click", ".mainmenu", function () {
//     const m = $(".mainmenu").length;
//     // console.log(m);
//     // console.log($(".mainmenu"));
    
//     $(".mainmenu").map((i, el) => {
//         $(el).find("details").removeAttr("open");
//     });
//     // const details = $(this);
//     // $(".mainmenu details").not(details).removeAttr("open");
    
// });

$(document).on("click", ".msg-close", function (e) {
    $(".alert-message").removeClass("opacity-100");
    $(".alert-message").addClass("opacity-0");
    setTimeout(() => {
      $(".alert-message").remove();
    }, 700);
  });

$(document).on('click', '#togglebar', function(){
    toggleSidebar();
});
$(document).on('click', '#overlay', function(){
    toggleSidebar();
});

function toggleSidebar(){
    const sidebar = $("#sidebar");
    const overlay = $("#overlay");
    // Toggle sidebar visibility
    if (sidebar.hasClass("-translate-x-0")) {
        sidebar.removeClass("-translate-x-0");
        sidebar.addClass("-translate-x-full");
        overlay.addClass("hidden");
    } else {
        sidebar.addClass("-translate-x-0");
        sidebar.removeClass("-translate-x-full");
        overlay.removeClass("hidden");
    }
}

//------------------------------------------

/**
 * Required input
 */
$(document).on('input blur', '.req', function(){
    // console.log($(this));
    
    RequiredElement($(this));
    // if($(this).val() != ''){
    //     $(this).removeClass('input-error');
    // }else{
    //     $(this).addClass('input-error');
    // }
});

$(document).on('click', '.menu-name', function(){
    showpreloader();
});

/**
 * thai only
 */
$(document).on('input', ".th-only", function(){
    const val = $(this).val();
    if(val == ''){return;}
    thaionly($(this));
});

/**
 * english only
 */
$(document).on('input', ".en-only", function(){
    const val = $(this).val();
    if(val == ''){return;}
    engonly($(this));
});

/**
 * Close modal
 */
$(document).on('click', '#closeModal', function(){
    const dialog = $(this).closest('dialog');
    const id = dialog.attr('id');
    closeModal(`#${id}`);
});

/**
 * Set local stored
 */
//Delete Cache
const lversion = localStorage.getItem("version")||null;
if (lversion === null || lversion != version) {
  localStorage.removeItem("dayoff");
  localStorage.removeItem("schedule");
  localStorage.setItem("version", version);
}
//Setting Dayoff
if (
  localStorage.getItem("dayoff") === null ||
  localStorage.getItem("schedule") === null
) {
  getameccalendar();
} else {
  const itemStr = localStorage.getItem("dayoff");
  const item = JSON.parse(itemStr);
  const now = new Date();
  if (
    now.getTime() > item.expiry ||
    item.version === undefined ||
    item.version < 240128
  ) {
    localStorage.removeItem("dayoff");
    localStorage.removeItem("schedule");
    getameccalendar();
  }
}


function getameccalendar() {
    var today = new Date();
    var sdate = today.getFullYear() - 1 + "-01-01";
    var edate = today.getFullYear() + 1 + "-12-31";
    var dayoff = [];
    //var schedule = [];
    let calenda = [];
    let url = `${scheme}://amecweb1.mitsubishielevatorasia.co.th/`;
    url += "webservice/api/calendar/getcalendarrange";
    $.ajax({
      url: url,
      type: "post",
      dataType: "json",
      data: { sdate: sdate, edate: edate },
      async: false,
      success: function (res) {
        res.map(function (data) {
          //console.log(data);
          var schd = {
            WORKID: data.WORKID,
            MFGSCHD: data.SCHDMFG,
            MFGSCHDNUM: data.SCHDNUMBER,
            MFGSCHDP: data.PRIORITY,
            MFGFEEDER1: data.FEEDER1,
            MFGFEEDER2: data.FEEDER1,
            MFGSUBASSY: data.SUBASSY_FINISH,
            MFGASSY: data.ASSY_FINISH,
            MFGPACKING: data.PACKING,
            DAYOFF: data.DAYOFF,
            WORKNUM: data.WORKNUM,
          };
          calenda.push(schd);
          if (data.DAYOFF == 1)
            dayoff.push(
              data.WORKYEAR + "-" + data.WORKMONTH + "-" + data.WORKDAY
            );
        });
  
        const item_dayoff = {
          value: dayoff,
          version: 240128,
          expiry: today.getTime() + 7889400000,
        };
        const item_schedule = {
          value: calenda,
          version: 240128,
          expiry: today.getTime() + 7889400000,
        };
        localStorage.setItem("dayoff", JSON.stringify(item_dayoff));
        localStorage.setItem("schedule", JSON.stringify(item_schedule));
      },
    });
  }

