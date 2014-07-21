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
                            colorFilmposterBG   : '#000000'
                            
                        };
                        
                        $scope.images = {
                            imageBackground : null,
                            imageLogo       : null
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
                            console.log(key);
                            //$files: an array of files selected, each file has name, size, and type.
                            var file = $files[0];
                            $upload.upload({
                                url: 'settings/upload/'+key, //upload.php script, node.js route, or servlet url
                                method: 'POST',
                                //headers: {'header-key': 'header-value'},
                                //withCredentials: true,
//                                data: {myObj: $scope.myModelObj},
                                file: file, // or list of files ($files) for html5 only
                                //fileName: 'doc.jpg' or ['1.jpg', '2.jpg', ...] // to modify the name of the file(s)
                            // customize file formData name ('Content-Desposition'), server side file variable name. 
                                //fileFormDataName: myFile, //or a list of names for multiple files (html5). Default is 'file' 
                                // customize how data is added to formData. See #40#issuecomment-28612000 for sample code
                                //formDataAppender: function(formData, key, val){}
                              }).success(function(data, status, headers, config) {
                                $scope.images[key] = file.name;
                              }).error(function(response,code){
                                  alert(response.error.message);
                              });
                              //.error(...)
                              //.then(success, error, progress); 
                              // access or attach event listeners to the underlying XMLHttpRequest.
                              //.xhr(function(xhr){xhr.upload.addEventListener(...)})
                        };
                        
                        $scope.deleteImage = function(key){
                            var obj = {};
                            obj[key] = null;
                            $http.post('settings/ajax',obj).success(function(){
                               $scope.images[key] = null; 
                            });
                        };

                }]);
//            });
            
      
    
    </script>
    
    <style>
        .tab-pane {padding-top:20px;}
        input.ng-dirty {background-color: lightsalmon;}
        .imageDrop {
            display: inline-block;
            width: 100%;
            text-align: center;
            line-height: 50px;
            
            border: 3px dashed black;
        }
        .imageDropDragOver {
            border: 3px solid red;
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
                    <li><a href="#films" role="tab" data-toggle="tab">Filme</a></li>
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
                                <input colorpicker ng-model="colors.colorFilmdetailsBG" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorFilmdetailsBG">
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
                                <input colorpicker ng-model="colors.colorFilmposterBG" placeholder="" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-2" ng-model="colors.colorFilmposterBG">
                                <input disabled class="form-control" style="background-color: @{{colors.colorFilmposterBG}}">
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
                                     ng-file-drag-over-class="'imageDropDragOver'"
                                     class="imageDrop clearfix">Ziehe neues Bild hierhin</div>
                                
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
                                     ng-file-drag-over-class="'imageDropDragOver'"
                                     class="imageDrop clearfix">Ziehe neues Bild hierhin</div>
                                
                                <br><br/><br/>
                                
                                <div class="clearfix">
                                    <button class="btn btn-danger" ng-click="deleteImage('imageBackground')"
                                            ng-show="images.imageBackground">Löschen</button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="tab-pane" id="films">
                        {{ Form::open(array('url'=>'settings','class'=>'form-horizontal','role'=>'form')) }} 
                        <input type="hidden" name="section" value="films"/>
                        {{ Form::close() }} 
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>