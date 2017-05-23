<!-- array input -->

<?php
    $max = isset($field['max']) ? $field['max'] : -1;
    $min = isset($field['min']) ? $field['min'] : -1;
    $item_name = strtolower( isset($field['entity_singular']) && !empty($field['entity_singular']) ? $field['entity_singular'] : $field['label']);
    $items = old($field['name']) ? (old($field['name'])) : (isset($field['value']) ? ($field['value']) : (isset($field['default']) ? ($field['default']) : '' ));
    // make sure not matter the attribute casting
    // the $items variable contains a properly defined JSON
    if(is_array($items)) {
        if (count($items)) {
            $items = json_encode($items);
        }
        else
        {
            $items = '[]';
        }
    } elseif (is_string($items) && !is_array(json_decode($items))) {
        $items = '[]';
    }

    print_r($items);
?>
<div ng-app="backpackTable" ng-controller="tableController" @include('crud::inc.field_wrapper_attributes') >

    <label>{!! $field['label'] !!}</label>

    <input class="array-json" type="hidden" id="{{ $field['name'] }}" name="{{ $field['name'] }}">

    <div class="array-container form-group">


        <table class="table table-bordered table-striped m-b-0" ng-init="field = '#{{ $field['name'] }}'; items = {{ $items }}; max = {{$max}}; min = {{$min}}; maxErrorTitle = '{{trans('backpack::crud.table_cant_add', ['entity' => $item_name])}}'; maxErrorMessage = '{{trans('backpack::crud.table_max_reached', ['max' => $max])}}'">

            <thead>
                <tr>

                    @foreach( $field['columns'] as $prop )
                    <th style="font-weight: 600!important;">
                        {{ $prop }}
                    </th>
                    @endforeach
                    <th class="text-center"> {{-- <i class="fa fa-sort"></i> --}} </th>
                    <th class="text-center"> {{-- <i class="fa fa-trash"></i> --}} </th>
                </tr>
            </thead>

            <tbody ui-sortable="sortableOptions" ng-model="items" class="table-striped">

                <tr ng-repeat="item in items" class="array-row">

                    @foreach( $field['columns'] as $prop => $label)
                    <td>
                        <input class="form-control input-sm" type="text" ng-model="item.{{ $prop }}">
                    </td>
                    @endforeach
                    <td>
                        <span class="btn btn-sm btn-default sort-handle"><span class="sr-only">sort item</span><i class="fa fa-sort" role="presentation" aria-hidden="true"></i></span>
                    </td>
                    <td>
                        <button ng-hide="min > -1 && $index < min" class="btn btn-sm btn-default" type="button" ng-click="removeItem(item);"><span class="sr-only">delete item</span><i class="fa fa-trash" role="presentation" aria-hidden="true"></i></button>
                    </td>
                </tr>

            </tbody>

        </table>

        <div class="array-controls btn-group">
            <button class="btn btn-sm btn-default" type="button" ng-click="addItem()"><i class="fa fa-plus"></i> Add {{ $item_name }}</button>
        </div>

    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    {{-- @push('crud_fields_styles')
        {{-- YOUR CSS HERE --}}
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        {{-- YOUR JS HERE --}}

        <script>
            angular.module('backpackTable', ['ui.sortable'], function($interpolateProvider){
                $interpolateProvider.startSymbol('<%');
                $interpolateProvider.endSymbol('%>');
            })
            .controller('tableController', function($scope){
                $scope.sortableOptions = {
                    handle: '.sort-handle'
                };
                $scope.addItem = function(){
                    if( $scope.max > -1 ){
                        if( $scope.items.length < $scope.max ){
                            var item = {};
                            $scope.items.push(item);
                        } else {
                            new PNotify({
                                title: $scope.maxErrorTitle,
                                text: $scope.maxErrorMessage,
                                type: 'error'
                            });
                        }
                    }
                    else {
                        var item = {};
                        $scope.items.push(item);
                    }
                }
                $scope.removeItem = function(item){
                    var index = $scope.items.indexOf(item);
                    $scope.items.splice(index, 1);
                }
                $scope.$watch('items', function(a, b){
                    if( $scope.min > -1 ){
                        while($scope.items.length < $scope.min){
                            $scope.addItem();
                        }
                    }
                    if( typeof $scope.items != 'undefined' && $scope.items.length ){
                        if( typeof $scope.field != 'undefined'){
                            if( typeof $scope.field == 'string' ){
                                $scope.field = $($scope.field);
                            }
                            $scope.field.val( angular.toJson($scope.items) );
                        }
                    }
                }, true);
                if( $scope.min > -1 ){
                    for(var i = 0; i < $scope.min; i++){
                        $scope.addItem();
                    }
                }
            });
        </script>

    @endpush
@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}