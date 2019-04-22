var deflection = new Object({
    URL: {
        BASE_URL: function() {
            return "http://54.193.40.253:8080/data/data/";
            // return "http://localhost:8088/data/data/";
        },
        DEFLECTION: function(cancerType1, cancerType2, dataType, chrom, showType) {
            var value;
            if ($("#islog").is(':checked')) {
                value = "l";
            } else {
                value = "y";
            }
            var url = deflection.URL.BASE_URL() + cancerType1 + "/" +cancerType2 + "/" + dataType+ "/" + $("#newOrOld").val()  + "/"+ value+"/"+ chrom + "/" + showType + "/bydeflection";
            console.log("url", url);
            return url;
        }
    },
    OPERATE: {
        getdeflection: function(cancerType1, cancerType2,dataType, chrom, highLightedGene, showType) {

            if (cancerType1 =="" && cancerType2 =="") {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select a cancer type1 and a cancer type2 respectively.</p>');
            } else if (cancerType1 =="" || cancerType2 =="") {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select a cancer type.</p>');
            } else if (deflection.Utils.VerifyBothSetsOfData(cancerType1, cancerType2)) {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please make sure to select two DIFFERENT groups!.</p>');
            } else {
                $("#echarts").html('<div class="spinner">' +
                    '<div class="rect1"></div>' +
                    '<div class="rect2"></div>' +
                    '<div class="rect3"></div>' +
                    '<div class="rect4"></div>' +
                    '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>Due to the big data size and the internet speed, this may take a while.</p>');

                var url = deflection.URL.DEFLECTION(cancerType1, cancerType2,dataType, chrom, showType);

                $.get(url, {}, function (result, status) {
                    if (status) {
                        if (result == null) {
                            alert("error null");
                        } else {
                            var resultData = result.data[0];//未处理的数据

                            // if($("#isAbsolute").is(':checked')==false){
                            //     for(var mm=0;mm<resultData.mResult.length; mm++){
                            //         resultData.mResult[mm].y = Math.abs(resultData.mResult[mm].y);
                            //     }
                            // }
                            var ucarmIndex = result.data[0].ucarmIndex;
                            var colorFlag =  result.data[0].color;
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
                                    second.push(deflection.Utils.getResult(data.mResult[k].median1,4));
                                    second.push(deflection.Utils.getResult(data.mResult[k].median1N,4));
                                    second.push(deflection.Utils.getResult(data.mResult[k].median2,4));
                                    second.push(deflection.Utils.getResult(data.mResult[k].median2N,4));
                                    var p =Math.pow(10,-1*data.mResult[k].y/10);
                                    p=p.toExponential(2);
                                    second.push(p);
                                    second.push(data.mResult[k].geneName);
                                    tableData1.push(second);
                                }
                                tableData.push(tableData1);
                                if(ucarmIndex>=0){
                                    for (var kk = ucarmIndex; kk < data.mResult.length; kk++) {
                                        var second = [];
                                        second.push(deflection.Utils.getResult(data.mResult[kk].median1,4));
                                        second.push(deflection.Utils.getResult(data.mResult[kk].median1N,4));
                                        second.push(deflection.Utils.getResult(data.mResult[kk].median2,4));
                                        second.push(deflection.Utils.getResult(data.mResult[kk].median2N,4));
                                        var q =Math.pow(10,-1*data.mResult[kk].y/10);
                                        q=q.toExponential(2);
                                        second.push(q);
                                        second.push(data.mResult[kk].geneName);
                                        tableData2.push(second);
                                    }
                                    tableData.push(tableData2);
                                }
                                return tableData;
                            }


                            var manhattanStyle = new Array();
                            var is_reRender = false;
                            var is_downloadConfigured = false;
                            var markLineData = getmarkLineData(resultData);
                            var tableData = gettableData(resultData, ucarmIndex);

                            deflection.View.draw(ucarmIndex, markLineData, tableData, chrom, manhattanStyle, is_reRender, is_downloadConfigured, highLightedGene, colorFlag);

                        }
                    }
                });
            }
        }
    },
    Utils:{
        //验证两组数据是否相同
        VerifyBothSetsOfData:function(cancerType1, cancerType2){
            if(cancerType1 == cancerType2){
                return true;
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

        lineData:function (markLineData, ucarmIndex, cutoffall, XMAX, YMAX, YMIN, manhattanStyle, _highLightedGeneName, highLightedGene, colorFlag) {
            var lineData=[];
            var lineData1=[];
            var lineData2=[];
            var linePlot1=[];
            var linePlot2=[];
            for(var sy=0;sy<colorFlag.length;sy++){
                var wsy1=[];
                var wsy2=[];
                if(colorFlag[sy]==1){
                    if(sy < ucarmIndex){
                        wsy1.push((sy+1));
                        wsy1.push(markLineData[sy][0]);
                        wsy1.push(markLineData[sy][1]);

                    }else {
                        wsy1.push((sy+100));
                        wsy1.push(markLineData[sy][0]);
                        wsy1.push(markLineData[sy][1]);
                    }
                linePlot1.push(wsy1);
                }else {
                    if(sy < ucarmIndex){
                        wsy2.push((sy+1));
                        wsy2.push(markLineData[sy][0]);
                        wsy2.push(markLineData[sy][1]);

                    }else {
                        wsy2.push((sy+100));
                        wsy2.push(markLineData[sy][0]);
                        wsy2.push(markLineData[sy][1]);
                    }
                linePlot2.push(wsy2);
                }
            }

            for(var ii=0;ii<linePlot1.length;ii++){
                var pvalue1=deflection.Utils.pToScience(linePlot1[ii][1]);

                lineData1.push([{
                    name:linePlot1[ii][2],
                    value:pvalue1,
                    coord: [linePlot1[ii][0], 0],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: manhattanStyle[0],
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
                var pvalue2=deflection.Utils.pToScience(linePlot2[jj][1]);
                lineData2.push([{
                    name:linePlot2[jj][2],
                    value:pvalue2,
                    coord: [linePlot2[jj][0], 0],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: manhattanStyle[1],
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
                                color: "#748aff"
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
                                color: "#748aff"
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
                    var pvalue3=deflection.Utils.pToScience(_highLightedGeneName[w][1]);
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


            /**/
            var cancer_arr_e = [
                /*"⚠️Acute Myeloid Leukemia [LAML]",
                "⚠️Adrenocortical carcinoma [ACC]",*/
                "Bladder Urothelial Carcinoma [BLCA]",
                /*"⚠️Brain Lower Grade Glioma [LGG]",*/
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
                /*"⚠️Lymphoid Neoplasm Diffuse Large B-cell Lymphoma [DLBC]",
                "⚠️Mesothelioma [MESO]",
                "⚠️Ovarian serous cystadenocarcinoma [OV]",*/
                "Pancreatic adenocarcinoma [PAAD]",
                "Pheochromocytoma and Paraganglioma [PCPG]",
                "Prostate adenocarcinoma [PRAD]",
                "Rectum adenocarcinoma [READ]",
                "Sarcoma [SARC]",
                "Skin Cutaneous Melanoma [SKCM]",
                "Stomach adenocarcinoma [STAD]",
                /*"⚠️Testicular Germ Cell Tumors [TGCT]",*/
                "Thymoma [THYM]",
                "Thyroid carcinoma [THCA]",
                /*"⚠️Uterine Carcinosarcoma [UCS]",*/
                "Uterine Corpus Endometrial Carcinoma [UCEC]"/*,*/
                /*"⚠️Uveal Melanoma [UVM]"*/
            ];

            var cancer_arr_c = [
                /**/
                "Acute Myeloid Leukemia [LAML]",
                /**/
                "Adrenocortical carcinoma [ACC]",
                /**/
                "Bladder Urothelial Carcinoma [BLCA]",
                /**/
                "Brain Lower Grade Glioma [LGG]",
                /**/
                "Breast invasive carcinoma [BRCA]",
                /**/
                "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESC]",
                /**/
                "Cholangiocarcinoma [CHOL]",
                /**/
                "Colon adenocarcinoma [COAD]",
                /**/
                "Esophageal carcinoma [ESCA]",
                /**/
                "Glioblastoma multiforme [GBM]",
                /**/
                "Head and Neck squamous cell carcinoma [HNSC]",
                /**/
                "Kidney Chromophobe [KICH]",
                /**/
                "Kidney renal clear cell carcinoma [KIRC]",
                /**/
                "Kidney renal papillary cell carcinoma [KIRP]",
                /**/
                "Liver hepatocellular carcinoma [LIHC]",
                /**/
                "Lung adenocarcinoma [LUAD]",
                /**/
                "Lung squamous cell carcinoma [LUSC]",
                /**/
                "Lymphoid Neoplasm Diffuse Large B-cell Lymphoma [DLBC]",
                /**/
                "Mesothelioma [MESO]",
                /**/
                "Ovarian serous cystadenocarcinoma [OV]",
                /**/
                "Pancreatic adenocarcinoma [PAAD]",
                /**/
                "Pheochromocytoma and Paraganglioma [PCPG]",
                /**/
                "Prostate adenocarcinoma [PRAD]",
                /**/
                "Rectum adenocarcinoma [READ]",
                /**/
                "Sarcoma [SARC]",
                /**/
                "Skin Cutaneous Melanoma [SKCM]",
                /**/
                "Stomach adenocarcinoma [STAD]",
                /**/
                "Testicular Germ Cell Tumors [TGCT]",
                /**/
                "Thymoma [THYM]",
                /**/
                "Thyroid carcinoma [THCA]",
                /**/
                "Uterine Carcinosarcoma [UCS]",
                /**/
                "Uterine Corpus Endometrial Carcinoma [UCEC]",
                /**/
                "Uveal Melanoma [UVM]"
                /**/
            ];

            $("#cancername_1").html("");
            $("#cancername_1").append("<option disabled selected value='0'>Please select a cancer type.</option>");
            for(var ij = 0; ij < cancer_arr_c.length; ij++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername_1").append("<option class='col-md-12' value='" + cancer_arr_c[ij].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[ij] + "</option>");
            }

            $("#cancername_2").html("");
            $("#cancername_2").append("<option disabled selected value='0'>Please select a cancer type.</option>");
            for(var hehe = 0; hehe < cancer_arr_c.length; hehe++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername_2").append("<option class='col-md-12' value='" + cancer_arr_c[hehe].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[hehe] + "</option>");
            }

            $("#dataType").change(function(){
                var index = this.selectedIndex;
                $("#cancername_1").html("");
                $("#cancername_2").html("");
                $("#cancername_1").append("<option disabled selected value='0'>Please select a cancer type.</option>");
                $("#cancername_2").append("<option disabled selected value='0'>Please select a cancer type.</option>");

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
        draw: function(ucarmIndex, markLineData, tableData, chrom, manhattanStyle, is_reRender, is_downloadConfigured, highLightedGene, colorFlag) {
            var XMIN = 0;
            var XMAX = markLineData.length+200;
            var YMAX = 1;
            var YMIN = 0;
            var series = [];

            if($("#yLimitMin").val() !=""){
                if($("#yLimitMin").val()*1<YMIN){
                    YMIN=$("#yLimitMin").val()*1;
                }
            }
            if($("#yLimitMax").val() !=""){
                if($("#yLimitMax").val()*1>YMAX){
                    YMAX=$("#yLimitMax").val()*1;
                }
            }

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
            /*console.log(_highLightedGeneName);*/

            //初始化图片下载配置结束
            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();
            //初始化图片下载配置结束


            if(!is_downloadConfigured) {
                $("#downloadConfigureSave_btn").click(function() {
                    is_downloadConfigured = true;
                    deflection.View.draw(ucarmIndex, markLineData, tableData, chrom, manhattanStyle, is_reRender, is_downloadConfigured, highLightedGene, colorFlag);
                });
            }


            var glist_ex = [];
            glist_ex.push($("#cancer_1").val().toUpperCase());
            glist_ex.push($("#cancer_2").val().toUpperCase());

            //页面中的Group,初始化蜂群选择器,填充选择框
            // if (!is_reRender) {
            //     $("#manhattan_which_select").html("");
            //     for (var i = 0; i < glist_ex.length; i++) {
            //         $("#manhattan_which_select").append("<option class='col-md-12' value='" + glist_ex[i] + "'>" + glist_ex[i] + "</option>")
            //     }
            // }

            var color0 = '#ff4a4d';
            var color1 = '#3b92ff';
            //样式数组初始化
            if(manhattanStyle.length == 0) {

                manhattanStyle.push(color0);
                manhattanStyle.push(color1);

            } else {
                var colorSelect0 = $("#Color_select").val();
                var colorSelect1 = $("#Color_select2").val();

                manhattanStyle[0] = colorSelect0;
                manhattanStyle[1] = colorSelect1;

            }


            //监听重新渲染按钮重新渲染
            $("#btn_Rerender").click(function() {
                deflection.View.draw(ucarmIndex, markLineData, tableData, chrom, manhattanStyle, is_reRender, is_downloadConfigured, highLightedGene, colorFlag);
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
            if(ucarmIndex>=0){

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
            }

            if($("#line1").val() != ""){

                cutoffall[0][1] = (Math.round((cutoffall[0][1] / ucarmIndex)*10000))/100;

                cutoffall[1][1] = (Math.round((cutoffall[1][1] / (markLineData.length-ucarmIndex))*10000))/100;

            }

            var lineGroupData =deflection.Utils.lineData(markLineData, ucarmIndex, cutoffall, XMAX, YMAX, YMIN, manhattanStyle, _highLightedGeneName, highLightedGene, colorFlag);

            for(var w=0;w<2;w++){
                series.push({
                    name: glist_ex[w],
                    type: 'scatter',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data: tableData[w],
                    symbol:"none",
                    itemStyle: {
                        normal: {
                            color: manhattanStyle[w]
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

                                    table +='<tr><td align="center">GeneName</td><td align="center">Data1MedianTumor</td><td align="center">Data1MedianNormal</td><td align="center">Data2MedianTumor</td><td align="center">Data2MedianNormal</td><td align="center">Qvalue</td></tr>';

                                }else {

                                    table +='<tr><td align="center">GeneName</td><td align="center">Data1MeanTumor</td><td align="center">Data1MeanNormal</td><td align="center">Data2MeanTumor</td><td align="center">Data2MeanNormal</td><td align="center">Qvalue</td></tr>';

                                }

                                for(var i=0;i<series.length;i++){
                                    for(var jj=0;jj<series[i].data.length;jj++){
                                        table +='<tr><td align="center">'+series[i].data[jj][5]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][0]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][1]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][2]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][3]+'</td>'+
                                            '<td align="center">'+series[i].data[jj][4]+'</td></tr>';
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

            if (myChart === undefined){
                myChart = echarts.init(document.getElementById('echarts'));
            }
            myChart.setOption(option);

            $("#bt_draw").removeAttr("disabled");
            $("#btn_Rerender").removeAttr("disabled");
            //监听事件
            myChart.on("dblclick", function(param) {
                var mes = '【' + param.type + '】';
                if(typeof param.seriesIndex != 'undefined') {
                    mes += '  seriesIndex : ' + param.seriesIndex;
                    mes += '  dataIndex : ' + param.dataIndex;
                }
                if(param.type == 'hover') {
                    document.getElementById('hover-console').innerHTML = 'Event Console : ' + mes;
                } else {
                    if((Math.round(param.data[0]) - 1) % 3 == 0) {
                        $("#note").append('<div class="alert alert-info alert-dismissible" role="alert" style="padding:0;margin:0;margin-top:1px;font-weight:normal;font-size:0.8em;">' +
                            '<button type="button" style="padding:0;margin:0;" class="close" data-dismiss="alert">' +
                            '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
                            '<strong style="padding:0;margin:0;font-weight:light;font-size:0.8em;">' + param.data[2] + ' </strong> ' + param.data[1] +
                            '</div>');
                    } else {
                        $("#note").append('<div class="alert alert-danger alert-dismissible" role="alert" style="padding:0;margin:0;margin-top:1px;font-weight:normal;font-size:0.8em;">' +
                            '<button type="button" style="padding:0;margin:0;" class="close" data-dismiss="alert">' +
                            '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
                            '<strong style="padding:0;margin:0;font-weight:light;font-size:0.8em;">' + param.data[2] + ' </strong> ' + param.data[1] +
                            '</div>');
                    }
                    //递归绘制
                    deflection.View.draw(ucarmIndex, markLineData, tableData, chrom, manhattanStyle, is_reRender, is_downloadConfigured, highLightedGene, colorFlag);
                }
            });

        }
    }
});