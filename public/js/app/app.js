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
        example.output  = {};
        example.userProgress  = {};
    }

    MainCtrl.$inject = ['$scope', 'Example'];
    function MainCtrl ($scope, Example){
        var vm = this;

        $scope.$watch(function(){
            return Example.userProgress;
        }, function (newValue){
            vm.username  = newValue.name;
        });
    }

    //todo remove $rootScope
    ExamplesCtrl.$inject = ['$scope', '$location', '$http', '$rootScope', 'Examples', 'Example'];
    function ExamplesCtrl ($scope, $location, $http, $rootScope, Examples, Example){
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
            Example.hash = vm.hash;
            Examples.get({id:id}).$promise.then(function success(success){
                Example.example = success;
            }, function error(error){
                console.warn(error);
            });
        }

        $rootScope.$on('correctAnswer', function(e, data){
            if(data){
                //todo notification that exercise is done
                Example.userProgress.progress.push(Example.hash);
            }
        });

        $scope.$watch(function(){
            return Example.userProgress;
        }, function (newValue){
            vm.progress  = newValue.progress;
        });
    }

    DescriptionCtrl.$inject = ['$scope', 'Example'];
    function DescriptionCtrl ($scope, Example){
        var vm = this;
        $scope.$watch(function(){
            return Example.example;
        }, function (newValue){
            vm.description = newValue.description;
            vm.exercise = newValue.exercise;
        });
    }

    //todo remove rootScope
    ConsoleCtrl.$inject = ['$scope', '$http', '$rootScope', 'Example'];
    function ConsoleCtrl ($scope, $http, $rootScope, Example){
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
                    id: Example.hash,
                    lang: lang,
                    code: code
                }
            ).success(function(data){
                Example.output = data.data;
                $rootScope.$broadcast('correctAnswer', data.correct);
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