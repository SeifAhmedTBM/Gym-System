@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.warehouse.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">

            <button class="btn btn-info py-2 mb-2" data-toggle="modal" data-target="#addProduct">
                <i class="fa fa-plus-circle"></i> {{ trans('global.add') }}
            </button>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>
                            {{ trans('cruds.product.fields.id') }}
                        </th>

                        <th>
                            {{ trans('cruds.product.title_singular') }}
                        </th>

                        <th>
                            {{ trans('cruds.account.fields.balance') }}
                        </th>

                        <th>
                            {{ trans('global.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($warehouse->warehouseProducts as $warehouseProduct)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $warehouseProduct->product->name }}
                            </td>
                            <td>
                                {{ $warehouseProduct->balance }}
                            </td>
                            <td>
                                <div class="btn-group">

                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addQuantity" onclick="addQuantity({{ $warehouseProduct->id }})"><i class="fa fa-plus"></i></button>

                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#minusQuantity" onclick="minusQuantity({{ $warehouseProduct->id }})"><i class="fa fa-minus"></i></button>

                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#transfer" onclick="transfer({{ $warehouseProduct->id }})"><i class="fa fa-exchange"></i></button>

                                    <a href="{{ route('admin.product.transactions',$warehouseProduct->product_id) }}" class="btn btn-primary"><i class="fa fa-file"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4" class="text-center">No data available</td>
                    @endforelse
                </tbody>
            </table>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.warehouses.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Remove Quantity -->
<div class="modal fade" id="minusQuantity" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-minus-circle"></i>  <span class="addHead"></span> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="modalForm2" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="out">
                <input type="hidden" name="from_warehouse" value="{{ $warehouse->id }}">
                <div class="alert alert-warning ">
                    <i class="fa fa-exclamation-triangle"></i> <strong class="warning"></strong>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" oninput="this.value = Math.abs(this.value)" min="0" name="quantity" id="quantity" value="{{ 0 }}">
                    </div>

                    <div class="form-group">
                        <label for="notes">{{ trans('global.note_text') }}</label>
                        <textarea name="notes" id="notes" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> {{ trans('global.close') }}</button>
                    <button type="submit" class="btn btn-primary saveBtn"><i class="fa fa-check"></i> {{ trans('global.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Quantity -->
<div class="modal fade" id="addQuantity" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title " id="exampleModalLabel"><i class="fa fa-plus-circle"></i> <span class="addHead"></span> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="modalForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="in">
                <input type="hidden" name="from_warehouse" value="{{ $warehouse->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" oninput="this.value = Math.abs(this.value)" min="0" name="quantity" id="quantity" value="{{ 0 }}">
                    </div>

                    <div class="form-group">
                        <label for="notes">{{ trans('global.note_text') }}</label>
                        <textarea name="notes" id="notes" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> {{ trans('global.close') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer -->
<div class="modal fade" id="transfer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-minus-circle"></i>  <span>{{ $warehouse->name }}</span> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="modalForm3" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="type" value="transfer">
                <input type="hidden" name="from_warehouse" value="{{ $warehouse->id }}">
                <div class="alert alert-warning ">
                    <i class="fa fa-exclamation-triangle"></i> <strong class="warning"></strong>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="to_warehouse">To Warehouse</label>
                        <select name="to_warehouse" id="to_warehouse" class="form-control select2">
                            @foreach ($warehouses as $id => $entry)
                                <option value="{{ $id }}">{{ $entry }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" oninput="this.value = Math.abs(this.value)" min="0" name="quantity" id="tranfer_quantity" value="{{ 0 }}">
                    </div>

                    <div class="form-group">
                        <label for="notes">{{ trans('global.note_text') }}</label>
                        <textarea name="notes" id="notes" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> {{ trans('global.close') }}</button>
                    <button type="submit" class="btn btn-primary saveBtn"><i class="fa fa-check"></i>  {{ trans('global.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add Product -->
<div class="modal fade" id="addProduct" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title " id="exampleModalLabel"><i class="fa fa-plus-circle"></i> {{ trans('global.add').' '. trans('cruds.product.title_singular') }} </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.warehouse-products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="warehouse_id" value="{{ $warehouse->id }}">
                    <div class="form-group">
                        <label for="product_id">{{ trans('cruds.product.title_singular') }}</label>
                        <select name="product_id" id="product_id" class="form-control select2">
                            @foreach ($products as $id => $entry)
                                <option value="{{ $id }}">{{ $entry }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="balance">{{ trans('cruds.warehouseProduct.fields.balance') }}</label>
                        <input type="balance" class="form-control" name="balance" id="balance" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> {{ trans('global.close') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
    <script>
        function addQuantity(id){

            var id = id;

            var url = '{{ route("admin.getWarehouseProduct", ":id")}}';
            
            url = url.replace(':id', id);
            
            $.ajax({
                method: 'GET',
                url: url,
                success: function(response) {

                    var warehouseProdcut_id = id;

                    var url2 = '{{ route("admin.warehouse-products.update", ":id")}}';

                    url2 = url2.replace(':id',warehouseProdcut_id)

                    $(".modalForm").attr('action',url2);

                    $('.addHead').text(response.warehouseProduct.product.name)
                }
            })
        }

        function minusQuantity(id)
        {
            var id = id;

            var url = '{{ route("admin.getWarehouseProduct", ":id")}}';

            url = url.replace(':id', id);
            
            $.ajax({
                method: 'GET',
                url: url,
                success: function(response) 
                {
                    var warehouseProdcut_id = id;

                    var url2 = '{{ route("admin.warehouse-products.update", ":id")}}';

                    url2 = url2.replace(':id',warehouseProdcut_id)

                    $(".modalForm2").attr('action',url2);

                    $('.addHead').text(response.warehouseProduct.product.name);

                    $('.warning').text('The maximum amount that can be withdrawn is ' + response.warehouseProduct.balance);

                    $('#quantity').on('keyup',function(){

                        if (parseInt($('#quantity').val()) <= response.warehouseProduct.balance) {
                            $('#quantity').removeClass('is-invalid').addClass('is-valid');
                            $('.saveBtn').attr('disabled',false);
                        }else{
                            $('#quantity').removeClass('is-valid').addClass('is-invalid');
                            $('.saveBtn').attr('disabled',true);
                        }
                    })
                }
            })
        }

        function transfer(id)
        {
            var id = id;

            var url = '{{ route("admin.getWarehouseProduct", ":id")}}';

            url = url.replace(':id', id);
            
            $.ajax({
                method: 'GET',
                url: url,
                success: function(response) 
                {
                    var warehouseProdcut_id = id;

                    var url2 = '{{ route("admin.warehouse-products.update", ":id")}}';

                    url2 = url2.replace(':id',warehouseProdcut_id)

                    $(".modalForm3").attr('action',url2);

                    $('.warning').text('The maximum amount that can be withdrawn is ' + response.warehouseProduct.balance);

                    $('#tranfer_quantity').on('keyup',function(){
                        if (parseInt($('#tranfer_quantity').val()) <= response.warehouseProduct.balance) {
                            $('#tranfer_quantity').removeClass('is-invalid').addClass('is-valid');
                            $('.saveBtn').attr('disabled',false);
                        }else{
                            $('#tranfer_quantity').removeClass('is-valid').addClass('is-invalid');
                            $('.saveBtn').attr('disabled',true);
                        }
                    })
                }
            })
        }
    </script>
@endsection