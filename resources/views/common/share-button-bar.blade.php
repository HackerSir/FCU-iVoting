<div id="share-button-bar" class="btn-toolbar pull-right">
    <a href="#" onClick="window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}','ShareWindow','width=600,height=300' ); return false;" class="btn btn-social-icon btn-facebook" title="透過 Facebook 分享"><i class="fa fa-facebook"></i></a>
    <a href="#" onClick="window.open('http://twitter.com/home/?status={{ urlencode($title.' '.$url) }}','ShareWindow','width=600,height=300' ); return false;" class="btn btn-social-icon btn-twitter" title="透過 Twitter 分享"><i class="fa fa-twitter"></i></a>
    <a href="http://www.plurk.com/?qualifier=shares&status={{ urlencode($url.' ('.$title.')') }}" class="btn btn-social-icon btn-soundcloud" title="透過 噗浪 分享" target="_blank"><i class="fa fa-plurk">P</i></a>
    <a href="#" onClick="window.open('https://plus.google.com/share?url={{ urlencode($url) }}','ShareWindow','width=500,height=300' ); return false;" class="btn btn-social-icon btn-google" title="透過 Google+ 分享"><i class="fa fa-google"></i></a>
</div>
