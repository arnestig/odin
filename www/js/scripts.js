/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2016  Tobias Eliasson <arnestig@gmail.com>
                            Jonas Berglund <jonas.jberglund@gmail.com>
                            Martin Rydin <martin.rydin@gmail.com>

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License along
   with this program; if not, write to the Free Software Foundation, Inc.,
   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

*/

$(document).ready(function() {

    $('body').on('click', 'a.disabled', function(event) {
        event.preventDefault();
    });

    $('.mailusersinnetwork').popover({
        trigger: 'click',
        placement: 'right',
        title: 'Users',
        container: 'body',
        html: true,
        content: function() {
            var theLog = '<p>No users leasing on this network</p>' + this.val;
            var nwid = $(".form-group #mailNetworkIdUsers").val();
            var heyhey = 'alhd';
            $.ajax({
                url: 'manage_networks.php',
                type: 'GET',
                dataType: 'text',
                data: 'mailnetworkid=' + nwid,
                //very bad with async false....
                // TODO: rewrite with callback
                async: false,
                success: function( response ) {
                    theLog = response;
                    //console.log(theLog);
                }
            });
            //console.log(theLog);
            return theLog;
        }
    });

    $('.history').popover({
        trigger: 'click',
        placement: 'right',
        title: 'Log',
        container: 'body',
        html: true,
        content: function() {
            var theLog = '<p>No history for this ip yet</p>';
            var host = this.id.substr(3);
            var heyhey = 'alhd';
            $.ajax({
                url: 'overview_handler.php',
                type: 'GET',
                dataType: 'text',
                data: 'host=' + host,
                //very bad with async false....
                // TODO: rewrite with callback
                async: false,
                success: function( response ) {
                    theLog = response;
                    //console.log(theLog);
                }
            });
            //console.log(theLog);
            return theLog;
        }   
    });


    $('td.check-lease-opt').on('click', 'input:checkbox', function() {
        var ip = this.id.substr(8);
        if ($(this).is(':checked')) {
            $('p[id="ciEmpty"]').remove();
            $('#leaseBasket').append('<p id="ci' + ip + '"">' + ip + '</p>');
            $('input[id="leasesActionBtn"]').show(300);
        } else {
            $('p[id="ci' + ip + '"]').remove();
        }
        if ( $('#leaseBasket').children().length < 1 ) {
            $('#leaseBasket').append('<p id="ciEmpty">Nothing selected</p>');
            $('input[id="leasesActionBtn"]').hide(300);
        }
    });

    // overview.php
    $('td.check-reserve').on('click', 'input:checkbox', function() {
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
                var ips = data.ipList;
                if (action && !data.opStatus) {
                    alert("Another user reserved this host. The host might be available in a few minutes again if the user don't book the address.");
                    location.reload(true);
                    $element.prop('checked', false);
                } else if (action && data.opStatus) {
                    var basketHtml = '';
                    for (i = 0; i < ips.length; i++) {
                        $('a.bookAddrBtn').show();
                        basketHtml += '<p id="bi' + ips[i] + '" class="cart-item">' + ips[i] + '<span id="rm' + ips[i] + '" class="glyphicon glyphicon-remove cart-remove pull-right"></span><p>';
                    }
                    $('div#choosenAddr').html(basketHtml);
                } else if (!action && data.opStatus) {
                    $('p[id="bi' + ip + '"]').remove();
                    if (ips.length < 1) {
                        $('a.bookAddrBtn').hide();
                        $('div#choosenAddr').html('<p class="text-center">EMPTY</p>');
                    }
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
                // TODO: compare recieved data to value sent to ensure proper removal
                for (j = 0; j < ips.length; j++) {
                    console.log('ipList[' + j + ']: ' + ips[j]);
                }
                var checkbox = 'input[id="cb' + ip + '"]';
                var basketItem = 'p[id="bi' + ip + '"]';
                $(checkbox).prop('checked', false);
                $(basketItem).remove();
                if (ips.length < 1) {
                    $('a.bookAddrBtn').hide();
                    $('div#choosenAddr').html('<p class="text-center">EMPTY</p>');
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Your request was not handled properly. Please try again.');
            }
        });
        console.log('#bi' +ip);
    });

    
    $(".rm-lease").on('click', function(event) {
        if( !confirm('Are you sure that you want to terminate the lease?') ) {
            event.preventDefault();
        }
    });

    $("#updateSettingsForm").submit(function(event) {
        if( !confirm('Are you sure that you save the settings?\nYou may need to logout first before certain settings are visible for you.') ) {
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
                if ( $(document).find('.book-address-container').length == 1 ) {
                    var getUrl = window.location;
                    var baseUrl = getUrl .protocol + "//" + getUrl.host + "/";
                    window.location.replace(baseUrl + 'overview.php');
                }
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Your request was not handled properly. Please try again.');
            }
        });
        
    });

    // Fix for autofocus in modals
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('[autofocus]').focus();
    });

    // UserIPS
    $(document).on("click", ".open-EditHostDialog", function () {

        var hostIp = $(this).data('hostip');
        var hostName = $(this).data('hostname');
        var hostDescription = $(this).data('hostdescription');

        $(".form-group #userHostIp").val( hostIp );
        $(".form-group #userHostIp2").val( hostIp );
        $(".form-group #userHostName").val( hostName );
        $(".form-group #userHostDescription").val( hostDescription );

    });

    // Manage Users
    $(document).on("click", ".open-EditUserDialog", function () {

        var userId = $(this).data('userid');
        var userName = $(this).data('username');
        var firstName = $(this).data('firstname');
        var lastName = $(this).data('lastname');
        var email = $(this).data('email');
        var privileges = $(this).data('privileges');

        $(".form-group #editUserId").val( userId );
        $(".form-group #editUserName").val( userName );
        $(".form-group #editFirstName").val( firstName );
        $(".form-group #editLastName").val( lastName );
        $(".form-group #editEmail").val( email );
        $(".form-group #editPrivileges").val( privileges ).change();

    });    

    $(document).on("click", ".open-RemoveUserDialog", function () {
        
        var userId = $(this).data('userid');
        var userName = $(this).data('username');
        var firstName = $(this).data('firstname');
        var lastName = $(this).data('lastname');
        var email = $(this).data('email');

        $(".form-group #removeUserId").val( userId );
        $(".form-group #removeUserName").val( userName );
        $(".form-group #removeFirstName").val( firstName );
        $(".form-group #removeLastName").val( lastName );
        $(".form-group #removeEmail").val( email );
    });

    $(document).on("click", ".open-MailUserDialog", function () {
        
        var userID = $(this).data('userid');
        var userName = $(this).data('username');
        var firstName = $(this).data('firstname');
        var lastName = $(this).data('lastname');
        var email = $(this).data('email');

        $(".form-group #mailUserID").val( userID );
        $(".form-group #mailUserName").val( userName );
        $(".form-group #mailUserName2").val( userName );
        $(".form-group #mailFirstName").val( firstName );
        $(".form-group #mailLastName").val( lastName );
        $(".form-group #mailEmail").val( email );
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

    $(document).on("click", ".open-MailNetworkUsersDialog", function () {
        
        var networkId = $(this).data('networkid');
        var networkBase = $(this).data('networkbase');
        var networkCidr = $(this).data('networkcidr');
        var numberOfUsersInNw = $(this).data('usersinnw');
        if ( numberOfUsersInNw == 1 ) {
            numberOfUsersInNw += " user";
        } else {
            numberOfUsersInNw += " users";
        }

        $(".form-group #mailNetworkId").val( networkId );
        $(".form-group #mailNetworkBase").val( networkBase );
        $(".form-group #mailNetworkCidr").val( networkCidr );
        $(".form-group #mailNetworkIdUsers").val( networkId );
        $("#mailNetworkIdUsersLink").text( numberOfUsersInNw );
    });
    

    //Submit page number when hitting enter
    $('.result-page-field').keydown(function(event) {
        if (event.keyCode == 13) {
            this.form.submit();
            return false;
         }
    });

    /*
    $('.accordion-toggle').on('click', function() {
        var id = $(this).prop('id').split('.').join('');
        console.log(id);
        if ($('#acc' + id + ' > i').hasClass('glyphicon-triangle-right')) {
            console.log('yes');
            $(this + '> i.glyphicon-triangle-right').hide();
            $(this + '> i.glyphicon-triangle-down').display('block');
        }
    });
    */
});
