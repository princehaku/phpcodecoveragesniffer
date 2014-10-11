<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Code Coverage for <?php echo $dir_path ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <![endif]-->
</head>
<body>
<header>
    <div class="container">
        <div class="row">
            <div class="span12">
                <ul class="breadcrumb">
                    <li>
                        <a href="<?php echo empty($breads[0]) ? "#" : $breads[0] . "/../index.html"; ?>">Home</a> <span
                            class="divider">/</span>
                    </li>
                    <?php
                    $pathesp = array();

                    foreach ($breads as $path_seg) {
                        if (empty($path_seg)) {
                            continue;
                        }
                        $pathesp[] = $path_seg;
                        $seg_name = implode("_", $pathesp) . ".html";

                        if (isset($codeinfos[$this->outPutDir . $seg_name])) {
                            ?>
                            <li>
                                <a href="<?php echo implode("_", $pathesp) . ".html" ?>"><?php echo $path_seg ?></a> <span
                                    class="divider">/</span></li>
                        <?php } else { ?>
                            <li><?php echo $path_seg ?><span
                                    class="divider">/</span></li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</header>
<div class="container">
    <table class="table table-bordered">
        <thead>
        <tr>
            <td>&nbsp;</td>
            <td colspan="9">
                <div align="center"><strong>File Using Coverage</strong></div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td colspan="3">
                <div align="center"><strong>Lines</strong></div>
            </td>
            <td colspan="3">
                <div align="center"><strong>Functions and Methods</strong></div>
            </td>
        </tr>
        </thead>
        <tbody>
        <!--<tr>-->
        <!--<td class="warning">Total</td>-->
        <!--<td class="warning big">-->
        <!--<div class="progress progress-warning" style="width: 100px;">-->
        <!--<div class="bar" style="width: 35.21%;"></div>-->
        <!--</div>-->
        <!--</td>-->
        <!--<td class="warning small">-->
        <!--<div align="right">35.21%</div>-->
        <!--</td>-->
        <!--<td class="warning small">-->
        <!--<div align="right">25&nbsp;/&nbsp;71</div>-->
        <!--</td>-->
        <!--<td class="warning big">-->
        <!--<div class="progress progress-warning" style="width: 100px;">-->
        <!--<div class="bar" style="width: 55.56%;"></div>-->
        <!--</div>-->
        <!--</td>-->
        <!--<td class="warning small">-->
        <!--<div align="right">55.56%</div>-->
        <!--</td>-->
        <!--<td class="warning small">-->
        <!--<div align="right">10&nbsp;/&nbsp;18</div>-->
        <!--</td>-->
        <!--</tr>-->

        <?php foreach ($scaned_res as $path => $status) {
            $name = basename($path);
            $class = "";
            $persental = $using_line = $total_line = "";
            if ($status == 1) {
                $class = "success";
                // 统计行数
                $total_line = count($codeinfos[$path]);
                foreach ($codeinfos[$path] as $k) {
                    if ($k == 1) {
                        $using_line++;
                    }
                }
                // 如果非全部在使用中 标为黄色
                if ((100 * $using_line / $total_line) < 99) {
                    $class = "warning";
                }
            }
            // 仅php文件进行统计
            if ($status == -1 && strpos($name, ".php") !== false) {
                $class = "danger";
            }

            if ($total_line > 0) {
                $persental = number_format(100 * $using_line / $total_line, 2) . "%";
            }

            $file_path = implode("_", $pathesp) . "_" . $name . ".html";
            if (empty($pathesp)) {
                $file_path = $name . ".html";
            }
            $file_exists = file_exists(realpath($this->outPutDir . $file_path));
            // 几种特殊类型的拼接
            switch ($name) {
                case  ".":
                    $file_path = implode("_", $pathesp) == "" ? 'index.html' : implode("_", $pathesp) . ".html";
                    $file_exists = true;
                    break;
                case  "..":
                    $newpath = $pathesp;
                    unset($newpath[count($newpath) - 1]);
                    $file_path = implode("_", $newpath) == "" ? 'index.html' : implode("_", $newpath) . ".html";
                    $file_exists = true;
                    break;
            }

            ?>
            <tr>
                <td class="<?php echo $class ?>">
                    <?php if ($class == "") { ?>
                        <i class="icon-folder-open"></i>
                        <?php if (!$file_exists) {
                            ?>
                            <?php echo $name ?>
                        <?php
                        } else {
                            ?>
                            <a href="<?php echo $file_path ?>"><?php echo $name ?></a>
                        <?php } ?>
                    <?php } else if ($class == "danger") { ?>
                        <i class="icon-file"></i> <?php echo $name ?>
                    <?php } else { ?>
                        <i class="icon-file"></i>
                        <a href="<?php echo $file_path ?>"><?php echo $name ?></a>
                    <?php } ?>
                </td>
                <td class="<?php echo $class ?> big">
                    <div class="progress progress-<?php echo $class ?>" style="width: 100px;">
                        <div class="bar" style="width: <?php echo $persental ?>;"></div>
                    </div>
                </td>
                <td class="<?php echo $class ?> small">
                    <div align="right"><?php echo $persental ?></div>
                </td>
                <td class="<?php echo $class ?> small">
                    <div align="right"><?php echo $using_line ?>&nbsp;/&nbsp;<?php echo $total_line ?></div>
                </td>
                <td class="<?php echo $class ?> big">
                    <div class="progress progress-warning" style="width: 100px;">
                        <div class="bar" style="width: 0%;"></div>
                    </div>
                </td>
                <td class="<?php echo $class ?> small">
                    <div align="right"></div>
                </td>
                <td class="<?php echo $class ?> small">
                    <div align="right">&nbsp;/&nbsp;</div>
                </td>
            </tr>
        <?php
        }?>

        </tbody>
    </table>
    <footer>
        <h4>Legend</h4>

        <p>
            <span class="danger"><strong>Dead</strong></span>
            <span class="warning"><strong></strong></span>
            <span class="success"><strong>Using</strong></span>
        </p>

        <p>
            <small>Generated by CodeCovergeSniffer</small>
        </p>
    </footer>
</div>
</body>
</html>
