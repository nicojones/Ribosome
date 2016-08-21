<nav class="navbar navbar-default navbar-fixed-top <?php echo @$header_class;?>" role="navigation" id="_header-nav">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#home-navbar">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand _tooltip" href="<?php $this->path('Home');?>" title="<?php echo __SITE_NAME__;?>" data-toggle="tooltip" data-placement="bottom">
                <b id="_loader" style="display:none;"><img src="<?php $this->asset('/images/icons/loader.gif');?>" height="47px"/></b>
                <img id="_logo" src="<?php $this->asset('/images/icons/momimhere.png');?>" style="margin-top:-10px; cursor:pointer !important"
                     height="46px"/></a>
        </div>

        <div class="collapse navbar-collapse" id="home-navbar">
            <ul class="nav navbar-nav">
                <?php if (!empty($mapTitle)) { ?>
                    <?php if (!empty($mapTitle['user']) || $mapTitle['shared']) { ?>
                        <li class="btn-group" id="_tripsMenu">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <?php echo ($mapTitle['shared'] ? 'Collaborators' : (Session::getUser('id') == $mapTitle['user_id'] ? 'Your maps' : $mapTitle['user']));?> <span class="caret"></span>
                                <img src="<?php $this->asset('/images/icons/map/favicon.ico');?>" height="22px">
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <?php if (!$mapTitle['shared']) { ?>
                                    <li>
                                        <a href="<?php $this->path('MapViewNow', [':url' => $url]);?>">
                                            <span class="glyphicon glyphicon glyphicon-screenshot"></span> Now
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php $this->path('MapViewCountries', [':url' => $url]);?>">
                                            <span class="glyphicon glyphicon-globe"></span> Countries
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if (!empty($trips) && count($trips)) { ?>
                                    <li class="divider"></li>
                                    <?php foreach ($trips as $trip) {?>
                                        <li>
                                            <a href="<?php echo $this->mapURL($trip, FALSE, FALSE); ?>" 
                                               class="_tripDropdown <?php if (!empty($tripID) && $trip['id'] == $tripID) echo 'alert-info'; ?>" data-id="<?php echo $trip['trip_id'];?>">
                                                   <span class="glyphicon glyphicon-map-marker"></span> <?php echo $trip['name'];?></a></li>
                                    <?php } ?>
                                <?php } ?>
                                <?php if (!empty($collaborators) && count($collaborators)) { ?>
                                    <li class="divider"></li>
                                    <!--<li class="disabled">Collaborators</li>-->
                                    <?php foreach ($collaborators as $c) {?>
                                        <li>
                                            <a href="<?php $this->path('MapView', [':url' => $c['url']]);?>" class="_tripDropdown">
                                                <span class="glyphicon glyphicon-user"></span> <?php echo $c['name'];?></a></li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li id="_searchUserHeaderInputBox">
                    <!--<span id="_searchUserHeaderInputBox" class="pull-left" style="">-->
                    <input id="_searchUserHeaderInput" class="pull-right" placeholder="Search...">
                	<a href="#" id="_searchUserHeaderButton" class="" style="color:white;">
<!--                            <span class="glyphicon glyphicon-search"></span>-->
                        <img src="<?php $this->asset('/images/icons/map/search.png');?>" style="height:23px;margin:-5px -10px 0 0"/>
                        </a>
                        <ul id="autocomplete" class="list-unstyled">
                            <li class="autocomplete_loader">
                                <img src="<?php $this->asset('/images/icons/loader-line.gif');?>" height="25px"/>
                                Loading results for <b id="autocomplete_query"></b>...
                            </li>
                        </ul>
                    <!--</span>-->
                </li>
                <?php if (Session::isAuthenticated()) { ?>
                 <li class="dropdown" id="_notificationsMenu">
                     <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="padding:10px">
                         <img src="<?php $this->asset('/images/icons/map/notifications.png');?>" height="28px"/>
                     </a>
                     <ul class="dropdown-menu right_menu" role="menu">
                         <li style="padding:8px">
                             Notifications coming soon!
                         </li>
                     </ul>
                 </li>
                 <li class="dropdown" id="_profileMenu">
                    	<a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <?php echo Session::getUser('name');?>&nbsp;
                            <img src="<?php $this->asset('/images/icons/map/pin.png');?>" height="20px"/>
                        </a>
                        <ul class="dropdown-menu right_menu" role="menu">
                            <li><a href="<?php $this->path('LoginHome');?>">
                                <span class="glyphicon glyphicon-map-marker"></span> My Trips</a></li>
                            <li><a href="<?php $this->path('Settings');?>">
                                <span class="glyphicon glyphicon-cog"></span> Settings</a></li>
                            <?php if (Session::isSudo()) { ?>
                                <li><a href="<?php $this->path('Admin', [':section' => 'home']);?>">
                                    <span class="glyphicon glyphicon-user"></span> Admin</a></li>
                            <?php } ?>
                            <li><a href="#" class="_helpModal" data-url="<?php $this->path('AjaxHelpGet', [':section' => 'help']);?>">
                                <span class="glyphicon glyphicon-question-sign"></span> Help</a></li>
                            <li><a href="<?php echo $this->path('Logout');?>">
                            <span class="glyphicon glyphicon-off"></span> Logout</a></li>
                     </ul>
                </li>
                <?php } else { ?>
                <li><a href="#" class="_helpModal" data-url="<?php $this->path('AjaxHelpGet', [':section' => 'help']);?>">How it works</a></li>
                <li class="hidden-lg hidden-md hidden-sm"><a href="<?php $this->path('SignUp');?>">Sign Up</a></li>
                <li class="hidden-xs">
                    <a href="#" onclick="openLoginModal('signup');">Sign Up</a>
                </li>
                <li class="hidden-lg hidden-md hidden-sm"><a href="<?php $this->path('Login');?>">Login</a></li>
                <li class="hidden-xs">
                    <a href="#" onclick="openLoginModal('login');">Login</a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php if (!empty($mapTitle)) { ?>
        <span id="_mapTitle" class="">
            <h4 style="color:white">
                <?php if (!empty($tripID) && Session::isAuthenticated()) { ?>
                    <a href="<?php $this->path('FollowTrip');?>" style="cursor:pointer;font-size:14px;color:white" 
                       data-trip="<?php echo $tripID;?>" class="_followTrip _tooltip" title="Follow this map!" data-placement='bottom'>
                        <span class="glyphicon glyphicon-heart<?php echo $mapTitle['following'] ? '' : '-empty';?>"></span>
                    </a>
                <?php } ?>
                <?php if (!empty($mapTitle['now'])) { ?>
                    Where's <?php echo $mapTitle['text'];?> <u>now</u>?
                <?php } elseif (isset($mapTitle['countries'])) { ?>
                    Where has <?php echo $mapTitle['text'];?> been? 
                    (<big class='_tooltip' data-toggle='tooltip' data-placement='bottom' style='cursor:help'
                             title='<?php echo implode('<br/>', $mapTitle['countries']);?>'>
                                 <?php echo count($mapTitle['countries']);?>
                    </big>)
                <?php } else { ?>
                    <?php if (!empty($mapTitle['title'])) { ?>
                        <span class="_tooltip" data-placement="bottom" title="<?php echo $mapTitle['title'];?>">
                    <?php } else { ?>
                        <span>
                    <?php } ?>
                            <?php echo $mapTitle['text'];?>
                        </span>
                <?php } ?>
            </h4>
        </span>
    <?php } ?>
</nav>
<br/>