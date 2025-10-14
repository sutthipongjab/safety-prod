

/**
 * Create electronic stamp
 * @param {object} doc 
 * @param {number} x  282.5
 * @param {number} y  20
 * @param {string} position DDEM
 * @param {string} date 4 FEB 2025
 * @param {string} name SUNANTA
 */
export function createStamp(doc, x, y, position, date, name){
    const radius = 9;
    // กำหนดสีสำหรับตราประทับ
    doc.setDrawColor(255, 0, 0); // สีแดงสำหรับเส้น
    doc.setLineWidth(0.5);

    // วาดวงกลม (ตำแหน่ง x, y, รัศมี)
    doc.circle(x, y, radius, 'S'); // 'S' หมายถึงลักษณะการวาดเป็นเส้น

    // วาดเส้นแนวนอน (ตำแหน่ง x1, y1, x2, y2)
    doc.line(x-8.5, y-2, x+8.5, y-2); // เส้นด้านบน
    doc.line(x-8.5, y+3, x+8.5, y+3); // เส้นด้านล่าง

    doc.setFontSize(14);
    // doc.text(`S/T ${position}`, x, 10, {align : 'center'});
    doc.text(`S/T DEPT.`, x, 10, {align : 'center'});
    doc.setTextColor(255, 0, 0); // สีแดงสำหรับข้อความ
    doc.setFontSize(18);
    // doc.text('AMEC', x, y-3, { align: 'center' }); // ข้อความด้านบน
    doc.text('SAFETY', x, y-3, { align: 'center' }); // ข้อความด้านบน
    doc.setFontSize(10);
    doc.text(date, x, y+1.5, { align: 'center' }); // ข้อความตรงกลาง
    doc.setFontSize(13);
    doc.setCharSpace(-0.4);
    doc.text('APPROVED', x+1, y+7, { align: 'center' }); // ข้อความด้านล่าง
    // doc.setFontSize(14);
    // doc.setCharSpace(-0.3);
    // doc.text(name, x, y+7, { align: 'center' }); // ข้อความด้านล่าง
    doc.setCharSpace(0);
    doc.setTextColor(0, 0, 0);
}

/**
 * Load font for set font in jsPDF or autoTable
 * @param {string} host 
 * @param {string} pathfont freesiaUPC/upcfl.ttf
 * @returns 
 */
export const loadFont = async (host, pathfont) => {
    // const response = await fetch(`${host}assets/dist/fonts/freesiaUPC/upcfl.ttf`); // ใส่ path ของไฟล์ฟอนต์
    const response = await fetch(`${host}assets/dist/fonts/${pathfont}`); // ใส่ path ของไฟล์ฟอนต์
    const fontData = await response.arrayBuffer();
    const fontBase64 = btoa(
      String.fromCharCode(...new Uint8Array(fontData))
    );
    return fontBase64;
};

/**
 * Constant option for AutoTable
 * const opt = DeepCopy JSON.parse(JSON.stringify(optAutoTable)); || {...optAutoTable}
 */
export const optAutoTable = {
    styles: { 
        font: "freesiaUPC",
        fontSize : 12,
        lineColor : 'black',
        lineWidth : 0.1,
        cellPadding : 0.5
    },
    headStyles: { 
        font: "freesiaUPC", 
        overflow: 'linebreak',
        halign: 'center', // จัดข้อความแนวนอนให้อยู่ตรงกลาง
        valign: 'middle', // จัดข้อความแนวตั้งให้อยู่กึ่งกลาง
        // minCellHeight: headerHeight,
        // fillColor : [173, 216, 230]
    },
    theme: 'plain',
    margin: { left: 5, right: 5, bottom: 5 },
    startY: 0, // เริ่มตำแหน่งไหน doc.lastAutoTable.finalY ในกรณีมีหลายตาราง สามารถ  + เพิ่มได้
    // columnStyles
}


/**
 * Optimize font size
 * @param {object} doc 
 * @param {string} text 
 * @param {number} x 
 * @param {number} y 
 * @param {number} maxRadius 
 * @param {number} fontSize 
 * @param {string} alignment 
 */
function fitTextToCircle(doc, text, x, y, maxRadius, fontSize, alignment = 'center') {
    let textWidth = doc.getTextWidth(text);
    while (textWidth > maxRadius * 2) {
        console.log(textWidth);
        
      fontSize -= 0.5; // ลดขนาดฟอนต์ทีละน้อย
      doc.setFontSize(fontSize);
      textWidth = doc.getTextWidth(text);
    }
    doc.text(text, x, y, { align: alignment });
  }