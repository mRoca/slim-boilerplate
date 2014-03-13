var initMap = function () {
    var mapsDiv = document.getElementById("maps");
    if ( !mapsDiv ) return;

    var markers = [
        [50.638631, 3.071008],
        [48.876838, 2.317217]
    ];

    var infoWindows = [
        '<p><b>First Point</b></p>',
        '<p><b>Second Point</b>'
    ];

    var mapObj = new google.maps.Map(
        mapsDiv,
        {
            zoom               : 5,
            center             : new google.maps.LatLng(46.953974, 1.403366),
            mapTypeId          : google.maps.MapTypeId.ROADMAP,
            mapTypeControlStyle: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
            scrollwheel        : false
        }
    );

    var infowindow = new google.maps.InfoWindow();

    var marker, i;
    for ( i = 0; i < markers.length; i++ ) {
        marker = new google.maps.Marker({
            position : new google.maps.LatLng(markers[i][0], markers[i][1]),
            map      : mapObj,
            optimized: false,
            flat     : true
        });

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                infowindow.setContent(infoWindows[i]);
                infowindow.open(mapObj, marker);
            }
        })(marker, i));
    }
};

initMap();