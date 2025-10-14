
/**
 * setForm
 * @param {array} data e.g. [[],[],[],detail[],image[]]
 * @param {string} template e.g. webflow
 * @param {string} mode e.g. view
 * @returns 
 */
export async function setElecForm(data, template = 'safety', mode = 'edit'){
    console.log(data);
    let imageTag = '';
    const thTop = template == 'safety' ? 'top-16' : 'top-0';
    const disabled = mode == 'edit' ? '' : 'disabled';
    // console.log(disabled, mode);
    
    let table = `
    <table class="table table-pin-rows table-preview border rounded-lg shadow-md ">
        <thead class="${thTop} sticky z-10 drop-shadow-md max-md:hidden">
            <tr class="text-center text-base bg-primary-content">
                <th rowspan="2">No.</th>
                <th rowspan="2">ภาพประกอบ</th>
                <th rowspan="2">รายละเอียด</th>
                <th colspan="4">ผลการตรวจสอบ</th>
            </tr>
            <tr class="text-center text-base bg-primary-content">
                <th>ใช่</th>
                <th>ไม่ใช่</th>
                <th>ไม่เกี่ยวข้อง</th>
                <th>รายละเอียดความผิดปกติ</th>
            </tr>
        </thead>
        <tbody class="max-md:flex max-md:flex-col">`;
    for (const t of data) {
        imageTag = ''
        // console.log(t, t.image, t.detail);
        for(const i of t.image){
            imageTag += `
                <a href="${i.base64}" class="relative f-carousel__slide flex justify-center items-center spaceFancy yFancy" data-fancybox="gallery_${t.FRMT_NO}">
                    <img src="${i.base64}">
                </a>
            `;
        }
        const rowspan = t.detail.length+1;
        table += `
        <tr class="bg-gray-300">
             <td colspan="7" >
                <span>${t.FRMT_TOPIC || ''}</span>
                <span class="text-blue-500"> ${t.FRMT_TOPICEN || ''}</span>
            </td>
        </tr>
        <tr>
            <td rowspan="${rowspan}" class="max-md:hidden">${t.FRMT_NO}</td>
            <td rowspan="${rowspan}" class="md:min-w-80 ">
                <div id="navFancy_${t.FRMT_NO}" class="f-carousel h-auto">
                    ${imageTag}
                </div>
            </td>
        </tr>
        `
        for(const d of t.detail){
            if(d.TYPE_SEQ == 'disabled'){
                table += `
                <tr>
                    <td class="flex flex-col"><span>${d.DETAIL || ''}</span><span class="text-blue-500">${d.DETAILEN || ''}</span></td>
                </tr>`;
            } else {
                const cls = d.TYPE_SEQ == 'radio' ? 'radio' : (d.TYPE_SEQ == 'text' ? 'input input-bordered' : 'checkbox');
                const nameArray = d.TYPE_SEQ == 'checkbox' ? '[]' : '';
                if(d.TYPE_SEQ == 'text'){
                    table += `
                    <tr>
                        <td class="flex flex-col md:whitespace-nowrap"><span>${d.DETAIL || ''}</span><span class="text-blue-500">${d.DETAILEN || ''}</span></td>
                        <td colspan="3">
                            <input type="${d.TYPE_SEQ}" class="${cls} w-full min-w-40 req" name="data${d.TOPIC_NO}_${d.SEQ}" ${disabled}/>
                        </td>
                        <td><textarea class="textarea textarea-bordered w-full min-w-40" name="remark${d.TOPIC_NO}_${d.SEQ}" ${disabled}></textarea></td>
                    </tr>`;
                }else{
                table += `
                <tr class="max-md:flex max-md:flex-col">
                    <td class="flex flex-col md:whitespace-nowrap"><span>${d.DETAIL || ''}</span><span class="text-blue-500">${d.DETAILEN || ''}</span></td>
                    <td >
                        <div class="form-control">
                            <label class="label cursor-pointer gap-5">
                                <span class="label-text md:hidden">ปกติ</span>
                                <input type="${d.TYPE_SEQ}" name="data${d.TOPIC_NO}_${d.SEQ}${nameArray}" class="${cls} req" value="1" ${disabled}>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="form-control">
                            <label class="label cursor-pointer gap-5">
                                <span class="label-text md:hidden">ผิดปกติ</span>
                                <input type="${d.TYPE_SEQ}" name="data${d.TOPIC_NO}_${d.SEQ}${nameArray}" class="${cls} req" value="2" ${disabled}>
                            </label>
                        </div>
                    </td>
                    <td>
                        <div class="form-control">
                            <label class="label cursor-pointer gap-5">
                                <span class="label-text md:hidden">ไม่มี</span>
                                <input type="${d.TYPE_SEQ}" name="data${d.TOPIC_NO}_${d.SEQ}${nameArray}" class="${cls} req" value="3" ${disabled}>
                            </label>
                        </div>
                    </td>
                    <td><textarea class="textarea textarea-bordered w-full min-w-40" name="remark${d.TOPIC_NO}_${d.SEQ}" ${disabled}></textarea></td>
                </tr>`
                }
            }

        }
    }
    table += `
        </tbody>
    </table>`;
    return table;
}

export async function setElecData(data, frm){
    // console.log('set Elec', data);
    data.forEach(d => {
        // console.log(d);
        // const input    = $(`input[name="data${d.ET_TOPIC}_${d.ET_SEQ}"]`);
        // const textarea = $(`textarea[name="remark${d.ET_TOPIC}_${d.ET_SEQ}"]`);
        if(d.DATA_TYPE == 'text'){
            $(`input[name="data${d.ET_TOPIC}_${d.ET_SEQ}"]`).val(d.ET_DATA)
        } else if (d.DATA_TYPE == 'radio') {
            $(`input[name="data${d.ET_TOPIC}_${d.ET_SEQ}"][value="${d.ET_DATA}"]`).prop('checked', true);
        } else if (d.DATA_TYPE == 'checkbox') {
            if (d.ET_DATA.includes('|')) {
                const split = d.ET_DATA.split('|');
                // console.log(split);
                split.forEach(s => {
                    $(`input[name="data${d.ET_TOPIC}_${d.ET_SEQ}[]"][value="${s}"]`).prop('checked', true);
                });
            } else {
                $(`input[name="data${d.ET_TOPIC}_${d.ET_SEQ}[]"][value="${d.ET_DATA}"]`).prop('checked', true);
            }
        }
        $(`textarea[name="remark${d.ET_TOPIC}_${d.ET_SEQ}"]`).val(d.ET_REMARK)
        

    });
    
}
