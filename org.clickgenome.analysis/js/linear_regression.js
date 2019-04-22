var linear = new Object({
    URL: {
        BASE_URL: function () {
            return "http://54.193.40.253:8080/data/data/";
            // return "http://localhost:8088/data/data/";
        },
        CHECK_URL: function(geneName) {
            return linear.URL.BASE_URL() + geneName+ "/" + $("#newOrOld").val() + "/geneLinearCheck";
        },
        LINEAR: function (cancerType, geneName, dataType, dataType2, sampleType) {
            var value1;
            var value2;
            if ($("#isLogOne").is(':checked')) {
                value1 = "l";
            } else {
                value1 = "y";
            }
            if ($("#isLogTwo").is(':checked')) {
                value2 = "l";
            } else {
                value2 = "y";
            }
            var url;
                url = linear.URL.BASE_URL() + cancerType + "/" + geneName + "/" +sampleType + "/" + dataType +"/" + value2 +"/" + dataType2 + "/" + $("#newOrOld").val() + "/" +value1 +"/linearPoint";
            return url;
        }
    },
    Operate: {
        getLinearData: function (cancerType,geneName,dataType,dataType2,sampleType) {
            if (linear.Utils.VerifyBothSetsOfData(dataType,dataType2)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check DataType y Axia and DataType x Axis!.</p>');
            }else if (linear.Utils.VerifyNormal(cancerType, sampleType)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check cancer type and sample type!.</br>When the data type is mRNA expression, such cancer types as "LAML", "ACC", "LGG", "DLBC", "MESO", "OV", "TGCT", "UCS" and "UVM" don\'t have samples of Non-malignant type.</p>');
            }else if(geneName == ""){
                $("#bt_draw").disabled=false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please input gene symbol.<br />Gene (For example: EGFR). </p>');
            }else {
                $("#echarts").html('<div class="spinner">' +
                    '<div class="rect1"></div>' +
                    '<div class="rect2"></div>' +
                    '<div class="rect3"></div>' +
                    '<div class="rect4"></div>' +
                    '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>Due to the big data size and the internet speed, this may take a while.</p>');
                $("#saveName_input").val("Linear-"+cancerType+"-"+geneName);
                var url = linear.URL.LINEAR(cancerType, geneName, dataType, dataType2, sampleType);
                var linearStyle=new Array();
                var is_reRender=false;
                //获取点数据
                var point = [];
                var pValue;
                var piersen;
                var slope;
                var intercept;
                $.get(linear.URL.CHECK_URL(geneName), {}, function(data) {
                    if(data.data) {
                        $("#echarts").html('<div class="spinner">' +
                            '<div class="rect1"></div>' +
                            '<div class="rect2"></div>' +
                            '<div class="rect3"></div>' +
                            '<div class="rect4"></div>' +
                            '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>Due to the big data size and the internet speed, this may take a while.</p>');
                        $.get(url, {}, function (result, status) {
                            if (status) {
                                if (result == null) {
                                    alert("error null");
                                } else {
                                    var resultData = result.data;
                                    for(var wsy=0;wsy<resultData.length-2;wsy++){
                                        point.push([resultData[wsy].y1,resultData[wsy].y2,resultData[wsy].sampleId])
                                    }
                                    pValue=resultData[(resultData.length-2)].y1;
                                    piersen=linear.Utils.getResult(resultData[(resultData.length-2)].y2,4);
                                    slope=linear.Utils.getResult(resultData[(resultData.length-1)].y1,4);
                                    intercept=linear.Utils.getResult(resultData[(resultData.length-1)].y2,4);
                                    linear.View.draw(point, linearStyle, is_reRender, pValue, piersen, slope, intercept);
                                }
                            }
                        });
                    }else {
                        $("#bt_draw").removeAttr("disabled");
                        $("#echarts").html('<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌Please make sure the mRNA expression value and the copy number are both available!.</p>');
                    }})
            }
        }
    },
    Utils:{
        //验证两组数据是否相同
        VerifyBothSetsOfData:function(dataType, dataType2){
            if(dataType == dataType2){
                return true;
            }
            return false;
        },
        VerifyNormal:function(cancerType, sampleType){
                if("n"==sampleType){
                    var array = ["laml","acc","lgg","dlbc","meso","ov","tgct","ucs","uvm"];
                    var indexOf = array.indexOf(cancerType);
                    if(indexOf!=-1){
                        return true;
                    }
                }
            return false;
        },
        //定义一个求四舍五入到num后面的n位的函数
        getResult:function(value,n){
            return Math.round(value*Math.pow(10,n))/Math.pow(10,n);
        },

        //定义一个求平均数的函数
        getMean:function (data) {
            var meanValue=[];
            var x=0;
            var y=0;
            for(var wsy=0;wsy < data.length;wsy++){
                x+=data[wsy][0];
                y+=data[wsy][1];
            }
            meanValue[0]=x/data.length;
            meanValue[1]=y/data.length;
            return meanValue;
        },
        //定义一个求标准差的函数
        getVar:function (data,mean) {
            var x=0;
            var y=0;
            var varValue=[];
            for(var wsy=0;wsy<data.length;wsy++){

                x += (data[wsy][0]-mean[0])*(data[wsy][0]-mean[0]);
                y += (data[wsy][1]-mean[1])*(data[wsy][1]-mean[1]);

            }

            varValue[0]=Math.sqrt(x/(data.length-1));
            varValue[1]=Math.sqrt(y/(data.length-1));

            return varValue;
        },
        //定义一个求相关系数的函数
        //计算相关系数r Correlation
        //r=1/(N-1)Σ[(x-ux)/σx*(y-uy)/σy]  u为平均值
        getR:function (data,mean,varValue) {
            var r;
            var temp=0;

            for(var wsy=0;wsy<data.length;wsy++){

                var xTemp=data[wsy][0]-mean[0];

                var yTemp=data[wsy][1]-mean[1];

                temp += xTemp/varValue[0]*yTemp/varValue[1];
            }

            r=temp/(data.length-1);
            return r;
        },
        //定义一个求坐标最大值最小值的函数
        getValue:function (data) {

            var value=[];
            var xMin=10;
            var xMax=-10;
            var yMin=5;
            var yMax=-10;

            for(var wsy=0;wsy<data.length;wsy++){

                if(data[wsy][0]<xMin){//找x最小值
                    xMin=data[wsy][0];
                }
                if(data[wsy][1]<yMin){//找y最小值
                    yMin=data[wsy][1];
                }

                if(data[wsy][0]>xMax){//找x最大值
                    xMax=data[wsy][0];
                }
                if(data[wsy][1]>yMax){//找y最大值
                    yMax=data[wsy][1];
                }
            }

            value.push(xMin,xMax,yMin,yMax);

            return value;
        },
        lineData:function (linePoint, lineColor, lineStyle, lineName,r) {
            var lineData=[];
            lineData.push([{
                name:lineName,
                value:'Correlation coefficient ='+r,
                coord:[linePoint[0],linePoint[1]],
                symbol: 'none',
                lineStyle: {
                    normal: {
                        color: lineColor,
                        width: 1,
                        type: lineStyle
                    }
                },
                label:{
                    normal:{
                        show:false
                    }
                }
            },{
                coord:[linePoint[2],linePoint[3]],
                symbol: 'none'
            }]);

            return lineData;
        }
    },
    View: {
        init: function () {
            /**初始化配置项ui*/
            /**/
            $("#pixelRatio_select").html("");
            /**/
            for (var i = 1; i < 50; i++) {
                /**/
                $("#pixelRatio_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
                /**/
            }
            /**/
            $("#saveType_select").html("");
            /**/
            $("#saveType_select").append("<option class='col-md-12' value='png'>png</option>");
            /**/
            $("#saveType_select").append("<option class='col-md-12' value='jpeg'>jpeg</option>");
            /**/
            /**/
            $("#pointStyle_select").html("");
            /**/
            $("#pointStyle_select").append("<option class='col-md-12' value='circle'>circle</option>");
            /**/
            $("#pointStyle_select").append("<option class='col-md-12' value='rect'>rect</option>");
            /**/
            $("#pointStyle_select").append("<option class='col-md-12' value='roundRect'>roundRect</option>");
            /**/
            $("#pointStyle_select").append("<option class='col-md-12' value='triangle'>triangle</option>");
            /**/
            $("#pointStyle_select").append("<option class='col-md-12' value='diamond'>diamond</option>");
            /**/
            $("#pointStyle_select").append("<option class='col-md-12' value='pin'>pin</option>");
            /**/
            $("#pointStyle_select").append("<option class='col-md-12' value='arrow'>arrow</option>");
            /**/
            /**/
            $("#pointSize_select").html("");
            /**/
            $("#pointSize_select").append("<option class='col-md-12' value='5'>5</option>");
            /**/
            for (var i = 1; i < 20; i++) {
                /**/
                $("#pointSize_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
                /**/
            }
            /**/
            $("#pointColor_select").html("");
            /**/
            $("#lineColor_select").html("");
            /**/
            $("#lineStyle_select").html("");

            $("#lineWidth_select").html("");

            for(var y = 1; y <= 10; y++) {
                $("#lineWidth_select").append("<option class='col-md-12' value='" + y + "'>" + y + "</option>");
            }
            /**/
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
            /**/
            for (var i = 0; i < color_arr.length; i++) {
                /**/
                $("#pointColor_select").append("<option class='col-md-12' value='" + color_arr[i] + "'>" + color_arr[i] + "</option>");
                /**/
                /**/
                $("#boxColor_select").append("<option class='col-md-12' value='" + color_arr[i] + "'>" + color_arr[i] + "</option>");
                /**/
                /**/
                $("#lineColor_select").append("<option class='col-md-12' value='" + color_arr[i] + "'>" + color_arr[i] + "</option>");
                /**/
                /**/
            }
            /**/
            $("#lineStyle_select").append("<option class='col-md-12' value='solid'>solid</option>");
            /**/
            $("#lineStyle_select").append("<option class='col-md-12' value='dashed'>dashed</option>");
            /**/
            $("#lineStyle_select").append("<option class='col-md-12' value='dotted'>dotted</option>");
            /**/
            var cancer_arr = [
                "⚠️Acute Myeloid Leukemia [LAML]",
                "⚠️Adrenocortical carcinoma [ACC]",
                "Bladder Urothelial Carcinoma [BLCA]",
                "⚠️Brain Lower Grade Glioma [LGG]",
                "Breast invasive carcinoma [BRCA]",
                "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESC]",
                "Cholangiocarcinoma [CHOL]",
                "Colon adenocarcinoma [COAD]",
                "Esophageal carcinoma [ESCA]",
                "Glioblastoma multiforme [GBM]",
                "Head and Neck squamous cell carcinoma [HNSC]",
                "Kidney Chromophobe [KICH]",
                "Kidney renal clear cell carcinoma [KIRC]",
                "Kidney renal papillary cell carcinoma [KIRP]",
                "Liver hepatocellular carcinoma [LIHC]",
                "Lung adenocarcinoma [LUAD]",
                "Lung squamous cell carcinoma [LUSC]",
                "⚠️Lymphoid Neoplasm Diffuse Large B-cell Lymphoma [DLBC]",
                "⚠️Mesothelioma [MESO]",
                "⚠️Ovarian serous cystadenocarcinoma [OV]",
                "Pancreatic adenocarcinoma [PAAD]",
                "Pheochromocytoma and Paraganglioma [PCPG]",
                "Prostate adenocarcinoma [PRAD]",
                "Rectum adenocarcinoma [READ]",
                "Sarcoma [SARC]",
                "Skin Cutaneous Melanoma [SKCM]",
                "Stomach adenocarcinoma [STAD]",
                "⚠️Testicular Germ Cell Tumors [TGCT]",
                "Thymoma [THYM]",
                "Thyroid carcinoma [THCA]",
                "⚠️Uterine Carcinosarcoma [UCS]",
                "Uterine Corpus Endometrial Carcinoma [UCEC]",
                "⚠️Uveal Melanoma [UVM]"
            ];
            /**/ //dlbc acc laml meso ov tgct ucs uvm无正常样本
            /**/
            $("#cancername").html("");
            /**/
            for (var i = 0; i < cancer_arr.length; i++) {
                /**/
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                /**/
                $("#cancername").append("<option class='col-md-12' value='" + cancer_arr[i].match(r)[1].toLowerCase() + "'>" + cancer_arr[i] + "</option>");
                /**/
            }
            /**结束*/

        },

        draw: function (point, linearStyle, is_reRender, pValue, piersen, slope, intercept) {
            //求坐标的最大最小值
            var value=linear.Utils.getValue(point);

            var lineName="";

            if(intercept < 0){
                lineName= "Y="+slope+"X"+intercept;
            }else {
                lineName= "Y="+slope+"X"+"+"+intercept
            }

            //求回归直线的最左边的点
            var yLeft=slope*value[0]+intercept;

            if(yLeft < value[2]){
                value[2]=yLeft;
            }

            if(yLeft > value[3]){
                value[3]=yLeft;
            }
            //求回归直线的最右边的点
            var yRight=slope*value[1]+intercept;

            if(yRight <value[2]){
                value[2]=yRight;
            }

            if(yRight > value[3]){
                value[3]=yRight;
            }

            var linePoint=[];

            linePoint.push(value[0],yLeft,value[1],yRight);

            //获取坐标轴名称
            var xName;
            var yName;
            if($("#dataType").val()=="e"){
                xName='Expression value of genes';
                yName='Copy number of genes';
            }else {
                xName='Copy number of genes';
                yName='Expression value of genes';
            }

            //构造样式配置数组
            var pointColor= $("#pointColor_select").val();//颜色
            var pointSize = $("#pointSize_select").val();//点的大小
            var pointStyle = $("#pointStyle_select").val();//点的样式
            var lineColor = $("#lineColor_select").val();//线的颜色
            var lineStyle = $("#lineStyle_select").val();//线的样式

            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();

            //监听重新渲染按钮重新渲染
            $("#btn_Rerender").click(function() {
                is_reRender = true;

                var myChartTwo=myChart.getOption();

                myChartTwo.toolbox[0].feature.saveAsImage.type=$("#saveType_select").val();
                myChartTwo.toolbox[0].feature.pixelRatio=$("#pixelRatio_select").val();


                // for(var key in myChartTwo){
                //                     console.log(key);
                //                     console.log(myChartTwo[key]);
                //                 }


                if($("#yLimitMin").val() !=""){
                    myChartTwo.yAxis[0].min = $("#yLimitMin").val()*1;
                }

                if($("#yLimitMax").val() !=""){
                    myChartTwo.yAxis[0].max=$("#yLimitMax").val()*1;
                }

                for (var i = 0; i < myChartTwo.series.length; i++){

                        for(var wsy=0; wsy < myChartTwo.series[i].markLine.data.length; wsy++){
                            myChartTwo.series[i].markLine.data[wsy][0].lineStyle.normal.color=$("#lineColor_select").val();
                            myChartTwo.series[i].markLine.data[wsy][0].lineStyle.normal.width=$("#lineWidth_select").val()*1;
                            myChartTwo.series[i].markLine.data[wsy][0].lineStyle.normal.type=$("#lineStyle_select").val();//线的样式
                        }

                        myChartTwo.series[i].itemStyle.normal.color=$("#pointColor_select").val();//点的颜色
                        myChartTwo.series[i].symbol=$("#pointStyle_select").val();//点的样式
                        myChartTwo.series[i].symbolSize=$("#pointSize_select").val();//点的大小

                }

                myChartTwo.toolbox[0].feature.saveAsImage.type = $("#saveType_select").val();
                myChartTwo.toolbox[0].feature.saveAsImage.pixelRatio = $("#pixelRatio_select").val();


                myChart.setOption(myChartTwo,true);

            });
            var lineData=linear.Utils.lineData(linePoint,lineColor,lineStyle,lineName,piersen);
            var series=[];

            series.push({
                name: 'Linear regression',
                type: 'scatter',
                data: point,
                symbol:pointStyle,   //点形状
                symbolSize:pointSize,   //点大小
                itemStyle:{
                    normal:{
                        color:pointColor  //点颜色
                    }
                },
                markLine:{
                    data: lineData
                }
            });

            var title = "PValue is  " + pValue.toExponential(4) +"   "+lineName+"    Correlation coefficient  "+piersen;
            var option = {
                backgroundColor: '#ffffff',
                title: {
                    text: title,
                    left:5,
                    top:5,
                    textStyle: {
                        color:'#000000',
                        fontStyle:'normal',
                        fontWeight:'bold',
                        fontFamily:'Microsoft YaHei',
                        fontSize:18
                    }
                },
                grid: [{
                    x: '10%',
                    y: '10%',
                    width: '88%',
                    height: '80%'
                }],
                tooltip: {
                    trigger:"item",
                    formatter: "{b}&nbsp;&nbsp{c}&nbsp;&nbsp;"
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
                        },
                        dataView:{
                            show:true,
                            title:"Data table",
                            readOnly: true,
                            lang:["Data table","Close"],
                            buttonColor:'#748aff',
                            optionToContent:function(opt) {
                                //console.log('opt'+opt);
                                // var series = opt.series;
                                //在此处把四分位数和均值的函数封装好，把该算的东西用数组封装好。
                                //重新开始
                                var table = '<div style="width: 100%;height: 100%;overflow: auto;user-select: all;">' +
                                    '<table width="100%" border="1" cellspacing="0" cellpadding="0"><tbody>';
                                    if($("#dataType").val()=="e"){
                                        table += '<tr><td colspan="2" align="center" >EXP</td><td colspan="2" align="center" >CNV</td><td colspan="2" align="center" >SampleId</td></tr>';
                                    }else {
                                        table += '<tr><td colspan="2" align="center" >CNV</td><td colspan="2" align="center" >EXP</td><td colspan="2" align="center" >SampleId</td></tr>';
                                    }
                                for(var wy=0;wy < point.length;wy++){

                                    table += '<tr>'
                                        + '<td colspan="2" align="center">' + point[wy][0] + '</td>'
                                        + '<td colspan="2" align="center">' + point[wy][1] + '</td>'
                                        + '<td colspan="2" align="center">' + point[wy][2] + '</td>'+'</tr>';
                                }
                                table +='</tbody></table></div>';
                                return table;
                            }
                        }
                    }
                },

                xAxis: [
                    {   name:xName,
                        gridIndex: 0,
                        min:Math.floor(value[0]),
                        max:Math.ceil(value[1]),
                        nameLocation:'middle',
                        nameGap:25,
                        nameTextStyle:{
                            frontSize:30
                        }
                    }
                ],

                yAxis: [
                    {   name:yName,
                        gridIndex: 0,
                        min:Math.floor(value[2]),
                        max:Math.ceil(value[3]),
                        nameLocation:'middle',
                        nameGap:40,
                        nameTextStyle:{
                            frontSize:30
                        }
                    }
                ],
                axisPointer: {
                    link: {xAxisIndex: 'all'},
                    label: {
                        backgroundColor: '#777'
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
                series: series
            };

            // for (var y = 0; y < series.length; y++){
            //     console.log(series[y]);
            // }

            var myChart = echarts.init(document.getElementById('echarts'));
            myChart.setOption(option);


            setTimeout(function (){
                window.onresize = function () {
                    myChart.resize();
                }
            },200);


            $("#bt_draw").removeAttr("disabled");
        }
    }
});
