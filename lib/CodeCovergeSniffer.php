<?php

/**
 * Copyright 2014
 *  FileName   : CodeCovergeSniffer.php
 *  Created on : 14-9-26 , 下午3:10
 *  Author     : haku-mac
 *  Account    : zhongwei.bzw
 *  Blog       : http://3haku.net
 *
 */
class CodeCovergeSniffer {

    /**
     * 收集覆盖率的文件夹
     * @var string
     */
    protected $collectDir = "";

    /**
     * 代码工程基础目录
     * @var string
     */
    protected $baseDir = "";

    /**
     * 代码工程文件的编码
     * @var string
     */
    protected $fileEncoding = "UTF-8";

    /**
     * 输出的html的目录
     * @var string
     */
    protected $outPutDir = "";

    /**
     * @param string $baseDir 工程文件的基础目录
     * @param string $file_encoding 工程文件的编码 默认UTF-8
     */
    public function setBaseDir($baseDir, $file_encoding = "") {
        $this->fileEncoding = strtoupper($file_encoding);
        $this->baseDir = realpath($baseDir) . "/";
    }

    /**
     * 收集的info存放的目录
     * @param string $collectDir
     */
    public function setCollectDir($collectDir) {
        $this->collectDir = realpath($collectDir) . "/";
    }

    /**
     * 文件输出的目录
     * @param string $outPutDir
     */
    public function setOutPutDir($outPutDir) {
        $this->outPutDir = realpath($outPutDir) . "/";
    }

    /**
     * ʹ��һ��key���г�ʼ��
     * @param $code_coverage_key
     * @return bool
     */
    public function init($code_coverage_key) {
        if (!function_exists("xdebug_start_code_coverage")) {
            error_log("CodeCovergeSniffer INIT ERROR: xdebug not install ");
            return false;
        }
        if (empty($this->collectDir)) {
            error_log("CodeCovergeSniffer INIT ERROR: have not set collectDir yet");
            return false;
        }
        if (!file_exists($this->collectDir)) {
            mkdir($this->collectDir, 0777, true);
        }
        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
        register_shutdown_function(function ($obj, $k) {
            $obj->collect($k);
        }, $this, $code_coverage_key);
        return true;
    }

    public function collect($code_coverage_key) {
        // 捞一份旧的
        $old_cc = array();
        $cg = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();
        if (file_exists($this->collectDir . "/$code_coverage_key.php")) {
            $old_cc = include $this->collectDir . "/$code_coverage_key.php";
        }
        foreach ($cg as $path => $lines) {
            if (!isset($old_cc[$path])) {
                $old_cc[$path] = array();
            }
            foreach ($lines as $line => $status) {
                if ((isset($old_cc[$path][$line]) && $old_cc[$path][$line] == 1) || $status == 1) {
                    $old_cc[$path][$line] = 1;
                } else {
                    $old_cc[$path][$line] = $status;
                }
            }
        }
        if (is_array($old_cc)) {
            $content = var_export($old_cc, 1);
            file_put_contents($this->collectDir . "/$code_coverage_key.php", "<?php return $content;", LOCK_EX);
        }
    }

    /**
     * 创建html们
     * @param $code_coverage_key
     */
    public function generateHtml($code_coverage_key) {
        if (empty($this->collectDir) || empty($this->outPutDir) || empty($this->baseDir)) {
            error_log("You Must specifial collectDir outPutDir baseDir");
            return;
        }
        $this->copyr(realpath(__DIR__ . "/../css"), $this->outPutDir);
        $this->copyr(__DIR__ . "/../js", $this->outPutDir);
        $this->copyr(__DIR__ . "/../img", $this->outPutDir);

        $old_cc = include $this->collectDir . "/$code_coverage_key.php";
        // 对key做一次处理，处理出目录的信息
        foreach($old_cc as $path=>$v) {
            $dir_path = dirname($path);
            $old_cc[$dir_path] = array();
        }
        $this->generateDir($this->baseDir, $old_cc);
    }

