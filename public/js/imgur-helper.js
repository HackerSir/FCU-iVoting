function getImgurImageId(url) {
    var reg = /(?:(?:https?:)?\/\/)?[iw\.]*imgur\.[^\/]*\/(?:gallery\/)?([^\?\s\.]*).*$/im;
    return reg.exec(url)[1];
}

function getImgurThumbnail(url) {
    var extensionReg = /[^\\\\]*\.(\w+)$/;
    var extension = extensionReg.exec(url)[1];
    return "//i.imgur.com/" + getImgurImageId(url) + ((extension != "gif") ? "l." : ".") + extension;
}
