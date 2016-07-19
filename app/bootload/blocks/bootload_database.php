<?php $config_Login = $config_INI['Login']; ?>

<?php
    $tables = array_keys(queryIndexed($db, 'show tables', [], false)); ?>
    <div class="row" id="table_list" style="<?= count($tables) ? '' : 'display:none'?>">
        <div class="col-xs-12">
            <h4>Tables in the database</h4>
        </div>
        <div class="col-xs-12" id="tables_in_the_database">
            <?php foreach ($tables as $t) echo '<code>', $t, '</code>&nbsp;'; ?>
        </div>
    </div>
    <br/>
    <br/>

<?php if ($config_Login['DB_SUPPORT'] == '1') { ?>
    <div class="row">
        <div class="col-xs-12">
            <span style="<?= (in_array($config_Login['TABLE'], $tables)) ? '' : 'display:none'?>" id="login_table_configured">
                <h4>Login table <code><?= $config_Login['TABLE'] ?></code> is configured</h4>
                <p class="help-block">Nothing to do here :-)</p>
            </span>
            <span style="<?= (in_array($config_Login['TABLE'], $tables)) ? 'display:none' : ''?>" id="login_table_not_configured">
                <h4>Login table <code><?= $config_Login['TABLE'] ?></code> is <span class="danger">NOT</span> configured</h4>
                <p class="help-block">You can create it automatically by clicking on the following button.</p>
                <img id="_configure-user-login-table-loader" src="<?= LOADER?>" height="30px" style="display: none;" class="pull-left"/>
                <a href="?action=configure-user-login-table" class="_async btn btn-default"
                   data-loading-text="Creating table <?= $config_Login['TABLE'] ?>"
                   data-loading="_configure-user-login-table-loader"
                   data-response-box="tables_in_the_database"
                   data-success="$('#login_table_not_configured').hide() && $('#table_list, #login_table_configured').show()">
                    Create table <code><?= $config_Login['TABLE'] ?></code></a>
            </span>
        </div>
    </div>
    <br/>
    <br/>
<?php } ?>

<div class="row">
    <div class="col-xs-12">
        <h4>Add new tables</h4>
        <p>This tool lets you add new tables to the database</p>
    </div>
    <form action="?action=configure-new-table" method="post" id="configure_table_form">
        <div class="col-xs-12">
            <input type="text" placeholder="Table Name" name="TABLE_NAME" class="form-control" id="input_table_name"
                autocomplete="off"/>
            <br/>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-3">Name</div>
                <div class="col-xs-4">Type</div>
                <div class="col-xs-1">Primary Key</div>
                <div class="col-xs-1">Auto Increment</div>
                <div class="col-xs-1"></div>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-10">
                    <span class="_add_remove_button plus" data-action="add">+</span>
                </div>
                <span style="display:none">
                    <div class="row form-group _add_remove _database_row" id="_add_remove_database_row_{{ID}}" data-id="{{ID}}">
                        <div class="col-xs-3">
                            <input type="text" class="_add_remove_label input_database_colname form-control"
                                   data-id="{{ID}}" data-class="_input_database_row_"/>
                        </div>
                        <div class="col-xs-4">
                            <select class="_input_database_row_{{ID}} form-control input_database_coltype" name="">
                                <option value="int(11)">int(11) - Integer</option>
                                <option value="int(1)">int(1) - Boolean</option>
                                <option value="varchar(250)">varchar(250) - Short text</option>
                                <option value="varchar(2000)">varchar(2000) - Medium text</option>
                                <option value="text">text - Long text</option>
                                <option value="date">date - Date</option>
                                <option value="datetime">datetime - Date and Time</option>
                                <option value="timestamp">timestamp - UNIX timestamp</option>
                                <option value="double">double - Decimal number</option>
                            </select>
                        </div>
                        <div class="col-xs-1">
                            <input class="_input_database_row_{{ID}} input_database_key form-control" type="checkbox" name="" value="1"/>
                        </div>
                        <div class="col-xs-1">
                            <input class="form-control input_database_autoincrement" type="radio" name="AUTO_INCREMENT" value="{{ID}}"/>
                        </div>
                        <div class="col-xs-1">
                            <span class="plus _add_remove_button" data-action="remove" data-id="_add_remove_database_row_{{ID}}">-</span>
                        </div>
                        <input type="hidden" name="ID" value="{{ID}}"/>
                    </div>
                </span>
            </div>
        </div>
        <input type="submit" value="Create Table" class="btn btn-success"/>
    </form>
</div>
<br/>
<br/>
<br/>
<div class="row">
    <div class="col-xs-12">
        <h4>Perform raw queries</h4>
        <p>Use it to perform raw queries or paste here the <code>.sql</code> from a newly added plugin.<br/>
        <code>Cmd + Return</code> (shortcut) to run it.</p>
    </div>
    <form action="?action=raw-query" method="post" class="_form-async" id="raw_query_form" data-response-box="raw_query_response_box"
        data-success="$('#raw_query_response_box').show()">
        <div class="col-xs-12 form-group">
            <label for="raw_query_textarea">Query</label>
            <br/>
            <textarea id="raw_query_textarea" name="query" class="form_control"
                      placeholder="Write or paste the query here" style="width: 60%;"></textarea>
        </div>
        <input type="submit" value="Run query" class="btn btn-success"/>
    </form>
    <br/>
    <br/>
    <div class="col-xs-12" id='raw_query_response_box' style="display:none"></div>
</div>
