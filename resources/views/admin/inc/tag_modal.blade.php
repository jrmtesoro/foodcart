@if (Route::is('tag.index'))
<div class="modal fade" id="add_tag" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-header bg-transparent">
                        <div class="text-muted text-center mt-2">
                            <h3>Add Tag</h3>
                        </div>
                    </div>
                    <div class="card-body px-lg-5">
                        <form id="add_tag_form">
                            <div class="form-group mb-3">
                                <label class="form-control-label">Tag Name</label> {!! Form::text('tag_name', '',
                                [ 
                                    'id' => 'tag_name',
                                    'class' => 'form-control form-control-alternative',
                                    'placeholder' => 'Enter your tag name here',
                                    'required' => true
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

$('#add_tag_form').validate({
    rules: {
        tag_name: "required",
        tag_name: {
            required: true,
            maxlength: 21,
            minlength: 5
        }
    },
    messages: {
        tag_name: "Please enter the category name",

        tag_name: {
            required: "Please enter the category name",
            maxlength: "Category name must not exceed 21 characters in length",
            minlength: "Category name must be atleast 5 characters in length"
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

$('#add_tag_form').submit(function(e) {
    e.preventDefault;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
        },
        url: "{{ route('tag.store') }}",
        method: 'POST',
        data: {
            tag_name : $('#tag_name').val()
        },
        success: function(result){
            if (!result.success) {
                if (!$('#tag_name-error').length) {
                    var insert = $('<div id="tag_name-error" class="error invalid-feedback">'+result.errors.tag_name[0]+'</div>');
                    insert.insertAfter('#tag_name');
                } else {
                    $('#tag_name-error').css('display', 'block');
                    $('#tag_name-error').text(result.errors.tag_name[0]);
                }
                Swal.fire({
                    type: 'error',
                    title: 'Add Tag Failed',
                    text: result.message
                });
                $('input[name=tag_name]').removeClass('is-valid').addClass('is-invalid');
            } else {    
                $('#add_tag').modal('hide');

                Swal.fire({
                    type: 'success',
                    title: 'Add Tag Success',
                    text: result.message
                });

                table.columns.adjust().draw();
            }
        }
    });
    return false;
});
</script>
@endif
