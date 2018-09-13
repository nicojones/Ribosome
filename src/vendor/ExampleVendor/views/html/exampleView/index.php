<h1>ExampleVendorController successfully loaded</h1>
<h3>(from the Plugin)</h3>
<br>
<br>
<p>This GLOBAL var is ONLY for this controller/namespace: <b><?= Vendor\ExampleVendor\EXAMPLE_GLOBAL ?></b></p>
<p>you can edit it at /src/vendor/ExampleVendor/config/config.ini</p>

<br>
<br>
<br>
<a href="<?php $this->path('Home');?>">Go home</a> <small><- pointing to OUTSIDE the vendor!</small>