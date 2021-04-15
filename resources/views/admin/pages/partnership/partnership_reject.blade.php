<div class="modal fade" id="reject_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
            </div>
            <div class="modal-body">
                Are you sure you want to reject this application?
            </div>
            <div class="modal-footer">
                <form method="post" id="reject_form">
                    <input type="hidden" name="reject_id" id="reject_id">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#reject_confirmation').on('show.bs.modal', function (e) {
        var tag_id = e.relatedTarget.dataset.id;

        $('#reject_id').val(tag_id);
    })
</script>
<script>
    $('#reject_form').submit(function (e){
        e.preventDefault;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                'Accept' : 'application/json'
            },
            url: "{{ url('admin/partnership/') }}/"+$('#reject_id').val()+"/reject",
            method: 'GET',
            success: function(result){
                if (!result.success) {
                    Swal.fire({
                        type: 'error',
                        title: 'Reject Application Failed',
                        text: result.message
                    });
                } else {
                    Swal.fire({
                        type: 'success',
                        title: 'Reject Application Success',
                        text: result.message
                    });
                    $('#reject_confirmation').modal('hide');

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