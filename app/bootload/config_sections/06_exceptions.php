<?php $section = "Exceptions"; $id = 3000;?>
<h4><?= $section ?></h4>
<p class="help-block">Set default texts for the <code>exceptions</code> that the framework can <code>throw</code> and <code>catch</code>.</p>
<form action="<?php echo BOOTLOAD_URL ?>&step=6&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>" style="_display:none">
    <?php foreach ($config_INI[$section] as $k => $v) { ?>
        <div class="row form-group _add_remove" id="_add_remove_exception_<?= $id ?>">
            <div class="col-md-2 col-xs-4">
                <label class="_add_remove_label" contenteditable="true" data-id="<?= $id ?>"
                       data-class="_input_exception_"><?= $k ?></label>
            </div>
            <div class="col-xs-6">
                <input id="_input_exception_<?= $id ?>" class="_input_exception_<?= $id ?> form-control"
                       type="text" name="<?= $k ?>" value="<?= htmlentities($v) ?>"/>
            </div>
            <div class="col-xs-1">
                <span class="plus _add_remove_button" data-action="remove" data-id="_add_remove_exception_<?= $id ?>">-</span>
            </div>
        </div>
        <?php ++$id ?>
    <?php } ?>
        <div class="row">
            <div class="col-xs-12">
                <span class="_add_remove_button plus" data-action="add">+</span>
            </div>
            <span style="display:none">
                <div class="row form-group _add_remove" id="_add_remove_exception_{{ID}}">
                    <div class="col-md-2 col-xs-4">
                        <label class="_add_remove_label" contenteditable="true" data-id="{{ID}}"
                               data-class="_input_exception_">&lt;VAR_NAME&gt;</label>
                    </div>
                    <div class="col-xs-6">
                        <input id="_input_exception_{{ID}}" class="_input_exception_{{ID}} form-control" type="text" name="" value=""/>
                    </div>
                    <div class="col-xs-1">
                        <span class="plus _add_remove_button" data-action="remove" data-id="_add_remove_exception_{{ID}}">-</span>
                    </div>
                </div>
            </span>
        </div>
    <div class="form-group">
        <input type="submit" value="Save &quot;<?= $section ?>&quot;" class="btn btn-success btn-sm"/>
    </div>
</form>