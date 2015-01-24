<!DOCTYPE html>
<html lang="en" ng-app="mongodb-playground">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mongo Playground</title>
    <link href="/components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link href="/components/codemirror/lib/codemirror.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
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
                <div id="a-examples" ng-controller="examplesCtrl as e">
                    <ul class="nav nav-pills full-width">
                        <li ng-repeat="example in e.examples">
                            <a  ng-click="e.loadExample(example._id.$id)">
                                {{example.name}}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-7">
                <div id="a-console" ng-controller="consoleCtrl as c">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active">
                            <a href="#js" aria-controls="js" role="tab" data-toggle="tab">JS</a>
                        </li>
                        <li role="presentation">
                            <a href="#php" aria-controls="php" role="tab" data-toggle="tab">PHP</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="js">
                            <ui-codemirror ng-model="c.codeJS" ui-codemirror-opts="c.editorOptionsJS"></ui-codemirror>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="php">
                            <ui-codemirror ng-model="c.codePHP" ui-codemirror-opts="c.editorOptionsPHP"></ui-codemirror>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row info">

            <div class="col-md-5">
                <div id="a-description" ng-controller="descriptionCtrl as d">
                    {{d.description}}
                </div>
            </div>

            <div class="col-md-7">
                <div id="a-output" ng-controller="outputCtrl as o">
                    output
                </div>
            </div>

        </div>
    </div>
    <?//= $data['userProgress']['_id']; ?>

    <script type="text/javascript" src="/components/jquery/dist/jquery.js"></script>
    <script type="text/javascript" src="/components/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="/components/angular/angular.js"></script>
    <script type="text/javascript" src="/components/angular-route/angular-route.js"></script>
    <script type="text/javascript" src="/components/angular-resource/angular-resource.js"></script>
    <script type="text/javascript" src="/components/angular-contenteditable/angular-contenteditable.js"></script>
    <script type="text/javascript" src="/components/codemirror/lib/codemirror.js"></script>
    <script type="text/javascript" src="/components/codemirror/mode/javascript/javascript.js" charset="utf-8"></script>
    <script type="text/javascript" src="/components/codemirror/mode/php/php.js" charset="utf-8"></script>
    <script type="text/javascript" src="/components/angular-ui-codemirror/ui-codemirror.js"></script>
    <script type="text/javascript" src="/js/app/app.js"></script>
</body>
</html>