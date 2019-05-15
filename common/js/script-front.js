jQuery(document).ready(function () {
    // для картинок 18+
    var img = jQuery("img.hide_cock");
    setTimeout(function () {
        img.each(function (i, elem) {
            jQuery(elem).wrap('<div class="hide_cock_over" id="ov' + i + '"></div>').after('<div class="hide_cock_over_after" id="ov_af' + i + '">');
            var after = jQuery("#ov_af" + i);
            var over = jQuery("#ov" + i);
            over.width(jQuery(elem).width() + 10);
            after.width(jQuery(elem).width() - 1);
            after.height(jQuery(elem).height() - 10).text("18+");
            after.css({"line-height": after.height() + "px"});
            if (jQuery(elem).hasClass("aligncenter")) {
                over.css({
                    "margin-left": "auto",
                    "margin-right": "auto",
                    "width": jQuery(this).width() + 20,
                    "max-width": "100%"
                });
                after.css({
                    "top": "0",
                    "width": jQuery(this).width(),
                    "max-width": "100%"
                });
            }
            if (jQuery(elem).hasClass("alignright")) {
                over.css({"float": "right", "max-width": "100%", "margin-left": "20px"});
            }
            after.click(function () { //удаляем по клику ee
                jQuery(elem).unwrap();
                jQuery(this).remove();
                if (jQuery("#ov_af" + i)) jQuery("#ov_af" + i).css({"display": "none"});
                jQuery(elem).css({
                    "filter": "none",
                    "-webkit-filter": "none"
                });
            });
        });
    }, 100);
    jQuery('.contents_title').click(function (){
        var text = jQuery('.contents_title');
        jQuery(this).next('ul.contents').slideToggle('fast')
        text.text(text.text() === "Показать содержание" ? "Скрыть содержание" : "Показать содержание");
    });
});