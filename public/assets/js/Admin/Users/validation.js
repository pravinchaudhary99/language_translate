var userCreateForm = function() {
    var handleForm = function() {
        var formElement = document.getElementById('add_user_form');
        var validator = FormValidation.formValidation(
            formElement, {
                fields: {
                    'name': {
                        validators: {
                            notEmpty: {
                                message: messages.required.replace(':attribute', $("input[name=name]").attr('data-label'))
                            },
                        }
                    },
                    'email': {
                        validators: {
                            notEmpty: {
                                message: messages.required.replace(':attribute', $("input[name=email]").attr('data-label'))
                            },
                        }
                    },
                    'role': {
                        validators: {
                            notEmpty: {
                                message: messages.required.replace(':attribute', $("#userRoleSelect").attr('data-label'))
                            },
                        }
                    },
                    'password': {
                        validators: {
                            notEmpty: {
                                message: messages.required.replace(':attribute', $("input[name=password]").attr('data-label'))
                            },
                        }
                    },
                    'password_confirmation': {
                        validators: {
                            identical: {
                                compare: function () {
                                    return formElement.querySelector('[name="password"]').value;
                                },
                                message: messages.required.replace(':attribute', $("input[name=password_confirmation]").attr('data-label'))
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

        document.getElementById("userFormSubmit").addEventListener("click", function(e) {
            e.preventDefault();
            var submitButton = this;
            var id = $("#userFormSubmit").attr('data-update');
            var data = new FormData($("#add_user_form")[0]);

            var url = '/users/store';
            if(id) {
                url = '/users/update/' + id
            }

            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                    $.ajax({
                            method: "POST",
                            url: url,
                            data: data,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                submitButton.setAttribute("data-kt-indicator", "on");
                            },
                            success: function(response) {
                                if(response.success) {
                                    toastr.success(response.message ?? 'User has been updated successfully')
                                }
                                $("#add_user_form").trigger("reset");
                                $("#userRoleSelect").val(null).trigger("change");
                                $("#kt_modal_add_user").modal("hide");
                                datatable.ajax.reload(null, true);
                                
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

        $(document).on("click", ".editUser", function() {
            const id = $(this).attr('data-id');
    
            $.ajax({
                method: 'get',
                url: '/users/edit/' + id,
                success: function(response) {
                    var user = response.data.user;
                    $("#kt_modal_add_user").modal('show');
                    $("#userFormSubmit").attr('data-update', user.id);
                    $("input[name=name]").val(user.name);
                    $("input[name=email]").val(user.email);
                    $("#userRoleSelect").val(user.role_id).trigger("change");

                    validator.disableValidator('password', 'notEmpty');
                    validator.disableValidator('password_confirmation', 'notEmpty');
                    $("h2.addUser").addClass('d-none');
                    $("h2.editUser").removeClass('d-none');
                },
                error: function(response, textStatus, errorThrown){
                    const errorMessage = getErrorMessage(response);
                    toastr.error(errorMessage);
                }
            });
        });
    };

    $("button[data-bs-target='#kt_modal_add_user']").on("click", function() {
        $("h2.editUser").addClass('d-none');
        $("h2.addUser").removeClass('d-none');
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

    $("#userRoleSelect").select();

    return {
        init: function() {
            handleForm();
        }
    };
}();

KTUtil.onDOMContentLoaded((function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
    userCreateForm.init()
}));