(function(){
    'use strict';

    var mongoPlayground = angular.module('mongodb-playground', [
        'ngRoute',
        'ngResource',
        //'contenteditable',
        'ui.codemirror'
    ]);

    mongoPlayground.factory('Examples', Examples);
    mongoPlayground.service('Example', Example);

    mongoPlayground.controller('mainCtrl', MainCtrl);
    mongoPlayground.controller('examplesCtrl', ExamplesCtrl);
    mongoPlayground.controller('descriptionCtrl', DescriptionCtrl);
    mongoPlayground.controller('consoleCtrl', ConsoleCtrl);
    mongoPlayground.controller('outputCtrl', OutputCtrl);

    Examples.$inject = ['$resource'];
    function Examples($resource){
        return $resource('/?url=example&id=:id', {}, {
            query: {isArray: false}
        })
    }

    function Example(){
        var example = this;
        example.example = {};
        example.output  = {}
    }

    MainCtrl.$inject = ['$scope'];
    function MainCtrl ($scope){}

    ExamplesCtrl.$inject = ['$scope', '$location', '$http', 'Examples', 'Example'];
    function ExamplesCtrl ($scope, $location, $http, Examples, Example){
        var vm = this;

        vm.loadExample = loadExample;
        vm.hash = null;
        angular.element(document).ready(function(){ loadExamples(); loadProgress(); });

        function loadExamples() {
            Examples.query().$promise.then(function success(success){
                vm.examples = success;

                //if isset hash load this example
                var hash = $location.path();
                if(hash){
                    hash = hash.substr(1);
                    loadExample(hash);
                }
            }, function error(error) {
                console.warn(error);
            });
        }

        function loadProgress() {
            $http.get(
                '?url=get-progress'
            ).success(function(data){
                //todo from here
                Example.userProgress = data;
            }).error(function(error){
                console.warn(error);
            });
        }

        function loadExample (id){
            vm.hash = id;
            Examples.get({id:id}).$promise.then(function success(success){
                Example.example = success;
            }, function error(error){
                console.warn(error);
            });
        }
    }

    DescriptionCtrl.$inject = ['$scope', 'Example'];
    function DescriptionCtrl ($scope, Example){
        var vm = this;
        $scope.$watch(function(){
            return Example.example;
        }, function (newValue){
            vm.description = newValue.description;
        });
    }

    ConsoleCtrl.$inject = ['$scope', '$http', 'Example'];
    function ConsoleCtrl ($scope, $http, Example){
        var vm = this;
        vm.run = run;
        vm.editorOptionsJS = {
            lineWrapping : true,
            lineNumbers: true,
            readOnly: false,
            mode: 'javascript',
            theme: 'ambiance'
        };
        //not working
        vm.editorOptionsPHP = {
            lineWrapping : true,
            lineNumbers: true,
            readOnly: false,
            mode: 'php',
            theme: 'ambiance'
        };
        $scope.$watch(function(){
            return Example.example;
        }, function (newValue){
            vm.codeJS  = newValue.example;
            vm.codePHP = newValue.examplePHP;
        });

        function run(lang){
            lang        = (lang === 'js') ? lang : 'php';
            var code    = (lang === 'js') ? vm.codeJS : vm.codePHP;
            $http.post(
                '?url=run-code',
                {
                    lang: lang,
                    code: code
                }
            ).success(function(data){
                Example.output = data;
            }).error(function(error){
                console.warn(error);
            });
        }
    }

    OutputCtrl.$inject = ['$scope', 'Example'];
    function OutputCtrl ($scope, Example){
        var vm = this;
        vm.activeTab= 'JS';
        vm.outputOptions = {
            lineWrapping : true,
            lineNumbers: true,
            readOnly: true,
            mode: 'javascript',
            theme: 'ambiance'
        };

        $scope.$watch(function(){
            return Example.output;
        }, function (newValue){
            vm.output  = JSON.stringify(newValue, null, '\t');
        });
    }
})();