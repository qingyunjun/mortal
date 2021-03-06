<?php
/**
 * Global helpers file with misc functions.
 */

/**
 * 获取随机字符串
 * @param int $randLength 长度
 * @param int $addtime 是否加入当前时间戳
 * @param int $includenumber 是否包含数字
 * @return string
 */
if (! function_exists('rand_str')) {
    function rand_str($randLength = 6, $addtime = 0, $includenumber = 0)
    {
        if ($includenumber) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
        } else {
            $chars = 'abcdefghijklmnopqrstuvwxyz';
        }
        $len = strlen($chars);
        $randStr = '';
        for ($i = 0; $i < $randLength; $i++) {
            $randStr .= $chars[mt_rand(0, $len - 1)];
        }
        if ($addtime) {
            $randStr .= time();
        }
        return $randStr;
    }
}

/**
 * 跳转地址
 * @param $url
 */
if (! function_exists('rand_str')) {
    function jump($url)
    {
        header('Location:'.$url);
        exit();
    }
}

/**
 * 字符串脱敏处理
 *----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
 *----------------------------------------------------------
 * @param string $string 待转换的字符串
 * @param int  $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int  $len  需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int  $type  转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；
 *                               3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string $glue  分割符
 *----------------------------------------------------------
 * @return string 处理后的字符串
 *----------------------------------------------------------
 */
if (! function_exists('hiding_sensitive_string')) {
    function hiding_sensitive_string($string, $bengin = 0, $len = 4, $type = 0, $char = "*", $glue = "@")
    {
        if (empty($string)) return false;
        $array = array();
        if ($type == 0 || $type == 1 || $type == 4) {
            $strlen = $length = mb_strlen($string);
            while ($strlen) {
                $array[] = mb_substr($string, 0, 1, "utf8");
                $string = mb_substr($string, 1, $strlen, "utf8");
                $strlen = mb_strlen($string);
            }
        }
        if ($type == 0) {
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = $char;
            }
            $string = implode("", $array);
        } else if ($type == 1) {
            $array = array_reverse($array);
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i]))
                    $array[$i] = $char;
            }
            $string = implode("", array_reverse($array));
        } else if ($type == 2) {
            $array = explode($glue, $string);
            $array[0] = hiding_sensitive_data($array[0], $bengin, $len, 1);
            $string = implode($glue, $array);
        } else if ($type == 3) {
            $array = explode($glue, $string);
            $array[1] = hiding_sensitive_data($array[1], $bengin, $len, 0);
            $string = implode($glue, $array);
        } else if ($type == 4) {
            //需要替换的下标数组
            $arr_count = count($array);
            $left = $bengin;
            $right = $arr_count-$len;
            for ($i = 0; $i < $arr_count; $i++) {
                if (isset($array[$i])  &&  $i>=$left  &&  $i <$right){
                    $array[$i]="*";
                }
            }
            $string = implode("", $array);
        }
        return $string;
    }
}

/**
 * AES-128-CBC加密
 */
if (! function_exists('encrypt_cbc')) {
    function encrypt_cbc($string, $key = "",$iv="")
    {
        $data = openssl_encrypt($string, 'AES-128-CBC', $key, OPENSSL_RAW_DATA,$iv);
        $data = base64_encode($data);
        return $data;
    }
}

/**
 * AES-128-CBC解密
 */
if (! function_exists('decrypt_cbc')) {
    function decrypt_cbc($string, $key = "",$iv="")
    {
        $content = base64_decode($string);
        $data = openssl_decrypt($content, "AES-128-CBC", $key, OPENSSL_RAW_DATA,$iv);
        return $data;
    }
}

/**
 * 解析日期获取年龄、星座、生肖
 *
 * @param string $date 解析日期
 * @param string $type "age";"constellation";"zodiac"
 *
 * @return false|string
 * @example   "20120122"
 */
