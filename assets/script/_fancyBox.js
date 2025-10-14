// import $ from "jquery";
import { Fancybox } from "@fancyapps/ui";
import { Carousel } from "@fancyapps/ui/dist/carousel/carousel.esm.js";
import { Autoplay } from "@fancyapps/ui/dist/carousel/carousel.autoplay.esm.js";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import "@fancyapps/ui/dist/carousel/carousel.css";
import "@fancyapps/ui/dist/carousel/carousel.autoplay.css";
import { checkFileFormat, fileImgFormat } from "./_file";
import { ajaxOptions, getData, host, showMessage } from "./utils";

/**
 * Open image management dialog
 * @param {object} table dataTable 
 * @param {object} e     element
 * @param {callbackfunction}
 */
export async function openImgDialog(){
    $('#fancyDialog').removeClass('hidden');
    $('#formAddFancyImage').addClass('hidden').removeClass('flex');
    $('#showFancyImgForm').removeClass('hidden');
    Fancybox.show([{
        src: "#fancyDialog",
        type: "inline"
    }]);
}

export async function setFancyImage(images, herderName = 'จัดการภาพ'){
    let container = $("#fancyContainer");
    let navFancy  = $("#navFancy");
    container.empty(); // ล้างภาพเดิมออก
    navFancy.empty(); // ล้างภาพเดิมออก

    if (images.length > 0) {
        $('#fancyDialog').addClass('w-fit').removeClass('w-11/12');
        $('.FancyHeader').html(herderName);

        for (const img of images) {
            // let imageTag = `
            //     <a href="${img.base64}" class="relative f-carousel__slide flex justify-center items-center" data-fancybox="gallery">
            //         <img src="${img.base64}"  class="max-w-[90vw] max-h-[65vh]"
            //              ${img.attr}">
                         
            //         <button class="deleteFancyImage absolute top-1 right-1 btn btn-error"
            //            ${img.attr} >
            //             <i class="icofont-trash p-1"></i>
            //         </button>
            //     </a>
            // `;
            // let navTag = `
            //     <a href="${img.base64}" class="relative f-carousel__slide flex justify-center items-center" data-fancybox="nav">
            //         <img src="${img.base64}"  class="w-24">
            //     </a>
            // `;
            let imageTag = `
                <div href="${img.base64}" class="relative f-carousel__slide flex justify-center items-center" data-fancybox="gallery">
                    <img src="${img.base64}"  class="max-w-[90vw] max-h-[65vh]"
                         ${img.attr}">
                         
                    <button class="deleteFancyImage absolute top-1 right-1 btn btn-error"
                       ${img.attr} >
                        <i class="icofont-trash p-1"></i>
                    </button>
                </div>
            `;
            let navTag = `
                <div class="relative f-carousel__slide flex justify-center items-center" data-fancybox="nav">
                    <img src="${img.base64}"  class="">
                </div>
            `;
            container.append(imageTag);
            navFancy.append(navTag);
        }
        // Fancybox.bind('[data-fancybox="gallery"]',{
        //     Slideshow: {
        //         playOnStart: true,
        //       },
        // });
        const mainCarousel = carouselAuto('fancyContainer', carouselAutoOption);
        const navCarrousel = carousel('navFancy', {...carouselNavOpt, Sync: {target: mainCarousel}});
        // const mainCarousel = new Carousel(document.getElementById("fancyContainer"), {
        //     Autoplay : {
        //         timeout : 3000
        //     },
        //   }, { Autoplay });
          console.log('main', mainCarousel);
        // options
            // slidesPerPage: กำหนดจำนวนสไลด์ที่แสดงพร้อมกันใน 1 หน้า
            // ถ้า slidesPerPage: 1 → แสดงทีละ 1 รูป → ได้ 10 หน้า
            // ถ้า slidesPerPage: 5 → แสดงทีละ 5 รูป → ได้ 2 หน้า

            // fill: false:
            // ถ้า fill: true (ค่า default บางเวอร์ชัน) แล้ว Carousel เห็นว่ามีที่ว่างเหลือ มันจะขยายรูปให้เต็ม → ทำให้รูปทั้งหมดแสดงในหน้าเดียว (ไม่มีการเลื่อน)
            // ถ้า fill: false → แสดงเท่าที่กำหนดใน slidesPerPage เท่านั้น เหลือที่ก็ไม่ขยาย จึงเกิด “หลายหน้า”

            // infinite: false:
            // ถ้า true → เลื่อนสุดแล้วจะวนกลับหน้าแรก (Carousel แบบ Loop)
            // ถ้า false → เลื่อนสุดแล้วหยุด

            // center: false:
            // ถ้า center: true → สไลด์อาจถูกจัดกึ่งกลางใน container และ “พยายาม” ขยายเต็ม
            // ถ้า false → จะจัดตามธรรมชาติ (ชิดซ้าย)

            // transition: "slide":
            // เพื่อให้เห็นเอฟเฟกต์เลื่อนจากซ้ายไปขวา
            // หรือใช้ "fade" ถ้าอยากให้ภาพจางไป-จางมา
        // const navCarrousel = new Carousel(document.getElementById("navFancy"), {
        //     slidesPerPage: 1,
        //     fill: false,
        //     infinite: true,
        //     transition: "slide",
        //     Dots: false,
        //     Sync: {
        //         target: mainCarousel,
        //     },
        // });
        console.log('nav', navCarrousel);

        $('#fancyDialog').removeClass('w-fit h-fit').addClass('w-11/12 h-full');
        setTimeout(() => {
            $('#fancyContainer').removeClass('hidden');
            $('#navFancy').removeClass('hidden').addClass('flex');
        }, 100);
    }else{
        $('.FancyHeader').html('');
        showMessage('ไม่พบรูปภาพกรุณา คลิกเพิ่มรูปภาพ', 'warning');
        $('#fancyDialog').addClass('w-fit h-fit').removeClass('w-11/12 h-full');
    }

}

