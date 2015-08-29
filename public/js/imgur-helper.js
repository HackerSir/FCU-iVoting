function getImgurImageId(url) {
    var reg = /(?:(?:https?:)?\/\/)?[iw\.]*imgur\.[^\/]*\/(?:gallery\/)?([^\?\s\.]*).*$/im;
    return reg.exec(url)[1];
}

function getImgurThumbnail(url, suffix) {
    var extensionReg = /[^\\\\]*\.(\w+)$/;
    var extension = extensionReg.exec(url)[1];
    suffix = (typeof suffix === 'undefined' || !$.inArray(suffix, ['s', 'b', 't', 'm', 'l', 'h'])) ? '' : suffix;
    return '//i.imgur.com/' + getImgurImageId(url) + ((extension != "gif") ? suffix : '') + '.' + extension;
}
