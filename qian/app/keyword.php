<?php
header("Content-Type:text/html;charset=UTF-8");
date_default_timezone_set("PRC");
$showapi_appid = '55750';  //�滻��ֵ,�ڹ�����"�ҵ�Ӧ��"���ҵ����ֵ
$showapi_secret = '1a5906422c39468795d8db8621535d59';  //�滻��ֵ,�ڹ�����"�ҵ�Ӧ��"���ҵ����ֵ
$paramArr = array(
    'showapi_appid'=> $showapi_appid,
    'text'=> "",
    'num'=> ""
//�����������
);

//��������(����ǩ���Ĵ���)
function createParam ($paramArr,$showapi_secret) {
    $paraStr = "";
    $signStr = "";
    ksort($paramArr);
    foreach ($paramArr as $key => $val) {
        if ($key != '' && $val != '') {
            $signStr .= $key.$val;
            $paraStr .= $key.'='.urlencode($val).'&';
        }
    }
    $signStr .= $showapi_secret;//�ź���Ĳ�������secret,����md5
    $sign = strtolower(md5($signStr));
    $paraStr .= 'showapi_sign='.$sign;//��md5���ֵ��Ϊ����,���ڷ�������Ч��
    echo "�ź���Ĳ���:".$signStr."\r\n";
    return $paraStr;
}

$param = createParam($paramArr,$showapi_secret);
$url = 'http://route.showapi.com/941-1?'.$param;
echo "�����url:".$url."\r\n";
$result = file_get_contents($url);
echo "���ص�json����:\r\n";
print $result.'\r\n';
$result = json_decode($result);
echo "\r\nȡ��showapi_res_code��ֵ:\r\n";
print_r($result->showapi_res_code);
echo "\r\n";
?>