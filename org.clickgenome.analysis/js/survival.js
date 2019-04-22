var survival = new Object({
    URL: {
        BASE_URL: function () {
            return "http://54.193.40.253:8080/data/data/";
            //return "http://localhost:8088/data/data/";
        },
        SURVIVAL: function (cancerType) {
            var url = survival.URL.BASE_URL() + cancerType + "/survival";
            console.log("url", url);
            return url;
        }
    },
    Operate: {
        getSurvivalData: function (cancerType, covariates) {
            $("#echarts").html('<div class="spinner">' +
                '<div class="rect1"></div>' +
                '<div class="rect2"></div>' +
                '<div class="rect3"></div>' +
                '<div class="rect4"></div>' +
                '</div><p class="col-md-12" style="text-align:center;color:rgb(61,132,193);">Loading~~~</p>');
            var url = survival.URL.SURVIVAL(cancerType);
            $.get(url, covariates, function (result, status) {
                if (status) {
                    if (result == null) {
                        alert("error null");
                    } else {
                        console.log(result);
                        survival.View.draw(result);
                        var DData = survival.n_t_points.getData(result);
                        console.log("DData:", DData);
                        //重新构建table
                        $('#example').dataTable().fnClearTable();   //将数据清除
                        $('#example').dataTable().fnAddData(survival.Table.packagingdatatabledata(DData), true);  //数据必须是json对象或json对象数组
                    }
                } else {
                    alert("there is something wrong when request " + url + ",data:" + data);
                }
            });
        }
    },
    n_t_points: {
        getData: function (data) {
            var data = data.data;
            var points = data.point;
            var npoints = points[0];
            var tpoints = points[1];
            var n_points = [];
            for (var element in npoints) { //--->基因
                var sample = [];
                for (var a in npoints[element]) { //--->样本正常否
                    sample.push(npoints[element][a]);
                }
                n_points.push(sample);
            }
            var t_points = [];
            for (var element in tpoints) {
                var sample = [];
                for (var a in tpoints[element]) {
                    sample.push(tpoints[element][a]);
                }
                t_points.push(sample);
            }
            if (t_points.length > n_points.length) {
                L = t_points.length;
            } else {
                L = n_points.length;
            }

            var tArray = new Array(); //定义一维数组

            for (var a = 0; a < L; a++) {

                //tArray[a] = [t_sortedTime[a], t_sortedSurvivalEstimate[a],n_sortedTime[a],n_sortedSurvivalEstimate[a]];
                tArray[a] = [t_points[a], n_points[a]];

            }

            return tArray;
        }
    },
    Table: {
        packagingdatatabledata: function (arr) {
            console.log("arr:" + arr);
            var a = [];
            var tableName = ['coxHighTime', 'coxLowTime'];
            var tempObj = new Object();
            for (var i = 0; i < arr.length; i++) {
                tempObj.coxHighTime = arr[i][0];
                tempObj.coxLowTime = arr[i][1];
                a.push(JSON.parse(JSON.stringify(tempObj, tableName)));

            }
            return a;
        }
    },
    View: {
        init: function () {
            var cancer_arr = [
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
            $("#cancername").html("");
            /**/
            for (var i = 0; i < cancer_arr.length; i++) {
                /**/
                var r = /\[(.+?)\]/; //正则获取方括号内容并转为小写
                /**/
                $("#cancername").append("<option class='col-md-12' value='" + cancer_arr[i].match(r)[1].toLowerCase() + "'>" + cancer_arr[i] + "</option>");
                /**/
            }
        },
        draw: function (data) {
            console.log("data", data);
            var data = data.data;
            var points = data.point;
            var npoints = points[0];
            var tpoints = points[1];
            // var cyto = data.cyto;
            // var arm = data.arm;


            //npoints格式化
            var n_points = [];
            for (var element in npoints) { //--->基因
                var sample = [];
                for (var a in npoints[element]) { //--->样本正常否
                    sample.push(npoints[element][a]);
                }
                n_points.push(sample);
            }
            //tpoints格式化
            var t_points = [];
            for (var element in tpoints) {
                var sample = [];
                for (var a in tpoints[element]) {
                    sample.push(tpoints[element][a]);
                }
                t_points.push(sample);
            }


            //查询高亮基因
            // var highLightedGeneName = highLightedGeneName.split(",");
            // var _highLightedGeneName = [];
            // for (var i = 0; i < highLightedGeneName.length; i++) {
            //     for (var j = 0; j < n_points.length; j++) {
            //         if (highLightedGeneName[i] == n_points[j][2]) {
            //             _highLightedGeneName.push([n_points[j][0], n_points[j][1], n_points[j][2]]);
            //         }
            //     }
            //     for (var k = 0; k < t_points.length; k++) {
            //         if (highLightedGeneName[i] == t_points[k][2]) {
            //             _highLightedGeneName.push([t_points[k][0], t_points[k][1], t_points[k][2]]);
            //         }
            //     }
            // }
            // console.log(
            //     'highLightedGeneName', _highLightedGeneName
            // );


            //cyto 格式化
            // var _cyto = [];
            // if ($("#cyto").is(':checked')) {
            //     for (var element in cyto) {
            //         var cc = [];
            //         for (var a in cyto[element]) {
            //             cc.push(cyto[element][a]);
            //         }
            //         _cyto.push(cc);
            //     }
            // }
            //console.log("cyto", _cyto);

            //arm格式化

            // var _arm = [];
            // for (element in arm) {
            //     _arm.push(arm[element]);
            // }
            // //console.log("arm", _arm);
            // //排序
            // var x_list = [];
            // for (var i = 0; i < n_points.length; i++) {
            //     x_list.push(n_points[0]);
            // }
            // x_list.sort();
            // for (var i = 0; i < x_list.length; i++) {
            //     if (x_list[i] == _arm[0]) {
            //         _arm[0] = x_list[i - 1] + (x_list[i] - x_list[i - 1]) / 2;
            //     }
            // }


            // var cyto_data = [];
            // var CYTO_Y = YMIN - 0.1;
            // var CYTO_HEIGHT = 20;
            // var CYTO_COLOR = "";
            // for (var i = 0; i < _cyto.length; i++) {
            //     if (i % 2 == 0) {
            //         CYTO_COLOR = '#565656';
            //     } else {
            //         CYTO_COLOR = "#000";
            //     }
            //     if (i < _cyto.length - 1) {
            //         cyto_data.push([{
            //             coord: [_cyto[i][0], CYTO_Y],
            //             symbol: 'none',
            //             symbolRotate: 90,
            //             lineStyle: {
            //                 normal: {
            //                     color: CYTO_COLOR,
            //                     width: CYTO_HEIGHT,
            //                     type: 'solid'
            //                 }
            //             },
            //             label: {
            //                 normal: {
            //                     show: true,
            //                     formatter: _cyto[i][1]
            //                 }
            //             }
            //         }, {
            //             coord: [_cyto[i + 1][0], CYTO_Y],
            //             symbol: 'none',
            //             symbolRotate: 90,
            //             lineStyle: {
            //                 normal: {
            //                     color: CYTO_COLOR,
            //                     width: CYTO_HEIGHT,
            //                     type: 'solid'
            //                 }
            //             },
            //             label: {
            //                 normal: {
            //                     show: true,
            //                     formatter: _cyto[i][1]
            //                 }
            //             }
            //         }]);
            //     } else if (i = _cyto.length - 1) {
            //         cyto_data.push([{
            //             coord: [_cyto[i][0], CYTO_Y],
            //             symbol: 'none',
            //             lineStyle: {
            //                 normal: {
            //                     color: CYTO_COLOR,
            //                     width: CYTO_HEIGHT,
            //                     type: 'solid'
            //                 }
            //             },
            //             label: {
            //                 normal: {
            //                     show: true,
            //                     formatter: _cyto[i][1]
            //                 }
            //             }
            //         }, {
            //             coord: [XMAX, CYTO_Y],
            //             symbol: 'none',
            //             lineStyle: {
            //                 normal: {
            //                     color: CYTO_COLOR,
            //                     width: CYTO_HEIGHT,
            //                     type: 'solid'
            //                 }
            //             },
            //             label: {
            //                 normal: {
            //                     show: true,
            //                     formatter: _cyto[i][1]
            //                 }
            //             }
            //         }]);
            //     }
            // }


            // var cyto_line = {
            //     animation: true,
            //     data: cyto_data
            // };


            //基准线
            // var STD_COLOR = "#00ff00";
            // var STD_HEIGHT = 3;
            // var std_data = [];
            // if ($("#islog").is(':checked')) {
            //     std_data.push([{
            //         coord: [0, 0],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: STD_COLOR,
            //                 width: STD_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }, {
            //         coord: [XMAX, 0],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: CYTO_COLOR,
            //                 width: CYTO_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }]);
            // } else {
            //     std_data.push([{
            //         coord: [0, 2],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: STD_COLOR,
            //                 width: STD_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }, {
            //         coord: [XMAX, 2],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: CYTO_COLOR,
            //                 width: CYTO_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }]);
            // }

            //自定义分割线
            // var _cutoffLine = [];

            // if ($("#line1").val() != "") {
            //     std_data.push([{
            //         coord: [0, $("#line1").val()],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: STD_COLOR,
            //                 width: STD_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }, {
            //         coord: [XMAX, $("#line1").val()],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: CYTO_COLOR,
            //                 width: CYTO_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }]);
            // }
            // if ($("#line2").val() != "") {
            //     std_data.push([{
            //         coord: [0, $("#line2").val()],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: STD_COLOR,
            //                 width: STD_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }, {
            //         coord: [XMAX, $("#line2").val()],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: CYTO_COLOR,
            //                 width: CYTO_HEIGHT,
            //                 type: 'solid'
            //             }
            //         }
            //     }]);
            // }


            //加入arm线
            // console.log("arm", _arm);
            // var ARM_COLOR = "#000";
            // var ARM_HEIGHT = 5;
            // if ($("#islog").is(':checked')) {
            //     std_data.push([{
            //         coord: [_arm[0], 0 + (YMAX - 0) / 4],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: ARM_COLOR,
            //                 width: ARM_HEIGHT,
            //                 type: 'dotted'
            //             }
            //         }
            //     }, {
            //         coord: [_arm[0], 0 - (0 - YMIN) / 4],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: ARM_COLOR,
            //                 width: ARM_HEIGHT,
            //                 type: 'dotted'
            //             }
            //         }
            //     }]);
            // } else {
            //     std_data.push([{
            //         coord: [_arm[0], 2 + (YMAX - 2) / 4],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: ARM_COLOR,
            //                 width: ARM_HEIGHT,
            //                 type: 'dotted'
            //             }
            //         }
            //     }, {
            //         coord: [_arm[0], 2 - (2 - YMIN) / 4],
            //         symbol: 'none',
            //         lineStyle: {
            //             normal: {
            //                 color: ARM_COLOR,
            //                 width: ARM_HEIGHT,
            //                 type: 'dotted'
            //             }
            //         }
            //     }]);
            // }
            // var std_line = {
            //     animation: true,
            //     data: std_data
            // };


            /**初始化图片下载配置*/
            /**/
            var saveName = $("#saveName_input").val();
            /**/
            var saveType = $("#saveType_select").val();
            /**/
            var pixelRatio = $("#pixelRatio_select").val();
            /**初始化图片下载配置结束*/
            option = {
                title: {
                    text: 'Survival plot',
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
                    data: ['Survival analysis_n', 'Survival analysis_t'],
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
                    formatter: 'Group {a}: ({c})'
                },
                xAxis: [
                    {gridIndex: 0, min: 0, max: 5000}
                ],
                yAxis: [
                    {gridIndex: 0, min: 0, max: 1}
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
                                zoom: "Data zoom",
                                back: "Zoom back"
                            }
                        },
                        restore: {
                            show: true,
                            title: "Restore"
                        },
                        saveAsImage: {
                            show: true,
                            title: "Save as Image",
                            name: saveName,
                            type: saveType,
                            pixelRatio: pixelRatio
                        },
                        brush: {
                            show: true,
                            title: {
                                rect: 'Rect select',
                                polygon: 'Polygon select',
                                lineX: 'LineX select',
                                lineY: 'LineY select',
                                keep: 'Keep select',
                                clear: 'Clear select'
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
                series: [{
                    name: 'Survival analysis_n',
                    type: 'line',
                    step: 'start',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data: n_points
                }, {
                    name: 'Survival analysis_t',
                    type: 'line',
                    step: 'end',
                    xAxisIndex: 0,
                    yAxisIndex: 0,
                    data: t_points
                }]
            };

            var myChart = echarts.init(document.getElementById('echarts'));

            myChart.setOption(option);
        }
    }

});