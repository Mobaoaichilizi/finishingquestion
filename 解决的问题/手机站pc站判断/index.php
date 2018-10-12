<?php

// +----------------------------------------------------------------------

// | OneThink [ WE CAN DO IT JUST THINK IT ]

// +----------------------------------------------------------------------

// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.

// +----------------------------------------------------------------------

// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>

// +----------------------------------------------------------------------

header('X-Frame-Options: deny');
ini_set("session.cookie_httponly", 1);
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
  function IsMobile()
    {

        //如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  return TRUE;

        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if(isset($_SERVER['HTTP_VIA']))
        {
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }

        //判断手机发送的客户端标志,兼容性有待提高
        if(isset($_SERVER['HTTP_USER_AGENT']))
        {

            $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile');

            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if(preg_match('/('.implode('|', $clientkeywords).')/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
            {
                return TRUE;
            }
        }

        //协议法，因为有可能不准确，放到最后判断
        if(isset($_SERVER['HTTP_ACCEPT']))
        {
            //如果只支持wml并且不支持html那一定是移动设备
            //如果支持wml和html但是wml在html之前则是移动设备
            if((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) &&
               (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false ||
               (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
            {
                    return TRUE;
            }
        }

        return FALSE;
    }


/**

 * 系统调试设置

 * 项目正式部署后请设置为false

 */

define ( 'APP_DEBUG', false );
if(IsMobile())
{
$_GET['m'] = 'Wap';
}

/**

 * 应用目录设置

 * 安全期间，建议安装调试完成后移动到非WEB目录

 */

define ( 'APP_PATH', './Application/' );



if(!is_file(APP_PATH . 'User/Conf/config.php')){

	header('Location: ./install.php');

	exit;

}



/**

 * 缓存目录设置

 * 此目录必须可写，建议移动到非WEB目录

 */

define ( 'RUNTIME_PATH', './Runtime/' );



/**

 * 引入核心入口

 * ThinkPHP亦可移动到WEB以外的目录

 */

require './ThinkPHP/ThinkPHP.php';