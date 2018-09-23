<?php $section = "Database"; $ci = $config_INI[$section];?>
<h4><?= $section ?></h4>
<p class="help-block">Feel free to use or not use a database for your project.<br/>
If you do, you must set the proper <code>host</code>, <code>database name</code> and <code>credentials</code>.</p>
<form action="<?php echo BOOTLOAD_URL ?>&step=1&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>" style="_display:none">
    <div class="row form-group">
        <div class="col-md-2 col-xs-4">
            <label for="_db-support">Database support?</label>
        </div>
        <div class="col-xs-6">
            <select name="DB_SUPPORT" id="_db-support" class="_toggle form-control" data-toggle-yes="._db-support-yes">
                <option value="1" <?= $config_INI[$section]['DB_SUPPORT'] == '1' ? 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?= $config_INI[$section]['DB_SUPPORT'] == '0' ? 'selected="selected"' : ''?>>No</option>
            </select>
        </div>
    </div>
    <div class="row form-group _db-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Host name</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="HOST" value="<?= $ci['HOST'];?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group _db-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Database name</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="DB_NAME" value="<?= $ci['DB_NAME'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group _db-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Username</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="USERNAME" value="<?= $ci['USERNAME'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group _db-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Password</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="PASSWORD" value="<?= $ci['PASSWORD'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" value="Save &quot;<?= $section ?>&quot;" class="btn btn-success btn-sm"/>
    </div>
</form>