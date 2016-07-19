<ul class="list-unstyled">
    <li class="list-group-item-<?= $cacheFolderWritable ? 'success' : 'danger' ?>">
        <p>
            Cache folder <code>/app/cache</code> writable: <b><?= $cacheFolderWritable ? 'YES' : 'NO' ?></b>
        </p>
    </li>
    <li class="list-group-item-<?= $configFileWritable ? 'success' : 'warning' ?>">
        <p>
            Config file <code>/app/config/config.ini</code> file writable: <b><?= $configFileWritable ? 'YES' : 'NO' ?></b>
        </p>
    </li>
    <li class="list-group-item-<?= $versionWritable ? 'success' : 'warning' ?>">
        <p>
            <code>/app/versioning</code> file writable: <b><?= $versionWritable ? 'YES' : 'NO' ?></b>
            <?= !$versionWritable ? "<br>You'll not be able to update the framework automatically" : ""?>
        </p>
    </li>
    <li class="list-group-item-<?= $appWritable ? 'success' : 'warning' ?>">
        <p>
            Folder <code>/app/*</code> writable: <b><?= $appWritable ? 'YES' : 'NO' ?></b>
            <?= !$appWritable ? "<br>You'll not be able to update the framework automatically" : ""?>
        </p>
    </li>
    <li class="list-group-item-<?= $PDO_Supported ? 'success' : 'danger' ?>">
        <p>
            PHP supports <code>PDO</code> class (databases): <b><?= $PDO_Supported ? 'YES' : 'NO' ?></b>
        </p>
    </li>
    <li class="list-group-item-<?= $shell_execSupported ? 'success' : 'warning' ?>">
        <p>
            PHP supports <code>shell_exec()</code> function: <b><?= $shell_execSupported ? 'YES' : 'NO' ?></b>
        </p>
    </li>
    <li class="list-group-item-info">
        <p>
            Database support enabled (change from <code>config.ini</code>): <b><?= $webappDatabaseEnabled ? 'YES' : 'NO' ?></b>
        </p>
    </li>
    <?php if ($webappDatabaseEnabled) { ?>
        <li class="list-group-item-<?= $webappConnexionParamsOK ? 'success' : 'danger' ?>">
            <p>
                Valid <code>MySQL</code> connexion params: <b><?= $webappConnexionParamsOK ? 'YES' : 'NO' ?></b>
            </p>
        </li>
    <?php } ?>
    <li class="list-group-item-info">
        <p>
            Vendor (plugins) enabled? (folder <code><?= $vendorsFolder ?></code>): <b><?= $vendorsEnabled ? 'YES' : 'NO' ?></b>
        </p>
    </li>
</ul>