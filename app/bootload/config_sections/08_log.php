<?php $section = "Log"; $ci = $config_INI[$section];?>
<h4><?= $section ?></h4>
<form action="?step=8&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>" style="_display:none">
    <div class="row form-group">
        <div class="col-xs-12">
            <p class="help-block">Log each <code>Fatal Error</code> to an external database<br/>
            You can configure it from <code>/app/kernel/Logger.php</code></p>
        </div>
        <div class="col-md-2 col-xs-4">
            <label>Log Enabled</label>
        </div>
        <div class="col-xs-6">
            <select name="LOG_ENABLED" class="form-control _toggle" data-toggle-yes="._log_enabled">
                <option value="1" <?= $ci['LOG_ENABLED'] == '1' ? 'selected="selected"' : '' ?>>Yes</option>
                <option value="0" <?= $ci['LOG_ENABLED'] == '0' ? 'selected="selected"' : '' ?>>No</option>
            </select>
        </div>
    </div>
    <div class="row form-group _log_enabled" style="<?= $ci['LOG_ENABLED'] == '0' ? 'display:none' : '' ?>">
        <div class="col-xs-12">
            <p class="help-block">Receive an email on each <code>Fatal Error</code></p>
        </div>
        <div class="col-md-2 col-xs-4">
            <label>Email Enabled</label>
        </div>
        <div class="col-xs-6">
            <select name="EMAIL_ENABLED" class="form-control">
                <option value="1" <?= $ci['EMAIL_ENABLED'] == '1' ? 'selected="selected"' : '' ?>>Yes</option>
                <option value="0" <?= $ci['EMAIL_ENABLED'] == '0' ? 'selected="selected"' : '' ?>>No</option>
            </select>
        </div>
        <?php unset($ci['EMAIL_ENABLED']) ?>
    </div>
    <?php foreach ($ci as $k => $v) { ?>
        <?php if ($k === 'LOG_ENABLED') continue; ?>
        <div class="row form-group _log_enabled" style="<?= $ci['LOG_ENABLED'] == '0' ? 'display:none' : '' ?>">
            <div class="col-md-2 col-xs-4">
                <label><?= $k ?></label>
            </div>
            <div class="col-xs-6">
                <input type="text" name="<?= $k ?>" value="<?= $v ?>" class="form-control"/>
            </div>
        </div>
    <?php } ?>
    <div class="form-group">
        <input type="submit" value="Save &quot;<?= $section ?>&quot;" class="btn btn-success btn-sm"/>
    </div>
</form>