if(! function_exists("parse_date")) {
    function parse_date($date, $type)
    {
        if (! strtotime($date)){
            return "";
        }
        switch ($type){
            case "age":
                $reStr = date("Y", strtotime(date("Y-m")) - strtotime($date)) - 1970;
                break;
            case "constellation":
                $month = (int)substr($date,4,2);
                $day = (int)substr($date,6);
                if(($month == 1 && $day <= 21) || ($month == 2 && $day <= 19)) {
                    $reStr = "水瓶座";
                }else if(($month == 2 && $day > 20) || ($month == 3 && $day <= 20)) {
                    $reStr = "双鱼座";
                }else if (($month == 3 && $day > 20) || ($month == 4 && $day <= 20)) {
                    $reStr = "白羊座";
                }else if (($month == 4 && $day > 20) || ($month == 5 && $day <= 21)) {
                    $reStr = "金牛座";
                }else if (($month == 5 && $day > 21) || ($month == 6 && $day <= 21)) {
                    $reStr = "双子座";
                }else if (($month == 6 && $day > 21) || ($month == 7 && $day <= 22)) {
                    $reStr = "巨蟹座";
                }else if (($month == 7 && $day > 22) || ($month == 8 && $day <= 23)) {
                    $reStr = "狮子座";
                }else if (($month == 8 && $day > 23) || ($month == 9 && $day <= 23)) {
                    $reStr = "处女座";
                }else if (($month == 9 && $day > 23) || ($month == 10 && $day <= 23)) {
                    $reStr = "天秤座";
                }else if (($month == 10 && $day > 23) || ($month == 11 && $day <= 22)) {
                    $reStr = "天蝎座";
                }else if (($month == 11 && $day > 22) || ($month == 12 && $day <= 21)) {
                    $reStr = "射手座";
                }else if (($month == 12 && $day > 21) || ($month == 1 && $day <= 20)) {
                    $reStr = "魔羯座";
                } else {
                    $reStr = "";
                }
                break;
            case "zodiac":
                $start = 1901;
                $year = substr($date, 0,4);
                $x = ($start - $year) % 12;
                if($x == 1 || $x == -11){
                    $reStr = "鼠";
                } elseif ($x == 0) {
                    $reStr = "牛";
                } elseif ($x == 11 || $x == -1){
                    $reStr = "虎";
                } elseif ($x == 10 || $x == -2){
                    $reStr = "兔";
                } elseif ($x == 9 || $x == -3){
                    $reStr = "龙";
                } elseif ($x == 8 || $x == -4){
                    $reStr = "蛇";
                } elseif ($x == 7 || $x == -5){
                    $reStr = "马";
                } elseif ($x == 6 || $x == -6){
                    $reStr = "羊";
                } elseif ($x == 5 || $x == -7){
                    $reStr = "猴";
                } elseif ($x == 4 || $x == -8){
                    $reStr = "鸡";
                } elseif ($x == 3 || $x == -9){
                    $reStr = "狗";
                } elseif ($x == 2 || $x == -10){
                    $reStr = "猪";
                } else {
                    $reStr = "";
                }
                break;
            default:
                $reStr = "";
        }

        return $reStr;
    }
}

/**
 * 将15位身份证转换成18位
 */
if (! function_exists("handle_idcard_15")) {
    function handle_idcard_15($card)
    {
        $len = strlen($card);
        if($len != 15){
            return false;
        }
        $result = [];
        for($i=0;$i<$len;$i++){
            if($i<=5){
                $result[$i] = intval($card[$i]);
            }else{
                //15位的年份是两位数，18位的是4位数，留出2位
                $result[$i+2] = intval($card[$i]);
            }
        }
        //留出的2位，补充为年份，年份最后两位小于17,年份为20XX，否则为19XX
        if(intval(substr($card,6,2)) <= 17){
            $result[6] = 2;
            $result[7] = 0;
        }else{
            $result[6] = 1;
            $result[7] = 9;
        }
        ksort($result);
        //计算最后一位
        //前十七位乘以系数[7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2],
        $arrInt = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $sum = 0;
        for($i=0;$i<17;$i++){
            $sum += $result[$i] * $arrInt[$i];
        }
        //对11求余，的余数 0 - 10
        $rod = $sum % 11;
        //所得余数映射到对应数字即可
        $arrCh = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $result[17] = $arrCh[$rod];
        return implode('',$result);
    }
}

/**
 * 解析身份证号码获取星座、生肖、性别、生日、年龄
 *
 * @param string $card        18位身份证号码
 * @param array  $parseParams 需要解析的内容
 *
 * @example   "51010199901019999",["gender","constellation", "birthday", "age", "zodiac"]
 * @return array
 */
