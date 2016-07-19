function toggleSection(id) {
    var obj = document.getElementById('section_' + id);
    obj.className = obj.className.match(/(open)/) ? obj.className.replace('open','') : (obj.className + ' open');
    var plus = document.getElementById('toggle_plus_' + id);
    plus.className = plus.className.match(/(open)/) ? plus.className.replace('open','') : (plus.className + ' open');
}

function gid(id) {
    try {
        return document.getElementById(id);
    } catch (e) {
        return null;
    }
}

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

$(document).ready(function() {
    /**
     * Checking updates now
     */
    if (navigator.onLine) {
        $("#check_for_updates_button").click();
    } else {
        $("#check_for_updates_box").hide();
    }

    $("._toggle").on('change', function() {
        if (Boolean(Number($(this).val()))) {
            $($(this).attr('data-toggle-yes')).show();
            $($(this).attr('data-toggle-no')).hide();
        } else {
            $($(this).attr('data-toggle-yes')).hide();
            $($(this).attr('data-toggle-no')).show();
        }
    });

    $("._form-async").on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            dataType: 'JSON',
            data: form.serialize(),
            success: function(data) {
                notifyMe(data.responseData.message, data.success == 1 ? 'success' : 'error');
                if (form.attr('data-response-box')) {
                    $("#" + form.attr('data-response-box')).html(data.responseData.content);
                }
                if (form.attr('data-success')) {
                    eval('(' + form.attr('data-success') + ')');
                }
            }
        });
    });

    $("#configure_table_form").on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var tableName = $("#input_table_name").val();
        if (!tableName) {
            alert('Specify a name for the table');
            return;
        }
        var form = $(this);
        var rows = $("._database_row");
        var postObj = {};
        $.each(rows, function(key, value) {
            if ($(value).attr('data-id') !== '{{ID}}') {
                postObj[$(value).attr('data-id')] = {
                    col_name: $(value).find('.input_database_colname').val(),
                    col_type: $(value).find('.input_database_coltype').val(),
                    col_key: Number($(value).find('.input_database_key').is(':checked')),
                    col_autoincrement: Number($(value).find('.input_database_autoincrement').is(':checked'))
                };
            }
        });
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            dataType: 'JSON',
            data: {
                name: tableName,
                fields: postObj
            },
            success: function(data) {
                notifyMe(data.responseData.message, data.success == 1 ? 'success' : 'error');
                $("#tables_in_the_database").html(data.responseData.content);
                $("#configure_table_form > ._database_row").remove();
            }
        });
    });

    $("#raw_query_textarea").on('keydown', function(e) {
        console.log(e);
        if (e.keyCode == 13 && (e.metaKey || e.ctrlKey)) {
            e.preventDefault();
            e.stopPropagation();
            $(e.currentTarget).closest('form').trigger('submit');
        }
    });

    $("#class_name, #class_model_also").on('keyup change click', function() {
        var input = $("#class_name"),
            val = input.val() || '&lt;NAME&gt;',
            model = $("#class_model_also").is(':checked');
        $("#model_name_placeholder").html(val);
        if (input.val()) {
            $("#will_generate_class").html("Will generate code for:<br/>" + val + "Controller" + ( model ? ("<br/>" + val + "Model") : "" ) );
        }
    });
});

var globals_id = 1000;
$(document).on('click', '._add_remove_button', function() {
    if ($(this).attr('data-action') == 'remove' && confirm('Remove?')) {
        $("#" + $(this).attr('data-id')).remove();
    } else {
        $(this).parent().parent().before(
            $(this).parent().next().html().replace('{{ID}}', globals_id++, 'gi')
        );
    }
});
$(document).on('keyup', '._add_remove_label', function() {
    var label = $(this),
        id = label.attr('data-id'),
        labelClass = label.attr('data-class'),
        input = $("." + labelClass + id);
    console.log(input, label.html() + label.val());
    input.attr('name', label.html() + label.val());
});
$(document).on('keydown', "body input, body textarea", function(e) {
    if (e.keyCode === 83 && (e.metaKey || e.ctrlKey)) {
        // Save button!
        e.preventDefault();
        e.stopPropagation();
        $(e.currentTarget).closest('form').trigger('submit');
    }
});

$(document).on('click', "._async", function(e) {
    e.preventDefault();
    e.stopPropagation();
    var a = $(this), buttonText = a.html();
    if (a.attr('data-beforesend') && !eval('(' + a.attr('data-beforesend') + ')')) {
        return false;
    }

    a.addClass('disabled');
    if (a.attr('data-loading-text')) {
        a.html(a.attr('data-loading-text'));
    }
    if (a.attr('data-loading')) {
        $("#" + a.attr('data-loading')).show();
    }
    $.ajax({
        url: a.attr('href'),
        type: 'POST',
        dataType: 'JSON',
        success: function(data) {
            a.html(buttonText).removeClass('disabled');
            if (a.attr('data-loading')) {
                $("#" + a.attr('data-loading')).hide();
            }
            if (data.success == 1) {
                notifyMe(data.responseData.message, 'success');
                if (a.attr('data-success')) {
                    eval('(' + a.attr('data-success') + ')');
                }
            } else {
                notifyMe(data.responseData.message, 'error');
            }
            if (a.attr('data-response-box')) {
                $("#" + a.attr('data-response-box')).html(data.responseData.content);
            }
        }
    });
});

function writeControllerCode(data) {
    gid('createclass_controller').value = data.controller;
    gid('createclass_model').innerHTML = data.model;
    gid('createclass_controllername').value = data.controllerName;
    gid('createclass_modelname').innerHTML = data.modelName;
}