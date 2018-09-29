$(document).ready(function() {
    $('#Joyride').joyride({
        'tipLocation': 'bottom', // 'top' or 'bottom' in relation to parent
        'nubPosition': 'auto', // override on a per tooltip bases
        'scrollSpeed': 300, // Page scrolling speed in ms
        'timer': 0, // 0 = off, all other numbers = time(ms) 
        'startTimerOnClick': false, // true/false to start timer on first click
        'nextButton': true, // true/false for next button visibility
        'tipAnimation': 'pop', // 'pop' or 'fade' in each tip
        'pauseAfter': [], // array of indexes where to pause the tour after
        'tipAnimationFadeSpeed': 300, // if 'fade'- speed in ms of transition
        'tipContainer': document.body, // Where the tip be attached if not inline
        'postRideCallback': function(e) {
        }, // a method to call once the tour closes
        'postStepCallback': function(e) {
            if (e == 2) 
                $("#_addTripPlus").click();
        },
        autoStart: true
    });
});