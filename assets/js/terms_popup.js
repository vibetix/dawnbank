$(document).ready(function () {
    $("#openTerms").click(function (event) {
        event.preventDefault();
        $("#termsPopup").fadeIn();
    });

    $(".close, #acceptTerms").click(function () {
        $("#termsPopup").fadeOut();
    });

    $("#acceptTerms").click(function () {
        $("#terms").prop("checked", true);
        $("#submitBtn").prop("disabled", false);
    });

    $("#terms").change(function () {
        $("#submitBtn").prop("disabled", !this.checked);
    });
});
