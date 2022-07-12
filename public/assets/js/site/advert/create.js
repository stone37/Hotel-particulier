$(document).ready(function() {
    /**
     * ##############
     * Upload File
     * ##############
     */

    let $ordPhoto = [];
    let $nbPhotos = photoFree;
    let $body = $('body');

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
            //afficherAlertePourQuitterLaPage = false;
            //window.location.href+='&imgv=1';
        },
        onDragEnter: function(){
            this.addClass('active');
        },
        onDragLeave: function(){
            this.removeClass('active');
        },
        onInit: function(){
            //console.log('Callback: Plugin initialized');
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

            if ($ordPhoto.length > (photoFree-1)) {

                if ($ordPhoto.length === photoFree) {
                    $('#imgUpload-list .imgUpload-add > div.btn-image-upload').addClass('disabled');
                }

                if($ordPhoto.length > photoFree && $nbPhotos === photoFree) {
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

            if ($ordPhoto.length === (photoFree+photoPay)) {
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

            if ($ordPhoto[0]===id) changePrincipale(id);
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
     * Choisir l'image principale
     */
    $body.on('click', 'div.img-principale', function(e) {
        e.preventDefault();

        changePrincipale($(this).attr('img-id'));
    });

    /**
     * Gestion de l'option photo
     */
    $('#option_PHOTO').click(function () {
        let $this = $(this);

        loader(true);

        if ($(this).prop('checked')) {
            $.ajax({
                url: Routing.generate('app_cart_add', {'id': $this.val()}),
                success: function(data) {
                    if (data.success) {
                        activeUPHOTO(1);
                        addCart()

                        notification("Panier", "Option ajouter au panier", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option déjà dans le panier", {}, 'error')
                    }

                    loader(false);
                }
            });
        } else {
            $.ajax({
                url: Routing.generate('app_cart_delete', {'id': $this.val()}),
                success: function(data) {
                    if (data.success) {
                        activeUPHOTO(0);
                        deleteCart()

                        notification("Panier", "Option retirer du panier", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option n'est pas dans le panier", {}, 'error')
                    }

                    loader(false);
                }
            });
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

        let $html = '<div class="col-lg-3 col-md-4 col-12 scale-up-ver-top '
            +(($ordPhoto[0]===file.id)?'principale':'')+
            (($ordPhoto.indexOf(file.id) > $nbPhotos-1)?' disabled':'')+'" id="'+file.id+'">';

        $html +=' <div class="img-bloc mx-4 z-depth-4">';
        $html +='	<img src="'+file.src+'" alt="Image de l\'annonce" class="">';
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
        $html +='	<div class="mx-4 img-principale" img-id="'+file.id+'">';
        $html +='		Photo principale';
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

                if ($ordPhoto.length < (photoFree-1)) {
                    $('#Upsell_PhotosSupp').hide();
                }

                $('#nbPhoto-ac').html(Math.min($ordPhoto.length, $nbPhotos));
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
     * Choisir la photo principale
     *
     * @param id
     */
    function changePrincipale(id) {
        var el = $('#'+id);
        if(el.hasClass('disabled')) return;

        let pos = $ordPhoto.indexOf(id);

        $.ajax({
            url: Routing.generate('app_image_upload_principale', {'pos': pos}),
            success: function() {
                $('#imgUpload-list .imgUpload-add > div').removeClass('principale');
                el.addClass('principale');
            }
        });
    }

    /**
     * Active l'option photo
     *
     * @param active
     */
    function activeUPHOTO(active) {
        $nbPhotos = photoFree;

        if (active){
            $nbPhotos = (photoFree+photoPay);
            $('#imgUpload-list .imgUpload-add > div').removeClass('disabled');

            if ($ordPhoto.length >= $nbPhotos) {
                $('#imgUpload-list .imgUpload-add > div.btn-image-upload').addClass('disabled');
            }
        } else {
            let set = $('#imgUpload-list .imgUpload-add > div');
            let last = set.length-1;
            set.each(function(i){
                if(i>photoFree-1 && i<last) $(this).addClass('disabled');
            });

            if ($ordPhoto.length > (photoFree-1))
                $('#imgUpload-list .imgUpload-add > div.btn-image-upload').addClass('disabled');
        }

        $('#nbPhoto-ac').html(Math.min($ordPhoto.length, $nbPhotos));
        $('#nbPhoto-info').html($nbPhotos);
    }

    // Loading page
    let $btnSubmit = $('.app-ad-submit-btn');

    $btnSubmit.click(() => {
        loader(true);
    })

    // Option visual
    let $optionSelect = $('#app-ad-option .option-select'),
        $productNumber = 0;

    $optionSelect.change(function (){
        let $this = $(this), $id = $this.val(), $type = $this.attr('data-type');
        let $input = $('#option'+$type),
            $price = $this.parents('.app-option-data-bulk').find('.price');

        if ($input.prop('checked')) {
            loader(true);

            $.ajax({
                url: Routing.generate('app_cart_replace', {'id': $input.val(), 'newId': $id}),
                success: function(data) {
                    if (data.success) {
                        notification("Panier", "Votre panier a été mis à jour", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option n'est pas dans le panier", {}, 'error')
                    }

                    loader(false);
                }
            });
        }

        $input.val($id);
        $price.text($donnees[$id]);
    });

    $('#app-ad-option .app-option-data input').click(function() {

        let $this = $(this);

        loader(true);

        if ($this.prop('checked')) {
            $.ajax({
                url: Routing.generate('app_cart_add', {'id': $this.val()}),
                success: function(data) {
                    if (data.success) {
                        $btn = $('#optionCarousel'+$this.attr('data-type'));
                        $btn.removeClass('btn-primary').addClass('hasCart btn-default');
                        $btn.html('<i class="fas fa-check"></i>');

                        addCart();
                        notification("Panier", "Option ajouter au panier", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option déjà dans le panier", {}, 'error')
                    }

                    loader(false);
                }
            });
        } else {
            $.ajax({
                url: Routing.generate('app_cart_delete', {'id': $this.val()}),
                success: function(data) {
                    if (data.success) {
                        $btn = $('#optionCarousel'+$this.attr('data-type'));
                        $btn.addClass('btn-primary').removeClass('hasCart btn-default');
                        $btn.html('Ajouter au panier');

                        deleteCart();
                        notification("Panier", "Option retirer du panier", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option n'est pas dans le panier", {}, 'error')
                    }

                    loader(false);
                }
            });
        }
    });

    $('#app-ad-option .btn-option').click(function(e) {

        e.preventDefault();

        let $this = $(this),
            $type = $this.attr('data-type'),
            $option = $('#option'+$type);

        loader(true);

        if (!$this.hasClass('hasCart')) {
            $.ajax({
                url: Routing.generate('app_cart_add', {'id': $option.val()}),
                success: function(data) {
                    if (data.success) {
                        $option.prop('checked', true)
                        $this.removeClass('btn-primary').addClass('hasCart btn-default');
                        $this.html('<i class="fas fa-check"></i>');

                        addCart();
                        notification("Panier", "Option ajouter au panier", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option déjà dans le panier", {}, 'error')
                    }

                    loader(false);
                }
            });
        } else {
            $.ajax({
                url: Routing.generate('app_cart_delete', {'id': $option.val()}),
                success: function(data) {
                    if (data.success) {
                        $option.prop('checked', false)
                        $this.addClass('btn-primary').removeClass('hasCart btn-default');
                        $this.html('Ajouter au panier');

                        deleteCart();
                        notification("Panier", "Option retirer du panier", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option n'est pas dans le panier", {}, 'error')
                    }

                    loader(false);
                }
            });
        }
    });

    $('.app-cart').click(function (e) {
        e.preventDefault();

        if ($productNumber) {
            $.ajax({
                url: Routing.generate('app_cart_index'),
                success: function(data) {
                    let $result = $.parseJSON(data), $total = 0,
                        $productContainer = $('#modalCart table tbody');

                    $productContainer.html("");

                    $.map($result, function (val, i) {
                        $productContainer.append('<tr>' +
                            '<th scope="row">'+(i+1)+'</th>' +
                            '<td>'+productName(val)+'</td> ' +
                            '<td class="font-weight-stone-500">'+val.price+
                            '<span class="small-8 font-weight-stone-500 pl-1">CFA</span></td>' +
                            '</tr>');

                        $total += parseInt(val.price);
                    });

                    $productContainer.append('<tr>' +
                        '<th scope="row"></th>' +
                        '<td>Total</td> ' +
                        '<td class="h5-responsive font-weight-stone-600">'+$total+
                        '<span class="small-8 font-weight-stone-600 pl-1">CFA</span></td> ' +
                        '</tr>');

                    $('#modalCart').modal();
                }
            });
        } else {
            notification("Panier", "Votre panier est vide", {}, 'info')
        }
    })




    function addCart() {
        $productNumber++;
        $('.app-cart .cart').text($productNumber)
    }

    function deleteCart() {
        $productNumber--;
        $('.app-cart .cart').text($productNumber)
    }

    function productName(element) {
        if (element.type === 1) {
            return 'Annonce en tête de liste pendant '+element.days+' jours';
        } else if (element.type === 2) {
            return 'Logo urgent pendant '+element.days+' jours';
        } else if (element.type === 3) {
            return 'Galerie de la page d\'accueil pendant '+element.days+' jours';
        } else if (element.type === 4) {
            return 'Annonce en vedette pendant '+element.days+' jours';
        } else {
            return 'Photos supplémentaires';
        }
    }




});

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

function loader(status) {
    if (status) {
        $('#loader .preloader-wrapper').addClass('active');
        $(".page-content").addClass('disabled');
    } else {
        $('#loader .preloader-wrapper').removeClass('active');
        $(".page-content").removeClass('disabled');
    }
}



