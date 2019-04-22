var volcano = new Object({
    URL: {
        BASE_URL: function () {
            // return "http://localhost:8088/data/data/";
            return "http://54.193.40.253:8080/data/data/";
        },
        VOLCANO: function (cancerType1, cancerType2, normal1, normal2, dataType) {
            var value;
            if ($("#islog").is(':checked')) {
                value = "l";
            } else {
                value = "y";
            }
            var url = volcano.URL.BASE_URL() + cancerType1 + "/" +cancerType2 + "/"+normal1 + "/"+normal2 + "/"+ dataType + "/" + $("#newOrOld").val() +"/"+value+"/volcano";
            return url;
        }
    },
    Operate: {
        getVolcanoData: function (cancerType1, cancerType2, normal1, normal2, dataType, highLightedGene) {
            if (cancerType1 =="" && cancerType2 =="") {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select a cancer type1 and a cancer type2 respectively.</p>');
            } else if (cancerType1 =="" || cancerType2 =="") {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select a cancer type.</p>');
            } else if (volcano.Utils.VerifyBothSetsOfData(cancerType1, cancerType2, normal1, normal2)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please make sure to select two DIFFERENT groups!.</p>');
            } else if (volcano.Utils.VerifyNormal(dataType, cancerType1, normal1)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check cancer type1 and sample type1!.</br>When the data type is mRNA expression, such cancer types as "LAML", "ACC", "LGG", "DLBC", "MESO", "OV", "TGCT", "UCS" and "UVM" don\'t have samples of Non-malignant type.</p>');
            } else if (volcano.Utils.VerifyNormal(dataType, cancerType2, normal2)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check cancer type2 and sample type2!.</br>When the data type is mRNA expression, such cancer types as "LAML", "ACC", "LGG", "DLBC", "MESO", "OV", "TGCT", "UCS" and "UVM" don\'t have samples of Non-malignant type.</p>');
            }  else {
                $("#echarts").html('<div class="spinner">' +
                    '<div class="rect1"></div>' +
                    '<div class="rect2"></div>' +
                    '<div class="rect3"></div>' +
                    '<div class="rect4"></div>' +
                    '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>It takes the Volcano plot about half a minute to undertake the Genome-wise profiling calculation. Please be patient.</p>');

                var url = volcano.URL.VOLCANO(cancerType1, cancerType2, normal1, normal2, dataType);

                $.get(url, {}, function (result, status) {
                    console.log("url", url);
                    if (status) {
                            var resultData = result.data;
                            function getdata(resultData) {
                                var second=[];
                                for(var wsy=0;wsy<resultData.length;wsy++){
                                    var first=[];
                                    if(resultData[wsy].geneName.y == NaN){
                                        continue;
                                    }
                                    if(isFinite(resultData[wsy].y)){
                                        first.push((resultData[wsy].x));
                                        first.push((resultData[wsy].y));
                                        first.push(resultData[wsy].geneName);
                                        first.push(resultData[wsy].d1Median);
                                        first.push(resultData[wsy].d2Median);
                                    }
                                     second.push(first);
                                }
                                return second;
                            }
                            var newData=getdata(resultData);
                            var volcanoStyle = new Array();
                            var is_reRender = false;
                            var is_downloadConfigured = false;
                            volcano.View.draw(newData, volcanoStyle, is_reRender, is_downloadConfigured, highLightedGene);
                    }
                });
            }
        }
    },
    Utils:{
        //验证两组数据是否相同
        VerifyBothSetsOfData:function(cancerType1, cancerType2, normal1, normal2){
            if(cancerType1 == cancerType2 && normal1 == normal2){
                return true;
            }
            return false;
        },
        VerifyNormal:function(dataType, cancerType, normal){
            if("e"==dataType){
                if("n"==normal){
                    var array = ["laml","acc","lgg","dlbc","meso","ov","tgct","ucs","uvm"];
                    var indexOf = array.indexOf(cancerType);
                    if(indexOf!=-1){
                        return true;
                    }
                }
            }
            return false;
        },
        //定义一个求四舍五入到num后面的n位的函数
        getResult:function(value,n){
            return Math.round(value*Math.pow(10,n))/Math.pow(10,n);
        },
        pToScience:function(value){
            var pvalue=Math.abs(value);
            pvalue=Math.pow(10,-1*pvalue/10);
            pvalue=pvalue.toExponential(2);
            return pvalue;
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
            $("#pointSize_select").append("<option class='col-md-12' value='3'>3</option>");
            /**/
            for (var i = 1; i < 20; i++) {
                /**/
                $("#pointSize_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
                /**/
            }

            var cancer_arr_e = [
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

            var cancer_arr_c = [
                "Acute Myeloid Leukemia [LAML]",
                "Adrenocortical carcinoma [ACC]",
                "Bladder Urothelial Carcinoma [BLCA]",
                "Brain Lower Grade Glioma [LGG]",
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
                "Lymphoid Neoplasm Diffuse Large B-cell Lymphoma [DLBC]",
                "Mesothelioma [MESO]",
                "Ovarian serous cystadenocarcinoma [OV]",
                "Pancreatic adenocarcinoma [PAAD]",
                "Pheochromocytoma and Paraganglioma [PCPG]",
                "Prostate adenocarcinoma [PRAD]",
                "Rectum adenocarcinoma [READ]",
                "Sarcoma [SARC]",
                "Skin Cutaneous Melanoma [SKCM]",
                "Stomach adenocarcinoma [STAD]",
                "Testicular Germ Cell Tumors [TGCT]",
                "Thymoma [THYM]",
                "Thyroid carcinoma [THCA]",
                "Uterine Carcinosarcoma [UCS]",
                "Uterine Corpus Endometrial Carcinoma [UCEC]",
                "Uveal Melanoma [UVM]"
            ];

            $("#cancername_1").html("");

            $("#cancername_1").append("<option disabled selected value='0'>Please select a cancer type.</option>");
            for(var ij = 0; ij < cancer_arr_e.length; ij++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername_1").append("<option class='col-md-12' value='" + cancer_arr_e[ij].match(r)[1].toLowerCase() + "'>" + cancer_arr_e[ij] + "</option>");
            }

            $("#cancername_2").html("");

            $("#cancername_2").append("<option disabled selected value='0'>Please select a cancer type.</option>");
            for(var hehe = 0; hehe < cancer_arr_e.length; hehe++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername_2").append("<option class='col-md-12' value='" + cancer_arr_e[hehe].match(r)[1].toLowerCase() + "'>" + cancer_arr_e[hehe] + "</option>");
            }

            $("#dataType").change(function(){
                var index = this.selectedIndex;
                $("#cancername_1").html("");
                $("#cancername_2").html("");
                $("#cancername_1").append("<option disabled selected value='0'>Please select a cancer type.</option>");
                $("#cancername_2").append("<option disabled selected value='0'>Please select a cancer type.</option>");

                if(index==1){
                    for(var i = 0; i < cancer_arr_c.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername_1").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[i] + "</option>");
                        $("#cancername_2").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[i] + "</option>");
                    }
                }else{
                    for(var i = 0; i < cancer_arr_e.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername_1").append("<option class='col-md-12' value='" + cancer_arr_e[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_e[i] + "</option>");
                        $("#cancername_2").append("<option class='col-md-12' value='" + cancer_arr_e[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_e[i] + "</option>");
                    }
                }
            });
        },

        draw: function (newData, volcanoStyle, is_reRender, is_downloadConfigured, highLightedGene) {

            var XMIN = 10000;
            var XMAX = -100;

            var YMIN = 1;
            var YMAX = -1;

            //准备高亮基因数据
            if(highLightedGene !=""){
                var highLightedGeneName = highLightedGene.split(",");
            }

            var _highLightedGeneName = [];

            for(var ws = 0; ws < newData.length; ws++){

                if(highLightedGene != ""){
                    for (var kkk = 0; kkk < highLightedGeneName.length; kkk++) {
                        //字符串相等比较
                        if (highLightedGeneName[kkk].indexOf(newData[ws][2]) ==0 && newData[ws][2].indexOf(highLightedGeneName[kkk]) ==0) {

                            _highLightedGeneName.push(newData[ws]);
                        }
                    }
                }

                if(YMAX < newData[ws][1]){
                    YMAX = newData[ws][1];
                }

                if(YMIN > newData[ws][1]){
                    YMIN = newData[ws][1];
                }

                if(XMAX < newData[ws][0]){
                    XMAX = newData[ws][0];
                }

                if(XMIN > newData[ws][0]){
                    XMIN = newData[ws][0];
                }
            }

            var cutoff=[];

            if($("#line1").val() == ""){
                cutoff.push(-10 * Math.log(0.05) / Math.log(10));
            }else{
                cutoff.push(-10 * Math.log($("#line1").val()*1) / Math.log(10));
            }

            if($("#lineFold1").val() == ""){
                cutoff.push(-1);
            }else {
                cutoff.push($("#lineFold1").val()*1);
            }

            if($("#lineFold2").val() == ""){
                cutoff.push(1);
            }else {
                cutoff.push($("#lineFold2").val()*1);
            }

            var points=new Array;

            for(var s = 0;s < 6;s++){
                points[s]=[];
            }

            for(var kk=0;kk<newData.length;kk++){

                if(newData[kk][1]<cutoff[0]){
                        if(newData[kk][0] < cutoff[1] ){
                            points[0].push(newData[kk]);
                        }else if(newData[kk][0] >= cutoff[2]){
                            points[2].push(newData[kk]);
                        }else {
                            points[1].push(newData[kk]);
                        }
                }else {
                        if(newData[kk][0] < cutoff[1] ){
                            points[5].push(newData[kk]);
                        }else if(newData[kk][0] >= cutoff[2]){
                            points[3].push(newData[kk]);
                        }else {
                            points[4].push(newData[kk]);
                        }
                }

            }

            if(volcanoStyle.length == 0) {
                volcanoStyle.push('red','MediumPurple','purple','HotPink','ForestGreen','yellow');
                volcanoStyle.push($("#pointSize_select").val());
                volcanoStyle.push($("#pointStyle_select").val());
            }

            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();
            /*$("#downloadConfigureSave_btn").click(function() {
                var saveType = $("#saveType_select").val();
                var pixelRatio = $("#pixelRatio_select").val()*1;
                var myChartOne=myChart.getOption();
                // for(var key in myChartOne){
                //     console.log(key);
                //     console.log(myChartOne[key]);
                // }

                myChartOne.toolbox[0].feature.saveAsImage.pixelRatio=pixelRatio;
                myChartOne.toolbox[0].feature.saveAsImage.type=saveType;

                // console.log(myChartOne.toolbox[0].feature.saveAsImage.pixelRatio);
                // console.log(myChartOne.toolbox[0].feature.saveAsImage.type);
                myChart.setOption(myChartOne,true);

                });*/



            //监听重新渲染按钮重新渲染
            $("#btn_Rerender").click(function() {

                is_reRender = true;

                var myChartTwo=myChart.getOption();
                // console.log(JSON.stringify(myChartTwo));

                // for(var key in myChartTwo){
                //     console.log(key);
                //     console.log(myChartTwo[key]);
                // }

                myChartTwo.toolbox[0].feature.saveAsImage.pixelRatio=$("#pixelRatio_select").val();
                myChartTwo.toolbox[0].feature.saveAsImage.type=$("#saveType_select").val();

                if($("#yLimitMin").val() !=""){

                    myChartTwo.yAxis[0].min = $("#yLimitMin").val()*1;
                }

                if($("#yLimitMax").val() !=""){
                    myChartTwo.yAxis[0].max=$("#yLimitMax").val()*1;
                }

                for (var i = 0; i < myChartTwo.series.length - 2; i++){

                        myChartTwo.series[i].itemStyle.normal.color=$("#pointColor_select"+i).val();
                        myChartTwo.series[i].symbol=$("#pointStyle_select").val();
                        myChartTwo.series[i].symbolSize=$("#pointSize_select").val();

                }
                myChart.setOption(myChartTwo,true);
            });





            //获取Y轴名称
            var yAxisName = "－log10(p-value)";

            var series=[];
            for(var i=0;i<points.length;i++){
                series.push({
                    name: 'Volcano plot',
                    type: 'scatter',
                    data: points[i],
                    symbol:volcanoStyle[7],
                    symbolSize:volcanoStyle[6],
                    itemStyle:{
                        normal:{
                            color:volcanoStyle[i]
                        }
                    }
                })
            }

            series.push({
                name: 'Volcano plot',
                type: 'scatter',
                markLine:{
                    silent: true,
                    animation:false,
                    symbolSize:0,

                    lineStyle:{
                        normal:{
                            color:"#3b92ff",    //线颜色
                            width:1.5,
                            type:'solid'    //线类型
                        }
                    },

                    data:[{
                        name:"αlpha_ttest",
                        value:cutoff[0],
                        yAxis: cutoff[0],
                        label:{
                            normal:{
                                show:false
                            }
                        }
                    },
                    {
                        name:"fold chang",
                        value:cutoff[1],
                        xAxis:cutoff[1],
                        label:{
                            normal:{
                                show:false
                            }
                        }
                    },{
                        name:"fold chang",
                        value:cutoff[2],
                        xAxis:cutoff[2],
                        label:{
                            normal:{
                                show:false
                            }
                        }
                    }]
                }
            });


            series.push(
                {
                    name:'Highlighted Gene',
                    type: 'scatter',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data: _highLightedGeneName,
                    symbolSize:20,
                    symbol:'pin',
                    itemStyle: {
                        normal: {
                            color:'#ff663a'
                        }
                    }
                }
            );




            var option = {
                backgroundColor: '#ffffff',
                title: {
                    text: 'Volcano plot',
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
                    y: '20%',
                    width: '88%',
                    height: '70%'
                }],

                tooltip: {
                    trigger:"item",
                    formatter: "{b}</<br>{c}&nbsp;&nbsp;"
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
                                    '<table width="100%" border="1" cellspacing="0" cellpadding="0"><tbody>' +
                                    '<tr><td colspan="2" align="center" >geneName</td><td colspan="2" align="center" >Median1</td><td colspan="2" align="center" >Median2</td><td colspan="2" align="center" >x</td><td colspan="2" align="center" >PValue</td></tr>';


                                for(var wy=0;wy < newData.length;wy++){
                                    if(newData[wy][2]==undefined){
                                        continue;
                                    }
                                    table += '<tr>'
                                        + '<td colspan="2" align="center">' + newData[wy][2] + '</td>'
                                        + '<td colspan="2" align="center">' + volcano.Utils.getResult(newData[wy][3],4) + '</td>'
                                        + '<td colspan="2" align="center">' + volcano.Utils.getResult(newData[wy][4],4) + '</td>'
                                        + '<td colspan="2" align="center">' + newData[wy][0] + '</td>'
                                        + '<td colspan="2" align="center">' + volcano.Utils.pToScience(Math.pow(10,-1*newData[wy][1])) + '</td>'+'</tr>';
                                }

                                table +='</tbody></table></div>';
                                return table;
                            }
                        }
                    }
                },
                xAxis: [
                    { gridIndex: 0,
                        min:Math.floor(XMIN),
                        max:Math.ceil(XMAX),
                        name:"log2(ratio)",
                        nameLocation:'middle',
                        nameGap:20
                    }
                ],
                yAxis: [
                    {   gridIndex: 0,
                        name: yAxisName,
                        min: 0,
                        max: Math.ceil(YMAX+10),
                        nameLocation:'middle',
                        position:"left",
                        nameGap:30}
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

            // for (var wsy = 0; wsy < series.length; wsy++){
            //     console.log(series[wsy]);
            // }


            var myChart = echarts.init(document.getElementById('echarts'));

            myChart.setOption(option);


        }
    }
});