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
            for(var i in films){
                films[i].titleSort = films[i].title_en ? films[i].title_en : films[i].title;
            }
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
        
        $scope.playTimeout = null;
        $scope.showPlayer = function(film){
            if (!film.video){
                return;
            }
            
            $scope.activeView = 'player';
            
            var frameStyle = window.getComputedStyle(document.getElementsByClassName('player')[0], null);
            $scope.videojs.width(frameStyle.getPropertyValue('width'));
            $scope.videojs.height(frameStyle.getPropertyValue('height'));
            
//            $scope.videojs.posterImage.el.style.display = 'none';
            
            
            if (film.poster){
                $scope.videojs.poster(settings.filesDir + film.poster);
            } else {
                $scope.videojs.poster(false);
            }
            $scope.videojs.src(settings.filesDir + film.video).ready(function(){
                $scope.videojs.currentTime(0);
                if ($scope.activeView == 'player'){
                    $scope.playTimeout = window.setTimeout(function(){
                        $scope.videojs.play();
                        $scope.playTimeout = null;
                    }, 1000);
                }
            });
            
            
            
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
            
        };
        
        $scope.videojs = videojs(document.getElementById('video-player'),{},function(){
            
        });
        
        $scope.showBrowser = function(){
            $scope.activeView = 'browser';
            
            $scope.videojs.pause();
            if($scope.playTimeout != null){
                window.clearTimeout($scope.playTimeout);
                $scope.playTimeout = null;
            }
        }
        
        $scope.video = {};
        
        $scope.playFilm = function(filmId){
            
        };
        
}]);