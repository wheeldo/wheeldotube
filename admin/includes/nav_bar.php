<?php
?>
<div class="navbar navbar-default" id="navbar">
    <script type="text/javascript">
            try{ace.settings.check('navbar' , 'fixed')}catch(e){}
    </script>

    <div class="navbar-container" id="navbar-container">
            <div class="navbar-header pull-left">
                    <a href="#" class="navbar-brand">
                            <small>
                                    <img style="max-height:26px;" src="/media/css/dice/img/wheeldo_logo.png" />
                                    
                            </small>
                    </a><!-- /.brand -->
            </div><!-- /.navbar-header -->

            <div class="navbar-header pull-right" role="navigation">
                    <ul class="nav ace-nav">
                         

                            <li class="purple">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                            <i class="icon-bell-alt icon-animated-bell"></i>
                                            <span class="badge badge-important">8</span>
                                    </a>

                                    <ul class="pull-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
                                            <li class="dropdown-header">
                                                    <i class="icon-warning-sign"></i>
                                                    8 Notifications
                                            </li>

                                            <li>
                                                    <a href="#">
                                                            <div class="clearfix">
                                                                    <span class="pull-left">
                                                                            <i class="btn btn-xs no-hover btn-pink icon-comment"></i>
                                                                            New Comments
                                                                    </span>
                                                                    <span class="pull-right badge badge-info">+12</span>
                                                            </div>
                                                    </a>
                                            </li>

                                            <li>
                                                    <a href="#">
                                                            <i class="btn btn-xs btn-primary icon-user"></i>
                                                            Bob just signed up as an editor ...
                                                    </a>
                                            </li>

                                            <li>
                                                    <a href="#">
                                                            <div class="clearfix">
                                                                    <span class="pull-left">
                                                                            <i class="btn btn-xs no-hover btn-success icon-shopping-cart"></i>
                                                                            New Orders
                                                                    </span>
                                                                    <span class="pull-right badge badge-success">+8</span>
                                                            </div>
                                                    </a>
                                            </li>

                                            <li>
                                                    <a href="#">
                                                            <div class="clearfix">
                                                                    <span class="pull-left">
                                                                            <i class="btn btn-xs no-hover btn-info icon-twitter"></i>
                                                                            Followers
                                                                    </span>
                                                                    <span class="pull-right badge badge-info">+11</span>
                                                            </div>
                                                    </a>
                                            </li>

                                            <li>
                                                    <a href="#">
                                                            See all notifications
                                                            <i class="icon-arrow-right"></i>
                                                    </a>
                                            </li>
                                    </ul>
                            </li>

                         

                            <li class="light-blue">
                                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                                            <img class="nav-user-photo" src="<?=$_SESSION['login']['image']?>" alt="<?=$_SESSION['login']['fullname'];?>'s Photo" />
                                            <span class="user-info">
                                                    <small>Hello,</small>
                                                    <?=$_SESSION['login']['fullname'];?>
                                            </span>

                                            <i class="icon-caret-down"></i>
                                    </a>

                                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

                                            <li>
                                                    <a href="/admin/login">
                                                            <i class="icon-off"></i>
                                                            Logout
                                                    </a>
                                            </li>
                                    </ul>
                            </li>

                    </ul><!-- /.ace-nav -->
            </div><!-- /.navbar-header -->
    </div><!-- /.container -->
</div>