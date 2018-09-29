/**
 * notifyMe(text, errorType, position, attacher);
 * Shows the "success" | "warn" | "error" message for the given text
 * @param text The text to show (default = "Success!")
 * @param errorType The type of alert to show. ( "success" [default], "warn", "error" and "info" accepted)
 * @param position The position of the alert (default = "left bottom").<br> 
 *        See <a href="http://notifyjs.com/#position">http://notifyjs.com/#position</a> for details
 * @param attacher The SELECTOR ("#myDiv", ".allFields", ...) to attach it to. (default: global notifications)
 * @author Nico Kupfer
 */
function notifyMe(text, errorType, position, attacher) {
    text      = text      || "Success!";
    errorType = errorType || "success";
    position  = position  || "left bottom";
    attacher  = attacher  || false;
    if (!attacher)
        $.notify(text, {className: errorType, position: position});
    else 
        $(attacher).notify(text, {className : errorType, position: position});
}

/**
 * Show and hide a loader automatically on each $.ajax call.
 * For this you need to add a #_loader somewhere, with display:none
 */
$( document ).ajaxStart(function() {
    $("#_loader").show();
});
$( document ).ajaxStop(function() {
    var e = setTimeout(function() {
        $("#_loader").stop(true, true).fadeOut(0, function() {
            $(this).css('display', 'none');
        });
    }, 300);
});