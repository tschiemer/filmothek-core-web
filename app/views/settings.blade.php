<!DOCTYPE html>
<html ng-app="settingsApp">
<head>
    <meta charset="UTF-8">
    
    <title>{{ Setting::get('page-title','Filmothek') }}</title>
    
    <meta name="author" content="Philip Tschiemer, filou.se"/>
    
    
    <script src="bower_components/ng-file-upload/angular-file-upload-shim.js"></script>
    
    <script src="bower_components/angular/angular.js"></script>
    <script src="bower_components/angular-route/angular-route.js"></script>
    <script src="bower_components/angular-resource/angular-resource.js"></script>
    <script src="bower_components/underscore/underscore.js"></script>
    <script src="bower_components/angular-underscore/angular-underscore.js"></script>
    
    <link rel="stylesheet" href="bower_components/angular-bootstrap-colorpicker/css/colorpicker.css">
    <script src="bower_components/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.js"></script>
    
    <script src="bower_components/ng-file-upload/angular-file-upload.js"></script>
    
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css">
    <script src="bower_components/jquery/dist/jquery.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>
    <!--<link rel="stylesheet" href="css/settings.css">-->
    
    <script>
    var settingsApp;
    var settingsControllers;
    
//            $.ready(function(){
                    settingsApp = angular.module('settingsApp', [
                    'ngRoute',
                    'angular-underscore',
                    'colorpicker.module',
                    'ngResource',
                    'angularFileUpload',
                    'settingsControllers'
                  ]);
                  settingsControllers = angular.module('settingsControllers', []);

                settingsControllers.controller('settingController', [
                        '$scope',
                        '$http',
                        '$resource',
                        '$upload',
                        function ($scope,$http,$resource,$upload) {

                        $scope.page = {
                            pageTitle       : 'Filmothek',
                            pageKeywords    : '',
                            pageDescription : ''
                        };

                        $scope.colors = {
                            colorPageText       : '#000000',
                            colorPageBg         : '#ffffff',
                            colorMenuText       : '#000000',
                            colorMenuBg         : '#ffffff',
                            colorSearchText     : '#000000',
                            colorSearchBg       : '#ffffff',
                            colorFilmdetailsBG  : '#ffffff',
                            colorListText       : '#000000',
                            colorListBg         : '#ffffff',
                            colorListTextHover  : '#ffffff',
                            colorListBgHover    : '#008e36',
                            colorListTextSelected: '#ffffff',
                            colorListBgSelected : '#008e36',
                            colorCaptionText    : '#000000',
                            colorFilmposterBg   : '#000000'
                            
                        };
                        
                        $scope.images = {
                            imageBackground : null,
                            imageLogo       : null,
                            imageNoPoster   : null
                        };
                        
                        $scope.files = {
                            dirFilesReal: null,
                            dirFilesPublic: null
                        };

                        $http.get('settings/ajax').success(function(data) {
                            for(var i in data){
                                if(data[i].key.match(/^color/)){
                                    $scope.colors[data[i].key] = data[i].value;
                                }
                                if (data[i].key.match(/^page/)){
                                    $scope.page[data[i].key] = data[i].value;
                                }
                                if (data[i].key.match(/^image/)){
                                    $scope.images[data[i].key] = data[i].value;
                                }
                                if (data[i].key.match(/^(file|dir)/)){
                                    $scope.files[data[i].key] = data[i].value;
                                }
                            }
                        });
                        
                        $scope.savePage = function(key){
                            var obj = {};
                            if (key == undefined){
                                obj = $scope.page;
                            } else {
                                obj[key] = $scope.page[key];
                            }
                            
                            $http.post('settings/ajax',obj).success(function(){
                                $scope.pageForm.$setPristine();
                            });
                        };
                        
                        $scope.saveColors = function(key){
                            var obj = {};
                            if (key == undefined){
                                obj = $scope.colors;
                            } else {
                                obj[key] = $scope.colors[key];
                            }
                            
                            $http.post('settings/ajax',obj).success(function(){
                                $scope.colorsForm.$setPristine();
                            });
                        };
                        
                        $scope.onImageSelect = function(key,$files) {
//                            console.log(key);
                            //$files: an array of files selected, each file has name, size, and type.
                            var file = $files[0];
                            $upload.upload({
                                url: 'settings/upload/'+key, //upload.php script, node.js route, or servlet url
                                method: 'POST',
                                //headers: {'header-key': 'header-value'},
                                file: file,
                              }).success(function(data, status, headers, config) {
                                $scope.images[key] = file.name;
                              }).error(function(response,code){
                                  alert(response.error.message);
                              });
                        };
                        
                        $scope.deleteImage = function(key){
                            var obj = {};
                            obj[key] = null;
                            $http.post('settings/ajax',obj).success(function(){
                               $scope.images[key] = null; 
                            });
                        };
                        
                        $http.get('settings/film').success(function(data) {
                            $scope.films = data;
                        });
                        
                        
                        $scope.saveFiles = function(key){
                            var obj = {};
                            if (key == undefined){
                                obj = $scope.files;
                            } else {
                                obj[key] = $scope.files[key];
                            }
                            
                            $http.post('settings/ajax',obj).success(function(){
                                $scope.filesForm.$setPristine();
                            });
                        };
                        
                        $scope.updateFilm = function(film){
                            $http.post('settings/film/'+film.id,film);
                        };
                        
                        $scope.importInProgress = false;
                        $scope.onFilmImport = function($files){
                            $scope.importInProgress = true;
                            var file = $files[0];
                            $upload.upload({
                                url: 'settings/import-films', //upload.php script, node.js route, or servlet url
                                method: 'POST',
                                //headers: {'header-key': 'header-value'},
                                file: file,
                                }).success(function(data, status, headers, config) {
                                    $http.get('settings/film').success(function(data) {
                                        $scope.films = data;
                                        //alert('Filme importiert');
                                        $scope.importInProgress = false;
                                    });
                                }).error(function(response,code){
                                    alert(response.error.message);
                                    $scope.importInProgress = false;
                                });
                        };
                        
                        $scope.scanInProgress = false;
                        $scope.scanAllFilmFiles = function(){
                            $scope.scanInProgress = true;
                            $http.get('settings/scan-for-files').success(function(){
                                $http.get('settings/film').success(function(data) {
                                    $scope.films = data;
                                    alert('Files scan completed.');
                                    $scope.scanInProgress = false;
                                });
                            }).error(function(response,code){
//                                console.log(response);
                                alert(response.error.message);
                                $scope.scanInProgress = false;
                            });
                        };
                        
                        $scope.deleteAllFilms = function(){
                            $http.delete('settings/film').success(function(){
                                $scope.films = [];
                            }).error(function(data,data2){
                                console.log(data);
                                console.log(data2);
                            });
                        };

                }]);
