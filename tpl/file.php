<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Code Coverage for <?php echo $file_path ?></title>
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
                    <?php
                    $pathes = array();
                    foreach ($breads as $path_seg) {
                        if (empty($path_seg)) {
                            continue;
                        }
                        $pathesp[] = $path_seg;
                        $seg_name = implode("_", $pathesp) . ".html";

                        if (file_exists($this->outPutDir . $seg_name)) {
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
    <!--   <table class="table table-bordered">-->
    <!--    <thead>-->
    <!--     <tr>-->
    <!--      <td>&nbsp;</td>-->
    <!--      <td colspan="9"><div align="center"><strong>Code Coverage</strong></div></td>-->
    <!--     </tr>-->
    <!--     <tr>-->
    <!--      <td>&nbsp;</td>-->
    <!--      <td colspan="3"><div align="center"><strong>Lines</strong></div></td>-->
    <!--      <td colspan="3"><div align="center"><strong>Functions and Methods</strong></div></td>-->
    <!--      <td colspan="3"><div align="center"><strong>Classes and Traits</strong></div></td>-->
    <!--     </tr>-->
    <!--    </thead>-->
    <!--    <tbody>-->
    <!--     <tr>-->
    <!--      <td class="warning">Total</td>-->
    <!--      <td class="warning big">       <div class="progress progress-warning" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 35.21%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="warning small"><div align="right">35.21%</div></td>-->
    <!--      <td class="warning small"><div align="right">25&nbsp;/&nbsp;71</div></td>-->
    <!--      <td class="warning big">       <div class="progress progress-warning" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 55.56%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="warning small"><div align="right">55.56%</div></td>-->
    <!--      <td class="warning small"><div align="right">10&nbsp;/&nbsp;18</div></td>-->
    <!--      <td class="danger big">       <div class="progress progress-danger" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 33.33%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="danger small"><div align="right">33.33%</div></td>-->
    <!--      <td class="danger small"><div align="right">1&nbsp;/&nbsp;3</div></td>-->
    <!--     </tr>-->
    <!---->
    <!--     <tr>-->
    <!--      <td class="None"><i class="icon-file"></i> <a href="web_widget_Api.php.html">Api.php</a></td>-->
    <!--      <td class="None big">&nbsp;</td>-->
    <!--      <td class="None small"><div align="right"></div></td>-->
    <!--      <td class="None small"><div align="right">&nbsp;</div></td>-->
    <!--      <td class="None big">&nbsp;</td>-->
    <!--      <td class="None small"><div align="right"></div></td>-->
    <!--      <td class="None small"><div align="right">&nbsp;</div></td>-->
    <!--      <td class="success big">       <div class="progress progress-success" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 100.00%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="success small"><div align="right">100.00%</div></td>-->
    <!--      <td class="success small"><div align="right">1&nbsp;/&nbsp;1</div></td>-->
    <!--     </tr>-->
    <!---->
    <!--     <tr>-->
    <!--      <td class="warning"><i class="icon-file"></i> <a href="web_widget_Widget.php.html">Widget.php</a></td>-->
    <!--      <td class="warning big">       <div class="progress progress-warning" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 41.67%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="warning small"><div align="right">41.67%</div></td>-->
    <!--      <td class="warning small"><div align="right">10&nbsp;/&nbsp;24</div></td>-->
    <!--      <td class="warning big">       <div class="progress progress-warning" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 50.00%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="warning small"><div align="right">50.00%</div></td>-->
    <!--      <td class="warning small"><div align="right">5&nbsp;/&nbsp;10</div></td>-->
    <!--      <td class="danger big">       <div class="progress progress-danger" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 0.00%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="danger small"><div align="right">0.00%</div></td>-->
    <!--      <td class="danger small"><div align="right">0&nbsp;/&nbsp;1</div></td>-->
    <!--     </tr>-->
    <!---->
    <!--     <tr>-->
    <!--      <td class="danger"><i class="icon-file"></i> <a href="web_widget_WidgetManager.php.html">WidgetManager.php</a></td>-->
    <!--      <td class="danger big">       <div class="progress progress-danger" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 31.91%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="danger small"><div align="right">31.91%</div></td>-->
    <!--      <td class="danger small"><div align="right">15&nbsp;/&nbsp;47</div></td>-->
    <!--      <td class="warning big">       <div class="progress progress-warning" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 62.50%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="warning small"><div align="right">62.50%</div></td>-->
    <!--      <td class="warning small"><div align="right">5&nbsp;/&nbsp;8</div></td>-->
    <!--      <td class="danger big">       <div class="progress progress-danger" style="width: 100px;">-->
    <!--        <div class="bar" style="width: 0.00%;"></div>-->
    <!--       </div>-->
    <!--</td>-->
    <!--      <td class="danger small"><div align="right">0.00%</div></td>-->
    <!--      <td class="danger small"><div align="right">0&nbsp;/&nbsp;1</div></td>-->
    <!--     </tr>-->
    <!---->
    <!---->
    <!--    </tbody>-->
    <!--   </table>-->

    <table class="table table-borderless table-condensed">
        <tbody>
        <?php foreach ($file_lines as $file_line => $line_content) {
            if (!isset($line_status[$file_line + 1])) {
                $class = "";
            } else if ($line_status[$file_line + 1] == 1) {
                $class = "success";
            } else if ($line_status[$file_line + 1] == -2) {
                $class = "warning";
            } else if ($line_status[$file_line + 1] == -1) {
                $class = "danger";
            }
            ?>
            <tr class="<?php echo $class; ?>">
                <td>
                    <div align="right"><a name="<?php echo $file_line ?>"></a><a
                            href="#<?php echo $file_line ?>"><?php echo $file_line ?></a></div>
                </td>
                <td class="codeLine"><?php
                    if ($encoding != "UTF-8") {
                        $line_content = (@iconv($encoding, "utf-8//IGNORE", $line_content));
                    }
                    echo $line_content;
                    ?></td>
            </tr>
        <?php
        }
        ?>

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
