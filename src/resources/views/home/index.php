<div class="screen_centered">
    <h1><?php echo __SITE_NAME__; ?></h1>
    <br/>
    <h5><?php echo __SITE_DESCR__;?></h5>
    <br/>
    <br/>
    <p>Seems you got to configure it right!<br/>Now you can start developing</p>
    <br/>


    <div class="row">
        <div class="row col-xs-12 col-sm-8 col-sm-offset-2 col-lg-4 col-lg-offset-4">
            <div class="row">
                <div class="col-xs-4">
                    <a href="https://github.com/nicojones/Ribosome" target="_blank">
                        <img src="<?php asset('images/icons/homepage_icons/github.png')?>"
                             height="64px" alt="github">
                        <br/>
                        see on Github
                    </a>
                </div>
                <div class="col-xs-4">
                    <a href="https://rawgit.com/nicojones/Ribosome/master/docs/index.html"
                       target="_blank">
                        <img src="<?php asset('images/icons/homepage_icons/doc.png')?>" alt="docs" height="64px">
                        <br/>
                        docs
                    </a>
                </div>
                <div class="col-xs-4">
                    <a href="https://nico.kupfer.es/"
                       target="_blank">
                        <img src="<?php asset('images/icons/homepage_icons/kupfer.ico')?>" alt="kupfer.es" height="64px">
                        <br/>
                        my website
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-lg-offset-4">
                <br>
                <br>
                <br>
                (try the login!)
                <a href="<?php path('Login');?>" class="btn btn-default btn-sm">Login</a>
            </div>
        </div>
    </div>
</div>