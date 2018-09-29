<span style="width:94%;margin-left:2%;margin-right:2%;border:1px solid #aaa;border-radius:4px;position:relative;display:inline-block;
      clear:both;padding:15px; background-color: rgb(250,250,250);box-shadow: 7px 15px 10px #bbb;
      -moz-box-shadow: 7px 15px 10px #bbb;-ms-box-shadow: 7px 15px 10px #bbb;-webkit-box-shadow: 7px 15px 10px #bbb;
      -o-box-shadow: 7px 15px 10px #bbb">

    <h1>
        <img src="<?php echo __SITE_URL__ . $this->asset('/images/icons/favicon.png', TRUE);?>" style="border-radius:100% !important;position:relative !important;float:left !important;top:-14px !important;margin-right:8px !important;"/>
        Hello!
    </h1>

    <br/>
    <!--    Here goes the body.-->
    <div>
        <?php echo $_body;?>
    </div>

    <!--    Here starts the footer-->
    <div style="display:block;clear:both;width:100%">
        <img src="<?php echo __SITE_URL__ . $this->asset('/images/icons/favicon.png', TRUE);?>"
             style="float: left; max-height: 30px; margin: 10px 10px 0px 5px;"/>
    </div>
    <small style="color: #999;display:block;clear:both;width:100%">
        If you did not sign up at <?php echo __SITE_URL__;?>, please ignore this email.<br/>
        You can unsubscribe at any time by clicking
        <a href="<?php echo __SITE_URL__ ?> target="_blank">here</a> and visiting the Settings page
    </small>
</span>