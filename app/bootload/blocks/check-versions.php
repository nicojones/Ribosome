<?php if ($checking) { ?>
<h4>Your current version: <?php echo FW_VERSION;?></h4>
<a href="?versioning=CHANGELOG.txt" target="_blank" class="btn btn-default">Changelog</a>
<br/><br/>
<?php if (version_compare(FW_VERSION, $latest, '=')) { ?>
    <label class="label label-info">You're up to date</label>
<?php } elseif (version_compare(FW_VERSION, $latest, '<')) { ?>
    <label class="label label-success">There's a new version!</label>
<?php } ?>
<h4>Latest version: <?php echo $latest;?></h4>
<a href="?action=update-framework&version=<?php echo $latest;?>" class="_async btn btn-warning pull-left <?php echo ($latest == FW_VERSION) ? ' disabled ' : '' ?>"
   data-loading-text="Upgrading..." data-loading="frameworkversions_loader"
   data-beforesend="confirm('Upgrade to <?php echo $latest;?>?')"
   <?php echo ($latest == FW_VERSION) ? ' disabled="disabled" ' : '' ?>
   data-response-box="frameworkversions_results">Upgrade to <?php echo $latest;?></a>
<a href="<?php echo FW_UPDATE_HOST;?>/version/v-<?php echo $latest;?>/CHANGELOG.txt" target="_blank" class="btn btn-default">See changelog (new tab)</a>
<br/><br/>
<h4>Other versions:</h4>
<ul class="list-unstyled">
    <?php foreach ($res->versions as $versionID) { ?>
        <li>
            <a href="?action=update-framework&version=<?php echo $versionID;?>" class="_async btn btn-warning pull-left"
               data-loading-text="Upgrading..." data-loading="frameworkversions_loader"
               data-beforesend="confirm('Upgrade to <?php echo $versionID;?>?')"
               data-response-box="frameworkversions_results">Upgrade to <?php echo $versionID;?></a>
            <a href="<?php echo FW_UPDATE_HOST;?>/version/v-<?php echo $versionID;?>/CHANGELOG.txt" target="_blank" class="btn btn-default">See changelog (new tab)</a><br/><br/>
        </li>
    <?php } ?>
</ul>

<?php } elseif (!$checking) { ?>
<p>Updated to version <?php echo $version ?>!</p>

<h2 style='color:green'>Yeah!</h2>
<a href='<?php echo FW_UPDATE_HOST?>/version/v-<?php echo $version?>/CHANGELOG.txt' target="_blank" class="btn btn-info">See CHANGELOG.txt file</a>

<br/>
<Br/>
<small>or read it just here</small>
<h3>Changelog</h3>
<pre><?php echo nl2br($changelog);?></pre>

<p>Please <a href="">reload</a> the page before doing any other operation</p>
<?php } ?>