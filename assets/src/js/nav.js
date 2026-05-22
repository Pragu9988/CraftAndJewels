(function($) {
    $(document).on("ready", function() {
        let iconOpenMenu = $(".hamburger");
        let iconCloseMenu = $(".icon-cross");
        console.log(iconCloseMenu);
        iconOpenMenu.click(function() {
            $(this).parent().find(".navbar").addClass("active");
        });

        iconCloseMenu.click(function() {
            $(this).parent().parent().removeClass("active");

        });
    });
})(jQuery);