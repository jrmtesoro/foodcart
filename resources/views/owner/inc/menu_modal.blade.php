@if (Route::is('menu.*'))
    <div class="modal fade" id="delete_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hide Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to hide this item?
                </div>
                <div class="modal-footer">
                    <form method="post" id="delete_form" action="">
                        @method('delete') @csrf
                        <input type="hidden" name="delete_id" id="delete_id">
                        <button type="submit" class="btn btn-primary">Yes</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#delete_confirmation').on('show.bs.modal', function (e) {
            var menu_id = e.relatedTarget.dataset.menu;

            @if (Route::is('menu.index'))
            $('#delete_id').val(menu_id);
            @else
            $('#delete_form').attr('action',"{{ url('owner/menu') }}/"+menu_id)
            @endif
        })
    </script>
    @if (Route::is('menu.index'))
    <script>
        $('#delete_form').submit(function (e){
            e.preventDefault;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                    'Accept' : 'application/json'
                },
                url: "{{ url('owner/menu') }}/"+$('#delete_id').val(),
                method: 'DELETE',
                success: function(result){
                    if (!result.success) {
                        Swal.fire({
                            type: 'error',
                            title: 'Hide Item Failed',
                            text: result.message
                        });
                    } else {
                        Swal.fire({
                            type: 'success',
                            title: 'Hide Item Success',
                            text: result.message
                        });
                        $('#delete_confirmation').modal('hide');
                        table.columns.adjust().draw();
                    }
                }
            });
            return false;
        });
    </script>
    @endif
@endif

@if (Route::is('menu.create') || Route::is('menu.edit'))
<div class="modal fade" id="add_category" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-header bg-transparent">
                        <div class="text-muted text-center mt-2"><h3>Add Category</h3></div>
                    </div>
                    <div class="card-body px-lg-5">
                        <form id="add_category_form">
                        <div class="form-group mb-3">
                            <label class="form-control-label">Category Name</label>
                            {!! Form::text('category_name', '', 
                                [
                                    'id' => 'category_name',
                                    'class' => 'form-control form-control-alternative ',
                                    'placeholder' => 'Enter your category name here'
                                ]) !!}
                        </div>
                        <div class="text-center pt-3">
                            <button type="submit" class="btn btn-primary" id="submit_add_category">Submit</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$('#add_category_form').submit(function(e) {
    e.preventDefault;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Authorization' : 'Bearer {{session()->get("token")}}'
        },
        url: "{{ url('api/owner/category') }}",
        method: 'POST',
        data: {
            category_name : $('#category_name').val()
        },
        success: function(result){
            if (!result.success) {
                $('#category_name').addClass('is-invalid');
                var insert = $('<div id="category_name-error-temp" class="error invalid-feedback">'+result.errors.category_name[0]+'</div>');
                insert.insertAfter('#category_name');
                Swal.fire({
                    type: 'error',
                    title: 'Add Category Failed',
                    text: result.message
                });
            } else {
                $('select[name=menu_category]').append('<option value='+result.data.id+'>'+result.data.name+'</option>');

                var category_options = $("select[name=menu_category] option");
                $('select[name=menu_category]').find('option').remove().end();

                var opt = category_options.sort(function (a,b) { 
                    return a.text.toUpperCase().localeCompare(b.text.toUpperCase()) 
                });
                $("select[name=menu_category]").append(opt);
                
                $('#add_category').modal('hide');

                $('#category_name').val('');

                Swal.fire({
                    type: 'success',
                    title: 'Add Category Success',
                    text: result.message
                });
            }
        }
    });
    return false;
});

$('#add_category_form').validate({
    rules: {
        category_name: "required",
        category_name: {
            required: true,
            maxlength: 20,
            minlength: 3,
            alphanumeric: true
        }
    },
    messages: {
        category_name: "Please enter the category name",

        category_name: {
            required: "Please enter the category name",
            maxlength: "Category name must not exceed 20 characters in length",
            minlength: "Category name must be atleast 3 characters in length",
            alphanumeric: "Category name not contain special characters"
        }

    },
    errorElement: "div",
    errorPlacement: function(error, element) {
            var err_id = '#'+error.attr('id')+"-temp";
            if ($(err_id).length) {
                $(err_id).remove();
            } 

            error.addClass("invalid-feedback");
            error.insertAfter(element);
    },
    highlight: function(element, errorClass, validClass) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element, errorClass, validClass) {
        $(element).addClass("is-valid").removeClass("is-invalid");
    }
});

jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[a-zA-Z0-9\s]+$/.test(value);
}, "Letters, numbers, and underscores only please");

</script>
@endif

@if (Route::is('menu.index'))
    <div class="modal fade" id="restore_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Show Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to show this item?
                </div>
                <div class="modal-footer">
                    <form method='post' id="restore_form" action=''>
                    @csrf
                    <input type="hidden" name="restore_id" id="restore_id">
                    <button type="submit" class="btn btn-primary">Yes</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    $('#restore_confirmation').on('show.bs.modal', function (e) {
        var menu_id = e.relatedTarget.dataset.menu;
        $('#restore_id').val(menu_id);
    });
    </script>
    <script>
        $('#restore_form').submit(function (e){
            e.preventDefault;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                    'Accept' : 'application/json'
                },
                url: '{{ route('menu.index') }}/'+$('#restore_id').val()+"/restore",
                method: 'POST',
                success: function(result){
                    if (!result.success) {
                        Swal.fire({
                            type: 'error',
                            title: 'Show Item Failed',
                            text: result.message
                        });
                    } else {
                        Swal.fire({
                            type: 'success',
                            title: 'Show Item Success',
                            text: result.message
                        });
                        $('#restore_confirmation').modal('hide');
                        table.columns.adjust().draw();
                    }
                }
            });
            return false;
        });
    </script>
    
@endif