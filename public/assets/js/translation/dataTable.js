var LanguageList = function() {
    var initTable = function() {
        var table = $('#languageListTable');

        // begin first table
        datatable = table.DataTable({
            scrollX: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [1, 'asc'],
            ajax: {
                url: '/translations/list',
                type: 'post',
                error: function(response, textStatus, errorThrown) {
                    toastr.error(response.responseJSON.message ?? response.responseJSON.errors);
                }
            },
            columns: [
                { data: 'id', orderable: false, },
                { data: 'language_name' },
                {   data: 'phrases_count',
                    orderable: false,
                    render: function(data, type, full) {
                        if(full.language.code == 'en') {
                            return data + ' source keys';
                        }
                        var progress = parseInt(full.progress ?? 0);
                       
                        var line = `<div class="progress h-5px w-100">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: ${progress}%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>`;
                        return line;
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
                   
                    if(full.language.code == 'en') {
                        return `<a href="/translations/source-translation" class="btn btn-icon" title="source translation"><i class="la fs-2 text-opacity-75 la-cog"></i></a>`;
                    }
                    var action = `<a href="/translations/phrases/${data}" class="btn btn-icon" title="edit translation" data-id="${data}"><i class="la fs-2 text-opacity-75 la-plus"></i></a>
                    <a href="javascript:void(0)" class="btn btn-icon deleteTranslation" title="delete translation" data-id="${data}"><i class="la fs-2 text-opacity-75 la-trash"></i></a>`
                    return action;
                }

            }],
        })
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-language-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });
    }

    $("#languageSelect").select2();

    $("div[language-modal-action='close'], button[language-modal-action='cancel']").on("click", function(e) {
        $("#add_language_form").trigger("reset");
        $("#languageSelect").val(null).trigger("change");
        $("#kt_modal_add_language").modal("hide");
    });

    $(document).on('click', 'a.deleteTranslation', function() {
        var deleteId = $(this).attr('data-id');

        Swal.fire({
            text: "Are you sure you want to delete translation?",
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
                    url: '/translations/destroy/' + deleteId,
                    success: function(response) {
                        toastr.success(response.message ?? 'Translation has been deleted successfully');
                        datatable.ajax.reload(null, false);
                    },
                    error: function(response) {
                        toastr.error(response.responseJSON.error ?? 'something went wrong');
                    }
                });
            }
        })
    });

    var handleForm = function() {
        // Initialize form validation
        var formElement = document.getElementById('add_language_form');
        var validator = FormValidation.formValidation(
            formElement, {
                fields: {
                    'language': {
                        validators: {
                            notEmpty: {
                                message: 'Language name is required'
                            },
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );
    
        document.getElementById("languageFormSubmit").addEventListener("click", function(e) {
            e.preventDefault();
            var submitButton = this;
            var language = $("select[name=language] option:selected").val();
            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                       $.ajax({
                            method: "POST",
                            url: "/translations/store",
                            data: { "language" : language},
                            beforeSend: function() {
                                submitButton.setAttribute("data-kt-indicator", "on");
                            },
                            success: function(response) {
                                if(response.success) {
                                    toastr.success(response.message ?? 'Language has been updated successfully')
                                }
                                $("#add_language_form").trigger("reset");
                                $("#languageSelect").val(null).trigger("change");
                                $("#kt_modal_add_language").modal("hide");
                                datatable.ajax.reload(null, false);
                            },
                            error: function(response) {
                                const errorMessage = getErrorMessage(response);
                                toastr.error(errorMessage);
                            },
                            complete: function(){
                                submitButton.setAttribute("data-kt-indicator", "off");
                            }
                       });
                       
                    } else {
                        console.log("Form validation failed");
                    }
                });
            }
        });
    };

    $("#publicTranslateFile").on("click", function() {
        $.ajax({
            method: "POST",
            url: "/translations/public",
            success: function(response) {
                if(response.success) {
                    toastr.success(response.message ?? 'Publish has been updated successfully')
                }
                setTimeout(() => {
                    location.href = '/translations'
                }, 2000);
            },
            error: function(response) {
                const errorMessage = getErrorMessage(response);
                toastr.error(errorMessage);
            }
       });
    });

    function getErrorMessage(response) {
        if (!response || !response.responseJSON) {
            return 'Something went wrong';
        }
    
        const { errors, message } = response.responseJSON;
    
        if (errors && errors.length > 0) {
            return errors;
        }
    
        if (message) {
            return message;
        }
    
        return 'Something went wrong';
    }

    return {
        init: function() {
            initTable();
            handleSearchDatatable();
            handleForm();
        }
    }
}();

KTUtil.onDOMContentLoaded((function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
    LanguageList.init()
}));