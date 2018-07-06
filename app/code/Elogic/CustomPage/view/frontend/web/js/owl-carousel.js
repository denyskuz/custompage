define([
    "jquery",
    "Elogic_CustomPage/js/owlcarousel/owl.carousel"
], function($){
    return function (config, element) {
        console.log('work');
        return $(element).owlCarousel(config);
    }
});