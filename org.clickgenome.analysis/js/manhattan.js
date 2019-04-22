var manhattan = new Object({
    URL: {
        BASE_URL: function() {
            return "http://54.193.40.253:8080/data/data/";
            // return "http://localhost:8088/data/data/";
        },
        MANHATTAN: function(cancerType1, cancerType2, normal1, normal2, dataType, chrom, showType) {
            var value;
            if ($("#islog").is(':checked')) {
                value = "l";
            } else {
                value = "y";
            }
            var url = manhattan.URL.BASE_URL() + cancerType1 + "/" +cancerType2 + "/"+normal1 + "/"+normal2 + "/"+ dataType + "/" + $("#newOrOld").val() + "/" + value + "/" + chrom + "/" + showType + "/bytanhattan";
            console.log("url", url);
            return url;
        }
    },
    OPERATE: {
        getmanhattan: function(cancerType1, cancerType2, normal1, normal2, dataType, chrom, highLightedGene, showType) {
            if (cancerType1 =="" && cancerType2 =="") {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select a cancer type1 and a cancer type2 respectively.</p>');
            } else if (cancerType1 =="" || cancerType2 =="") {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select a cancer type.</p>');
            } else if (manhattan.Utils.VerifyBothSetsOfData(cancerType1, cancerType2, normal1, normal2)) {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please make sure to select two DIFFERENT groups!.</p>');
            }else if (manhattan.Utils.VerifyNormal(dataType, cancerType1, normal1)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check cancer type1 and sample type1!.</br>When the data type is mRNA expression, such cancer types as "LAML", "ACC", "LGG", "DLBC", "MESO", "OV", "TGCT", "UCS" and "UVM" don\'t have samples of Non-malignant type.</p>');
            }else if (manhattan.Utils.VerifyNormal(dataType, cancerType2, normal2)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check cancer type2 and sample type2!.</br>When the data type is mRNA expression, such cancer types as "LAML", "ACC", "LGG", "DLBC", "MESO", "OV", "TGCT", "UCS" and "UVM" don\'t have samples of Non-malignant type.</p>');
            } else {
                $("#echarts").html('<div class="spinner">' +
                    '<div class="rect1"></div>' +
                    '<div class="rect2"></div>' +
                    '<div class="rect3"></div>' +
                    '<div class="rect4"></div>' +
                    '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>Due to the big data size and the internet speed, this may take a while.</p>');

                var url = manhattan.URL.MANHATTAN(cancerType1, cancerType2, normal1, normal2, dataType, chrom, showType);

                $.get(url, {}, function (result, status) {
                    if (status) {
                        if (result == null) {
                            alert("error null");
                        } else {
                            var resultData = result.data[0];//未处理的数据

                            if($("#isAbsolute").is(':checked')==false){

                                for(var mm=0;mm < resultData.mResult.length; mm++){
                                    resultData.mResult[mm].y = Math.abs(resultData.mResult[mm].y);
                                }

                            }

                            var ucarmIndex = result.data[0].ucarmIndex;

                            //准备markLine数据
                            function getmarkLineData(data) {
                                var markLineData = [];
                                for (var i = 0; i < data.mResult.length; i++) {
                                    var second = [];
                                    second.push(data.mResult[i].y);
                                    second.push(data.mResult[i].geneName);
                                    markLineData.push(second);
                                }
                                return markLineData;
                            }

                            //准备tableData数据
                            function gettableData(data, ucarmIndex) {
                                var tableData = [];
                                var tableData1 = [];
                                var tableData2 = [];
                                for (var k = 0; k < ucarmIndex; k++) {
                                    var second = [];
                                    second.push(manhattan.Utils.getResult(data.mResult[k].median1,4));
                                    second.push(manhattan.Utils.getResult(data.mResult[k].median2,4));
                                    var p =Math.pow(10,-1*data.mResult[k].y/10);
                                    p=p.toExponential(2);
                                    second.push(p);
                                    second.push(data.mResult[k].geneName);
                                    tableData1.push(second);
                                }
                                tableData.push(tableData1);
                                for (var kk = ucarmIndex; kk < data.mResult.length; kk++) {
                                    var second = [];
                                    second.push(manhattan.Utils.getResult(data.mResult[kk].median1,4));
                                    second.push(manhattan.Utils.getResult(data.mResult[kk].median2,4));
                                    var q =Math.pow(10,-1*data.mResult[kk].y/10);
                                    q=q.toExponential(2);
                                    second.push(q);
                                    second.push(data.mResult[kk].geneName);
                                    tableData2.push(second);
                                }
                                tableData.push(tableData2);
                                return tableData;
                            }

                            var manhattanStyle = new Array();

                            var is_reRender = false;

                            var is_downloadConfigured = false;

                            var markLineData = getmarkLineData(resultData);

                            var tableData = gettableData(resultData, ucarmIndex);

                            manhattan.View.draw(ucarmIndex, markLineData, tableData, chrom, manhattanStyle, is_reRender, is_downloadConfigured, highLightedGene);

                        }
                    }
                });
            }
        }
    },
    Utils:{
        //验证两组数据是否相同
        VerifyBothSetsOfData:function(cancerType1, cancerType2, normal1, normal2){
            if(cancerType1 == cancerType2&& normal1 == normal2){
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
        getResult:function(value, n){
            return Math.round(value*Math.pow(10,n))/Math.pow(10,n);
        },
        //定义一个产生随机颜色的函数
        getRandomColor: function() {
            return (function(m, s, c) {
                return (c ? arguments.callee(m, s, c - 1) : '#') +
                    s[m.floor(m.random() * 16)]
            })(Math, '0123456789abcdef', 5)
        },
        //在群名称中查找target
        search: function(src, target) {
            for(var i = 0; i < src.length; i++) {
                if(src[i] == target) {
                    return i;
                }
            }
        },
        //科学计数法
        pToScience:function(value){
            var pvalue=Math.abs(value);
            pvalue=Math.pow(10,-1*pvalue/10);
            pvalue=pvalue.toExponential(2);
            return pvalue;
        },

        lineData:function (markLineData, ucarmIndex, cutoffall, XMAX, YMAX, YMIN, manhattanStyle, _highLightedGeneName, highLightedGene) {

            var lineData=[];

            var lineData1=[];

            var lineData2=[];

            for(var ii=0;ii < ucarmIndex;ii++){

                var pvalue1=manhattan.Utils.pToScience(markLineData[ii][0]);

                lineData1.push([{

                    name:markLineData[ii][1],

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
                    coord: [ii+1, markLineData[ii][0]],
                    symbol: 'none'
                }])
            }

            lineData.push(lineData1);

            for(var jj = ucarmIndex;jj < markLineData.length;jj++){

                var pvalue2=manhattan.Utils.pToScience(markLineData[jj][0]);

                lineData2.push([{

                    name:markLineData[jj][1],

                    value:pvalue2,

                    coord: [jj+100, 0],

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
                    coord: [jj+100, markLineData[jj][0]],

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
                            coord: [XMAX, cutoffall[0][0]],
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
                            coord: [XMAX, cutoffall[0][0]],
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
                            coord: [XMAX, -1*cutoffall[0][0]],
                            symbol: 'none'
                        }];
                    lineData2.push(line2);


                }

                var cutoffShape1 = [{
                    name:"P-arm",
                    value: ("" + cutoffall[0][1]) + "%",
                    coord: [ucarmIndex-40, cutoffall[0][1]-3],
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
                    coord: [ucarmIndex-40, cutoffall[0][1]-3],
                    symbol: 'none'
                }];
                lineData2.push(cutoffShape1);

                var cutoffShape2= [{
                    name:"Q-arm",
                    value: ("" + cutoffall[1][1] ) + "%",
                    coord: [ucarmIndex+500, cutoffall[0][1]-3],
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
                    coord: [ucarmIndex+500, cutoffall[0][1]-3 ],
                    symbol: 'none'
                }];
                lineData2.push(cutoffShape2);
            }

            if(highLightedGene !=""){

                for(var w=0;w<_highLightedGeneName.length;w++){
                    var pvalue3=manhattan.Utils.pToScience(_highLightedGeneName[w][1]);
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
    View: {
        init: function() {
            /**初始化配置项ui*/
            $("#pixelRatio_select").html("");

            for (var i = 1; i < 50; i++) {

                $("#pixelRatio_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");

            }
            /**/
            $("#saveType_select").html("");
            /**/
            $("#saveType_select").append("<option class='col-md-12' value='png'>png</option>");
            /**/
            $("#saveType_select").append("<option class='col-md-12' value='jpeg'>jpeg</option>");

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
            $("#cancername_1").append("<option disabled selected value=''>Please select the cancer type.</option>");
            for(var ij = 0; ij < cancer_arr_c.length; ij++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername_1").append("<option class='col-md-12' value='" + cancer_arr_c[ij].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[ij] + "</option>");
            }

            $("#cancername_2").html("");
            $("#cancername_2").append("<option disabled selected value=''>Please select the cancer type.</option>");
            for(var hehe = 0; hehe < cancer_arr_c.length; hehe++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername_2").append("<option class='col-md-12' value='" + cancer_arr_c[hehe].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[hehe] + "</option>");
            }

            $("#dataType").change(function(){
                var index = this.selectedIndex;
                $("#cancername_1").html("");
                $("#cancername_2").html("");
                $("#cancername_1").append("<option disabled selected value=''>Please select the cancer type.</option>");
                $("#cancername_2").append("<option disabled selected value=''>Please select the cancer type.</option>");
                if(index==1){
                    for(var i = 0; i < cancer_arr_e.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername_1").append("<option class='col-md-12' value='" + cancer_arr_e[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_e[i] + "</option>");
                        $("#cancername_2").append("<option class='col-md-12' value='" + cancer_arr_e[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_e[i] + "</option>");
                    }
                }else{
                    for(var i = 0; i < cancer_arr_c.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername_1").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[i] + "</option>");
                        $("#cancername_2").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[i] + "</option>");
                    }
                }
            });
        },
        getRandomColor: function() {
            return (function(m, s, c) {
                return (c ? arguments.callee(m, s, c - 1) : '#') +
                    s[m.floor(m.random() * 16)]
            })(Math, '0123456789abcdef', 5)
        },
        draw: function(ucarmIndex, markLineData, tableData, chrom, manhattanStyle, is_reRender, is_downloadConfigured, highLightedGene) {
            var XMIN = 0;
            var XMAX = markLineData.length+200;
            var YMAX = 1;
            var YMIN = 0;
            var series = [];

            //准备高亮基因数据
            if(highLightedGene !=""){
                var highLightedGeneName = highLightedGene.split(",");
            }

            var _highLightedGeneName = [];

            for(var ll=0;ll<markLineData.length;ll++){

                if(highLightedGene != 0){
                    for (var lll = 0; lll < highLightedGeneName.length; lll++) {
                        //字符串相等比较
                        if (highLightedGeneName[lll].indexOf(markLineData[ll][1]) ==0 && markLineData[ll][1].indexOf(highLightedGeneName[lll]) ==0) {
                            var bb=[];
                            if(ll<ucarmIndex){
                                bb.push((ll+1));

                            }else {
                                bb.push((ll+100));
                            }
                            bb.push(markLineData[ll][0]);
                            bb.push(markLineData[ll][1]);
                            _highLightedGeneName.push(bb);
                        }
                    }
                }
            }

            //初始化图片下载配置结束
            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();
            //初始化图片下载配置结束


            var glist_ex = ["P-Arm","Q-Arm"];
            //页面中的Group,初始化蜂群选择器,填充选择框(只执行一次)
            if (!is_reRender) {
                $("#manhattan_which_select").html("");
                for (var i = 0; i < glist_ex.length; i++) {
                    $("#manhattan_which_select").append("<option class='col-md-12' value='" + glist_ex[i] + "'>" + glist_ex[i] + "</option>")
                }
            }

            //样式数组初始化
            if(manhattanStyle.length == 0) {

                    manhattanStyle[0]=new Array();

                    manhattanStyle[1]=new Array();

                    manhattanStyle[0][0]='#e09e41';

                    manhattanStyle[1][0]='#29fd2f';

            }


            //监听重新渲染按钮重新渲染
            $("#btn_Rerender").click(function() {

                is_reRender = true;

                var myChartTwo=myChart.getOption();

                myChartTwo.toolbox[0].feature.saveAsImage.type=$("#saveType_select").val();
                myChartTwo.toolbox[0].feature.pixelRatio=$("#pixelRatio_select").val();

                if($("#yLimitMin").val() !=""){

                    myChartTwo.yAxis[0].min = $("#yLimitMin").val()*1;

                }

                if($("#yLimitMax").val() !=""){

                    myChartTwo.yAxis[0].max=$("#yLimitMax").val()*1;

                }

                if(myChartTwo.series[0].name == $("#manhattan_which_select").val() ){

                    for(var wsy=0; wsy < ucarmIndex; wsy++){

                        myChartTwo.series[0].markLine.data[wsy][0].lineStyle.normal.color=$("#Color_select").val();

                    }

                    myChartTwo.series[0].itemStyle.normal.color=$("#Color_select").val();
                }


                if(myChartTwo.series[1].name == $("#manhattan_which_select").val() ){

                    for(var wsy1=0; wsy1 < markLineData.length-ucarmIndex; wsy1++){

                        myChartTwo.series[1].markLine.data[wsy1][0].lineStyle.normal.color=$("#Color_select").val();

                    }

                    myChartTwo.series[1].itemStyle.normal.color=$("#Color_select").val();
                }

                myChart.setOption(myChartTwo,true);



            });
            //获取Y轴名称
            var yAxisName = "－10log10(q-value)";

            var α=-10 * Math.log($("#line1").val()*1) / Math.log(10);
            α=(Math.round(α*10000))/10000;
            var cutoff = [];
            var cutoffall = [];

            if($("#line1").val() != "") {

                cutoff.push(α);
                cutoffall.push([cutoff[0], 0]);//P-arm
                cutoffall.push([cutoff[0], 0]);//Q-arm

                if(YMIN > α) { //计算y最小
                    YMIN = α;
                }

                if(YMAX < α) { //计算y最大
                    YMAX = α;
                }

            }

            for (var kk = 0; kk < ucarmIndex; kk++) {

                if (markLineData[kk][0] < YMIN) {

                    YMIN = markLineData[kk][0];

                }
                if (markLineData[kk][0] > YMAX) {

                    YMAX = markLineData[kk][0];

                }
                if($("#line1").val() != "") {

                    if (Math.abs(markLineData[kk][0]) >= cutoff[0]) {

                        cutoffall[0][1]=cutoffall[0][1]+1;

                    }
                }
            }

            for (var kkk = ucarmIndex; kkk < markLineData.length; kkk++) {

                if (markLineData[kkk][0] < YMIN) {

                    YMIN = markLineData[kkk][0];

                }
                if (markLineData[kkk][0] > YMAX) {

                    YMAX = markLineData[kkk][0];

                }
                if($("#line1").val() != "") {

                    if (Math.abs(markLineData[kkk][0]) >= cutoff[0]) {

                        cutoffall[1][1]=cutoffall[1][1]+1;

                    }
                }
            }

            if($("#line1").val() != ""){

                cutoffall[0][1] = (Math.round((cutoffall[0][1] / ucarmIndex)*10000))/100;

                cutoffall[1][1] = (Math.round((cutoffall[1][1] / (markLineData.length-ucarmIndex))*10000))/100;

            }

            var lineGroupData =manhattan.Utils.lineData(markLineData, ucarmIndex, cutoffall, XMAX, YMAX, YMIN, manhattanStyle, _highLightedGeneName, highLightedGene);

            for(var w=0;w < 2;w++){

                series.push({

                    name: glist_ex[w],
                    type: 'scatter',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data: tableData[w],
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
                title: {
                    text: 'Chromosome: ' + chrom + "; GeneNumber =" + markLineData.length,
                    x: 'left',
                    y: 0
                },
                grid: [{
                    x: '10%',
                    y: '35%',
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
                tooltip: {
                    trigger:"item",
                    formatter: "{b} &nbsp;&nbsp; {c}&nbsp;&nbsp;"
                },
                xAxis: [
                    { gridIndex: 0,
                      min: XMIN,
                      max: XMAX }
                ],
                yAxis: [
                    { gridIndex: 0,
                      name: yAxisName,
                      min: Math.floor(YMIN-20),
                      max: Math.ceil(YMAX+20) }
                ],
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
                        },
                        dataView:{
                            show:true,
                            title:"Data table",
                            readOnly: true,
                            lang:["Data table","Close"],
                            buttonColor:'#748aff',
                            optionToContent:function(opt){

                                var series = opt.series;
                                //重新开始
                                var table = '<div style="width: 100%;height: 100%;overflow: auto;user-select: all;">' +
                                    '<table width="100%" border="1" cellspacing="0" cellpadding="0"><tbody>';

                                if($("#showType").val()=="mid"){

                                    table +='<tr><td align="center">GeneName</td><td align="center">Data1Median</td><td align="center">Data2Median</td><td align="center">Qvalue</td></tr>';

                                }else {

                                    table +='<tr><td align="center">GeneName</td><td align="center">Data1Mean</td><td align="center">Data2Mean</td><td align="center">Qvalue</td></tr>';

                                }

                                for(var i=0;i<series.length;i++){
                                    for(var jj=0;jj<series[i].data.length;jj++){

                                        table +='<tr><td align="center">'+series[i].data[jj][3]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][0]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][1]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][2]+'</td></tr>';

                                    }
                                }
                                table +='</tbody></table></div>';
                                return table;
                            }
                        }
                    }
                },
                axisPointer: {
                    link: {xAxisIndex: 'all'},
                    label: {
                        backgroundColor: '#777'
                    }
                },
                series: series
            };

            // for (var i = 0; i < series.length; i++){
            //     console.log(series[i]);
            // }

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