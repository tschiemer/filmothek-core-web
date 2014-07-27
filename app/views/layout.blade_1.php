<!DOCTYPE html>

<html>
<head>

    <title>FANTOCHE</title>
    
    <meta charset="UTF-8">
    
    <!-- jquery -->
    <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>-->
    <script src="/resources/js/jquery-1.10.2.min.js"></script>
    
    <!--<link href="/resources/css/jquery-ui.css" rel="stylesheet"/>-->
    <link href="resources/css/jquery-ui-1.9.0.custom.min.css" rel="styleshee"/>
    <!--<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
    <script src="/resources/js/jquery-ui-1.9.0.custom.min.js"></script>
    
    <link href="/resources/css/keyboard.css" rel="stylesheet"/>
    <script src="/resources/js/jquery.keyboard.min.js"></script>

    <!--<link href="http://vjs.zencdn.net/4.1/video-js.css" rel="stylesheet">-->
    <!--<script src="http://vjs.zencdn.net/4.1/video.js"></script>-->
    
    <!-- video js -->
    <link href="/resources/css/video-js.css" rel="stylesheet"/>
    <script src="/resources/js/video.js"></script>
    <script>
      videojs.options.flash.swf = "/resources/swf/video-js.swf";
    </script>
    <!--<script src="/resources/js/popcorn-complete.min.js"></script>-->
    
    <!-- liquid slider -->
    <link rel="stylesheet" href="/resources/css/animate.css"/> 
    <link rel="stylesheet" href="/resources/css/liquid-slider.css"/>
    <script src="/resources/js/jquery.easing.1.3.js"></script>
    <script src="/resources/js/jquery.touchSwipe.min.js"></script>
    <script src="/resources/js/jquery.liquid-slider.min.js"></script>

    <!-- nanoscroller -->
    <link rel="stylesheet" href="/resources/css/nanoscroller.css"/>
    <script src="/resources/js/jquery.nanoscroller.min.js"></script>
    
    <!-- spin.js -->
    <script src="/resources/js/spin.min.js"></script>
    
    <script src="/resources/js/mustache.js"/></script>
    
    <!-- Custom styles & javascript -->
    <link rel="stylesheet" href="index.php/css/style.css"/>
    <script src="index.php/javascript/controller.js"></script>
    
    <script>
        
    var slider;
    
    var timeout = 60*30;
    
    var last_activity = (new Date()).getTime() / 1000;
    
    window.setInterval(function(){
        var now = (new Date()).getTime() / 1000;
        
        if (now > last_activity + timeout)
        {
            reset_kiosk();
        }
    },1000);
    
    window.onmousemove = function(){
      last_activity = (new Date()).getTime() / 1000;
    };
    
    window.onblur = function(){
//        alert(this);
        document.body.focus();
        return false;
    };
    
    $(document).ready(function(){
        $('#content').liquidSlider({
            dynamicTabs     : false,
            dynamicArrows   : false,
            autoHeight      : false,
//            hashLinking     : true,
            crossLinks      : true,
            slideEaseDuration:500
//            useCSSMaxWidth  : '100%'
//            responsize      : false
        });
        $('#content').parent().css('max-width','');
        
        slider = $.data( $('#content')[0], 'liquidSlider');
        
        init_player();//_with_id('video_player');
        
        $('#select-category tbody tr td').bind('click',function()
        {   
           var new_selection = this.getAttribute('value');
           select_category(new_selection);
        });
        
        var opts = {
            lines: 7, // The number of lines to draw
            length: 2, // The length of each line
            width: 2, // The line thickness
            radius: 4, // The radius of the inner circle
            corners: 1, // Corner roundness (0..1)
            rotate: 0, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#000', // #rgb or #rrggbb or array of colors
            speed: 1.3, // Rounds per second
            trail: 66, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: 'auto', // Top position relative to parent in px
            left: 'auto' // Left position relative to parent in px
          };
          var target = document.getElementById('search-busy');
          new Spinner(opts).spin(target);
          
//          $('#search-term').keyboard({
//              usePreview: false,
//              autoAccept: true,
//              position: {
//                  of: document.getElementById('search-keyboard-target'),
//                  at2: 'left top',
//                  my: 'right top'
////                  offset: '50 50'
//              }
//          });

        select_category($('#select-category tbody tr td').first().attr('value'));
        
    });  
    
    document.oncontextmenu = function(){return false;}
    </script>
    
</head>
<body >
    
    
    <div id="overlay"></div>

    <div id="templates" type="text/Mustache-tpl" style="display:none;">
        <div id="tpl-preview">
            
