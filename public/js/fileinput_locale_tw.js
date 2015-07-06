/*!
 * FileInput Chinese Translations
 *
 * This file must be loaded after 'fileinput.js'. Patterns in braces '{}', or
 * any HTML markup tags in the messages must not be converted or translated.
 *
 * @see http://github.com/kartik-v/bootstrap-fileinput
 * @author kangqf <kangqingfei@gmail.com>
 *
 * NOTE: this file must be saved in UTF-8 encoding.
 */
(function ($) {
    "use strict";

    $.fn.fileinputLocales['tw'] = {
        fileSingle: '檔案',
        filePlural: '檔案',
        browseLabel: '瀏覽 &hellip;',
        removeLabel: '移除',
        removeTitle: '清除已選擇的檔案',
        cancelLabel: '取消',
        cancelTitle: '取消進行中的上傳',
        uploadLabel: '上傳',
        uploadTitle: '上傳已選擇的檔案',
        msgSizeTooLarge: '檔案 "{name}" (<b>{size} KB</b>) 超過了允許容量 <b>{maxSize} KB</b>. 請重新上傳!',
        msgFilesTooLess: '你至少必須選擇 <b>{n}</b> {files} 個檔案. 請重新上傳!',
        msgFilesTooMany: '已選擇的上傳檔案數量 <b>({n})</b> 超出最大檔案限制數量 <b>{m}</b>. 請重新上傳!',
        msgFileNotFound: '檔案 "{name}" 找不到!',
        msgFileSecured: '安全限制，為了防止讀取檔案 "{name}".',
        msgFileNotReadable: '檔案 "{name}" 不可讀.',
        msgFilePreviewAborted: '取消 "{name}" 的預覽.',
        msgFilePreviewError: '讀取 "{name}" 時出現一個錯誤.',
        msgInvalidFileType: '不正確的類型 "{name}". 只支援 "{types}" 類型的檔案.',
        msgInvalidFileExtension: '不正確的副檔名 "{name}". 只支援 "{extensions}" 的副檔名.',
        msgValidationError: '檔案上傳錯誤',
        msgLoading: '載入第 {index} 個檔案 共 {files} &hellip;',
        msgProgress: '載入第 {index} 個檔案 共 {files} - {name} - {percent}% 完成.',
        msgSelected: '{n} {files} 已選擇',
        msgFoldersNotAllowed: '只支援拖曳檔案! 忽略 {n} 拖曳的資料夾.',
        msgImageWidthSmall: '圖像檔案"{name}"的寬度必須是至少{size}像素.',
        msgImageHeightSmall: '圖像檔案"{name}"的高度必須至少為{size}像素.',
        msgImageWidthLarge: '圖像檔案"{name}"的寬度不能超過{size}像素.',
        msgImageHeightLarge: '圖像檔案"{name}"的高度不能超過{size}像素.',
        dropZoneTitle: '拖曳圖片到這裡 &hellip;',
        slugCallback: function(text) {
            return text ? text.split(/(\\|\/)/g).pop().replace(/[^\w\u4e00-\u9fa5\-.\\\/ ]+/g, '') : '';
        }
    };
})(window.jQuery);
