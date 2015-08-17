App.controller('TodoController', ['$scope', '_', 'Todo', 'UserManagement', function ($scope, _, Todo, UserManagement) {
    $scope.userNames = [];

    UserManagement.list().success(function (userNames) {
        for (var userId in userNames) {
            $scope.userNames.push({
                id: userId, name: userNames[userId]
            });
        }
    });

    Todo.getData().success(function (data) {
        $scope.states = data.states;
        $scope.items  = data.list;
    });

    $scope.assign = function (itemId, userId) {
        Todo.assign(itemId, userId);
    };

    $scope.addTodo = function () {
        var errorMessage = _("Name must not be empty"),
            name, description, date, tempData;

        description = $scope.newDescription;
        name = $scope.newTitle;
        date = $scope.newDateline;

        if (!name) {
            alert(errorMessage);
            return;
        }

        tempData = {
            name: name,
            deadline: date,
            description: description,
            status: 'open'
        };

        Todo.add(tempData).success(function (result) {
            $scope.items.push(result);
            $scope.newTitle = $scope.newDescription = $scope.newDateline = '';
        });
    };

    $scope.onDelete = function (data) {
        Todo.deleteItem(data.todoId).success(function() {
            $scope.items.removeByValue(data);
        });
    };

    $scope.onDrop = function (status, event, data) {
        if (status == data.status) {
            return;
        }

        $scope.items.forEach(function(current) {
            if (data.todoId == current.todoId) {
                current.status = data.status;
            }
        });

        Todo.edit(data);
    };
}]);
