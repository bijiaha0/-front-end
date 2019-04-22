
var fileUploadMountain = new Object({
    URL: {
        BASE_URL: function() {
            return "http://54.193.40.253:8080/data/data/";
        },
        TTEST_URL : function(){
            return fileUploadMountain.URL.BASE_URL() + "test";
        }
    },
    Utils:{
        //在群名称中查找target
        search: function(src, target) {
            for(var i = 0; i < src.length; i++) {
                if(src[i] == target) {
                    return i;
                }
            }
        },
        //定义一个产生随机颜色的函数
        getRandomColor: function() {
            return (function(m, s, c) {
                return (c ? arguments.callee(m, s, c - 1) : '#') +
                    s[m.floor(m.random() * 16)]
            })(Math, '0123456789abcdef', 5)
        },
        //定义一个求四舍五入到num后面的n位的函数
        getResult:function(value,n){
            return Math.round(value*Math.pow(10,n))/Math.pow(10,n);
        },
        lineData:function (Ymin,Xmax,Xmin,qPosition,cutoffall) {
            var lineData=[];

            //加入arm线
            if($("#islog").is(':checked')){
                lineData.push([{
                    coord: [qPosition, -0.05],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: "#000",
                            width: 5,
                            type: 'dotted'
                        }
                    },
                    tooltip: {
                        formatter: 'Centromere: ' + qPosition
                    }
                }, {
                    coord: [qPosition, 0.05],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: "#000",
                            width: 5,
                            type: 'dotted'
                        }
                    }
                }])
            }else{
                lineData.push([{
                    coord: [qPosition, 1.95],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: "#000",
                            width: 5,
                            type: 'dotted'
                        }
                    },
                    tooltip: {
                        formatter: 'Centromere: ' + qPosition
                    }
                }, {
                    coord: [qPosition, 2.05],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: "#000",
                            width: 5,
                            type: 'dotted'
                        }
                    }
                }])
            }
            //实实在在的画cutoff的线
            if(cutoffall.length !=0){
                cutoffall.forEach(function(c) {
                    var line = [{
                        coord: [Xmin, c[0]],
                        symbol: 'arrow',
                        tooltip: {
                            formatter: "cutoff"
                        },
                        lineStyle: {
                            normal: {
                                color: '#000',
                                width: 1,
                                type: 'solid'
                            }
                        }
                    }, {
                        coord: [Xmax, c[0]],
                        symbol: 'arrow'
                    }];
                    lineData.push(line);
                });
            }
            return lineData;
        },

    },
    view:{
        init:function () {
            /**初始化配置项ui*/
            $("#pixelRatio_select").html("");
            for(var i = 1; i < 50; i++) {
                $("#pixelRatio_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
            }
            $("#saveType_select").html("");
            $("#saveType_select").append("<option class='col-md-12' value='png'>png</option>");
            $("#saveType_select").append("<option class='col-md-12' value='jpeg'>jpeg</option>");

            $("#pointStyle_select").html("");
            $("#pointStyle_select").append("<option class='col-md-12' value='circle'>circle</option>");
            $("#pointStyle_select").append("<option class='col-md-12' value='rect'>rect</option>");
            $("#pointStyle_select").append("<option class='col-md-12' value='roundRect'>roundRect</option>");
            $("#pointStyle_select").append("<option class='col-md-12' value='triangle'>triangle</option>");
            $("#pointStyle_select").append("<option class='col-md-12' value='diamond'>diamond</option>");
            $("#pointStyle_select").append("<option class='col-md-12' value='pin'>pin</option>");
            $("#pointStyle_select").append("<option class='col-md-12' value='arrow'>arrow</option>");

            $("#pointSize_select").html("");
            $("#GeneSizeStyle_select").html("");
            $("#pointSize_select").append("<option class='col-md-12' value='5'>5</option>");
            $("#GeneSizeStyle_select").append("<option class='col-md-12' value='10'>10</option>");
            for (var i = 1; i < 30; i++) {
                $("#pointSize_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
                $("#GeneSizeStyle_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
            }

            $("#pointColor_select").html("");

            $("#geneColor_select").html("");

            var color_arr = ["Black", "AliceBlue", "AntiqueWhite", "Aqua", "Aquamarine",
                /**/
                "Azure", "Beige", "Bisque", "Black", "BlanchedAlmond", "Blue", "BlueViolet",
                /**/
                "Brown", "BurlyWood", "CadetBlue", "Chartreuse", "Chocolate", "Coral", "CornflowerBlue", "Cornsilk", "Crimson",
                /**/
                "Cyan", "DarkBlue", "DarkCyan", "DarkGoldenRod", "DarkGray", "DarkGreen",
                /**/
                "DarkKhaki", "DarkMagenta", "DarkOliveGreen", "Darkorange", "DarkOrchid", "DarkRed",
                /**/
                "DarkSalmon", "DarkSeaGreen", "DarkSlateBlue", "DarkSlateGray", "DarkTurquoise",
                /**/
                "DarkViolet", "DeepPink", "DeepSkyBlue", "DimGray", "DodgerBlue", "Feldspar",
                /**/
                "FireBrick", "FloralWhite", "ForestGreen", "Fuchsia", "Gainsboro", "GhostWhite",
                /**/
                "Gold", "GoldenRod", "Gray", "Green", "GreenYellow", "HoneyDew", "HotPink",
                /**/
                "IndianRed", "Indigo", "Ivory", "Khaki", "Lavender", "LavenderBlush", "LawnGreen",
                /**/
                "LemonChiffon", "LightBlue", "LightCoral", "LightCyan", "LightGoldenRodYellow", "LightGrey",
                /**/
                "LightGreen", "LightPink", "LightSalmon", "LightSeaGreen",
                /**/
                "LightSkyBlue", "LightSlateBlue", "LightSlateGray", "LightSteelBlue",
                /**/
                "LightYellow", "Lime", "LimeGreen", "Linen", "Magenta", "Maroon",
                /**/
                "MediumAquaMarine", "MediumBlue", "MediumOrchid", "MediumPurple",
                /**/
                "MediumSeaGreen", "MediumSlateBlue", "MediumSpringGreen", "MediumTurquoise",
                /**/
                "MediumVioletRed", "MidnightBlue", "MintCream", "MistyRose", "Moccasin", "NavajoWhite",
                /**/
                "Navy", "OldLace", "Olive", "OliveDrab", "Orange", "OrangeRed", "Orchid",
                /**/
                "PaleGoldenRod", "PaleGreen", "PaleTurquoise", "PaleVioletRed", "PapayaWhip",
                /**/
                "PeachPuff", "Peru", "Pink", "Plum", "PowderBlue", "Purple", "Red",
                /**/
                "RosyBrown", "RoyalBlue", "SaddleBrown", "Salmon", "SandyBrown",
                /**/
                "SeaGreen", "SeaShell", "Sienna", "Silver", "SkyBlue", "SlateBlue",
                /**/
                "SlateGray", "Snow", "SpringGreen", "SteelBlue", "Tan", "Teal",
                /**/
                "Thistle", "Tomato", "Turquoise", "Violet", "VioletRed",
                /**/
                "Wheat", "White", "WhiteSmoke", "Yellow", "YellowGreen"
            ];

            for (var i = 0; i < color_arr.length; i++) {
                $("#pointColor_select").append("<option class='col-md-12' value='" + color_arr[i] + "'>" + color_arr[i] + "</option>");
                $("#geneColor_select").append("<option class='col-md-12' value='" + color_arr[i] + "'>" + color_arr[i] + "</option>");
            }

            $("#GeneShapeStyle_select").html("");
            $("#GeneShapeStyle_select").append("<option class='col-md-12' value='circle'>circle</option>");
            $("#GeneShapeStyle_select").append("<option class='col-md-12' value='rect'>rect</option>");
            $("#GeneShapeStyle_select").append("<option class='col-md-12' value='roundRect'>roundRect</option>");
            $("#GeneShapeStyle_select").append("<option class='col-md-12' value='triangle'>triangle</option>");
            $("#GeneShapeStyle_select").append("<option class='col-md-12' value='dashed'>diamond</option>");
            $("#GeneShapeStyle_select").append("<option class='col-md-12' value='dotted'>pin</option>");
            $("#GeneShapeStyle_select").append("<option class='col-md-12' value='dotted'>arrow</option>");
        },

        draw:function (data, mountainStyle, highLightedGene, highlightedGeneStyle, is_reRender,qPosition) {
            var Xmax = -1000 ;
            var Xmin = 0;
            var Ymax = 0;
            var Ymin = 20;
            var armTo=Xmin+15000000;
            var series=[];
            for(var l =0 ;l < data.length; l++){
                for(var ll = 0;ll <data[l].length;ll++ ){
                    if(data[l][ll][0] < Xmin){
                        Xmin=data[l][ll][0];
                    }
                    if(data[l][ll][0] > Xmax){
                        Xmax=data[l][ll][0];
                    }
                    if(data[l][ll][1] < Ymin){
                        Ymin=data[l][ll][1];
                    }
                    if(data[l][ll][1] > Ymax){
                        Ymax=data[l][ll][1];
                    }
                }
            }

            //获取Y轴名称
            var yAxisName = "Values";
            //初始化群名称数组
            var glist_ex=[];
            for(var kk = 1;kk <= data.length; kk++){
                glist_ex.push(kk+"");
            }
            //页面中的Group,初始化蜂群选择器,填充选择框
            if(!is_reRender) {
                $("#mountain_which_select").html("");
                for(var i = 0; i < glist_ex.length; i++) {
                    $("#mountain_which_select").append("<option class='col-md-12' value='" + glist_ex[i] + "'>" + glist_ex[i] + "</option>")
                }
            }
            /**初始化图片下载配置*/
            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();
            //样式数组初始化
            if(mountainStyle.length == 0) {
                for(var i = 0; i < glist_ex.length; i++) {
                    //初始像素样式
                    var pointStyle = $("#pointStyle_select").val();
                    //js未知长度的二维数组
                    if(glist_ex.length == 1){
                        mountainStyle[0]=new Array();
                        mountainStyle[0][0] = '#1224fb';//颜色
                        mountainStyle[0][1] = 8;//大小
                        mountainStyle[0][2] = pointStyle;//形状
                    }else if(glist_ex.length == 2){
                        mountainStyle[0]=new Array();
                        mountainStyle[0][0] = '#1224fb';//颜色
                        mountainStyle[0][1] = 8;//大小
                        mountainStyle[0][2] = pointStyle;//形状
                        mountainStyle[1]=new Array();
                        mountainStyle[1][0] = '#27FE2E';//颜色
                        mountainStyle[1][1] = 8;//大小
                        mountainStyle[1][2] = pointStyle;//形状
                    }else {
                        for (var keke=0;keke<glist_ex.length;keke++){
                            mountainStyle[keke]=new Array();
                            mountainStyle[keke][0] = chromsome.Utils.getRandomColor();//颜色
                            mountainStyle[keke][1] = $("#pointSize_select").val();//大小
                            mountainStyle[keke][2] = pointStyle;//形状
                        }
                    }
                }
            }
            highlightedGeneStyle[0] = $("#geneColor_select").val();
            highlightedGeneStyle[1] = $("#GeneSizeStyle_select").val();
            highlightedGeneStyle[2] = $("#GeneShapeStyle_select").val();
            $("#btn_Rerender").click(function() {
                is_reRender = true;
                var myChartTwo=myChart.getOption();
                if($("#yLimitMin").val() !=""){
                    myChartTwo.yAxis[0].min = $("#yLimitMin").val()*1;
                }
                if($("#yLimitMax").val() !=""){
                    myChartTwo.yAxis[0].max=$("#yLimitMax").val()*1;
                }
                for (var i = 0; i <= myChartTwo.series.length - 1; i++){
                    if(myChartTwo.series[i].name == $("#mountain_which_select").val() ){
                        myChartTwo.series[i].itemStyle.normal.color=$("#pointColor_select").val();
                        myChartTwo.series[i].symbol=$("#pointStyle_select").val();
                        myChartTwo.series[i].symbolSize=$("#pointSize_select").val();
                    }
                    if(myChartTwo.series[i].name == 'Highlighted Gene' ){
                        myChartTwo.series[i].itemStyle.normal.color=$("#geneColor_select").val();
                        myChartTwo.series[i].symbol=$("#GeneShapeStyle_select").val();
                        myChartTwo.series[i].symbolSize=$("#GeneSizeStyle_select").val();
                    }
                }
                myChartTwo.toolbox[0].feature.saveAsImage.type = $("#saveType_select").val();
                myChartTwo.toolbox[0].feature.saveAsImage.pixelRatio = $("#pixelRatio_select").val();
                myChart.setOption(myChartTwo,true);
            });
            //准备高亮基因数据
            if(highLightedGene !=""){
                var highLightedGeneName = highLightedGene.split(",");
            }
            var _highLightedGeneName = [];
            if(highLightedGene != ""){
                for (var kkk = 0; kkk < highLightedGeneName.length; kkk++) {
                    for(var ws=0; ws<data.length; ws++){
                        for(var wsy=0; wsy < data[ws].length; wsy++){
                            if(data[ws][wsy][2]==highLightedGeneName[kkk].toUpperCase()){
                                _highLightedGeneName.push(data[ws][wsy]);
                            }
                        }
                    }
                }
            }
            //自定义排序算法
            function sequence(a,b){
                if (a>b) {
                    return 1;
                }else if(a<b){
                    return -1
                }else{
                    return 0;
                }
            }

            //准备cutoff数据
            var input_cutoff1 = $("#line1").val();
            var input_cutoff2 = $("#line2").val();
            var cutoff = [];
            if(input_cutoff1 != "") {
                cutoff.push(input_cutoff1*1);
                if(Ymin > Number(input_cutoff1)) { //计算y最小
                    Ymin = Number(input_cutoff1);
                }
                if(Ymax < Number(input_cutoff1)) { //计算y最大
                    Ymax = Number(input_cutoff1);
                }
            }
            if(input_cutoff2 != "") {
                cutoff.push(input_cutoff2*1);
                if(Ymin > Number(input_cutoff2)) { //计算y最小
                    Ymin = Number(input_cutoff2);
                }
                if(Ymax < Number(input_cutoff2)) { //计算y最大
                    Ymax = Number(input_cutoff2);
                }
            }
            cutoff=cutoff.sort(sequence);

            var cutoffTo=cutoff[0];

            for(var j=0;j < data.length;j++){
                var value_list = [];
                var cutoffall = [];
                cutoff.forEach(function(cc) {
                    cutoffall.push([cc, 0]);
                });
                for(var gg=0;gg<data[j].length;gg++){
                    if(cutoffall.length == 1) {
                        if(data[j][gg][1] < cutoff[0]) {
                            cutoffall[0][1]=cutoffall[0][1]+1;
                        }
                    } else if(cutoffall.length == 2) {
                        if(data[j][gg][1] < cutoff[0]) {
                            cutoffall[0][1]=cutoffall[0][1]+1;
                        } else if(data[j][gg][1] < cutoff[1]) {
                            cutoffall[1][1]=cutoffall[1][1]+1;
                        }
                    }
                    value_list.push(data[j][gg][1]);
                }
                //cutoff统计百分比
                if(cutoffall.length == 2){
                    cutoffall[0][1]=(Math.round(((cutoffall[0][1] / value_list.length)*10000)))/100;
                    cutoffall[1][1]=(Math.round((cutoffall[1][1] / value_list.length)*10000))/100;
                }

                if(cutoffall.length == 1){
                    cutoffall.forEach(function(cuu) {
                        cuu[1] =Math.round((cuu[1] / value_list.length)*10000);//计算个数的所占的百分比
                        cuu[1] =cuu[1]/100;
                    });
                }

                var lineData=fileUploadMountain.Utils.lineData(Ymin,Xmax,Xmin,qPosition,cutoffall);

                //cutoff百分比显示
                if(cutoffall.length == 1) {
                    var line1 = [{ //up
                        coord: [armTo, cutoffTo+0.2],
                        symbol: 'none',
                        symbolSize:15,
                        tooltip: {
                            formatter: 'cutoff'
                        },
                        label: {
                            normal: {
                                show:true,
                                position:'end',
                                formatter: ("" + (100 - cutoffall[0][1])).substring(0,5) + "%",
                                textStyle: {
                                    align: 'center',
                                    color: "#68b3ff"
                                }
                            }
                        }
                    }, {
                        coord: [armTo, cutoffTo+0.2],
                        symbol: 'none',
                        symbolSize:15
                    }];
                    var line2 = [{
                        coord: [armTo, cutoffTo+0.1],
                        symbol: 'none',
                        symbolSize:15,
                        tooltip: {
                            formatter: 'cutoff'
                        },
                        label: {
                            normal: {
                                show:true,
                                position:'end',
                                formatter: ("" + cutoffall[0][1] ).substring(0,5) + "%",
                                textStyle: {
                                    align: 'center',
                                    color: "#68b3ff"
                                }
                            }
                        }
                    }, {
                        coord: [armTo, cutoffTo+0.1],
                        symbol: 'none',
                        symbolSize:15
                    }];
                    lineData.push(line1);
                    lineData.push(line2);
                }
                if(cutoffall.length == 2) {
                    var line1 = [{ //up
                        coord: [armTo, cutoffTo+0.3],
                        symbol: 'none',
                        symbolSize:15,
                        tooltip: {
                            formatter: 'cutoff'
                        },
                        label: {
                            normal: {
                                show:true,
                                position:'end',
                                formatter: ("" + (Math.round((100 - cutoffall[0][1] - cutoffall[1][1])*100)/100)) + "%",
                                textStyle: {
                                    align: 'center',
                                    color: "#68b3ff"
                                }
                            }
                        }
                    }, {
                        coord: [armTo, cutoffTo+0.3],
                        symbol: 'none',
                        symbolSize:15
                    }];
                    var line2 = [{ //center
                        coord: [armTo, cutoffTo+0.2],
                        symbol: 'none',
                        symbolSize:15,
                        tooltip: {
                            formatter: 'cutoff'
                        },
                        label: {
                            normal: {
                                show:true,
                                position:'end',
                                formatter: ("" + cutoffall[1][1] ).substring(0,5) + "%",
                                textStyle: {
                                    align: 'center',
                                    color: "#68b3ff"
                                }
                            }
                        }
                    }, {
                        coord: [armTo, cutoffTo+0.2],
                        symbol: 'none',
                        symbolSize:15
                    }];
                    var line3 = [{ //down
                        coord: [armTo, cutoffTo+0.1],
                        symbol: 'none',
                        symbolSize:15,
                        tooltip: {
                            formatter: 'cutoff'
                        },
                        label: {
                            normal: {
                                show:true,
                                position:'end',
                                formatter: ("" + (cutoffall[0][1]) ).substring(0,5) + "%",
                                textStyle: {
                                    align: 'center',
                                    color: "#68b3ff"
                                }
                            }
                        }
                    }, {
                        coord: [armTo, cutoffTo+0.1],
                        symbol: 'none',
                        symbolSize:15
                    }];
                    lineData.push(line1);
                    lineData.push(line2);
                    lineData.push(line3);
                }

                var serie = {
                    name:glist_ex[j],
                    type: 'scatter',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data: data[j],
                    symbolSize:mountainStyle[j][1],
                    symbol:mountainStyle[j][2],
                    itemStyle: {
                        normal: {
                            color: mountainStyle[j][0]
                        }
                    },
                    markLine: {
                        lineStyle: {
                            normal: {
                                type: 'solid'
                            }
                        },
                        label: {
                            normal: {
                                show: false
                            }
                        },
                        data:lineData
                    }
                };
                series.push(serie);
                armTo=armTo+30000000;
            }

            var serie_h = {
                name:'Highlighted Gene',
                type: 'scatter',
                xAxisIndex: 0,
                yAxisIndex: 0,
                data: _highLightedGeneName,
                symbolSize:highlightedGeneStyle[1],
                symbol:highlightedGeneStyle[2],
                itemStyle: {
                    normal: {
                        color:highlightedGeneStyle[0]
                    }
                }
            };
            series.push(serie_h);

            var option = {
                backgroundColor: '#ffffff',
                grid: [{
                    x: '10%',
                    y: '25%',
                    width: '88%',
                    height: '60%'
                }],

                legend: {
                    type:'scroll',
                    top:'7%',
                    data:glist_ex,
                    padding: 3,
                    orient:'horizontal',
                    align:'left',
                    itemGap: 10,
                    textStyle: {
                        color: '#000',
                        fontSize: 16
                    }
                },
                toolbox: {
                    orient: "horizontal",
                    feature: {
                        mark: {
                            show: true,
                            title: "english"
                        },
                        dataZoom: {
                            show: true,
                            title: {
                                zoom: "Zoom in",
                                back: "Zoom out"
                            }
                        },
                        restore: {
                            show: true,
                            title: "Restore"
                        },
                        saveAsImage: {
                            show: true,
                            title: "Save as Image",
                            name: "image",
                            type: saveType,
                            pixelRatio: pixelRatio
                        }
                    }
                },
                dataZoom: [{
                    type: 'inside',
                    start: 0,
                    end: 100
                }, {
                    start: 0,
                    end: 100,
                    orient:"vertical",
                    handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                    handleSize: '80%',
                    handleStyle: {
                        color: '#fff',
                        shadowBlur: 1,
                        shadowColor: 'rgba(0, 0, 0, 0.6)',
                        shadowOffsetX: 0,
                        shadowOffsetY: 0
                    }
                }],
                tooltip: {
                    trigger:"item",
                    axisPointer: {
                        type: 'cross'
                    }
                },
                axisPointer: {
                    link: {xAxisIndex: 'all'},
                    label: {
                        backgroundColor: '#777'
                    }
                },
                xAxis: [{
                    gridIndex: 0,
                    min: Xmin,
                    max: Xmax+10000000
                }],
                yAxis: [{
                    gridIndex: 0,
                    name: yAxisName,
                    min: Math.floor(Ymin),
                    max: Math.ceil(Ymax)

                }],
                series: series
            };
            console.log(series.length);
            for (var i = 0; i <= series.length - 1; i++){
                console.log(series[i]);
            }

            var myChart = echarts.init(document.getElementById('echarts'));

            myChart.setOption(option);

            setTimeout(function (){
                window.onresize = function () {
                    myChart.resize();
                }
            },200);
            $("#bt_draw").removeAttr("disabled");
            $("#btn_Rerender").removeAttr("disabled");
        }
    }

});