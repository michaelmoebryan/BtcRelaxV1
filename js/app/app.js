/* global finaly */

var App = (function () {
    //our private functions and variables
    var vSessionId;
    var btnPS;
    var vAuthBusy;
    //our public functions and variables
    var dhxWins, myToolbar;
    var wSession;
    let myInterval;

    return {
        isLoginRun: function ()
        {
            let logWin = $(document.getElementById('copobanId'));

        },
        init: function () {

        },
        createUsr: function()
        {
//           var request = $.ajax({
//                        url: "/",
//                        method: "POST",
//                        async: false,
//                        data: { action:"createUser" },
//                        });
            $('#frmPage').submit(); 
//                request.done(function( msg ) {
//                        App.showRegisterOk();
//                    });
// 
//                request.fail(function( jqXHR, textStatus ) {
//                        App.showRegisterFail();
//                    });
            
//            $.ajax( "json.php?action=createUser", function() {
//              }).done(function() {
//                  App.showRegisterOk();
//                })
//                .fail(function() {
//                  App.showRegisterFail();
//                });
        },
        logoBtnClick: function ()
        {
            if (this.vAuthBusy !== true)
            {
                try {
                    this.vAuthBusy = true;
                    switch (localStorage.getItem("last_state"))
                    {
                        case 'UNAUTHENTICATED':
                            $('#idMainButton').switchClass("logo_unauth", "logo_auth");
                            this.showLoginWindow();
                            break;
                        default:
                            $.post( "json.php", { action: "kill" }, function(){                            
                                $('#idMainButton').switchClass("logo_auth", "logo_unauth");
                                $(document.getElementById('copobanId')).hide('slow');
                                location.reload(true);
                            });
                    }
                } catch (e) {
                    alert('Error while login:'.e.name);
                } finally {
                    this.vAuthBusy = false;
                }             
            }
        },
        playClick: function ()
        {
            btnPS = new Audio();
            btnPS.src = "/js/app/open.mp3";
            btnPS.play();
        },
        showRegisterOk: function()
        {
            dhtmlx.alert("Ваш акаунт успешно создан!", function(result){
                
                App.refreshPage();
                //location.reload(true);    
                //$('#frmLogin').submit();
		});          
            
        },
        showRegisterFail: function()
        {
            dhtmlx.alert({
                title:"Ошибка!",
                type:"alert-error",
                text:"В ходе регистрации, произошёл сбой, попробуйте позже.",
                callback:function()
                    {
                        App.refreshPage();    
                        //$('#frmLogin').submit();
                    }
                }); 
        },
        refresh_state: function ()
        {
            $.get('json.php?action=ping', function (data, status) {
                newState = data.message;
                oldState = localStorage.getItem("last_state");
                if (newState !== oldState)
                {
                    App.set_state(newState,0);
                }
            });
        },
        showLoginWindow: function ()
        {
            //App.myInterval =  setInterval(function() {   App.refresh_state(); }, 30000);
            $(document.getElementById('copobanId')).show('slow');
        },
        hideLoginWindow: function ()
        {
            clearInterval(App.myInterval);
            $(document.getElementById('copobanId')).hide('slow');
        },
        runui: function()
        {
            //var vCopoban = document.getElementById('copobanId');
            //wSession.attachObj('copobanId');

            //this.btnLogo = new dhtmlXWindowsButton();  
        },
        refreshPage: function()
        {
                    $.ajax({
                      success: function(html) {
                        document.replaceWith($.parseHTML(html));                        
                      }
                    });
                    location.reload();   
        },
        kill: function()
        {
            $.ajax(
                    {
                       url: "json.php",
                       async: false,
                       method: "POST",
                       data: { action:"kill" },
                       success:function() {
                          App.refreshPage();                  
                      }
                    });            
        },
        set_state: function (newState, sid)
        {
            localStorage.setItem("sid", sid);
            localStorage.setItem("last_state", newState);
            switch (newState) {
                case 'GUEST':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');
                    $('#copobanId').addClass('guest_reg_frm').slideUp('5000');
                    $('#dialog').dialog('open');
                    break;
                case 'USER':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');
                    if (document.getElementById('frmConfirmOrder') instanceof Object){
                        $('#dialog').dialog('open');
                    }
                    else
                    {
                        $('#copobanId').addClass('front_shop_panel').slideUp('5000');
                        $(document.getElementById('copobanId')).show('slow');                        
                    };
                    break;
                case 'ROOT':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');
                    $('#copobanId').addClass('front_shop_panel').slideUp('5000');
                    $(document.getElementById('copobanId')).show('slow');
                    break;
                case 'BANNED':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');
                    break;
                default:
                    $('#idMainButton').addClass('logo_unauth').fadeIn('5000');
                    };
        
                    $('#idMainButton').on("click", '#idMainButton img' , function () {
                        App.logoBtnClick();
                    });
        },
        
        finalize: function ()
        {

        }
    };
}());