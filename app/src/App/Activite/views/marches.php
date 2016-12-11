<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Smart Metrix</title>

    <link rel="stylesheet" type="text/css" href="lib/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="lib/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/app.css">
</head>
<body>

 <!-- side bar -->
<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="index.html" class="site_title"><i class="fa fa-dashboard"></i> <span>Tableau de bord</span></a>
    <div class="side_links">
        <li>
            <a>
                <i class="fa fa-eye"></i> Discover
            </a>
        </li>
        <li>
            <a>
                <i class="fa fa-line-chart"></i> Visualize
            </a>
        </li>
        <li>
            <a>
                <i class="fa fa-bar-chart"></i> Graphs
            </a>
        </li>
    </div>
</div>

<!-- core de la page avec top navbar -->
<div id="main" style="margin-left: 250px">
    <ul class="nav navbar-nav name_custom">
        <span class="hamb" onclick="openNav()"><i class="fa fa-bars"></i></span>
        <li>
            <a href="#">Admin</a>
        </li>
    </ul>

    <!-- core de la page sous la navbar grise -->
    <div id="chart" style="width: 800px; height: 600px;"></div>
    <?php var_dump();?>
</div>


<script type="text/javascript" src="lib/js/jquery.js"></script>
<script type="text/javascript" src="lib/js/bootstrap.min.js"></script>
<script type="text/javascript" src="lib/js/echarts-all.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script>

var option = {
    tooltip : {
        trigger: 'axis',
        axisPointer : {
            type : 'shadow'
        }
    },
    legend: {
        data:['Toto','Yannux','Stock','Titi','搜索引擎','百度','谷歌','必应','其他']
    },
    toolbox: {
        show : true,
        orient: 'vertical',
        x: 'right',
        y: 'center',
        feature : {
            mark : {show: true},
            dataView : {show: true, readOnly: false},
            magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
            restore : {show: true},
            saveAsImage : {show: true}
        }
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            data : ['Accessoires','周二','周三','周四','周五','周六','周日']
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'Toto',
            type:'bar',
            data:[320, 332, 301, 334, 390, 330, 320]
        },
        {
            name:'Yannux',
            type:'bar',
            stack: 'latifa',
            data:[120, 132, 101, 134, 90, 230, 210]
        },
        {
            name:'Stock',
            type:'bar',
            stack: 'latifa',
            data:[220, 182, 191, 234, 290, 330, 310]
        },
        {
            name:'Titi',
            type:'bar',
            stack: 'latifa',
            data:[150, 232, 201, 154, 190, 330, 410]
        },
        {
            name:'搜索引擎',
            type:'bar',
            data:[862, 1018, 964, 1026, 1679, 1600, 1570],
            markLine : {
                itemStyle:{
                    normal:{
                        lineStyle:{
                            type: 'dashed'
                        }
                    }
                },
                data : [
                    [{type : 'min'}, {type : 'max'}]
                ]
            }
        },
        {
            name:'百度',
            type:'bar',
            barWidth : 5,
            stack: '搜索引擎',
            data:[620, 732, 701, 734, 1090, 1130, 1120]
        },
        {
            name:'谷歌',
            type:'bar',
            stack: '搜索引擎',
            data:[120, 132, 101, 134, 290, 230, 220]
        },
        {
            name:'必应',
            type:'bar',
            stack: '搜索引擎',
            data:[60, 72, 71, 74, 190, 130, 110]
        },
        {
            name:'其他',
            type:'bar',
            stack: '搜索引擎',
            data:[62, 82, 91, 84, 109, 110, 120]
        }
    ]
};
var chart = echarts.init(document.getElementById('chart'));
chart.setOption(option);

</script>
</body>
</html>