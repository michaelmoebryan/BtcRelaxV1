function ShowServerMessage()
{   
	$( "#copobanid" ).show("slow");                          
	return;
};



function doOnLoad()
{
	var xmlhttp = new XMLHttpRequest();
	var baseUrl = this.location.protocol + "//" + this.location.hostname;
	var url = baseUrl + "/api/json.php?method=ping";
	var targetObject = "#copobanid"; 
		
	 xmlhttp.onreadystatechange=function() {
	 if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
		PingBack(xmlhttp.responseText, targetObject);
		}
	 };
	 xmlhttp.open("GET", url, true);
	 xmlhttp.send();
};

function PingBack(response,targetObject)
{
	try 
	{
		var arr = JSON.parse(response);         
		if (arr === undefined)
		{
			return;
		}
		var dhxWins, w1, myForm, formData;
		myForm = new dhtmlXForm("ServerQuestion");
		myForm.loadStruct(arr, function(){});
		var myWins = new dhtmlXWindows({
			image_path:"codebase/imgs/",
			
		});
		w1 = myWins.createWindow({
			id:"ServerQuestion",
			top:50,
			left:20,        
			width:300,
			height:200,
			center:true,
			modal: true,
			keepInViewport: true,
		});
		w1.setText("Не определённое место входа!");
		w1.button("close").disable();
		w1.attachObject("#copobanid");
		
		dhtmlx.confirm({
			title: "Первый вход с этого места!",
			type:"confirm-warning",
			text: "У тебя есть BitId и ты желаеешь скорее зайти?",
			callback: function() {dhtmlx.confirm("Ок, сча зайдёш");}
			});    
	}
	catch(err) {
		alert(err);
	} 
	finally {
		   ShowServerMessage();	
	}
}

function CloseMessage()
{
//      killSession();
	  $( "#copobanid" ).hide();    
};
  
function killSession()
{
	$.ajax({
	  method: "POST",
	  url: "kill"
	})
	  .done(function( ) {
		window.location.assign(document.baseURI);
		location.reload();
	  })
		
};


