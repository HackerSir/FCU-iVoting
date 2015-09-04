function notifySuccess($message, $delaySeconds) {
    $delaySeconds = typeof $delaySeconds !== 'undefined' ? $delaySeconds : 5;
    $.notify({
        // options
        message: $message
    }, {
        // settings
        type: 'success',
        placement: {
            align: 'center'
        },
        offset: 70,
        delay: parseInt($delaySeconds, 10) * 1000,
        timer: 500,
        mouse_over: 'pause',
        animate: {
            enter: 'animated zoomIn',
            exit: 'animated zoomOut'
        }
    });
}
function notifyWarning($message, $delaySeconds) {
    $delaySeconds = typeof $delaySeconds !== 'undefined' ? $delaySeconds : 0;
    $.notify({
        // options
        message: $message
    }, {
        // settings
        type: 'danger',
        placement: {
            align: 'center'
        },
        offset: 70,
        delay: parseInt($delaySeconds, 10) * 1000,
        timer: 500,
        mouse_over: 'pause',
        animate: {
            enter: 'animated rubberBand',
            exit: 'animated zoomOut'
        }
    });
}
