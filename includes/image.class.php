<?php

/**
 * 检查GD库是否存在，可用gd_info();
 * Array (
 * [GD Version] => bundled (2.0.34 compatible)
 * [FreeType Support] => 1
 * [FreeType Linkage] => with freetype
 * [T1Lib Support] => 1
 * [GIF Read Support] => 1
 * [GIF Create Support] => 1
 * [JPEG Support] => 1
 * [PNG Support] => 1
 * [WBMP Support] => 1
 * [XPM Support] =>
 * [XBM Support] => 1
 * [JIS-mapped Japanese Font Support] =>
 * )
 */
class Image
{
	public static $captcha = '';
	public static $ext = array();

	public function __construct()
	{
		if(!function_exists('gd_info')){
			throw new Exception('当前环境不支持GD图形库');
		}
	}

	/**
	 * 生成验证码图片
	 * @param $width int 验证码宽度
	 * @param $height int 验证码高度
	 * @param $type string 'en'生成英文验证码 / 'ch'生成中文验证码
	 */
	public static function captcha($width = 70, $height = 20, $type = 'en')
	{
		header("content-type: image/jpeg");
		$im = imagecreate($width, $height);
		$bgcolor = imagecolorallocate($im, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
		$fontcolor = imagecolorallocate($im, mt_rand(0, 160), mt_rand(0, 160), mt_rand(0, 160));
		$linecolor = imagecolorallocate($im, mt_rand(160, 255), mt_rand(160, 255), mt_rand(160, 255));
		$linecolor2 = imagecolorallocate($im, mt_rand(160, 255), mt_rand(160, 255), mt_rand(160, 255));
		imagefill($im, 0, 0, $bgcolor);
		if($type == 'en'){
			$string = 'abcdefghijkmnopqrstuvxyzABCDEFGHIJKLJMPQRSTUVXYZ23456789';
			$char = substr(str_shuffle($string), 0, 6);
			imagestring($im, 12, 8, 2, $char, $fontcolor);
			self::$captcha = $char;
		}else{
			$string = '吖你呢拿年能难那耐内牛奶记得深刻浪费闻中心首页新浪新闻是国内知名的新闻门户网站' . '是立志服务于中国及全球华人社群的公司重要产品更新社会军事体育财经等';
			$stringList = chunk_split($string, 3, ",");
			$stringList = explode(',', $stringList);
			array_pop($stringList);
			shuffle($stringList);
			// $char = implode('', array_slice($stringList, 0, 4));
			imagefttext($im, 12, mt_rand(-15, 15), 2, 16, $fontcolor, dirname(__FILE__) . '/captcha.ttf', 
					$stringList[0]);
			imagefttext($im, 12, mt_rand(-15, 15), 18, 16, $fontcolor, dirname(__FILE__) . '/captcha.ttf', 
					$stringList[1]);
			imagefttext($im, 12, mt_rand(-15, 15), 34, 16, $fontcolor, dirname(__FILE__) . '/captcha.ttf', 
					$stringList[2]);
			imagefttext($im, 12, mt_rand(-15, 15), 50, 16, $fontcolor, dirname(__FILE__) . '/captcha.ttf', 
					$stringList[3]);
			self::$captcha = $stringList[0] . $stringList[1] . $stringList[2] . $stringList[3];
		}
		//imageline($im, 0, mt_rand(0, $height), $width, mt_rand(0, $height), $linecolor);
		//imageline($im, 0, mt_rand(0, $height), $width, mt_rand(0, $height), $linecolor2);
		imagejpeg($im);
		imagedestroy($im);
	}

	public static function thumb($name, $path)
	{}

	public static function watermark()
	{}

	public function pieChart(array $data)
	{
		// create a blank image
		$image = imagecreate(400, 300);
		// fill the background color
		imagecolorallocate($image, 255, 255, 255);
		// choose a color for the ellipse
		$color1 = imagecolorallocate($image, 222, 222, 222);
		// draw the white ellipse
		imagefilledellipse($image, 200, 150, 300, 300, $color1);
		// output the picture
		header("Content-type: image/png");
		imagepng($image);
	}

	public static function gdVersion()
	{
		$gdVersion = array();
		if(!extension_loaded('gd') || !function_exists('gd_info')){
			return null;
		}
		$gdinfo = gd_info();
		preg_match('/\d/', $gdinfo['GD Version'], $match);
		$gd = $match[0];
		if($gd == 0){
			$gdVersion['version'] = 'N/A';
		}else{
			if($gd == 1){
				$gdVersion['version'] = 'GD1';
			}else{
				$gdVersion['version'] = 'GD2';
			}
			/* 检查系统支持的图片类型 */
			$gdVersion['support'] = '(';
			if($gd && (imagetypes() & IMG_JPG) > 0){
				$gdVersion['support'] .= 'JPEG';
			}
			if($gd && (imagetypes() & IMG_GIF) > 0){
				$gdVersion['support'] .= ' GIF';
			}
			if($gd && (imagetypes() & IMG_PNG) > 0){
				$gdVersion['support'] .= ' PNG';
			}
			$gdVersion['support'] .= ')';
		}
		return $gdVersion;
	}
}