    /**
     * 遍历目录获取目录信息
     * @param $dir_scan
     * @param $old_cc
     */
    private function generateDir($dir_scan, $old_cc) {
        $scaned_dirs = scandir($dir_scan);
        $scaned_res = array();
        foreach ($scaned_dirs as $dir) {
            $path = (rtrim(($dir_scan), "/") . "/" . $dir);
            $scaned_res[$path] = 0;
            if (in_array($dir, array(".", "..", ".git", "vendor"))) {
                continue;
            }
            echo $path . PHP_EOL;
            if (is_dir($path)) {
                echo "DIR" . PHP_EOL;
                $this->generateDir($path, $old_cc);
            } else if (is_file($path)) {
                if (isset($old_cc[$path])) {
                    echo "IN" . PHP_EOL;
                    $scaned_res[$path] = 1;
                    $this->_generatefile($path, $old_cc);
                } else {
                    echo "NOTIN" . PHP_EOL;
                    $scaned_res[$path] = -1;
                }
            }
        }

        $this->_generateDir($dir_scan, $scaned_res, $old_cc);

    }

    /**
     * 生成目录信息
     * @param $dir_path
     * @param $scaned_res
     * @param $old_cc
     */
    private function _generateDir($dir_path, $scaned_res, $old_cc) {
        $breads = explode("/", str_replace($this->baseDir, "", $dir_path));
        ob_start();
        include __DIR__ . "/../tpl/dir.php";
        $html = ob_get_contents();
        ob_end_clean();
        $dir_path = str_replace($this->baseDir, "", $dir_path);
        $save_path = str_replace("/", "_", trim($dir_path, "/")) . ".html";
        if ($save_path == ".html") {
            $save_path = "index.html";
        }
        echo "SAVING DIR: " . $this->outPutDir . $save_path . PHP_EOL;
        file_put_contents($this->outPutDir . $save_path, $html);
    }

    /**
     * 生成单文件信息
     * @param $file_path
     * @param $old_cc
     */
    private function _generatefile($file_path, $old_cc) {
        $breads = explode("/", str_replace($this->baseDir, "", $file_path));
        $file_content = file_get_contents($file_path);
        $file_lines = explode(PHP_EOL, $file_content);
        $line_status = $old_cc[$file_path];
        $encoding = $this->fileEncoding;
        ob_start();
        include __DIR__ . "/../tpl/file.php";
        $html = ob_get_contents();
        ob_end_clean();
        $file_path = str_replace($this->baseDir, "", $file_path);
        $save_path = str_replace("/", "_", trim($file_path, "/")) . ".html";
        if ($save_path == ".html") {
            $save_path = "index.html";
        }
        echo "SAVING FILE: " . $this->outPutDir . $save_path . PHP_EOL;
        file_put_contents($this->outPutDir . $save_path, $html);
    }

    /**
     * 用于批量复制文件过去，主要是css和js，方便查看
     * @param $source
     * @param $dest
     */
    private function copyr($source, $dest) {
        // recursive function to copy
        // all subdirectories and contents:
        if (is_dir($source)) {
            $dir_handle = opendir($source);
            $sourcefolder = basename($source);
            if (!file_exists($dest . "/" . $sourcefolder)) {
                mkdir($dest . "/" . $sourcefolder, 0777, true);
            }
            while ($file = readdir($dir_handle)) {
                if ($file != "." && $file != "..") {
                    if (is_dir($source . "/" . $file)) {
                        $this->copyr($source . "/" . $file, $dest . "/" . $sourcefolder);
                    } else {
                        copy($source . "/" . $file, $dest . "/" . $sourcefolder . "/" . $file);
                    }
                }
            }
            closedir($dir_handle);
        } else {
            // can also handle simple copy commands
            copy($source, $dest);
        }
    }
}