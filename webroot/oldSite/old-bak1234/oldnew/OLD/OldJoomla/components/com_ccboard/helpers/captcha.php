<?php
/**
 * @version		$Id: captcha.php 109 2009-04-26 07:50:55Z thomasv $
 * @Project		ccBoard - Joomla! Bulletin Board Extension/Component
 * @author 		Thomas Varghese
 * @package		ccBoard
 * @copyright	Copyright (C) 2008-2009 codeclassic.org. All rights reserved.
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

	$sid = $_GET['sid'];
	session_id($sid);
	session_start();

	$string = $_SESSION['ccbkey'];
	$img = imagecreatetruecolor(120,25);

	$white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    $green = imagecolorallocate($img, 0, 0, 0);
    $red   = imagecolorallocate($img, 0, 0, 0);

    imagefilledrectangle($img, 2, 2, 117, 22, $white);

    $lines = 5;
    $i = 1;
    $xcentre = (120 / 2) - ((strlen($string) / 2) * imagefontwidth(5));
    $ycentre = (25 - imagefontheight(5)) / 2;
    imagestring($img, 11, $xcentre, $ycentre, $string, $red);

    while($i <= $lines){
        $randxs = rand(5, 117);
        $randys = rand(5, 2);
        $randxe = rand(5, 117);
        $randye = rand(0, 22);

        imageline($img, $randxs, $randys, $randxe, $randye, $green);
        $i += 1;
    }
	ob_start();
    header("Content-Type: image/png");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', false );
	header( 'Pragma: no-cache' );
    imagepng($img);
    imagedestroy($img);
	ob_end_flush();

?>