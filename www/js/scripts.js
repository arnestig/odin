function calculate() {
    var arr = $.map($('input:checkbox:checked'), function(e, i) {
        //console.log(e.value);
        return e.value;
    });
    var newHTML = [];
    for (var i = 0; i < arr.length; i++) {
        newHTML.push('<p>' + arr[i] + '</p>');
    }
    if (arr.length < 1) newHTML.push('<p><em>Empty</em></p>');
    $('div#choosenAddr').html(newHTML.join(""));
    /* Makes server update sesh var to preserve checked hosts */
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
          checkbox:arr
        },
        url: 'overview_handler.php',
        // Only show reply if ticked address was taken
        // if so, untick box
        success : function(data){
            console.log('reply is' + data.reply);
            //alert(data.reply);
            if (false) {
                $().prop('checked', false);
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            alert('There was an error.');
        }
    });
}


$(document).ready(function() {
    calculate();
    $('td').delegate('input:checkbox', 'click', calculate);

    // Manage Users
    $(document).on("click", ".open-EditUserDialog", function () {

        var userId = $(this).data('userid');
        var userName = $(this).data('username');
        var firstName = $(this).data('firstname');
        var lastName = $(this).data('lastname');
        var email = $(this).data('email');

        $(".form-group #userId").val( userId );
        $(".form-group #userName").val( userName );
        $(".form-group #firstName").val( firstName );
        $(".form-group #lastName").val( lastName );
        $(".form-group #email").val( email );
    });    

    $(document).on("click", ".open-RemoveUserDialog", function () {
        
        var userId = $(this).data('userid');
        var userName = $(this).data('username');
        var firstName = $(this).data('firstname');
        var lastName = $(this).data('lastname');
        var email = $(this).data('email');

        $(".form-group #userId").val( userId );
        $(".form-group #userName").val( userName );
        $(".form-group #firstName").val( firstName );
        $(".form-group #lastName").val( lastName );
        $(".form-group #email").val( email );
    });

    // Manage Networks
    $(document).on("click", ".open-EditNetworkDialog", function () {
        
        var networkId = $(this).data('networkid');
        var networkBase = $(this).data('networkbase');
        var networkCidr = $(this).data('networkcidr');
        var networkDescription = $(this).data('networkdescription');

        $(".form-group #networkId").val( networkId );
        $(".form-group #networkBase").val( networkBase );
        $(".form-group #networkBase2").val( networkBase );
        $(".form-group #networkCidr").val( networkCidr );
        $(".form-group #networkCidr2").val( networkCidr );
        $(".form-group #networkDescription").val( networkDescription );
    });

    $(document).on("click", ".open-RemoveNetworkDialog", function () {
        
        var networkId = $(this).data('networkid');
        var networkBase = $(this).data('networkbase');
        var networkCidr = $(this).data('networkcidr');
        var networkDescription = $(this).data('networkdescription');

        $(".form-group #networkId").val( networkId );
        $(".form-group #networkBase").val( networkBase );
        $(".form-group #networkCidr").val( networkCidr );
        $(".form-group #networkDescription").val( networkDescription );
    });
    
    // get id of plus click, then replace with glyphicon-ok or glyphicon-exclamation-sign
    // when ajax-call returns status on ip (locked/available)
    /*
    $('td>i.glyphicon-plus').on('click', function() {
        var elementIP = this.id;
        console.log(elementIP + ' first hurdle..');
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                ip:elementIP
            },
            url: "overview_handler.php",
            success : function(data) {
                console.log('reply is' + data.reply);
                alert(data.reply);
                $('i#plus192.168.0.1').replaceWith('<i class="glyphicon glyphicon-exclamation-sign"></i>');
            },
        });
    });
    */
    /*
    $('table').click(function() {
        if ( $('#choosenAddr').children().length > 0 ) {
            $('#choosenAddrDiv').show(300);
        } else {
            $('#choosenAddrDiv').hide(200);
        }
    });
    */

    //Submit page number when hitting enter
    $('.result-page-field').keydown(function(event) {
        if (event.keyCode == 13) {
            this.form.submit();
            return false;
         }
    });

    $('.accordion-toggle').click(function() {

        console.log($('td#' + this.id + ' i'));
        console.log($('td#' + this.id + '>i').hasClass('glyphicon-triangle-right'));

        if ($(this.id + ' i').hasClass('glyphicon-triangle-right')) {
            return console.log('yis - glyphicon-triangle-right');
        }


/*      TODO: Make the right-arrow a down-arrow when expanding host in overview.php

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