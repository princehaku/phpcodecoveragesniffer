<?php
/**
 * Copyright 2014
 *  FileName   : generate.php
 *  Created on : 14-9-26 , ����3:09
 *  Author     : haku-mac
 *  Account    : zhongwei.bzw
 *  Blog       : http://3haku.net
 */

include "lib/CodeCovergeSniffer.php";
// ������ʱ����Ҫָ����Ĺ���Ŀ¼. �����·��
$collect_dir = __DIR__ . "/../../../protected/runtime/";
$working_dir = __DIR__ . "/../../../";
$output_dir = __DIR__ . "/../../../srp/cc/";
if (!file_exists($output_dir)) {
    mkdir($output_dir);
}
$cs = new CodeCovergeSniffer();
$cs->setCollectDir($collect_dir);
$cs->setBaseDir($working_dir);
$cs->setOutPutDir($output_dir);
$cs->generateHtml("mido");