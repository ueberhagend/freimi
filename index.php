<!doctype html>
<head>
	<link rel="stylesheet" href="includes\css\style.css">
	<script src="includes\js\javascript.js"></script>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
</head>
<body ng-class>
<header>
	<nav class="navbar blue ng-isolate-scope">
		<a href="#/">
			<img src="includes\css\bilder\logo.svg" alt="AIESEC Logo" class="logo">
		</a>
		<ul>
			<li>
				<a class="ng-scope active" href="#">Hello Freimis!</a>
			</li>
		</ul>
	</nav>
	<nav class="breadcrumbs ng-isolate-scope">
		<ul ng-show="vm.canDisplay" class>
			<li ng-repeat="crumb in vm.breadcrumbs" class="ng-scope" style>
				<a ng-href="#" href="">Finance</a>
			</li>
		</ul>
	</nav>
</header>
<main ui-view="root" class="ng-scope">
	<div class="wrapper ng-scope">
		<div class="main-content">
			<class="crm-header">
				<select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);" class="dropdown-menu" style="width:250px; border-radius:25px; box-shadow: 0px 0px 0px; font-size:14pt; padding-left:20px;">
 				 	<option value="index.php?site=content">Choose...</option>
					<option value="index.php?site=ep">Check EP</option>
					<option value="index.php?site=opso">OPS Online</option>
 				 	<option value="index.php?site=createops">Enter Physical OPS</option>
					<option value="index.php?site=loadops">Load Physical OPS</option>
					<option value="index.php?site=createwhs">Enter WHS</option>
					<option value="index.php?site=loadwhs">Load WHS</option>
				</select>
			<div>
				<?php include 'includes/php/siteswitcher.php'; ?>
			</div>
		</div>
	</div>
</div>
</main>

