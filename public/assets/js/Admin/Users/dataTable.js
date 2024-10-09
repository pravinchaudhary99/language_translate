var UserList = function() {
    var datatable;
    var initTable = function() {
        var table = $('#kt_table_users');

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
                url: '/users/list',
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
                { 
                    data: 'created_at',
                    orderable: false,
                    render: function(data, type, full) {
                        return full.role.name ?? ''
                    }
                },
                { data: 'id'},
            ],
            columnDefs: [{
                targets: 0,
                orderable: false,
                render: function(data, type, full, meta) {
                    let start = datatable.ajax.params().start;
                    return start + meta.row + 1;
                },
            },{
                targets: -1,
                title: 'Actions',
                orderable: false,
                className: 'text-center',
                render: function(data, type, full, meta) {
                    var actions = `
                        <a href="javascript:void(0);" class="btn btn-sm btn-icon editUser" data-id="${data}" title="source translation"><i class="la fs-2 text-opacity-75 la-edit text-warning"></i></a>
                        <a href="javascript:void(0);" class="btn btn-sm btn-icon deleteUser" data-id="${data}" title="source translation"><i class="la fs-2 text-opacity-75 la-trash text-danger"></i></a>
                    `;
                    return actions;
                }

            }],
        })
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-user-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    }

    $(document).on("click", ".deleteUser", function() {
        const id = $(this).attr('data-id');

        Swal.fire({
            text: "Are you sure you want to delete user?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            showCloseButton: true,
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-light"
            }
        }).then(function(result) {
            if (result.value) {
                $.ajax({
                    method: "DELETE",
                    url: '/users/destroy/' + id,
                    success: function(response) {
                        toastr.success(response.message ?? 'User has been deleted successfully');
                        datatable.ajax.reload(null, false);
                    },
                    error: function(response) {
                        toastr.error(response.responseJSON.error ?? 'something went wrong');
                    }
                });
            }
        })
    });

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
    UserList.init()
}));