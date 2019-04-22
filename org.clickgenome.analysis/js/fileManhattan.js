var fileUploadManhattan = new Object({

    Utils:{
        //在群名称中查找target
        search: function(src, target) {
            for(var i = 0; i < src.length; i++) {
                if(src[i] == target) {
                    return i;
                }
            }
        },
        //js的log函数
        log:function (value,base) {

            return Math.log(value) / Math.log(base);

        },

        //科学计数法
        pToScience:function(value){

            var pvalue=Math.abs(value);
            pvalue = Math.pow(10,-1*pvalue/10);
            pvalue=pvalue.toExponential(2);
            return pvalue;
        },
        //准备线数据
        lineData:function (markLineData, manhattanStyle, cutoffall, _highLightedGeneName, highLightedGene, Xmax, Ymax, Ymix) {

            var lineData=[];

            var lineData1=[];

            var lineData2=[];

            for(var ii=0; ii < markLineData[0].length; ii++){

                var pvalue1 = fileUploadManhattan.Utils.pToScience(markLineData[0][ii][0]);

                lineData1.push([{

                    name:markLineData[0][ii][1],

                    value:pvalue1,

                    coord: [ii+1, 0],

                    symbol: 'none',

                    lineStyle: {
                        normal: {
                            color: manhattanStyle[0][0],
                            width: 1,
                            type: 'solid'
                        }
                    },

                    label:{
                        normal:{
                            show:false
                        }
                    }

                },
                    {
                        coord: [ii+1, markLineData[0][ii][0]],

                        symbol: 'none'
                    }])
            }

            lineData.push(lineData1);

            for(var jj = 0;jj < markLineData[1].length;jj++){

                var pvalue2 = fileUploadManhattan.Utils.pToScience(markLineData[1][jj][0]);

                lineData2.push([{

                    name:markLineData[1][jj][1],

                    value:pvalue2,

                    coord: [markLineData[0].length+50+jj, 0],

                    symbol: 'none',

                    lineStyle: {

                        normal: {

                            color: manhattanStyle[1][0],

                            width: 1,

                            type: 'solid'

                        }

                    },
                    label:{

                        normal:{

                            show:false

                        }
                    }
                },
                    {
                        coord: [markLineData[0].length+50+jj, markLineData[1][jj][0]],

                        symbol: 'none'
                    }])
            }

            if($("#line1").val() != ""){
                //实实在在的画cutoff的线
                if($("#isAbsolute").is(':checked')==false){//没有被选中，走的是绝对值，一条线
                    var line = [{
                        name:"cutoff",
                        value: cutoffall[0][0],
                        coord: [0, cutoffall[0][0]],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: '#000',
                                width: 1,
                                type: 'solid'
                            }
                        },
                        label:{
                            normal:{
                                show:false
                            }
                        }
                    },
                        {
                            coord: [Xmax, cutoffall[0][0]],
                            symbol: 'none'
                        }];
                    lineData2.push(line);

                }else{//被选中，走的是方向，两条线
                    var line1 = [{
                        name:"cutoff",
                        value: cutoffall[0][0],
                        coord: [0, cutoffall[0][0]],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: '#000',
                                width: 1,
                                type: 'solid'
                            }
                        },
                        label:{
                            normal:{
                                show:false
                            }
                        }
                    },
                        {
                            coord: [Xmax, cutoffall[0][0]],
                            symbol: 'none'
                        }];
                    lineData2.push(line1);

                    var line2 = [{
                        name:"cutoff",
                        value: -1*cutoffall[0][0],
                        coord: [0, -1*cutoffall[0][0]],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: '#000',
                                width: 1,
                                type: 'solid'
                            }
                        },
                        label:{
                            normal:{
                                show:false
                            }
                        }
                    },
                        {
                            coord: [Xmax, -1*cutoffall[0][0]],
                            symbol: 'none'
                        }];
                    lineData2.push(line2);


                }

                var cutoffShape1 = [{
                    name:"P-arm",
                    value: ("" + cutoffall[0][1]) + "%",
                    coord: [markLineData[0].length-40, cutoffall[0][1]-3],
                    symbol: 'none',
                    label: {
                        normal: {
                            formatter: ("" + cutoffall[0][1]) + "%",
                            textStyle: {
                                align: 'center',
                                color: "#000000"
                            }
                        }
                    }
                }, {
                    coord: [markLineData[0].length-40, cutoffall[0][1]-3],
                    symbol: 'none'
                }];
                lineData2.push(cutoffShape1);

                var cutoffShape2= [{
                    name:"Q-arm",
                    value: ("" + cutoffall[1][1] ) + "%",
                    coord: [markLineData[0].length+500, cutoffall[0][1]-3],
                    symbol: 'none',
                    label: {
                        normal: {
                            formatter: ("" + cutoffall[1][1] ) + "%",
                            textStyle: {
                                align: 'center',
                                color: "#000000"
                            }
                        }
                    }
                }, {
                    coord: [markLineData[0].length+500, cutoffall[0][1]-3 ],
                    symbol: 'none'
                }];
                lineData2.push(cutoffShape2);
            }

            if(highLightedGene !=""){

                for(var w=0;w<_highLightedGeneName.length;w++){
                    var pvalue3=fileUploadManhattan.Utils.pToScience(_highLightedGeneName[w][1]);
                    lineData2.push([{
                        name:_highLightedGeneName[w][2],
                        value:pvalue3,
                        coord: [_highLightedGeneName[w][0], 0],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: '#ff663a',
                                width: 1,
                                type: 'solid'
                            }
                        },
                        label:{
                            normal:{
                                show:false
                            }
                        }
                    },
                        {
                            coord: [_highLightedGeneName[w][0], _highLightedGeneName[w][1]],
                            symbol: 'none'
                        }])

                }

            }
            lineData.push(lineData2);

            return lineData;
        }
    },

    view:{

        init:function () {

            $("#pixelRatio_select").html("");

            for(var i = 1; i < 50; i++) {

                $("#pixelRatio_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");

            }

            $("#saveType_select").html("");

            $("#saveType_select").append("<option class='col-md-12' value='png'>png</option>");

            $("#saveType_select").append("<option class='col-md-12' value='jpeg'>jpeg</option>");
        },

        draw:function (data ,manhattanStyle, is_reRender,highLightedGene) {

            //订制坐标轴坐标的最大值和最小值
            var Ymax = 0;
            var Ymix = 0;
            var Xmax = data[0].length+data[1].length+50;


            var cutoff = [];
            var cutoffall = [];
            if($("#line1").val() != "") {
                var α=-10 * Math.log($("#line1").val()*1) / Math.log(10);
                α=(Math.round(α*10000))/10000;
                cutoff.push(α);
                cutoffall.push([cutoff[0], 0]);//P-arm
                cutoffall.push([cutoff[0], 0]);//Q-arm
                if(Ymix > α) { //计算y最小
                    Ymix = α;
                }
                if(Ymax < α) { //计算y最大
                    Ymax = α;
                }
            }


            for(var l = 0 ;l < data.length; l++){

                for(var ll = 0;ll <data[l].length;ll++ ){

                    var tmp = data[l][ll][0];

                    if(tmp < Ymix){

                        Ymix = tmp;
                    }

                    if(tmp > Ymax){

                        Ymax = tmp;

                    }
                    if(l==0){//第0组

                        if($("#line1").val() != "") {

                            if (Math.abs(data[l][ll][0]) >= cutoff[0]) {

                                cutoffall[0][1]=cutoffall[0][1]+1;

                            }
                        }


                    }else {//第1组

                        if($("#line1").val() != "") {

                            if (tmp >= cutoff[0]) {

                                cutoffall[1][1]=cutoffall[1][1]+1;

                            }
                        }
                    }

                }

            }

            if($("#line1").val() != ""){
                cutoffall[0][1] = (Math.round((cutoffall[0][1] / data[0].length)*10000))/100;
                cutoffall[1][1] = (Math.round((cutoffall[1][1] / data[1].length)*10000))/100;
            }


            //准备高亮基因数据
            if(highLightedGene !=""){
                var highLightedGeneName = highLightedGene.split(",");
            }

            var _highLightedGeneName = [];

            if(highLightedGene != 0){

                for(var kaka=0;kaka<data.length;kaka++){
                    for(var ka = 0;ka <data[kaka].length;ka++ ){
                        for (var lll = 0; lll < highLightedGeneName.length; lll++){

                            //字符串相等比较
                            if (highLightedGeneName[lll].indexOf(data[kaka][ka][1]) ==0 && data[kaka][ka][1].indexOf(highLightedGeneName[lll]) ==0) {
                                var bb=[];
                                if(kaka==0){
                                    bb.push((ka+1));//基因所在的位置

                                }else {
                                    bb.push((data[0].length + 50 + ka));
                                }
                                bb.push();
                                bb.push(data[kaka][ka][0]);//pValue
                                bb.push(data[kaka][ka][1]);//geneName
                                _highLightedGeneName.push(bb);
                            }
                        }
                    }
                }
            }

            //获取Y轴名称
            var yAxisName = "-10log10(q-value)";

            //初始化群名称数组
            var glist_ex = [];

                glist_ex.push('P-Arm','Q-Arm');

            //页面中的Group,初始化蜂群选择器,填充选择框
            if(!is_reRender) {

                $("#manhattan_which_select").html("");

                for(var i = 0; i < glist_ex.length; i++) {

                    $("#manhattan_which_select").append("<option class='col-md-12' value='" + glist_ex[i] + "'>" + glist_ex[i] + "</option>")

                }
            }

            /**初始化图片下载配置*/
            var saveType = $("#saveType_select").val();

            var pixelRatio = $("#pixelRatio_select").val();

            //样式数组初始化
            if(manhattanStyle.length == 0) {

                manhattanStyle[0]=[];

                manhattanStyle[1]=[];

                manhattanStyle[0][0]='#e09e41';

                manhattanStyle[1][0]='#29fd2f';

            }

            $("#btn_Rerender").click(function() {

                var myChartTwo=myChart.getOption();

                if($("#yLimitMin").val() !=""){

                    myChartTwo.yAxis[0].min = $("#yLimitMin").val()*1;

                }

                if($("#yLimitMax").val() !=""){

                    myChartTwo.yAxis[0].max=$("#yLimitMax").val()*1;

                }

                if(myChartTwo.series[0].name == $("#manhattan_which_select").val() ){

                    for(var wsy=0; wsy < data[0].length; wsy++){

                        myChartTwo.series[0].markLine.data[wsy][0].lineStyle.normal.color=$("#Color_select").val();

                    }

                    myChartTwo.series[0].itemStyle.normal.color=$("#Color_select").val();
                }

                if(myChartTwo.series[1].name == $("#manhattan_which_select").val() ){

                    for(var wsy1=0; wsy1 < data[1].length; wsy1++){

                        myChartTwo.series[1].markLine.data[wsy1][0].lineStyle.normal.color=$("#Color_select").val();

                    }

                    myChartTwo.series[1].itemStyle.normal.color=$("#Color_select").val();
                }


                myChartTwo.toolbox[0].feature.saveAsImage.type = $("#saveType_select").val();

                myChartTwo.toolbox[0].feature.saveAsImage.pixelRatio = $("#pixelRatio_select").val();

                myChart.setOption(myChartTwo,true);

            });



            var lineGroupData =fileUploadManhattan.Utils.lineData(data, manhattanStyle,cutoffall,_highLightedGeneName,highLightedGene,Xmax,Ymax,Ymix);



            var series = [];

            for(var w = 0;w < 2;w++){

                series.push({
                    name: glist_ex[w],
                    type: 'scatter',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data: [],
                    symbol:"none",
                    itemStyle: {
                        normal: {
                            color: manhattanStyle[w][0]
                        }
                    },
                    markLine: {
                        lineStyle: {
                            normal: {
                                type: 'solid'
                            }
                        },
                        data: lineGroupData[w]
                    }
                });
            }

            var option = {

                grid: [{
                    x: '10%',
                    y: '25%',
                    width: '88%',
                    height: '60%'
                }],

                legend: {
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
                        saveAsImage: {
                            show: true,
                            title: "Save as Image",
                            name: "image",
                            type: saveType,
                            pixelRatio: pixelRatio
                        }
                    }
                },
                tooltip: {
                    trigger:"item",
                    formatter: "{b} &nbsp;&nbsp; {c}&nbsp;&nbsp;"
                },
                axisPointer: {
                    link: {xAxisIndex: 'all'},
                    label: {
                        backgroundColor: '#777'
                    }
                },
                xAxis: [{
                    gridIndex: 0,
                    min: 0,
                    max: Xmax+1
                }],
                yAxis: [{
                    gridIndex: 0,
                    name: yAxisName,
                    min: Math.floor(Ymix),
                    max: Math.ceil(Ymax)

                }],
                series: series
            };

            for (var j = 0; j <= series.length - 1; j++){
                console.log(series[j]);
            }

            var myChart = echarts.init(document.getElementById('echarts'));

            myChart.setOption(option);

            $("#btn_Rerender").removeAttr("disabled");
            $("#bt_draw").removeAttr("disabled");

        }

    }

});