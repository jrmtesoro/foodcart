$('#loading_screen').removeClass('d-none');

var table = $("#owner_menu_table").DataTable({
    serverSide : true,
    autoWidth : true,
    order: [[ 2, "desc" ]],
    dom : 
        "<'row'<'col px-0'tr>>" +
        "<'card-footer'<'row'<'col-sm-12 col-lg-6'i><'col-sm-12 col-lg-6'p>>>",
    ajax : {
        "url" : datatables_link,
        "type" : "get",
        "contentType": 'application/json',
        'data' : function (d) {
            d.search = $('#search').val();
            d.start_date = $('#start_date').val();
            d.end_date = $('#end_date').val();
            d.show_trash = $('#show_trash').val();
        }
    },
    initComplete : function( settings, json ) {
        $('#loading_screen').addClass('d-none');
    },
    columns: [
        { data: 'name', name:'name' },
        { data: 'used_by', name:'used_by' },
        { data: 'created_at', name:'created_at' },
        { data: 'deleted_at', name:'deleted_at', visible: false },
        { data: 'action', name:'action', orderable: false, searchable: false }
    ],
    oLanguage : {
        oPaginate : {
            sNext : '<i class="fas fa-angle-right"></i>',
            sPrevious : '<i class="fas fa-angle-left"></i>'
        }
    },
});

$('#length_change').val(table.page.len());

$('#length_change').change( function() { 
    table.page.len( $(this).val() ).draw();
});

$('#show_trash').change(function (){
    if (this.value == 'trash') {
        table.column(3).visible(true);
        table.column(2).visible(false);
    } else if (this.value == 'without') {
        table.column(3).visible(false);
        table.column(2).visible(true);
    } else {
        table.column(3).visible(true);
        table.column(2).visible(true);
    }
    table.columns.adjust().draw();
});

$('#filter_form').on('submit', function(e) {
    table.columns.adjust().draw();
    e.preventDefault();
});