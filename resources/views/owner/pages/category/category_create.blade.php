<div class="modal fade" id="request_category_modal" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered" role="document">
        <div class="modal-content"> 
            <div class="modal-body p-0">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-header bg-transparent">
                        <div class="text-muted text-center mt-2"><h3>Request Category</h3></div>
                    </div>
                    <div class="card-body px-lg-5">
                        {!! Form::open(['route' => 'owner.category.store', 'method' => 'post', 'id' => 'request_category_form']) !!}
                        <div class="form-group mb-3">
                            <label><small class="text-muted">Category Name</small></label>
                            {!! Form::text('category_name', old('category_name') ?? '', 
                                [
                                    'class' => 'form-control form-control-alternative '.($errors->has('category_name') ? 'is-invalid' : ''),
                                    'placeholder' => 'Enter your category name here'
                                ]) !!}
                            @if ($errors->first('category_name'))
                            <div id="category_name-error-temp" class="error invalid-feedback">{{$errors->first('category_name')}}</div>
                            @endif
                        </div>
                        <div class="text-center text-muted mb-2">
                            <small>Categories will be sent to the administrator for reviews</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">Close</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>