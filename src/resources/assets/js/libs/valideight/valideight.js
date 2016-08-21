/** GLOBALS */
    var valideightGlobals = valideightGlobals || {};
    
    //they are set with var foo = foo || "bar"; to allow them to be set from the HTML itself or even another script without being overwritten here! :-)
    //And don't forget to set Valideight.js as your last script, to avoid conflicts.
/****************************************************/
    
/** Valideight global vars. Do not touch */    
    var areTooltips = false, 
        zIndex = 100;    
/**
 * Customizes (initialises) the form adding the custom classes needed for JS.
 * It is VERY important to run this function each time a &lt;form/&gt; that needs to
 *    be 'valideighted' is added to the DOM.
 * @param e object. RTFM
 */
$.fn.valideight = function(e) {
    var form = $(this), e = e || {};
    console.log(form, e);
    if (e && typeof e.callback === 'function') {
        // this way we can call custom functions from outside
        window[e.callback]();
        return true;
    }

    var options = {
        wrongField : e.wrongField || "Please enter a valid value",
        successCallback: e.successCallback || false, // function() { /*alert("Everything OK!"); Using a function will prevent html submission*/ },
        errorCallback: e.errorCallback || function() { /*alert("Wrong fields!");*/ },
        onValideightReady: e.onValideightReady || function() {/*console.log("Valideight.js loaded!")*/},
        minPassLength: e.minPassLength || 4,
        bootstrap: e.bootstrap || true,
        responsiveSize: e.responsiveSize || 600,
        dataTooltip: e.dataTooltip || 'tooltip' // the data-(...) to use for tooltips, in case there is a conflict with user data-attributes.
    };
    
    restart = function() {
        if (options.bootstrap) {
            form.find(".valideight-box").each(function() {
                $(this).addClass("has-feedback");
            });
        }
        //general initialization for all NOT INITIALIZED valideight forms from the DOM
        form.find(".valideight-box").each(function() {
            $(this).removeClass("has-error has-error-message has-success");
        });
        //form.find(".error-block").each(function() {
        //    $(this).remove();
        //});
        form.find("input:not([type='hidden'], [type='submit'], [type=''], [type='button']), textarea, *[data-forcevalideight]").each(function() {
            var inp = $(this);
            var check = inp.attr("required") || inp.data('required') || false;
            var error = inp.data('error') || options.wrongField || false;
            if (inp.attr('type') === 'submit') inp.attr("data-novalideight","");
    //        console.log(this.id, error);
            if (typeof check !== 'undefined' && check !== false && this.type === "radio") 
                inp.attr("checked", "checked"); //checkmark the required radiobuttons, to avoid form submission without any selected button...!
            if (error && error !== "none" && error !== "false") {
                inp.after("<p class='error-block'>" + error + "</p>");
            }
            if (options.bootstrap) {
                inp.closest('.valideight-box').append('<span class="glyphicon glyphicon-ok form-control-feedback">' +
                    '</span><span class="glyphicon glyphicon-remove form-control-feedback">');
            }
        });
    };
    
    /**
     * returns TRUE if INPUT is valid, FALSE otherwise, based on the custom rules.
     * remember the minimum password length is set at the Global Variables at the top of this script.
     *
     * You can add custom data-type and set your own validation rules here.
     * @param input The DOM input field
     */
    checkValideightInput = function(input) {
//        console.log("input.required is: ", input.required, " for the input " + input.name)
        var type   = $(input).data('type') || input.type, // type of input (text, number, password... and date)
            val    = (typeof input.value === 'undefined' ? input.innerHTML : input.value) || "", // :-)
            minLen = $(input).data('minlength') || false, //minimum input length
            notReq = !(!!input.required || !!$(input).data('required')) && (val.length === 0), // TRUE if (not required && empty), i.e. VALID
            cRegEx = $(input).data('regex') || input.pattern || false, //if set, it will validate using the custom regex expression
            check  = $(input).data('check') || false;
        if (check) // this means we are checking that two fields match, like a confirm Password or confirm Email
             return Boolean(document.getElementById(check).value === val);
        if(notReq) return true; // this means it is a VALID field
        if (minLen && (val.length < minLen)) return false; // returns false if the input length is shorter than min required.
        switch (type) {
            case "text":
            case "textarea":
                if (cRegEx) {
                    var match = val.match(cRegEx) || false;
                    return Boolean(match);
                }
                else return Boolean(val.length);
                break;

            case "password":
                // returns true if val matches the regex for password AND is longer than required length
                // remember that minPassLength = 4 is set on options.
                var match = true;
                if (cRegEx && !(cRegEx == 'none' || cRegEx == 'false')) {
                    cRegEx = cRegEx || /^[a-zA-Z0-9\-_\?!\*@#\.\+]+$/; //allowed: a-z, A-Z, 0-9, -_?!*@#.+
                    match = val.match(cRegEx) || false;
                }
                return Boolean(match && (val.length >= (options.minPassLength)));
                break;

            case "checkbox":
                return notReq ? true : $(input).is(":checked");
                break;

            case "radio":
                //the $.fn.formValideight() has already selected a radio. So we're fine
                return true;
                break;

            case "number":
                //we return TRUE if val is a number.
                return (!isNaN(val) && val !== ""); // true if (isANumber === true)
                break;

            case "decimal":
                //we return TRUE if val is a decimal number.
                cRegEx = cRegEx || /^[0-9]+(\.[0-9]+)?$/;
                var match = val.match(cRegEx) || false;
                return Boolean(match);
                break;

            case "email":
                // returns true if val matches the regex for email
                cRegEx = cRegEx || /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/gi;
                var match = val.match(cRegEx) || false;
                return Boolean(match);
                break;

            case "url":
                cRegEx = cRegEx || /^(((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+)?|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)$/;
                var match = val.match(cRegEx) || false;
                return Boolean(match);
                break;

            case "date":
                var dateType = $(input).data('datetype') || "US" || "EU"; //Custom, US (YYYY-MM-DD) or European (DD-MM-YYYY)
                switch (dateType) {
                    case "US":
                        cRegEx = cRegEx || /^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/;
                        break;

                    case "EU":
                        cRegEx = cRegEx || /^[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}$/;
                        break;

                    default:
                        //custom regex!!!
                        cRegEx = cRegEx || dateType;
                        break;
                }
                var match = val.match(cRegEx) || false;
                return Boolean(match);
                break;

            case "file":
                return (val.length ? true : false);
                break;

            default:
                // Why check a field that doesn't match ANY type? :-)
                // type="submit", type="hidden", and also "reset", "button", "foo"
                return true;
                break
        }
    };

    /**
     * "Paints" the input and its span.error with the error,
     * or, if none, removes its (possible) paint
     * @param input the DOM input field
     * @param blur Whether we want to valideight the field after a keypress(false) or a field blur(true)
     * @param error tells if you want to show the span.error or just the input.error field. Useful for "live"
     *        validation, for you only need to show the error message on blur or on on submission
     */
    paintFields = function(input, blur) {
        // no need to check anything if we don't want to show errors...
        var parent = $(input).closest(".valideight-box");
         
        if (checkValideightInput(input)) {
            parent.removeClass("has-error has-error-message")
                    .addClass("has-success");
            return true;
        }
        else {
            parent.removeClass("has-success").addClass("has-error");
            // if we are blurring the field, we must add the error label:
            if (blur) parent.addClass("has-error-message");
            return false;
        }
    };

    /**
     * Validates all input fields in the given form
     * @param form . The jQuery object $(form) to validate.
     */
    valideightForm = function(form) {
        // console.log(form);
        var parent = parent || false;
        var req    = form.find("input:not([data-novalideight] input, input[data-novalideight]), " +
                     "textarea:not([data-novalideight] textarea, textarea[data-novalideight]), " +
                     "*[data-forcevalideight]");
        var errors = false;
        for (var i = 0; i < req.length; ++i) {
            correct = paintFields(req[i], true);
            if (!correct) { //if we have a wrong field...
                errors = true;
            }
        }
        if (!errors) return true;
        else return false;
    };

    /**
     * Triggers on form submission and processes the validation
     */
    this.on("submit", function(e) {
        //submitting form
        var valid = valideightForm($(this));
        if (valid === true) {
            console.log("Valid!");
            if (typeof options.successCallback === 'function') {
                e.preventDefault(); e.stopPropagation();
                options.successCallback();
            }
            return true;
        } else if (valid === false) {
            console.log("Not valid!");
            options.errorCallback();
            return false;
        }
        else return "crocodiles love to procastinate";
    });

    /**
     * General DOM listeners
     */
    // Fields that we do NOT want to validate
    this.on("keyup", "input:not([data-novalideight] input, input[data-novalideight]), " +
                     "textarea:not([data-novalideight] textarea, textarea[data-novalideight])" , function() {
        paintFields(this, false);
    });
    this.on("blur",  "input:not([data-novalideight] input, input[data-novalideight]), " +
                     "textarea:not([data-novalideight] textarea, textarea[data-novalideight]), " +
                     "select, *[data-forcevalideight]", function() {
        paintFields(this, true);
    });

    restart(); // start or restart valideight() without reloading listeners
    form.attr("novalidate", "").addClass("valideight").removeAttr("data-valideight");
    if (typeof options.onValideightReady === 'function') {
        options.onValideightReady();
    }
};

$(document).ready(function() {
    //on DOM Ready
    var forms = $("form[data-valideight]");
    if (forms.length) {
        forms.valideight(valideightGlobals);
    }
});