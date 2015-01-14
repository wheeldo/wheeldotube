<?php
require_once 'bin/top_functions.php';

if(!isset($_SESSION['login'])) {
    header("location:/admin/login");
}



//echo create_pa("1234");


?>
<!DOCTYPE html>
<html lang="e" xmlns:ng="http://angularjs.org">
	<head>
		<meta charset="utf-8" />
		<title>Wheeldo Application management</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- basic styles -->
                <link rel="stylesheet" type="text/css" href="/vendor/bootstrap-2.3.1/css/bootstrap.min.css">
                <style>
                    .navbar .nav > li > a {
                        padding: 0 8px;
                        text-shadow: none;
                        color:#ffffff;
                    }
                    
                    .container, .navbar-static-top .container, .navbar-fixed-top .container, .navbar-fixed-bottom .container {
                        width:auto;
                    }
                </style>
                <link href="admin/assets/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="admin/assets/css/font-awesome.min.css" />
                <link rel="stylesheet" href="admin/assets/css/chosen.css" />
                <style>
                    .modal {
                        bottom:auto !important;
                        display:block !important;
                        overflow:hidden !important;
                        left:50%;
                    }
                    
                    select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
                        height:30px;
                    }
                    
                    .form-control {
                        height:34px !important;
                    }
                    
                    
                </style>

		<!--[if IE 7]>
		  <link rel="stylesheet" href="admin/assets/css/font-awesome-ie7.min.css" />
		<![endif]-->

		<!-- page specific plugin styles -->

		<!-- fonts -->

		

		<!-- ace styles -->

		
                <link rel="stylesheet" href="admin/media/css/style.css" />
                <link rel="stylesheet" href="admin/assets/css/jquery-ui-1.10.3.custom.min.css" />
                <link rel="stylesheet" href="admin/assets/css/chosen.css" />
		<link rel="stylesheet" href="admin/assets/css/datepicker.css" />
		<link rel="stylesheet" href="admin/assets/css/bootstrap-timepicker.css" />
		<link rel="stylesheet" href="admin/assets/css/daterangepicker.css" />
		<link rel="stylesheet" href="admin/assets/css/colorpicker.css" />

                
                <link rel="stylesheet" href="admin/assets/css/ace-fonts.css" />
                
                
                <link rel="stylesheet" href="admin/assets/css/ace.min.css" />
		<link rel="stylesheet" href="admin/assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="admin/assets/css/ace-skins.min.css" />
                
                <script src="admin/assets/js/bootstrap-wysiwyg.min.js"></script>
		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="admin/assets/css/ace-ie.min.css" />                   
		<![endif]-->

		<!-- inline styles related to this page -->
                
		<!-- ace settings handler -->
                
		<script src="admin/assets/js/ace-extra.min.js"></script>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and admin/media queries -->

		<!--[if lt IE 9]>
		<script src="admin/assets/js/html5shiv.js"></script>
		<script src="admin/assets/js/respond.min.js"></script>
		<![endif]-->
                
                <!--[if lte IE 8]>
                <script src="admin/media/js/JSON-js-master/json2.js"></script>
                <![endif]-->
                <script type="text/javascript">
                    var BASE='<?=BASEURL?>';
                </script>
                <style type="text/css">
                    body {
                        font-family: 'Arial';
                    }
                    
                    
                    
                    .loader_afterload {display:none;}
                    .loader_afterload.loaded {display:block;}
                    
                    .loader_beforeload {
                        display:block;
                        margin-top:100px;
                        padding:20px 0px;
                        text-align:center;
                    }
                    
                    .loader_beforeload.loaded {display:none;}
                    
                </style>
	</head>

	<body ng-app="WheeldoAdminApp" id="ng-app">
		<? require "includes/nav_bar.php";?>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<div class="main-container-inner">
				<a class="menu-toggler" id="menu-toggler" href="#">
					<span class="menu-text"></span>
				</a>

				<? require "includes/side_bar.php";?>

                                <div class="main-content">
<!--					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>

						<ul class="breadcrumb">
							<li>
								<i class="icon-home home-icon"></i>
								<a href="#">Home</a>
							</li>

							<li>
								<a href="#">Other Pages</a>
							</li>
							<li class="active">Blank Page</li>
						</ul> .breadcrumb 


					</div>-->

					<div class="page-content">
						<div class="row">
							<div class="col-xs-12" id="main_scope" ng-view="">
								<!-- PAGE CONTENT BEGINS -->

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div><!-- /.main-content -->

				<div class="ace-settings-container" id="ace-settings-container">
					<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
						<i class="icon-cog bigger-150"></i>
					</div>

					<div class="ace-settings-box" id="ace-settings-box">
						<div>
							<div class="pull-left">
								<select id="skin-colorpicker" class="hide">
									<option data-skin="default" value="#438EB9">#438EB9</option>
									<option data-skin="skin-1" value="#222A2D">#222A2D</option>
									<option data-skin="skin-2" value="#C6487E">#C6487E</option>
									<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
								</select>
							</div>
							<span>&nbsp; Choose Skin</span>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />
							<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
							<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
							<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
							<label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
							<label class="lbl" for="ace-settings-add-container">
								Inside
								<b>.container</b>
							</label>
						</div>
					</div>
				</div><!-- /#ace-settings-container -->
			</div><!-- /.main-container-inner -->

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='admin/assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='admin/assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='admin/assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="admin/assets/js/bootstrap.min.js"></script>
		<script src="admin/assets/js/typeahead-bs2.min.js"></script>

		<!-- page specific plugin scripts -->

		<!-- ace scripts -->

		<script src="admin/assets/js/ace-elements.min.js"></script>
		<script src="admin/assets/js/ace.min.js"></script>
                
                <script src="admin/assets/js/markdown/markdown.min.js"></script>
		<script src="admin/assets/js/markdown/bootstrap-markdown.min.js"></script>
		<script src="admin/assets/js/jquery.hotkeys.min.js"></script>
                <script src="admin/assets/js/bootstrap-wysiwyg.min.js"></script>

		<!-- inline scripts related to this page -->
                
                <script src='/vendor/angularjs-1.0.7/angular.min.js'></script>
                <script src='/vendor/angularjs-1.0.7/angular-resource.min.js'></script>
                <script src="/vendor/ui.bootstrap/ui-bootstrap-tpls-0.6.0.min.js"></script>
                <!-- App libs -->
                <script src="admin/app/app.js?t=<?=time()?>"></script>
                <script src="admin/app/controllers/controllers.js?t=<?=time()?>"></script>
                <script src="admin/app/services/Services.js?t=<?=time()?>"></script>
                <script src="admin/app/filters/filters.js?t=<?=time()?>"></script>
                <script src="/vendor/angular-file-upload-master/angular-file-upload.js"></script>
	</body>
</html>