if(! function_exists("parse_idcard")) {
    function parse_idcard($card, $parseParams = ["gender","constellation", "birthday", "age", "zodiac"])
    {
        $len = strlen($card);
        if ($len != 18 && $len != 15){
            return [];
        }
        if ($len == 15) {
            $card = handle_idcard_15($card);
            if (!$card){
                return [];
            }
        }
        $reData = [];
        $birthday = substr($card, 6, 8);
        $sign = substr($card, 14, 3);
        array_map(function ($param) use ($birthday, $sign, &$reData){
            switch ($param){
                case "gender":
                    $sign = (int)$sign;
                    if ($sign == 0){
                        $reData["gender"] = "未知";
                    } else {
                        $y = $sign % 2;
                        if ($y) {
                            $reData["gender"] = "男";
                        } else {
                            $reData["gender"] = "女";
                        }
                    }
                    break;
                case "constellation":
                    $reData["constellation"] = parse_date($birthday, "constellation");
                    break;
                case "birthday":
                    $reData["birthday"] = (int)$birthday == 0 ? "" : $birthday;
                    break;
                case "age":
                    $reData["age"] =  parse_date($birthday, "age");
                    break;
                case "zodiac":
                    $reData["zodiac"] =  parse_date($birthday, "zodiac");
                    break;
                default:
                    break;
            }
        }, $parseParams);

        return $reData;
    }
}

/**
 * 生成uuid
 */
if (! function_exists('create_uuid')) {
    function create_uuid($prefix = "", $head = "", $length = 32)
    {
        $str = \Webpatser\Uuid\Uuid::generate()->hex;
        return $prefix . $str . $head;
    }
}

/**
 * curl post请求发送json
 */
if (! function_exists('curl_post_json')) {
    function curl_post_json($url, array $param)
    {
        $data = json_encode($param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ['Content-Type: application/json;', 'Content-Length: ' . strlen($data)]
        );
        $response = curl_exec($ch);
        curl_close ($ch);
        if (false === $response) {
            throw new \Exception("请求第三方接口错误:request:".$data);
        }
        return $response;
    }
}

/**
 * curl post请求
 */
if (! function_exists('curl_post')) {
    function curl_post($url, $param)
    {
        $data = http_build_query($param);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/x-www-form-urlencoded",
            ]
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
        if (false === $response) {
            throw new \Exception("请求第三方接口错误:request:".$url.' param:'.json_encode($param));
        }
        return $response;
    }
}

/**
 * curl post请求发送json
 */
if (! function_exists('curl_get')) {
    function curl_get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        if (false === $response) {
            throw new \Exception("请求第三方接口错误:request:".$url);
        }
        return $response;
    }
}

/**
 * 获取IP地址
 */
if (! function_exists('get_ip')) {
    function get_ip()
    {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        //匹配IP信息
        $result =  preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : 'unknown';
        return $result;
    }
}


/**
 * 获取文件的大小，并且转换成便于阅读的KB，MB等格式
 */
if (! function_exists('formatSize')) {
    function formatSize($size)
    {
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) {
            return('n/a');
        } else {
            return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
        }
    }
}

/**
 * 强制下载文件
 */
if (! function_exists('download')) {
    function download($filename)
    {
        if ((isset($filename))&&(file_exists($filename))){
            header("Content-length: ".filesize($filename));
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            readfile("$filename");
        } else {
            echo "Looks like file does not exist!";
        }
    }
}

/**
 * google api 二维码生成【QRcode可以存储最多4296个字母数字类型的任意文本，具体可以查看二维码数据格式】
 * @param string $chl 二维码包含的信息，可以是数字、字符、二进制信息、汉字。不能混合数据类型，数据必须经过UTF-8 URL-encoded
 * @param int $widhtHeight 生成二维码的尺寸设置
 * @param string $EC_level 可选纠错级别，QR码支持四个等级纠错，用来恢复丢失的、读错的、模糊的、数据。
 * L-默认：可以识别已损失的7%的数据
 * M-可以识别已损失15%的数据
 * Q-可以识别已损失25%的数据
 * H-可以识别已损失30%的数据
 * @param int $margin 生成的二维码离图片边框的距离
 */
