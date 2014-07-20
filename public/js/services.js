var filmothekServices = angular.module('filmothekServices', ['ngResource']);

  filmothekServices.factory('Category', ['$resource',function($resource){
    return $resource('category/:subCat/:search', {}, {
          query: {
              method:'GET',
              params:{
                  subCat:'',
                  search:''
              },
              isArray:true
          }
    });
  }]);
  
filmothekServices.factory('Film', ['$http',function($http){
    return {
        success: function(data){
            //do nothing
        },
        error: function(){
            //do nothing
        },
        query: function(filter){ //search,filters){
            
            $http.post('film',filter).success(this.success).error(this.error);
            
//            var url = 'film';
//            if (typeof search == 'string'){
//                url += '/' + search;
//            }
//            
//            var filtersPrep = [];
//            filters = filters ? filters : {};
//            for(var name in filters){
//                if (['artist','title','country','technique'].indexOf(name) >= 0){
//                    filtersPrep.push(name+'='+filters[name]);
//                }
//            }
//            url += '?' + filtersPrep.join('&');
//            
//            console.log(url);
//            
//            $http.post(url,{
//                transformRequest: function(data,headersGetter){
//                    console.log(data);
//                    console.log(headersGetter);
//                }
//            }).success(this.success).error(this.error);
        }
    };
  }]);
  