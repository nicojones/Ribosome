<?php $section = "PHP_ini"; $id = 2000;?>
<h4><?= $section ?></h4>
<p class="help-block">Set php.ini settings here. This values will be executed as <code>ini_set('key', 'value')</code>.</p>
<form action="?step=3&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>" style="_display:none">
    <?php foreach ($config_INI[$section] as $k => $v) { ?>
        <div class="row form-group _add_remove" id="_add_remove_<?= $id ?>">
            <div class="col-md-2 col-xs-4">
                <label class="_add_remove_label" contenteditable="true" data-id="<?= $id ?>"><?= $k ?></label>
            </div>
            <div class="col-xs-6">
                <input id="_input_global_<?= $id ?>" type="text" name="<?= $k ?>" value="<?= $v ?>" class="form-control"/>
            </div>
            <div class="col-xs-1">
                <span class="plus _add_remove_button" data-action="remove" data-id="<?= $id ?>">-</span>
            </div>
        </div>
        <?php ++$id ?>
    <?php } ?>
        <div class="row" id="_add_remove_box">
            <div class="col-xs-10">
                <span class="_add_remove_button plus" data-action="add">+</span>
            </div>
            <span id="_add_remove_content" style="display:none">
                <div class="row form-group _add_remove" id="_add_remove_{{ID}}">
                    <div class="col-md-2 col-xs-4">
                        <label class="_add_remove_label" contenteditable="true" data-id="{{ID}}">&lt;VAR_NAME&gt;</label>
                    </div>
                    <div class="col-xs-6">
                        <input id="_input_global_{{ID}}" type="text" name="" value="" class="form-control"/>
                    </div>
                    <div class="col-xs-1">
                        <span class="plus _add_remove_button" data-action="remove" data-id="{{ID}}">-</span>
                    </div>
                </div>
            </span>
        </div>
    <div class="form-group">
        <input type="submit" value="Save &quot;<?= $section ?>&quot;" class="btn btn-success btn-sm"/>
    </div>
</form>