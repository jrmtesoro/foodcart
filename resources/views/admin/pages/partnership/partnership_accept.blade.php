<div class="modal fade" id="accept_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accept Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
            </div>
            <div class="modal-body">
                Are you sure you want to accept this application?
            </div>
            <div class="modal-footer">
                <form method="post" id="accept_form">
                    <input type="hidden" name="accept_id" id="accept_id">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#accept_confirmation').on('show.bs.modal', function (e) {
            var tag_id = e.relatedTarget.dataset.id;
    
            $('#accept_id').val(tag_id);
        })
</script>
<script>
    $('#accept_form').submit(function (e){
            e.preventDefault;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                    'Accept' : 'application/json'
                },
                url: "{{ url('admin/partnership/') }}/"+$('#accept_id').val()+"/accept",
                method: 'GET',
                success: function(result){
                    if (!result.success) {
                        Swal.fire({
                            type: 'error',
                            title: 'Accept Application Failed',
                            text: result.message
                        });
                    } else {
                        Swal.fire({
                            type: 'success',
                            title: 'Accept Application Success',
                            text: result.message
                        });
                        $('#accept_confirmation').modal('hide');

                        window.open("{{ url('email/restaurant/verification') }}"+"/"+result.data.email+"/"+result.data.password);
                        @if (Route::is('partnership.show'))
                        document.location.reload(true);
                        @endif

                        table.columns.adjust().draw();
                    }
                }
            });
            return false;
        });
</script>