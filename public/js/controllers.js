var filmothekControllers = angular.module('filmothekControllers', []);

filmothekControllers.controller('testController', [
        '$scope',
        'Category',
        'Film',
        function ($scope,Category, Film) {
        
        $scope.activeView = 'browser';
        
        $scope.searchTerm = '';
        
        $scope.searchInProgress = false;

        $scope.selectedCategory = {key:'title',label:'Titel'};
        $scope.categories = Category.query();
        $scope.selectCategory = function(cat){
            // do not change if in same subcategory
            if ($scope.selectedCategory.key == cat.key){
                return;
            }
            
            $scope.selectedCategory = cat;
            $scope.selectedFilm = null;
            
//            if (cat.key == 'title'){
                $scope.selectedSubCategory = null;
//                $scope.subCategories = [];    
//            }
            $scope.films = [];
            
            $scope.search();
        };
        
        $scope.selectedSubCategory = null;
        $scope.subCategories = [];
        $scope.selectSubCategory = function(subCat){
            
            if ($scope.selectedSubCategory != null && $scope.selectedSubCategory.key == subCat.key){
                return;
            }
            
            $scope.selectedSubCategory = subCat;
            $scope.selectedFilm = null;
            $scope.films = [];
            
            $scope.searchFilms();
        };
        
        $scope.selectedFilm = null;
        
        $scope.selectFilm = function(film){
            $scope.selectedFilm = film;
        };
        
        Film.success = function(films){
            $scope.films = films;
            $scope.searchInProgress = false;
        };
        
        $scope.searchFilms = function(){
            
            $scope.searchInProgress = true;
            $scope.films = [];
            
            var filter = {};
            
            if ($scope.selectedSubCategory != null) {
                filter[$scope.selectedCategory.key] = $scope.selectedSubCategory.label;
            }
            
            filter.search = $scope.searchTerm;
            
            Film.query(filter);
//            Film.query($scope.searchTerm,filters);
        }
        
        
        
//        Category.success = function(cat){
//            console.log(cat);
//            var indexedCat = [];
//            for(var i in cat){
//                console.log(cat[i]);
//                indexedCat.push({
//                   key: i,
//                   label: cat[i]
//                });
//            }
//            $scope.subCategories = indexedCat;
//            $scope.films = [];
//        };
        
        $scope.searchCategory = function(){
            if ($scope.selectedCategory.key == 'title'){
                return;
            }
            
            $scope.searchInProgress = true;
            $scope.subCategories = [];
            
            $scope.subCategories = Category.query({
                subCat: $scope.selectedCategory.key,
                search: $scope.searchTerm
            });
            
            $scope.searchInProgress = false;
//            var indexed = [];
//            for(var i in stringList){
//                console.log(stringList[i]);
//                indexed.push({
//                    key: i,
//                    label: stringList[i]
//                });
//            }
//            $scope.subCategories = indexed;
        }
        
        $scope.search = function(){
//            console.log('searching with: '+$scope.searchTerm);
            if ($scope.selectedCategory.key == 'title'){
                $scope.searchFilms();
            } else {
                $scope.searchCategory();
            }
        };
        
        $scope.search();
        
        $scope.showPlayer = function(film){
            $scope.activeView = 'player';
            
            var frameStyle = window.getComputedStyle(document.getElementsByClassName('player')[0], null);
            $scope.videojs.width(frameStyle.getPropertyValue('width'));
            $scope.videojs.height(frameStyle.getPropertyValue('height'));
            
            $scope.videojs.currentTime(0.1);
//            $scope.videojs.posterImage.el.style.display = 'none';
            
            
            $scope.videojs.poster(film.poster);
            $scope.videojs.src(film.video);
            
            
//            var player = document.getElementById('video-player');
            
//            if (typeof video.src == 'string'){
//                var source = document.createElement('source');
//                source.src = video.src;
//                
//                if (video.src.match(/mp4/)){
//                    source.type = 'video/mp4';
//                } else {
//                    source.type = 'video/*';
//                }
//                player.appendChild(source);
//            }
            
            window.setTimeout(function(){
                $scope.videojs.play();
            }, 2000);
        };
        
        $scope.videojs = videojs(document.getElementById('video-player'),{},function(){
            
        });
        
        $scope.showBrowser = function(){
            $scope.activeView = 'browser';
            
            $scope.videojs.pause();            
        }
        
        $scope.video = {};
        
        $scope.playFilm = function(filmId){
            
        };
        
}]);