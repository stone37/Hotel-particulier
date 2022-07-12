$(document).ready(function() {
    let $containerP  = $('.list-group.app-category-content-parent'),
        $containerP1 = $('.app-category-content-parent-children-1'),
        $containerP2 = $('.app-category-content-parent-children-2'),
        $container1  = $('.list-group.app-category-content-children-1'),
        $container2  = $('.list-group.app-category-content-children-2');

    $('.children-0').click(function (e) {
        e.preventDefault();
        let $this = $(this);

        $.ajax({
            url: Routing.generate('app_category_by_parent', {id: $(this).attr('id')}),
            type: 'GET',
            beforeSend: function() {
                $('#loader .preloader-wrapper').addClass('active');
                $(".page-content").addClass('disabled');
            },
            success: function(data){
                $('#loader .preloader-wrapper').removeClass('active');
                $(".page-content").removeClass('disabled');

                let $result = $.parseJSON(data);

                $container1.html(""); $containerP1.html("");
                $containerP1.show(); $container1.show();

                let $parent = $('<a class="list-group-item d-flex justify-content-between align-items-center">' +
                      $result[0].parent.name + ' <i class="fas fa-times" title="Retour"></i></a>');

                $containerP1.append($parent);
                $containerP.hide();

                $.each($result, function(i, obj) {
                    let $route = Routing.generate('app_advert_create', {
                        category_slug: obj.parent.slug,
                        c: obj.slug,
                    });

                    let $i     = !(obj.children.length > 0) ? '<i class="fas fa-angle-right"></i>' : '',
                        $class = (obj.children.length > 0) ? 'children-1' : '',
                        $r     = !(obj.children.length > 0) ? $route : '';

                    $container1.append($('<a href="'+$r+'" id="'+obj.id+'" ' +
                                    'class="list-group-item d-flex justify-content-between ' +
                                    'align-items-center '+ $class +'">' + obj.name +' '+ $i + '</a>'));
                });
            }
        });
    });

    $container1.on("click", "a.children-1",function (e) {
        e.preventDefault();

        $.ajax({
            url: Routing.generate('app_category_by_parent', {id: $(this).attr('id')}),
            type: 'GET',
            beforeSend: function() {
                $('#loader .preloader-wrapper').addClass('active');
                $(".page-content").addClass('disabled');
            },
            success: function(data){
                $('#loader .preloader-wrapper').removeClass('active');
                $(".page-content").removeClass('disabled');

                let $result = $.parseJSON(data);

                $container2.html(""); $containerP2.html("");
                $containerP2.show(); $container2.show();

                let $parent = $('<a class="list-group-item d-flex justify-content-between align-items-center">' +
                    $result[0].parent.name + ' <i class="fas fa-times" title="Retour"></i></a>');

                $containerP2.append($parent);
                $container1.hide();

                $.each($result, function(i, obj) {
                    let $route = Routing.generate('app_advert_create', {
                        category_slug: obj.parent.parent.slug,
                        c: obj.parent.slug,
                        sc: obj.slug,
                    });

                    let $s     = !(obj.children.length > 0) ? '<i class="fas fa-angle-right"></i>' : '',
                        $class = (obj.children.length > 0) ? 'children-2' : '',
                        $r     = !(obj.children.length > 0) ? $route : '';

                    $container2.append($('<a href="'+$r+'" id="'+obj.id+'" class="list-group-item d-flex justify-content-between ' +
                        'align-items-center '+ $class +'">' + obj.name +' '+ $s + '</a>'));
                });
            }
        });
    });

    $containerP1.on("click", "i.fas", function () {
        $containerP.show();

        $containerP1.hide(); $container1.hide(); $containerP2.hide(); $container2.hide();
    });

    $containerP2.on("click", "i.fas", function () {
        $container1.show();

        $containerP2.hide(); $container2.hide()
    });

});


