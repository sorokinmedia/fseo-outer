var AutoLink;
var LinkAreaJs;
var textArea;
var textCarPos;

jQuery(document).ready(function(){
    var url = jQuery(location).attr('href');
    if ((url.indexOf('post.php') + 1 && url.indexOf('action=edit') + 1) || (url.indexOf('post-new.php') + 1)){
        var login_box_text = jQuery('#wp-admin-bar-my-account').text();
        if(login_box_text.indexOf('fabricav23') + 1){
            renderArticleStata()
            jQuery('#content').focusout(function () {
                sendGET(
                    'https://api.workhard.online/v1/common/task/prices?'
                    + 'wp_post_id=' + googleImagesPostId
                    + '&site_name=' + jQuery(location).attr('host')
                )
            })
        }
    }

    jQuery('.option_input').slideUp('fast');
    jQuery('.input_title > h3').click(function(){
        if(jQuery(this).parent().next('.option_input').css('display')=='none')
        {
            jQuery(this).removeClass('inactive');
            jQuery(this).addClass('active');
        }
        else
        {
            jQuery(this).removeClass('active');
            jQuery(this).addClass('inactive');
        }
        jQuery(this).parent().next('.option_input').slideToggle('fast');
    });

    jQuery('#content').select(function () {
        OnLinkSelect(jQuery(this));
        selectHandler(jQuery(this));
    });
    jQuery("#descr_descrtop").select(function () {
        OnLinkSelect(jQuery(this));
        selectHandler(jQuery(this));
    });
    jQuery("#descr_descrbottom").select(function () {
        OnLinkSelect(jQuery(this));
        selectHandler(jQuery(this));
    });

    if(!jQuery('#insert-media-button').attr('class')) {
        jQuery('#wp-content-editor-tools').remove();
        jQuery('#postimagediv').remove();
    }

    var pathname = jQuery(location).attr('host');
    if(!qadmin_ajax_url) var qadmin_ajax_url = '//' + pathname + '/wp-admin/admin-ajax.php';

    document.onkeydown = function(e) {
        if(location !== '/wp-admin/term.php' || location !== '/wp-admin/post.php') return null;
        e = e || window.event;
        window.eeCommon.emit('commonKeyDown', e);
        if (e.ctrlKey && e.keyCode === 73) { //ctrl+i
            e.preventDefault();
            if (window.getSelection) {
                textCarPos = textArea[0].selectionStart; // Мониторим положение курсора в редакторе
            }
            wrapSelected(ShowSelection(),'italic')
            window.getSelection().removeAllRanges();
        }
        if (e.ctrlKey && e.keyCode === 83) { //ctrl+s
            e.preventDefault();
            if (window.getSelection) {
                textCarPos = textArea[0].selectionStart; // Мониторим положение курсора в редакторе
            }
            jQuery('#save-post').trigger('click')
            window.getSelection().removeAllRanges();
        }
        if (e.ctrlKey && e.keyCode === 66) { //ctrl+b
            e.preventDefault();
            if (window.getSelection) {
                textCarPos = textArea[0].selectionStart; // Мониторим положение курсора в редакторе
            }
            wrapSelected(ShowSelection(),'bold')
            window.getSelection().removeAllRanges();
        }
    }

});

// Ф-я чтения выделенного
function ShowSelection()
{
    var textComponent = LinkAreaJs;
    var selectedText;
    // IE version
    if (document.selection != undefined)
    {
        textComponent.focus();
        var sel = document.selection.createRange();
        selectedText = sel.text;
        document.selection.empty();
    }
    // Mozilla version
    else if (textComponent.selectionStart != undefined)
    {
        var startPos = textComponent.selectionStart;
        var endPos = textComponent.selectionEnd;
        selectedText = textComponent.value.substring(startPos, endPos)
    }
    return selectedText;
}

function OnLinkSelect(redactor){

    var url = jQuery(location).attr('href');

    LinkAreaJs = document.getElementById(jQuery(redactor).attr('id'));

    var stxt = ShowSelection();

    if( 2 < stxt.length && stxt.length < 50){
        AutoLink = stxt;
    }
    jQuery('#qt_content_link').click(function () {
        autoLinkInsert();
    });
    jQuery('#qt_descr_descrtop_link').click(function () {
        autoLinkInsert();
    });
    jQuery('#qt_descr_descrbottom_link').click(function () {
        autoLinkInsert();
    });
}

function autoLinkInsert() {
    jQuery("#wp-link-search").focus();
    console.log('Вставлю '+AutoLink);
    jQuery('#wp-link-search').val(AutoLink);
    var e = jQuery.Event("keyup", { keyCode: 13}); //"keydown" if that's what you're doing
    jQuery('#wp-link-search').keyup();
}

/*
 * Модуль парсинга текста
 */
