<div ng-controller="MoneyStatController">
    <h2>Выручка</h2>
    <div class="well">
        <form role="form" class="form-inline" ng-submit="search()">
            <div class="form-group">
                <input ng-model="filter.name" type="text" name="name" class="form-control" placeholder="Название">
            </div>
            <div class="form-group">
                <input ng-model="filter.priceFrom" type="text" class="form-control number" placeholder="Цена от">
                <input ng-model="filter.priceTo" type="text" class="form-control number" placeholder="Цена до">
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Найти</button>
            </div>
        </form>
    </div>
    <table ng-show="goodList.length" class="element-list table-hover table-striped table-responsive">
        <tr>
            <th>Название</th>
            <th>Цена, руб.</th>
        </tr>
        <tr ng-repeat="good in goodList">
            <td>{{good.name}}</td>
            <td>{{good.price}}</td>
        </tr>
    </table>
</div>