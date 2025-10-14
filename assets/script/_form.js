

//JS Loader
import $ from "jquery";
import { showpreload, hidepreload, checkAuthen, uri } from "./utils";

const url = uri.includes('amecwebtest') ? `${uri}/api-auth/api-dev/` : `${uri}/api-auth/api/`;
console.log(url);

/**
 * Create Form and Flow
 * @param {string} NFRMNO 
 * @param {string} VORGNO 
 * @param {string} CYEAR 
 * @returns 
 */
export function createForm(NFRMNO, VORGNO, CYEAR, req, key, remark, mflag=1){
    return new Promise((resolve) => {
        $.ajax({
            url: `${url}flow/create2`,
            type: "post",
            dataType: "json",
            data: { 
                empno: req, 
                inputempno: key, 
                remark : remark,
                nfrmno : NFRMNO,
                vorgno : VORGNO,
                cyear  : CYEAR,
                mflag  : mflag
            },
            beforeSend: function (){
                showpreload()
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}
/**
 * Create Form and Flow
 * @param {string} NFRMNO 
 * @param {string} VORGNO 
 * @param {string} CYEAR 
 * @param {string} req 
 * @param {string} key 
 * @param {string} remark 
 * @param {string} draft  0 == under preparation, 1 = wait for approval
 * @returns 
 */
export function createForm2(NFRMNO, VORGNO, CYEAR, req, key, remark, draft = 1){
    return new Promise((resolve) => {
        $.ajax({
            url: `${uri}/webservice/webflow/form/create`,
            type: "post",
            dataType: "json",
            data: { 
                empno: req, 
                inputempno: key, 
                remark : remark,
                nfrmno : NFRMNO,
                vorgno : VORGNO,
                cyear  : CYEAR,
                draft  : draft
            },
            beforeSend: function (){
                showpreload()
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * Delete Form and Flow
 * @param {string} NFRMNO
 * @param {string} VORGNO
 * @param {string} CYEAR
 * @param {string} CYEAR2
 * @param {string} NRUNNO
 * @return {Promise}
 */
export function deleteForm(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO){
    return new Promise((resolve) => {
        $.ajax({
            url: `${uri}/webservice/webflow/form/deleteForm`,
            // url: `${url}flow/deleteForm`,
            type: "post",
            dataType: "json",
            data: { 
                nfrmno : NFRMNO,
                vorgno : VORGNO,
                cyear  : CYEAR,
                cyear2 : CYEAR2,
                runno  : NRUNNO,
            },
            beforeSend: function (){
                showpreload()
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * Show Flow
 * @param {string} NFRMNO
 * @param {string} VORGNO
 * @param {string} CYEAR
 * @param {string} CYEAR2
 * @param {string} NRUNNO
 * @return {Promise}
 */
export function showFlow(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO){
    return new Promise((resolve) => {
        $.ajax({
            // url: `${url}flow/showflow`,
            // url: uri.includes('amecwebtest') ? `${url}flow/showflow` : `${uri}/webservice/webflow/Flow/showflow`,
            url: `${uri}/webservice/webflow/Flow/showflow`,
            type: "post",
            dataType: "json",
            data: { 
                nfrmno : NFRMNO,
                vorgno : VORGNO,
                cyear  : CYEAR,
                cyear2 : CYEAR2,
                runno  : NRUNNO,
            },
            beforeSend: function (){
                showpreload()
            },
            success: function (res) {
                res.html = res.html.replace(/<table style="/g, '<table style=" display:block; overflow-x:scroll;');
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * Action Flow
 * @param {string} NFRMNO
 * @param {string} VORGNO
 * @param {string} CYEAR
 * @param {string} CYEAR2
 * @param {string} NRUNNO
 * @param {string} action
 * @param {string} empno
 * @param {string} remark
 * @return {Promise}
 */
export function doaction(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, action, empno, remark){
    return new Promise((resolve) => {
        $.ajax({
            // url: `http://amecwebtest.mitsubishielevatorasia.co.th/api-auth/api-dev/appflow/doaction`,
            url: `${url}appflow/doaction`,
            type: "post",
            dataType: "json",
            data: { 
                frmNo  : NFRMNO,
                orgNo  : VORGNO,
                y      : CYEAR,
                y2     : CYEAR2,
                runNo  : NRUNNO,
                action : action,
                apv    : empno,
                remark : remark,
            },
            beforeSend: function (){
                showpreload()
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}

/**
 * Action Flow
 * @param {string} NFRMNO
 * @param {string} VORGNO
 * @param {string} CYEAR
 * @param {string} CYEAR2
 * @param {string} NRUNNO
 * @param {string} action
 * @param {string} empno
 * @param {string} remark
 * @return {Promise}
 */
export function doactionWebservice(NFRMNO, VORGNO, CYEAR, CYEAR2, NRUNNO, action, empno, remark){
    return new Promise((resolve) => {
        $.ajax({
            // url: `http://amecwebtest.mitsubishielevatorasia.co.th/api-auth/api-dev/appflow/doaction`,
            url: `${uri}/webservice/webflow/flow/doaction`,
            type: "post",
            dataType: "json",
            data: { 
                frmNo  : NFRMNO,
                orgNo  : VORGNO,
                y      : CYEAR,
                y2     : CYEAR2,
                runNo  : NRUNNO,
                action : action,
                apv    : empno,
                remark : remark,
            },
            beforeSend: function (){
                showpreload()
            },
            success: function (res) {
                resolve(res);
            },
            complete: function(xhr, status){
                checkAuthen(xhr, status);
                hidepreload();
            }
        });
    });
}




