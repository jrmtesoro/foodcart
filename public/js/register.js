$(document).ready(function() {
    $('#register_form').validate({
        rules: {
            reg_fname: "required",
            reg_lname: "required",
            reg_contact_number: "required",
            reg_email: "required",
            reg_password: "required",
            reg_password1: "required",

            reg_fname: {
                required: true
            },
            reg_lname: {
                required: true
            },
            reg_contact_number: {
                required: true,
                minlength: 7,
                maxlength: 11,
                digits: true,
                validMobile: true
            },
            reg_email: {
                required: true,
                email: true
            },
            reg_password: {
                required: true,
                passwordStrength: true,
                minlength: 8,
                maxlength: 21
            },
            reg_password1: {
                required: true,
                equalTo: "#reg_password"
            }
        },
        messages: {
            reg_fname: "Please enter your First Name",
            reg_lname: "Please enter your Last Name",
            reg_contact_number: "Please enter your Contact Number",
            reg_email: "Please enter your Email Address",
            reg_password: "Please provide a password",
            reg_password1: "Please provide a password",
            reg_fname: {
                required: "Please enter your First Name"
            },
            reg_lname: {
                required: "Please enter your Last Name"
            },
            reg_contact_number: {
                required: "Please enter your Contact Number",
                minlength: "Contact Number should be a landline or mobile number",
                maxlength: "Contact Number should be a landline or mobile number",
                digits: "Contact Number should be in numeric form",
                validMobile: "Invalid mobile contact number"
            },
            reg_email: {
                required: "Please enter your Email Address",
                email: "Please enter a valid Email Address"
            },
            reg_password: {
                required: "Please provide a password",
                minlength: "Password must be atleast 8 characters in length",
                maxlength: "Password must not exceed 21 characters in length"
            },
            reg_password1: {
                required: "Please provide a password",
                equalTo: "Please enter the same password as above"
            }
        },
        errorElement: "div",
        errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).addClass("is-valid").removeClass("is-invalid");
        }
    });

    jQuery.validator.addMethod("validMobile", function(value, element) {
        return this.optional(element) || /^[0][9][1-9]\d{8}$|^[1-9]\d{6}$/.test(value);
    },"Invalid mobile contact number");

    jQuery.validator.addMethod("passwordStrength", function(value, element) {
        return this.optional(element) || /^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z])/.test(value);
    },"Password must contain atleast 1 digit, lower case letter, uppercase letter and symbol.");
});