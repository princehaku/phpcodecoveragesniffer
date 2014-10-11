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

/**
 * 代码覆盖收集
 * Class CodeCovergeSniffer
 *
 */
class CodeCovergeSniffer {

    /**
     * 收集覆盖率的文件夹
     * @var string
     * @default ../runtime/
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
     * 忽略收集的文件|目录名
     * @var
     */
    protected $ignoreName = array(
        ".",
        "..",
        ".idea",
        ".git",
        "vendor"
    );

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
     * 不进行代码覆盖收集的目录|文件名
     * 不支持通配符
     * @param $array
     */
    public function addIgnoreNames($array) {
        $this->ignoreName = array_merge($this->ignoreName, $array);
    }

    /**
     * 初始化一次收集
     * 默认会自动在程序结束时进行收集
     * @param $code_coverage_key | 收集用的唯一标志
     * @param $auto_collect | 默认开启
     */
    public function init($code_coverage_key, $auto_collect = true) {
        if (!function_exists("xdebug_start_code_coverage")) {
            error_log("CodeCovergeSniffer INIT ERROR: xdebug not install ");
            return false;
        }
        // 如果收集文件夹不存在，使用默认目录
        if (empty($this->collectDir)) {
            $this->collectDir = realpath(__DIR__ . "/../runtime/") . "/";
        }
        if (!file_exists($this->collectDir)) {
            mkdir($this->collectDir, 0777, true);
        }
        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);

        if ($auto_collect) {
            register_shutdown_function(function ($obj, $k) {
                $obj->collect($k);
            }, $this, $code_coverage_key);
        }

        return true;
    }

    /**
     * 保存已经收集到的数据们
     * @param $code_coverage_key
     */
    public function collect($code_coverage_key) {
        // 捞一份旧的
        $codeinfos = array();
        $cg = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();
        if (file_exists($this->collectDir . "/$code_coverage_key.php")) {
            $codeinfos = include $this->collectDir . "/$code_coverage_key.php";
        }
        foreach ($cg as $path => $lines) {
            if (!isset($codeinfos[$path])) {
                $codeinfos[$path] = array();
            }
            foreach ($lines as $line => $status) {
                if ((isset($codeinfos[$path][$line]) && $codeinfos[$path][$line] == 1) || $status == 1) {
                    $codeinfos[$path][$line] = 1;
                } else {
                    $codeinfos[$path][$line] = $status;
                }
            }
        }
        if (is_array($codeinfos)) {
            $content = var_export($codeinfos, 1);
            // 加锁形式的写入，性能会有下降的哦
            file_put_contents($this->collectDir . "/$code_coverage_key.php", "<?php return $content;", LOCK_EX);
        }
    }

    /**
     * 创建html们
     * @param $code_coverage_key
     */
    public function generateHtml($code_coverage_key, $clean_outputdir = false) {
        // 如果收集文件夹不存在，使用工程下默认目录
        if (empty($this->collectDir)) {
            $this->collectDir = realpath(__DIR__ . "/../runtime/") . "/";
        }
        if (empty($this->outPutDir) || empty($this->baseDir)) {
            error_log("You Must specifial collectDir outPutDir baseDir");
            return;
        }
        if ($clean_outputdir) {
            self::deldir($this->outPutDir);
        }

        self::copyr(realpath(__DIR__ . "/../css"), $this->outPutDir);
        self::copyr(__DIR__ . "/../js", $this->outPutDir);
        self::copyr(__DIR__ . "/../img", $this->outPutDir);

        $codeinfos = include $this->collectDir . "/$code_coverage_key.php";
        // 对key做一次处理，处理出目录的信息
        foreach ($codeinfos as $path => $v) {
            $dir_path = dirname($path);
            $codeinfos[$dir_path] = array();
        }
        $this->generateDir($this->baseDir, $codeinfos);
    }

    /**
     * 遍历目录获取目录信息
     * @param $dir_scan
     * @param $codeinfos
     */
    private function generateDir($dir_scan, $codeinfos) {
        $scaned_dirs = scandir($dir_scan);
        $scaned_res = array();
        foreach ($scaned_dirs as $name) {
            $path = (rtrim(($dir_scan), "/") . "/" . $name);
            $scaned_res[$path] = 0;
            if (in_array($name, $this->ignoreName)) {
                continue;
            }
            echo $path . PHP_EOL;
            if (is_dir($path)) {
                echo "DIR" . PHP_EOL;
                $this->generateDir($path, $codeinfos);
            } else if (is_file($path)) {
                if (isset($codeinfos[$path])) {
                    echo "IN" . PHP_EOL;
                    $scaned_res[$path] = 1;
                    $this->_generatefile($path, $codeinfos);
                } else {
                    echo "NOTIN" . PHP_EOL;
                    $scaned_res[$path] = -1;
                }
            }
        }
        // 文件创建玩后再处理目录,可以统计文件信息
        $this->_generateDir($dir_scan, $scaned_res, $codeinfos);

    }

    /**
     * 生成目录信息
     * @param $dir_path
     * @param $scaned_res
     * @param $codeinfos
     */
    private function _generateDir($dir_path, $scaned_res, $codeinfos) {
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
     * @param $codeinfos
     */
    private function _generatefile($file_path, $codeinfos) {
        $breads = explode("/", str_replace($this->baseDir, "", $file_path));
        $file_content = file_get_contents($file_path);
        $file_lines = explode(PHP_EOL, $file_content);
        $line_status = $codeinfos[$file_path];
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
    private static function copyr($source, $dest) {
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
                        self::copyr($source . "/" . $file, $dest . "/" . $sourcefolder);
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

    private static function deldir($dir) {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }

    }
}