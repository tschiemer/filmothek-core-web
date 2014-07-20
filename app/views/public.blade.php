<!DOCTYPE html>
<html ng-app="filmothekApp">
<head>
    <meta charset="UTF-8">
    
    <title>{{ Setting::get('page-title','Filmothek') }}</title>
    
    <meta name="author" content="Philip Tschiemer, filou.se"/>
    <meta name="description" content=""/>
    <meta name="keywords"   content=""/>
    
    <script src="bower_components/angular/angular.js"></script>
    <script src="bower_components/angular-route/angular-route.js"></script>
    <script src="bower_components/angular-resource/angular-resource.js"></script>
    <script src="bower_components/underscore/underscore.js"></script>
    <script src="bower_components/angular-underscore/angular-underscore.js"></script>
    <script src="js/app.js"></script>
    <script src="js/services.js"></script>
    <script src="js/controllers.js"></script>
    
    <script src="bower_components/videojs/dist/video-js/video.js"></script>
    <link rel="stylesheet" href="bower_components/videojs/dist/video-js/video-js.css">
    
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="css/app.css">
    <style type="text/css">
        .video-js.vjs-default-skin .vjs-big-play-button { display: none; }
        
        body {
            color: white;
            background-color: {{ Setting::get('style-bg-color','black') }};
            background-image: url("{{ Setting::get('style-bg-image','/uploads/background.jpg') }}");
        }
        
        #logo {
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background-image: url("{{ Setting::get('style-logo-image','/uploads/logo.png') }}");
            background-repeat: no-repeat;
        }
    </style>
    
</head>
<body ng-controller="testController" filmothek-view="@{{ activeView }}" filmothek-category="@{{ activeCategory }}">
    
    <div id="logo"></div>
    
    <div class="viewer" active="@{{ activeView }}">
        <div class="slider">
            <aside class="browser" active="@{{ activeCategory.key }}">
                
                <div class="menu">
                    <div class="search">
                        <input tabindex="1" type="text" ng-model="searchTerm" value="@{{ searchTerm }}" placeholder="Search.." ng-keyup="search()"/>
                    </div>

                    <div class="categories">
                        <div class="caption">Ordne nach</div>
                        <ul class="categories">
                            <li ng-repeat="cat in categories">
                                <a ng-click="selectCategory(cat)">Nach @{{ cat.label }}</a>
                            </li>
                        </ul>
                    </div>

                    <div class="sub-categories">
                        <div class="caption" style="display:inline-block;width:98%;">@{{ activeCategory ? activeCategory.label : '' }}</div>
                        <ul class="sub-categories">
                            <li ng-repeat="subcat in subCategories">
                                <a ng-click="selectSubCategory(subcat)">@{{ subcat }}</a>
                            </li>
                        </ul>
                    </div>

                    <div class="films">
                        <div class="caption">Filme</div>
                        <ul class="films">
                            <li ng-repeat="film in films">
                                <a ng-click="selectFilm(film)">@{{ film.title }}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div id="title-view" class="film-details" ng-model="film" visible="@{{ film == null ? 'no' : 'yes'}}">
                    <div class="preview-image">
                        <div>
                            <img ng-src="@{{ film == null ? 'img/black.png' : (film.poster ? film.poster : 'uploads/no-poster.jpg') }}"/>
                        </div>
                    </div>

                    <aside style="position:absolute;background-color:transparent;margin-left:-203px;">

                        <div style="background-color:transparent;text-align:right;width:200px">
                            <button ng-if="film.video" ng-click="showPlayer(film)" class="btn-play">Abspielen <span></span></button>
                            <button ng-if="!film.video" disabled class="btn-no-film">Leider kein Film vorhanden.</button>
                        </div>

                    </aside>
                    <div class="caption"><b>@{{film.title}}</b><br/>@{{film.artist}}</div>

                    @{{ film.country ? film.country : '?' }},
                    @{{ film.year ? film.year : '?' }},
                    @{{ film.length ? film.length : '?' }},
                    @{{ film.technique ? film.technique : '?' }}
                </div>
                
            </aside>
            <aside class="player" ng-model="film">
<!--                <div style="position: relative; top: -20px;">
                    <button ng-click="showBrowser()" tabindex="-1">Browser</button>
                </div>-->
                
                <video id="video-player" width="200" height="200"
                       class="video-js vjs-default-skin"
                       controls preload="auto">
                    <!--<source ng-src="@{{ mp4 }}" type='video/mp4'/>-->
<!--                    <source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm' />
                    <source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg' />-->
                    <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
                </video>
                
                <div style="position:relative; top:-100%; padding: 10px 20px;">
                    
                    <p>
                        <a ng-click="showBrowser()">
                            <i  class="glyphicon glyphicon-chevron-left"></i> Zurück
                        </a>
                        
                        <span style="margin-left: 20px;">
                            @{{ film.title }}
                        </span>
                    </p>
                </div>
                
            </aside>
        </div>
    </div>
 
    <div id="overlay"></div>
</body>
</html>