<!--                                                <div class="caption">
                                        Filminfos 
                                    </div>-->
                                    <div class="preview-image">
                                        
                                        <!--
                                        <aside  style="position:absolute;background-color:transparent;height:282px; width:500px;">
                                                
                                            <div style="background-color:transparent;text-align:right;margin-top:250px;">
                                                {{#movieFile}}
                                                <button onclick="present_player_for({{key}});" class="btn-play">Abspielen</button>
                                                {{/movieFile}}
                                                {{^movieFile}}
                                                <button disabled class="btn-no-film">Leider kein Film vorhanden.</button>
                                                {{/movieFile}}
                                            </div>
                                            
                                        </aside>
                                        -->
                                        
                                        <div>
                                            {{#imageFile}}
                                            <img src="{{imageFile}}"/>
                                            {{/imageFile}}
                                            {{^imageFile}}
                                            <img src="/resources/img/no-preview-image.jpg"/>
                                            {{/imageFile}}
                                        </div>
                                    </div>

                                    <aside style="position:absolute;background-color:transparent;margin-left:-203px;">
                                                
                                        <div style="background-color:transparent;text-align:right;width:200px">
                                            {{#movieFile}}
                                            <button onclick="present_player_for({{key}});" class="btn-play">Abspielen <span></span></button>
                                            {{/movieFile}}
                                            {{^movieFile}}
                                            <button disabled class="btn-no-film">Leider kein Film vorhanden.</button>
                                            {{/movieFile}}
                                        </div>

                                    </aside>
                                    <div class="caption"><b>{{title}}</b><br/>{{artist}}</div>
                                    
                                    {{#country}}{{country}}{{/country}}{{#year}} {{year}}{{/year}}{{#length}}, {{length}}{{/length}}{{#technique}}, {{technique}}{{/technique}}
        </div>
    </div>
    
    <div id="content" class="liquid-slider">
        <div id="selection-view" class="content-panel">
            <!--<h4>selection</h4>-->
            <!--<aside>-->
                <table cellpadding="0" cellspacing="0" style="width:100%;height:100%;empty-cells:show">
                    <colgroup>
                        <col/>
                        <col width="250"/>
                    </colgroup>
                    
                    <tbody>
                        <tr height="10">
                            <td style="vertical-align:bottom;text-align:right;padding-right:20px" rowspan="4">
                                <span></span>
                            </td>
                            <td style="vertical-align:top; style="padding:0px 5px 5px 5px; background-color:white"">
                                <div  style="padding:5px 5px 5px 5px; background-color:white">
                                    <input id="search-term" placeholder="Suchbegriff" onfocus="search_focus();" onkeydown="search_start_edit();" onchange="search_end_edit();" onkeyup="search_end_edit();" onblur="search_blur();" value="" type="text"/>
                                </div>
                                <aside id="search-clear">
                                    <a href="javascript:search_clear();"><img src="/resources/img/cross.png" style="width:15px; background-color:white;"/></a>
                                </aside>
                                <aside id="search-busy"></aside>
                                <aside style="position:absolute;margin-top:-32px;margin-left:-20px;" id="search-keyboard-target">
                                </aside>
                            </td>
                        </tr>
                        <tr height="10">
                            <td></td>
                        </tr>
                        <tr height="20">
                            <td style="vertical-align:top;">
                                <div style="padding:2px 5px 5px 5px; background-color:white">
                                    <div class="caption">
                                        Ordne nach 
                                    </div>
                                    <table id="select-category" class="select-table" cellpadding="0" cellspacing="0" style="width:100%">
                                        <tbody>
                                            <tr>
                                                <td value="title">Nach Titel</td>
                                            </tr>

                                            <tr>
                                                <td value="artist">Nach Regie</td>
                                            </tr>
                                            <tr>
                                                <td value="country">Nach Land</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr height="10">
                            <td></td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;text-align:right;padding-right:20px">
                                
                                <div id="title-view" style="display:none;">
                                </div>
                            </td>
                            <td style="vertical-align:top;" class="list-container">
                                <div id="artist-list" style="padding:2px 5px 5px 5px; background-color:white;margin-bottom:10px;">
                                    <div class="caption">
                                        Regie
                                    </div>
                                    <div class="nano">
                                        <div class="content">
                                            <table id="select-artist" class="select-table" cellpadding="0" cellspacing="0" style="width:100%">
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="country-list" style="padding:2px 5px 5px 5px; background-color:white;margin-bottom:10px;">
                                    <div class="caption">
                                        Land
                                    </div>
                                    <div class="nano">
                                        <div class="content">
                                            <table id="select-country" class="select-table" cellpadding="0" cellspacing="0" style="width:100%">
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="title-list" style="padding:2px 5px 5px 5px; background-color:white">
                                    <div class="caption">
                                        Filme
                                    </div>
                                    <div class="nano">
                                        <div class="content">
                                            <table id="select-title" class="select-table" cellpadding="0" cellspacing="0" style="width:100%">
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    
                    <!--<button onclick="$('#title-view').animate({opacity: 'toggle'});">toggle</button>-->
                <!--</div>-->
<!--            -->
        </div>
        <div id="player-view" class="content-panel">
            
            <aside style="position:absolute;" class="player-close-container">
                <button class="player-close" onclick="hide_player();">
                    Zurück zu Auswahl
                </button>
            </aside>
            
            <div class="player-container" style="width:100%;height:90%"></div>
            
<!--            <video id="video_player" 
                   controls preload="auto" 
                   width="100%" height="90%">
                <source id="video_player_source" src="" type="video/mp4"/>
                your player does not support video!
                <source src="resources/films/11001.mp4" type="video/mp4"/>class="video-js vjs-default-skin"
              </video>-->
              
                <!--poster="/resources/img/no-preview-image.jpg"-->
               <!--<source src="resources/films/11001.mp4" type='video/mp4' />-->
        </div>
    </div>
    


</body>
</html>