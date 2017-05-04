/* global finaly, ROOT */

var App = (function () {
    //our private functions and variables
    var vSessionId;
    var btnPS;
    var vAuthBusy;
    //our public functions and variables
    var dhxWins;
    var myToolbar;
    var wSession;
    var role = {
			ROOT: "ROOT",
			USER: "USER",
			UNAUTHENTICATED: "UNAUTHENTICATED",
			BANNED: "BANNED",
                        GUEST: 'GUEST'
                    };

    return {
        isLoginRun: function ()
        {
            let logWin = $(document.getElementById('copobanId'));

        },
        init: function () {

        },
        createUsr: function()
        {
            $('#frmPage').submit(); 
        },
        checkOrder: function ()
        {
            $('#btnOrderCheck').children('i').addClass('fa-spin');
                  var request = $.getJSON("json.php", {action: "checkOrder"})
                    .done(function( json )
                        {
                            $('#btnOrderCheck').children('i').removeClass('fa-spin');
                            if (json.message == "Ok")
                            {
                                if (json.isNeedRefresh == true )
                                {
                                   $('#getOrder').submit();
                                }
                            }
                        }
                    );
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
                            $.get( "json.php", { action: "kill" }, function(){                            
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
            dhtmlx.alert("Ваш акаунт успешно зарегистрирован!", function(result){               
                App.refreshPage();
		});          
            
        },
        showRegisterFail: function()
        {
            dhtmlx.alert({
                title:"Ошибка!",
                type:"alert-error",
                text:"В момент регистрации произошёл сбой. Попробуйте позже.",
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
            var dhxWins = new dhtmlXWindows();
            var layoutWin  = dhxWins.createWindow("rootWindow", 20, 20, 600, 400);
            layoutWin.setSkin(terrace);
            layoutWin.setText("Core:");
            //var myLayout = layoutWin.attachLayout("1C");       
            var myToolbar = layoutWin.attachToolbar(
                {
                    icon_path: "img/icons/",
                    skin: dhx_terrace,
                    xml:"/toolbarStruct.xml"
                }
                ); 
            //myToolbar.setIconsPath("img/icons/");
            //myToolbar.loadStruct("/toolbarStruct.xml");
        },
        refreshPage: function()
        {
                    //$.ajax({
                    //  success: function(html) {
                    //    
                    //    
                    //  }
                    //});
                    document.location.href="";   
        },
        kill: function()
        {
            $.ajax(
                    {
                       url: "json.php",
                       async: false,
                       method: "get",
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
                        $('#copobanId').addClass('w3-modal').slideUp('5000');
                        $(document.getElementById('copobanId')).show('slow');                        
                    };
                    break;
                case 'ROOT':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');
                    $('#copobanId').addClass('front_shop_panel').slideUp('5000');
//                    $(document.getElementById('copobanId')).show('slow');
                    break;
                case 'BANNED':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');
                    break;
                default:
                    $('#idMainButton').addClass('logo_unauth').fadeIn('5000');
                    $('#copobanId').addClass('w3-display-middle').slideUp('5000');
            	
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