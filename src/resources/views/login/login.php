<div class="screen_centered" style="margin-top: 10%;">
    <div class="col-md-8 col-md-offset-2">
        <form method="POST" action="<?php $this->path('LoginAuth');?>" class="form" style="">
            <h2 class="centered center-text">Please login</h2>
            <p>default is <b>user</b>/<b>pass</b><br/>
                change it from config.ini or set up a database</p>
            <div class="form-group">
                <input name="username" placeholder="username" type="text" autofocus="on"
                       class="form-control" required="" autocomplete="off"/>
            </div>
            <br/>
            <div class="form-group">
                <input name="password" placeholder="password" type="password" class="form-control" required=""/>
            </div>
            <br/>
            <input type="submit" value="Login" class="btn btn-success"/>
        </form>
    </div>
</div>