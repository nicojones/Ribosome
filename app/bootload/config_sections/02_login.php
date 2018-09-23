<?php $section = "Login"; $ci = $config_INI[$section];?>
<h4><?= $section ?></h4>
<p class="help-block">You can use a single and fixed <code>username</code> and <code>password</code> to access the login area or,
if you plan to have multiple users, they can be automatically authenticated from the database.</p>
<form action="<?php echo BOOTLOAD_URL ?>&step=2&action=save_ini&ini_section=<?= $section ?>" method="post" class="_form-async"
      id="section_<?= $section ?>" style="_display:none">
    <div class="row form-group">
        <div class="col-md-2 col-xs-4">
            <label for="_login-support">Login with database?</label>
        </div>
        <div class="col-xs-6">
            <select name="DB_SUPPORT" id="_login-support" class="_toggle form-control" data-toggle-yes="._login-support-yes" data-toggle-no="._login-support-no">
                <option value="1" <?= $config_INI[$section]['DB_SUPPORT'] == '1' ? 'selected="selected"' : ''?>>Yes</option>
                <option value="0" <?= $config_INI[$section]['DB_SUPPORT'] == '0' ? 'selected="selected"' : ''?>>No</option>
            </select>
        </div>
    </div>
    <div class="row form-group _login-support-no" style="<?= $ci['DB_SUPPORT'] == '1' ? 'display:none' : ''?>">
        <div class="col-xs-12">
            <p class="help-block">You will have only one user. Set <code>username</code> and <code>password</code>
            as the credentials. You will need them in the <code>/login</code> page</p>
        </div>
    </div>
    <div class="row form-group _login-support-no" style="<?= $ci['DB_SUPPORT'] == '1' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Username</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="LOGIN_USERNAME" value="<?= $ci['LOGIN_USERNAME'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group _login-support-no" style="<?= $ci['DB_SUPPORT'] == '1' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Password</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="LOGIN_PASSWORD" value="<?= $ci['LOGIN_PASSWORD'] ?>" class="form-control"/>
        </div>
    </div>

    <div class="row form-group _login-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-xs-12">
            <p class="help-block">Set the table and column names that must be check to allow a user's authentication.<br/>
            For example, you want to do login using the table <code>users</code>, with credentials on the columns
            <code>username</code> and <code>password</code>.<br/>
            <code>$salt</code> is for enhanced security; I recommend at least 30 characters.
            <br/><br/>
            Note: unless <b>Database Support</b> is set to <b>YES</b> on the <b>Database</b> section, this will not work.</p>
        </div>
    </div>
    <div class="row form-group _login-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Login table</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="TABLE" value="<?= $ci['TABLE'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group _login-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Column name for user</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="TABLE_COLUMN_USER" value="<?= $ci['TABLE_COLUMN_USER'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group _login-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Column name for password</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="TABLE_COLUMN_PASS" value="<?= $ci['TABLE_COLUMN_PASS'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="row form-group _login-support-yes" style="<?= $ci['DB_SUPPORT'] == '0' ? 'display:none' : ''?>">
        <div class="col-md-2 col-xs-4">
            <label>Salt (random string)</label>
        </div>
        <div class="col-xs-6">
            <input type="text" name="SALT" value="<?= $ci['SALT'] ?>" class="form-control"/>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" value="Save &quot;<?= $section ?>&quot;" class="btn btn-success btn-sm"/>
    </div>
</form>