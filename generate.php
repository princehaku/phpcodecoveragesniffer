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
$working_dir = __DIR__ . "/../../";
$output_dir =  __DIR__ . "/../../srp/cc/";
if (!file_exists($output_dir)) {
    mkdir($output_dir);
}
CodeCovergeSniffer::generateHtml("develop", $working_dir, $output_dir);