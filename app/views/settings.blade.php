<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    
    <title>{{ Setting::get('page-title','Filmothek') }}</title>
    
    <meta name="author" content="Philip Tschiemer, filou.se"/>
    <meta name="description" content=""/>
    <meta name="keywords"   content=""/>
    
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css">
    <!--<link rel="stylesheet" href="css/settings.css">-->
    
</head>
<body>
    <nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <a class="navbar-brand">{{ Setting::get('page-title','Filmothek') }} - Settings</a>
      </div>

    </div><!-- /.container-fluid -->
    </nav>
    
    <div class="container">
        <div class="row">
            <div class="col-sm-2">
            </div>
            <div class="col-sm-8">
                
                {{ Form::open(array('url'=>'settings','class'=>'form-horizontal','role'=>'form')) }}
                    
                    <div class="form-group">
                        <label class="col-sm-3">Page Title</label>
                        <div class="col-sm-6">
                            <input name="page-title" placeholder="Filmothek" type="text" class="form-control"/>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-6">
                            <button type="submit" class="btn btn-default">Save</button>
                        </div>
                    </div>
                    
                {{ Form::close() }}
                
            </div>
        </div>
    </div>

</body>
</html>