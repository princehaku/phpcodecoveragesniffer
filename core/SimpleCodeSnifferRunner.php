<?php
/**
 * Copyright 2014
 *  FileName   : SimpleCodeSnifferRunner.php
 *  Created on : 14-10-10 , 下午2:56
 *  Author     : haku-mac
 *  Account    : zhongwei.bzw
 *  Blog       : http://3haku.net
 */

/**
 * 封装的最简单使用的codecollector
 * Class SimpleCodeSnifferRunner
 */
class SimpleCodeSnifferRunner {

    public static function init($default_key = "ccs") {
        // 代码覆盖统计，需xdebug支持 -- zhongwei.bzw
        if (!isset($_GET['code_collect'])) {
            return false;
        }
        $code_coverage_key = $default_key . "_" . md5(time());
        // 如果是append模式，保持key不变
        if (isset($_GET['collect_mode']) && $_GET['collect_mode'] == "APPEND") {
            $code_coverage_key = $default_key;
        }
        if (isset($_GET['code_key'])) {
            $code_coverage_key = $_GET['code_key'];
        }
        $cs = new CodeCovergeSniffer();
        // 创建的时候需要指定你的工程目录. 和输出路径
        $working_dir = $_SERVER['DOCUMENT_ROOT'] . "/";
        $output_dir = $working_dir . "/ccs/$code_coverage_key/";
        // 如果是输出文件模式
        if (isset($_GET['code_generate'])) {
            if (!file_exists($output_dir)) {
                mkdir($output_dir, 0777, 1);
            }
            $cs->setBaseDir($working_dir);
            $cs->setOutPutDir($output_dir);
            $cs->generateHtml("$code_coverage_key");
            $path = "/ccs/$code_coverage_key/index.html";
            echo "<script>window.document.location = '$path';</script>";
            die;
        }
        // 初始化收集器
        if ($cs->init($code_coverage_key)) {
            register_shutdown_function(function ($code_coverage_key) {
                echo "<a target='_blank' href='?code_collect=true&code_generate=true&code_key=${code_coverage_key}'>show code coverage graph</a>";
            }, $code_coverage_key);
        }
    }
}