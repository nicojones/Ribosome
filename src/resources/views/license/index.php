<div class="screen_centered" style="text-align: left;">

    <h3>Hi, <?php echo $license['owner_name'];?></h3>
    <p>some info for you:</p>
    <ul>
        <li>You've downloaded the framework <b><?php echo $license['times_downloaded']?></b> times.</li>
        <li>Your license expires on <b><?php echo date('d M Y', strtotime($license['valid_until']));?></b>
            (in <b><?php echo round((strtotime($license['valid_until']) - strtotime($license['now'])) / 86400) ?></b> days)</li>
        <li>Your current version is <b><?php echo $license['version_current']?></b> (you started with <?php echo $license['version_original'];?>)</li>
        <li>License key: <b><?php echo $license['license_key'];?></b></li>
    </ul>

    <h4>Things you can do:</h4>
    <p>Download the Framewok (v. <?php echo $license['version_current']?>) as a <a href="">.zip</a> file.</p>
    <p>See the <a href="<?php $this->path('LicenseDocs', [':version' => $license['version_current'], ':file' => 'index.html']);?>"
            >docs</a> for <?php echo $license['version_current']?>.</p>

</div>