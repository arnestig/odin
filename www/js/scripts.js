$(document).ready(function() {
    
    $('td').on('click', 'input:checkbox', function() {
        var $element = $(this);
        var ip = this.value;
        var action = $(this).is(':checked');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ip: ip,
                action: action
            },
            url: 'overview_handler.php',
            success : function(data){
                if (action && !data.opStatus) {
                    alert("Another user reserved this host. The host might be available in a few minutes again if the user don't book the address.");
                    location.reload(true);
                    $element.prop('checked', false);
                } else if (action && data.opStatus) {
                    var ips = data.ipList;
                    var basketHtml = '';
                    for (i = 0; i < ips.length; i++) {
                        basketHtml += '<p id="bi' + ips[i] + '" class="cart-item">' + ips[i] + '<span id="rm' + ips[i] + '" class="glyphicon glyphicon-remove cart-remove pull-right"></span><p>';
                    }
                    $('div#choosenAddr').html(basketHtml);
                } else if (!action && data.opStatus) {
                    $('p#bi' + ip).remove();
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Your request was not handled properly. Please try again.');
            }
        });
    });

    $('div#choosenAddr').on('click', '.cart-remove', function() {
        var ip = $(this).prop('id').substr(2);
        var action = 'false';
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ip: ip,
                action: action
            },
            url: 'overview_handler.php',
            success : function(data){
                console.log(data);
                var ips = data.ipList;
                for (j = 0; j < ips.length; j++) {
                    console.log('ipList[' + j + ']: ' + ips[j]);
                }
                var checkbox = 'input[id="cb' + ip + '"]';
                var basketItem = 'p[id="bi' + ip + '"]';
                $(checkbox).prop('checked', false);
                $(basketItem).remove();
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Your request was not handled properly. Please try again.');
            }
        });
        console.log('#bi' +ip);
    });

    
    $(".rm-lease").click(function(event) {
        if( !confirm('Are you sure that you want to terminate the lease?') ) {
            event.preventDefault();
        }
    });

    $(".book-address-container").on('click', '.book-address-remove', function() {
        var ip = this.id.substr(5);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                ip: ip,
                action: 'false'
            },
            url: 'overview_handler.php',
            success : function(data){
                var target = 'div[id="book' + ip + '"]';
                $(target).hide('slow', function(){ 
                    $(target).remove(); 
                });
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Your request was not handled properly. Please try again.');
            }
        });
        
    });

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

    });
});
