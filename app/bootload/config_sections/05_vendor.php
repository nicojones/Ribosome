<?php $section = "Vendor"; $ci = $config_INI[$section];?>
<h4><?= $section ?></h4>
<p class="help-block">Set here the settings for the vendor plugins</p>
<form action="<?php echo BOOTLOAD_URL ?>&step=5&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>">
    <div class="row form-group">
        <div class="col-md-2 col-xs-4">
            <label>Vendor (plugins) enabled?</label>
        </div>
        <div class="col-xs-6">
            <select name="VENDOR_ENABLED" class="form-control _toggle" data-toggle-yes="._vendor_enabled">
                <option value="1" <?= $ci['VENDOR_ENABLED'] == '1' ? 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?= $ci['VENDOR_ENABLED'] == '0' ? 'selected="selected"' : ''?>>No</option>
            </select>
        </div>
    </div>
    <div class="row form-group _vendor_enabled" style="<?= $ci['VENDOR_ENABLED'] == '0' ? 'display:none' : '' ?>">
        <div class="col-xs-12">
            <p class="help-block">Note: If you ever change this, you'll need to manually edit <code>/.htaccess</code> to allow
            for proper reading of all assets!</p>
        </div>
        <div class="col-md-2 col-xs-4">
            <label>Vendor folder</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="VENDOR_FOLDER" value="<?= $ci['VENDOR_FOLDER'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" value="Save &quot;<?= $section ?>&quot;" class="btn btn-success btn-sm"/>
    </div>
</form>