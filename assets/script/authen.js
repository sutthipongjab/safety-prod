
import { decryptText } from "@root/inc/_crypto";
import { getAppDataById } from '@root/indexDB/userAuth';
// import Cookies from "js-cookie";
import { directlogin } from '@root/webservice.js';
import { getCookie, setCookie } from '@root/inc/_jsCookie';

var indexedDBID;

const host = self.location.origin + self.location.pathname.split('/').slice(0,2).join('/');

const root = self.location.origin;

$(async function(){

     const cookie = getCookie(process.env.APP_NAME);
    if(!cookie){
        window.location.href = `${root}/form/authen/index/${process.env.APP_ID}`;
    }else{
        // ถ้ามี cookie ให้ decrypt ค่า cookie และเก็บค่าในตัวแปร indexedDBID
        indexedDBID = decryptText(cookie, process.env.APP_NAME);

        setCookie(process.env.APP_NAME, cookie, { expires: new Date(new Date().getTime() + 30 * 60 * 1000) }); // Set cookie ทุกครั้งที่โหลดหน้าเว็บ
        // Cookies.set(process.env.APP_NAME, cookie, { expires: 0.5 / 24 }); // Set cookie ทุกครั้งที่โหลดหน้าเว็บ

        // const res = await getAppDataById(indexedDBID);
        const [appid, empno] = indexedDBID.split('-');
        const res = await directlogin(empno, appid);
        const direct = await setSession(res);
        window.location.href = `${host}/${direct.url}`;
        // setWebsite(indexedDBID); // ดึงข้อมูลแอปพลิเคชันตาม ID ที่เก็บไว้ใน indexedDBID
    }
});


export function setSession(res) {
    return new Promise((resolve) => {
      $.ajax({
            type: "post",
            dataType: "json",
            url: `${host}/authen/setSession`,
            data: {
                group: res.appgroup,
                info: res.appuser,
                menu: res.auth,
                // group: res.group.data,
                // info: res.info.data,
                // menu: res.menu.data,
            },
            success: function (data) {
            resolve(data);
            },
        })
    });
  }
