function calculate() {
    var arr = $.map($('input:checkbox:checked'), function(e, i) {
        console.log(e.value);
        return e.value;
    });
    var newHTML = [];
    for (var i = 0; i < arr.length; i++) {
        newHTML.push('<p>' + arr[i] + '</p>');
    }
    $('div#choosenAddr').html(newHTML.join(""));
}

$(document).ready(function() {
    calculate();
    $('div').delegate('input:checkbox', 'click', calculate);

    $('form').click(function() {
        if ( $('#choosenAddr').children().length > 0 ) {
            $('#choosenAddrDiv').show(300);
        } else {
            $('#choosenAddrDiv').hide(200);
        }
    });

    $('.accordion-toggle').click(function() {

        console.log($('td#' + this.id + ' i'));
        console.log($('td#' + this.id + ' i').hasClass('glyphicon-triangle-right'));

        if ($(this.id + ' i').hasClass('glyphicon-triangle-right')) {

        }


/*
        var parentGlyphElement = document.getElementById(this.id);
        glyphElement = $(":first-child", parentGlyphElement);
        console.log(glyphElement);
        if ($(glyphElement).hasClass('glyphicon-triangle-right')) {
            console.log($(glyphElement).hasClass('glyphicon-triangle-right'));
            $(glyphElement).switchClass('glyphicon-triangle-right', 'glyphicon-triangle-down');
        } else {
            $(glyphElement).switchClass('glyphicon-triangle-down', 'glyphicon-triangle-right');
        }
*/
    });
});