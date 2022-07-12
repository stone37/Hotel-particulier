$(document).ready(function() {
    /**
     * ##############
     * Upload File
     * ##############
     */
    function readURL(input) {

        let url = input.value;
        let ext = url.substring(url.lastIndexOf('.')+1).toLowerCase();

        if (input.files && input.files[0] && (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg')) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $('#image-view').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0])
        }
    }

    $('#entity-image').change(function () { readURL(this)});


    // Password view
    $('.input-prefix.fa-eye').click(function () {
        passwordView($(this));
    });

    // Password generate
    $('#password-generate-btn').click(function(e){
        e.preventDefault();

        generatePassword($('#password-bulk').find('input'));
    })
});




