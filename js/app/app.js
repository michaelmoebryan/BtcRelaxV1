 var App = (function() {
    //our private functions and variables
    var vSessionId;
    var btnPS; 
    
    //our public functions and variables
    var dhxWins, myToolbar;
    var wSession;
    let myInterval;

    return {
        isLoginRun: function()
        {
            let logWin = $(document.getElementById('copobanId'));

        },
        init: function() {
            
        },
        logoBtnClick: function()
        {
            btnPS = new Audio();
            btnPS.src = "/js/app/open.mp3";
            btnPS.play();
            switch(localStorage.getItem("last_state"))
            {
                case 'UNAUTHENTICATED':
                    $('#idMainButton').switchClass("logo_unauth","logo_auth");
                    App.showLoginWindow();
                    break;
                default:
                    $('#idMainButton').switchClass("logo_auth","logo_unauth");
                    $.get("json.php?action=kill", function(data, status){
                        alert("Data: " + data + "\nStatus: " + status);
                        location.reload();
                        App.hideLoginWindow();
                    });
            };
            $('#idMainButton').one("click", function(){
                    App.logoBtnClick();
                });
            
        },
        playClick: function()
        {
            
        },
        refresh_state: function() 
        {
            $.get("json.php?action=ping", function(data, status){
                newState = data.message;
                oldState = localStorage.getItem("last_state");                
                if (newState !== oldState)
                {
                    localStorage.setItem("last_state",newState);                
                };
            });                    
        },
        showLoginWindow: function()
        {
            //App.myInterval =  setInterval(function() {   App.refresh_state(); }, 30000);
            $(document.getElementById('copobanId')).show('slow');         
        },
        hideLoginWindow: function()
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
        set_state: function(newState)
        {           
            localStorage.setItem("last_state",newState);
            switch(newState) {
                case 'GUEST':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');                    
                    $('#copobanId').addClass('guest_reg_frm').slideUp('3000');
                    $('#dialog').dialog('open');
                    break;
                case 'USER':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');                    
                    break;
                case 'ROOT':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');                    
                    break;                 
                case 'BANNED':
                    $('#idMainButton').addClass('logo_auth').fadeIn('5000');                    
                    break;
                default:
                    $('#idMainButton').addClass('logo_unauth').fadeIn('5000');
            $('#idMainButton').one("click", function(){
                    App.logoBtnClick();
                });
            };
        },
        finalize: function()
        {

        }
        };
    }());