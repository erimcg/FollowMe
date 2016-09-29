$(document).ready(function () {


    $("#searchBar").click(function () {
        this.value = "";
    });

    $(".excursion").click(function () {
        var id = this.id;
        var myclass = $(this).attr("class");

        if (myclass === "excursion") {
            $('.' + id).show("fast");
            $(this).addClass("selected");
        }
        else {
            $('.' + id).hide("fast");
            $(this).removeClass("selected")
        }
    });

});