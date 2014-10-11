<?php
/**
 * Copyright 2014
 *  FileName   : generate.php
 *  Created on : 14-9-26 , 下午3:09
 *  Author     : haku-mac
 *  Account    : zhongwei.bzw
 *  Blog       : http://3haku.net
 */

include "../none_composer_loader.php";

// 初始化: 创建的时候需要指定你的工程目录. 和输出路径
$working_dir = __DIR__ . "/";
$output_dir = __DIR__ . "/ccs/";
if (!file_exists($output_dir)) {
    mkdir($output_dir);
}
$cs = new CodeCovergeSniffer();
$cs->addIgnoreNames(array("ccs", "test.php"));
$cs->setBaseDir($working_dir);
$cs->setOutPutDir($output_dir);

// 尝试收集
$cs->init("test", false);
include "demo_test_res.php";
$cs->collect("test");

// 输出结果
$cs->generateHtml("test");