<div class="modal fade" id="show_tag" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">View Tag</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-secondary">
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Name</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-6" id="name_tag">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Slug</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-6" id="tag_slug">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Used By</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-6" id="tag_use">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h6 class="heading-small"><strong>Status</strong></h6>
                    </div>
                    <div class="col-md-1">
                        :
                    </div>
                    <div class="col-md-6" id="tag_status">
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-md-12 text-center">
                        <a href="#" class="heading-small" id="tag_view"><strong>View Items</strong></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('#show_tag').on('show.bs.modal', function (e) {
    var dataset = e.relatedTarget.dataset
    var tag_name = dataset.name;
    var tag_slug = dataset.slug;
    var tag_status = dataset.status;
    var tag_use = dataset.use;
    var tag_view = dataset.view;

    $('#name_tag').text(tag_name);
    $('#tag_slug').text(tag_slug);
    $('#tag_use').text(tag_use+' Item(s)');
    var color = 'danger';
    var title = 'rejected';

    if (tag_status == 0) {
        color = 'primary';
        title= 'pending';
    } else if (tag_status == 1) {
        color = 'success';
        title= 'accepted';
    }
    $('#tag_status').html('<span class="badge badge-dot"><i class="bg-'+color+'"></i> '+title+'</span>');

    $('#tag_view').attr('href', tag_view);
})
</script>
