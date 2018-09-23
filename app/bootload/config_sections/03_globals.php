<?php $section = "Globals"; $id = 1; $ci = $config_INI[$section];?>
<h4><?= $section ?></h4>
<p class="help-block"><code>DEFINE("GLOBAL_VARS", "and its values")</code> here and use them everywhere in the project.</p><br/>
<form action="<?php echo BOOTLOAD_URL ?>&step=3&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>" style="_display:none">
    <div class="row form-group">
        <div class="col-xs-10">
            <p class="help-block">The required <code>SYSTEM</code> global vars are sandwiched with <code>__&lt;VAR-NAME&gt;__</code>
            and cannot be removed</p>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-2 col-xs-4">
            <label>__ENVIRONMENT__</label>
        </div>
        <div class="col-xs-6">
            <select name="__ENVIRONMENT__" class="form-control">
                <option value="prod" <?= $ci['__ENVIRONMENT__'] == 'prod' ? 'selected="selected"' : '' ?>>Production</option>
                <option value="dev"  <?= $ci['__ENVIRONMENT__'] == 'dev'  ? 'selected="selected"' : '' ?>>Developement</option>
            </select>
        </div>
        <?php unset($ci['__ENVIRONMENT__']); ?>
    </div>
    <div class="row form-group">
        <div class="col-xs-12">
            <p class="help-block">
                If your website is not on the root (<code>example.com</code>) but instead in a folder named <code>webapp</code>
                reachable via <code>example.com/my-website</code>, then:<br/>
                <code>__PATH__ = "my-website"</code><br/>
                <code>__FOLDER__ = "webapp"</code><br/>
                Leave both fields blank otherwise.
            </p>
        </div>
        <div class="col-md-2 col-xs-4">
            <label>__PATH__</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="__PATH__" value="<?= $ci['__PATH__'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-2 col-xs-4">
            <label>__FOLDER__</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="__FOLDER__" value="<?= $ci['__FOLDER__'] ?>" class="form-control"/>
        </div>
        <?php unset($ci['__PATH__']); ?>
        <?php unset($ci['__FOLDER__']); ?>
    </div>

    <div class="row form-group">
        <div class="col-xs-12">
            <p class="help-block">
                Set the path where the <code>Minify</code> looks for assets (without leading nor trailing slash).<br/>
                <code>__MINIFY_FILES__ = "src/resources/views/assets" is the default</code>
            </p>
        </div>
        <div class="col-md-2 col-xs-4">
            <label>__MINIFY_FILES__</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="__MINIFY_FILES__" value="<?= $ci['__MINIFY_FILES__'] ?>" class="form-control"/>
        </div>
        <?php unset($ci['__MINIFY_FILES__']); ?>
    </div>

    <div class="row form-group">
        <div class="col-xs-12">
            <p class="help-block">Site details, usually displayed in the <code>&lt;head&gt;&lt;meta ...&gt;&lt;/head&gt;</code>,
                <code>&lt;head&gt;&lt;title ...&gt;&lt;/head&gt;</code> of the page</p>
        </div>
    </div>
    <?php foreach ($ci as $k => $v) { ?>
        <?php if (substr($k, 0, 6) != '__SITE') continue; ?>
        <div class="row form-group">
            <div class="col-md-2 col-xs-4">
                <label><?= $k ?></label>
            </div>
            <div class="col-xs-6">
                <input type="text" name="<?= $k ?>" value="<?= $v ?>" class="form-control"/>
            </div>
            <?php unset($ci[$k]); ?>
        </div>
    <?php } ?>


    <div class="row form-group">
    <div class="col-xs-12">
        <p class="help-block">Other System-wide globals</p>
    </div>
    </div>
    <?php foreach ($ci as $k => $v) { ?>
        <?php if (substr($k, 0, 2) != '__') continue; ?>
        <div class="row form-group">
            <div class="col-md-2 col-xs-4">
                <label><?= $k ?></label>
            </div>
            <div class="col-xs-6">
                <input type="text" name="<?= $k ?>" value="<?= $v ?>" class="form-control"/>
            </div>
        </div>
    <?php } ?>




    <div class="row form-group">
        <div class="col-xs-10">
            <br/>
            <p class="help-block">Under here will be shown the <code>USER</code> global vars. If you sandwich it with
                <code>__&lt;VAR-NAME&gt;__</code> you'll only be able to delete them through manually editing <code>config.ini</code></p>
        </div>
    </div>
    <?php foreach ($ci as $k => $v) { ?>
        <?php if (substr($k, 0, 2) == '__') continue; ?>
        <div class="row form-group _add_remove" id="_add_remove_global_<?= $id ?>">
            <div class="col-md-2 col-xs-4">
                <label class="_add_remove_label" contenteditable="true" data-id="<?= $id ?>" data-class="_input_global_"><?= $k ?></label>
            </div>
            <div class="col-xs-6">
                <input id="_input_global_<?= $id ?>" class="_input_global_<?= $id ?> form-control" type="text" name="<?= $k ?>" value="<?= $v ?>"/>
            </div>
            <div class="col-xs-1">
                <span class="plus _add_remove_button" data-action="remove" data-id="_add_remove_global_<?= $id ?>">-</span>
            </div>
        </div>
        <?php ++$id ?>
    <?php } ?>
        <div class="row">
            <div class="col-xs-10">
                <span class="_add_remove_button plus" data-action="add">+</span>
            </div>
            <span style="display:none">
                <div class="row form-group _add_remove" id="_add_remove_global_{{ID}}">
                    <div class="col-md-2 col-xs-4">
                        <label class="_add_remove_label" contenteditable="true" data-id="{{ID}}" data-class="_input_global_">&lt;VAR_NAME&gt;</label>
                    </div>
                    <div class="col-xs-6">
                        <input id="_input_global_{{ID}}" class="_input_global_{{ID}} form-control" type="text" name="" value=""/>
                    </div>
                    <div class="col-xs-1">
                        <span class="plus _add_remove_button" data-action="remove" data-id="_add_remove_global_{{ID}}">-</span>
                    </div>
                </div>
            </span>
        </div>
    <div class="form-group">
        <input type="submit" value="Save &quot;<?= $section ?>&quot;" class="btn btn-success btn-sm"/>
    </div>
</form>