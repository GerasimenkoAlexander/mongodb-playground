<!DOCTYPE html>
<html lang="en" ng-app="mongodb-playground">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mongo Playground</title>
    <link href="/components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
    <script type="text/javascript" src="/components/jquery/dist/jquery.js"></script>
    <script type="text/javascript" src="/components/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="/components/angular/angular.js"></script>
    <script type="text/javascript" src="/components/angular-route/angular-route.js"></script>
    <script type="text/javascript" src="/components/angular-resource/angular-resource.js"></script>
    <script type="text/javascript" src="/components/angular-contenteditable/angular-contenteditable.js"></script>
    <script type="text/javascript" src="/js/app/app.js"></script>
</head>
<body ng-controller="mainCtrl">
    <div class="container-fluid full-size">

        <div class="row">
            <div class="col-md-12">
                <h1>
                    <span class="glyphicon glyphicon-heart"></span>
                    MongoDB Playground
                </h1>
            </div>
        </div>

        <div class="row control">

            <div class="col-md-5">
                <div id="a-examples" ng-controller="examplesCtrl">
                    <ul class="nav nav-pills full-width">
                        <?php foreach($data['examples'] as $example): ?>
                            <li>
                                <a href="#<?= $example['_id']; ?>" ng-click="loadExample()">
                                    <?= $example['name']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-7">
                <div id="a-console" ng-controller="consoleCtrl">
                    console
                </div>
            </div>

        </div>

        <div class="row info">

            <div class="col-md-5">
                <div id="a-description" ng-controller="descriptionCtrl">
                    description
                </div>
            </div>

            <div class="col-md-7">
                <div id="a-output" ng-controller="outputCtrl">
                    output
                </div>
            </div>

        </div>
    </div>
    <?//= $data['userProgress']['_id']; ?>
</body>
</html>