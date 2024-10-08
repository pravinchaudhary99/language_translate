var RoleValidation = function() {
    var handleForm = function() {
        // Initialize form validation
        var formElement = document.getElementById('kt_modal_add_role_form');
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
                    'permissions[]': {
                        validators: {
                            notEmpty: {
                                message: messages.required.replace(':attribute', $("input[name='permissions[]']").attr('data-label'))
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
    
        document.getElementById("rolesAddSubmitButton").addEventListener("click", function(e) {
            e.preventDefault();
            var submitButton = this;
            var id = $("#rolesAddSubmitButton").attr('data-update');
            var formData = new FormData($("#kt_modal_add_role_form")[0]);
            
            var url = "/roles/store";
            if(id){
                url = "/roles/update/"+id;
            }

            if (validator) {
                validator.validate().then(function(status) {
                    if (status === 'Valid') {
                       $.ajax({
                            method: "POST",
                            url: url,
                            data: formData,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                submitButton.setAttribute("data-kt-indicator", "on");
                            },
                            success: function(response) {
                                if(response.success) {
                                    toastr.success(response.message ?? 'Phrases has been updated successfully')
                                }
                                $("#kt_modal_add_role_form").trigger("reset");
                                $("#kt_modal_add_role").modal("hide");
                                location.reload();
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

    $(document).on("click", "#kt_modal_add_role #kt_roles_select_all", function() {
        var modal = $("#kt_modal_add_role");
        if(this.checked){
            modal.find("input[name='permissions[]']").prop("checked",true);
        }else{
            modal.find("input[name='permissions[]']").prop("checked",false);
        }
    });

    $(document).on("click", "#kt_modal_add_role input[name='permissions[]']", function() {
        const modal = $("#kt_modal_add_role");
        const permissions = modal.find("input[name='permissions[]']");
        const totalLength = permissions.length;
        const checkedLength = permissions.filter(":checked").length;
    
        modal.find("#kt_roles_select_all").prop("checked", totalLength === checkedLength);
    });

    $(".editRoleAndPermission").on("click", function() {
        const name = $(this).attr("data-role");
        const id = $(this).attr("data-id");
        const permissions = $(this).data("permissions");
        const modal = $("#kt_modal_add_role");
        const permissionsAll = modal.find("input[name='permissions[]']").length;
        
        modal.find("input[name=name]").val(name);
        modal.find("input[name='permissions[]']").prop("checked", false);
        permissions.forEach(element => {
            modal.find(`input[name='permissions[]'][value='${element}']`).prop("checked", true);
        });

        $("#rolesAddSubmitButton").attr('data-update', id);
        $(".addRole").addClass("d-none");
        $(".editRole").removeClass("d-none");
        
        modal.find("#kt_roles_select_all").prop("checked", permissions.length === permissionsAll);
    });

    return {
        init: function() {
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
    RoleValidation.init()
}));