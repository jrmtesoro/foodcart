<div class="modal fade" id="review_confirmation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
            </div>
            <div class="modal-body">
                Are you sure you want to review this application?
            </div>
            <div class="modal-footer">
                <form method="post" id="review_form">
                    <input type="hidden" name="review_id" id="review_id">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#review_confirmation').on('show.bs.modal', function (e) {
            var tag_id = e.relatedTarget.dataset.id;
    
            $('#review_id').val(tag_id);
        })
</script>
<script>
    $('#review_form').submit(function (e){
            e.preventDefault;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content'),
                    'Accept' : 'application/json'
                },
                url: "{{ url('admin/partnership/') }}/"+$('#review_id').val()+"/review",
                method: 'GET',
                success: function(result){
                    if (!result.success) {
                        Swal.fire({
                            type: 'error',
                            title: 'Review Application Failed',
                            text: result.message
                        });
                    } else {
                        Swal.fire({
                            type: 'success',
                            title: 'Review Application Success',
                            text: result.message
                        });
                        $('#review_confirmation').modal('hide');

                        @if (Route::is('partnership.index'))
                        table.columns.adjust().draw();
                        @elseif (Route::is('partnership.show'))
                        document.location.reload(true);
                        @endif
                    }
                }
            });
            return false;
        });
</script>