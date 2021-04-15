<div class="modal fade" id="signInModal" tabindex="-1" role="">
    <div class="modal-dialog modal-login" role="document">
        <div class="modal-content">
            <div class="card card-signup card-plain">
                <div class="modal-header bg-light">
                    <div class="card-header card-header-text card-header-info w-100" style="z-index: 999; background: #c40514 !important;">
                        <div class="card-text d-flex">
                            <h4 class="card-title m-0">Sign In</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                <i class="material-icons text-white">clear</i>
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::open(['route' => 'guest.login', 'action' => 'POST']) !!}
                <div class="modal-body pb-0 bg-light">
                    <p class="description text-center">Sign in with credentials</p>
                    <div class="card-body">
                        <div class="form-group pt-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="material-icons">email</i></div>
                                </div>
                                <input type="text" name="login_email" class="form-control" placeholder="Email Address" required>
                            </div>
                        </div>
                        <div class="form-group pt-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="material-icons">lock_outline</i>
                                    </div>
                                </div>
                                <input type="password" name="login_password" placeholder="Password" class="form-control" required>
                            </div>
                        </div>
                        <div class="text-right">
                            <a class="my-auto text-warning" href="{{ route('forgot') }}">Forgot Password?</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center bg-light">
                    <button class="btn btn-warning btn-round" type="submit" id="sign_in">Sign In</button>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-round ml-3 red-color">Register</a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cartModal" tabindex="-1" role="">
    <div class="modal-dialog modal-lg modal-login" role="document">
        <div class="modal-content">
            <div class="card card-signup card-plain">
                <div class="modal-header bg-light">
                    <div class="card-header card-header-text card-header-info w-100" style="z-index: 999; background: #c40514 !important;">
                        <div class="card-text d-flex">
                            <h4 class="card-title m-0">Cart</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                <i class="material-icons text-white">clear</i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-body pb-0 bg-light" id="cart_body">
                </div>
                <div class="modal-footer justify-content-center bg-light pt-3">
                    <button type="button" class="btn btn-danger ml-3" id="empty_cart">Empty Cart</button>
                    <a href="{{ route('guest.checkout') }}" class="btn btn-primary ml-3 disabled" id="checkout">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$('#cartModal').on('show.bs.modal', function () {
    loadCart();
});

function loadCart() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'applcation/json'
        },
        url: "{{ route('cart.index') }}",
        method: 'GET',
        success: function(result) {
            $('#cart_body').html(result.html);
            getCartCount();
            if (result.success) {
                $('#empty_cart').removeAttr('disabled');
                $('#checkout').removeClass('disabled');
            } else {
                $('#empty_cart').attr('disabled', 'disabled');
                $('#checkout').addClass('disabled');
            }
        }
    });
}

function deleteBtn(button) {
    var cart_id = $(button)[0].dataset.id;
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'applcation/json'
        },
        url: "{{ url('guest/cart') }}/"+cart_id,
        method: 'delete',
        success: function(result) {
            if (result.success) {
                loadCart();
                iziToast.success({
                    title: 'Hey',
                    color: 'green',
                    theme: 'light',
                    icon: 'fa fa-cart-plus',
                    title: 'Success!',
                    message: result.message
                });
            }
        }
    });
}

$('#empty_cart').click(function(e) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'applcation/json'
        },
        url: "{{ route('cart.empty') }}",
        method: 'get',
        success: function(result){
            getCartCount();
            $('#cart_body').html(result.html);
            loadCart();
            iziToast.success({
                title: 'Hey',
                color: 'green',
                theme: 'light',
                icon: 'fa fa-cart-plus',
                title: 'Success!',
                message: result.message
            });
        }
    });
});
</script>

  
<!-- Modal -->
<div class="modal fade" id="editQuantity" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Quantity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="alert alert-danger d-none" id="quantityErrorContainer">
                <div class="h6" id="quantityError">
                </div>
            </div>
            <div class="modal-body mx-auto">
                <form id="cartUpdate">
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" class="form-control" min="1" id="quantity" onKeyDown="return false" style="width: 55px; text-align: center;" required>
                    <input type="hidden" id="cart_id">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="quantityButton">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$('#editQuantity').on('show.bs.modal', function (e) {
    var cart_id = e.relatedTarget.dataset.id;
    var quantity = e.relatedTarget.dataset.quantity;

    $('#cart_id').val(cart_id);
    $('#quantity').val(quantity);
});

$('#quantityButton').click(function(e) {
    e.preventDefault;

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
            'Accept' : 'applcation/json'
        },
        url: "{{ url('guest/cart') }}/"+$('#cart_id').val(),
        method: 'post',
        data: {
            quantity : $('#quantity').val()
        },
        success: function(result){
            if (!result.success) {
                $('#quantityError').html(result.html);
                $('#quantityErrorContainer').removeClass('d-none');
            } else {
                $('#quantityErrorContainer').addClass('d-none');
                iziToast.success({
                    title: 'Hey',
                    color: 'green',
                    theme: 'light',
                    icon: 'fa fa-cart-plus',
                    title: 'Success!',
                    message: result.message
                });
                $('#editQuantity').modal('hide');
                loadCart();

                @if (Route::is('guest.checkout'))
                location.reload();
                @endif
            }
        }
    });

    return false;
});
</script>