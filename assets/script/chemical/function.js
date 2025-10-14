import $ from "jquery";

/**
 * Add input
 * @param {object} e 
 * @param {string} html 
 */
export function addInput(e, html){
    e.closest('div').siblings('.inputGroup').append(html);
    e.siblings('i').removeClass('hidden');
    // console.log(e.siblings('i')); 
}

/**
 * Remove input
 * @param {object} e 
 */
export function removeInput(e){
    const input = e.closest('div').siblings('.inputGroup').find('input');
    // console.log(input.length);
    if(input.length == 2){
        e.closest('div').siblings('.inputGroup').find('input').last().remove();
        e.addClass('hidden');
    }else{
        e.closest('div').siblings('.inputGroup').find('input').last().remove();
    }
}

/**
 * reset multi input
 * @param {string} input e.g. input[name="USED_AREA[]"]
 * @param {string} button e.g. .remove-area
 */
export async function resetInput(input, button = '', clear = false){
    console.log('reset');
    
    $(input).each(function() {
        console.log('val',$(this).val().trim());
        console.log('length',$(input).length);
        
        if(clear){
            if ($(this).val().trim() === "" && $(input).length > 1) $(this).remove();
        }else{
            if ($(input).length > 1) $(this).remove();
        }
    });

    const updateInput = $(input)

    if(button != '' && updateInput.length == 1) $(button).addClass('hidden');
}

/**
 * set input on select list
 * @param {string} input e.g. input[name="USED_AREA[]"]
 * @param {string} button e.g. .add-area
 * @param {string} data e.g. Water Water Treatment | aaa | xxx
 */
export function setInput(input, button, data){
    console.log('set');

    console.log(data);
    const arr = data.split('|');
    console.log(arr);
    
    if(arr.length > 1){
        for (let index = 0; index < arr.length-1; index++) {
            $(button).trigger('click');
        }
        $(input).each(function(i, el){
            $(el).val(arr[i]);
        });
    }else{
        $(input).val(data);
    }
}