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
    }

    MainCtrl.$inject = ['$scope'];
    function MainCtrl ($scope){}

    ExamplesCtrl.$inject = ['$scope', 'Examples', 'Example'];
    function ExamplesCtrl ($scope, Examples, Example){
        var vm = this;

        vm.loadExample = loadExample;
        angular.element(document).ready(loadExamples());

        function loadExamples() {
            Examples.query().$promise.then(function success(success){
                vm.examples = success;
            }, function error(error) {
                console.warn(error);
            });
        }

        function loadExample (id){
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

    ConsoleCtrl.$inject = ['$scope', 'Example'];
    function ConsoleCtrl ($scope, Example){
        var vm = this;
        vm.activeTab= 'JS';
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
    }

    OutputCtrl.$inject = ['$scope'];
    function OutputCtrl ($scope){}
})();