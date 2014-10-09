<?php

/**
 * Copyright 2014
 *  FileName   : CodeCovergeSniffer.php
 *  Created on : 14-9-26 , ����3:10
 *  Author     : haku-mac
 *  Account    : zhongwei.bzw
 *  Blog       : http://3haku.net
 *
 */
class CodeCovergeSniffer {

    /**
     * �ռ������ʵ��ļ���
     * @var string
     */
    protected $collectDir = "";

    /**
     * ���빤�̻���Ŀ¼
     * @var string
     */
    protected $baseDir = "";

    /**
     * ���빤���ļ��ı���
     * @var string
     */
    protected $fileEncoding = "UTF-8";

    /**
     * �����html��Ŀ¼
     * @var string
     */
    protected $outPutDir = "";

    /**
     * @param string $baseDir
     */
    public function setBaseDir($baseDir, $file_encoding = "") {
        $this->fileEncoding = strtoupper($file_encoding);
        $this->baseDir = realpath($baseDir) . "/";
    }

    /**
     * @param string $collectDir
     */
    public function setCollectDir($collectDir) {
        $this->collectDir = realpath($collectDir) . "/";
    }

    /**
     * @param string $outPutDir
     */
    public function setOutPutDir($outPutDir) {
        $this->outPutDir = realpath($outPutDir) . "/";
    }

    public function init($code_coverage_key) {
        if (!function_exists("xdebug_start_code_coverage")) {
            error_log("CodeCovergeSniffer INIT ERROR: xdebug not install ");
            return;
        }
        if (empty($this->collectDir)) {
            error_log("CodeCovergeSniffer INIT ERROR: have not set collectDir yet");
            return;
        }
        if (!file_exists($this->collectDir)) {
            mkdir($this->collectDir, 0777, true);
        }
        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
        register_shutdown_function(function ($obj, $k) {
            $obj->collect($k);
        }, $this, $code_coverage_key);
    }

    public function collect($code_coverage_key) {
        // ��һ�ݾɵ�
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
     * ����html
     * @param $code_coverage_key
     * @param $htdocs_dir
     * @param $output_dir
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

        $this->generateDir($this->baseDir, $old_cc);
    }

    /**
     * ����Ŀ¼��ȡĿ¼��Ϣ
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
     * ����Ŀ¼��Ϣ
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
     * ���ɵ��ļ���Ϣ
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
     * �������������ļ���ȥ
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