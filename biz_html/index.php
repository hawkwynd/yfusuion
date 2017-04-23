<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Search Business Sample</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="Description" lang="en" content="Sample Business Search">
    <meta name="author" content="Scott Fleming">
    <meta name="robots" content="index, follow">

    <!-- icons -->
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
    <link rel="shortcut icon" href="05-bootstrap-blog-post-template-left/favicon.ico">

    <!-- Bootstrap Core CSS file -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Override CSS file - add your own CSS rules -->
    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- Conditional comment containing JS files for IE6 - 8 -->
    <!--[if lt IE 9]>
    <script src="assets/js/html5.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-fixed-top navbar-inverse" role="navigation">
    <div class="container-fluid">

        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">biz.hawkwynd.com</a>
        </div>
        <!-- /.navbar-header -->

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="/">Home</a></li>
                <li><a href="#">Link2</a></li>
                <li><a href="#">link3</a></li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
<!-- /.navbar -->

<div class="col-lg-10">
    <div class="well col-sm-3">
    <form action="index.php" method="get">
       <div class="form-group">
        <label for="search">Search</label>
            <input type="text" name="term" class="form-control" placeholder="search"/>
       </div>
        <div class="form-group">
        <label for="loc">Location</label>
            <input type="text" name="loc"  class="form-control" placeholder="zipcode"/>
        </div>
        <div class="form-group">
        <label for="limit">Results</label>
        <select name="limit" class="form-control col-sm-1">
            <option value="3">3</option>
            <option value="5">5</option>
            <option value="7">7</option>
            <option value="9">9</option>
        </select>

        </div>

        <div class="form-group">
            <input type="submit"/>
        </div>
    </form>
    </div><!-- col-sm-3 well -->

<!-- Results begin here -->
<div class="col-sm-4 col-sm-push-1">
<?php

include('yfusion/yfusion.php');

?>
</div>

    <footer class="margin-tb-3">
        <div class="row">
            <div class="col-lg-12">
                <p>A sample API demonstration @<?php echo date('Y'); ?></p>
            </div>
        </div>
    </footer>

</body>
</html>