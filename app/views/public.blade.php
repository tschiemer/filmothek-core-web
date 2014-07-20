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
            
            font-family: Times, Georgia, "Times New Roman", serif;
            font-size: 14px;
            line-height: 18px;
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
<body ng-controller="testController" filmothek-view="@{{ activeView }}" filmothek-category="@{{ selectedCategory }}">
    
    <div id="logo"></div>
    
    <div class="viewer" active="@{{ activeView }}">
        <div class="slider">
            <aside class="browser" active="@{{ selectedCategory.key }}">
                
                <div class="menu">
                    <div class="search">
                        <input tabindex="1" type="text" ng-model="searchTerm" value="@{{ searchTerm }}" placeholder="Search.." ng-keyup="search()"/>
                    </div>

                    <div class="categories">
                        <div class="caption">Suche</div>
                        <ul class="categories" ng-model="selectedCategory">
                            <li ng-repeat="cat in categories"
                                ng-click="selectCategory(cat)"
                                class="@{{ (selectedCategory != null && selectedCategory.label == cat.label ? 'selected' : '')}}">
                                Nach @{{ cat.label }}
                            </li>
                        </ul>
                    </div>

                    <div class="sub-categories">
                        <div class="caption" style="display:inline-block;width:98%;">@{{ selectedCategory ? selectedCategory.label : '' }}</div>
                        <div class="scrollable">
                            <ul class="sub-categories" ng-model="selectedSubCategory">
                                <li ng-repeat="subcat in subCategories | orderBy:'label'"
                                    ng-click="selectSubCategory(subcat)"
                                    class="@{{ (selectedSubCategory != null && selectedSubCategory.key == subcat.key ? 'selected' : '')}}">
                                    @{{ subcat.label }}
                                </li>
                                <li ng-show="!subCategories.length && searchInProgress" class="empty-result">Suchen..</li>
                                <li ng-show="!subCategories.length && !searchInProgress" class="empty-result">Keine Resultate</li>
                            </ul>
                        </div>
                    </div>

                    <div class="films">
                        <div class="caption">Filme</div>
                        <div class="scrollable">
                            <ul class="films" ng-model="selectedFilm">
                                <li ng-repeat="film in films | orderBy:'title'"
                                    ng-click="selectFilm(film)"
                                    class="@{{ (selectedFilm != null && selectedFilm.id == film.id) ? 'selected' : '' }}">
                                    @{{ film.title }}
                                </li>
                                <li ng-show="!films.length && selectedCategory.key != 'title' && selectedSubCategory == null" class="empty-result">Bitte @{{ selectedCategory.label }} auswählen.</li>
                                <li ng-show="!films.length && selectedSubCategory != null && searchInProgress" class="empty-result">@{{ selectedCategory.key != 'title' ? 'Laden..' : 'Suchen..' }}</li>
                                <li ng-show="!films.length && (selectedCategory.key == 'title' || selectedSubCategory != null) && !searchInProgress" class="empty-result">Keine Filme</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="title-view" class="film-details" ng-model="selectedFilm" visible="@{{ selectedFilm == null ? 'no' : 'yes'}}">
                    <div class="preview-image">
                        <div>
                            <img ng-src="@{{ selectedFilm == null ? 'img/black.png' : (selectedFilm.poster ? selectedFilm.poster : 'uploads/no-poster.jpg') }}"/>
                        </div>
                    </div>

                    <aside style="position:absolute;background-color:transparent;margin-left:-203px;">

                        <div style="background-color:transparent;text-align:right;width:200px">
                            <button ng-if="selectedFilm.video" ng-click="showPlayer(selectedFilm)" class="btn-play">Abspielen <span></span></button>
                            <button ng-if="!selectedFilm.video" disabled class="btn-no-film">Leider kein Film vorhanden.</button>
                        </div>

                    </aside>
                    <div class="caption"><b>@{{selectedFilm.title}}</b><br/>@{{selectedFilm.artist}}</div>

                    @{{ selectedFilm.country ? selectedFilm.country : '?' }},
                    @{{ selectedFilm.year ? selectedFilm.year : '?' }},
                    @{{ selectedFilm.length ? selectedFilm.length : '?' }},
                    @{{ selectedFilm.technique ? selectedFilm.technique : '?' }}
                </div>
                
            </aside>
            <aside class="player" ng-model="selectedFilm">
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
                            @{{ selectedFilm.title }}, @{{ selectedFilm.artist }}
                        </span>
                    </p>
                </div>
                
            </aside>
        </div>
    </div>
 
    <div id="overlay"></div>
</body>
</html>