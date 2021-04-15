@if (Route::is('category.index'))

<div class="modal fade" id="edit_category" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> 
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-header bg-transparent">
                        <div class="text-muted text-center mt-2"><h3>Edit Category</h3></div>
                    </div>
                    <div class="card-body px-lg-5">
                        <form id="edit_category_form">
                        <input type="hidden" id="edit_id">
                        <div class="form-group mb-3">
                            <label class="form-control-label">Category Name</label>
                            {!! Form::text('category_name', '', 
                                [
                                    'id' => 'edit_category_name',
                                    'class' => 'form-control form-control-alternative',
                                    'placeholder' => 'Enter your category name here'
                                ]) !!}
                        </div>
                        <div class="text-center pt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
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
$('#edit_category').on('show.bs.modal', function (e) {
    var category_id = e.relatedTarget.dataset.menu;
    var category_name = e.relatedTarget.dataset.name;

    $('#edit_id').val(category_id);
    $('#edit_category_name').val(category_name);
});

$('#edit_category_form').validate({
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
            var err_id = '#'+error.attr('id');
            if ($(err_id).length) {
                $(err_id).text(error.text());
            } else {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
            }
    },
    highlight: function(element, errorClass, validClass) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element, errorClass, validClass) {
        $(element).addClass("is-valid").removeClass("is-invalid");
    }
});

$('#edit_category_form').submit(function(e) {
    e.preventDefault;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'applcation/json'
        },
        url: "{{ url('owner/category') }}/"+$('#edit_id').val(),
        method: 'POST',
        data: {
            category_name : $('#edit_category_name').val()
        },
        success: function(result){
            if (!result.success) {
                $('#edit_category_name').addClass('is-invalid').removeClass('is-valid');
                if (!$('#category_name-error').length) {
                    var insert = $('<div id="category_name-error" class="error invalid-feedback">'+result.errors.category_name[0]+'</div>');
                    insert.insertAfter('#category_name');
                } else {
                    $('#category_name-error').css('display', 'block');
                    $('#category_name-error').text(result.errors.category_name[0]);
                }
                Swal.fire({
                    type: 'error',
                    title: 'Edit Category Failed',
                    text: result.message
                });
            } else {    
                $('#edit_category').modal('hide');

                Swal.fire({
                    type: 'success',
                    title: 'Edit Category Success',
                    text: result.message
                });

                table.columns.adjust().draw();
            }
        }
    });
    return false;
});
</script>


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
                                    'class' => 'form-control form-control-alternative',
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
                $('#category_name').addClass('is-invalid').removeClass('is-valid');
                if (!$('#category_name-error').length) {
                    var insert = $('<div id="category_name-error" class="error invalid-feedback">'+result.errors.category_name[0]+'</div>');
                    insert.insertAfter('#category_name');
                } else {
                    $('#category_name-error').css('display', 'block');
                    $('#category_name-error').text(result.errors.category_name[0]);
                }
                Swal.fire({
                    type: 'error',
                    title: 'Add Category Failed',
                    text: result.message
                });
            } else {    
                $('#add_category').modal('hide');

                Swal.fire({
                    type: 'success',
                    title: 'Add Category Success',
                    text: result.message
                });

                table.columns.adjust().draw();
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
            var err_id = '#'+error.attr('id');
            if ($(err_id).length) {
                $(err_id).text(error.text());
            } else {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
            }
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
<div class="modal fade" id="delete_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item?
            </div>
            <div class="modal-footer">
                <form id="delete_form" action="">
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

        $('#delete_id').val(menu_id);
    });
</script>
<script>
    $('#delete_form').submit(function (e){
        e.preventDefault;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                'Accept' : 'application/json'
            },
            url: "{{ url('owner/category') }}/"+$('#delete_id').val(),
            method: 'DELETE',
            success: function(result){
                if (!result.success) {
                    var config = {
                        type: 'error',
                        title: 'Delete Category Failed',
                        text: result.message
                    };

                    if (typeof result.footer != "undefined") {
                        config.footer = result.footer;
                    }

                    Swal.fire(config);
                } else {
                    Swal.fire({
                        type: 'success',
                        title: 'Delete Category Success',
                        text: result.message
                    });
                    table.columns.adjust().draw();
                }
                $('#delete_confirmation').modal('hide');
            }
        });
        return false;
    });
</script>

<div class="modal fade" id="restore_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to restore this item?
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
            url: '{{ route('category.index') }}/'+$('#restore_id').val()+"/restore",
            method: 'POST',
            success: function(result){
                if (!result.success) {
                    Swal.fire({
                        type: 'error',
                        title: 'Restore Item Failed',
                        text: result.message
                    });
                } else {
                    Swal.fire({
                        type: 'success',
                        title: 'Restore Item Success',
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
