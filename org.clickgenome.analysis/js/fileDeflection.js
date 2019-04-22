var fileUploadDeflection = new Object({
    Utils:{
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
        lineData:function (markLineData, deflectionStyle, cutoffall,_highLightedGeneName,highLightedGene,Xmax,Ymax,Ymin) {
            var colorFlag=[];
            for(var kk=0;kk<markLineData.length;kk++){
                for(var kkk=0;kkk<markLineData[kk].length;kkk++){
                    colorFlag.push(markLineData[kk][kkk][2]);
                }
            }
            var lineData = [];
            var lineData1 = [];
            var lineData2 = [];
            var linePlot1=[];
            var linePlot2=[];
            for(var sy = 0;sy < colorFlag.length; sy++){
                var wsy1=[];
                var wsy2=[];
                if(colorFlag[sy] == 1){
                    if(sy < markLineData[0].length){
                        wsy1.push((sy+1));
                        wsy1.push(markLineData[0][sy][0]);
                        wsy1.push(markLineData[0][sy][1]);
                    }else {
                        wsy1.push((sy+50));
                        wsy1.push(markLineData[1][sy-markLineData[0].length][0]);
                        wsy1.push(markLineData[1][sy-markLineData[0].length][1]);
                    }
                    linePlot1.push(wsy1);
                }else {
                    if(sy < markLineData[0].length){
                        wsy2.push((sy+1));
                        wsy2.push(markLineData[0][sy][0]);
                        wsy2.push(markLineData[0][sy][1]);
                    }else {
                        wsy2.push((sy+50));
                        wsy2.push(markLineData[1][sy-markLineData[0].length][0]);
                        wsy2.push(markLineData[1][sy-markLineData[0].length][1]);
                    }
                    linePlot2.push(wsy2);
                }
            }

            for(var ii=0;ii<linePlot1.length;ii++){
                var pvalue1=fileUploadDeflection.Utils.pToScience(linePlot1[ii][1]);

                lineData1.push([{
                    name:linePlot1[ii][2],
                    value:pvalue1,
                    coord: [linePlot1[ii][0], 0],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: deflectionStyle[0],
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
                        coord: [linePlot1[ii][0], linePlot1[ii][1]],
                        symbol: 'none'
                    }])
            }
            lineData.push(lineData1);
            for(var jj=0;jj<linePlot2.length;jj++){
                var pvalue2=fileUploadDeflection.Utils.pToScience(linePlot2[jj][1]);
                lineData2.push([{
                    name:linePlot2[jj][2],
                    value:pvalue2,
                    coord: [linePlot2[jj][0], 0],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: deflectionStyle[1],
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
                        coord: [linePlot2[jj][0], linePlot2[jj][1]],
                        symbol: 'none'
                    }])
            }

            if($("#line1").val() != ""){
                //实实在在的画cutoff的线
                //被选中，走的是方向，两条线
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
                    var pvalue3=fileUploadDeflection.Utils.pToScience(_highLightedGeneName[w][1]);
                    lineData2.push([{
                        name:_highLightedGeneName[w][2],
                        value:pvalue3,
                        coord: [_highLightedGeneName[w][0], 0],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: '#000000',
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
        draw:function (data ,deflectionStyle, is_reRender,highLightedGene) {
            var Ymax = 0;
            var Ymin = 0;
            var Xmax = data[0].length+data[1].length+50;
            var cutoff = [];
            var cutoffall = [];
            if($("#line1").val() != "") {
                var α=-10 * Math.log($("#line1").val()*1) / Math.log(10);
                α=(Math.round(α*10000))/10000;
                cutoff.push(α);
                cutoffall.push([cutoff[0], 0]);//P-arm
                cutoffall.push([cutoff[0], 0]);//Q-arm
                if(Ymin > α) { //计算y最小
                    Ymin = α;
                }
                if(Ymax < α) { //计算y最大
                    Ymax = α;
                }
            }
            for(var l = 0 ;l < data.length; l++){
                for(var ll = 0;ll <data[l].length;ll++ ){
                    var tmp = data[l][ll][0];
                    if(tmp < Ymin){
                        Ymin = tmp;
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
            if(!is_reRender){
                is_reRender = true;
                glist_ex.push('1','2');
            }
            /**初始化图片下载配置*/
            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();
            //样式数组初始化
            var color0 = '#ff4a4d';
            var color1 = '#3b92ff';
            if(deflectionStyle.length == 0) {
                deflectionStyle.push(color0, color1);
            }
            //用户定制
            $("#btn_Rerender").click(function() {
                var myChartTwo=myChart.getOption();
                if($("#yLimitMin").val() !=""){
                    myChartTwo.yAxis[0].min = $("#yLimitMin").val()*1;
                }
                if($("#yLimitMax").val() !=""){
                    myChartTwo.yAxis[0].max=$("#yLimitMax").val()*1;
                }
                myChartTwo.toolbox[0].feature.saveAsImage.type = $("#saveType_select").val();
                myChartTwo.toolbox[0].feature.saveAsImage.pixelRatio = $("#pixelRatio_select").val();
                // console.log(series[0].markLine.data[0][0].lineStyle.normal.color);
                if(myChartTwo.series[0].name == '1' ){
                    for(var jj=0; jj< series[0].markLine.data.length; jj++){
                        myChartTwo.series[0].markLine.data[jj][0].lineStyle.normal.color = $("#Color_select").val();
                    }
                    myChartTwo.series[0].itemStyle.normal.color=$("#Color_select").val();
                }
                if(myChartTwo.series[1].name == '2' ){
                    for(var jjj=0; jjj< series[1].markLine.data.length; jjj++){
                        myChartTwo.series[1].markLine.data[jjj][0].lineStyle.normal.color = $("#Color_select2").val();
                    }
                    myChartTwo.series[1].itemStyle.normal.color=$("#Color_select2").val();
                }
                myChart.setOption(myChartTwo,true);
            });

            var lineGroupData = fileUploadDeflection.Utils.lineData(data, deflectionStyle, cutoffall,_highLightedGeneName,highLightedGene,Xmax,Ymax,Ymin);
            var series = [];
            for(var w = 0; w < 2; w++){
                series.push({
                    name: glist_ex[w],
                    type: 'scatter',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data:[],
                    symbol:"none",
                    itemStyle: {
                        normal: {
                            color: deflectionStyle[w]
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
                    max: Xmax
                }],
                yAxis: [{
                    gridIndex: 0,
                    name: yAxisName,
                    min: Math.floor(Ymin),
                    max: Math.ceil(Ymax)
                }],
                series: series
            };
            var myChart = echarts.init(document.getElementById('echarts'));
            myChart.setOption(option);
            $("#bt_draw").removeAttr("disabled");
            $("#btn_Rerender").removeAttr("disabled");
        }
    }
});