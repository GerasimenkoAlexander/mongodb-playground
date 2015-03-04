<!DOCTYPE html>
<html lang="en" ng-app="mongodb-playground">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mongo Playground</title>
    <link href="/components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <link href="/components/codemirror/lib/codemirror.css" rel="stylesheet">
    <link href="/components/codemirror/theme/ambiance.css" rel="stylesheet">
    <link href="/components/toastr/toastr.css" rel="stylesheet">
    <!--<link href="/components/codemirror/theme/pastel-on-dark.css" rel="stylesheet">-->
    <link href="/css/main.css" rel="stylesheet">
</head>
<body ng-controller="mainCtrl as mc">
    <div class="container-fluid full-size">

        <div class="row header">
            <div class="col-sm-9">
                <h1>
                    <!--<span class="glyphicon glyphicon-heart"></span>-->
                    MongoDB Playground
                </h1>
            </div>
            <div class="col-sm-3">
                <div class="pull-right" id="ip">
                    <button class="btn btn-xs btn-danger" ng-click="mc.restoreDb()">Restore DB</button> |
                    Hi, <span contenteditable ng-model="mc.username" strip-br="true" ng-change="mc.changeName()"><span>
                </div>
            </div>
        </div>

        <div class="row control">

            <div class="col-sm-4">
                <div id="a-examples" ng-controller="examplesCtrl as e">
                    <ul class="nav nav-pills full-width">
                        <li ng-repeat="example in e.examples" ng-class="{active: e.hash == example._id.$id}">
                            <a ng-href="#{{example._id.$id}}" ng-click="e.loadExample(example._id.$id)" >
                                <div class="correct">
                                    <span ng-show="e.progress.indexOf(example._id.$id) !== -1" class="glyphicon glyphicon-ok-sign"></span>
                                </div>
                                {{example.name}}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-sm-8">
                <div id="a-description" ng-controller="descriptionCtrl as d">
                    <div>
                        <h2>Description</h2>
                        {{d.description}}
                    </div>
                    <div ng-if="d.exercise">
                        <h2>Exercise</h2>
                        {{d.exercise}}
                        <div class="clearfix"></div>
                        <div class="pull-right">
                            <button class="btn grey" answer paste="d.paste()" answer-text="d.answer">Show answer</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row info">

            <div class="col-sm-7">
                <div id="a-console" class="inputData" ng-controller="consoleCtrl as c">
                    <div id="js">
                        <button class="btn run" ng-click="c.run('js')">RUN</button>
                        <ui-codemirror ng-model="c.codeJS" ui-codemirror-opts="c.editorOptionsJS" id="cm1"></ui-codemirror>
                    </div>
                    <div id="php">
                        <button class="btn run" ng-click="c.run('php')">RUN</button>
                        <ui-codemirror ng-model="c.codePHP" ui-codemirror-opts="c.editorOptionsPHP" id="cm2"></ui-codemirror>
                    </div>
                </div>
            </div>

            <div class="col-sm-5">
                <div id="a-output" ng-controller="outputCtrl as o">
                    <ui-codemirror ng-model="o.output" ui-codemirror-opts="o.outputOptions" id="cm3"></ui-codemirror>
                </div>
            </div>

        </div>
    </div>
    <?//= $data['userProgress']['_id']; ?>

    <script type="text/javascript" src="/components/jquery/dist/jquery.js"></script>
    <script type="text/javascript" src="/components/bootstrap/dist/js/bootstrap.js"></script>
    <script type="text/javascript" src="/components/angular/angular.js"></script>
    <script type="text/javascript" src="/components/angular-route/angular-route.js"></script>
    <script type="text/javascript" src="/components/angular-sanitize/angular-sanitize.js"></script>
    <script type="text/javascript" src="/components/angular-resource/angular-resource.js"></script>
    <script type="text/javascript" src="/components/angular-contenteditable/angular-contenteditable.js"></script>
    <script type="text/javascript" src="/components/codemirror/lib/codemirror.js"></script>
    <script type="text/javascript" src="/components/codemirror/mode/javascript/javascript.js" charset="utf-8"></script>
    <script type="text/javascript" src="/components/codemirror/mode/php/php.js" charset="utf-8"></script>
    <script type="text/javascript" src="/components/angular-ui-codemirror/ui-codemirror.js"></script>
    <script type="text/javascript" src="/components/toastr/toastr.js"></script>
    <script type="text/javascript" src="/js/common.js"></script>
    <script type="text/javascript" src="/js/app/app.js"></script>
</body>
</html>