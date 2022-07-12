$(document).ready(function() {
    /**
     * ##############
     * Upload File
     * ##############
     */

    let $ordPhoto = [];
    let $nbPhotos = nbrPhoto;
    let $body = $('body');

    console.log($ordPhoto);
    console.log($nbPhotos);

    $('.btn-photos').on('click', function(){
        $('input[type="file"].input-photo').trigger('click');
    });

    $("#photosAnnonce").dmUploader({
        allowedTypes: 'image/*',
        maxFileSize: 8388608, // 8 Megs max
        extFilter: ['jpg', 'jpeg','png','gif'],
        url: Routing.generate('app_image_upload_add'),
        onFallbackMode: function(){
            console.log('Callback: onFallbackMode');
        },
        onDragEnter: function(){
            this.addClass('active');
        },
        onDragLeave: function(){
            this.removeClass('active');
        },
        onInit: function(){
            console.log('Callback: Plugin initialized');
        },
        onNewFile: function(id, file){
            if($ordPhoto.length === 0) {
                $('#imgUpload-dropdiv').hide();
            }

            if (typeof FileReader !== "undefined"){
                var reader = new FileReader();

                reader.onload = function (e) {
                    // Photo chargée, on affiche
                    ajoutPhotoLoaded({id: id, src: e.target.result});
                };
                reader.readAsDataURL(file);
            }

            // Compteur nbPhoto
            $ordPhoto.push(id);

            // Affichage de la div contenant la photo (en attendant le chargement)
            ajoutPhoto({id: id, name: file.name, size: file.size, uploaded: false});

            if ($ordPhoto.length > (nbrPhoto-1)) {

                if ($ordPhoto.length === nbrPhoto) {
                    $('#imgUpload-list .imgUpload-add > div.btn-image-upload').addClass('disabled');
                }

                if ($ordPhoto.length > nbrPhoto && $nbPhotos === nbrPhoto) {
                    activeUPHOTO(0);

                    notification(
                        'Plus de visibilité ?',
                        "Seules les 3 premières photos seront visibles. " +
                        "<br>Vous pouvez déposer jusqu'a "+photoPay+" photos supplémentaires en activant l'option.",
                        {"timeOut": "10000", "closeButton": true},
                        'error');
                }

                $('#imgUpload-add').addClass('option-photo');
                $('#Upsell_PhotosSupp').show();
            }

            if ($ordPhoto.length === (nbrPhoto)) {
                $('#imgUpload-list .imgUpload-add > div.btn-image-upload').addClass('disabled');
            }

        },
        onUploadProgress: function(id, percent){
            $('#'+id+' .progresslabel').html("Envoi "+percent+'%');
            $('#'+id+' circle').css({"stroke-dasharray": percent+" 100"});
        },
        onUploadSuccess: function(id, data){

            $('#'+id+'').addClass('ok');
            $('#'+id+' .progresslabel').html("Envoi terminé");
            $('#'+id+' .progress').addClass('hide');
            $('#'+id+' .remove').show().addClass('show');
            ajoutPhotoLoaded({
                id: id,
                src: data.url
            });
        },
        onUploadError: function(id){
            $('#'+id+'').addClass('error');
            $('#'+id+' .progresslabel').html("Envoi échoué");
            $('#'+id+' .progress').hide();
            $('#'+id+' .remove').show().addClass('show');
        },
        onFileSizeError: function(file){
            console.error('File \'' + file.name + '\' cannot be added: size excess limit');

            notification(
                'Fichier refusé',
                'L\'image est trop volumineuse (supérieur a 8Mo).',
                {"timeOut": "8000", "closeButton": true},
                'error');
        },
        onFileTypeError: function(file){
            console.error('File \'' + file.name + '\' cannot be added: must be an image (type error)');

            notification(
                'Fichier refusé',
                'Le type du fichier n\'est pas supporté.',
                {"timeOut": "8000", "closeButton": true},
                'error');
        },
        onFileExtError: function(file){
            console.error('File \'' + file.name + '\' cannot be added: must be an image (extension error)');

            notification(
                'Fichier refusé',
                'L\'extension du fichier n\'est pas supporté.',
                {"timeOut": "8000", "closeButton": true},
                'error');
        }
    });

    /**
     * Suppression de l'image
     */
    $body.on('click', 'button.remove', function(e) {
        e.preventDefault();

        uploadRemove($(this).attr('img-id'));
    });

    /**
     * Annuler le telechargement de l'image
     */
    $body.on('click', 'button.progress', function(e) {
        e.preventDefault();

        uploadCancel($(this).attr('img-id'));
    });

    /**
     * Gestion de l'option photo
     */
    $('#option_PHOTO').click(function () {

        if ($(this).prop('checked')) {
            activeUPHOTO(1);
        } else {
            activeUPHOTO(0);
        }
    });

    /**
     * Notification quand l'utilisateur n'a pas de photo
     */
    $('#no-image').click(function (e) {
        e.preventDefault();

        notification(
            "Pas de photos ?",
            "vous pourrez ajouter gratuitement vos photos plus tard en quelques secondes.",
            {"timeOut": "8000", "closeButton": true})
    });

    /**
     * Ajouter un bloc image
     *
     * @param file
     */
    function ajoutPhoto(file) {
        let filesize = getReadableFileSizeString(file.size);

        $('#nbPhoto-ac').html(Math.min($ordPhoto.length, $nbPhotos));

        let $html = '<div class="col-lg-3 col-md-4 col-12 scale-up-ver-top rounded' +
            (($ordPhoto.indexOf(file.id) > $nbPhotos-1)?' disabled':'')+'" id="'+file.id+'">';
        $html +=' <div class="img-bloc mx-4 z-depth-4 rounded">';
        $html +='	<img src="'+file.src+'" alt="Image de l\'annonce" class="rounded">';
        $html +='	    <div class="info small font-weight-stone-500">';
        $html +='	        <span>'+file.name+'</span>';
        $html +='	        <span>'+filesize+'</span>';
        $html +='	        <span class="progresslabel">Envoi '+((file.uploaded)?'terminé':'0%')+'</span>';
        $html +='	    </div>';
        $html +='	    <div class="action">';
        $html +='		    <button class="progress '+((file.uploaded)?'hide':'')+'" img-id="'+file.id+'"  title="Annuler"><svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><circle r="16" cx="16" cy="16"></circle></svg></button>';
        $html +='		    <button class="remove '+((file.uploaded)?'show':'')+'" img-id="'+file.id+'" title="Supprimer"><svg viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M11.586 13l-2.293 2.293a1 1 0 0 0 1.414 1.414L13 14.414l2.293 2.293a1 1 0 0 0 1.414-1.414L14.414 13l2.293-2.293a1 1 0 0 0-1.414-1.414L13 11.586l-2.293-2.293a1 1 0 0 0-1.414 1.414L11.586 13z" fill="currentColor" fill-rule="nonzero"></path></svg></button>';
        $html +='	    </div>';
        $html +='	</div>';
        $html +='</div>';

        $('#imgUpload-list .imgUpload-add > div:last').before($html);
    }

    /**
     * Charge une image
     *
     * @param file
     */
    function ajoutPhotoLoaded(file) {
        $('#'+file.id+' img').attr('src', file.src);
    }

    /**
     * Rend la taille de l'image
     *
     * @param fileSizeInBytes
     * @returns {string}
     */
    function getReadableFileSizeString(fileSizeInBytes) {
        let i = -1;

        let byteUnits = [' Ko', ' Mo', ' Go', ' To', 'Po', 'Eo', 'Zo', 'Yo'];

        do {
            fileSizeInBytes = fileSizeInBytes / 1024;
            i++;
        } while (fileSizeInBytes > 1024);

        return Math.max(fileSizeInBytes, 1).toFixed(0) + byteUnits[i];
    }

    /**
     * Supprime une image
     *
     * @param id
     * @returns {boolean}
     */
    function uploadRemove(id) {

        $('#loader .preloader-wrapper').addClass('active');
        $(".page-content").addClass('disabled');

        let pos = $ordPhoto.indexOf(id);
        $ordPhoto.splice(pos, 1);

        $.ajax({
            url: Routing.generate('app_image_upload_delete', {'pos': pos}),
            success: function() {

                $('#'+id).addClass('fade-out-bck');
                setTimeout(function(){ $('#'+id).remove(); }, 400);

                if (pos === 0) {
                    $('#imgUpload-list .imgUpload-add > div:eq(1)').addClass('principale');
                }

                if(pos < 3){
                    $('#imgUpload-list .imgUpload-add > div.disabled').first().removeClass('disabled');
                }

                if ($ordPhoto.length < 2) {
                    $('#Upsell_PhotosSupp').hide();
                }

                $('#nbPhoto-ac').html(Math.min($ordPhoto.length, $nbPhotos));

                $('#loader .preloader-wrapper').removeClass('active');
                $(".page-content").removeClass('disabled');
            }
        });

        if($ordPhoto.length === 0) {
            $('#imgUpload-dropdiv').show();
        }

        return false;
    }

    /**
     * Annule le telechargement de l'image
     *
     * @param id
     * @returns {boolean}
     */
    function uploadCancel(id) {
        $('#photosAnnonce').dmUploader("cancel", id);
        $('#'+id).remove();

        return false;
    }

    /**
     * Active l'option photo
     *
     * @param active
     */
    function activeUPHOTO(active) {
        $nbPhotos = 3;

        if (active){
            $nbPhotos = 12;
            $('#imgUpload-list .imgUpload-add > div').removeClass('disabled');

        } else {
            let set = $('#imgUpload-list .imgUpload-add > div');
            let last = set.length-1;
            set.each(function(i){
                if(i>2 && i<last) $(this).addClass('disabled');
            });

            if ($ordPhoto.length > 2)
                $('#imgUpload-list .imgUpload-add > div.btn-image-upload').addClass('disabled');
        }

        $('#nbPhoto-ac').html(Math.min($ordPhoto.length, $nbPhotos));
        $('#nbPhoto-info').html($nbPhotos);
    }

    /**
     * Affiche des notifications
     *
     * @param titre
     * @param message
     * @param options
     * @param type
     */
    function notification (titre, message, options, type) {
        if(typeof options == 'undefined') options = {};
        if(typeof type == 'undefined') type = "info";

        toastr[type](message, titre, options);
    }

    // Loading page

    let $btnSubmit = $('.app-ad-submit-btn');

    $btnSubmit.click(() => {
        $('#loader .preloader-wrapper').addClass('active');
        $(".page-content").addClass('disabled');
    })


});




