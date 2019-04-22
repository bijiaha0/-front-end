var chromsome = new Object({
    URL: {
        BASE_URL: function() {
            return "http://54.193.40.253:8080/data/data/";
            // return "http://localhost:8088/data/data/";
        },
        MOUTAIN: function(cancerType, chromsomes, showType) {
            var value;
            if ($("#islog").is(':checked')) {
                value = "l";
            } else {
                value = "y";
            }
            var url;
            var arr = cancerType.split(',');
            var arrNew=[];
            if(chromsome.Operate.isContains(cancerType,'all-non-malignant')){
                for(var ww=0;ww<arr.length;ww++){
                    if(arr[ww]!='all-non-malignant'){
                        arrNew.push(arr[ww]);
                    }
                }
                // console.log("Another");
                // console.log(arrNew.join(','));
                url = chromsome.URL.BASE_URL() +  arrNew.join(',') + "/" + chromsomes + "/" + $("#dataType").val() + "/" + value + "/" + showType + "/" + $("#newOrOld").val() + "/bychromosomes/Another";
                // console.log("cancerType", cancerType);
                // console.log(url)
                return url
            }else {
                url = chromsome.URL.BASE_URL() + cancerType + "/" + chromsomes + "/" + $("#dataType").val() + "/" + value + "/" + showType + "/" + $("#newOrOld").val() + "/bychromosomes";
                // console.log("one");
                // console.log("url", url);
                return url
            }
        }
    },
    Operate: {
        //检验是否存在
        isContains:function(arrString, Str) {
            if(arrString.indexOf(Str)!=-1){
                return true;
            }else {
                return false;
            }
        },
        getMoutainData: function(highLightedGene, cancerType, chromsomes, showType) {
            if ($("#cancer_s").val() =="") {
                $("#bt_draw").disabled=false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select at least one cancerType.</p>');
            }else if(chromsome.Utils.VerifyNormal(cancerType)){
                $("#bt_draw").disabled=false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ It doesn`t make any sense for selecting only this reference sample set!</p>');
            }else if (chromsome.Utils.VerifyBothSetsOfData(cancerType)) {
                $("#bt_draw").disabled = false;
                $("#echarts").html(
                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select at least one cancerType with a non-malignant sample!.</br>When the data type is mRNA expression, such cancer types as "LAML", "ACC", "LGG", "DLBC", "MESO", "OV", "TGCT", "UCS" and "UVM" don\'t have samples of Non-malignant type.</p>');
            } else {
                    $("#echarts").html('<div class="spinner">' +
                        '<div class="rect1"></div>' +
                        '<div class="rect2"></div>' +
                        '<div class="rect3"></div>' +
                        '<div class="rect4"></div>' +
                        '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>Due to the big data size and the internet speed, this may take a while.</p>');
                    var url = chromsome.URL.MOUTAIN(cancerType, chromsomes, showType);
                    $.get(url, {}, function (result, status) {
                        if (status) {
                            if (result == null) {
                                alert("error null");
                            } else {
                                var resultData = result.data;//未处理的数据

                                var yValueArr = resultData.yGroup;
                                var xStart = resultData.xStart;
                                var geneNames = resultData.geneNames;
                                var cyto = resultData.cyto;
                                var qPosition = resultData.arm.position;
                                var index =resultData.arm.index;
                                var DTW=resultData.dtw;

                                var mountainStyle = new Array();
                                var hignGeneStyle = new Array();
                                var is_reRender = false;
                                chromsome.View.draw(yValueArr, xStart,geneNames,cyto,qPosition,index,DTW, highLightedGene, cancerType, mountainStyle, hignGeneStyle, is_reRender);
                            }
                        }
                    });
                }
        }
    },
    Utils: {
        VerifyBothSetsOfData:function(cancerType){
            if($("#dataType").val()=="e"){
                var count = 0;
                var split = cancerType.split(",");
                var array = ["laml","acc","lgg","dlbc","meso","ov","tgct","ucs","uvm"];
                for(var i = 0 ;i < split.length;i++){
                    var indexOf = array.indexOf(split[i]);
                    if(indexOf!=-1){
                        count++;
                    }
                }
                if(count==split.length){
                    return true;
                }
            }
            return false;
        },
        VerifyNormal:function(cancerType){
                if("all-non-malignant"==cancerType){
                        return true;
                }
            return false;
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
        //自定义从小到大的排序函数与sort函数组合起来用
        sequence:function (a,b) {
                if (a>b) {
                    return 1;
                }else if(a<b){
                    return -1
                }else{
                    return 0;
                }
        },
        //定义一个函数:用于判断一个数字是否为整数
        isInteger:function(obj){
            return Math.floor(obj) == obj
        },
        //定义一个函数:用于求一组数的中位数
        getMedian:function(oneArray){
            var oneArrayLength=oneArray.length;
            //console.log(oneArrayLength+"medianIndex中位数");
            var index = (oneArrayLength+1)/2;
            //console.log(index+"index索引");
            if(chromsome.Utils.isInteger(index)){
                return oneArray[index-1];
            }else{

                return (1/2*(oneArray[((oneArrayLength/2)-1)]+oneArray[(oneArrayLength/2)]));
                // return chromsome.Utils.getResult((1/2*(oneArray[((oneArrayLength/2)-1)]+oneArray[(oneArrayLength/2)])),4);
            }
        },
        //定义一个函数:用于求一组数的平均数
        getMean:function(oneArray){
            var sum=0;
            for(var i=0;i<oneArray.length;i++){
                sum +=oneArray[i];
            }
            var avg=sum/oneArray.length;
            return avg;
        },
        //在群名称中查找target
        search: function(src, target) {
            for(var i = 0; i < src.length; i++) {
                if(src[i] == target) {
                    return i;
                }
            }
        },
        //定义一个求四舍五入到num后面的n位的函数
        getResult:function(value,n){
            return Math.round(value*Math.pow(10,n))/Math.pow(10,n);
        },
        //定义求曲线相似性的函数,要求dataAll是所有的肿瘤样本组和一组取中位数的正常样本组。
        curveSimilarity:function (dataAll,index) {
            // console.log(gm+"gm的值");
            var score=[];
            var lastIndex=dataAll.length-1;
            var array_n_p=[];//p正常基因组
            var array_n_q=[];//q正常基因组
            //把正常的基因根据p q分成两组
            for(var l=0;l<index;l++){//
                array_n_p.push(dataAll[lastIndex][l]);
            }
            for(var lll=index;lll < dataAll[lastIndex].length; lll++){//
                array_n_q.push(dataAll[lastIndex][lll]);
            }

            var array_n=array_n_p.concat(array_n_q);
            //console.log(array_n+"    n");
            // console.log(dataAll.length+"dataAll.length的长度");
            for(var i=0;i < dataAll.length-1;i++){//所有的肿瘤癌症
                // console.log("打印了几次");
                var value=[0,0,0,0,0,0,0,0,0];
                var array_t_p=[];
                var array_t_q=[];
                for(var ii=0;ii<index;ii++){//单个的肿瘤癌症
                        var a=dataAll[i][ii]-dataAll[lastIndex][ii];
                        value[0] +=a;//值和
                        value[1] +=Math.abs(a);//绝对值和
                        value[2] += Math.pow(a,2);//平方值和
                        array_t_p.push(dataAll[i][ii]);
                }
                for(var iii=index;iii < dataAll[0].length; iii++){//单个的肿瘤癌症
                        var b=dataAll[i][iii]-dataAll[lastIndex][iii];
                        value[3] +=b;//值和
                        value[4] +=Math.abs(b);//绝对值和
                        value[5] += Math.pow(b,2);//平方值和
                        array_t_q.push(dataAll[i][iii]);
                }


                var array_t=array_t_p.concat(array_t_q);
                //console.log(array_t+"    t");
                if(array_n_p.length == 0 && array_t_p.length == 0){
                    value[6]=0;
                }else {
                    value[6]=dtw(array_t_p, array_n_p, euclidean_pdist);
                }
                value[7]=dtw(array_t_q, array_n_q, euclidean_pdist);
                value[8]=dtw(array_t, array_n, euclidean_pdist);
                console.log("卡死");
                score.push(value);//每个癌症算一组
            }
            return score;
        },
        //tableData是markPoint点,为echarts的dataview数据视图做准备
        tableData:function(DTW){

            //var preData=chromsome.Utils.curveSimilarity(dataAll,index);
            // console.log(preData+'    preData');
            var viewData=[];
            for(var m=0;m<DTW.length;m++){
                var valueOne=[];
                valueOne.push(chromsome.Utils.getResult(DTW[m][0],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][1],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][2],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][3],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][4],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][5],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][6],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][7],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][8],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][9],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][10],2));
                valueOne.push(chromsome.Utils.getResult(DTW[m][11],2));
                    var preViewData=[
                                        [   { coord: [valueOne[0], valueOne[1],valueOne[2]]}],
                                        [   { coord: [valueOne[3], valueOne[4],valueOne[5]]}],
                                        [   { coord: [valueOne[6], valueOne[7],valueOne[8]]}],
                                        [   { coord: [valueOne[9], valueOne[10],valueOne[11]]}]
                    ];
                viewData.push(preViewData);
            }
            return viewData;
        },

        lineData:function (cyto,YMIN,XMAX,XMIN,qPosition,cutoffall) {
            var lineData=[];
            if ($("#cyto").is(':checked')) {
                var _cyto=[];
                var CYTO_Y = Math.floor(YMIN);
                var CYTO_COLOR = "";
                for (var element in cyto) {
                    var cc = [];
                    for (var a in cyto[element]) {
                        cc.push(cyto[element][a]);
                    }
                    _cyto.push(cc);
                }
                    var CYTO_HEIGHT = 15;
                    for (var i = 0; i < _cyto.length; i++) {
                        if (i % 2 == 0) {
                            CYTO_COLOR = '#7e7e7e';
                        } else {
                            CYTO_COLOR = "#000";
                        }
                        if (i < _cyto.length - 1) {
                            lineData.push([{
                                name:_cyto[i][0],
                                value:_cyto[i][1],
                                coord: [_cyto[i][0], CYTO_Y],
                                symbol: 'none',
                                tooltip: {
                                    formatter: 'cytoBand:' + _cyto[i][1]
                                },
                                lineStyle: {
                                    normal: {
                                        color: CYTO_COLOR,
                                        width: CYTO_HEIGHT,
                                        type: 'solid'
                                    }
                                }
                            }, {
                                coord: [_cyto[i + 1][0], CYTO_Y],
                                symbol: 'none'
                            }]);
                        } else if (i = _cyto.length - 1) {
                            lineData.push([{
                                name:_cyto[i][0],
                                value:_cyto[i][1],
                                coord: [_cyto[i][0], CYTO_Y],
                                symbol: 'none',
                                tooltip: {
                                    formatter: 'cytoBand:' + _cyto[i][1]
                                },
                                lineStyle: {
                                    normal: {
                                        color: CYTO_COLOR,
                                        width: CYTO_HEIGHT,
                                        type: 'solid'
                                    }
                                }
                            }, {
                                coord: [XMAX, CYTO_Y],
                                symbol: 'none'
                            }]);
                        }
                    }
            }

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
                        coord: [XMIN, c[0]],
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
                        coord: [XMAX, c[0]],
                        symbol: 'arrow'
                    }];
                    lineData.push(line);
                });
            }
            return lineData;
        }
    },
    View: {
        init: function() {
            //初始化配置项ui
            $("#pixelRatio_select").html("");
            for (var i = 1; i < 50; i++) {
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

            var cancer_arr_c = [
                "Acute Myeloid Leukemia [LAML]",
                "Adrenocortical carcinoma [ACC]",
                "Bladder Urothelial Carcinoma [BLCA]",
                "Brain Lower Grade Glioma [LGG]",
                "Breast invasive carcinoma [BRCA]",
                "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESC]",
                // "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESCAD]",
                // "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESCSC]",
                "Cholangiocarcinoma [CHOL]",
                "Colon adenocarcinoma [COAD]",
                "Esophageal carcinoma [ESCA]",
                // "Esophageal carcinoma [ESCAAD]",
                // "Esophageal carcinoma [ESCASC]",
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
                "Uveal Melanoma [UVM]",
                "[All-non-malignant]"
            ];

            var cancer_arr_e = [
                "⚠️Acute Myeloid Leukemia [LAML]",
                "⚠️Adrenocortical carcinoma [ACC]",
                "Bladder Urothelial Carcinoma [BLCA]",
                "⚠️Brain Lower Grade Glioma [LGG]",
                "Breast invasive carcinoma [BRCA]",
                "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESC]",
                // "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESCAD]",
                // "Cervical squamous cell carcinoma and endocervical adenocarcinoma [CESCSC]",
                "Cholangiocarcinoma [CHOL]",
                "Colon adenocarcinoma [COAD]",
                "Esophageal carcinoma [ESCA]",
                // "Esophageal carcinoma [ESCAAD]",
                // "Esophageal carcinoma [ESCASC]",
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
                "⚠️Uveal Melanoma [UVM]",
                "[All-non-malignant]"
            ];

            $("#cancername").html("");

            $("#cancername").append("<option disabled selected value=''>Please select the cancer type.</option>");

            for(var i = 0; i < cancer_arr_c.length; i++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1] + "'>" + cancer_arr_c[i] + "</option>");
            }
            $("#dataType").change(function(){
                var index = this.selectedIndex;
                $("#cancername").html("");
                $("#cancername").append("<option disabled selected value=''>Please select the cancer type.</option>");
                if(index==1){
                    for(var i = 0; i < cancer_arr_e.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername").append("<option class='col-md-12' value='" + cancer_arr_e[i].match(r)[1] + "'>" + cancer_arr_e[i] + "</option>");
                    }
                }else{
                    for(var i = 0; i < cancer_arr_c.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1] + "'>" + cancer_arr_c[i] + "</option>");
                    }
                }
            });
        },
        draw: function(yValueArr, xStart,geneNames,cyto,qPosition,index,DTW,highLightedGene, cancerType, mountainStyle, hignGeneStyle, is_reRender){

            var glist = cancerType.split(',');

            var glist_ex = [];

            if(chromsome.Operate.isContains(cancerType,'all-non-malignant')){

                for(var ww=0;ww<glist.length;ww++){

                    if(glist[ww]=='all-non-malignant'){
                        continue;
                    }
                    glist_ex.push(glist[ww].toUpperCase() + "_T");
                }
            }else {
                for(var www=0;www<glist.length;www++){

                    glist_ex.push(glist[www].toUpperCase() + "_T");
                }


            }

            if(chromsome.Operate.isContains(cancerType,'all-non-malignant')){

                if($("#showType").val() == "mid"){
                    glist_ex.push("all-non-malignant_median");
                }else {
                    glist_ex.push("all-non-malignant_mean");
                }

            }else {
                if($("#showType").val() == "mid"){

                    glist_ex.push("Non-malignant_median");
                }else {
                    glist_ex.push("Non-malignant_mean");
                }
            }


            //获取Y轴名称
            var yAxisName = "";
            switch($("#dataType").val()) {
                case "e":
                {
                    if($("#islog").is(':checked')) {
                        yAxisName = "mRNA Expression Values (log2)";
                    } else {
                        yAxisName = "mRNA Expression Values";
                    }
                }
                    break;
                case "c":
                {
                    if($("#islog").is(':checked')) {
                        yAxisName = "Copy Number Variations (log2)";
                    } else {
                        yAxisName = "Copy Number Variations";
                    }
                }
                    break;
                default:
                {
                    yAxisName: "yAxis";
                }
                    break;
            }


            //页面中的Group,初始化蜂群选择器,填充选择框(只执行一次)
            if (!is_reRender) {
                $("#mountain_which_select").html("");
                for (var i = 0; i < glist_ex.length; i++) {
                    $("#mountain_which_select").append("<option class='col-md-12' value='" + glist_ex[i] + "'>" + glist_ex[i] + "</option>")
                }
            }


            var series = [];
            //初始化图片下载配置结束
            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();


            //样式数组初始化
            if(mountainStyle.length == 0) {
                    //初始像素点大小
                    //初始像素样式
                    var pointStyle = $("#pointStyle_select").val();
                    //将初始配置加入配置数组

                    if(glist_ex.length == 2){

                        mountainStyle[0]=new Array();
                        mountainStyle[0][0] = '#1224fb';//颜色
                        mountainStyle[0][1] = 8;//大小
                        mountainStyle[0][2] = pointStyle;//形状

                        mountainStyle[1]=new Array();
                        mountainStyle[1][0] = '#27FE2E';//颜色
                        mountainStyle[1][1] = 8;//大小
                        mountainStyle[1][2] = pointStyle;//形状

                    }else if (glist_ex.length == 5){
                        mountainStyle[0]=new Array();
                        mountainStyle[0][0] = '#f2944e';//颜色
                        mountainStyle[0][1] = 8;//大小
                        mountainStyle[0][2] = pointStyle;//形状

                        mountainStyle[1]=new Array();
                        mountainStyle[1][0] = '#6f2add';//颜色
                        mountainStyle[1][1] = 8;//大小
                        mountainStyle[1][2] = pointStyle;//形状

                        mountainStyle[2]=new Array();
                        mountainStyle[2][0] = '#f29ab3';//颜色
                        mountainStyle[2][1] = 8;//大小
                        mountainStyle[2][2] = pointStyle;//形状

                        mountainStyle[3]=new Array();
                        mountainStyle[3][0] = '#8b4457';//颜色
                        mountainStyle[3][1] = 8;//大小
                        mountainStyle[3][2] = pointStyle;//形状

                        mountainStyle[4]=new Array();
                        mountainStyle[4][0] = '#27FE2E';//颜色
                        mountainStyle[4][1] = 8;//大小
                        mountainStyle[4][2] = pointStyle;//形状
                    }else {

                        for (var keke=0;keke<glist_ex.length;keke++){
                            mountainStyle[keke]=new Array();
                            mountainStyle[keke][0] = chromsome.Utils.getRandomColor();//颜色
                            mountainStyle[keke][1] = $("#pointSize_select").val();//大小
                            mountainStyle[keke][2] = pointStyle;//形状
                        }
                    }
            }

            if(hignGeneStyle.length == 0) {
                    //初始颜色样式
                    var geneColor = '#ff663a';
                    //高亮基因像素点大小
                    var geneSize = 20;
                    //高亮基因的样式
                    var geneShape = 'pin';
                    //将初始配置加入配置数组
                        hignGeneStyle[0] = geneColor;
                        hignGeneStyle[1] = geneSize;
                        hignGeneStyle[2] = geneShape;
            } else {
                hignGeneStyle[0] = $("#geneColor_select").val();
                hignGeneStyle[1] = $("#GeneSizeStyle_select").val();
                hignGeneStyle[2] = $("#GeneShapeStyle_select").val()
            }
            //样式数组初始化完毕
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
                        // console.log(myChartTwo.series[i]);
                    }
                }
                myChart.setOption(myChartTwo,true);
            });

            //准备高亮基因数据
            if(highLightedGene !=""){
                var highLightedGeneName = highLightedGene.split(",");
            }

            var _highLightedGeneName = [];

            if(highLightedGene != ""){

                for (var kkk = 0; kkk < highLightedGeneName.length; kkk++) {

                    for(var ws=0; ws<yValueArr.length; ws++){

                        for(var wsy=0; wsy < geneNames.length; wsy++){

                            if(geneNames[wsy]==highLightedGeneName[kkk]){

                                var bb=[];
                                bb.push(xStart[wsy]);
                                bb.push(yValueArr[ws][wsy]);
                                bb.push(geneNames[wsy]);
                                _highLightedGeneName.push(bb);

                            }
                        }
                    }

                }
            }

            // console.log("_highLightedGeneName   "+ _highLightedGeneName);
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
                if(YMIN > Number(input_cutoff1)) { //计算y最小
                    YMIN = Number(input_cutoff1);
                }
                if(YMAX < Number(input_cutoff1)) { //计算y最大
                    YMAX = Number(input_cutoff1);
                }
            }
            if(input_cutoff2 != "") {
                cutoff.push(input_cutoff2*1);
                if(YMIN > Number(input_cutoff2)) { //计算y最小
                   YMIN = Number(input_cutoff2);
                }
                if(YMAX < Number(input_cutoff2)) { //计算y最大
                   YMAX = Number(input_cutoff2);
                }
            }

            cutoff=cutoff.sort(sequence);



            var cutoffTo=cutoff[0];

            var XMIN = 100000000000000;
            var XMAX = -100000;
            var YMAX = 4;
            var YMIN = 1;
            var armTo=XMIN+15000000;
            var data=[];
            for(var hah=0;hah < yValueArr.length;hah++){

                var tmpOne=[];

                for(var haha=0;haha<yValueArr[0].length;haha++){

                    var tmp=[];
                    tmp[0]=xStart[haha];
                    tmp[1]=yValueArr[hah][haha];
                    tmp[2]=geneNames[haha];

                    var gene = [];

                    gene.push(xStart[haha]);

                    gene.push(yValueArr[hah][haha]);

                    if (gene[0] > XMAX) {
                        XMAX = gene[0];
                    }
                    if (gene[0] < XMIN) {
                        XMIN = gene[0];
                    }
                    if (gene[1] > YMAX) {
                        YMAX = gene[1];
                    }
                    if (gene[1] < YMIN) {
                        YMIN = gene[1];

                    }

                    tmpOne.push(tmp);
                }

                data.push(tmpOne);


            }



            for(var j=0;j<yValueArr.length;j++){
                var value_list = [];
                var cutoffall = [];
                cutoff.forEach(function(cc) {
                    cutoffall.push([cc, 0]);
                });
                for(var gg=0;gg<yValueArr[j].length;gg++){
                    if(cutoffall.length == 1) {
                        if(yValueArr[j][gg] < cutoff[0]) {
                            cutoffall[0][1]=cutoffall[0][1]+1;
                        }
                    } else if(cutoffall.length == 2) {
                        if(yValueArr[j][gg] < cutoff[0]) {
                            cutoffall[0][1]=cutoffall[0][1]+1;
                        } else if(yValueArr[j][gg] < cutoff[1]) {
                            cutoffall[1][1]=cutoffall[1][1]+1;
                        }
                    }
                    value_list.push(yValueArr[j][gg]);
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

                //准备markPoint数据
                var markPointViewData = chromsome.Utils.tableData(DTW);

                var lineData=chromsome.Utils.lineData(cyto,YMIN,XMAX,XMIN,qPosition,cutoffall);

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


                //console.log(JSON.stringify(lineData)+"   lineData");
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
                    markPoint:{
                        symbol:'none',
                        itemStyle:{
                            normal:{
                                color:"blue"
                            }
                        },
                        data :markPointViewData
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
                symbolSize:hignGeneStyle[1],
                symbol:hignGeneStyle[2],
                itemStyle: {
                    normal: {
                        color:hignGeneStyle[0]
                    }
                }
            };
            series.push(serie_h);

            var option = {
                backgroundColor: '#ffffff',
                title: {
                    text: 'Chromosome: ' + $("#chromosome").val() + "; GeneNumber =" + yValueArr[0].length,
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
                    y: '35%',
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
                tooltip: {
                    trigger:"item",
                    formatter: "{a} <br/> {b} {c}"
                },
                xAxis: [
                    {   gridIndex: 0,
                        min: XMIN-10,
                        max: XMAX+10000000 }
                ],
                yAxis: [
                    {   gridIndex: 0,
                        name: yAxisName,
                        min: Math.floor(YMIN),
                        max: Math.ceil(YMAX)
                    }
                ],
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
                                    '<tr><td align="center" rowspan="2">Scores</td>';
                                //动态生成表头开始
                                for(var i=0;i<series.length-2;i++){
                                    table += '<td align="center" colspan="3">'+series[i].name+'</td>';
                                }
                                table+='</tr>';

                                for(var i=0;i<series.length-2;i++){
                                    table += '<td align="center">P-Arm</td>';
                                    table += '<td align="center">Q-Arm</td>';
                                    table += '<td align="center">Chrom</td>';
                                }
                                table+='</tr>';


                                //动态生成表头结束
                                //动态生成主题
                                var param=['Distance','Absolute','Square','DTW'];
                                var demo=[0,1,2,3];//控制行数
                                for(var ii=0;ii<demo.length;ii++){
                                    var haha=param[ii];
                                    var e=demo[ii];
                                    table +='<tr><td align="center">'+ haha+'</td>';
                                        var argu=series[0].markPoint.data;//癌症个数
                                        for(var vk=0;vk<argu.length;vk++){
                                            table +='<td align="center">'+ argu[vk][e][0].coord[0]+'</td>';
                                            table +='<td align="center">'+ argu[vk][e][0].coord[1]+'</td>';
                                            table +='<td align="center">'+ argu[vk][e][0].coord[2]+'</td>'
                                        }
                                    table +='</tr>';
                                }
                                table +='<tr><td colspan="4" align="center">Gene Values</td></tr>';
                                for(var i=0;i<series.length;i++){
                                    if(series[i].data.length == 0){
                                        continue;
                                    }
                                    table +='<tr><td colspan="2" align="center">'+series[i].name+'</td><td colspan="2" align="center">GeneName</td></tr>';
                                    for(var ii=0;ii<series[i].data.length;ii++){
                                        table += '<tr>'
                                            + '<td colspan="2" align="center">' + series[i].data[ii][1] + '</td>'
                                            + '<td colspan="2" align="center">' + series[i].data[ii][2] + '</td>'+'</tr>';
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
            // console.log(series.length);
            // for (var i = 0; i <= series.length - 1; i++){
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