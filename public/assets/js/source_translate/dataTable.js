var LanguageList = function() {
    var initTable = function() {
        var table = $('#translationListTable');
        // begin first table
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
                url: '/translations/source-translation/list',
                type: 'post',
                data: function(d) {
                    d.file = $('select[data-kt-translate-table-filter="file"]').val();
                    d.status = $('select[data-kt-translate-table-filter="status"]').val();
                },
                error: function(response, textStatus, errorThrown) {
                    toastr.error(response.responseJSON.message ?? response.responseJSON.errors);
                }
            },
            columns: [
                { data: 'id', orderable: false, },
                { data: 'key' },
                { data: 'value' },
                { data: 'uuid'},
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
                    var action = `
                    <a href="javascript:void(0);" class="btn btn-icon editTranslationButton" title="edit translation" data-id="${data}" data-key="${full.enValue}" data-value="${full.value ?? ''}"><i class="la fs-2 text-opacity-75 la-edit"></i></a>`;
                    return action;
                }

            }],
        })
    };

    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-translate-table-filter="search"]');
        filterSearch.addEventListener('keyup', function(e) {
            datatable.search(e.target.value).draw();
        });

        $(document).on('change', 'select[data-kt-translate-table-filter="file"], select[data-kt-translate-table-filter="status"]', function() {
            datatable.ajax.reload();
        });
    }


    $("div[add-translation-modal-action='close'], button[add-translation-modal-action='cancel']").on("click", function(e) {
        $("#update_translation_value_form").trigger("reset");
        $("#update_translation_value").modal("hide");
    });

    $(document).on('click', '.editTranslationButton', function(e) {
        e.preventDefault();
        const key = $(this).attr('data-key');
        const value = $(this).attr('data-value') ?? '';
        const id = $(this).attr('data-id');

        const modal = $("#update_translation_value");
        modal.find("#englishValue").text(key);
        modal.find("#translationValue").val(value);
        modal.find("#translationFormSubmit").attr('data-id', id)
        modal.modal('show');
    });

    $("#addNewKeySource").on("click", function() {
        $("#add_new_key_translation").modal("show");
    })

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

    var handleForm = function() {
        // Initialize form validation
        var formElement = document.getElementById('add_new_key_translation_form');
        var validator = FormValidation.formValidation(
            formElement, {
                fields: {
                    'file': {
                        validators: {
                            notEmpty: {
                                message: 'File is required'
                            },
                        }
                    },
                    'key': {
                        validators: {
                            notEmpty: {
                                message: 'Key is required'
                            },
                        }
                    },
                    'content': {
                        validators: {
                            notEmpty: {
                                message: 'Content is required'
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
    
        document.getElementById("sourceTranslationFormSubmit").addEventListener("click", function(e) {
            e.preventDefault();
            var submitButton = this;
            var values = {
                'file' : $("select[name='file'] option:selected").val(),
                "key" : $("input[name='key']").val(),
                "content" : $("textarea[name='content']").val()
            }

            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                       $.ajax({
                            method: "POST",
                            url: "/translations/source-translation/store",
                            data: values,
                            beforeSend: function() {
                                submitButton.setAttribute("data-kt-indicator", "on");
                            },
                            success: function(response) {
                                if(response.success) {
                                    toastr.success(response.message ?? 'Phrases has been updated successfully')
                                }
                                $("#add_new_key_translation_form").trigger("reset");
                                $("select[name=file]").val(null).trigger("change");
                                $("#add_new_key_translation").modal("hide");
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