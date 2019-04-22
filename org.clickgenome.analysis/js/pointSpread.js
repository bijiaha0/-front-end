
function pointSpread(arrCurrent) {

    var minData = Math.min.apply(null,arrCurrent);

    var maxData = Math.max.apply(null,arrCurrent);

    var reScale = Math.ceil(maxData-minData);

    var iGroup = 1;

    var arrCurrentChange=[];

    var newData = [];

    for(var i = 0; i < arrCurrent.length; i++){

        arrCurrentChange.push(arrCurrent[i] / reScale);

        newData.push(1.0);

    }

    for(var ii = Math.min.apply(null,arrCurrentChange); ii <= Math.max.apply(null,arrCurrentChange); ii += 0.1){

        var stepIndex = getIndex(ii,arrCurrentChange);

        var stepN = stepIndex.length;

        if(stepN > 1){

            var spreadSize = 0.45 * (1- Math.exp(Math.log(0.9) * (stepN - 1)));

            var spreadDist = spreadSize / (stepN - 1);

            var spreadStep;

            if (stepN % 2 == 0) {
                spreadStep = spreadDist / 2;
            } else {
                spreadStep = 2.2204e-16;
            }

            for(var k = 0; k < stepN; k++){

                newData[k]=getResult(iGroup+spreadStep,4);

                spreadStep = spreadStep - signum(spreadStep) * spreadDist * (k + 1);
            }

        }
    }
    return newData;
}



//定义一个求四舍五入到num后面的n位的函数
function getResult(value, n){
    return Math.round(value*Math.pow(10,n))/Math.pow(10,n);
}


function signum(step) {
    if(step > 0){
        return 1;
    }else if(step < 0){
        return -1;
    }else {
        return 0;
    }
}



//重新排列二维数组
function resetArr(datashow) {

    var currentData=[];

    if(datashow[0].length > 0){

        for(var j = 0;j < datashow[0].length;j++){//循环里面的数组

            currentData[j] = new Array();

            for(var i = 0;i < datashow.length;i++){//循环最外面的数组
                if(datashow[i][j] !=""){
                    currentData[j].push(datashow[i][j]);
                }
            }
        }

    }
    return currentData;
}



function getIndex(key,arr) {

    var resultIndex=[];

    for(var i=0;i < arr.length;i++){

        if(arr[i] >= key && arr[i] < (key+0.1)){

            resultIndex.push(i);
        }
    }
    return resultIndex;
}














