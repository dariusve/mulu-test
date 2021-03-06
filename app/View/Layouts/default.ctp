<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->script(
			array(
				 "https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js",
				 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
				
			),
			array(
				'inline' => false
			)
		);
		echo $this->Html->css(
			array(
				'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
				'font-awesome',
				'style'
				),
				array('inline' => false)
		);

		echo $this->fetch('meta');
		echo $this->fetch('css');
		
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="wrapper">
		<div id="page-wrapper">
		    <div class='container'>
			<div class="row">
                <div class="col-md-12">
					<?php echo $this->Flash->render(); ?>
					<?php echo $this->fetch('content'); ?>
				</div>
			</div>
			</div>
		</div>
	</div>
</body>
</html>
