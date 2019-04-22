<?php
/**
 * Created by IntelliJ IDEA.
 * User: wujindong
 * Date: 2016/12/12
 * Time: 下午3:48
 */
function http_curl($url,$data='',$type="post",$res='json'){
    //初始化curl
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$ul=$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //默认是以get形式请求
    if ($type == 'post'){
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }
    //采集
    $output = curl_exec($ch);
    curl_close($ch);
    if ($res == 'json'){
        if ( curl_errno($ch)){
            return false;
        }else{
            return json_encode($output,true);
        }
    }
}