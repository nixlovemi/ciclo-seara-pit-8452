<?php
include_once('constants.php');
include_once('functions.php');
$config = readConfig();
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<title><?=$config['title']?></title>
		<meta name="description" content="<?=$config['title']?>" />
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0" /> 
		<link rel="shortcut icon" href="../favicon.ico" /> 
		<link rel="stylesheet" type="text/css" href="css/jquery.jscrollpane.custom.css" />
		<link rel="stylesheet" type="text/css" href="css/bookblock.css" />
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="css/font-awesome-4.7.0/css/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="css/custom.css" />
	</head>
	<body>
		<div id="dv-container" class="dv-container">	
			<div class="menu-panel">
				<h3>Menu</h3>
				<ul id="menu-toc" class="menu-toc">
					<li>
						<span class="menu-group">Linha 1</span>
						<ul>
							<li class="clickable menu-toc-current">
								<a href="javascript:;">Levíssimo</a>
							</li>
						</ul>
					</li>
					<li class="clickable"><a href="javascript:;">Produto 2</a></li>
					<li class="clickable"><a href="javascript:;">Produto 3</a></li>
					<li class="clickable"><a href="javascript:;">Produto 4</a></li>
					<li class="clickable"><a href="javascript:;">Produto 5</a></li>
					<li class="clickable"><a href="javascript:;">Produto 6</a></li>
					<li class="clickable"><a href="javascript:;">Produto 7</a></li>
					<li class="clickable"><a href="javascript:;">Produto 8</a></li>
					<li class="clickable"><a href="javascript:;">Produto 9</a></li>
					<li class="clickable"><a href="javascript:;">Produto 10</a></li>
				</ul>
			</div>

			<div class="bb-custom-wrapper">
				<div id="bb-bookblock" class="bb-bookblock">
					<?php foreach($config['pages'] as $nbr => $arrInfo): ?>
						<div class="bb-item" id="<?=ITEM_PAGE_PREFIX . $nbr?>">
							<div class="content">
								<div class="scroller">
									<?php include($arrInfo['path']) ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				
				<nav>
					<span id="bb-email" data-toggle="modal" data-target="#exampleModal">
						<i class="fa fa-envelope-o" aria-hidden="true"></i>
					</span>
					<span id="bb-nav-prev">&larr;</span>
					<span id="bb-nav-next">&rarr;</span>
				</nav>

				<span id="tblcontents" class="menu-button">Lista dos itens</span>

			</div>
		</div>

		<script src="js/modernizr.custom.79639.js"></script>
		<script src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.mousewheel.js"></script>
		<script src="js/jquery.jscrollpane.min.js"></script>
		<script src="js/jquerypp.custom.js"></script>
		<script src="js/jquery.bookblock.js"></script>
		<script src="js/page.js"></script>
		<script>
			$(function() {
				Page.init();
			});
		</script>

		<?php
		// modals
		include('email_modal.php');
		?>
	</body>
</html>
