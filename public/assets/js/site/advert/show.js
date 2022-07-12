$(document).ready(function() {

    /**
     * ligthBox plus
     *
     * @type {*|Window.jQuery|HTMLElement}
     */
    let $lightBoxPlus = $('#lightbox-plus');

    $lightBoxPlus.click(function () {
        $('img#img-plus').trigger('click');
    });

    /**
     * Envoie de message d'annonce en ajax
     *
     * @type {jQuery|HTMLElement}
     */
    messageAjax($('#app-advert-message-form'));

    let messageData = {
        1: "Bonjour, je souhaiterais avoir des informations complémentaires concernant votre annonce, merci de me recontacter aux coordonnées indiquées",

        2: "Bonjour, je souhaiterais recevoir d'autres photos concernant votre annonce, merci de me recontacter aux coordonnées indiquées",

        3: "Bonjour, je souhaiterais prendre rendez-vous avec vous, merci de me recontacter aux coordonnées indiquées"
    };

    $('#app-advert-message-motif').change(function () {
        let $this = $(this);

        $('#content').focus().val(messageData[$this.val()]);
    })

    // Signalement d'annonce
    reportAjax($('#app-advert-report-form'));


    // Annonce similaire
    let $advertBlock = $('.app-advert-similar-block');

    $advertBlock.click(function () {
        let $this = $(this);
        let $link = $this.find('.btn-advert-show')[0];

        $link.click();
    });

    // <---------
    //  Gestion des options des annonces
    let $inputOption = $('input.option-input'), $optionID = "";

    $inputOption.click(function () {
        let $this = $(this), $parent = $this.parents('#option-data'),
            $btn = $parent.find('a.btn-primary');

        $btn.removeClass('disabled');

        if (!($this.val() === $optionID)) {
            if ($optionID !== "") {
                $.ajax({
                    url: Routing.generate('app_cart_delete', {'id': $optionID}),
                    success: function(data) {
                        if (data.success) {
                            notification("Panier", "Precedent option retire du panier", {}, 'success')
                        }
                        loader(false);
                    }
                });
            }

            $optionID = $this.val();

            $.ajax({
                url: Routing.generate('app_cart_add', {'id': $optionID}),
                success: function(data) {
                    if (data.success) {
                        notification("Panier", "Option ajouter au panier", {}, 'success')
                    } else {
                        notification("Panier", "Erreur: Option déjà dans le panier", {}, 'error')
                    }
                }
            });
        } else {
            notification("Panier", "Cette option a deja été selectionnée", {}, 'info');
        }
    });
    // --------->

    // <---------
    // Gestion de l'impression de la page

    $('.app-ad-print').click(function (e){
        e.preventDefault();

        window.print() ;
    })
    // --------->
});

function messageAjax(element) {
    element.submit(function (e) {
        e.preventDefault();

        $('#loader .preloader-wrapper').addClass('active');
        $(".page-content").addClass('disabled');

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {
                if (data.success) {
                    notification("", data.message, {}, 'success')
                } else {
                    let errors = $.parseJSON(data.errors), errorContent = $('#app-advert-message-form-error');

                    errorContent.html("").addClass("mt-3");

                    $(errors).each(function (key, value) {
                        errorContent.append('<div class="small text-danger font-weight-stone-500">'+value+'</div>');
                    });

                    notification("Messagerie", "Erreur de validation, votre message n'a pas pu etre envoyer", {}, 'error')
                }

                $('#loader .preloader-wrapper').removeClass('active');
                $(".page-content").removeClass('disabled');
            }
        })
    });
}

function reportAjax(element) {
    element.submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {
                if (data.success) {
                    notification("", data.message, {}, 'success')
                } else {
                    let errors = $.parseJSON(data.errors), errorContent = $('#app-advert-report-form-error');

                    errorContent.html("").addClass("mt-2 mb-3");

                    $(errors).each(function (key, value) {
                        errorContent.append('<div class="small text-danger font-weight-stone-500">'+value+'</div>');
                    });

                    notification("Messagerie", "Erreur de validation, votre signalement n'a pas pu etre envoyer", {}, 'error')
                }
            }
        })
    });
}

function printit(){
    if (NS) {
        window.print() ;
    } else {
        var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
        document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
        WebBrowser1.ExecWB(6, 2);
    }
}




