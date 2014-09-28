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
    public static $collectDir = "/home/admin/web/ccs/";

    /**
     * 代码工程基础目录
     * @var string
     */
    public static $baseDir = "";

    /**
     * 输出的html的目录
     * @var string
     */
    public static $outPutDir = "";

    public static function init($code_coverage_key) {
        if (!function_exists("xdebug_start_code_coverage")) {
            error_log("CodeCovergeSniffer INIT ERROR: xdebug not install ");
            return;
        }
        if (!file_exists(self::$collectDir)) {
            mkdir(self::$collectDir, 0777, true);
        }
        xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
        register_shutdown_function(function ($k) {
            CodeCovergeSniffer::collect($k);
        }, $code_coverage_key);
    }

    public static function collect($code_coverage_key) {
        // 捞一份旧的
        $old_cc = array();
        $cg = xdebug_get_code_coverage();
        xdebug_stop_code_coverage();
        if (file_exists(self::$collectDir . "/$code_coverage_key.php")) {
            $old_cc = include self::$collectDir . "/$code_coverage_key.php";
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
            file_put_contents(self::$collectDir . "/$code_coverage_key.php", "<?php return $content;", LOCK_EX);
        }
    }

    /**
     * 创建html
     * @param $code_coverage_key
     * @param $htdocs_dir
     * @param $output_dir
     */
    public static function generateHtml($code_coverage_key, $htdocs_dir, $output_dir) {
        $htdocs_dir = realpath($htdocs_dir) . "/";
        self::$baseDir = $htdocs_dir;
        self::$outPutDir = realpath($output_dir) . "/";
        self::copyr(realpath(__DIR__ . "/../css"), self::$outPutDir);
        self::copyr(__DIR__ . "/../js", self::$outPutDir);
        self::copyr(__DIR__ . "/../img", self::$outPutDir);

        $old_cc = include self::$collectDir . "/$code_coverage_key.php";

        self::generateDir($htdocs_dir, $old_cc);
    }

    /**
     * 遍历目录获取目录信息
     * @param $dir_scan
     * @param $old_cc
     */
    private static function generateDir($dir_scan, $old_cc) {
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
                self::generateDir($path, $old_cc);
            } else if (is_file($path)) {
                if (isset($old_cc[$path])) {
                    echo "IN" . PHP_EOL;
                    $scaned_res[$path] = 1;
                    self::_generatefile($path, $old_cc);
                } else {
                    echo "NOTIN" . PHP_EOL;
                    $scaned_res[$path] = -1;
                }
            }
        }

        self::_generateDir($dir_scan, $scaned_res, $old_cc);

    }

    /**
     * 生成目录信息
     * @param $dir_path
     * @param $scaned_res
     * @param $old_cc
     */
    private static function _generateDir($dir_path, $scaned_res, $old_cc) {
        $breads = explode("/", str_replace(self::$baseDir, "", $dir_path));
        ob_start();
        include __DIR__ . "/../tpl/dir.php";
        $html = ob_get_contents();
        ob_end_clean();
        $dir_path = str_replace(self::$baseDir, "", $dir_path);
        $save_path = str_replace("/", "_", trim($dir_path, "/")) . ".html";
        if ($save_path == ".html") {
            $save_path = "index.html";
        }
        echo "SAVING DIR: " . self::$outPutDir . $save_path . PHP_EOL;
        file_put_contents(self::$outPutDir . $save_path, $html);
    }

    /**
     * 生成单文件信息
     * @param $file_path
     * @param $old_cc
     */
    private static function _generatefile($file_path, $old_cc) {
        $breads = explode("/", str_replace(self::$baseDir, "", $file_path));
        $file_content = file_get_contents($file_path);
        $file_lines = explode(PHP_EOL, $file_content);
        $line_status = $old_cc[$file_path];
        ob_start();
        include __DIR__ . "/../tpl/file.php";
        $html = ob_get_contents();
        ob_end_clean();
        $file_path = str_replace(self::$baseDir, "", $file_path);
        $save_path = str_replace("/", "_", trim($file_path, "/")) . ".html";
        if ($save_path == ".html") {
            $save_path = "index.html";
        }
        echo "SAVING FILE: " . self::$outPutDir . $save_path . PHP_EOL;
        file_put_contents(self::$outPutDir . $save_path, $html);
    }

    /**
     * 用于批量复制文件过去
     * @param $source
     * @param $dest
     */
    static public function copyr($source, $dest) {
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
}