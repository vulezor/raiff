(function(){
    var fileUploadService = function($http) {
        this.uploadFileToUrl = function(file, uploadUrl){
            var fd = new FormData();
            fd.append('file', file);
            return $http.post(uploadUrl, fd, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            });

        }
    };
    fileUploadService.$inject = ['$http'];
    angular.module('_raiffisenApp').service('fileUploadService', fileUploadService);
}());