//            });
            
      
    
    </script>
    
    <style>
        body {padding-bottom: 100px;}
        .tab-pane {padding-top:20px;}
        input.ng-dirty {background-color: lightsalmon;}
        .fileDrop {
            display: inline-block;
            width: 100%;
            text-align: center;
            line-height: 50px;
            
            border: 3px dashed black;
        }
        .fileDropDragOver {
            border: 3px solid red;
        }
        
        #film input.ng-dirty {background-color:}
        #film input[type=text]{
            border-width: 0px;
        }
        
        #film input[type=text]:focus {
            border:-width: 1px;
        }
        
    </style>
</head>
<body ng-controller="settingController">
    <nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <a class="navbar-brand">@{{ page.pageTitle }} - Settings</a>
      </div>

    </div><!-- /.container-fluid -->
    </nav>
    
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#security" role="tab" data-toggle="tab">Sicherheit</a></li>
                    <li><a href="#page" role="tab" data-toggle="tab">Seiteneinstellungen</a></li>
                    <li><a href="#colors" role="tab" data-toggle="tab">Farben</a></li>
                    <li><a href="#images" role="tab" data-toggle="tab">Bilder</a></li>
                    <li><a href="#film-list" role="tab" data-toggle="tab">Filme (@{{ films ? films.length : 0 }})</a></li>
                    <li><a href="#film-import" role="tab" data-toggle="tab">Film-Admin (u.A. Import)</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="security">
                        
                    </div>
                    <div class="tab-pane" id="page" ng-form="pageForm">
                        
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Page Title</label>
                            <div class="col-sm-8">
                                <input placeholder="Filmothek" type="text" class="form-control" ng-model="page.pageTitle"/>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-sm-4">HTML Meta Keywords</label>
                            <div class="col-sm-8">
                                <textarea placeholder="Filmothek, Film Viewer, .."class="form-control" ng-model="page.pageKeywords"></textarea>
                            </div>
                        </div>


                        <div class="form-group clearfix">
                            <label class="col-sm-4">HTML Meta Description</label>
                            <div class="col-sm-8">
                                <textarea placeholder="Web-Filmothek of film entries." class="form-control" ng-model="page.pageDescription"></textarea>
                            </div>
                        </div>


                        <div class="form-group clearfix">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button type="submit" class="btn btn-default" ng-click="savePage()">Speichere</button>
                            </div>
                        </div>
                            
                    </div>
                    <div class="tab-pane" id="colors" ng-form="colorsForm">
                        
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Seite Textfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorPageText" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorPageText">
                                <input disabled class="form-control" style="background-color: @{{colors.colorPageText}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Seite Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorPageBg" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorPageBg">
                                <input disabled class="form-control" style="background-color: @{{colors.colorPageBg}}">
                            </div>
                        </div>
                        
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenkasten rechts Textfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorMenuText" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorMenuText">
                                <input disabled class="form-control" style="background-color: @{{colors.colorMenuText}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenkasten rechts Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorMenuBg" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorMenuBg">
                                <input disabled class="form-control" style="background-color: @{{colors.colorMenuBg}}">
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-sm-4">Suchkasten Textfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorSearchText" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorSearchText">
                                <input disabled class="form-control" style="background-color: @{{colors.colorSearchText}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Suchkasten Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorSearchBg"  placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorSearchBg">
                                <input disabled class="form-control" style="background-color: @{{colors.colorSearchBg}}">
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-sm-4">Filmdetails Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorFilmdetailsBg" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorFilmdetailsBg">
                                <input disabled class="form-control" style="background-color: @{{colors.colorFilmdetailsBG}}">
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenelement Textfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorListText"  placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorListText">
                                <input disabled class="form-control" style="background-color: @{{colors.colorListText}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenelement Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorListBg" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorListBg">
                                <input disabled class="form-control" style="background-color: @{{colors.colorListBg}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenelement "Mausdrüber" Textfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorListTextHover" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorListTextHover">
                                <input disabled class="form-control" style="background-color: @{{colors.colorListTextHover}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenelement "Mausdrüber" Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorListBgHover" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorListBgHover">
                                <input disabled class="form-control" style="background-color: @{{colors.colorListBgHover}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenelement Ausgewählt Textfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorListTextSelected" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorListTextSelected">
                                <input disabled class="form-control" style="background-color: @{{colors.colorListTextSelected}}">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-sm-4">Listenelement Ausgewählt  Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorListBgSelected" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorListBgSelected">
                                <input disabled class="form-control" style="background-color: @{{colors.colorListBgSelected}}">
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-sm-4">Überschriften Textfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorCaptionText" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorCaptionText">
                                <input disabled class="form-control" style="background-color: @{{colors.colorCaptionText}}">
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label class="col-sm-4">Überschriften Hintergrundfarbe</label>
                            <div class="col-sm-6">
                                <input colorpicker ng-model="colors.colorFilmposterBg" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorFilmposterBg">
                                <input disabled class="form-control" style="background-color: @{{colors.colorFilmposterBg}}">
                            </div>
                        </div>


                        <div class="form-group clearfix">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button type="submit" class="btn btn-default" ng-click="saveColors()">Speichere</button>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="images">
                        
                        <div class="form-group clearfix" ng-model="images.imageLogo">
                            <label class="col-sm-3">Logobild</label>
                            <div class="col-sm-6">
                                <div ng-show="images.imageLogo">
                                    <p>Auf Weiss:</p>
                                    <a ng-href="@{{ images.imageLogo ? 'uploads/'+images.imageLogo : 'img/black.png'}}" target="_blank">
                                        <img ng-src="@{{ images.imageLogo ? 'uploads/'+images.imageLogo : 'img/black.png'}}" ng-show="images.imageLogo" style='max-width:100%;border: 1px solid black;'>
                                    </a>
                                <br/><br/>
                                    <p>Auf Schwarz:</p>
                                    <a ng-href="@{{ images.imageLogo ? 'uploads/'+images.imageLogo : 'img/black.png'}}" target="_blank">
                                        <img ng-src="@{{ images.imageLogo ? 'uploads/'+images.imageLogo : 'img/black.png'}}" ng-show="images.imageLogo" style='max-width:100%;border: 1px solid black;background-color:black;'>
                                    </a>
                                </div>
                                <span ng-show="!images.imageLogo">Kein Logobild hochgeladen!</span>
                            </div>
                            <div class="col-sm-3">
                                
                                <div ng-file-drop="onImageSelect('imageLogo',$files)"
                                     ng-file-drag-over-class="'fileDropDragOver'"
                                     class="fileDrop clearfix">Ziehe neues Bild hierhin</div>
                                
                                <br><br/><br/>
                                
                                <div class="clearfix">
                                    <button class="btn btn-danger" ng-click="deleteImage('imageLogo')"
                                            ng-show="images.imageLogo">Löschen</button>
                                </div>
                            </div>
                        </div>
                        
                        
                         <div class="form-group clearfix" ng-model="images.imageBackground">
                            <label class="col-sm-3">Hintergrundbild</label>
                            <div class="col-sm-6">
                                <a ng-show="images.imageBackground" ng-href="@{{ images.imageBackground ? 'uploads/'+images.imageBackground : 'img/black.png'}}" target="_blank">
                                    <img ng-src="@{{ images.imageBackground ? 'uploads/'+images.imageBackground : 'img/black.png'}}" ng-show="images.imageBackground" style='max-width:100%;;border: 1px solid black'>
                                </a>
                                <span ng-show="!images.imageBackground">Kein Hintergrundbild hochgeladen!</span>
                            </div>
                            <div class="col-sm-3">
                                
                                <div ng-file-drop="onImageSelect('imageBackground',$files)"
                                     ng-file-drag-over-class="'fileDropDragOver'"
                                     class="fileDrop clearfix">Ziehe neues Bild hierhin</div>
                                
                                <br><br/><br/>
                                
                                <div class="clearfix">
                                    <button class="btn btn-danger" ng-click="deleteImage('imageBackground')"
                                            ng-show="images.imageBackground">Löschen</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group clearfix" ng-model="images.imageBackground">
                            <label class="col-sm-3">Film-Poster (fehlt) Ersatzbild </label>
                            <div class="col-sm-6">
                                <a ng-show="images.imageNoPoster" ng-href="@{{ images.imageNoPoster ? 'uploads/'+images.imageNoPoster : 'img/black.png'}}" target="_blank">
                                    <img ng-src="@{{ images.imageNoPoster ? 'uploads/'+images.imageNoPoster : 'img/black.png'}}" ng-show="images.imageNoPoster" style='max-width:100%;;border: 1px solid black'>
                                </a>
                                <span ng-show="!images.imageNoPoster">Kein Hintergrundbild hochgeladen!</span>
                            </div>
                            <div class="col-sm-3">
                                
                                <div ng-file-drop="onImageSelect('imageNoPoster',$files)"
                                     ng-file-drag-over-class="'fileDropDragOver'"
                                     class="fileDrop clearfix">Ziehe neues Bild hierhin</div>
                                
                                <br><br/><br/>
                                
                                <div class="clearfix">
                                    <button class="btn btn-danger" ng-click="deleteImage('imageNoPoster')"
                                            ng-show="images.imageNoPoster">Löschen</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="tab-pane" id="film-list">
                        
                        <table class="table table-condensed table-hover">
                            <thead>
                                <tr>
                                    <td>Nr</td>
                                    <td>Titel</td>
                                    <td>Titel (eng)</td>
                                    <td>Regie</td>
                                    <td>Land</td>
                                    <td>Jahr</td>
                                    <td>Dauer</td>
                                    <td>Techniken</td>
                                    <td>Poster</td>
                                    <td>Film</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="film in films | orderBy:'nr'">
                                    <td>
                                        <input ng-model="film.nr" placeholder="<Nr>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <input ng-model="film.title" placeholder="<Titel>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <input ng-model="film.title_en" placeholder="<Titel (eng)>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <input ng-model="film.artist" placeholder="<Regie>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <input ng-model="film.country" placeholder="<Land>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <input ng-model="film.year" placeholder="<Jahr>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <input ng-model="film.length" placeholder="<Dauer>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <input ng-model="film.technique" placeholder="<Techniken>" ng-change="updateFilm(film)" class="form-control input-sm" type="text">
                                    </td>
                                    <td>
                                        <i class="glyphicon glyphicon-ok" ng-show="film.poster"></i>
                                        <i class="glyphicon glyphicon-remove" style="color:red"  ng-show="!film.poster"></i>
                                    </td>
                                    <td>
                                        <i class="glyphicon glyphicon-ok" ng-show="film.video"></i>
                                        <i class="glyphicon glyphicon-remove" style="color:red" ng-show="!film.video"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="film-import">
                        <div class="row" ng-form="filesForm" style="margin-bottom: 30px;">
                            <div class="col-sm-3">
                                Interner Ordner-Pfad f. Film-Dateien
                            </div>
                            <div class="col-sm-9">
                                    <input ng-model="files.dirFilesReal" placeholder="" type="text" class="form-control"/>
                                    <p class="help-block">Unter diesem absoluten Dateipfad müssen sich sämtliche Film-Dateien befinden. Wird zum scannen verwendet.</p>
                            </div>
                            
                            <div class="col-sm-3">
                                Web Ordner-Pfad f. Film-Dateien
                            </div>
                            <div class="col-sm-9">
                                    <input ng-model="files.dirFilesPublic" placeholder="/films" type="text" class="form-control"/>
                                    <p class="help-block">Unter diesem relativen Dateipfad zum Webserver müssen sich alle Film-Dateien befinden, die abrufbar sein sollten.</p>
                            </div>
                            
                            <button class="col-sm-offset-3 btn btn-default" ng-click="saveFiles()">Speichere Pfäde</button>
                            
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                Film-Import
                            </div>
                            <div class="col-sm-6">
                                <div ng-file-drop="onFilmImport($files)"
                                     ng-file-drag-over-class="'fileDropDragOver'"
                                     class="fileDrop clearfix"
                                     ng-show="!importInProgress">Ziehe Excelliste hierhin</div>
                                <div ng-show="importInProgress" class="fileDrop clearfix">Am Importieren, bitte warten..</div>
                                <p class="help-block">
                                    Die Spalten <em>müssen</em> folgende Namen tragen: nr, title, title_en, artist, country, year, length, technique
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                Scanne nach Film-Dateien (Poster, Videos)
                            </div>
                            <div class="col-sm-3">
                                <button ng-show="!scanInProgress" class="btn btn-info" ng-click="scanAllFilmFiles()">Scanne..</button>
                                <button ng-show="scanInProgress" class="btn btn-info" disabled>Scanne..</button>
                            </div>
                        </div>
                        <div class="row" style="margin: 50px 10px; border:2px solid #d9534f; padding: 30px">
                            <div class="col-sm-3">
                                Lösche alle Filme von DB
                            </div>
                            <div class="col-sm-3">
                                <button class="btn btn-danger" ng-click="deleteAllFilms()">Lösche</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="container">
             <footer  style="margin-top:50px;padding:10px;text-align:center">
                <small>
                    <p>
                        &copy; 2014 <a href='http://www.fantoche.ch' target="_blank">Fantoche - Internationales Festival für Animationsfilm</a>, Baden Switzerland. <br/>
                        Open source licensed under GPLv2, <a href="http://github.com/tschiemer/filmothek" target="_blank">download from Github</a><br/>
                        Author: <a href="http://filou.se" target="_blank">filou.se</a>
                    </p>

                </small>
            </footer>
        </div>
    </div>

</body>
</html>