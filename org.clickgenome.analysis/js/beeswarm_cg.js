var beeswarm = new Object({
    //请求路径
    URL: {
        BASE_URL: function () {
            return "http://54.193.40.253:8080/data/data/";
            // return "http://localhost:8088/data/data/";
        },
        CHECK_URL: function (geneName) {
            return beeswarm.URL.BASE_URL() + geneName + "/" + $("#dataType").val() + "/" + $("#newOrOld").val() + "/gene/check";
        },
        BEESWARM: function (genename, cancertype, mode) {
            var value;
            if ($("#islog").is(':checked')) {
                value = "l";
            } else {
                value = "y";
            }
            if ($("#simple_nonmalignant").is(':checked') && $("#simple_tumor").is(':checked')) {
                return beeswarm.URL.BASE_URL() + genename + "/" + cancertype + "/" + $("#dataType").val() + "/" + $("#newOrOld").val() + "/" + value + "/by" + mode;
            } else if ($("#simple_tumor").is(':checked')) {
                return beeswarm.URL.BASE_URL() + genename + "/" + cancertype + "/" + $("#dataType").val() + "/" + $("#newOrOld").val() + "/" + value + "/by" + mode + "/tumor";
            } else if ($("#simple_nonmalignant").is(':checked')) {
                return beeswarm.URL.BASE_URL() + genename + "/" + cancertype + "/" + $("#dataType").val() + "/" + $("#newOrOld").val() + "/" + value + "/by" + mode + "/nonmalignant";
            } else {
                return beeswarm.URL.BASE_URL() + genename + "/" + cancertype + "/" + $("#dataType").val() + "/" + $("#newOrOld").val() + "/" + value + "/by" + mode;
            }
        },
        TTEST_URL: function () {
            return beeswarm.URL.BASE_URL() + "test";
        }
    },
    Operate: {
        BEESWARM: function (genename, cancertype, mode) {
            if (mode === "gene") {
                if (genename == "" && cancertype == "") {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please input a gene symbol and select the cancertype.</p>');
                    return;
                } else if (genename == "") {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please input a gene symbol.</p>');
                    return;
                } else if (cancertype == "") {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select at least one cancertype.</p>');
                    return;
                } else if (beeswarm.Utils.VerifyInput(genename)) {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check the chromosome, gene symbol or use the correct separation symbol.<br />Chr:Gene (For example: 7:EGFR). </p>');
                    return;
                }
            } else if (mode === "cancer") {
                if (genename == "" && cancertype == "") {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please input gene symbols and select one cancertype.</p>');
                    return;
                } else if (cancertype == "") {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please select one cancertype.</p>');
                    return;
                } else if (genename == "") {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please input gene symbols.</p>');
                    return;
                } else if (beeswarm.Utils.VerifyInput(genename)) {
                    $("#bt_draw").disabled = false;
                    $("#echarts").html(
                        '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌ Please check the chromosomes, gene symbols or use the correct separation symbol.<br />Chr:Genes (For example: 7:EGFR, 3:TP63). </p>');
                    return;
                }
            }
            $("#bt_draw").attr("disabled", "true");
            $.get(beeswarm.URL.CHECK_URL(genename), {}, function (data) {
                //第一个if:对输入的检查
                if (data.data) {
                    $("#echarts").html('<div class="spinner">' +
                        '<div class="rect1"></div>' +
                        '<div class="rect2"></div>' +
                        '<div class="rect3"></div>' +
                        '<div class="rect4"></div>' +
                        '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>Due to the big data size and the internet speed, this may take a while.</p>');
                    //ajax、get请求数据
                    $.get(beeswarm.URL.BEESWARM(genename, cancertype, mode), {}, function (data, status) {
                        if (status === 'success') {
                            if (!data.state) {
                                $("#bt_draw").disabled = false;
                                $("#echarts").html(
                                    '<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌' + data.msg + '</p>');
                                return;
                            }
                            var flag = data.data.flag;
                            var resultData = [];
                            //解析
                            for (key in data.data.points) {
                                var tempData = [];
                                for (value in data.data.points[key]) {
                                    var temp = [];
                                    for (f in data.data.points[key][value]) {
                                        temp.push(data.data.points[key][value][f]);
                                    }
                                    tempData.push(temp);
                                }
                                resultData.push(tempData)
                            }
                            //pointSpread填充
                            var pointSpreadData = beeswarm.Utils.pointSpread(resultData);
                            for (var gg = 0; gg < resultData.length; gg++) {
                                for (var mm = 0; mm < resultData[gg].length; mm++) {
                                    resultData[gg][mm][0] = pointSpreadData[gg][mm] + gg;
                                }
                            }
                            var beesStyle = [];
                            var is_reRender = false;
                            var title = (mode === 'gene') ? genename : cancertype;
                            beeswarm.View.draw(flag, title, resultData, beesStyle, is_reRender);
                        } else {
                            $("#echarts").html('<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">There is something wrong with server~~~</p>');
                        }
                    });
                } else {
                    $("#bt_draw").removeAttr("disabled");
                    $("#echarts").html('<p class="col-md-12" style="text-align:center;color:rgb(61,132,193);margin-top:25%;">❌The gene does not exist in the gene list.</p>');
                }
            });
        }
    },
    //定义一个工具函数
    Utils: {
        //验证输入
        VerifyInput: function (genename) {
            var split = genename.split(",");
            for (var kk = 0; kk < split.length; kk++) {
                var result = split[kk].split(":");
                if (result.length != 2) {
                    return true;
                }
                if (isNaN(result[0]) || result[1] == "" || result[0] == "") {
                    return true;
                }
            }
            return false;
        },
        //打散函数
        pointSpread: function (data) {
            var beeswarmData = [];
            for (var kk = 0; kk < data.length; kk++) {
                var dataset = [];
                for (var t = 0; t < data[kk].length; t++) {
                    dataset.push({
                        'x': undefined,
                        'y': data[kk][t][1]
                    });
                }
                var beeswarmjs = new Beeswarm(dataset, 500, 3);
                var result = beeswarmjs.swarm();
                var intermediateData = [];
                for (var kakaka = 0; kakaka < result.length; kakaka++) {
                    intermediateData.push(result[kakaka].x);
                }
                var finalData = [];
                for (var bjh = 0; bjh < intermediateData.length; bjh++) {
                    finalData.push(beeswarm.Utils.normalize(intermediateData[bjh], Math.max.apply(null, intermediateData), Math.min.apply(null, intermediateData)));
                }
                beeswarmData.push(finalData);
            }
            return beeswarmData;
        },
        //缩放函数
        normalize: function (val, max, min) {
            return 0.75 + (1.25 - 0.75) * (val - min) / (max - min);
        },
        //tableData是markPoint点,为echarts的dataview数据视图做准备
        tableData: function (up, q3, avg, std, mid, q1, down, xmin) {
            var viewData;
            viewData = [
                [{
                    coord: [xmin, beeswarm.Utils.getResult(up, 4)],
                    symbol: 'none'
                }, {
                    coord: [xmin, beeswarm.Utils.getResult(up, 4)],
                    symbol: 'none'
                }],
                [{
                    coord: [xmin, beeswarm.Utils.getResult(q3, 4)],
                    symbol: 'none'
                }, {
                    coord: [xmin, beeswarm.Utils.getResult(q3, 4)],
                    symbol: 'none'
                }],
                [{
                    coord: [xmin, beeswarm.Utils.getResult(mid, 4)],
                    symbol: 'none'
                }, {
                    coord: [xmin, beeswarm.Utils.getResult(mid, 4)],
                    symbol: 'none'
                }],
                [{ //q1
                    coord: [xmin, beeswarm.Utils.getResult(q1, 4)],
                    symbol: 'none'
                }, {
                    coord: [xmin, beeswarm.Utils.getResult(q1, 4)],
                    symbol: 'none'
                }],
                [{ //down
                    coord: [xmin, beeswarm.Utils.getResult(down, 4)],
                    symbol: 'none'
                }, {
                    coord: [xmin, beeswarm.Utils.getResult(down, 4)],
                    symbol: 'none'
                }],
                [{ //avg
                    coord: [xmin, beeswarm.Utils.getResult(avg, 4)],
                    symbol: 'none'
                }, {
                    coord: [xmin, beeswarm.Utils.getResult(avg, 4)],
                    symbol: 'none'
                }],
                [{ //std
                    coord: [xmin, beeswarm.Utils.getResult(std, 4)],
                    symbol: 'none'
                }, {
                    coord: [xmin, beeswarm.Utils.getResult(std, 4)],
                    symbol: 'none'
                }]
            ];
            return viewData;
        },
        //为绘制箱线图和平均值做准备
        box: function (up, q3, avg, std, mid, q1, down, xmin, xmax, cutoffall, Xmax, boxColor, boxStyle, boxWidth, lineColor, lineStyle, lineWidth) {
            var ups = "";
            var q3s = "";
            var avgs = "";
            var mids = "";
            var q1s = "";
            var downs = "";
            var std_1_zhen = "";
            var std_1_fu = "";
            var std_2_zhen = "";
            var std_2_fu = "";
            var std_3_zhen = "";
            var std_3_fu = "";
            if (up != "") {
                ups = "up:" + (up + "").substring(0, 9);
            }
            if (q3 != "") {
                q3s = "q3:" + (q3 + "").substring(0, 9);
            }
            if (avg != "") {
                avgs = "mean:" + (avg + "").substring(0, 9);
            }
            if (mid != "") {
                mids = "mid:" + (mid + "").substring(0, 9);
            }
            if (q1 != "") {
                q1s = "q1:" + (q1 + "").substring(0, 9);
            }
            if (down != "") {
                downs = "down:" + (down + "").substring(0, 9);
            }
            if (std != "") {
                std_1_zhen = "mean+1std:" + (avg + std + "").substring(0, 9);
                std_1_fu = "mean-1std:" + (avg - std + "").substring(0, 9);
                std_2_zhen = "mean+2std:" + (avg + 2 * std + "").substring(0, 9);
                std_2_fu = "mean-2std:" + (avg - 2 * std + "").substring(0, 9);
                std_3_zhen = "mean+3std:" + (avg + 3 * std + "").substring(0, 9);
                std_3_fu = "mean-3std:" + (avg - 3 * std + "").substring(0, 9);
            }
            var data_;
            if ($("#boxplot").is(':checked')) {
                data_ = [
                    //上离群点
                    [{
                        coord: [xmin, up],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [xmax, up],
                        symbol: 'none'
                    }],
                    //上离群点和四分三分位数中间的那条线
                    [{
                        coord: [(xmin + xmax) / 2, up],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }

                    }, {
                        coord: [(xmin + xmax) / 2, q3],
                        symbol: 'none'
                    }],
                    //四分之三分位数
                    [{
                        coord: [xmin, q3],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [xmax, q3],
                        symbol: 'none'
                    }],
                    //四分之三分位数左侧的那条线
                    [{
                        coord: [xmin, q3],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [xmin, q1],
                        symbol: 'none'
                    }],
                    //四分之三分位数右侧的那条线
                    [{
                        coord: [xmax, q3],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [xmax, q1],
                        symbol: 'none'
                    }],
                    //中位数的那条线
                    [{
                        coord: [xmin, mid],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [xmax, mid],
                        symbol: 'none'
                    }],
                    //四分之一分位数
                    [{
                        coord: [xmin, q1],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [xmax, q1],
                        symbol: 'none'
                    }],
                    //下离群点和四分一分位数中间的那条线
                    [{
                        coord: [(xmin + xmax) / 2, q1],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [(xmin + xmax) / 2, down],
                        symbol: 'none'
                    }],
                    //下离群点
                    [{
                        coord: [xmin, down],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: boxColor,
                                width: boxWidth,
                                type: boxStyle
                            }
                        }
                    }, {
                        coord: [xmax, down],
                        symbol: 'none'
                    }]
                ];
            } else {
                data_ = [];
            }
            if ($("#mean").is(':checked')) {
                data_.push(
                    [{
                        coord: [xmax, avg],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: lineColor,
                                width: lineWidth,
                                type: lineStyle
                            }
                        },
                        label: {
                            normal: {
                                show: false,
                                formatter: avgs,
                                textStyle: {
                                    align: 'left'
                                }
                            }
                        },
                        tooltip: {
                            formatter: 'mean:' + avg
                        }
                    }, {
                        coord: [xmin, avg],
                        symbol: 'none'
                    }]
                );
            }
            if ($("#mean1").is(':checked')) {
                data_.push(
                    [{
                        coord: [xmax, avg + std],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: lineColor,
                                width: lineWidth,
                                type: lineStyle
                            }
                        },
                        label: {
                            normal: {
                                show: false,
                                formatter: std_1_zhen,
                                textStyle: {
                                    align: 'left'
                                }
                            }
                        },
                        tooltip: {
                            formatter: std_1_zhen
                        }
                    }, {
                        coord: [xmin, avg + std],
                        symbol: 'none'
                    }]
                );
                data_.push([{
                    coord: [xmax, avg - std],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: lineColor,
                            width: lineWidth,
                            type: lineStyle
                        }
                    },
                    label: {
                        normal: {
                            show: false,
                            formatter: std_1_fu,
                            textStyle: {
                                align: 'left'
                            }
                        }
                    },
                    tooltip: {
                        formatter: std_1_fu
                    }
                }, {
                    coord: [xmin, avg - std],
                    symbol: 'none'
                }]);
            }
            if ($("#mean2").is(':checked')) {
                data_.push(
                    [{
                        coord: [xmax, avg + 2 * std],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: lineColor,
                                width: lineWidth,
                                type: lineStyle
                            }
                        },
                        label: {
                            normal: {
                                show: false,
                                formatter: std_2_zhen,
                                textStyle: {
                                    align: 'left'
                                }
                            }
                        },
                        tooltip: {
                            formatter: std_2_zhen
                        }
                    }, {
                        coord: [xmin, avg + 2 * std],
                        symbol: 'none'
                    }]
                );
                data_.push([{
                    coord: [xmax, avg - 2 * std],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: lineColor,
                            width: lineWidth,
                            type: lineStyle
                        }
                    },
                    label: {
                        normal: {
                            show: false,
                            formatter: std_2_fu,
                            textStyle: {
                                align: 'left'
                            }
                        }
                    },
                    tooltip: {
                        formatter: std_2_fu
                    }
                }, {
                    coord: [xmin, avg - 2 * std],
                    symbol: 'none'
                }]);
            }
            if ($("#mean3").is(':checked')) {
                data_.push(
                    [{
                        coord: [xmax, avg + 3 * std],
                        symbol: 'none',
                        lineStyle: {
                            normal: {
                                color: lineColor,
                                width: lineWidth,
                                type: lineStyle
                            }
                        },
                        label: {
                            normal: {
                                show: false,
                                formatter: std_3_zhen,
                                textStyle: {
                                    align: 'left'
                                }
                            }
                        },
                        tooltip: {
                            formatter: std_3_zhen
                        }
                    }, {
                        coord: [xmin, avg + 3 * std],
                        symbol: 'none'
                    }]
                );
                data_.push([{
                    coord: [xmax, avg - 3 * std],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: lineColor,
                            width: lineWidth,
                            type: lineStyle
                        }
                    },
                    label: {
                        normal: {
                            show: false,
                            formatter: std_3_fu,
                            textStyle: {
                                align: 'left'
                            }
                        }
                    },
                    tooltip: {
                        formatter: std_3_fu
                    }
                }, {
                    coord: [xmin, avg - 3 * std],
                    symbol: 'none'
                }]);
            }
            //准备cutoff数据
            cutoffall.forEach(function (c) {
                var line = [{
                    coord: [0, c[0]],
                    symbol: 'none',
                    lineStyle: {
                        normal: {
                            color: '#000',
                            width: 1,
                            type: 'solid'
                        }
                    }
                }, {
                    coord: [Xmax, c[0]],
                    symbol: 'none'
                }];
                data_.push(line);
            });
            return data_;
        },
        //定义做t检验的两组数据
        getTestData: function (data, gene1, gene2) {
            //console.log(data+'hahah');
            var testlist;
            var list = "";
            var list1 = "";
            var list2 = "";
            if ((parseInt(gene1) - 1) <= (data.length - 1) && (parseInt(gene1) - 1) >= 0 && (parseInt(gene2) - 1) <= (data.length - 1) && (parseInt(gene2) - 1) >= 0) {
                for (var gj = 0; gj < data[parseInt(gene1) - 1].length; gj++) {
                    if (isNaN(data[parseInt(gene1) - 1][gj][1])) {
                        continue;
                    }
                    list1 = list1 + data[parseInt(gene1) - 1][gj][1].toString() + ","
                }
                list1 = list1.substring(0, list1.length - 1);
                list = list1 + "|";
                for (var ggj = 0; ggj < data[parseInt(gene2) - 1].length; ggj++) {

                    if (isNaN(data[parseInt(gene2) - 1][ggj][1])) {
                        continue;
                    }
                    list2 = list2 + data[parseInt(gene2) - 1][ggj][1].toString() + ","
                }
                list2 = list2.substring(0, list2.length - 1);

                list = list + list2;
                testlist = {testList: list};
                //var testData = JSON.stringify({testList:list});
                //var testData = {testList:list};
                console.log(JSON.stringify(testlist));
                return testlist;
            }
        },
        //定义t检验的函数,
        ttest: function (data, gene1, gene2) {
            var testData_ = beeswarm.Utils.getTestData(data, gene1, gene2);
            //json序列化
            //var jsonObj=json.stringify(testlist);
            var result;
            $.ajax({
                url: beeswarm.URL.TTEST_URL(),
                async: false,
                type: "post",
                dataType: "json",
                //contentType: "application/json; charset=utf-8",
                data: testData_,
                success: function (data) {
                    //result = data.data;
                    //console.log(data+"t检验");
                    for (var tt in data) {
                        console.log(data[tt] + "tt检验结果");
                        result = data[tt].toExponential(2);
                    }
                }
            });
            return result;
        },

        //在群名称中查找target
        search: function (src, target) {
            for (var i = 0; i < src.length; i++) {
                if (src[i] == target) {
                    return i;
                }
            }
        },
        //定义一个求四舍五入到num后面的n位的函数
        getResult: function (value, n) {
            return Math.round(value * Math.pow(10, n)) / Math.pow(10, n);
        },
        //定义求平均数的函数
        getMean: function (valueList) {
            var sum = 0;
            for (var i = 0; i < valueList.length; i++) {
                sum += valueList[i];
            }
            var mean = sum / valueList.length;
            return mean;
        },
        //定义求标准差的函数
        getVar: function (valueList) {
            var devAll = 0;
            var mean = beeswarm.Utils.getMean(valueList);
            for (var i = 0; i < valueList.length; i++) {
                var cha = valueList[i] - mean;
                devAll += cha * cha;
            }
            var dev = Math.sqrt(devAll / valueList.length);
            return dev;
        }
    },
    //echarts画图
    View: {
        init: function () {
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
            $("#pointSize_select").append("<option class='col-md-12' value='5'>5</option>");
            for (var i = 1; i < 20; i++) {
                $("#pointSize_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
            }

            $("#pointColor_select").html("");
            $("#boxColor_select").html("");
            $("#boxStyle_select").html("");
            $("#box_lineWidth_select").html("");
            $("#lineColor_select").html("");
            $("#lineStyle_select").html("");
            $("#mean_lineWidth_select").html("");
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
                $("#boxColor_select").append("<option class='col-md-12' value='" + color_arr[i] + "'>" + color_arr[i] + "</option>");
                $("#lineColor_select").append("<option class='col-md-12' value='" + color_arr[i] + "'>" + color_arr[i] + "</option>");
            }
            /**/
            $("#boxStyle_select").append("<option class='col-md-12' value='solid'>solid</option>");
            $("#boxStyle_select").append("<option class='col-md-12' value='dashed'>dashed</option>");
            $("#boxStyle_select").append("<option class='col-md-12' value='dotted'>dotted</option>");
            $("#boxStyle_select").append("<option class='col-md-12' value='double'>double</option>");
            $("#boxStyle_select").append("<option class='col-md-12' value='groove'>groove</option>");
            $("#boxStyle_select").append("<option class='col-md-12' value='ridge'>ridge</option>");
            $("#boxStyle_select").append("<option class='col-md-12' value='inset'>inset</option>");
            $("#boxStyle_select").append("<option class='col-md-12' value='outset'>outset</option>");
            /**/
            $("#lineStyle_select").append("<option class='col-md-12' value='solid'>solid</option>");
            $("#lineStyle_select").append("<option class='col-md-12' value='dashed'>dashed</option>");
            $("#lineStyle_select").append("<option class='col-md-12' value='dotted'>dotted</option>");
            $("#lineStyle_select").append("<option class='col-md-12' value='double'>double</option>");
            $("#lineStyle_select").append("<option class='col-md-12' value='groove'>groove</option>");
            $("#lineStyle_select").append("<option class='col-md-12' value='ridge'>ridge</option>");
            $("#lineStyle_select").append("<option class='col-md-12' value='inset'>inset</option>");
            $("#lineStyle_select").append("<option class='col-md-12' value='outset'>outset</option>");
            /**/
            for (var i = 1; i <= 10; i++) {
                $("#mean_lineWidth_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
            }
            for (var i = 1; i <= 10; i++) {
                $("#box_lineWidth_select").append("<option class='col-md-12' value='" + i + "'>" + i + "</option>");
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
            /**/ //dlbc acc laml meso ov tgct ucs uvm无正常样本
            $("#cancername").html("");
            $("#cancername").append("<option disabled selected value=''>Please select the cancer type.</option>");
            /**/
            for (var i = 0; i < cancer_arr_c.length; i++) {
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                $("#cancername").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[i] + "</option>");
            }
            $("#dataType").change(function () {
                var index = this.selectedIndex;
                $("#cancername").html("");
                $("#cancername").append("<option disabled selected value=''>Please select the cancer type.</option>");
                if (index == 0) {
                    for (var i = 0; i < cancer_arr_c.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername").append("<option class='col-md-12' value='" + cancer_arr_c[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_c[i] + "</option>");
                    }
                } else {
                    for (var i = 0; i < cancer_arr_e.length; i++) {
                        var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                        $("#cancername").append("<option class='col-md-12' value='" + cancer_arr_e[i].match(r)[1].toLowerCase() + "'>" + cancer_arr_e[i] + "</option>");
                    }
                }
            });
        },
        draw: function (flag, title, data, beesStyle, is_reRender) {
            $("#echarts").html('<div class="spinner">' +
                '<div class="rect1"></div>' +
                '<div class="rect2"></div>' +
                '<div class="rect3"></div>' +
                '<div class="rect4"></div>' +
                '<div class="rect5"></div>' +
                '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading... </br>Due to the big data size and the internet speed, this may take a while.</p>');
            var ttest_gene1 = $("#gene1").val();
            var ttest_gene2 = $("#gene2").val();
            var t_value = "";
            if (ttest_gene1 != "" & ttest_gene2 != "") {
                t_value = beeswarm.Utils.ttest(data, ttest_gene1, ttest_gene2);
            }
            //实时订制X轴的坐标 Xmax是最大值，Xmin是最小值
            var Xmax = flag.length + 1;
            //Ymax是最大值,Ymin是最小值
            var Ymax = 0;
            var Ymin = 20;
            //获取Y轴名称
            var yAxisName = "";
            switch ($("#dataType").val()) {
                case "e": {
                    if ($("#islog").is(':checked')) {
                        yAxisName = "mRNA Expression Values (log2)";
                    } else {
                        yAxisName = "mRNA Expression Values";
                    }
                }
                    break;
                case "c": {
                    if ($("#islog").is(':checked')) {
                        yAxisName = "Copy Number Variations (log2)";
                    } else {
                        yAxisName = "Copy Number Variations";
                    }
                }
                    break;
                default: {
                    yAxisName: "yAxis";
                }
                    break;

            }
            var glist_ex_ = flag;
            //页面中的Group,初始化蜂群选择器,填充选择框
            if (!is_reRender) {
                $("#bees_which_select").html("");
                for (var i = 0; i < glist_ex_.length; i++) {
                    $("#bees_which_select").append("<option class='col-md-12' value='" + glist_ex_[i] + "'>" + glist_ex_[i] + "</option>")
                }
            }
            var series = [];
            /**初始化图片下载配置*/
            var saveType = $("#saveType_select").val();
            var pixelRatio = $("#pixelRatio_select").val();

            //样式数组初始化
            if (beesStyle.length == 0) {
                for (var i = 0; i < glist_ex_.length; i++) {
                    //判断颜色
                    var color;
                    // if (i % 2 == 0) {
                    //     color = $("#pointColor_select").val();
                    // } else {
                    //     color = $("#pointColor_select").val();
                    // }
                    //初始像素点大小
                    var pointSize = $("#pointSize_select").val();
                    //初始像素样式
                    var pointStyle = $("#pointStyle_select").val();
                    //箱线图颜色
                    var boxColor = $("#boxColor_select").val();
                    //箱线图样式
                    var boxStyle = $("#boxStyle_select").val();
                    //箱线图线宽
                    var boxWidth = $("#box_lineWidth_select").val();
                    //线条颜色
                    var lineColor = $("#lineColor_select").val();
                    //线条样式
                    var lineStyle = $("#lineStyle_select").val();
                    //线条宽度
                    var lineWidth = $("#mean_lineWidth_select").val();
                    //将初始配置加入配置数组
                    for (var keke = 0; keke < glist_ex_.length; keke++) {
                        beesStyle[keke] = new Array();
                        if (keke % 2 == 0) {
                            beesStyle[keke][0] = '#27FE2E';
                        } else {
                            beesStyle[keke][0] = '#fe2127';
                        }
                        beesStyle[keke][1] = pointSize;
                        beesStyle[keke][2] = pointStyle;
                        beesStyle[keke][3] = boxColor;
                        beesStyle[keke][4] = boxStyle;
                        beesStyle[keke][5] = boxWidth * 1;
                        beesStyle[keke][6] = lineColor;
                        beesStyle[keke][7] = lineStyle;
                        beesStyle[keke][8] = lineWidth * 1;
                    }
                }
            } else {
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][0] = $("#pointColor_select").val();
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][1] = $("#pointSize_select").val();
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][2] = $("#pointStyle_select").val();
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][3] = $("#boxColor_select").val();
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][4] = $("#boxStyle_select").val();
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][5] = $("#box_lineWidth_select").val() * 1;
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][6] = $("#lineColor_select").val();
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][7] = $("#lineStyle_select").val();
                beesStyle[beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val())][8] = $("#mean_lineWidth_select").val() * 1;
            }
            /**样式数组初始化完毕*/
            /**监听重新渲染按钮 重新渲染*/
            $("#btn_Rerender").click(function () {
                is_reRender = true;
                var myChartTwo = myChart.getOption();
                myChartTwo.toolbox[0].feature.saveAsImage.type = $("#saveType_select").val();
                myChartTwo.toolbox[0].feature.pixelRatio = $("#pixelRatio_select").val();
                //总共几个图
                var i = beeswarm.Utils.search(glist_ex_, $("#bees_which_select").val());
                if (myChartTwo.series[i].name == $("#bees_which_select").val()) {
                    myChartTwo.series[i].itemStyle.normal.color = $("#pointColor_select").val();
                    myChartTwo.series[i].symbol = $("#pointStyle_select").val();
                    myChartTwo.series[i].symbolSize = $("#pointSize_select").val();
                }
                if ($("#boxplot").is(':checked')) {//选中了箱线图，是否选中mean需要再进行判断
                    for (var wsy = 0; wsy < myChartTwo.series[i].markLine.data.length; wsy++) {
                        myChartTwo.series[i].markLine.data[wsy][0].lineStyle.normal.color = $("#boxColor_select").val();
                        myChartTwo.series[i].markLine.data[wsy][0].lineStyle.normal.type = $("#boxStyle_select").val();
                        myChartTwo.series[i].markLine.data[wsy][0].lineStyle.normal.width = $("#box_lineWidth_select").val() * 1;
                    }
                    if ($("#mean").is(':checked') || $("#mean1").is(':checked') || $("#mean2").is(':checked') || $("#mean3").is(':checked')) {
                        for (var bjh = 9; bjh < myChartTwo.series[i].markLine.data.length; bjh++) {
                            myChartTwo.series[i].markLine.data[bjh][0].lineStyle.normal.color = $("#lineColor_select").val();
                            myChartTwo.series[i].markLine.data[bjh][0].lineStyle.normal.type = $("#lineStyle_select").val();
                            myChartTwo.series[i].markLine.data[bjh][0].lineStyle.normal.width = $("#mean_lineWidth_select").val() * 1;
                        }
                    }
                } else {//没有选择箱线图，选中了mean
                    if ($("#mean").is(':checked') || $("#mean1").is(':checked') || $("#mean2").is(':checked') || $("#mean3").is(':checked')) {
                        for (var wsy2 = 0; wsy2 < myChartTwo.series[i].markLine.data.length; wsy2++) {
                            myChartTwo.series[i].markLine.data[wsy2][0].lineStyle.normal.color = $("#lineColor_select").val();
                            myChartTwo.series[i].markLine.data[wsy2][0].lineStyle.normal.type = $("#lineStyle_select").val();
                            myChartTwo.series[i].markLine.data[wsy2][0].lineStyle.normal.width = $("#mean_lineWidth_select").val() * 1;
                        }
                    }
                }
                myChart.setOption(myChartTwo, true);
            });

            //自定义从小到大的排序函数与sort合起来用
            function sequence(a, b) {
                if (a > b) {
                    return 1;
                } else if (a < b) {
                    return -1
                } else {
                    return 0;
                }
            }

            var input_cutoff1 = $("#line1").val();
            var input_cutoff2 = $("#line2").val();
            var cutoff = [];
            if (input_cutoff1 != "") {
                cutoff.push(input_cutoff1 * 1);
                if (Ymin > Number(input_cutoff1)) { //计算y最小
                    Ymin = Number(input_cutoff1);
                }
                if (Ymax < Number(input_cutoff1)) { //计算y最大
                    Ymax = Number(input_cutoff1);
                }
            }
            if (input_cutoff2 != "") {
                cutoff.push(input_cutoff2 * 1);
                if (Ymin > Number(input_cutoff2)) { //计算y最小
                    Ymin = Number(input_cutoff2);
                }
                if (Ymax < Number(input_cutoff2)) { //计算y最大
                    Ymax = Number(input_cutoff2);
                }
            }
            cutoff = cutoff.sort(sequence);
            //从这里开始遍历data数组
            if (data.length != 0) {
                for (var dd = 0; dd < data.length; dd++) {
                    var cutoffall = [];
                    //forEach()方法用于调用数组的每个元素，并将元素传递给回调函数。forEach()对于空数组是不会执行回调函数的。
                    cutoff.forEach(function (cc) {
                        cutoffall.push([cc, 0]);
                    });
                    var xmin = 10000;
                    var xmax = -10000;
                    var ymin = 20000;
                    var ymax = -20000;
                    var q1Index = 0;
                    var q2Index = 0;
                    var q3Index = 0;
                    var upIndex = 0;
                    var downIndex = 0;
                    var q1 = 0;
                    var mid = 0;
                    var q3 = 0;
                    var up = 0;
                    var down = 0;
                    var value_list = [];
                    var value_list_order = [];
                    for (var qz = 0; qz < data[dd].length; qz++) {
                        //这个的xmin xmax ymin ymax 是为了cutoff的显示做准备
                        if (xmin > data[dd][qz][0]) {
                            xmin = data[dd][qz][0];
                        }
                        if (xmax < data[dd][qz][0]) {
                            xmax = data[dd][qz][0];
                        }
                        if (ymin > data[dd][qz][1]) {
                            ymin = data[dd][qz][1];
                        }
                        if (ymax < data[dd][qz][1]) {
                            ymax = data[dd][qz][1];
                        }
                        value_list.push(data[dd][qz][1]);
                        if (cutoffall.length == 1) {
                            if (data[dd][qz][1] < cutoff[0]) {
                                cutoffall[0][1] = cutoffall[0][1] + 1;
                            }
                        } else if (cutoffall.length == 2) {
                            if (data[dd][qz][1] < cutoff[0]) {
                                cutoffall[0][1] = cutoffall[0][1] + 1;
                            } else if (data[dd][qz][1] < cutoff[1] && data[dd][qz][1] > cutoff[0]) {
                                cutoffall[1][1] = cutoffall[1][1] + 1;
                            }
                        }
                    }
                    value_list_order = value_list.sort(sequence); //把每一坨点的y值排序
                    var avg = beeswarm.Utils.getMean(value_list);
                    var std = beeswarm.Utils.getVar(value_list);
                    //cutoff统计百分比
                    if (cutoffall.length == 2) {
                        cutoffall[0][1] = (Math.round(((cutoffall[0][1] / value_list.length) * 10000))) / 100;
                        cutoffall[1][1] = (Math.round((cutoffall[1][1] / value_list.length) * 10000)) / 100;
                    }

                    if (cutoffall.length == 1) {
                        cutoffall.forEach(function (cuu) {
                            cuu[1] = Math.round((cuu[1] / value_list.length) * 10000);//计算个数的所占的百分比
                            cuu[1] = cuu[1] / 100;
                        });
                    }
                    //四分位数开始
                    //定义一个函数:用于判断一个数字是否为整数
                    function isInteger(obj) {
                        return Math.floor(obj) == obj
                    }

                    q1Index = (value_list_order.length) * (25 / 100);
                    q2Index = (value_list_order.length) * (50 / 100);
                    q3Index = (value_list_order.length) * (75 / 100);
                    upIndex = (value_list_order.length) * (91 / 100);
                    downIndex = (value_list_order.length) * (9 / 100);

                    //定义四分位数的函数
                    function quartile(index) {
                        if (value_list_order.length > 0) {
                            if (isInteger(index)) {
                                return (1 / 2) * (value_list_order[index - 1] + value_list_order[index]);
                            } else {
                                return value_list_order[Math.ceil(index) - 1];
                            }
                        }
                    }

                    q1 = quartile(q1Index);
                    mid = quartile(q2Index);
                    q3 = quartile(q3Index);
                    up = quartile(upIndex);
                    down = quartile(downIndex);
                    // 四分位数结束
                    if (up > Ymax) {
                        Ymax = up;
                    }
                    if (down < Ymin) {
                        Ymin = down;
                    }
                    var linedata = [];
                    var tabledata = [];
                    if ($("#boxplot").is(':checked')) {
                        if ($("#mean").is(':checked') || $("#mean1").is(':checked') || $("#mean2").is(':checked') || $("#mean3").is(':checked')) {
                            linedata = beeswarm.Utils.box(up, q3, avg, std, mid, q1, down, xmin, xmax, cutoffall, Xmax, beesStyle[dd][3], beesStyle[dd][4], beesStyle[dd][5], beesStyle[dd][6], beesStyle[dd][7], beesStyle[dd][8]);
                            tabledata = beeswarm.Utils.tableData(up, q3, avg, std, mid, q1, down, xmin);
                        } else {
                            linedata = beeswarm.Utils.box(up, q3, "", std, mid, q1, down, xmin, xmax, cutoffall, Xmax, beesStyle[dd][3], beesStyle[dd][4], beesStyle[dd][5], beesStyle[dd][6], beesStyle[dd][7], beesStyle[dd][8]);
                            tabledata = beeswarm.Utils.tableData(up, q3, avg, std, mid, q1, down, xmin);
                        }
                    } else {
                        linedata = beeswarm.Utils.box("", "", avg, std, "", "", "", xmin, xmax, cutoffall, Xmax, "", "", "", beesStyle[dd][6], beesStyle[dd][7], beesStyle[dd][8]);
                        tabledata = beeswarm.Utils.tableData(up, q3, avg, std, mid, q1, down, xmin);
                    }
                    //构造cutoff百分比
                    if (cutoffall.length == 1) {
                        var line1 = [{ //up
                            coord: [xmax + 0.2, ymax - (ymax - cutoffall[0][0]) / 2],
                            symbol: 'none',
                            label: {
                                normal: {
                                    formatter: ("" + (100 - cutoffall[0][1])).substring(0, 5) + "%",
                                    textStyle: {
                                        align: 'center',
                                        color: "#000000"
                                    }
                                }
                            },
                            lineStyle: {
                                normal: {
                                    color: '#000',
                                    width: 1,
                                    type: 'solid'
                                }
                            }
                        }, {
                            coord: [xmax + 0.2, ymax - (ymax - cutoffall[0][0]) / 2],
                            symbol: 'none'
                        }];
                        var line2 = [{ //down
                            coord: [xmax + 0.2, cutoffall[0][0] - (cutoffall[0][0] - ymin) / 2],
                            symbol: 'none',
                            label: {
                                normal: {
                                    formatter: ("" + cutoffall[0][1]).substring(0, 5) + "%",
                                    textStyle: {
                                        align: 'center',
                                        color: "#000000"
                                    }
                                }
                            },
                            lineStyle: {
                                normal: {
                                    color: '#000',
                                    width: 1,
                                    type: 'solid'
                                }
                            }
                        }, {
                            coord: [xmax + 0.2, cutoffall[0][0] - (cutoffall[0][0] - ymin) / 2],
                            symbol: 'none'
                        }];
                        linedata.push(line1);
                        linedata.push(line2);
                    }
                    if (cutoffall.length == 2) {
                        var line1 = [{ //up
                            coord: [xmax + 0.2, ymax - (ymax - cutoffall[1][0]) / 2],
                            symbol: 'none',
                            label: {
                                normal: {
                                    formatter: ("" + (Math.round((100 - cutoffall[0][1] - cutoffall[1][1]) * 100) / 100)) + "%",
                                    textStyle: {
                                        align: 'center',
                                        color: "#000000"
                                    }
                                }
                            },
                            lineStyle: {
                                normal: {
                                    color: '#000',
                                    width: 1,
                                    type: 'solid'
                                }
                            }
                        }, {
                            coord: [xmax + 0.2, ymax - (ymax - cutoffall[1][0]) / 2],
                            symbol: 'none'
                        }];
                        var line2 = [{ //center
                            coord: [xmax + 0.2, cutoffall[1][0] - (cutoffall[1][0] - cutoffall[0][0]) / 2],
                            symbol: 'none',
                            label: {
                                normal: {
                                    formatter: ("" + cutoffall[1][1]).substring(0, 5) + "%",
                                    textStyle: {
                                        align: 'center',
                                        color: "#000000"
                                    }
                                }
                            },
                            lineStyle: {
                                normal: {
                                    color: '#000',
                                    width: 1,
                                    type: 'solid'
                                }
                            }
                        }, {
                            coord: [xmax + 0.2, cutoffall[1][0] - (cutoffall[1][0] - cutoffall[0][0]) / 2],
                            symbol: 'none'
                        }];
                        var line3 = [{ //down
                            coord: [xmax + 0.2, cutoffall[0][0] - (cutoffall[0][0] - ymin) / 2],
                            symbol: 'none',
                            label: {
                                normal: {
                                    formatter: ("" + (cutoffall[0][1])).substring(0, 5) + "%",
                                    textStyle: {
                                        align: 'center',
                                        color: "#000000"
                                    }
                                }
                            },
                            lineStyle: {
                                normal: {
                                    color: '#000',
                                    width: 1,
                                    type: 'solid'
                                }
                            }
                        }, {
                            coord: [xmax + 0.2, cutoffall[0][0] - (cutoffall[0][0] - ymin) / 2],
                            symbol: 'none'
                        }];
                        linedata.push(line1);
                        linedata.push(line2);
                        linedata.push(line3);
                    }
                    //构造绘制数据组(散点图)
                    var serie = {
                        name: glist_ex_[dd],
                        type: 'scatter',
                        xAxisIndex: 0,
                        yAxisIndex: 0,
                        data: data[dd],
                        itemStyle: {
                            normal: {
                                color: beesStyle[dd][0] //    颜色配置，读取beesStyle配置数组
                            }
                        },
                        symbol: beesStyle[dd][2], //   点形状配置，读取beesStyle配置数组
                        symbolSize: beesStyle[dd][1], //  点大小配置，读取beesStyle配置数组
                        markLine: {
                            label: {
                                normal: {
                                    formatter: '',
                                    textStyle: {
                                        color: '#fff',
                                        fontStyle: 'normal',
                                        fontSize: 11,
                                        align: 'right'
                                    }
                                }
                            },
                            lineStyle: {
                                normal: {
                                    color: "#000",
                                    type: 'solid'
                                }
                            },
                            data: linedata
                        },
                        markPoint: {
                            data: tabledata
                        }
                    };
                    series.push(serie);

                    if (ymax > Ymax) {
                        Ymax = ymax;
                    }
                    if (ymin < Ymin) {
                        Ymin = ymin;
                    }
                }
            }
            var ttest_title = (ttest_gene1 != "" & ttest_gene2 != "") ? "          T-Test of " + glist_ex_[parseInt(ttest_gene1) - 1] + " and " + glist_ex_[parseInt(ttest_gene2) - 1] + " is " + t_value : "";
            var option = {
                title: {
                    text: title.toUpperCase() + ttest_title,
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
                    top: '7%',
                    data: glist_ex_,
                    padding: 3,
                    orient: 'horizontal',
                    align: 'left',
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
                            name: title,
                            type: saveType,
                            pixelRatio: pixelRatio
                        },
                        dataView: {
                            show: true,
                            title: "Data table",
                            readOnly: true,
                            lang: ["Data table", "Close"],
                            buttonColor: '#748aff',
                            optionToContent: function (opt) {
                                //console.log('opt'+opt);
                                var series = opt.series;
                                //在此处把四分位数和均值的函数封装好，把该算的东西用数组封装好。
                                //重新开始
                                var table = '<div style="width: 100%;height: 100%;overflow: auto;user-select: all;">' +
                                    '<table width="100%" border="1" cellspacing="0" cellpadding="0"><tbody>' +
                                    '<tr><td align="center">Statistics</td>';
                                //动态生成表头开始
                                for (var i = 0; i < series.length; i++) {
                                    if (series[i].data.length == 0) {
                                        continue;
                                    }
                                    table += '<td align="center">' + series[i].name + '</td>';
                                }
                                table += '</tr>';
                                //动态生成表头结束
                                //动态生成box主题
                                var param = ['Upper Extreme', 'Upper Quartile', 'Median', 'Lower Quartile', 'Lower Extreme', 'mean', 'STD'];
                                var demo = [0, 1, 2, 3, 4, 5, 6];
                                for (var ii = 0; ii < demo.length; ii++) {
                                    var haha = param[ii];
                                    var e = demo[ii];
                                    table += '<tr><td align="center">' + haha + '</td>';
                                    for (var i = 0; i < series.length; i++) {
                                        if (series[i].data.length == 0) {
                                            continue;
                                        }
                                        var argu = series[i].markPoint.data[e];
                                        table += '<td align="center">' + argu[0].coord[1] + '</td>'
                                    }
                                    table += '</tr>';
                                }
                                table += '<tr><td colspan="4" align="center">Sample Values</td></tr>';
                                for (var i = 0; i < series.length; i++) {
                                    if (series[i].data.length == 0) {
                                        continue;
                                    }
                                    table += '<tr><td colspan="2" align="center">' + series[i].name + '</td><td colspan="2" align="center">SampleID</td></tr>';
                                    for (var ii = 0; ii < series[i].data.length; ii++) {
                                        table += '<tr>'
                                            + '<td colspan="2" align="center">' + series[i].data[ii][1] + '</td>'
                                            + '<td colspan="2" align="center">' + series[i].data[ii][2] + '</td>' + '</tr>';
                                    }
                                }
                                table += '</tbody></table></div>';
                                return table;
                            }
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
                    orient: "vertical",
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
                    // padding: 10,
                    // backgroundColor: '#777',
                    // borderColor: '#777',
                    //
                    trigger: "item",
                    // formatter: function(obj) {
                    // 	var value = obj.value;
                    // 	console.log(value);
                    // 	return '<div style="border-bottom: 1px solid rgba(255,255,255,.3); font-size: 18px;padding-bottom: 7px;margin-bottom: 7px">' +
                    // 		obj.seriesName + '  ' + value[2] +
                    // 		'</div>' +
                    // 		'value: ' + value[1] + '<br>';
                    // }
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
                    min: 0,
                    max: Xmax
                }],
                yAxis: [{
                    gridIndex: 0,
                    name: yAxisName,
                    min: Math.floor(-(Ymax - Ymin) * 0.1) - 1,
                    max: Math.ceil(Ymax) + 3
                }],
                series: series
            };
            // for (var i = 0; i <= series.length - 1; i++){
            //      console.log(series[i].markLine.data);
            // }
            // 基于准备好的dom，初始化echarts实例
            var myChart = echarts.init(document.getElementById('echarts'));
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
            $("#bt_draw").removeAttr("disabled");
            $("#btn_Rerender").removeAttr("disabled");
        }
    }
});
