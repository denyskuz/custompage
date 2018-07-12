define([
    "jquery",
    "Elogic_CustomPage/js/owlcarousel/owl.carousel"
], function($){
    $.widget('mage.OwlCarousel', {
        options:{
            autoPlay: 3000,
            nav:false,
            margin:10,
            dots: false,
            responsiveClass:true,
            responsive:{
                0:{
                    items:2,
                    nav:false,
                    dots: false
                },
                800:{
                    items:4,
                    nav:false
                }
            }
        },

        _create: function () {
            $(this.element).owlCarousel(this.options);
        }
    });

    return $.mage.OwlCarousel;

});