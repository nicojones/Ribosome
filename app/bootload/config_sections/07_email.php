<?php $section = "Email"; ?>
<h4><?= $section ?></h4>
<p class="help-block">
    Set here some default information for the emails you will (or potentially can) send.
</p>
<form action="<?php echo BOOTLOAD_URL ?>&step=7&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>" style="_display:none">
    <?php foreach ($config_INI[$section] as $k => $v) {
        if (is_array($v)) {
            $aux = 0;
            foreach ($v as $w) {
                $config_INI[$section][$k.'[' . $aux++ . ']'] = $w;
            }
            unset($config_INI[$section][$k]);
        }

    } ?>

    <?php foreach ($config_INI[$section] as $k => $v) { ?>
        <div class="row form-group _db-support">
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