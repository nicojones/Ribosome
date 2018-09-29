<?php if ($flash = \Core\Helpers\Flash::flush()) { // Alerts and Notifications. Use with Session::setAlert() ?>
    <div class="row" style="margin-top:-1em">
        <div class="col-xs-12 notice notice-<?= $flash['type'];?>" role="alert">
            <a href="javascript:;" class="close" style="position: absolute;right: 30px"
               onclick="this.parentNode.parentNode.remove()">&times;</a>
            <strong><?= $flash['message'];?></strong>
        </div>
    </div>
    <br/>
<?php } ?>