<div id="footer" class='row'>
    <div class="col-xs-10 col-xs-offset-1 col-sm-3 col-sm-offset-2">
        <h4 class="">Stuff</h4>
        <ul class="list-unstyled">
            <li><a href="#" class="_helpModal" data-url="<?php $this->path('AjaxHelpGet', [':section' => 'terms']);?>">
                    <span class="glyphicon glyphicon-list-alt"></span>&nbsp;Terms</a></li>
            <li><a href="#" class="_helpModal" data-url="<?php $this->path('AjaxHelpGet', [':section' => 'privacy']);?>">
                    <span class="glyphicon glyphicon-eye-close"></span>&nbsp;Privacy</a></li>
            <li><a href="#" class="_helpModal" data-url="<?php $this->path('AjaxHelpGet', [':section' => 'thanks']);?>">
                    <span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;Thanks</a></li>
            <li><a href="#" class="_helpModal" data-url="<?php $this->path('AjaxHelpGet', [':section' => 'help']);?>">
                    <span class="glyphicon glyphicon-question-sign"></span>&nbsp;Help</a></li>
            <li><a href="#" class="_helpModal" data-url="<?php $this->path('AjaxHelpGet', [':section' => 'faq']);?>">
                    <span class="glyphicon glyphicon glyphicon-book"></span>&nbsp;FAQ</a></li>
        </ul>
    </div>
    <div class="col-xs-10 col-xs-offset-1 col-sm-4 col-sm-offset-0 col-sm-push-3">
        <h4 class="">Social</h4>
        <ul class="list-unstyled list-inline">
            <li><a href="https://facebook.com/MumImHere" target="_blank" 
                    class="_tooltip" data-placement="top" title="Mum I'm Here - Facebook">
                       <img src="<?php $this->asset('/images/icons/social/facebook.png');?>" width="30px"/>
                </a></li>&nbsp;
            <li><a href="https://twitter.com/MumImHere" target="_blank"
                    class="_tooltip" data-placement="top" title="@MumImHere - Twitter">
                       <img src="<?php $this->asset('/images/icons/social/twitter.png');?>" width="30px"/>
                </a></li>&nbsp;
            <li><a href="https://plus.google.com/u/0/109848100968114519223/" target="_blank"
                    class="_tooltip" data-placement="top" title="Mum I'm Here - Google+">
                       <img src="<?php $this->asset('/images/icons/social/google.png');?>" width="30px"/>
                </a></li>
        </ul>
    </div>
    <div class="col-xs-10 col-xs-offset-1 col-sm-3 col-sm-offset-0 col-sm-pull-4">
        <p class=""><br/>Made with <span style="color:#c0392b">&hearts;</span> by
			<a href="http://kupfer.es/" target="_blank">Nico Kupfer</a></p>
    </div>
</div>