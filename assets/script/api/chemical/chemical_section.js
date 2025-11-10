import { fetchMsgErr } from "@api/fetch-utils";
/**
 * @typedef {object} updateSection
 * @property {string} [OWNER] ชื่อแผนกเจ้าของสารเคมี
 * @property {string} OWNERCODE รหัสแผนกเจ้าของสารเคมี
 * @property {number} AMEC_SDS_ID รหัส SDS ในระบบ AMEC
 * @property {Date} [RECEIVED_SDS_DATE] วันที่ได้รับ SDS
 * @property {string} [USED_FOR] ใช้เพื่อ
 * @property {string} [USED_AREA] พื้นที่การใช้งาน
 * @property {string} [KEEPING_POINT] พื้นที่การจัดเก็บ
 * @property {number} [QTY] จำนวน
 * @property {number} [REC4052] จำนวน
 * @property {number} [REC4054] จำนวน
 * @property {string} [USER_UPDATE] รหัสพนักงานผู้แก้ไข
 * @property {number} [status]
 * 
 * @typedef {object} returnUpdateSection
 * @property {boolean} status
 * @property {string} message
 * 
 * @param {updateSection} data
 * @returns {Promise<returnUpdateSection>}
 * @example 
 * const data = {
 *   OWNERCODE: "D001",
 *   AMEC_SDS_ID: 1234,
 *   RECEIVED_SDS_DATE: "2023-10-01",
 *   USER_UPDATE: "24008",
 * }
 * const res = await updateChemicalSection(data);
 */
export async function updateChemicalSection(data) {
    const res = await fetch(
        `${process.env.APP_API}/safety/chemical-section/update`,
        {
            method: "PATCH",
            body: data,
        }
    );
    if (!res.ok) {
        await fetchMsgErr(res);
        throw new Error("Failed to update chemical section");
    }
    return await res.json();
}
