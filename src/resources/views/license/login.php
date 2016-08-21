<div class="screen_centered" style="margin-top: 10%;">
    <div class="col-md-8 col-md-offset-2">
        <form method="POST" action="<?php $this->path('LicenseAuth');?>" class="form" style="">
            <h2 class="centered center-text">Enter your license number to continue</h2>
            <p>The license number is located in <code>/app/config/config.ini</code></p>
            <div class="form-group">
                <input name="license" placeholder="License Key" type="text" autofocus="" class="form-control" required="" autocomplete="off"/>
            </div>
            <input type="submit" value="Submit" class="btn btn-success"/>
        </form>
    </div>
</div>