function agregateCounters(redactor) {
    //var img_reg = new RegExp(<img([^>]*[^/])>, 'gi')
    var text = redactor.val()
    if(!text) return false;
    var imgs = text.match(/<img[^>]* src=\"([^\"]*)\"[^>]*>/g)
    var frames = text.match(/\[\/embed\]/g)
    var divs_warring = text.match(/<div class=\"warning\">/g)
    var divs_advice = text.match(/<div class=\"advice\">/g)
    var divs_stop = text.match(/<div class=\"stop\">/g)
    var divs_zakon = text.match(/<div class=\"zakon\"><!--noindex-->(.*?)<!--\/noindex--><\/div>/g)
    var thmb = jQuery('#postimagediv').find('img')
    var links = text.match(/href=\"([^\"]+)/g)


    var outLinkCounter = 0;
    var docsCounter = 0;
    if(links) links.forEach( function (link) {
        if(!link.indexOf(window.location.host) + 1) outLinkCounter++;
        if(
            link.indexOf('.doc')  + 1  ||
            link.indexOf('.docx')  + 1 ||
            link.indexOf('.txt')   + 1 ||
            link.indexOf('.pdf')   + 1 ||
            link.indexOf('.ods')  + 1
        ) docsCounter++;
    })

    //console.log(links,window.location.host)

    var thmb_count = thmb.length ? 1 : 0
    var blocks_count = 0

    //console.log(thmb_count)

    if(divs_warring) blocks_count += divs_warring.length
    if(divs_advice) blocks_count += divs_advice.length
    if(divs_stop) blocks_count += divs_stop.length

    return {
        blocks: blocks_count,
        videos: frames ? frames.length : 0,
        imgs: imgs ? imgs.length + thmb_count: thmb_count,
        out_links: outLinkCounter,
        docs: docsCounter,
        zakon: divs_zakon ? divs_zakon.length : 0
    }
}
function  renderArticleStata() {
    if(!googleImagesPostId) return false;
    var side_ar_st = document.createElement('div');
    side_ar_st.className = 'article-stata postbox ';
    document.getElementById('side-sortables').insertBefore(
        side_ar_st,
        document.getElementById('submitdiv')
    );
    sendGET(
        'https://api.workhard.online/v1/common/task/prices?'
        + 'wp_post_id=' + googleImagesPostId
        + '&site_name=' + jQuery(location).attr('host')
    )
}
function sendGET(call){
    var xhr = new XMLHttpRequest();

    console.log('GETTING TASK PRICE...')

    xhr.open('GET', call, true);
    //xhr.setRequestHeader('Authorization', 'Bearer oNE0n3FKDrYc4bqvB8MsO2rgTj-2d5nW');
    xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');

    xhr.send()

    xhr.onreadystatechange = function() { // (3)
        if (xhr.readyState !== 4) return;

        if (xhr.status !== 200) {
            console.log(xhr.status + ': ' + xhr.statusText);
        } else {
            console.log('Статус ' + xhr.status)
            var resp = JSON.parse(xhr.responseText).response
            if(!resp){
                console.log(JSON.parse(xhr.responseText).messages[0].message);
                return
            } ;
            if(!jQuery('#content')) return false
            var stata = agregateCounters(jQuery('#content'))
            var priceData = [];
            switch (resp.step){
                case 3:
                    priceData = [
                        {title: 'Блоки внимания', price: resp.price_blocks, value: stata.blocks},
                        {title: 'Видео', price: resp.price_video, value: stata.videos},
                        {title: 'Картинки', price: resp.price_images, value: stata.imgs}
                    ]
                    break;
                case 4:
                    priceData = [
                        {title: 'Ссылки на внешние источники', price: resp.price_link, value: stata.out_links},
                        {title: 'Документы', price: resp.price_doc, value: stata.docs},
                        {title: 'Блоки "Закон"', price: resp.price_zakon, value: stata.zakon}
                    ]
                    break;
            }
            var htmlStr = ''; var finalPrice = resp.base_price;
            priceData.forEach(function (elem) {
                htmlStr += '<div class="rb_one">'
                    +'<strong>' + elem.title + '</strong>'
                    +renderBlock(Number(elem.price).toFixed(2), elem.value)
                    +'</div>';
                finalPrice += Number(elem.value) * Number(elem.price);
            });

            jQuery('.article-stata').html(
                '<h2 class="hndle ui-sortable-handle">Цена</h2>'
                +'<div class="st_wrap">'
                +htmlStr
                +'<div class="rb_all_price">'
                +'<button id="rb_refresh" onclick="refresh_rb_handle(event)">Обновить</button>'
                +'<strong>'
                + Number(finalPrice).toFixed(2)
                +' руб</strong>'
                +'<span>Приблизительная стоимость</span></div>'
                +'</div>'
            )
        }

    }
}

function refresh_rb_handle(e) {
    e.preventDefault()
    sendGET(
        'https://api.workhard.online/v1/common/task/prices?'
        + 'wp_post_id=' + googleImagesPostId
        + '&site_name=' + jQuery(location).attr('host')
    )
}

function renderBlock(price, count){
    return '<div class="rb_one_counter">'
        +'<div class="rb_left">'
        +'<div class="rb_count">' + count + ' шт</div>'
        +'<div class="rb_price">' + price + ' руб.шт</div>'
        +'</div>'
        +'<div class="rb_right">'
        +count*price + ' руб'
        +'</div>'
        +'</div>'
}

function wrapSelected(selected, type){
    if(!selected) return false
    var tagBefore = ''
    var tagAfter = ''
    switch(type){
        case 'bold':
            tagBefore = '<strong>'
            tagAfter = '</strong>'
            break;
        case 'italic':
            tagBefore = '<em>'
            tagAfter = '</em>'
            break;
    }
    text = textArea.val()
    if(textCarPos != undefined) {
        textArea.val(
            text.slice(0, textCarPos)
            + tagBefore
            + selected
            + tagAfter
            + text.slice(textCarPos + selected.length)
        );

    }
    else{
        textArea.trigger('focus');
        range = document.selection.createRange();
        range.text = selected;
    }
}

function selectHandler(redactor){
    textArea = redactor;
}