if (! function_exists('generateQRGoogle')) {
    function generateQRGoogle($chl, $widthHeight ='150', $EC_level='L', $margin='0')
    {
        $chl = urlencode($chl);
        echo '<img src="http://chart.apis.google.com/chart?chs='.$widthHeight.'x'.$widthHeight.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl.'" alt="QR code" widthHeight="'.$widthHeight.'" widthHeight="'.$widthHeight.'"/>';
    }
}

/**
 * 打印类详情
 * @param $class object 对象
 */
if (! function_exists('classType')) {
    function classType($class)
    {
        $ref = new \ReflectionClass($class);
        $br = '<br>';
        $consts = $ref->getConstants(); //返回所有常量名和值
        echo "----------------consts:---------------" . $br;
        foreach ($consts  as $key => $val) {
            echo "$key : $val" . $br;
        }

        $props = $ref->getDefaultProperties();  //返回类中所有属性与值类型
        echo "--------------------props:--------------" . $br . $br;
        foreach ($props as $key => $val) {
            echo "$key ::" . gettype($val) . $br;   //  属性名和属性值
        }

        $methods = $ref->getMethods();     //返回类中所有方法与参数
        echo "-----------------methods:---------------" . $br . $br;
        foreach ($methods as $method) {
            $getParam = $method->getParameters();
            if(!count($getParam))echo $method->getName();
            foreach ($getParam as $key=>$value) {
                echo $method->getName() ."::".$value.' + ';
            }
            echo $br;
        }
    }
}

/**
 * 不显示信息直接跳转
 *
 * @param string $url
 */
if (! function_exists('redirect')) {
    function redirect($url = '')
    {
        if (empty($url)) {
            if (!empty($_REQUEST['ref_url'])) {
                $url = $_REQUEST['ref_url'];
            } else {
                $url = getReferer();
            }
        }
        header('Location: '.$url);
        exit();
    }
}

/**
 * 取上一步来源地址
 *
 * @param
 * @return string 字符串类型的返回结果
 */
if (! function_exists('getReferer')) {
    function getReferer()
    {
        return empty($_SERVER['HTTP_REFERER'])?'':$_SERVER['HTTP_REFERER'];
    }
}

/**
* 时间轴函数, Unix 时间戳
* @param int $time 时间
*/
if (! function_exists('tranTime')) {
    function tranTime($time)
    {
        $nowTime = time ();
        $message = '';
        //一年前
        if (idate ( 'Y', $nowTime ) != idate ( 'Y', $time )) {
            $message = date ( 'Y年m月d日', $time );
        } else {
            //同一年
            $days = idate ( 'z', $nowTime ) - idate ( 'z', $time );
            switch(true){
                //一天内
                case (0 == $days):
                    $seconds = $nowTime - $time;
                    //一小时内
                    if ($seconds < 3600) {
                        //一分钟内
                        if ($seconds < 60) {
                            if (3 > $seconds) {
                                $message = '刚刚';
                            } else {
                                $message = $seconds . '秒前';
                            }
                        }
                        $message = intval ( $seconds / 60 ) . '分钟前';
                    }
                    $message = idate ( 'H', $nowTime ) - idate ( 'H', $time ) . '小时前';
                    break;
                //昨天
                case (1 == $days):
                    $message = '昨天' . date ( 'H:i', $time );
                    break;
                //前天
                case (2 == $days):
                    $message = '前天 ' . date ( 'H:i', $time );
                    break;
                //7天内
                case (7 > $days):
                    $message = $days . '天前';
                    break;
                //超过7天
                default:
                    $message = date ( 'n月j日 H:i', $time );
                    break;
            }
        }
        return $message;
    }
}

/**
 * 打印变量并终止
 */
if (! function_exists("dd")) {
    function dd($data)
    {
        var_dump($data);die();
    }
}

/**
 * todo 待实现
 */
if (! function_exists("I")) {
    function I()
    {

    }
}

/**
 * todo 待实现
 */
if (! function_exists("D")) {
    function D()
    {

    }
}

/**
 * todo 待实现
 */
if (! function_exists("M")) {
    function M()
    {

    }
}