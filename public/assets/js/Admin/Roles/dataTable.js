var RoleTable = function() {
    var datatable;
    var initTable = function() {
        var table = $('#kt_roles_view_table');
        var id = table.attr('data-id');
        if(id){
            datatable = table.DataTable({
                scrollX: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                language: {
                    processing: translations.processing,
                    info: translations.info,
                    infoEmpty: translations.infoEmpty,
                },
                order: [1, 'asc'],
                ajax: {
                    url: '/roles/list/'+id,
                    type: 'post',
                    error: function(response, textStatus, errorThrown) {
                        toastr.error(response.responseJSON.message ?? response.responseJSON.errors);
                    }
                },
                columns: [
                    { 
                        data: 'id', 
                        orderable: false
                    },
                    { data: 'name' },
                    { data: 'email'},
                ],
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    render: function(data, type, full, meta) {
                        let start = datatable.ajax.params().start;
                        return start + meta.row + 1;
                    },
                }],
            })
        }
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-roles-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    }

    return {
        init: function() {
            initTable();
            handleSearchDatatable();
        }
    }
}();

KTUtil.onDOMContentLoaded((function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
    RoleTable.init()
}));