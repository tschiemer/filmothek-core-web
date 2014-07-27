<!DOCTYPE html>
<html ng-app="filmothekApp">
<head>
    <meta charset="UTF-8">
    
    <title>{{ Setting::get('pageTitle','Filmothek') }}</title>
    
    <meta name="author" content="Philip Tschiemer, filou.se"/>
    <meta name="description" content="{{ Setting::get('pageKeywords','') }}"/>
    <meta name="keywords"   content="{{ Setting::get('pageDescription','') }}"/>
    
    <script>
    
    var settings = {
        'filesDir': '{{Setting::get('dirFilesPublic','films/')}}'
    };
    
    </script>
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
            color: {{ Setting::get('colorPageText','black') }};
            background-color: {{ Setting::get('colorPageBg','white') }};
            @if(Setting::get('imageBackground'))
            background-image: url("uploads/{{ Setting::get('imageBackground') }}");
            @endif
            
            font-family: Times, Georgia, "Times New Roman", serif;
            font-size: 14px;
            line-height: 18px;
        }
        
        #logo {
            @if(Setting::get('imageLogo'))
            background-image: url("uploads/{{ Setting::get('imageLogo') }}");
            @endif
        }
        
        div.search, div.categories, div.sub-categories, div.films {
            color: {{ Setting::get('colorMenuText','black') }};
            background-color: {{ Setting::get('colorMenuBg','white') }};
        }
        
        .search input {
            color: {{ Setting::get('colorSearchText','black') }};
            background-color: {{ Setting::get('colorSearchBg','white') }};
        }
        
        .film-details {
            /*background-color: white;*/
            background-color: {{ Setting::get('colorFilmdetailsBg','white') }};
        }
        
        li {
            color: {{ Setting::get('colorListText','black') }};
            background-color: {{ Setting::get('colorListBg','white') }};
        }
        li.empty-result {
            color: {{ Setting::get('colorListText','black') }};
            background-color: {{ Setting::get('colorListBg','white') }};
        }
        li.selected {
            color: {{ Setting::get('colorListSelectedText','white') }};
            background-color: {{ Setting::get('colorListSelectedBg','#008e36') }};
        }
        li:hover {
            color: {{ Setting::get('colorListHoverText','white') }};
            background-color: {{ Setting::get('colorListHoverBg','#008e36') }};
        }
        
        .caption {
            font-family: Verdana, sans-serif;
            text-transform: uppercase;
            color: {{ Setting::get('colorCaptionText','black') }};
            /*background-color: {{ Setting::get('style-caption-bgcolor','white') }};*/
        }
        
        .preview-image div {
            background-color: {{ Setting::get('colorFilmposterBg','black') }};
            /*background-color: black;*/
        }
        
        .btn-no-film {
            border: 1px solid lightcoral;
            background-color: lightcoral;
            color: white;
        }

        .btn-play {
            border: 1px solid #008e36;
            background-color: #008e36;
            color: white;
        }
    </style>
    
</head>
<body oncontextmenu="return false;" ng-controller="testController" filmothek-view="@{{ activeView }}" filmothek-category="@{{ selectedCategory }}">
    
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
                            @if(Setting::get('imageNoPoster'))
                            <img ng-src="{{{ "@{{ selectedFilm == null ? 'img/black.png' : (selectedFilm.poster ? '".Setting::get('dirFilesPublic',  'films/')."'+selectedFilm.poster : 'uploads/". Setting::get('imageNoPoster') ."') }}" }}}"/>
                            @else
                            <img ng-src="{{{ "@{{ selectedFilm == null ? 'img/black.png' : (selectedFilm.poster ? '".Setting::get('dirFilesPublic',  'films/')."'+selectedFilm.poster : 'img/no-poster.jpg') }}" }}}"/>
                            @endif
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
                        
                        <a style="margin-left: 20px;">
                            @{{ selectedFilm.title }}, @{{ selectedFilm.artist }}
                        </a>
                    </p>
                </div>
                
            </aside>
        </div>
    </div>
 
    <div id="overlay"></div>
</body>
</html>