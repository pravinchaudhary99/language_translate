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
            var data = new FormData($("#add_user_form")[0]);

            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                    $.ajax({
                            method: "POST",
                            url: "/users/store",
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