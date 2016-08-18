(function(){
    var googleMapService = function($rootScope){
        this.latlong = null;
       this.geocoder = new google.maps.Geocoder();

        this.geocodePosition = function(pos) {
            this.geocoder.geocode({
                latLng: pos
            }, function(responses) {
                if (responses && responses.length > 0) {
                    //this.updateMarkerAddress(responses[0].formatted_address);
                } else {
                    //  updateMarkerAddress('Cannot determine address at this location.');
                }
            });
        };

        this.updateMarkerStatus = function(str) {
            document.getElementById('markerStatus').innerHTML = str;
        };


        this.getValues = function(){
            return this.latlong;
        };

        this.updateMarkerPosition = function(latLng) {
             this.latlong = {
                latitude: latLng.lat(),
                longitude: latLng.lng()
            }
        };

        /*function updateMarkerAddress(str) {
         document.getElementById('address').innerHTML = str;
         } */

        this.initialize = function() {
            var self = this;
            var latLng = new google.maps.LatLng(45.26690893464459, 19.84215063476563);
            var map = new google.maps.Map(document.getElementById('mapCanvas'), {
                zoom: 10,
                //center: setCenter(latLng),
                center: latLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var marker = new google.maps.Marker({
                position: latLng,
                title: 'Označi geopoziciju magacina na mapi',
                map: map,
                draggable: true
            });

            // Update current position info.
            self.updateMarkerPosition(latLng);
            self.geocodePosition(latLng);

            // Add dragging event listeners.
            google.maps.event.addListener(marker, 'dragstart', function() {

            });

            google.maps.event.addListener(marker, 'drag', function() {
                 self.updateMarkerPosition(marker.getPosition());
                 $rootScope.$broadcast('latlong.update');
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                self.geocodePosition(marker.getPosition());
            });


        }
    };
    googleMapService.$inject = ['$rootScope'];
    angular.module('_raiffisenApp')
        .service('googleMapService', googleMapService);
}());