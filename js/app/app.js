var App = (function() {
    //our private functions and variables
    var vSessionId;

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
            this.refresh_state();

        },
        refresh_state: function() 
        {
            $.get("json.php?action=ping", function(data, status){
                newState = data.message;
                oldState = localStorage.getItem("last_state");                
                if (newState !== oldState)
                {
                    localStorage.setItem("last_state",newState);                };
            });                    
        },
        showLoginWindow: function()
        {
             App.myInterval =  setInterval(function() {   App.refresh_state(); }, 30000);
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
        finalize: function()
        {

        }
        };
    }());