// $(document).on('click', '.deleteFancyImage', function(){
//     e.stopPropagation(); // ป้องกันการคลิกซ้ำ Fancybox
//     let imageId = $(this).data("id");
//     deleteImage(imageId, $(this).parent());
// });


/**
 * Show input form
 */
$(document).on('click','#showFancyImgForm', function(){
    $(this).addClass('hidden');
    $('#formAddFancyImage').toggleClass('hidden flex');
});

/**
 * Cancel
 */
$(document).on('click','#cancelFancyImage', function(){
    $('#formAddFancyImage').toggleClass('hidden flex');
    $('#showFancyImgForm').removeClass('hidden');
});

/**
 * Check file format
 */
$(document).on('change', '#FancyImage', function(){
    const format = Array.isArray(fileImgFormat) ?fileImgFormat.join(', ') : fileImgFormat;
    checkFileFormat($(this), fileImgFormat, `ไฟล์ไม่ถูกต้อง กรุณาแนบไฟล์นามสกุล ${format}`);
});




export const carouselSlide = {
    Slideshow: {
        playOnStart: true,
    }
};

export const carouselAutoOption = {
    Autoplay : {
        timeout : 3000
    }
};
// options
            // slidesPerPage: กำหนดจำนวนสไลด์ที่แสดงพร้อมกันใน 1 หน้า
            // ถ้า slidesPerPage: 1 → แสดงทีละ 1 รูป → ได้ 10 หน้า
            // ถ้า slidesPerPage: 5 → แสดงทีละ 5 รูป → ได้ 2 หน้า

            // fill: false:
            // ถ้า fill: true (ค่า default บางเวอร์ชัน) แล้ว Carousel เห็นว่ามีที่ว่างเหลือ มันจะขยายรูปให้เต็ม → ทำให้รูปทั้งหมดแสดงในหน้าเดียว (ไม่มีการเลื่อน)
            // ถ้า fill: false → แสดงเท่าที่กำหนดใน slidesPerPage เท่านั้น เหลือที่ก็ไม่ขยาย จึงเกิด “หลายหน้า”

            // infinite: false:
            // ถ้า true → เลื่อนสุดแล้วจะวนกลับหน้าแรก (Carousel แบบ Loop)
            // ถ้า false → เลื่อนสุดแล้วหยุด

            // center: false:
            // ถ้า center: true → สไลด์อาจถูกจัดกึ่งกลางใน container และ “พยายาม” ขยายเต็ม
            // ถ้า false → จะจัดตามธรรมชาติ (ชิดซ้าย)

            // transition: "slide":
            // เพื่อให้เห็นเอฟเฟกต์เลื่อนจากซ้ายไปขวา
            // หรือใช้ "fade" ถ้าอยากให้ภาพจางไป-จางมา
export const carouselNavOpt = {
    slidesPerPage: 1,
    fill: false,
    infinite: true,
    transition: "slide",
    Dots: false,
};

/**
 * create fancy box
 * @param {string} dataFancy e.g. gallery 
 */
export function fancy(dataFancy){
    Fancybox.bind(`[data-fancybox="${dataFancy}"]`,{
        Slideshow: {
            playOnStart: true,
        },
    });
}

/**
 * 
 * @param {string} id 
 * @param {object} opt 
 * @returns 
 */
export function carousel(id , opt){
    // return autoplay ? new Carousel(document.getElementById(id), opt, {Autoplay}) : new Carousel(document.getElementById(id), opt);
    return new Carousel(document.getElementById(id), opt);
} 

export function carouselAuto(id, opt = carouselAutoOption){
    // console.log($(`#${id}`), opt);
    
    return new Carousel(document.getElementById(id),opt,{Autoplay});
}