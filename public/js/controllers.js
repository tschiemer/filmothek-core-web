var filmothekControllers = angular.module('filmothekControllers', []);

filmothekControllers.controller('testController', [
        '$scope',
        'Category',
        'Film',
        function ($scope,Category, Film) {
        
        $scope.activeView = 'browser';
        
        $scope.searchTerm = '';

        $scope.activeCategory = {key:'title'};
        $scope.categories = Category.query();
        $scope.selectCategory = function(cat){
            // do not change if in same subcategory
            if ($scope.activeCategory.key == cat.key){
                return;
            }
            
            $scope.activeCategory = cat;
            $scope.film = null;
            
            if (cat.key == 'title'){
                $scope.activeSubCategory = null;
                $scope.subCategories = [];
            } else {
                $scope.searchCategory();
            }
        };
        
        $scope.activeSubCategory = null;
        $scope.subCategories = [];
        $scope.selectSubCategory = function(subCatName){
            if ($scope.activeSubCategory == subCatName){
                return;
            }
            
            $scope.activeSubCategory = subCatName;
            $scope.film = null;
            
            $scope.searchFilms();
        };
        
        $scope.film = null;
        
        $scope.selectFilm = function(film){
            $scope.film = film;
        };
        
        Film.success = function(films){
            $scope.films = films;  
        };
        
        $scope.searchFilms = function(){
            var filter = {};
            
            if ($scope.activeSubCategory != null) {
                filter[$scope.activeCategory.key] = $scope.activeSubCategory;
            }
            
            filter.search = $scope.searchTerm;
            
            Film.query(filter);
//            Film.query($scope.searchTerm,filters);
        }
        
        
        
        Category.success = function(cat){
            $scope.subCategories = cat;
            $scope.films = [];
        };
        
        $scope.searchCategory = function(){
            if ($scope.activeCategory.key == 'title'){
                return;
            }
            $scope.subCategories = Category.query({
                subCat: $scope.activeCategory.key,
                search: $scope.searchTerm
            });
        }
        
        $scope.search = function(){
            if ($scope.activeCategory.key == 'title'){
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