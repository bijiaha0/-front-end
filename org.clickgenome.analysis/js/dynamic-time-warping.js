function getDynamicTimeWarping(ts1, ts2, distanceFunction) {
        var ser1 = ts1;
        var ser2 = ts2;
        var distFunc = distanceFunction;
        var matrix;

        var getDistance = function() {
            matrix = [];
            for ( var i = 0; i < ser1.length; i++ ) {
                matrix[ i ] = [];
                for ( var j = 0; j < ser2.length; j++ ) {
                    var cost = Infinity;
                    if ( i > 0 ) {
                        cost = Math.min( cost, matrix[ i - 1 ][ j ] );
                        if ( j > 0 ) {
                            cost = Math.min( cost, matrix[ i - 1 ][ j - 1 ] );
                            cost = Math.min( cost, matrix[ i ][ j - 1 ] );
                        }
                    } else {
                        if ( j > 0 ) {
                            cost = Math.min( cost, matrix[ i ][ j - 1 ] );
                        } else {
                            cost = 0;
                        }
                    }
                    matrix[ i ][ j ] = cost + distFunc( ser1[ i ], ser2[ j ] );
                }
            }

            return matrix[ ser1.length - 1 ][ ser2.length - 1 ];
        }();
        return getDistance;
}

function distFunc_(a, b) {
    //return Math.sqrt(Math.pow(a - b, 2));
    return Math.abs( a - b );
}


